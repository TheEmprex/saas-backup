<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\User;
use App\Services\SubscriptionService;
use App\View\Composers\SubscriptionComposer;

class SubscriptionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(SubscriptionService::class, function ($app) {
            return new SubscriptionService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register the subscription view composer for authenticated layouts
        View::composer([
            'layouts.app',
            'layouts.dashboard', 
            'dashboard.*',
            'job-posts.*',
            'messages.*',
            'profile.*',
            'subscription.*',
            'components.*'
        ], SubscriptionComposer::class);
        
        // Assign free plan to new users
        User::created(function (User $user) {
            $subscriptionService = app(SubscriptionService::class);
            
            // Wait for the user type to be set, then assign free plan
            dispatch(function () use ($user, $subscriptionService) {
                $user->refresh();
                if ($user->userType) {
                    $subscriptionService->assignFreePlan($user);
                }
            })->afterResponse();
        });
    }
}
