<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'user_type_id',
        'kyc_verified',
        'kyc_document_type',
        'kyc_document_number',
        'kyc_document_path',
        'kyc_verified_at',
        'typing_speed_wpm',
        'typing_accuracy',
        'typing_test_taken_at',
        'english_proficiency_score',
        'experience_agencies',
        'traffic_sources',
        'availability_timezone',
        'availability_hours',
        'company_name',
        'company_description',
        'stripe_account_id',
        'paxum_account_id',
        'results_screenshots',
        'team_members',
        'average_rating',
        'total_ratings',
        'jobs_completed',
        'bio',
        'portfolio_links',
        'is_verified',
        'is_active',
        // Additional profile fields
        'location',
        'website',
        'phone',
        'experience_years',
        'languages',
        'skills',
        'services',
        'availability',
        'hourly_rate',
        'preferred_rate_type',
        'portfolio_url',
        'linkedin_url',
        'views',
        'is_available',
        'response_time',
        // Featured profile fields
        'is_featured',
        'featured_until',
        'featured_payment_amount',
        'featured_payment_id',
        'featured_paid_at',
        // New agency fields
        'monthly_revenue',
        'traffic_types',
        'timezone',
        'availability_hours',
        'shift_requirements',
    ];

    protected $casts = [
        'kyc_verified' => 'boolean',
        'kyc_verified_at' => 'datetime',
        'typing_speed_wpm' => 'integer',
        'typing_accuracy' => 'integer',
        'typing_test_taken_at' => 'datetime',
        'english_proficiency_score' => 'integer',
        'experience_agencies' => 'array',
        'traffic_sources' => 'array',
        'availability_hours' => 'array',
        'results_screenshots' => 'array',
        'team_members' => 'array',
        'average_rating' => 'decimal:2',
        'total_ratings' => 'integer',
        'jobs_completed' => 'integer',
        'portfolio_links' => 'array',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        // Additional casts for new fields
        'languages' => 'array',
        'skills' => 'array',
        'services' => 'array',
        'experience_years' => 'integer',
        'hourly_rate' => 'decimal:2',
        'views' => 'integer',
        'is_available' => 'boolean',
        // Featured profile casts
        'is_featured' => 'boolean',
        'featured_until' => 'datetime',
        'featured_payment_amount' => 'decimal:2',
        'featured_paid_at' => 'datetime',
        // New agency field casts
        'traffic_types' => 'array',
        'shift_requirements' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function userType(): BelongsTo
    {
        return $this->belongsTo(UserType::class);
    }
}
