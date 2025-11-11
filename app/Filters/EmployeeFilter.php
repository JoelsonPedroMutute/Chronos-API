<?php

namespace App\Filters;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;



class EmployeeFilter extends QueryFilter
{
    protected ?string $search;
    protected ?string $sortBy;
    protected ?string $sortOrder;
    protected ?string $firstName;
    protected ?string $lastName;
    protected ?string $email;
    protected ?string $phoneNumber;
    protected ?string $position;
    protected ?string $companyId;
    protected ?string $employeeCategoryId;
    protected ?string $status;
    protected ?string $role;
    protected ?string $trashed;

    public function __construct(Builder $query, Request $request)
    {
        parent::__construct($query, $request);

        $this->search = $this->input('search');
        $this->sortBy = $this->input('sort_by', 'name');
        $this->sortOrder = $this->input('sort_order', 'asc');
        $this->firstName = $this->input('first_name');
        $this->lastName = $this->input('last_name');
        $this->email = $this->input('email');
        $this->phoneNumber = $this->input('phone_number');
        $this->position = $this->input('position');
        $this->companyId = $this->input('company_id');
        $this->employeeCategoryId = $this->input('employee_category_id');
        $this->status = $this->input('status');
        $this->role = $this->input('role');
        $this->trashed = $this->input('trashed');
    }

    public function apply(): Builder
    {
        return $this->applyFilters()->orderBy($this->sortBy, $this->sortOrder);
    }

    public function applyFilters(): Builder
    {
        $this->filterByFirstName();
        $this->filterByLastName();
        $this->filterByEmail();
        $this->filterByPhoneNumber();
        $this->filterByPosition();
        $this->filterByCompanyId();
        $this->filterByEmployeeCategoryId();
        $this->filterByStatus();
        $this->filterByRole();
        $this->filterByTrashed(); // agora retorna Builder

        return $this->query;
    }


    public function filterBySearch(): void
    {
        if ($this->search) {
            $this->query->where(function ($query) {
                $query->where('first_name', 'like', "%{$this->search}%")
                    ->orWhere('last_name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('phone_number', 'like', "%{$this->search}%")
                    ->orWhere('position', 'like', "%{$this->search}%");
            });
        }
    }
    protected function setSorting(): void
    {
        $this->sortBy = $this->input('sort_by', 'name');
        $this->sortOrder = $this->input('sort_order', 'asc');
    }
    protected function filterByEmail(): void
    {
        if ($this->email) {
            $this->query->where('email', 'like', "%{$this->email}%");
        }
    }
    protected function filterByFirstName(): void
    {
        if ($this->firstName) {
            $this->query->where('first_name', 'like', "%{$this->firstName}%");
        }
    }
    protected function filterByLastName(): void
    {
        if ($this->lastName) {
            $this->query->where('last_name', 'like', "%{$this->lastName}%");
        }
    }
    protected function filterByPhoneNumber(): void
    {
        if ($this->phoneNumber) {
            $this->query->where('phone_number', 'like', "%{$this->phoneNumber}%");
        }
    }
    protected function filterByPosition(): void
    {
        if ($this->position) {
            $this->query->where('position', 'like', "%{$this->position}%");
        }
    }
    protected function filterByCompanyId(): void
    {
        if ($this->companyId) {
            $this->query->where('company_id', $this->companyId);
        }
    }
    protected function filterByEmployeeCategoryId(): void
    {
        if ($this->employeeCategoryId) {
            $this->query->where('employee_category_id', $this->employeeCategoryId);
        }
    }
    protected function filterByStatus(): void
    {
        if ($this->status) {
            $this->query->where('status', $this->status);
        }
    }
    protected function filterByRole(): void
    {
        if ($this->role) {
            $this->query->where('role', $this->role);
        }
    }

    protected function filterByTrashed(): Builder
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

        return $this->query;
    }
}
