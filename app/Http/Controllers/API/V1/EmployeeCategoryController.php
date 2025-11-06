<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeCategoryResource;
use Illuminate\Http\Request;
use App\Models\EmployeeCategory;
use App\Services\EmployeeCategoryService;
use App\Http\Requests\StoreEmployeeCategoryRequest;
use App\Http\Requests\UpdateEmployeeCategoryRequest;
use Gate;
use Str;


class EmployeeCategoryController extends Controller
{
    protected $employeeCategoryService;

    public function __construct(EmployeeCategoryService $employeeCategoryService)
    {
        $this->employeeCategoryService = $employeeCategoryService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', EmployeeCategory::class);

        $categories = $this->employeeCategoryService->getAll($request);

        $message = $categories->isEmpty()
            ? $message = 'Nenhuma categoria de funcionário encontrada.'
            : $message = 'Categorias de funcionário encontradas com sucesso.';


        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => EmployeeCategoryResource::collection($categories),
        ]);
    }

    public function show($id)
    {
        if (!Str::isUuid($id)) {
            return response()->json([
                'success' => false,
                'message' => 'ID inválido. Deve ser um UUID.',
            ], 400);
        }

        $category = EmployeeCategory::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Categoria de funcionário não encontrada.',
            ], 404);
        }

        $this->authorize('view', $category);

        return response()->json([
            'success' => true,
            'message' => 'Categoria de funcionário encontrada com sucesso.',
            'data' => new EmployeeCategoryResource($category),
        ], 200);
    }
    public function store(StoreEmployeeCategoryRequest $request)
    {
        $this->authorize('create', EmployeeCategory::class);

        $category = $this->employeeCategoryService->create($request->validated());

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar categoria de funcionário',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Categoria de funcionário criada com sucesso',
            'data' => new EmployeeCategoryResource($category),
        ], 201);
    }


    public function update(UpdateEmployeeCategoryRequest $request, EmployeeCategory $category)
    {
        // Autorização
        if (!$this->authorize('update', $category)) {
            return response()->json([
                'success' => false,
                'message' => 'Ação não autorizada.',
                'error' => 'Você não possui permissão para atualizar esta categoria.'
            ], 403);
        }

        // Atualiza
        $updated = $this->employeeCategoryService->update($category, $request->validated());

        // Caso o serviço retorne null ou falso
        if (!$updated) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar categoria de funcionário.',
                'error' => 'Falha interna ao atualizar os dados no banco.'
            ], 500);
        }

        // Sucesso
        return response()->json([
            'success' => true,
            'message' => 'Categoria de funcionário atualizada com sucesso.',
            'data' => new EmployeeCategoryResource($updated),
        ], 200);
    }
    public function destroy($categoryId)
{
    $category = EmployeeCategory::find($categoryId);

    if (!$category) {
        return response()->json([
            'success' => false,
            'message' => 'Categoria de funcionário não encontrada.',
            'error' => 'UUID informado não corresponde a nenhum registro.'
        ], 404);
    }

    // Autorização
    if (!Gate::allows('delete', $category)) {
        return response()->json([
            'success' => false,
            'message' => 'Ação não autorizada.',
            'error' => 'Você não possui permissão para deletar esta categoria.'
        ], 403);
    }

    // Deleta
    $deleted = $category->delete();

    if (!$deleted) {
        return response()->json([
            'success' => false,
            'message' => 'Erro ao deletar categoria de funcionário.',
            'error' => 'Falha interna ao remover os dados no banco.'
        ], 500);
    }

    return response()->json([
        'success' => true,
        'message' => 'Categoria de funcionário deletada com sucesso.',
    ], 200);
}

}
