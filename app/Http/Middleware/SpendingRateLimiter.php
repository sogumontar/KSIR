<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;

class SpendingRateLimiter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only limit POST requests
        if ($request->method() !== 'POST') {
            return $next($request);
        }

        $user = $request->user();

        // Allow if no user (should be protected by auth anyway), or if admin, or if bypass flag is set
        if (!$user || $user->is_admin || $user->bypass_split_limit) {
            return $next($request);
        }

        $key = 'spending_post_count_' . $user->id;
        
        // TTL for 24 hours
        $count = Cache::get($key, 0);

        if ($count >= 5) {
            return response()->json([
                'message' => 'Rate limit exceeded. Contact the system administrator.'
            ], 403);
        }

        Cache::put($key, $count + 1, now()->addDay());

        return $next($request);
    }
}
