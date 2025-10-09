<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

abstract class QueryFilter
{
    protected Builder $query;
    protected Request $request;

    public function __construct(Builder $query, Request $request)
    {
        $this->query = $query;
        $this->request = $request;
    }

    /**
     * Aplica os filtros concretos definidos nas subclasses.
     */
    abstract protected function applyFilters(): Builder; 

    /**
     * Método principal que chama os filtros.
     */
    public function apply(): Builder
    {
        return $this->applyFilters();
    }

    /**
     * Recupera um valor do request.
     */
    protected function input(?string $key = null, $default = null)
    {
        return $this->request->get($key, $default);
    }

    /**
     * Helpers para construir consultas dinâmicas.
     */
    protected function addWhere(string $column, $value): void
    {
        if ($value !== null) {
            $this->query->where($column, $value);
        }
    }

    protected function addLike(string $column, $value): void
    {
        if ($value !== null) {
            $this->query->where($column, 'like', "%{$value}%");
        }
    }

    protected function addIn(string $column, array $values): void
    {
        if (!empty($values)) {
            $this->query->whereIn($column, $values);
        }
    }
}