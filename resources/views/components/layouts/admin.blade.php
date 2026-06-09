<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin - Inventory Pro' }}</title>
    @vite(['resources/css/app.css'])
    @livewireStyles
</head>
<body class="bg-background text-on-background min-h-screen flex">
<!-- SideNavBar Component -->
<aside class="fixed left-0 top-0 h-full w-[280px] bg-primary-container flex flex-col py-lg border-r border-outline-variant z-50">
    <div class="px-gutter mb-xl">
        <h1 class="font-headline-lg text-headline-lg text-on-secondary">Inventory Admin</h1>
        <p class="font-label-md text-label-md text-on-primary-container">System Control</p>
    </div>
    <nav class="flex-1 space-y-1 px-sm">
        <!-- Dashboard -->
        <a class="flex items-center gap-sm px-md py-sm transition-colors duration-200
            {{ request()->routeIs('admin.dashboard') ? 'border-l-4 border-on-tertiary-container text-on-secondary font-bold bg-primary' : 'text-on-primary-container hover:bg-primary hover:text-on-secondary' }}"
           href="{{ route('admin.dashboard') }}" wire:navigate>
            <span class="material-symbols-outlined" data-icon="dashboard">dashboard</span>
            <span class="font-label-lg text-label-lg">Dashboard</span>
        </a>
        <!-- User Management -->
        <a class="flex items-center gap-sm px-md py-sm transition-colors duration-200
            {{ request()->routeIs('admin.users') ? 'border-l-4 border-on-tertiary-container text-on-secondary font-bold bg-primary' : 'text-on-primary-container hover:bg-primary hover:text-on-secondary' }}"
           href="{{ route('admin.users') }}" wire:navigate>
            <span class="material-symbols-outlined" data-icon="group">group</span>
            <span class="font-label-lg text-label-lg">User Management</span>
        </a>
    </nav>
    <div class="mt-auto px-sm space-y-1">
        <a class="flex items-center gap-sm px-md py-sm text-on-primary-container hover:text-on-secondary transition-colors duration-200" href="#">
            <span class="material-symbols-outlined" data-icon="settings">settings</span>
            <span class="font-label-lg text-label-lg">Settings</span>
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex items-center gap-sm px-md py-sm text-on-primary-container hover:text-on-secondary transition-colors duration-200 w-full">
                <span class="material-symbols-outlined" data-icon="logout">logout</span>
                <span class="font-label-lg text-label-lg">Logout</span>
            </button>
        </form>
    </div>
</aside>
<!-- Main Content Area -->
<div class="flex-1 ml-[280px] flex flex-col min-h-screen">
    <!-- TopAppBar Component -->
    <header class="sticky top-0 z-40 bg-surface flex justify-between items-center w-full px-gutter h-16 border-b-2 border-surface-container-high">
        <div class="flex items-center gap-xl flex-1">
            <span class="font-headline-md text-headline-md font-bold text-primary">InventoryPro</span>
            <div class="relative w-full max-w-md">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-on-surface-variant">
                    <span class="material-symbols-outlined" data-icon="search">search</span>
                </span>
                <input class="block w-full pl-10 pr-3 py-2 border border-outline-variant rounded-lg bg-surface-container-low text-body-md focus:ring-2 focus:ring-tertiary focus:border-transparent outline-none transition-all" placeholder="Search systems..." type="text">
            </div>
        </div>
        <nav class="hidden md:flex items-center gap-lg px-lg">
            <a class="font-label-md text-label-md text-on-surface-variant hover:text-primary transition-all" href="#">Reports</a>
            <a class="font-label-md text-label-md text-primary border-b-2 border-primary" href="#">Inventory</a>
            <a class="font-label-md text-label-md text-on-surface-variant hover:text-primary transition-all" href="#">Alerts</a>
        </nav>
        <div class="flex items-center gap-md">
            <button class="material-symbols-outlined text-on-surface-variant hover:text-primary transition-all p-2" data-icon="notifications">notifications</button>
            <button class="material-symbols-outlined text-on-surface-variant hover:text-primary transition-all p-2" data-icon="help">help</button>
            <div class="flex items-center gap-sm ml-sm cursor-pointer group">
                <div class="w-10 h-10 rounded-full bg-surface-container-highest overflow-hidden border-2 border-outline-variant group-hover:border-primary transition-all">
                    <img alt="System Administrator Profile" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAYvpVVNgmB4Ai42zxC0aCx2jc0d4R1rK7twu6Vc8ZOdP0i-GiOPEFlAXlfC1B7YvpZDxESZNu5dKBN4EmpgOPBVK_moJy4GUpBt79VWCPApeItVJAWJvZbYqBdUUdvbC4p_WSlfUGf3-nYGYinkXGlcMhGMOpBb5FiOMhQYHuslFuMnGFf6b5yProrNYJpjGfcEpvjS46Wd2zqx0klhTOaqMBc8ydtDqfNaDcEoGILkwZ9UsYH1FL3v-T8wN7OYAisRsYYZdWk">
                </div>
                <span class="material-symbols-outlined text-on-surface-variant" data-icon="expand_more">expand_more</span>
            </div>
        </div>
    </header>
    <!-- Page Content -->
    <main class="p-gutter max-w-[1440px] mx-auto w-full space-y-lg">
        {{ $slot }}
    </main>
</div>
@livewireScripts
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
