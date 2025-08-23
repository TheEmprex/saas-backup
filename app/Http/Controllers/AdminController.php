<?php

namespace App\Http\Controllers;

use App\Models\KycVerification;
use App\Models\EarningsVerification;
use App\Models\User;
use App\Models\JobPost;
use App\Models\JobApplication;
use App\Models\Message;
use App\Models\UserSubscription;
use App\Models\SubscriptionPlan;
use App\Models\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isAdmin()) {
                abort(403, 'Unauthorized access');
            }
            return $next($request);
        });
    }

    /**
     * Admin dashboard
     */
    public function dashboard()
    {
        // User statistics
        $totalUsers = User::count();
        $newUsersToday = User::whereDate('created_at', today())->count();
        $newUsersThisWeek = User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)->count();
        $bannedUsers = User::where('is_banned', true)->count();
        $emailVerifiedUsers = User::whereNotNull('email_verified_at')->count();
        
        // Job statistics
        $totalJobs = JobPost::count();
        $activeJobs = JobPost::where('status', 'active')->count();
        $jobsToday = JobPost::whereDate('created_at', today())->count();
        $totalApplications = JobApplication::count();
        $applicationsToday = JobApplication::whereDate('created_at', today())->count();
        
        // Verification statistics
        $kycPendingCount = KycVerification::where('status', 'pending')->count();
        $earningsPendingCount = EarningsVerification::where('status', 'pending')->count();
        
        // Subscription statistics
        $activeSubscriptions = UserSubscription::where('expires_at', '>', now())
            ->orWhereNull('expires_at')
            ->count();
        
        // Message statistics
        $totalMessages = Message::count();
        $messagesToday = Message::whereDate('created_at', today())->count();
        
        // Recent users
        $recentUsers = User::with(['userType', 'kycVerification', 'earningsVerification'])
            ->latest()
            ->take(10)
            ->get();
        
        // Users requiring attention (banned, pending verification, etc.)
        $usersRequiringAttention = User::with(['userType', 'kycVerification', 'earningsVerification'])
            ->where(function($query) {
                $query->where('is_banned', true)
                    ->orWhereHas('kycVerification', function($q) {
                        $q->where('status', 'pending');
                    })
                    ->orWhereHas('earningsVerification', function($q) {
                        $q->where('status', 'pending');
                    })
                    ->orWhereNull('email_verified_at');
            })
            ->latest()
            ->take(10)
            ->get();
        
        $stats = [
            // User stats
            'total_users' => $totalUsers,
            'new_users_today' => $newUsersToday,
            'new_users_week' => $newUsersThisWeek,
            'new_users_month' => $newUsersThisMonth,
            'banned_users' => $bannedUsers,
            'email_verified_users' => $emailVerifiedUsers,
            'email_unverified_users' => $totalUsers - $emailVerifiedUsers,
            
            // Job stats
            'total_jobs' => $totalJobs,
            'active_jobs' => $activeJobs,
            'jobs_today' => $jobsToday,
            'total_applications' => $totalApplications,
            'applications_today' => $applicationsToday,
            
            // Verification stats
            'kyc_pending' => $kycPendingCount,
            'kyc_approved' => KycVerification::where('status', 'approved')->count(),
            'kyc_rejected' => KycVerification::where('status', 'rejected')->count(),
            'earnings_pending' => $earningsPendingCount,
            'earnings_approved' => EarningsVerification::where('status', 'approved')->count(),
            'earnings_rejected' => EarningsVerification::where('status', 'rejected')->count(),
            
            // Subscription stats
            'active_subscriptions' => $activeSubscriptions,
            
            // Message stats
            'total_messages' => $totalMessages,
            'messages_today' => $messagesToday,
        ];

        return view('admin.dashboard', compact('stats', 'recentUsers', 'usersRequiringAttention'));
    }

    /**
     * List all KYC verifications
     */
    public function kycVerifications(Request $request)
    {
        $query = KycVerification::with('user')->latest();
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $verifications = $query->paginate(20);
        
        return view('admin.kyc.index', compact('verifications'));
    }

    /**
     * Show specific KYC verification
     */
    public function showKycVerification(KycVerification $verification)
    {
        $verification->load('user');
        return view('admin.kyc.show', compact('verification'));
    }

    /**
     * Update KYC verification status
     */
    public function updateKycStatus(Request $request, KycVerification $verification)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'rejection_reason' => 'nullable|string|max:1000'
        ]);

        $verification->update([
            'status' => $request->status,
            'rejection_reason' => $request->status === 'rejected' ? $request->rejection_reason : null,
            'reviewed_at' => $request->status === 'approved' ? now() : null,
            'reviewed_by' => auth()->id(),
        ]);

        return redirect()->route('admin.kyc.show', $verification)
            ->with('success', 'KYC verification status updated successfully.');
    }

    /**
     * List all earnings verifications
     */
    public function earningsVerifications(Request $request)
    {
        $query = EarningsVerification::with('user')->latest();
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $verifications = $query->paginate(20);
        
        return view('admin.earnings.index', compact('verifications'));
    }

    /**
     * Show specific earnings verification
     */
    public function showEarningsVerification(EarningsVerification $verification)
    {
        $verification->load('user');
        return view('admin.earnings.show', compact('verification'));
    }

    /**
     * Update earnings verification status
     */
    public function updateEarningsStatus(Request $request, EarningsVerification $verification)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'rejection_reason' => 'nullable|string|max:1000'
        ]);

        // Only update specific fields, don't affect file paths
        $verification->status = $request->status;
        $verification->rejection_reason = $request->status === 'rejected' ? $request->rejection_reason : null;
        $verification->verified_at = $request->status === 'approved' ? now() : null;
        $verification->save();

        return redirect()->route('admin.earnings.show', $verification)
            ->with('success', 'Earnings verification status updated successfully.');
    }

    /**
     * Download verification files
     */
    public function downloadEarningsFile(EarningsVerification $verification, $type)
    {
        $filePath = match ($type) {
            'earnings_screenshot' => $verification->earnings_screenshot_path,
            'profile_screenshot' => $verification->profile_screenshot_path,
            default => null
        };

        if (!$filePath || !Storage::disk('private')->exists($filePath)) {
            abort(404, 'File not found');
        }

        return Storage::disk('private')->download($filePath);
    }

    /**
     * Preview verification files (serve as image)
     */
    public function previewEarningsFile(EarningsVerification $verification, $type)
    {
        $filePath = match ($type) {
            'earnings_screenshot' => $verification->earnings_screenshot_path,
            'profile_screenshot' => $verification->profile_screenshot_path,
            default => null
        };

        if (!$filePath || !Storage::disk('private')->exists($filePath)) {
            abort(404, 'File not found');
        }

        $file = Storage::disk('private')->get($filePath);
        $mimeType = Storage::disk('private')->mimeType($filePath);

        return response($file, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline',
        ]);
    }

    /**
     * List all users with filtering and search
     */
    public function users(Request $request)
    {
        $query = User::with(['userType', 'kycVerification', 'earningsVerification', 'subscriptions'])
            ->withCount(['jobPosts', 'jobApplications', 'sentMessages']);
        
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }
        
        // Filter by user type
        if ($request->filled('user_type')) {
            $query->whereHas('userType', function($q) use ($request) {
                $q->where('name', $request->user_type);
            });
        }
        
        // Filter by status
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'banned':
                    $query->where('is_banned', true);
                    break;
                case 'active':
                    $query->where('is_banned', false);
                    break;
                case 'email_verified':
                    $query->whereNotNull('email_verified_at');
                    break;
                case 'email_unverified':
                    $query->whereNull('email_verified_at');
                    break;
                case 'kyc_verified':
                    $query->whereHas('kycVerification', function($q) {
                        $q->where('status', 'approved');
                    });
                    break;
                case 'earnings_verified':
                    $query->whereHas('earningsVerification', function($q) {
                        $q->where('status', 'approved');
                    });
                    break;
            }
        }
        
        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortDir = $request->get('direction', 'desc');
        $query->orderBy($sortBy, $sortDir);
        
        $users = $query->paginate(20);
        
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show specific user details
     */
    public function showUser(User $user)
    {
        $user->load([
            'userType',
            'userProfile',
            'kycVerification',
            'earningsVerification',
            'subscriptions.subscriptionPlan',
            'jobPosts' => function($q) { $q->latest()->take(5); },
            'jobApplications' => function($q) { $q->with('jobPost')->latest()->take(5); },
            'sentMessages' => function($q) { $q->latest()->take(5); },
            'receivedMessages' => function($q) { $q->latest()->take(5); },
            'ratingsReceived',
            'ratingsGiven'
        ]);
        
        return view('admin.users.show', compact('user'));
    }

    /**
     * Ban a user
     */
    public function banUser(Request $request, User $user)
    {
        $request->validate([
            'reason' => 'required|string|max:1000'
        ]);
        
        $user->ban($request->reason);
        
        $message = 'User has been banned successfully.';
        if ($user->isAdmin()) {
            $message .= ' Note: This is an admin user - admin privileges may be maintained.';
        }
        
        return back()->with('success', $message);
    }

    /**
     * Unban a user
     */
    public function unbanUser(User $user)
    {
        $user->unban();
        
        return back()->with('success', 'User has been unbanned successfully.');
    }

    /**
     * Manually verify user email
     */
    public function verifyUserEmail(User $user)
    {
        $user->update([
            'email_verified_at' => now()
        ]);
        
        return back()->with('success', 'User email has been verified successfully.');
    }

    /**
     * Reset user email verification
     */
    public function unverifyUserEmail(User $user)
    {
        $user->update([
            'email_verified_at' => null
        ]);
        
        return back()->with('success', 'User email verification has been reset.');
    }

    /**
     * Delete user account
     */
    public function deleteUser(User $user)
    {
        // Soft delete or hard delete based on your preference
        $user->delete();
        
        $message = 'User has been deleted successfully.';
        if ($user->isAdmin()) {
            $message = 'Admin user has been deleted. Note: This may affect system administration.';
        }
        
        return redirect()->route('admin.users.index')
            ->with('success', $message);
    }

    /**
     * Impersonate a user (login as user)
     */
    public function impersonateUser(User $user)
    {
        // Store original admin user ID in session
        session(['impersonating_admin' => auth()->id()]);
        
        auth()->login($user);
        
        $message = 'You are now impersonating ' . $user->name;
        if ($user->isAdmin()) {
            $message .= ' Warning: You are impersonating another admin user.';
        }
        
        return redirect()->route('dashboard')
            ->with('success', $message);
    }

    /**
     * Stop impersonating and return to admin
     */
    public function stopImpersonating()
    {
        if (!session()->has('impersonating_admin')) {
            return redirect()->route('dashboard');
        }
        
        $adminId = session('impersonating_admin');
        session()->forget('impersonating_admin');
        
        $admin = User::find($adminId);
        if ($admin) {
            auth()->login($admin);
        }
        
        return redirect()->route('admin.dashboard')
            ->with('success', 'Stopped impersonating user.');
    }

    /**
     * List all jobs with filtering
     */
    public function jobs(Request $request)
    {
        $query = JobPost::with(['user', 'applications'])
            ->withCount('applications');
        
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQ) use ($search) {
                      $userQ->where('name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $jobs = $query->latest()->paginate(20);
        
        return view('admin.jobs.index', compact('jobs'));
    }

    /**
     * Show specific job
     */
    public function showJob(JobPost $job)
    {
        $job->load(['user', 'applications.user']);
        
        return view('admin.jobs.show', compact('job'));
    }

    /**
     * Delete a job
     */
    public function deleteJob(JobPost $job)
    {
        $job->delete();
        
        return redirect()->route('admin.jobs.index')
            ->with('success', 'Job has been deleted successfully.');
    }

    /**
     * List all messages with filtering
     */
    public function messages(Request $request)
    {
        $query = Message::with(['sender', 'recipient'])
            ->latest();
        
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('content', 'like', "%{$search}%")
                  ->orWhereHas('sender', function($userQ) use ($search) {
                      $userQ->where('name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('recipient', function($userQ) use ($search) {
                      $userQ->where('name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        $messages = $query->paginate(50);
        
        return view('admin.messages.index', compact('messages'));
    }

    /**
     * Delete a message
     */
    public function deleteMessage(Message $message)
    {
        $message->delete();
        
        return back()->with('success', 'Message has been deleted successfully.');
    }
    
    /**
     * Manage user subscription - show form
     */
    public function editUserSubscription(User $user)
    {
        $user->load(['subscriptions.subscriptionPlan', 'userType']);
        $subscriptionPlans = SubscriptionPlan::all();
        $userTypes = UserType::where('active', true)->get();
        
        return view('admin.users.edit-subscription', compact('user', 'subscriptionPlans', 'userTypes'));
    }
    
    /**
     * Update user subscription
     */
    public function updateUserSubscription(Request $request, User $user)
    {
        $request->validate([
            'subscription_plan_id' => 'nullable|exists:subscription_plans,id',
            'action' => 'required|in:add,remove,extend',
            'expires_at' => 'nullable|date|after:now',
            'duration_months' => 'nullable|integer|min:1|max:120'
        ]);
        
        DB::transaction(function () use ($request, $user) {
            // Remove existing active subscriptions if action is 'add' or 'remove'
            if (in_array($request->action, ['add', 'remove'])) {
                $user->subscriptions()
                    ->where('expires_at', '>', now())
                    ->orWhereNull('expires_at')
                    ->update(['expires_at' => now()]);
            }
            
            if ($request->action === 'add' && $request->subscription_plan_id) {
                $expiresAt = null;
                
                if ($request->duration_months) {
                    $expiresAt = now()->addMonths($request->duration_months);
                } elseif ($request->expires_at) {
                    $expiresAt = $request->expires_at;
                }
                
                UserSubscription::create([
                    'user_id' => $user->id,
                    'subscription_plan_id' => $request->subscription_plan_id,
                    'started_at' => now(),
                    'expires_at' => $expiresAt,
                ]);
                
            } elseif ($request->action === 'extend' && $request->subscription_plan_id) {
                $currentSubscription = $user->currentSubscription();
                
                if ($currentSubscription) {
                    $newExpirationDate = $currentSubscription->expires_at 
                        ? $currentSubscription->expires_at->addMonths($request->duration_months ?? 1)
                        : now()->addMonths($request->duration_months ?? 1);
                    
                    $currentSubscription->update([
                        'expires_at' => $newExpirationDate
                    ]);
                } else {
                    // Create new subscription if none exists
                    $expiresAt = $request->duration_months 
                        ? now()->addMonths($request->duration_months)
                        : ($request->expires_at ?? now()->addMonth());
                    
                    UserSubscription::create([
                        'user_id' => $user->id,
                        'subscription_plan_id' => $request->subscription_plan_id,
                        'started_at' => now(),
                        'expires_at' => $expiresAt,
                    ]);
                }
            }
        });
        
        $actionMessage = match($request->action) {
            'add' => 'Subscription added successfully.',
            'remove' => 'Subscription removed successfully.',
            'extend' => 'Subscription extended successfully.',
        };
        
        return redirect()->route('admin.users.show', $user)
            ->with('success', $actionMessage);
    }
    
    /**
     * Update user type
     */
    public function updateUserType(Request $request, User $user)
    {
        $request->validate([
            'user_type_id' => 'required|exists:user_types,id'
        ]);
        
        $oldUserType = $user->userType;
        $newUserType = UserType::find($request->user_type_id);
        
        // Update the user type
        $user->update([
            'user_type_id' => $request->user_type_id
        ]);
        
        $message = "User type changed from {$oldUserType?->display_name} to {$newUserType->display_name} successfully.";
        
        // Add warning for admin users
        if ($user->isAdmin()) {
            $message .= ' Note: This is an admin user - admin privileges are maintained regardless of user type.';
        }
        
        return back()->with('success', $message);
    }
    
    /**
     * Create/Update KYC verification directly for a user
     */
    public function createOrUpdateKycVerification(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'rejection_reason' => 'nullable|string|max:1000'
        ]);
        
        $kyc = $user->kycVerification;
        
        if ($kyc) {
            // Update existing KYC
            $kyc->update([
                'status' => $request->status,
                'rejection_reason' => $request->status === 'rejected' ? $request->rejection_reason : null,
                'reviewed_at' => $request->status === 'approved' ? now() : null,
                'reviewed_by' => auth()->id(),
            ]);
            $message = 'KYC verification status updated successfully.';
        } else {
            // Create new KYC record
            KycVerification::create([
                'user_id' => $user->id,
                'status' => $request->status,
                'rejection_reason' => $request->status === 'rejected' ? $request->rejection_reason : null,
                'reviewed_at' => $request->status === 'approved' ? now() : null,
                'reviewed_by' => auth()->id(),
                'submitted_at' => now(),
                // Default values for required fields
                'first_name' => $user->name ?? 'Admin Created',
                'last_name' => 'Admin',
                'date_of_birth' => '1990-01-01',
                'phone_number' => '+1-000-000-0000',
                'address' => 'Admin managed verification',
                'city' => 'Unknown',
                'state' => 'Unknown',
                'country' => 'Unknown',
                'postal_code' => '00000',
                'id_document_type' => 'admin_override',
                'id_document_number' => 'ADMIN-' . $user->id,
                'id_document_front_path' => null,
                'id_document_back_path' => null,
                'selfie_path' => null,
                'proof_of_address_path' => null,
            ]);
            $message = 'KYC verification created successfully.';
        }
        
        return back()->with('success', $message);
    }
    
    /**
     * Create/Update Earnings verification directly for a user
     */
    public function createOrUpdateEarningsVerification(Request $request, User $user)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'rejection_reason' => 'nullable|string|max:1000'
        ]);
        
        $earnings = $user->earningsVerification;
        
        if ($earnings) {
            // Update existing earnings verification
            $earnings->update([
                'status' => $request->status,
                'rejection_reason' => $request->status === 'rejected' ? $request->rejection_reason : null,
                'verified_at' => $request->status === 'approved' ? now() : null,
            ]);
            $message = 'Earnings verification status updated successfully.';
        } else {
            // Create new earnings verification record
            EarningsVerification::create([
                'user_id' => $user->id,
                'status' => $request->status,
                'rejection_reason' => $request->status === 'rejected' ? $request->rejection_reason : null,
                'verified_at' => $request->status === 'approved' ? now() : null,
                // Default values for required fields
                'platform_name' => 'Admin Override',
                'platform_username' => 'admin_' . $user->id,
                'monthly_earnings' => 0.00,
                'earnings_screenshot_path' => null,
                'profile_screenshot_path' => null,
                'additional_notes' => 'Admin managed verification',
            ]);
            $message = 'Earnings verification created successfully.';
        }
        
        return back()->with('success', $message);
    }
    
    /**
     * Remove KYC verification for a user
     */
    public function removeKycVerification(User $user)
    {
        $kyc = $user->kycVerification;
        
        if ($kyc) {
            $kyc->delete();
            return back()->with('success', 'KYC verification removed successfully.');
        }
        
        return back()->with('error', 'No KYC verification found for this user.');
    }
    
    /**
     * Remove Earnings verification for a user
     */
    public function removeEarningsVerification(User $user)
    {
        $earnings = $user->earningsVerification;
        
        if ($earnings) {
            $earnings->delete();
            return back()->with('success', 'Earnings verification removed successfully.');
        }
        
        return back()->with('error', 'No earnings verification found for this user.');
    }
    
    /**
     * Quick subscription actions (AJAX)
     */
    public function quickSubscriptionAction(Request $request, User $user)
    {
        $request->validate([
            'action' => 'required|in:remove_current,add_free,add_premium',
            'plan_id' => 'nullable|exists:subscription_plans,id'
        ]);
        
        DB::transaction(function () use ($request, $user) {
            switch ($request->action) {
                case 'remove_current':
                    $user->subscriptions()
                        ->where('expires_at', '>', now())
                        ->orWhereNull('expires_at')
                        ->update(['expires_at' => now()]);
                    break;
                    
                case 'add_free':
                    // Remove existing subscriptions
                    $user->subscriptions()
                        ->where('expires_at', '>', now())
                        ->orWhereNull('expires_at')
                        ->update(['expires_at' => now()]);
                        
                    // Add free plan (assuming plan ID 1 is free)
                    $freePlan = SubscriptionPlan::where('price', 0)->first();
                    if ($freePlan) {
                        UserSubscription::create([
                            'user_id' => $user->id,
                            'subscription_plan_id' => $freePlan->id,
                            'started_at' => now(),
                            'expires_at' => null, // Permanent free plan
                        ]);
                    }
                    break;
                    
                case 'add_premium':
                    if ($request->plan_id) {
                        // Remove existing subscriptions
                        $user->subscriptions()
                            ->where('expires_at', '>', now())
                            ->orWhereNull('expires_at')
                            ->update(['expires_at' => now()]);
                            
                        UserSubscription::create([
                            'user_id' => $user->id,
                            'subscription_plan_id' => $request->plan_id,
                            'started_at' => now(),
                            'expires_at' => now()->addMonth(), // 1 month default
                        ]);
                    }
                    break;
            }
        });
        
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Action completed successfully.']);
        }
        
        return back()->with('success', 'Subscription updated successfully.');
    }

    /**
     * Download KYC verification files
     */
    public function downloadKycFile(KycVerification $verification, $type)
    {
        $filePath = match ($type) {
            'id_document_front' => $verification->id_document_front_path,
            'id_document_back' => $verification->id_document_back_path,
            'selfie' => $verification->selfie_path,
            'proof_of_address' => $verification->proof_of_address_path,
            default => null
        };

        if (!$filePath || !Storage::disk('private')->exists($filePath)) {
            abort(404, 'File not found');
        }

        return Storage::disk('private')->download($filePath);
    }

    /**
     * Preview KYC verification files (serve as image)
     */
    public function previewKycFile(KycVerification $verification, $type)
    {
        $filePath = match ($type) {
            'id_document_front' => $verification->id_document_front_path,
            'id_document_back' => $verification->id_document_back_path,
            'selfie' => $verification->selfie_path,
            'proof_of_address' => $verification->proof_of_address_path,
            default => null
        };

        if (!$filePath || !Storage::disk('private')->exists($filePath)) {
            abort(404, 'File not found');
        }

        $file = Storage::disk('private')->get($filePath);
        $mimeType = Storage::disk('private')->mimeType($filePath);

        return response($file, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline',
        ]);
    }

    /**
     * Get system statistics for API/AJAX calls
     */
    public function getStats()
    {
        $stats = [
            'users_today' => User::whereDate('created_at', today())->count(),
            'users_online' => User::where('last_seen_at', '>=', now()->subMinutes(5))->count(),
            'jobs_today' => JobPost::whereDate('created_at', today())->count(),
            'applications_today' => JobApplication::whereDate('created_at', today())->count(),
            'messages_today' => Message::whereDate('created_at', today())->count(),
        ];
        
        return response()->json($stats);
    }
}
