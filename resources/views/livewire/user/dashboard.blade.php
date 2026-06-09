<div>
    <!-- Header Section -->
    <div class="mb-lg flex justify-between items-end">
        <div>
            <h2 class="font-headline-lg text-headline-lg text-primary">Executive Dashboard</h2>
            <p class="font-body-md text-on-surface-variant">Real-time performance metrics and inventory status</p>
        </div>
    </div>

    <!-- KPI Cards Section -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-gutter mb-lg">
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

    <!-- Main Analytics Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter">
        <!-- Sales Growth Analytics -->
        <div class="lg:col-span-8 bg-white border border-outline-variant p-lg rounded-lg">
            <div class="flex justify-between items-center mb-lg">
                <div>
                    <h4 class="font-headline-md text-headline-md text-primary">Sales Growth Analytics</h4>
                    <p class="font-body-md text-on-surface-variant">Performance trend visualization</p>
                </div>
                <div class="flex items-center gap-md">
                    <div class="flex bg-surface-container-low p-base rounded-lg border border-outline-variant">
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
                <!-- SVG Chart -->
                <div class="w-full h-full relative">
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
                        @foreach($chartPoints as $point)
                        <circle class="cursor-pointer hover:r-8 transition-all" cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" fill="#10B981" r="6" stroke="white" stroke-width="2"><title>${{ number_format($point['value'], 0) }}</title></circle>
                        @endforeach
                    </svg>
                    @endif
                </div>
            </div>
            <div class="flex justify-between mt-md px-md">
                @foreach($chartLabels as $label)
                <span class="font-label-md text-on-surface-variant">{{ $label }}</span>
                @endforeach
            </div>
        </div>

        <!-- Recent Notifications / Activity -->
        <div class="lg:col-span-4 flex flex-col gap-gutter">
            <div class="bg-white border border-outline-variant p-lg rounded-lg flex-1">
                <div class="flex justify-between items-center mb-md">
                    <h4 class="font-headline-md text-headline-md text-primary">System Health</h4>
                    <span class="px-sm py-xs bg-secondary-container text-on-secondary-container rounded-full text-xs font-bold uppercase tracking-wider">Operational</span>
                </div>
                <div class="space-y-lg">
                    <div class="flex items-center justify-between">
                        <span class="font-label-lg text-on-surface-variant">Server Load</span>
                        <div class="w-32 h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="w-[34%] h-full bg-secondary"></div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="font-label-lg text-on-surface-variant">API Latency</span>
                        <span class="font-label-md text-primary">24ms</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="font-label-lg text-on-surface-variant">DB Connectivity</span>
                        <span class="font-label-md text-secondary">99.9%</span>
                    </div>
                </div>
            </div>
            <div class="relative rounded-lg overflow-hidden h-64 border border-outline-variant group">
                <img class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" alt="A modern, high-tech logistics control center with large digital screens displaying global shipping data and inventory analytics." src="https://lh3.googleusercontent.com/aida-public/AB6AXuDsNkFxaLUoF5jSUlcjcF9s_Wg1jRCxq0VjKd0ypWMqm9DT8x4kgDQAa7-H2ZRvMwRS1uawRObNjZeS2ksRWHAMZS2k9rsPYBiRfcEdZeVL-33bIUMylcphOY4IsaivBvt25NFeZPrOubwyot_Eq_aSm6Wi79cY9JVTPGc1lNRPIQ2E0U9oXdXPs2Fedi23YoRPeRaF_hCSM0LFMv-e95i33oKP0Bkn2_K4p2pGs4GIiuDQ87LLJkZd8tQ04UIJOKEcENceBe0_"/>
                <div class="absolute inset-0 bg-gradient-to-t from-primary/90 to-transparent flex flex-col justify-end p-lg">
                    <h5 class="text-white font-headline-md mb-xs">Inventory Audit 2024</h5>
                    <p class="text-on-primary-container text-sm">Review scheduled for Q3 is now available for early preparation.</p>
                    <button class="mt-md text-white font-label-md flex items-center gap-xs">
                        Learn more
                        <span class="material-symbols-outlined text-sm" data-icon="chevron_right">chevron_right</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Grid: Recent Transactions (Real Data) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter mt-lg">
        <div class="lg:col-span-12 bg-white border border-outline-variant rounded-lg overflow-hidden shadow-sm">
            <div class="p-lg border-b border-outline-variant flex justify-between items-center">
                <h4 class="font-headline-md text-headline-md text-primary">Recent Inventory Transactions</h4>
                <a href="{{ route('user.goods') }}" wire:navigate class="text-tertiary font-label-lg hover:underline transition-all">View All History</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                    <tr class="bg-slate-50">
                        <th class="px-lg py-md font-label-lg text-primary border-b-2 border-outline-variant">Transaction ID</th>
                        <th class="px-lg py-md font-label-lg text-primary border-b-2 border-outline-variant">Recipient</th>
                        <th class="px-lg py-md font-label-lg text-primary border-b-2 border-outline-variant">Item</th>
                        <th class="px-lg py-md font-label-lg text-primary border-b-2 border-outline-variant">Status</th>
                        <th class="px-lg py-md font-label-lg text-primary border-b-2 border-outline-variant text-right">Value</th>
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
                        <td class="px-lg py-md font-label-lg text-primary text-right">${{ number_format($tx->total_price, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-lg py-md text-center text-on-surface-variant">No recent transactions found.</td>
                    </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
