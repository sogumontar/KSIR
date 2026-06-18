<?php

namespace App\Livewire\Concerns;

trait WithTableFiltering
{
    public $sortColumn = 'created_at';
    public $sortDirection = 'desc';

    public function applyTableFilters($query, $defaultSortColumn = 'created_at')
    {
        // Simple placeholder implementation to allow application to boot
        return $query->orderBy($this->sortColumn ?? $defaultSortColumn, $this->sortDirection ?? 'desc');
    }
}
