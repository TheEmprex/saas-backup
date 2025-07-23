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
use App\Traits\ReviewHelper;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends WaveUser implements MustVerifyEmail
{
    use HasProfileKeyValues;
    use Notifiable;
    use ReviewHelper;
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
        'is_banned',
        'banned_at',
        'ban_reason',
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
        if ($this->isChatter()) {
            return !$this->isKycVerified();
        }
        
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
    public function ban(string $reason = null): void
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
}
