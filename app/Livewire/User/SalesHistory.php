<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.user')]
#[Title('Sales Monitoring - Inventory Pro')]
class SalesHistory extends Component
{
    public string $dateFrom = '';
    public string $dateTo = '';
    public string $period = 'Monthly';
    public string $staffFilter = '';

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
        $transactions = $query->orderBy('transaction_date')->get();

        if ($transactions->isEmpty()) {
            return ['labels' => [], 'values' => []];
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
        }

        return ['labels' => $labels, 'values' => $values];
    }

    public function mount()
    {
        abort_if(!auth()->user()?->menu_sales_monitoring, 403, 'Unauthorized access to Sales Monitoring.');
    }

    public function render()
    {
        $filteredQuery = $this->getFilteredQuery();

        $transactions = $filteredQuery->clone()->latest()->paginate(10);

        $allFiltered = $filteredQuery->clone()->get();
        $totalRevenue = $allFiltered->sum('total_price');
        $totalTransactionsCount = $allFiltered->count();
        $avgOrderValue = $totalTransactionsCount > 0 ? $totalRevenue / $totalTransactionsCount : 0;

        $chartData = $this->getChartData($filteredQuery->clone());

        return view('livewire.user.sales-history', [
            'transactions' => $transactions,
            'realTotalRevenue' => $totalRevenue,
            'realTotalTransactions' => $totalTransactionsCount,
            'realAvgOrderValue' => $avgOrderValue,
            'chartLabels' => $chartData['labels'],
            'chartValues' => $chartData['values'],
        ]);
    }
}