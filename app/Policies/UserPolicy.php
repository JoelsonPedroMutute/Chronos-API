<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Log;

class UserPolicy
{
    /** --------------------
     * VISUALIZAÇÃO DE USUÁRIOS
     * -------------------- */
    public function viewAny(User $authUser): bool
    {
        return in_array($authUser->role, ['superadmin', 'admin']);
    }

    public function view(User $authUser, User $model): bool
    {
        if ($authUser->hasRole('superadmin')) return true;

        if ($authUser->hasRole('admin'))
            return !$model->hasRole('superadmin');

        if (in_array($authUser->role, ['manager', 'user']))
            return $authUser->id === $model->id;

        return false;
    }

    /** --------------------
     * CRIAÇÃO E ATUALIZAÇÃO DE USUÁRIOS
     * -------------------- */
    public function create(User $authUser): bool
    {
        $requestedRole = request()->input('role', 'user');

        if ($authUser->hasRole('admin') && $requestedRole === 'superadmin')
            return false;

        return in_array($authUser->role, ['superadmin', 'admin']);
    }

    public function update(User $authUser, User $model): bool
    {
        if ($authUser->hasRole('superadmin')) return true;

        if ($authUser->hasRole('admin'))
            return !$model->hasRole('superadmin');

        if (in_array($authUser->role, ['manager', 'user']))
            return $authUser->id === $model->id;

        return false;
    }

    public function updateStatus(User $authUser, User $model): bool
    {
        if ($authUser->hasRole('superadmin')) return true;

        if ($authUser->hasRole('admin'))
            return !$model->hasRole('superadmin');

        return false;
    }

    public function changeRole(User $authUser, User $model): bool
    {
        if ($authUser->hasRole('superadmin')) return true;

        if ($authUser->hasRole('admin')) {
            $newRole = request()->input('role');
            if ($newRole === 'superadmin') return false;
            return !$model->hasRole('superadmin');
        }

        return false;
    }

    public function changePassword(User $authUser, User $model): bool
    {
        Log::info('🔐 Policy check', [
            'authUser' => $authUser->id,
            'authUserRole' => $authUser->role,
            'targetUser' => $model->id,
            'targetRole' => $model->role,
        ]);

        if ($authUser->hasRole('superadmin')) return true;

        if ($authUser->hasRole('admin'))
            return $authUser->id === $model->id || !$model->hasRole('superadmin');

        return $authUser->id === $model->id;
    }

    public function destroy(User $authUser, User $model): bool
    {
        if (request()->routeIs('users.me.destroy'))
            return $authUser->id === $model->id;

        if ($authUser->id === $model->id)
            throw new AuthorizationException('Você não pode deletar a própria conta por esta rota.');

        if ($authUser->hasRole('superadmin')) return true;

        if ($authUser->hasRole('admin'))
            return !$model->hasRole('superadmin');

        return false;
    }
     public function restore(User $authUser, User $model): bool
   {
        if ($authUser->hasRole('superadmin')) return true;

        if ($authUser->hasRole('admin'))
            return !$model->hasRole('superadmin');

        if (in_array($authUser->role, ['manager', 'user']))
            return $authUser->id === $model->id;

        return false;
    }

    /** --------------------
     * IMAGENS DE PERFIL (mesmo padrão do update/view)
     * -------------------- */

    // Ver imagem (inclui download)
    public function viewImage(User $authUser, User $model): bool
    {
        return $this->view($authUser, $model);
    }

    // Criar/atualizar/deletar imagem (todas modificações)
    public function manageImage(User $authUser, User $model): bool
    {
        if ($authUser->hasRole('superadmin')) return true;

        if ($authUser->hasRole('admin'))
            return !$model->hasRole('superadmin');

        if (in_array($authUser->role, ['manager', 'user']))
            return $authUser->id === $model->id;

        return false;
    }
}
