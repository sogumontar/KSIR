<div>
    <!-- Sub-nav pills -->
    <div class="flex gap-2 mb-8 overflow-x-auto pb-2">
        <a href="/laundry/dashboard" wire:navigate class="px-4 py-2 rounded-full font-label-md bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 whitespace-nowrap">Dashboard</a>
        <a href="/laundry/services" wire:navigate class="px-4 py-2 rounded-full font-label-md bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 whitespace-nowrap">Services</a>
        <a href="/laundry/promos" wire:navigate class="px-4 py-2 rounded-full font-label-md bg-secondary text-white shadow-sm whitespace-nowrap">Promos</a>
        <a href="/laundry/settings" wire:navigate class="px-4 py-2 rounded-full font-label-md bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 whitespace-nowrap">Settings</a>
    </div>

    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-md mb-8">
        <div>
            <h2 class="font-headline-lg text-primary m-0">Promo & Rewards Manager</h2>
        </div>
        <div class="flex items-center gap-md">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search promos..." class="form-input bg-white w-64">
            <button wire:click="openAdd" class="btn-primary gap-2">
                <span class="material-symbols-outlined">add</span>
                Add Promo
            </button>
        </div>
    </div>

    <!-- Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($promos as $promo)
        <div class="card-surface p-6 flex flex-col relative group transition-all hover:shadow-md border-t-4 {{ $promo->type === 'percentage' ? 'border-t-blue-500' : 'border-t-purple-500' }}">
            <div class="flex justify-between items-start mb-2">
                <h3 class="font-headline-md text-slate-900 pr-12">{{ $promo->name }}</h3>
                <div class="absolute top-4 right-4 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity bg-white/90 rounded-lg p-1 backdrop-blur-sm">
                    <button wire:click="openEdit({{ $promo->id }})" class="btn-icon w-8 h-8 text-slate-500 hover:text-primary"><span class="material-symbols-outlined text-sm">edit</span></button>
                    <button wire:click="delete({{ $promo->id }})" class="btn-icon w-8 h-8 text-slate-500 hover:text-error"><span class="material-symbols-outlined text-sm">delete</span></button>
                </div>
            </div>
            
            <div class="flex items-center gap-2 mb-4">
                <span class="px-2 py-0.5 rounded text-xs font-medium uppercase tracking-wider {{ $promo->type === 'percentage' ? 'bg-blue-50 text-blue-700' : 'bg-purple-50 text-purple-700' }}">
                    {{ $promo->type }}
                </span>
                <span class="px-2 py-0.5 rounded text-xs font-medium {{ $promo->is_active ? 'bg-green-100 text-green-800' : 'bg-slate-100 text-slate-600' }}">
                    {{ $promo->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>

            <div class="bg-slate-50 rounded-lg p-4 flex-grow flex items-center justify-center">
                @if($promo->type === 'percentage')
                    <div class="text-center">
                        <span class="text-4xl font-display text-blue-600">{{ intval($promo->discount_percent) }}%</span>
                        <span class="block text-sm text-slate-500 mt-1 font-medium">Discount applied to total</span>
                    </div>
                @else
                    <div class="text-center">
                        <div class="flex items-center justify-center gap-3 text-2xl font-display text-purple-600">
                            <span>Buy {{ $promo->buy_quantity }}</span>
                            <span class="material-symbols-outlined text-purple-300">arrow_forward</span>
                            <span>Free {{ $promo->free_quantity }}</span>
                        </div>
                        <span class="block text-sm text-slate-500 mt-2 font-medium">Item-based accumulation</span>
                    </div>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-1 md:col-span-3 card-surface p-12 text-center">
            <span class="material-symbols-outlined text-6xl text-slate-300 mb-4">loyalty</span>
            <h3 class="font-headline-md text-slate-500">No promos found</h3>
            <p class="text-slate-400 mt-2">Create promotional campaigns to attract more customers.</p>
            <button wire:click="openAdd" class="btn-primary mt-6 mx-auto">Create Promo</button>
        </div>
        @endforelse
    </div>

    <!-- Modal -->
    @if($showForm)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl overflow-hidden flex flex-col">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-headline-md m-0">{{ $editingId ? 'Edit Promo' : 'Create Promo' }}</h3>
                <button wire:click="cancel" class="text-slate-400 hover:text-slate-600"><span class="material-symbols-outlined">close</span></button>
            </div>
            <div class="p-6 space-y-5">
                <div>
                    <label class="form-label">Promo Name</label>
                    <input wire:model="name" type="text" class="form-input w-full" placeholder="e.g. Weekend Special or Buy 5 Free 1">
                    @error('name') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="form-label">Promo Type</label>
                    <div class="flex gap-4">
                        <label class="flex-1 border border-slate-200 rounded-xl p-4 cursor-pointer hover:border-secondary hover:bg-slate-50 transition-colors {{ $type === 'percentage' ? 'border-secondary ring-1 ring-secondary bg-surface-container-low' : '' }}">
                            <div class="flex items-center gap-2 mb-2">
                                <input wire:model.live="type" type="radio" value="percentage" class="text-secondary focus:ring-secondary">
                                <span class="font-semibold text-slate-900">Percentage</span>
                            </div>
                            <p class="text-xs text-slate-500 ml-6">Discount off the total bill.</p>
                        </label>
                        <label class="flex-1 border border-slate-200 rounded-xl p-4 cursor-pointer hover:border-secondary hover:bg-slate-50 transition-colors {{ $type === 'accumulative' ? 'border-secondary ring-1 ring-secondary bg-surface-container-low' : '' }}">
                            <div class="flex items-center gap-2 mb-2">
                                <input wire:model.live="type" type="radio" value="accumulative" class="text-secondary focus:ring-secondary">
                                <span class="font-semibold text-slate-900">Accumulative</span>
                            </div>
                            <p class="text-xs text-slate-500 ml-6">Buy X get Y free on items.</p>
                        </label>
                    </div>
                </div>

                <!-- Dynamic Fields -->
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                    @if($type === 'percentage')
                        <div>
                            <label class="form-label">Discount Percentage (%)</label>
                            <input wire:model="percent" type="number" min="1" max="100" class="form-input w-full" placeholder="e.g. 10">
                            @error('percent') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    @else
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">Buy Qty</label>
                                <input wire:model="buyQty" type="number" min="1" class="form-input w-full" placeholder="e.g. 5">
                                @error('buyQty') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="form-label">Free Qty</label>
                                <input wire:model="freeQty" type="number" min="1" class="form-input w-full" placeholder="e.g. 1">
                                @error('freeQty') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    @endif
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input wire:model="isActive" type="checkbox" id="isActivePromo" class="rounded border-slate-300 text-secondary focus:ring-secondary">
                    <label for="isActivePromo" class="text-sm text-slate-700 font-medium cursor-pointer">Active Promo</label>
                </div>
            </div>
            <div class="p-6 border-t border-slate-100 bg-slate-50 flex justify-end gap-4">
                <button wire:click="cancel" class="btn-ghost">Cancel</button>
                <button wire:click="save" class="btn-primary relative">
                    <span wire:loading.remove wire:target="save">Save Promo</span>
                    <span wire:loading wire:target="save">Saving...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
