<?php

declare(strict_types=1);

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
        'availability',
        'hourly_rate',
        'preferred_rate_type',
        'portfolio_url',
        'linkedin_url',
        'views',
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
        'experience_years' => 'integer',
        'hourly_rate' => 'decimal:2',
        'views' => 'integer',
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
