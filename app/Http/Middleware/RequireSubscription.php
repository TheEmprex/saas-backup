<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow access for guests (they'll be redirected to login)
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();
        
        // Always allow access for admins
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Check if user has an active subscription
        if (!$user->hasActiveSubscription()) {
            // If it's an API request, return JSON error
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Subscription required',
                    'message' => 'You need an active subscription to access this feature.',
                    'redirect' => route('subscription.plans')
                ], 402);
            }

            // For web requests, redirect to pricing with error message
            return redirect()->route('subscription.plans')
                ->with('error', 'You need an active subscription to access this feature.')
                ->with('intended_url', $request->fullUrl());
        }

        return $next($request);
    }
}
