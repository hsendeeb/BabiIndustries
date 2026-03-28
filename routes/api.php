<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\IndustryController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('industries', [IndustryController::class, 'store']);
        Route::put('industries/{industry}', [IndustryController::class, 'update']);
        Route::delete('industries/{industry}', [IndustryController::class, 'destroy']);

        Route::post('services', [ServiceController::class, 'store']);
        Route::put('services/{service}', [ServiceController::class, 'update']);
        Route::delete('services/{service}', [ServiceController::class, 'destroy']);

        Route::post('categories', [CategoryController::class, 'store']);
        Route::put('categories/{category}', [CategoryController::class, 'update']);
        Route::delete('categories/{category}', [CategoryController::class, 'destroy']);

        Route::post('/logout', [AuthController::class, 'logout']);
    });
    Route::get('industries/{industry}', [IndustryController::class, 'show']);
    Route::get('industries', [IndustryController::class, 'index']);
    Route::get('services/{service}', [ServiceController::class, 'show']);
    Route::get('services', [ServiceController::class, 'index']);
    Route::get('categories/{category}', [CategoryController::class, 'show']);
    Route::get('categories', [CategoryController::class, 'index']);
});
