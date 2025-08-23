<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class JobPostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = JobPost::with(['user', 'user.userType', 'applications'])
            ->where('status', 'active')
            ->where('expires_at', '>', now());

        // Search filters
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('requirements', 'like', "%{$search}%");
            });
        }

        if ($request->has('market')) {
            $query->where('market', $request->market);
        }

        if ($request->has('rate_type')) {
            $query->where('rate_type', $request->rate_type);
        }

        if ($request->has('experience_level')) {
            $query->where('experience_level', $request->experience_level);
        }

        if ($request->has('contract_type')) {
            $query->where('contract_type', $request->contract_type);
        }

        // Timezone filtering - find jobs with compatible working hours
        if ($request->has('timezone')) {
            $userTimezone = $request->timezone;
            $query->where(function ($q) use ($userTimezone) {
                $q->where('timezone_preference', $userTimezone)
                  ->orWhereNull('timezone_preference')
                  ->orWhere('timezone_preference', '');
            });
        }

        // Working hours filtering - find jobs that match user's availability
        if ($request->has('available_hours')) {
            $availableHours = $request->available_hours; // Expected format: [{'day': 'monday', 'start': '09:00', 'end': '17:00'}]
            if (is_array($availableHours) && !empty($availableHours)) {
                $query->where(function ($q) use ($availableHours) {
                    foreach ($availableHours as $hours) {
                        if (isset($hours['day'], $hours['start'], $hours['end'])) {
                            // This would need more complex logic to match JSON working_hours field
                            // For now, we'll include jobs that don't specify working hours
                            $q->orWhereNull('working_hours')
                              ->orWhere('working_hours', '')
                              ->orWhere('working_hours', 'LIKE', '%' . $hours['day'] . '%');
                        }
                    }
                });
            }
        }

        // Salary range filters
        if ($request->has('min_rate') && $request->rate_type === 'hourly') {
            $query->where('hourly_rate', '>=', $request->min_rate);
        }

        if ($request->has('max_rate') && $request->rate_type === 'hourly') {
            $query->where('hourly_rate', '<=', $request->max_rate);
        }

        // Sort options
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $jobs = $query->paginate($request->get('per_page', 15));

        return response()->json($jobs);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'market' => 'required|in:management,chatting,content_creation,marketing,design,other',
            'rate_type' => 'required|in:hourly,fixed,commission',
            'hourly_rate' => 'required_if:rate_type,hourly|numeric|min:0',
            'fixed_rate' => 'required_if:rate_type,fixed|numeric|min:0',
            'commission_percentage' => 'required_if:rate_type,commission|numeric|min:0|max:100',
            'contract_type' => 'required|in:full_time,part_time,contract,freelance',
            'experience_level' => 'required|in:beginner,intermediate,advanced',
            'requirements' => 'nullable|string',
            'hours_per_week' => 'nullable|integer|min:1|max:168',
            'max_applications' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date|after_or_equal:today',
            'expires_at' => 'nullable|date|after:start_date',
            'timezone_preference' => 'nullable|string',
            'expected_response_time' => 'nullable|string',
            'min_typing_speed' => 'nullable|integer|min:0',
            'min_english_proficiency' => 'nullable|integer|min:0|max:100',
            'tags' => 'nullable|array',
            'working_hours' => 'nullable|array',
            'required_traffic_sources' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $jobData = $validator->validated();
        $jobData['user_id'] = Auth::id();
        $jobData['status'] = 'active';
        $jobData['current_applications'] = 0;
        $jobData['views'] = 0;
        
        // Convert arrays to JSON
        if (isset($jobData['tags'])) {
            $jobData['tags'] = json_encode($jobData['tags']);
        }
        if (isset($jobData['working_hours'])) {
            $jobData['working_hours'] = json_encode($jobData['working_hours']);
        }
        if (isset($jobData['required_traffic_sources'])) {
            $jobData['required_traffic_sources'] = json_encode($jobData['required_traffic_sources']);
        }

        $jobPost = JobPost::create($jobData);

        return response()->json($jobPost->load(['user', 'user.userType']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(JobPost $jobPost)
    {
        // Increment view count
        $jobPost->increment('views');

        return response()->json($jobPost->load([
            'user',
            'user.userType',
            'user.profile',
            'applications.user',
            'applications.user.userType'
        ]));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JobPost $jobPost)
    {
        // Check if user owns this job post
        if ($jobPost->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'description' => 'string',
            'market' => 'in:management,chatting,content_creation,marketing,design,other',
            'rate_type' => 'in:hourly,fixed,commission',
            'hourly_rate' => 'required_if:rate_type,hourly|numeric|min:0',
            'fixed_rate' => 'required_if:rate_type,fixed|numeric|min:0',
            'commission_percentage' => 'required_if:rate_type,commission|numeric|min:0|max:100',
            'contract_type' => 'in:full_time,part_time,contract,freelance',
            'experience_level' => 'in:beginner,intermediate,advanced',
            'requirements' => 'nullable|string',
            'hours_per_week' => 'nullable|integer|min:1|max:168',
            'max_applications' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'expires_at' => 'nullable|date|after:start_date',
            'timezone_preference' => 'nullable|string',
            'expected_response_time' => 'nullable|string',
            'min_typing_speed' => 'nullable|integer|min:0',
            'min_english_proficiency' => 'nullable|integer|min:0|max:100',
            'tags' => 'nullable|array',
            'working_hours' => 'nullable|array',
            'required_traffic_sources' => 'nullable|array',
            'status' => 'in:active,paused,closed,expired',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $jobData = $validator->validated();
        
        // Convert arrays to JSON
        if (isset($jobData['tags'])) {
            $jobData['tags'] = json_encode($jobData['tags']);
        }
        if (isset($jobData['working_hours'])) {
            $jobData['working_hours'] = json_encode($jobData['working_hours']);
        }
        if (isset($jobData['required_traffic_sources'])) {
            $jobData['required_traffic_sources'] = json_encode($jobData['required_traffic_sources']);
        }

        $jobPost->update($jobData);

        return response()->json($jobPost->load(['user', 'user.userType']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobPost $jobPost)
    {
        // Check if user owns this job post
        if ($jobPost->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $jobPost->delete();

        return response()->json(['message' => 'Job post deleted successfully']);
    }

    /**
     * Get jobs posted by the authenticated user
     */
    public function myJobs(Request $request)
    {
        $jobs = JobPost::with(['applications.user', 'applications.user.userType'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 10));

        return response()->json($jobs);
    }

    /**
     * Get job applications for a specific job
     */
    public function applications(JobPost $jobPost)
    {
        // Check if user owns this job post
        if ($jobPost->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $applications = $jobPost->applications()
            ->with(['user', 'user.userType', 'user.profile'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($applications);
    }
}
