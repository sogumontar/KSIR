<div x-data="{ 
    showAddModal: @entangle('showAddModal'), 
    showEditModal: @entangle('showEditModal'), 
    showViewModal: @entangle('showViewModal'), 
    showDeleteModal: @entangle('showDeleteModal'), 
    
    qty: @entangle('qty'),
    price: @entangle('price'),
    
    editQty: @entangle('editQty'),
    editPrice: @entangle('editPrice'),
    
    get addTotal() { return (this.qty || 0) * (this.price || 0); }, 
    get editTotal() { return (this.editQty || 0) * (this.editPrice || 0); }
}">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <h2 class="font-headline-lg text-headline-lg text-slate-900 m-0">Goods &amp; Recipients</h2>
        <div class="flex items-center gap-4">
            <!-- Status Filter -->
            <select wire:model.live="statusFilter" class="form-input bg-white appearance-none text-sm py-2 px-4 rounded-lg border border-slate-300">
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="transit">In Transit</option>
                <option value="delivered">Delivered</option>
                <option value="loan">On Loan</option>
                <option value="in_progress">In Progress</option>
                <option value="failed">Failed</option>
            </select>
            <button wire:click="openAdd" class="btn-primary gap-2">
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
        <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center gap-4">
            <button wire:click="selectAll" class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:opacity-90 transition-all">
                <span class="material-symbols-outlined text-sm">select_all</span>
                Select All
            </button>
            <button wire:click="clearSelection" class="inline-flex items-center gap-2 px-4 py-2 bg-white text-slate-700 rounded-lg text-sm font-medium border border-slate-300 hover:bg-slate-50 transition-all">
                <span class="material-symbols-outlined text-sm">deselect</span>
                Clear Selection
            </button>
            @if(count($selected) > 0)
            <span class="text-sm text-slate-600 font-medium">{{ count($selected) }} selected</span>
            @endif
            <div class="ml-auto">
                <button wire:click="exportSelected" class="inline-flex items-center gap-2 px-4 py-2 bg-secondary text-white rounded-lg text-sm font-medium hover:opacity-90 transition-all {{ count($selected) === 0 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ count($selected) === 0 ? 'disabled' : '' }}>
                    <span class="material-symbols-outlined text-sm">picture_as_pdf</span>
                    Export Selected (PDF)
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                <tr>
                    <th class="table-header font-label-lg text-label-lg w-12">
                        <input type="checkbox" wire:click="selectAll" class="form-checkbox rounded" />
                    </th>
                    <th class="table-header font-label-lg text-label-lg">Date</th>
                    <th class="table-header font-label-lg text-label-lg">Good/Item</th>
                    <th class="table-header font-label-lg text-label-lg">Recipient</th>
                    <th class="table-header font-label-lg text-label-lg text-right">Quantity</th>
                    <th class="table-header font-label-lg text-label-lg text-right">Unit Price</th>
                    <th class="table-header font-label-lg text-label-lg text-right">Total Value</th>
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
                    </td>
                    <td class="table-cell">{{ $tx->recipient_name }}</td>
                    <td class="table-cell text-right">{{ $tx->quantity }}</td>
                    <td class="table-cell text-right">${{ number_format($tx->price, 2) }}</td>
                    <td class="table-cell text-right font-semibold">${{ number_format($tx->total_price, 2) }}</td>
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
                    <td colspan="9" class="text-center p-8 text-slate-500">
                        <span class="material-symbols-outlined text-4xl text-slate-300 mb-2 block">inventory_2</span>
                        No transactions found.
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4 border-t border-slate-200 bg-slate-50 flex items-center justify-between text-slate-600 font-label-md text-label-md">
            <span>Showing {{ $transactions->firstItem() ?? 0 }} to {{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() }} records</span>
            <div class="flex gap-2">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>

    <!-- Modal: Add New Record -->
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4" x-show="showAddModal" style="display: none;">
        <!-- Overlay -->
        <div @click="showAddModal = false" class="fixed inset-0 modal-overlay" x-show="showAddModal" x-transition.opacity></div>
        <!-- Dialog -->
        <div class="relative bg-white w-full max-w-2xl rounded-xl modal-content flex flex-col max-h-[921px]" x-show="showAddModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:leave="transition ease-in duration-200" x-transition:leave-end="opacity-0 scale-95 translate-y-4" x-transition:leave-start="opacity-100 scale-100 translate-y-0">
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
                        <select wire:model.live="goodId" class="form-input bg-white appearance-none">
                            <option value="">-- Select Good --</option>
                            @foreach($goodsList as $good)
                                <option value="{{ $good->id }}">{{ $good->name }} (Stock: {{ $good->stock }} | Price: ${{ number_format($good->price, 2) }})</option>
                            @endforeach
                        </select>
                        <p class="text-sm text-slate-500 mt-2">Select the good from your inventory.</p>
                        @error('goodId') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <!-- Quantity -->
                    <div>
                        <label class="form-label">Quantity</label>
                        <input class="form-input" min="1" placeholder="e.g., 50" type="number" x-model.number="qty">
                        @error('qty') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <!-- Unit Price -->
                    <div>
                        <label class="form-label">Unit Price ($)</label>
                        <input class="form-input" min="0" placeholder="0.00" step="0.01" type="number" x-model.number="price" disabled>
                        <p class="text-xs text-slate-400 mt-1">Managed automatically via inventory.</p>
                    </div>
                </div>
                <!-- Calculated Total (Visual only) -->
                <div class="bg-slate-50 p-6 rounded-lg border border-slate-200 flex items-center justify-between">
                    <div>
                        <span class="block text-slate-500 font-label-md text-sm mb-1">Estimated Total Value</span>
                        <span class="font-body-md text-slate-700 text-sm">Quantity × Unit Price</span>
                    </div>
                    <div class="text-right">
                        <span class="font-display text-display text-slate-900 block" x-text="new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(addTotal)">$0.00</span>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Recipient -->
                    <div>
                        <label class="form-label">Recipient / Destination</label>
                        <input type="text" wire:model="recipientId" class="form-input bg-white" placeholder="e.g., Acme Corp HQ">
                        @error('recipientId') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <!-- Status -->
                    <div>
                        <label class="form-label">Initial Status</label>
                        <select class="form-input bg-white appearance-none" wire:model="status">
                            <option value="pending">Pending</option>
                            <option value="transit">In Transit</option>
                            <option value="delivered">Delivered</option>
                            <option value="loan">Loan</option>
                        </select>
                        @error('status') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <!-- Due Date (Conditional) -->
                    <div x-show="$wire.status === 'loan'" x-transition>
                        <label class="form-label">Due Date</label>
                        <input wire:model="dueDate" class="form-input" type="date">
                        @error('dueDate') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
                <!-- Proof of Delivery (Always Visible) -->
                <div class="col-span-1 md:col-span-2 mt-4 pt-6 border-t border-slate-200">
                    <label class="form-label">Proof of Delivery / Manifest</label>
                    <div class="border-2 border-dashed border-slate-300 rounded-lg p-8 text-center hover:bg-slate-50 transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-4xl text-slate-400 mb-2">upload_file</span>
                        <p class="font-label-md text-slate-700 mb-1">Click to upload or drag and drop</p>
                        <p class="text-sm text-slate-500">PDF, JPG, or PNG (max. 10MB)</p>
                        <input accept=".pdf,.jpg,.jpeg,.png" class="hidden" type="file">
                    </div>
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
        <div class="relative bg-white w-full max-w-2xl rounded-xl modal-content flex flex-col max-h-[921px]" x-show="showEditModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:leave="transition ease-in duration-200" x-transition:leave-end="opacity-0 scale-95 translate-y-4" x-transition:leave-start="opacity-100 scale-100 translate-y-0">
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
                        <input type="text" wire:model="editItemName" class="form-input bg-slate-50 text-slate-500" disabled>
                        <p class="text-sm text-slate-500 mt-2">Item cannot be changed once recorded.</p>
                    </div>
                    <!-- Quantity -->
                    <div>
                        <label class="form-label">Quantity</label>
                        <input class="form-input" min="1" placeholder="e.g., 50" type="number" x-model.number="editQty">
                        @error('editQty') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <!-- Unit Price -->
                    <div>
                        <label class="form-label">Unit Price ($)</label>
                        <input class="form-input bg-slate-50 text-slate-500" min="0" placeholder="0.00" step="0.01" type="number" x-model.number="editPrice" disabled>
                        <p class="text-xs text-slate-400 mt-1">Managed automatically via inventory.</p>
                    </div>
                </div>
                <!-- Calculated Total (Visual only) -->
                <div class="bg-slate-50 p-6 rounded-lg border border-slate-200 flex items-center justify-between">
                    <div>
                        <span class="block text-slate-500 font-label-md text-sm mb-1">Estimated Total Value</span>
                        <span class="font-body-md text-slate-700 text-sm">Quantity × Unit Price</span>
                    </div>
                    <div class="text-right">
                        <span class="font-display text-display text-slate-900 block" x-text="new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(editTotal)">$0.00</span>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Recipient (display only) -->
                    <div>
                        <label class="form-label">Recipient / Destination</label>
                        <input type="text" wire:model="editRecipientId" class="form-input bg-slate-50 text-slate-500" disabled>
                    </div>
                    <!-- Status -->
                    <div>
                        <label class="form-label">Status</label>
                        <select class="form-input bg-white appearance-none" wire:model="editStatus">
                            <option value="pending">Pending</option>
                            <option value="transit">In Transit</option>
                            <option value="delivered">Delivered</option>
                            <option value="loan">Loan</option>
                            <option value="failed">Failed</option>
                        </select>
                        @error('editStatus') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <!-- Due Date (Conditional) -->
                    <div x-show="$wire.editStatus === 'loan'" x-transition>
                        <label class="form-label">Due Date</label>
                        <input wire:model="editDueDate" class="form-input" type="date">
                        @error('editDueDate') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
                <!-- Proof of Delivery (Always Visible) -->
                <div class="col-span-1 md:col-span-2 mt-4 pt-6 border-t border-slate-200">
                    <label class="form-label">Proof of Delivery / Manifest</label>
                    <div class="border-2 border-dashed border-slate-300 rounded-lg p-8 text-center hover:bg-slate-50 transition-colors cursor-pointer">
                        <span class="material-symbols-outlined text-4xl text-slate-400 mb-2">upload_file</span>
                        <p class="font-label-md text-slate-700 mb-1">Click to upload or drag and drop</p>
                        <p class="text-sm text-slate-500">PDF, JPG, or PNG (max. 10MB)</p>
                        <input accept=".pdf,.jpg,.jpeg,.png" class="hidden" type="file">
                    </div>
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
        <div class="relative bg-white w-full max-w-2xl rounded-xl modal-content flex flex-col" x-show="showViewModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-end="opacity-100 scale-100" x-transition:enter-start="opacity-0 scale-95">
            <div class="p-6 border-b border-slate-200 flex items-center justify-between bg-slate-50 rounded-t-xl">
                <h3 class="font-headline-md text-headline-md text-slate-900 m-0">Transaction Details</h3>
                <button @click="showViewModal = false" class="text-slate-400 hover:text-slate-700 transition-colors p-2 rounded-full hover:bg-slate-200 flex items-center justify-center">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="p-8 space-y-4">
                <div class="grid grid-cols-2 gap-4 text-body-md">
                    <div class="font-semibold">Date:</div>
                    <div>{{ data_get($viewRecord, 'date') }}</div>
                    <div class="font-semibold">Item:</div>
                    <div>{{ data_get($viewRecord, 'item') }}</div>
                    <div class="font-semibold">Recipient:</div>
                    <div>{{ data_get($viewRecord, 'recipient') }}</div>
                    <div class="font-semibold">Quantity:</div>
                    <div>{{ data_get($viewRecord, 'qty') }}</div>
                    <div class="font-semibold">Unit Price:</div>
                    <div>${{ number_format((float) data_get($viewRecord, 'price', 0), 2) }}</div>
                    <div class="font-semibold">Total Value:</div>
                    <div class="font-bold">${{ number_format((float) data_get($viewRecord, 'total', 0), 2) }}</div>
                    <div class="font-semibold">Status:</div>
                    <div>{{ data_get($viewRecord, 'status') }}</div>
                    @if(data_get($viewRecord, 'dueDate'))
                        <div class="font-semibold">Due Date:</div>
                        <div>{{ data_get($viewRecord, 'dueDate') }}</div>
                    @endif
                    <div class="font-semibold">Proof of Delivery:</div>
                    <div>{{ data_get($viewRecord, 'proof') ?? 'Uploaded' }}</div>
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
