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
    public function handle(Request $request, Closure $next, string $feature = null, string $tier = null): Response
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
        
        // Get current subscription tier info
        $currentTier = $user->getSubscriptionTier();
        $usage = $user->getSubscriptionUsage();

        // Check if user has an active subscription
        if (!$user->hasActiveSubscription()) {
            // If it's an API request, return JSON error
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Subscription required',
                    'message' => 'You need an active subscription to access this feature.',
                    'current_tier' => $currentTier,
                    'usage' => $usage,
                    'redirect' => route('subscription.plans')
                ], 402);
            }

            // For web requests, redirect to pricing with error message
            return redirect()->route('subscription.plans')
                ->with('error', 'You need an active subscription to access this feature.')
                ->with('current_tier', $currentTier)
                ->with('usage_stats', $usage)
                ->with('intended_url', $request->fullUrl());
        }
        
        // Check specific feature access if requested
        if ($feature && !$user->hasFeatureAccess($feature)) {
            $message = "This feature is not available in your {$currentTier['name']} plan.";
            
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Feature not available',
                    'message' => $message,
                    'current_tier' => $currentTier,
                    'required_features' => [$feature],
                    'redirect' => route('subscription.plans')
                ], 403);
            }
            
            return redirect()->route('subscription.plans')
                ->with('error', $message)
                ->with('required_feature', $feature)
                ->with('current_tier', $currentTier)
                ->with('intended_url', $request->fullUrl());
        }
        
        // Check specific tier requirement if requested
        if ($tier && !$this->meetsMinimumTier($currentTier['tier'], $tier)) {
            $message = "This feature requires a {$tier} plan or higher. You are currently on {$currentTier['name']}.";
            
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Insufficient subscription tier',
                    'message' => $message,
                    'current_tier' => $currentTier,
                    'required_tier' => $tier,
                    'redirect' => route('subscription.plans')
                ], 403);
            }
            
            return redirect()->route('subscription.plans')
                ->with('error', $message)
                ->with('required_tier', $tier)
                ->with('current_tier', $currentTier)
                ->with('intended_url', $request->fullUrl());
        }
        
        // Check if subscription requires renewal soon
        if ($user->subscriptionRequiresRenewal()) {
            $request->session()->flash('warning', 'Your subscription expires soon. Please renew to continue using premium features.');
        }

        return $next($request);
    }
    
    /**
     * Check if current tier meets minimum requirement.
     */
    private function meetsMinimumTier(string $current, string $required): bool
    {
        $tiers = ['free' => 0, 'basic' => 1, 'premium' => 2, 'enterprise' => 3];
        
        return ($tiers[$current] ?? 0) >= ($tiers[$required] ?? 0);
    }
}
