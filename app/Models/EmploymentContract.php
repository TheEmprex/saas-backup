<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmploymentContract extends Model
{
    protected $fillable = [
        'agency_id',
        'chatter_id',
        'job_application_id',
        'job_post_id',
        'agreed_rate',
        'expected_hours_per_week',
        'contract_terms',
        'special_instructions',
        'status',
        'start_date',
        'end_date',
        'terminated_at',
        'termination_reason',
        'terminated_by',
        'average_rating',
        'total_shifts',
        'total_hours_worked',
        'total_earnings',
    ];

    protected $casts = [
        'agreed_rate' => 'decimal:2',
        'expected_hours_per_week' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'terminated_at' => 'datetime',
        'average_rating' => 'decimal:2',
        'total_shifts' => 'integer',
        'total_hours_worked' => 'integer',
        'total_earnings' => 'decimal:2',
    ];

    public function agency(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agency_id');
    }

    public function chatter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'chatter_id');
    }

    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class);
    }

    public function jobPost(): BelongsTo
    {
        return $this->belongsTo(JobPost::class);
    }

    public function terminatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'terminated_by');
    }

    public function workShifts(): HasMany
    {
        return $this->hasMany(WorkShift::class);
    }

    public function shiftReviews(): HasMany
    {
        return $this->hasMany(ShiftReview::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isTerminated(): bool
    {
        return $this->status === 'terminated';
    }

    public function updateAverageRating(): void
    {
        $avgRating = $this->shiftReviews()->avg('overall_rating');
        $this->update(['average_rating' => $avgRating]);
    }

    public function updateTotals(): void
    {
        $completedShifts = $this->workShifts()->where('status', 'completed');

        $this->update([
            'total_shifts' => $completedShifts->count(),
            'total_hours_worked' => $completedShifts->sum('total_minutes') / 60,
            'total_earnings' => $completedShifts->sum('total_earnings'),
        ]);
    }
}
