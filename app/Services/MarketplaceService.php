<?php

namespace App\Services;

use App\Models\JobPost;
use App\Models\UserType;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\JobApplication;
use App\Models\Message;
use App\Models\Conversation;
use App\Models\Contract;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class MarketplaceService
{
    /**
     * Get dashboard statistics for a user
     */
    public function getDashboardStats(User $user): array
    {
        return Cache::remember(
            "user_dashboard_stats_{$user->id}",
            now()->addMinutes(10),
            function () use ($user) {
                return [
                    'jobs_posted' => $user->jobPosts()->count(),
                    'jobs_posted_this_month' => $user->jobPosts()
                        ->whereMonth('created_at', now()->month)
                        ->count(),
                    'active_jobs' => $user->jobPosts()
                        ->where('status', 'active')
                        ->count(),
                    'applications_sent' => $user->jobApplications()->count(),
                    'applications_sent_this_month' => $user->jobApplications()
                        ->whereMonth('created_at', now()->month)
                        ->count(),
                    'applications_received' => $this->getApplicationsReceivedCount($user),
                    'unread_messages' => $this->getUnreadMessagesCount($user),
                    'average_rating' => $user->ratingsReceived()->avg('overall_rating') ?? 0,
                    'total_ratings' => $user->ratingsReceived()->count(),
                    'total_reviews' => $user->ratingsReceived()->count(),
                    'profile_views' => $user->userProfile?->views ?? 0,
                    'earnings_this_month' => $this->getEarningsThisMonth($user),
                ];
            }
        );
    }

    /**
     * Get subscription statistics for a user
     */
    public function getSubscriptionStats(User $user): array
    {
        try {
            $subscriptionService = app(\App\Services\SubscriptionService::class);
            return $subscriptionService->getSubscriptionStats($user);
        } catch (\Exception $e) {
            // Fallback if SubscriptionService doesn't exist
            return [
                'plan_name' => 'Free',
                'job_posts_limit' => 0,
                'job_posts_used' => 0,
                'features' => []
            ];
        }
    }

    /**
     * Get recent jobs for a user
     */
    public function getRecentJobs(User $user, int $limit = 5): Collection
    {
        return $user->jobPosts()
            ->with(['applications' => function ($query) {
                $query->select('id', 'job_post_id', 'status')->limit(5);
            }])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent applications for a user
     */
    public function getRecentApplications(User $user, int $limit = 5): Collection
    {
        return $user->jobApplications()
            ->with([
                'jobPost:id,title,status,user_id', 
                'jobPost.user:id,name'
            ])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent contracts for a user
     */
    public function getRecentContracts(User $user, int $limit = 5): Collection
    {
        return Contract::where(function($query) use ($user) {
                $query->where('employer_id', $user->id)
                      ->orWhere('contractor_id', $user->id);
            })
            ->with([
                'employer:id,name,avatar', 
                'contractor:id,name,avatar'
            ])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get featured jobs for homepage/dashboard
     */
    public function getFeaturedJobs(int $limit = 6): Collection
    {
        return Cache::remember(
            "featured_jobs_{$limit}",
            now()->addMinutes(30),
            function () use ($limit) {
                return JobPost::activeAndNotExpired()
                    ->withOptimizedRelations()
                    ->featuredWithPriority()
                    ->limit($limit)
                    ->get();
            }
        );
    }

    /**
     * Get marketplace statistics
     */
    public function getMarketplaceStats(): array
    {
        return Cache::remember(
            'marketplace_stats',
            now()->addHours(1),
            function () {
                return [
                    'total_jobs' => JobPost::where('status', 'active')->count(),
                    'total_chatters' => User::whereHas('userType', function($query) {
                        $query->where('name', 'chatter');
                    })->count(),
                    'total_agencies' => User::whereHas('userType', function($query) {
                        $query->whereIn('name', ['ofm_agency', 'chatting_agency']);
                    })->count(),
                    'jobs_filled' => JobPost::where('status', 'closed')->count()
                ];
            }
        );
    }

    /**
     * Search and filter jobs
     */
    public function searchJobs(array $filters = [], int $perPage = 18): LengthAwarePaginator
    {
        return JobPost::activeAndNotExpired()
            ->withOptimizedRelations()
            ->applyFilters($filters)
            ->featuredWithPriority()
            ->paginate($perPage);
    }

    /**
     * Get job details with related data
     */
    public function getJobDetails(JobPost $job): JobPost
    {
        return $job->load([
            'user:id,name,avatar',
            'user.userType:id,name',
            'user.userProfile:user_id,bio,rating,total_reviews',
            'applications' => function ($query) {
                $query->with('user:id,name,avatar')
                      ->latest()
                      ->limit(10);
            }
        ]);
    }

    /**
     * Get user profiles with filtering
     */
    public function searchProfiles(array $filters = [], int $perPage = 18): LengthAwarePaginator
    {
        $query = User::whereHas('userProfile')
            ->with([
                'userType:id,name',
                'userProfile:user_id,bio,hourly_rate,rating,total_reviews,skills,experience_level'
            ]);

        $this->applyProfileFilters($query, $filters);

        return $query->orderBy('created_at', 'desc')
                    ->paginate($perPage);
    }

    /**
     * Get profile details with related data
     */
    public function getProfileDetails(User $user): User
    {
        // Increment profile view
        if ($user->userProfile) {
            $user->userProfile->increment('views');
        }

        return $user->load([
            'userType:id,name',
            'userProfile',
            'ratingsReceived' => function ($query) {
                $query->with('rater:id,name')
                      ->latest()
                      ->limit(10);
            },
            'jobPosts' => function ($query) {
                $query->where('status', 'active')
                      ->latest()
                      ->limit(5);
            }
        ]);
    }

    /**
     * Private helper methods
     */
    private function applyJobFilters($query, array $filters): void
    {
        if (!empty($filters['market'])) {
            $query->where('market', $filters['market']);
        }

        if (!empty($filters['experience_level'])) {
            $query->where('experience_level', $filters['experience_level']);
        }

        if (!empty($filters['rate_type'])) {
            $query->where('rate_type', $filters['rate_type']);
        }

        if (!empty($filters['contract_type'])) {
            $query->where('contract_type', $filters['contract_type']);
        }

        if (!empty($filters['min_rate'])) {
            $query->where(function($q) use ($filters) {
                $q->where('hourly_rate', '>=', $filters['min_rate'])
                  ->orWhere('fixed_rate', '>=', $filters['min_rate']);
            });
        }

        if (!empty($filters['max_rate'])) {
            $query->where(function($q) use ($filters) {
                $q->where('hourly_rate', '<=', $filters['max_rate'])
                  ->orWhere('fixed_rate', '<=', $filters['max_rate']);
            });
        }

        if (!empty($filters['timezone'])) {
            $query->where('required_timezone', $filters['timezone']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('requirements', 'like', "%{$search}%")
                  ->orWhere('benefits', 'like', "%{$search}%");
            });
        }
    }

    private function applyProfileFilters($query, array $filters): void
    {
        if (!empty($filters['user_type'])) {
            $query->whereHas('userType', function($q) use ($filters) {
                $q->where('name', $filters['user_type']);
            });
        }

        if (!empty($filters['experience_level'])) {
            $query->whereHas('userProfile', function($q) use ($filters) {
                $q->where('experience_level', $filters['experience_level']);
            });
        }

        if (!empty($filters['min_rate'])) {
            $query->whereHas('userProfile', function($q) use ($filters) {
                $q->where('hourly_rate', '>=', $filters['min_rate']);
            });
        }

        if (!empty($filters['max_rate'])) {
            $query->whereHas('userProfile', function($q) use ($filters) {
                $q->where('hourly_rate', '<=', $filters['max_rate']);
            });
        }

        if (!empty($filters['skills'])) {
            $skills = is_array($filters['skills']) ? $filters['skills'] : explode(',', $filters['skills']);
            $query->whereHas('userProfile', function($q) use ($skills) {
                foreach ($skills as $skill) {
                    $q->whereJsonContains('skills', trim($skill));
                }
            });
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('userProfile', function($profileQuery) use ($search) {
                      $profileQuery->where('bio', 'like', "%{$search}%");
                  });
            });
        }
    }

    private function getApplicationsReceivedCount(User $user): int
    {
        return $user->jobPosts()
            ->withCount('applications')
            ->get()
            ->sum('applications_count');
    }

    private function getUnreadMessagesCount(User $user): int
    {
        return Conversation::forUser($user->id)
            ->get()
            ->sum(function ($conversation) use ($user) {
                return $conversation->getUnreadCountForUser($user->id);
            });
    }

    private function getEarningsThisMonth(User $user): float
    {
        // This would depend on your earnings/payment system
        // For now, returning 0 as placeholder
        return 0.0;
        
        // Example implementation:
        // return $user->earnings()
        //     ->whereMonth('created_at', now()->month)
        //     ->sum('amount');
    }
}
