<div x-data="{
    showAddModal: @entangle('showAddModal'),
    showEditModal: @entangle('showEditModal'),
    showViewModal: @entangle('showViewModal'),
    showDeleteModal: @entangle('showDeleteModal')
}">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-8">
        <div>
            <h2 class="font-headline-lg text-headline-lg text-slate-900 m-0">Goods Inventory</h2>
            <p class="text-sm text-slate-500 mt-1">Manage physical goods, prices, and stock levels.</p>
        </div>
        <div class="flex items-center gap-4 flex-wrap">
            <div class="relative w-full sm:w-64">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">search</span>
                <input wire:model.live.debounce.300ms="search" class="w-full pl-9 pr-4 py-2 bg-white border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-secondary focus:border-transparent transition-all" placeholder="Search goods...">
            </div>
            <button wire:click="openAdd" class="btn-primary gap-2">
                <span class="material-symbols-outlined">add</span>
                Add New Good
            </button>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-center gap-2">
            <span class="material-symbols-outlined text-green-600">check_circle</span>
            <span class="text-sm font-medium">{{ session('message') }}</span>
        </div>
    @endif

    <!-- Data Table Card -->
    <div class="card-surface overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[640px]">
                <thead>
                    <tr>
                        <th class="table-header font-label-lg text-label-lg">Image</th>
                        <th class="table-header font-label-lg text-label-lg">Name</th>
                        <th class="table-header font-label-lg text-label-lg">Description</th>
                        <th class="table-header font-label-lg text-label-lg text-right">Unit Price</th>
                        <th class="table-header font-label-lg text-label-lg">Unit Type</th>
                        <th class="table-header font-label-lg text-label-lg text-right">Stock Level</th>
                        <th class="table-header font-label-lg text-label-lg text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="font-body-md text-body-md text-slate-700">
                    @forelse($goods as $good)
                        <tr class="table-row-zebra border-b border-slate-200">
                            <td class="table-cell">
                                @if($good->image)
                                    <img src="{{ asset('storage/' . $good->image) }}" alt="{{ $good->name }}" class="w-10 h-10 rounded object-cover border border-slate-200">
                                @else
                                    <div class="w-10 h-10 rounded bg-slate-100 border border-slate-200 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-slate-400 text-lg">image</span>
                                    </div>
                                @endif
                            </td>
                            <td class="table-cell font-semibold text-slate-900">{{ $good->name }}</td>
                            <td class="table-cell max-w-xs truncate">{{ $good->description ?? '-' }}</td>
                            <td class="table-cell text-right">Rp{{ number_format($good->price, 0) }}</td>
                            <td class="table-cell">
                                @if($good->unit_type)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">{{ ucfirst($good->unit_type) }}</span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="table-cell text-right">
                                @if($good->stock == 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800 border border-red-200">Out of Stock</span>
                                @elseif($good->stock < 10)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 border border-amber-200">{{ $good->stock }} (Low)</span>
                                @else
                                    <span class="font-semibold text-slate-900">{{ $good->stock }}</span>
                                @endif
                            </td>
                            <td class="table-cell text-center">
                                <div class="flex justify-center gap-2">
                                    <button wire:click="openView({{ $good->id }})" class="btn-icon" title="View Details">
                                        <span class="material-symbols-outlined">visibility</span>
                                    </button>
                                    <button wire:click="openEdit({{ $good->id }})" class="btn-icon" title="Edit Good">
                                        <span class="material-symbols-outlined">edit</span>
                                    </button>
                                    <button wire:click="confirmDelete({{ $good->id }})" class="btn-icon text-error hover:bg-error-container hover:text-error" title="Delete (Soft Delete)">
                                        <span class="material-symbols-outlined">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center p-12 text-slate-500">
                                <span class="material-symbols-outlined text-4xl text-slate-300 mb-2 block">layers</span>
                                No inventory items found. Add some goods to get started!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4 border-t border-slate-200 bg-slate-50 flex flex-col sm:flex-row items-center justify-between text-slate-600 font-label-md text-label-md gap-sm">
            <span>Showing {{ $goods->firstItem() ?? 0 }} to {{ $goods->lastItem() ?? 0 }} of {{ $goods->total() }} goods</span>
            <div class="flex gap-2">
                {{ $goods->links() }}
            </div>
        </div>
    </div>

    <!-- Modal: Add New Good -->
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4" x-show="showAddModal" style="display: none;">
        <!-- Overlay -->
        <div @click="showAddModal = false" class="fixed inset-0 modal-overlay" x-show="showAddModal" x-transition.opacity></div>
        <!-- Dialog -->
        <div class="relative bg-white w-full max-w-lg rounded-xl modal-content flex flex-col max-h-[90vh]" x-show="showAddModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-end="opacity-100 scale-100" x-transition:enter-start="opacity-0 scale-95">
            <!-- Modal Header -->
            <div class="p-6 border-b border-slate-200 flex items-center justify-between bg-slate-50 rounded-t-xl">
                <h3 class="font-headline-md text-headline-md text-slate-900 m-0">Add New Good</h3>
                <button @click="showAddModal = false" class="text-slate-400 hover:text-slate-700 transition-colors p-2 rounded-full hover:bg-slate-200 flex items-center justify-center">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <!-- Modal Body -->
            <div class="p-8 overflow-y-auto font-body-md text-body-md space-y-6">
                <!-- Name -->
                <div>
                    <label class="form-label">Item Name</label>
                    <input type="text" wire:model="name" class="form-input bg-white" placeholder="e.g., MacBook Pro M3 Max">
                    @error('name') <span class="text-error text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Price -->
                    <div>
                        <label class="form-label">Price (Rp)</label>
                        <input type="number" step="0.01" min="0" wire:model="price" class="form-input bg-white" placeholder="0.00">
                        @error('price') <span class="text-error text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <!-- Stock -->
                    <div>
                        <label class="form-label">Initial Stock</label>
                        <input type="number" min="0" wire:model="stock" class="form-input bg-white" placeholder="0">
                        @error('stock') <span class="text-error text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Unit Type -->
                <div>
                    <label class="form-label">Unit Type</label>
                    <select wire:model="unitType" class="form-input bg-white appearance-none w-full">
                        <option value="">-- Select Unit Type --</option>
                        <option value="pcs">Pieces (pcs)</option>
                        <option value="box">Box</option>
                        <option value="pack">Pack</option>
                        <option value="set">Set</option>
                        <option value="kg">Kilogram (kg)</option>
                        <option value="kg">Ons</option>
                        <option value="kg">Gram</option>
                        <option value="liter">Liter</option>
                        <option value="bundle">Bundle</option>
                        <option value="roll">Roll</option>
                        <option value="drum">Drum</option>
                        <option value="unit">Unit</option>
                        <option value="unit">Renceng</option>
                        <option value="unit">Bal</option>
                        <option value="unit">Krat</option>
                        <option value="unit">Slop</option>
                        <option value="unit">Lusin</option>
                        <option value="unit">Kardus</option>
                    </select>
                    @error('unitType') <span class="text-error text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Description -->
                <div>
                    <label class="form-label">Description</label>
                    <textarea wire:model="description" class="form-input bg-white min-h-[100px] py-2" placeholder="Optional product description..."></textarea>
                    @error('description') <span class="text-error text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Image -->
                <div>
                    <label class="form-label">Product Image</label>
                    @if($imageFile)
                        <div class="flex items-center gap-3 p-3 bg-green-50 border border-green-200 rounded-lg mb-3">
                            <img src="{{ $imageFile->temporaryUrl() }}" alt="Preview" class="w-16 h-16 rounded object-cover border border-green-300">
                            <div class="flex-1">
                                <span class="text-sm text-green-800 font-medium">{{ $imageFile->getClientOriginalName() }}</span>
                                <span class="text-xs text-green-600 ml-2">{{ number_format($imageFile->getSize() / 1024, 1) }} KB</span>
                            </div>
                            <button wire:click="$set('imageFile', null)" class="text-red-500 hover:text-red-700">
                                <span class="material-symbols-outlined text-sm">close</span>
                            </button>
                        </div>
                    @endif
                    <div x-show="!$wire.imageFile" class="border-2 border-dashed border-slate-300 rounded-lg p-6 text-center hover:bg-slate-50 transition-colors cursor-pointer"
                         onclick="this.querySelector('input[type=file]').click()">
                        <span class="material-symbols-outlined text-3xl text-slate-400 mb-1">add_photo_alternate</span>
                        <p class="font-label-md text-slate-700 mb-1">Click to upload image</p>
                        <p class="text-sm text-slate-500">JPG, PNG, or WebP (max. 2MB)</p>
                        <input accept=".jpg,.jpeg,.png,.webp" class="hidden" type="file" wire:model="imageFile">
                    </div>
                    @error('imageFile') <span class="text-error text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
            <!-- Modal Footer -->
            <div class="p-6 border-t border-slate-200 flex justify-end gap-4 bg-slate-50 rounded-b-xl">
                <button @click="showAddModal = false" class="btn-ghost">Cancel</button>
                <button wire:click="saveRecord" class="btn-primary gap-2">
                    <span class="material-symbols-outlined">save</span>
                    Save Good
                </button>
            </div>
        </div>
    </div>

    <!-- Modal: Edit Good -->
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4" x-show="showEditModal" style="display: none;">
        <!-- Overlay -->
        <div @click="showEditModal = false" class="fixed inset-0 modal-overlay" x-show="showEditModal" x-transition.opacity></div>
        <!-- Dialog -->
        <div class="relative bg-white w-full max-w-lg rounded-xl modal-content flex flex-col max-h-[90vh]" x-show="showEditModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-end="opacity-100 scale-100" x-transition:enter-start="opacity-0 scale-95">
            <!-- Modal Header -->
            <div class="p-6 border-b border-slate-200 flex items-center justify-between bg-slate-50 rounded-t-xl">
                <h3 class="font-headline-md text-headline-md text-slate-900 m-0">Edit Good</h3>
                <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-700 transition-colors p-2 rounded-full hover:bg-slate-200 flex items-center justify-center">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <!-- Modal Body -->
            <div class="p-8 overflow-y-auto font-body-md text-body-md space-y-6">
                <!-- Name -->
                <div>
                    <label class="form-label">Item Name</label>
                    <input type="text" wire:model="editName" class="form-input bg-white" placeholder="e.g., MacBook Pro M3 Max">
                    @error('editName') <span class="text-error text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Price -->
                    <div>
                        <label class="form-label">Price (Rp)</label>
                        <input type="number" step="0.01" min="0" wire:model="editPrice" class="form-input bg-white" placeholder="0.00">
                        @error('editPrice') <span class="text-error text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <!-- Stock -->
                    <div>
                        <label class="form-label">Stock Level</label>
                        <input type="number" min="0" wire:model="editStock" class="form-input bg-white" placeholder="0">
                        @error('editStock') <span class="text-error text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Unit Type -->
                <div>
                    <label class="form-label">Unit Type</label>
                    <select wire:model="editUnitType" class="form-input bg-white appearance-none w-full">
                        <option value="">-- Select Unit Type --</option>
                        <option value="pcs">Pieces (pcs)</option>
                        <option value="box">Box</option>
                        <option value="pack">Pack</option>
                        <option value="set">Set</option>
                        <option value="kg">Kilogram (kg)</option>
                        <option value="kg">Ons</option>
                        <option value="kg">Gram</option>
                        <option value="liter">Liter</option>
                        <option value="bundle">Bundle</option>
                        <option value="roll">Roll</option>
                        <option value="drum">Drum</option>
                        <option value="unit">Unit</option>
                        <option value="unit">Renceng</option>
                        <option value="unit">Bal</option>
                        <option value="unit">Krat</option>
                        <option value="unit">Slop</option>
                       <option value="unit">Lusin</option>
                       <option value="unit">Kardus</option>
                    </select>
                    @error('editUnitType') <span class="text-error text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Description -->
                <div>
                    <label class="form-label">Description</label>
                    <textarea wire:model="editDescription" class="form-input bg-white min-h-[100px] py-2" placeholder="Optional product description..."></textarea>
                    @error('editDescription') <span class="text-error text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Image -->
                <div>
                    <label class="form-label">Product Image</label>
                    @if($existingImage)
                        <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg mb-3">
                            <img src="{{ asset('storage/' . $existingImage) }}" alt="Current image" class="w-16 h-16 rounded object-cover border border-blue-300">
                            <div class="flex-1">
                                <span class="text-sm text-blue-800 font-medium">Current image</span>
                            </div>
                            <button wire:click="$set('existingImage', null)" class="text-red-500 hover:text-red-700" title="Remove current image">
                                <span class="material-symbols-outlined text-sm">close</span>
                            </button>
                        </div>
                    @endif
                    @if($editImageFile)
                        <div class="flex items-center gap-3 p-3 bg-green-50 border border-green-200 rounded-lg mb-3">
                            <img src="{{ $editImageFile->temporaryUrl() }}" alt="New preview" class="w-16 h-16 rounded object-cover border border-green-300">
                            <div class="flex-1">
                                <span class="text-sm text-green-800 font-medium">{{ $editImageFile->getClientOriginalName() }}</span>
                                <span class="text-xs text-green-600 ml-2">{{ number_format($editImageFile->getSize() / 1024, 1) }} KB</span>
                            </div>
                            <button wire:click="$set('editImageFile', null)" class="text-red-500 hover:text-red-700">
                                <span class="material-symbols-outlined text-sm">close</span>
                            </button>
                        </div>
                    @endif
                    <div x-show="!$wire.editImageFile && !$wire.existingImage" class="border-2 border-dashed border-slate-300 rounded-lg p-6 text-center hover:bg-slate-50 transition-colors cursor-pointer"
                         onclick="this.querySelector('input[type=file]').click()">
                        <span class="material-symbols-outlined text-3xl text-slate-400 mb-1">add_photo_alternate</span>
                        <p class="font-label-md text-slate-700 mb-1">Click to upload image</p>
                        <p class="text-sm text-slate-500">JPG, PNG, or WebP (max. 2MB)</p>
                        <input accept=".jpg,.jpeg,.png,.webp" class="hidden" type="file" wire:model="editImageFile">
                    </div>
                    @error('editImageFile') <span class="text-error text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
            <!-- Modal Footer -->
            <div class="p-6 border-t border-slate-200 flex justify-end gap-4 bg-slate-50 rounded-b-xl">
                <button @click="showEditModal = false" class="btn-ghost">Cancel</button>
                <button wire:click="updateRecord" class="btn-primary gap-2">
                    <span class="material-symbols-outlined">save</span>
                    Update Good
                </button>
            </div>
        </div>
    </div>

    <!-- Modal: View Good Details -->
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4" x-show="showViewModal" style="display: none;">
        <!-- Overlay -->
        <div @click="showViewModal = false" class="fixed inset-0 modal-overlay" x-show="showViewModal" x-transition.opacity></div>
        <!-- Dialog -->
        <div class="relative bg-white w-full max-w-md rounded-xl modal-content flex flex-col" x-show="showViewModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-end="opacity-100 scale-100" x-transition:enter-start="opacity-0 scale-95">
            <div class="p-6 border-b border-slate-200 flex items-center justify-between bg-slate-50 rounded-t-xl">
                <h3 class="font-headline-md text-headline-md text-slate-900 m-0">Good Details</h3>
                <button @click="showViewModal = false" class="text-slate-400 hover:text-slate-700 transition-colors p-2 rounded-full hover:bg-slate-200 flex items-center justify-center">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="p-8 space-y-4">
                @if(data_get($viewRecord, 'image'))
                    <div class="flex justify-center mb-4">
                        <img src="{{ asset('storage/' . data_get($viewRecord, 'image')) }}" alt="{{ data_get($viewRecord, 'name') }}" class="w-32 h-32 rounded-lg object-cover border border-slate-200 shadow-sm">
                    </div>
                @endif
                <div class="grid grid-cols-2 gap-4 text-body-md">
                    <div class="font-semibold text-slate-500">Name:</div>
                    <div class="text-slate-950 font-medium">{{ data_get($viewRecord, 'name') }}</div>

                    <div class="font-semibold text-slate-500">Unit Price:</div>
                    <div class="text-slate-950 font-bold">Rp{{ number_format((float) data_get($viewRecord, 'price', 0), 0) }}</div>

                    <div class="font-semibold text-slate-500">Unit Type:</div>
                    <div class="text-slate-950 font-medium">{{ data_get($viewRecord, 'unitType') }}</div>

                    <div class="font-semibold text-slate-500">Current Stock:</div>
                    <div class="text-slate-950 font-medium">{{ data_get($viewRecord, 'stock') }} units</div>

                    <div class="font-semibold text-slate-500">Created At:</div>
                    <div class="text-slate-950">{{ data_get($viewRecord, 'created') }}</div>

                    <div class="col-span-2 mt-2 pt-2 border-t border-slate-100">
                        <div class="font-semibold text-slate-500 mb-1">Description:</div>
                        <div class="text-slate-700 bg-slate-50 p-3 rounded-lg border border-slate-100 min-h-[60px]">{{ data_get($viewRecord, 'description') }}</div>
                    </div>
                </div>
            </div>
            <div class="p-6 border-t border-slate-200 flex justify-end bg-slate-50 rounded-b-xl">
                <button @click="showViewModal = false" class="btn-primary">Close</button>
            </div>
        </div>
    </div>

    <!-- Modal: Delete Good (Soft Delete) -->
    <div class="fixed inset-0 z-[110] flex items-center justify-center p-4" x-show="showDeleteModal" style="display: none;">
        <!-- Overlay -->
        <div @click="showDeleteModal = false" class="fixed inset-0 modal-overlay" x-show="showDeleteModal" x-transition.opacity></div>
        <!-- Dialog -->
        <div class="relative bg-white w-full max-w-md rounded-xl modal-content p-8 text-center" x-show="showDeleteModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-end="opacity-100 scale-100" x-transition:enter-start="opacity-0 scale-95">
            <div class="mb-4 inline-flex items-center justify-center w-12 h-12 rounded-full bg-error-container text-error">
                <span class="material-symbols-outlined">warning</span>
            </div>
            <h3 class="font-headline-md text-headline-md text-slate-900 mb-4">Delete Inventory Item</h3>
            <p class="text-slate-600 mb-8">Are you sure you want to delete this good? Existing transactions will retain records of this good, but new transactions won't be able to select it.</p>
            <div class="flex justify-center gap-4">
                <button @click="showDeleteModal = false" class="btn-ghost">Cancel</button>
                <button wire:click="deleteRecord" class="bg-error text-white font-semibold py-2 px-6 rounded hover:bg-red-700 transition-colors">Delete</button>
            </div>
        </div>
    </div>
</div>
