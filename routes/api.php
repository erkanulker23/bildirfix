<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\DeviceController;
use App\Http\Controllers\Api\V1\LocationController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\StoryController;
use App\Http\Controllers\Api\V1\SupportController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::middleware('throttle:api')->group(function (): void {
        Route::get('locations/cities', [LocationController::class, 'cities']);
        Route::get('locations/cities/{city}/districts', [LocationController::class, 'districts']);
        Route::get('locations/districts/{district}/neighborhoods', [LocationController::class, 'neighborhoods']);

        Route::get('posts', [PostController::class, 'index']);
        Route::get('posts/{post}', [PostController::class, 'show']);
        Route::get('posts/{post}/comments', [CommentController::class, 'index']);
        Route::get('stories', [StoryController::class, 'index']);
    });

    Route::post('auth/register', [AuthController::class, 'register'])->middleware('throttle:otp-send');
    Route::post('auth/resend-otp', [AuthController::class, 'resendOtp'])->middleware('throttle:otp-send');
    Route::post('auth/verify-otp', [AuthController::class, 'verifyOtp'])->middleware('throttle:otp-verify');
    Route::post('auth/login', [AuthController::class, 'login'])->middleware('throttle:login');

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me', [AuthController::class, 'me']);
    });

    Route::middleware(['auth:sanctum', 'verified.phone'])->group(function (): void {
        Route::post('devices', [DeviceController::class, 'store']);

        Route::post('posts', [PostController::class, 'store'])->middleware('throttle:create-content');
        Route::post('stories', [StoryController::class, 'store'])->middleware('throttle:create-content');
        Route::post('posts/{post}/comments', [CommentController::class, 'store'])->middleware('throttle:create-content');
        Route::post('posts/{post}/supports/toggle', [SupportController::class, 'toggle'])->middleware('throttle:create-content');
    });
});
