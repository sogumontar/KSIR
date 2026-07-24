<div x-data="{
    revenueChart: null,
    txChart: null,

    initCharts() {
        this.initRevenueChart();
        this.initTxChart();
    },

    initRevenueChart() {
        const existing = Chart.getChart('laundryRevenueChart');
        if (existing) existing.destroy();
        const ctx = document.getElementById('laundryRevenueChart');
        if (!ctx) return;
        const c = ctx.getContext('2d');
        const grad = c.createLinearGradient(0, 0, 0, 260);
        grad.addColorStop(0, 'rgba(0,108,73,0.35)');
        grad.addColorStop(1, 'rgba(0,108,73,0)');
        this.revenueChart = new Chart(c, {
            type: 'line',
            data: {
                labels: @js($revenueLabels),
                datasets: [{
                    label: 'Revenue (Rp)',
                    data: @js($revenueValues),
                    borderColor: '#006c49',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    backgroundColor: grad,
                    pointRadius: 5,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#006c49',
                    pointBorderWidth: 2,
                    pointHoverRadius: 7,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#091426',
                        padding: 10,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: ctx => 'Rp ' + ctx.parsed.y.toLocaleString('id-ID')
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#E2E8F0', borderDash: [4,4] },
                        ticks: { color: '#64748B', callback: v => 'Rp ' + v.toLocaleString('id-ID') }
                    },
                    x: { grid: { display: false }, ticks: { color: '#64748B' } }
                }
            }
        });
    },

    initTxChart() {
        const existing = Chart.getChart('laundryTxChart');
        if (existing) existing.destroy();
        const ctx = document.getElementById('laundryTxChart');
        if (!ctx) return;
        const c = ctx.getContext('2d');
        const grad = c.createLinearGradient(0, 0, 0, 200);
        grad.addColorStop(0, 'rgba(59,130,246,0.35)');
        grad.addColorStop(1, 'rgba(59,130,246,0)');
        this.txChart = new Chart(c, {
            type: 'bar',
            data: {
                labels: @js($txCountLabels),
                datasets: [{
                    label: 'Orders',
                    data: @js($txCountValues),
                    backgroundColor: grad,
                    borderColor: '#3B82F6',
                    borderWidth: 2,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#091426',
                        padding: 10,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: ctx => ctx.parsed.y + ' orders'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: '#64748B', precision: 0 },
                        grid: { color: '#E2E8F0', borderDash: [4,4] }
                    },
                    x: { grid: { display: false }, ticks: { color: '#64748B' } }
                }
            }
        });
    }
}" x-init="$nextTick(() => initCharts())" x-on:livewire-update.window="$nextTick(() => initCharts())">

    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-md mb-8">
        <div>
            <h2 class="font-headline-lg text-headline-lg text-slate-900 m-0">Laundry Dashboard</h2>
        </div>
        <div class="flex flex-wrap items-center gap-4">
            @if(session()->has('status_message'))
                <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-lg border border-emerald-100">{{ session('status_message') }}</span>
            @endif

            {{-- Store Status Widget --}}
            <div class="flex items-center gap-3 bg-white px-4 py-2 rounded-2xl shadow-sm border border-slate-200">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Status Toko:</span>
                <div class="flex bg-slate-100 p-0.5 rounded-xl">
                    <button wire:click="changeStoreStatus('open')" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 {{ $storeStatus === 'open' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                        <span class="w-2 h-2 rounded-full {{ $storeStatus === 'open' ? 'bg-white' : 'bg-emerald-500' }}"></span> Buka
                    </button>
                    <button wire:click="changeStoreStatus('unattended')" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 {{ $storeStatus === 'unattended' ? 'bg-amber-500 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                        <span class="w-2 h-2 rounded-full {{ $storeStatus === 'unattended' ? 'bg-white' : 'bg-amber-500' }} animate-pulse"></span> Tidak Ada Penjaga
                    </button>
                    <button wire:click="changeStoreStatus('closed')" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all flex items-center gap-1.5 {{ $storeStatus === 'closed' ? 'bg-red-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                        <span class="w-2 h-2 rounded-full {{ $storeStatus === 'closed' ? 'bg-white' : 'bg-red-500' }}"></span> Tutup
                    </button>
                </div>
            </div>

            <a href="{{ route('laundry.orders.create') }}" wire:navigate class="btn-primary gap-2">
                <span class="material-symbols-outlined">add</span>
                New Order
            </a>
        </div>
    </div>

    <!-- Sub-nav pills -->
    <div class="flex gap-2 mb-8 overflow-x-auto pb-2">
        <a href="/laundry/dashboard" wire:navigate class="px-4 py-2 rounded-full font-label-md bg-secondary text-white shadow-sm whitespace-nowrap">Dashboard</a>
        <a href="/laundry/services" wire:navigate class="px-4 py-2 rounded-full font-label-md bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 whitespace-nowrap">Services</a>
        <a href="/laundry/promos" wire:navigate class="px-4 py-2 rounded-full font-label-md bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 whitespace-nowrap">Promos</a>
        <a href="/laundry/settings" wire:navigate class="px-4 py-2 rounded-full font-label-md bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 whitespace-nowrap">Settings</a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="card-surface p-6 flex items-center justify-between">
            <div>
                <p class="text-sm font-label-md text-slate-500 uppercase">Today's Orders</p>
                <h3 class="text-3xl font-headline-lg text-primary mt-2">{{ number_format($totalOrdersToday) }}</h3>
            </div>
            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                <span class="material-symbols-outlined">shopping_bag</span>
            </div>
        </div>
        <div class="card-surface p-6 flex items-center justify-between">
            <div>
                <p class="text-sm font-label-md text-slate-500 uppercase">Today's Revenue</p>
                <h3 class="text-3xl font-headline-lg text-primary mt-2">Rp{{ number_format($revenueToday, 0) }}</h3>
            </div>
            <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                <span class="material-symbols-outlined">payments</span>
            </div>
        </div>
        <div class="card-surface p-6 flex items-center justify-between">
            <div>
                <p class="text-sm font-label-md text-slate-500 uppercase">Active Orders</p>
                <h3 class="text-3xl font-headline-lg text-primary mt-2">{{ number_format($activeOrdersCount) }}</h3>
            </div>
            <div class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center text-amber-600">
                <span class="material-symbols-outlined">pending_actions</span>
            </div>
        </div>
    </div>

    <!-- Charts Row (Revenue Trend + Transaction Count) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Revenue Trend -->
        <div class="card-surface p-6">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="font-headline-md text-primary">Revenue Trend</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Last 14 days</p>
                </div>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-emerald-600 text-lg">show_chart</span>
                </div>
            </div>
            <div class="h-[240px] w-full" wire:ignore>
                <canvas id="laundryRevenueChart"></canvas>
            </div>
        </div>

        <!-- Transaction Count Trend -->
        <div class="card-surface p-6">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="font-headline-md text-primary">Orders Count</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Last 14 days</p>
                </div>
                <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-blue-500 text-lg">bar_chart</span>
                </div>
            </div>
            <div class="h-[240px] w-full" wire:ignore>
                <canvas id="laundryTxChart"></canvas>
            </div>
        </div>
    </div>

    <!-- ── Due Soon Section ────────────────────────────────────────────────── -->
    @if($dueSoonOrders->isNotEmpty())
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center">
                <span class="material-symbols-outlined text-amber-600 text-lg">alarm</span>
            </div>
            <h3 class="font-headline-md text-slate-900 m-0">Due Today &amp; Tomorrow</h3>
            <span class="ml-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                {{ $dueSoonOrders->count() }} order{{ $dueSoonOrders->count() > 1 ? 's' : '' }}
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($dueSoonOrders as $due)
            @php
                $order = $due['order'];
                $isToday = $due['is_today'];
                $dueDate = $due['due_date'];
            @endphp
            <a href="{{ route('laundry.orders.show', $order->id) }}" wire:navigate class="block">
                <div class="card-surface p-4 border-l-4 hover:shadow-md transition-shadow {{ $isToday ? 'border-l-red-500' : 'border-l-amber-400' }}">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <span class="text-xs font-bold {{ $isToday ? 'text-red-600 bg-red-50' : 'text-amber-700 bg-amber-50' }} px-2 py-0.5 rounded-full">
                                {{ $isToday ? '⚡ Today' : '📅 Tomorrow' }}
                            </span>
                            <p class="font-bold text-slate-900 mt-2 text-sm">{{ $order->order_code }}</p>
                        </div>
                        @php
                            $statusColors = [
                                'pending'    => 'bg-slate-100 text-slate-700',
                                'processing' => 'bg-blue-100 text-blue-700',
                                'ready'      => 'bg-amber-100 text-amber-700',
                            ];
                        @endphp
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$order->status] ?? 'bg-slate-100 text-slate-700' }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    <p class="text-sm text-slate-600 mb-1 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm text-slate-400">person</span>
                        {{ $order->customer_name }}
                    </p>
                    <p class="text-xs text-slate-500 flex items-center gap-1">
                        <span class="material-symbols-outlined text-xs text-slate-400">category</span>
                        {{ $due['items']->count() }} item{{ $due['items']->count() > 1 ? 's' : '' }}
                        &nbsp;·&nbsp;
                        Rp{{ number_format($order->total_amount, 0) }}
                    </p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Orders Table -->
    <div class="card-surface overflow-hidden">
        <div class="p-4 border-b border-slate-200 flex flex-col md:flex-row gap-4 items-center justify-between bg-slate-50">
            <h3 class="font-headline-md text-primary m-0">Recent Orders</h3>
            <div class="flex gap-4 w-full md:w-auto">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search orders..." class="form-input w-full md:w-64 bg-white">
                <select wire:model.live="statusFilter" class="form-input bg-white w-full md:w-48">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="ready">Ready</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[800px]">
                <thead>
                    <tr>
                        @php
                            $cols = [
                                'order_code'   => 'Order Code',
                                'customer_name'=> 'Customer',
                                'created_at'   => 'Date',
                                'total_amount' => 'Total',
                                'status'       => 'Status',
                            ];
                        @endphp
                        @foreach($cols as $col => $label)
                        <th class="table-header font-label-lg cursor-pointer hover:bg-slate-200 select-none" wire:click="sort('{{ $col }}')">
                            <div class="flex items-center gap-1 {{ $col === 'total_amount' ? 'justify-end' : '' }}">
                                {{ $label }}
                                @if($sortColumn === $col)
                                    <span class="material-symbols-outlined text-xs text-secondary">{{ $sortDirection === 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                                @endif
                            </div>
                        </th>
                        @endforeach
                        <th class="table-header font-label-lg text-center">Payment</th>
                        <th class="table-header font-label-lg text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="font-body-md text-slate-700">
                    @forelse($orders as $order)
                    <tr class="border-b border-slate-200 hover:bg-slate-50 transition-colors">
                        <td class="table-cell font-medium">{{ $order->order_code }}</td>
                        <td class="table-cell">{{ $order->customer_name }}</td>
                        <td class="table-cell">{{ $order->created_at->format('M d, Y') }}</td>
                        <td class="table-cell text-right font-bold">Rp{{ number_format($order->total_amount, 0) }}</td>
                        <td class="table-cell text-center">
                            @php
                                $statusColors = [
                                    'pending'    => 'bg-slate-100 text-slate-800',
                                    'processing' => 'bg-blue-100 text-blue-800',
                                    'ready'      => 'bg-amber-100 text-amber-800',
                                    'completed'  => 'bg-green-100 text-green-800',
                                    'cancelled'  => 'bg-red-100 text-red-800',
                                ];
                            @endphp
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$order->status] ?? 'bg-slate-100' }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="table-cell text-center">
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </td>
                        <td class="table-cell text-center">
                            <a href="{{ route('laundry.orders.show', $order->id) }}" wire:navigate class="btn-icon" title="View Details">
                                <span class="material-symbols-outlined">visibility</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-slate-500">No orders found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-200 bg-slate-50">
            {{ $orders->links() }}
        </div>
    </div>
</div>
