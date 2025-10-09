<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class UserFilter extends QueryFilter
{
    public function applyFilters(): Builder
    {
        $this->filterByRole();
        $this->filterByStatus();
        $this->filterByName();
        $this->filterByEmail();
        $this->filterByPhoneNumber();

        return $this->query;
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
            if (in_array($status, ['active', 'inactive'])) {
                $this->query->where('active', $status === 'active' ? 1 : 0);
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