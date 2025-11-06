<?php

use App\Http\Controllers\API\V1\CompanyController;
use App\Http\Controllers\API\V1\EmployeeCategoryController;
use App\Http\Controllers\API\V1\UserController;
use App\Http\Controllers\API\V1\UserImageController;
use App\Http\Controllers\API\V1\EmployeeController;
use App\Http\Controllers\Api\V1\EmployeeImageController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Version 1 (v1)
|--------------------------------------------------------------------------
| Esta versão segue o padrão RESTful, com rotas agrupadas por entidade e
| protegidas por autenticação via Sanctum, middleware de status e roles.
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | 🔐 AUTH ROUTES
    |--------------------------------------------------------------------------
    */
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);

        // Logout requer autenticação
        Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);

        // Recuperação de senha
        Route::post('forgot-password', [UserController::class, 'forgotPassword']);
        Route::post('reset-password', [UserController::class, 'resetPassword']);
    });

    /*
    |--------------------------------------------------------------------------
    | 👤 USER ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth:sanctum', 'active', 'role:superadmin,admin,manager,user'])->group(function () {

        // Listagem e visualização
        Route::get('users', [UserController::class, 'index']);
        Route::get('users/profile', [UserController::class, 'profile']);
        Route::get('users/{id}', [UserController::class, 'show']);

        // Criação de usuário
        Route::post('users', [UserController::class, 'store']);

        // Atualização de senha
        Route::patch('users/password/{id?}', [UserController::class, 'changePassword']);

        // Atualizações gerais
        Route::patch('users/{user}', [UserController::class, 'update']);
        Route::patch('users/{user}/status', [UserController::class, 'updateStatus']);
        Route::patch('users/{user}/role', [UserController::class, 'changeRole']);

        // Exclusões
        Route::delete('users/me', [UserController::class, 'destroy'])->name('users.me.destroy');
        Route::delete('users/{id}', [UserController::class, 'delete'])->name('users.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | 🖼️ USER IMAGE ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth:sanctum', 'active', 'role:superadmin,admin,manager,user'])->group(function () {
        Route::get('users/{user}/image', [UserImageController::class, 'show']);
        Route::post('users/{user}/image', [UserImageController::class, 'store']);
        Route::get('users/{user}/image/download', [UserImageController::class, 'download']);
        Route::patch('users/{user}/image/crop', [UserImageController::class, 'crop']);
        Route::delete('users/{user}/image', [UserImageController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | 🧑‍💼 EMPLOYEE ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth:sanctum', 'active', 'role:superadmin,admin,manager'])->group(function () {

        // Criação e listagem
        Route::post('employees', [EmployeeController::class, 'store']);
        Route::get('employees/profile', [EmployeeController::class, 'profile']);
        Route::get('employees', [EmployeeController::class, 'index']);
        Route::get('employees/company/{id}', [EmployeeController::class, 'getEmployeeByCompany']);

        // Visualização individual
        Route::get('employees/{id}', [EmployeeController::class, 'show']);

        // Atualizações
        Route::patch('employees/{employee}', [EmployeeController::class, 'update']);
        Route::patch('employees/{employee}/settings', [EmployeeController::class, 'updateSettings']);
        Route::patch('employees/{employee}/status', [EmployeeController::class, 'updateStatus']);
        Route::patch('employees/{employee}/role', [EmployeeController::class, 'updateRole']);

        // Exclusão
        Route::delete('employees/{employee}', [EmployeeController::class, 'destroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | 🏷️ EMPLOYEE CATEGORY ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth:sanctum', 'active', 'role:superadmin,admin,manager'])
        ->prefix('employee-categories')
        ->group(function () {
            Route::get('/', [EmployeeCategoryController::class, 'index']);
            Route::get('/{category}', [EmployeeCategoryController::class, 'show']);
            Route::post('/', [EmployeeCategoryController::class, 'store']);
            Route::patch('/{category}', [EmployeeCategoryController::class, 'update']);
            Route::delete('/{category}', [EmployeeCategoryController::class, 'destroy']);
        });

    /*
    |--------------------------------------------------------------------------
    | 🏢 COMPANY ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth:sanctum', 'active', 'role:superadmin,admin,manager'])->prefix('companies')->group(function () {
            Route::get('/', [CompanyController::class, 'index']);
            Route::get('/{id}', [CompanyController::class, 'show']);
            Route::post('/', [CompanyController::class, 'store']);
            Route::patch('/{id}', [CompanyController::class, 'update']);
            Route::delete('/{id}', [CompanyController::class, 'destroy']);
        });

    /*
    |--------------------------------------------------------------------------
    | 🖼️ EMPLOYEE IMAGE ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth:sanctum', 'active', 'role:superadmin,admin,manager'])
        ->prefix('employees')
        ->group(function () {
            Route::get('{employee}/image', [EmployeeImageController::class, 'show']);
            Route::post('{employee}/image', [EmployeeImageController::class, 'store']);
            Route::get('{employee}/image/download', [EmployeeImageController::class, 'download']);
            Route::patch('{employee}/image/crop', [EmployeeImageController::class, 'cropImage']);
            Route::delete('{employee}/image', [EmployeeImageController::class, 'destroy']);
        });
});
