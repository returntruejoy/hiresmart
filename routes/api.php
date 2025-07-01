<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\RegistrationController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\JobPostController;
use App\Http\Controllers\Api\V1\ApplicationController;

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

// API Version 1 Routes
Route::prefix('v1')->group(function () {
    Route::post('register', [RegistrationController::class, 'register']);
    Route::post('login', [LoginController::class, 'login']);

    // Public Job Post Routes
    Route::get('job-posts', [JobPostController::class, 'index'])->name('job-posts.index');
    Route::get('job-posts/{job_post}', [JobPostController::class, 'show'])->name('job-posts.show');

    // Authenticated Routes
    Route::middleware('auth:api')->group(function () {
        // Employer Routes
        Route::middleware('role:employer')->group(function () {
            Route::get('employer/job-posts', [JobPostController::class, 'indexForEmployer'])->name('employer.job-posts.index');
            Route::post('job-posts', [JobPostController::class, 'store'])->name('job-posts.store');
            Route::put('job-posts/{job_post}', [JobPostController::class, 'update'])->name('job-posts.update');
            Route::delete('job-posts/{job_post}', [JobPostController::class, 'destroy'])->name('job-posts.destroy');
            Route::get('job-posts/{job_post}/applications', [ApplicationController::class, 'index'])->name('job-posts.applications.index');
        });

        // Candidate Routes
        Route::middleware('role:candidate')->group(function () {
            Route::post('job-posts/{job_post}/apply', [ApplicationController::class, 'store'])->name('job-posts.apply');
        });
    });
});

// Legacy route (you may want to remove this)
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
