<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SubscriptionLayoutService
{
    /**
     * Get subscription context for the current user.
     */
    public function getSubscriptionContext(): array
    {
        $user = Auth::user();
        
        if (!$user) {
            return $this->getGuestContext();
        }
        
        $tierInfo = $user->getSubscriptionTier();
        $usage = $user->getSubscriptionUsage();
        
        return [
            'user' => [
                'layout_type' => $user->getLayoutType(),
                'is_admin' => $user->isAdmin(),
                'user_type' => $user->userType?->name,
                'requires_verification' => $user->requiresVerification(),
            ],
            'subscription' => [
                'has_active' => $user->hasActiveSubscription(),
                'tier' => $tierInfo,
                'usage' => $usage,
                'requires_renewal' => $user->subscriptionRequiresRenewal(),
                'can_access_premium' => $user->canAccessPremiumContent(),
            ],
            'features' => $this->getFeatureAvailability($user),
            'limits' => $this->getLimitStatus($user),
            'ui' => $this->getUIConfiguration($user),
        ];
    }
    
    /**
     * Get context for guest users.
     */
    private function getGuestContext(): array
    {
        return [
            'user' => [
                'layout_type' => 'guest',
                'is_admin' => false,
                'user_type' => null,
                'requires_verification' => false,
            ],
            'subscription' => [
                'has_active' => false,
                'tier' => [
                    'tier' => 'guest',
                    'name' => 'Guest',
                    'features' => [],
                    'limits' => ['job_posts' => 0, 'applications' => 0, 'conversations' => 0],
                ],
                'usage' => [],
                'requires_renewal' => false,
                'can_access_premium' => false,
            ],
            'features' => $this->getGuestFeatures(),
            'limits' => $this->getGuestLimits(),
            'ui' => $this->getGuestUIConfiguration(),
        ];
    }
    
    /**
     * Get feature availability for a user.
     */
    private function getFeatureAvailability(User $user): array
    {
        $features = [
            'basic_messaging',
            'enhanced_messaging',
            'file_uploads',
            'voice_messages',
            'message_reactions',
            'conversation_search',
            'unlimited_chats',
            'advanced_filters',
            'analytics',
            'priority_listings',
            'featured_status',
        ];
        
        $availability = [];
        foreach ($features as $feature) {
            $availability[$feature] = $user->hasFeatureAccess($feature);
        }
        
        return $availability;
    }
    
    /**
     * Get limit status for a user.
     */
    private function getLimitStatus(User $user): array
    {
        return [
            'job_posts' => [
                'reached' => $user->hasReachedLimit('job_posts'),
                'can_use' => $user->canPostJob(),
                'remaining' => $user->getRemainingJobPosts(),
                'used_this_month' => $user->getJobPostsUsedThisMonth(),
            ],
            'applications' => [
                'reached' => $user->hasReachedLimit('applications'),
                'can_use' => $user->canApplyToJob(),
                'remaining' => $user->getRemainingJobApplications(),
                'used_this_month' => $user->getJobApplicationsUsedThisMonth(),
            ],
            'conversations' => [
                'reached' => $user->hasReachedLimit('conversations'),
                'count' => $user->allConversations()->count(),
            ],
        ];
    }
    
    /**
     * Get UI configuration based on subscription.
     */
    private function getUIConfiguration(User $user): array
    {
        $tierInfo = $user->getSubscriptionTier();
        
        return [
            'show_upgrade_prompts' => $tierInfo['tier'] === 'free',
            'show_feature_badges' => $user->canAccessPremiumContent(),
            'show_usage_indicators' => true,
            'theme_class' => $this->getThemeClass($tierInfo['tier']),
            'header_badges' => $this->getHeaderBadges($user),
            'sidebar_sections' => $this->getSidebarSections($user),
            'dashboard_widgets' => $this->getDashboardWidgets($user),
        ];
    }
    
    /**
     * Get theme class based on subscription tier.
     */
    private function getThemeClass(string $tier): string
    {
        return match($tier) {
            'enterprise' => 'theme-enterprise',
            'premium' => 'theme-premium',
            'basic' => 'theme-basic',
            default => 'theme-free'
        };
    }
    
    /**
     * Get header badges for user.
     */
    private function getHeaderBadges(User $user): array
    {
        $badges = [];
        
        if (!$user->hasActiveSubscription()) {
            $badges[] = [
                'text' => 'Free Plan',
                'class' => 'badge-secondary',
                'tooltip' => 'Upgrade to unlock premium features'
            ];
        } else {
            $tierInfo = $user->getSubscriptionTier();
            $badges[] = [
                'text' => $tierInfo['name'],
                'class' => $this->getBadgeClass($tierInfo['tier']),
                'tooltip' => null
            ];
        }
        
        if ($user->subscriptionRequiresRenewal()) {
            $badges[] = [
                'text' => 'Renewal Required',
                'class' => 'badge-warning',
                'tooltip' => 'Your subscription expires soon'
            ];
        }
        
        return $badges;
    }
    
    /**
     * Get badge class based on tier.
     */
    private function getBadgeClass(string $tier): string
    {
        return match($tier) {
            'enterprise' => 'badge-primary',
            'premium' => 'badge-success',
            'basic' => 'badge-info',
            default => 'badge-secondary'
        };
    }
    
    /**
     * Get sidebar sections based on subscription and user type.
     */
    private function getSidebarSections(User $user): array
    {
        $sections = ['dashboard', 'profile'];
        
        // Add sections based on user type
        if ($user->isAgency()) {
            $sections[] = 'job_posts';
            if ($user->hasFeatureAccess('analytics')) {
                $sections[] = 'analytics';
            }
        } elseif ($user->isChatter() || $user->isVA()) {
            $sections[] = 'job_search';
            $sections[] = 'applications';
        }
        
        // Add messaging for all users
        $sections[] = 'messages';
        
        // Add premium sections
        if ($user->hasFeatureAccess('advanced_filters')) {
            $sections[] = 'advanced_search';
        }
        
        if ($user->hasFeatureAccess('analytics')) {
            $sections[] = 'analytics';
        }
        
        return $sections;
    }
    
    /**
     * Get dashboard widgets based on subscription.
     */
    private function getDashboardWidgets(User $user): array
    {
        $widgets = ['welcome', 'quick_actions'];
        
        // Usage statistics for subscribed users
        if ($user->hasActiveSubscription()) {
            $widgets[] = 'usage_stats';
        }
        
        // Analytics for users with access
        if ($user->hasFeatureAccess('analytics')) {
            $widgets[] = 'analytics_overview';
        }
        
        // Recent activity
        $widgets[] = 'recent_activity';
        
        // Upgrade prompt for free users
        if (!$user->hasActiveSubscription()) {
            $widgets[] = 'upgrade_prompt';
        }
        
        return $widgets;
    }
    
    /**
     * Get feature availability for guest users.
     */
    private function getGuestFeatures(): array
    {
        return array_fill_keys([
            'basic_messaging',
            'enhanced_messaging',
            'file_uploads',
            'voice_messages',
            'message_reactions',
            'conversation_search',
            'unlimited_chats',
            'advanced_filters',
            'analytics',
            'priority_listings',
            'featured_status',
        ], false);
    }
    
    /**
     * Get limits for guest users.
     */
    private function getGuestLimits(): array
    {
        return [
            'job_posts' => ['reached' => true, 'can_use' => false, 'remaining' => 0],
            'applications' => ['reached' => true, 'can_use' => false, 'remaining' => 0],
            'conversations' => ['reached' => true, 'count' => 0],
        ];
    }
    
    /**
     * Get UI configuration for guest users.
     */
    private function getGuestUIConfiguration(): array
    {
        return [
            'show_upgrade_prompts' => true,
            'show_feature_badges' => false,
            'show_usage_indicators' => false,
            'theme_class' => 'theme-guest',
            'header_badges' => [
                [
                    'text' => 'Sign Up',
                    'class' => 'badge-primary',
                    'tooltip' => 'Create an account to get started'
                ]
            ],
            'sidebar_sections' => ['home', 'features', 'pricing'],
            'dashboard_widgets' => ['welcome', 'signup_prompt'],
        ];
    }
    
    /**
     * Check if user can access a specific UI element.
     */
    public function canAccessUIElement(string $element, User $user = null): bool
    {
        $user = $user ?? Auth::user();
        
        if (!$user) {
            return false;
        }
        
        return match($element) {
            'premium_search' => $user->hasFeatureAccess('advanced_filters'),
            'analytics_dashboard' => $user->hasFeatureAccess('analytics'),
            'unlimited_messaging' => $user->hasFeatureAccess('unlimited_chats'),
            'file_upload_button' => $user->hasFeatureAccess('file_uploads'),
            'voice_message_button' => $user->hasFeatureAccess('voice_messages'),
            'message_reactions' => $user->hasFeatureAccess('message_reactions'),
            'conversation_search' => $user->hasFeatureAccess('conversation_search'),
            'job_post_form' => $user->canPostJob(),
            'job_application_form' => $user->canApplyToJob(),
            'new_conversation_button' => !$user->hasReachedLimit('conversations'),
            'premium_job_details' => fn($jobId) => $user->hasAccessToPremiumJob($jobId),
            default => true
        };
    }
    
    /**
     * Get subscription upgrade suggestions based on usage patterns.
     */
    public function getUpgradeSuggestions(User $user = null): array
    {
        $user = $user ?? Auth::user();
        
        if (!$user || $user->hasFeatureAccess('analytics')) {
            return [];
        }
        
        $suggestions = [];
        $usage = $user->getSubscriptionUsage();
        
        // Job posting suggestions
        if ($user->hasReachedLimit('job_posts')) {
            $suggestions[] = [
                'title' => 'Increase Job Post Limit',
                'description' => 'You\'ve reached your monthly job posting limit. Upgrade for more posts.',
                'feature' => 'job_posts',
                'action' => 'upgrade'
            ];
        }
        
        // Conversation suggestions
        if ($user->hasReachedLimit('conversations')) {
            $suggestions[] = [
                'title' => 'Unlimited Conversations',
                'description' => 'Start unlimited conversations with potential talent.',
                'feature' => 'unlimited_chats',
                'action' => 'upgrade'
            ];
        }
        
        // Feature-based suggestions
        if (!$user->hasFeatureAccess('advanced_filters')) {
            $suggestions[] = [
                'title' => 'Advanced Search',
                'description' => 'Find the perfect candidates with advanced filtering options.',
                'feature' => 'advanced_filters',
                'action' => 'upgrade'
            ];
        }
        
        return $suggestions;
    }
}
