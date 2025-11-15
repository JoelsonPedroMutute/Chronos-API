<?php

namespace App\Policies;

use App\Models\Punch;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PunchPolicy
{
    /**
     * Create a new policy instance.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['superadmin', 'admin']);
    }

    public function view(User $user, Punch $punch): bool
    {
        if($user->hasRole('superadmin')) {
            return true;
        }

        if ($user->hasRole('admin')) {
            return $user->company_id === $punch->company_id;
        }

        if (in_array($user->role, ['manager', 'user'])) {
            return $user->company_id === $punch->company_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['superadmin', 'admin']);
    }

    public function update(User $user, Punch $punch): bool    
    {
        // Superadmin pode atualizar qualquer Punch
        if ($user->hasRole('superadmin')) {
            return true;
        }

        // Admin só pode atualizar Punch da mesma empresa
        if ($user->hasRole('admin')) {
            return $user->company_id === $punch->company_id;
        }

        // Demais papéis não podem atualizar punches
        return false;
    }

    public function delete(User $user, Punch $punch): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        if ($user->hasRole('admin')) {
            // Admin pode deletar punches dentro da empresa dele
            return $user->company_id === $punch->company_id;
        }

        return false; // Managers e Users não podem deletar punches
    }

    public function restore(User $user, Punch $punch): bool
    {
        if ($user->hasRole('superadmin')) {
            return true;
        }

        if ($user->hasRole('admin')) {
            // Admin pode restaurar punches dentro da empresa dele
            return $user->company_id === $punch->company_id;
        }

        return false; // Managers e Users não podem restaurar punches
    }
}