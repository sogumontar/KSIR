<?php

namespace App\Notifications;

use App\Models\Group;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AddedToGroupNotification extends Notification
{
    use Queueable;

    public Group $group;
    public User $addedBy;

    /**
     * Create a new notification instance.
     */
    public function __construct(Group $group, User $addedBy)
    {
        $this->group = $group;
        $this->addedBy = $addedBy;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'group_id' => $this->group->id,
            'group_name' => $this->group->name,
            'added_by_name' => $this->addedBy->name,
            'message' => "{$this->addedBy->name} added you to the group \"{$this->group->name}\".",
        ];
    }
}
