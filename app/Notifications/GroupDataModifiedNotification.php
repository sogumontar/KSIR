<?php

namespace App\Notifications;

use App\Models\Group;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GroupDataModifiedNotification extends Notification
{
    use Queueable;

    public Group $group;

    public function __construct(Group $group)
    {
        $this->group = $group;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'group_id' => $this->group->id,
            'message' => "Group '{$this->group->name}' data has been modified.",
        ];
    }
}
