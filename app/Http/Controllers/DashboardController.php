<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\EarningsVerification;
use App\Models\JobApplication;
use App\Models\JobPost;
use App\Models\KycVerification;
use App\Models\Message;
use App\Models\Rating;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get user's posted jobs
        $postedJobs = JobPost::where('user_id', $user->id)
            ->with('applications')
            ->latest()
            ->limit(5)
            ->get();

        // Get user's applications
        $myApplications = JobApplication::where('user_id', $user->id)
            ->with('jobPost.user')
            ->latest()
            ->limit(5)
            ->get();

        // Get pending applications for user's jobs
        $pendingApplications = JobApplication::whereHas('jobPost', function ($query) use ($user): void {
            $query->where('user_id', $user->id);
        })->where('status', 'pending')->count();

        // Get total applications received on user's jobs
        $totalApplicationsReceived = JobApplication::whereHas('jobPost', function ($query) use ($user): void {
            $query->where('user_id', $user->id);
        })->count();

        // Get verification status based on user type
        $verificationStatus = $this->getVerificationStatus($user);

        // Legacy KYC status for backward compatibility
        $kycVerification = $user->kycVerification;
        $kycStatus = [
            'submitted' => $user->hasKycSubmitted(),
            'verified' => $user->isKycVerified(),
            'status' => $kycVerification ? $kycVerification->status : 'not_submitted',
            'rejection_reason' => $kycVerification ? $kycVerification->rejection_reason : null,
        ];

        // Get user profile completion
        $profileCompletion = $this->calculateProfileCompletion($user);

        // Get comprehensive analytics
        $analytics = $this->getAnalytics($user);

        // Get recent activity
        $recentActivity = $this->getRecentActivity($user);

        // Get performance metrics
        $performanceMetrics = $this->getPerformanceMetrics($user);

        // Get admin stats if user is admin
        $adminStats = null;

        if ($user->isAdmin()) {
            $adminStats = $this->getAdminStats();
        }

        return view('theme::dashboard.index', ['postedJobs' => $postedJobs, 'myApplications' => $myApplications, 'pendingApplications' => $pendingApplications, 'totalApplicationsReceived' => $totalApplicationsReceived, 'kycStatus' => $kycStatus, 'verificationStatus' => $verificationStatus, 'profileCompletion' => $profileCompletion, 'analytics' => $analytics, 'recentActivity' => $recentActivity, 'performanceMetrics' => $performanceMetrics, 'adminStats' => $adminStats]);
    }

    private function getVerificationStatus($user): array
    {
        // Ensure user type is loaded
        if (! $user->userType) {
            $user->load('userType');
        }

        // Check if user type exists
        if (! $user->userType) {
            return [
                'type' => 'none',
                'required' => false,
                'submitted' => true,
                'verified' => true,
                'status' => 'verified',
                'rejection_reason' => null,
                'title' => 'No Verification Required',
                'description' => 'Your account type is not set',
                'url' => null,
                'button_text' => null,
            ];
        }

        try {
            if ($user->isChatter()) {
                // Chatters need KYC verification
                $kycVerification = $user->kycVerification;

                return [
                    'type' => 'kyc',
                    'required' => true,
                    'submitted' => $user->hasKycSubmitted(),
                    'verified' => $user->isKycVerified(),
                    'status' => $kycVerification ? $kycVerification->status : 'not_submitted',
                    'rejection_reason' => $kycVerification ? $kycVerification->rejection_reason : null,
                    'title' => 'KYC Verification Required',
                    'description' => 'You must complete KYC verification to post jobs and apply for positions.',
                    'url' => route('profile.kyc'),
                    'button_text' => 'Complete KYC',
                ];
            }

            if ($user->isAgency()) {
                // Agencies need earnings verification
                $earningsVerification = $user->earningsVerification;

                return [
                    'type' => 'earnings',
                    'required' => true,
                    'submitted' => (bool) $earningsVerification,
                    'verified' => $user->isEarningsVerified(),
                    'status' => $earningsVerification ? $earningsVerification->status : 'not_submitted',
                    'rejection_reason' => $earningsVerification ? $earningsVerification->rejection_reason : null,
                    'title' => 'Earnings Verification Required',
                    'description' => 'You must complete earnings verification to post jobs and apply for positions.',
                    'url' => route('profile.earnings-verification'),
                    'button_text' => 'Complete Earnings Verification',
                ];
            }
        } catch (Exception $exception) {
            Log::error('Error in getVerificationStatus: '.$exception->getMessage());
        }

        // Default case - no verification required
        return [
            'type' => 'none',
            'required' => false,
            'submitted' => true,
            'verified' => true,
            'status' => 'verified',
            'rejection_reason' => null,
            'title' => 'No Verification Required',
            'description' => 'Your account type does not require verification',
            'url' => null,
            'button_text' => null,
        ];
    }

    private function calculateProfileCompletion($user): array
    {
        // Base fields for all users
        $totalFields = 8;
        $completedFields = 0;

        // Check basic user fields
        if ($user->name) {
            $completedFields++;
        }

        if ($user->email) {
            $completedFields++;
        }

        if ($user->avatar) {
            $completedFields++;
        }

        if ($user->user_type_id) {
            $completedFields++;
        }

        // Check user profile fields
        if ($user->userProfile) {
            if ($user->userProfile->bio) {
                $completedFields++;
            }

            if ($user->userProfile->availability_timezone) {
                $completedFields++;
            }

            if ($user->userProfile->availability_hours) {
                $completedFields++;
            }

            if ($user->userProfile->english_proficiency_score) {
                $completedFields++;
            }
        }

        // Add typing test requirement only for chatters
        try {
            if ($user->userType && $user->isChatter()) {
                $totalFields++; // Add typing test to total fields

                if ($user->userProfile && $user->userProfile->typing_speed_wpm) {
                    $completedFields++;
                }
            }
        } catch (Exception) {
            // Handle any errors in type checking silently
        }

        // Check appropriate verification type
        try {
            if ($user->userType && $user->isChatter() && $user->isKycVerified()) {
                $completedFields++;
            } elseif ($user->userType && $user->isAgency() && $user->isEarningsVerified()) {
                $completedFields++;
            }
        } catch (Exception) {
            // Handle any errors in verification checking silently
        }

        return [
            'percentage' => round(($completedFields / $totalFields) * 100),
            'completed_fields' => $completedFields,
            'total_fields' => $totalFields,
        ];
    }

    private function getAnalytics($user)
    {
        $totalApplications = JobApplication::where('user_id', $user->id)->count();
        $acceptedApplications = JobApplication::where('user_id', $user->id)->where('status', 'accepted')->count();
        $unreadMessages = Message::where('recipient_id', $user->id)->where('is_read', false)->count();
        $averageRating = Rating::where('rated_id', $user->id)->avg('overall_rating') ?: 0;

        return [
            'total_applications' => $totalApplications,
            'accepted_applications' => $acceptedApplications,
            'success_rate' => $totalApplications > 0 ? round(($acceptedApplications / $totalApplications) * 100, 1) : 0,
            'unread_messages' => $unreadMessages,
            'average_rating' => round($averageRating, 1),
            'total_jobs_posted' => JobPost::where('user_id', $user->id)->count(),
            'active_jobs' => JobPost::where('user_id', $user->id)->where('status', 'active')->count(),
            'monthly_growth' => $this->calculateMonthlyGrowth($user),
            'profile_views' => $user->userProfile ? $user->userProfile->views : 0,
        ];
    }

    private function getRecentActivity($user)
    {
        $activities = collect();

        // Recent applications
        $recentApplications = JobApplication::where('user_id', $user->id)
            ->with(['jobPost', 'jobPost.user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentApplications as $application) {
            $activities->push([
                'type' => 'application',
                'title' => 'Applied to: '.$application->jobPost->title,
                'description' => 'Status: '.ucfirst($application->status),
                'created_at' => $application->created_at,
                'url' => route('jobs.show', $application->jobPost),
                'icon' => 'briefcase',
                'color' => $this->getStatusColor($application->status),
            ]);
        }

        // Recent messages
        $recentMessages = Message::where('recipient_id', $user->id)
            ->with(['sender'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        foreach ($recentMessages as $message) {
            $activities->push([
                'type' => 'message',
                'title' => 'Message from '.$message->sender->name,
                'description' => substr($message->message_content, 0, 50).'...',
                'created_at' => $message->created_at,
                'url' => route('messages.web.show', $message->sender),
                'icon' => 'message-circle',
                'color' => 'blue',
            ]);
        }

        // Recent job posts
        $recentJobs = JobPost::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        foreach ($recentJobs as $job) {
            $activities->push([
                'type' => 'job',
                'title' => 'Posted: '.$job->title,
                'description' => 'Applications: '.$job->applications()->count(),
                'created_at' => $job->created_at,
                'url' => route('jobs.show', $job),
                'icon' => 'plus-circle',
                'color' => 'green',
            ]);
        }

        return $activities->sortByDesc('created_at')->take(10);
    }

    private function getPerformanceMetrics($user)
    {
        $totalApplications = JobApplication::where('user_id', $user->id)->count();
        $acceptedApplications = JobApplication::where('user_id', $user->id)->where('status', 'accepted')->count();
        $completedJobs = JobPost::where('user_id', $user->id)->where('status', 'completed')->count();
        $totalJobs = JobPost::where('user_id', $user->id)->count();
        $averageRating = Rating::where('rated_id', $user->id)->avg('overall_rating') ?: 0;

        return [
            'application_success_rate' => $totalApplications > 0 ? round(($acceptedApplications / $totalApplications) * 100, 1) : 0,
            'job_completion_rate' => $totalJobs > 0 ? round(($completedJobs / $totalJobs) * 100, 1) : 0,
            'average_rating' => round($averageRating, 1),
            'total_ratings' => Rating::where('rated_id', $user->id)->count(),
            'response_time' => $this->calculateAverageResponseTime(),
            'earnings_trend' => $this->getEarningsTrend(),
            'rating_distribution' => $this->getRatingDistribution($user),
        ];
    }

    private function calculateMonthlyGrowth($user): float
    {
        $thisMonth = JobApplication::where('user_id', $user->id)
            ->where('created_at', '>=', Carbon::now()->startOfMonth())
            ->count();

        $lastMonth = JobApplication::where('user_id', $user->id)
            ->where('created_at', '>=', Carbon::now()->subMonth()->startOfMonth())
            ->where('created_at', '<', Carbon::now()->startOfMonth())
            ->count();

        $growth = $lastMonth > 0 ? (($thisMonth - $lastMonth) / $lastMonth) * 100 : 0;

        return round($growth, 1);
    }

    private function calculateAverageResponseTime(): string
    {
        // This would typically calculate based on message response times
        // For now, return a simulated value
        return random_int(1, 6).' hours';
    }

    /**
     * @return list<array{month: mixed, earnings: int}>
     */
    private function getEarningsTrend(): array
    {
        // Simulate earnings data for the last 6 months
        $earningsTrend = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $earningsTrend[] = [
                'month' => $month->format('M'),
                'earnings' => random_int(500, 3000), // Simulated earnings
            ];
        }

        return $earningsTrend;
    }

    /**
     * @return mixed[]
     */
    private function getRatingDistribution($user): array
    {
        $distribution = [];

        for ($i = 1; $i <= 5; $i++) {
            $count = Rating::where('rated_id', $user->id)->where('overall_rating', $i)->count();
            $distribution[$i] = $count;
        }

        return $distribution;
    }

    private function getStatusColor($status): string
    {
        return match ($status) {
            'accepted', 'hired' => 'green',
            'rejected' => 'red',
            'pending' => 'yellow',
            default => 'gray',
        };
    }

    private function getAdminStats()
    {
        return [
            'kyc_pending' => KycVerification::where('status', 'pending')->count(),
            'kyc_approved' => KycVerification::where('status', 'approved')->count(),
            'kyc_rejected' => KycVerification::where('status', 'rejected')->count(),
            'earnings_pending' => EarningsVerification::where('status', 'pending')->count(),
            'earnings_approved' => EarningsVerification::where('status', 'approved')->count(),
            'earnings_rejected' => EarningsVerification::where('status', 'rejected')->count(),
        ];
    }
}
