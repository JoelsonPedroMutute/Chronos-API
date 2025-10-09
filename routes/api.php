<?php

use App\Http\Controllers\API\V1\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // 🔹 Auth routes
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);
        Route::post('/forgot-password', [UserController::class, 'forgotPassword']);
        Route::post('/reset-password', [UserController::class, 'resetPassword']);
    });

    // 🔹 User routes (fora do grupo 'auth', mas com middleware)
    Route::middleware(['auth:sanctum', 'active'])->group(function () {
        Route::get('users', [UserController::class, 'index']);
        Route::get('users/profile', [UserController::class, 'profile']); // ✅ CORRIGIDO
    });
});