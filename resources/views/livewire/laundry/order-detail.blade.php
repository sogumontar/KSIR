<div>
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('laundry.dashboard') }}" wire:navigate class="btn-icon bg-white shadow-sm hover:bg-slate-50 border border-slate-200 w-10 h-10 rounded-full flex items-center justify-center text-slate-600">
                <span class="material-symbols-outlined text-sm">arrow_back</span>
            </a>
            <div>
                <h2 class="font-headline-lg text-primary m-0 flex items-center gap-3">
                    Order {{ $order->order_code }}
                    @php
                        $statusColors = [
                            'pending' => 'bg-slate-100 text-slate-800 border-slate-200',
                            'processing' => 'bg-blue-100 text-blue-800 border-blue-200',
                            'ready' => 'bg-amber-100 text-amber-800 border-amber-200',
                            'completed' => 'bg-green-100 text-green-800 border-green-200',
                            'cancelled' => 'bg-red-100 text-red-800 border-red-200',
                        ];
                    @endphp
                    <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium border {{ $statusColors[$order->status] }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </h2>
                <p class="text-slate-500 font-label-md mt-1">Placed on {{ $order->created_at->format('M d, Y H:i') }}</p>
            </div>
        </div>
        
        <div class="flex gap-2">
            <a href="{{ route('laundry.orders.receipt', $order->id) }}" target="_blank" class="btn-ghost border border-slate-200 bg-white hover:bg-slate-50 gap-2">
                <span class="material-symbols-outlined text-slate-600">receipt_long</span>
                Print Receipt
            </a>
            <a href="{{ route('laundry.public.track', $order->tracking_code) }}" target="_blank" class="btn-ghost border border-slate-200 bg-white hover:bg-slate-50 gap-2">
                <span class="material-symbols-outlined text-slate-600">public</span>
                Tracking Link
            </a>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-center gap-2">
            <span class="material-symbols-outlined text-green-600">check_circle</span>
            <span class="text-sm font-medium">{{ session('message') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Items Table -->
            <div class="card-surface overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center gap-3">
                    <span class="material-symbols-outlined text-secondary">list_alt</span>
                    <h3 class="font-headline-md text-slate-900 m-0">Order Items</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50">
                                <th class="table-header font-label-md">Service</th>
                                <th class="table-header font-label-md">Treatment</th>
                                <th class="table-header font-label-md">Date In</th>
                                <th class="table-header font-label-md">Est. Done</th>
                                <th class="table-header font-label-md text-right">Price</th>
                            </tr>
                        </thead>
                        <tbody class="font-body-md text-slate-700 divide-y divide-slate-100">
                            @foreach($order->items as $item)
                            <tr class="hover:bg-slate-50 {{ $item->is_free ? 'bg-emerald-50/30' : '' }}">
                                <td class="table-cell font-medium">
                                    {{ $item->service_name_snapshot }}
                                    @if($item->is_free)
                                        <span class="ml-2 inline-block text-[10px] font-bold text-emerald-600 bg-emerald-100 px-2 py-0.5 rounded">FREE</span>
                                    @endif
                                </td>
                                <td class="table-cell text-sm">{{ $item->treatment ?: '-' }}</td>
                                <td class="table-cell text-sm">{{ \Carbon\Carbon::parse($item->date_in)->format('M d') }}</td>
                                <td class="table-cell text-sm">{{ \Carbon\Carbon::parse($item->date_estimated_done)->format('M d') }}</td>
                                <td class="table-cell text-right font-bold">
                                    @if($item->is_free)
                                        <span class="font-bold text-emerald-600">Rp0</span>
                                    @elseif($item->final_price < $item->price_snapshot)
                                        <span class="text-xs text-slate-400 line-through block font-normal">Rp{{ number_format($item->price_snapshot, 0) }}</span>
                                        <span class="font-bold text-slate-900">Rp{{ number_format($item->final_price, 0) }}</span>
                                    @else
                                        <span>Rp{{ number_format($item->price_snapshot, 0) }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Photos -->
            <div class="card-surface p-6">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-3">
                    <span class="material-symbols-outlined text-secondary">photo_library</span>
                    <h3 class="font-headline-md text-slate-900 m-0">Condition Photos</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-label-lg text-slate-700 mb-3 text-center">Before Wash</h4>
                        @if($order->photo_before)
                            <img src="{{ storage_url($order->photo_before) }}" alt="Before" class="w-full h-48 object-cover rounded-xl shadow-sm border border-slate-200">
                        @else
                            <div class="w-full h-48 rounded-xl border-2 border-dashed border-slate-200 flex items-center justify-center text-slate-400 bg-slate-50">
                                No photo provided
                            </div>
                        @endif
                    </div>
                    
                    <div>
                        <h4 class="font-label-lg text-slate-700 mb-3 text-center">After Wash</h4>
                        @if($order->photo_after)
                            <img src="{{ storage_url($order->photo_after) }}" alt="After" class="w-full h-48 object-cover rounded-xl shadow-sm border border-slate-200">
                        @else
                            <label class="w-full h-48 rounded-xl border-2 border-dashed border-slate-300 flex flex-col items-center justify-center text-slate-500 hover:bg-slate-50 transition-colors cursor-pointer bg-white relative">
                                <span class="material-symbols-outlined text-3xl mb-2 text-slate-400">upload_file</span>
                                <span class="text-sm font-medium">Upload After Photo</span>
                                <input accept="image/*" class="hidden" type="file" wire:model="photoAfter">

                                {{-- Livewire Uploading Status --}}
                                <div wire:loading wire:target="photoAfter" class="absolute inset-0 bg-white/95 flex items-center justify-center rounded-xl">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg class="animate-spin h-8 w-8 text-secondary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                        <span class="text-xs font-semibold text-slate-600">Uploading photo...</span>
                                    </div>
                                </div>
                            </label>
                            @if($photoAfter)
                                <button wire:click="uploadPhotoAfter" wire:loading.attr="disabled" class="w-full mt-3 btn-primary text-sm justify-center py-2 relative">
                                    <span wire:loading.remove wire:target="uploadPhotoAfter">Save Photo</span>
                                    <span wire:loading wire:target="uploadPhotoAfter">Saving...</span>
                                </button>
                            @endif
                            @error('photoAfter') <span class="text-error text-xs mt-1 block text-center">{{ $message }}</span> @enderror
                        @endif
                    </div>
                </div>
            </div>

            <div class="card-surface p-6">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-3">
                    <span class="material-symbols-outlined text-secondary">published_with_changes</span>
                    <h3 class="font-headline-md text-slate-900 m-0">Update Status</h3>
                </div>
                
                <div class="flex flex-wrap gap-3">
                    @php
                        $statusButtons = [
                            'pending' => ['label' => 'Pending', 'activeBg' => 'bg-slate-800', 'activeBorder' => 'border-slate-800'],
                            'processing' => ['label' => 'Processing', 'activeBg' => 'bg-blue-600', 'activeBorder' => 'border-blue-600'],
                            'ready' => ['label' => 'Ready', 'activeBg' => 'bg-amber-500', 'activeBorder' => 'border-amber-500'],
                            'completed' => ['label' => 'Completed', 'activeBg' => 'bg-green-600', 'activeBorder' => 'border-green-600'],
                            'cancelled' => ['label' => 'Cancelled', 'activeBg' => 'bg-red-600', 'activeBorder' => 'border-red-600'],
                        ];
                    @endphp

                    @foreach($statusButtons as $statusKey => $btn)
                        <button
                            wire:click="updateStatus('{{ $statusKey }}')"
                            wire:loading.attr="disabled"
                            class="px-4 py-2 rounded-lg font-medium text-sm transition-colors border relative
                                {{ $order->status === $statusKey
                                    ? "{$btn['activeBg']} text-white {$btn['activeBorder']} shadow-md"
                                    : 'bg-white border-slate-300 text-slate-700 hover:bg-slate-50' }}"
                        >
                            <span wire:loading.remove wire:target="updateStatus('{{ $statusKey }}')">{{ $btn['label'] }}</span>
                            <span wire:loading wire:target="updateStatus('{{ $statusKey }}')" class="inline-flex items-center gap-1">
                                <svg class="animate-spin h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                {{ $btn['label'] }}
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Sidebar Content -->
        <div class="space-y-6">
            
            <!-- Customer Card -->
            <div class="card-surface p-6">
                <div class="flex items-center gap-3 mb-4 border-b border-slate-100 pb-3">
                    <span class="material-symbols-outlined text-secondary">person</span>
                    <h3 class="font-headline-md text-slate-900 m-0">Customer Info</h3>
                </div>
                
                <div class="space-y-4 font-body-md text-slate-700">
                    <div>
                        <span class="block text-xs text-slate-500 font-medium uppercase tracking-wider mb-1">Name</span>
                        <div class="font-semibold text-slate-900">{{ $order->customer_name }}</div>
                    </div>
                    
                    @if($order->customer_phone)
                        <div>
                            <span class="block text-xs text-slate-500 font-medium uppercase tracking-wider mb-1">Phone</span>
                            <div class="flex items-center justify-between">
                                <span class="font-medium">{{ $order->customer_phone }}</span>
                                <a href="{{ $this->getWhatsappLink() }}" target="_blank" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-100 text-green-700 hover:bg-green-200 transition-colors" title="Chat on WhatsApp">
                                    <span class="material-symbols-outlined text-sm">chat</span>
                                </a>
                            </div>
                        </div>
                    @endif
                    
                    <div>
                        <span class="block text-xs text-slate-500 font-medium uppercase tracking-wider mb-1">Delivery</span>
                        <div class="capitalize font-medium">{{ $order->delivery_type }}</div>
                        @if($order->delivery_type === 'delivery' && $order->customer_address)
                            <div class="text-sm mt-1 bg-slate-50 p-2 rounded border border-slate-100">{{ $order->customer_address }}</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Financial Summary -->
            <div class="card-surface p-6 bg-slate-800 text-white">
                <div class="flex items-center justify-between mb-4 border-b border-slate-700 pb-3">
                    <h3 class="font-headline-md text-white m-0">Payment Summary</h3>
                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-bold {{ $order->payment_status === 'paid' ? 'bg-green-500/20 text-green-400 border border-green-500/30' : 'bg-red-500/20 text-red-400 border border-red-500/30' }}">
                        {{ strtoupper($order->payment_status) }}
                    </span>
                </div>
                
                <div class="space-y-3 mb-6 font-body-md text-slate-300">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span>Rp{{ number_format($order->subtotal, 0) }}</span>
                    </div>
                    @if($order->promo)
                        <div class="flex justify-between text-green-400">
                            <span>Promo ({{ $order->promo->name }})</span>
                            <span>-Rp{{ number_format($order->discount, 0) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-xl font-display text-white pt-3 border-t border-slate-700">
                        <span>Total</span>
                        <span>Rp{{ number_format($order->total, 0) }}</span>
                    </div>
                </div>
                
                @if($order->payment_status === 'unpaid')
                    <button wire:click="updatePaymentStatus('paid')" class="w-full bg-green-500 hover:bg-green-600 text-white py-3 rounded-lg font-bold shadow-lg transition-colors flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined">payments</span>
                        Mark as Paid
                    </button>
                @else
                    <button wire:click="updatePaymentStatus('unpaid')" class="w-full bg-slate-700 hover:bg-slate-600 text-white py-2 rounded-lg font-medium transition-colors border border-slate-600">
                        Mark as Unpaid
                    </button>
                @endif
            </div>

        </div>
    </div>
</div>
