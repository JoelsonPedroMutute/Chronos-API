<?php

namespace App\Filters;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Builder;

class EmployeeFilter extends QueryFilter
{
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

        return $this->query;
    }

    protected function filterByFirstName(): void
    {
        if ($firstName = $this->input('first_name')) {
            $this->addLike('first_name', $firstName);
        }
    }

    protected function filterByLastName(): void
    {
        if ($lastName = $this->input('last_name')) {
            $this->addLike('last_name', $lastName);
        }
    }
    protected function filterByEmail(): void
    {
        if ($email = $this->input('email')) {
            $this->addLike('email', $email);
        }
    }
    protected function filterByPhoneNumber(): void
    {
        if ($phoneNumber = $this->input('phone_number')) {
            $this->addLike('phone_number', $phoneNumber);
        }
    }   
    protected function filterByPosition(): void
    {
        if ($position = $this->input('position')) {
            $this->addLike('position', $position);
        }
    }
    protected function filterByCompanyId(): void
    {
        if ($companyId = $this->input('company_id')) {
            $this->query->where('company_id', $companyId);
        }
    }
    protected function filterByEmployeeCategoryId(): void
    {
        if ($employeeCategoryId = $this->input('employee_category_id')) {
            $this->query->where('employee_category_id', $employeeCategoryId);
        }
    }
    protected function filterByStatus(): void
    {
        if ($status = $this->input('status')) {
            $this->query->where('status', $status);
        }
    }
    protected function filterByRole(): void
    {
        if ($role = $this->input('role')) {
            $this->query->whereHas('user', function ($query) use ($role) {
                $query->where('role', $role);
            });
        }
    }
}