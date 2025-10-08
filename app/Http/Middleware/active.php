<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class active
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if(!$user){
            return response()->json([
                'message' => 'Usuário não autenticado'
            ], 401);
        }

        
        // Se o usuário NÃO é superadmin/admin E status NÃO é active → bloqueia
        if($user->status !== 'active' && !in_array($user->role, ['superadmin', 'admin'])) {
            return response()->json([
                'message' => 'Usuário não ativo'
            ], 403);
        }

        return $next($request);
    }
}