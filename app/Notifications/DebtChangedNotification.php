<?php

namespace App\Notifications;

use App\Models\Group;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DebtChangedNotification extends Notification
{
    use Queueable;

    public Group $group;
    public string $reason;

    public function __construct(Group $group, string $reason = '')
    {
        $this->group = $group;
        $this->reason = $reason;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'group_id' => $this->group->id,
            'reason' => $this->reason,
            'message' => "Your debt balance in group '{$this->group->name}' has been updated.",
        ];
    }
}
