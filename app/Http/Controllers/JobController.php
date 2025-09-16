<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\JobPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class JobController extends Controller
{
    public function index(): View
    {
        $jobs = JobPost::query->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('theme::jobs.index', ['jobs' => $jobs]);
    }

    public function create(): View
    {
        $user = Auth::user();

        // Get subscription info
        $subscription = $user->currentSubscription();
        $plan = $subscription ? $subscription->subscriptionPlan : null;

        // Get usage stats
        $remainingJobPosts = $user->getRemainingJobPosts();
        $usedJobPosts = $user->getJobPostsUsedThisMonth();
        $totalJobPosts = $plan ? $plan->job_post_limit : 0;

        return view('theme::marketplace.jobs.create', ['user' => $user, 'subscription' => $subscription, 'plan' => $plan, 'remainingJobPosts' => $remainingJobPosts, 'usedJobPosts' => $usedJobPosts, 'totalJobPosts' => $totalJobPosts]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Check if user can post jobs
        if (! $user->canPostJob()) {
            return redirect()->back()
                ->with('error', 'You have reached your job posting limit for this month. Please upgrade your subscription.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'market' => 'required|in:english,spanish,french,german',
            'experience_level' => 'required|in:beginner,intermediate,advanced',
            'contract_type' => 'required|in:full_time,part_time,contract',
            'rate_type' => 'required|in:hourly,fixed,commission',
            'hourly_rate' => 'nullable|numeric|min:0',
            'fixed_rate' => 'nullable|numeric|min:0',
            'commission_percentage' => 'nullable|numeric|min:0|max:100',
            'expected_hours_per_week' => 'required|integer|min:1|max:80',
            'duration_months' => 'required|integer|min:1|max:36',
            'min_typing_speed' => 'nullable|integer|min:20|max:150',
            'start_date' => 'nullable|date|after_or_equal:today',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'max_applications' => 'required|integer|min:1|max:200',
            'is_featured' => 'boolean',
            'is_urgent' => 'boolean',
        ]);

        // Set default values
        $validated['status'] = 'active';
        $validated['current_applications'] = 0;
        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_urgent'] = $request->has('is_urgent');

        // Check if payment is required for featured/urgent features
        $isFeatured = $validated['is_featured'];
        $isUrgent = $validated['is_urgent'];
        $featuredCost = $isFeatured && ! $user->canUseFeaturedForFree() ? 10 : 0;
        $urgentCost = $isUrgent ? 5 : 0;
        $totalCost = $featuredCost + $urgentCost;

        // If payment is required, store job data in session and redirect to payment
        if ($totalCost > 0) {
            session(['job_data' => $validated]);

            return redirect()->route('job.payment')
                ->with('info', 'Payment required for selected features. Total cost: $'.$totalCost);
        }

        // No payment required, create job directly
        $validated['user_id'] = Auth::id();
        $job = JobPost::create($validated);

        return redirect()->route('jobs.show', $job->id)
            ->with('success', 'Job posted successfully!');
    }

    public function show(int $id): View
    {
        $job = JobPost::with('user.userProfile')->findOrFail($id);

        return view('theme::marketplace.jobs.show', ['job' => $job]);
    }

    public function edit(int $id): View
    {
        $job = JobPost::findOrFail($id);

        return view('theme::marketplace.jobs.edit', ['job' => $job]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $job = JobPost::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'market' => 'required|in:english,spanish,french,german',
            'experience_level' => 'required|in:beginner,intermediate,advanced',
            'contract_type' => 'required|in:full_time,part_time,contract',
            'rate_type' => 'required|in:hourly,fixed,commission',
            'hourly_rate' => 'nullable|numeric|min:0',
            'fixed_rate' => 'nullable|numeric|min:0',
            'commission_percentage' => 'nullable|numeric|min:0|max:100',
            'expected_hours_per_week' => 'required|integer|min:1|max:80',
            'duration_months' => 'required|integer|min:1|max:36',
            'min_typing_speed' => 'nullable|integer|min:20|max:150',
            'start_date' => 'nullable|date|after_or_equal:today',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'max_applications' => 'required|integer|min:1|max:200',
            'is_featured' => 'boolean',
            'is_urgent' => 'boolean',
        ]);

        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_urgent'] = $request->has('is_urgent');

        $job->update($validated);

        return redirect()->route('jobs.show', $job->id)
            ->with('success', 'Job updated successfully!');
    }

    public function destroy(int $id): RedirectResponse
    {
        $job = JobPost::findOrFail($id);

        $job->delete();

        return redirect()->route('marketplace.index')
            ->with('success', 'Job deleted successfully!');
    }

    public function apply(Request $request, int $id): RedirectResponse
    {
        $job = JobPost::findOrFail($id);
        $user = Auth::user()->load('userProfile', 'userType');

        // Check if user already applied
        $existingApplication = JobApplication::query->where('job_post_id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingApplication) {
            return redirect()->back()->with('error', 'You have already applied to this job.');
        }

        // Check if job is still accepting applications
        if ($job->current_applications >= $job->max_applications) {
            return redirect()->back()->with('error', 'This job is no longer accepting applications.');
        }

        // Check if user is the job poster
        if ($job->user_id === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot apply to your own job.');
        }

        // Check if typing test is required for chatter positions
        $requiresTypingTest = $this->requiresTypingTest($job, $user);

        $validationRules = [
            'cover_letter' => 'required|string|max:2000',
            'proposed_rate' => 'required|numeric|min:0',
            'available_hours' => 'required|integer|min:1|max:80',
        ];

        // Add typing test validation if required
        if ($requiresTypingTest) {
            $validationRules['typing_test_wpm'] = 'required|integer|min:'.($job->min_typing_speed ?? 30);
            $validationRules['typing_test_accuracy'] = 'required|integer|min:85|max:100';
            $validationRules['typing_test_results'] = 'required|json';
        }

        $validated = $request->validate($validationRules);

        DB::transaction(function () use ($validated, $job, $id, $requiresTypingTest): void {
            $applicationData = [
                'job_post_id' => $id,
                'user_id' => Auth::id(),
                'cover_letter' => $validated['cover_letter'],
                'proposed_rate' => $validated['proposed_rate'],
                'available_hours' => $validated['available_hours'],
                'status' => 'pending',
            ];

            // Add typing test data if provided
            if ($requiresTypingTest) {
                $applicationData['typing_test_wpm'] = $validated['typing_test_wpm'];
                $applicationData['typing_test_accuracy'] = $validated['typing_test_accuracy'];
                $applicationData['typing_test_results'] = $validated['typing_test_results'];
                $applicationData['typing_test_taken_at'] = now();
                $applicationData['typing_test_passed'] = $validated['typing_test_wpm'] >= ($job->min_typing_speed ?? 30) && $validated['typing_test_accuracy'] >= 85;
            }

            // Create application
            JobApplication::create($applicationData);

            // Increment application count
            $job->increment('current_applications');
        });

        return redirect()->route('jobs.show', $id)
            ->with('success', 'Your application has been submitted successfully!');
    }

    public function applications(int $id): View
    {
        $job = JobPost::with('applications.user.userProfile')->findOrFail($id);

        return view('theme::marketplace.jobs.applications', ['job' => $job]);
    }

    public function updateApplicationStatus(Request $request, int $jobId, int $applicationId): RedirectResponse
    {
        JobPost::findOrFail($jobId);
        $application = JobApplication::findOrFail($applicationId);

        $validated = $request->validate([
            'status' => 'required|in:pending,accepted,rejected',
        ]);

        $application->update($validated);

        return redirect()->back()->with('success', 'Application status updated successfully!');
    }

    public function userApplications(): View
    {
        $applications = JobApplication::with('jobPost.user.userProfile')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('theme::jobs.applications', ['applications' => $applications]);
    }

    private function requiresTypingTest(JobPost $job, $user): bool
    {
        // Check if job requires typing test (for chatter positions)
        if ($job->min_typing_speed && $job->min_typing_speed > 0) {
            return true;
        }

        // Check if user type is chatter
        return $user->userType && in_array($user->userType->name, ['chatter', 'chatting_agency']);
    }
}
