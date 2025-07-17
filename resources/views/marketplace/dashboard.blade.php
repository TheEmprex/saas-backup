<x-layouts.app>
<div class="min-h-screen bg-white dark:bg-zinc-900">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Dashboard</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Welcome back, {{ $user->name }}</p>
        </div>

        <!-- Main Actions -->
        <div class="mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Browse Jobs -->
                <a href="{{ route('marketplace.jobs') }}" class="bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg p-6 hover:bg-gray-100 dark:hover:bg-zinc-700">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6.99A23.931 23.931 0 0120 15"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Browse Jobs</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Find opportunities</p>
                        </div>
                    </div>
                </a>

                @if($user->isAgency())
                <!-- Post Job -->
                <a href="{{ route('marketplace.jobs.create') }}" class="bg-gray-50 border border-gray-200 rounded-lg p-6 hover:bg-gray-100">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-black">Post Job</h3>
                            <p class="text-sm text-black">Create listing</p>
                        </div>
                    </div>
                </a>
                @endif

                <!-- Contracts -->
                <a href="{{ route('contracts.index') }}" class="bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg p-6 hover:bg-gray-100 dark:hover:bg-zinc-700">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-indigo-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Contracts</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Manage contracts</p>
                        </div>
                    </div>
                </a>

                <!-- Profile -->
                <a href="{{ route('profile.edit') }}" class="bg-gray-50 border border-gray-200 rounded-lg p-6 hover:bg-gray-100">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-black">Profile</h3>
                            <p class="text-sm text-black">Edit profile</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        
        <!-- Recent Contracts -->
        <div class="mt-8">
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-xl font-bold text-black">Recent Contracts</h2>
                        <p class="text-black text-sm">Review your latest contracts and status</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($recentContracts ?? [] as $contract)
                        <div class="bg-white border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <h3 class="font-semibold text-black text-sm">{{ $contract->contractor->name }} / {{ $contract->employer->name }}</h3>
                                    <p class="text-black text-xs">{{ ucfirst($contract->status) }}</p>
                                </div>
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs">{{ $contract->rate }} {{ $contract->currency }}/hr</span>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-8">
                            <i class="fas fa-handshake text-gray-400 text-2xl mb-4"></i>
                            <h3 class="text-black font-semibold mb-2">No Recent Contracts</h3>
                            <p class="text-black mb-4">Start a new contract today</p>
                            <a href="{{ route('contracts.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                                Create New Contract
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        
        <!-- Subscription Status -->
        @if($subscriptionStats['has_subscription'])
            <div class="mb-8">
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h2 class="text-xl font-bold text-black">{{ $subscriptionStats['plan_name'] }}</h2>
                            <p class="text-black text-sm">Your current subscription plan</p>
                        </div>
                        <div class="flex space-x-3">
                            <a href="{{ route('subscription.plans') }}" class="bg-white border border-gray-200 px-4 py-2 rounded-lg hover:bg-gray-50 text-black">
                                Change Plan
                            </a>
                            <a href="{{ route('subscription.dashboard') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                                Manage
                            </a>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @if($user->isAgency())
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-black text-sm">Job Posts</span>
                                    <i class="fas fa-briefcase text-blue-500"></i>
                                </div>
                                <div class="text-xl font-bold text-black mb-2">{{ $subscriptionStats['job_posts_used'] }} / {{ $subscriptionStats['job_posts_limit'] ?: '∞' }}</div>
                                @if($subscriptionStats['job_posts_limit'])
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ min(($subscriptionStats['job_posts_used'] / $subscriptionStats['job_posts_limit']) * 100, 100) }}%"></div>
                                    </div>
                                @endif
                            </div>
                        @endif
                        @if($user->isChatter())
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-black text-sm">Applications</span>
                                    <i class="fas fa-file-alt text-green-500"></i>
                                </div>
                                <div class="text-xl font-bold text-black mb-2">{{ $subscriptionStats['applications_used'] }} / {{ $subscriptionStats['applications_limit'] ?: '∞' }}</div>
                                @if($subscriptionStats['applications_limit'])
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ min(($subscriptionStats['applications_used'] / $subscriptionStats['applications_limit']) * 100, 100) }}%"></div>
                                    </div>
                                @endif
                            </div>
                        @endif
                        <div class="bg-white border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-black text-sm">Expires</span>
                                <i class="fas fa-calendar-alt text-purple-500"></i>
                            </div>
                            <div class="text-lg font-semibold text-black mb-1">{{ $subscriptionStats['expires_at'] ? \Carbon\Carbon::parse($subscriptionStats['expires_at'])->format('M d, Y') : 'Never' }}</div>
                            @if($subscriptionStats['expires_at'])
                                <div class="text-xs text-black">
                                    {{ \Carbon\Carbon::parse($subscriptionStats['expires_at'])->diffForHumans() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="mb-8">
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-xl font-bold text-black">No Active Subscription</h2>
                            <p class="text-black mb-4">Unlock the full potential of our marketplace with a subscription plan.</p>
                            <ul class="text-sm text-black space-y-1">
                                <li>• Post unlimited jobs</li>
                                <li>• Advanced messaging features</li>
                                <li>• Priority support</li>
                            </ul>
                        </div>
                        <div class="flex flex-col space-y-3">
                            <a href="{{ route('subscription.plans') }}" class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600">
                                Choose Your Plan
                            </a>
                            <a href="{{ route('marketplace.jobs') }}" class="bg-white border border-gray-200 px-6 py-3 rounded-lg hover:bg-gray-50 text-black text-center">
                                Browse Jobs
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Featured Jobs Section -->
        <div class="mb-8">
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-xl font-bold text-black">Featured Jobs</h2>
                        <p class="text-black text-sm">Discover top opportunities from premium employers</p>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('marketplace.jobs') }}" class="bg-white border border-gray-200 px-4 py-2 rounded-lg hover:bg-gray-50 text-black">
                            Browse All Jobs
                        </a>
                        @if($user->isAgency())
                            <a href="{{ route('marketplace.jobs.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                                Post Job
                            </a>
                        @endif
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($featuredJobs ?? [] as $job)
                        <div class="bg-white border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-briefcase text-white text-sm"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-black text-sm">{{ $job->title }}</h3>
                                        <p class="text-black text-xs">{{ $job->user->name }}</p>
                                    </div>
                                </div>
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs">Featured</span>
                            </div>
                            <div class="space-y-1 mb-3">
                                <div class="flex items-center text-black text-xs">
                                    <i class="fas fa-map-marker-alt mr-2 text-blue-500"></i>
                                    <span>{{ $job->market }}</span>
                                </div>
                                <div class="flex items-center text-black text-xs">
                                    <i class="fas fa-clock mr-2 text-blue-500"></i>
                                    <span>{{ ucfirst($job->rate_type) }}</span>
                                </div>
                                <div class="flex items-center text-black text-xs">
                                    <i class="fas fa-users mr-2 text-blue-500"></i>
                                    <span>{{ $job->applications->count() }} applications</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="text-black font-semibold text-sm">
                                    @if($job->rate_type === 'fixed')
                                        ${{ number_format($job->rate, 0) }}
                                    @else
                                        ${{ number_format($job->rate, 0) }}/hr
                                    @endif
                                </div>
                                <a href="{{ route('marketplace.jobs.show', $job->id) }}" class="bg-blue-500 text-white px-3 py-1 rounded-lg hover:bg-blue-600 text-xs">
                                    View Details
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-8">
                            <i class="fas fa-briefcase text-gray-400 text-2xl mb-4"></i>
                            <h3 class="text-black font-semibold mb-2">No Featured Jobs Yet</h3>
                            <p class="text-black mb-4">Be the first to discover amazing opportunities!</p>
                            <a href="{{ route('marketplace.jobs') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                                Browse All Jobs
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Jobs Posted -->
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-briefcase text-white text-sm"></i>
                    </div>
                    <div class="text-xs text-black bg-gray-100 px-2 py-1 rounded-full">
                        @if($user->isAgency())
                            +{{ $stats['jobs_posted_this_month'] ?? 0 }} this month
                        @else
                            Total posted
                        @endif
                    </div>
                </div>
                <div class="space-y-1">
                    <h4 class="text-2xl font-bold text-black">{{ $stats['jobs_posted'] }}</h4>
                    <p class="text-sm text-black">Jobs Posted</p>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ min(($stats['jobs_posted'] / 10) * 100, 100) }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Applications -->
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-alt text-white text-sm"></i>
                    </div>
                    <div class="text-xs text-black bg-gray-100 px-2 py-1 rounded-full">
                        @if($user->isChatter())
                            +{{ $stats['applications_sent_this_month'] ?? 0 }} this month
                        @else
                            Total sent
                        @endif
                    </div>
                </div>
                <div class="space-y-1">
                    <h4 class="text-2xl font-bold text-black">{{ $stats['applications_sent'] }}</h4>
                    <p class="text-sm text-black">Applications Sent</p>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ min(($stats['applications_sent'] / 20) * 100, 100) }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Messages -->
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-envelope text-white text-sm"></i>
                    </div>
                    <div class="text-xs text-black bg-gray-100 px-2 py-1 rounded-full">
                        @if(($stats['unread_messages'] ?? 0) > 0)
                            <span class="text-red-600 font-medium">New!</span>
                        @else
                            All read
                        @endif
                    </div>
                </div>
                <div class="space-y-1">
                    <h4 class="text-2xl font-bold text-black">{{ $stats['unread_messages'] ?? 0 }}</h4>
                    <p class="text-sm text-black">Unread Messages</p>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        @if(($stats['unread_messages'] ?? 0) > 0)
                            <div class="bg-purple-500 h-2 rounded-full" style="width: 100%"></div>
                        @else
                            <div class="bg-gray-300 h-2 rounded-full" style="width: 100%"></div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Rating -->
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-yellow-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-star text-white text-sm"></i>
                    </div>
                    <div class="text-xs text-black bg-gray-100 px-2 py-1 rounded-full">
                        @if($stats['average_rating'] >= 4.5)
                            Excellent
                        @elseif($stats['average_rating'] >= 4.0)
                            Very Good
                        @elseif($stats['average_rating'] >= 3.5)
                            Good
                        @else
                            Average
                        @endif
                    </div>
                </div>
                <div class="space-y-1">
                    <h4 class="text-2xl font-bold text-black">{{ number_format($stats['average_rating'], 1) }}</h4>
                    <p class="text-sm text-black">Average Rating</p>
                    <div class="flex items-center space-x-1">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= floor($stats['average_rating']))
                                <i class="fas fa-star text-yellow-500 text-sm"></i>
                            @elseif($i == ceil($stats['average_rating']) && $stats['average_rating'] - floor($stats['average_rating']) >= 0.5)
                                <i class="fas fa-star-half-alt text-yellow-500 text-sm"></i>
                            @else
                                <i class="far fa-star text-gray-300 text-sm"></i>
                            @endif
                        @endfor
                        <span class="text-xs text-black ml-2">({{ $stats['total_reviews'] ?? 0 }} reviews)</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Jobs -->
            @if($user->isAgency())
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-900">Recent Job Posts</h3>
                            <a href="{{ route('marketplace.jobs.create') }}" class="bg-blue-600 text-white px-3 py-1 rounded-md text-sm hover:bg-blue-700 transition-colors">
                                <i class="fas fa-plus mr-1"></i>Post Job
                            </a>
                        </div>
                    </div>
                    <div class="p-6">
                        @if($recentJobs->count() > 0)
                            <div class="space-y-4">
                                @foreach($recentJobs as $job)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $job->title }}</h4>
                                            <p class="text-sm text-gray-600">{{ $job->market }} • {{ ucfirst($job->rate_type) }}</p>
                                            <p class="text-xs text-gray-500 mt-1">{{ $job->applications->count() }} applications</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ ucfirst($job->status) }}
                                            </span>
                                            <p class="text-xs text-gray-500 mt-1">{{ $job->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-briefcase text-gray-400 text-3xl mb-4"></i>
                                <p class="text-gray-500">No jobs posted yet</p>
                                <a href="{{ route('marketplace.jobs.create') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Post your first job
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Recent Applications -->
            @if($user->isChatter())
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-900">Recent Applications</h3>
                            <a href="{{ route('marketplace.jobs') }}" class="bg-green-600 text-white px-3 py-1 rounded-md text-sm hover:bg-green-700 transition-colors">
                                <i class="fas fa-search mr-1"></i>Find Jobs
                            </a>
                        </div>
                    </div>
                    <div class="p-6">
                        @if($recentApplications->count() > 0)
                            <div class="space-y-4">
                                @foreach($recentApplications as $application)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $application->jobPost->title }}</h4>
                                            <p class="text-sm text-gray-600">{{ $application->jobPost->user->name }}</p>
                                            <p class="text-xs text-gray-500 mt-1">Applied {{ $application->created_at->diffForHumans() }}</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                @if($application->status == 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($application->status == 'hired') bg-green-100 text-green-800
                                                @elseif($application->status == 'rejected') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ ucfirst($application->status) }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-file-alt text-gray-400 text-3xl mb-4"></i>
                                <p class="text-gray-500">No applications yet</p>
                                <a href="{{ route('marketplace.jobs') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Browse available jobs
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('marketplace.jobs') }}" class="flex items-center p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow">
                <div class="flex-shrink-0">
                    <i class="fas fa-search text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h4 class="font-medium text-gray-900">Browse Jobs</h4>
                    <p class="text-sm text-gray-600">Find opportunities</p>
                </div>
            </a>
            
            <a href="{{ route('marketplace.profiles') }}" class="flex items-center p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow">
                <div class="flex-shrink-0">
                    <i class="fas fa-users text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h4 class="font-medium text-gray-900">Find Talent</h4>
                    <p class="text-sm text-gray-600">Browse profiles</p>
                </div>
            </a>
            
            <a href="{{ route('marketplace.messages') }}" class="flex items-center p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow">
                <div class="flex-shrink-0">
                    <i class="fas fa-envelope text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h4 class="font-medium text-gray-900">Messages</h4>
                    <p class="text-sm text-gray-600">Chat with contacts</p>
                </div>
            </a>
            
            <a href="{{ route('contracts.index') }}" class="flex items-center p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow">
                <div class="flex-shrink-0">
                    <i class="fas fa-file-contract text-indigo-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h4 class="font-medium text-gray-900">Contracts</h4>
                    <p class="text-sm text-gray-600">Manage contracts</p>
                </div>
            </a>
        </div>
        
        <!-- Additional Quick Actions -->
        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('profile.show') }}" class="flex items-center p-4 bg-white rounded-lg shadow hover:shadow-md transition-shadow">
                <div class="flex-shrink-0">
                    <i class="fas fa-user text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h4 class="font-medium text-gray-900">Profile</h4>
                    <p class="text-sm text-gray-600">View your profile</p>
                </div>
            </a>
        </div>
    </div>
</div>
</x-layouts.app>
