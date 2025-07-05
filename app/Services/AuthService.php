<?php

namespace App\Services;

use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    /**
     * Attempt to authenticate a user and return a JWT token if successful.
     *
     * @param  array  $credentials  The user's credentials (email and password).
     * @return string|null The JWT token on success, null on failure.
     */
    public function login(array $credentials): ?string
    {
        if (! $token = JWTAuth::attempt($credentials)) {
            Log::warning('Login attempt failed for email.', [
                'email' => $credentials['email'] ?? 'not_provided',
            ]);

            return null;
        }

        $user = JWTAuth::user();
        Log::info('User logged in successfully.', ['user_id' => $user->id, 'email' => $user->email]);

        return $token;
    }

    /**
     * Log the currently authenticated user out by invalidating their token.
     */
    public function logout(): void
    {
        try {
            $user = JWTAuth::user();
            JWTAuth::invalidate(JWTAuth::getToken());

            if ($user) {
                Log::info('User logged out successfully.', ['user_id' => $user->id]);
            }
        } catch (JWTException $e) {
            Log::error('Error during JWT logout.', [
                'message' => $e->getMessage(),
                'user_id' => $user->id ?? 'unknown',
            ]);
        }
    }

    /**
     * Refresh the token for the currently authenticated user.
     *
     * @return string The new JWT token.
     *
     * @throws JWTException
     */
    public function refresh(): string
    {
        return JWTAuth::refresh(JWTAuth::getToken());
    }

    /**
     * Get the currently authenticated user.
     *
     * @return User|null The authenticated user, or null if not authenticated.
     */
    public function getAuthenticatedUser(): ?User
    {
        try {
            return JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            return null;
        }
    }

    /**
     * Create a structured response array for a successful authentication.
     *
     * @param  string  $token  The JWT token.
     * @return array The structured token response.
     */
    public function respondWithToken(string $token): array
    {
        return [
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => config('jwt.ttl') * 60,
            'user' => new UserResource(auth()->user()),
        ];
    }
}
