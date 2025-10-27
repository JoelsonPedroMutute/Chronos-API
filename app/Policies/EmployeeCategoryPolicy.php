<?php

namespace App\Policies;

use App\Models\User;
use App\Models\EmployeeCategory;

class EmployeeCategoryPolicy
{
    /**
     * Ver todas as categorias
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['superadmin', 'admin', 'manager']);
    }

    /**
     * Ver uma categoria específica
     */
    public function view(User $user, EmployeeCategory $employeeCategory): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        if ($user->hasRole('admin')) {
            // Admin pode ver categorias dentro da empresa dele
            return $user->employee &&
                   $user->employee->company_id === $employeeCategory->company_id;
        }

        if (in_array($user->role, ['manager', 'user'])) {
            // Manager e User só podem ver sua própria categoria
            return $user->employee &&
                   $user->employee->employee_category_id === $employeeCategory->id;
        }

        return false;
    }

    /**
     * Criar categoria
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['superadmin', 'admin']);
    }

    /**
     * Atualizar categoria
     */
    public function update(User $user, EmployeeCategory $employeeCategory): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        if ($user->hasRole('admin')) {
            // Admin pode editar categorias da sua própria empresa
            return $user->employee &&
                   $user->employee->company_id === $employeeCategory->company_id;
        }

        return false; // Managers e Users não podem editar categorias
    }

    /**
     * Deletar categoria
     */
    public function delete(User $user, EmployeeCategory $employeeCategory): bool
    {
        // Apenas superadmin pode deletar categorias
        return $user->hasRole('superadmin');
    }
}
