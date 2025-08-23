<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class UserAvailabilitySchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'day_of_week',
        'start_time',
        'end_time',
        'is_available',
        'break_times',
        'notes',
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_available' => 'boolean',
        'break_times' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get availability in a different timezone
     */
    public function getAvailabilityInTimezone(string $targetTimezone): array
    {
        if (!$this->is_available || !$this->start_time || !$this->end_time) {
            return [];
        }

        $userTimezone = $this->user->timezone ?? 'UTC';
        
        // Create Carbon instances for start and end times in user's timezone
        $startTime = Carbon::createFromFormat('H:i', $this->start_time, $userTimezone);
        $endTime = Carbon::createFromFormat('H:i', $this->end_time, $userTimezone);
        
        // Convert to target timezone
        $convertedStart = $startTime->setTimezone($targetTimezone);
        $convertedEnd = $endTime->setTimezone($targetTimezone);
        
        return [
            'day_of_week' => $this->day_of_week,
            'start_time' => $convertedStart->format('H:i'),
            'end_time' => $convertedEnd->format('H:i'),
            'start_day' => $convertedStart->format('l'),
            'end_day' => $convertedEnd->format('l'),
            'is_next_day' => $convertedStart->day !== $convertedEnd->day,
        ];
    }

    /**
     * Common timezones list
     */
    public static function getCommonTimezones(): array
    {
        return [
            'UTC' => 'UTC',
            'America/New_York' => 'Eastern Time (ET)',
            'America/Chicago' => 'Central Time (CT)', 
            'America/Denver' => 'Mountain Time (MT)',
            'America/Los_Angeles' => 'Pacific Time (PT)',
            'Europe/London' => 'London (GMT/BST)',
            'Europe/Paris' => 'Paris (CET/CEST)',
            'Europe/Berlin' => 'Berlin (CET/CEST)',
            'Asia/Tokyo' => 'Tokyo (JST)',
            'Asia/Shanghai' => 'Shanghai (CST)',
            'Asia/Kolkata' => 'India (IST)',
            'Australia/Sydney' => 'Sydney (AEST/AEDT)',
            'Asia/Manila' => 'Manila (PHT)',
        ];
    }

    /**
     * Days of the week
     */
    public static function getDaysOfWeek(): array
    {
        return [
            'monday' => 'Monday',
            'tuesday' => 'Tuesday', 
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday',
        ];
    }
}
