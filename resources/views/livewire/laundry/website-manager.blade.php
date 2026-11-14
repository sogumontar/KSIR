@php
    $publicUrl = $websiteSlug === 'clean-lab'
        ? route('cleanlab.landing')
        : route('laundry.public.site', ['slug' => $websiteSlug]);
@endphp

<div>
    <!-- Sub-nav pills -->
    <div class="flex gap-2 mb-8 overflow-x-auto pb-2">
        <a href="{{ route('laundry.dashboard') }}" wire:navigate class="px-4 py-2 rounded-full font-label-md bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 whitespace-nowrap">Dashboard</a>
        <a href="{{ route('laundry.services') }}" wire:navigate class="px-4 py-2 rounded-full font-label-md bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 whitespace-nowrap">Services</a>
        <a href="{{ route('laundry.promos') }}" wire:navigate class="px-4 py-2 rounded-full font-label-md bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 whitespace-nowrap">Promos</a>
        <a href="{{ route('laundry.website') }}" wire:navigate class="px-4 py-2 rounded-full font-label-md bg-secondary text-white shadow-sm whitespace-nowrap flex items-center gap-1.5">
            <span class="material-symbols-outlined text-sm">language</span>
            Website Clean Lab
        </a>
        <a href="{{ route('laundry.showcase') }}" wire:navigate class="px-4 py-2 rounded-full font-label-md bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 whitespace-nowrap flex items-center gap-1.5">
            <span class="material-symbols-outlined text-sm">compare</span>
            Before / After
        </a>
        <a href="{{ route('laundry.settings') }}" wire:navigate class="px-4 py-2 rounded-full font-label-md bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 whitespace-nowrap">Settings</a>
    </div>

    <!-- Header & Live URL Banner -->
    <div class="card-surface p-6 mb-8 bg-gradient-to-r from-[#091426] via-[#0e2240] to-[#006c49] text-white border-0 shadow-lg">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-bold text-emerald-300 mb-2">
                    <span class="material-symbols-outlined text-sm">public</span>
                    Standalone Customer Website (Mobile-Friendly, Tanpa Login & Terisolasi dari KSIR)
                </div>
                <h2 class="text-2xl font-extrabold text-white m-0">{{ $brandName ?: 'CLEAN LAB' }} — Website & Promo Manager</h2>
                <p class="text-xs text-white/80 mt-1">
                    Atur logo, pricelist resmi, promo dinamis (logo, teks, potongan, masa berlaku, S&amp;K/TNC), dan lokasi Google Maps.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ $publicUrl }}" target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center gap-2 rounded-xl bg-emerald-400 px-4 py-2.5 text-sm font-extrabold text-slate-950 shadow-md hover:bg-emerald-300 transition-colors">
                    <span class="material-symbols-outlined text-base">open_in_new</span>
                    Buka Website Clean Lab
                </a>
                <button type="button"
                        onclick="navigator.clipboard.writeText('{{ $publicUrl }}'); this.innerText = 'Link Tersalin ✓'; setTimeout(() => this.innerText = 'Salin Link Website', 2000);"
                        class="inline-flex items-center gap-2 rounded-xl bg-white/15 border border-white/20 px-4 py-2.5 text-sm font-bold text-white hover:bg-white/25 transition-colors">
                    Salin Link Website
                </button>
            </div>
        </div>
    </div>

    @if (session()->has('website_saved'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-2">
            <span class="material-symbols-outlined text-emerald-600">check_circle</span>
            <span class="text-sm font-semibold">{{ session('website_saved') }}</span>
        </div>
    @endif

    <!-- CMS Section Tabs -->
    <div class="flex flex-wrap gap-2 mb-6 border-b border-slate-200 pb-3">
        @php
            $tabs = [
                'hero'       => ['icon' => 'auto_awesome', 'label' => '1. Logo & Hero'],
                'promos'     => ['icon' => 'campaign', 'label' => '2. Promo Dinamis (Logo, Diskon & TNC)'],
                'pricelist'  => ['icon' => 'receipt_long', 'label' => '3. Pricelist & Brosur'],
                'categories' => ['icon' => 'steps', 'label' => '4. Kategori Layanan'],
                'gallery'    => ['icon' => 'photo_library', 'label' => '5. Keunggulan & Galeri'],
                'location'   => ['icon' => 'location_on', 'label' => '6. Kontak & Google Maps'],
            ];
        @endphp
        @foreach($tabs as $key => $tab)
            <button type="button" wire:click="$set('activeTab', '{{ $key }}')"
                    class="px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold flex items-center gap-2 transition-all {{ $activeTab === $key ? 'bg-primary text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                <span class="material-symbols-outlined text-base">{{ $tab['icon'] }}</span>
                <span>{{ $tab['label'] }}</span>
            </button>
        @endforeach
    </div>

    <form wire:submit="save" class="space-y-8">

        {{-- ================= TAB 1: BRAND & HERO ================= --}}
        @if($activeTab === 'hero')
        <div class="card-surface p-6 space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h3 class="font-headline-md text-slate-900 m-0">Identitas Brand, Logo & Hero Utama</h3>
                    <p class="text-xs text-slate-500 mt-1">Logo resmi Clean Lab dan headline utama yang tampil di halaman depan.</p>
                </div>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" wire:model="websiteEnabled" class="rounded border-slate-300 text-secondary focus:ring-secondary">
                    <span class="text-sm font-bold text-slate-700">Aktifkan Website Publik</span>
                </label>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="form-label">Nama Brand / Toko</label>
                    <input type="text" wire:model="brandName" class="form-input w-full" placeholder="CLEAN LAB">
                    @error('brandName') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="form-label">Tagline Singkat</label>
                    <input type="text" wire:model="tagline" class="form-input w-full" placeholder="PREMIUM SERVICE — SHOES, HELMETS, BAGS, HATS, SANDALS">
                </div>
                <div>
                    <label class="form-label">Custom URL Slug</label>
                    <div class="flex items-center">
                        <span class="bg-slate-100 border border-r-0 border-slate-300 rounded-l-lg px-3 py-2 text-xs text-slate-500">/s/</span>
                        <input type="text" wire:model="websiteSlug" class="form-input w-full rounded-l-none font-mono text-sm" placeholder="clean-lab">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Juga selalu aktif di <code class="font-mono">/cleanlab</code></p>
                    @error('websiteSlug') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="form-label">Teks Badge Atas (Hero Pill)</label>
                    <input type="text" wire:model="heroBadge" class="form-input w-full" placeholder="✨ SERAHKAN PEMBERSIHAN ANDA KEPADA AHLINYA!">
                </div>
                <div>
                    <label class="form-label">Headline Utama (Hero Title)</label>
                    <input type="text" wire:model="heroTitle" class="form-input w-full font-bold">
                    @error('heroTitle') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="form-label">Deskripsi Sub-Headline</label>
                    <textarea wire:model="heroSubtitle" rows="3" class="form-input w-full py-2"></textarea>
                </div>
            </div>

            {{-- Stats Highlight --}}
            <div>
                <label class="form-label block mb-2">3 Highlight Keunggulan Singkat di Hero</label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($stats as $sIdx => $st)
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                            <input type="text" wire:model="stats.{{ $sIdx }}.value" class="form-input w-full font-bold text-sm" placeholder="Teknologi Modern">
                            <input type="text" wire:model="stats.{{ $sIdx }}.label" class="form-input w-full text-xs" placeholder="Alat & Cairan Khusus Aman">
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Logo & Hero Banner Upload --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <label class="form-label block mb-2">Logo Website Clean Lab</label>
                    @if($existingLogoPath)
                        <div class="mb-3 flex items-center gap-4">
                            <img src="{{ storage_url($existingLogoPath) }}" alt="Logo" class="h-20 w-auto max-w-[200px] rounded-xl object-contain bg-white p-2 border border-slate-200">
                            <button type="button" wire:click="removeLogo" class="text-xs text-red-600 font-semibold hover:underline">Hapus Logo</button>
                        </div>
                    @endif
                    <input type="file" wire:model="logoUpload" accept=".jpg,.jpeg,.png,.webp" class="form-input w-full text-sm bg-white py-2">
                    <p class="text-xs text-slate-400 mt-1">Tampil di Navbar, Hero Banner, dan Favicon halaman Clean Lab.</p>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                    <label class="form-label block mb-2">Background Hero Image (Opsional)</label>
                    @if($existingHeroPath)
                        <div class="mb-3 flex items-center gap-4">
                            <img src="{{ storage_url($existingHeroPath) }}" alt="Hero" class="h-20 w-32 rounded-xl object-cover border border-slate-200">
                            <button type="button" wire:click="removeHeroImage" class="text-xs text-red-600 font-semibold hover:underline">Hapus Background</button>
                        </div>
                    @endif
                    <input type="file" wire:model="heroImageUpload" accept=".jpg,.jpeg,.png,.webp" class="form-input w-full text-sm bg-white py-2">
                </div>
            </div>
        </div>
        @endif

        {{-- ================= TAB 2: DYNAMIC PROMO BANNERS ================= --}}
        @if($activeTab === 'promos')
        <div class="card-surface p-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                <div>
                    <h3 class="font-headline-md text-slate-900 m-0">Kampanye Promo Dinamis di Website</h3>
                    <p class="text-xs text-slate-500 mt-1">Tambahkan info promo yang sedang berlangsung lengkap dengan Logo Promo, Teks, Besar Potongan/Diskon, Masa Berlaku, dan Syarat &amp; Ketentuan (T&amp;C).</p>
                </div>
                <button type="button" wire:click="addPromoBanner" class="btn-primary text-xs gap-1">
                    <span class="material-symbols-outlined text-sm">add</span> Tambah Promo Baru
                </button>
            </div>

            <div class="space-y-6">
                @forelse($promoBanners as $pIdx => $banner)
                    <div class="p-6 rounded-2xl border-2 {{ ($banner['is_active'] ?? true) ? 'border-emerald-200 bg-emerald-50/30' : 'border-slate-200 bg-slate-50' }} space-y-4">
                        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200/70 pb-3">
                            <div class="flex items-center gap-3">
                                <span class="px-2.5 py-1 rounded-lg bg-primary text-white text-xs font-bold">Promo #{{ $pIdx + 1 }}</span>
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" wire:model="promoBanners.{{ $pIdx }}.is_active" class="rounded border-slate-300 text-secondary focus:ring-secondary">
                                    <span class="text-xs font-bold text-slate-700">Tampilkan Promo Ini di Website</span>
                                </label>
                            </div>
                            <button type="button" wire:click="removePromoBanner({{ $pIdx }})" class="text-xs text-red-600 font-semibold hover:underline flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">delete</span> Hapus Promo
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="form-label text-xs">Label / Badge Promo</label>
                                <input type="text" wire:model="promoBanners.{{ $pIdx }}.badge" class="form-input w-full bg-white text-sm" placeholder="🔥 PROMO SPESIAL">
                            </div>
                            <div>
                                <label class="form-label text-xs">Teks Potongan / Highlight Diskon</label>
                                <input type="text" wire:model="promoBanners.{{ $pIdx }}.discount_text" class="form-input w-full bg-white text-sm font-bold text-emerald-700" placeholder="Contoh: DISKON 20% / POTONGAN RP 15.000 / FREE PICKUP">
                            </div>
                            <div>
                                <label class="form-label text-xs">Logo / Banner Promo (Opsional)</label>
                                @if(!empty($banner['logo_path']))
                                    <div class="flex items-center gap-3 mb-2">
                                        <img src="{{ storage_url($banner['logo_path']) }}" alt="Promo Logo" class="h-12 w-12 rounded-lg object-cover border border-slate-200 bg-white">
                                        <button type="button" wire:click="removePromoLogo({{ $pIdx }})" class="text-xs text-red-600 hover:underline">Hapus Logo</button>
                                    </div>
                                @endif
                                <input type="file" wire:model="promoLogoUploads.{{ $pIdx }}" accept=".jpg,.jpeg,.png,.webp" class="form-input w-full bg-white text-xs py-2">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label text-xs">Judul Promo</label>
                                <input type="text" wire:model="promoBanners.{{ $pIdx }}.title" class="form-input w-full bg-white text-sm font-bold" placeholder="Contoh: Promo Cuci 3 Pasang Sepatu Diskon 25%">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="form-label text-xs">Berlaku Dari</label>
                                    <input type="date" wire:model="promoBanners.{{ $pIdx }}.valid_from" class="form-input w-full bg-white text-sm">
                                </div>
                                <div>
                                    <label class="form-label text-xs">Berlaku Sampai</label>
                                    <input type="date" wire:model="promoBanners.{{ $pIdx }}.valid_until" class="form-input w-full bg-white text-sm">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label text-xs">Deskripsi Promo</label>
                                <textarea wire:model="promoBanners.{{ $pIdx }}.description" rows="3" class="form-input w-full bg-white text-xs py-2" placeholder="Penjelasan promo untuk menarik pelanggan..."></textarea>
                            </div>
                            <div>
                                <label class="form-label text-xs">Syarat &amp; Ketentuan (T&amp;C — Kosongkan jika tidak ada)</label>
                                <textarea wire:model="promoBanners.{{ $pIdx }}.tnc" rows="3" class="form-input w-full bg-white text-xs py-2" placeholder="1. Berlaku untuk Deep Cleaning&#10;2. Tidak dapat digabung promo lain"></textarea>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 border border-dashed border-slate-300 rounded-2xl">
                        Belum ada banner promo kustom. Klik "Tambah Promo Baru" untuk membuat info promo.
                    </div>
                @endforelse
            </div>

            <div class="pt-4 border-t border-slate-100">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" wire:model="showLivePromos" class="rounded border-slate-300 text-secondary focus:ring-secondary">
                    <span class="text-sm font-semibold text-slate-700">Tampilkan juga daftar promo otomatis dari menu <strong>Laundry → Promos</strong></span>
                </label>
            </div>
        </div>
        @endif

        {{-- ================= TAB 3: PRICELIST & BROSUR ================= --}}
        @if($activeTab === 'pricelist')
        <div class="space-y-6">
            <div class="card-surface p-6 space-y-4">
                <div class="border-b border-slate-100 pb-4">
                    <h3 class="font-headline-md text-slate-900 m-0">Gambar Brosur Pricelist Resmi Clean Lab</h3>
                    <p class="text-xs text-slate-500 mt-1">Gambar pricelist ini akan ditampilkan langsung di halaman website Clean Lab dan bisa di-zoom oleh customer.</p>
                </div>

                <div class="flex flex-col md:flex-row items-start gap-6">
                    @if($existingPricelistImagePath)
                        <div class="space-y-2">
                            <img src="{{ storage_url($existingPricelistImagePath) }}" alt="Pricelist Clean Lab" class="w-56 rounded-2xl border border-slate-200 shadow-sm object-contain bg-white">
                            <button type="button" wire:click="removePricelistImage" class="text-xs text-red-600 font-semibold hover:underline block">Hapus Gambar Pricelist</button>
                        </div>
                    @endif
                    <div class="flex-1 space-y-3">
                        <label class="form-label text-xs">Upload / Ganti Gambar Brosur Pricelist (JPG/PNG maks 5MB)</label>
                        <input type="file" wire:model="pricelistImageUpload" accept=".jpg,.jpeg,.png,.webp" class="form-input w-full bg-white text-sm py-2">
                        @error('pricelistImageUpload') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <div class="card-surface p-6 space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="font-headline-md text-slate-900 m-0">Tabel Pricelist Interaktif (Shoes, Sandals, Helmets, Bags &amp; Caps)</h3>
                        <p class="text-xs text-slate-500 mt-1">Daftar harga terstruktur yang mudah dibaca di layar HP dan memiliki tombol pesan langsung per grup.</p>
                    </div>
                    <button type="button" wire:click="addPricelistSection" class="btn-primary text-xs gap-1">
                        <span class="material-symbols-outlined text-sm">add</span> Tambah Grup Pricelist
                    </button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @foreach($pricelistSections as $sIdx => $sec)
                        <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50 space-y-4">
                            <div class="flex items-center justify-between gap-2">
                                <input type="text" wire:model="pricelistSections.{{ $sIdx }}.category" class="form-input bg-white font-bold text-sm flex-1" placeholder="Nama Kategori (misal: Shoes)">
                                <button type="button" wire:click="removePricelistSection({{ $sIdx }})" class="text-xs text-red-600 hover:underline px-2">Hapus Grup</button>
                            </div>

                            <div class="space-y-2">
                                @foreach($sec['items'] ?? [] as $iIdx => $item)
                                    <div class="grid grid-cols-12 gap-2 items-center bg-white p-2 rounded-xl border border-slate-200">
                                        <div class="col-span-5">
                                            <input type="text" wire:model="pricelistSections.{{ $sIdx }}.items.{{ $iIdx }}.name" class="form-input text-xs w-full" placeholder="Fast Cleaning">
                                        </div>
                                        <div class="col-span-3">
                                            <input type="number" wire:model="pricelistSections.{{ $sIdx }}.items.{{ $iIdx }}.price" class="form-input text-xs w-full font-bold" placeholder="35000">
                                        </div>
                                        <div class="col-span-3">
                                            <input type="text" wire:model="pricelistSections.{{ $sIdx }}.items.{{ $iIdx }}.note" class="form-input text-xs w-full" placeholder="Keterangan">
                                        </div>
                                        <div class="col-span-1 text-center">
                                            <button type="button" wire:click="removePricelistItem({{ $sIdx }}, {{ $iIdx }})" class="text-red-500 hover:text-red-700">
                                                <span class="material-symbols-outlined text-sm">close</span>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <button type="button" wire:click="addPricelistItem({{ $sIdx }})" class="text-xs font-bold text-secondary hover:underline flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">add_circle</span> Tambah Baris Harga
                            </button>
                        </div>
                    @endforeach
                </div>

                <div class="pt-4 border-t border-slate-100 flex flex-wrap gap-6">
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="showOrderTracker" class="rounded border-slate-300 text-secondary focus:ring-secondary">
                        <span class="text-sm font-semibold text-slate-700">Aktifkan Fitur Cek Status Nota di Website</span>
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="showLiveServices" class="rounded border-slate-300 text-secondary focus:ring-secondary">
                        <span class="text-sm font-semibold text-slate-700">Tampilkan juga layanan dari menu <strong>Laundry → Services</strong></span>
                    </label>
                </div>
            </div>
        </div>
        @endif

        {{-- ================= TAB 4: KATEGORI BARANG ================= --}}
        @if($activeTab === 'categories')
        <div class="card-surface p-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                <div>
                    <h3 class="font-headline-md text-slate-900 m-0">Kartu Highlight Kategori Barang</h3>
                    <p class="text-xs text-slate-500 mt-1">Kelola kartu kategori seperti Sepatu, Sandal, Helm, Tas &amp; Topi, Koper, dll.</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" wire:click="resetToDefaultCategories" class="btn-ghost text-xs border border-slate-200">
                        Reset Default Clean Lab
                    </button>
                    <button type="button" wire:click="addCategory" class="btn-primary text-xs gap-1">
                        <span class="material-symbols-outlined text-sm">add</span> Tambah Kategori
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($categories as $idx => $cat)
                    <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50/70 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Kategori #{{ $idx + 1 }}</span>
                            @if(count($categories) > 1)
                                <button type="button" wire:click="removeCategory({{ $idx }})" class="text-xs text-red-600 hover:underline flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">delete</span> Hapus
                                </button>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="sm:col-span-2">
                                <label class="form-label text-xs">Nama Kategori</label>
                                <input type="text" wire:model="categories.{{ $idx }}.name" class="form-input w-full bg-white text-sm font-bold">
                            </div>
                            <div>
                                <label class="form-label text-xs">Badge</label>
                                <input type="text" wire:model="categories.{{ $idx }}.badge" class="form-input w-full bg-white text-sm">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="form-label text-xs">Material Icon</label>
                                <select wire:model="categories.{{ $idx }}.icon" class="form-input w-full bg-white text-sm">
                                    <option value="steps">👟 Sepatu (steps)</option>
                                    <option value="roller_skating">🩴 Sandal (roller_skating)</option>
                                    <option value="sports_motorsports">🪖 Helm (sports_motorsports)</option>
                                    <option value="work">👜 Tas &amp; Topi (work)</option>
                                    <option value="luggage">🧳 Koper / Carrier (luggage)</option>
                                    <option value="checkroom">🧢 Apparel (checkroom)</option>
                                    <option value="auto_awesome">✨ Unyellowing / Detailing (auto_awesome)</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label text-xs">Harga Mulai Dari (Rp)</label>
                                <input type="number" wire:model="categories.{{ $idx }}.price_from" class="form-input w-full bg-white text-sm">
                            </div>
                        </div>

                        <div>
                            <label class="form-label text-xs">Ringkasan Layanan / Harga</label>
                            <input type="text" wire:model="categories.{{ $idx }}.items_label" class="form-input w-full bg-white text-xs">
                        </div>

                        <div>
                            <label class="form-label text-xs">Deskripsi Perawatan</label>
                            <textarea wire:model="categories.{{ $idx }}.description" rows="2" class="form-input w-full bg-white text-xs py-2"></textarea>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ================= TAB 5: KEUNGGULAN & GALERI ================= --}}
        @if($activeTab === 'gallery')
        <div class="space-y-6">
            <div class="card-surface p-6 space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="font-headline-md text-slate-900 m-0">Poin Keunggulan Clean Lab</h3>
                        <p class="text-xs text-slate-500 mt-1">Teknologi Modern, Hasil Premium &amp; Terjamin, Konsultasi Gratis, Antar Jemput Radius 3KM.</p>
                    </div>
                    <button type="button" wire:click="addFeature" class="btn-primary text-xs gap-1">
                        <span class="material-symbols-outlined text-sm">add</span> Tambah Keunggulan
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($features as $fIdx => $feat)
                        <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold text-slate-400">Keunggulan #{{ $fIdx + 1 }}</span>
                                @if(count($features) > 1)
                                    <button type="button" wire:click="removeFeature({{ $fIdx }})" class="text-xs text-red-600 hover:underline">Hapus</button>
                                @endif
                            </div>
                            <input type="text" wire:model="features.{{ $fIdx }}.title" class="form-input w-full bg-white text-sm font-bold">
                            <textarea wire:model="features.{{ $fIdx }}.description" rows="2" class="form-input w-full bg-white text-xs py-2"></textarea>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card-surface p-6 space-y-6">
                <div class="border-b border-slate-100 pb-4">
                    <h3 class="font-headline-md text-slate-900 m-0">Galeri Hasil Pengerjaan / Foto Workshop</h3>
                    <p class="text-xs text-slate-500 mt-1">Unggah foto Before/After cuci sepatu, tas, helm, atau suasana toko Clean Lab.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end bg-slate-50 p-4 rounded-xl border border-slate-200">
                    <div>
                        <label class="form-label text-xs">Pilih Foto (JPG/PNG maks 5MB)</label>
                        <input type="file" wire:model="galleryUpload" accept=".jpg,.jpeg,.png,.webp" class="form-input w-full bg-white text-sm py-2">
                        @error('galleryUpload') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="form-label text-xs">Caption Foto</label>
                        <input type="text" wire:model="galleryCaption" class="form-input w-full bg-white text-sm" placeholder="Contoh: Deep Clean Sneakers">
                    </div>
                    <div>
                        <button type="button" wire:click="uploadGalleryItem" class="btn-primary w-full justify-center">
                            <span wire:loading.remove wire:target="uploadGalleryItem">Upload ke Galeri</span>
                            <span wire:loading wire:target="uploadGalleryItem">Mengunggah...</span>
                        </button>
                    </div>
                </div>

                @if(!empty($gallery))
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        @foreach($gallery as $gIdx => $item)
                            <div class="rounded-xl border border-slate-200 overflow-hidden bg-white shadow-sm">
                                <img src="{{ storage_url($item['path'] ?? '') }}" alt="Gallery" class="h-36 w-full object-cover">
                                <div class="p-3 flex items-center justify-between gap-2">
                                    <span class="text-xs font-medium text-slate-700 truncate">{{ $item['caption'] ?? '' }}</span>
                                    <button type="button" wire:click="removeGalleryItem({{ $gIdx }})" class="text-red-500 hover:text-red-700">
                                        <span class="material-symbols-outlined text-base">delete</span>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        @endif

        {{-- ================= TAB 6: KONTAK & GOOGLE MAPS ================= --}}
        @if($activeTab === 'location')
        <div class="card-surface p-6 space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h3 class="font-headline-md text-slate-900 m-0">Kontak, Sosial Media &amp; Lokasi Google Maps</h3>
                <p class="text-xs text-slate-500 mt-1">Atur nomor WhatsApp, Instagram, TikTok, alamat Menara Cawang, serta link Google Maps.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="form-label">Nomor WhatsApp</label>
                    <input type="text" wire:model="whatsappNumber" class="form-input w-full" placeholder="6285139763366">
                </div>
                <div>
                    <label class="form-label">Instagram</label>
                    <input type="text" wire:model="instagramHandle" class="form-input w-full" placeholder="@clean_lab77">
                </div>
                <div>
                    <label class="form-label">TikTok</label>
                    <input type="text" wire:model="tiktokHandle" class="form-input w-full" placeholder="@clean_lab77">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="form-label">Pesan Pembuka Chat WhatsApp Otomatis</label>
                    <input type="text" wire:model="whatsappGreeting" class="form-input w-full">
                </div>
                <div>
                    <label class="form-label">Jam Operasional</label>
                    <input type="text" wire:model="operatingHours" class="form-input w-full">
                </div>
            </div>

            <div>
                <label class="form-label">Alamat Lengkap Workshop</label>
                <textarea wire:model="addressText" rows="2" class="form-input w-full py-2"></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="form-label">Link Google Maps</label>
                    <input type="url" wire:model="googleMapsLink" class="form-input w-full font-mono text-xs" placeholder="https://maps.app.goo.gl/7KEhxvXJmQnWpAbz6">
                    @error('googleMapsLink') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="form-label">Google Maps Embed Iframe URL</label>
                    <input type="text" wire:model="googleMapsEmbedUrl" class="form-input w-full font-mono text-xs">
                </div>
            </div>
        </div>
        @endif

        <div class="flex justify-end gap-3">
            <a href="{{ $publicUrl }}" target="_blank" rel="noopener noreferrer" class="btn-ghost border border-slate-200 gap-2">
                <span class="material-symbols-outlined text-base">visibility</span>
                Lihat Website
            </a>
            <button type="submit" class="btn-primary px-8 gap-2">
                <span class="material-symbols-outlined text-base">save</span>
                <span wire:loading.remove wire:target="save">Simpan Perubahan Website</span>
                <span wire:loading wire:target="save">Menyimpan...</span>
            </button>
        </div>
    </form>
</div>
