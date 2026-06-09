<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>{{ $title ?? 'Inventory Pro' }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet"/>
    <!-- Material Symbols -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>



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
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            vertical-align: middle;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #F1F5F9;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 10px;
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .chart-container {
            position: relative;
            height: 350px;
            width: 100%;
        }

        /* Custom styling from design files */
        .btn-primary { background-color: #10B981; color: white; min-height: 48px; border-radius: 4px; padding: 0 24px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; transition: background-color 0.2s; }
        .btn-primary:hover { background-color: #059669; }
        .btn-secondary { background-color: #3B82F6; color: white; min-height: 48px; border-radius: 4px; padding: 0 24px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; transition: background-color 0.2s; }
        .btn-secondary:hover { background-color: #2563eb; }
        .btn-ghost { border: 1px solid #e2e8f0; color: #475569; min-height: 48px; border-radius: 4px; padding: 0 24px; font-weight: 600; display: inline-flex; align-items: center; justify-content: center; background: white; transition: background-color 0.2s; }
        .btn-ghost:hover { background-color: #f1f5f9; }
        .btn-icon { color: #475569; padding: 8px; border-radius: 4px; display: inline-flex; align-items: center; justify-content: center; transition: background-color 0.2s, color 0.2s; }
        .btn-icon:hover { background-color: #f1f5f9; color: #3B82F6; }
        .form-input { min-height: 48px; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0 16px; width: 100%; transition: border-color 0.2s, box-shadow 0.2s; }
        .form-input:focus { outline: none; border-color: #3B82F6; box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5); }
        .form-label { display: block; margin-bottom: 8px; font-weight: 600; color: #0f172a; font-size: 16px; }
        .table-header { background-color: #0f172a; color: white; font-weight: 600; padding: 16px; text-align: left; }
        .table-cell { padding: 16px; border-bottom: 1px solid #e2e8f0; }
        .table-row-zebra:nth-child(even) { background-color: #f8fafc; }
        .table-row-zebra:nth-child(odd) { background-color: #ffffff; }
        .card-surface { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; }
        .modal-overlay { background: rgba(15, 23, 42, 0.5); }
        .modal-content { box-shadow: 0px 10px 15px -3px rgba(15, 23, 42, 0.15); }
    </style>

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
