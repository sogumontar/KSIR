<!DOCTYPE html>
<html class="scroll-smooth" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=5.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'CLEAN LAB — Premium Service Shoes, Helmets, Bags, Hats & Sandals' }}</title>
    <meta name="description" content="CLEAN LAB — Serahkan pembersihan Anda kepada ahlinya! Spesialis cuci sepatu, sandal, helm, tas, topi & koper di Apartemen Menara Cawang, Jakarta Timur. Gratis konsultasi & antar jemput radius 3KM.">
    <link rel="icon" type="image/jpeg" href="{{ asset('images/cleanlab/logo.jpg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css'])
    @livewireStyles
    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
        [x-cloak] { display: none !important; }
        /* Hide scrollbar on horizontal mobile pills */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="min-h-screen bg-[#060B14] text-slate-100 antialiased selection:bg-emerald-500 selection:text-white overflow-x-hidden pb-20 sm:pb-0">
    {{ $slot }}
    @livewireScripts
</body>
</html>
