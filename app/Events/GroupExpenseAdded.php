<?php

namespace App\Events;

use App\Models\GroupExpense;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GroupExpenseAdded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public GroupExpense $expense;
    public User $addedBy;

    public function __construct(GroupExpense $expense, User $addedBy)
    {
        $this->expense = $expense;
        $this->addedBy = $addedBy;
    }
}
