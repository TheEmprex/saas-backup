<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class UserAvailability extends Model
{
    protected $fillable = [
        'user_id',
        'day_of_week',
        'start_time',
        'end_time',
        'timezone',
        'is_available'
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_available' => 'boolean'
    ];

    /**
     * Get the user that owns this availability.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all days of the week.
     */
    public static function getDaysOfWeek()
    {
        return [
            'monday' => 'Monday',
            'tuesday' => 'Tuesday', 
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday'
        ];
    }

    /**
     * Convert availability to a target timezone.
     */
    public function convertToTimezone($targetTimezone)
    {
        try {
            $userTz = new \DateTimeZone($this->timezone);
            $targetTz = new \DateTimeZone($targetTimezone);
            
            // Create datetime objects for today in user's timezone
            $startDateTime = new \DateTime('today ' . $this->start_time, $userTz);
            $endDateTime = new \DateTime('today ' . $this->end_time, $userTz);
            
            // Convert to target timezone
            $startDateTime->setTimezone($targetTz);
            $endDateTime->setTimezone($targetTz);
            
            return [
                'day_of_week' => $this->day_of_week,
                'start_time' => $startDateTime->format('H:i'),
                'end_time' => $endDateTime->format('H:i'),
                'timezone' => $targetTimezone,
                'original_timezone' => $this->timezone
            ];
        } catch (\Exception $e) {
            return [
                'day_of_week' => $this->day_of_week,
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
                'timezone' => $this->timezone,
                'error' => 'Timezone conversion failed'
            ];
        }
    }

    /**
     * Check if this availability overlaps with a given time range.
     */
    public function overlapsWithTimeRange($startTime, $endTime, $targetTimezone = null)
    {
        $availability = $targetTimezone ? 
            $this->convertToTimezone($targetTimezone) : 
            [
                'start_time' => $this->start_time,
                'end_time' => $this->end_time
            ];

        $availStart = strtotime($availability['start_time']);
        $availEnd = strtotime($availability['end_time']);
        $rangeStart = strtotime($startTime);
        $rangeEnd = strtotime($endTime);

        return $availStart < $rangeEnd && $availEnd > $rangeStart;
    }

    /**
     * Get availability summary for a user in a specific timezone.
     */
    public static function getAvailabilitySummary($userId, $targetTimezone = null)
    {
        $availabilities = self::where('user_id', $userId)
            ->where('is_available', true)
            ->orderByRaw("FIELD(day_of_week, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday')")
            ->get();

        $summary = [];
        foreach ($availabilities as $availability) {
            $converted = $targetTimezone ? 
                $availability->convertToTimezone($targetTimezone) : 
                [
                    'day_of_week' => $availability->day_of_week,
                    'start_time' => $availability->start_time,
                    'end_time' => $availability->end_time,
                    'timezone' => $availability->timezone
                ];
            
            $summary[] = $converted;
        }

        return $summary;
    }

    /**
     * Get common timezones for selection.
     */
    public static function getCommonTimezones()
    {
        return [
            'America/New_York' => 'Eastern Time (ET)',
            'America/Chicago' => 'Central Time (CT)',
            'America/Denver' => 'Mountain Time (MT)',
            'America/Los_Angeles' => 'Pacific Time (PT)',
            'America/Phoenix' => 'Arizona Time',
            'America/Toronto' => 'Eastern Time (Canada)',
            'America/Vancouver' => 'Pacific Time (Canada)',
            'Europe/London' => 'Greenwich Mean Time (GMT)',
            'Europe/Paris' => 'Central European Time (CET)',
            'Europe/Berlin' => 'Central European Time (CET)',
            'Europe/Rome' => 'Central European Time (CET)',
            'Europe/Madrid' => 'Central European Time (CET)',
            'Europe/Amsterdam' => 'Central European Time (CET)',
            'Europe/Stockholm' => 'Central European Time (CET)',
            'Europe/Zurich' => 'Central European Time (CET)',
            'Asia/Tokyo' => 'Japan Standard Time (JST)',
            'Asia/Shanghai' => 'China Standard Time (CST)',
            'Asia/Kolkata' => 'India Standard Time (IST)',
            'Asia/Dubai' => 'Gulf Standard Time (GST)',
            'Asia/Singapore' => 'Singapore Standard Time (SGT)',
            'Asia/Manila' => 'Philippine Standard Time (PST)',
            'Asia/Bangkok' => 'Indochina Time (ICT)',
            'Asia/Jakarta' => 'Western Indonesia Time (WIB)',
            'Australia/Sydney' => 'Australian Eastern Time (AET)',
            'Australia/Melbourne' => 'Australian Eastern Time (AET)',
            'Australia/Perth' => 'Australian Western Time (AWT)',
            'Pacific/Auckland' => 'New Zealand Standard Time (NZST)',
            'Africa/Cairo' => 'Eastern European Time (EET)',
            'Africa/Johannesburg' => 'South Africa Standard Time (SAST)',
            'America/Sao_Paulo' => 'Brasília Time (BRT)',
            'America/Argentina/Buenos_Aires' => 'Argentina Time (ART)',
            'America/Mexico_City' => 'Central Standard Time (Mexico)',
            'UTC' => 'Coordinated Universal Time (UTC)'
        ];
    }

    /**
     * Get availability for multiple users in a target timezone.
     */
    public static function getBulkAvailability(array $userIds, $targetTimezone = null)
    {
        $availabilities = self::whereIn('user_id', $userIds)
            ->where('is_available', true)
            ->with('user:id,name')
            ->get()
            ->groupBy('user_id');

        $result = [];
        foreach ($userIds as $userId) {
            $userAvailabilities = $availabilities->get($userId, collect());
            $converted = [];
            
            foreach ($userAvailabilities as $availability) {
                $converted[] = $targetTimezone ? 
                    $availability->convertToTimezone($targetTimezone) : 
                    [
                        'day_of_week' => $availability->day_of_week,
                        'start_time' => $availability->start_time,
                        'end_time' => $availability->end_time,
                        'timezone' => $availability->timezone
                    ];
            }
            
            $result[$userId] = [
                'user' => $userAvailabilities->first()?->user,
                'availability' => $converted
            ];
        }

        return $result;
    }

    /**
     * Find users available during specific hours in a timezone.
     */
    public static function findAvailableUsers($dayOfWeek, $startTime, $endTime, $timezone)
    {
        return self::where('day_of_week', $dayOfWeek)
            ->where('is_available', true)
            ->get()
            ->filter(function ($availability) use ($startTime, $endTime, $timezone) {
                return $availability->overlapsWithTimeRange($startTime, $endTime, $timezone);
            })
            ->pluck('user_id')
            ->unique();
    }

    /**
     * Create default availability for a user (9 AM - 5 PM in their timezone, Monday-Friday).
     */
    public static function createDefaultAvailability($userId, $timezone = 'UTC')
    {
        $workdays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        
        foreach ($workdays as $day) {
            self::create([
                'user_id' => $userId,
                'day_of_week' => $day,
                'start_time' => '09:00',
                'end_time' => '17:00',
                'timezone' => $timezone,
                'is_available' => true
            ]);
        }
    }
}
