<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\UserSubscription;
use Carbon\Carbon;

class SubscriptionService
{
    /**
     * Assign a free plan to a user based on their type.
     */
    public function assignFreePlan(User $user): UserSubscription
    {
        $planName = $user->isAgency() ? 'Agency Free' : 'Chatter Free';

        $plan = SubscriptionPlan::where('name', $planName)->firstOrFail();

        return $this->assignPlan($user, $plan);
    }

    /**
     * Assign a subscription plan to a user.
     */
    public function assignPlan(User $user, SubscriptionPlan $plan, ?Carbon $expiresAt = null): UserSubscription
    {
        // End any existing active subscription
        $this->endCurrentSubscription($user);

        return UserSubscription::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'started_at' => now(),
            'expires_at' => $expiresAt,
        ]);
    }

    /**
     * End a user's current subscription.
     */
    public function endCurrentSubscription(User $user): void
    {
        $currentSubscription = $user->currentSubscription();

        if ($currentSubscription) {
            $currentSubscription->update([
                'expires_at' => now(),
            ]);
        }
    }

    /**
     * Get available subscription plans for a user type.
     */
    public function getAvailablePlans(string $userType): array
    {
        if (in_array($userType, ['ofm_agency', 'chatting_agency', 'agency'])) {
            return SubscriptionPlan::where('name', 'like', 'Agency%')->get()->toArray();
        }

        if ($userType === 'chatter') {
            return SubscriptionPlan::where('name', 'like', 'Chatter%')->get()->toArray();
        }

        return [];
    }

    /**
     * Check if a user can upgrade to a specific plan.
     */
    public function canUpgradeToPlan(User $user, SubscriptionPlan $plan): bool
    {
        $currentSubscription = $user->currentSubscription();

        if (! $currentSubscription) {
            return true; // Can upgrade from no subscription
        }

        // Allow any plan change (upgrade or downgrade)
        return $plan->id !== $currentSubscription->subscription_plan_id;
    }

    /**
     * Calculate upgrade pricing without assigning plan.
     */
    public function calculateUpgradePricing(User $user, SubscriptionPlan $newPlan): array
    {
        $currentSubscription = $user->currentSubscription();
        $currentPlan = $currentSubscription ? $currentSubscription->subscriptionPlan : null;

        if (! $currentSubscription) {
            // No current subscription
            return [
                'type' => 'new_subscription',
                'amount_to_charge' => $newPlan->price,
                'proration_credit' => 0,
                'net_amount' => $newPlan->price,
                'message' => 'New subscription will be created.',
            ];
        }

        // Calculate prorated amounts
        $remainingDays = $currentSubscription->expires_at
            ? max(0, $currentSubscription->expires_at->diffInDays(now())) : 0;

        $daysInMonth = now()->daysInMonth;
        $proratedCredit = ($currentPlan->price / $daysInMonth) * $remainingDays;
        $proratedNewCharge = ($newPlan->price / $daysInMonth) * $remainingDays;

        if ($newPlan->price > $currentPlan->price) {
            // Upgrade: charge difference for remaining days + full amount for next month
            $upgradeCharge = $proratedNewCharge - $proratedCredit;
            $netAmount = $upgradeCharge + $newPlan->price;
            $type = 'upgrade';
            $message = 'Subscription will be upgraded.';
        } else {
            // Downgrade: credit difference, apply to next billing cycle
            $downgradeCredit = $proratedCredit - $proratedNewCharge;
            $netAmount = max(0, $newPlan->price - $downgradeCredit);
            $type = 'downgrade';
            $message = 'Subscription will be downgraded.';
        }

        return [
            'type' => $type,
            'amount_to_charge' => $newPlan->price,
            'proration_credit' => $proratedCredit,
            'net_amount' => $netAmount,
            'message' => $message,
        ];
    }

    /**
     * Get downgrade warnings without changing subscription.
     */
    public function getDowngradeWarnings(User $user, SubscriptionPlan $newPlan): array
    {
        $currentStats = $this->getSubscriptionStats($user);
        $warnings = [];

        // Check if current usage exceeds new plan limits
        if ($newPlan->job_post_limit && $currentStats['job_posts_used'] > $newPlan->job_post_limit) {
            $warnings[] = "You've used {$currentStats['job_posts_used']} job posts this month, but the new plan only allows {$newPlan->job_post_limit}.";
        }

        if ($newPlan->chat_application_limit && $currentStats['applications_used'] > $newPlan->chat_application_limit) {
            $warnings[] = "You've used {$currentStats['applications_used']} applications this month, but the new plan only allows {$newPlan->chat_application_limit}.";
        }

        // Check feature usage
        if (! $newPlan->featured_status && $this->userHasActiveFeaturedJobs($user)) {
            $warnings[] = "You have active featured jobs, but the new plan doesn't include featured job benefits.";
        }

        return $warnings;
    }

    /**
     * Get plan change preview without making changes.
     */
    public function getPlanChangePreview(User $user, SubscriptionPlan $newPlan): array
    {
        $currentSubscription = $user->currentSubscription();
        $currentPlan = $currentSubscription ? $currentSubscription->subscriptionPlan : null;

        if (! $currentSubscription) {
            return [
                'type' => 'new_subscription',
                'current_plan' => null,
                'new_plan' => $newPlan->name,
                'immediate_charge' => $newPlan->price,
                'next_billing_date' => now()->addMonth()->format('Y-m-d'),
                'features_gained' => $this->getFeatureComparison($currentPlan, $newPlan)['gained'],
                'features_lost' => [],
                'warnings' => [],
            ];
        }

        $remainingDays = $currentSubscription->expires_at
            ? max(0, $currentSubscription->expires_at->diffInDays(now())) : 0;

        $isUpgrade = $newPlan->price > $currentPlan->price;
        $featureComparison = $this->getFeatureComparison($currentPlan, $newPlan);

        $warnings = [];

        if (! $isUpgrade) {
            $currentStats = $this->getSubscriptionStats($user);

            if ($newPlan->job_post_limit && $currentStats['job_posts_used'] > $newPlan->job_post_limit) {
                $warnings[] = "Current usage ({$currentStats['job_posts_used']} job posts) exceeds new plan limit ({$newPlan->job_post_limit}).";
            }
        }

        return [
            'type' => $isUpgrade ? 'upgrade' : 'downgrade',
            'current_plan' => $currentPlan->name,
            'new_plan' => $newPlan->name,
            'immediate_charge' => $newPlan->price,
            'next_billing_date' => now()->addMonth()->format('Y-m-d'),
            'remaining_days' => $remainingDays,
            'features_gained' => $featureComparison['gained'],
            'features_lost' => $featureComparison['lost'],
            'warnings' => $warnings,
        ];
    }

    /**
     * Get subscription statistics for a user.
     */
    public function getSubscriptionStats(User $user): array
    {
        $subscription = $user->currentSubscription();

        if (! $subscription) {
            return [
                'has_subscription' => false,
                'plan_name' => null,
                'job_posts_used' => 0,
                'job_posts_limit' => 0,
                'applications_used' => 0,
                'applications_limit' => 0,
                'expires_at' => null,
            ];
        }

        $plan = $subscription->subscriptionPlan;
        $currentMonth = now()->format('Y-m');

        $jobPostsUsed = $user->jobPosts()
            ->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$currentMonth])
            ->count();

        $applicationsUsed = $user->jobApplications()
            ->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$currentMonth])
            ->count();

        return [
            'has_subscription' => true,
            'plan_name' => $plan->name,
            'job_posts_used' => $jobPostsUsed,
            'job_posts_limit' => $plan->job_post_limit,
            'applications_used' => $applicationsUsed,
            'applications_limit' => $plan->chat_application_limit,
            'expires_at' => $subscription->expires_at,
            'features' => [
                'unlimited_chats' => $plan->unlimited_chats,
                'advanced_filters' => $plan->advanced_filters,
                'analytics' => $plan->analytics,
                'priority_listings' => $plan->priority_listings,
                'featured_status' => $plan->featured_status,
            ],
        ];
    }

    /**
     * Check if a user's subscription is about to expire.
     */
    public function isSubscriptionExpiringSoon(User $user, int $daysThreshold = 7): bool
    {
        $subscription = $user->currentSubscription();

        if (! $subscription || ! $subscription->expires_at) {
            return false;
        }

        return $subscription->expires_at->diffInDays(now()) <= $daysThreshold;
    }

    /**
     * Get expired subscriptions that need to be handled.
     */
    public function getExpiredSubscriptions(): array
    {
        return UserSubscription::with(['user', 'subscriptionPlan'])
            ->where('expires_at', '<', now())
            ->whereNull('processed_at') // Assuming we add this column to track processing
            ->get()
            ->toArray();
    }

    /**
     * Check if user has active featured jobs.
     */
    private function userHasActiveFeaturedJobs(User $user): bool
    {
        return $user->jobPosts()
            ->where('is_featured', true)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->exists();
    }

    /**
     * Compare features between two plans.
     */
    private function getFeatureComparison($currentPlan, \App\Models\SubscriptionPlan $newPlan): array
    {
        $currentFeatures = $currentPlan ? [
            'job_post_limit' => $currentPlan->job_post_limit,
            'chat_application_limit' => $currentPlan->chat_application_limit,
            'unlimited_chats' => $currentPlan->unlimited_chats,
            'advanced_filters' => $currentPlan->advanced_filters,
            'analytics' => $currentPlan->analytics,
            'priority_listings' => $currentPlan->priority_listings,
            'featured_status' => $currentPlan->featured_status,
        ] : [];

        $newFeatures = [
            'job_post_limit' => $newPlan->job_post_limit,
            'chat_application_limit' => $newPlan->chat_application_limit,
            'unlimited_chats' => $newPlan->unlimited_chats,
            'advanced_filters' => $newPlan->advanced_filters,
            'analytics' => $newPlan->analytics,
            'priority_listings' => $newPlan->priority_listings,
            'featured_status' => $newPlan->featured_status,
        ];

        $gained = [];
        $lost = [];

        foreach ($newFeatures as $feature => $newValue) {
            $currentValue = $currentFeatures[$feature] ?? null;

            if ($feature === 'job_post_limit' || $feature === 'chat_application_limit') {
                if ($newValue > $currentValue) {
                    $gained[] = ucfirst(str_replace('_', ' ', $feature)).': '.($newValue ?: 'Unlimited');
                } elseif ($newValue < $currentValue) {
                    $lost[] = ucfirst(str_replace('_', ' ', $feature)).': reduced to '.($newValue ?: 'Unlimited');
                }
            } elseif ($newValue && ! $currentValue) {
                $gained[] = ucfirst(str_replace('_', ' ', $feature));
            } elseif (! $newValue && $currentValue) {
                $lost[] = ucfirst(str_replace('_', ' ', $feature));
            }
        }

        return ['gained' => $gained, 'lost' => $lost];
    }
}
