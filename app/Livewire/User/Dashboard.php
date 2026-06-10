<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Transaction;
use Carbon\Carbon;

#[Layout('components.layouts.user')]
#[Title('Dashboard - Inventory Pro')]
class Dashboard extends Component
{
    public string $totalSales = 'Rp0';
    public string $totalProfit = 'Rp0';
    public int $itemsInProgress = 0;
    public int $itemsOnLoan = 0;
    public string $chartFilter = 'daily';

    public int $overdueLoans = 0;

    public array $chartPoints = [];
    public array $chartLabels = [];
    public array $chartPath = [];

    public array $profitChartPoints = [];
    public array $profitChartLabels = [];
    public array $profitChartPath = [];

    public function mount()
    {
        $userId = auth()->id();

        $total = Transaction::where('user_id', $userId)->sum('total_price');
        $this->totalSales = 'Rp' . number_format($total, 0);

        $profit = Transaction::where('user_id', $userId)->sum('profit');
        $this->totalProfit = 'Rp' . number_format($profit, 0);

        $this->itemsInProgress = Transaction::where('user_id', $userId)
            ->whereIn('status', ['pending', 'transit'])
            ->count();

        $this->itemsOnLoan = Transaction::where('user_id', $userId)
            ->where('status', 'loan')
            ->count();

        try {
            $this->overdueLoans = Transaction::where('user_id', $userId)
                ->where('status', 'loan')
                ->whereNotNull('due_date')
                ->where('due_date', '<', now()->format('Y-m-d'))
                ->count();
        } catch (\Exception $e) {
            $this->overdueLoans = 0;
        }

        $this->calculateChartData($userId);
    }

    private function calculateChartData($userId)
    {
        switch ($this->chartFilter) {
            case 'daily':
                $startOfWeek = now()->startOfWeek(Carbon::MONDAY);
                $range = collect(range(0, 6))->map(fn($i) => $startOfWeek->copy()->addDays($i));
                $format = 'D';
                $groupFormat = 'Y-m-d';
                break;

            case 'weekly':
                $range = collect(range(3, 0))->map(fn($i) => now()->subWeeks($i)->startOfWeek(Carbon::MONDAY));
                $format = 'M d';
                $groupFormat = 'Y-W';
                break;

            case 'monthly':
                $range = collect(range(0, 11))->map(fn($i) => Carbon::create(now()->year, $i + 1, 1));
                $format = 'M';
                $groupFormat = 'Y-m';
                break;

            case 'annual':
            default:
                $firstTx = Transaction::where('user_id', $userId)->oldest('transaction_date')->first();
                $startYear = $firstTx && $firstTx->transaction_date
                    ? Carbon::parse($firstTx->transaction_date)->year
                    : now()->year;
                $endYear = now()->year;

                if ($startYear === $endYear) {
                    $startYear = $endYear - 1;
                }

                $range = collect(range($startYear, $endYear))->map(fn($y) => Carbon::create($y, 1, 1));
                $format = 'Y';
                $groupFormat = 'Y';
                break;
        }

        $labels = $range->map(fn($d) => $d->format($format))->toArray();
        $this->chartLabels = $labels;
        $this->profitChartLabels = $labels;

        $startDate = $range->first()->copy()->startOfDay();
        $transactions = Transaction::where('user_id', $userId)
            ->where('transaction_date', '>=', $startDate)
            ->get()
            ->groupBy(fn($tx) => Carbon::parse($tx->transaction_date)->format($groupFormat));

        $salesData = $transactions->map(fn($group) => $group->sum('total_price'));
        $profitData = $transactions->map(fn($group) => $group->sum('profit'));

        $salesValues = $range->map(fn($d) => (float) ($salesData[$d->format($groupFormat)] ?? 0))->toArray();
        $profitValues = $range->map(fn($d) => (float) ($profitData[$d->format($groupFormat)] ?? 0))->toArray();

        $this->chartPoints = $this->buildChartPoints($salesValues, $labels);
        $this->chartPath = $this->buildChartPath($this->chartPoints);

        $this->profitChartPoints = $this->buildChartPoints($profitValues, $labels);
        $this->profitChartPath = $this->buildChartPath($this->profitChartPoints);
    }

    private function buildChartPoints(array $data, array $labels): array
    {
        $max = max($data) > 0 ? max($data) : 1;
        $width = 800;
        $height = 250;
        $maxY = 300;
        $count = count($data);

        if ($count <= 1) {
            return [['x' => 400, 'y' => $maxY - (($data[0] ?? 0) / $max) * $height, 'value' => $data[0] ?? 0, 'label' => $labels[0] ?? '']];
        }

        $points = [];
        foreach ($data as $index => $value) {
            $x = ($index / ($count - 1)) * $width;
            $y = $maxY - (($value / $max) * $height);
            $points[] = ['x' => round($x, 2), 'y' => round($y, 2), 'value' => $value, 'label' => $labels[$index]];
        }
        return $points;
    }

    private function buildChartPath(array $points): array
    {
        $width = 800;
        $maxY = 300;

        if (count($points) <= 1) {
            $p = $points[0] ?? ['x' => 400, 'y' => 150];
            $line = "M {$p['x']} {$p['y']}";
            return ['line' => $line, 'fill' => $line . " L {$width} {$maxY} L 0 {$maxY} Z"];
        }

        $path = "M {$points[0]['x']} {$points[0]['y']}";
        for ($i = 1; $i < count($points); $i++) {
            $prev = $points[$i - 1];
            $curr = $points[$i];
            $cp1X = $prev['x'] + ($curr['x'] - $prev['x']) / 2;
            $cp1Y = $prev['y'];
            $cp2X = $prev['x'] + ($curr['x'] - $prev['x']) / 2;
            $cp2Y = $curr['y'];
            $path .= " C {$cp1X} {$cp1Y}, {$cp2X} {$cp2Y}, {$curr['x']} {$curr['y']}";
        }

        return [
            'line' => $path,
            'fill' => $path . " L {$width} {$maxY} L 0 {$maxY} Z",
        ];
    }

    public function setChartFilter(string $filter)
    {
        $this->chartFilter = $filter;
        $this->calculateChartData(auth()->id());
    }

    public function render()
    {
        $userId = auth()->id();

        $recentTransactions = Transaction::where('user_id', $userId)
            ->latest('transaction_date')
            ->take(5)
            ->get();

        return view('livewire.user.dashboard', [
            'recentTransactions' => $recentTransactions,
        ]);
    }
}