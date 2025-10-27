<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeCategoryResource;
use Illuminate\Http\Request;
use App\Models\EmployeeCategory;
use App\Services\EmployeeCategoryService;
use App\Http\Requests\StoreEmployeeCategoryRequest;
use App\Http\Requests\UpdateEmployeeCategoryRequest;


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

    public function show(EmployeeCategory $category)
    {
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
    $this->authorize('update', $category);

    $updated = $this->employeeCategoryService->update($category, $request->validated());

    if (!$updated) {
        return response()->json([
            'success' => false,
            'message' => 'Erro ao atualizar categoria de funcionário',
        ], 500);
    }

    return response()->json([
        'success' => true,
        'message' => 'Categoria de funcionário atualizada com sucesso',
        'data' => new EmployeeCategoryResource($updated),
    ], 200);
}


    public function destroy(EmployeeCategory $category)
    {
        $this->authorize('delete', $category);

        $deleted = $this->employeeCategoryService->delete($category);

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao deletar categoria de funcionário.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Categoria de funcionário deletada com sucesso.',
        ], 200);
    }
}
