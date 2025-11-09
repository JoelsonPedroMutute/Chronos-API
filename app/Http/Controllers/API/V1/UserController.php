<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use App\Filters\UserFilter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;


class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Lista todos os usuários (com filtros e paginação)
     */
    public function index(Request $request)
    {
        Log::info(
            'Usuário autenticado',
            ['id' => $request->user()->id, 'role' => $request->user()->role]
        );

        $this->authorize('viewAny', User::class);

        $users = $this->userService->getAllFiltered($request);

        $message = $users->isEmpty()
            ? 'Nenhum usuário encontrado para o filtro aplicado'
            : 'Usuários encontrados com sucesso';

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => UserResource::collection($users),
        ], 200);
    }
    /*
      pegar o resource  e não o error , pegar esta estutura como exemplo
     return response()->json([
            'success' => true,
            'message' => $message,
            'data' => UserResource::collection($users),
        ], 200);
    */

    public function profile(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Perfil do usuário encontrado com sucesso',
            'data' => new UserResource($request->user()),
        ], 200);
    }
    public function show(Request $request, $id)
    {

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não encontrado ou foi removido.'
            ], 400);
        }

        $this->authorize('view', $user);

        return response()->json([
            'success' => true,
            'message' => 'Usuário encontrado com sucesso',
            'data' => new UserResource($user),
        ], 200);
    }

    public function store(StoreUserRequest $request)
    {
        if ($request->user()->cannot('create', User::class)) {
            return response()->json([
                'success' => false,
                'message' => 'Ação não permitida: apenas superadmins podem criar superadministradores.'
            ], 403);
        }

        $user = $this->userService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Usuário criado com sucesso',
            'data' => new UserResource($user),
        ], 201);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        // Verifica se o usuário pode atualizar este usuário específico
        $this->authorize('update', $user);

        $updatedUser = $this->userService->update($user, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Usuário atualizado com sucesso',
            'data' => new UserResource($updatedUser),
        ], 200);
    }
    public function updateStatus(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $user->update([
            'status' => $request->input('status'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status do usuário atualizado com sucesso',
            'data' => new UserResource($user),
        ], 200);
    }
    public function changeRole(Request $request, User $user)
    {
        $this->authorize('changeRole', $user);

        $validated = $request->validate([
            'role' => 'required|string|in:superadmin,admin,manager,user',
        ]);

        $updatedUser = $this->userService->changeRole($user, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Função (role) do usuário atualizada com sucesso',
            'data' => new UserResource($updatedUser),
        ], 200);
    }
    public function forgotPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);
        $this->userService->sendPasswordResetEmail($request->input('email'));
        return response()->json([
            'success' => true,
            'message' => 'E-mail de redefinição enviado com sucesso',
        ], 200);
    }
    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);
        $this->userService->resetPassword($validated);
        return response()->json([
            'success' => true,
            'message' => 'Senha redefinida com sucesso',
        ], 200);
    }


    public function changePassword(Request $request, ?string $id = null)
    {
        $authUser = $request->user();

        // Se o ID não for passado, usa o próprio usuário autenticado
        $targetUser = $id ? User::findOrFail($id) : $authUser;

        try {
            $this->authorize('changePassword', $targetUser);
        } catch (AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para alterar a senha deste usuário.',
            ], 403);
        }

        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $this->userService->changePassword($targetUser->id, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Senha alterada com sucesso.',
        ], 200);
    }



    public function destroy(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não encontrado ou foi removido.'
            ], 400);
        }

        $this->authorize('destroy', $user);
        $this->userService->destroy($user->id);

        return response()->json([
            'success' => true,
            'message' => 'Sua conta foi deletada com sucesso.',
        ], 200);
    }

    public function delete(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não encontrado ou foi removido.'
            ], 400);
        }
        $user = User::findOrFail($id);
        $this->authorize('destroy', $user);

        $this->userService->delete($user->id);

        return response()->json([
            'success' => true,
            'message' => 'Usuário deletado com sucesso.',
        ], 200);
    }
    public function restore(string $id)
    {
        $user = User::withTrashed()->find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não encontrado ou foi removido.'
            ], 400);
        }

        $this->authorize('restore', $user);

        $this->userService->restore($user->id);

        return response()->json([
            'success' => true,
            'message' => 'Usuário restaurado com sucesso.',
        ], 200);
    }
}
