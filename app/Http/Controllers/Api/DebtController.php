<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\GroupExpense;
use Illuminate\Http\Request;

class DebtController extends Controller
{
    public function index($groupId)
    {
        $group = Group::with('members')->findOrFail($groupId);

        $balances = [];
        foreach ($group->members as $member) {
            $balances[$member->id] = 0.0;
        }

        $expenses = GroupExpense::where('group_id', $groupId)
            ->with('shares')
            ->get();

        foreach ($expenses as $expense) {
            if (isset($balances[$expense->paid_by])) {
                $balances[$expense->paid_by] += (float)$expense->amount;
            }
            foreach ($expense->shares as $share) {
                if (isset($balances[$share->user_id])) {
                    $balances[$share->user_id] -= (float)$share->owed_amount;
                }
            }
        }

        $results = [];
        foreach ($group->members as $member) {
            // Net balance. Negative means debt. 
            // The prompt says "total_debt > 0" for people in debt. So total_debt = -balance.
            $debt = -round($balances[$member->id], 2);
            $results[] = [
                'user_id' => $member->id,
                'name' => $member->name,
                'total_debt' => $debt
            ];
        }

        // Sort descending by debt quantity
        usort($results, function ($a, $b) {
            return $b['total_debt'] <=> $a['total_debt'];
        });

        return response()->json($results);
    }
}
