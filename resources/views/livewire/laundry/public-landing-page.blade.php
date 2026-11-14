@php
    $brandName          = $config['brand_name'] ?? 'CLEAN LAB';
    $tagline            = $config['tagline'] ?? 'PREMIUM SERVICE — SHOES, HELMETS, BAGS, HATS, SANDALS';
    $heroBadge          = $config['hero_badge'] ?? '✨ SERAHKAN PEMBERSIHAN ANDA KEPADA AHLINYA!';
    $heroTitle          = $config['hero_title'] ?? 'Bikin Sepatu, Helm, Tas, Topi & Sandal Kembali Seperti Baru.';
    $heroSubtitle       = $config['hero_subtitle'] ?? 'Perawatan pencucian profesional dengan teknologi modern, hasil bersih maksimal, wangi tahan lama, dan gratis konsultasi serta antar-jemput radius 3KM.';
    $waNumber           = preg_replace('/[^0-9]/', '', $config['whatsapp_number'] ?? '6285139763366');
    $waGreeting         = urlencode($config['whatsapp_greeting'] ?? 'Halo Clean Lab! Saya ingin konsultasi / cuci barang.');
    $waLink             = "https://wa.me/{$waNumber}?text={$waGreeting}";
    $igHandle           = $config['instagram_handle'] ?? '@clean_lab77';
    $igClean            = ltrim($igHandle, '@');
    $tiktokHandle       = $config['tiktok_handle'] ?? '@clean_lab77';
    $tiktokClean        = ltrim($tiktokHandle, '@');
    $mapsLink           = $config['google_maps_link'] ?? 'https://maps.app.goo.gl/7KEhxvXJmQnWpAbz6';
    $mapsEmbed          = $config['google_maps_embed_url'] ?? 'https://maps.google.com/maps?q=Apartemen%20Menara%20Cawang%20Jakarta%20Timur&t=&z=16&ie=UTF8&iwloc=&output=embed';
    $addressText        = $config['address_text'] ?? 'Lobby Apartement (B1/B2) Menara Cawang, Kec. Kramat Jati, Kota Jakarta Timur, 13630';
    $opHours            = $config['operating_hours'] ?? 'Senin - Minggu: 09.00 - 21.00 WIB';
    $categories         = $config['categories'] ?? [];
    $pricelistSections  = $config['pricelist_sections'] ?? [];
    $promoBanners       = collect($config['promo_banners'] ?? [])->filter(fn($b) => $b['is_active'] ?? true)->values()->all();
    $features           = $config['features'] ?? [];
    $gallery            = $config['gallery'] ?? [];
    $stats              = $config['stats'] ?? [];
    $logoPath           = $config['logo_path'] ?? 'laundry/website/cleanlab-logo.jpg';
    $pricelistImgPath   = $config['pricelist_image_path'] ?? 'laundry/website/cleanlab-pricelist.jpg';
    $heroImgPath        = $config['hero_image_path'] ?? ($setting?->header_bg_image ?? null);

    $logoUrl = ($logoPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($logoPath))
        ? storage_url($logoPath)
        : route('storage.file', ['path' => 'images/cleanlab/logo.jpg']);

    $pricelistImgUrl = ($pricelistImgPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($pricelistImgPath))
        ? storage_url($pricelistImgPath)
        : route('storage.file', ['path' => 'images/cleanlab/pricelist.jpg']);

    $statusLabels = [
        'open'       => 'Buka Sekarang',
        'unattended' => 'Drop-Off Ready',
        'closed'     => 'Sedang Tutup',
    ];
    $statusColors = [
        'open'       => 'bg-emerald-500/20 text-emerald-300 border-emerald-400/30',
        'unattended' => 'bg-amber-500/20 text-amber-300 border-amber-400/30',
        'closed'     => 'bg-rose-500/20 text-rose-300 border-rose-400/30',
    ];
    $statusDots = [
        'open'       => 'bg-emerald-400 animate-pulse',
        'unattended' => 'bg-amber-400 animate-pulse',
        'closed'     => 'bg-rose-400',
    ];
@endphp

<div class="relative min-h-screen bg-[#060B14] text-slate-100" x-data="{ mobileMenu: false, zoomPricelist: false }">

    {{-- ================= AMBIENT GLOW BACKGROUND ================= --}}
    <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-32 left-1/4 h-[380px] w-[380px] sm:h-[520px] sm:w-[520px] rounded-full bg-emerald-500/15 blur-[120px]"></div>
        <div class="absolute top-1/3 -right-24 h-[340px] w-[340px] sm:h-[480px] sm:w-[480px] rounded-full bg-cyan-500/15 blur-[120px]"></div>
    </div>

    {{-- ================= STICKY NAVBAR (CLEAN LAB EXCLUSIVE & MOBILE OPTIMIZED) ================= --}}
    <header class="sticky top-0 z-50 border-b border-white/10 bg-[#060B14]/90 backdrop-blur-xl">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-3.5 py-2.5 sm:px-6 sm:py-3.5 lg:px-8">
            <a href="#top" class="flex items-center gap-2.5 sm:gap-3 min-w-0">
                <div class="flex h-10 w-14 sm:h-12 sm:w-16 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-white p-1 shadow-md border border-white/20">
                    <img src="{{ $logoUrl }}" alt="{{ $brandName }}" class="h-full w-full object-contain">
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="text-base sm:text-xl font-extrabold tracking-tight text-white truncate">{{ $brandName }}</span>
                        <span class="inline-flex items-center gap-1 rounded-full border px-2 py-0.5 text-[10px] font-bold shrink-0 {{ $statusColors[$storeStatus] ?? $statusColors['open'] }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $statusDots[$storeStatus] ?? $statusDots['open'] }}"></span>
                            {{ $statusLabels[$storeStatus] ?? 'Buka' }}
                        </span>
                    </div>
                    <p class="text-[10px] sm:text-[11px] text-slate-400 truncate">Shoes • Helmets • Bags • Hats • Sandals</p>
                </div>
            </a>

            {{-- Desktop Nav Links --}}
            <nav class="hidden lg:flex items-center gap-6 text-sm font-semibold text-slate-300">
                @if(!empty($promoBanners) || (($config['show_live_promos'] ?? true) && $promos->isNotEmpty()))
                    <a href="#promo" class="hover:text-emerald-400 transition-colors flex items-center gap-1">
                        <span class="h-2 w-2 rounded-full bg-amber-400 animate-ping"></span> Promo
                    </a>
                @endif
                <a href="#pricelist" class="hover:text-emerald-400 transition-colors">Pricelist</a>
                <a href="#kategori" class="hover:text-emerald-400 transition-colors">Layanan</a>
                @if($config['show_order_tracker'] ?? true)
                    <a href="#cek-nota" class="hover:text-emerald-400 transition-colors">Cek Nota</a>
                @endif
                <a href="#keunggulan" class="hover:text-emerald-400 transition-colors">Keunggulan</a>
                <a href="#lokasi" class="hover:text-emerald-400 transition-colors">Lokasi</a>
            </nav>

            <div class="flex items-center gap-2">
                <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer"
                   class="hidden sm:inline-flex items-center gap-1.5 rounded-full bg-gradient-to-r from-emerald-400 to-cyan-400 px-4 py-2 text-xs sm:text-sm font-extrabold text-slate-950 shadow-lg shadow-emerald-500/20 hover:brightness-110 transition-all">
                    <span class="material-symbols-outlined text-base">chat</span>
                    <span>Order WA</span>
                </a>
                <button type="button" @click="mobileMenu = !mobileMenu" aria-label="Menu"
                        class="lg:hidden inline-flex items-center justify-center rounded-xl border border-white/15 bg-white/5 p-2 text-slate-200 hover:bg-white/10">
                    <span class="material-symbols-outlined text-xl" x-text="mobileMenu ? 'close' : 'menu'">menu</span>
                </button>
            </div>
        </div>

        {{-- Mobile Quick Scrollable Nav Pills (Always visible on mobile for 1-tap navigation) --}}
        <div class="lg:hidden flex items-center gap-2 overflow-x-auto no-scrollbar border-t border-white/5 bg-[#08101E]/90 px-3.5 py-2 text-xs font-bold text-slate-300">
            @if(!empty($promoBanners) || (($config['show_live_promos'] ?? true) && $promos->isNotEmpty()))
                <a href="#promo" class="shrink-0 rounded-full bg-amber-500/15 border border-amber-400/30 px-3 py-1 text-amber-300 flex items-center gap-1">
                    🔥 Promo
                </a>
            @endif
            <a href="#pricelist" class="shrink-0 rounded-full bg-white/5 border border-white/10 px-3 py-1 hover:bg-white/10">🏷️ Pricelist</a>
            @if($showcases->isNotEmpty())
                <a href="#hasil" class="shrink-0 rounded-full bg-white/5 border border-white/10 px-3 py-1 hover:bg-white/10">✨ Before/After</a>
            @endif
            @if($config['show_order_tracker'] ?? true)
                <a href="#cek-nota" class="shrink-0 rounded-full bg-white/5 border border-white/10 px-3 py-1 hover:bg-white/10">🔍 Cek Nota</a>
            @endif
            <a href="#kategori" class="shrink-0 rounded-full bg-white/5 border border-white/10 px-3 py-1 hover:bg-white/10">👟 Layanan</a>
            <a href="#lokasi" class="shrink-0 rounded-full bg-white/5 border border-white/10 px-3 py-1 hover:bg-white/10">📍 Lokasi Maps</a>
        </div>

        {{-- Mobile Full Drawer --}}
        <div x-show="mobileMenu" x-cloak x-transition class="lg:hidden border-t border-white/10 bg-[#0A1220] px-4 py-4 space-y-2 shadow-2xl">
            <a @click="mobileMenu = false" href="#promo" class="block rounded-xl px-3.5 py-2.5 text-sm font-bold text-amber-300 bg-amber-500/10">🔥 Promo Sedang Berlangsung</a>
            <a @click="mobileMenu = false" href="#pricelist" class="block rounded-xl px-3.5 py-2.5 text-sm font-semibold text-slate-200 hover:bg-white/5">🏷️ Daftar Harga & Brosur Pricelist</a>
            @if($config['show_order_tracker'] ?? true)
                <a @click="mobileMenu = false" href="#cek-nota" class="block rounded-xl px-3.5 py-2.5 text-sm font-semibold text-slate-200 hover:bg-white/5">🔍 Cek Status Cucian (Nomor Nota)</a>
            @endif
            <a @click="mobileMenu = false" href="#kategori" class="block rounded-xl px-3.5 py-2.5 text-sm font-semibold text-slate-200 hover:bg-white/5">👟 Kategori Barang (Sepatu, Sandal, Helm, Tas)</a>
            <a @click="mobileMenu = false" href="#keunggulan" class="block rounded-xl px-3.5 py-2.5 text-sm font-semibold text-slate-200 hover:bg-white/5">✨ Keunggulan Clean Lab</a>
            <a @click="mobileMenu = false" href="#lokasi" class="block rounded-xl px-3.5 py-2.5 text-sm font-semibold text-slate-200 hover:bg-white/5">📍 Lokasi Menara Cawang & Google Maps</a>
            <div class="pt-2">
                <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer"
                   class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-400 py-3 text-sm font-extrabold text-slate-950">
                    <span class="material-symbols-outlined text-lg">chat</span>
                    Chat WhatsApp Sekarang
                </a>
            </div>
        </div>
    </header>

    {{-- ================= HERO SECTION (MOBILE-FIRST + OFFICIAL LOGO SHOWCASE) ================= --}}
    <section id="top" class="relative overflow-hidden pt-6 pb-12 sm:pt-14 sm:pb-20">
        @if($heroImgPath)
            <div class="pointer-events-none absolute inset-0 opacity-20">
                <img src="{{ storage_url($heroImgPath) }}" alt="{{ $brandName }}" class="h-full w-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-b from-[#060B14] via-[#060B14]/80 to-[#060B14]"></div>
            </div>
        @endif

        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 items-center gap-8 lg:grid-cols-12 lg:gap-12">

                {{-- Brand Logo Banner Card (Shown prominently at top on mobile, right column on desktop) --}}
                <div class="lg:col-span-5 lg:order-2">
                    <div class="relative mx-auto max-w-md lg:max-w-none">
                        <div class="absolute -inset-1 rounded-3xl bg-gradient-to-r from-emerald-500 via-cyan-500 to-indigo-500 opacity-35 blur-xl"></div>
                        <div class="relative rounded-3xl border border-white/15 bg-[#0C1526]/95 p-4 sm:p-6 shadow-2xl">
                            {{-- Official Clean Lab Logo Graphic --}}
                            <div class="overflow-hidden rounded-2xl bg-white p-4 sm:p-6 shadow-inner flex items-center justify-center">
                                <img src="{{ $logoUrl }}" alt="{{ $brandName }} Official Logo" class="w-full max-h-52 sm:max-h-64 object-contain">
                            </div>

                            {{-- Quick Info Strip inside Card --}}
                            <div class="mt-4 grid grid-cols-2 gap-2.5 text-left">
                                <div class="rounded-xl border border-white/10 bg-white/[0.04] p-3">
                                    <div class="flex items-center gap-1.5 text-emerald-400 text-xs font-bold">
                                        <span class="material-symbols-outlined text-base">two_wheeler</span>
                                        <span>Antar Jemput</span>
                                    </div>
                                    <p class="text-xs font-extrabold text-white mt-0.5">Radius 3 KM</p>
                                    <p class="text-[10px] text-slate-400">Menara Cawang & sekitarnya</p>
                                </div>
                                <div class="rounded-xl border border-white/10 bg-white/[0.04] p-3">
                                    <div class="flex items-center gap-1.5 text-cyan-400 text-xs font-bold">
                                        <span class="material-symbols-outlined text-base">sell</span>
                                        <span>Mulai Dari</span>
                                    </div>
                                    <p class="text-xs font-extrabold text-white mt-0.5">Rp10.000 - Rp35.000</p>
                                    <p class="text-[10px] text-slate-400">Shoes, Helmets, Bags, Sandals</p>
                                </div>
                            </div>

                            <div class="mt-3 flex items-center justify-between rounded-xl bg-emerald-500/10 border border-emerald-400/25 px-3.5 py-2.5">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="material-symbols-outlined text-lg text-emerald-400 shrink-0">location_on</span>
                                    <p class="text-[11px] font-semibold text-slate-200 truncate">Lobby Apt. (B1/B2) Menara Cawang, Jaktim</p>
                                </div>
                                <a href="{{ $mapsLink }}" target="_blank" rel="noopener noreferrer" class="shrink-0 ml-2 rounded-lg bg-emerald-400 px-2.5 py-1 text-[11px] font-extrabold text-slate-950">
                                    Maps →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Hero Copy Column --}}
                <div class="lg:col-span-7 lg:order-1 space-y-5 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 rounded-full border border-emerald-400/30 bg-emerald-500/10 px-3.5 py-1.5 text-[11px] sm:text-xs font-extrabold tracking-wide text-emerald-300">
                        <span>{{ $heroBadge }}</span>
                    </div>

                    <h1 class="text-2xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white leading-[1.18]">
                        {{ $heroTitle }}
                    </h1>

                    <p class="mx-auto lg:mx-0 max-w-2xl text-sm sm:text-base text-slate-300 leading-relaxed">
                        {{ $heroSubtitle }}
                    </p>

                    {{-- Item Chips --}}
                    <div class="flex flex-wrap justify-center lg:justify-start gap-1.5 sm:gap-2">
                        @foreach(['👟 Shoes (30K-85K)', '🩴 Sandals (10K-50K)', '🪖 Helmets (20K-50K)', '👜 Bags & Caps (35K-55K)', '🧳 Carrier & Koper'] as $pill)
                            <span class="rounded-xl border border-white/10 bg-white/5 px-3 py-1.5 text-[11px] sm:text-xs font-semibold text-slate-200">
                                {{ $pill }}
                            </span>
                        @endforeach
                    </div>

                    {{-- CTA Buttons (Full-width stacked on mobile, row on desktop) --}}
                    <div class="grid grid-cols-1 sm:flex sm:flex-wrap items-center justify-center lg:justify-start gap-3 pt-1">
                        <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-emerald-400 via-teal-400 to-cyan-400 px-6 py-3.5 text-sm sm:text-base font-extrabold text-slate-950 shadow-xl shadow-emerald-500/25 hover:brightness-110 transition-all">
                            <span class="material-symbols-outlined text-xl">chat</span>
                            Order / Pickup via WhatsApp
                        </a>
                        <div class="grid grid-cols-2 sm:flex gap-2.5">
                            <a href="#pricelist"
                               class="inline-flex items-center justify-center gap-1.5 rounded-2xl border border-white/15 bg-white/5 px-4 py-3.5 text-xs sm:text-sm font-bold text-white hover:bg-white/10 transition-all">
                                <span class="material-symbols-outlined text-cyan-400 text-lg">receipt_long</span>
                                Lihat Pricelist
                            </a>
                            @if($config['show_order_tracker'] ?? true)
                                <a href="#cek-nota"
                                   class="inline-flex items-center justify-center gap-1.5 rounded-2xl border border-white/15 bg-white/5 px-4 py-3.5 text-xs sm:text-sm font-bold text-white hover:bg-white/10 transition-all">
                                    <span class="material-symbols-outlined text-emerald-400 text-lg">manage_search</span>
                                    Cek Nota
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Stats Row --}}
                    @if(!empty($stats))
                        <div class="grid grid-cols-3 gap-2 sm:gap-4 pt-5 border-t border-white/10 text-center lg:text-left">
                            @foreach($stats as $st)
                                <div class="rounded-xl bg-white/[0.02] p-2 sm:p-0 sm:bg-transparent">
                                    <p class="text-xs sm:text-base font-extrabold text-emerald-400 leading-tight">{{ $st['value'] ?? '' }}</p>
                                    <p class="text-[10px] sm:text-xs text-slate-400 mt-0.5 leading-snug">{{ $st['label'] ?? '' }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ================= DYNAMIC PROMO CAMPAIGNS (LOGO, TEXT, DISKON, MASA BERLAKU, TNC) ================= --}}
    @if(!empty($promoBanners) || (($config['show_live_promos'] ?? true) && $promos->isNotEmpty()))
    <section id="promo" class="py-10 sm:py-16 border-t border-white/10 bg-gradient-to-b from-[#091224] to-[#060B14]">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 mb-8 text-center sm:text-left">
                <div>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-400/15 border border-amber-400/30 px-3 py-1 text-[11px] font-extrabold uppercase tracking-wider text-amber-300">
                        <span class="material-symbols-outlined text-sm">campaign</span>
                        Special Promo & Deals
                    </span>
                    <h2 class="mt-2 text-2xl sm:text-3xl font-extrabold text-white">Promo yang Sedang Berlangsung</h2>
                </div>
                <p class="text-xs sm:text-sm text-slate-400">Klaim promo saat konsultasi WhatsApp atau drop-off langsung di workshop.</p>
            </div>

            {{-- Rich Dynamic Promo Banners from CMS --}}
            @if(!empty($promoBanners))
                <div class="grid grid-cols-1 {{ count($promoBanners) > 1 ? 'lg:grid-cols-2' : '' }} gap-6 mb-6">
                    @foreach($promoBanners as $pIdx => $banner)
                        @php
                            $promoLogo = !empty($banner['logo_path']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($banner['logo_path'])
                                ? storage_url($banner['logo_path'])
                                : $logoUrl;
                        @endphp
                        <div class="relative overflow-hidden rounded-3xl border border-amber-400/35 bg-gradient-to-br from-amber-500/15 via-[#0E1A2F] to-[#08101E] p-5 sm:p-7 shadow-xl"
                             x-data="{ openTnc: false }">
                            <div class="flex flex-col sm:flex-row items-start gap-4 sm:gap-5">
                                {{-- Promo Logo --}}
                                <div class="flex items-center gap-3 sm:block w-full sm:w-auto">
                                    <div class="h-16 w-16 sm:h-20 sm:w-20 shrink-0 rounded-2xl bg-white p-2 shadow-md border border-amber-300/40 flex items-center justify-center overflow-hidden">
                                        <img src="{{ $promoLogo }}" alt="Promo Logo" class="h-full w-full object-contain">
                                    </div>
                                    <div class="sm:hidden flex-1">
                                        @if(!empty($banner['badge']))
                                            <span class="inline-block rounded-full bg-amber-400/20 border border-amber-400/40 px-2.5 py-0.5 text-[10px] font-extrabold text-amber-300">
                                                {{ $banner['badge'] }}
                                            </span>
                                        @endif
                                        @if(!empty($banner['discount_text']))
                                            <div class="mt-1 inline-block rounded-lg bg-gradient-to-r from-amber-400 to-emerald-400 px-3 py-1 text-xs font-extrabold text-slate-950">
                                                {{ $banner['discount_text'] }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Promo Body --}}
                                <div class="flex-1 min-w-0 space-y-2.5">
                                    <div class="hidden sm:flex flex-wrap items-center justify-between gap-2">
                                        @if(!empty($banner['badge']))
                                            <span class="rounded-full bg-amber-400/20 border border-amber-400/40 px-3 py-1 text-xs font-extrabold text-amber-300">
                                                {{ $banner['badge'] }}
                                            </span>
                                        @endif
                                        @if(!empty($banner['discount_text']))
                                            <span class="rounded-xl bg-gradient-to-r from-amber-400 to-emerald-400 px-3.5 py-1 text-xs sm:text-sm font-extrabold text-slate-950 shadow">
                                                {{ $banner['discount_text'] }}
                                            </span>
                                        @endif
                                    </div>

                                    <h3 class="text-lg sm:text-xl font-extrabold text-white leading-snug">
                                        {{ $banner['title'] ?? '' }}
                                    </h3>

                                    @if(!empty($banner['description']))
                                        <p class="text-xs sm:text-sm text-slate-200 leading-relaxed">
                                            {{ $banner['description'] }}
                                        </p>
                                    @endif

                                    {{-- Validity Period & Action Row --}}
                                    <div class="pt-3 border-t border-white/10 flex flex-wrap items-center justify-between gap-3">
                                        <div class="flex items-center gap-1.5 text-xs text-amber-200/90 font-medium">
                                            <span class="material-symbols-outlined text-sm text-amber-400">event_available</span>
                                            @if(!empty($banner['valid_from']) || !empty($banner['valid_until']))
                                                <span>
                                                    Berlaku:
                                                    {{ !empty($banner['valid_from']) ? \Carbon\Carbon::parse($banner['valid_from'])->translatedFormat('d M Y') : '' }}
                                                    {{ (!empty($banner['valid_from']) && !empty($banner['valid_until'])) ? ' - ' : '' }}
                                                    {{ !empty($banner['valid_until']) ? \Carbon\Carbon::parse($banner['valid_until'])->translatedFormat('d M Y') : 'Selama periode promo' }}
                                                </span>
                                            @else
                                                <span>Promo sedang berlangsung</span>
                                            @endif
                                        </div>

                                        <div class="flex items-center gap-2 w-full sm:w-auto justify-between sm:justify-end">
                                            @if(!empty($banner['tnc']))
                                                <button type="button" @click="openTnc = !openTnc"
                                                        class="inline-flex items-center gap-1 rounded-xl border border-white/15 bg-white/5 px-3 py-1.5 text-xs font-bold text-slate-200 hover:bg-white/10">
                                                    <span class="material-symbols-outlined text-sm">gavel</span>
                                                    <span x-text="openTnc ? 'Tutup S&K' : 'Lihat S&K (T&C)'">Lihat S&K (T&C)</span>
                                                </button>
                                            @endif
                                            <a href="https://wa.me/{{ $waNumber }}?text={{ urlencode('Halo Clean Lab! Saya mau klaim promo: ' . ($banner['title'] ?? 'Promo Clean Lab')) }}"
                                               target="_blank" rel="noopener noreferrer"
                                               class="inline-flex items-center gap-1 rounded-xl bg-amber-400 px-3.5 py-1.5 text-xs font-extrabold text-slate-950 hover:bg-amber-300">
                                                <span>Klaim Promo</span>
                                                <span class="material-symbols-outlined text-sm">arrow_outward</span>
                                            </a>
                                        </div>
                                    </div>

                                    {{-- Collapsible T&C Box --}}
                                    @if(!empty($banner['tnc']))
                                        <div x-show="openTnc" x-cloak x-transition class="mt-3 rounded-2xl border border-white/10 bg-black/40 p-3.5 text-xs text-slate-300 space-y-1">
                                            <p class="font-bold text-amber-300 uppercase tracking-wider text-[10px]">Syarat & Ketentuan (T&C):</p>
                                            <p class="whitespace-pre-line leading-relaxed">{{ $banner['tnc'] }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Live Promos from Laundry Promo Table (if any) --}}
            @if(($config['show_live_promos'] ?? true) && $promos->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($promos as $promo)
                        <div class="rounded-2xl border border-emerald-400/30 bg-[#0C1526] p-5 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between gap-2 mb-3">
                                    <div class="flex items-center gap-2">
                                        <img src="{{ $logoUrl }}" alt="Logo" class="h-8 w-8 rounded-lg bg-white p-1 object-contain">
                                        <span class="rounded-full bg-emerald-500/20 px-2.5 py-0.5 text-[11px] font-bold text-emerald-300">VOUCHER AKTIF</span>
                                    </div>
                                    @if($promo->type === 'percentage')
                                        <span class="rounded-lg bg-emerald-400 px-2.5 py-1 text-xs font-extrabold text-slate-950">
                                            DISKON {{ intval($promo->discount_percent) }}%
                                        </span>
                                    @else
                                        <span class="rounded-lg bg-cyan-400 px-2.5 py-1 text-xs font-extrabold text-slate-950">
                                            CUCI {{ $promo->buy_quantity }} GRATIS {{ $promo->free_quantity }}
                                        </span>
                                    @endif
                                </div>
                                <h3 class="text-base font-extrabold text-white">{{ $promo->name }}</h3>
                                <p class="mt-1 text-xs text-slate-300">
                                    @if($promo->type === 'percentage')
                                        Potongan harga {{ intval($promo->discount_percent) }}% untuk layanan pencucian di {{ $brandName }}.
                                    @else
                                        Kumpulkan pencucian {{ $promo->buy_quantity }} item dan dapatkan gratis {{ $promo->free_quantity }} item!
                                    @endif
                                </p>
                            </div>
                            <div class="mt-4 pt-3 border-t border-white/10 flex items-center justify-between text-[11px] text-slate-400">
                                <span>
                                    @if($promo->valid_until)
                                        Berlaku s/d {{ $promo->valid_until->translatedFormat('d M Y') }}
                                    @else
                                        Promo Berlaku Sekarang
                                    @endif
                                </span>
                                <a href="https://wa.me/{{ $waNumber }}?text={{ urlencode('Halo Clean Lab, saya ingin pakai promo ' . $promo->name) }}"
                                   target="_blank" rel="noopener noreferrer"
                                   class="font-bold text-emerald-400 hover:underline">Pakai →</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
    @endif

    {{-- ================= OFFICIAL PRICELIST SECTION (BROCHURE IMAGE + INTERACTIVE CARDS) ================= --}}
    <section id="pricelist" class="py-12 sm:py-20 border-t border-white/10 bg-[#08101E]">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-10">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-cyan-500/10 border border-cyan-400/20 px-3.5 py-1 text-xs font-bold uppercase tracking-widest text-cyan-300">
                    Official Price List
                </span>
                <h2 class="mt-2.5 text-2xl sm:text-4xl font-extrabold text-white">Daftar Harga Resmi {{ $brandName }}</h2>
                <p class="mt-2 text-xs sm:text-sm text-slate-400">
                    Harga transparan untuk Shoes, Sandals, Helmets, serta Bags &amp; Caps. Klik gambar pricelist untuk memperbesar.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                {{-- Left: Official Pricelist Flyer Image (Tap to Zoom) --}}
                <div class="lg:col-span-5">
                    <div class="rounded-3xl border border-white/15 bg-[#0C1526] p-4 sm:p-5 shadow-2xl">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold text-slate-300 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-emerald-400 text-base">image</span>
                                Brosur Pricelist Clean Lab
                            </span>
                            <button type="button" @click="zoomPricelist = true" class="text-xs font-bold text-cyan-300 hover:underline flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">zoom_in</span> Perbesar
                            </button>
                        </div>
                        <div class="overflow-hidden rounded-2xl bg-white cursor-pointer group" @click="zoomPricelist = true">
                            <img src="{{ $pricelistImgUrl }}" alt="Pricelist Clean Lab" class="w-full h-auto object-contain group-hover:scale-[1.02] transition-transform duration-300">
                        </div>
                        <p class="text-[11px] text-center text-slate-400 mt-3">Ketuk gambar di atas untuk melihat ukuran penuh</p>
                    </div>
                </div>

                {{-- Right: Mobile-Friendly Interactive Pricelist Menu Cards --}}
                <div class="lg:col-span-7 grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                    @foreach($pricelistSections as $sec)
                        <div class="rounded-3xl border border-white/10 bg-[#0C1526] p-5 sm:p-6 flex flex-col justify-between hover:border-emerald-400/40 transition-all">
                            <div>
                                <div class="flex items-center gap-3 pb-3.5 mb-4 border-b border-white/10">
                                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-500/15 text-emerald-400 border border-emerald-400/30">
                                        <span class="material-symbols-outlined text-2xl">{{ $sec['icon'] ?? 'steps' }}</span>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-extrabold text-white">{{ $sec['category'] ?? '' }}</h3>
                                        <p class="text-[11px] text-slate-400">{{ $brandName }} Premium Care</p>
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    @foreach($sec['items'] ?? [] as $item)
                                        <div class="flex items-baseline justify-between gap-2">
                                            <div class="min-w-0">
                                                <p class="text-sm font-bold text-slate-100">{{ $item['name'] ?? '' }}</p>
                                                @if(!empty($item['note']))
                                                    <p class="text-[11px] text-slate-400">{{ $item['note'] }}</p>
                                                @endif
                                            </div>
                                            <div class="flex-1 border-b border-dotted border-white/15 mx-1"></div>
                                            <span class="shrink-0 rounded-lg bg-emerald-500/15 border border-emerald-400/30 px-2.5 py-0.5 text-xs sm:text-sm font-extrabold text-emerald-300">
                                                Rp{{ number_format((float)($item['price'] ?? 0), 0, ',', '.') }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mt-5 pt-3.5 border-t border-white/10">
                                <a href="https://wa.me/{{ $waNumber }}?text={{ urlencode('Halo Clean Lab! Saya mau order cuci kategori ' . ($sec['category'] ?? '')) }}"
                                   target="_blank" rel="noopener noreferrer"
                                   class="flex w-full items-center justify-center gap-1.5 rounded-xl bg-white/10 py-2.5 text-xs font-extrabold text-white hover:bg-emerald-400 hover:text-slate-950 transition-colors">
                                    <span class="material-symbols-outlined text-base">chat</span>
                                    <span>Pesan {{ $sec['category'] ?? 'Sekarang' }}</span>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Fullscreen Lightbox Modal for Pricelist Image --}}
        <div x-show="zoomPricelist" x-cloak x-transition
             @click.self="zoomPricelist = false"
             class="fixed inset-0 z-[100] flex items-center justify-center bg-black/85 p-4 backdrop-blur-md">
            <div class="relative max-h-[90vh] max-w-lg w-full overflow-auto rounded-2xl bg-white p-2">
                <button type="button" @click="zoomPricelist = false"
                        class="sticky top-2 float-right z-10 flex h-9 w-9 items-center justify-center rounded-full bg-slate-900 text-white shadow-lg">
                    <span class="material-symbols-outlined text-lg">close</span>
                </button>
                <img src="{{ $pricelistImgUrl }}" alt="Pricelist Clean Lab Full" class="w-full h-auto rounded-xl">
            </div>
        </div>
    </section>

    {{-- ================= BEFORE / AFTER SHOWCASE (PER TREATMENT) ================= --}}
    @php $showcases = $showcases->filter(fn($s) => $s->photos->isNotEmpty() || $s->description || $s->video_url); @endphp
    @if($showcases->isNotEmpty())
    <section id="hasil" class="py-12 sm:py-20 border-t border-white/10 bg-gradient-to-b from-[#08101E] to-[#060B14]"
             x-data="{ cat: 'all' }">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-8">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/10 border border-emerald-400/20 px-3.5 py-1 text-xs font-bold uppercase tracking-widest text-emerald-300">
                    <span class="material-symbols-outlined text-sm">compare</span> Before &amp; After
                </span>
                <h2 class="mt-2.5 text-2xl sm:text-4xl font-extrabold text-white">Hasil Nyata {{ $brandName }}</h2>
                <p class="mt-2 text-xs sm:text-sm text-slate-400">Geser slider pada foto untuk melihat perbedaan sebelum &amp; sesudah treatment.</p>
            </div>

            {{-- Category filter pills --}}
            @if($showcases->count() > 1)
            <div class="flex gap-2 overflow-x-auto no-scrollbar justify-start sm:justify-center pb-3 mb-6">
                <button type="button" @click="cat = 'all'" :class="cat === 'all' ? 'bg-emerald-400 text-slate-950 border-emerald-300' : 'bg-white/5 text-slate-200 border-white/10'"
                        class="shrink-0 rounded-full border px-4 py-2 text-xs sm:text-sm font-bold transition-colors">Semua</button>
                @foreach($showcases as $sc)
                    <button type="button" @click="cat = '{{ $sc->id }}'" :class="cat === '{{ $sc->id }}' ? 'bg-emerald-400 text-slate-950 border-emerald-300' : 'bg-white/5 text-slate-200 border-white/10'"
                            class="shrink-0 inline-flex items-center gap-1.5 rounded-full border px-4 py-2 text-xs sm:text-sm font-bold transition-colors">
                        <span class="material-symbols-outlined text-base">{{ $sc->icon }}</span> {{ $sc->title }}
                    </button>
                @endforeach
            </div>
            @endif

            <div class="space-y-8">
                @foreach($showcases as $sc)
                    <article x-show="cat === 'all' || cat === '{{ $sc->id }}'" x-transition.opacity
                             class="rounded-3xl border border-white/10 bg-[#0C1526] p-4 sm:p-7">
                        <div class="grid grid-cols-1 {{ $sc->video_url ? 'lg:grid-cols-12' : '' }} gap-5 mb-6">
                            <div class="{{ $sc->video_url ? 'lg:col-span-7' : '' }}">
                                <div class="flex items-center gap-3">
                                    <div class="h-12 w-12 shrink-0 rounded-2xl bg-gradient-to-br from-emerald-400/25 to-cyan-400/25 border border-emerald-400/30 text-emerald-300 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-2xl">{{ $sc->icon }}</span>
                                    </div>
                                    <div>
                                        <h3 class="text-xl sm:text-2xl font-extrabold text-white">{{ $sc->title }}</h3>
                                        @if($sc->photos->isNotEmpty())
                                            <p class="text-[11px] text-slate-400">{{ $sc->photos->count() }} hasil pengerjaan</p>
                                        @endif
                                    </div>
                                </div>
                                @if($sc->description)
                                    <p class="mt-3 text-sm text-slate-300 leading-relaxed whitespace-pre-line">{{ $sc->description }}</p>
                                @endif
                                <a href="https://wa.me/{{ $waNumber }}?text={{ urlencode('Halo ' . $brandName . '! Saya tertarik treatment ' . $sc->title) }}" target="_blank" rel="noopener noreferrer"
                                   class="mt-4 inline-flex items-center gap-1.5 rounded-xl bg-emerald-400 px-4 py-2.5 text-xs sm:text-sm font-extrabold text-slate-950 hover:bg-emerald-300">
                                    <span class="material-symbols-outlined text-base">chat</span> Tanya / Order {{ $sc->title }}
                                </a>
                            </div>

                            @if($sc->video_url)
                                <div class="lg:col-span-5">
                                    @if($sc->video_embed_url)
                                        <div class="relative aspect-video overflow-hidden rounded-2xl border border-white/10 bg-black">
                                            <iframe src="{{ $sc->video_embed_url }}" loading="lazy" class="absolute inset-0 h-full w-full" title="{{ $sc->title }}"
                                                    allow="accelerometer; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                        </div>
                                    @else
                                        <a href="{{ $sc->video_url }}" target="_blank" rel="noopener noreferrer"
                                           class="flex aspect-video items-center justify-center gap-2 rounded-2xl border border-white/10 bg-gradient-to-br from-[#14213A] to-[#0A1220] text-sm font-extrabold text-white hover:border-emerald-400/50">
                                            <span class="material-symbols-outlined text-4xl text-emerald-400">play_circle</span> Tonton Videonya
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>

                        @if($sc->photos->isNotEmpty())
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($sc->photos as $ph)
                                {{-- Before/After comparison slider --}}
                                <figure class="m-0" x-data="{ pos: 50 }">
                                    <div class="relative aspect-[4/3] select-none overflow-hidden rounded-2xl border border-white/10 bg-black">
                                        <img src="{{ storage_url($ph->after_path) }}" loading="lazy" alt="After {{ $ph->label }}" class="absolute inset-0 h-full w-full object-cover">
                                        <img src="{{ storage_url($ph->before_path) }}" loading="lazy" alt="Before {{ $ph->label }}" class="absolute inset-0 h-full w-full object-cover"
                                             :style="`clip-path: inset(0 ${100 - pos}% 0 0)`">
                                        <span class="absolute top-2 left-2 rounded bg-black/65 px-2 py-0.5 text-[10px] font-extrabold tracking-wider text-white">BEFORE</span>
                                        <span class="absolute top-2 right-2 rounded bg-emerald-500 px-2 py-0.5 text-[10px] font-extrabold tracking-wider text-white">AFTER</span>
                                        <div class="pointer-events-none absolute inset-y-0 w-0.5 bg-white shadow" :style="`left: ${pos}%`">
                                            <div class="absolute top-1/2 -translate-x-1/2 -translate-y-1/2 flex h-9 w-9 items-center justify-center rounded-full bg-white text-slate-900 shadow-lg">
                                                <span class="material-symbols-outlined text-lg">swap_horiz</span>
                                            </div>
                                        </div>
                                        <input type="range" min="0" max="100" x-model.number="pos" aria-label="Geser before/after"
                                               class="absolute inset-0 h-full w-full cursor-ew-resize opacity-0">
                                    </div>
                                    @if($ph->label)
                                        <figcaption class="mt-2 text-center text-xs font-semibold text-slate-300">{{ $ph->label }}</figcaption>
                                    @endif
                                </figure>
                            @endforeach
                        </div>
                        @endif
                    </article>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ================= INLINE LIVE ORDER TRACKING SECTION ================= --}}
    @if($config['show_order_tracker'] ?? true)
    <section id="cek-nota" class="py-12 border-t border-white/10 bg-[#091120]">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl border border-emerald-400/25 bg-gradient-to-br from-[#0E1A2F] to-[#091220] p-5 sm:p-8 shadow-2xl">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5">
                    <div>
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-widest text-emerald-400">
                            <span class="material-symbols-outlined text-sm">radar</span>
                            Real-Time Laundry Tracker
                        </span>
                        <h2 class="text-xl sm:text-3xl font-extrabold text-white mt-1">Cek Status Cucian Anda</h2>
                        <p class="text-xs sm:text-sm text-slate-300 mt-1">Masukkan Nomor Nota untuk melihat progress pengerjaan langsung di halaman ini.</p>
                    </div>

                    <form wire:submit="checkOrder" class="flex w-full md:w-auto flex-col sm:flex-row gap-2.5">
                        <div class="relative flex-1 sm:w-64">
                            <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg">receipt_long</span>
                            <input wire:model="trackingInput" type="text" placeholder="Ketik Nomor Nota..."
                                   class="w-full rounded-2xl border border-white/15 bg-[#060B14] pl-10 pr-4 py-3 text-sm font-semibold text-white placeholder-slate-500 focus:border-emerald-400 focus:outline-none">
                        </div>
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-400 px-6 py-3 text-sm font-extrabold text-slate-950 hover:bg-emerald-300 transition-colors shrink-0">
                            <span class="material-symbols-outlined text-lg">search</span>
                            <span wire:loading.remove wire:target="checkOrder">Lacak Status</span>
                            <span wire:loading wire:target="checkOrder">Mencari...</span>
                        </button>
                    </form>
                </div>

                @if($hasSearched)
                    <div class="mt-6 pt-6 border-t border-white/10">
                        @if($trackedOrder)
                            @php
                                $statuses = ['pending', 'processing', 'ready', 'completed'];
                                $currentIndex = array_search($trackedOrder->status, $statuses);
                                if ($currentIndex === false && $trackedOrder->status === 'in_progress') {
                                    $currentIndex = 1;
                                }
                                if ($currentIndex === false) {
                                    $currentIndex = 0;
                                }
                                $stepLabels = ['Diterima', 'Dicuci', 'Siap Ambil', 'Selesai'];
                                $stepIcons  = ['schedule', 'local_laundry_service', 'inventory_2', 'check_circle'];
                            @endphp

                            <div class="rounded-2xl border border-white/10 bg-[#060B14]/90 p-4 sm:p-6 space-y-5">
                                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-white/10 pb-4">
                                    <div>
                                        <p class="text-[11px] uppercase tracking-wider text-slate-400">Nomor Nota</p>
                                        <div class="flex items-center gap-2.5 mt-0.5">
                                            <span class="text-lg sm:text-xl font-extrabold text-white font-mono">{{ $trackedOrder->order_code }}</span>
                                            <span class="rounded-full px-2.5 py-0.5 text-[11px] font-bold {{ $trackedOrder->payment_status === 'paid' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-400/30' : 'bg-amber-500/20 text-amber-300 border border-amber-400/30' }}">
                                                {{ $trackedOrder->payment_status === 'paid' ? 'LUNAS' : 'BELUM LUNAS' }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-400 mt-1">Pelanggan: <strong class="text-slate-200">{{ $trackedOrder->customer_name }}</strong></p>
                                    </div>
                                    <button type="button" wire:click="clearTrackedOrder" class="text-xs text-slate-400 hover:text-white flex items-center gap-1">
                                        <span class="material-symbols-outlined text-sm">close</span> Tutup
                                    </button>
                                </div>

                                @if($trackedOrder->status === 'cancelled')
                                    <div class="rounded-xl bg-rose-500/15 border border-rose-500/30 p-3.5 text-rose-300 text-xs sm:text-sm font-bold flex items-center gap-2">
                                        <span class="material-symbols-outlined">cancel</span>
                                        Pesanan ini telah dibatalkan.
                                    </div>
                                @else
                                    <div class="grid grid-cols-4 gap-1.5 sm:gap-2">
                                        @foreach($stepLabels as $idx => $lbl)
                                            @php
                                                $done = $currentIndex >= $idx;
                                                $curr = $currentIndex === $idx;
                                            @endphp
                                            <div class="flex flex-col items-center text-center">
                                                <div class="flex h-9 w-9 sm:h-10 sm:w-10 items-center justify-center rounded-full border transition-all {{ $curr ? 'bg-emerald-400 text-slate-950 border-emerald-300 ring-4 ring-emerald-400/20' : ($done ? 'bg-emerald-500/30 text-emerald-300 border-emerald-400/40' : 'bg-white/5 text-slate-500 border-white/10') }}">
                                                    <span class="material-symbols-outlined text-base sm:text-lg">{{ $stepIcons[$idx] }}</span>
                                                </div>
                                                <span class="mt-1.5 text-[10px] sm:text-xs font-bold {{ $curr ? 'text-emerald-300' : ($done ? 'text-slate-200' : 'text-slate-500') }}">{{ $lbl }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="space-y-2">
                                    <div class="divide-y divide-white/10 rounded-xl border border-white/10 bg-white/[0.02]">
                                        @foreach($trackedOrder->items as $item)
                                            <div class="flex items-center justify-between px-3.5 py-2.5 text-xs sm:text-sm">
                                                <div>
                                                    <p class="font-bold text-white">{{ $item->service_name_snapshot }} <span class="text-xs text-slate-400">×{{ $item->qty ?? 1 }}</span></p>
                                                    @if($item->treatment)
                                                        <p class="text-[11px] text-slate-400">{{ $item->treatment }}</p>
                                                    @endif
                                                </div>
                                                <div class="text-right">
                                                    <p class="font-bold text-emerald-300">Rp{{ number_format((float)$item->final_price, 0, ',', '.') }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="flex items-center justify-between pt-2 text-sm sm:text-base font-extrabold text-white">
                                        <span>Total Tagihan</span>
                                        <span class="text-emerald-400 text-base sm:text-lg">Rp{{ number_format((float)$trackedOrder->total_amount, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="rounded-2xl border border-amber-400/30 bg-amber-500/10 p-4 text-xs sm:text-sm text-amber-200 flex items-center gap-3">
                                <span class="material-symbols-outlined text-amber-400">info</span>
                                <span>Nomor nota <strong>"{{ $trackingInput }}"</strong> tidak ditemukan.</span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </section>
    @endif

    {{-- ================= KATEGORI BARANG YANG DILAYANI ================= --}}
    <section id="kategori" class="py-12 sm:py-20 border-t border-white/10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-10">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-500/10 border border-emerald-400/20 px-3.5 py-1 text-xs font-bold uppercase tracking-widest text-emerald-300">
                    Shoes • Helmets • Bags • Hats • Sandals
                </span>
                <h2 class="mt-2.5 text-2xl sm:text-4xl font-extrabold text-white tracking-tight">
                    Layanan Perawatan Lengkap di {{ $brandName }}
                </h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                @foreach($categories as $cat)
                    <div class="group relative rounded-3xl border border-white/10 bg-gradient-to-b from-white/[0.05] to-white/[0.02] p-5 sm:p-6 hover:border-emerald-400/50 transition-all flex flex-col justify-between">
                        <div>
                            <div class="flex items-start justify-between gap-3 mb-4">
                                <div class="flex h-12 w-12 sm:h-14 sm:w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-400/20 to-cyan-400/20 border border-emerald-400/30 text-emerald-300">
                                    <span class="material-symbols-outlined text-2xl sm:text-3xl">{{ $cat['icon'] ?? 'steps' }}</span>
                                </div>
                                @if(!empty($cat['badge']))
                                    <span class="rounded-full bg-emerald-400/15 border border-emerald-400/30 px-2.5 py-0.5 text-[11px] font-bold text-emerald-300">
                                        {{ $cat['badge'] }}
                                    </span>
                                @endif
                            </div>

                            <h3 class="text-lg sm:text-xl font-extrabold text-white">
                                {{ $cat['name'] ?? '' }}
                            </h3>
                            @if(!empty($cat['items_label']))
                                <p class="mt-1 text-xs font-semibold text-cyan-300/90">
                                    {{ $cat['items_label'] }}
                                </p>
                            @endif
                            <p class="mt-2.5 text-xs sm:text-sm text-slate-300 leading-relaxed">
                                {{ $cat['description'] ?? '' }}
                            </p>
                        </div>

                        <div class="mt-5 pt-3.5 border-t border-white/10 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] uppercase tracking-wider text-slate-400 block">Mulai Dari</span>
                                <span class="text-base sm:text-lg font-extrabold text-white">
                                    Rp{{ number_format((float)($cat['price_from'] ?? 0), 0, ',', '.') }}
                                </span>
                            </div>
                            <a href="https://wa.me/{{ $waNumber }}?text={{ urlencode('Halo ' . $brandName . '! Saya mau tanya layanan ' . ($cat['name'] ?? '')) }}"
                               target="_blank" rel="noopener noreferrer"
                               class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-400 px-3.5 py-2 text-xs font-extrabold text-slate-950 hover:bg-emerald-300 transition-colors">
                                <span>Order WA</span>
                                <span class="material-symbols-outlined text-sm">arrow_outward</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================= KEUNGGULAN & GALERI ================= --}}
    <section id="keunggulan" class="py-12 sm:py-16 bg-[#091120]/60 border-t border-white/10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-10">
                <span class="text-xs font-bold uppercase tracking-widest text-emerald-400">Why Choose {{ $brandName }}</span>
                <h2 class="mt-1.5 text-2xl sm:text-3xl font-extrabold text-white">Serahkan Pembersihan Anda Kepada Ahlinya!</h2>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-6">
                @foreach($features as $feat)
                    <div class="rounded-2xl border border-white/10 bg-[#0C1526] p-4 sm:p-6">
                        <div class="mb-3 inline-flex h-10 w-10 sm:h-12 sm:w-12 items-center justify-center rounded-xl bg-emerald-500/15 text-emerald-400">
                            <span class="material-symbols-outlined text-xl sm:text-2xl">{{ $feat['icon'] ?? 'verified' }}</span>
                        </div>
                        <h3 class="font-bold text-white text-sm sm:text-base">{{ $feat['title'] ?? '' }}</h3>
                        <p class="mt-1.5 text-[11px] sm:text-xs text-slate-400 leading-relaxed">{{ $feat['description'] ?? '' }}</p>
                    </div>
                @endforeach
            </div>

            @if(!empty($gallery))
                <div class="mt-12">
                    <h3 class="text-center text-xl sm:text-2xl font-extrabold text-white mb-6">Galeri Hasil Pengerjaan</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
                        @foreach($gallery as $img)
                            <div class="group relative aspect-square overflow-hidden rounded-2xl border border-white/10 bg-white/5">
                                <img src="{{ storage_url($img['path'] ?? $img) }}" alt="{{ $img['caption'] ?? $brandName }}" class="h-full w-full object-cover">
                                @if(!empty($img['caption']))
                                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent p-2.5">
                                        <p class="text-[11px] font-semibold text-white">{{ $img['caption'] }}</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

    {{-- ================= LOCATION & GOOGLE MAPS SECTION ================= --}}
    <section id="lokasi" class="py-12 sm:py-20 border-t border-white/10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 items-stretch">
                {{-- Contact & Location Details --}}
                <div class="lg:col-span-5 rounded-3xl border border-white/10 bg-[#0C1526] p-5 sm:p-8 flex flex-col justify-between">
                    <div class="space-y-5">
                        <div>
                            <span class="inline-flex items-center gap-1 text-xs font-bold uppercase tracking-widest text-emerald-400">
                                <span class="material-symbols-outlined text-sm">location_on</span>
                                Lokasi Workshop
                            </span>
                            <h2 class="mt-1.5 text-2xl sm:text-3xl font-extrabold text-white">{{ $brandName }}</h2>
                            <p class="mt-2 text-xs sm:text-sm text-slate-200 leading-relaxed font-medium">{{ $addressText }}</p>
                        </div>

                        <div class="space-y-3.5 pt-3 border-t border-white/10">
                            <div class="flex items-start gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white/5 text-emerald-400">
                                    <span class="material-symbols-outlined text-lg">schedule</span>
                                </div>
                                <div>
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Jam Operasional</p>
                                    <p class="text-xs sm:text-sm font-semibold text-white mt-0.5">{{ $opHours }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white/5 text-emerald-400">
                                    <span class="material-symbols-outlined text-lg">call</span>
                                </div>
                                <div>
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">WhatsApp (Konsultasi & Antar Jemput)</p>
                                    <p class="text-xs sm:text-sm font-semibold text-white mt-0.5">0851-3976-3366 (+{{ $waNumber }})</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white/5 text-emerald-400">
                                    <span class="material-symbols-outlined text-lg">share</span>
                                </div>
                                <div>
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Instagram & TikTok</p>
                                    <div class="flex flex-wrap items-center gap-3 mt-0.5 text-xs sm:text-sm font-semibold text-emerald-300">
                                        @if($igHandle)
                                            <a href="https://instagram.com/{{ $igClean }}" target="_blank" rel="noopener noreferrer" class="hover:underline">IG: {{ $igHandle }}</a>
                                        @endif
                                        @if($tiktokHandle)
                                            <a href="https://tiktok.com/@{{ $tiktokClean }}" target="_blank" rel="noopener noreferrer" class="hover:underline">TikTok: {{ $tiktokHandle }}</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 pt-5 border-t border-white/10 grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <a href="{{ $mapsLink }}" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-emerald-400 to-cyan-400 px-4 py-3.5 text-xs sm:text-sm font-extrabold text-slate-950 shadow-lg hover:brightness-110 transition-all">
                            <span class="material-symbols-outlined text-lg">map</span>
                            Buka Google Maps
                        </a>
                        <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center justify-center gap-2 rounded-2xl border border-white/15 bg-white/5 px-4 py-3.5 text-xs sm:text-sm font-bold text-white hover:bg-white/10 transition-all">
                            <span class="material-symbols-outlined text-lg text-emerald-400">chat</span>
                            Chat WhatsApp
                        </a>
                    </div>
                </div>

                {{-- Interactive Map Card --}}
                <div class="lg:col-span-7 rounded-3xl border border-white/10 bg-[#0C1526] overflow-hidden min-h-[300px] sm:min-h-[380px] flex flex-col">
                    <div class="flex items-center justify-between px-4 py-3 border-b border-white/10 bg-white/[0.02]">
                        <div class="flex items-center gap-2 text-xs font-semibold text-slate-300 truncate">
                            <span class="material-symbols-outlined text-emerald-400 text-base">explore</span>
                            <span class="truncate">{{ $addressText }}</span>
                        </div>
                        <a href="{{ $mapsLink }}" target="_blank" rel="noopener noreferrer" class="shrink-0 ml-2 text-xs font-bold text-emerald-400 hover:underline flex items-center gap-1">
                            <span>Rute</span>
                            <span class="material-symbols-outlined text-xs">open_in_new</span>
                        </a>
                    </div>
                    <div class="relative flex-1 min-h-[260px] sm:min-h-[340px]">
                        <iframe
                            src="{{ $mapsEmbed }}"
                            class="absolute inset-0 h-full w-full border-0"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            title="Lokasi {{ $brandName }}">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= STANDALONE FOOTER (NO KSIR LINKS) ================= --}}
    <footer class="border-t border-white/10 bg-[#040810] py-8 sm:py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
            <div class="flex items-center gap-3">
                <div class="h-10 w-14 rounded-xl bg-white p-1 flex items-center justify-center overflow-hidden">
                    <img src="{{ $logoUrl }}" alt="{{ $brandName }}" class="h-full w-full object-contain">
                </div>
                <div class="text-left">
                    <p class="text-sm font-extrabold text-white">{{ $brandName }}</p>
                    <p class="text-[11px] text-slate-400">{{ $tagline }}</p>
                </div>
            </div>
            <p class="text-xs text-slate-500">
                &copy; {{ date('Y') }} {{ $brandName }} — Menara Cawang, Jakarta Timur. All rights reserved.
            </p>
        </div>
    </footer>

    {{-- ================= STICKY BOTTOM ACTION BAR FOR MOBILE ================= --}}
    <div class="sm:hidden fixed bottom-0 inset-x-0 z-50 border-t border-white/15 bg-[#060B14]/95 backdrop-blur-xl px-3 py-2.5">
        <div class="grid grid-cols-3 gap-2">
            <a href="#pricelist" class="flex flex-col items-center justify-center rounded-xl bg-white/5 py-2 text-[11px] font-bold text-slate-200">
                <span class="material-symbols-outlined text-base text-cyan-400">receipt_long</span>
                <span>Pricelist</span>
            </a>
            <a href="{{ $mapsLink }}" target="_blank" rel="noopener noreferrer" class="flex flex-col items-center justify-center rounded-xl bg-white/5 py-2 text-[11px] font-bold text-slate-200">
                <span class="material-symbols-outlined text-base text-amber-400">location_on</span>
                <span>Maps</span>
            </a>
            <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer" class="flex flex-col items-center justify-center rounded-xl bg-emerald-400 py-2 text-[11px] font-extrabold text-slate-950 shadow-lg">
                <span class="material-symbols-outlined text-base">chat</span>
                <span>Order WA</span>
            </a>
        </div>
    </div>

    {{-- Desktop Floating WhatsApp CTA --}}
    <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer"
       class="hidden sm:inline-flex fixed bottom-5 right-5 z-50 items-center gap-2 rounded-full bg-emerald-400 px-5 py-3.5 text-sm font-extrabold text-slate-950 shadow-2xl shadow-emerald-500/40 hover:scale-105 transition-transform">
        <span class="material-symbols-outlined text-xl">chat</span>
        <span>Chat {{ $brandName }}</span>
    </a>
</div>
