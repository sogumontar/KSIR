<div x-data="{
    showAddModal: @entangle('showAddModal'),
    showEditModal: @entangle('showEditModal'),
    showViewModal: @entangle('showViewModal'),
    showDeleteModal: @entangle('showDeleteModal'),

    qty: @entangle('qty'),
    price: @entangle('price'),
    sellPrice: @entangle('sellPrice'),

    editQty: @entangle('editQty'),
    editPrice: @entangle('editPrice'),
    editSellPrice: @entangle('editSellPrice'),

    get addTotal() { return (this.qty || 0) * (this.sellPrice || 0); },
    get addProfit() { return ((this.sellPrice || 0) - (this.price || 0)) * (this.qty || 0); },
    get editTotal() { return (this.editQty || 0) * (this.editSellPrice || 0); },
    get editProfit() { return ((this.editSellPrice || 0) - (this.editPrice || 0)) * (this.editQty || 0); }
}">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-md mb-8">
        <h2 class="font-headline-lg text-headline-lg text-slate-900 m-0">Sales Record</h2>
        <div class="flex items-center gap-md flex-col sm:flex-row flex-wrap">
            <!-- Status Filter -->
            <select wire:model.live="statusFilter" class="form-input bg-white appearance-none text-sm py-2.5 px-4 rounded-lg border border-slate-300 w-full sm:w-auto min-h-[42px]">
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="transit">In Transit</option>
                <option value="delivered">Delivered</option>
                <option value="loan">On Loan</option>
                <option value="in_progress">In Progress</option>
                <option value="failed">Failed</option>
            </select>
            <button wire:click="openAdd" class="btn-primary gap-2 w-full sm:w-auto justify-center">
                <span class="material-symbols-outlined">add</span>
                Add New Record
            </button>
        </div>
    </div>

    @if (session()->has('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg flex items-center gap-2">
            <span class="material-symbols-outlined text-red-600">error</span>
            <span class="text-sm font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Data Table Card -->
    <div class="card-surface overflow-hidden">
        <!-- Bulk Actions Bar -->
        <div class="p-4 bg-slate-50 border-b border-slate-200 flex flex-wrap items-center gap-2 sm:gap-4">
            @if(count($selected) > 0)
            <span class="text-sm text-slate-600 font-medium">{{ count($selected) }} selected</span>
            @endif
            <div class="sm:ml-auto w-full sm:w-auto">
                <button wire:click="exportSelected" class="inline-flex items-center gap-2 px-4 py-2 bg-secondary text-white rounded-lg text-sm font-medium hover:opacity-90 transition-all w-full sm:w-auto justify-center {{ count($selected) === 0 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ count($selected) === 0 ? 'disabled' : '' }}>
                    <span class="material-symbols-outlined text-sm">picture_as_pdf</span>
                    Export Selected (PDF)
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[700px]">
                <thead>
                <tr>
                    <th class="table-header font-label-lg text-label-lg w-12">
                        <input type="checkbox" @change="$el.checked ? $wire.selectAll() : $wire.clearSelection()" class="form-checkbox rounded" />
                    </th>
                    <th class="table-header font-label-lg text-label-lg">Date</th>
                    <th class="table-header font-label-lg text-label-lg">Good/Item</th>
                    <th class="table-header font-label-lg text-label-lg">Recipient</th>
                    <th class="table-header font-label-lg text-label-lg">Sales Type</th>
                    <th class="table-header font-label-lg text-label-lg text-right">Quantity</th>
                    <th class="table-header font-label-lg text-label-lg text-right">Cost Price</th>
                    <th class="table-header font-label-lg text-label-lg text-right">Sell Price</th>
                    <th class="table-header font-label-lg text-label-lg text-right">Total Value</th>
                    <th class="table-header font-label-lg text-label-lg text-right">Profit</th>
                    <th class="table-header font-label-lg text-label-lg text-center">Status</th>
                    <th class="table-header font-label-lg text-label-lg text-center">Action</th>
                </tr>
                </thead>
                <tbody class="font-body-md text-body-md text-slate-700">
                @forelse($transactions as $tx)
                <tr class="table-row-zebra border-b border-slate-200">
                    <td class="table-cell">
                        <input type="checkbox" wire:model.live="selected" value="{{ $tx->id }}" class="form-checkbox rounded" />
                    </td>
                    <td class="table-cell">{{ optional($tx->transaction_date)->format('M d, Y') ?? '-' }}</td>
                    <td class="table-cell font-semibold text-slate-900">
                        {{ $tx->item_name }}
                        @if($tx->good_id && $tx->good && $tx->good->trashed())
                            <span class="text-xs bg-red-100 text-red-800 px-2 py-0.5 rounded border border-red-200 ml-2 font-normal">Deleted Good</span>
                        @endif
                        @if($tx->good_id && $tx->good && !$tx->good->trashed() && $tx->good->unit_type)
                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded border border-blue-200 ml-2 font-normal">{{ ucfirst($tx->good->unit_type) }}</span>
                        @endif
                    </td>
                    <td class="table-cell">{{ $tx->recipient_name }}</td>
                    <td class="table-cell">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ ($tx->sales_type === 'online') ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                            {{ ucfirst($tx->sales_type ?? 'Offline') }}
                        </span>
                        @if($tx->sales_type === 'online' && $tx->sales_code)
                            <span class="block text-xs text-slate-500 mt-1">{{ $tx->sales_code }}</span>
                        @endif
                    </td>
                    <td class="table-cell text-right">
                        {{ $tx->quantity }}
                        @if($tx->good_id && $tx->good && !$tx->good->trashed() && $tx->good->unit_type)
                            <span class="text-xs text-slate-500 ml-1">{{ $tx->good->unit_type }}</span>
                        @endif
                    </td>
                    <td class="table-cell text-right">Rp{{ number_format($tx->price, 0) }}</td>
                    <td class="table-cell text-right">Rp{{ number_format($tx->sell_price ?? $tx->price, 0) }}</td>
                    <td class="table-cell text-right font-semibold">Rp{{ number_format($tx->total_price, 0) }}</td>
                    <td class="table-cell text-right font-semibold {{ ($tx->profit ?? 0) >= 0 ? 'text-green-700' : 'text-red-600' }}">Rp{{ number_format($tx->profit ?? 0, 0) }}</td>
                    <td class="table-cell text-center">
                        @php
                            $statusColors = [
                                'pending' => 'bg-amber-100 text-amber-800 border-amber-300',
                                'transit' => 'bg-blue-100 text-blue-800 border-blue-300',
                                'delivered' => 'bg-green-100 text-green-800 border-green-300',
                                'loan' => 'bg-purple-100 text-purple-800 border-purple-300',
                                'failed' => 'bg-red-100 text-red-800 border-red-300',
                            ];
                            $colorClass = $statusColors[$tx->status] ?? 'bg-slate-100 text-slate-800 border-slate-300';
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium {{ $colorClass }} border">{{ ucfirst($tx->status) }}</span>
                    </td>
                    <td class="table-cell text-center">
                        <div class="flex justify-center gap-2">
                            <button wire:click="openView({{ $tx->id }})" class="btn-icon" title="View">
                                <span class="material-symbols-outlined">visibility</span>
                            </button>

                            @if($tx->good_id && $tx->good && $tx->good->trashed())
                                <button class="btn-icon opacity-40 cursor-not-allowed text-slate-300" title="Cannot edit transaction with deleted good" disabled>
                                    <span class="material-symbols-outlined">edit</span>
                                </button>
                            @else
                                <button wire:click="openEdit({{ $tx->id }})" class="btn-icon" title="Edit">
                                    <span class="material-symbols-outlined">edit</span>
                                </button>
                            @endif

                            <button wire:click="confirmDelete({{ $tx->id }})" class="btn-icon text-error hover:bg-error-container hover:text-error" title="Delete">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center p-8 text-slate-500">
                        <span class="material-symbols-outlined text-4xl text-slate-300 mb-2 block">inventory_2</span>
                        No transactions found.
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4 border-t border-slate-200 bg-slate-50 flex flex-col sm:flex-row items-center justify-between text-slate-600 font-label-md text-label-md gap-sm">
            <span>Showing {{ $transactions->firstItem() ?? 0 }} to {{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() }} records</span>
            <div class="flex gap-2">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
    <br/>

<!-- Loan Summary Section -->
    @if(!empty($loanSummary))
    <div class="card-surface overflow-hidden mb-lg" x-data="{ expandedRecipient: null }">
        <div class="p-lg border-b border-outline-variant flex justify-between items-center">
            <div class="flex items-center gap-sm">
                <span class="material-symbols-outlined text-purple-700" data-icon="contract_edit">contract_edit</span>
                <h4 class="font-headline-md text-headline-md text-primary m-0">Loan Summary</h4>
            </div>
            <span class="bg-purple-100 text-purple-800 px-sm py-xs rounded-full font-label-md">{{ count($loanSummary) }} recipients with active loans</span>
        </div>
        <div class="divide-y divide-slate-200">
            @foreach($loanSummary as $recipient)
            <div class="border-b border-slate-200 last:border-b-0">
                <button @click="expandedRecipient === '{{ $recipient['name'] }}' ? expandedRecipient = null : expandedRecipient = '{{ $recipient['name'] }}'"
                    class="w-full p-md flex items-center justify-between hover:bg-slate-50 transition-colors cursor-pointer">
                    <div class="flex items-center gap-md">
                        <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                            <span class="text-purple-800 font-bold text-sm">{{ strtoupper(substr($recipient['name'], 0, 1)) }}</span>
                        </div>
                        <div>
                            <p class="font-label-lg text-left font-semibold">{{ $recipient['name'] }}</p>
                            <p class="text-sm text-on-surface-variant">{{ $recipient['loan_count'] }} loan(s) · Nearest due:
                                @if($recipient['nearest_due_date_is_overdue'])
                                    <span class="text-red-600 font-semibold">{{ $recipient['nearest_due_date'] }} (Overdue)</span>
                                @elseif($recipient['nearest_due_date'])
                                    <span>{{ $recipient['nearest_due_date'] }}</span>
                                @else
                                    <span class="text-slate-400">No due date</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-md">
                        <span class="text-lg font-semibold text-secondary">Rp{{ number_format($recipient['total_loan_amount'], 0) }}</span>
                        <span class="material-symbols-outlined text-on-surface-variant" x-text="expandedRecipient === '{{ $recipient['name'] }}' ? 'expand_less' : 'expand_more'">expand_more</span>
                    </div>
                </button>
                <div x-show="expandedRecipient === '{{ $recipient['name'] }}'" x-transition class="bg-slate-50 px-md pb-md">
                    <table class="w-full text-left border-collapse min-w-[400px]">
                        <thead>
                            <tr class="bg-purple-50">
                                <th class="px-md py-sm font-label-md text-purple-800">Item</th>
                                <th class="px-md py-sm font-label-md text-purple-800 text-right">Qty</th>
                                <th class="px-md py-sm font-label-md text-purple-800 text-right">Amount</th>
                                <th class="px-md py-sm font-label-md text-purple-800">Transaction Date</th>
                                <th class="px-md py-sm font-label-md text-purple-800">Due Date</th>
                                <th class="px-md py-sm font-label-md text-purple-800 text-center">View</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recipient['loans'] as $loan)
                            <tr class="border-b border-slate-200 last:border-b-0">
                                <td class="px-md py-sm font-body-md text-on-surface">{{ $loan['item_name'] }}</td>
                                <td class="px-md py-sm font-body-md text-on-surface text-right">{{ $loan['quantity'] }}</td>
                                <td class="px-md py-sm font-body-md font-semibold text-on-surface text-right">Rp{{ number_format($loan['total_price'], 0) }}</td>
                                <td class="px-md py-sm font-body-md text-on-surface-variant">{{ $loan['transaction_date'] }}</td>
                                <td class="px-md py-sm font-body-md">
                                    @if($loan['is_overdue'])
                                        <span class="text-red-600 font-semibold">{{ $loan['due_date'] ?? 'No date' }} ⚠️</span>
                                    @elseif($loan['due_date'])
                                        <span>{{ $loan['due_date'] }}</span>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-md py-sm text-center">
                                    <button wire:click="openView({{ $loan['id'] }})" class="material-symbols-outlined text-on-surface-variant hover:text-secondary p-xs rounded transition-colors">visibility</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif


    <!-- Modal: Add New Record -->
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4" x-show="showAddModal" style="display: none;">
        <!-- Overlay -->
        <div @click="showAddModal = false" class="fixed inset-0 modal-overlay" x-show="showAddModal" x-transition.opacity></div>
        <!-- Dialog -->
        <div class="relative bg-white w-full max-w-2xl rounded-xl modal-content flex flex-col max-h-[90vh]" x-show="showAddModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:leave="transition ease-in duration-200" x-transition:leave-end="opacity-0 scale-95 translate-y-4" x-transition:leave-start="opacity-100 scale-100 translate-y-0">
            <!-- Modal Header -->
            <div class="p-6 border-b border-slate-200 flex items-center justify-between bg-slate-50 rounded-t-xl">
                <h3 class="font-headline-md text-headline-md text-slate-900 m-0">Add New Record</h3>
                <button @click="showAddModal = false" class="text-slate-400 hover:text-slate-700 transition-colors p-2 rounded-full hover:bg-slate-200 flex items-center justify-center">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <!-- Modal Body (Scrollable) -->
            <div class="p-8 overflow-y-auto font-body-md text-body-md space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Select Good -->
                    <div class="col-span-1 md:col-span-2">
                        <label class="form-label">Good / Item</label>
                        <select wire:model.live="goodId" class="form-input bg-white appearance-none w-full">
                            <option value="">-- Select Good --</option>
                            @foreach($goodsList as $good)
                                <option value="{{ $good->id }}">{{ $good->name }} (Stock: {{ $good->stock }} {{ $good->unit_type ?? 'pcs' }} | Cost: Rp{{ number_format($good->price, 0) }})</option>
                            @endforeach
                        </select>
                        <p class="text-sm text-slate-500 mt-2">Select the good from your inventory.</p>
                        @error('goodId') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <!-- Transaction Date -->
                    <div>
                        <label class="form-label">Transaction Date <span class="text-error">*</span></label>
                        <input wire:model="transactionDate" class="form-input bg-white w-full" type="date">
                        @error('transactionDate') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <!-- Quantity -->
                    <div>
                        <label class="form-label">Quantity</label>
                        <input class="form-input w-full" min="1" placeholder="e.g., 50" type="number" x-model.number="qty">
                        @error('qty') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <!-- Cost Price -->
                    <div>
                        <label class="form-label">Cost Price (Rp)</label>
                        <input class="form-input w-full" min="0" placeholder="0.00" step="0.01" type="number" x-model.number="price" disabled>
                        <p class="text-xs text-slate-400 mt-1">Managed automatically via inventory.</p>
                    </div>
                    <!-- Sell Price -->
                    <div>
                        <label class="form-label">Sell Price (Rp) <span class="text-error">*</span></label>
                        <input class="form-input w-full" min="0" placeholder="0.00" step="0.01" type="number" x-model.number="sellPrice">
                        <p class="text-xs text-slate-400 mt-1">The price you sell to the customer.</p>
                        @error('sellPrice') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
                <!-- Calculated Total & Profit -->
                <div class="bg-slate-50 p-6 rounded-lg border border-slate-200 flex items-center justify-between">
                    <div>
                        <span class="block text-slate-500 font-label-md text-sm mb-1">Total Value (Qty × Sell Price)</span>
                        <span class="font-body-md text-slate-700 text-sm">Profit: (Sell - Cost) × Qty</span>
                    </div>
                    <div class="text-right space-y-1">
                        <span class="font-display text-display text-slate-900 block" x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(addTotal)">Rp0</span>
                        <span class="font-headline-md text-headline-md block" :class="addProfit >= 0 ? 'text-green-700' : 'text-red-600'" x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(addProfit)">Rp0</span>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Recipient -->
                    <div>
                        <label class="form-label">Recipient / Destination</label>
                        <input type="text" wire:model="recipientId" class="form-input bg-white w-full" placeholder="e.g., Acme Corp HQ">
                        @error('recipientId') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <!-- Status -->
                    <div>
                        <label class="form-label">Initial Status</label>
                        <select class="form-input bg-white appearance-none w-full" wire:model="status">
                            <option value="pending">Pending</option>
                            <option value="transit">In Transit</option>
                            <option value="delivered">Delivered</option>
                            <option value="loan">Loan</option>
                        </select>
                        @error('status') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <!-- Sales Type -->
                    <div>
                        <label class="form-label">Sales Type <span class="text-error">*</span></label>
                        <select class="form-input bg-white appearance-none w-full" wire:model.live="salesType">
                            <option value="offline">Offline</option>
                            <option value="online">Online</option>
                        </select>
                        @error('salesType') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <!-- Sales Code (Conditional) -->
                    <div x-show="$wire.salesType === 'online'" x-transition>
                        <label class="form-label">Sales Code <span class="text-error">*</span></label>
                        <input type="text" wire:model="salesCode" class="form-input w-full" placeholder="e.g., INV-OL-001">
                        @error('salesCode') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <!-- Due Date (Conditional) -->
                    <div x-show="$wire.status === 'loan'" x-transition>
                        <label class="form-label">Due Date</label>
                        <input wire:model="dueDate" class="form-input w-full" type="date">
                        @error('dueDate') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
                <!-- Proof of Delivery (Always Visible) -->
                <div class="col-span-1 md:col-span-2 mt-4 pt-6 border-t border-slate-200">
                    <label class="form-label">
                        Proof of Delivery / Manifest
                        <span x-show="$wire.status === 'delivered' || $wire.status === 'loan'" class="text-error">*</span>
                    </label>
                    @if($proofFile)
                        <div class="flex items-center gap-3 p-3 bg-green-50 border border-green-200 rounded-lg mb-3">
                            <span class="material-symbols-outlined text-green-600">check_circle</span>
                            <span class="text-sm text-green-800 font-medium">{{ $proofFile->getClientOriginalName() }}</span>
                            <span class="text-xs text-green-600">{{ number_format($proofFile->getSize() / 1024, 1) }} KB</span>
                            <button wire:click="$set('proofFile', null)" class="text-red-500 hover:text-red-700 ml-auto">
                                <span class="material-symbols-outlined text-sm">close</span>
                            </button>
                        </div>
                    @endif
                    <div x-show="!$wire.proofFile" class="border-2 border-dashed border-slate-300 rounded-lg p-8 text-center hover:bg-slate-50 transition-colors cursor-pointer"
                         onclick="this.querySelector('input[type=file]').click()">
                        <span class="material-symbols-outlined text-4xl text-slate-400 mb-2">upload_file</span>
                        <p class="font-label-md text-slate-700 mb-1">Click to upload or drag and drop</p>
                        <p class="text-sm text-slate-500">PDF, JPG, or PNG (max. 10MB)</p>
                        <p x-show="$wire.status === 'delivered' || $wire.status === 'loan'" class="text-xs text-red-500 mt-1">Required for delivered and loan status</p>
                        <input accept=".pdf,.jpg,.jpeg,.png" class="hidden" type="file" wire:model="proofFile">
                    </div>
                    @error('proofFile') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
                </div>
            </div>
            <!-- Modal Footer -->
            <div class="p-6 border-t border-slate-200 flex justify-end gap-4 bg-slate-50 rounded-b-xl">
                <button @click="showAddModal = false" class="btn-ghost">Cancel</button>
                <button wire:click="saveRecord" class="btn-primary gap-2">
                    <span class="material-symbols-outlined">save</span>
                    Save Record
                </button>
            </div>
        </div>
    </div>

    <!-- Modal: Update Record -->
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4" style="display: none;" x-show="showEditModal">
        <!-- Overlay -->
        <div @click="showEditModal = false" class="fixed inset-0 modal-overlay" x-show="showEditModal" x-transition.opacity></div>
        <!-- Dialog -->
        <div class="relative bg-white w-full max-w-2xl rounded-xl modal-content flex flex-col max-h-[90vh]" x-show="showEditModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:leave="transition ease-in duration-200" x-transition:leave-end="opacity-0 scale-95 translate-y-4" x-transition:leave-start="opacity-100 scale-100 translate-y-0">
            <!-- Modal Header -->
            <div class="p-6 border-b border-slate-200 flex items-center justify-between bg-slate-50 rounded-t-xl">
                <h3 class="font-headline-md text-headline-md text-slate-900 m-0">Update Record</h3>
                <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-700 transition-colors p-2 rounded-full hover:bg-slate-200 flex items-center justify-center">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <!-- Modal Body (Scrollable) -->
            <div class="p-8 overflow-y-auto font-body-md text-body-md space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Select Good (Disabled for Edit) -->
                    <div class="col-span-1 md:col-span-2">
                        <label class="form-label">Good / Item</label>
                        <input type="text" wire:model="editItemName" class="form-input bg-slate-50 text-slate-500 w-full" disabled>
                        <p class="text-sm text-slate-500 mt-2">Item cannot be changed once recorded.</p>
                    </div>
                    <!-- Transaction Date -->
                    <div>
                        <label class="form-label">Transaction Date <span class="text-error">*</span></label>
                        <input wire:model="editTransactionDate" class="form-input bg-white w-full" type="date">
                        @error('editTransactionDate') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <!-- Quantity -->
                    <div>
                        <label class="form-label">Quantity</label>
                        <input class="form-input w-full" min="1" placeholder="e.g., 50" type="number" x-model.number="editQty">
                        @error('editQty') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <!-- Cost Price -->
                    <div>
                        <label class="form-label">Cost Price (Rp)</label>
                        <input class="form-input bg-slate-50 text-slate-500 w-full" min="0" placeholder="0.00" step="0.01" type="number" x-model.number="editPrice" disabled>
                        <p class="text-xs text-slate-400 mt-1">Managed automatically via inventory.</p>
                    </div>
                    <!-- Sell Price -->
                    <div>
                        <label class="form-label">Sell Price (Rp) <span class="text-error">*</span></label>
                        <input class="form-input w-full" min="0" placeholder="0.00" step="0.01" type="number" x-model.number="editSellPrice">
                        <p class="text-xs text-slate-400 mt-1">The price you sell to the customer.</p>
                        @error('editSellPrice') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
                <!-- Calculated Total & Profit -->
                <div class="bg-slate-50 p-6 rounded-lg border border-slate-200 flex items-center justify-between">
                    <div>
                        <span class="block text-slate-500 font-label-md text-sm mb-1">Total Value (Qty × Sell Price)</span>
                        <span class="font-body-md text-slate-700 text-sm">Profit: (Sell - Cost) × Qty</span>
                    </div>
                    <div class="text-right space-y-1">
                        <span class="font-display text-display text-slate-900 block" x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(editTotal)">Rp0</span>
                        <span class="font-headline-md text-headline-md block" :class="editProfit >= 0 ? 'text-green-700' : 'text-red-600'" x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(editProfit)">Rp0</span>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Recipient (display only) -->
                    <div>
                        <label class="form-label">Recipient / Destination</label>
                        <input type="text" wire:model="editRecipientId" class="form-input bg-slate-50 text-slate-500 w-full" disabled>
                    </div>
                    <!-- Status -->
                    <div>
                        <label class="form-label">Status</label>
                        <select class="form-input bg-white appearance-none w-full" wire:model="editStatus">
                            <option value="pending">Pending</option>
                            <option value="transit">In Transit</option>
                            <option value="delivered">Delivered</option>
                            <option value="loan">Loan</option>
                            <option value="failed">Failed</option>
                        </select>
                        @error('editStatus') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <!-- Sales Type -->
                    <div>
                        <label class="form-label">Sales Type <span class="text-error">*</span></label>
                        <select class="form-input bg-white appearance-none w-full" wire:model.live="editSalesType">
                            <option value="offline">Offline</option>
                            <option value="online">Online</option>
                        </select>
                        @error('editSalesType') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <!-- Sales Code (Conditional) -->
                    <div x-show="$wire.editSalesType === 'online'" x-transition>
                        <label class="form-label">Sales Code <span class="text-error">*</span></label>
                        <input type="text" wire:model="editSalesCode" class="form-input w-full" placeholder="e.g., INV-OL-001">
                        @error('editSalesCode') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <!-- Due Date (Conditional) -->
                    <div x-show="$wire.editStatus === 'loan'" x-transition>
                        <label class="form-label">Due Date</label>
                        <input wire:model="editDueDate" class="form-input w-full" type="date">
                        @error('editDueDate') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
                <!-- Proof of Delivery (Always Visible) -->
                <div class="col-span-1 md:col-span-2 mt-4 pt-6 border-t border-slate-200">
                    <label class="form-label">
                        Proof of Delivery / Manifest
                        <span x-show="$wire.editStatus === 'delivered' || $wire.editStatus === 'loan' && !$wire.existingProof" class="text-error">*</span>
                    </label>
                    @if($existingProof)
                        <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg mb-3">
                            <span class="material-symbols-outlined text-blue-600">description</span>
                            <span class="text-sm text-blue-800 font-medium">Current proof uploaded</span>
                            <a href="{{ asset('storage/' . $existingProof) }}" target="_blank" class="text-sm text-blue-600 underline ml-2">View file</a>
                            <button wire:click="$set('existingProof', null)" class="text-red-500 hover:text-red-700 ml-auto" title="Remove existing proof">
                                <span class="material-symbols-outlined text-sm">close</span>
                            </button>
                        </div>
                    @endif
                    @if($editProofFile)
                        <div class="flex items-center gap-3 p-3 bg-green-50 border border-green-200 rounded-lg mb-3">
                            <span class="material-symbols-outlined text-green-600">check_circle</span>
                            <span class="text-sm text-green-800 font-medium">{{ $editProofFile->getClientOriginalName() }}</span>
                            <span class="text-xs text-green-600">{{ number_format($editProofFile->getSize() / 1024, 1) }} KB</span>
                            <button wire:click="$set('editProofFile', null)" class="text-red-500 hover:text-red-700 ml-auto">
                                <span class="material-symbols-outlined text-sm">close</span>
                            </button>
                        </div>
                    @endif
                    <div x-show="!$wire.editProofFile && !$wire.existingProof" class="border-2 border-dashed border-slate-300 rounded-lg p-8 text-center hover:bg-slate-50 transition-colors cursor-pointer"
                         onclick="this.querySelector('input[type=file]').click()">
                        <span class="material-symbols-outlined text-4xl text-slate-400 mb-2">upload_file</span>
                        <p class="font-label-md text-slate-700 mb-1">Click to upload or drag and drop</p>
                        <p class="text-sm text-slate-500">PDF, JPG, or PNG (max. 10MB)</p>
                        <p x-show="$wire.editStatus === 'delivered' || $wire.editStatus === 'loan'" class="text-xs text-red-500 mt-1">Required for delivered and loan status</p>
                        <input accept=".pdf,.jpg,.jpeg,.png" class="hidden" type="file" wire:model="editProofFile">
                    </div>
                    @error('editProofFile') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
                </div>
            </div>
            <!-- Modal Footer -->
            <div class="p-6 border-t border-slate-200 flex justify-end gap-4 bg-slate-50 rounded-b-xl">
                <button @click="showEditModal = false" class="btn-ghost">Cancel</button>
                <button wire:click="updateRecord" class="btn-primary gap-2">
                    <span class="material-symbols-outlined">save</span>
                    Update Record
                </button>
            </div>
        </div>
    </div>

    <!-- Modal: View Record Details -->
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4" x-show="showViewModal" style="display: none;">
        <!-- Overlay -->
        <div @click="showViewModal = false" class="fixed inset-0 modal-overlay" x-show="showViewModal" x-transition.opacity></div>
        <!-- Dialog -->
        <div class="relative bg-white w-full max-w-2xl rounded-xl modal-content flex flex-col max-h-[90vh]" x-show="showViewModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-end="opacity-100 scale-100" x-transition:enter-start="opacity-0 scale-95">
            <div class="p-6 border-b border-slate-200 flex items-center justify-between bg-slate-50 rounded-t-xl">
                <h3 class="font-headline-md text-headline-md text-slate-900 m-0">Transaction Details</h3>
                <button @click="showViewModal = false" class="text-slate-400 hover:text-slate-700 transition-colors p-2 rounded-full hover:bg-slate-200 flex items-center justify-center">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="p-8 space-y-4 overflow-y-auto custom-scrollbar">
                <div class="grid grid-cols-2 gap-4 text-body-md">
                    <div class="font-semibold">Date:</div>
                    <div>{{ data_get($viewRecord, 'date') }}</div>
                    <div class="font-semibold">Item:</div>
                    <div>{{ data_get($viewRecord, 'item') }}</div>
                    <div class="font-semibold">Unit Type:</div>
                    <div>{{ data_get($viewRecord, 'unitType') }}</div>
                    <div class="font-semibold">Recipient:</div>
                    <div>{{ data_get($viewRecord, 'recipient') }}</div>
                    <div class="font-semibold">Sales Type:</div>
                    <div>{{ data_get($viewRecord, 'salesType') }}</div>
                    <div class="font-semibold">Sales Code:</div>
                    <div>{{ data_get($viewRecord, 'salesCode') }}</div>
                    <div class="font-semibold">Quantity:</div>
                    <div>{{ data_get($viewRecord, 'qty') }}</div>
                    <div class="font-semibold">Cost Price:</div>
                    <div>Rp{{ number_format((float) data_get($viewRecord, 'price', 0), 0) }}</div>
                    <div class="font-semibold">Sell Price:</div>
                    <div>Rp{{ number_format((float) data_get($viewRecord, 'sellPrice', 0), 0) }}</div>
                    <div class="font-semibold">Total Value:</div>
                    <div class="font-bold">Rp{{ number_format((float) data_get($viewRecord, 'total', 0), 0) }}</div>
                    <div class="font-semibold">Profit:</div>
                    <div class="font-bold {{ data_get($viewRecord, 'profit') >= 0 ? 'text-green-700' : 'text-red-600' }}">Rp{{ number_format((float) data_get($viewRecord, 'profit', 0), 0) }}</div>
                    <div class="font-semibold">Status:</div>
                    <div>{{ data_get($viewRecord, 'status') }}</div>
                    @if(data_get($viewRecord, 'dueDate'))
                        <div class="font-semibold">Due Date:</div>
                        <div>{{ data_get($viewRecord, 'dueDate') }}</div>
                    @endif
                    <div class="font-semibold">Proof of Delivery:</div>
                    <div>
                        @if(data_get($viewRecord, 'proof'))
                            <a href="{{ asset('storage/' . data_get($viewRecord, 'proof')) }}" target="_blank" class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg border border-blue-200 hover:bg-blue-100 transition-colors">
                                <span class="material-symbols-outlined text-sm">description</span>
                                <span class="text-sm font-medium">View / Download</span>
                            </a>
                        @else
                            <span class="text-slate-400 text-sm">No proof uploaded</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="p-6 border-t border-slate-200 flex justify-end bg-slate-50 rounded-b-xl">
                <button @click="showViewModal = false" class="btn-primary">Close</button>
            </div>
        </div>
    </div>

    <!-- Modal: Delete Record -->
    <div class="fixed inset-0 z-[110] flex items-center justify-center p-4" x-show="showDeleteModal" style="display: none;">
        <!-- Overlay -->
        <div @click="showDeleteModal = false" class="fixed inset-0 modal-overlay" x-show="showDeleteModal" x-transition.opacity></div>
        <!-- Dialog -->
        <div class="relative bg-white w-full max-w-md rounded-xl modal-content p-8 text-center" x-show="showDeleteModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-end="opacity-100 scale-100" x-transition:enter-start="opacity-0 scale-95">
            <div class="mb-4 inline-flex items-center justify-center w-12 h-12 rounded-full bg-error-container text-error">
                <span class="material-symbols-outlined">warning</span>
            </div>
            <h3 class="font-headline-md text-headline-md text-slate-900 mb-4">Confirm Deletion</h3>
            <p class="text-slate-600 mb-8">Are you sure you want to delete this record? This action cannot be undone.</p>
            <div class="flex justify-center gap-4">
                <button @click="showDeleteModal = false" class="btn-ghost">Cancel</button>
                <button wire:click="deleteRecord" class="bg-error text-white font-semibold py-2 px-6 rounded hover:bg-red-700 transition-colors">Delete</button>
            </div>
        </div>
    </div>
</div>
