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
    public string $totalSales = '$0';
    public int $itemsInProgress = 0;
    public int $itemsOnLoan = 0;
    public string $chartFilter = 'daily';

    public int $overdueLoans = 0;

    public array $chartPoints = [];
    public array $chartLabels = [];
    public array $chartPath = [];

    public function mount()
    {
        $userId = auth()->id();
        
        $total = Transaction::where('user_id', $userId)->sum('total_price');
        $this->totalSales = '$' . number_format($total, 0);

        $this->itemsInProgress = Transaction::where('user_id', $userId)
            ->whereIn('status', ['pending', 'transit'])
            ->count();

        $this->itemsOnLoan = Transaction::where('user_id', $userId)
            ->where('status', 'loan')
            ->count();

        // Calculate Overdue Loans safely
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
                // Monday to Sunday of current week
                $startOfWeek = now()->startOfWeek(Carbon::MONDAY);
                $range = collect(range(0, 6))->map(fn($i) => $startOfWeek->copy()->addDays($i));
                $format = 'D'; // Mon, Tue, Wed...
                $groupFormat = 'Y-m-d';
                break;

            case 'weekly':
                // Last 4 weeks
                $range = collect(range(3, 0))->map(fn($i) => now()->subWeeks($i)->startOfWeek(Carbon::MONDAY));
                $format = 'M d'; // e.g. "Jun 02"
                $groupFormat = 'Y-W';
                break;

            case 'monthly':
                // All 12 months of the current year
                $range = collect(range(0, 11))->map(fn($i) => Carbon::create(now()->year, $i + 1, 1));
                $format = 'M'; // Jan, Feb, Mar...
                $groupFormat = 'Y-m';
                break;

            case 'annual':
            default:
                // From first transaction year to current year
                $firstTx = Transaction::where('user_id', $userId)->oldest('transaction_date')->first();
                $startYear = $firstTx && $firstTx->transaction_date
                    ? Carbon::parse($firstTx->transaction_date)->year
                    : now()->year;
                $endYear = now()->year;
                
                // Ensure at least 2 data points
                if ($startYear === $endYear) {
                    $startYear = $endYear - 1;
                }
                
                $range = collect(range($startYear, $endYear))->map(fn($y) => Carbon::create($y, 1, 1));
                $format = 'Y';
                $groupFormat = 'Y';
                break;
        }

        $this->chartLabels = $range->map(fn($d) => $d->format($format))->toArray();

        // Fetch all sales in the period
        $startDate = $range->first()->copy()->startOfDay();
        $sales = Transaction::where('user_id', $userId)
            ->where('transaction_date', '>=', $startDate)
            ->get()
            ->groupBy(fn($tx) => Carbon::parse($tx->transaction_date)->format($groupFormat))
            ->map(fn($group) => $group->sum('total_price'));

        $data = $range->map(fn($d) => (float) ($sales[$d->format($groupFormat)] ?? 0))->toArray();

        // Edge case: all zeros
        $max = max($data) > 0 ? max($data) : 1;
        $width = 800;
        $height = 250;
        $maxY = 300;
        $count = count($data);

        // Prevent division by zero
        if ($count <= 1) {
            $this->chartPoints = [['x' => 400, 'y' => $maxY - (($data[0] ?? 0) / $max) * $height, 'value' => $data[0] ?? 0]];
            $this->chartPath = ['line' => 'M 400 ' . $this->chartPoints[0]['y'], 'fill' => 'M 400 ' . $this->chartPoints[0]['y'] . ' L 800 300 L 0 300 Z'];
            return;
        }

        $points = [];
        foreach ($data as $index => $value) {
            $x = ($index / ($count - 1)) * $width;
            $y = $maxY - (($value / $max) * $height);
            $points[] = ['x' => round($x, 2), 'y' => round($y, 2), 'value' => $value];
        }
        $this->chartPoints = $points;

        // Build SVG smooth curve path
        $path = "M " . $points[0]['x'] . " " . $points[0]['y'];
        for ($i = 1; $i < count($points); $i++) {
            $prev = $points[$i - 1];
            $curr = $points[$i];
            $cp1X = $prev['x'] + ($curr['x'] - $prev['x']) / 2;
            $cp1Y = $prev['y'];
            $cp2X = $prev['x'] + ($curr['x'] - $prev['x']) / 2;
            $cp2Y = $curr['y'];
            $path .= " C {$cp1X} {$cp1Y}, {$cp2X} {$cp2Y}, {$curr['x']} {$curr['y']}";
        }

        $this->chartPath = [
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

        // Fetch recent transactions for the table (real data)
        $recentTransactions = Transaction::where('user_id', $userId)
            ->latest('transaction_date')
            ->take(5)
            ->get();

        return view('livewire.user.dashboard', [
            'recentTransactions' => $recentTransactions,
        ]);
    }
}
