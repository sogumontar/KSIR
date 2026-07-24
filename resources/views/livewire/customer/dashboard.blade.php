<div class="px-margin-mobile md:px-margin-desktop py-lg space-y-lg max-w-[1440px] mx-auto">
    <!-- Header Section -->
    <header class="mb-lg flex flex-col sm:flex-row sm:justify-between sm:items-center gap-md">
        <div>
            <h1 class="font-headline-lg-mobile md:font-headline-lg text-primary font-bold tracking-tight">My Merchant Stores</h1>
            <p class="font-body-sm md:font-body-md text-on-surface-variant mt-xs">Access and manage your exclusive merchant connections.</p>
        </div>
        @if(session()->has('message'))
            <div class="p-sm bg-secondary-container text-on-secondary-container rounded-lg font-label-md flex items-center gap-xs animate-fade-in shadow-sm">
                <span class="material-symbols-outlined text-[20px]">check_circle</span>
                {{ session('message') }}
            </div>
        @endif
    </header>

    @if($merchants->isEmpty())
        <!-- Empty State -->
        <div class="bg-white p-xl rounded-2xl border border-outline-variant text-center shadow-sm flex flex-col items-center justify-center min-h-[300px]">
            <div class="w-16 h-16 bg-surface-container rounded-full flex items-center justify-center mb-md">
                <span class="material-symbols-outlined text-4xl text-primary" data-icon="storefront">storefront</span>
            </div>
            <h2 class="font-headline-md text-on-surface font-semibold">No Stores Linked Yet</h2>
            <p class="text-on-surface-variant mt-sm max-w-sm">Use an exclusive invitation link from a merchant to gain access to their products and start ordering.</p>
            <div class="mt-lg p-md bg-surface-container-low rounded-xl border border-dashed border-outline-variant inline-block">
                <span class="text-sm font-label-md text-primary italic">Invitation link required to link stores</span>
            </div>
        </div>
    @else
        <!-- Merchant Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-gutter">
            @foreach($merchants as $merchant)
                <div class="bg-white rounded-2xl border border-outline-variant shadow-sm hover:shadow-lg transition-all duration-300 card-hover group flex flex-col overflow-hidden">
                    <!-- Banner/Cover area (Subtle) -->
                    <div class="h-24 w-full bg-surface-container-low relative">
                         @if($merchant->banner_photo)
                            <img src="{{ Storage::disk('public')->url($merchant->banner_photo) }}" class="w-full h-full object-cover opacity-50">
                         @endif
                         <div class="absolute inset-0 bg-gradient-to-t from-white to-transparent"></div>
                    </div>

                    <div class="px-lg pb-lg -mt-12 flex flex-col items-center flex-1">
                        <!-- Profile Photo -->
                        <div class="relative mb-md">
                            <img src="{{ $merchant->profile_photo ? Storage::disk('public')->url($merchant->profile_photo) : 'https://ui-avatars.com/api/?name='.urlencode($merchant->name).'&color=7F9CF5&background=EBF4FF' }}" 
                                 alt="{{ $merchant->name }}" 
                                 class="w-24 h-24 rounded-2xl object-cover border-4 border-white shadow-md group-hover:scale-105 transition-transform duration-300 bg-white">
                            @if($merchant->operating_status)
                                <span class="absolute bottom-1 right-1 w-4 h-4 bg-green-500 border-2 border-white rounded-full" title="Active"></span>
                            @endif
                        </div>
                        
                        <!-- Merchant Details -->
                        <h3 class="font-headline-md text-primary text-center font-bold tracking-tight">{{ $merchant->store_name ?: $merchant->name }}</h3>
                        <p class="font-label-caps text-[10px] text-on-primary-container text-center mb-sm uppercase tracking-widest">{{ $merchant->category ?: 'General Store' }}</p>
                        
                        @if($merchant->store_description)
                            <p class="text-body-sm text-on-surface-variant text-center line-clamp-2 mb-md min-h-[40px]">
                                {{ $merchant->store_description }}
                            </p>
                        @endif

                        <!-- Contact Badges -->
                        <div class="flex flex-wrap justify-center gap-xs mb-lg">
                            @if($merchant->business_address)
                                <span class="flex items-center gap-xs px-sm py-1 bg-surface-container rounded-full text-[10px] text-on-surface-variant">
                                    <span class="material-symbols-outlined text-[14px]">location_on</span>
                                    <span class="truncate max-w-[100px]">{{ $merchant->business_address }}</span>
                                </span>
                            @endif
                        </div>
                        
                        <div class="mt-auto w-full">
                            <a href="{{ route('customer.storefront', $merchant->unique_code) }}" 
                               class="w-full py-md flex items-center justify-center gap-sm bg-primary text-white rounded-xl font-label-lg hover:bg-on-primary-fixed transition-all shadow-sm active:scale-[0.98] duration-200">
                                <span class="material-symbols-outlined text-[20px]">shopping_bag</span>
                                View Storefront
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Order History --}}
    <section class="mt-xl">
        <header class="flex items-center justify-between border-b border-outline-variant pb-xs mb-md">
            <h2 class="font-headline-md text-primary font-bold flex items-center gap-xs">
                <span class="material-symbols-outlined text-[24px]">receipt_long</span>
                My Orders
            </h2>
            <span class="text-on-surface-variant font-label-md">{{ $orders->count() }} {{ Str::plural('order', $orders->count()) }}</span>
        </header>

        @if($orders->isEmpty())
            <div class="bg-white rounded-2xl border border-outline-variant shadow-sm p-xl text-center flex flex-col items-center">
                <div class="w-16 h-16 bg-surface-container-low rounded-full flex items-center justify-center mb-md">
                    <span class="material-symbols-outlined text-outline text-4xl">shopping_bag</span>
                </div>
                <h3 class="font-headline-sm font-semibold text-on-surface">No orders yet</h3>
                <p class="text-on-surface-variant text-sm mt-xs max-w-sm">Once you place an order from a merchant storefront, it will appear here.</p>
            </div>
        @else
            <div class="space-y-sm">
                @foreach($orders as $order)
                    <div class="bg-white rounded-2xl border border-outline-variant shadow-sm p-md hover:shadow-md transition-shadow group">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-sm">
                            {{-- Order info --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-sm flex-wrap">
                                    <span class="font-bold text-on-surface text-sm">Order #{{ $order->id }}</span>
                                    <span class="text-outline-variant">•</span>
                                    <span class="text-on-surface-variant text-xs">{{ $order->created_at->format('d M Y, H:i') }}</span>
                                </div>
                                <div class="text-on-surface-variant text-xs mt-1 flex items-center gap-xs">
                                    <span class="material-symbols-outlined text-[14px]">storefront</span>
                                    {{ $order->merchant?->store_name ?: $order->merchant?->name ?: 'Unknown' }}
                                </div>
                                {{-- Item summary --}}
                                <div class="mt-sm text-xs text-on-surface-variant">
                                    @foreach($order->items->take(3) as $item)
                                        <span class="inline-flex items-center gap-1 bg-surface-container-low px-2 py-0.5 rounded-full mr-1 mb-1">
                                            {{ $item->good?->name ?: 'Deleted item' }} × {{ $item->quantity }}
                                        </span>
                                    @endforeach
                                    @if($order->items->count() > 3)
                                        <span class="text-primary text-[11px] font-medium">+{{ $order->items->count() - 3 }} more</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Status & Total --}}
                            <div class="flex sm:flex-col items-center sm:items-end gap-sm sm:gap-xs flex-shrink-0">
                                @php
                                    $statusColors = [
                                        'Pending'    => 'bg-amber-100 text-amber-800 border-amber-200',
                                        'Delivering' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'Completed'  => 'bg-green-100 text-green-800 border-green-200',
                                        'Cancelled'  => 'bg-red-100 text-red-800 border-red-200',
                                    ];
                                    $color = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                                @endphp
                                <span class="px-sm py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $color }}">
                                    {{ $order->status }}
                                </span>
                                <span class="font-bold text-primary text-sm">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</div>
