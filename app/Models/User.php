<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Wave\Traits\HasProfileKeyValues;
use Wave\User as WaveUser;
use App\Models\UserProfile;
use App\Models\UserType;
use App\Models\JobPost;
use App\Models\JobApplication;
use App\Models\Message;
use App\Models\Rating;
use App\Models\KycVerification;
use App\Models\EarningsVerification;
use App\Models\UserSubscription;
use App\Models\ChatterMicrotransaction;
use App\Models\FeaturedJobPost;
use App\Models\ContractReview;
use App\Models\UserMonthlyStat;
use App\Models\MessageFolder;
use App\Models\Conversation;
use App\Models\UserOnlineStatus;
use App\Models\TypingIndicator;
use App\Traits\ReviewHelper;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends WaveUser implements MustVerifyEmail
{
    use HasProfileKeyValues;
    use Notifiable;
    use ReviewHelper;
    public $guard_name = 'web';

    // Add messaging-related relationships

    /**
     * Get the user's online status
     */
    public function onlineStatus()
    {
        return $this->hasOne(UserOnlineStatus::class);
    }

    /**
     * Get conversations where user is participant 1
     */
    public function conversationsAsUser1()
    {
        return $this->hasMany(Conversation::class, 'user1_id');
    }

    /**
     * Get conversations where user is participant 2
     */
    public function conversationsAsUser2()
    {
        return $this->hasMany(Conversation::class, 'user2_id');
    }

    /**
     * Get all conversations for this user
     */
    public function allConversations()
    {
        return Conversation::where(function ($query) {
            $query->where('user1_id', $this->id)
                  ->orWhere('user2_id', $this->id);
        });
    }

    /**
     * Get messages sent by this user
     */
    public function messagesSent()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Get typing indicators for this user
     */
    public function typingIndicators()
    {
        return $this->hasMany(TypingIndicator::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'username',
        'avatar',
        'password',
        'role_id',
        'verification_code',
        'verified',
        'trial_ends_at',
        'user_type_id',
        'user_type_locked',
        'user_type_locked_at',
        'last_seen_at',
        'kyc_status',
        'is_banned',
        'banned_at',
        'ban_reason',
        'phone_number',
        'timezone',
        'availability_hours',
        'available_for_work',
        'hourly_rate',
        'preferred_currency',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'banned_at' => 'datetime',
        'is_banned' => 'boolean',
        'user_type_locked' => 'boolean',
        'user_type_locked_at' => 'datetime',
        'availability_hours' => 'array',
        'available_for_work' => 'boolean',
'hourly_rate' => 'decimal:2',
        'dashboard_preferences' => 'array',
    ];

    /**
     * Get the user's profile.
     */
    public function userProfile()
    {
        return $this->hasOne(UserProfile::class);
    }
    
    /**
     * Get the user's profile (alias for userProfile).
     */
    public function userProfileRelation()
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Get the user's type.
     */
    public function userType()
    {
        return $this->belongsTo(UserType::class);
    }

    /**
     * Get the jobs posted by this user.
     */
    public function jobPosts()
    {
        return $this->hasMany(JobPost::class);
    }

    /**
     * Get the job applications made by this user.
     */
    public function jobApplications()
    {
        return $this->hasMany(JobApplication::class);
    }
    
    public function ratingsReceived()
    {
        return $this->hasMany(Rating::class, 'rated_id');
    }
    
    public function ratingsGiven()
    {
        return $this->hasMany(Rating::class, 'rater_id');
    }

    /**
     * Get the user's training progress records.
     */
    public function trainingProgress()
    {
        return $this->hasMany(UserTrainingProgress::class);
    }

    /**
     * Get the user's test results.
     */
    public function userTestResults()
    {
        return $this->hasMany(UserTestResult::class);
    }

    public function kycVerification(): HasOne
    {
        return $this->hasOne(KycVerification::class);
    }

    public function isKycVerified(): bool
    {
        return $this->kycVerification?->isApproved() ?? false;
    }

    public function hasKycSubmitted(): bool
    {
        return $this->kycVerification !== null;
    }

    public function earningsVerification(): HasOne
    {
        return $this->hasOne(EarningsVerification::class);
    }

    public function isEarningsVerified(): bool
    {
        return $this->earningsVerification?->isApproved() ?? false;
    }

    public function hasEarningsSubmitted(): bool
    {
        return $this->earningsVerification !== null;
    }

    public function isChatter(): bool
    {
        return $this->userType && in_array($this->userType->name, ['chatter', 'Chatter', 'Content Creator']);
    }

    public function isAgency(): bool
    {
        return $this->userType && in_array($this->userType->name, ['ofm_agency', 'chatting_agency', 'Agency']);
    }

    public function isVA(): bool
    {
        return $this->userType && in_array($this->userType->name, ['va', 'VA', 'Virtual Assistant']);
    }

    public function canPostJobs(): bool
    {
        // Only agencies and OFM agencies can post jobs
        return $this->isAgency() || $this->isAdmin();
    }

    public function canApplyToJobs(): bool
    {
        // Only chatters and VAs can apply to jobs
        return $this->isChatter() || $this->isVA() || $this->isAdmin();
    }

    public function getLayoutType(): string
    {
        if ($this->isChatter() || $this->isVA()) {
            return 'talent';
        } elseif ($this->isAgency()) {
            return 'agency';
        }
        return 'default';
    }

    public function requiresVerification(): bool
    {
        // Check if user type requires KYC
        if ($this->userType && $this->userType->requires_kyc) {
            return !$this->isKycVerified();
        }
        
        // Check for earnings verification for agencies
        if ($this->isAgency()) {
            return !$this->isEarningsVerified();
        }
        
        return false;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is banned.
     */
    public function isBanned(): bool
    {
        return $this->is_banned ?? false;
    }

    /**
     * Ban the user.
     */
    public function ban(?string $reason = null): void
    {
        $this->update([
            'is_banned' => true,
            'banned_at' => now(),
            'ban_reason' => $reason,
        ]);
    }

    /**
     * Unban the user.
     */
    public function unban(): void
    {
        $this->update([
            'is_banned' => false,
            'banned_at' => null,
            'ban_reason' => null,
        ]);
    }

    /**
     * Get the messages sent by this user.
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Get the messages received by this user.
     */
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'recipient_id');
    }

    /**
     * Get the user's message folders.
     */
    public function messageFolders()
    {
        return $this->hasMany(MessageFolder::class);
    }

    /**
     * Get the user's conversations.
     */
    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    /**
     * Get the user's default message folder.
     */
    public function defaultMessageFolder()
    {
        return $this->messageFolders()->where('is_default', true)->first();
    }

    /**
     * Get the ratings given by this user.
     */
    public function givenRatings()
    {
        return $this->hasMany(Rating::class, 'rater_id');
    }

    /**
     * Get the ratings received by this user.
     */
    public function receivedRatings()
    {
        return $this->hasMany(ShiftReview::class, 'chatter_id');
    }

    /**
     * Web push subscriptions for this user.
     */
    public function webPushSubscriptions()
    {
        return $this->hasMany(\App\Models\WebPushSubscription::class);
    }
    
    // Review methods are now provided by the ReviewHelper trait

    /**
     * Get the user's subscriptions.
     */
    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    /**
     * Get the user's current active subscription.
     */
    public function currentSubscription()
    {
        return $this->subscriptions()->where('expires_at', '>', now())->orWhereNull('expires_at')->first();
    }

    /**
     * Override Wave's subscriber method to use our custom subscription system
     */
    public function subscriber()
    {
        return $this->hasActiveSubscription();
    }

    /**
     * Override Wave's onTrial method to use our custom subscription system
     */
    public function onTrial()
    {
        if (is_null($this->trial_ends_at)) {
            return false;
        }

        return ! ($this->subscriber());
    }

    /**
     * Get the user's microtransactions.
     */
    public function microtransactions()
    {
        return $this->hasMany(ChatterMicrotransaction::class);
    }

    /**
     * Get the user's featured job posts.
     */
    public function featuredJobPosts()
    {
        return $this->hasMany(JobPost::class)->where('is_featured', true);
    }

    /**
     * Get employment contracts where this user is the agency.
     */
    public function agencyContracts()
    {
        return $this->hasMany(EmploymentContract::class, 'agency_id');
    }

    /**
     * Get employment contracts where this user is the chatter.
     */
    public function chatterContracts()
    {
        return $this->hasMany(EmploymentContract::class, 'chatter_id');
    }

    /**
     * Get work shifts where this user is the chatter.
     */
    public function workShifts()
    {
        return $this->hasMany(WorkShift::class, 'chatter_id');
    }

    /**
     * Get work shifts where this user is the agency.
     */
    public function agencyShifts()
    {
        return $this->hasMany(WorkShift::class, 'agency_id');
    }

    /**
     * Get reviews this user has given (as agency).
     */
    public function reviewsGiven()
    {
        return $this->hasMany(ShiftReview::class, 'reviewer_id');
    }

    /**
     * Get reviews this user has received (as chatter).
     */
    public function reviewsReceived()
    {
        return $this->hasMany(ShiftReview::class, 'chatter_id');
    }

    /**
     * Get contract reviews this user has given.
     */
    public function contractReviewsGiven()
    {
        return $this->hasMany(ContractReview::class, 'reviewer_id');
    }

    /**
     * Get contract reviews this user has received.
     */
    public function contractReviewsReceived()
    {
        return $this->hasMany(ContractReview::class, 'reviewed_user_id');
    }

    /**
     * Get average contract review rating.
     */
    public function getAverageContractRatingAttribute()
    {
        return $this->contractReviewsReceived()->avg('rating') ?? 0;
    }

    /**
     * Get total contract reviews count.
     */
    public function getTotalContractReviewsAttribute()
    {
        return $this->contractReviewsReceived()->count();
    }

    /**
     * Get active employment contracts for this agency.
     */
    public function activeEmployees()
    {
        return $this->agencyContracts()->where('status', 'active');
    }

    /**
     * Get the user's featured job posts.
     */
    public function featuredJobPostsOld()
    {
        return $this->hasMany(FeaturedJobPost::class);
    }

    /**
     * Get the user's monthly statistics.
     */
    public function monthlyStats()
    {
        return $this->hasMany(UserMonthlyStat::class);
    }


    /**
     * Get the user's availability schedule.
     */
    public function availability()
    {
        return $this->hasMany(UserAvailability::class);
    }

    /**
     * Get the user's detailed availability schedule.
     */
    public function availabilitySchedule()
    {
        return $this->hasMany(UserAvailabilitySchedule::class);
    }

    /**
     * Get the user's availability for specific days.
     */
    public function getAvailabilityForDays($days, $targetTimezone = null)
    {
        $availability = $this->availability()
            ->whereIn('day_of_week', $days)
            ->where('is_available', true)
            ->get();

        if ($targetTimezone) {
            return $availability->map(function ($avail) use ($targetTimezone) {
                return $avail->convertToTimezone($targetTimezone);
            });
        }

        return $availability;
    }

    /**
     * Check if user has an active subscription.
     */
    public function hasActiveSubscription()
    {
        return $this->currentSubscription() !== null;
    }

    /**
     * Get the user's subscription plan.
     */
    public function subscriptionPlan()
    {
        return $this->currentSubscription()?->subscriptionPlan;
    }

    /**
     * Check if user has access to a specific feature based on their subscription.
     */
    public function hasFeatureAccess(string $feature): bool
    {
        // Admins have access to everything
        if ($this->isAdmin()) {
            return true;
        }

        $subscription = $this->currentSubscription();
        if (!$subscription) {
            return $feature === 'basic_messaging'; // Only basic messaging for free users
        }

        $plan = $subscription->subscriptionPlan;
        if (!$plan) {
            return false;
        }

        return match($feature) {
            'unlimited_chats' => (bool) $plan->unlimited_chats,
            'advanced_filters' => (bool) $plan->advanced_filters,
            'analytics' => (bool) $plan->analytics,
            'priority_listings' => (bool) $plan->priority_listings,
            'featured_status' => (bool) $plan->featured_status,
            'enhanced_messaging' => $plan->price > 0, // Paid plans get enhanced messaging
            'file_uploads' => $plan->price > 0, // Paid plans can upload files
            'voice_messages' => $plan->price > 0, // Paid plans can send voice messages
            'message_reactions' => $plan->price > 0, // Paid plans can react to messages
            'conversation_search' => $plan->price > 0, // Paid plans can search conversations
            'basic_messaging' => true, // All subscribed users get basic messaging
            default => false
        };
    }

    /**
     * Check if user can post jobs based on their subscription limits.
     */
    public function canPostJob()
    {
        // First check if user type is authorized to post jobs (only agencies)
        if (!$this->isAgency() && !$this->isAdmin()) {
            return false;
        }
        
        $subscription = $this->currentSubscription();
        if (!$subscription) {
            return false;
        }

        $plan = $subscription->subscriptionPlan;
        if ($plan->job_post_limit === null) {
            return true; // Unlimited
        }

        // Use permanent counter - doesn't decrease when jobs are deleted
        $jobPostsThisMonth = $this->getJobPostsUsedThisMonth();
        
        return $jobPostsThisMonth < $plan->job_post_limit;
    }

    /**
     * Check if user can apply to jobs based on their subscription limits.
     */
    public function canApplyToJob()
    {
        $subscription = $this->currentSubscription();
        if (!$subscription) {
            return $this->isChatter(); // Chatters can apply without subscription
        }

        $plan = $subscription->subscriptionPlan;
        if ($plan->chat_application_limit === null) {
            return true; // Unlimited
        }

        $applicationsThisMonth = $this->jobApplications()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        return $applicationsThisMonth < $plan->chat_application_limit;
    }

    /**
     * Check if user has unlimited chats feature.
     */
    public function hasUnlimitedChats()
    {
        $subscription = $this->currentSubscription();
        return $subscription && $subscription->subscriptionPlan->unlimited_chats;
    }

    /**
     * Check if user has access to premium job (via subscription or microtransaction).
     */
    public function hasAccessToPremiumJob($jobPostId)
    {
        // Check if user has advanced filters in their subscription
        $subscription = $this->currentSubscription();
        if ($subscription && $subscription->subscriptionPlan->advanced_filters) {
            return true;
        }

        // Check if user has paid for this specific job via microtransaction
        return $this->microtransactions()
            ->where('job_post_id', $jobPostId)
            ->where('status', 'completed')
            ->where('expires_at', '>', now())
            ->exists();
    }

    /**
     * Get remaining job posts for current month.
     */
    public function getRemainingJobPosts()
    {
        $subscription = $this->currentSubscription();
        if (!$subscription) {
            return 0;
        }

        $plan = $subscription->subscriptionPlan;
        if ($plan->job_post_limit === null) {
            return 999; // Unlimited
        }

        // Use permanent counter - doesn't decrease when jobs are deleted
        $jobPostsThisMonth = $this->getJobPostsUsedThisMonth();
        
        return max(0, $plan->job_post_limit - $jobPostsThisMonth);
    }

    /**
     * Get remaining job applications for current month.
     */
    public function getRemainingJobApplications()
    {
        $subscription = $this->currentSubscription();
        if (!$subscription) {
            return $this->isChatter() ? 999 : 0; // Chatters can apply without subscription
        }

        $plan = $subscription->subscriptionPlan;
        if ($plan->chat_application_limit === null) {
            return 999; // Unlimited
        }

        $applicationsThisMonth = $this->jobApplications()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        return max(0, $plan->chat_application_limit - $applicationsThisMonth);
    }

    /**
     * Get job posts used this month (from permanent counter).
     */
    public function getJobPostsUsedThisMonth()
    {
        $stats = $this->monthlyStats()
            ->where('year', now()->year)
            ->where('month', now()->month)
            ->first();
            
        return $stats ? $stats->jobs_posted : 0;
    }

    /**
     * Get job applications used this month.
     */
    public function getJobApplicationsUsedThisMonth()
    {
        return $this->jobApplications()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    /**
     * Get subscription tier information for dynamic layout adaptation.
     */
    public function getSubscriptionTier(): array
    {
        $subscription = $this->currentSubscription();
        
        if (!$subscription) {
            return [
                'tier' => 'free',
                'name' => 'Free Plan',
                'features' => ['basic_messaging'],
                'limits' => [
                    'job_posts' => 0,
                    'applications' => 5, // Free users can still apply to basic jobs
                    'conversations' => 3,
                ]
            ];
        }

        $plan = $subscription->subscriptionPlan;
        
        return [
            'tier' => $this->determineTierLevel($plan),
            'name' => $plan->name,
            'features' => $this->getAvailableFeatures($plan),
            'limits' => [
                'job_posts' => $plan->job_post_limit,
                'applications' => $plan->chat_application_limit,
                'conversations' => $plan->unlimited_chats ? null : 50,
            ],
            'expires_at' => $subscription->expires_at,
        ];
    }

    /**
     * Determine tier level based on subscription plan.
     */
    private function determineTierLevel($plan): string
    {
        if ($plan->price == 0) {
            return 'free';
        } elseif ($plan->price <= 29.99) {
            return 'basic';
        } elseif ($plan->price <= 59.99) {
            return 'premium';
        } else {
            return 'enterprise';
        }
    }

    /**
     * Get available features based on subscription plan.
     */
    private function getAvailableFeatures($plan): array
    {
        $features = ['basic_messaging'];
        
        if ($plan->unlimited_chats) $features[] = 'unlimited_chats';
        if ($plan->advanced_filters) $features[] = 'advanced_filters';
        if ($plan->analytics) $features[] = 'analytics';
        if ($plan->priority_listings) $features[] = 'priority_listings';
        if ($plan->featured_status) $features[] = 'featured_status';
        
        // Add messaging features for paid plans
        if ($plan->price > 0) {
            $features = array_merge($features, [
                'enhanced_messaging',
                'file_uploads',
                'voice_messages',
                'message_reactions',
                'conversation_search'
            ]);
        }
        
        return $features;
    }

    /**
     * Check if user has reached their subscription limits.
     */
    public function hasReachedLimit(string $limitType): bool
    {
        return match($limitType) {
            'job_posts' => !$this->canPostJob(),
            'applications' => !$this->canApplyToJob(),
            'conversations' => $this->hasReachedConversationLimit(),
            default => false
        };
    }

    /**
     * Check if user has reached conversation limit.
     */
    private function hasReachedConversationLimit(): bool
    {
        $subscription = $this->currentSubscription();
        
        if (!$subscription) {
            // Free users limited to 3 active conversations
            return $this->allConversations()->count() >= 3;
        }

        $plan = $subscription->subscriptionPlan;
        
        // Unlimited chats feature bypasses conversation limits
        if ($plan->unlimited_chats) {
            return false;
        }
        
        // Paid plans get higher limits
        $limit = $plan->price > 0 ? 50 : 3;
        return $this->allConversations()->count() >= $limit;
    }

    /**
     * Get usage statistics for the current subscription period.
     */
    public function getSubscriptionUsage(): array
    {
        return [
            'job_posts' => [
                'used' => $this->getJobPostsUsedThisMonth(),
                'limit' => $this->subscriptionPlan()?->job_post_limit,
                'remaining' => $this->getRemainingJobPosts()
            ],
            'applications' => [
                'used' => $this->getJobApplicationsUsedThisMonth(),
                'limit' => $this->subscriptionPlan()?->chat_application_limit,
                'remaining' => $this->getRemainingJobApplications()
            ],
            'conversations' => [
                'used' => $this->allConversations()->count(),
                'limit' => $this->hasFeatureAccess('unlimited_chats') ? null : ($this->hasActiveSubscription() ? 50 : 3)
            ]
        ];
    }

    /**
     * Check if user can access premium content or features.
     */
    public function canAccessPremiumContent(): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        $subscription = $this->currentSubscription();
        return $subscription && $subscription->subscriptionPlan->price > 0;
    }

    /**
     * Check if subscription requires renewal soon (within 7 days).
     */
    public function subscriptionRequiresRenewal(): bool
    {
        $subscription = $this->currentSubscription();
        
        if (!$subscription || !$subscription->expires_at) {
            return false;
        }
        
        return $subscription->expires_at->diffInDays(now()) <= 7;
    }

    /**
     * Calculate total cost for featured/urgent job features.
     */
    public function calculateJobFeatureCost($isFeatured = false, $isUrgent = false)
    {
        $cost = 0;
        
        if ($isFeatured) {
            $cost += 10; // $10 for featured job
        }
        
        if ($isUrgent) {
            $cost += 5; // $5 for urgent job
        }
        
        return $cost;
    }

    /**
     * Check if user can use featured job feature without payment.
     */
    public function canUseFeaturedForFree()
    {
        $subscription = $this->currentSubscription();
        return $subscription && $subscription->subscriptionPlan->featured_status;
    }

    /**
     * Check if user has priority listings feature.
     */
    public function hasPriorityListings()
    {
        $subscription = $this->currentSubscription();
        return $subscription && $subscription->subscriptionPlan->priority_listings;
    }

    /**
     * Override parent avatar method to handle persistence properly
     */
    public function avatar()
    {
        if ($this->avatar && \Storage::disk('public')->exists($this->avatar)) {
            return asset('storage/' . $this->avatar);
        }

        // Return a data URL for a generated avatar with user initials
        return $this->generateAvatarDataUrl();
    }

    /**
     * Generate a data URL for an avatar with user initials
     */
    private function generateAvatarDataUrl()
    {
        $initials = strtoupper(substr($this->name, 0, 1));
        if (str_contains($this->name, ' ')) {
            $parts = explode(' ', $this->name);
            $initials = strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1));
        }
        
        // Create a simple SVG avatar with initials
        $svg = '<svg width="100" height="100" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">';
        $svg .= '<rect width="100" height="100" fill="#6366f1"/>';
        $svg .= '<text x="50" y="50" font-family="Arial, sans-serif" font-size="36" font-weight="bold" fill="white" text-anchor="middle" dominant-baseline="middle">' . $initials . '</text>';
        $svg .= '</svg>';
        
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    /**
     * Get the user's profile picture URL
     */
    public function getProfilePictureUrl(): string
    {
        return $this->avatar();
    }

    /**
     * Get the user's profile picture URL as an attribute
     */
    public function getProfilePictureUrlAttribute(): string
    {
        return $this->getProfilePictureUrl();
    }

    protected static function boot()
    {
        parent::boot();

        // Listen for the creating event of the model
        static::creating(function ($user): void {
            // Check if the username attribute is empty
            if (empty($user->username)) {
                // Use the name to generate a slugified username
                $username = Str::slug($user->name, '');
                $i = 1;

                while (self::where('username', $username)->exists()) {
                    $username = Str::slug($user->name, '').$i;
                    $i++;
                }

                $user->username = $username;
            }
        });

        // Listen for the created event of the model
        static::created(function ($user): void {
            // Remove all roles
            $user->syncRoles([]);
            // Assign the default role
            $user->assignRole(config('wave.default_user_role', 'registered'));
        });
    }

    /**
     * Check if user is online (active within last 5 minutes)
     */
    public function isOnline(): bool
    {
        return $this->last_seen_at && $this->last_seen_at->diffInMinutes(now()) < 5;
    }
    
    /**
     * Check if user's profile is currently featured.
     */
    public function isProfileFeatured(): bool
    {
        return $this->userProfile && 
               $this->userProfile->is_featured && 
               $this->userProfile->featured_until && 
               $this->userProfile->featured_until->isFuture();
    }
    
    /**
     * Get the featured profile until date.
     */
    public function getFeaturedUntil()
    {
        return $this->userProfile?->featured_until;
    }
    
    /**
     * Get user type change requests.
     */
    public function userTypeChangeRequests()
    {
        return $this->hasMany(UserTypeChangeRequest::class);
    }
    
    /**
     * Check if user type is locked.
     */
    public function isUserTypeLocked(): bool
    {
        return $this->user_type_locked ?? false;
    }
    
    /**
     * Lock user type (usually done after registration).
     */
    public function lockUserType(): void
    {
        $this->update([
            'user_type_locked' => true,
            'user_type_locked_at' => now(),
        ]);
    }
    
    /**
     * Unlock user type (admin only).
     */
    public function unlockUserType(): void
    {
        $this->update([
            'user_type_locked' => false,
            'user_type_locked_at' => null,
        ]);
    }
    
    /**
     * Check if user can change their user type.
     */
    public function canChangeUserType(): bool
    {
        return !$this->isUserTypeLocked() || $this->isAdmin();
    }
    
    /**
     * Get pending user type change request.
     */
    public function getPendingUserTypeChangeRequest()
    {
        return $this->userTypeChangeRequests()
            ->where('status', 'pending')
            ->latest()
            ->first();
    }
    
    /**
     * Check if user has a pending user type change request.
     */
    public function hasPendingUserTypeChangeRequest(): bool
    {
        return $this->getPendingUserTypeChangeRequest() !== null;
    }
    
    /**
     * Request user type change.
     */
    public function requestUserTypeChange(int $newUserTypeId, string $reason, array $supportingDocuments = []): UserTypeChangeRequest
    {
        // Cancel any existing pending requests
        $this->userTypeChangeRequests()
            ->where('status', 'pending')
            ->update(['status' => 'cancelled']);
            
        return $this->userTypeChangeRequests()->create([
            'current_user_type_id' => $this->user_type_id,
            'requested_user_type_id' => $newUserTypeId,
            'reason' => $reason,
            'supporting_documents' => $supportingDocuments,
            'status' => 'pending',
        ]);
    }

    /**
     * Check if user has completed all training modules.
     */
    public function hasCompletedAllTraining(): bool
    {
        // Get count of active training modules
        $totalModules = \App\Models\TrainingModule::where('is_active', true)->count();
        
        if ($totalModules === 0) {
            return true; // No modules required
        }
        
        // Get count of completed modules for this user
        $completedModules = $this->trainingProgress()
            ->where('status', 'completed')
            ->whereHas('trainingModule', function($query) {
                $query->where('is_active', true);
            })
            ->count();
            
        return $completedModules >= $totalModules;
    }

    /**
     * Check if user has passed at least one typing test.
     */
    public function hasPassedTypingTest(): bool
    {
        return $this->userTestResults()
            ->where('testable_type', 'App\\Models\\TypingTest')
            ->where('passed', true)
            ->exists();
    }

    /**
     * Check if user meets all requirements to appear in talent marketplace.
     */
    public function meetsMarketplaceRequirements(): bool
    {
        $requirements = [
            $this->hasVerifiedEmail()
        ];
        
        if ($this->userType && $this->userType->requires_kyc) {
            $requirements[] = $this->isKycVerified();
        }
        
        return !in_array(false, $requirements);
    }

    /**
     * Get the requirements status for marketplace visibility.
     */
    public function getMarketplaceRequirementsStatus(): array
    {
        $requirements = [
            'email_verified' => $this->hasVerifiedEmail()
        ];
        
        if ($this->userType && $this->userType->requires_kyc) {
            $requirements['kyc_completed'] = $this->isKycVerified();
        }

        // Training and typing tests are not required for marketplace visibility,
        // but we can still check their status for displaying on the profile.
        if ($this->isChatter() || $this->isVA()) {
            $requirements['training_completed'] = $this->hasCompletedAllTraining();
        }

        if ($this->isChatter()) {
            $requirements['typing_test_passed'] = $this->hasPassedTypingTest();
        }
        
        return $requirements;
    }
}
