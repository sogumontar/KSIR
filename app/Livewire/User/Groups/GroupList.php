<?php

namespace App\Livewire\User\Groups;

use App\Models\Group;
use App\Models\GroupMember;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Str;

#[Layout('components.layouts.user')]
#[Title('Split Groups - Inventory Pro')]
class GroupList extends Component
{
    public string $name = '';
    public string $joinCode = '';
    
    public bool $showCreateModal = false;
    public bool $showJoinModal = false;

    protected $rules = [
        'name' => 'required|string|min:3|max:100',
    ];

    public function createGroup()
    {
        $this->validate();

        $group = Group::create([
            'name' => $this->name,
            'created_by' => auth()->id(),
        ]);

        // Auto-join creator
        GroupMember::create([
            'group_id' => $group->id,
            'user_id' => auth()->id(),
        ]);

        $this->reset(['name', 'showCreateModal']);
        session()->flash('success', 'Group created successfully.');
    }

    public function joinGroup()
    {
        $this->validate([
            'joinCode' => 'required|string',
        ]);

        // Find group by invite token (UUID or token string)
        $group = Group::where('invite_token', trim($this->joinCode))->first();

        if (!$group) {
            $this->addError('joinCode', 'No group found with this invite code.');
            return;
        }

        $isMember = GroupMember::where('group_id', $group->id)
            ->where('user_id', auth()->id())
            ->exists();

        if ($isMember) {
            $this->addError('joinCode', 'You are already a member of this group.');
            return;
        }

        GroupMember::create([
            'group_id' => $group->id,
            'user_id' => auth()->id(),
        ]);

        $this->reset(['joinCode', 'showJoinModal']);
        session()->flash('success', "Successfully joined \"{$group->name}\".");
    }

    public function render()
    {
        $groups = auth()->user()->joinedGroups()
            ->with(['members', 'creator'])
            ->withCount('expenses')
            ->latest()
            ->get();

        return view('livewire.user.groups.group-list', [
            'groups' => $groups,
        ]);
    }
}
