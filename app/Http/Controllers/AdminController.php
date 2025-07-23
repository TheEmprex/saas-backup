<?php

namespace App\Http\Controllers;

use App\Models\KycVerification;
use App\Models\EarningsVerification;
use App\Models\User;
use App\Models\JobPost;
use App\Models\JobApplication;
use App\Models\Message;
use App\Models\UserSubscription;
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
            'verified_at' => $request->status === 'approved' ? now() : null,
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
        
        if ($user->isAdmin()) {
            return back()->withErrors(['error' => 'Cannot ban admin users.']);
        }
        
        $user->ban($request->reason);
        
        return back()->with('success', 'User has been banned successfully.');
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
        if ($user->isAdmin()) {
            return back()->withErrors(['error' => 'Cannot delete admin users.']);
        }
        
        // Soft delete or hard delete based on your preference
        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User has been deleted successfully.');
    }

    /**
     * Impersonate a user (login as user)
     */
    public function impersonateUser(User $user)
    {
        if ($user->isAdmin()) {
            return back()->withErrors(['error' => 'Cannot impersonate admin users.']);
        }
        
        // Store original admin user ID in session
        session(['impersonating_admin' => auth()->id()]);
        
        auth()->login($user);
        
        return redirect()->route('dashboard')
            ->with('success', 'You are now impersonating ' . $user->name);
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
