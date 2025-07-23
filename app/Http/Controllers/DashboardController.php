<?php

namespace App\Http\Controllers;

use App\Models\JobPost;
use App\Models\JobApplication;
use App\Models\Message;
use App\Models\Rating;
use App\Models\UserProfile;
use App\Models\KycVerification;
use App\Models\EarningsVerification;
use App\Models\Contract;
use App\Models\Earning;
use App\Models\Analytics;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get OnlyFans marketplace specific data
        $dashboardData = $this->getOnlyFansMarketplaceData($user);
        
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
        $pendingApplications = JobApplication::whereHas('jobPost', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('status', 'pending')->count();
        
        // Get total applications received on user's jobs
        $totalApplicationsReceived = JobApplication::whereHas('jobPost', function($query) use ($user) {
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

        // Get recent jobs
        $recentJobs = JobPost::where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();
        
        // Get performance metrics
        $performanceMetrics = $this->getPerformanceMetrics($user);
        
        // Get contracts data
        $contractsData = $this->getContractsData($user);
        
        // Get earnings data
        $earningsData = $this->getEarningsData($user);
        
        // Get admin stats if user is admin
        $adminStats = null;
        if ($user->isAdmin()) {
            $adminStats = $this->getAdminStats();
        }
        
        return view('marketplace.dashboard', compact(
            'user',
            'dashboardData',
            'postedJobs',
            'myApplications',
            'pendingApplications',
            'totalApplicationsReceived',
            'kycStatus',
            'verificationStatus',
            'profileCompletion',
            'analytics',
            'recentActivity',
            'recentJobs',
            'performanceMetrics',
            'contractsData',
            'earningsData',
            'adminStats'
        ))->with([
            'stats' => $this->getStatsForView($user, $dashboardData),
            'featuredJobs' => $dashboardData['featured_jobs'] ?? []
        ]);
    }
    
    private function getVerificationStatus($user)
    {
        // Ensure user type is loaded
        if (!$user->userType) {
            $user->load('userType');
        }
        
        // Check if user type exists
        if (!$user->userType) {
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
                    'submitted' => $earningsVerification ? true : false,
                    'verified' => $user->isEarningsVerified(),
                    'status' => $earningsVerification ? $earningsVerification->status : 'not_submitted',
                    'rejection_reason' => $earningsVerification ? $earningsVerification->rejection_reason : null,
                    'title' => 'Earnings Verification Required',
                    'description' => 'You must complete earnings verification to post jobs and apply for positions.',
                    'url' => route('profile.earnings-verification'),
                    'button_text' => 'Complete Earnings Verification',
                ];
            }
        } catch (\Exception $e) {
            \Log::error('Error in getVerificationStatus: ' . $e->getMessage());
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
    
    private function calculateProfileCompletion($user)
    {
        // Base fields for all users
        $totalFields = 8;
        $completedFields = 0;
        
        // Check basic user fields
        if ($user->name) $completedFields++;
        if ($user->email) $completedFields++;
        if ($user->avatar) $completedFields++;
        if ($user->user_type_id) $completedFields++;
        
        // Check user profile fields
        if ($user->userProfile) {
            if ($user->userProfile->bio) $completedFields++;
            if ($user->userProfile->availability_timezone) $completedFields++;
            if ($user->userProfile->availability_hours) $completedFields++;
            if ($user->userProfile->english_proficiency_score) $completedFields++;
        }
        
        // Add typing test requirement only for chatters
        try {
            if ($user->userType && $user->isChatter()) {
                $totalFields++; // Add typing test to total fields
                if ($user->userProfile && $user->userProfile->typing_speed_wpm) {
                    $completedFields++;
                }
            }
        } catch (\Exception $e) {
            // Handle any errors in type checking silently
        }
        
        // Check appropriate verification type
        try {
            if ($user->userType && $user->isChatter() && $user->isKycVerified()) {
                $completedFields++;
            } elseif ($user->userType && $user->isAgency() && $user->isEarningsVerified()) {
                $completedFields++;
            }
        } catch (\Exception $e) {
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
                'title' => 'Applied to: ' . $application->jobPost->title,
                'description' => 'Status: ' . ucfirst($application->status),
                'created_at' => $application->created_at,
                'url' => route('jobs.show', $application->jobPost),
                'icon' => 'briefcase',
                'color' => $this->getStatusColor($application->status)
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
                'title' => 'Message from ' . $message->sender->name,
                'description' => substr($message->message_content, 0, 50) . '...',
                'created_at' => $message->created_at,
                'url' => route('messages.web.show', $message->sender),
                'icon' => 'message-circle',
                'color' => 'blue'
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
                'title' => 'Posted: ' . $job->title,
                'description' => 'Applications: ' . $job->applications()->count(),
                'created_at' => $job->created_at,
                'url' => route('jobs.show', $job),
                'icon' => 'plus-circle',
                'color' => 'green'
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
            'response_time' => $this->calculateAverageResponseTime($user),
            'earnings_trend' => $this->getEarningsTrend($user),
            'rating_distribution' => $this->getRatingDistribution($user),
        ];
    }
    
    private function calculateMonthlyGrowth($user)
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
    
    private function calculateAverageResponseTime($user)
    {
        // This would typically calculate based on message response times
        // For now, return a simulated value
        return rand(1, 6) . ' hours';
    }
    
    private function getEarningsTrend($user)
    {
        // Simulate earnings data for the last 6 months
        $earningsTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $earningsTrend[] = [
                'month' => $month->format('M'),
                'earnings' => rand(500, 3000) // Simulated earnings
            ];
        }
        
        return $earningsTrend;
    }
    
    private function getRatingDistribution($user)
    {
        $distribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $count = Rating::where('rated_id', $user->id)->where('overall_rating', $i)->count();
            $distribution[$i] = $count;
        }
        
        return $distribution;
    }
    
    private function getStatusColor($status)
    {
        switch ($status) {
            case 'accepted':
            case 'hired':
                return 'green';
            case 'rejected':
                return 'red';
            case 'pending':
                return 'yellow';
            default:
                return 'gray';
        }
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
    
    /**
     * Get OnlyFans marketplace specific dashboard data
     */
    private function getOnlyFansMarketplaceData($user)
    {
        $stats = [
            'total_earnings' => $this->getTotalEarnings($user),
            'monthly_earnings' => $this->getMonthlyEarnings($user),
            'weekly_earnings' => $this->getWeeklyEarnings($user),
            'active_contracts' => $this->getActiveContracts($user),
            'completed_contracts' => $this->getCompletedContracts($user),
            'profile_views' => $this->getProfileViews($user),
            'response_rate' => $this->getResponseRate($user),
            'success_rate' => $this->getSuccessRate($user),
            'subscription_status' => $this->getSubscriptionStatus($user),
            'featured_jobs' => $this->getFeaturedJobs(),
            'trending_skills' => $this->getTrendingSkills(),
            'marketplace_stats' => $this->getMarketplaceStats(),
        ];
        
        return $stats;
    }
    
    /**
     * Get total earnings for user
     */
    private function getTotalEarnings($user)
    {
        return Earning::where('user_id', $user->id)
            ->where('status', 'paid')
            ->sum('amount');
    }
    
    /**
     * Get monthly earnings for user
     */
    private function getMonthlyEarnings($user)
    {
        return Earning::where('user_id', $user->id)
            ->where('status', 'paid')
            ->thisMonth()
            ->sum('amount');
    }
    
    /**
     * Get weekly earnings for user
     */
    private function getWeeklyEarnings($user)
    {
        return Earning::where('user_id', $user->id)
            ->where('status', 'paid')
            ->thisWeek()
            ->sum('amount');
    }
    
    /**
     * Get active contracts count
     */
    private function getActiveContracts($user)
    {
        return Contract::where(function($query) use ($user) {
            $query->where('employer_id', $user->id)
                  ->orWhere('contractor_id', $user->id);
        })
        ->where('status', 'active')
        ->count();
    }
    
    /**
     * Get completed contracts count
     */
    private function getCompletedContracts($user)
    {
        return Contract::where(function($query) use ($user) {
            $query->where('employer_id', $user->id)
                  ->orWhere('contractor_id', $user->id);
        })
        ->where('status', 'completed')
        ->count();
    }
    
    /**
     * Get profile views from analytics
     */
    private function getProfileViews($user)
    {
        return Analytics::where('user_id', $user->id)
            ->where('metric_type', 'profile_views')
            ->thisMonth()
            ->sum('metric_value');
    }
    
    /**
     * Get response rate percentage
     */
    private function getResponseRate($user)
    {
        // Calculate based on message responses
        $totalMessages = Message::where('recipient_id', $user->id)->count();
        $respondedMessages = Message::where('sender_id', $user->id)->count();
        
        return $totalMessages > 0 ? round(($respondedMessages / $totalMessages) * 100, 1) : 0;
    }
    
    /**
     * Get success rate for applications/jobs
     */
    private function getSuccessRate($user)
    {
        if ($user->isChatter()) {
            $totalApplications = JobApplication::where('user_id', $user->id)->count();
            $acceptedApplications = JobApplication::where('user_id', $user->id)
                ->where('status', 'accepted')
                ->count();
            
            return $totalApplications > 0 ? round(($acceptedApplications / $totalApplications) * 100, 1) : 0;
        }
        
        if ($user->isAgency()) {
            $totalJobs = JobPost::where('user_id', $user->id)->count();
            $filledJobs = JobPost::where('user_id', $user->id)
                ->where('status', 'filled')
                ->count();
            
            return $totalJobs > 0 ? round(($filledJobs / $totalJobs) * 100, 1) : 0;
        }
        
        return 0;
    }
    
    /**
     * Get subscription status
     */
    private function getSubscriptionStatus($user)
    {
        try {
            $subscriptionService = app(\App\Services\SubscriptionService::class);
            return $subscriptionService->getSubscriptionStats($user);
        } catch (\Exception $e) {
            return [
                'plan_name' => 'Free',
                'has_subscription' => false,
                'expires_at' => null,
                'features' => []
            ];
        }
    }
    
    /**
     * Get featured jobs for dashboard
     */
    private function getFeaturedJobs()
    {
        return JobPost::where('status', 'active')
            ->where('expires_at', '>', now())
            ->where('is_featured', true)
            ->with(['user', 'user.userType', 'applications'])
            ->latest()
            ->limit(6)
            ->get();
    }
    
    /**
     * Get trending skills
     */
    private function getTrendingSkills()
    {
        try {
            // Check if skills column exists, if not return empty array
            $skills = JobPost::where('status', 'active')
                ->where('expires_at', '>', now())
                ->whereNotNull('requirements') // Use requirements instead of skills
                ->pluck('requirements')
                ->flatMap(function($requirements) {
                    // Extract skills from requirements text
                    return explode(',', $requirements);
                })
                ->map(function($skill) {
                    return trim($skill);
                })
                ->filter(function($skill) {
                    return !empty($skill);
                })
                ->countBy()
                ->sortDesc()
                ->take(10)
                ->toArray();
            
            return $skills;
        } catch (\Exception $e) {
            // Return empty array if there's an error
            return [];
        }
    }
    
    /**
     * Get marketplace statistics
     */
    private function getMarketplaceStats()
    {
        return [
            'total_users' => \App\Models\User::count(),
            'total_agencies' => \App\Models\User::whereHas('userType', function($query) {
                $query->whereIn('name', ['ofm_agency', 'chatting_agency']);
            })->count(),
            'total_chatters' => \App\Models\User::whereHas('userType', function($query) {
                $query->where('name', 'chatter');
            })->count(),
            'total_jobs' => JobPost::where('status', 'active')->count(),
            'total_applications' => JobApplication::count(),
            'total_contracts' => Contract::count(),
            'total_earnings' => Earning::where('status', 'paid')->sum('amount'),
        ];
    }
    
    /**
     * Get contracts data for user
     */
    private function getContractsData($user)
    {
        $contracts = Contract::where(function($query) use ($user) {
            $query->where('employer_id', $user->id)
                  ->orWhere('contractor_id', $user->id);
        })
        ->with(['employer', 'contractor', 'jobPost'])
        ->latest()
        ->limit(10)
        ->get();
        
        return [
            'recent_contracts' => $contracts,
            'active_count' => $contracts->where('status', 'active')->count(),
            'completed_count' => $contracts->where('status', 'completed')->count(),
            'total_value' => $contracts->sum('total_amount'),
        ];
    }
    
    /**
     * Get earnings data for user
     */
    private function getEarningsData($user)
    {
        $earnings = Earning::where('user_id', $user->id)
            ->with(['contract'])
            ->latest()
            ->limit(10)
            ->get();
        
        $monthlyEarnings = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $amount = Earning::where('user_id', $user->id)
                ->where('status', 'paid')
                ->whereMonth('earned_date', $month->month)
                ->whereYear('earned_date', $month->year)
                ->sum('amount');
            
            $monthlyEarnings[] = [
                'month' => $month->format('M Y'),
                'amount' => $amount
            ];
        }
        
        return [
            'recent_earnings' => $earnings,
            'monthly_trend' => $monthlyEarnings,
            'total_pending' => $earnings->where('status', 'pending')->sum('amount'),
            'total_paid' => $earnings->where('status', 'paid')->sum('amount'),
        ];
    }
    
    /**
     * Get stats formatted for the view
     */
    private function getStatsForView($user, $dashboardData)
    {
        // Get basic stats
        $jobsPosted = JobPost::where('user_id', $user->id)->count();
        $jobsPostedThisMonth = JobPost::where('user_id', $user->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        $applicationsSent = JobApplication::where('user_id', $user->id)->count();
        $applicationsSentThisMonth = JobApplication::where('user_id', $user->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        $unreadMessages = Message::where('recipient_id', $user->id)
            ->where('is_read', false)
            ->count();
        
        $averageRating = Rating::where('rated_id', $user->id)
            ->avg('overall_rating') ?: 0;
        
        $totalReviews = Rating::where('rated_id', $user->id)->count();
        
        return [
            'jobs_posted' => $jobsPosted,
            'jobs_posted_this_month' => $jobsPostedThisMonth,
            'applications_sent' => $applicationsSent,
            'applications_sent_this_month' => $applicationsSentThisMonth,
            'unread_messages' => $unreadMessages,
            'average_rating' => $averageRating,
            'total_reviews' => $totalReviews,
        ];
    }
}
