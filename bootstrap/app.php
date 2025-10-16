<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
            'active' => \App\Http\Middleware\Active::class,
            'role' => \App\Http\Middleware\Role::class,
        ]);

        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
    //  Exceções de autenticação
    $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
        return response()->json([
            'success' => false,
            'message' => 'Não autenticado. Faça login novamente.',
        ], 401);
    });

    // 🔹 Exceções de autorização
    $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) {
        return response()->json([
            'success' => false,
            'message' => 'Você não tem permissão para realizar esta ação.',
        ], 403);
    });

    // 🔹 Recurso não encontrado
    $exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) {
        return response()->json([
            'success' => false,
            'message' => 'Recurso não encontrado.',
        ], 404);
    });

    // 🔹 Erros de validação
    $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
        return response()->json([
            'success' => false,
            'message' => 'Erro de validação nos dados enviados.',
            'errors' => $e->errors(),
        ], 422);
    });

    // 🔹 Erros genéricos (fallback)
    $exceptions->render(function (Throwable $e, $request) {
        return response()->json([
            'success' => false,
            'message' => 'Ocorreu um erro interno no servidor.',
            // ⚠️ Em produção, é melhor comentar esta linha:
            'error' => $e->getMessage(),
        ], 500);
    });
})->create();
