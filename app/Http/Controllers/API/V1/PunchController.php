<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePunchRequest;
use App\Http\Requests\UpdatePunchRequest;
use App\Http\Resources\PunchResource;
use App\Models\Punch;
use App\Services\PunchService;
use Illuminate\Http\Request;

class PunchController extends Controller
{
    protected PunchService $punchService;

    public function __construct(PunchService $punchService)
    {
        $this->punchService = $punchService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Punch::class);
        $punches = $this->punchService->getAll($request);

        if (!$punches) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhum registro encontrado.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Registros encontrados.',
            'data' => PunchResource::collection($punches),
        ], 200);
    }

    public function show($id)
{
    $punch = Punch::find($id);

    if (!$punch) {
        return response()->json([
            'success' => false,
            'message' => 'Registro não encontrado.',
            'data' => null,
        ], 404);
    }

    $this->authorize('view', $punch);

    return response()->json([
        'success' => true,
        'message' => 'Registro encontrado.',
        'data' => new PunchResource($punch),
    ], 200);
}


    public function store(StorePunchRequest $request)
    {
        $this->authorize('create', Punch::class);

        $punch = $this->punchService->create($request->validated());

        if (!$punch) {
            return response()->json([
                'success' => false,
                'message' => 'Registro não criado.',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Registro criado com sucesso.',
            'data' => new PunchResource($punch),
        ], 201);
    }

    public function update(UpdatePunchRequest $request, Punch $punch)
    {
        $this->authorize('update', $punch);

        $punch = $this->punchService->update($request->validated(), $punch);

        if (!$punch) {
            return response()->json([
                'success' => false,
                'message' => 'Registro não atualizado.',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Registro atualizado com sucesso.',
            'data' => new PunchResource($punch),
        ], 200);
    }

    public function destroy(Punch $punch)
    {
        $this->authorize('delete', $punch);

        $this->punchService->delete($punch);

        return response()->json([
            'success' => true,
            'message' => 'Registro excluído com sucesso.',
        ], 200);
    }
}