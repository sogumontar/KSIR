<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupMember;
use Illuminate\Http\Request;

class GroupInviteController extends Controller
{
    public function join(string $token)
    {
        $group = Group::where('invite_token', $token)->first();

        if (!$group) {
            return redirect()->route('user.groups')->with('error', 'Invalid or expired invitation link.');
        }

        $userId = auth()->id();

        // Check if user is already a member
        $isMember = GroupMember::where('group_id', $group->id)
            ->where('user_id', $userId)
            ->exists();

        if (!$isMember) {
            GroupMember::create([
                'group_id' => $group->id,
                'user_id' => $userId,
            ]);
            
            // Note: Since they joined themselves via invitation link, 
            // sending them an "Added to group" notification is redundant.
            // We only send it when added by another user.
        }

        return redirect()->route('user.group-detail', $group->id)
            ->with('success', "You have successfully joined the group: {$group->name}.");
    }
}
