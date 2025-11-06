<?php

namespace App\Policies;

use App\Models\Companies;
use App\Models\User;

class CompanyPolicy
{
    /**
     * Create a new policy instance.
     */
   public function viewAny(User $user): bool
    {
        return in_array($user->role, ['superadmin', 'admin']);
    }

    public function view(User $user, Companies $company): bool
    {
           if($user->hasRole('superadmin')) {
            return true;
        }

        if ($user->hasRole('admin')) {
            // Admin pode ver empresas dentro da empresa dele
            return $user->employee &&
                   $user->employee->company_id === $company->id;
        }

        if (in_array($user->role, ['manager', 'user'])) {
            // Manager e User só podem ver sua própria empresa
            return $user->employee &&
                   $user->employee->company_id === $company->id;
        }

        return false;
    }
    public function create(User $user): bool
    {
         return in_array($user->role, ['superadmin', 'admin']);
    }

  public function update(User $user, Companies $company): bool
{
    // Superadmin pode atualizar qualquer empresa
    if ($user->hasRole('superadmin')) {
        return true;
    }

    // Admin só pode atualizar a empresa à qual pertence
    if ($user->hasRole('admin')) {
        return $user->employee &&
               $user->employee->company_id === $company->id;
    }

    // Demais papéis não podem atualizar empresas
    return false;
}


    public function delete(User $user, Companies $company): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        if ($user->hasRole('admin')) {
            // Admin pode deletar empresas dentro da empresa dele
            return $user->company &&
                   $user->company->id === $company->id;
        }

        return false; // Managers e Users não podem deletar empresas
    }
    public function restore(User $user, Companies $company): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        if ($user->hasRole('admin')) {
            // Admin pode restaurar empresas dentro da empresa dele
            return $user->company &&
                   $user->company->id === $company->id;
        }

        return false; // Managers e Users não podem restaurar empresas
    }
}  
    

