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
     *
     * Allows access to staff/regular user areas.
     * - Admins (is_admin = true) are redirected to admin dashboard.
     * - Customers (role = 'customer') are redirected to customer dashboard.
     * - Everyone else (staff) is allowed through.
     *
     * We use is_admin as the primary gate, and role as a secondary signal
     * for distinguishing staff from customers. If role is null/missing
     * (e.g. old accounts before the role migration), we default to allowing
     * access so staff aren't locked out.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Admins should not be in the staff portal
        if ($user->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        // Customers should go to their own portal
        if ($user->role === 'customer') {
            return redirect()->route('customer.dashboard');
        }

        // Allow staff (role = 'staff') and legacy accounts with no role set
        return $next($request);
    }
}
