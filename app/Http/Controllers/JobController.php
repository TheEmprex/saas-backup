<?php

namespace App\Http\Controllers;

use App\Models\JobPost;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class JobController extends Controller
{
    public function show(int $id): View
    {
        $job = JobPost::with('user.userProfile')->findOrFail($id);
        
        return view('theme::marketplace.jobs.show', compact('job'));
    }

    public function create(): View
    {
        $user = Auth::user();
        
        // Check if user can post jobs (only agencies)
        if (!$user->isAgency()) {
            return view('theme::marketplace.jobs.access-denied');
        }
        
        // Get subscription info
        $subscription = $user->currentSubscription();
        $plan = $subscription ? $subscription->subscriptionPlan : null;
        
        // Get usage stats
        $remainingJobPosts = $user->getRemainingJobPosts();
        $usedJobPosts = $user->getJobPostsUsedThisMonth();
        $totalJobPosts = $plan ? $plan->job_post_limit : 0;
        
        return view('theme::marketplace.jobs.create', compact(
            'user', 'subscription', 'plan', 'remainingJobPosts', 'usedJobPosts', 'totalJobPosts'
        ));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Properly detect AJAX requests
        $isAjax = $request->ajax() || $request->wantsJson() || $request->has('ajax') || $request->header('X-Requested-With') === 'XMLHttpRequest';
        
        // Check if user is authorized to post jobs (only agencies)
        if (!$user->isAgency()) {
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'error' => 'Only agencies are authorized to post jobs.'
                ]);
            }
            
            return redirect()->back()
                ->with('error', 'Only agencies are authorized to post jobs.');
        }
        
        // Check if user can post jobs
        if (!$user->canPostJob()) {
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'error' => 'You have reached your job posting limit for this month. Please upgrade your subscription.'
                ]);
            }
            
            return redirect()->back()
                ->with('error', 'You have reached your job posting limit for this month. Please upgrade your subscription.');
        }

        try {
            $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'market' => 'required|in:management,chatting,content_creation,marketing,onlyfans,english,spanish,french,german',
            'experience_level' => 'required|in:beginner,intermediate,advanced',
            'contract_type' => 'required|in:full_time,part_time,contract',
            'rate_type' => 'required|in:hourly,fixed,commission',
            'hourly_rate' => 'nullable|numeric|min:0',
            'fixed_rate' => 'nullable|numeric|min:0',
            'commission_percentage' => 'nullable|numeric|min:0|max:100',
            'expected_hours_per_week' => 'required|integer|min:1|max:160',
            'duration_months' => 'required|integer|min:1|max:36',
            'min_typing_speed' => 'nullable|integer|min:20|max:150',
            'start_date' => 'nullable|date|after_or_equal:today',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'max_applications' => 'required|integer|min:1|max:200',
            'is_featured' => 'boolean',
            'is_urgent' => 'boolean',
            // Timezone and shift fields
            'required_timezone' => 'nullable|string|max:50',
            'shift_start_time' => 'nullable|date_format:H:i',
            'shift_end_time' => 'nullable|date_format:H:i',
            'required_days' => 'nullable|array',
            'required_days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'timezone_flexible' => 'boolean',
        ]);

        // Set default values
        $validated['status'] = 'active';
        $validated['current_applications'] = 0;
        $validated['expires_at'] = now()->addMonths(3);
        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_urgent'] = $request->has('is_urgent');
        $validated['user_id'] = Auth::id();

        // For now, skip payment logic to get basic functionality working
        // Create job directly
        $job = JobPost::create($validated);
        
        // Increment the permanent job post counter
        $stats = \App\Models\UserMonthlyStat::getOrCreateForMonth(Auth::id());
        $stats->incrementJobsPosted();

        // If AJAX request, return JSON
        if ($isAjax) {
            return response()->json([
                'success' => true,
                'message' => 'Job posted successfully!',
                'job' => [
                    'id' => $job->id,
                    'title' => $job->title,
                    'url' => route('marketplace.jobs.show', $job->id)
                ]
            ]);
        }

            // Regular form submission
            return redirect()->route('marketplace.jobs.show', $job->id)
                ->with('success', 'Job posted successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'error' => 'Please fix the form errors: ' . implode(' ', collect($e->errors())->flatten()->toArray())
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'error' => 'An unexpected error occurred: ' . $e->getMessage()
                ], 500);
            }
            throw $e;
        }
    }

    public function edit(int $id): View
    {
        $job = JobPost::findOrFail($id);
        
        // Check if user owns this job
        if ($job->user_id !== Auth::id()) {
            abort(403, 'You are not authorized to edit this job.');
        }
        
        return view('theme::marketplace.jobs.edit', compact('job'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $job = JobPost::findOrFail($id);
        
        // Check if user owns this job
        if ($job->user_id !== Auth::id()) {
            abort(403, 'You are not authorized to edit this job.');
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'market' => 'required|in:management,chatting,content_creation,marketing,onlyfans,english,spanish,french,german',
            'experience_level' => 'required|in:beginner,intermediate,advanced',
            'contract_type' => 'required|in:full_time,part_time,contract',
            'rate_type' => 'required|in:hourly,fixed,commission',
            'hourly_rate' => 'nullable|numeric|min:0',
            'fixed_rate' => 'nullable|numeric|min:0',
            'commission_percentage' => 'nullable|numeric|min:0|max:100',
            'expected_hours_per_week' => 'required|integer|min:1|max:160',
            'duration_months' => 'required|integer|min:1|max:36',
            'min_typing_speed' => 'nullable|integer|min:20|max:150',
            'start_date' => 'nullable|date|after_or_equal:today',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'max_applications' => 'required|integer|min:1|max:200',
            'is_featured' => 'boolean',
            'is_urgent' => 'boolean',
            // Timezone and shift fields
            'required_timezone' => 'nullable|string|max:50',
            'shift_start_time' => 'nullable|date_format:H:i',
            'shift_end_time' => 'nullable|date_format:H:i',
            'required_days' => 'nullable|array',
            'required_days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'timezone_flexible' => 'boolean',
        ]);

        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_urgent'] = $request->has('is_urgent');

        $job->update($validated);

        return redirect()->route('marketplace.my-jobs')
            ->with('success', 'Job updated successfully!');
    }

    public function destroy(int $id): RedirectResponse
    {
        $job = JobPost::findOrFail($id);
        
        // Check if user owns this job
        if ($job->user_id !== Auth::id()) {
            abort(403, 'You are not authorized to delete this job.');
        }
        
        $job->delete();

        return redirect()->route('marketplace.my-jobs')
            ->with('success', 'Job deleted successfully!');
    }

    public function promote(Request $request, int $id): RedirectResponse
    {
        $job = JobPost::findOrFail($id);
        
        // Check if user owns this job
        if ($job->user_id !== Auth::id()) {
            abort(403, 'You are not authorized to promote this job.');
        }
        
        $validated = $request->validate([
            'type' => 'required|in:featured,urgent',
        ]);
        
        $type = $validated['type'];
        $cost = $type === 'featured' ? 10.00 : 5.00;
        $fieldName = 'is_' . $type;
        
        // Check if job is already promoted with this type
        if ($job->$fieldName) {
            return redirect()->route('marketplace.my-jobs')
                ->with('error', 'Job is already ' . $type . '.');
        }
        
        // For now, skip payment processing and just update the job
        // In production, you would process payment here
        
        $job->update([$fieldName => true]);
        
        $message = 'Job has been made ' . $type . ' successfully! ';
        if ($type === 'featured') {
            $message .= 'Your job will now appear at the top of search results.';
        } else {
            $message .= 'Your job will now show an urgent indicator.';
        }
        
        return redirect()->route('marketplace.my-jobs')
            ->with('success', $message);
    }

    public function apply(Request $request, int $id)
    {
        try {
            $job = JobPost::findOrFail($id);
            $user = Auth::user()->load('userProfile', 'userType');
            
            // Check if user is authorized to apply to jobs (not agencies)
            if ($user->isAgency()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Agencies cannot apply to jobs. You can only post jobs.'
                    ], 400);
                }
                return redirect()->back()->with('error', 'Agencies cannot apply to jobs. You can only post jobs.');
            }
            
            // Check if user already applied (including withdrawn applications)
            $existingApplication = JobApplication::where('job_post_id', $id)
                ->where('user_id', Auth::id())
                ->first();

            if ($existingApplication) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'error' => 'You have already applied to this job.'
                    ], 400);
                }
                return redirect()->back()->with('error', 'You have already applied to this job.');
            }

            // Check if job is still accepting applications
            if ($job->current_applications >= $job->max_applications) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'error' => 'This job is no longer accepting applications.'
                    ], 400);
                }
                return redirect()->back()->with('error', 'This job is no longer accepting applications.');
            }

            // Check if user is the job poster
            if ($job->user_id === Auth::id()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'error' => 'You cannot apply to your own job.'
                    ], 400);
                }
                return redirect()->back()->with('error', 'You cannot apply to your own job.');
            }

            // Check if user can apply to jobs (subscription limits)
            if (!$user->canApplyToJob()) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'error' => 'You have reached your job application limit for this month. Please upgrade your subscription.'
                    ], 400);
                }
                return redirect()->back()
                    ->with('error', 'You have reached your job application limit for this month. Please upgrade your subscription.');
            }

            // Simplified validation rules - remove typing test requirement for now
            $validationRules = [
                'cover_letter' => 'required|string|max:2000',
                'proposed_rate' => 'required|numeric|min:0',
                'available_hours' => 'required|integer|min:1|max:160',
            ];
            
            // For now, don't require typing tests to simplify application process
            $requiresTypingTest = false;
            
            $validated = $request->validate($validationRules);

            DB::transaction(function() use ($validated, $job, $id, $requiresTypingTest) {
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

            // Handle AJAX requests vs regular form submissions
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Your application has been submitted successfully!'
                ]);
            }

            return redirect()->route('marketplace.jobs.show', $id)
                ->with('success', 'Your application has been submitted successfully!');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Validation failed: ' . collect($e->errors())->flatten()->implode(' ')
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Job application error: ' . $e->getMessage(), [
                'job_id' => $id,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'An error occurred while submitting your application. Please try again.'
                ], 500);
            }
            throw $e;
        }
    }
    
    private function requiresTypingTest(JobPost $job, $user): bool
    {
        // Check if job requires typing test for chatter positions only
        if ($job->min_typing_speed && $job->min_typing_speed > 0 && $user->userType && $user->userType->name === 'chatter') {
            return true;
        }

        return false;
    }

    public function applications(int $id): View
    {
        $job = JobPost::with('applications.user.userProfile')->findOrFail($id);
        
        return view('theme::marketplace.jobs.applications', compact('job'));
    }

    public function updateApplicationStatus(Request $request, int $jobId, int $applicationId): RedirectResponse
    {
        $job = JobPost::findOrFail($jobId);
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
        
        return view('theme::jobs.applications', compact('applications'));
    }

    public function index(): View
    {
        $jobs = JobPost::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('theme::jobs.index', compact('jobs'));
    }
    
    public function testAjax(Request $request)
    {
        // Simple test endpoint
        return response()->json([
            'success' => true,
            'message' => 'AJAX test working!',
            'job' => [
                'id' => 999,
                'title' => 'Test Job Title',
                'url' => '/marketplace/jobs/999'
            ]
        ]);
    }

    public function testJobPost(Request $request)
    {
        // Test job posting without any restrictions for popup testing
        
        $isAjax = $request->ajax() || $request->wantsJson() || $request->has('ajax');
        
        // Mock a successful job creation
        if ($isAjax) {
            return response()->json([
                'success' => true,
                'message' => 'Test job posted successfully!',
                'job' => [
                    'id' => 999,
                    'title' => $request->input('title', 'Test Job'),
                    'url' => '/marketplace/jobs/999'
                ]
            ]);
        }
        
        // Regular form submission fallback
        return redirect()->route('marketplace.jobs.index')
            ->with('success', 'Test job posted successfully!');
    }
}
