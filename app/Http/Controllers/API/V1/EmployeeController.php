<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use App\Services\EmployeerService;
use App\Services\EmployeeService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

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
            ? 'Nenhum empregado encontrado para o filtro aplicado'
            : 'Empregados encontrados com sucesso';

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => EmployeeResource::collection($employees),
        ], 200);
    }
    public function profile(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Perfil do empregado encontrado com sucesso',
            'data' => new EmployeeResource($request->user()->employee),
        ], 200);
    }
    public function show(Request $request, $id)
    {
        try {
            $employee = $this->employeeService->getById($id, $request->user());

            $this->authorize('view', $employee);

            return response()->json([
                'success' => true,
                'message' => 'Empregado encontrado com sucesso',
                'data' => new EmployeeResource($employee),
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Empregado não encontrado',
            ], 404);
        } catch (AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ação não autorizada',
            ], 403);
        }
    }
    public function getEmployeeByCompany(Request $request, $id)
    {
        try {
            $employee = $this->employeeService->getByCompany($id, $request->user());

            $message = $employee->isEmpty()
                ? 'Nenhum empregado encontrado para o filtro aplicado'
                : 'Empregados encontrados com sucesso';
            $employee = EmployeeResource::collection($employee);

            $this->authorize('view', $employee);

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $employee,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Empregado não encontrado',
            ], 404);
        } catch (AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ação não autorizada',
            ], 403);
        }
    }

    public function store(Request $request)
    {
        if (!$request->user()->employee || !$request->user()->employee->hasRole('manager')) {
            return response()->json([
                'success' => false,
                'message' => 'Ação não autorizada',
            ], 403);
        }

        $employee = $this->employeeService->create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Empregado criado com sucesso',
            'data' => new EmployeeResource($employee),
        ], 201);
    }

    public function update(Request $request, Employee $employee)
    {
        $this->authorize('update', $employee);

        $updatedEmployee = $this->employeeService->update($employee, $request->all());

        return response()->json([
            'success' => true,
            'message' => 'Empregado atualizado com sucesso',
            'data' => new EmployeeResource($updatedEmployee),
        ], 200);
    }
    public function updatePosition(Request $request, Employee $employee)
    {
        $this->authorize('update', $employee);

        $validated = $request->validate([
            'position' => 'required|string|max:255',
        ]);

        $employee->update([
            'position' => $validated['position'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cargo atualizado com sucesso.',
            'data' => $employee,
        ], 200);
    }
    public function updateSettings(Request $request, Employee $employee)
    {
        $this->authorize('update', $employee);

        $validated = $request->validate([
            'settings' => 'required|array',
        ]);

        $employee->update([
            'settings' => $validated['settings'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Configurações atualizadas com sucesso.',
            'data' => $employee,
        ], 200);
    }

    public function destroy(Request $request)
    {
        $employee = $request->user()->employee;

        $this->authorize('delete', $employee);

        $this->employeeService->delete($employee);

        return response()->json([
            'success' => true,
            'message' => 'Empregado removido com sucesso',
        ], 200);
    }
}
