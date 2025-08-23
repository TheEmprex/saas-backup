<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class UserTestResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'testable_type',
        'testable_id',
        'score',
        'accuracy',
        'wpm',
        'answers',
        'passed',
        'time_taken_seconds',
        'completed_at'
    ];

    protected $casts = [
        'accuracy' => 'decimal:2',
        'answers' => 'array',
        'passed' => 'boolean',
        'completed_at' => 'datetime'
    ];

    /**
     * Get the user that owns the test result.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the testable model (TypingTest or TrainingTest).
     */
    public function testable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to only include passed results.
     */
    public function scopePassed($query)
    {
        return $query->where('passed', true);
    }

    /**
     * Scope a query to only include failed results.
     */
    public function scopeFailed($query)
    {
        return $query->where('passed', false);
    }

    /**
     * Scope a query to only include typing test results.
     */
    public function scopeTypingTests($query)
    {
        return $query->where('testable_type', TypingTest::class);
    }

    /**
     * Scope a query to only include training test results.
     */
    public function scopeTrainingTests($query)
    {
        return $query->where('testable_type', TrainingTest::class);
    }

    /**
     * Get formatted time taken.
     */
    public function getFormattedTimeAttribute(): string
    {
        $minutes = floor($this->time_taken_seconds / 60);
        $seconds = $this->time_taken_seconds % 60;
        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    /**
     * Check if this is a typing test result.
     */
    public function isTypingTest(): bool
    {
        return $this->testable_type === TypingTest::class;
    }

    /**
     * Check if this is a training test result.
     */
    public function isTrainingTest(): bool
    {
        return $this->testable_type === TrainingTest::class;
    }
}
