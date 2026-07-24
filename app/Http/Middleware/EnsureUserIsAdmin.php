<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * Checks is_admin flag only — this is the single source of truth.
     * The `role` column is a convenience field and may not be in sync
     * (e.g. accounts created before the role migration ran), so we
     * do NOT use it as a hard gate here.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!$user->is_admin) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
