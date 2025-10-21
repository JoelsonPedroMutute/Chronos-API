<?php

namespace App\Services;

use App\Filters\EmployeeFilter;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class EmployeeService
{
    public function getAllFiltered(Request $request)
    {
        $query = Employee::query(); // inicializa primeiro

        if ($request->has('status')) {
            $status = $request->get('status');

            // filtra apenas employees que possuem user com esse status
            $query->whereHas('user', function ($q) use ($status) {
                $q->where('status', $status);
            });
        }

        $filter = new EmployeeFilter($query, $request);

        return $filter->apply()->paginate($request->input('per_page', 10));
    }


    public function getById(string $id, ?Employee $contextEmployee = null): Employee
    {
        $query = Employee::where('id', $id);

        if ($contextEmployee) {
            $query->where('company_id', $contextEmployee->company_id);
        }

        return $query->firstOrFail();
    }

    public function getByCompany(string $companyId): Collection
    {
        return Employee::where('company_id', $companyId)->get();
    }

    public function create(array $data): Employee
    {
        $user = Auth::user();

        if (!$user) {
            throw new Exception('Usuário não autenticado.');
        }

        if (Employee::where('email', $data['email'])->exists()) {
            throw new Exception('Email já está em uso.');
        }

        // Caso venha "name" completo
        if (!empty($data['name'])) {
            $parts = explode(' ', trim($data['name']));
            $data['first_name'] = $parts[0];
            $data['last_name'] = $parts[1] ?? null;
            unset($data['name']);
        }

        // Caso venha first_name e last_name separados (preferível)
        if (empty($data['first_name'])) {
            throw new Exception('O campo "first_name" é obrigatório.');
        }

        if (!array_key_exists('last_name', $data)) {
            $data['last_name'] = null;
        }

        // Herda automaticamente dados do usuário autenticado
        $data['role'] = $user->role ?? 'user';
        $data['user_id'] = $user->id;
        $data['company_id'] = $user->employee->company_id ?? null;

        // Campos opcionais
        $data['position'] = $data['position'] ?? null;
        $data['department'] = $data['department'] ?? null;
        $data['salary'] = $data['salary'] ?? 0;

        $employee = Employee::create($data);
        $employee->load('user', 'company', 'employeeCategory');
        return $employee;
    }




    public function update(Employee $employee, array $data): Employee
    {
        if (
            isset($data['email']) &&
            $data['email'] !== $employee->email &&
            Employee::where('email', $data['email'])->exists()
        ) {
            throw new Exception('Email já está em uso.');
        }

        $employee->update($data);
        return $employee->fresh();
    }
    public function updateStatus(Employee $employee, string $status): Employee
    {
        if ($employee->user) {
            // Atualiza a coluna 'status' do usuário
            $employee->user->status = $status;
            $employee->user->save();
        } else {
            // Atualiza o campo status do employee (ou status_manual, se preferir)
            $employee->status = $status;
            $employee->save();
        }

        return $employee->fresh();
    }
    public function updateRole(Employee $employee, string $role): Employee
    {
        if ($employee->user) {
            // Atualiza a coluna 'role' do usuário
            $employee->user->role = $role;
            $employee->user->save();
        } else {
            // Atualiza o campo role do employee (ou role_manual, se preferir)
            $employee->role = $role;
            $employee->save();
        }

        return $employee->fresh();
    }

    public function updateSettings(Employee $employee, array $settings): Employee
    {
        $currentSettings = $employee->settings ?? [];
        $employee->settings = array_merge($currentSettings, $settings);
        $employee->save();

        return $employee->fresh();
    }

    public function delete(Employee $employee): void
    {
        if ($employee->user) {
            $employee->user->delete();
        }
        $employee->delete();
    }
}
