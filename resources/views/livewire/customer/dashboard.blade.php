<div class="p-lg md:p-xl space-y-lg">
    <header class="mb-lg">
        <h1 class="font-headline-lg text-headline-lg text-primary font-bold">My Merchant Stores</h1>
        <p class="font-body-md text-on-surface-variant mt-xs">Access and manage your exclusive merchant connections.</p>
    </header>

    @if($merchants->isEmpty())
        <div class="bg-white p-xl rounded-xl border border-outline-variant text-center shadow-sm">
            <span class="material-symbols-outlined text-4xl text-on-primary-container mb-md">storefront</span>
            <p class="font-headline-md text-on-surface">No merchant stores linked yet.</p>
            <p class="text-on-surface-variant mt-sm">Use an exclusive invitation link from a merchant to get started.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-gutter">
            @foreach($merchants as $merchant)
                <div class="bg-white p-lg rounded-xl border border-outline-variant shadow-sm hover:shadow-md transition-all card-hover group flex flex-col items-center">
                    <img src="{{ $merchant->profile_photo ? asset('storage/' . $merchant->profile_photo) : asset('images/default-avatar.png') }}" 
                         alt="{{ $merchant->name }}" 
                         class="w-24 h-24 rounded-full object-cover mb-md border-4 border-surface-container-low">
                    
                    <h3 class="font-headline-md text-primary text-center mb-xs">{{ $merchant->name }}</h3>
                    <p class="font-label-md text-on-primary-container text-center mb-lg uppercase tracking-wider">{{ $merchant->category }}</p>
                    
                    <a href="{{ route('customer.storefront', $merchant->unique_code) }}" 
                       class="w-full py-md text-center bg-primary text-white rounded-lg font-label-lg hover:bg-primary/90 transition-all">
                        View Store
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</div>
