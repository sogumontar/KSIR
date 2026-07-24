<div x-data="{
    exportOpen: false,
    activeTrend: 'revenue',
    initChart() {
        const existingChart = Chart.getChart('salesPerformanceChart');
        if (existingChart) {
            existingChart.destroy();
        }

        const canvas = document.getElementById('salesPerformanceChart');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        const isRevenue = this.activeTrend === 'revenue';

        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, isRevenue ? 'rgba(16, 185, 129, 0.4)' : 'rgba(59, 130, 246, 0.4)');
        gradient.addColorStop(1, isRevenue ? 'rgba(16, 185, 129, 0)' : 'rgba(59, 130, 246, 0)');

        const chartLabels = @json($chartLabels);
        const chartValues = isRevenue ? @json($chartValues) : @json($unitChartValues);
        const color = isRevenue ? '#10B981' : '#3B82F6';

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: isRevenue ? 'Revenue (Rp)' : 'Selling Trend (Units)',
                    data: chartValues,
                    borderColor: color,
                    borderWidth: 4,
                    fill: true,
                    backgroundColor: gradient,
                    tension: 0.4,
                    pointRadius: 6,
                    pointBackgroundColor: '#FFFFFF',
                    pointBorderColor: color,
                    pointBorderWidth: 2,
                    pointHoverRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#091426',
                        titleFont: { family: 'Public Sans', size: 14 },
                        bodyFont: { family: 'Public Sans', size: 16, weight: 'bold' },
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return isRevenue 
                                    ? 'Rp ' + context.parsed.y.toLocaleString('id-ID')
                                    : context.parsed.y.toLocaleString('id-ID') + ' units';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#E2E8F0', borderDash: [5, 5] },
                        ticks: {
                            color: '#64748B',
                            font: { family: 'Public Sans', size: 12 },
                            callback: function(value) {
                                return isRevenue 
                                    ? 'Rp ' + value.toLocaleString('id-ID')
                                    : value.toLocaleString('id-ID') + ' pcs';
                            }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748B', font: { family: 'Public Sans', size: 12 } }
                    }
                }
            }
        });
    }
}" x-init="$nextTick(() => initChart()); Livewire.hook('morph.updated', () => $nextTick(() => initChart()))">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-md mb-xl">
        <div>
            <nav class="flex text-on-surface-variant text-label-md mb-xs">
                <span>Admin</span>
                <span class="mx-2">/</span>
                <span class="text-primary font-bold">Sales Monitoring</span>
            </nav>
            <h2 class="font-headline-lg text-headline-lg text-primary uppercase tracking-tight">Sales Monitoring</h2>
        </div>
        <div class="relative">
            <button @click="exportOpen = !exportOpen" class="flex items-center gap-sm bg-secondary text-white px-lg py-3 rounded-lg font-label-lg hover:brightness-110 transition-all shadow-sm">
                <span class="material-symbols-outlined">download</span>
                Export Data
                <span class="material-symbols-outlined">expand_more</span>
            </button>
            <div @click.away="exportOpen = false" class="absolute right-0 mt-xs w-48 bg-white border border-outline-variant shadow-xl rounded-lg z-50 overflow-hidden" x-show="exportOpen">
                <a class="flex items-center gap-sm px-md py-md hover:bg-surface-container-low text-label-lg border-b border-outline-variant" href="#">
                    <img class="w-5 h-5" src="https://lh3.googleusercontent.com/aida-public/AB6AXuARxikiYQ8YruaNvdamKD_1ykBWO1XLoIiZBcm2TbqXyk3j9hMO1iepNOVoyErfMPvnuxxDoFrc9RBElBPLfy6PdyHE8DyaspvcmK-H95pwq6nQU5e6ZByo1W6aMupPkycV8tlhSxlMXQ_OarniVBsq0Se7l8L6iiEAFgfHSRu1rMEFkF2pLHrbmvC0mkmK4Qae0Dg3AyXuB1TD0eiv5US9tTU9LQ9KDbUZq1mzGbr0QJormohuq6tt01B3zaMS2JUXfSjiSto6"/> Excel (.xlsx)
                </a>
                <a class="flex items-center gap-sm px-md py-md hover:bg-surface-container-low text-label-lg" href="#">
                    <img class="w-5 h-5" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCKcUglAs6pIRPcPJ-ks-v_Z-6KYk7VaRlu3zeV9MZ-DwbrXLoAhhwvT0Kpj3YSkpgXzYHCEWNHcQY3WKcuxJxuWVetpxu0gZl6nSDZ1FMfjdtcrBImiI0N9Dj5RysccJpsG6MA0nioNDjqLr-vjQXZLYtRHpAVaHql_85cD-h0THMs8fOZGzxK0lbODfbFj9NgERN-v24iMP-w_GS-oRJOikHhlzWrhX_V56hJyRSBNtKvjZE9HOet-Qtt2usXxiE28OgVkCuL"/> PDF Document
                </a>
            </div>
        </div>
    </div>
    <!-- Filters Section -->
    <section class="bg-white border border-outline-variant rounded-xl p-md mb-lg shadow-sm flex flex-wrap items-end gap-md">
        <div class="flex-1 min-w-[200px]">
            <label class="block font-label-md text-on-surface-variant mb-xs">Date Range</label>
            <div class="flex flex-col sm:flex-row sm:items-center gap-xs">
                <input wire:model="dateFrom" class="border border-outline-variant rounded-lg bg-surface-container-lowest p-2 text-body-md focus:ring-2 focus:ring-secondary w-full" type="date"/>
                <span class="text-outline text-center sm:px-xs">to</span>
                <input wire:model="dateTo" class="border border-outline-variant rounded-lg bg-surface-container-lowest p-2 text-body-md focus:ring-2 focus:ring-secondary w-full" type="date"/>
            </div>
        </div>
        <div class="w-64">
            <label class="block font-label-md text-on-surface-variant mb-xs">User / Staff</label>
            <select wire:model="staffFilter" class="w-full bg-surface-container-lowest border border-outline-variant rounded-lg p-2 text-body-md focus:ring-2 focus:ring-secondary">
                <option value="">All Staff Members</option>
                <option value="Jonathan Smith">Jonathan Smith</option>
                <option value="Sarah Parker">Sarah Parker</option>
                <option value="Michael Chen">Michael Chen</option>
                <option value="Elena Rodriguez">Elena Rodriguez</option>
            </select>
        </div>
        <button wire:click="applyFilters" class="bg-primary text-white px-xl py-2 h-[48px] rounded-lg font-label-lg hover:bg-primary-container transition-colors">
            Apply Filters
        </button>
        <button wire:click="resetFilters" class="border border-outline px-md py-2 h-[48px] rounded-lg font-label-lg text-on-surface-variant hover:bg-surface-container-low">
            Reset
        </button>
    </section>
    <!-- KPI Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-lg mb-lg">
        <div class="bg-white border border-outline-variant rounded-xl p-lg shadow-sm overflow-hidden relative">
            <div class="flex justify-between items-start mb-sm">
                <div>
                    <p class="text-on-surface-variant font-label-md uppercase tracking-wider">Total Revenue</p>
                    <h3 class="font-display text-headline-lg text-primary mt-xs">Rp{{ number_format($realTotalRevenue, 0) }}</h3>
                </div>
                <span class="material-symbols-outlined text-secondary text-4xl">payments</span>
            </div>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-secondary opacity-20"></div>
        </div>
        <div class="bg-white border border-outline-variant rounded-xl p-lg shadow-sm overflow-hidden relative">
            <div class="flex justify-between items-start mb-sm">
                <div>
                    <p class="text-on-surface-variant font-label-md uppercase tracking-wider">Selling Units</p>
                    <h3 class="font-display text-headline-lg text-blue-600 mt-xs">{{ number_format($realTotalUnits) }} Pcs</h3>
                </div>
                <span class="material-symbols-outlined text-blue-500 text-4xl">shopping_cart</span>
            </div>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-blue-500 opacity-20"></div>
        </div>
        <div class="bg-white border border-outline-variant rounded-xl p-lg shadow-sm overflow-hidden relative">
            <div class="flex justify-between items-start mb-sm">
                <div>
                    <p class="text-on-surface-variant font-label-md uppercase tracking-wider">Total Transactions</p>
                    <h3 class="font-display text-headline-lg text-primary mt-xs">{{ number_format($realTotalTransactions) }}</h3>
                </div>
                <span class="material-symbols-outlined text-secondary text-4xl">receipt_long</span>
            </div>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-secondary opacity-20"></div>
        </div>
        <div class="bg-white border border-outline-variant rounded-xl p-lg shadow-sm overflow-hidden relative">
            <div class="flex justify-between items-start mb-sm">
                <div>
                    <p class="text-on-surface-variant font-label-md uppercase tracking-wider">Avg Order Value</p>
                    <h3 class="font-display text-headline-lg text-primary mt-xs">Rp{{ number_format($realAvgOrderValue, 0) }}</h3>
                </div>
                <span class="material-symbols-outlined text-secondary text-4xl">analytics</span>
            </div>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-secondary opacity-20"></div>
        </div>
    </div>
    <!-- Middle Section: Performance Chart -->
    <section class="bg-white border border-outline-variant rounded-xl p-lg mb-lg shadow-sm">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-md mb-lg">
            <div class="flex flex-wrap items-center gap-md">
                <h4 class="font-headline-md text-headline-md text-primary">Performance Trend</h4>
                <!-- Trend Metric Selector -->
                <div class="flex gap-xs bg-surface-container-low p-1 rounded-lg">
                    <button :class="activeTrend === 'revenue' ? 'bg-white shadow-sm font-bold text-emerald-700' : 'text-on-surface-variant'" @click="activeTrend = 'revenue'; $nextTick(() => initChart())" class="px-md py-1 rounded font-label-md transition-all flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">payments</span> Revenue Trend
                    </button>
                    <button :class="activeTrend === 'selling' ? 'bg-white shadow-sm font-bold text-blue-700' : 'text-on-surface-variant'" @click="activeTrend = 'selling'; $nextTick(() => initChart())" class="px-md py-1 rounded font-label-md transition-all flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">shopping_cart</span> Selling Trend (Units)
                    </button>
                </div>
            </div>

            <!-- Timeframe Selector -->
            <div class="flex gap-xs bg-surface-container-low p-1 rounded-lg">
                <button wire:click="$set('period', 'Daily')" class="px-md py-1 rounded font-label-md transition-all {{ $period === 'Daily' ? 'bg-white shadow-sm font-bold text-primary' : 'text-on-surface-variant' }}">Daily</button>
                <button wire:click="$set('period', 'Weekly')" class="px-md py-1 rounded font-label-md transition-all {{ $period === 'Weekly' ? 'bg-white shadow-sm font-bold text-primary' : 'text-on-surface-variant' }}">Weekly</button>
                <button wire:click="$set('period', 'Monthly')" class="px-md py-1 rounded font-label-md transition-all {{ $period === 'Monthly' ? 'bg-white shadow-sm font-bold text-primary' : 'text-on-surface-variant' }}">Monthly</button>
                <button wire:click="$set('period', 'Annually')" class="px-md py-1 rounded font-label-md transition-all {{ $period === 'Annually' ? 'bg-white shadow-sm font-bold text-primary' : 'text-on-surface-variant' }}">Annually</button>
            </div>
        </div>
        <div class="h-[400px] w-full" wire:ignore.self>
            <canvas id="salesPerformanceChart"></canvas>
        </div>
    </section>
    <!-- Bottom Section: Detailed Table -->
    <section class="bg-white border border-outline-variant rounded-xl shadow-sm overflow-hidden">
        <div class="px-lg py-md border-b border-outline-variant flex justify-between items-center">
            <h4 class="font-headline-md text-headline-md text-primary">Transaction Logs</h4>
            <div class="flex items-center gap-md">
                <span class="text-label-md text-on-surface-variant">Showing {{ $transactions->firstItem() ?? 0 }}-{{ $transactions->lastItem() ?? 0 }} of {{ number_format($transactions->total()) }}</span>
                <div class="flex gap-xs">
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[640px]">
                <thead>
                <tr class="bg-primary text-white">
                    @foreach(['transaction_date' => 'Date', 'id' => 'Transaction ID', 'item_name' => 'Item', 'user_id' => 'User', 'recipient_name' => 'Recipient', 'total_price' => 'Total Value'] as $col => $label)
                        <th class="px-lg py-md font-label-lg uppercase tracking-wider border-r border-white/10 cursor-pointer hover:bg-primary-container transition-colors" wire:click="sort('{{ $col }}')">
                            <div class="flex items-center gap-1 {{ $label === 'Total Value' ? 'justify-end' : '' }}">
                                {{ $label }}
                                @if($sortColumn === $col)
                                    <span class="material-symbols-outlined text-sm">{{ $sortDirection === 'asc' ? 'arrow_upward' : 'arrow_downward' }}</span>
                                @endif
                            </div>
                        </th>
                    @endforeach
                </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                @forelse($transactions as $tx)
                <tr class="hover:bg-surface-container-low transition-colors {{ $loop->even ? 'bg-surface-container-lowest' : 'bg-white' }}">
                    <td class="px-lg py-md text-body-md font-bold">{{ $tx->transaction_date->format('M d, Y, H:i') }}</td>
                    <td class="px-lg py-md text-body-md text-primary font-mono">TXN-{{ str_pad($tx->id, 5, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-lg py-md text-body-md">{{ $tx->item_name }} x {{ $tx->quantity }}</td>
                    <td class="px-lg py-md text-body-md flex items-center gap-sm">
                        {{ $tx->user->name ?? 'System' }}
                    </td>
                    <td class="px-lg py-md text-body-md">{{ $tx->recipient_name }}</td>
                    <td class="px-lg py-md text-body-md text-right font-bold text-secondary">Rp{{ number_format($tx->total_price, 0) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center p-4">No transactions found.</td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

