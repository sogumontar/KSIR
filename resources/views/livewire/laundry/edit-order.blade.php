<div>
    <!-- Header -->
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('laundry.orders.show', $order->id) }}" wire:navigate class="btn-icon bg-white shadow-sm hover:bg-slate-50 border border-slate-200 w-10 h-10 rounded-full flex items-center justify-center text-slate-600">
            <span class="material-symbols-outlined text-sm">arrow_back</span>
        </a>
        <div>
            <h2 class="font-headline-lg text-primary m-0">Edit Order {{ $order->order_code }}</h2>
            <p class="text-xs text-slate-500 mt-1">{{ $isOwner ? 'Ubah detail order, status, atau promo.' : 'Mode Kontributor — Hanya bisa update status & pembayaran.' }}</p>
        </div>
    </div>

    {{-- Contributor notice --}}
    @if(!$isOwner)
    <div class="mb-6 p-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl flex items-center gap-3">
        <span class="material-symbols-outlined text-amber-500 shrink-0">info</span>
        <span class="text-sm">Sebagai <strong>kontributor</strong>, Anda hanya dapat mengubah <strong>status order</strong> dan <strong>status pembayaran</strong>. Hubungi owner untuk mengubah detail lainnya.</span>
    </div>
    @endif

    {{-- Flash --}}
    @if(session()->has('message'))
    <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm flex items-center gap-2">
        <span class="material-symbols-outlined text-green-600 text-base">check_circle</span>
        {{ session('message') }}
    </div>
    @endif
    @if(session()->has('error'))
    <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm flex items-center gap-2">
        <span class="material-symbols-outlined text-red-600 text-base">error</span>
        {{ session('error') }}
    </div>
    @endif

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
                    @if($existingPhotoBefore && !$photoBefore)
                        <div class="mb-3 flex items-center gap-3 bg-slate-50 p-3 rounded-xl border border-slate-200">
                            <img src="{{ storage_url($existingPhotoBefore) }}" alt="Current before photo" class="w-16 h-16 object-cover rounded-lg">
                            <span class="text-xs text-slate-600 font-medium">Current before photo uploaded</span>
                        </div>
                    @endif
                    <label class="border-2 border-dashed border-slate-300 rounded-xl p-6 text-center hover:bg-slate-50 transition-colors cursor-pointer relative flex flex-col items-center justify-center">
                        <span class="material-symbols-outlined text-3xl text-slate-400 mb-2">add_a_photo</span>
                        <p class="font-label-md text-slate-700">{{ $existingPhotoBefore ? 'Click to replace photo' : 'Click to upload photo' }}</p>
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
                            New photo selected: {{ $photoBefore->getClientOriginalName() }}
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
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:col-span-2 border-t border-slate-200 pt-3 mt-1">
                                    <div>
                                        <label class="form-label">Price per Unit (Rp) <span class="text-error">*</span></label>
                                        <input wire:model.live="items.{{ $index }}.price" type="number" class="form-input w-full bg-white" min="0" step="any">
                                        @error('items.'.$index.'.price') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="form-label">Qty / Weight (kg/pcs) <span class="text-error">*</span></label>
                                        <input wire:model.live="items.{{ $index }}.qty" type="number" class="form-input w-full bg-white" min="0.01" step="any" placeholder="1">
                                        @error('items.'.$index.'.qty') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="md:col-span-2 flex justify-between items-center text-xs font-semibold text-slate-700 bg-white p-2 rounded border border-slate-200">
                                        <span>Item Subtotal:</span>
                                        <span class="text-secondary font-bold text-sm">Rp{{ number_format(((float)($item['price'] ?? 0)) * ((float)($item['qty'] ?? 1)), 0) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Sidebar Summary -->
        <div class="space-y-6">

            {{-- Contributor quick-update panel (shown instead of full form for contributors) --}}
            @if(!$isOwner)
            <div class="card-surface p-6">
                <div class="flex items-center gap-3 mb-4 border-b border-slate-100 pb-3">
                    <span class="material-symbols-outlined text-secondary">published_with_changes</span>
                    <h3 class="font-headline-md text-slate-900 m-0">Update Status</h3>
                </div>

                <div class="mb-4">
                    <label class="form-label block mb-2">Status Order</label>
                    <select wire:model="orderStatus" class="form-input w-full">
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="in_progress">In Progress</option>
                        <option value="ready">Ready</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <button type="button" wire:click="quickUpdateStatus" class="btn-primary w-full justify-center mb-4">
                    <span wire:loading.remove wire:target="quickUpdateStatus">Simpan Status</span>
                    <span wire:loading wire:target="quickUpdateStatus">Menyimpan...</span>
                </button>

                <div class="mb-4">
                    <label class="form-label block mb-2">Status Pembayaran</label>
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
                <button type="button" wire:click="quickUpdatePayment" class="btn-primary w-full justify-center">
                    <span wire:loading.remove wire:target="quickUpdatePayment">Simpan Pembayaran</span>
                    <span wire:loading wire:target="quickUpdatePayment">Menyimpan...</span>
                </button>
            </div>
            @endif

            <!-- Status & Payment Card (Owner only) -->
            @if($isOwner)
            <div class="card-surface p-6">
                <div class="flex items-center gap-3 mb-4 border-b border-slate-100 pb-3">
                    <span class="material-symbols-outlined text-secondary">published_with_changes</span>
                    <h3 class="font-headline-md text-slate-900 m-0">Status</h3>
                </div>

                <div class="mb-4">
                    <label class="form-label block mb-2">Order Status</label>
                    <select wire:model="orderStatus" class="form-input w-full">
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="in_progress">In Progress</option>
                        <option value="ready">Ready</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <div class="mb-4">
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

                {{-- Assignee (Owner only) --}}
                <div class="mt-4">
                    <label class="form-label block mb-2">Assignee</label>
                    <select wire:model="assigneeId" class="form-input w-full bg-white">
                        <option value="">-- Tidak ada assignee --</option>
                        @foreach($assignableUsers as $aUser)
                            @if($aUser)
                            <option value="{{ $aUser->id }}">{{ $aUser->name }}</option>
                            @endif
                        @endforeach
                    </select>
                    @error('assigneeId')<span class="text-error text-xs mt-1 block">{{ $message }}</span>@enderror
                </div>
            </div>
            @endif

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

            {{-- Summary / Save (Owner only) --}}
            @if($isOwner)
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

                <div class="flex gap-3">
                    <a href="{{ route('laundry.orders.show', $order->id) }}" wire:navigate class="btn-ghost flex-1 justify-center border border-slate-200">
                        Cancel
                    </a>
                    <button type="submit" wire:loading.attr="disabled" wire:target="submit, photoBefore" class="btn-primary flex-1 justify-center py-3 text-base shadow-sm relative">
                        <span wire:loading.remove wire:target="submit" class="flex items-center gap-2">
                            <span class="material-symbols-outlined">save</span>
                            Save Changes
                        </span>
                        <span wire:loading wire:target="submit">Saving...</span>
                    </button>
                </div>
            </div>
            @endif
        </div>
    </form>
</div>
