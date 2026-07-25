<div>
    <!-- Header -->
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('laundry.dashboard') }}" wire:navigate class="btn-icon bg-white shadow-sm hover:bg-slate-50 border border-slate-200 w-10 h-10 rounded-full flex items-center justify-center text-slate-600">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
        </a>
        <h2 class="font-headline-lg text-primary m-0">Create New Order</h2>
    </div>

    <form wire:submit="submit" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Form Column -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Customer Info Card -->
            <div class="card-surface p-6">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-3">
                    <span class="material-symbols-outlined text-secondary">person</span>
                    <h3 class="font-headline-md text-slate-900 m-0">Customer Info</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="form-label">Customer Name <span class="text-error">*</span></label>
                        <input wire:model="customerName" type="text" class="form-input w-full" placeholder="e.g. John Doe">
                        @error('customerName') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="form-label">WhatsApp Number</label>
                        <input wire:model="customerPhone" type="text" class="form-input w-full" placeholder="e.g. 08123456789">
                        @error('customerPhone') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
                
                <div>
                    <label class="form-label block mb-2">Condition Photo Before (Optional)</label>
                    <label class="border-2 border-dashed border-slate-300 rounded-xl p-6 text-center hover:bg-slate-50 transition-colors cursor-pointer relative flex flex-col items-center justify-center">
                        <span class="material-symbols-outlined text-3xl text-slate-400 mb-2">add_a_photo</span>
                        <p class="font-label-md text-slate-700">Click to upload photo</p>
                        <input accept="image/*" class="hidden" type="file" wire:model="photoBefore">

                        {{-- Livewire Uploading Status --}}
                        <div wire:loading wire:target="photoBefore" class="absolute inset-0 bg-white/95 flex items-center justify-center rounded-xl">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="animate-spin h-8 w-8 text-secondary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                <span class="text-xs font-semibold text-slate-600">Uploading photo...</span>
                            </div>
                        </div>
                    </label>
                    @if($photoBefore)
                        <div class="mt-3 text-sm text-green-700 flex items-center gap-2">
                            <span class="material-symbols-outlined text-sm">check_circle</span>
                            Photo selected: {{ $photoBefore->getClientOriginalName() }}
                        </div>
                    @endif
                    @error('photoBefore') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Delivery Card -->
            <div class="card-surface p-6">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-3">
                    <span class="material-symbols-outlined text-secondary">local_shipping</span>
                    <h3 class="font-headline-md text-slate-900 m-0">Delivery Method</h3>
                </div>
                
                <div class="flex gap-4 mb-6">
                    <label class="flex-1 border border-slate-200 rounded-xl p-4 cursor-pointer hover:border-secondary hover:bg-slate-50 transition-colors {{ $deliveryType === 'pickup' ? 'border-secondary ring-1 ring-secondary bg-surface-container-low' : '' }}">
                        <div class="flex items-center gap-2">
                            <input wire:model.live="deliveryType" type="radio" value="pickup" class="text-secondary focus:ring-secondary">
                            <span class="font-semibold text-slate-900">Self Pickup</span>
                        </div>
                    </label>
                    <label class="flex-1 border border-slate-200 rounded-xl p-4 cursor-pointer hover:border-secondary hover:bg-slate-50 transition-colors {{ $deliveryType === 'delivery' ? 'border-secondary ring-1 ring-secondary bg-surface-container-low' : '' }}">
                        <div class="flex items-center gap-2">
                            <input wire:model.live="deliveryType" type="radio" value="delivery" class="text-secondary focus:ring-secondary">
                            <span class="font-semibold text-slate-900">Delivery</span>
                        </div>
                    </label>
                </div>
                
                @if($deliveryType === 'delivery')
                    <div>
                        <label class="form-label">Delivery Address <span class="text-error">*</span></label>
                        <textarea wire:model="customerAddress" class="form-input w-full h-24" placeholder="Enter complete address"></textarea>
                        @error('customerAddress') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                @endif
            </div>

            <!-- Order Items Repeater -->
            <div class="card-surface p-6">
                <div class="flex items-center justify-between mb-6 border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-secondary">list_alt</span>
                        <h3 class="font-headline-md text-slate-900 m-0">Order Items</h3>
                    </div>
                    <button type="button" wire:click="addItem" class="btn-ghost text-secondary hover:bg-green-50 flex items-center gap-1 py-1 px-3 text-sm rounded-full">
                        <span class="material-symbols-outlined text-sm">add</span> Add Item
                    </button>
                </div>

                <div class="space-y-6">
                    @foreach($items as $index => $item)
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 relative group">
                            @if(count($items) > 1)
                                <button type="button" wire:click="removeItem({{ $index }})" class="absolute -top-3 -right-3 w-8 h-8 bg-white border border-slate-200 text-error rounded-full flex items-center justify-center shadow-sm hover:bg-error hover:text-white transition-colors z-10">
                                    <span class="material-symbols-outlined text-sm">close</span>
                                </button>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="form-label">Service <span class="text-error">*</span></label>
                                    <select wire:model.live="items.{{ $index }}.service_id" class="form-input w-full bg-white">
                                        <option value="">-- Select Service --</option>
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}">{{ $service->name }} (Rp{{ number_format($service->price, 0) }})</option>
                                        @endforeach
                                    </select>
                                    @error('items.'.$index.'.service_id') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="md:col-span-2">
                                    <label class="form-label">Treatment/Notes</label>
                                    <input wire:model="items.{{ $index }}.treatment" type="text" class="form-input w-full bg-white" placeholder="e.g. Iron only, delicate wash">
                                </div>

                                <div>
                                    <label class="form-label">Date In <span class="text-error">*</span></label>
                                    <input wire:model="items.{{ $index }}.date_in" type="date" class="form-input w-full bg-white">
                                    @error('items.'.$index.'.date_in') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="form-label">Est. Done <span class="text-error">*</span></label>
                                    <input wire:model="items.{{ $index }}.date_estimated_done" type="date" class="form-input w-full bg-white">
                                    @error('items.'.$index.'.date_estimated_done') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                
                                <div class="md:col-span-2 border-t border-slate-200 pt-3 mt-1">
                                    <label class="form-label">Price Override (Rp)</label>
                                    <input wire:model.live="items.{{ $index }}.price" type="number" class="form-input w-full bg-white" min="0">
                                    <p class="text-xs text-slate-500 mt-1">Automatically filled from service, but can be manually adjusted.</p>
                                    @error('items.'.$index.'.price') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Sidebar Summary -->
        <div class="space-y-6">
            <!-- Promo Card -->
            <div class="card-surface p-6">
                <div class="flex items-center gap-3 mb-4 border-b border-slate-100 pb-3">
                    <span class="material-symbols-outlined text-secondary">loyalty</span>
                    <h3 class="font-headline-md text-slate-900 m-0">Promo</h3>
                </div>
                
                <select wire:model.live="selectedPromoId" class="form-input w-full">
                    <option value="">No Promo Applied</option>
                    @foreach($promos as $promo)
                        <option value="{{ $promo->id }}">{{ $promo->name }}</option>
                    @endforeach
                </select>
                @if($this->discountAmount > 0)
                    <p class="text-sm text-green-600 font-medium mt-3 flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm">check_circle</span>
                        Promo applied successfully!
                    </p>
                @endif
            </div>

            <!-- Summary Card -->
            <div class="card-surface p-6">
                <div class="flex items-center gap-3 mb-4 border-b border-slate-100 pb-3">
                    <span class="material-symbols-outlined text-secondary">receipt_long</span>
                    <h3 class="font-headline-md text-slate-900 m-0">Summary</h3>
                </div>

                <div class="space-y-3 mb-6 font-body-md">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal ({{ count($items) }} items)</span>
                        <span>Rp{{ number_format($this->subtotal, 0) }}</span>
                    </div>
                    @if($this->discountAmount > 0)
                        <div class="flex justify-between text-green-600 font-medium">
                            <span>Discount</span>
                            <span>-Rp{{ number_format($this->discountAmount, 0) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-lg font-bold text-slate-900 pt-3 border-t border-slate-100">
                        <span>Total</span>
                        <span>Rp{{ number_format($this->total, 0) }}</span>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="form-label block mb-2">Payment Status</label>
                    <div class="flex rounded-lg overflow-hidden border border-slate-200">
                        <label class="flex-1 text-center cursor-pointer">
                            <input wire:model="paymentStatus" type="radio" value="unpaid" class="peer sr-only">
                            <div class="py-2 peer-checked:bg-amber-100 peer-checked:text-amber-800 peer-checked:font-bold text-slate-500 bg-slate-50 transition-colors">Unpaid</div>
                        </label>
                        <label class="flex-1 text-center cursor-pointer border-l border-slate-200">
                            <input wire:model="paymentStatus" type="radio" value="paid" class="peer sr-only">
                            <div class="py-2 peer-checked:bg-green-100 peer-checked:text-green-800 peer-checked:font-bold text-slate-500 bg-slate-50 transition-colors">Paid</div>
                        </label>
                    </div>
                </div>

                <button type="submit" wire:loading.attr="disabled" wire:target="submit, photoBefore" class="btn-primary w-full justify-center py-3 text-base shadow-sm relative">
                    <span wire:loading.remove wire:target="submit" class="flex items-center gap-2">
                        <span class="material-symbols-outlined">check_circle</span>
                        Create Order
                    </span>
                    <span wire:loading wire:target="submit">Processing...</span>
                </button>
            </div>
        </div>
    </form>
</div>
