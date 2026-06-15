<?php

namespace App\Events;

use App\Models\Group;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GroupDebtMutated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Group $group;
    public User $user;
    public string $reason;

    public function __construct(Group $group, User $user, string $reason = '')
    {
        $this->group = $group;
        $this->user = $user;
        $this->reason = $reason;
    }
}
