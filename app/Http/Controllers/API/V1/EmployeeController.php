<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use App\Services\EmployeeService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class EmployeeController extends Controller
{
    protected EmployeeService $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Employee::class);
        $employees = $this->employeeService->getAllFiltered($request);

        $message = $employees->isEmpty()
            ? 'Nenhum empregado encontrado para o filtro aplicado.'
            : 'Empregados encontrados com sucesso.';

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => EmployeeResource::collection($employees),
        ], 200);
    }

    public function profile(Request $request)
    {
        $employee = $request->user()->employee;

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não possui perfil de empregado vinculado.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Perfil do empregado encontrado com sucesso.',
            'data' => new EmployeeResource($employee->load(['user', 'company'])),
        ], 200);
    }


    public function show(Request $request, $id)
    {
        try {
            // Busca direta e segura
            $employee = Employee::findOrFail($id);

            $this->authorize('view', $employee);

            return response()->json([
                'success' => true,
                'message' => 'Empregado encontrado com sucesso.',
                'data' => new EmployeeResource($employee),
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Empregado não encontrado.',
            ], 404);
        } catch (AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ação não autorizada.',
            ], 403);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro interno no servidor.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getEmployeeByCompany(Request $request, $id)
    {
        try {
            $employees = $this->employeeService->getByCompany($id);

            $message = $employees->isEmpty()
                ? 'Nenhum empregado encontrado para a empresa informada.'
                : 'Empregados encontrados com sucesso.';

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => EmployeeResource::collection($employees),
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Empresa não encontrada.',
            ], 404);
        }
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $this->authorize('create', Employee::class);

        $employee = $this->employeeService->create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Empregado criado com sucesso.',
            'data' => new EmployeeResource($employee),
        ], 201);
    }

    
    public function update(Request $request, Employee $employee)
    {
        $this->authorize('update', $employee);
        $updatedEmployee = $this->employeeService->update($employee, $request->all());

        return response()->json([
            'success' => true,
            'message' => 'Empregado atualizado com sucesso.',
            'data' => new EmployeeResource($updatedEmployee),
        ], 200);
    }
    public function updateStatus(Request $request, Employee $employee)
    {
        $this->authorize('update', $employee);

        $validated = $request->validate([
            'status' => 'required|string|in:active,inactive',
        ]);

        $employee = $this->employeeService->updateStatus($employee, $validated['status']);

        return response()->json([
            'success' => true,
            'message' => 'Status do empregado atualizado com sucesso.',
            'data' => new EmployeeResource($employee),
        ], 200);
    }
    public function updateRole(Request $request, Employee $employee)
    {
        $this->authorize('update', $employee);

        $validated = $request->validate([
            'role' => 'required|string|in:superadmin,admin,manager,user',
        ]);

        $employee = $this->employeeService->updateRole($employee, $validated['role']);

        return response()->json([
            'success' => true,
            'message' => 'Função do empregado atualizada com sucesso.',
            'data' => new EmployeeResource($employee),
        ], 200);
    }

    public function updateSettings(Request $request, Employee $employee)
    {
        $this->authorize('update', $employee);

        $validated = $request->validate([
            'settings' => 'required|array',
        ]);

        $employee = $this->employeeService->updateSettings($employee, $validated['settings']);

        return response()->json([
            'success' => true,
            'message' => 'Configurações atualizadas com sucesso.',
            'data' => new EmployeeResource($employee),
        ], 200);
    }

    public function destroy(Request $request, string $id)
    {
        // 1. Se o ID nem for um UUID válido → já retorna
        if (!Str::isUuid($id)) {
            return response()->json([
                'success' => false,
                'message' => 'Empregado não encontrado.'
            ], 404);
        }

        // 2. Busca segura (inclui registros apagados)
        $employee = Employee::withTrashed()->where('id', $id)->first();

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Empregado não encontrado.'
            ], 404);
        }

        $this->authorize('delete', $employee);

        $this->employeeService->delete($employee);

        return response()->json([
            'success' => true,
            'message' => 'Empregado removido com sucesso.',
        ], 200);
    }

    public function restore(Request $request, string $id)
{
    if (!Str::isUuid($id)) {
        return response()->json([
            'success' => false,
            'message' => 'Empregado não encontrado.'
        ], 404);
    }

    $employee = Employee::withTrashed()->where('id', $id)->first();

    if (!$employee) {
        return response()->json([
            'success' => false,
            'message' => 'Empregado não encontrado.'
        ], 404);
    }

    $this->authorize('restore', $employee);
    $employee = $this->employeeService->restore($employee);

    return response()->json([
        'success' => true,
        'message' => 'Empregado restaurado com sucesso.',
        'data' => new EmployeeResource($employee),
    ], 200);
}
}
