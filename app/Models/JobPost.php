<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\QueryBuilders\JobPostQueryBuilder;

class JobPost extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'requirements',
        'min_typing_speed',
        'min_english_proficiency',
        'required_traffic_sources',
        'market',
        'experience_level',
        'expected_response_time',
        'hourly_rate',
        'fixed_rate',
        'rate_type',
        'commission_percentage',
        'hours_per_week',
        'timezone_preference',
        'working_hours',
        'contract_type',
        'start_date',
        'end_date',
        'status',
        'is_featured',
        'is_urgent',
        'featured_cost',
        'urgent_cost',
        'feature_payment_required',
        'payment_status',
        'payment_intent_id',
        'payment_completed_at',
        'max_applications',
        'current_applications',
        'expires_at',
        'tags',
        'views',
        'benefits',
        'expected_hours_per_week',
        'duration_months',
        // Timezone and shift fields
        'required_timezone',
        'shift_start_time',
        'shift_end_time',
        'preferred_start_time',
        'preferred_end_time',
        'required_days',
        'timezone_flexible',
        'shift_notes'
    ];

    protected $casts = [
        'requirements' => 'array',
        'required_traffic_sources' => 'array',
        'working_hours' => 'array',
        'tags' => 'array',
        'hourly_rate' => 'decimal:2',
        'fixed_rate' => 'decimal:2',
        'commission_percentage' => 'decimal:2',
        'featured_cost' => 'decimal:2',
        'urgent_cost' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_urgent' => 'boolean',
        'feature_payment_required' => 'boolean',
        'payment_completed_at' => 'datetime',
        'start_date' => 'date',
        'end_date' => 'date',
        'expires_at' => 'datetime',
        // Timezone and shift casts
        'required_days' => 'array',
        'shift_start_time' => 'datetime:H:i',
        'shift_end_time' => 'datetime:H:i',
        'timezone_flexible' => 'boolean'
    ];

    /**
     * Get the user who posted this job.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the applications for this job.
     */
    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    /**
     * Get the messages related to this job.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get the ratings related to this job.
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Scope for active jobs.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for featured jobs.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Get job shift time in a specific timezone.
     */
    public function getShiftTimeInTimezone($targetTimezone)
    {
        if (!$this->required_timezone || !$this->shift_start_time || !$this->shift_end_time) {
            return null;
        }

        try {
            $jobTz = new \DateTimeZone($this->required_timezone);
            $targetTz = new \DateTimeZone($targetTimezone);
            
            $startDateTime = new \DateTime('today ' . $this->shift_start_time, $jobTz);
            $endDateTime = new \DateTime('today ' . $this->shift_end_time, $jobTz);
            
            $startDateTime->setTimezone($targetTz);
            $endDateTime->setTimezone($targetTz);
            
            return [
                'start_time' => $startDateTime->format('H:i'),
                'end_time' => $endDateTime->format('H:i'),
                'timezone' => $targetTimezone,
                'original_timezone' => $this->required_timezone
            ];
        } catch (\Exception $e) {
            return [
                'start_time' => $this->shift_start_time,
                'end_time' => $this->shift_end_time,
                'timezone' => $this->required_timezone,
                'error' => 'Timezone conversion failed'
            ];
        }
    }

    /**
     * Check if a user's availability matches this job's requirements.
     */
    public function matchesUserAvailability($userId, $targetTimezone = null)
    {
        $userAvailability = UserAvailability::where('user_id', $userId)
            ->where('is_available', true)
            ->get();

        if ($userAvailability->isEmpty()) {
            return false;
        }

        $requiredDays = $this->required_days ?? [];
        if (empty($requiredDays)) {
            return true; // No specific days required
        }

        $jobShift = $targetTimezone ? $this->getShiftTimeInTimezone($targetTimezone) : [
            'start_time' => $this->shift_start_time,
            'end_time' => $this->shift_end_time
        ];

        if (!$jobShift || !isset($jobShift['start_time'])) {
            return true; // No shift requirements
        }

        // Check if user has availability that overlaps with job requirements
        foreach ($userAvailability as $availability) {
            if (in_array($availability->day_of_week, $requiredDays)) {
                if ($availability->overlapsWithTimeRange(
                    $jobShift['start_time'],
                    $jobShift['end_time'],
                    $targetTimezone
                )) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Scope to filter jobs by timezone compatibility.
     */
    public function scopeByTimezoneCompatibility($query, $userTimezone, $userId = null)
    {
        return $query->where(function ($q) use ($userTimezone, $userId) {
            // Include jobs that are timezone flexible
            $q->where('timezone_flexible', true)
              // Or jobs that don't have timezone requirements
              ->orWhereNull('required_timezone')
              // Or jobs in the user's timezone
              ->orWhere('required_timezone', $userTimezone);
              
            // If userId provided, also check availability compatibility
            if ($userId) {
                $q->orWhereExists(function ($query) use ($userId, $userTimezone) {
                    $query->selectRaw('1')
                          ->from('user_availability')
                          ->where('user_id', $userId)
                          ->where('is_available', true);
                    // Additional availability matching logic would go here
                });
            }
        });
    }

    /**
     * Create a new Eloquent query builder with custom methods
     */
    public function newEloquentBuilder($query): JobPostQueryBuilder
    {
        return new JobPostQueryBuilder($query);
    }
}
