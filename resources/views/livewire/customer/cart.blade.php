<div class="max-w-4xl mx-auto space-y-md md:space-y-lg pb-32 md:pb-10">
    <div class="flex items-center justify-between">
        <h1 class="font-headline-md md:font-headline-lg font-bold text-primary flex items-center gap-sm">
            <span class="material-symbols-outlined text-3xl">shopping_cart</span>
            Your Cart
        </h1>
        <a href="{{ url()->previous() }}" wire:navigate class="text-primary font-label-md hover:underline flex items-center gap-xs">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            Continue Shopping
        </a>
    </div>

    @if (session()->has('error'))
        <div class="p-md bg-error/10 text-error rounded-xl font-label-md flex items-center gap-sm animate-fade-in">
            <span class="material-symbols-outlined">error</span>
            {{ session('error') }}
        </div>
    @endif

    @if(empty($cart))
        <div class="bg-white rounded-3xl border border-outline-variant shadow-sm p-xl text-center flex flex-col items-center animate-fade-in">
            <div class="w-24 h-24 bg-surface-container-low rounded-full flex items-center justify-center mb-md">
                <span class="material-symbols-outlined text-outline text-5xl">shopping_cart</span>
            </div>
            <h2 class="font-headline-sm font-bold text-on-surface mb-xs">Your cart is empty</h2>
            <p class="text-on-surface-variant font-body-md mb-lg max-w-md">Looks like you haven't added anything to your cart yet. Discover great products from merchants!</p>
            <a href="{{ route('customer.dashboard') }}" wire:navigate class="px-xl py-md bg-primary text-on-primary rounded-xl font-label-lg font-bold hover:bg-on-primary-fixed-variant hover:shadow-md transition-all active:scale-95">Start Shopping</a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-md lg:gap-lg items-start animate-fade-in">
            <!-- Cart Items -->
            <div class="lg:col-span-2 space-y-sm">
                @foreach($cart as $id => $item)
                    <div class="bg-white rounded-2xl border border-outline-variant shadow-sm p-sm md:p-md flex items-center gap-md hover:shadow-md transition-shadow group relative overflow-hidden">
                        <!-- Left border accent -->
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-primary scale-y-0 group-hover:scale-y-100 transition-transform duration-300 origin-bottom"></div>
                        
                        <div class="w-16 h-16 md:w-24 md:h-24 bg-surface-container-low rounded-xl flex items-center justify-center flex-shrink-0 border border-outline-variant overflow-hidden">
                            <span class="material-symbols-outlined text-outline text-3xl md:text-5xl opacity-50">inventory_2</span>
                        </div>
                        
                        <div class="flex-1 min-w-0 py-xs">
                            <h3 class="font-body-lg md:font-headline-sm font-bold text-primary truncate" title="{{ $item['name'] }}">{{ $item['name'] }}</h3>
                            <div class="font-bold text-on-surface mt-xs text-sm md:text-base">Rp {{ number_format($item['price'], 0, ',', '.') }}</div>
                        </div>

                        <!-- Controls -->
                        <div class="flex flex-col items-end gap-sm md:gap-md">
                            <button wire:click="removeItem({{ $id }})" 
                                    wire:loading.attr="disabled"
                                    class="text-error hover:bg-error/10 p-1.5 md:p-2 rounded-lg transition-colors flex items-center justify-center disabled:opacity-50" 
                                    title="Remove Item">
                                <span class="material-symbols-outlined text-[20px] md:text-[24px]">delete</span>
                            </button>
                            
                            <div class="flex items-center bg-surface border border-outline-variant rounded-xl overflow-hidden shadow-sm h-9 md:h-11">
                                <button wire:click="decrement({{ $id }})" 
                                        wire:loading.attr="disabled"
                                        class="w-9 md:w-11 h-full flex items-center justify-center text-on-surface hover:bg-primary hover:text-on-primary transition-colors disabled:opacity-50 active:bg-primary/80">
                                    <span class="material-symbols-outlined text-[18px]">remove</span>
                                </button>
                                <span class="w-10 md:w-14 h-full flex items-center justify-center font-bold text-on-surface text-sm md:text-base bg-white">{{ $item['quantity'] }}</span>
                                <button wire:click="increment({{ $id }})" 
                                        wire:loading.attr="disabled"
                                        class="w-9 md:w-11 h-full flex items-center justify-center text-on-surface hover:bg-primary hover:text-on-primary transition-colors disabled:opacity-50 active:bg-primary/80">
                                    <span class="material-symbols-outlined text-[18px]">add</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Summary Card -->
            <div class="bg-white rounded-3xl border border-outline-variant shadow-md p-md md:p-xl lg:sticky lg:top-24">
                <h2 class="font-headline-sm font-bold text-primary border-b border-outline-variant pb-sm mb-md flex items-center gap-xs">
                    <span class="material-symbols-outlined text-[24px]">receipt_long</span>
                    Order Summary
                </h2>
                
                <div class="space-y-sm mb-lg text-on-surface-variant font-body-md">
                    <div class="flex justify-between items-center">
                        <span>Items Count</span>
                        <span class="font-medium text-on-surface">{{ array_sum(array_column($cart, 'quantity')) }} items</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span>Subtotal</span>
                        <span class="font-medium text-on-surface">Rp {{ number_format($this->total, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="border-t border-outline-variant pt-md mb-xl flex justify-between items-center bg-surface-container-lowest -mx-md md:-mx-xl px-md md:px-xl py-sm">
                    <span class="font-bold text-on-surface text-lg">Estimated Total</span>
                    <span class="font-headline-sm font-bold text-primary">Rp {{ number_format($this->total, 0, ',', '.') }}</span>
                </div>

                <button wire:click="proceedToCheckout" 
                        wire:loading.attr="disabled"
                        class="w-full py-md bg-secondary text-white rounded-2xl font-label-lg font-bold shadow-md hover:bg-on-secondary-fixed-variant hover:shadow-xl transition-all active:scale-95 flex items-center justify-center gap-sm disabled:opacity-70 disabled:cursor-not-allowed group">
                    <span wire:loading.remove wire:target="proceedToCheckout">Proceed to Checkout</span>
                    <span wire:loading wire:target="proceedToCheckout" class="material-symbols-outlined animate-spin">progress_activity</span>
                    <span wire:loading.remove wire:target="proceedToCheckout" class="material-symbols-outlined text-[20px] group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </button>
                
                <p class="text-center text-[11px] text-outline mt-sm flex items-center justify-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">lock</span>
                    Secure Checkout
                </p>
            </div>
        </div>
    @endif
</div>
