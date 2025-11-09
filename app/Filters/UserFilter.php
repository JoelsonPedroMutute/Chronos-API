<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;


class UserFilter extends QueryFilter
{
    protected ?string $search;
    protected ?string $email;
    protected ?string $phoneNumber;
    protected ?string $trashed;
    protected ?string $role;
    protected ?string $status;
    protected ?string $name;
    protected ?string $sortBy;
    protected ?string $sortOrder;
    protected ?string $roleRestriction;

    public function __construct(Builder $query, Request $request)
    {
        parent::__construct($query, $request);

        $this->search = $this->input('search');
        $this->email = $this->input('email');
        $this->phoneNumber = $this->input('phone_number');
        $this->role = $this->input('role');
        $this->status = $this->input('status');
        $this->name = $this->input('name');
        $this->trashed = $this->input('trashed');
        $this->sortBy = $this->input('sort_by', 'name');
        $this->sortOrder = $this->input('sort_order', 'asc');
        $this->roleRestriction = $this->input('role_restriction');
    }
    public function apply(): Builder
    {
        return $this->applyFilters()->orderBy($this->sortBy, $this->sortOrder);
    }

    protected function applyFilters(): Builder
    {
        $this->filterBySearch();
        $this->filterByRole();
        $this->filterByStatus();
        $this->filterByName();
        $this->filterByEmail();
        $this->filterByPhoneNumber();
        $this->filterByDeleted();
        $this->filterByRoleRestriction();

        return $this->query;
    }

    protected function filterBySearch(): void
    {
        if ($search = $this->input('search')) {
            $this->query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }
    }

    protected function setSorting(): void
    {
        $this->sortBy = $this->input('sort_by', 'id');
        $this->sortOrder = $this->input('sort_order', 'asc');
    }

    protected function filterByRoleRestriction(): void
    {
        if ($roleRestriction = $this->input('role_restriction')) {
            $this->query->where('role', $roleRestriction);
        }
    }

    protected function filterByEmail(): void
    {
        if ($email = $this->input('email')) {
            $this->query->where('email', 'like', "%{$email}%");
        }
    }

    protected function filterByName(): void
    {
        if ($name = $this->input('name')) {
            $this->query->where('name', 'like', "%{$name}%");
        }
    }

    protected function filterByPhoneNumber(): void
    {
        if ($phoneNumber = $this->input('phone_number')) {
            $this->query->where('phone_number', 'like', "%{$phoneNumber}%");
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
     protected function filterByRole(): void
    {
        if ($role = $this->input('role')) {
            $this->query->where('role', $role);
        }
    }
    protected function filterByDeleted(): self
    {
        if ($this->request->filled('trashed')) {
            if ($this->request->trashed === 'only') {
                $this->query->onlyTrashed();
            } elseif ($this->request->trashed === 'with') {
                $this->query->withTrashed();
            } else {
                $this->query->withoutTrashed();
            }
        } else {
            $this->query->withoutTrashed();
        }
        return $this;
    }
}
