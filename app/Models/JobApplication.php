<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplication extends Model
{
    protected $fillable = [
        'job_post_id',
        'user_id',
        'cover_letter',
        'proposed_rate',
        'available_hours',
        'attachments',
        'additional_notes',
        'status',
        'rejection_reason',
        'reviewed_at',
        'responded_at',
        'is_premium',
        'application_fee',
        'typing_test_wpm',
        'typing_test_accuracy',
        'typing_test_taken_at',
        'typing_test_results',
        'typing_test_passed',
    ];

    protected $casts = [
        'proposed_rate' => 'decimal:2',
        'available_hours' => 'integer',
        'attachments' => 'array',
        'reviewed_at' => 'datetime',
        'responded_at' => 'datetime',
        'is_premium' => 'boolean',
        'application_fee' => 'decimal:2',
        'typing_test_wpm' => 'integer',
        'typing_test_accuracy' => 'integer',
        'typing_test_taken_at' => 'datetime',
        'typing_test_results' => 'array',
        'typing_test_passed' => 'boolean',
    ];

    public function jobPost(): BelongsTo
    {
        return $this->belongsTo(JobPost::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
