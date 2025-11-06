<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class CompanyFilters extends QueryFilter
{
    protected ?string $search;
    protected ?string $email;
    protected ?string $address;
    protected ?string $nif;
    protected string $sortBy;
    protected string $sortOrder;
    protected ?int $companyId;
    protected ?bool $trashed;

    public function __construct(Builder $query, Request $request)
    {
        parent::__construct($query, $request);

        $this->search = $this->input('search');
        $this->email = $this->input('email');
        $this->address = $this->input('address');
        $this->nif = $this->input('nif');
        $this->sortBy = $this->input('sort_by', 'name');
        $this->sortOrder = $this->input('sort_order', 'asc');
        $this->companyId = $this->input('company_id');
        $this->trashed = $this->input('deleted', false);
    }

    public function apply(): Builder
    {
        return $this->applyFilters()
            ->orderBy($this->sortBy, $this->sortOrder);
    }

    protected function applyFilters(): Builder
    {
        $this->filterBySearch();
        $this->filterByCompanyId();
        $this->filterByEmail();
        $this->filterByAddress();
        $this->filterByNif();
        $this->filterByTrashed();

        return $this->query; // Retorna o builder modificado
    }

    protected function filterBySearch(): void
    {
        if ($this->search) {
            $this->query->where(function ($query) {
                $query->where('name', 'like', "%{$this->search}%")
                      ->orWhere('nif', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%")
                      ->orWhere('address', 'like', "%{$this->search}%");
            });
        }
    }

    protected function filterByCompanyId(): void
    {
        if ($this->companyId) {
            $this->query->where('company_id', $this->companyId);
        }
    }

    protected function filterByEmail(): void
    {
        if ($this->email) {
            $this->query->where('email', 'like', "%{$this->email}%");
        }
    }

    protected function filterByAddress(): void
    {
        if ($this->address) {
            $this->query->where('address', 'like', "%{$this->address}%");
        }
    }

    protected function filterByNif(): void
    {
        if ($this->nif) {
            $this->query->where('nif', 'like', "%{$this->nif}%");
        }
    }
    public function filterByTrashed(): void
    {
        if ($this->trashed) {
            $this->query->onlyTrashed();
        }
    }
}
