<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.user')]
#[Title('Sales History - Inventory Pro')]
class SalesHistory extends Component
{
    public string $dateFrom = '';
    public string $dateTo = '';
    public string $period = 'Monthly';
    public string $staffFilter = '';

    public string $totalRevenue = '$142,504.20';
    public string $totalTransactions = '1,842';
    public string $avgOrderValue = '$77.36';

    public function applyFilters()
    {
        // placeholder filter logic
    }

    public function resetFilters()
    {
        $this->reset(['dateFrom', 'dateTo', 'period', 'staffFilter']);
        $this->period = 'Monthly';
    }

    public function render()
    {
        $transactionsQuery = \App\Models\Transaction::where('user_id', auth()->id());
        
        if ($this->dateFrom) {
            $transactionsQuery->whereDate('transaction_date', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $transactionsQuery->whereDate('transaction_date', '<=', $this->dateTo);
        }

        $transactions = $transactionsQuery->latest()->paginate(10);
        
        $totalRevenue = \App\Models\Transaction::where('user_id', auth()->id())->sum('total_price');
        $totalTransactionsCount = \App\Models\Transaction::where('user_id', auth()->id())->count();
        $avgOrderValue = $totalTransactionsCount > 0 ? $totalRevenue / $totalTransactionsCount : 0;

        return view('livewire.user.sales-history', [
            'transactions' => $transactions,
            'realTotalRevenue' => $totalRevenue,
            'realTotalTransactions' => $totalTransactionsCount,
            'realAvgOrderValue' => $avgOrderValue,
        ]);
    }
}
