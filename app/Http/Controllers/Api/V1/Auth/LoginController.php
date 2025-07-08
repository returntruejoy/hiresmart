<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Services\AuthService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Exceptions\JWTException;

class LoginController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private AuthService $authService
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $credentials = $request->only('email', 'password');

            if (! $token = $this->authService->login($credentials)) {
                return $this->unauthorizedResponse('Invalid credentials provided.');
            }

            $tokenData = $this->authService->respondWithToken($token);

            return $this->successResponse($tokenData, 'Login successful.');
        } catch (\Exception $e) {
            return $this->errorResponse('Login failed due to a server error.');
        }
    }

    public function logout(): JsonResponse
    {
        try {
            $this->authService->logout();

            return $this->successResponse(null, 'Successfully logged out.');
        } catch (JWTException $e) {
            // Log the exception...
            return $this->errorResponse('Failed to logout. Please try again.');
        }
    }

    public function refresh(): JsonResponse
    {
        try {
            $token = $this->authService->refresh();
            $tokenData = $this->authService->respondWithToken($token);

            return $this->successResponse($tokenData, 'Token refreshed successfully.');
        } catch (JWTException $e) {
            return $this->unauthorizedResponse('Could not refresh token. Please log in again.');
        }
    }

    public function me(): JsonResponse
    {
        $user = $this->authService->getAuthenticatedUser();

        if (! $user) {
            return $this->unauthorizedResponse('User not authenticated.');
        }

        return $this->successResponse(
            new UserResource($user),
            'User profile retrieved successfully.'
        );
    }
}
