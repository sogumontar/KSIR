<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-8">
        <h2 class="font-headline-lg text-headline-lg text-slate-900 m-0">My Profile</h2>
    </div>

    @if(session()->has('message'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-center gap-2">
            <span class="material-symbols-outlined text-green-600">check_circle</span>
            <span class="text-sm font-medium">{{ session('message') }}</span>
        </div>
    @endif

    {{-- Profile Overview Card --}}
    <div class="bg-white border border-slate-200 rounded-xl p-8 shadow-sm mb-8">
        <div class="flex flex-col sm:flex-row items-center gap-6">
            @if(auth()->user()->avatar)
                <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}" class="w-24 h-24 rounded-full object-cover border-4 border-slate-100 shadow-md">
            @else
                <div class="w-24 h-24 rounded-full bg-primary flex items-center justify-center text-white font-bold text-2xl shadow-md">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                </div>
            @endif
            <div class="text-center sm:text-left">
                <h3 class="font-headline-md text-headline-md text-slate-900 m-0">{{ auth()->user()->name }}</h3>
                <p class="text-slate-500 mt-1">{{ auth()->user()->email }}</p>
                <span class="inline-flex items-center mt-2 px-3 py-1 rounded-full text-sm font-medium {{ auth()->user()->is_admin ? 'bg-purple-100 text-purple-800 border border-purple-200' : 'bg-blue-100 text-blue-800 border border-blue-200' }}">
                    {{ auth()->user()->is_admin ? 'Admin' : 'Staff' }}
                </span>
            </div>
        </div>
    </div>

    {{-- Editable Fields Card --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden mb-8">
        <div class="p-6 border-b border-slate-200 bg-slate-50">
            <h3 class="font-headline-md text-headline-md text-slate-900 m-0">Edit Profile</h3>
            <p class="text-slate-500 text-sm mt-1">Update your photo and phone number.</p>
        </div>
        <form wire:submit.prevent="updateProfile" class="p-8 space-y-6">
            {{-- Photo --}}
            <div>
                <label class="form-label">Profile Photo</label>
                @if($photo)
                    <div class="flex items-center gap-3 p-3 bg-green-50 border border-green-200 rounded-lg mb-3">
                        <img src="{{ $photo->temporaryUrl() }}" alt="Preview" class="w-16 h-16 rounded object-cover border border-green-300">
                        <div class="flex-1">
                            <span class="text-sm text-green-800 font-medium">{{ $photo->getClientOriginalName() }}</span>
                            <span class="text-xs text-green-600 ml-2">{{ number_format($photo->getSize() / 1024, 1) }} KB</span>
                        </div>
                        <button wire:click="$set('photo', null)" class="text-red-500 hover:text-red-700">
                            <span class="material-symbols-outlined text-sm">close</span>
                        </button>
                    </div>
                @elseif($existingPhoto)
                    <div class="flex items-center gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg mb-3">
                        <img src="{{ asset('storage/' . $existingPhoto) }}" alt="Current photo" class="w-16 h-16 rounded object-cover border border-blue-300">
                        <div class="flex-1">
                            <span class="text-sm text-blue-800 font-medium">Current photo</span>
                        </div>
                        <button wire:click="$set('existingPhoto', null)" class="text-red-500 hover:text-red-700" title="Remove photo">
                            <span class="material-symbols-outlined text-sm">close</span>
                        </button>
                    </div>
                @endif
                <div x-show="!$wire.photo && !$wire.existingPhoto" class="border-2 border-dashed border-slate-300 rounded-lg p-6 text-center hover:bg-slate-50 transition-colors cursor-pointer"
                     onclick="this.querySelector('input[type=file]').click()">
                    <span class="material-symbols-outlined text-3xl text-slate-400 mb-1">add_photo_alternate</span>
                    <p class="font-label-md text-slate-700 mb-1">Click to upload photo</p>
                    <p class="text-sm text-slate-500">JPG, PNG, or WebP (max. 2MB)</p>
                    <input accept=".jpg,.jpeg,.png,.webp" class="hidden" type="file" wire:model="photo">
                </div>
                @error('photo') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
            </div>

            {{-- Phone Number --}}
            <div>
                <label class="form-label">Phone Number</label>
                <input wire:model="phoneNumber" class="form-input bg-white w-full" type="tel" placeholder="e.g., +62 812 3456 7890">
                @error('phoneNumber') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-primary gap-2">
                    <span class="material-symbols-outlined">save</span>
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    {{-- Password Card --}}
    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
            <div>
                <h3 class="font-headline-md text-headline-md text-slate-900 m-0">Change Password</h3>
                <p class="text-slate-500 text-sm mt-1">Update your account password.</p>
            </div>
            <button @click="showPasswordForm = !showPasswordForm" class="text-primary hover:text-secondary transition-colors text-sm font-medium flex items-center gap-1">
                <span class="material-symbols-outlined text-sm" x-text="showPasswordForm ? 'expand_less' : 'expand_more'">expand_more</span>
                <span x-text="showPasswordForm ? 'Cancel' : 'Change Password'">Change Password</span>
            </button>
        </div>
        <div x-show="showPasswordForm" x-transition class="p-8 space-y-6">
            <form wire:submit.prevent="updatePassword">
                <div>
                    <label class="form-label">Current Password <span class="text-error">*</span></label>
                    <input wire:model="currentPassword" class="form-input bg-white w-full" type="password" autocomplete="current-password">
                    @error('currentPassword') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
                </div>
                <div class="mt-4">
                    <label class="form-label">New Password <span class="text-error">*</span></label>
                    <input wire:model="newPassword" class="form-input bg-white w-full" type="password" autocomplete="new-password">
                    @error('newPassword') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
                </div>
                <div class="mt-4">
                    <label class="form-label">Confirm New Password <span class="text-error">*</span></label>
                    <input wire:model="newPassword_confirmation" class="form-input bg-white w-full" type="password" autocomplete="new-password">
                    @error('newPassword_confirmation') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
                </div>
                <div class="flex justify-end mt-6">
                    <button type="submit" class="btn-primary gap-2">
                        <span class="material-symbols-outlined">lock</span>
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>