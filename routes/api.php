<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\RegistrationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// API Version 1 Routes
Route::prefix('v1')->group(function () {
    
    // Test route to check if API is working
    Route::get('test', function () {
        return response()->json([
            'success' => true,
            'message' => 'API is working!',
            'timestamp' => now(),
            'database' => [
                'connection' => config('database.default'),
                'host' => config('database.connections.' . config('database.default') . '.host'),
            ]
        ]);
    });
    
    // Debug route to check configurations
    Route::get('debug', function () {
        try {
            // Test database connection
            $dbTest = \DB::connection()->getPdo() ? 'Connected' : 'Failed';
        } catch (\Exception $e) {
            $dbTest = 'Failed: ' . $e->getMessage();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Debug information',
            'configuration' => [
                'app_debug' => config('app.debug'),
                'app_env' => config('app.env'),
                'database_default' => config('database.default'),
                'database_host' => config('database.connections.' . config('database.default') . '.host'),
                'database_test' => $dbTest,
                'jwt_secret_set' => config('jwt.secret') ? 'Yes' : 'No',
                'jwt_ttl' => config('jwt.ttl'),
            ],
            'services' => [
                'user_model_exists' => class_exists('App\Models\User') ? 'Yes' : 'No',
                'user_service_exists' => class_exists('App\Services\UserService') ? 'Yes' : 'No',
                'user_repository_exists' => class_exists('App\Repositories\UserRepository') ? 'Yes' : 'No',
                'jwt_facade_exists' => class_exists('Tymon\JWTAuth\Facades\JWTAuth') ? 'Yes' : 'No',
            ]
        ]);
    });
    
    // Test registration controller
    // Route::get('register/test', [RegistrationController::class, 'test']);
    
    // Registration routes (public, no auth required)
    Route::post('register', [RegistrationController::class, 'register']);
});

// Legacy route (you may want to remove this)
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
