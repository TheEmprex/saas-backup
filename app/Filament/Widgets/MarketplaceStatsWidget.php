<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\JobApplication;
use App\Models\JobPost;
use App\Models\User;
use Exception;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MarketplaceStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        try {
            $totalJobs = JobPost::count();
            $activeJobs = JobPost::query->where('status', 'active')->count();
            $totalApplications = JobApplication::count();
            $totalUsers = User::count();

            return [
                Stat::make('Total Jobs', $totalJobs)
                    ->description('Total job posts created')
                    ->descriptionIcon('heroicon-m-briefcase')
                    ->color('success'),

                Stat::make('Active Jobs', $activeJobs)
                    ->description('Currently active job posts')
                    ->descriptionIcon('heroicon-m-eye')
                    ->color('primary'),

                Stat::make('Total Applications', $totalApplications)
                    ->description('Total job applications')
                    ->descriptionIcon('heroicon-m-document-text')
                    ->color('warning'),

                Stat::make('Total Users', $totalUsers)
                    ->description('Registered users')
                    ->descriptionIcon('heroicon-m-users')
                    ->color('info'),
            ];
        } catch (Exception) {
            return [
                Stat::make('Error', 'N/A')
                    ->description('Unable to load stats')
                    ->descriptionIcon('heroicon-m-exclamation-triangle')
                    ->color('danger'),
            ];
        }
    }
}
