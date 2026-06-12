<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>{{ $title ?? 'Inventory Pro' }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css'])
    @livewireStyles

    @stack('styles')
</head>
<body class="bg-[#F8FAFC] font-body-md text-on-surface" x-data="{ sidebarOpen: false, notifOpen: false, n: [{read: true}, {read: true}, {read: true}, {read: true}] }">

<!-- Mobile Sidebar Overlay -->
<div
    x-show="sidebarOpen"
    x-transition:enter="transition-opacity ease-linear duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-300"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="sidebarOpen = false"
    class="fixed inset-0 bg-primary/50 backdrop-blur-sm z-40 lg:hidden"
    x-cloak
></div>

<!-- SideNavBar -->
<aside
    class="fixed left-0 top-0 h-screen w-[280px] flex flex-col bg-primary border-r border-outline-variant z-50 overflow-y-auto custom-scrollbar
           transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out"
    :class="sidebarOpen && 'translate-x-0'"
>
    <div class="p-lg">
        <h1 class="font-display text-display text-white mb-xs">Inventory Pro</h1>
        <p class="font-label-lg text-label-lg text-on-primary-container">Enterprise Management</p>
    </div>
    <nav class="flex-1 px-sm space-y-xs">
        <!-- Dashboard -->
        <a class="flex items-center gap-sm px-md py-sm {{ request()->routeIs('user.dashboard') ? 'text-white border-l-4 border-secondary bg-primary-container' : 'text-on-primary-container hover:text-white hover:bg-primary-container' }} transition-colors duration-200 cursor-pointer active:opacity-80"
           href="{{ route('user.dashboard') }}" wire:navigate @click="sidebarOpen = false">
            <span class="material-symbols-outlined" data-icon="dashboard">dashboard</span>
            <span class="font-label-lg text-label-lg">Dashboard</span>
        </a>
        <!-- Sales Record -->
        <a class="flex items-center gap-sm px-md py-sm {{ request()->routeIs('user.goods') ? 'text-white border-l-4 border-secondary bg-primary-container' : 'text-on-primary-container hover:text-white hover:bg-primary-container' }} transition-colors duration-200 cursor-pointer active:opacity-80"
           href="{{ route('user.goods') }}" wire:navigate @click="sidebarOpen = false">
            <span class="material-symbols-outlined" data-icon="inventory_2">inventory_2</span>
            <span class="font-label-lg text-label-lg">Sales Record</span>
        </a>
        
        <!-- Goods Inventory -->
        <a class="flex items-center gap-sm px-md py-sm {{ request()->routeIs('user.inventory') ? 'text-white border-l-4 border-secondary bg-primary-container' : 'text-on-primary-container hover:text-white hover:bg-primary-container' }} transition-colors duration-200 cursor-pointer active:opacity-80"
           href="{{ route('user.inventory') }}" wire:navigate @click="sidebarOpen = false">
            <span class="material-symbols-outlined" data-icon="layers">layers</span>
            <span class="font-label-lg text-label-lg">Goods Inventory</span>
        </a>
        
        <!-- Sales Monitoring -->
        <a class="flex items-center gap-sm px-md py-sm {{ request()->routeIs('user.sales') ? 'text-white border-l-4 border-secondary bg-primary-container' : 'text-on-primary-container hover:text-white hover:bg-primary-container' }} transition-colors duration-200 cursor-pointer active:opacity-80"
           href="{{ route('user.sales') }}" wire:navigate @click="sidebarOpen = false">
            <span class="material-symbols-outlined" data-icon="history">history</span>
            <span class="font-label-lg text-label-lg">Sales Monitoring</span>
        </a>
        
        <!-- Expenses -->
        <a class="flex items-center gap-sm px-md py-sm {{ request()->routeIs('user.expenses') ? 'text-white border-l-4 border-secondary bg-primary-container' : 'text-on-primary-container hover:text-white hover:bg-primary-container' }} transition-colors duration-200 cursor-pointer active:opacity-80"
            href="{{ route('user.expenses') }}" wire:navigate @click="sidebarOpen = false">
             <span class="material-symbols-outlined" data-icon="receipt_long">receipt_long</span>
             <span class="font-label-lg text-label-lg">Personal Expenses</span>
        </a>
        <!-- Profile -->
        <a class="flex items-center gap-sm px-md py-sm {{ request()->routeIs('user.profile') ? 'text-white border-l-4 border-secondary bg-primary-container' : 'text-on-primary-container hover:text-white hover:bg-primary-container' }} transition-colors duration-200 cursor-pointer active:opacity-80"
           href="{{ route('user.profile') }}" wire:navigate @click="sidebarOpen = false">
            <span class="material-symbols-outlined" data-icon="person">person</span>
            <span class="font-label-lg text-label-lg">Profile</span>
        </a>
    </nav>
    <div class="p-md mt-auto">
        <a href="{{ route('user.profile') }}" wire:navigate @click="sidebarOpen = false" class="flex items-center gap-md p-sm bg-primary-container rounded-lg hover:bg-primary/80 transition-colors cursor-pointer">
            @if(auth()->user()?->photo_path)
                <img src="{{ asset('storage/' . auth()->user()->photo_path) }}" alt="{{ auth()->user()?->name }}" class="w-10 h-10 rounded-full object-cover bg-secondary">
            @else
                <div class="w-10 h-10 rounded-full bg-secondary flex items-center justify-center text-white font-bold">
                    {{ strtoupper(substr(auth()->user()?->name ?? 'User', 0, 2)) }}
                </div>
            @endif
            <div>
                <p class="text-white font-label-lg">{{ auth()->user()?->name ?? 'User' }}</p>
                <p class="text-on-primary-container text-xs">{{ auth()->user()?->is_admin ? 'Admin' : 'Staff' }}</p>
            </div>
        </a>
    </div>
</aside>

<!-- Main Content Area -->
<div class="lg:ml-[280px] flex flex-col min-h-screen transition-all duration-300">
    <!-- TopNavBar -->
    <header class="sticky top-0 h-16 flex justify-between items-center px-md lg:px-lg bg-surface border-b border-outline-variant shadow-sm z-30">
        <!-- Hamburger (mobile/tablet only) -->
        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 -ml-2 rounded-lg text-on-surface-variant hover:bg-surface-container-low transition-colors" x-cloak>
            <span class="material-symbols-outlined" data-icon="menu">menu</span>
        </button>
        <div class="ml-auto flex items-center gap-lg">
            <div class="flex items-center gap-sm">
                <!-- Notification Button & Dropdown -->
                <div class="relative" @click.away="notifOpen = false">
                    <button @click="notifOpen = !notifOpen" class="p-xs hover:bg-surface-container-low rounded-full transition-all relative">
                        <span class="material-symbols-outlined text-primary" data-icon="notifications">notifications</span>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-error rounded-full" x-show="notifOpen === false"></span>
                    </button>
                    <!-- Notification Dropdown Panel -->
                    <div x-show="notifOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" @click.stop class="absolute right-0 top-full mt-2 w-80 bg-white rounded-xl shadow-lg border border-slate-200 z-50 overflow-y-auto" x-cloak style="min-width: 320px;">
                        <div class="p-4 border-b border-slate-200 bg-slate-50 flex justify-between items-center sticky top-0">
                            <span class="font-label-md text-slate-700 font-semibold">Notifications</span>
                            <button @click="notifOpen = false; $nextTick(() => { const n = [true]; for(let i = 0; i < n.length; i++) n[i].read = false; })" class="text-xs text-slate-500 hover:text-primary transition-colors">Mark all as read</button>
                        </div>
                        <div class="divide-y divide-y-0">
                            <!-- Dummy Notification 1 -->
                            <div class="p-3 hover:bg-slate-50 transition-colors cursor-pointer flex items-start gap-3" :class="!n[0].read && 'bg-slate-50'">
                                <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-amber-700 text-sm">inventory_2</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-slate-900 font-medium leading-tight">New order created</p>
                                    <p class="text-xs text-slate-500">Sales Record #1234 — 5 min ago</p>
                                </div>
                            </div>
                            <!-- Dummy Notification 2 -->
                            <div class="p-3 hover:bg-slate-50 transition-colors cursor-pointer flex items-start gap-3" :class="!n[1].read && 'bg-slate-50'">
                                <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-red-600 text-sm">warning</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-slate-900 font-medium leading-tight">Stock alert: Item X low</p>
                                    <p class="text-xs text-slate-500">Goods Inventory — 12 min ago</p>
                                </div>
                            </div>
                            <!-- Dummy Notification 3 -->
                            <div class="p-3 hover:bg-slate-50 transition-colors cursor-pointer flex items-start gap-3" :class="!n[2].read && 'bg-slate-50'">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-blue-600 text-sm">local_shipping</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-slate-900 font-medium leading-tight">Loan overdue reminder</p>
                                    <p class="text-xs text-slate-500">Sales Record #1190 — 1 hour ago</p>
                                </div>
                            </div>
                            <!-- Dummy Notification 4 -->
                            <div class="p-3 hover:bg-slate-50 transition-colors cursor-pointer flex items-start gap-3" :class="!n[3].read && 'bg-slate-50'">
                                <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-green-600 text-sm">check_circle</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-slate-900 font-medium leading-tight">Delivery confirmed</p>
                                    <p class="text-xs text-slate-500">Sales Record #1098 — 30 min ago</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-3 border-t border-slate-100">
                            <a href="#" class="block text-center text-sm text-primary hover:underline">View all notifications</a>
                        </div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="p-xs hover:bg-surface-container-low rounded-full transition-all">
                        <span class="material-symbols-outlined text-primary" data-icon="logout">logout</span>
                    </button>
                </form>
            </div>
        </div>
    </header>
    <!-- Page Content -->
    <main class="p-md lg:p-lg max-w-[1440px] mx-auto w-full space-y-lg relative">
        @php
            $isAuthorized = true;
            if (request()->routeIs('user.goods') && !auth()->user()?->menu_sales_record) $isAuthorized = false;
            if (request()->routeIs('user.inventory') && !auth()->user()?->menu_goods_inventory) $isAuthorized = false;
            if (request()->routeIs('user.sales') && !auth()->user()?->menu_sales_monitoring) $isAuthorized = false;
            if (request()->routeIs('user.expenses') && !auth()->user()?->menu_expenses) $isAuthorized = false;
        @endphp

        @if(!$isAuthorized)
            <div class="absolute inset-0 z-50 flex items-center justify-center p-4" style="min-height: 50vh;">
                <div class="bg-white/90 backdrop-blur-xl p-8 rounded-2xl shadow-2xl border border-slate-200 text-center max-w-md w-full relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-1 bg-error"></div>
                    <span class="material-symbols-outlined text-6xl text-error mb-4">lock</span>
                    <h2 class="text-2xl font-headline-lg font-bold text-slate-900 mb-2">Access Restricted</h2>
                    <p class="text-slate-600 font-body-md">You do not have permission to use this module. The page is in view-only mode. Please contact your administrator to request full access.</p>
                </div>
            </div>
            <div inert class="blur-[4px] opacity-60 pointer-events-none select-none transition-all duration-300">
                {{ $slot }}
            </div>
        @else
            {{ $slot }}
        @endif
    </main>
</div>

@livewireScripts
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@stack('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.hook('request', ({ fail }) => {
            fail(({ status, preventDefault }) => {
                if (status === 419 || status === 401) {
                    window.location.href = '/';
                    preventDefault();
                }
            });
        });
    });

    setInterval(() => {
        fetch('{{ route('session-check') }}')
            .then(response => {
                if (response.status === 419 || response.status === 401 || response.status === 302) {
                    window.location.href = '/';
                }
                return response.json();
            })
            .then(data => {
                if (data && data.authenticated === false) {
                    window.location.href = '/';
                }
            })
            .catch(() => {});
    }, 15000);
</script>
</body>
</html>