<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMonthlyStat extends Model
{
    protected $fillable = [
        'user_id',
        'year',
        'month',
        'jobs_posted',
        'applications_sent',
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'jobs_posted' => 'integer',
        'applications_sent' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get or create stats for a specific month/year
     */
    public static function getOrCreateForMonth($userId, $year = null, $month = null)
    {
        $year = $year ?? now()->year;
        $month = $month ?? now()->month;

        return static::firstOrCreate(
            [
                'user_id' => $userId,
                'year' => $year,
                'month' => $month,
            ],
            [
                'jobs_posted' => 0,
                'applications_sent' => 0,
            ]
        );
    }

    /**
     * Increment jobs posted counter
     */
    public function incrementJobsPosted()
    {
        $this->increment('jobs_posted');
    }

    /**
     * Increment applications sent counter
     */
    public function incrementApplicationsSent()
    {
        $this->increment('applications_sent');
    }
}
