<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\JobPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class JobApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $applications = JobApplication::with(['jobPost', 'jobPost.user', 'user', 'user.userType'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json($applications);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'job_post_id' => 'required|exists:job_posts,id',
            'cover_letter' => 'required|string|min:50',
            'proposed_rate' => 'nullable|numeric|min:0',
            'portfolio_links' => 'nullable|array',
            'portfolio_links.*' => 'url',
            'availability_start' => 'nullable|date|after:today',
            'additional_notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $jobPost = JobPost::findOrFail($request->job_post_id);

        // Check if user already applied
        $existingApplication = JobApplication::query->where('job_post_id', $jobPost->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingApplication) {
            return response()->json(['error' => 'You have already applied to this job'], 409);
        }

        // Check if job is still accepting applications
        if ($jobPost->status !== 'active') {
            return response()->json(['error' => 'This job is no longer accepting applications'], 400);
        }

        if ($jobPost->max_applications && $jobPost->current_applications >= $jobPost->max_applications) {
            return response()->json(['error' => 'Maximum applications reached for this job'], 400);
        }

        // Check if user is trying to apply to their own job
        if ($jobPost->user_id === Auth::id()) {
            return response()->json(['error' => 'You cannot apply to your own job'], 400);
        }

        $applicationData = $validator->validated();
        $applicationData['user_id'] = Auth::id();
        $applicationData['status'] = 'pending';

        // Convert arrays to JSON
        if (isset($applicationData['portfolio_links'])) {
            $applicationData['portfolio_links'] = json_encode($applicationData['portfolio_links']);
        }

        $application = JobApplication::create($applicationData);

        // Update job post application count
        $jobPost->increment('current_applications');

        return response()->json($application->load(['jobPost', 'user', 'user.userType']), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(JobApplication $jobApplication)
    {
        // Check if user owns this application or the job post
        if ($jobApplication->user_id !== Auth::id() && $jobApplication->jobPost->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($jobApplication->load([
            'jobPost',
            'jobPost.user',
            'user',
            'user.userType',
            'user.profile',
        ]));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JobApplication $jobApplication)
    {
        // Only the job poster can update application status
        if ($jobApplication->jobPost->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,shortlisted,interviewed,hired,rejected',
            'notes' => 'nullable|string',
            'interview_date' => 'nullable|date|after:now',
            'rejection_reason' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $applicationData = $validator->validated();
        $jobApplication->update($applicationData);

        return response()->json($jobApplication->load(['jobPost', 'user', 'user.userType']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobApplication $jobApplication)
    {
        // Only the applicant can withdraw their application
        if ($jobApplication->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Only allow withdrawal if status is pending
        if ($jobApplication->status !== 'pending') {
            return response()->json(['error' => 'Cannot withdraw application with current status'], 400);
        }

        $jobPost = $jobApplication->jobPost;
        $jobApplication->delete();

        // Update job post application count
        $jobPost->decrement('current_applications');

        return response()->json(['message' => 'Application withdrawn successfully']);
    }

    /**
     * Get applications for jobs posted by the authenticated user
     */
    public function received(Request $request)
    {
        $applications = JobApplication::with(['jobPost', 'user', 'user.userType', 'user.profile'])
            ->whereHas('jobPost', function ($query): void {
                $query->where('user_id', Auth::id());
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json($applications);
    }

    /**
     * Update application status in bulk
     */
    public function bulkUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:job_applications,id',
            'status' => 'required|in:pending,shortlisted,interviewed,hired,rejected',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $applications = JobApplication::query->whereIn('id', $request->application_ids)
            ->whereHas('jobPost', function ($query): void {
                $query->where('user_id', Auth::id());
            })
            ->get();

        if ($applications->count() !== count($request->application_ids)) {
            return response()->json(['error' => 'Some applications not found or unauthorized'], 400);
        }

        $updateData = [
            'status' => $request->status,
            'notes' => $request->notes,
        ];

        JobApplication::query->whereIn('id', $request->application_ids)
            ->whereHas('jobPost', function ($query): void {
                $query->where('user_id', Auth::id());
            })
            ->update($updateData);

        return response()->json(['message' => 'Applications updated successfully']);
    }
}
