<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class BypassController extends Controller
{
    public function toggle($id)
    {
        $user = User::findOrFail($id);
        
        $user->update([
            'bypass_split_limit' => !$user->bypass_split_limit,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bypass authorization ' . ($user->bypass_split_limit ? 'granted' : 'revoked') . ' for user ' . $user->name,
            'bypass_split_limit' => $user->bypass_split_limit,
        ]);
    }
}
