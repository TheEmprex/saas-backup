<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkShift extends Model
{
    protected $fillable = [
        'employment_contract_id',
        'chatter_id',
        'agency_id',
        'shift_start',
        'shift_end',
        'total_minutes',
        'hourly_rate',
        'total_earnings',
        'status',
        'shift_notes',
        'agency_notes',
        'performance_metrics',
        'reviewed_by_agency',
        'reviewed_at',
    ];

    protected $casts = [
        'shift_start' => 'datetime',
        'shift_end' => 'datetime',
        'total_minutes' => 'integer',
        'hourly_rate' => 'decimal:2',
        'total_earnings' => 'decimal:2',
        'performance_metrics' => 'array',
        'reviewed_by_agency' => 'boolean',
        'reviewed_at' => 'datetime',
    ];

    public function employmentContract(): BelongsTo
    {
        return $this->belongsTo(EmploymentContract::class);
    }

    public function chatter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'chatter_id');
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agency_id');
    }

    public function shiftReview(): BelongsTo
    {
        return $this->belongsTo(ShiftReview::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function calculateEarnings(): void
    {
        $this->total_minutes = $this->shift_end->diffInMinutes($this->shift_start);
        $this->total_earnings = ($this->total_minutes / 60) * $this->hourly_rate;
        $this->save();
    }
}
