<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class EmployeeCategoryFilter extends QueryFilter
{
    protected ?string $search;
    protected string $sortBy;
    protected string $sortOrder;
    protected int $perPage;
    protected ?int $companyId;

    public function __construct(Builder $query, Request $request)
    {
        parent::__construct($query, $request);

        $this->search = $this->input('search');
        $this->sortBy = $this->input('sort_by', 'name');
        $this->sortOrder = $this->input('sort_order', 'asc');
        $this->perPage = (int) $this->input('per_page', 10);
        $this->companyId = $this->input('company_id');
    }

    protected function applyFilters(): Builder
    {
        $this->filterByName();
        $this->filterByCode();
        $this->filterByCompanyId();

        return $this->query->orderBy($this->sortBy, $this->sortOrder);
    }

    protected function filterByName(): void
    {
        if ($this->search) {
            $this->query->where('name', 'like', "%{$this->search}%");
        }
    }

    protected function filterByCode(): void
    {
        if ($this->search) {
            $this->query->orWhere('code', 'like', "%{$this->search}%");
        }
    }

    protected function filterByCompanyId(): void
    {
        if ($this->companyId) {
            $this->query->where('company_id', $this->companyId);
        }
    }
}
