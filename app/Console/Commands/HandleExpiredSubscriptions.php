<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserSubscription;
use App\Services\SubscriptionService;

class HandleExpiredSubscriptions extends Command
{
    protected $signature = 'subscriptions:handle-expired';
    protected $description = 'Handle expired subscriptions by moving users to free plans';

    protected SubscriptionService $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        parent::__construct();
        $this->subscriptionService = $subscriptionService;
    }

    public function handle(): int
    {
        $this->info('Checking for expired subscriptions...');

        // Find expired subscriptions that haven't been processed
        $expiredSubscriptions = UserSubscription::with(['user', 'subscriptionPlan'])
            ->where('expires_at', '<', now())
            ->whereNull('processed_at')
            ->get();

        if ($expiredSubscriptions->isEmpty()) {
            $this->info('No expired subscriptions found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$expiredSubscriptions->count()} expired subscriptions to process.");

        foreach ($expiredSubscriptions as $subscription) {
            try {
                $user = $subscription->user;
                
                if (!$user) {
                    $this->warn("User not found for subscription ID: {$subscription->id}");
                    continue;
                }

                $this->info("Processing expired subscription for user: {$user->email}");

                // Mark the expired subscription as processed
                $subscription->update(['processed_at' => now()]);

                // Assign free plan to the user
                $this->subscriptionService->assignFreePlan($user);

                $this->info("Successfully moved user {$user->email} to free plan.");

                // TODO: Send notification email to user about subscription expiry

            } catch (\Exception $e) {
                $this->error("Error processing subscription ID {$subscription->id}: " . $e->getMessage());
            }
        }

        $this->info('Finished processing expired subscriptions.');
        return Command::SUCCESS;
    }
}
