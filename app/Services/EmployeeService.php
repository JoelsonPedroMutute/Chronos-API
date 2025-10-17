<?php

namespace App\Services;

use App\Filters\EmployeeFilter;
use App\Models\Employeer;
use App\Filters\EmployeerFilter;
use App\Models\Employee;
use Exception;
use Illuminate\Http\Request;
use Illumiate\Support\Facades\Log;
use Illumiate\Support\Facades\Auth;
 use Illuminate\Database\Eloquent\Collection;

class EmployeeService
{
    public function getAllFiltered(Request $request)
    {
        $query = Employee::query();
        $filter = new EmployeeFilter($query, $request);

        return $filter->apply()->paginate($request->input('per_page', 10));
    }
    public function getById(string $id, EmployeeFilter $filter): Employee
    {
        $query = Employee::where('id', $id);
        $filteredQuery = $filter->apply($query);
        return $filteredQuery->firstOrFail();
    }
    /**
     * Retorna todos os funcionários de uma determinada empresa.
     *
     * @param  string  $companyId
     * @param  EmployeeFilter  $filter
     * @return \Illuminate\Database\Eloquent\Collection<int, \App\Models\Employee>
     */
   

    public function getByCompany(string $companyId, EmployeeFilter $filter): Collection
    {
        $query = Employee::where('company_id', $companyId);
        $filteredQuery = $filter->apply($query);

        return $filteredQuery->get();
    }

    public function create(array $data): Employee
    {
        if (Employee::where('email', $data['email'])->exists()) {
            throw new Exception('Email já está em uso.');
        }
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
        $newSettings = array_merge($currentSettings, $settings);
        $employee->settings = $newSettings;
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
