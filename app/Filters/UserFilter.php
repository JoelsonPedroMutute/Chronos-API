<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class UserFilter extends QueryFilter
{
    /**
     * Aplica todos os filtros e ordenação
     */
    protected function applyFilters(): Builder
    {
        $this->applySearchFilter();
        $this->applyEmailFilter();
        $this->applyPhoneNumberFilter();
        $this->applyRoleFilter();
        $this->applyStatusFilter();
        $this->applyNameFilter();
        $this->applyTrashedFilter();
        $this->applyRoleRestrictionFilter();
        $this->applySorting();

        return $this->query;
    }

    protected function applySearchFilter(): void
    {
        $search = $this->input('search');
        if ($search) {
            $this->query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }
    }

    protected function applyEmailFilter(): void
    {
        $email = $this->input('email');
        if ($email) {
            $this->addLike('email', $email);
        }
    }

    protected function applyPhoneNumberFilter(): void
    {
        $phoneNumber = $this->input('phone_number');
        if ($phoneNumber) {
            $this->addLike('phone_number', $phoneNumber);
        }
    }

    protected function applyRoleFilter(): void
    {
        $role = $this->input('role');
        if ($role) {
            $this->addWhere('role', $role);
        }
    }

    protected function applyStatusFilter(): void
    {
        $status = $this->input('status');
        if ($status && in_array($status, ['active', 'inactive', 'pending'])) {
            $this->addWhere('status', $status);
        }
    }

    protected function applyNameFilter(): void
    {
        $name = $this->input('name');
        if ($name) {
            $this->addLike('name', $name);
        }
    }

    protected function applyRoleRestrictionFilter(): void
    {
        $roleRestriction = $this->input('role_restriction');
        if ($roleRestriction) {
            $this->addWhere('role', $roleRestriction);
        }
    }

    protected function applyTrashedFilter(): void
    {
        $trashed = $this->input('trashed');
        if ($trashed) {
            if ($trashed === 'only') {
                $this->query->onlyTrashed();
            } elseif ($trashed === 'with') {
                $this->query->withTrashed();
            }
        }
    }

    protected function applySorting(): void
    {
        $sortBy = $this->input('sort_by', 'name');
        $sortOrder = $this->input('sort_order', 'asc');
        
        $validSortColumns = ['id', 'name', 'email', 'created_at', 'updated_at'];
        $sortBy = in_array($sortBy, $validSortColumns) ? $sortBy : 'name';
        
        $this->query->orderBy($sortBy, $sortOrder);
    }
}