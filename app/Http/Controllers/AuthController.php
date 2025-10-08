<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\UserResource; // Add this import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(RegisterUserRequest $request)
    {
        $data = $request->validated();
        
        if(User::where('email', $data['email'])->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Email já está registrado',
                'data' => null
            ], 400);
        }

        $isFirstSuperAdmin = User::where('role', 'superadmin')->count() == 0;
        $isFirstAdmin = User::where('role', 'admin')->count() == 0;

        $role = $data['role'] ?? 'user';
        $status = 'active';

        if($role === 'superadmin' && !$isFirstSuperAdmin) {
            $role = 'pending';
        }

        if($role === 'admin' && !$isFirstAdmin) {
            $role = 'pending';
        }

        // Create user
        $user = User::create([
            'id' => Str::uuid(),
            'name' => $data['name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'] ?? null,
            'password' => Hash::make($data['password']),
            'role' => $role,
            'status' => $status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usuário registrado com sucesso',
            'data' => [
                'user' => new UserResource($user) // Now this will work
            ]
        ], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciais inválidas',
                'data' => null
            ], 401);
        }

        if($user->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não está ativo',
                'data' => null
            ], 403);
        }

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login realizado com sucesso',
            'data' => [
                'user' => new UserResource($user), // Now this will work
                'token' => $token
            ]
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout realizado com sucesso',
            'data' => null
        ], 200);
    }
}