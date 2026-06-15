<div>
    {{-- Breadcrumb & Back --}}
    <div class="mb-6">
        <a href="{{ route('user.groups') }}" wire:navigate class="text-sm text-slate-500 hover:text-primary transition-colors flex items-center gap-1 font-medium">
            <span class="material-symbols-outlined text-base">arrow_back</span>
            Back to Split Groups
        </a>
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

    {{-- Group Header & Name Editor --}}
    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex-1">
            @if($isEditingName)
                <form wire:submit.prevent="saveGroupName" class="flex items-center gap-2 max-w-md">
                    <input wire:model="groupName" type="text" class="form-input bg-white w-full">
                    <button type="submit" class="px-3 py-2 bg-primary hover:bg-secondary text-white rounded-lg transition-colors text-sm font-medium flex items-center shrink-0">
                        Save
                    </button>
                    <button type="button" wire:click="$set('isEditingName', false)" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg transition-colors text-sm font-medium shrink-0">
                        Cancel
                    </button>
                </form>
            @else
                <div class="flex items-center gap-3">
                    <h2 class="font-headline-lg text-headline-lg text-slate-900 m-0">{{ $group->name }}</h2>
                    <button wire:click="$set('isEditingName', true)" class="p-1 text-slate-400 hover:text-primary transition-colors rounded-full" title="Rename group">
                        <span class="material-symbols-outlined text-lg">edit</span>
                    </button>
                </div>
                <p class="text-slate-500 text-sm mt-1">Invitation Token: <span class="font-mono bg-slate-50 px-2 py-0.5 rounded border border-slate-100 font-bold select-all">{{ $group->invite_token }}</span></p>
            @endif
        </div>
        <div class="flex gap-2">
            <button wire:click="openExpenseModal" class="btn-primary gap-2">
                <span class="material-symbols-outlined">add_card</span>
                Add Expense
            </button>
        </div>
    </div>

    {{-- Dashboard Grid Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Left 2 Columns: Analytics & Expenses Log --}}
        <div class="lg:col-span-2 space-y-8">
            
            {{-- Spender Analytics Chart --}}
            <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
                <h3 class="font-headline-md text-headline-md text-slate-900 mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">bar_chart</span>
                    Spending footprint (Who Paid)
                </h3>
                <div 
                    x-data="{
                        chart: null,
                        initChart() {
                            if (typeof Chart === 'undefined') {
                                setTimeout(() => this.initChart(), 100);
                                return;
                            }
                            if (this.chart) this.chart.destroy();
                            const ctx = document.getElementById('spenderChart');
                            if (!ctx) return;
                            
                            const data = $wire.chartData;
                            const labels = data ? (data.labels || []) : [];
                            const values = data ? (data.values || []) : [];

                            this.chart = new Chart(ctx.getContext('2d'), {
                                type: 'bar',
                                data: {
                                    labels: labels,
                                    datasets: [{
                                        label: 'Amount Paid ($)',
                                        data: values,
                                        backgroundColor: 'rgba(59, 130, 246, 0.75)',
                                        borderColor: 'rgb(59, 130, 246)',
                                        borderWidth: 1,
                                        borderRadius: 6,
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: { display: false }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            grid: { color: '#F1F5F9' },
                                            ticks: { color: '#64748B' }
                                        },
                                        x: {
                                            grid: { display: false },
                                            ticks: { color: '#64748B' }
                                        }
                                    }
                                }
                            });
                        }
                    }" 
                    x-init="initChart()"
                    @spender-updated.window="initChart()"
                    wire:ignore
                    class="h-[260px] relative"
                >
                    <canvas id="spenderChart"></canvas>
                </div>
            </div>

            {{-- Expenses Log --}}
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex justify-between items-center">
                    <h3 class="font-headline-md text-headline-md text-slate-900 m-0 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">receipt_long</span>
                        Group Expenses Log
                    </h3>
                    <span class="text-xs text-slate-500 font-medium">{{ $expenses->count() }} expenses</span>
                </div>
                
                <div class="divide-y divide-slate-100">
                    @forelse($expenses as $expense)
                        <div class="p-6 hover:bg-slate-50 transition-colors flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-semibold text-slate-900 text-base">{{ $expense->description }}</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold capitalize {{ $expense->split_mode === 'equal' ? 'bg-indigo-50 text-indigo-700' : 'bg-amber-50 text-amber-700' }}">
                                        {{ $expense->split_mode }} split
                                    </span>
                                </div>
                                <div class="text-sm text-slate-500 space-y-1">
                                    <p>Paid by <strong class="text-slate-700 font-medium">{{ $expense->payer?->name }}</strong> on {{ $expense->expense_date->format('M d, Y') }}</p>
                                    
                                    {{-- Participant shares inline --}}
                                    <div class="flex flex-wrap gap-1.5 mt-2 pt-1 border-t border-slate-100">
                                        <span class="text-xs text-slate-400 font-medium self-center mr-1">Involved:</span>
                                        @foreach($expense->shares as $share)
                                            <span class="inline-flex items-center gap-1 bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full text-xs" title="{{ $share->user?->name }}">
                                                {{ $share->user?->name }}: <span class="font-semibold">${{ number_format($share->owed_amount, 2) }}</span>
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-4 shrink-0 justify-between md:justify-end">
                                <div class="text-right">
                                    <span class="text-lg font-bold text-slate-900 block">${{ number_format($expense->amount, 2) }}</span>
                                </div>
                                @if($expense->paid_by === auth()->id() || $group->created_by === auth()->id())
                                    <button 
                                        wire:click="deleteExpense({{ $expense->id }})" 
                                        wire:confirm="Are you sure you want to delete this expense? Outstanding balances will automatically recalculate."
                                        class="p-2 text-slate-400 hover:text-red-600 transition-colors rounded-lg"
                                        title="Delete expense"
                                    >
                                        <span class="material-symbols-outlined">delete</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-500">
                            <span class="material-symbols-outlined text-4xl text-slate-300 mb-2">payments</span>
                            <p class="text-sm">No expenses recorded yet. Click "Add Expense" to get started.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Right 1 Column: Members, Balances & Settlements --}}
        <div class="space-y-8">
            
            {{-- Net Balances & Settlements Summary --}}
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                    <h3 class="font-headline-md text-headline-md text-slate-900 m-0 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">account_balance_wallet</span>
                        Balances & Settlements
                    </h3>
                </div>
                
                {{-- Balances list --}}
                <div class="p-6 border-b border-slate-100">
                    <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4">Member Balances</h4>
                    <div class="space-y-3">
                        @foreach($group->members as $member)
                            @php
                                $bal = $balances[$member->id] ?? 0.0;
                            @endphp
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-slate-700 font-medium">{{ $member->name }}</span>
                                @if($bal > 0)
                                    <span class="text-green-600 font-bold font-mono">+${{ number_format($bal, 2) }}</span>
                                @elseif($bal < 0)
                                    <span class="text-red-500 font-bold font-mono">-${{ number_format(abs($bal), 2) }}</span>
                                @else
                                    <span class="text-slate-400 font-semibold font-mono">$0.00</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- greedy matching settlement instructions --}}
                <div class="p-6 bg-slate-50/50">
                    <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4">Who Owes Whom</h4>
                    <div class="space-y-3">
                        @forelse($instructions as $inst)
                            <div class="p-3 bg-white border border-slate-100 rounded-lg shadow-sm flex items-start gap-2.5 text-sm">
                                <span class="material-symbols-outlined text-slate-400 text-sm mt-0.5">swap_horiz</span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-slate-700 font-medium">
                                        <span class="font-bold text-red-500">{{ $inst['debtor_name'] }}</span> 
                                        owes 
                                        <span class="font-bold text-green-600">{{ $inst['creditor_name'] }}</span>
                                    </p>
                                    <span class="text-slate-900 font-bold block mt-1 font-mono text-base">${{ number_format($inst['amount'], 2) }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-slate-400 text-xs">
                                All balances are currently settled!
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Member Management --}}
            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                    <h3 class="font-headline-md text-headline-md text-slate-900 m-0 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">group</span>
                        Group Members
                    </h3>
                </div>

                {{-- Add member form --}}
                <div class="p-6 border-b border-slate-100">
                    <form wire:submit.prevent="addMember" class="flex gap-2">
                        <input wire:model="newMemberInput" type="text" class="form-input bg-white w-full text-sm" placeholder="Enter Unique Code or ID">
                        <button type="submit" class="px-3 py-2 bg-primary hover:bg-secondary text-white rounded-lg text-sm font-medium transition-colors flex items-center justify-center shrink-0">
                            Add
                        </button>
                    </form>
                    @error('newMemberInput') <span class="text-error text-xs block mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Members List --}}
                <div class="divide-y divide-slate-100">
                    @foreach($group->members as $member)
                        <div class="px-6 py-4 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                @if($member->avatar)
                                    <img src="{{ $member->avatar }}" alt="{{ $member->name }}" class="w-8 h-8 rounded-full object-cover">
                                @else
                                    <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center font-bold text-slate-600 text-xs">
                                        {{ strtoupper(substr($member->name, 0, 2)) }}
                                    </div>
                                @endif
                                <div>
                                    <span class="font-medium text-slate-900 text-sm block">{{ $member->name }}</span>
                                    <span class="text-slate-400 text-xs block">Code: {{ $member->unique_code ?? $member->id }}</span>
                                </div>
                            </div>
                            
                            @if($member->id !== auth()->id())
                                {{-- Show remove button to group members/creator --}}
                                <button 
                                    wire:click="removeMember({{ $member->id }})" 
                                    wire:confirm="Are you sure you want to remove {{ $member->name }} from the group?"
                                    class="p-1 text-slate-300 hover:text-red-600 transition-colors rounded"
                                    title="Remove from group"
                                >
                                    <span class="material-symbols-outlined text-lg">person_remove</span>
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="p-6 bg-slate-50 border-t border-slate-100 text-center">
                    <button 
                        wire:click="leaveGroup" 
                        wire:confirm="Are you sure you want to leave this group?" 
                        class="text-xs text-red-600 font-semibold hover:text-red-700 transition-colors flex items-center gap-1 mx-auto"
                    >
                        <span class="material-symbols-outlined text-sm">logout</span>
                        Leave Group
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Expense Modal --}}
    <div 
        x-data="{ open: @entangle('showExpenseModal'), splitMode: @entangle('splitMode') }" 
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
            class="bg-white rounded-xl shadow-xl border border-slate-200 max-w-lg w-full overflow-hidden"
            x-transition:enter="transition ease-out duration-300 transform scale-95"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200 transform scale-100"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
        >
            <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                <h3 class="font-headline-md text-headline-md text-slate-900 m-0">Add Expense</h3>
                <button @click="open = false" class="text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <form wire:submit.prevent="saveExpense" class="p-6 space-y-4 max-h-[75vh] overflow-y-auto custom-scrollbar">
                
                {{-- Description --}}
                <div>
                    <label class="form-label">Description <span class="text-error">*</span></label>
                    <input wire:model="description" type="text" class="form-input bg-white w-full" placeholder="e.g. Grocery, Lunch bill">
                    @error('description') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Amount & Paid By --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Total Amount ($) <span class="text-error">*</span></label>
                        <input wire:model="amount" type="number" step="0.01" min="0.01" class="form-input bg-white w-full" placeholder="0.00">
                        @error('amount') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="form-label">Paid By <span class="text-error">*</span></label>
                        <select wire:model="paidBy" class="form-input bg-white w-full">
                            @foreach($group->members as $member)
                                <option value="{{ $member->id }}">{{ $member->name }}</option>
                            @endforeach
                        </select>
                        @error('paidBy') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Date & Split Mode --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Date <span class="text-error">*</span></label>
                        <input wire:model="expenseDate" type="date" class="form-input bg-white w-full">
                        @error('expenseDate') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="form-label">Split Calculation <span class="text-error">*</span></label>
                        <select wire:model="splitMode" x-model="splitMode" class="form-input bg-white w-full">
                            <option value="equal">Split Equally</option>
                            <option value="exact">Split by Exact Amounts</option>
                        </select>
                        @error('splitMode') <span class="text-error text-sm block mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Participants Split Checklist --}}
                <div class="border-t border-slate-100 pt-4">
                    <label class="form-label mb-2 block font-semibold text-slate-800">Select participating members:</label>
                    @error('selectedMembers') <span class="text-error text-sm block mb-2">{{ $message }}</span> @enderror
                    
                    <div class="space-y-3 bg-slate-50 p-4 rounded-xl border border-slate-100">
                        @foreach($group->members as $member)
                            <div class="flex items-center justify-between gap-4">
                                <label class="flex items-center gap-3 cursor-pointer text-slate-700 text-sm font-medium">
                                    <input type="checkbox" value="{{ $member->id }}" wire:model.live="selectedMembers" class="rounded border-slate-300 text-primary focus:ring-primary w-4 h-4">
                                    <span>{{ $member->name }}</span>
                                </label>
                                
                                {{-- exact split amount inputs --}}
                                <div x-show="splitMode === 'exact'" class="w-32" x-transition>
                                    @if(in_array($member->id, $selectedMembers))
                                        <div class="relative rounded-lg shadow-sm">
                                            <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                                <span class="text-slate-400 text-xs">$</span>
                                            </div>
                                            <input 
                                                type="number" 
                                                step="0.01" 
                                                min="0.00" 
                                                wire:model.live="exactAmounts.{{ $member->id }}" 
                                                class="form-input pl-6 w-full text-right bg-white text-xs" 
                                                placeholder="0.00"
                                            >
                                        </div>
                                    @else
                                        <span class="text-slate-400 text-xs italic block text-right">Excluded</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-100">
                    <button type="button" @click="open = false" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-lg hover:bg-slate-50 transition-colors text-sm font-medium">Cancel</button>
                    <button type="submit" class="btn-primary gap-2">
                        <span class="material-symbols-outlined">save</span>
                        Save Expense
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
