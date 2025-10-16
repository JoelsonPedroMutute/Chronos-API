<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class UserFilter extends QueryFilter
{
    public function applyFilters(): Builder
    {
        $this->applyRoleRestrictions();
        $this->filterByRole();
        $this->filterByStatus();
        $this->filterByName();
        $this->filterByEmail();
        $this->filterByPhoneNumber();

        return $this->query;
    }

    protected function applyRoleRestrictions(): void
{
    $authUser = $this->request->user();

    Log::info('Filtro de role aplicado', [
        'authUser' => $authUser ? $authUser->role : 'guest',
    ]);

    if (!$authUser) {
        return;
    }

    switch ($authUser->role) {
        case 'admin':
            Log::info('Admin filtrando usuários: excluindo superadmins');
            $this->query->where('role', '!=', 'superadmin');
            break;

        case 'manager':
        case 'user':
            Log::info('Manager/User filtrando: somente ele mesmo');
            $this->query->where('id', $authUser->id);
            break;
    }
}
    protected function filterByRole(): void
    {
        if ($role = $this->input('role')) {
            $this->query->where('role', $role);
        }
    }

    protected function filterByStatus(): void
    {
        if ($status = $this->input('status')) {
            if (in_array($status, ['active', 'inactive', 'pending'])) {
                $this->query->where('status', $status);
            }
        }
    }

    protected function filterByName(): void
    {
        if ($name = $this->input('name')) {
            $this->query->where('name', 'like', "%{$name}%");
        }
    }

    protected function filterByEmail(): void
    {
        if ($email = $this->input('email')) {
            $this->query->where('email', 'like', "%{$email}%");
        }
    }

    protected function filterByPhoneNumber(): void
    {
        if ($phoneNumber = $this->input('phone_number')) {
            $this->query->where('phone_number', 'like', "%{$phoneNumber}%");
        }
    }
}
