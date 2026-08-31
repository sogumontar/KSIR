<?php

namespace App\Http\Controllers;

use App\Models\LaundryStoreContributor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaundryContributorController extends Controller
{
    public function join(string $token)
    {
        $invite = LaundryStoreContributor::where('invite_token', $token)->firstOrFail();

        if ($invite->status === 'accepted') {
            return redirect()->route('laundry.store-select')
                ->with('status_message', 'Undangan ini sudah diterima sebelumnya.');
        }

        $userId = Auth::id();

        // Prevent owner from accepting their own invite
        if ($invite->owner_user_id === $userId) {
            return redirect()->route('laundry.dashboard')
                ->with('status_message', 'Anda tidak dapat bergabung ke toko Anda sendiri.');
        }

        // Accept the invite
        $invite->update([
            'contributor_user_id' => $userId,
            'status'              => 'accepted',
            'accepted_at'         => now(),
        ]);

        return redirect()->route('laundry.store-select')
            ->with('status_message', 'Berhasil bergabung sebagai kontributor toko ' . ($invite->owner->name ?? '') . '!');
    }
}
