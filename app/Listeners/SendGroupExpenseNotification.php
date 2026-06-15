<?php

namespace App\Listeners;

use App\Events\GroupExpenseAdded;
use App\Notifications\ExpenseAddedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendGroupExpenseNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(GroupExpenseAdded $event): void
    {
        $group = $event->expense->group;
        if (!$group) {
            return;
        }

        $members = $group->members()->where('users.id', '!=', $event->addedBy->id)->get();
        Notification::send($members, new ExpenseAddedNotification($event->expense, $event->addedBy));
    }
}
