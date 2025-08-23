<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UpdateLastSeen
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $userId = Auth::id();
            $now = now();
            
            // Use cache to avoid database hits on every request
            $cacheKey = 'user_last_seen_' . $userId;
            $lastUpdate = Cache::get($cacheKey);
            
            // Only update if it's been more than 5 minutes since last update
            if (!$lastUpdate || $now->diffInMinutes($lastUpdate) >= 5) {
                Auth::user()->update(['last_seen_at' => $now]);
                Cache::put($cacheKey, $now, now()->addMinutes(10));
            }
        }

        return $next($request);
    }
}
