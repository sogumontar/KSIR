<div>
    <!-- Sub-nav pills -->
    <div class="flex gap-2 mb-8 overflow-x-auto pb-2">
        <a href="{{ route('laundry.dashboard') }}" wire:navigate class="px-4 py-2 rounded-full font-label-md bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 whitespace-nowrap">Dashboard</a>
        <a href="{{ route('laundry.services') }}" wire:navigate class="px-4 py-2 rounded-full font-label-md bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 whitespace-nowrap">Services</a>
        <a href="{{ route('laundry.promos') }}" wire:navigate class="px-4 py-2 rounded-full font-label-md bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 whitespace-nowrap">Promos</a>
        <a href="{{ route('laundry.settings') }}" wire:navigate class="px-4 py-2 rounded-full font-label-md bg-secondary text-white shadow-sm whitespace-nowrap">Settings</a>
    </div>

    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-md mb-8">
        <div>
            <h2 class="font-headline-lg text-primary m-0">Merchant Settings</h2>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-center gap-2">
            <span class="material-symbols-outlined text-green-600">check_circle</span>
            <span class="text-sm font-medium">{{ session('message') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- QR Upload Card -->
        <div class="card-surface p-6">
            <h3 class="font-headline-md text-slate-900 mb-4 border-b border-slate-100 pb-2">Payment QR Code</h3>
            
            @if($existingQrPath)
                <div class="mb-6 p-4 border border-slate-200 rounded-lg flex flex-col items-center bg-slate-50">
                    <p class="text-sm text-slate-500 mb-4">Current QR Code</p>
                    <img src="{{ storage_url($existingQrPath) }}" alt="QR Code" class="w-48 h-48 object-contain rounded bg-white shadow-sm border border-slate-100">
                </div>
            @endif

            <div class="mb-4">
                <label class="form-label block mb-2">Upload New QR Code</label>
                <div class="border-2 border-dashed border-slate-300 rounded-xl p-8 text-center hover:bg-slate-50 transition-colors cursor-pointer"
                     onclick="this.querySelector('input[type=file]').click()">
                    <span class="material-symbols-outlined text-4xl text-slate-400 mb-2">qr_code_scanner</span>
                    <p class="font-label-md text-slate-700 mb-1">Click to upload image</p>
                    <p class="text-xs text-slate-500">JPG, PNG up to 2MB</p>
                    <input accept=".jpg,.jpeg,.png" class="hidden" type="file" wire:model="qrCode">
                </div>
                
                @if($qrCode)
                    <div class="mt-4 p-3 bg-blue-50 text-blue-800 rounded-lg text-sm flex items-center gap-2 border border-blue-200">
                        <span class="material-symbols-outlined text-blue-600 text-sm">image</span>
                        {{ $qrCode->getClientOriginalName() }} ready to save.
                    </div>
                @endif
                @error('qrCode') <span class="text-error text-xs mt-2 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Payment Notes Card -->
        <div class="card-surface p-6">
            <h3 class="font-headline-md text-slate-900 mb-4 border-b border-slate-100 pb-2">Receipt & Payment Notes</h3>
            
            <div class="mb-6">
                <label class="form-label block mb-2">Payment Instructions / Thank You Note</label>
                <textarea wire:model="paymentNotes" class="form-input w-full h-48 resize-y" placeholder="e.g. BCA 1234567890 a.n. John Doe. Thank you for your business!"></textarea>
                <p class="text-xs text-slate-500 mt-2">This note will appear at the bottom of the customer's PDF receipt.</p>
                @error('paymentNotes') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
            </div>
            
            <div class="flex justify-end pt-4 border-t border-slate-100">
                <button wire:click="save" class="btn-primary gap-2 relative px-8">
                    <span wire:loading.remove wire:target="save" class="flex items-center gap-2">
                        <span class="material-symbols-outlined">save</span>
                        Save Settings
                    </span>
                    <span wire:loading wire:target="save">Saving...</span>
                </button>
            </div>
        </div>
    </div>
</div>
