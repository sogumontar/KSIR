<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsRegularUser
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Admin cannot access staff areas (as per existing logic, though they might want to?)
        // Actually, the previous logic was: if is_admin, abort 403.
        if ($user->is_admin) {
            abort(403, 'Unauthorized access.');
        }

        // Must be a staff member
        if ($user->role !== 'staff') {
             // If it's a customer, redirect to customer dashboard or abort
             if ($user->role === 'customer') {
                 return redirect()->route('customer.dashboard');
             }
             abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
