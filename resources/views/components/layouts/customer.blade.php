<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>{{ $title ?? 'Inventory Pro - Customer' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css'])
    @livewireStyles
</head>
<body class="bg-[#F8FAFC] font-body-md text-on-surface">

@php
    $cartCount = array_sum(array_column(session()->get('cart', []), 'quantity'));
    $user = auth()->user();
    $avatarUrl = $user->photo_path
        ? Storage::disk('public')->url($user->photo_path)
        : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&color=7F9CF5&background=EBF4FF&size=64';
@endphp

<header class="h-16 bg-surface border-b border-outline-variant shadow-sm flex items-center px-4 sm:px-6 sticky top-0 z-30 gap-3">
    {{-- Logo / Brand --}}
    <a href="{{ route('customer.dashboard') }}" class="flex-1 min-w-0 truncate text-base sm:text-lg font-bold text-primary leading-none">
        Inventory Pro
    </a>

    {{-- Nav Actions --}}
    <nav class="flex-shrink-0 flex items-center gap-1 sm:gap-2">

        {{-- Cart Icon --}}
        <a href="{{ route('customer.cart') }}"
           title="View Cart"
           class="relative w-10 h-10 flex items-center justify-center rounded-xl text-on-surface hover:bg-surface-container hover:text-primary transition-colors">
            <span class="material-symbols-outlined text-[22px]">shopping_cart</span>
            @if($cartCount > 0)
                <span class="absolute top-1 right-1 bg-error text-white text-[9px] font-bold min-w-[16px] h-4 flex items-center justify-center rounded-full px-0.5 shadow">{{ $cartCount }}</span>
            @endif
        </a>

        <span class="text-outline-variant w-px h-5 mx-1"></span>

        {{-- Profile Avatar --}}
        <a href="{{ route('customer.profile') }}"
           title="My Profile"
           class="w-9 h-9 rounded-full overflow-hidden border-2 border-outline-variant hover:border-primary transition-colors shadow-sm flex-shrink-0">
            <img src="{{ $avatarUrl }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
        </a>

        <span class="text-outline-variant w-px h-5 mx-1 hidden sm:block"></span>

        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="hidden sm:flex items-center gap-1 px-3 py-2 rounded-xl font-label-md text-error text-sm hover:bg-error/10 transition-colors whitespace-nowrap">
                <span class="material-symbols-outlined text-[18px]">logout</span>
                Logout
            </button>
            {{-- Mobile: icon only --}}
            <button type="submit"
                    class="sm:hidden w-10 h-10 flex items-center justify-center rounded-xl text-error hover:bg-error/10 transition-colors">
                <span class="material-symbols-outlined text-[22px]">logout</span>
            </button>
        </form>
    </nav>
</header>

<main class="p-md lg:p-lg max-w-[1440px] mx-auto w-full">
    {{ $slot }}
</main>

@livewireScripts
</body>
</html>
