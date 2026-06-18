<div class="space-y-lg">
    <!-- Banner -->
    <div class="h-80 rounded-xl overflow-hidden bg-surface-container-low shadow-sm relative">
        <img src="{{ $merchant->banner_photo ? asset('storage/' . $merchant->banner_photo) : asset('images/default-banner.png') }}" 
             alt="Banner" class="w-full h-full object-cover">
        <div class="absolute bottom-0 left-0 w-full bg-gradient-to-t from-black/60 to-transparent p-lg">
            <div class="flex items-center gap-md">
                <img src="{{ $merchant->profile_photo ? asset('storage/' . $merchant->profile_photo) : asset('images/default-avatar.png') }}" 
                     alt="{{ $merchant->name }}" 
                     class="w-24 h-24 rounded-full border-4 border-white shadow-md bg-white">
                <div class="text-white">
                    <h1 class="font-display text-display">{{ $merchant->name }}</h1>
                    <p class="font-body-md opacity-90">{{ $merchant->business_address }}</p>
                </div>
            </div>
        </div>
    </div>
    
    @if (session()->has('message'))
        <div class="p-md bg-secondary-container text-on-secondary-container rounded-lg font-label-md flex items-center gap-sm">
            <span class="material-symbols-outlined">check_circle</span>
            {{ session('message') }}
        </div>
    @endif

    <!-- Catalog -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-gutter">
        @foreach($goods as $good)
            <div class="bg-white p-md rounded-xl border border-outline-variant shadow-sm hover:shadow-md transition-all card-hover">
                <img src="{{ asset('storage/' . $good->image) }}" alt="{{ $good->name }}" class="w-full h-48 object-cover rounded-lg mb-sm">
                <h3 class="font-headline-md text-primary">{{ $good->name }}</h3>
                <p class="text-secondary font-display text-headline-sm font-bold mb-md">{{ number_format($good->price, 2) }}</p>
                <button wire:click="addToCart({{ $good->id }})" 
                        class="w-full py-md bg-primary text-white rounded-lg font-label-lg hover:bg-primary/90 transition-all active:scale-[0.98]">
                    Add to Cart
                </button>
            </div>
        @endforeach
    </div>
    
    <!-- Checkout FAB (Polished) -->
    <a href="{{ route('customer.checkout') }}" 
       class="fixed bottom-xl right-xl p-lg bg-secondary text-white rounded-full shadow-xl hover:bg-secondary/90 transition-all flex items-center gap-xs">
        <span class="material-symbols-outlined">shopping_cart</span>
        <span class="font-label-lg">Checkout</span>
    </a>
</div>
