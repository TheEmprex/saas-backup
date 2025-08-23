<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserAvailabilitySchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class UserTimezoneAvailabilityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the user's availability management page.
     */
    public function index()
    {
        $user = auth()->user();
        $schedules = $user->availabilitySchedule()->get()->keyBy('day_of_week');
        $timezones = UserAvailabilitySchedule::getCommonTimezones();
        $days = UserAvailabilitySchedule::getDaysOfWeek();
        
        return view('profile.availability', compact('schedules', 'timezones', 'days', 'user'));
    }

    /**
     * Update user's timezone and availability.
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'timezone' => 'required|string|in:' . implode(',', array_keys(UserAvailabilitySchedule::getCommonTimezones())),
            'available_for_work' => 'boolean',
            'hourly_rate' => 'nullable|numeric|min:0|max:999.99',
            'preferred_currency' => 'required|string|size:3',
            'availability' => 'array',
            'availability.*.is_available' => 'boolean',
            'availability.*.start_time' => 'nullable|date_format:H:i',
            'availability.*.end_time' => 'nullable|date_format:H:i',
            'availability.*.break_times' => 'nullable|array',
            'availability.*.break_times.*.start' => 'nullable|date_format:H:i',
            'availability.*.break_times.*.end' => 'nullable|date_format:H:i',
            'availability.*.notes' => 'nullable|string|max:500',
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        // Update user's timezone and work preferences
        $user->update([
            'timezone' => $request->timezone,
            'available_for_work' => $request->boolean('available_for_work'),
            'hourly_rate' => $request->hourly_rate,
            'preferred_currency' => $request->preferred_currency,
        ]);
        
        // Update availability schedule
        if ($request->has('availability')) {
            foreach ($request->availability as $day => $schedule) {
                $user->availabilitySchedule()->updateOrCreate(
                    ['day_of_week' => $day],
                    [
                        'is_available' => $schedule['is_available'] ?? false,
                        'start_time' => $schedule['start_time'] ?? null,
                        'end_time' => $schedule['end_time'] ?? null,
                        'break_times' => $schedule['break_times'] ?? null,
                        'notes' => $schedule['notes'] ?? null,
                    ]
                );
            }
        }
        
        return back()->with('success', 'Availability updated successfully!');
    }

    /**
     * Get user availability in a specific timezone (API endpoint).
     */
    public function getAvailabilityInTimezone(Request $request, User $user)
    {
        $targetTimezone = $request->get('timezone', 'UTC');
        
        $availability = $user->availabilitySchedule()
            ->where('is_available', true)
            ->get()
            ->map(function ($schedule) use ($targetTimezone) {
                return $schedule->getAvailabilityInTimezone($targetTimezone);
            })
            ->filter() // Remove empty results
            ->values();
        
        return response()->json([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_timezone' => $user->timezone,
            'target_timezone' => $targetTimezone,
            'availability' => $availability,
            'hourly_rate' => $user->hourly_rate,
            'preferred_currency' => $user->preferred_currency,
        ]);
    }

    /**
     * Get bulk availability for multiple users in a timezone.
     */
    public function getBulkAvailability(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'timezone' => 'required|string',
        ]);
        
        $targetTimezone = $request->timezone;
        $userIds = $request->user_ids;
        
        $users = User::whereIn('id', $userIds)
            ->with('availabilitySchedule')
            ->get()
            ->map(function ($user) use ($targetTimezone) {
                $availability = $user->availabilitySchedule
                    ->where('is_available', true)
                    ->map(function ($schedule) use ($targetTimezone) {
                        return $schedule->getAvailabilityInTimezone($targetTimezone);
                    })
                    ->filter()
                    ->values();
                
                return [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_timezone' => $user->timezone,
                    'availability' => $availability,
                    'hourly_rate' => $user->hourly_rate,
                    'preferred_currency' => $user->preferred_currency,
                ];
            });
        
        return response()->json([
            'target_timezone' => $targetTimezone,
            'users' => $users,
        ]);
    }

    /**
     * Copy availability from one day to another.
     */
    public function copyDay(Request $request)
    {
        $request->validate([
            'from_day' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'to_day' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
        ]);
        
        $user = auth()->user();
        $fromSchedule = $user->availabilitySchedule()->where('day_of_week', $request->from_day)->first();
        
        if (!$fromSchedule) {
            return back()->withErrors(['error' => 'Source day has no availability set.']);
        }
        
        $user->availabilitySchedule()->updateOrCreate(
            ['day_of_week' => $request->to_day],
            [
                'is_available' => $fromSchedule->is_available,
                'start_time' => $fromSchedule->start_time,
                'end_time' => $fromSchedule->end_time,
                'break_times' => $fromSchedule->break_times,
                'notes' => $fromSchedule->notes,
            ]
        );
        
        return back()->with('success', 'Availability copied successfully!');
    }

    /**
     * Create an availability template.
     */
    public function createTemplate(Request $request)
    {
        $request->validate([
            'template_name' => 'required|string|max:100',
            'apply_to_days' => 'required|array',
            'apply_to_days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'is_available' => 'boolean',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'break_times' => 'nullable|array',
            'notes' => 'nullable|string|max:500',
        ]);
        
        $user = auth()->user();
        
        foreach ($request->apply_to_days as $day) {
            $user->availabilitySchedule()->updateOrCreate(
                ['day_of_week' => $day],
                [
                    'is_available' => $request->boolean('is_available'),
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                    'break_times' => $request->break_times,
                    'notes' => $request->notes,
                ]
            );
        }
        
        return back()->with('success', 'Template applied successfully!');
    }

    /**
     * Search users by availability criteria.
     */
    public function searchByAvailability(Request $request)
    {
        $request->validate([
            'timezone' => 'required|string',
            'day' => 'nullable|string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
        ]);
        
        $targetTimezone = $request->timezone;
        $dayFilter = $request->day;
        $startTime = $request->start_time;
        $endTime = $request->end_time;
        
        // Build query to find available users
        $query = User::where('available_for_work', true)
            ->whereHas('availabilitySchedule', function ($q) use ($dayFilter, $startTime, $endTime) {
                $q->where('is_available', true);
                
                if ($dayFilter) {
                    $q->where('day_of_week', $dayFilter);
                }
                
                if ($startTime && $endTime) {
                    $q->where('start_time', '<=', $startTime)
                      ->where('end_time', '>=', $endTime);
                }
            })
            ->with(['availabilitySchedule' => function ($q) {
                $q->where('is_available', true);
            }, 'userType'])
            ->limit(50); // Limit results for performance
        
        $users = $query->get()->map(function ($user) use ($targetTimezone) {
            $availability = $user->availabilitySchedule
                ->map(function ($schedule) use ($targetTimezone) {
                    return $schedule->getAvailabilityInTimezone($targetTimezone);
                })
                ->filter()
                ->values();
            
            return [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_type' => $user->userType ? $user->userType->name : 'User',
                'user_timezone' => $user->timezone,
                'available_for_work' => $user->available_for_work,
                'hourly_rate' => $user->hourly_rate,
                'preferred_currency' => $user->preferred_currency,
                'availability' => $availability,
                'avatar' => $user->getProfilePictureUrl(),
                'rating' => $user->average_rating ?? 0,
                'last_active' => $user->last_activity_at ? $user->last_activity_at->diffForHumans() : 'Unknown',
            ];
        });
        
        return response()->json([
            'success' => true,
            'target_timezone' => $targetTimezone,
            'filters' => [
                'day' => $dayFilter,
                'start_time' => $startTime,
                'end_time' => $endTime,
            ],
            'users' => $users,
            'total' => $users->count(),
        ]);
    }
}
