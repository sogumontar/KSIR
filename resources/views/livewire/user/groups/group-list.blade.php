<div>
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-8">
        <div>
            <h2 class="font-headline-lg text-headline-lg text-slate-900 m-0">Split Groups</h2>
            <p class="text-slate-500 text-sm mt-1">Manage shared expenses, split bills, and track who owes whom.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button wire:click="$set('showJoinModal', true)" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-lg text-sm transition-colors flex items-center gap-2 border border-slate-200">
                <span class="material-symbols-outlined text-base">group_add</span>
                Join Group
            </button>
            <button wire:click="$set('showCreateModal', true)" class="btn-primary gap-2">
                <span class="material-symbols-outlined">add</span>
                Create Group
            </button>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session()->has('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-center gap-2">
            <span class="material-symbols-outlined text-green-600">check_circle</span>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif
    @if(session()->has('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg flex items-center gap-2">
            <span class="material-symbols-outlined text-red-600">error</span>
            <span class="text-sm font-medium">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Groups Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($groups as $group)
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden flex flex-col group">
                <div class="p-6 flex-1">
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center text-primary">
                            <span class="material-symbols-outlined text-2xl">groups</span>
                        </div>
                        <span class="text-xs text-slate-400 font-medium">Created by {{ $group->creator?->id === auth()->id() ? 'You' : $group->creator?->name }}</span>
                    </div>
                    
                    <h3 class="font-headline-md text-headline-md text-slate-900 mb-2 group-hover:text-primary transition-colors">
                        <a href="{{ route('user.group-detail', $group->id) }}" wire:navigate>{{ $group->name }}</a>
                    </h3>
                    
                    <div class="space-y-2 mt-4 text-sm text-slate-600">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-slate-400 text-sm">group</span>
                            <span>{{ $group->members->count() }} members</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-slate-400 text-sm">receipt_long</span>
                            <span>{{ $group->expenses_count }} expenses logged</span>
                        </div>
                    </div>

                    {{-- Members Avatars Stack --}}
                    <div class="flex -space-x-2 overflow-hidden mt-6">
                        @foreach($group->members->take(5) as $member)
                            @if($member->avatar)
                                <img class="inline-block h-8 w-8 rounded-full ring-2 ring-white object-cover" src="{{ $member->avatar }}" alt="{{ $member->name }}" title="{{ $member->name }}">
                            @else
                                <div class="inline-block h-8 w-8 rounded-full ring-2 ring-white bg-slate-200 flex items-center justify-center text-xs font-bold text-slate-600" title="{{ $member->name }}">
                                    {{ strtoupper(substr($member->name, 0, 2)) }}
                                </div>
                            @endif
                        @endforeach
                        @if($group->members->count() > 5)
                            <div class="inline-block h-8 w-8 rounded-full ring-2 ring-white bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-500">
                                +{{ $group->members->count() - 5 }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-4">
                    <button 
                        onclick="navigator.clipboard.writeText('{{ route('user.groups.join', $group->invite_token) }}'); alert('Invite link copied to clipboard!');"
                        class="text-xs text-slate-500 hover:text-primary transition-colors flex items-center gap-1.5"
                        title="Copy invite link"
                    >
                        <span class="material-symbols-outlined text-base">link</span>
                        Copy Invite Link
                    </button>
                    <a href="{{ route('user.group-detail', $group->id) }}" wire:navigate class="text-xs text-primary font-semibold hover:text-secondary transition-colors flex items-center gap-1">
                        View Details
                        <span class="material-symbols-outlined text-xs">arrow_forward</span>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white border border-slate-200 rounded-xl p-12 text-center">
                <span class="material-symbols-outlined text-6xl text-slate-300 mb-4">account_balance_wallet</span>
                <h3 class="text-lg font-bold text-slate-900 mb-1">No Groups Yet</h3>
                <p class="text-slate-500 text-sm max-w-sm mx-auto mb-6">Create a group to start sharing expenses with friends, roommates, or colleagues.</p>
                <div class="flex justify-center gap-3">
                    <button wire:click="$set('showCreateModal', true)" class="btn-primary gap-2">
                        <span class="material-symbols-outlined">add</span>
                        Create Group
                    </button>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Create Group Modal --}}
    <div 
        x-data="{ open: @entangle('showCreateModal') }" 
        x-show="open" 
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak
    >
        <div 
            @click.away="open = false" 
            class="bg-white rounded-xl shadow-xl border border-slate-200 max-w-md w-full overflow-hidden"
            x-transition:enter="transition ease-out duration-300 transform scale-95"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200 transform scale-100"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
        >
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                <h3 class="font-headline-md text-headline-md text-slate-900 m-0">Create Split Group</h3>
                <button @click="open = false" class="text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form wire:submit.prevent="createGroup" class="p-6 space-y-4">
                <div>
                    <label class="form-label">Group Name <span class="text-error">*</span></label>
                    <input wire:model="name" class="form-input w-full bg-white border-slate-200" type="text" placeholder="e.g. Roommates, Weekend Trip">
                    @error('name') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
                </div>
                
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-100">
                    <button type="button" @click="open = false" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-lg hover:bg-slate-50 transition-colors text-sm font-medium">Cancel</button>
                    <button type="submit" class="btn-primary gap-2">
                        <span class="material-symbols-outlined">save</span>
                        Create Group
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Join Group Modal --}}
    <div 
        x-data="{ open: @entangle('showJoinModal') }" 
        x-show="open" 
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak
    >
        <div 
            @click.away="open = false" 
            class="bg-white rounded-xl shadow-xl border border-slate-200 max-w-md w-full overflow-hidden"
            x-transition:enter="transition ease-out duration-300 transform scale-95"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200 transform scale-100"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
        >
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                <h3 class="font-headline-md text-headline-md text-slate-900 m-0">Join Group via Code</h3>
                <button @click="open = false" class="text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form wire:submit.prevent="joinGroup" class="p-6 space-y-4">
                <div>
                    <label class="form-label">Invite Code <span class="text-error">*</span></label>
                    <input wire:model="joinCode" class="form-input w-full bg-white border-slate-200" type="text" placeholder="Enter invite token or link code">
                    <p class="text-xs text-slate-500 mt-1">Paste the code (UUID) or copy-paste the last segment of the invite URL.</p>
                    @error('joinCode') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
                </div>
                
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-100">
                    <button type="button" @click="open = false" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-lg hover:bg-slate-50 transition-colors text-sm font-medium">Cancel</button>
                    <button type="submit" class="btn-primary gap-2">
                        <span class="material-symbols-outlined">group_add</span>
                        Join Group
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
