<?php

namespace App\Livewire\Laundry;

use App\Models\LaundryOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            ->whereHas('items', fn($q) => $q->whereDate('date_in', $today))
            ->count();

        $totalAmountAllTime = LaundryOrder::where('user_id', $user->id)->sum('total_amount');
        $paidAmountAllTime  = LaundryOrder::where('user_id', $user->id)->where('payment_status', 'paid')->sum('total_amount');
        $unpaidAmountAllTime= LaundryOrder::where('user_id', $user->id)->where('payment_status', 'unpaid')->sum('total_amount');

        $totalUnpaidOutstanding = LaundryOrder::where('user_id', $user->id)
            ->where('payment_status', 'unpaid')
            ->whereNotIn('status', ['cancelled'])
            ->sum('total_amount');

        $activeOrdersCount = LaundryOrder::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'processing', 'ready'])
            ->count();

        // ─── Revenue & Orders Trend (Based on Date In, last 14 days) ──────────────
        $trendData = LaundryOrder::where('laundry_orders.user_id', $user->id)
            ->join(DB::raw('(SELECT laundry_order_id, MIN(date_in) as date_in FROM laundry_order_items GROUP BY laundry_order_id) as items_date'), 'laundry_orders.id', '=', 'items_date.laundry_order_id')
            ->whereDate('items_date.date_in', '>=', Carbon::now()->subDays(13))
            ->selectRaw("items_date.date_in as date,
                         SUM(laundry_orders.total_amount) as total_amount,
                         SUM(CASE WHEN laundry_orders.payment_status = 'paid' THEN laundry_orders.total_amount ELSE 0 END) as paid_amount,
                         SUM(CASE WHEN laundry_orders.payment_status = 'unpaid' THEN laundry_orders.total_amount ELSE 0 END) as unpaid_amount,
                         COUNT(laundry_orders.id) as tx_count")
            ->groupBy('items_date.date_in')
            ->orderBy('items_date.date_in')
            ->get();

        $chartLabels    = $trendData->pluck('date')->map(fn($d) => Carbon::parse($d)->format('M d'))->toArray();
        $totalValues    = $trendData->pluck('total_amount')->map(fn($v) => (float) $v)->toArray();
        $paidValues     = $trendData->pluck('paid_amount')->map(fn($v) => (float) $v)->toArray();
        $unpaidValues   = $trendData->pluck('unpaid_amount')->map(fn($v) => (float) $v)->toArray();
        $txCountValues  = $trendData->pluck('tx_count')->map(fn($v) => (int) $v)->toArray();

        // ─── Orders due today & tomorrow ─────────────────────────────────────────
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
            'orders'                 => $orders,
            'totalOrdersToday'       => $totalOrdersToday,
            'totalAmountAllTime'     => $totalAmountAllTime,
            'paidAmountAllTime'      => $paidAmountAllTime,
            'unpaidAmountAllTime'    => $unpaidAmountAllTime,
            'totalUnpaidOutstanding' => $totalUnpaidOutstanding,
            'activeOrdersCount'      => $activeOrdersCount,
            // Trends based on Date In
            'chartLabels'            => $chartLabels,
            'totalValues'            => $totalValues,
            'paidValues'             => $paidValues,
            'unpaidValues'           => $unpaidValues,
            'txCountValues'          => $txCountValues,
            // Due soon
            'dueSoonOrders'          => $dueSoonOrders,
        ]);
    }
}
