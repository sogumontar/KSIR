<div>
    <!-- Sub-nav pills -->
    <div class="flex gap-2 mb-8 overflow-x-auto pb-2">
        <a href="{{ route('laundry.dashboard') }}" wire:navigate class="px-4 py-2 rounded-full font-label-md bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 whitespace-nowrap">Dashboard</a>
        <a href="{{ route('laundry.services') }}" wire:navigate class="px-4 py-2 rounded-full font-label-md bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 whitespace-nowrap">Services</a>
        <a href="{{ route('laundry.promos') }}" wire:navigate class="px-4 py-2 rounded-full font-label-md bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 whitespace-nowrap">Promos</a>
        <a href="{{ route('laundry.website') }}" wire:navigate class="px-4 py-2 rounded-full font-label-md bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 whitespace-nowrap flex items-center gap-1.5">
            <span class="material-symbols-outlined text-sm">language</span>
            Website Clean Lab
        </a>
        <a href="{{ route('laundry.showcase') }}" wire:navigate class="px-4 py-2 rounded-full font-label-md bg-secondary text-white shadow-sm whitespace-nowrap flex items-center gap-1.5">
            <span class="material-symbols-outlined text-sm">compare</span>
            Before / After
        </a>
        <a href="{{ route('laundry.settings') }}" wire:navigate class="px-4 py-2 rounded-full font-label-md bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 whitespace-nowrap">Settings</a>
    </div>

    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4 mb-6">
        <div>
            <h2 class="font-headline-lg text-primary m-0">Before / After Showcase</h2>
            <p class="text-xs text-slate-500 mt-1">Kelompokkan hasil cucian per treatment (Unyellowing, Deep Clean, dll). Foto otomatis dikompres jadi WebP ±100KB dan disimpan sebagai file di server (bukan di database) agar hosting tetap ringan.</p>
        </div>
        <button wire:click="openAdd" class="btn-primary gap-2">
            <span class="material-symbols-outlined">add</span> Tambah Kategori Treatment
        </button>
    </div>

    @if (session()->has('showcase_msg'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-2 text-sm font-semibold">
            <span class="material-symbols-outlined text-emerald-600">check_circle</span>
            {{ session('showcase_msg') }}
        </div>
    @endif

    <div class="space-y-6">
        @forelse($showcases as $s)
            <div class="card-surface p-5 sm:p-6 {{ $s->is_active ? '' : 'opacity-60' }}" wire:key="sc-{{ $s->id }}">
                <div class="flex flex-wrap items-start justify-between gap-3 border-b border-slate-100 pb-4 mb-4">
                    <div class="flex items-start gap-3 min-w-0">
                        <div class="h-12 w-12 shrink-0 rounded-2xl bg-emerald-50 text-secondary flex items-center justify-center">
                            <span class="material-symbols-outlined text-2xl">{{ $s->icon }}</span>
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-headline-md text-slate-900 m-0">{{ $s->title }}</h3>
                            @if($s->description)
                                <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $s->description }}</p>
                            @endif
                            @if($s->video_url)
                                <a href="{{ $s->video_url }}" target="_blank" rel="noopener" class="text-xs text-blue-600 hover:underline inline-flex items-center gap-1 mt-1">
                                    <span class="material-symbols-outlined text-sm">play_circle</span> Video terhubung
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-1">
                        <button wire:click="toggleActive({{ $s->id }})" class="btn-icon" title="{{ $s->is_active ? 'Sembunyikan' : 'Tampilkan' }}">
                            <span class="material-symbols-outlined text-sm">{{ $s->is_active ? 'visibility' : 'visibility_off' }}</span>
                        </button>
                        <button wire:click="openEdit({{ $s->id }})" class="btn-icon" title="Edit"><span class="material-symbols-outlined text-sm">edit</span></button>
                        <button wire:click="delete({{ $s->id }})" wire:confirm="Hapus kategori ini beserta {{ $s->photos->count() }} foto?" class="btn-icon text-red-600" title="Hapus"><span class="material-symbols-outlined text-sm">delete</span></button>
                    </div>
                </div>

                {{-- Photo pairs --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach($s->photos as $p)
                        <div class="rounded-xl border border-slate-200 overflow-hidden bg-white" wire:key="ph-{{ $p->id }}">
                            <div class="grid grid-cols-2">
                                <div class="relative">
                                    <img src="{{ storage_url($p->before_path) }}" loading="lazy" class="h-28 w-full object-cover" alt="Before">
                                    <span class="absolute top-1 left-1 bg-black/60 text-white text-[9px] font-bold px-1.5 rounded">BEFORE</span>
                                </div>
                                <div class="relative">
                                    <img src="{{ storage_url($p->after_path) }}" loading="lazy" class="h-28 w-full object-cover" alt="After">
                                    <span class="absolute top-1 left-1 bg-emerald-600 text-white text-[9px] font-bold px-1.5 rounded">AFTER</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between gap-2 px-2.5 py-2">
                                <span class="text-xs font-semibold text-slate-700 truncate">{{ $p->label ?: 'Tanpa label' }}</span>
                                <button wire:click="deletePhoto({{ $p->id }})" wire:confirm="Hapus foto ini?" class="text-red-500 hover:text-red-700">
                                    <span class="material-symbols-outlined text-base">delete</span>
                                </button>
                            </div>
                        </div>
                    @endforeach

                    <button wire:click="openPhotoForm({{ $s->id }})" class="rounded-xl border-2 border-dashed border-slate-300 hover:border-secondary hover:bg-emerald-50/50 text-slate-500 hover:text-secondary min-h-[9.5rem] flex flex-col items-center justify-center gap-1 text-xs font-bold transition-colors">
                        <span class="material-symbols-outlined text-3xl">add_photo_alternate</span>
                        Tambah Before/After
                    </button>
                </div>

                {{-- Inline add-photo form with in-browser compression (saves bandwidth & PHP upload limits) --}}
                @if($photoShowcaseId === $s->id)
                    <div class="mt-5 p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-4"
                         x-data="{
                            busy: false,
                            async shrink(file, max = 1600, q = 0.82) {
                                if (!file.type.startsWith('image/')) return file;
                                const bmp = await createImageBitmap(file, { imageOrientation: 'from-image' });
                                const k = Math.min(1, max / Math.max(bmp.width, bmp.height));
                                const c = document.createElement('canvas');
                                c.width = Math.round(bmp.width * k); c.height = Math.round(bmp.height * k);
                                c.getContext('2d').drawImage(bmp, 0, 0, c.width, c.height);
                                const blob = await new Promise(r => c.toBlob(r, 'image/jpeg', q));
                                return new File([blob], 'photo.jpg', { type: 'image/jpeg' });
                            },
                            async pick(e, prop) {
                                const f = e.target.files[0]; if (!f) return;
                                this.busy = true;
                                try { const small = await this.shrink(f); $wire.upload(prop, small, () => this.busy = false, () => this.busy = false); }
                                catch (_) { $wire.upload(prop, f, () => this.busy = false, () => this.busy = false); }
                            }
                         }">
                        <div>
                            <label class="form-label text-xs">Label (opsional) — mis. Nike Air Force 1 / Helm Full Face</label>
                            <input type="text" wire:model="photoLabel" class="form-input w-full bg-white text-sm" placeholder="Nama barang">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label text-xs">Foto BEFORE</label>
                                <input type="file" accept="image/*" @change="pick($event, 'beforeUpload')" class="form-input w-full bg-white text-sm py-2">
                                @if($beforeUpload) <img src="{{ $beforeUpload->temporaryUrl() }}" class="mt-2 h-24 rounded-lg object-cover border"> @endif
                                @error('beforeUpload') <span class="text-error text-xs block mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="form-label text-xs">Foto AFTER</label>
                                <input type="file" accept="image/*" @change="pick($event, 'afterUpload')" class="form-input w-full bg-white text-sm py-2">
                                @if($afterUpload) <img src="{{ $afterUpload->temporaryUrl() }}" class="mt-2 h-24 rounded-lg object-cover border"> @endif
                                @error('afterUpload') <span class="text-error text-xs block mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" wire:click="closePhotoForm" class="btn-ghost">Batal</button>
                            <button type="button" wire:click="savePhoto" x-bind:disabled="busy" class="btn-primary disabled:opacity-50">
                                <span wire:loading.remove wire:target="savePhoto"><span x-show="!busy">Simpan Foto</span><span x-show="busy">Memproses gambar…</span></span>
                                <span wire:loading wire:target="savePhoto">Mengompres…</span>
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="card-surface p-12 text-center">
                <span class="material-symbols-outlined text-6xl text-slate-300 mb-3">compare</span>
                <h3 class="font-headline-md text-slate-500">Belum ada kategori treatment</h3>
                <p class="text-slate-400 mt-1 text-sm">Buat kategori seperti “Unyellowing”, “Deep Cleaning”, “Helmet Detailing”, lalu tambahkan foto before/after.</p>
                <button wire:click="openAdd" class="btn-primary mt-5 mx-auto">Tambah Kategori</button>
            </div>
        @endforelse
    </div>

    {{-- Category modal --}}
    @if($showForm)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
        <div class="bg-white w-full max-w-lg rounded-2xl shadow-xl overflow-hidden max-h-[92vh] flex flex-col">
            <div class="p-5 border-b flex justify-between items-center">
                <h3 class="font-headline-md m-0">{{ $editingId ? 'Edit Kategori' : 'Tambah Kategori Treatment' }}</h3>
                <button wire:click="cancel" class="text-slate-400 hover:text-slate-600"><span class="material-symbols-outlined">close</span></button>
            </div>
            <div class="p-5 space-y-4 overflow-y-auto">
                <div>
                    <label class="form-label text-sm">Nama Treatment</label>
                    <input wire:model="title" type="text" class="form-input w-full" placeholder="Unyellowing">
                    @error('title') <span class="text-error text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="form-label text-sm">Ikon</label>
                    <select wire:model="icon" class="form-input w-full">
                        <option value="auto_awesome">✨ Unyellowing / Detailing</option>
                        <option value="steps">👟 Sepatu</option>
                        <option value="roller_skating">🩴 Sandal</option>
                        <option value="sports_motorsports">🪖 Helm</option>
                        <option value="work">👜 Tas / Topi</option>
                        <option value="luggage">🧳 Koper</option>
                        <option value="cleaning_services">🧼 Deep Cleaning</option>
                    </select>
                </div>
                <div>
                    <label class="form-label text-sm">Deskripsi Treatment</label>
                    <textarea wire:model="description" rows="4" class="form-input w-full py-2" placeholder="Jelaskan proses & manfaat treatment ini…"></textarea>
                    @error('description') <span class="text-error text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="form-label text-sm">Link Video (opsional)</label>
                    <input wire:model="videoUrl" type="url" class="form-input w-full" placeholder="https://youtube.com/watch?v=… / TikTok / Instagram">
                    <p class="text-[11px] text-slate-400 mt-1">Video tidak diunggah ke hosting (berat). Tempel link YouTube (tampil langsung di halaman) atau TikTok/IG (tampil sebagai tombol tonton).</p>
                    @error('videoUrl') <span class="text-error text-xs">{{ $message }}</span> @enderror
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input wire:model="isActive" type="checkbox" class="rounded border-slate-300 text-secondary">
                    <span class="text-sm font-medium">Tampilkan di website</span>
                </label>
            </div>
            <div class="p-5 border-t bg-slate-50 flex justify-end gap-3">
                <button wire:click="cancel" class="btn-ghost">Batal</button>
                <button wire:click="save" class="btn-primary">Simpan</button>
            </div>
        </div>
    </div>
    @endif
</div>
