<?php

namespace App\Livewire\Concerns;

trait WithTableFiltering
{
    public $sortColumn = 'created_at';
    public $sortDirection = 'desc';

    public function sort($column)
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function applyTableFilters($query, $defaultSortColumn = 'created_at')
    {
        $sortCol = $this->sortColumn ?: $defaultSortColumn;
        $sortDir = $this->sortDirection ?: 'desc';

        return $query->orderBy($sortCol, $sortDir);
    }
}
