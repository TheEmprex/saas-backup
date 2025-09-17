<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShiftReview extends Model
{
    protected $fillable = [
        'work_shift_id',
        'employment_contract_id',
        'reviewer_id',
        'chatter_id',
        'overall_rating',
        'communication_rating',
        'reliability_rating',
        'quality_rating',
        'professionalism_rating',
        'review_comment',
        'positive_feedback',
        'areas_for_improvement',
        'on_time',
        'completed_tasks',
        'followed_instructions',
        'professional_behavior',
        'would_hire_again',
        'recommend_to_others',
    ];

    protected $casts = [
        'overall_rating' => 'integer',
        'communication_rating' => 'integer',
        'reliability_rating' => 'integer',
        'quality_rating' => 'integer',
        'professionalism_rating' => 'integer',
        'on_time' => 'boolean',
        'completed_tasks' => 'boolean',
        'followed_instructions' => 'boolean',
        'professional_behavior' => 'boolean',
        'would_hire_again' => 'boolean',
        'recommend_to_others' => 'boolean',
    ];

    public function workShift(): BelongsTo
    {
        return $this->belongsTo(WorkShift::class);
    }

    public function employmentContract(): BelongsTo
    {
        return $this->belongsTo(EmploymentContract::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function chatter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'chatter_id');
    }

    public function isPositiveReview(): bool
    {
        return $this->overall_rating >= 4;
    }

    public function hasIssues(): bool
    {
        return ! $this->on_time || ! $this->completed_tasks || ! $this->followed_instructions || ! $this->professional_behavior;
    }
}
