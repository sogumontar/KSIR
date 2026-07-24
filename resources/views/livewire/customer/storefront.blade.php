<div class="space-y-lg pb-32">
    <!-- Merchant Header/Banner -->
    <div class="rounded-2xl overflow-hidden bg-white border border-outline-variant shadow-sm relative group">
        <!-- Banner Image -->
        <div class="h-48 md:h-64 w-full relative overflow-hidden bg-surface-container-low">
            <img src="{{ $merchant->banner_photo ? Storage::disk('public')->url($merchant->banner_photo) : 'https://lh3.googleusercontent.com/aida-public/AB6AXuCmeXQj0HYzz-a8WJinV5aEnUhMWwXQqcvaL5vmcSTp3eznp9cBlsQ5equxTtzL2ZdrWfzXFbB-S-9Nd5HBMpuuaY_X8exQRaa2CVGp00f8elKiLnmGsDkebSo_yP29-V1vZqhUioeoMoLgdbtqtKzYf1nW3ncMfC0pEeLxdnv8mgmurWGGbgLvxXvdTTXE4iPBqDoNWSSK874XtFvN0NprhEtk9g3ThZFMfRUqv5Cpk9CL658WNYouLVqFG3S8E2C-cAJaPcTG1WM' }}"
                 alt="Banner" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
            <div class="absolute inset-0 bg-gradient-to-t from-primary/30 to-transparent"></div>
        </div>

        <!-- Merchant Info Bar -->
        <div class="px-md md:px-lg pb-lg pt-0 bg-surface-container-lowest relative">
            <div class="flex flex-col sm:flex-row sm:items-end gap-sm md:gap-md -mt-10 md:-mt-14 relative z-10">
                <!-- Profile Pic / Logo -->
                <div class="relative w-20 h-20 md:w-28 md:h-28 rounded-2xl bg-white p-1 shadow-md border border-outline-variant flex-shrink-0 self-start">
                    <img src="{{ $merchant->profile_photo ? Storage::disk('public')->url($merchant->profile_photo) : 'https://ui-avatars.com/api/?name='.urlencode($merchant->name).'&color=7F9CF5&background=EBF4FF' }}"
                         alt="{{ $merchant->name }}"
                         class="w-full h-full rounded-xl object-cover bg-white">
                    @if($merchant->operating_status)
                        <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white rounded-full shadow-sm animate-pulse"></span>
                    @endif
                </div>

                <!-- Info & Actions -->
                <div class="flex-1 flex flex-col gap-sm pb-1 min-w-0">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-xs sm:gap-md">
                        <!-- Name + Description -->
                        <div class="min-w-0 flex-1">
                            <h2 class="text-xl md:text-2xl font-bold text-on-surface tracking-tight leading-tight truncate">{{ $merchant->store_name ?: $merchant->name }}</h2>
                            @if($merchant->store_description)
                                <p class="text-on-surface-variant text-xs md:text-sm leading-relaxed mt-0.5 line-clamp-2 max-w-2xl">{{ $merchant->store_description }}</p>
                            @endif
                        </div>

                        <!-- Status Badge -->
                        <div class="flex-shrink-0">
                            @if($merchant->operating_status)
                                <span class="inline-flex items-center gap-xs px-sm py-1.5 bg-green-500/10 text-green-700 border border-green-500/20 rounded-xl text-xs font-bold uppercase tracking-wider">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                                    Open for Orders
                                </span>
                            @else
                                <span class="inline-flex items-center gap-xs px-sm py-1.5 bg-red-500/10 text-red-700 border border-red-500/20 rounded-xl text-xs font-bold uppercase tracking-wider">
                                    <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                                    Closed
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Meta Info -->
                    <div class="flex flex-wrap items-center gap-x-sm gap-y-1 text-xs text-on-surface-variant">
                        <div class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px] text-outline">location_on</span>
                            <span>{{ $merchant->business_address ?: 'Online Store' }}</span>
                        </div>
                        <span class="text-outline-variant hidden sm:inline">•</span>
                        <div class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px] text-outline">storefront</span>
                            <span class="uppercase tracking-wider font-semibold">{{ $merchant->category ?: 'General' }}</span>
                        </div>
                        @if($merchant->public_email)
                            <span class="text-outline-variant hidden sm:inline">•</span>
                            <div class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px] text-outline">mail</span>
                                <a href="mailto:{{ $merchant->public_email }}" class="hover:text-primary transition-colors underline decoration-dotted truncate max-w-[160px]">{{ $merchant->public_email }}</a>
                            </div>
                        @endif
                        @if($merchant->support_phone)
                            <span class="text-outline-variant hidden sm:inline">•</span>
                            <div class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px] text-outline">call</span>
                                <a href="tel:{{ $merchant->support_phone }}" class="hover:text-primary transition-colors">{{ $merchant->support_phone }}</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Notifications -->
    @if (session()->has('message'))
        <div class="p-md bg-secondary-container text-on-secondary-container rounded-xl font-label-md flex items-center gap-sm animate-fade-in shadow-sm">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            {{ session('message') }}
        </div>
    @endif

    <!-- Catalog Section Header -->
    <div class="flex items-center justify-between border-b border-outline-variant pb-xs">
        <h2 class="font-headline-md text-primary font-bold">Product Catalog</h2>
        <span class="text-on-surface-variant font-label-md">{{ $goods->count() }} Items Available</span>
    </div>

    <!-- Catalog Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-sm md:gap-gutter">
        @foreach($goods as $good)
            <div class="bg-white rounded-2xl border border-outline-variant shadow-sm hover:shadow-md transition-all duration-300 group flex flex-col h-full overflow-hidden">
                <!-- Product Image -->
                <div class="aspect-square w-full relative overflow-hidden bg-surface-container-low">
                    <img src="{{ $good->imageUrl }}"
                         alt="{{ $good->name }}"
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    <div class="absolute top-sm right-sm">
                         <span class="px-sm py-1 bg-white/90 backdrop-blur-sm rounded-full text-[10px] font-bold text-primary shadow-sm border border-outline-variant">
                             In Stock
                         </span>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="p-sm md:p-md flex flex-col flex-1">
                    <h3 class="font-body-md font-medium text-primary line-clamp-1 md:line-clamp-2" title="{{ $good->name }}">{{ $good->name }}</h3>

                    <div class="mt-auto pt-sm md:pt-md flex items-center justify-between">
                        <div class="font-bold text-primary text-sm md:text-base">Rp {{ number_format($good->price, 0, ',', '.') }}</div>
                        <button wire:click="addToCart({{ $good->id }})"
                                wire:loading.attr="disabled"
                                wire:target="addToCart({{ $good->id }})"
                                class="w-9 h-9 md:w-10 md:h-10 rounded-xl bg-surface border border-outline-variant flex items-center justify-center text-on-surface hover:bg-primary hover:text-on-primary hover:border-primary transition-all active:scale-95 duration-200 group/btn disabled:opacity-50 disabled:cursor-not-allowed"
                                title="Add to Cart">
                            <span wire:loading.remove wire:target="addToCart({{ $good->id }})" class="material-symbols-outlined text-[18px] md:text-[20px] group-hover/btn:scale-110 transition-transform">add_shopping_cart</span>
                            <span wire:loading wire:target="addToCart({{ $good->id }})" class="material-symbols-outlined text-[18px] md:text-[20px] animate-spin">progress_activity</span>
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($goods->isEmpty())
        <div class="py-xl text-center">
            <span class="material-symbols-outlined text-outline text-5xl">inventory_2</span>
            <p class="text-on-surface-variant mt-md">This merchant hasn't added any products yet.</p>
        </div>
    @endif

    <!-- Checkout FAB (Polished for Mobile) -->
    <div class="fixed bottom-margin-mobile md:bottom-lg left-1/2 -translate-x-1/2 md:translate-x-0 md:left-auto md:right-lg z-50 w-full max-w-xs px-margin-mobile md:px-0">
        <a href="{{ route('customer.cart') }}"
           class="w-full flex items-center justify-between p-md bg-secondary text-white rounded-2xl shadow-2xl hover:bg-on-secondary-fixed-variant transition-all hover:scale-105 active:scale-95 group border-2 border-white/20 relative">
            <div class="flex items-center gap-sm">
                <div class="relative">
                    <span class="material-symbols-outlined bg-white/20 p-sm rounded-xl">shopping_cart</span>
                    @if($this->cartCount > 0)
                        <span class="absolute -top-2 -right-2 bg-error text-white text-[10px] font-bold w-5 h-5 flex items-center justify-center rounded-full shadow-sm animate-pulse">{{ $this->cartCount }}</span>
                    @endif
                </div>
                <span class="font-label-lg font-bold">Review Order</span>
            </div>
            <div class="flex items-center gap-xs">
                <span class="text-xs font-label-md opacity-80">Proceed</span>
                <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">chevron_right</span>
            </div>
        </a>
    </div>
</div>
