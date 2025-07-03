<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\RegistrationRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class RegistrationController extends Controller
{
    public function __construct(
        private UserService $userService
    ) {}

    /**
     * Test method to check if controller is working.
     */
    public function test(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'RegistrationController is working!',
            'services' => [
                'userService' => $this->userService ? 'Available' : 'Not Available',
                'jwt' => class_exists('Tymon\JWTAuth\Facades\JWTAuth') ? 'Available' : 'Not Available',
            ]
        ]);
    }

    /**
     * Register a new user and return JWT token.
     */
    public function register(RegistrationRequest $request): JsonResponse
    {
        try {
            $user = $this->userService->createUser($request->validated());

            // Generate JWT token for the user
            $token = JWTAuth::fromUser($user);

            Log::info('User registered successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => config('jwt.ttl') * 60
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('User registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'email' => $request->email ?? 'unknown',
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Registration failed. Please try again.',
                'debug_message' => config('app.debug') ? $e->getMessage() : null,
                'errors' => [
                    'general' => ['An unexpected error occurred during registration.']
                ]
            ], 500);
        }
    }

    /**
     * Register a new employer with additional validation.
     */
    public function registerEmployer(RegistrationRequest $request): JsonResponse
    {
        try {
            // Merge employer role into request data
            $userData = array_merge($request->validated(), [
                'role' => \App\Models\User::ROLE_EMPLOYER
            ]);

            // Create employer user
            $user = $this->userService->createUser($userData);

            // Generate JWT token
            $token = JWTAuth::fromUser($user);

            Log::info('Employer registered successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Employer registered successfully',
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => config('jwt.ttl') * 60
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Employer registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'email' => $request->email ?? 'unknown',
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Employer registration failed. Please try again.',
                'debug_message' => config('app.debug') ? $e->getMessage() : null,
                'debug_trace' => config('app.debug') ? $e->getTraceAsString() : null,
                'errors' => [
                    'general' => ['Registration failed: ' . ($e->getMessage() ?: 'Unknown error')]
                ]
            ], 500);
        }
    }

    /**
     * Register a new candidate.
     */
    public function registerCandidate(RegistrationRequest $request): JsonResponse
    {
        try {
            // Merge candidate role into request data
            $userData = array_merge($request->validated(), [
                'role' => \App\Models\User::ROLE_CANDIDATE
            ]);

            // Create candidate user
            $user = $this->userService->createUser($userData);

            // Generate JWT token
            $token = JWTAuth::fromUser($user);

            Log::info('Candidate registered successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Candidate registered successfully',
                'data' => [
                    'user' => new UserResource($user),
                    'token' => $token,
                    'token_type' => 'bearer',
                    'expires_in' => config('jwt.ttl') * 60
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('Candidate registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'email' => $request->email ?? 'unknown',
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Candidate registration failed. Please try again.',
                'debug_message' => config('app.debug') ? $e->getMessage() : null,
                'debug_trace' => config('app.debug') ? $e->getTraceAsString() : null,
                'errors' => [
                    'general' => ['Registration failed: ' . ($e->getMessage() ?: 'Unknown error')]
                ]
            ], 500);
        }
    }
}
