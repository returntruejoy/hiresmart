<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\RegistrationController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\JobPostController;
use App\Http\Controllers\Api\V1\ApplicationController;
use App\Http\Controllers\Api\V1\Admin\DashboardController;

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

Route::prefix('v1')->group(function () {
    Route::post('register', [RegistrationController::class, 'register']);
    Route::post('login', [LoginController::class, 'login']);

    Route::get('job-posts', [JobPostController::class, 'index'])->name('job-posts.index');
    Route::get('job-posts/{job_post}', [JobPostController::class, 'show'])->name('job-posts.show');
    Route::get('job-posts/cache/stats', [JobPostController::class, 'cacheStats'])->name('job-posts.cache.stats');
    Route::post('job-posts/cache/clear', [JobPostController::class, 'clearCache'])->name('job-posts.cache.clear');

    Route::middleware('auth:api')->group(function () {
        Route::middleware('role:employer')->group(function () {
            Route::get('employer/job-posts', [JobPostController::class, 'indexForEmployer'])->name('employer.job-posts.index');
            Route::post('job-posts', [JobPostController::class, 'store'])->name('job-posts.store');
            Route::put('job-posts/{job_post}', [JobPostController::class, 'update'])->name('job-posts.update');
            Route::delete('job-posts/{job_post}', [JobPostController::class, 'destroy'])->name('job-posts.destroy');
            Route::get('job-posts/{job_post}/applications', [ApplicationController::class, 'index'])->name('job-posts.applications.index');
            Route::get('employer/stats', [DashboardController::class, 'employerStats'])->name('employer.stats');
        });

        Route::middleware('role:candidate')->group(function () {
            Route::post('job-posts/{job_post}/apply', [ApplicationController::class, 'store'])->name('job-posts.apply');
        });

        Route::middleware('role:admin')->group(function () {
            Route::get('admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        });
    });
});