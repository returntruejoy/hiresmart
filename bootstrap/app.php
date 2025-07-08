<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withProviders([
        \App\Providers\AuthServiceProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $createApiErrorResponse = function (string $message, int $statusCode, mixed $errors = null) {
            $response = [
                'success' => false,
                'message' => $message,
            ];

            if ($errors !== null) {
                $response['errors'] = $errors;
            }

            $response['meta'] = [
                'api_version' => 'v1',
                'timestamp' => now()->toISOString(),
            ];

            return response()->json($response, $statusCode);
        };

        $exceptions->render(function (ValidationException $e, Request $request) use ($createApiErrorResponse) {
            if ($request->is('api/*')) {
                return $createApiErrorResponse(
                    'Validation failed',
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    $e->errors()
                );
            }
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) use ($createApiErrorResponse) {
            if ($request->is('api/*')) {
                return $createApiErrorResponse(
                    'The requested resource was not found.',
                    Response::HTTP_NOT_FOUND
                );
            }
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) use ($createApiErrorResponse) {
            if ($request->is('api/*')) {
                return $createApiErrorResponse(
                    'Unauthenticated.',
                    Response::HTTP_UNAUTHORIZED
                );
            }
        });

        $exceptions->render(function (AuthorizationException $e, Request $request) use ($createApiErrorResponse) {
            if ($request->is('api/*')) {
                return $createApiErrorResponse(
                    $e->getMessage() ?: 'This action is unauthorized.',
                    Response::HTTP_FORBIDDEN
                );
            }
        });

        $exceptions->render(function (Throwable $e, Request $request) use ($createApiErrorResponse) {
            if ($request->is('api/*') && ! config('app.debug')) {
                return $createApiErrorResponse(
                    'Internal Server Error',
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }
        });
    })->create();
