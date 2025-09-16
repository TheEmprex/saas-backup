<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserProfileController extends Controller
{
    /**
     * Display a listing of profiles with filtering.
     */
    public function index(Request $request)
    {
        $query = UserProfile::with(['user', 'user.userType', 'user.ratingsReceived'])
            ->where('is_active', true)
            ->where('is_verified', true);

        // Search filters
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search): void {
                $q->where('bio', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search): void {
                        $userQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('company_description', 'like', "%{$search}%");
            });
        }

        if ($request->has('user_type_id')) {
            $query->where('user_type_id', $request->user_type_id);
        }

        if ($request->has('min_english_proficiency')) {
            $query->where('english_proficiency_score', '>=', $request->min_english_proficiency);
        }

        if ($request->has('min_typing_speed')) {
            $query->where('typing_speed_wpm', '>=', $request->min_typing_speed);
        }

        if ($request->has('timezone')) {
            $query->where('availability_timezone', $request->timezone);
        }

        // Sort options
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $profiles = $query->paginate($request->get('per_page', 15));

        return response()->json($profiles);
    }

    /**
     * Store a newly created profile.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bio' => 'required|string|max:2000',
            'availability_timezone' => 'required|string',
            'availability_hours' => 'required|array',
            'portfolio_links' => 'nullable|array',
            'portfolio_links.*' => 'url',
            'typing_speed_wpm' => 'nullable|integer|min:0|max:200',
            'english_proficiency_score' => 'nullable|integer|min:0|max:100',
            'experience_agencies' => 'nullable|array',
            'traffic_sources' => 'nullable|array',
            'company_name' => 'nullable|string|max:255',
            'company_description' => 'nullable|string|max:1000',
            'hourly_rate' => 'nullable|numeric|min:0',
            'preferred_work_type' => 'nullable|in:remote,hybrid,onsite',
            'languages' => 'nullable|array',
            'certifications' => 'nullable|array',
            'social_media_links' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if user already has a profile
        $existingProfile = UserProfile::query->where('user_id', Auth::id())->first();

        if ($existingProfile) {
            return response()->json(['error' => 'Profile already exists'], 409);
        }

        $profileData = $validator->validated();
        $profileData['user_id'] = Auth::id();
        $profileData['user_type_id'] = Auth::user()->user_type_id;
        $profileData['is_active'] = true;
        $profileData['is_verified'] = false;
        $profileData['views'] = 0;

        // Convert arrays to JSON
        $arrayFields = ['availability_hours', 'portfolio_links', 'experience_agencies', 'traffic_sources', 'languages', 'certifications', 'social_media_links'];

        foreach ($arrayFields as $field) {
            if (isset($profileData[$field])) {
                $profileData[$field] = json_encode($profileData[$field]);
            }
        }

        $profile = UserProfile::create($profileData);

        return response()->json($profile->load(['user', 'user.userType']), 201);
    }

    /**
     * Display the specified profile.
     */
    public function show(UserProfile $userProfile)
    {
        // Increment view count (but not for own profile)
        if ($userProfile->user_id !== Auth::id()) {
            $userProfile->increment('views');
        }

        return response()->json($userProfile->load([
            'user',
            'user.userType',
            'user.ratingsReceived',
            'user.jobPosts' => function ($query): void {
                $query->where('status', 'active')
                    ->orderBy('created_at', 'desc')
                    ->limit(5);
            },
        ]));
    }

    /**
     * Update the specified profile.
     */
    public function update(Request $request, UserProfile $userProfile)
    {
        // Check if user owns this profile
        if ($userProfile->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'bio' => 'string|max:2000',
            'availability_timezone' => 'string',
            'availability_hours' => 'array',
            'portfolio_links' => 'nullable|array',
            'portfolio_links.*' => 'url',
            'typing_speed_wpm' => 'nullable|integer|min:0|max:200',
            'english_proficiency_score' => 'nullable|integer|min:0|max:100',
            'experience_agencies' => 'nullable|array',
            'traffic_sources' => 'nullable|array',
            'company_name' => 'nullable|string|max:255',
            'company_description' => 'nullable|string|max:1000',
            'hourly_rate' => 'nullable|numeric|min:0',
            'preferred_work_type' => 'nullable|in:remote,hybrid,onsite',
            'languages' => 'nullable|array',
            'certifications' => 'nullable|array',
            'social_media_links' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $profileData = $validator->validated();

        // Convert arrays to JSON
        $arrayFields = ['availability_hours', 'portfolio_links', 'experience_agencies', 'traffic_sources', 'languages', 'certifications', 'social_media_links'];

        foreach ($arrayFields as $field) {
            if (isset($profileData[$field])) {
                $profileData[$field] = json_encode($profileData[$field]);
            }
        }

        $userProfile->update($profileData);

        return response()->json($userProfile->load(['user', 'user.userType']));
    }

    /**
     * Remove the specified profile.
     */
    public function destroy(UserProfile $userProfile)
    {
        // Check if user owns this profile
        if ($userProfile->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $userProfile->delete();

        return response()->json(['message' => 'Profile deleted successfully']);
    }

    /**
     * Get current user's profile.
     */
    public function me()
    {
        $profile = UserProfile::with(['user', 'user.userType'])
            ->where('user_id', Auth::id())
            ->first();

        if (! $profile) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

        return response()->json($profile);
    }

    /**
     * Update current user's profile.
     */
    public function updateMe(Request $request)
    {
        $profile = UserProfile::query->where('user_id', Auth::id())->first();

        if (! $profile) {
            return response()->json(['error' => 'Profile not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'bio' => 'string|max:2000',
            'availability_timezone' => 'string',
            'availability_hours' => 'array',
            'portfolio_links' => 'nullable|array',
            'portfolio_links.*' => 'url',
            'typing_speed_wpm' => 'nullable|integer|min:0|max:200',
            'english_proficiency_score' => 'nullable|integer|min:0|max:100',
            'experience_agencies' => 'nullable|array',
            'traffic_sources' => 'nullable|array',
            'company_name' => 'nullable|string|max:255',
            'company_description' => 'nullable|string|max:1000',
            'hourly_rate' => 'nullable|numeric|min:0',
            'preferred_work_type' => 'nullable|in:remote,hybrid,onsite',
            'languages' => 'nullable|array',
            'certifications' => 'nullable|array',
            'social_media_links' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $profileData = $validator->validated();

        // Convert arrays to JSON
        $arrayFields = ['availability_hours', 'portfolio_links', 'experience_agencies', 'traffic_sources', 'languages', 'certifications', 'social_media_links'];

        foreach ($arrayFields as $field) {
            if (isset($profileData[$field])) {
                $profileData[$field] = json_encode($profileData[$field]);
            }
        }

        $profile->update($profileData);

        return response()->json($profile->load(['user', 'user.userType']));
    }
}
