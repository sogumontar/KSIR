<div class="max-w-2xl mx-auto py-12 px-4">
    {{-- Flash message --}}
    @if(session()->has('status_message'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl flex items-center gap-2">
            <span class="material-symbols-outlined text-green-600">check_circle</span>
            <span class="text-sm font-medium">{{ session('status_message') }}</span>
        </div>
    @endif

    <div class="text-center mb-8">
        <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <span class="material-symbols-outlined text-primary text-3xl">local_laundry_service</span>
        </div>
        <h1 class="text-2xl font-bold text-slate-900">Pilih Toko Laundry</h1>
        <p class="text-slate-500 text-sm mt-1">Pilih toko yang ingin Anda kelola</p>
    </div>

    <div class="space-y-4">
        {{-- Own store --}}
        <button wire:click="selectStore({{ auth()->id() }})"
            class="w-full text-left p-5 bg-white border-2 border-primary rounded-xl shadow-sm hover:shadow-md hover:border-secondary transition-all group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center shrink-0 group-hover:bg-primary/20 transition-colors">
                    <span class="material-symbols-outlined text-primary text-xl">storefront</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-slate-900 text-base">
                        {{ $ownSetting?->store_name ?: auth()->user()->name }}
                    </p>
                    <p class="text-xs text-slate-500 mt-0.5">Toko Saya (Owner)</p>
                    @if($ownSetting?->store_address)
                        <p class="text-xs text-slate-400 truncate mt-0.5">{{ $ownSetting->store_address }}</p>
                    @endif
                </div>
                <span class="material-symbols-outlined text-primary text-xl">chevron_right</span>
            </div>
        </button>

        {{-- Contributed stores --}}
        @foreach($contributions as $contrib)
            <button wire:click="selectStore({{ $contrib->owner_user_id }})"
                class="w-full text-left p-5 bg-white border border-slate-200 rounded-xl shadow-sm hover:shadow-md hover:border-primary hover:border-2 transition-all group">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center shrink-0 group-hover:bg-indigo-100 transition-colors">
                        <span class="material-symbols-outlined text-indigo-500 text-xl">people</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-slate-900 text-base">
                            {{ $contrib->owner?->merchantSetting?->store_name ?: $contrib->owner?->name }}
                        </p>
                        <p class="text-xs text-indigo-500 font-medium mt-0.5">Kontributor — {{ $contrib->invite_name }}</p>
                        @if($contrib->owner?->merchantSetting?->store_address)
                            <p class="text-xs text-slate-400 truncate mt-0.5">{{ $contrib->owner->merchantSetting->store_address }}</p>
                        @endif
                    </div>
                    <span class="material-symbols-outlined text-slate-400 group-hover:text-primary text-xl transition-colors">chevron_right</span>
                </div>
            </button>
        @endforeach
    </div>
</div>
