<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\IndustryController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('industries', [IndustryController::class, 'store']);
    Route::put('industries/{industry}', [IndustryController::class, 'update']);
    Route::delete('industries/{industry}', [IndustryController::class, 'destroy']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
Route::get('industries/{industry}', [IndustryController::class, 'show']);
Route::get('industries', [IndustryController::class, 'index']);
