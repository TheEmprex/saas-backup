<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Subscription-related blade directives
        
        // @hasFeature('advanced_filters')
        Blade::if('hasFeature', function (string $feature) {
            $user = auth()->user();
            return $user && $user->hasFeatureAccess($feature);
        });
        
        // @hasSubscription
        Blade::if('hasSubscription', function () {
            $user = auth()->user();
            return $user && $user->hasActiveSubscription();
        });
        
        // @canPostJob
        Blade::if('canPostJob', function () {
            $user = auth()->user();
            return $user && $user->canPostJob();
        });
        
        // @canApplyToJob
        Blade::if('canApplyToJob', function () {
            $user = auth()->user();
            return $user && $user->canApplyToJob();
        });
        
        // @hasReachedLimit('job_posts')
        Blade::if('hasReachedLimit', function (string $limitType) {
            $user = auth()->user();
            return $user && $user->hasReachedLimit($limitType);
        });
        
        // @isPremiumUser
        Blade::if('isPremiumUser', function () {
            $user = auth()->user();
            return $user && $user->canAccessPremiumContent();
        });
        
        // @subscriptionExpiresSoon
        Blade::if('subscriptionExpiresSoon', function () {
            $user = auth()->user();
            return $user && $user->subscriptionRequiresRenewal();
        });
        
        // @userType('agency')
        Blade::if('userType', function (string $type) {
            $user = auth()->user();
            if (!$user) return false;
            
            return match($type) {
                'agency' => $user->isAgency(),
                'chatter' => $user->isChatter(),
                'va' => $user->isVA(),
                'admin' => $user->isAdmin(),
                default => false
            };
        });
        
        // @tierLevel('premium')
        Blade::if('tierLevel', function (string $requiredTier) {
            $user = auth()->user();
            if (!$user) return false;
            
            $tierInfo = $user->getSubscriptionTier();
            $currentTier = $tierInfo['tier'];
            
            $tiers = ['free' => 0, 'basic' => 1, 'premium' => 2, 'enterprise' => 3];
            
            return ($tiers[$currentTier] ?? 0) >= ($tiers[$requiredTier] ?? 0);
        });
    }
}
