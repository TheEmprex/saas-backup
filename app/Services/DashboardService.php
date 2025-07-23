<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\JobApplication;
use App\Models\JobPost;
use App\Models\User;
use App\Models\Earning;
use App\Models\Analytics;

class DashboardService
{
    public function getDashboardData(User $user)
    {
        return [
            'jobs_posted' => $this->getJobsPostedCount($user),
            'applications_received' => $this->getApplicationsReceivedCount($user),
            'monthly_earnings' => $this->getMonthlyEarnings($user),
            'profile_views' => $this->getProfileViews($user),
            'unread_messages' => $this->getUnreadMessagesCount($user)
        ];
    }

    protected function getJobsPostedCount(User $user)
    {
        return $user->jobPosts()->active()->count();
    }

    protected function getApplicationsReceivedCount(User $user)
    {
        return JobApplication::whereHas('jobPost', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();
    }

    protected function getMonthlyEarnings(User $user)
    {
        return $user->earnings()->thisMonth()->sum('amount');
    }

    protected function getProfileViews(User $user)
    {
        return Analytics::byMetric('profile_views')->where('user_id', $user->id)->sum('metric_value');
    }

    protected function getUnreadMessagesCount(User $user)
    {
        // Assuming there is a method to get unread messages count
        return $user->unreadMessages()->count();
    }
}
