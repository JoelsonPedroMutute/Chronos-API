<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Registra os callbacks de tratamento de exceções.
     */
    public function register(): void
    {
        // 🔹 Usuário não autenticado
        $this->renderable(function (AuthenticationException $e, $request) {
            return response()->json([
                'success' => false,
                'message' => 'Não autenticado. Por favor, faça login novamente.'
            ], 401);
        });

        // 🔹 Sem permissão
        $this->renderable(function (AuthorizationException $e, $request) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para executar esta ação.'
            ], 403);
        });

        // 🔹 Recurso não encontrado
        $this->renderable(function (NotFoundHttpException|ModelNotFoundException $e, $request) {
            return response()->json([
                'success' => false,
                'message' => 'Recurso não encontrado.'
            ], 404);
        });

        // 🔹 Erros genéricos
        $this->renderable(function (Throwable $e, $request) {
            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        });
    }
}
