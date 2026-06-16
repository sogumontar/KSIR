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

    {{-- ====================== SETTLE UP MODAL ====================== --}}
    @if($showSettleModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" wire:click="$set('showSettleModal', false)"></div>
            <div class="bg-white rounded-2xl w-full max-w-md mx-auto shadow-xl relative z-10 overflow-hidden flex flex-col max-h-[90vh]">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="font-headline-md font-bold text-slate-800 flex items-center gap-2">
                        <span class="material-symbols-outlined text-green-600">payments</span>
                        Record Payment
                    </h3>
                    <button wire:click="$set('showSettleModal', false)" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <div class="p-6 overflow-y-auto custom-scrollbar flex-1">
                    <form wire:submit.prevent="saveSettlement" class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Who is paying?</label>
                            <select wire:model="settleDebtorId" class="w-full rounded-xl border-slate-200 focus:border-primary focus:ring focus:ring-primary/20 transition-shadow">
                                <option value="">Select Payer</option>
                                @foreach($group->members as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                                @endforeach
                            </select>
                            @error('settleDebtorId') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex justify-center my-2">
                            <span class="material-symbols-outlined text-slate-300 text-3xl">arrow_downward</span>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Who is receiving?</label>
                            <select wire:model="settleCreditorId" class="w-full rounded-xl border-slate-200 focus:border-primary focus:ring focus:ring-primary/20 transition-shadow">
                                <option value="">Select Receiver</option>
                                @foreach($group->members as $member)
                                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                                @endforeach
                            </select>
                            @error('settleCreditorId') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Amount</label>
                            <div class="relative">
                                <input type="number" step="0.01" wire:model="settleAmount" class="w-full pl-8 pr-4 py-3 rounded-xl border-slate-200 focus:border-primary focus:ring focus:ring-primary/20 transition-shadow font-mono text-lg font-bold" placeholder="0.00">
                            </div>
                            @error('settleAmount') <span class="text-error text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" wire:click="$set('showSettleModal', false)" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg transition-colors text-sm font-medium">Cancel</button>
                            <button type="submit" class="px-3 py-2 bg-primary hover:bg-secondary text-white rounded-lg transition-colors text-sm font-medium">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- ====================== EXPENSE MODAL ====================== --}}
    @if($showExpenseModal)
        {{-- ... (Rest of the modal code) --}}
    @endif

    {{-- ====================== DEBT PODIUM ====================== --}}
    @php
        $debtors   = array_values(array_filter($debtPodium, fn($d) => $d['total_debt'] > 0));
        $balanced  = array_values(array_filter($debtPodium, fn($d) => $d['total_debt'] <= 0));
        $top3      = array_slice($debtors, 0, 3);
        $rest      = array_slice($debtors, 3);

        // Visual podium order: position 0=silver(2nd), 1=gold(1st), 2=bronze(3rd)
        $slots = [
            0 => ['rank'=>2, 'colH'=>'80px',  'bg'=>'#94a3b8', 'label'=>'#334155'],
            1 => ['rank'=>1, 'colH'=>'112px', 'bg'=>'#f59e0b', 'label'=>'#78350f'],
            2 => ['rank'=>3, 'colH'=>'56px',  'bg'=>'#b45309', 'label'=>'#fef3c7'],
        ];
        $animalEmojis = ['🐻','🦊','🐱'];
        $humanEmojis  = ['🧑','👩','🧑‍💼'];
    @endphp

    <div class="mb-8 rounded-2xl border border-slate-200 overflow-hidden shadow-sm" style="background:linear-gradient(160deg,#0f172a 0%,#1e293b 55%,#162032 100%)">

        {{-- Card Header --}}
        <div class="flex items-center justify-between px-6 pt-5 pb-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center" style="background:rgba(251,191,36,0.15)">
                    <span class="material-symbols-outlined" style="color:#fbbf24;font-size:20px">emoji_events</span>
                </div>
                <div>
                    <h3 class="font-bold text-base leading-tight" style="color:#f8fafc">Debt Podium</h3>
                    <p class="text-xs" style="color:#94a3b8">Ranked by outstanding balance</p>
                </div>
            </div>
            <span class="text-xs font-semibold px-2 py-1 rounded-full" style="background:rgba(239,68,68,0.15);color:#fca5a5">
                {{ count($debtors) }} in debt
            </span>
        </div>

        @if(count($debtors) > 0)

            {{-- ---- TOP 3 PODIUM ---- --}}
            <div class="flex items-end justify-center gap-3 px-6 pt-4" style="min-height:220px">
                @foreach($slots as $posIdx => $slot)
                    @php
                        $rank   = $slot['rank'];
                        $person = $top3[$rank - 1] ?? null;
                    @endphp
                    @if($person)
                        <div class="flex flex-col items-center" style="min-width:96px;max-width:112px">

                            {{-- Crown badge for #1 --}}
                            @if($rank === 1)
                                <div class="text-2xl mb-1" title="Top Debtor">👑</div>
                            @else
                                <div style="height:32px"></div>
                            @endif

                            {{-- Animated animal avatar --}}
                            <div class="relative flex items-center justify-center rounded-full mb-2 border-2"
                                 style="
                                     width:64px;height:64px;
                                     background:rgba(255,255,255,0.07);
                                     border-color:{{ $slot['bg'] }};
                                     animation: podiumFloat{{ $rank }} {{ $rank===1?'2':'3' }}s ease-in-out infinite;
                                 ">
                                <span style="font-size:30px;line-height:1">{{ $animalEmojis[$rank-1] }}</span>
                            </div>

                            {{-- Name --}}
                            <p class="text-xs font-semibold text-center mb-0.5 truncate w-full" style="color:#f1f5f9;max-width:96px">
                                {{ $person['name'] }}
                            </p>

                            {{-- Debt amount --}}
                            <p class="text-xs font-bold text-center mb-2" style="color:#fca5a5">
                                Rp{{ number_format($person['total_debt'], 2) }}
                            </p>

                            {{-- Podium block --}}
                            <div class="w-full rounded-t-lg flex items-start justify-center pt-2 relative overflow-hidden"
                                 style="height:{{ $slot['colH'] }};background:{{ $slot['bg'] }}">
                                <span class="font-black text-lg relative" style="z-index:2;color:{{ $slot['label'] }}">#{{ $rank }}</span>
                                <div class="absolute inset-0" style="background:rgba(255,255,255,0.12)"></div>
                            </div>
                        </div>
                    @else
                        {{-- Empty slot placeholder --}}
                        <div style="min-width:96px"></div>
                    @endif
                @endforeach
            </div>

            {{-- ---- RANK 4+ LIST ---- --}}
            @if(count($rest) > 0)
                <div class="mx-6 mt-4 space-y-1.5">
                    @foreach($rest as $idx => $person)
                        <div class="flex items-center justify-between rounded-xl px-4 py-2.5"
                             style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08)">
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-bold w-6 text-right" style="color:#64748b">#{{ $idx + 4 }}</span>
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-base"
                                     style="background:rgba(239,68,68,0.18)">🐾</div>
                                <span class="text-sm font-medium" style="color:#e2e8f0">{{ $person['name'] }}</span>
                            </div>
                            <span class="text-sm font-bold" style="color:#fca5a5">Rp{{ number_format($person['total_debt'], 2) }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

        @else
            {{-- No debtors state --}}
            <div class="flex flex-col items-center justify-center py-10">
                <span class="text-5xl mb-3">🎉</span>
                <p class="font-semibold text-sm" style="color:#94a3b8">Everyone is all settled up!</p>
                <p class="text-xs mt-1" style="color:#475569">Add expenses to start tracking balances.</p>
            </div>
        @endif

        {{-- ---- BALANCED / CREDITORS SECTION ---- --}}
        @if(count($balanced) > 0)
            <div class="mx-6 mt-5 pb-5" style="border-top:1px solid rgba(255,255,255,0.08);padding-top:16px">
                <p class="text-xs font-semibold uppercase tracking-widest mb-3" style="color:#64748b">
                    Settled & Receiving ✅
                </p>
                <div class="flex flex-wrap gap-2">
                    @foreach($balanced as $person)
                        @php $isCredit = $person['total_debt'] < 0; @endphp
                        <div class="flex items-center gap-2 rounded-full px-3 py-1.5"
                             style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.2)">
                            <span class="text-base leading-none">{{ $humanEmojis[array_search($person, $balanced) % 3] }}</span>
                            <span class="text-xs font-semibold" style="color:#6ee7b7">{{ $person['name'] }}</span>
                            @if($isCredit)
                                <span class="text-xs font-bold" style="color:#34d399">
                                    +Rp{{ number_format(abs($person['total_debt']), 2) }}
                                </span>
                            @else
                                <span class="text-xs" style="color:#475569">Balanced</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
    {{-- =================== END DEBT PODIUM =================== --}}

    {{-- CSS keyframes for float animation --}}
    <style>
        @keyframes podiumFloat1 { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-6px)} }
        @keyframes podiumFloat2 { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-4px)} }
        @keyframes podiumFloat3 { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-3px)} }
    </style>



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
                                                {{ $share->user?->name }}: <span class="font-semibold">Rp{{ number_format($share->owed_amount, 2) }}</span>
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-4 shrink-0 justify-between md:justify-end">
                                <div class="text-right">
                                    <span class="text-lg font-bold text-slate-900 block">Rp{{ number_format($expense->amount, 2) }}</span>
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
                                    <span class="text-green-600 font-bold font-mono">+Rp{{ number_format($bal, 2) }}</span>
                                @elseif($bal < 0)
                                    <span class="text-red-500 font-bold font-mono">-Rp{{ number_format(abs($bal), 2) }}</span>
                                @else
                                    <span class="text-slate-400 font-semibold font-mono">Rp0.00</span>
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
                                    <span class="text-slate-900 font-bold block mt-1 font-mono text-base">Rp{{ number_format($inst['amount'], 2) }}</span>
                                </div>
                                <button wire:click="openSettleModal({{ $inst['debtor_id'] }}, {{ $inst['creditor_id'] }}, {{ $inst['amount'] }})" class="mt-1 shrink-0 px-3 py-1.5 bg-green-50 text-green-700 hover:bg-green-100 font-semibold text-xs rounded-lg transition-colors border border-green-200">
                                    Settle Up
                                </button>
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
