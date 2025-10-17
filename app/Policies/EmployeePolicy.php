<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class EmployeePolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['superadmin', 'admin']);
    }

    public function view(User $user, Employee $employee): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        if ($user->hasRole('admin')) {
            return $user->employee &&
                $employee->company_id === $user->employee->company_id;
        }
         if(in_array($user->role, ['manager', 'user'])) {
        return $user->employee &&
            $employee->id === $user->employee->id;
    }
    return false;
    }
    public function create(User $user): bool
    {
        return in_array($user->role, ['superadmin', 'admin']);
    }
    public function update(User $user, Employee $employee): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        if ($user->hasRole('admin')) {
            return $user->employee &&
                $employee->company_id === $user->employee->company_id;
        }
         if(in_array($user->role, ['manager', 'user'])) {
        return $user->employee &&
            $employee->id === $user->employee->id;
    }
    return false;
    }
    public function delete(User $user, Employee $employee): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        if ($user->hasRole('admin')) {
            return $user->employee &&
                $employee->company_id === $user->employee->company_id;
        }

        return false;
    }

    /**
     * --------------------
     * Permissão funcional baseada na categoria do empregado
     * --------------------
     */

    /**
     * Regras para trabalhar com a imagem do empregado
     */

    public function manageImage(User $user, Employee $employee): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        if ($user->hasRole('admin')) {
            return $user->employee &&
                $employee->company_id === $user->employee->company_id;
        }
         if(in_array($user->role, ['manager', 'user'])) {
        return $user->employee &&
            $employee->id === $user->employee->id;
    }
    return false;
}
        
}    
