<?php

namespace App\Livewire\Laundry;

use App\Models\LaundryMerchantSetting;
use App\Models\LaundryOrder;
use App\Models\LaundryStoreContributor;
use App\Models\User;
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

    /** The owner of the store being managed (could be Auth user or a store they contribute to) */
    #[Url]
    public int $storeOwnerId = 0;

    #[Url]
    public string $search = '';

    #[Url]
    public string $statusFilter = '';

    public string $sortColumn    = 'date_in';
    public string $sortDirection = 'desc';
    public string $storeStatus   = 'open';

    // Assignee analytics filters
    public ?int   $assigneeFilter     = null;
    public string $assigneeDateFrom   = '';
    public string $assigneeDateTo     = '';
    public bool   $showAssigneeReport = false;

    private const SORTABLE = [
        'order_code'     => 'order_code',
        'customer_name'  => 'customer_name',
        'date_in'        => 'items_min_date_in',
        'total_amount'   => 'total_amount',
        'status'         => 'status',
        'payment_status' => 'payment_status',
    ];

    public function mount()
    {
        $user = Auth::user();

        // Default to own store
        if ($this->storeOwnerId === 0) {
            $this->storeOwnerId = $user->id;
        }

        // Validate access
        $this->authorizeStoreAccess();

        $setting = LaundryMerchantSetting::firstOrCreate(
            ['user_id' => $this->storeOwnerId],
            ['payment_notes' => '', 'store_status' => 'open']
        );
        $this->storeStatus = $setting->store_status ?? 'open';

        // Defaults for assignee date range
        $this->assigneeDateFrom = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->assigneeDateTo   = Carbon::now()->format('Y-m-d');
    }

    /** Check if the logged-in user is the owner of the currently selected store */
    public function isOwner(): bool
    {
        return Auth::id() === $this->storeOwnerId;
    }

    private function authorizeStoreAccess(): void
    {
        $user = Auth::user();
        if ($this->storeOwnerId === $user->id) return;

        $hasAccess = LaundryStoreContributor::where('owner_user_id', $this->storeOwnerId)
            ->where('contributor_user_id', $user->id)
            ->where('status', 'accepted')
            ->exists();

        if (!$hasAccess) {
            $this->storeOwnerId = $user->id;
        }
    }

    public function deleteOrder($id)
    {
        if (!$this->isOwner()) {
            session()->flash('status_message', 'Hanya owner yang dapat menghapus order.');
            return;
        }

        $order = LaundryOrder::where('user_id', $this->storeOwnerId)->find($id);
        if ($order) {
            $code = $order->order_code;
            $order->delete();
            session()->flash('status_message', "Order {$code} berhasil dihapus.");
        }
    }

    public function changeStoreStatus($newStatus)
    {
        if (!$this->isOwner()) return;

        if (in_array($newStatus, ['open', 'closed', 'unattended'])) {
            $this->storeStatus = $newStatus;
            $setting = LaundryMerchantSetting::where('user_id', $this->storeOwnerId)->first();
            if ($setting) {
                $setting->update(['store_status' => $newStatus]);
                session()->flash('status_message', 'Status toko berhasil diperbarui.');
            }
        }
    }

    public function sort($column)
    {
        if (!array_key_exists($column, self::SORTABLE)) return;

        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn    = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function updatedSearch()     { $this->resetPage(); }
    public function updatedStatusFilter() { $this->resetPage(); }

    public function toggleAssigneeReport(): void
    {
        $this->showAssigneeReport = !$this->showAssigneeReport;
    }

    public function render()
    {
        $today    = Carbon::today();
        $tomorrow = Carbon::tomorrow();
        $sortCol  = self::SORTABLE[$this->sortColumn] ?? 'items_min_date_in';

        // ─── Main Orders Table ────────────────────────────────────────────────
        $query = LaundryOrder::where('user_id', $this->storeOwnerId)
            ->withMin('items', 'date_in')
            ->withMax('items', 'date_estimated_done');

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

        // ─── KPI Stats ───────────────────────────────────────────────────────
        $totalOrdersToday = LaundryOrder::where('user_id', $this->storeOwnerId)
            ->whereHas('items', fn($q) => $q->whereDate('date_in', $today))
            ->count();

        $totalAmountAllTime  = LaundryOrder::where('user_id', $this->storeOwnerId)->sum('total_amount');
        $paidAmountAllTime   = LaundryOrder::where('user_id', $this->storeOwnerId)->where('payment_status', 'paid')->sum('total_amount');
        $unpaidAmountAllTime = LaundryOrder::where('user_id', $this->storeOwnerId)->where('payment_status', 'unpaid')->sum('total_amount');

        $totalUnpaidOutstanding = LaundryOrder::where('user_id', $this->storeOwnerId)
            ->where('payment_status', 'unpaid')
            ->whereNotIn('status', ['cancelled'])
            ->sum('total_amount');

        $activeOrdersCount = LaundryOrder::where('user_id', $this->storeOwnerId)
            ->whereIn('status', ['pending', 'processing', 'ready'])
            ->count();

        // ─── Revenue & Orders Trend ──────────────────────────────────────────
        $trendData = LaundryOrder::where('laundry_orders.user_id', $this->storeOwnerId)
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

        $chartLabels  = $trendData->pluck('date')->map(fn($d) => Carbon::parse($d)->format('M d'))->toArray();
        $totalValues  = $trendData->pluck('total_amount')->map(fn($v) => (float) $v)->toArray();
        $paidValues   = $trendData->pluck('paid_amount')->map(fn($v) => (float) $v)->toArray();
        $unpaidValues = $trendData->pluck('unpaid_amount')->map(fn($v) => (float) $v)->toArray();

        // ─── Services/Items Count Trend ──────────────────────────────────────
        $itemCountData = DB::table('laundry_order_items')
            ->join('laundry_orders', 'laundry_order_items.laundry_order_id', '=', 'laundry_orders.id')
            ->where('laundry_orders.user_id', $this->storeOwnerId)
            ->whereDate('laundry_order_items.date_in', '>=', Carbon::now()->subDays(13))
            ->selectRaw('laundry_order_items.date_in as date, SUM(COALESCE(laundry_order_items.qty, 1)) as item_count')
            ->groupBy('laundry_order_items.date_in')
            ->orderBy('laundry_order_items.date_in')
            ->get();

        $txCountLabels = $itemCountData->pluck('date')->map(fn($d) => Carbon::parse($d)->format('M d'))->toArray();
        $txCountValues = $itemCountData->pluck('item_count')->map(fn($v) => (int) $v)->toArray();

        // ─── Due soon ────────────────────────────────────────────────────────
        $dueSoonOrders = LaundryOrder::where('user_id', $this->storeOwnerId)
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

        // ─── Store info ───────────────────────────────────────────────────────
        $storeOwner = User::find($this->storeOwnerId);
        $storeSetting = LaundryMerchantSetting::where('user_id', $this->storeOwnerId)->first();

        // ─── Assignee Report ─────────────────────────────────────────────────
        $assigneeReport     = null;
        $assigneeReportOrders = collect();
        $assigneeReportTotal  = 0;
        $assigneeReportPcs    = 0;

        if ($this->showAssigneeReport && $this->assigneeFilter) {
            $reportQuery = LaundryOrder::where('user_id', $this->storeOwnerId)
                ->where('assignee_id', $this->assigneeFilter)
                ->with(['items', 'assignee']);

            if ($this->assigneeDateFrom) {
                $reportQuery->whereHas('items', fn($q) => $q->whereDate('date_in', '>=', $this->assigneeDateFrom));
            }
            if ($this->assigneeDateTo) {
                $reportQuery->whereHas('items', fn($q) => $q->whereDate('date_in', '<=', $this->assigneeDateTo));
            }

            $assigneeReportOrders = $reportQuery->orderByDesc('created_at')->get();
            $assigneeReportTotal  = $assigneeReportOrders->sum('total_amount');
            $assigneeReportPcs    = $assigneeReportOrders->sum(fn($o) => $o->items->sum(fn($i) => $i->qty ?? 1));
            $assigneeReport       = User::find($this->assigneeFilter);
        }

        // Build list of people who can be assignee (owner + accepted contributors)
        $assignableUsers = collect([User::find($this->storeOwnerId)]);
        $contributorUsers = LaundryStoreContributor::where('owner_user_id', $this->storeOwnerId)
            ->where('status', 'accepted')
            ->with('contributor')
            ->get()
            ->map(fn($c) => $c->contributor)
            ->filter();
        $assignableUsers = $assignableUsers->merge($contributorUsers);

        return view('livewire.laundry.dashboard', [
            'orders'                 => $orders,
            'totalOrdersToday'       => $totalOrdersToday,
            'totalAmountAllTime'     => $totalAmountAllTime,
            'paidAmountAllTime'      => $paidAmountAllTime,
            'unpaidAmountAllTime'    => $unpaidAmountAllTime,
            'totalUnpaidOutstanding' => $totalUnpaidOutstanding,
            'activeOrdersCount'      => $activeOrdersCount,
            'chartLabels'            => $chartLabels,
            'totalValues'            => $totalValues,
            'paidValues'             => $paidValues,
            'unpaidValues'           => $unpaidValues,
            'txCountLabels'          => $txCountLabels,
            'txCountValues'          => $txCountValues,
            'dueSoonOrders'          => $dueSoonOrders,
            'storeOwner'             => $storeOwner,
            'storeSetting'           => $storeSetting,
            'assignableUsers'        => $assignableUsers,
            'assigneeReport'         => $assigneeReport,
            'assigneeReportOrders'   => $assigneeReportOrders,
            'assigneeReportTotal'    => $assigneeReportTotal,
            'assigneeReportPcs'      => $assigneeReportPcs,
        ]);
    }
}
