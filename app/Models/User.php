<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Wave\Traits\HasProfileKeyValues;
use Wave\User as WaveUser;

class User extends WaveUser
{
    use HasProfileKeyValues;
    use Notifiable;
    public $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'avatar',
        'password',
        'role_id',
        'verification_code',
        'verified',
        'trial_ends_at',
        'user_type_id',
        'last_seen_at',
        'kyc_status',
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
    ];

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
        return $this->userType->name === 'chatter';
    }

    public function isAgency(): bool
    {
        return in_array($this->userType->name, ['ofm_agency', 'chatting_agency']);
    }

    public function requiresVerification(): bool
    {
        if ($this->isChatter()) {
            return ! $this->isKycVerified();
        }

        if ($this->isAgency()) {
            return ! $this->isEarningsVerified();
        }

        return false;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
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
        return $this->hasMany(Rating::class, 'rated_id');
    }

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
     * Check if user has an active subscription.
     */
    public function hasActiveSubscription(): bool
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
     * Check if user can post jobs based on their subscription limits.
     */
    public function canPostJob()
    {
        $subscription = $this->currentSubscription();

        if (! $subscription) {
            return false;
        }

        $plan = $subscription->subscriptionPlan;

        if ($plan->job_post_limit === null) {
            return true; // Unlimited
        }

        $currentMonth = now()->format('Y-m');
        $jobPostsThisMonth = $this->jobPosts()->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$currentMonth])->count();

        return $jobPostsThisMonth < $plan->job_post_limit;
    }

    /**
     * Check if user can apply to jobs based on their subscription limits.
     */
    public function canApplyToJob()
    {
        $subscription = $this->currentSubscription();

        if (! $subscription) {
            return $this->isChatter(); // Chatters can apply without subscription
        }

        $plan = $subscription->subscriptionPlan;

        if ($plan->chat_application_limit === null) {
            return true; // Unlimited
        }

        $currentMonth = now()->format('Y-m');
        $applicationsThisMonth = $this->jobApplications()->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$currentMonth])->count();

        return $applicationsThisMonth < $plan->chat_application_limit;
    }

    /**
     * Check if user has unlimited chats feature.
     */
    public function hasUnlimitedChats(): bool
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
    public function getRemainingJobPosts(): int|float
    {
        $subscription = $this->currentSubscription();

        if (! $subscription) {
            return 0;
        }

        $plan = $subscription->subscriptionPlan;

        if ($plan->job_post_limit === null) {
            return 999; // Unlimited
        }

        $currentMonth = now()->format('Y-m');
        $jobPostsThisMonth = $this->jobPosts()->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$currentMonth])->count();

        return max(0, $plan->job_post_limit - $jobPostsThisMonth);
    }

    /**
     * Get remaining job applications for current month.
     */
    public function getRemainingJobApplications(): int|float
    {
        $subscription = $this->currentSubscription();

        if (! $subscription) {
            return $this->isChatter() ? 999 : 0; // Chatters can apply without subscription
        }

        $plan = $subscription->subscriptionPlan;

        if ($plan->chat_application_limit === null) {
            return 999; // Unlimited
        }

        $currentMonth = now()->format('Y-m');
        $applicationsThisMonth = $this->jobApplications()->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$currentMonth])->count();

        return max(0, $plan->chat_application_limit - $applicationsThisMonth);
    }

    /**
     * Get job posts used this month.
     */
    public function getJobPostsUsedThisMonth()
    {
        $currentMonth = now()->format('Y-m');

        return $this->jobPosts()->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$currentMonth])->count();
    }

    /**
     * Get job applications used this month.
     */
    public function getJobApplicationsUsedThisMonth()
    {
        $currentMonth = now()->format('Y-m');

        return $this->jobApplications()->whereRaw('DATE_FORMAT(created_at, "%Y-%m") = ?', [$currentMonth])->count();
    }

    /**
     * Calculate total cost for featured/urgent job features.
     */
    public function calculateJobFeatureCost($isFeatured = false, $isUrgent = false): int
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
    public function canUseFeaturedForFree(): bool
    {
        $subscription = $this->currentSubscription();

        return $subscription && $subscription->subscriptionPlan->featured_status;
    }

    /**
     * Check if user has priority listings feature.
     */
    public function hasPriorityListings(): bool
    {
        $subscription = $this->currentSubscription();

        return $subscription && $subscription->subscriptionPlan->priority_listings;
    }

    /**
     * Get the user's profile picture URL
     */
    public function getProfilePictureUrl(): string
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return asset('storage/'.$this->avatar);
        }

        return asset('images/default-avatar.png');
    }

    /**
     * Get the user's profile picture URL as an attribute
     */
    public function getProfilePictureUrlAttribute(): string
    {
        return $this->getProfilePictureUrl();
    }
}
