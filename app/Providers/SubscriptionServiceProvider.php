<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Support\ServiceProvider;

class SubscriptionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(SubscriptionService::class, fn ($app) => new SubscriptionService());
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Assign free plan to new users
        User::created(function (User $user): void {
            $subscriptionService = app(SubscriptionService::class);

            // Wait for the user type to be set, then assign free plan
            dispatch(function () use ($user, $subscriptionService): void {
                $user->refresh();

                if ($user->userType) {
                    $subscriptionService->assignFreePlan($user);
                }
            })->afterResponse();
        });
    }
}
