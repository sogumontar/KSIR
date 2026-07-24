<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvitationController extends Controller
{
    public function handleInvite(Request $request, $merchantToken)
    {
        $merchant = User::where('unique_code', $merchantToken)->firstOrFail();

        if (Auth::check()) {
            $user = Auth::user();
            
            // Only bind if the logged in user is a customer
            if ($user->role === 'customer') {
                $user->merchants()->syncWithoutDetaching([$merchant->id]);
                return redirect()->route('customer.dashboard')->with('message', "You are now linked to {$merchant->name}.");
            }

            // If it's a merchant/staff, just send them to their dashboard
            return redirect()->route('user.dashboard');
        }

        // Store token in session to handle post-login/registration binding
        session(['merchant_invite_token' => $merchantToken]);

        return redirect()->route('login')->with('info', "Please login or register to access {$merchant->name}'s storefront.");
    }
}
