<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class Role
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        // Logging de informações para depuração
        Log::info('=== ROLE MIDDLEWARE EXECUTADO ===', [
        'user' => optional($request->user())->email,
        'user_role' => optional($request->user())->role,
        'roles_required' => $roles,
        'path' => $request->path()
        ]);

        // Verifica se há um usuário autenticado
        if (!$user) {
            // Aborta com 401 se não houver usuário.
            abort(401, 'Usuário não autenticado.');
        }

        // Aceita tanto "role:admin,manager" quanto "role:admin" como parâmetros
        if (count($roles) === 1 && str_contains($roles[0], ',')) {
            $roles = explode(',', $roles[0]);
        }

        // Normaliza as funções permitidas e a função do usuário (para minúsculas e sem espaços)
        $roles = array_map(fn($r) => strtolower(trim($r)), $roles);
        $userRole = strtolower(trim($user->role));

        // ⭐️ Lógica de BYPASS do Superadmin ⭐️
        // Se a função do usuário for 'superadmin', ele sempre tem permissão, ignorando as roles da rota.
        if ($userRole === 'superadmin') {
            Log::info('role: Superadmin bypass - Acesso concedido.', ['user_id' => $user->id]);
            return $next($request);
        }

        // Verifica se o papel do usuário está na lista permitida (aplica-se a todos exceto superadmin)
        if (!in_array($userRole, $roles)) {
            // Aborta com 403 (Forbidden) se o papel não for permitido
            abort(403, 'Não tens permissão para realizar esta operação.');
        }

        // Se o usuário tem a função permitida, continua a requisição
        return $next($request);
    }
}