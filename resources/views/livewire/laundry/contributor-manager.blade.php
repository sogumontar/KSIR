<div>
    {{-- Flash --}}
    @if(session()->has('contrib_message'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-center gap-2 text-sm">
            <span class="material-symbols-outlined text-green-600 text-base">check_circle</span>
            {{ session('contrib_message') }}
        </div>
    @endif

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-800 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">people</span>
                Kelola Kontributor
            </h3>
        </div>

        {{-- Create invite form --}}
        <div class="p-6 border-b border-slate-100">
            <p class="text-sm text-slate-500 mb-4">Buat link undangan untuk mengajak orang lain menjadi kontributor toko Anda.</p>
            <form wire:submit.prevent="createInvite" class="flex gap-2">
                <input
                    wire:model="inviteName"
                    type="text"
                    class="form-input bg-white flex-1 text-sm"
                    placeholder="Nama undangan (contoh: Karyawan 1)"
                >
                <button type="submit" class="px-4 py-2 bg-primary hover:bg-secondary text-white rounded-lg text-sm font-medium transition-colors flex items-center gap-1 shrink-0">
                    <span class="material-symbols-outlined text-base">add_link</span>
                    Buat Link
                </button>
            </form>
            @error('inviteName')
                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        {{-- Contributors list --}}
        <div class="divide-y divide-slate-100">
            @forelse($contributors as $contrib)
                <div class="px-6 py-4 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full {{ $contrib->isAccepted() ? 'bg-green-100' : 'bg-amber-100' }} flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-base {{ $contrib->isAccepted() ? 'text-green-600' : 'text-amber-600' }}">
                                {{ $contrib->isAccepted() ? 'person_check' : 'pending' }}
                            </span>
                        </div>
                        <div>
                            <p class="font-medium text-slate-900 text-sm">
                                {{ $contrib->invite_name }}
                                @if($contrib->contributor)
                                    <span class="text-slate-400 font-normal">— {{ $contrib->contributor->name }}</span>
                                @endif
                            </p>
                            <div class="flex items-center gap-2 mt-0.5">
                                @if($contrib->isAccepted())
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-green-700 bg-green-50 px-2 py-0.5 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Diterima
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Menunggu
                                    </span>
                                    {{-- Copy invite link --}}
                                    <button
                                        onclick="navigator.clipboard.writeText('{{ route('laundry.contributor.join', $contrib->invite_token) }}').then(() => alert('Link undangan disalin!'))"
                                        class="text-xs text-primary hover:underline flex items-center gap-0.5"
                                    >
                                        <span class="material-symbols-outlined text-sm">content_copy</span>
                                        Salin Link
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    <button
                        wire:click="confirmDelete({{ $contrib->id }})"
                        class="p-1.5 text-slate-300 hover:text-red-500 transition-colors rounded"
                        title="Hapus kontributor"
                    >
                        <span class="material-symbols-outlined text-lg">delete</span>
                    </button>
                </div>
            @empty
                <div class="px-6 py-8 text-center text-slate-400 text-sm">
                    <span class="material-symbols-outlined text-3xl mb-2 block text-slate-300">group_off</span>
                    Belum ada kontributor. Buat link undangan di atas.
                </div>
            @endforelse
        </div>
    </div>

    {{-- Delete confirm modal --}}
    @if($showDeleteConfirm)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" wire:click="cancelDelete"></div>
            <div class="bg-white rounded-2xl w-full max-w-sm shadow-xl relative z-10 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center">
                        <span class="material-symbols-outlined text-red-500">warning</span>
                    </div>
                    <h3 class="font-bold text-slate-800">Hapus Kontributor?</h3>
                </div>
                <p class="text-sm text-slate-500 mb-6">Kontributor yang dihapus tidak akan bisa mengakses toko ini lagi. Tindakan ini tidak dapat dibatalkan.</p>
                <div class="flex gap-3 justify-end">
                    <button wire:click="cancelDelete" class="px-4 py-2 text-sm text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors font-medium">Batal</button>
                    <button wire:click="removeContributor" class="px-4 py-2 text-sm text-white bg-red-500 hover:bg-red-600 rounded-lg transition-colors font-medium">Hapus</button>
                </div>
            </div>
        </div>
    @endif
</div>
