<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin - Inventory Pro' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
          darkMode: "class",
          theme: {
            extend: {
              "colors": {
                      "on-secondary": "#ffffff",
                      "on-tertiary-fixed": "#001a42",
                      "tertiary-fixed-dim": "#adc6ff",
                      "outline-variant": "#c5c6cd",
                      "on-surface-variant": "#45474c",
                      "on-primary-container": "#8590a6",
                      "on-error": "#ffffff",
                      "error": "#ba1a1a",
                      "secondary": "#006c49",
                      "surface-container": "#e5eeff",
                      "on-tertiary-fixed-variant": "#004395",
                      "inverse-primary": "#bcc7de",
                      "on-primary": "#ffffff",
                      "surface-container-highest": "#d3e4fe",
                      "primary-fixed": "#d8e3fb",
                      "on-background": "#0b1c30",
                      "primary-container": "#1e293b",
                      "surface-container-lowest": "#ffffff",
                      "on-tertiary": "#ffffff",
                      "surface-container-high": "#dce9ff",
                      "on-tertiary-container": "#4c8dff",
                      "surface-bright": "#f8f9ff",
                      "on-secondary-fixed-variant": "#005236",
                      "error-container": "#ffdad6",
                      "on-secondary-container": "#00714d",
                      "on-primary-fixed-variant": "#3c475a",
                      "tertiary-container": "#00275b",
                      "secondary-fixed-dim": "#4edea3",
                      "on-primary-fixed": "#111c2d",
                      "inverse-on-surface": "#eaf1ff",
                      "secondary-fixed": "#6ffbbe",
                      "primary-fixed-dim": "#bcc7de",
                      "background": "#f8f9ff",
                      "on-secondary-fixed": "#002113",
                      "tertiary": "#001334",
                      "inverse-surface": "#213145",
                      "on-surface": "#0b1c30",
                      "surface-variant": "#d3e4fe",
                      "surface": "#f8f9ff",
                      "surface-container-low": "#eff4ff",
                      "surface-dim": "#cbdbf5",
                      "outline": "#75777d",
                      "tertiary-fixed": "#d8e2ff",
                      "on-error-container": "#93000a",
                      "secondary-container": "#6cf8bb",
                      "primary": "#091426",
                      "surface-tint": "#545f73"
              },
              "borderRadius": {
                      "DEFAULT": "0.125rem",
                      "lg": "0.25rem",
                      "xl": "0.5rem",
                      "full": "0.75rem"
              },
              "spacing": {
                      "lg": "2rem",
                      "container-max": "1440px",
                      "base": "4px",
                      "xs": "0.5rem",
                      "xl": "3rem",
                      "sm": "1rem",
                      "md": "1.5rem",
                      "gutter": "1.5rem"
              },
              "fontFamily": {
                      "body-md": ["Public Sans"],
                      "body-lg": ["Public Sans"],
                      "label-lg": ["Public Sans"],
                      "display": ["Public Sans"],
                      "headline-lg-mobile": ["Public Sans"],
                      "headline-lg": ["Public Sans"],
                      "label-md": ["Public Sans"],
                      "headline-md": ["Public Sans"]
              },
              "fontSize": {
                      "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                      "body-lg": ["18px", {"lineHeight": "28px", "fontWeight": "400"}],
                      "label-lg": ["16px", {"lineHeight": "20px", "letterSpacing": "0.01em", "fontWeight": "600"}],
                      "display": ["36px", {"lineHeight": "44px", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                      "headline-lg-mobile": ["24px", {"lineHeight": "32px", "fontWeight": "700"}],
                      "headline-lg": ["28px", {"lineHeight": "36px", "fontWeight": "700"}],
                      "label-md": ["14px", {"lineHeight": "16px", "fontWeight": "600"}],
                      "headline-md": ["20px", {"lineHeight": "28px", "fontWeight": "600"}]
              }
            },
          },
        }
    </script>
    @livewireStyles
    <style>
        body {
            font-family: 'Public Sans', sans-serif;
            background-color: #F8FAFC;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .chart-container {
            background-image: linear-gradient(to right, #E2E8F0 1px, transparent 1px), linear-gradient(to bottom, #E2E8F0 1px, transparent 1px);
            background-size: 40px 40px;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .table-stripe tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .focus-ring:focus-within {
            outline: 2px solid #3B82F6;
            outline-offset: 2px;
        }
    </style>
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
