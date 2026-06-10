<div x-data="{
    hoveredPoint: null,
    tooltipX: 0,
    tooltipY: 0,
    tooltipLabel: '',
    tooltipValue: 0,

    hoveredProfitPoint: null,
    profitTooltipX: 0,
    profitTooltipY: 0,
    profitTooltipLabel: '',
    profitTooltipValue: 0,

    showTooltip(point, event) {
        const svg = event.target.closest('svg');
        const rect = svg.getBoundingClientRect();
        const viewBoxWidth = 800;
        const scale = rect.width / viewBoxWidth;

        this.hoveredPoint = point;
        this.tooltipX = point.x * scale;
        this.tooltipY = point.y * scale - 50;
        this.tooltipLabel = point.label;
        this.tooltipValue = point.value;
    },

    hideTooltip() {
        this.hoveredPoint = null;
    },

    showProfitTooltip(point, event) {
        const svg = event.target.closest('svg');
        const rect = svg.getBoundingClientRect();
        const viewBoxWidth = 800;
        const scale = rect.width / viewBoxWidth;

        this.hoveredProfitPoint = point;
        this.profitTooltipX = point.x * scale;
        this.profitTooltipY = point.y * scale - 50;
        this.profitTooltipLabel = point.label;
        this.profitTooltipValue = point.value;
    },

    hideProfitTooltip() {
        this.hoveredProfitPoint = null;
    }
}">
    <!-- Header Section -->
    <div class="mb-lg flex justify-between items-end">
        <div>
            <h2 class="font-headline-lg text-headline-lg text-primary">Executive Dashboard</h2>
            <p class="font-body-md text-on-surface-variant">Real-time performance metrics and inventory status</p>
        </div>
    </div>

    <!-- KPI Cards Section — 4 cards, mobile-first -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-gutter mb-lg">
        <!-- Total Sales Card -->
        <a href="{{ route('user.goods') }}" wire:navigate class="bg-white border border-outline-variant p-lg card-hover transition-all cursor-pointer group flex flex-col justify-between h-48 rounded-lg no-underline">
            <div class="flex justify-between items-start">
                <div class="p-sm bg-secondary-container rounded-lg">
                    <span class="material-symbols-outlined text-secondary" data-icon="payments">payments</span>
                </div>
                <span class="text-secondary font-label-md flex items-center gap-xs">
                    <span class="material-symbols-outlined text-sm" data-icon="trending_up">trending_up</span>
                    View All
                </span>
            </div>
            <div>
                <p class="font-label-lg text-on-surface-variant mb-xs">Total Sales</p>
                <h3 class="font-display text-display text-primary">{{ $totalSales }}</h3>
            </div>
            <span class="flex items-center text-tertiary font-label-md group-hover:underline">
                View transactions
                <span class="material-symbols-outlined text-sm ml-xs" data-icon="arrow_forward">arrow_forward</span>
            </span>
        </a>

        <!-- Total Profit Card -->
        <a href="{{ route('user.goods') }}" wire:navigate class="bg-white border border-outline-variant p-lg card-hover transition-all cursor-pointer group flex flex-col justify-between h-48 rounded-lg no-underline">
            <div class="flex justify-between items-start">
                <div class="p-sm bg-green-100 rounded-lg">
                    <span class="material-symbols-outlined text-green-700" data-icon="account_balance_wallet">account_balance_wallet</span>
                </div>
                <span class="text-green-700 font-label-md flex items-center gap-xs">
                    <span class="material-symbols-outlined text-sm" data-icon="trending_up">trending_up</span>
                    Net Margin
                </span>
            </div>
            <div>
                <p class="font-label-lg text-on-surface-variant mb-xs">Total Profit</p>
                <h3 class="font-display text-display text-green-700">{{ $totalProfit }}</h3>
            </div>
            <span class="flex items-center text-green-700 font-label-md group-hover:underline">
                View breakdown
                <span class="material-symbols-outlined text-sm ml-xs" data-icon="arrow_forward">arrow_forward</span>
            </span>
        </a>

        <!-- Items In Progress Card -->
        <a href="{{ route('user.goods', ['statusFilter' => 'in_progress']) }}" wire:navigate class="bg-white border border-outline-variant p-lg card-hover transition-all cursor-pointer group flex flex-col justify-between h-48 rounded-lg no-underline">
            <div class="flex justify-between items-start">
                <div class="p-sm bg-tertiary-fixed rounded-lg">
                    <span class="material-symbols-outlined text-tertiary" data-icon="pending_actions">pending_actions</span>
                </div>
                <span class="text-on-surface-variant font-label-md">Current cycle</span>
            </div>
            <div>
                <p class="font-label-lg text-on-surface-variant mb-xs">Items In Progress</p>
                <h3 class="font-display text-display text-primary">{{ $itemsInProgress }}</h3>
            </div>
            <span class="flex items-center text-tertiary font-label-md group-hover:underline">
                Manage processing
                <span class="material-symbols-outlined text-sm ml-xs" data-icon="arrow_forward">arrow_forward</span>
            </span>
        </a>

        <!-- Items On Loan Card -->
        <a href="{{ route('user.goods', ['statusFilter' => 'loan']) }}" wire:navigate class="bg-white border border-outline-variant p-lg card-hover transition-all cursor-pointer group flex flex-col justify-between h-48 rounded-lg no-underline">
            <div class="flex justify-between items-start">
                <div class="p-sm bg-surface-container rounded-lg">
                    <span class="material-symbols-outlined text-on-primary-fixed-variant" data-icon="contract_edit">contract_edit</span>
                </div>
                @if($overdueLoans > 0)
                <span class="text-error font-label-md flex items-center gap-xs">
                    <span class="material-symbols-outlined text-sm" data-icon="schedule">schedule</span>
                    {{ $overdueLoans }} Overdue
                </span>
                @else
                <span class="text-tertiary font-label-md flex items-center gap-xs">
                    <span class="material-symbols-outlined text-sm" data-icon="check_circle">check_circle</span>
                    All on time
                </span>
                @endif
            </div>
            <div>
                <p class="font-label-lg text-on-surface-variant mb-xs">Items On Loan</p>
                <h3 class="font-display text-display text-primary">{{ $itemsOnLoan }}</h3>
            </div>
            <span class="flex items-center text-tertiary font-label-md group-hover:underline">
                Review loan agreements
                <span class="material-symbols-outlined text-sm ml-xs" data-icon="arrow_forward">arrow_forward</span>
            </span>
        </a>
    </div>

    <!-- Sales Growth Analytics Chart -->
    <div class="bg-white border border-outline-variant p-md lg:p-lg rounded-lg mb-lg">
        <div class="flex flex-col md:flex-row justify-between items-center mb-lg gap-md">
            <div>
                <h4 class="font-headline-md text-headline-md text-primary">Sales Growth Analytics</h4>
                <p class="font-body-md text-on-surface-variant">Revenue trend visualization</p>
            </div>
            <div class="flex items-center gap-md overflow-x-auto">
                <div class="flex bg-surface-container-low p-base rounded-lg border border-outline-variant flex-shrink-0">
                    <button wire:click="setChartFilter('daily')"
                            class="chart-filter-btn px-md py-xs font-label-md rounded-lg transition-all {{ $chartFilter === 'daily' ? 'bg-white shadow-sm text-primary' : 'text-on-surface-variant hover:bg-surface-container' }}">
                        Daily
                    </button>
                    <button wire:click="setChartFilter('weekly')"
                            class="chart-filter-btn px-md py-xs font-label-md rounded-lg transition-all {{ $chartFilter === 'weekly' ? 'bg-white shadow-sm text-primary' : 'text-on-surface-variant hover:bg-surface-container' }}">
                        Weekly
                    </button>
                    <button wire:click="setChartFilter('monthly')"
                            class="chart-filter-btn px-md py-xs font-label-md rounded-lg transition-all {{ $chartFilter === 'monthly' ? 'bg-white shadow-sm text-primary' : 'text-on-surface-variant hover:bg-surface-container' }}">
                        Monthly
                    </button>
                    <button wire:click="setChartFilter('annual')"
                            class="chart-filter-btn px-md py-xs font-label-md rounded-lg transition-all {{ $chartFilter === 'annual' ? 'bg-white shadow-sm text-primary' : 'text-on-surface-variant hover:bg-surface-container' }}">
                        Annual
                    </button>
                </div>
            </div>
        </div>
        <div class="chart-container flex items-end justify-between gap-md pt-lg">
            <div class="w-full h-full relative" style="min-height: 250px;">
                <div class="absolute inset-0 flex flex-col justify-between pointer-events-none">
                    <div class="border-t border-slate-100 w-full h-0"></div>
                    <div class="border-t border-slate-100 w-full h-0"></div>
                    <div class="border-t border-slate-100 w-full h-0"></div>
                    <div class="border-t border-slate-100 w-full h-0"></div>
                    <div class="border-t border-slate-200 w-full h-0"></div>
                </div>
                @if(!empty($chartPath))
                <svg class="w-full h-full" viewBox="0 0 800 300">
                    <defs>
                        <linearGradient id="chartGradient" x1="0" x2="0" y1="0" y2="1">
                            <stop offset="0%" stop-color="#10B981" stop-opacity="0.2"></stop>
                            <stop offset="100%" stop-color="#10B981" stop-opacity="0"></stop>
                        </linearGradient>
                    </defs>
                    <path d="{{ $chartPath['line'] ?? '' }}" fill="none" stroke="#10B981" stroke-linecap="round" stroke-width="4"></path>
                    <path d="{{ $chartPath['fill'] ?? '' }}" fill="url(#chartGradient)"></path>
                    @foreach($chartPoints as $i => $point)
                    <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" fill="transparent" r="20"
                        @mouseenter="showTooltip({x: {{ $point['x'] }}, y: {{ $point['y'] }}, value: {{ $point['value'] }}, label: '{{ $point['label'] }}'}, $event)"
                        @mouseleave="hideTooltip()"
                        class="cursor-pointer" />
                    <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" fill="#10B981" r="6" stroke="white" stroke-width="2"
                        :class="hoveredPoint && hoveredPoint.x === {{ $point['x'] }} ? 'r-8 opacity-100 scale-110' : ''"
                        :style="hoveredPoint && hoveredPoint.x === {{ $point['x'] }} ? 'r: 8; filter: drop-shadow(0 0 6px #10B981)' : ''" />
                    @endforeach
                </svg>
                <div x-show="hoveredPoint !== null"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:enter-start="opacity-0 scale-90"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-end="opacity-0 scale-90"
                     x-transition:leave-start="opacity-100 scale-100"
                     :style="'left: ' + tooltipX + 'px; top: ' + tooltipY + 'px'"
                     class="absolute pointer-events-none bg-slate-900 text-white px-4 py-3 rounded-lg shadow-lg z-50 min-w-[140px]"
                     style="display: none;">
                    <div class="text-xs text-slate-400 mb-1" x-text="tooltipLabel"></div>
                    <div class="text-lg font-bold" x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(tooltipValue)"></div>
                </div>
                @endif
            </div>
        </div>
        <div class="flex justify-between mt-md px-md">
            @foreach($chartLabels as $label)
            <span class="font-label-md text-on-surface-variant">{{ $label }}</span>
            @endforeach
        </div>
    </div>

    <!-- Profit Growth Analytics Chart -->
    <div class="bg-white border border-outline-variant p-md lg:p-lg rounded-lg mb-lg">
        <div class="flex flex-col md:flex-row justify-between items-center mb-lg gap-md">
            <div>
                <h4 class="font-headline-md text-headline-md text-green-700">Profit Growth Analytics</h4>
                <p class="font-body-md text-on-surface-variant">Net profit trend visualization</p>
            </div>
        </div>
        <div class="chart-container flex items-end justify-between gap-md pt-lg">
            <div class="w-full h-full relative" style="min-height: 250px;">
                <div class="absolute inset-0 flex flex-col justify-between pointer-events-none">
                    <div class="border-t border-slate-100 w-full h-0"></div>
                    <div class="border-t border-slate-100 w-full h-0"></div>
                    <div class="border-t border-slate-100 w-full h-0"></div>
                    <div class="border-t border-slate-100 w-full h-0"></div>
                    <div class="border-t border-slate-200 w-full h-0"></div>
                </div>
                @if(!empty($profitChartPath))
                <svg class="w-full h-full" viewBox="0 0 800 300">
                    <defs>
                        <linearGradient id="profitGradient" x1="0" x2="0" y1="0" y2="1">
                            <stop offset="0%" stop-color="#3B82F6" stop-opacity="0.2"></stop>
                            <stop offset="100%" stop-color="#3B82F6" stop-opacity="0"></stop>
                        </linearGradient>
                    </defs>
                    <path d="{{ $profitChartPath['line'] ?? '' }}" fill="none" stroke="#3B82F6" stroke-linecap="round" stroke-width="4"></path>
                    <path d="{{ $profitChartPath['fill'] ?? '' }}" fill="url(#profitGradient)"></path>
                    @foreach($profitChartPoints as $i => $point)
                    <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" fill="transparent" r="20"
                        @mouseenter="showProfitTooltip({x: {{ $point['x'] }}, y: {{ $point['y'] }}, value: {{ $point['value'] }}, label: '{{ $point['label'] }}'}, $event)"
                        @mouseleave="hideProfitTooltip()"
                        class="cursor-pointer" />
                    <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" fill="#3B82F6" r="6" stroke="white" stroke-width="2"
                        :class="hoveredProfitPoint && hoveredProfitPoint.x === {{ $point['x'] }} ? 'r-8 opacity-100 scale-110' : ''"
                        :style="hoveredProfitPoint && hoveredProfitPoint.x === {{ $point['x'] }} ? 'r: 8; filter: drop-shadow(0 0 6px #3B82F6)' : ''" />
                    @endforeach
                </svg>
                <div x-show="hoveredProfitPoint !== null"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:enter-start="opacity-0 scale-90"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-end="opacity-0 scale-90"
                     x-transition:leave-start="opacity-100 scale-100"
                     :style="'left: ' + profitTooltipX + 'px; top: ' + profitTooltipY + 'px'"
                     class="absolute pointer-events-none bg-slate-900 text-white px-4 py-3 rounded-lg shadow-lg z-50 min-w-[140px]"
                     style="display: none;">
                    <div class="text-xs text-slate-400 mb-1" x-text="profitTooltipLabel"></div>
                    <div class="text-lg font-bold text-blue-400" x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(profitTooltipValue)"></div>
                </div>
                @endif
            </div>
        </div>
        <div class="flex justify-between mt-md px-md">
            @foreach($profitChartLabels as $label)
            <span class="font-label-md text-on-surface-variant">{{ $label }}</span>
            @endforeach
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white border border-outline-variant rounded-lg overflow-hidden shadow-sm">
        <div class="p-lg border-b border-outline-variant flex justify-between items-center">
            <h4 class="font-headline-md text-headline-md text-primary">Recent Inventory Transactions</h4>
            <a href="{{ route('user.goods') }}" wire:navigate class="text-tertiary font-label-lg hover:underline transition-all">View All History</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[500px]">
                <thead>
                <tr class="bg-slate-50">
                    <th class="px-lg py-md font-label-lg text-primary border-b-2 border-outline-variant">Transaction ID</th>
                    <th class="px-lg py-md font-label-lg text-primary border-b-2 border-outline-variant">Recipient</th>
                    <th class="px-lg py-md font-label-lg text-primary border-b-2 border-outline-variant">Item</th>
                    <th class="px-lg py-md font-label-lg text-primary border-b-2 border-outline-variant">Status</th>
                    <th class="px-lg py-md font-label-lg text-primary border-b-2 border-outline-variant text-right">Value</th>
                    <th class="px-lg py-md font-label-lg text-primary border-b-2 border-outline-variant text-right">Profit</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @forelse($recentTransactions as $tx)
                <tr class="{{ $loop->even ? 'bg-slate-50/50' : '' }} hover:bg-slate-50 transition-colors group">
                    <td class="px-lg py-md font-label-md text-primary">TXN-{{ str_pad($tx->id, 5, '0', STR_PAD_LEFT) }}</td>
                    <td class="px-lg py-md">
                        <div class="flex items-center gap-sm">
                            <div class="w-8 h-8 rounded-full bg-surface-container-highest flex items-center justify-center font-bold text-xs">
                                {{ strtoupper(substr($tx->recipient_name ?? 'U', 0, 1)) }}{{ strtoupper(substr(explode(' ', $tx->recipient_name ?? 'U')[1] ?? '', 0, 1)) }}
                            </div>
                            <span class="font-body-md text-on-surface">{{ $tx->recipient_name ?? '-' }}</span>
                        </div>
                    </td>
                    <td class="px-lg py-md font-body-md text-on-surface-variant">{{ $tx->item_name ?? '-' }}</td>
                    <td class="px-lg py-md">
                        @php
                            $statusStyles = [
                                'delivered' => 'bg-secondary-container text-on-secondary-container',
                                'pending' => 'bg-surface-container text-on-primary-container',
                                'transit' => 'bg-tertiary-fixed text-on-primary-container',
                                'loan' => 'bg-error-container text-on-error-container',
                                'failed' => 'bg-red-100 text-red-800',
                            ];
                            $style = $statusStyles[$tx->status] ?? 'bg-surface-container text-on-primary-container';
                        @endphp
                        <span class="px-sm py-base {{ $style }} rounded-full text-xs font-bold">{{ strtoupper($tx->status ?? 'UNKNOWN') }}</span>
                    </td>
                    <td class="px-lg py-md font-label-lg text-primary text-right">Rp{{ number_format($tx->total_price, 0) }}</td>
                    <td class="px-lg py-md font-label-lg text-right {{ ($tx->profit ?? 0) >= 0 ? 'text-green-700' : 'text-red-600' }}">Rp{{ number_format($tx->profit ?? 0, 0) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-lg py-md text-center text-on-surface-variant">No recent transactions found.</td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>