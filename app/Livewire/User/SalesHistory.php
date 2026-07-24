<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;
use App\Livewire\Concerns\WithTableFiltering;

#[Layout('components.layouts.user')]
#[Title('Sales Monitoring - Inventory Pro')]
class SalesHistory extends Component
{
    use WithPagination, WithTableFiltering;

    public string $period = 'Monthly';
    public string $staffFilter = '';
    public string $dateFrom = '';
    public string $dateTo = '';

    public function mount()
    {
        $this->sortColumn = 'transaction_date';
        $this->sortDirection = 'desc';
    }

    public function applyFilters()
    {
        // Livewire automatically re-renders when properties change,
        // so this method just serves as an explicit trigger.
    }

    public function resetFilters()
    {
        $this->reset(['dateFrom', 'dateTo', 'period', 'staffFilter']);
        $this->period = 'Monthly';
    }

    protected function getFilteredQuery()
    {
        $query = \App\Models\Transaction::where('user_id', auth()->id());

        if ($this->dateFrom) {
            $query->whereDate('transaction_date', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('transaction_date', '<=', $this->dateTo);
        }

        if ($this->staffFilter) {
            $query->whereHas('user', fn($q) => $q->where('name', $this->staffFilter));
        }

        return $query;
    }

    protected function getChartData($query)
    {
        $transactions = $query->reorder()->orderBy('transaction_date', 'asc')->get();

        if ($transactions->isEmpty()) {
            return ['labels' => [], 'values' => [], 'unitValues' => []];
        }

        $grouped = $transactions->groupBy(function ($tx) {
            $date = $tx->transaction_date;
            switch ($this->period) {
                case 'Daily':
                    return $date->format('Y-m-d');
                case 'Weekly':
                    return $date->format('Y-W');
                case 'Monthly':
                    return $date->format('Y-m');
                case 'Annually':
                    return $date->format('Y');
                default:
                    return $date->format('Y-m');
            }
        });

        $labels = [];
        $values = [];
        $unitValues = [];

        foreach ($grouped as $key => $group) {
            switch ($this->period) {
                case 'Daily':
                    $labels[] = $group->first()->transaction_date->format('M d');
                    break;
                case 'Weekly':
                    $labels[] = 'W' . $group->first()->transaction_date->format('W Y');
                    break;
                case 'Monthly':
                    $labels[] = $group->first()->transaction_date->format('M Y');
                    break;
                case 'Annually':
                    $labels[] = $group->first()->transaction_date->format('Y');
                    break;
                default:
                    $labels[] = $group->first()->transaction_date->format('M Y');
            }
            $values[] = round($group->sum('total_price'), 2);
            $unitValues[] = (int) $group->sum('quantity');
        }

        return [
            'labels' => $labels,
            'values' => $values,
            'unitValues' => $unitValues,
        ];
    }

    public function render()
    {
        $filteredQuery = $this->getFilteredQuery();

        $transactions = $this->applyTableFilters($filteredQuery->clone(), 'transaction_date')->paginate(10);

        $allFiltered = $filteredQuery->clone()->get();
        $totalRevenue = $allFiltered->sum('total_price');
        $totalTransactionsCount = $allFiltered->count();
        $totalUnitsSold = $allFiltered->sum('quantity');
        $avgOrderValue = $totalTransactionsCount > 0 ? $totalRevenue / $totalTransactionsCount : 0;

        $chartData = $this->getChartData($filteredQuery->clone());

        return view('livewire.user.sales-history', [
            'transactions' => $transactions,
            'realTotalRevenue' => $totalRevenue,
            'realTotalTransactions' => $totalTransactionsCount,
            'realTotalUnits' => $totalUnitsSold,
            'realAvgOrderValue' => $avgOrderValue,
            'chartLabels' => $chartData['labels'],
            'chartValues' => $chartData['values'],
            'unitChartValues' => $chartData['unitValues'],
        ]);
    }
}