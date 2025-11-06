<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Services\CompanyService;
use App\Models\Companies; // <-- CORRETO
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    protected $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Companies::class); // <-- CORRETO

        $companies = $this->companyService->getAll($request);

        $message = $companies->isEmpty()
            ? 'Nenhuma empresa encontrada.'
            : 'Empresas encontradas com sucesso.';

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => CompanyResource::collection($companies),
        ]);
    }

    public function show($id)
    {
        if (!$company = $this->companyService->findById($id)) {
            return response()->json([
                'success' => false,
                'message' => 'Empresa não encontrada.',
                'error' => 'ID informado não corresponde a nenhum registro.'
            ], 404);
        }

        $this->authorize('view', $company);

        return response()->json([
            'success' => true,
            'message' => 'Empresa encontrada com sucesso.',
            'data' => new CompanyResource($company),
        ]);
    }

    public function store(StoreCompanyRequest $request)
    {
        $this->authorize('create', Companies::class); // <-- CORRETO

        $company = $this->companyService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Empresa criada com sucesso.',
            'data' => new CompanyResource($company),
        ], 201);
    }

   public function update(UpdateCompanyRequest $request, $id)
{
    $company = $this->companyService->findById($id);

    if (!$company) {
        return response()->json([
            'success' => false,
            'message' => 'Empresa não encontrada.',
            'error' => 'ID informado não corresponde a nenhum registro.'
        ], 404);
    }

    $this->authorize('update', $company);

    $company = $this->companyService->update($company, $request->validated());

    return response()->json([
        'success' => true,
        'message' => 'Empresa atualizada com sucesso.',
        'data' => new CompanyResource($company),
    ]);
}



   public function destroy($id)
{
    $company = $this->companyService->findById($id);

    if (!$company) {
        return response()->json([
            'success' => false,
            'message' => 'Empresa não encontrada.',
            'error' => 'ID informado não corresponde a nenhum registro.'
        ], 404);
    }

    $this->authorize('delete', $company); // ✅ PASSA O MODELO

    if (!$this->companyService->delete($company)) {
        return response()->json([
            'success' => false,
            'message' => 'Erro ao excluir a empresa.'
        ], 400);
    }

    return response()->json([
        'success' => true,
        'message' => 'Empresa excluída com sucesso.',
    ]);
}
 public function restore($id)
{
    $company = $this->companyService->findById($id);

    if (!$company) {
        return response()->json([
            'success' => false,
            'message' => 'Empresa não encontrada.',
            'error' => 'ID informado não corresponde a nenhum registro.'
        ], 404);
    }

    $this->authorize('restore', $company); // ✅ PASSA O MODELO

    if (!$this->companyService->restore($company)) {
        return response()->json([
            'success' => false,
            'message' => 'Erro ao restaurar a empresa.'
        ], 400);
    }

    return response()->json([
        'success' => true,
        'message' => 'Empresa restaurada com sucesso.',
    ]);
}

}