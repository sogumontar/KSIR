<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Login | Inventory Pro' }}</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen flex flex-col justify-center items-center p-sm md:p-gutter">
    {{ $slot }}

    <!-- Visual Background Element (Subtle) -->
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="absolute top-[-10%] right-[-5%] w-[400px] h-[400px] rounded-full bg-surface-container opacity-50 blur-3xl"></div>
        <div class="absolute bottom-[-10%] left-[-5%] w-[600px] h-[600px] rounded-full bg-surface-container opacity-30 blur-3xl"></div>
    </div>
</body>
</html>
