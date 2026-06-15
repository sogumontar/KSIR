<?php

namespace App\Livewire\User\Groups;

use App\Models\Group;
use App\Models\GroupMember;
use App\Models\GroupExpense;
use App\Models\GroupExpenseShare;
use App\Models\User;
use App\Notifications\AddedToGroupNotification;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('components.layouts.user')]
#[Title('Group Details - Inventory Pro')]
class GroupDetail extends Component
{
    public int $groupId;
    
    // Group settings
    public string $groupName = '';
    public bool $isEditingName = false;

    // Member management
    public string $newMemberInput = ''; // can be ID or unique_code
    
    // Expense form
    public string $description = '';
    public string $amount = '';
    public string $splitMode = 'equal';
    public ?int $paidBy = null;
    public string $expenseDate = '';
    public array $selectedMembers = []; // array of user IDs
    public array $exactAmounts = []; // user ID => amount (string)
    public array $chartData = [];
    
    public bool $showExpenseModal = false;

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function mount(int $id)
    {
        $this->groupId = $id;
        $group = Group::findOrFail($id);

        // Check membership
        $isMember = GroupMember::where('group_id', $id)
            ->where('user_id', auth()->id())
            ->exists();

        if (!$isMember) {
            abort(403, 'You are not a member of this group.');
        }

        $this->groupName = $group->name;
        $this->expenseDate = date('Y-m-d');
        $this->paidBy = auth()->id();
        
        // Default selected members to all group members
        $this->selectedMembers = $group->members->pluck('id')->toArray();
    }

    public function saveGroupName()
    {
        $this->validate([
            'groupName' => 'required|string|min:3|max:100',
        ]);

        $group = Group::findOrFail($this->groupId);
        $group->update([
            'name' => $this->groupName,
        ]);

        $this->isEditingName = false;
        session()->flash('success', 'Group name updated.');
    }

    public function addMember()
    {
        $this->validate([
            'newMemberInput' => 'required|string',
        ]);

        $input = trim($this->newMemberInput);

        // Search by unique_code first, then try numeric ID
        $user = User::where('unique_code', $input)->first();
        if (!$user && is_numeric($input)) {
            $user = User::find((int)$input);
        }

        if (!$user) {
            $this->addError('newMemberInput', 'User not found. Try searching by their unique code or ID.');
            return;
        }

        // Check if already in group
        $exists = GroupMember::where('group_id', $this->groupId)
            ->where('user_id', $user->id)
            ->exists();

        if ($exists) {
            $this->addError('newMemberInput', 'This user is already a member of the group.');
            return;
        }

        GroupMember::create([
            'group_id' => $this->groupId,
            'user_id' => $user->id,
        ]);

        // Send database notification
        $user->notify(new AddedToGroupNotification(Group::findOrFail($this->groupId), auth()->user()));

        $this->reset('newMemberInput');
        
        // Refresh selected members
        $group = Group::findOrFail($this->groupId);
        $this->selectedMembers = $group->members->pluck('id')->toArray();

        session()->flash('success', "{$user->name} was added to the group.");
        $this->dispatch('spender-updated');
    }

    public function removeMember(int $userId)
    {
        // Creator cannot be easily removed or check if leaving
        GroupMember::where('group_id', $this->groupId)
            ->where('user_id', $userId)
            ->delete();

        // Refresh selected members
        $group = Group::findOrFail($this->groupId);
        $this->selectedMembers = $group->members->pluck('id')->toArray();

        session()->flash('success', 'Member removed from group.');
        $this->dispatch('spender-updated');
    }

    public function leaveGroup()
    {
        GroupMember::where('group_id', $this->groupId)
            ->where('user_id', auth()->id())
            ->delete();

        return redirect()->route('user.groups')
            ->with('success', 'You have left the group.');
    }

    public function openExpenseModal()
    {
        $group = Group::findOrFail($this->groupId);
        $this->reset(['description', 'amount', 'splitMode']);
        $this->expenseDate = date('Y-m-d');
        $this->paidBy = auth()->id();
        $this->selectedMembers = $group->members->pluck('id')->toArray();
        $this->exactAmounts = [];
        $this->showExpenseModal = true;
    }

    public function saveExpense()
    {
        $this->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|gt:0',
            'splitMode' => 'required|in:equal,exact',
            'paidBy' => 'required|exists:users,id',
            'expenseDate' => 'required|date',
            'selectedMembers' => 'required|array|min:1',
        ]);

        $totalAmount = (float)$this->amount;

        if ($this->splitMode === 'exact') {
            $sum = 0;
            foreach ($this->selectedMembers as $userId) {
                $sum += (float)($this->exactAmounts[$userId] ?? 0);
            }
            // Use round to avoid floating point precision issues
            if (round($sum, 2) !== round($totalAmount, 2)) {
                $this->addError('amount', 'The sum of exact amounts (' . number_format($sum, 2) . ') must equal the total expense amount (' . number_format($totalAmount, 2) . ').');
                return;
            }
        }

        // Save GroupExpense
        $expense = GroupExpense::create([
            'group_id' => $this->groupId,
            'paid_by' => $this->paidBy,
            'description' => $this->description,
            'amount' => $totalAmount,
            'split_mode' => $this->splitMode,
            'expense_date' => $this->expenseDate,
        ]);

        // Save shares
        if ($this->splitMode === 'equal') {
            $shareCount = count($this->selectedMembers);
            $equalShare = round($totalAmount / $shareCount, 2);
            
            // Adjust the last person's share for rounding discrepancies
            $accumulated = 0;
            
            foreach ($this->selectedMembers as $index => $userId) {
                if ($index === $shareCount - 1) {
                    $owed = $totalAmount - $accumulated;
                } else {
                    $owed = $equalShare;
                    $accumulated += $owed;
                }

                GroupExpenseShare::create([
                    'group_expense_id' => $expense->id,
                    'user_id' => $userId,
                    'owed_amount' => $owed,
                ]);
            }
        } else {
            foreach ($this->selectedMembers as $userId) {
                $owed = (float)($this->exactAmounts[$userId] ?? 0);
                GroupExpenseShare::create([
                    'group_expense_id' => $expense->id,
                    'user_id' => $userId,
                    'owed_amount' => $owed,
                ]);
            }
        }

        $this->showExpenseModal = false;
        session()->flash('success', 'Expense added successfully.');
        $this->dispatch('spender-updated');
    }

    public function deleteExpense(int $expenseId)
    {
        GroupExpense::findOrFail($expenseId)->delete();
        session()->flash('success', 'Expense deleted.');
        $this->dispatch('spender-updated');
    }

    // Settlement Summary engine
    public function getSettlementsProperty()
    {
        $group = Group::findOrFail($this->groupId);
        $members = $group->members;
        
        $balances = [];
        foreach ($members as $member) {
            $balances[$member->id] = 0.0;
        }

        // Fetch all expenses in this group with shares
        $expenses = GroupExpense::where('group_id', $this->groupId)
            ->with('shares')
            ->get();

        foreach ($expenses as $expense) {
            // Credit the payer
            if (isset($balances[$expense->paid_by])) {
                $balances[$expense->paid_by] += (float)$expense->amount;
            }
            
            // Debit the participants
            foreach ($expense->shares as $share) {
                if (isset($balances[$share->user_id])) {
                    $balances[$share->user_id] -= (float)$share->owed_amount;
                }
            }
        }

        // Greedy matching algorithm
        $debtors = [];
        $creditors = [];

        foreach ($balances as $userId => $balance) {
            // Round to 2 decimal places to avoid float errors
            $balance = round($balance, 2);
            if ($balance < 0) {
                $debtors[$userId] = abs($balance);
            } elseif ($balance > 0) {
                $creditors[$userId] = $balance;
            }
        }

        // Sort to optimize greedy pairings
        arsort($debtors);
        arsort($creditors);

        $instructions = [];
        $memberMap = $members->keyBy('id');

        while (!empty($debtors) && !empty($creditors)) {
            reset($debtors);
            $debtorId = key($debtors);
            $debtAmt = current($debtors);

            reset($creditors);
            $creditorId = key($creditors);
            $credAmt = current($creditors);

            $settleAmt = min($debtAmt, $credAmt);

            $instructions[] = [
                'debtor_id' => $debtorId,
                'debtor_name' => $memberMap[$debtorId]?->name ?? 'Unknown',
                'creditor_id' => $creditorId,
                'creditor_name' => $memberMap[$creditorId]?->name ?? 'Unknown',
                'amount' => $settleAmt,
            ];

            $debtors[$debtorId] -= $settleAmt;
            $creditors[$creditorId] -= $settleAmt;

            if (round($debtors[$debtorId], 2) <= 0) {
                unset($debtors[$debtorId]);
            }
            if (round($creditors[$creditorId], 2) <= 0) {
                unset($creditors[$creditorId]);
            }
        }

        return [
            'balances' => $balances,
            'instructions' => $instructions,
        ];
    }

    // Aggregate spendings for Chart.js
    public function calculateChartData()
    {
        $group = Group::findOrFail($this->groupId);
        $members = $group->members;

        $payments = [];
        foreach ($members as $member) {
            $payments[$member->name] = 0.0;
        }

        $expenses = GroupExpense::where('group_id', $this->groupId)
            ->with('payer')
            ->get();

        foreach ($expenses as $expense) {
            $name = $expense->payer?->name ?? 'Unknown';
            if (isset($payments[$name])) {
                $payments[$name] += (float)$expense->amount;
            } else {
                $payments[$name] = (float)$expense->amount;
            }
        }

        return [
            'labels' => array_keys($payments),
            'values' => array_values($payments),
        ];
    }

    public function render()
    {
        $group = Group::with('members')->findOrFail($this->groupId);
        
        $expenses = GroupExpense::where('group_id', $this->groupId)
            ->with(['payer', 'shares.user'])
            ->latest()
            ->get();

        $settlementsData = $this->settlements;
        $this->chartData = $this->calculateChartData();

        return view('livewire.user.groups.group-detail', [
            'group' => $group,
            'expenses' => $expenses,
            'balances' => $settlementsData['balances'],
            'instructions' => $settlementsData['instructions'],
        ]);
    }
}
