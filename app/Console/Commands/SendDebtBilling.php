<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\GroupExpense;
use App\Notifications\DebtBillingNotification;
use Illuminate\Console\Command;

class SendDebtBilling extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debt:billing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aggregate debts and send billing notifications on the 25th';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::all();

        foreach ($users as $user) {
            $totalDebt = 0.0;
            $groupDebts = [];

            // Fetch all expenses related to the user's groups
            $groups = $user->joinedGroups;
            foreach ($groups as $group) {
                $balance = 0.0;
                $expenses = GroupExpense::where('group_id', $group->id)->with('shares')->get();
                
                foreach ($expenses as $expense) {
                    if ($expense->paid_by === $user->id) {
                        $balance += (float)$expense->amount;
                    }
                    foreach ($expense->shares as $share) {
                        if ($share->user_id === $user->id) {
                            $balance -= (float)$share->owed_amount;
                        }
                    }
                }

                if ($balance < 0) {
                    $debt = abs($balance);
                    $totalDebt += $debt;
                    $groupDebts[] = [
                        'group_name' => $group->name,
                        'amount' => $debt
                    ];
                }
            }

            if ($totalDebt > 0) {
                $user->notify(new DebtBillingNotification($totalDebt, $groupDebts));
                $this->info("Sent billing to {$user->name} for \${$totalDebt}");
            }
        }
    }
}
