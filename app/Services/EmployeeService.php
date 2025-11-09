<?php

namespace App\Services;

use App\Filters\EmployeeFilter;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
        $authUser = Auth::user();

        if (!$authUser) {
            throw new Exception('Usuário não autenticado.');
        }

        //  Evita duplicidade de email entre employees e users
        if (!empty($data['email'])) {
            if (Employee::where('email', $data['email'])->exists()) {
                throw new Exception('Email já está em uso por outro empregado.');
            }

            if (User::where('email', $data['email'])->exists()) {
                throw new Exception('Email já está em uso por outro usuário do sistema.');
            }
        }

        //  Verifica se o employee está vinculado a um user
        if (!empty($data['user_id'])) {
            $user = User::find($data['user_id']);

            if (!$user) {
                throw new Exception('Usuário associado não encontrado.');
            }

            // Impede associar o mesmo user a outro employee
            if (Employee::where('user_id', $data['user_id'])->exists()) {
                throw new Exception('Este usuário já está associado a outro empregado.');
            }
            $data['user_id'] = $user->id;

            // herda o role e status do user, se não vierem no payload
            $data['role'] = $data['role'] ?? $user->role;
            $data['status'] = $data['status'] ?? $user->status;

            //  Se o email não foi informado, herda o email do user
            if (empty($data['email'])) {
                $data['email'] = $user->email;
            }

            //  Se o email foi informado e for diferente do user, bloqueia
            if (!empty($data['email']) && $data['email'] !== $user->email) {
                throw new Exception('O email do empregado deve coincidir com o email do usuário associado.');
            }

            //  Atualiza o role e status do user, se vierem no payload
            $validRoles = ['superadmin', 'admin', 'manager', 'user'];

            if (!empty($data['role'])) {
                if (!in_array($data['role'], $validRoles)) {
                    throw new Exception('O cargo informado não é válido. Valores permitidos: superadmin, admin, manager, user.');
                }

                $user->role = $data['role'];
            }

            if (!empty($data['status'])) {
                $user->status = $data['status'];
            }

            $user->save();

            $data['user_id'] = $user->id;
        }


        // 🔹 2. Se tiver o campo 'name', separa em 'first_name' e 'last_name'
        if (!empty($data['name'])) {
            $parts = explode(' ', trim($data['name']), 2);
            $data['first_name'] = $parts[0];
            $data['last_name'] = $parts[1] ?? null;
            unset($data['name']);
        }

        // 🔹 3. Verifica se o hire_date é uma data futura
        if (!empty($data['hire_date'])) {
            $hireDate = Carbon::parse($data['hire_date']);
            if ($hireDate->isFuture()) {
                throw new Exception('A data de contratação não pode ser uma data futura.');
            }
        }

        // 🔹 4. Define o company_id herdado do user autenticado (caso não venha no request)
        $data['company_id'] = $data['company_id'] ?? $authUser->employee?->company_id ?? null;

        // 🔹 5. Define campos opcionais com valor padrão
        $data['phone_number'] = $data['phone_number'] ?? null;
        $data['position'] = $data['position'] ?? null;
        $data['employee_category_id'] = $data['employee_category_id'] ?? null;
        $data['address'] = $data['address'] ?? null;
        $data['department'] = $data['department'] ?? null;
        $data['salary'] = $data['salary'] ?? null;

        $user = null;

        // 🔹 6. Se 'user_id' foi informado, associa o funcionário ao usuário existente
        if (!empty($data['user_id'])) {
            $user = User::find($data['user_id']);

            if (!$user) {
                throw new Exception('Usuário associado não encontrado.');
            }

            if (Employee::where('user_id', $data['user_id'])->exists()) {
                throw new Exception('Este usuário já está associado a outro empregado.');
            }

            $data['user_id'] = $user->id;
        }

        // 🔹 7. Se 'create_user' = true, cria uma conta de acesso vinculada
        elseif (!empty($data['create_user']) && $data['create_user'] === true) {
            $validRoles = ['superadmin', 'admin', 'manager', 'user'];
            $role = in_array($data['role'] ?? '', $validRoles) ? $data['role'] : 'user';

            $user = User::create([
                'name' => trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? '')),
                'email' => $data['email'],
                'password' => Hash::make($data['password'] ?? 'defaultpassword'),
                'role' => $role,
                'status' => $data['status'] ?? 'active',
            ]);

            $data['user_id'] = $user->id;

            // mantém o role e status do employee sincronizados com o user
            $data['role'] = $user->role;
            $data['status'] = $user->status;
        }

        // 🔹 8. Caso contrário, cria apenas o empregado (sem conta de acesso)
        else {
            $data['user_id'] = null;
        }

        // 🔹 9. Cria o Employee
        $employee = Employee::create($data);

        // 🔹 10. Carrega as relações úteis
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
    public function restore(Employee $employee): Employee
    {
        if(!$employee->trashed()){
            throw new Exception('Empregado não está excluído.');
        }
        $employee->restore();
        return $employee->fresh();
    }
}
