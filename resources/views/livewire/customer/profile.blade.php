<div class="max-w-2xl mx-auto space-y-md pb-16">

    {{-- Page Header --}}
    <div class="flex items-center gap-sm">
        <a href="{{ route('customer.dashboard') }}" wire:navigate
           class="w-9 h-9 flex items-center justify-center rounded-xl hover:bg-surface-container text-on-surface-variant transition-colors">
            <span class="material-symbols-outlined text-[22px]">arrow_back</span>
        </a>
        <h1 class="font-headline-md font-bold text-primary">My Profile</h1>
    </div>

    {{-- Profile Info Card --}}
    <div class="bg-white rounded-3xl border border-outline-variant shadow-sm overflow-hidden">
        {{-- Avatar Section --}}
        <div class="bg-gradient-to-r from-primary/10 to-secondary/10 px-lg py-xl flex flex-col sm:flex-row items-center sm:items-end gap-md">
            <div class="relative group">
                @php
                    $user = auth()->user();
                    $avatarUrl = $user->photo_path
                        ? Storage::disk('public')->url($user->photo_path)
                        : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&color=7F9CF5&background=EBF4FF&size=128';
                @endphp
                <div class="w-24 h-24 rounded-2xl overflow-hidden border-4 border-white shadow-lg">
                    @if($photo)
                        <img src="{{ $photo->temporaryUrl() }}" alt="Preview" class="w-full h-full object-cover">
                    @else
                        <img src="{{ $avatarUrl }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                    @endif
                </div>
                <label class="absolute -bottom-2 -right-2 w-8 h-8 bg-primary text-on-primary rounded-full flex items-center justify-center cursor-pointer hover:bg-primary/80 transition-colors shadow-md">
                    <span class="material-symbols-outlined text-[16px]">photo_camera</span>
                    <input type="file" wire:model="photo" class="sr-only" accept="image/*">
                </label>
            </div>
            <div class="text-center sm:text-left pb-1">
                <div class="font-headline-sm font-bold text-on-surface">{{ $user->name }}</div>
                <div class="text-on-surface-variant text-sm">{{ $user->email }}</div>
                @if($photo)
                    <div wire:loading.remove wire:target="photo" class="mt-xs text-xs text-primary font-medium">New photo ready to save</div>
                    <div wire:loading wire:target="photo" class="mt-xs text-xs text-on-surface-variant">Uploading preview…</div>
                @endif
            </div>
        </div>

        {{-- Form --}}
        <form wire:submit="updateProfile" class="p-lg space-y-md">
            @if($profileSaved)
                <div class="p-sm bg-secondary-container text-on-secondary-container rounded-xl text-sm font-medium flex items-center gap-xs animate-fade-in">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                    Profile updated successfully!
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-md">
                {{-- Name --}}
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-on-surface-variant uppercase tracking-wider mb-1.5">Full Name</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-[18px] text-outline">person</span>
                        <input wire:model="name" type="text"
                               class="w-full pl-10 pr-4 py-3 rounded-xl border border-outline-variant bg-surface text-on-surface text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all @error('name') border-error @enderror"
                               placeholder="Your full name">
                    </div>
                    @error('name') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-xs font-semibold text-on-surface-variant uppercase tracking-wider mb-1.5">Email</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-[18px] text-outline">mail</span>
                        <input wire:model="email" type="email"
                               class="w-full pl-10 pr-4 py-3 rounded-xl border border-outline-variant bg-surface text-on-surface text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all @error('email') border-error @enderror"
                               placeholder="your@email.com">
                    </div>
                    @error('email') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Phone --}}
                <div>
                    <label class="block text-xs font-semibold text-on-surface-variant uppercase tracking-wider mb-1.5">Phone Number</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-[18px] text-outline">call</span>
                        <input wire:model="phone_number" type="tel"
                               class="w-full pl-10 pr-4 py-3 rounded-xl border border-outline-variant bg-surface text-on-surface text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all @error('phone_number') border-error @enderror"
                               placeholder="+62 812 3456 7890">
                    </div>
                    @error('phone_number') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="pt-xs">
                <button type="submit"
                        wire:loading.attr="disabled"
                        class="w-full sm:w-auto px-xl py-3 bg-primary text-on-primary rounded-xl font-label-lg font-bold hover:bg-primary/90 hover:shadow-md transition-all active:scale-95 disabled:opacity-60 flex items-center justify-center gap-sm">
                    <span wire:loading.remove wire:target="updateProfile" class="material-symbols-outlined text-[18px]">save</span>
                    <span wire:loading wire:target="updateProfile" class="material-symbols-outlined text-[18px] animate-spin">progress_activity</span>
                    <span wire:loading.remove wire:target="updateProfile">Save Changes</span>
                    <span wire:loading wire:target="updateProfile">Saving…</span>
                </button>
            </div>
        </form>
    </div>

    {{-- Change Password Card --}}
    <div class="bg-white rounded-3xl border border-outline-variant shadow-sm p-lg space-y-md">
        <h2 class="font-headline-sm font-bold text-on-surface flex items-center gap-xs">
            <span class="material-symbols-outlined text-[22px] text-primary">lock</span>
            Change Password
        </h2>

        <form wire:submit="updatePassword" class="space-y-md">
            @if($passwordSaved)
                <div class="p-sm bg-secondary-container text-on-secondary-container rounded-xl text-sm font-medium flex items-center gap-xs animate-fade-in">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                    Password changed successfully!
                </div>
            @endif

            {{-- Current Password --}}
            <div>
                <label class="block text-xs font-semibold text-on-surface-variant uppercase tracking-wider mb-1.5">Current Password</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-[18px] text-outline">lock</span>
                    <input wire:model="currentPassword" type="password"
                           class="w-full pl-10 pr-4 py-3 rounded-xl border border-outline-variant bg-surface text-on-surface text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all @error('currentPassword') border-error @enderror"
                           placeholder="Enter current password">
                </div>
                @error('currentPassword') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid sm:grid-cols-2 gap-md">
                {{-- New Password --}}
                <div>
                    <label class="block text-xs font-semibold text-on-surface-variant uppercase tracking-wider mb-1.5">New Password</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-[18px] text-outline">key</span>
                        <input wire:model="newPassword" type="password"
                               class="w-full pl-10 pr-4 py-3 rounded-xl border border-outline-variant bg-surface text-on-surface text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all @error('newPassword') border-error @enderror"
                               placeholder="Min. 8 characters">
                    </div>
                    @error('newPassword') <p class="text-error text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Confirm New Password --}}
                <div>
                    <label class="block text-xs font-semibold text-on-surface-variant uppercase tracking-wider mb-1.5">Confirm New Password</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-[18px] text-outline">key</span>
                        <input wire:model="newPasswordConfirmation" type="password"
                               class="w-full pl-10 pr-4 py-3 rounded-xl border border-outline-variant bg-surface text-on-surface text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition-all"
                               placeholder="Repeat new password">
                    </div>
                </div>
            </div>

            <button type="submit"
                    wire:loading.attr="disabled"
                    class="w-full sm:w-auto px-xl py-3 bg-secondary text-white rounded-xl font-label-lg font-bold hover:bg-secondary/90 hover:shadow-md transition-all active:scale-95 disabled:opacity-60 flex items-center justify-center gap-sm">
                <span wire:loading.remove wire:target="updatePassword" class="material-symbols-outlined text-[18px]">lock_reset</span>
                <span wire:loading wire:target="updatePassword" class="material-symbols-outlined text-[18px] animate-spin">progress_activity</span>
                <span wire:loading.remove wire:target="updatePassword">Update Password</span>
                <span wire:loading wire:target="updatePassword">Updating…</span>
            </button>
        </form>
    </div>

    {{-- Logout --}}
    <div class="bg-white rounded-3xl border border-outline-variant shadow-sm p-lg">
        <h2 class="font-headline-sm font-bold text-on-surface mb-xs flex items-center gap-xs">
            <span class="material-symbols-outlined text-[22px] text-error">logout</span>
            Sign Out
        </h2>
        <p class="text-on-surface-variant text-sm mb-md">You will be redirected to the login page.</p>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="px-xl py-3 bg-error/10 text-error border border-error/20 rounded-xl font-label-lg font-bold hover:bg-error/20 transition-all active:scale-95">
                Logout
            </button>
        </form>
    </div>

</div>
