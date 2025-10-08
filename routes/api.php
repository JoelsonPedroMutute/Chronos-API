<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Auth
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);

        
        Route::middleware(['auth:sanctum'])->post('logout', [AuthController::class, 'logout']);

        
        Route::middleware(['auth:sanctum', 'active'])->group(function () {
            Route::get('profile', [AuthController::class, 'profile']);
            
        });
    });
});