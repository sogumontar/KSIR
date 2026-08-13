<div>
    <!-- Sub-nav pills -->
    <div class="flex gap-2 mb-8 overflow-x-auto pb-2">
        <a href="{{ route('laundry.dashboard') }}" wire:navigate class="px-4 py-2 rounded-full font-label-md bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 whitespace-nowrap">Dashboard</a>
        <a href="{{ route('laundry.services') }}" wire:navigate class="px-4 py-2 rounded-full font-label-md bg-secondary text-white shadow-sm whitespace-nowrap">Services</a>
        <a href="{{ route('laundry.promos') }}" wire:navigate class="px-4 py-2 rounded-full font-label-md bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 whitespace-nowrap">Promos</a>
        <a href="{{ route('laundry.settings') }}" wire:navigate class="px-4 py-2 rounded-full font-label-md bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 whitespace-nowrap">Settings</a>
    </div>

    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-md mb-8">
        <div>
            <h2 class="font-headline-lg text-primary m-0">Service Manager</h2>
        </div>
        <div class="flex items-center gap-md">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search services..." class="form-input bg-white w-64">
            <button wire:click="openAdd" class="btn-primary gap-2">
                <span class="material-symbols-outlined">add</span>
                Add Service
            </button>
        </div>
    </div>

    <!-- Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @forelse($services as $service)
        <div class="card-surface p-6 flex flex-col relative group transition-all hover:shadow-md">
            <div class="flex justify-between items-start mb-4">
                <h3 class="font-headline-md text-slate-900">{{ $service->name }}</h3>
                <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button wire:click="openEdit({{ $service->id }})" class="btn-icon w-8 h-8 text-slate-500 hover:text-primary"><span class="material-symbols-outlined text-sm">edit</span></button>
                    <button wire:click="delete({{ $service->id }})" class="btn-icon w-8 h-8 text-slate-500 hover:text-error"><span class="material-symbols-outlined text-sm">delete</span></button>
                </div>
            </div>
            <p class="text-slate-500 text-sm mb-4 flex-grow">{{ $service->description ?? 'No description provided.' }}</p>
            <div class="flex items-center justify-between border-t border-slate-100 pt-4 mt-auto">
                <div>
                    <span class="font-bold text-lg text-secondary">Rp{{ number_format($service->price, 0) }}</span>
                    @if($service->short_code)
                    <span class="ml-2 text-xs font-mono font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded">{{ $service->short_code }}</span>
                    @endif
                </div>
                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $service->is_active ? 'bg-green-100 text-green-800' : 'bg-slate-100 text-slate-600' }}">
                    {{ $service->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>
        @empty
        <div class="col-span-1 md:col-span-3 card-surface p-12 text-center">
            <span class="material-symbols-outlined text-6xl text-slate-300 mb-4">local_laundry_service</span>
            <h3 class="font-headline-md text-slate-500">No services found</h3>
            <p class="text-slate-400 mt-2">Get started by creating your first laundry service.</p>
            <button wire:click="openAdd" class="btn-primary mt-6 mx-auto">Add Service</button>
        </div>
        @endforelse
    </div>

    <!-- Modal -->
    @if($showForm)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl overflow-hidden flex flex-col">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-headline-md m-0">{{ $editingId ? 'Edit Service' : 'Add Service' }}</h3>
                <button wire:click="cancel" class="text-slate-400 hover:text-slate-600"><span class="material-symbols-outlined">close</span></button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="form-label">Service Name</label>
                    <input wire:model="name" type="text" class="form-input w-full">
                    @error('name') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="form-label">Order Code Prefix <span class="text-slate-400 text-xs font-normal">(optional – e.g. SHOES, SHIRT, BAG)</span></label>
                    <input wire:model="shortCode" type="text" class="form-input w-full font-mono uppercase" placeholder="e.g. SHOES" maxlength="20">
                    <p class="text-xs text-slate-400 mt-1">Used to generate order codes like <span class="font-mono">SHOES-0001</span>. Leave blank to use <span class="font-mono">ORD</span>.</p>
                    @error('shortCode') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="form-label">Price (Rp)</label>
                    <input wire:model="price" type="number" class="form-input w-full">
                    @error('price') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="form-label">Description</label>
                    <textarea wire:model="description" class="form-input w-full h-24" placeholder="e.g. Wash and fold per Kg"></textarea>
                    @error('description') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div class="flex items-center gap-2">
                    <input wire:model="isActive" type="checkbox" id="isActive" class="rounded border-slate-300 text-secondary focus:ring-secondary">
                    <label for="isActive" class="text-sm text-slate-700 font-medium cursor-pointer">Active Service</label>
                </div>
            </div>
            <div class="p-6 border-t border-slate-100 bg-slate-50 flex justify-end gap-4">
                <button wire:click="cancel" class="btn-ghost">Cancel</button>
                <button wire:click="save" class="btn-primary relative">
                    <span wire:loading.remove wire:target="save">Save Service</span>
                    <span wire:loading wire:target="save">Saving...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
