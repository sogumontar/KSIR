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

<header class="h-16 bg-surface border-b border-outline-variant shadow-sm flex items-center justify-between px-lg sticky top-0 z-30">
    <h1 class="font-display text-lg font-bold text-primary">Inventory Pro</h1>
    <div class="flex items-center gap-sm">
        <a href="{{ route('customer.dashboard') }}" class="font-label-lg text-primary">Dashboard</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="font-label-lg text-error">Logout</button>
        </form>
    </div>
</header>

<main class="p-md lg:p-lg max-w-[1440px] mx-auto w-full">
    {{ $slot }}
</main>

@livewireScripts
</body>
</html>
