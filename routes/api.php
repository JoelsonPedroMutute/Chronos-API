<?php

use App\Http\Controllers\API\V1\UserController;
use App\Http\Controllers\API\V1\UserImageController;
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

    // 🔹 User routes (com autenticação e verificação de status/role)
    Route::middleware(['auth:sanctum', 'active', 'role:superadmin,admin,manager,user'])->group(function () {
        Route::get('users', [UserController::class, 'index']);
        Route::get('users/profile', [UserController::class, 'profile']);
        Route::get('users/{id}', [UserController::class, 'show']);
        Route::post('users', [UserController::class, 'store']);

        // Atualização de senha (própria ou de outro usuário)
        Route::patch('/users/password/{id?}', [UserController::class, 'changePassword']);

        // Atualizações gerais
        Route::patch('/users/{user}', [UserController::class, 'update']);
        Route::patch('/users/{user}/status', [UserController::class, 'updateStatus']);
        Route::patch('/users/{user}/role', [UserController::class, 'changeRole']);

        // Exclusões
        Route::delete('/users/me', [UserController::class, 'destroy'])->name('users.me.destroy');
        Route::delete('/users/{id}', [UserController::class, 'delete'])->name('users.destroy');
    });


    //  User image routes
    Route::middleware(['auth:sanctum', 'active', 'role:superadmin,admin,manager,user'])->group(function () {
        Route::get('/users/{user}/image', [UserImageController::class, 'show']);
        Route::post('/users/{user}/image', [UserImageController::class, 'store']);
        Route::get('/users/{user}/image/download', [UserImageController::class, 'download']);
        Route::patch('users/{user}/image/crop', [UserImageController::class, 'crop']);
        Route::delete('/users/{user}/image', [UserImageController::class, 'destroy']);
    });
});
