<div>
    <!-- Header Section -->
    <section class="mb-lg">
        <h2 class="font-display text-display text-primary">Admin: Executive Overview</h2>
        <p class="font-body-lg text-body-lg text-on-surface-variant">Monitor platform growth and user registration metrics.</p>
    </section>
    <!-- KPI Cards Grid -->
    <section class="@container grid grid-cols-1 @sm:grid-cols-2 @md:grid-cols-3 gap-gutter">
        <!-- Total Users -->
        <div class="bg-surface-container-lowest border border-outline-variant p-lg rounded-xl shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-md">
                <span class="font-label-md text-label-md text-on-surface-variant">Total Users (This Month)</span>
                <span class="material-symbols-outlined text-secondary" data-icon="groups">groups</span>
            </div>
            <div class="flex items-baseline gap-sm">
                <span class="font-display text-display text-primary">{{ number_format($totalUsers) }}</span>
                <span class="font-label-md text-label-md {{ $userGrowth >= 0 ? 'text-secondary' : 'text-error' }}">
                    {{ $userGrowth >= 0 ? '+' : '' }}{{ $userGrowth }}% MoM
                </span>
            </div>
            <div class="mt-md h-12 w-full flex items-end gap-1">
                <!-- Tiny Sparkline Mockup -->
                <div class="flex-1 bg-tertiary-fixed h-[40%] rounded-sm"></div>
                <div class="flex-1 bg-tertiary-fixed h-[60%] rounded-sm"></div>
                <div class="flex-1 bg-tertiary-fixed h-[50%] rounded-sm"></div>
                <div class="flex-1 bg-tertiary-fixed h-[80%] rounded-sm"></div>
                <div class="flex-1 bg-tertiary-fixed h-[70%] rounded-sm"></div>
                <div class="flex-1 bg-tertiary-fixed h-[95%] rounded-sm"></div>
            </div>
        </div>
        <!-- New This Week -->
        <div class="bg-surface-container-lowest border border-outline-variant p-lg rounded-xl shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-md">
                <span class="font-label-md text-label-md text-on-surface-variant">New This Week</span>
                <span class="material-symbols-outlined text-tertiary" data-icon="person_add">person_add</span>
            </div>
            <div class="flex items-baseline gap-sm">
                <span class="font-display text-display text-primary">+{{ $newThisWeek }}</span>
                <span class="text-on-primary-container font-label-md text-label-md">vs prev week</span>
            </div>
            <div class="mt-md h-12 w-full flex items-end gap-1">
                <div class="flex-1 bg-tertiary-fixed-dim h-[30%] rounded-sm"></div>
                <div class="flex-1 bg-tertiary-fixed-dim h-[45%] rounded-sm"></div>
                <div class="flex-1 bg-tertiary-fixed-dim h-[60%] rounded-sm"></div>
                <div class="flex-1 bg-tertiary-fixed-dim h-[55%] rounded-sm"></div>
                <div class="flex-1 bg-tertiary-fixed-dim h-[75%] rounded-sm"></div>
                <div class="flex-1 bg-tertiary-fixed-dim h-[90%] rounded-sm"></div>
            </div>
        </div>
        <!-- Active Sessions -->
        <div class="bg-surface-container-lowest border border-outline-variant p-lg rounded-xl shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-md">
                <span class="font-label-md text-label-md text-on-surface-variant">Active Sessions</span>
                <span class="material-symbols-outlined text-secondary" data-icon="bolt">bolt</span>
            </div>
            <div class="flex items-baseline gap-sm">
                <span class="font-display text-display text-primary">{{ $activeSessions }}</span>
                <span class="text-on-primary-container font-label-md text-label-md">Real-time</span>
            </div>
            <div class="mt-md h-12 w-full flex items-end gap-1">
                <div class="flex-1 bg-secondary-fixed-dim h-[60%] rounded-sm"></div>
                <div class="flex-1 bg-secondary-fixed-dim h-[70%] rounded-sm"></div>
                <div class="flex-1 bg-secondary-fixed-dim h-[55%] rounded-sm"></div>
                <div class="flex-1 bg-secondary-fixed-dim h-[80%] rounded-sm"></div>
                <div class="flex-1 bg-secondary-fixed-dim h-[65%] rounded-sm"></div>
                <div class="flex-1 bg-secondary-fixed-dim h-[85%] rounded-sm"></div>
            </div>
        </div>
    </section>
    <!-- Main Chart Section -->
    <section class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-md mb-lg">
            <div>
                <h3 class="font-headline-md text-headline-md text-primary">User Registration Growth</h3>
                <p class="font-body-md text-body-md text-on-surface-variant">Visualizing registration trends across historical periods.</p>
            </div>
            <!-- Chart Filters (Segmented Control) -->
            <div class="flex bg-surface-container-low p-1 rounded-lg border border-outline-variant">
                <button wire:click="setChartFilter('daily')"
                        class="px-md py-xs font-label-md text-label-md rounded-md transition-all {{ $chartFilter === 'daily' ? 'bg-surface-container-lowest text-primary shadow-sm' : 'text-on-surface-variant hover:text-primary' }}">
                    Daily
                </button>
                <button wire:click="setChartFilter('monthly')"
                        class="px-md py-xs font-label-md text-label-md rounded-md transition-all {{ $chartFilter === 'monthly' ? 'bg-surface-container-lowest text-primary shadow-sm' : 'text-on-surface-variant hover:text-primary' }}">
                    Monthly
                </button>
                <button wire:click="setChartFilter('annual')"
                        class="px-md py-xs font-label-md text-label-md rounded-md transition-all {{ $chartFilter === 'annual' ? 'bg-surface-container-lowest text-primary shadow-sm' : 'text-on-surface-variant hover:text-primary' }}">
                    Annual
                </button>
            </div>
        </div>
        <!-- Chart Placeholder with custom SVG/WebGL or CSS simulation -->
        <div class="w-full h-[250px] md:h-[400px] chart-container relative rounded-lg overflow-hidden border border-outline-variant bg-surface">
            <!-- Data Points Representation -->
            <svg class="absolute inset-0 w-full h-full preserve-3d" viewBox="0 0 1000 400">
                <!-- High Contrast Area Fill -->
                <path class="opacity-20" d="M0,400 L0,320 C100,340 200,280 300,300 C400,320 500,240 600,260 C700,280 800,180 900,200 L1000,150 L1000,400 Z" fill="url(#gradient-area)"></path>
                <!-- High Contrast Line -->
                <path d="M0,320 C100,340 200,280 300,300 C400,320 500,240 600,260 C700,280 800,180 900,200 L1000,150" fill="none" stroke="#006c49" stroke-linecap="round" stroke-width="4"></path>
                <defs>
                    <linearGradient id="gradient-area" x1="0%" x2="0%" y1="0%" y2="100%">
                        <stop offset="0%" style="stop-color:#006c49;stop-opacity:1"></stop>
                        <stop offset="100%" style="stop-color:#006c49;stop-opacity:0"></stop>
                    </linearGradient>
                </defs>
                <!-- Interaction Nodes -->
                <circle class="animate-pulse" cx="300" cy="300" fill="#006c49" r="6"></circle>
                <circle class="animate-pulse" cx="600" cy="260" fill="#006c49" r="6"></circle>
                <circle class="animate-pulse" cx="1000" cy="150" fill="#006c49" r="6"></circle>
            </svg>
            <!-- Chart Axis Labels -->
            <div class="absolute bottom-4 left-4 right-4 flex justify-between font-label-md text-label-md text-on-surface-variant">
                <span class="">Mon</span>
                <span class="">Tue</span>
                <span class="">Wed</span>
                <span class="">Thu</span>
                <span class="">Fri</span>
                <span class="">Sat</span>
                <span class="">Sun</span>
            </div>
        </div>
    </section>
    <!-- Bottom Data Table / Recent Activity (Bento Grid Style) -->
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-gutter pb-xl">
        <!-- User Registration List -->
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden flex flex-col">
            <div class="p-md border-b border-outline-variant flex justify-between items-center bg-surface-container-low">
                <h4 class="font-headline-md text-headline-md text-primary">Recent Registrations</h4>
                <button class="text-tertiary font-label-md text-label-md hover:underline">View All</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[500px]">
                    <thead class="bg-primary text-on-secondary">
                    <tr>
                        <th class="p-md font-label-md text-label-md">User Name</th>
                        <th class="p-md font-label-md text-label-md">Role</th>
                        <th class="p-md font-label-md text-label-md">Registered</th>
                        <th class="p-md font-label-md text-label-md">Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($recentUsers as $user)
                    <tr class="border-b border-outline-variant hover:bg-surface-container-low transition-colors {{ $loop->even ? 'bg-surface-container-low/30' : '' }}">
                        <td class="p-md font-body-md text-body-md text-primary">{{ $user->name }}</td>
                        <td class="p-md font-body-md text-body-md">{{ $user->role ?? 'User' }}</td>
                        <td class="p-md font-body-md text-body-md">{{ $user->created_at->diffForHumans() }}</td>
                        <td class="p-md">
                            @if($user->is_active ?? true)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[12px] font-bold bg-secondary-container text-on-secondary-container">Active</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[12px] font-bold bg-surface-variant text-on-surface-variant">Pending</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @if($recentUsers->isEmpty())
                    <tr>
                        <td colspan="4" class="p-md text-center text-on-surface-variant font-body-md">No recent registrations found.</td>
                    </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Platform Health / System Alerts -->
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-lg flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-sm mb-lg">
                    <span class="material-symbols-outlined text-secondary" data-icon="verified_user" style="font-variation-settings: 'FILL' 1;">verified_user</span>
                    <h4 class="font-headline-md text-headline-md text-primary">System Health</h4>
                </div>
                <div class="space-y-md">
                    <div class="flex justify-between items-center p-md border border-outline-variant rounded-lg bg-surface-container-low">
                        <div class="flex flex-col">
                            <span class="font-label-lg text-label-lg text-primary">Database Sync</span>
                            <span class="font-body-md text-body-md text-on-surface-variant">Last updated 4 mins ago</span>
                        </div>
                        <span class="material-symbols-outlined text-secondary" data-icon="check_circle">check_circle</span>
                    </div>
                    <div class="flex justify-between items-center p-md border border-outline-variant rounded-lg bg-surface-container-low">
                        <div class="flex flex-col">
                            <span class="font-label-lg text-label-lg text-primary">API Gateway</span>
                            <span class="font-body-md text-body-md text-on-surface-variant">Latency: 42ms</span>
                        </div>
                        <span class="material-symbols-outlined text-secondary" data-icon="check_circle">check_circle</span>
                    </div>
                </div>
            </div>
            <div class="mt-lg pt-lg border-t border-outline-variant">
                <div class="flex items-center justify-between text-on-surface-variant font-label-md text-label-md">
                    <span class="">Uptime: 99.98%</span>
                    <span class="text-secondary font-bold">ALL SYSTEMS GO</span>
                </div>
                <div class="w-full h-2 bg-surface-container-high rounded-full mt-sm overflow-hidden">
                    <div class="h-full bg-secondary w-[99.98%]"></div>
                </div>
            </div>
        </div>
    </section>
</div>
