<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserAvailability;

class UserAvailabilityController extends Controller
{
    /**
     * Display the user's availability schedule.
     */
    public function index()
    {
        $availability = Auth::user()->availability()
            ->orderByRaw("FIELD(day_of_week, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday')")
            ->get()
            ->groupBy('day_of_week');

        $daysOfWeek = UserAvailability::getDaysOfWeek();
        $timezones = UserAvailability::getCommonTimezones();

        return view('availability.index', compact('availability', 'daysOfWeek', 'timezones'));
    }

    /**
     * Show the form for editing availability.
     */
    public function edit()
    {
        $availability = Auth::user()->availability()
            ->orderByRaw("FIELD(day_of_week, 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday')")
            ->get()
            ->groupBy('day_of_week');

        $daysOfWeek = UserAvailability::getDaysOfWeek();
        $timezones = UserAvailability::getCommonTimezones();

        return view('availability.edit', compact('availability', 'daysOfWeek', 'timezones'));
    }

    /**
     * Update the user's availability.
     */
    public function update(Request $request)
    {
        $request->validate([
            'timezone' => 'required|string|max:50',
            'availability' => 'required|array',
            'availability.*.day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'availability.*.is_available' => 'boolean',
            'availability.*.start_time' => 'required_if:availability.*.is_available,true|date_format:H:i',
            'availability.*.end_time' => 'required_if:availability.*.is_available,true|date_format:H:i|after:availability.*.start_time'
        ]);

        // Delete existing availability
        Auth::user()->availability()->delete();

        // Create new availability records
        foreach ($request->availability as $availData) {
            if (isset($availData['is_available']) && $availData['is_available']) {
                UserAvailability::create([
                    'user_id' => Auth::id(),
                    'day_of_week' => $availData['day_of_week'],
                    'start_time' => $availData['start_time'],
                    'end_time' => $availData['end_time'],
                    'timezone' => $request->timezone,
                    'is_available' => true
                ]);
            } else {
                // Create unavailable record
                UserAvailability::create([
                    'user_id' => Auth::id(),
                    'day_of_week' => $availData['day_of_week'],
                    'start_time' => '00:00',
                    'end_time' => '00:00',
                    'timezone' => $request->timezone,
                    'is_available' => false
                ]);
            }
        }

        return redirect()->route('availability.index')
            ->with('success', 'Availability updated successfully!');
    }

    /**
     * Get availability for a specific user in a target timezone (API endpoint).
     */
    public function getAvailability(Request $request, $userId)
    {
        $targetTimezone = $request->input('timezone', 'UTC');
        
        try {
            $availability = UserAvailability::getAvailabilitySummary($userId, $targetTimezone);
            
            return response()->json([
                'success' => true,
                'availability' => $availability,
                'timezone' => $targetTimezone
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to get availability'
            ], 500);
        }
    }

    /**
     * Create quick availability template (e.g., 9-5 weekdays).
     */
    public function createTemplate(Request $request)
    {
        $request->validate([
            'template' => 'required|in:weekdays_9_5,weekdays_flexible,weekends,full_time',
            'timezone' => 'required|string|max:50'
        ]);

        // Delete existing availability
        Auth::user()->availability()->delete();

        $templates = [
            'weekdays_9_5' => [
                'days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
                'start_time' => '09:00',
                'end_time' => '17:00'
            ],
            'weekdays_flexible' => [
                'days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
                'start_time' => '08:00',
                'end_time' => '18:00'
            ],
            'weekends' => [
                'days' => ['saturday', 'sunday'],
                'start_time' => '10:00',
                'end_time' => '16:00'
            ],
            'full_time' => [
                'days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'],
                'start_time' => '08:00',
                'end_time' => '20:00'
            ]
        ];

        $template = $templates[$request->template];

        foreach ($template['days'] as $day) {
            UserAvailability::create([
                'user_id' => Auth::id(),
                'day_of_week' => $day,
                'start_time' => $template['start_time'],
                'end_time' => $template['end_time'],
                'timezone' => $request->timezone,
                'is_available' => true
            ]);
        }

        return redirect()->route('availability.index')
            ->with('success', 'Availability template applied successfully!');
    }

    /**
     * Copy availability from another day.
     */
    public function copyDay(Request $request)
    {
        $request->validate([
            'from_day' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'to_days' => 'required|array',
            'to_days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'
        ]);

        $sourceAvailability = Auth::user()->availability()
            ->where('day_of_week', $request->from_day)
            ->first();

        if (!$sourceAvailability) {
            return response()->json([
                'success' => false,
                'error' => 'Source day availability not found'
            ]);
        }

        foreach ($request->to_days as $toDay) {
            // Delete existing availability for target day
            Auth::user()->availability()
                ->where('day_of_week', $toDay)
                ->delete();

            // Create new availability based on source
            UserAvailability::create([
                'user_id' => Auth::id(),
                'day_of_week' => $toDay,
                'start_time' => $sourceAvailability->start_time,
                'end_time' => $sourceAvailability->end_time,
                'timezone' => $sourceAvailability->timezone,
                'is_available' => $sourceAvailability->is_available
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Availability copied successfully!'
        ]);
    }

    /**
     * Get availability for multiple users in a target timezone (API endpoint).
     */
    public function getBulkAvailability(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|string', // comma-separated user IDs
            'timezone' => 'nullable|string'
        ]);

        $userIds = explode(',', $request->input('user_ids'));
        $userIds = array_filter(array_map('trim', $userIds)); // Clean up
        $targetTimezone = $request->input('timezone', 'UTC');
        
        try {
            $availability = \App\Models\UserAvailability::getBulkAvailability($userIds, $targetTimezone);
            
            return response()->json([
                'success' => true,
                'availability' => $availability,
                'timezone' => $targetTimezone
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to get bulk availability: ' . $e->getMessage()
            ], 500);
        }
    }
}
