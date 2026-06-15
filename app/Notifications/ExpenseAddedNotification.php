<?php

namespace App\Notifications;

use App\Models\GroupExpense;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ExpenseAddedNotification extends Notification
{
    use Queueable;

    public GroupExpense $expense;
    public User $addedBy;

    /**
     * Create a new notification instance.
     */
    public function __construct(GroupExpense $expense, User $addedBy)
    {
        $this->expense = $expense;
        $this->addedBy = $addedBy;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database']; // can extend to mail later
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'expense_id' => $this->expense->id,
            'group_id' => $this->expense->group_id,
            'description' => $this->expense->description,
            'amount' => $this->expense->amount,
            'added_by' => $this->addedBy->name,
            'message' => "{$this->addedBy->name} added an expense '{$this->expense->description}' of {$this->expense->amount}.",
        ];
    }
}
