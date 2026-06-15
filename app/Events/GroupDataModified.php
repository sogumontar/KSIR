<?php

namespace App\Events;

use App\Models\Group;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GroupDataModified
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Group $group;

    public function __construct(Group $group)
    {
        $this->group = $group;
    }
}
