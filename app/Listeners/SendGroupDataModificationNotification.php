<?php

namespace App\Listeners;

use App\Events\GroupDataModified;
use App\Notifications\GroupDataModifiedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendGroupDataModificationNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(GroupDataModified $event): void
    {
        $members = $event->group->members;
        Notification::send($members, new GroupDataModifiedNotification($event->group));
    }
}
