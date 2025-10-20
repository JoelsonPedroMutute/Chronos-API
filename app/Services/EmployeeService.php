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
        $query = Employee::query();
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

    // Herda role e empresa do criador
    $data['role'] = $user->role ?? 'user';
    $data['user_id'] = $user->id;
    $data['company_id'] = $user->employee->company_id ?? null;

    return Employee::create($data);
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
