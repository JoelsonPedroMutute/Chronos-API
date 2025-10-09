<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function viewAny(User $authUser): bool
    {
        return in_array($authUser->role, ['superadmin', 'admin']);
    }

    public function view(User $authUser, User $model): bool
    {
        if ($authUser->hasRole('superadmin')) {
            return true;
        }

        if ($authUser->hasRole('admin')) {
            return !$model->hasRole('superadmin');
        }

        if (in_array($authUser->role, ['manager', 'user'])) {
            return $authUser->id === $model->id;
        }

        return false;
    }
     public function create(User $authUser): bool
     {
        return in_array($authUser->role, ['superadmin', 'admin']);
     }
      public function update(User $authUser, User $model): bool
      {
        if ($authUser->hasRole('superadmin')) {
            return true;
        }

        if ($authUser->hasRole('admin')) {
            return !$model->hasRole('superadmin');
        }

        if (in_array($authUser->role, ['manager', 'user'])) {
            return $authUser->id === $model->id;
        }

        return false;
      }
       public function updateStatus(User $authUser, User $model): bool
       {
        if($authUser->hasRole('superadmin')){
            return true;
        }

        if($authUser->hasRole('admin')){
            return !$model->hasRole('superadmin');
        }
        return false;
       }
}