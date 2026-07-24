@php
    $statuses = ['pending', 'processing', 'ready', 'completed'];
    $currentIndex = array_search($order->status, $statuses);
    $isCancelled = $order->status === 'cancelled';
    if ($currentIndex === false && !$isCancelled) $currentIndex = 0;

    $statusIcons = ['schedule', 'local_laundry_service', 'inventory_2', 'check_circle'];
    $statusLabels = ['Pesanan Diterima', 'Sedang Dikerjakan', 'Siap Diambil', 'Selesai'];
    $statusDescriptions = [
        $order->created_at->translatedFormat('d M Y, H:i'),
        'Pesanan sedang dalam proses pengerjaan',
        $order->delivery_type === 'delivery' ? 'Siap untuk diantar' : 'Siap untuk diambil di toko',
        'Pesanan telah selesai',
    ];

    $latestEstimate = $order->items->max('date_estimated_done');
    $hasPromo = $order->promo && $order->promo->type === 'percentage' && $order->promo->discount_percent > 0;

    $storeStatus = $merchantSetting->store_status ?? 'open';
    $storeStatusLabels = [
        'open' => 'Buka',
        'closed' => 'Tutup',
        'unattended' => 'Tidak Ada Penjaga'
    ];
    $storeStatusColors = [
        'open' => 'bg-emerald-500/25 text-emerald-300 border-emerald-400/30',
        'closed' => 'bg-red-500/25 text-red-300 border-red-400/30',
        'unattended' => 'bg-amber-500/25 text-amber-300 border-amber-400/30'
    ];
@endphp

<div class="w-full max-w-lg mx-auto my-4 sm:my-8 antialiased" x-data="{ showPhotos: true }">

    {{-- ===== HEADER CARD ===== --}}
    <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-200/60">

        {{-- Hero Header with gradient --}}
        <div class="relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-[#091426] via-[#0e2240] to-[#006c49]"></div>
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-[-20%] right-[-10%] w-64 h-64 rounded-full bg-white/20 blur-3xl"></div>
                <div class="absolute bottom-[-20%] left-[-10%] w-48 h-48 rounded-full bg-emerald-400/30 blur-3xl"></div>
            </div>

            <div class="relative z-10 px-6 pt-8 pb-6 text-center text-white">
                {{-- Merchant branding --}}
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-white mb-4 overflow-hidden shadow-md border border-white/20">
                    @if($order->user->profile_photo)
                        <img src="{{ Storage::disk('public')->url($order->user->profile_photo) }}" alt="Logo" class="w-full h-full object-cover">
                    @else
                        <span class="material-symbols-outlined text-4xl text-slate-800">local_laundry_service</span>
                    @endif
                </div>
                <h1 class="text-2xl font-bold tracking-tight mb-1">{{ $order->user->store_name ?? $order->user->name ?? 'Laundry' }}</h1>
                @if($order->user->business_address)
                    <p class="text-white/60 text-xs leading-relaxed max-w-xs mx-auto">{{ $order->user->business_address }}</p>
                @endif

                {{-- Store Status Badge --}}
                <div class="mt-3 inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border {{ $storeStatusColors[$storeStatus] ?? 'bg-emerald-500/25 text-emerald-300 border-emerald-400/30' }} backdrop-blur-sm">
                    <span class="w-2 h-2 rounded-full {{ $storeStatus === 'open' ? 'bg-emerald-400' : ($storeStatus === 'unattended' ? 'bg-amber-400 animate-pulse' : 'bg-red-400') }}"></span>
                    Toko: {{ $storeStatusLabels[$storeStatus] ?? 'Buka' }}
                </div>
            </div>

            {{-- Order code badge --}}
            <div class="relative z-10 flex justify-center -mb-5 px-6">
                <div class="bg-white rounded-2xl shadow-lg px-6 py-3 border border-slate-100 flex items-center gap-3">
                    <div>
                        <p class="text-[10px] uppercase tracking-widest text-slate-400 font-semibold">No. Nota</p>
                        <p class="text-lg font-extrabold text-slate-900 tracking-tight leading-tight">{{ $order->order_code }}</p>
                    </div>
                    <div class="w-px h-10 bg-slate-200"></div>
                    <div class="text-center">
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold {{ $order->payment_status === 'paid' ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200' : 'bg-red-50 text-red-700 ring-1 ring-red-200' }}">
                            <span class="material-symbols-outlined text-sm">{{ $order->payment_status === 'paid' ? 'check_circle' : 'pending' }}</span>
                            {{ $order->payment_status === 'paid' ? 'LUNAS' : 'BELUM LUNAS' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== MAIN CONTENT ===== --}}
        <div class="px-6 pt-10 pb-6 space-y-6">

            {{-- Customer info row --}}
            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                <div class="flex-shrink-0 w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center">
                    <span class="material-symbols-outlined text-lg text-slate-500">person</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-slate-800 text-sm truncate">{{ $order->customer_name }}</p>
                    <p class="text-xs text-slate-500">{{ $order->customer_phone ?? '-' }}</p>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-[10px] uppercase tracking-wider text-slate-400 font-medium">Tipe</p>
                    <span class="inline-flex items-center gap-1 text-xs font-semibold {{ $order->delivery_type === 'delivery' ? 'text-blue-600' : 'text-slate-600' }}">
                        <span class="material-symbols-outlined text-sm">{{ $order->delivery_type === 'delivery' ? 'local_shipping' : 'storefront' }}</span>
                        {{ $order->delivery_type === 'delivery' ? 'Delivery' : 'Pick Up' }}
                    </span>
                </div>
            </div>

            {{-- ===== STATUS TIMELINE ===== --}}
            <div>
                <h3 class="text-xs uppercase tracking-widest text-slate-400 font-semibold mb-4 flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">timeline</span>
                    Status Pesanan
                </h3>

                @if($isCancelled)
                    <div class="flex items-center gap-3 p-4 bg-red-50 rounded-xl border border-red-100">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-500 flex items-center justify-center shadow-md shadow-red-200">
                            <span class="material-symbols-outlined text-white text-lg">cancel</span>
                        </div>
                        <div>
                            <p class="font-bold text-red-700">Pesanan Dibatalkan</p>
                            <p class="text-xs text-red-500">Pesanan ini telah dibatalkan</p>
                        </div>
                    </div>
                @else
                    <div class="flex items-stretch justify-between gap-0">
                        @foreach($statuses as $idx => $status)
                            @php
                                $isActive = $currentIndex >= $idx;
                                $isCurrent = $currentIndex === $idx;
                            @endphp
                            <div class="flex-1 relative flex flex-col items-center text-center">
                                {{-- Connector line (left side) --}}
                                @if($idx > 0)
                                    <div class="absolute top-5 right-1/2 w-full h-0.5 {{ $isActive ? 'bg-emerald-400' : 'bg-slate-200' }} -z-0"></div>
                                @endif

                                {{-- Circle icon --}}
                                <div class="relative z-10 w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300
                                    {{ $isCurrent ? 'bg-emerald-500 shadow-lg shadow-emerald-200 ring-4 ring-emerald-100 scale-110' : ($isActive ? 'bg-emerald-400' : 'bg-slate-100') }}">
                                    <span class="material-symbols-outlined text-lg {{ $isCurrent || $isActive ? 'text-white' : 'text-slate-400' }}">{{ $statusIcons[$idx] }}</span>
                                </div>

                                {{-- Label --}}
                                <p class="mt-2 text-[10px] sm:text-xs font-semibold leading-tight px-1 {{ $isCurrent ? 'text-emerald-700' : ($isActive ? 'text-slate-700' : 'text-slate-400') }}">
                                    {{ $statusLabels[$idx] }}
                                </p>

                                {{-- Description on current step --}}
                                @if($isCurrent)
                                    <p class="mt-0.5 text-[9px] sm:text-[10px] text-emerald-600/70 leading-tight px-1">{{ $statusDescriptions[$idx] }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ===== KEY DATES ===== --}}
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-blue-50/60 rounded-xl p-3 border border-blue-100/60">
                    <div class="flex items-center gap-1.5 mb-1">
                        <span class="material-symbols-outlined text-blue-400 text-sm">calendar_today</span>
                        <span class="text-[10px] uppercase tracking-wider text-blue-400 font-semibold">Tgl Terima</span>
                    </div>
                    <p class="font-bold text-blue-800 text-sm">{{ $order->created_at->translatedFormat('d M Y') }}</p>
                </div>
                <div class="bg-amber-50/60 rounded-xl p-3 border border-amber-100/60">
                    <div class="flex items-center gap-1.5 mb-1">
                        <span class="material-symbols-outlined text-amber-500 text-sm">event_available</span>
                        <span class="text-[10px] uppercase tracking-wider text-amber-500 font-semibold">Est. Selesai</span>
                    </div>
                    <p class="font-bold text-amber-800 text-sm">{{ $latestEstimate ? \Carbon\Carbon::parse($latestEstimate)->translatedFormat('d M Y') : '-' }}</p>
                </div>
            </div>

            {{-- ===== ORDER ITEMS ===== --}}
            <div>
                <h3 class="text-xs uppercase tracking-widest text-slate-400 font-semibold mb-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">receipt_long</span>
                    Detail Pesanan ({{ $order->items->count() }} item)
                </h3>

                <div class="bg-slate-50/60 rounded-xl border border-slate-100 divide-y divide-slate-100 overflow-hidden">
                    @foreach($order->items as $idx => $item)
                        <div class="flex items-start gap-3 p-3 {{ $item->is_free ? 'bg-emerald-50/40' : '' }}">
                            <div class="flex-shrink-0 w-7 h-7 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-xs font-bold text-slate-500 mt-0.5">
                                {{ $idx + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-slate-800 text-sm leading-tight">{{ $item->service_name_snapshot }}</p>
                                @if($item->treatment)
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $item->treatment }}</p>
                                @endif
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[10px] text-slate-400 flex items-center gap-0.5">
                                        <span class="material-symbols-outlined text-[10px]">calendar_today</span>
                                        {{ \Carbon\Carbon::parse($item->date_in)->format('d/m') }} → {{ \Carbon\Carbon::parse($item->date_estimated_done)->format('d/m/Y') }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                @if($item->is_free)
                                    <span class="inline-block text-xs font-bold text-emerald-600 bg-emerald-100 px-2 py-0.5 rounded-md">GRATIS</span>
                                @elseif($item->final_price < $item->price_snapshot)
                                    <span class="text-[10px] text-slate-400 line-through block">Rp{{ number_format($item->price_snapshot, 0, ',', '.') }}</span>
                                    <p class="font-bold text-slate-800 text-sm">Rp{{ number_format($item->final_price, 0, ',', '.') }}</p>
                                @else
                                    <p class="font-bold text-slate-800 text-sm">Rp{{ number_format($item->price_snapshot, 0, ',', '.') }}</p>
                                    @if($hasPromo)
                                        <p class="text-[10px] text-emerald-600 font-medium">Disc {{ intval($order->promo->discount_percent) }}%</p>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pricing summary --}}
                <div class="mt-3 space-y-1.5 px-1">
                    <div class="flex justify-between text-sm text-slate-500">
                        <span>Subtotal</span>
                        <span>Rp{{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    @if($order->discount_amount > 0)
                        <div class="flex justify-between text-sm text-emerald-600 font-medium">
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">sell</span>
                                Diskon{{ $hasPromo ? ' (' . intval($order->promo->discount_percent) . '%)' : '' }}
                            </span>
                            <span>-Rp{{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between items-center pt-2 border-t border-dashed border-slate-200">
                        <span class="font-extrabold text-slate-900">Total</span>
                        <span class="text-xl font-extrabold text-[#091426]">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- ===== CONDITION PHOTOS ===== --}}
            @if($order->photo_before || $order->photo_after)
                <div>
                    <button
                        @click="showPhotos = !showPhotos"
                        class="w-full flex items-center justify-between p-3 bg-slate-50 rounded-xl border border-slate-100 hover:bg-slate-100 transition-colors group"
                    >
                        <span class="flex items-center gap-2 text-xs uppercase tracking-widest text-slate-500 font-semibold">
                            <span class="material-symbols-outlined text-sm text-slate-400">photo_camera</span>
                            Foto Kondisi Barang
                        </span>
                        <span class="material-symbols-outlined text-slate-400 transition-transform duration-200 text-lg" :class="showPhotos && 'rotate-180'">expand_more</span>
                    </button>

                    <div x-show="showPhotos" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="mt-3 grid {{ ($order->photo_before && $order->photo_after) ? 'grid-cols-2' : 'grid-cols-1' }} gap-3">
                        @if($order->photo_before)
                            <div class="relative group/photo">
                                <div class="absolute top-2 left-2 z-10 bg-black/50 backdrop-blur-sm text-white text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded-lg">Before</div>
                                <img
                                    src="{{ Storage::disk('public')->url($order->photo_before) }}"
                                    alt="Kondisi Sebelum"
                                    class="w-full aspect-square object-cover rounded-xl border border-slate-200 shadow-sm group-hover/photo:shadow-md transition-shadow cursor-pointer"
                                    onclick="window.open(this.src, '_blank')"
                                >
                            </div>
                        @endif
                        @if($order->photo_after)
                            <div class="relative group/photo">
                                <div class="absolute top-2 left-2 z-10 bg-emerald-600/80 backdrop-blur-sm text-white text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded-lg">After</div>
                                <img
                                    src="{{ Storage::disk('public')->url($order->photo_after) }}"
                                    alt="Kondisi Sesudah"
                                    class="w-full aspect-square object-cover rounded-xl border border-slate-200 shadow-sm group-hover/photo:shadow-md transition-shadow cursor-pointer"
                                    onclick="window.open(this.src, '_blank')"
                                >
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- ===== QR CODE PAYMENT ===== --}}
            @if($order->payment_status === 'unpaid' && $merchantSetting?->qr_code_path)
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-5 border border-blue-100 text-center">
                    <div class="flex items-center justify-center gap-2 mb-3">
                        <span class="material-symbols-outlined text-blue-500 text-xl">qr_code_2</span>
                        <h3 class="font-bold text-slate-800 text-sm">Scan untuk Pembayaran</h3>
                    </div>
                    <div class="inline-block bg-white p-3 rounded-2xl shadow-md border border-slate-100">
                        <img
                            src="{{ Storage::disk('public')->url($merchantSetting->qr_code_path) }}"
                            alt="QR Code Pembayaran"
                            class="w-44 h-44 object-contain"
                        >
                    </div>
                    @if($merchantSetting->payment_notes)
                        <p class="mt-3 text-xs text-slate-600 bg-white/60 rounded-lg px-3 py-2 border border-blue-100/60 italic">{{ $merchantSetting->payment_notes }}</p>
                    @endif
                </div>
            @endif

            {{-- ===== NOTES ===== --}}
            @if($order->notes)
                <div class="flex items-start gap-2 p-3 bg-amber-50 rounded-xl border border-amber-100">
                    <span class="material-symbols-outlined text-amber-500 text-lg mt-0.5 flex-shrink-0">sticky_note_2</span>
                    <div>
                        <p class="text-[10px] uppercase tracking-widest text-amber-500 font-semibold mb-0.5">Catatan</p>
                        <p class="text-sm text-amber-800">{{ $order->notes }}</p>
                    </div>
                </div>
            @endif

        </div>

        {{-- ===== FOOTER ===== --}}
        <div class="bg-slate-50 px-6 py-4 text-center border-t border-slate-100">
            <p class="text-[10px] text-slate-400 uppercase tracking-widest font-medium">Powered by Inventory Pro</p>
        </div>

    </div>

</div>
