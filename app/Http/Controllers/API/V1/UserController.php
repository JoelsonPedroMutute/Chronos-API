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
            'data'=> new UserResource($request->user()),
        ],200);
    }
}