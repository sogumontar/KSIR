<div>
    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-md mb-xl">
        <div>
            <h2 class="font-headline-lg text-headline-lg text-primary">Admin: User Management</h2><span class="inline-flex items-center rounded-md bg-secondary-container px-2 py-1 text-xs font-medium text-on-secondary-container ring-1 ring-inset ring-secondary/20 ml-3">Administrator Access Level</span>
            <p class="text-on-surface-variant font-body-md">Configure access and roles for all enterprise platform members.</p>
        </div>
        <button wire:click="openAdd" class="bg-secondary text-white font-label-lg px-md h-12 rounded-lg flex items-center justify-center gap-2 hover:bg-on-secondary-container transition-all active:scale-[0.98] shadow-lg">
            <span class="material-symbols-outlined">person_add</span>
            Add New User
        </button>
    </div>

    {{-- Filter & Search Bar --}}
    <section class="bg-surface border border-outline-variant rounded-xl p-md mb-gutter shadow-sm">
        <div class="flex flex-col lg:flex-row gap-md items-stretch lg:items-end">
            <div class="flex-1 w-full">
                <label class="block font-label-md text-on-surface mb-2">Search Users</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
                    <input wire:model.live="search" class="w-full pl-10 pr-4 h-12 bg-white border border-outline rounded-lg focus:ring-2 focus:ring-secondary focus:border-transparent" placeholder="Name or email..." type="text">
                </div>
            </div>
            <div class="w-full lg:w-48">
                <label class="block font-label-md text-on-surface mb-2">Role</label>
                <select wire:model.live="roleFilter" class="w-full h-12 bg-white border border-outline rounded-lg px-4 focus:ring-2 focus:ring-secondary">
                    <option value="">All Roles</option>
                    <option value="admin">Admin</option>
                    <option value="staff">Staff</option>
                </select>
            </div>
            <div class="w-full lg:w-48">
                <label class="block font-label-md text-on-surface mb-2">Status</label>
                <select wire:model.live="statusFilter" class="w-full h-12 bg-white border border-outline rounded-lg px-4 focus:ring-2 focus:ring-secondary">
                    <option value="">Any Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <button wire:click="resetFilters" class="h-12 px-md border border-outline text-on-surface-variant font-label-lg rounded-lg hover:bg-surface-container-low transition-colors">
                Reset
            </button>
        </div>
    </section>

    {{-- User Table Card --}}
    <section class="bg-surface border border-outline-variant rounded-xl overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse min-w-[640px]">
                <thead class="bg-primary text-white">
                <tr>
                    <th class="text-left py-4 px-6 font-label-lg">User</th>
                    <th class="text-left py-4 px-6 font-label-lg">Role</th>
                    <th class="text-left py-4 px-6 font-label-lg">Status</th>
                    <th class="text-left py-4 px-6 font-label-lg">Created</th>
                    <th class="text-right py-4 px-6 font-label-lg">Actions</th>
                </tr>
                </thead>
                <tbody class="table-stripe text-on-surface">
                @forelse($users as $user)
                <tr class="border-b border-outline-variant hover:bg-surface-container transition-colors group">
                    <td class="py-4 px-6">
                        <div class="flex items-center gap-md">
                            @if($user->avatar)
                                <img class="w-12 h-12 rounded-full object-cover border-2 border-white shadow-sm" src="{{ $user->avatar }}" alt="{{ $user->name }}">
                            @else
                                <div class="w-12 h-12 rounded-full bg-surface-dim flex items-center justify-center font-label-lg text-on-surface-variant">
                                    {{ collect(explode(' ', $user->name))->map(fn($part) => strtoupper(substr($part, 0, 1)))->take(2)->implode('') }}
                                </div>
                            @endif
                            <div>
                                <div class="font-label-lg text-primary">{{ $user->name }}</div>
                                <div class="text-on-surface-variant font-body-md">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-6">
                        @if($user->is_admin)
                            <span class="bg-secondary-container text-on-secondary-container px-3 py-1 rounded-full font-label-md">Admin</span>
                        @else
                            <span class="bg-surface-container-highest text-on-surface-variant px-3 py-1 rounded-full font-label-md">Staff</span>
                        @endif
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex items-center gap-2">
                            @if($user->status === 'active')
                                <div class="w-2.5 h-2.5 rounded-full bg-green-500"></div>
                                <span class="font-label-md">Active</span>
                            @else
                                <div class="w-2.5 h-2.5 rounded-full bg-red-500"></div>
                                <span class="font-label-md">Inactive</span>
                            @endif
                        </div>
                    </td>
                    <td class="py-4 px-6 text-on-surface-variant font-body-md">
                        {{ $user->created_at->format('M d, Y') }}
                    </td>
                    <td class="py-4 px-6 text-right">
                        <div class="flex items-center justify-end gap-xs">
                            <button wire:click="openEdit({{ $user->id }})" class="material-symbols-outlined text-on-surface-variant hover:text-secondary p-2 rounded hover:bg-surface-container-high transition-all">edit</button>
                            <button wire:click="deleteUser({{ $user->id }})" wire:confirm="Are you sure you want to delete this user?" class="material-symbols-outlined text-on-surface-variant hover:text-error p-2 rounded hover:bg-surface-container-high transition-all">delete</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-8 px-6 text-center text-on-surface-variant font-body-md">
                        No users found.
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{-- Pagination --}}
        <div class="bg-surface-container-low px-6 py-4 flex flex-col sm:flex-row items-center justify-between gap-sm border-t border-outline-variant">
            <span class="text-on-surface-variant font-label-md">Showing {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users</span>
            <div>
                {{ $users->links() }}
            </div>
        </div>
    </section>

    {{-- Overlay Backdrop & Edit/Add User Slide-out Sidebar --}}
    <div x-data x-show="$wire.showEditSidebar" x-cloak class="relative z-[60]">
        {{-- Overlay --}}
        <div
            x-show="$wire.showEditSidebar"
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-primary/40 backdrop-blur-sm"
            wire:click="cancelEdit"
        ></div>

        {{-- Sidebar Panel --}}
        <aside
            x-show="$wire.showEditSidebar"
            x-transition:enter="transform transition ease-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in duration-300"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="fixed top-0 right-0 h-full w-full max-w-[480px] bg-white z-[70] shadow-2xl flex flex-col"
        >
            <header class="p-gutter border-b border-outline-variant flex items-center justify-between bg-surface-container-low">
                <h3 class="font-headline-md text-headline-md text-primary">{{ $editingUserId ? 'Edit User' : 'Add New User' }}</h3>
                <button wire:click="cancelEdit" class="material-symbols-outlined text-on-surface-variant p-2 rounded-full hover:bg-surface-container-high">close</button>
            </header>
            <div class="flex-1 overflow-y-auto p-gutter custom-scrollbar">
                {{-- User Photo Section --}}
                <div class="mb-xl">
                    <label class="block font-label-lg text-primary mb-md">User Photo</label>
                    <div class="flex items-center gap-lg">
                        <div class="relative">
                            @if($editPhoto)
                                <img src="{{ $editPhoto->temporaryUrl() }}" alt="Preview" class="w-32 h-32 rounded-xl object-cover border-4 border-surface-variant shadow-lg">
                            @elseif($existingPhoto)
                                <img src="{{ asset('storage/' . $existingPhoto) }}" alt="{{ $editName }}" class="w-32 h-32 rounded-xl object-cover border-4 border-surface-variant shadow-lg">
                            @else
                                <div class="w-32 h-32 rounded-xl bg-surface-dim flex items-center justify-center font-headline-lg text-on-surface-variant border-4 border-surface-variant shadow-lg">
                                    {{ $editName ? collect(explode(' ', $editName))->map(fn($part) => strtoupper(substr($part, 0, 1)))->take(2)->implode('') : '?' }}
                                </div>
                            @endif
                            <div class="absolute -bottom-2 -right-2 bg-secondary text-white w-10 h-10 rounded-full flex items-center justify-center shadow-md cursor-pointer" onclick="this.closest('.mb-xl').querySelector('input[type=file]').click()">
                                <span class="material-symbols-outlined text-[20px]">photo_camera</span>
                            </div>
                        </div>
                        <div class="space-y-sm">
                            @if($editPhoto)
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-green-700 font-medium">{{ $editPhoto->getClientOriginalName() }}</span>
                                    <button wire:click="$set('editPhoto', null)" class="text-red-500 hover:text-red-700">
                                        <span class="material-symbols-outlined text-sm">close</span>
                                    </button>
                                </div>
                            @elseif($existingPhoto)
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-blue-700 font-medium">Current photo</span>
                                    <button wire:click="$set('existingPhoto', null)" class="text-red-500 hover:text-red-700" title="Remove photo">
                                        <span class="material-symbols-outlined text-sm">close</span>
                                    </button>
                                </div>
                            @else
                                <button type="button" onclick="this.closest('.mb-xl').querySelector('input[type=file]').click()" class="bg-tertiary text-white font-label-md px-md py-3 rounded-lg hover:bg-tertiary-container transition-all flex items-center gap-2">
                                    <span class="material-symbols-outlined">upload</span>
                                    Upload Photo
                                </button>
                            @endif
                            <p class="text-on-surface-variant font-body-md text-sm">PNG, JPG or WebP. Max 2MB.</p>
                            <input accept=".jpg,.jpeg,.png,.webp" class="hidden" type="file" wire:model="editPhoto">
                        </div>
                    </div>
                    @error('editPhoto') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Personal Info Form --}}
                <form wire:submit.prevent="saveEdit" class="space-y-gutter">
                    <div class="space-y-base">
                        <label class="block font-label-md text-on-surface">Full Name</label>
                        <input wire:model="editName" class="w-full h-12 px-4 border border-outline rounded-lg focus:ring-2 focus:ring-secondary font-body-md" type="text">
                        @error('editName') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="space-y-base">
                        <label class="block font-label-md text-on-surface">Email Address</label>
                        <input wire:model="editEmail" class="w-full h-12 px-4 border border-outline rounded-lg focus:ring-2 focus:ring-secondary font-body-md" type="email">
                        @error('editEmail') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="space-y-base">
                        <label class="block font-label-md text-on-surface">Phone Number</label>
                        <input wire:model="editPhone" class="w-full h-12 px-4 border border-outline rounded-lg focus:ring-2 focus:ring-secondary font-body-md" type="tel" placeholder="e.g., +62 812 3456 7890">
                        @error('editPhone') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="space-y-base">
                        <label class="block font-label-md text-on-surface">
                            @if($editingUserId)
                                New Password <span class="text-on-surface-variant text-sm">(leave blank to keep current)</span>
                            @else
                                Password <span class="text-error">*</span>
                            @endif
                        </label>
                        <input wire:model="editPassword" class="w-full h-12 px-4 border border-outline rounded-lg focus:ring-2 focus:ring-secondary font-body-md" type="password" autocomplete="new-password">
                        @error('editPassword') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div class="space-y-base">
                        <label class="block font-label-md text-on-surface">Confirm Password</label>
                        <input wire:model="editPassword_confirmation" class="w-full h-12 px-4 border border-outline rounded-lg focus:ring-2 focus:ring-secondary font-body-md" type="password" autocomplete="new-password">
                        @error('editPassword_confirmation') <span class="text-error text-sm">{{ $message }}</span> @enderror
                    </div>

                    <hr class="border-outline-variant my-xl">

                    {{-- Account Settings --}}
                    <div class="space-y-gutter">
                        <div class="space-y-base">
                            <label class="block font-label-md text-on-surface">Role Selection</label>
                            <div class="grid grid-cols-1 gap-sm">
                                <label class="flex items-center gap-md p-4 border rounded-lg cursor-pointer transition-colors {{ $editRole === 'admin' ? 'border-secondary bg-secondary-container/10' : 'border-outline hover:bg-surface-container-low' }}">
                                    <input wire:model="editRole" class="text-secondary focus:ring-secondary w-5 h-5" name="role" type="radio" value="admin">
                                    <div>
                                        <div class="font-label-lg text-primary">Admin</div>
                                        <div class="text-on-surface-variant text-sm">Full access to all modules and user management.</div>
                                    </div>
                                </label>
                                <label class="flex items-center gap-md p-4 border rounded-lg cursor-pointer transition-colors {{ $editRole === 'staff' ? 'border-secondary bg-secondary-container/10' : 'border-outline hover:bg-surface-container-low' }}">
                                    <input wire:model="editRole" class="text-secondary focus:ring-secondary w-5 h-5" name="role" type="radio" value="staff">
                                    <div>
                                        <div class="font-label-lg text-primary">Staff</div>
                                        <div class="text-on-surface-variant text-sm">Standard access to sales and inventory modules.</div>
                                    </div>
                                </label>
                            </div>
                            @error('editRole') <span class="text-error text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div x-data="{ enabled: @entangle('editStatus') }" class="flex items-center justify-between p-4 bg-surface-container rounded-lg">
                            <div>
                                <div class="font-label-lg text-primary">Account Status</div>
                                <div class="text-on-surface-variant text-sm">Toggle user access to the system.</div>
                            </div>
                            <button
                                type="button"
                                x-on:click="enabled = !enabled"
                                :class="enabled ? 'bg-secondary' : 'bg-outline'"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                            >
                                <span
                                    aria-hidden="true"
                                    :class="enabled ? 'translate-x-5' : 'translate-x-0'"
                                    class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                ></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <footer class="p-gutter border-t border-outline-variant flex items-center gap-md bg-surface-container-low">
                <button wire:click="cancelEdit" class="flex-1 h-12 font-label-lg border border-outline text-on-surface-variant rounded-lg hover:bg-white transition-all">Cancel</button>
                <button wire:click="saveEdit" class="flex-1 h-12 font-label-lg bg-secondary text-white rounded-lg hover:bg-on-secondary-container shadow-md transition-all active:scale-[0.98] shadow-lg">
                    {{ $editingUserId ? 'Save Changes' : 'Create User' }}
                </button>
            </footer>
        </aside>
    </div>
</div>