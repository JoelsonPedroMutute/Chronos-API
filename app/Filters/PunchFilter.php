<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class PunchFilter extends QueryFilter
{
    protected function applyFilters(): Builder
    {
        $this->applySearchFilter();
        $this->applyTypeFilter();
        $this->applyPunchTimeFilter();
        $this->applyPunchTimeRangeFilter();
        $this->applyAutoClosedFilter();
        $this->applyExtraTimeFilter();
        $this->applyNoteFilter();
        $this->applyEmployeeIdFilter();
        $this->applyCompanyIdFilter();
        $this->applyCreatedAtFilter();
        $this->applyCreatedAtRangeFilter();
        $this->applyUpdatedAtFilter();
        $this->applyUpdatedAtRangeFilter();
        $this->applySortFilter();

        return $this->query;
    }

    protected function applySearchFilter(): void
    {
        if ($search = $this->input('search')) {
            $this->query->where(function ($query) use ($search) {
                $query->where('type', 'like', "%{$search}%")
                    ->orWhere('note', 'like', "%{$search}%")
                    ->orWhereHas('employee', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }
    }

    protected function applyTypeFilter(): void
    {
        if ($type = $this->input('type')) {
            $this->query->where('type', $type);
        }
    }

    protected function applyPunchTimeFilter(): void
    {
        if ($punchTime = $this->input('punch_time')) {
            $this->query->whereDate('punch_time', $punchTime);
        }
    }

    protected function applyPunchTimeRangeFilter(): void
    {
        if ($startDate = $this->input('punch_time_start')) {
            $this->query->where('punch_time', '>=', $startDate);
        }

        if ($endDate = $this->input('punch_time_end')) {
            $this->query->where('punch_time', '<=', $endDate);
        }
    }

    protected function applyAutoClosedFilter(): void
    {
        if ($this->input('auto_closed') !== null) {
            $autoClosed = filter_var($this->input('auto_closed'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($autoClosed !== null) {
                $this->query->where('auto_closed', $autoClosed);
            }
        }
    }

    protected function applyExtraTimeFilter(): void
    {
        if ($this->input('extra_time') !== null) {
            $extraTime = $this->input('extra_time');
            
            if ($extraTime === 'true' || $extraTime === '1') {
                $this->query->where('extra_time', '>', 0);
            } elseif ($extraTime === 'false' || $extraTime === '0') {
                $this->query->where(function($q) {
                    $q->whereNull('extra_time')
                      ->orWhere('extra_time', '=', 0);
                });
            } else {
                $this->query->where('extra_time', $extraTime);
            }
        }
    }

    protected function applyNoteFilter(): void
    {
        if ($note = $this->input('note')) {
            $this->query->where('note', 'like', "%{$note}%");
        }

        // Filtrar registros COM ou SEM nota
        if ($this->input('has_note') !== null) {
            $hasNote = filter_var($this->input('has_note'), FILTER_VALIDATE_BOOLEAN);
            if ($hasNote) {
                $this->query->whereNotNull('note');
            } else {
                $this->query->whereNull('note');
            }
        }
    }

    protected function applyEmployeeIdFilter(): void
    {
        if ($employeeId = $this->input('employee_id')) {
            // Suporta múltiplos IDs separados por vírgula
            if (str_contains($employeeId, ',')) {
                $ids = explode(',', $employeeId);
                $this->query->whereIn('employee_id', $ids);
            } else {
                $this->query->where('employee_id', $employeeId);
            }
        }
    }

    protected function applyCompanyIdFilter(): void
    {
        if ($companyId = $this->input('company_id')) {
            // Suporta múltiplos IDs separados por vírgula
            if (str_contains($companyId, ',')) {
                $ids = explode(',', $companyId);
                $this->query->whereIn('company_id', $ids);
            } else {
                $this->query->where('company_id', $companyId);
            }
        }
    }

    protected function applyCreatedAtFilter(): void
    {
        if ($createdAt = $this->input('created_at')) {
            $this->query->whereDate('created_at', $createdAt);
        }
    }

    protected function applyCreatedAtRangeFilter(): void
    {
        if ($startDate = $this->input('created_at_start')) {
            $this->query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate = $this->input('created_at_end')) {
            $this->query->whereDate('created_at', '<=', $endDate);
        }
    }

    protected function applyUpdatedAtFilter(): void
    {
        if ($updatedAt = $this->input('updated_at')) {
            $this->query->whereDate('updated_at', $updatedAt);
        }
    }

    protected function applyUpdatedAtRangeFilter(): void
    {
        if ($startDate = $this->input('updated_at_start')) {
            $this->query->whereDate('updated_at', '>=', $startDate);
        }

        if ($endDate = $this->input('updated_at_end')) {
            $this->query->whereDate('updated_at', '<=', $endDate);
        }
    }

    protected function applySortFilter(): void
    {
        $sortBy = $this->input('sort_by', 'created_at');
        $sortOrder = $this->input('sort_order', 'desc');

        // Lista de campos permitidos para ordenação (segurança)
        $allowedSorts = [
            'id', 'type', 'punch_time', 'auto_closed', 
            'extra_time', 'employee_id', 'company_id',
            'created_at', 'updated_at'
        ];

        if (in_array($sortBy, $allowedSorts) && in_array(strtolower($sortOrder), ['asc', 'desc'])) {
            $this->query->orderBy($sortBy, $sortOrder);
        }
    }
}

