<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'duration_months'
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
        'expires_at' => 'datetime'
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
}
