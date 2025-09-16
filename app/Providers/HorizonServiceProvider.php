<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        parent::boot();

        // Horizon::routeSmsNotificationsTo('15556667777');
        // Horizon::routeMailNotificationsTo('example@example.com');
        // Horizon::routeSlackNotificationsTo('slack-webhook-url', '#channel');
    }

    /**
     * Register the Horizon gate.
     *
     * This gate determines who can access Horizon in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewHorizon', function ($user = null) {
            // Allow only authenticated admins (or users with proper permission) to view Horizon
            if (!$user) {
                return false;
            }

            // Allow access for explicitly authorized emails via env (comma-separated)
            $allowedEmails = array_filter(array_map('trim', explode(',', (string) env('HORIZON_ADMINS', ''))));
            if (!empty($allowedEmails) && in_array($user->email, $allowedEmails, true)) {
                return true;
            }

            if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
                return true;
            }

            // Fallback to permission check if available
            if (method_exists($user, 'can') && $user->can('browse_admin')) {
                return true;
            }

            return false;
        });
    }
}
