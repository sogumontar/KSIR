<?php

namespace App\Listeners;

use App\Events\GroupDebtMutated;
use App\Notifications\DebtChangedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendGroupDebtMutationNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(GroupDebtMutated $event): void
    {
        $event->user->notify(new DebtChangedNotification($event->group, $event->reason));
    }
}
