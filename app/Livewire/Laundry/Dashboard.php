<?php

namespace App\Livewire\Laundry;

use App\Models\LaundryOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.user')]
#[Title('Laundry Dashboard - Inventory Pro')]
class Dashboard extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $statusFilter = '';

    public string $sortColumn = 'created_at';
    public string $sortDirection = 'desc';
    public string $chartPeriod = 'week';
    public string $storeStatus = 'open';

    /** Allowed sortable columns mapped to actual DB columns */
    private const SORTABLE = [
        'order_code'    => 'order_code',
        'customer_name' => 'customer_name',
        'created_at'    => 'created_at',
        'total_amount'  => 'total_amount',
        'status'        => 'status',
    ];

    public function mount()
    {
        $setting = \App\Models\LaundryMerchantSetting::firstOrCreate(
            ['user_id' => Auth::id()],
            ['payment_notes' => '', 'store_status' => 'open']
        );
        $this->storeStatus = $setting->store_status ?? 'open';
    }

    public function deleteOrder($id)
    {
        $order = LaundryOrder::where('user_id', Auth::id())->find($id);
        if ($order) {
            $code = $order->order_code;
            $order->delete();
            session()->flash('status_message', "Order {$code} deleted successfully.");
        }
    }

    public function changeStoreStatus($newStatus)
    {
        if (in_array($newStatus, ['open', 'closed', 'unattended'])) {
            $this->storeStatus = $newStatus;
            $setting = \App\Models\LaundryMerchantSetting::where('user_id', Auth::id())->first();
            if ($setting) {
                $setting->update(['store_status' => $newStatus]);
                session()->flash('status_message', 'Status toko berhasil diperbarui.');
            }
        }
    }

    public function sort($column)
    {
        // Only allow whitelisted columns
        if (!array_key_exists($column, self::SORTABLE)) {
            return;
        }

        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        // Resolve the actual DB column from the whitelist (fallback to created_at)
        $sortCol = self::SORTABLE[$this->sortColumn] ?? 'created_at';

        // Orders table query
        $query = LaundryOrder::where('user_id', $user->id);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('customer_name', 'like', '%' . $this->search . '%')
                  ->orWhere('order_code', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $orders = $query->orderBy($sortCol, $this->sortDirection)->paginate(15);

        // ─── KPI Stats ───────────────────────────────────────────────────────────
        $totalOrdersToday = LaundryOrder::where('user_id', $user->id)
            ->whereDate('created_at', $today)
            ->count();

        $revenueToday = LaundryOrder::where('user_id', $user->id)
            ->whereDate('created_at', $today)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $activeOrdersCount = LaundryOrder::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'processing', 'ready'])
            ->count();

        // ─── Revenue Trend chart (last 14 days) ─────────────────────────────────
        $revenueTrendData = LaundryOrder::where('user_id', $user->id)
            ->whereDate('created_at', '>=', Carbon::now()->subDays(13))
            ->selectRaw("DATE(created_at) as date, sum(total_amount) as revenue")
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $revenueLabels = $revenueTrendData->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->format('M d'))
            ->toArray();
        $revenueValues = $revenueTrendData->pluck('revenue')->map(fn($v) => (float) $v)->toArray();

        // ─── Transaction Count chart (last 14 days) ──────────────────────────────
        $txCountData = LaundryOrder::where('user_id', $user->id)
            ->whereDate('created_at', '>=', Carbon::now()->subDays(13))
            ->selectRaw("DATE(created_at) as date, count(*) as tx_count")
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $txCountLabels = $txCountData->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->format('M d'))
            ->toArray();
        $txCountValues = $txCountData->pluck('tx_count')->map(fn($v) => (int) $v)->toArray();

        // ─── Orders due today & tomorrow ─────────────────────────────────────────
        // Query orders that have at least one item due today or tomorrow
        $dueSoonOrders = LaundryOrder::where('user_id', $user->id)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->whereHas('items', function ($q) use ($today, $tomorrow) {
                $q->whereDate('date_estimated_done', '>=', $today)
                  ->whereDate('date_estimated_done', '<=', $tomorrow);
            })
            ->with(['items' => function ($q) use ($today, $tomorrow) {
                $q->whereDate('date_estimated_done', '>=', $today)
                  ->whereDate('date_estimated_done', '<=', $tomorrow)
                  ->orderBy('date_estimated_done');
            }, 'promo'])
            ->orderBy('created_at')
            ->get()
            ->map(function ($order) use ($today) {
                $earliestItem = $order->items->first();
                return [
                    'order'    => $order,
                    'items'    => $order->items,
                    'due_date' => $earliestItem ? $earliestItem->date_estimated_done : null,
                    'is_today' => $earliestItem
                        ? Carbon::parse($earliestItem->date_estimated_done)->isToday()
                        : false,
                ];
            });

        return view('livewire.laundry.dashboard', [
            'orders'           => $orders,
            'totalOrdersToday' => $totalOrdersToday,
            'revenueToday'     => $revenueToday,
            'activeOrdersCount'=> $activeOrdersCount,
            // Revenue trend
            'revenueLabels'    => $revenueLabels,
            'revenueValues'    => $revenueValues,
            // Transaction count trend
            'txCountLabels'    => $txCountLabels,
            'txCountValues'    => $txCountValues,
            // Due soon
            'dueSoonOrders'    => $dueSoonOrders,
        ]);
    }
}
