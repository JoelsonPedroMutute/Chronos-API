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

        return response()->json([
            'success' => true,
            'message' => 'Usuários encontrados com sucesso',
            'data' => UserResource::collection($users),
        ], 200);
    }

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
        // Tenta encontrar o usuário primeiro
        $user = User::findOrFail($id);

        // Autoriza com base no usuário autenticado e no usuário-alvo
        $this->authorize('view', $user);

        // Se passou, continua
        return response()->json([
            'success' => true,
            'message' => 'Usuário encontrado com sucesso',
            'data' => new UserResource($user),
        ], 200);
    }

    public function store(StoreUserRequest $request)
    {


        $this->authorize('create', User::class);
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
}
