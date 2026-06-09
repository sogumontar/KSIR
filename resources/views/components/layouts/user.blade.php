<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>{{ $title ?? 'Inventory Pro' }}</title>

    @vite(['resources/css/app.css'])
    @livewireStyles

    @stack('styles')
</head>
<body class="bg-[#F8FAFC] font-body-md text-on-surface">
<!-- SideNavBar -->
<aside class="fixed left-0 top-0 h-screen w-[280px] flex flex-col bg-primary border-r border-outline-variant z-50 overflow-y-auto custom-scrollbar">
    <div class="p-lg">
        <h1 class="font-display text-display text-white mb-xs">Inventory Pro</h1>
        <p class="font-label-lg text-label-lg text-on-primary-container">Enterprise Management</p>
    </div>
    <nav class="flex-1 px-sm space-y-xs">
        <!-- Dashboard -->
        <a class="flex items-center gap-sm px-md py-sm {{ request()->routeIs('user.dashboard') ? 'text-white border-l-4 border-secondary bg-primary-container' : 'text-on-primary-container hover:text-white hover:bg-primary-container' }} transition-colors duration-200 cursor-pointer active:opacity-80"
           href="{{ route('user.dashboard') }}" wire:navigate>
            <span class="material-symbols-outlined" data-icon="dashboard">dashboard</span>
            <span class="font-label-lg text-label-lg">Dashboard</span>
        </a>
        <!-- Goods & Recipients -->
        <a class="flex items-center gap-sm px-md py-sm {{ request()->routeIs('user.goods') ? 'text-white border-l-4 border-secondary bg-primary-container' : 'text-on-primary-container hover:text-white hover:bg-primary-container' }} transition-colors duration-200 cursor-pointer active:opacity-80"
           href="{{ route('user.goods') }}" wire:navigate>
            <span class="material-symbols-outlined" data-icon="inventory_2">inventory_2</span>
            <span class="font-label-lg text-label-lg">Goods &amp; Recipients</span>
        </a>
        <!-- Goods Inventory -->
        <a class="flex items-center gap-sm px-md py-sm {{ request()->routeIs('user.inventory') ? 'text-white border-l-4 border-secondary bg-primary-container' : 'text-on-primary-container hover:text-white hover:bg-primary-container' }} transition-colors duration-200 cursor-pointer active:opacity-80"
           href="{{ route('user.inventory') }}" wire:navigate>
            <span class="material-symbols-outlined" data-icon="layers">layers</span>
            <span class="font-label-lg text-label-lg">Goods Inventory</span>
        </a>
        <!-- Sales History -->
        <a class="flex items-center gap-sm px-md py-sm {{ request()->routeIs('user.sales') ? 'text-white border-l-4 border-secondary bg-primary-container' : 'text-on-primary-container hover:text-white hover:bg-primary-container' }} transition-colors duration-200 cursor-pointer active:opacity-80"
           href="{{ route('user.sales') }}" wire:navigate>
            <span class="material-symbols-outlined" data-icon="history">history</span>
            <span class="font-label-lg text-label-lg">Sales History</span>
        </a>
    </nav>
    <div class="p-md mt-auto">
        <div class="flex items-center gap-md p-sm bg-primary-container rounded-lg">
            <div class="w-10 h-10 rounded-full bg-secondary flex items-center justify-center text-white font-bold">
                {{ strtoupper(substr(auth()->user()?->name ?? 'User', 0, 2)) }}
            </div>
            <div>
                <p class="text-white font-label-lg">{{ auth()->user()?->name ?? 'User' }}</p>
                <p class="text-on-primary-container text-xs">{{ ucfirst(auth()->user()?->role ?? 'User') }}</p>
            </div>
        </div>
    </div>
</aside>

<!-- TopNavBar -->
<header class="fixed left-[280px] top-0 h-16 flex justify-between items-center px-lg bg-surface border-b border-outline-variant shadow-sm z-40 w-[calc(100%-280px)]">
    <div class="flex items-center gap-lg">
        <div class="relative w-96">
            <span class="material-symbols-outlined absolute left-sm top-1/2 -translate-y-1/2 text-on-surface-variant" data-icon="search">search</span>
            <input class="w-full pl-xl pr-md py-xs bg-surface-container-low border-none rounded-lg focus:ring-2 focus:ring-tertiary font-label-md" placeholder="Search transactions, inventory..." type="text"/>
        </div>
    </div>
    <div class="flex items-center gap-lg">

        <div class="flex items-center gap-sm">
            <button class="p-xs hover:bg-surface-container-low rounded-full transition-all relative">
                <span class="material-symbols-outlined text-primary" data-icon="notifications">notifications</span>
                <span class="absolute top-1 right-1 w-2 h-2 bg-error rounded-full"></span>
            </button>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="p-xs hover:bg-surface-container-low rounded-full transition-all">
                    <span class="material-symbols-outlined text-primary" data-icon="logout">logout</span>
                </button>
            </form>
        </div>
    </div>
</header>

<!-- Main Content -->
<main class="ml-[280px] mt-16 p-lg max-w-[1440px]">
    {{ $slot }}
</main>

@livewireScripts
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
