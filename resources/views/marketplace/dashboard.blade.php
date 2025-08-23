<x-layouts.app>
    <div class="min-h-screen bg-gray-50 dark:bg-zinc-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Enhanced Header with Stats -->
            <div class="mb-12">
                <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-2xl shadow-sm p-8">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <div class="flex-1">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h1 class="text-4xl font-black text-gray-900 dark:text-white mb-2">
                                        Welcome back, <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">{{ explode(' ', $user->name)[0] }}</span>
                                    </h1>
                                <p class="text-lg text-gray-600 dark:text-gray-300">Here's what's happening with your marketplace activity</p>
                                </div>
                            </div>
                            
                            <!-- Quick Stats Row -->
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl p-4">
                                    <div class="text-2xl font-bold text-blue-700 dark:text-blue-300">{{ $stats['jobs_posted'] ?? 0 }}</div>
                                    <div class="text-sm text-blue-600 dark:text-blue-400">Jobs Posted</div>
                                </div>
                                <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-xl p-4">
                                    <div class="text-2xl font-bold text-green-700 dark:text-green-300">{{ $stats['applications_sent'] ?? 0 }}</div>
                                    <div class="text-sm text-green-600 dark:text-green-400">Applications</div>
                                </div>
                                <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-xl p-4">
                                    <div class="text-2xl font-bold text-purple-700 dark:text-purple-300">{{ $stats['unread_messages'] ?? 0 }}</div>
                                    <div class="text-sm text-purple-600 dark:text-purple-400">Messages</div>
                                </div>
                                <div class="bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-800/20 rounded-xl p-4">
                                    <div class="text-2xl font-bold text-orange-700 dark:text-orange-300">{{ number_format($stats['average_rating'] ?? 0, 1) }}</div>
                                    <div class="text-sm text-orange-600 dark:text-orange-400">Rating</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Quick Action Panel -->
                        <div class="flex flex-col sm:flex-row lg:flex-col gap-3">
                            @if($user->isAgency())
                                <a href="{{ route('marketplace.jobs.create') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Post New Job
                                </a>
                            @endif
                            <a href="{{ route('marketplace.jobs.index') }}" class="inline-flex items-center justify-center px-6 py-3 bg-white dark:bg-zinc-700 text-gray-900 dark:text-white rounded-xl font-semibold shadow-lg hover:shadow-xl border border-gray-200 dark:border-zinc-600 transform hover:scale-105 transition-all duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Browse Jobs
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        
            <!-- Recent Contracts -->
            <div class="mb-8">
                <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Recent <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">Contracts</span></h2>
                            <p class="text-gray-600 dark:text-gray-400">Review your latest contracts and status</p>
                        </div>
                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl p-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse($recentContracts ?? [] as $contract)
                            <div class="group bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-xl p-5 hover:shadow-md hover:border-blue-300 dark:hover:border-blue-600 transition-all duration-200">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-gray-900 dark:text-gray-100 text-sm">{{ $contract->contractor->name }}</h3>
                                            <p class="text-gray-500 dark:text-gray-400 text-xs">with {{ $contract->employer->name }}</p>
                                        </div>
                                    </div>
                                    <span class="bg-gradient-to-r from-blue-100 to-purple-100 text-blue-800 px-3 py-1 rounded-full text-xs font-medium">{{ $contract->rate }} {{ $contract->currency }}/hr</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($contract->status == 'active') bg-green-100 text-green-800
                                        @elseif($contract->status == 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($contract->status == 'completed') bg-blue-100 text-blue-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($contract->status) }}
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $contract->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-12">
                                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">No Recent Contracts</h3>
                                <p class="text-gray-600 dark:text-gray-400 mb-6">Start a new contract today and begin building professional relationships</p>
                                <a href="{{ route('contracts.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Create New Contract
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        
            <!-- Subscription Status -->
            @if(isset($subscriptionStats['has_subscription']) && $subscriptionStats['has_subscription'])
                <div class="mb-8">
                    <div class="bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h2 class="text-lg font-semibold">Current <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-pink-600">{{ $subscriptionStats['plan_name'] }}</span></h2>
                                <p class="text-gray-600 dark:text-gray-400 text-sm">Your current subscription plan</p>
                            </div>
                            <div class="flex space-x-3">
                                <a href="{{ route('subscription.plans') }}" class="bg-white dark:bg-zinc-700 border border-gray-200 dark:border-zinc-600 px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-600 text-gray-900 dark:text-gray-100">
                                    Change Plan
                                </a>
                                <a href="{{ route('subscription.dashboard') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                                    Manage
                                </a>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            @if($user->isAgency())
                                <div class="bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-gray-900 dark:text-gray-100 text-sm">Job Posts</span>
                                        <i class="fas fa-briefcase text-blue-500"></i>
                                    </div>
                                    <div class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">{{ $subscriptionStats['job_posts_used'] }} / {{ $subscriptionStats['job_posts_limit'] ?: '∞' }}</div>
                                    @if($subscriptionStats['job_posts_limit'])
                                        <div class="w-full bg-gray-200 dark:bg-zinc-700 rounded-full h-2">
                                            <div class="bg-blue-500 h-2 rounded-full" style="width: {{ min(($subscriptionStats['job_posts_used'] / $subscriptionStats['job_posts_limit']) * 100, 100) }}%"></div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                            @if($user->isChatter())
                                <div class="bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-gray-900 dark:text-gray-100 text-sm">Applications</span>
                                        <i class="fas fa-file-alt text-green-500"></i>
                                    </div>
                                    <div class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">{{ $subscriptionStats['applications_used'] }} / {{ $subscriptionStats['applications_limit'] ?: '∞' }}</div>
                                    @if($subscriptionStats['applications_limit'])
                                        <div class="w-full bg-gray-200 dark:bg-zinc-700 rounded-full h-2">
                                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ min(($subscriptionStats['applications_used'] / $subscriptionStats['applications_limit']) * 100, 100) }}%"></div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                            <div class="bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-gray-900 dark:text-gray-100 text-sm">Expires</span>
                                    <i class="fas fa-calendar-alt text-purple-500"></i>
                                </div>
                                <div class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">{{ $subscriptionStats['expires_at'] ? \Carbon\Carbon::parse($subscriptionStats['expires_at'])->format('M d, Y') : 'Never' }}</div>
                                @if($subscriptionStats['expires_at'])
                                    <div class="text-xs text-gray-600 dark:text-gray-400">
                                        {{ \Carbon\Carbon::parse($subscriptionStats['expires_at'])->diffForHumans() }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="mb-8">
                    <div class="bg-gradient-to-r from-orange-50 to-red-50 dark:from-orange-900/20 dark:to-red-900/20 border border-orange-200 dark:border-orange-700 rounded-2xl p-6 shadow-lg">
                        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center space-y-6 lg:space-y-0">
                            <div class="flex-1">
                                <div class="flex items-center gap-4 mb-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">No <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-600 to-red-600">Active Subscription</span></h2>
                                        <p class="text-gray-600 dark:text-gray-400">Unlock the full potential of our marketplace with a subscription plan</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Post unlimited jobs</span>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Advanced messaging</span>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Priority support</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col space-y-3">
                                <a href="{{ route('subscription.plans') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-orange-600 to-red-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    Choose Your Plan
                                </a>
                                <a href="{{ route('marketplace.jobs.index') }}" class="inline-flex items-center justify-center px-6 py-3 bg-white dark:bg-zinc-700 text-gray-900 dark:text-white rounded-xl font-semibold shadow-lg hover:shadow-xl border border-gray-200 dark:border-zinc-600 transform hover:scale-105 transition-all duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Browse Jobs
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Featured Jobs Section -->
            <div class="mb-8">
                <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Featured <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">Jobs</span></h2>
                            <p class="text-gray-600 dark:text-gray-400">Discover top opportunities from premium employers</p>
                        </div>
                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl p-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse($featuredJobs ?? [] as $job)
                            <div class="group bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-xl p-5 hover:shadow-md hover:border-blue-300 dark:hover:border-blue-600 transition-all duration-200">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-gray-900 dark:text-gray-100 text-sm">{{ $job->title }}</h3>
                                            <p class="text-gray-500 dark:text-gray-400 text-xs">by {{ $job->user->name }}</p>
                                        </div>
                                    </div>
                                    <span class="bg-gradient-to-r from-blue-100 to-purple-100 text-blue-800 px-3 py-1 rounded-full text-xs font-medium">Featured</span>
                                </div>
                                <div class="space-y-2 mb-4">
                                    <div class="flex items-center text-gray-600 dark:text-gray-400 text-xs">
                                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span>{{ $job->market }}</span>
                                    </div>
                                    <div class="flex items-center text-gray-600 dark:text-gray-400 text-xs">
                                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span>{{ ucfirst($job->rate_type) }}</span>
                                    </div>
                                    <div class="flex items-center text-gray-600 dark:text-gray-400 text-xs">
                                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        <span>{{ $job->applications->count() }} applications</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="text-gray-900 dark:text-gray-100 font-semibold text-sm">
                                        @if($job->rate_type === 'fixed')
                                            ${{ number_format($job->fixed_rate ?? 0, 0) }}
                                        @else
                                            ${{ number_format($job->hourly_rate ?? 0, 0) }}/hr
                                        @endif
                                    </div>
                                    <a href="{{ route('marketplace.jobs.show', $job->id) }}" class="inline-flex items-center px-3 py-1 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 text-xs">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-12">
                                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">No Featured Jobs Yet</h3>
                                <p class="text-gray-600 dark:text-gray-400 mb-6">Be the first to discover amazing opportunities!</p>
                                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                                    <a href="{{ route('marketplace.jobs.index') }}" class="inline-flex items-center px-6 py-3 bg-white dark:bg-zinc-700 text-gray-900 dark:text-white rounded-xl font-semibold shadow-lg hover:shadow-xl border border-gray-200 dark:border-zinc-600 transform hover:scale-105 transition-all duration-200">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                        Browse All Jobs
                                    </a>
                                    @if($user->isAgency())
                                        <a href="{{ route('marketplace.jobs.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Post New Job
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <!-- Statistics Cards -->
            <div class="mb-8">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Statistics <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-blue-600">Overview</span></h2>
                    <p class="text-gray-600 dark:text-gray-400">Your activity metrics and performance summary</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Jobs Posted -->
                <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="text-xs text-gray-900 dark:text-gray-100 bg-blue-50 dark:bg-blue-900/20 px-3 py-1 rounded-full font-medium">
                            @if($user->isAgency())
                                +{{ $stats['jobs_posted_this_month'] ?? 0 }} this month
                            @else
                                Total posted
                            @endif
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-baseline justify-between">
                            <h4 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['jobs_posted'] ?? 0 }}</h4>
                            <div class="flex items-center text-sm text-green-600 dark:text-green-400">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17l9.2-9.2M17 17V7H7"></path>
                                </svg>
                                <span>{{ $stats['jobs_posted_this_month'] ?? 0 }}%</span>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">Jobs Posted</p>
                        <div class="w-full bg-gray-200 dark:bg-zinc-700 rounded-full h-2.5">
                            <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ min((($stats['jobs_posted'] ?? 0) / 10) * 100, 100) }}%"></div>
                        </div>
                        <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                            <span>Progress</span>
                            <span>{{ min((($stats['jobs_posted'] ?? 0) / 10) * 100, 100) }}%</span>
                        </div>
                    </div>
                </div>

                <!-- Applications -->
                <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="text-xs text-gray-900 dark:text-gray-100 bg-green-50 dark:bg-green-900/20 px-3 py-1 rounded-full font-medium">
                            @if($user->isChatter())
                                +{{ $stats['applications_sent_this_month'] ?? 0 }} this month
                            @else
                                Total sent
                            @endif
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-baseline justify-between">
                            <h4 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['applications_sent'] ?? 0 }}</h4>
                            <div class="flex items-center text-sm text-green-600 dark:text-green-400">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17l9.2-9.2M17 17V7H7"></path>
                                </svg>
                                <span>{{ $stats['applications_sent_this_month'] ?? 0 }}%</span>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">Applications Sent</p>
                        <div class="w-full bg-gray-200 dark:bg-zinc-700 rounded-full h-2.5">
                            <div class="bg-gradient-to-r from-green-500 to-green-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ min((($stats['applications_sent'] ?? 0) / 20) * 100, 100) }}%"></div>
                        </div>
                        <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                            <span>Progress</span>
                            <span>{{ min((($stats['applications_sent'] ?? 0) / 20) * 100, 100) }}%</span>
                        </div>
                    </div>
                </div>

                <!-- Messages -->
                <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="text-xs text-gray-900 dark:text-gray-100 bg-purple-50 dark:bg-purple-900/20 px-3 py-1 rounded-full font-medium">
                            @if(($stats['unread_messages'] ?? 0) > 0)
                                <span class="text-red-600 dark:text-red-400 font-bold">{{ $stats['unread_messages'] }} New!</span>
                            @else
                                <span class="text-green-600 dark:text-green-400">All Read</span>
                            @endif
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-baseline justify-between">
                            <h4 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['unread_messages'] ?? 0 }}</h4>
                            <div class="flex items-center text-sm {{ ($stats['unread_messages'] ?? 0) > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                @if(($stats['unread_messages'] ?? 0) > 0)
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @endif
                                <span>{{ ($stats['unread_messages'] ?? 0) > 0 ? 'Action needed' : 'Up to date' }}</span>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">Unread Messages</p>
                        <div class="w-full bg-gray-200 dark:bg-zinc-700 rounded-full h-2.5">
                            @if(($stats['unread_messages'] ?? 0) > 0)
                                <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-2.5 rounded-full transition-all duration-500" style="width: 100%"></div>
                            @else
                                <div class="bg-gradient-to-r from-green-500 to-green-600 h-2.5 rounded-full transition-all duration-500" style="width: 100%"></div>
                            @endif
                        </div>
                        <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                            <span>Status</span>
                            <a href="{{ route('messages.index') }}" class="text-purple-600 dark:text-purple-400 hover:underline">View All</a>
                        </div>
                    </div>
                </div>

                <!-- Rating -->
                <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                        </div>
                        <div class="text-xs text-gray-900 dark:text-gray-100 bg-yellow-50 dark:bg-yellow-900/20 px-3 py-1 rounded-full font-medium">
                            @if(($stats['average_rating'] ?? 0) >= 4.5)
                                <span class="text-green-600 dark:text-green-400">Excellent</span>
                            @elseif(($stats['average_rating'] ?? 0) >= 4.0)
                                <span class="text-blue-600 dark:text-blue-400">Very Good</span>
                            @elseif(($stats['average_rating'] ?? 0) >= 3.5)
                                <span class="text-yellow-600 dark:text-yellow-400">Good</span>
                            @elseif(($stats['average_rating'] ?? 0) >= 3.0)
                                <span class="text-orange-600 dark:text-orange-400">Average</span>
                            @else
                                <span class="text-gray-600 dark:text-gray-400">New</span>
                            @endif
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-baseline justify-between">
                            <h4 class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($stats['average_rating'] ?? 0, 1) }}</h4>
                            <div class="flex items-center text-sm text-yellow-600 dark:text-yellow-400">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                </svg>
                                <span>/5.0</span>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">Average Rating</p>
                        <div class="flex items-center space-x-1">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($stats['average_rating'] ?? 0))
                                    <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                    </svg>
                                @elseif($i == ceil($stats['average_rating'] ?? 0) && (($stats['average_rating'] ?? 0) - floor($stats['average_rating'] ?? 0)) >= 0.5)
                                    <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                    </svg>
                                @endif
                            @endfor
                        </div>
                        <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                            <span>{{ $stats['total_reviews'] ?? 0 }} reviews</span>
                            <a href="#" class="text-yellow-600 dark:text-yellow-400 hover:underline">View All</a>
                        </div>
                    </div>
                </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="mb-8">
                <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-2xl shadow-sm p-8">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Recent <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-600 to-blue-600">Activity</span></h2>
                        <p class="text-gray-600 dark:text-gray-400">Your latest job posts and applications</p>
                    </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Jobs -->
                @if($user->isAgency())
                    <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200">
                        <div class="p-6 border-b border-gray-200 dark:border-zinc-700">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Recent <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">Job Posts</span></h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Your latest job listings</p>
                                    </div>
                                </div>
                                <a href="{{ route('marketplace.jobs.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-semibold rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Post Job
                                </a>
                            </div>
                        </div>
                        <div class="p-6">
                            @if($recentJobs->count() > 0)
                                <div class="space-y-4">
                                    @foreach($recentJobs as $job)
                                        <div class="group p-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-200 dark:border-blue-800 rounded-xl hover:shadow-md transition-all duration-200">
                                            <div class="flex items-center justify-between">
                                                <div class="flex-1">
                                                    <div class="flex items-center space-x-3 mb-2">
                                                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                            </svg>
                                                        </div>
                                                        <h4 class="font-bold text-gray-900 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $job->title }}</h4>
                                                    </div>
                                                    <div class="flex items-center space-x-4 text-sm text-gray-600 dark:text-gray-400 mb-2">
                                                        <div class="flex items-center">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                            <span class="capitalize">{{ str_replace('_', ' ', $job->market) }}</span>
                                                        </div>
                                                        <div class="flex items-center">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                                            </svg>
                                                            <span class="capitalize">{{ str_replace('_', ' ', $job->rate_type) }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-500">
                                                        <div class="flex items-center">
                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                            </svg>
                                                            <span class="font-medium text-blue-600 dark:text-blue-400">{{ $job->applications->count() }} applications</span>
                                                        </div>
                                                        <div class="flex items-center">
                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                            <span>{{ $job->created_at->diffForHumans() }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="ml-4 flex flex-col items-end space-y-2">
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                                                        @if($job->status === 'active') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300
                                                        @elseif($job->status === 'draft') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300
                                                        @else bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300 @endif">
                                                        @if($job->status === 'active')
                                                            ✅ Active
                                                        @elseif($job->status === 'draft')
                                                            📝 Draft
                                                        @else
                                                            ⏸️ {{ ucfirst($job->status) }}
                                                        @endif
                                                    </span>
                                                    <div class="flex space-x-1">
                                                        <a href="{{ route('marketplace.jobs.show', $job) }}" class="inline-flex items-center px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-xs font-medium rounded-md hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors">
                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                            </svg>
                                                            View
                                                        </a>
                                                        @if($job->applications->count() > 0)
                                                            <a href="{{ route('marketplace.jobs.applications', $job) }}" class="inline-flex items-center px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 text-xs font-medium rounded-md hover:bg-green-200 dark:hover:bg-green-900/50 transition-colors">
                                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                                </svg>
                                                                {{ $job->applications->count() }}
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <!-- Show More Jobs Link -->
                                @if($recentJobs->count() >= 3)
                                    <div class="mt-6 text-center">
                                        <a href="{{ route('marketplace.jobs.my-jobs') }}" class="inline-flex items-center text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                                            <span>View all your jobs</span>
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </a>
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-12">
                                    <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-10 h-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">No jobs posted yet</h4>
                                    <p class="text-gray-500 dark:text-gray-400 mb-4">Start by posting your first job to find the perfect talent</p>
                                    <a href="{{ route('marketplace.jobs.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Post your first job
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Recent Applications -->
                @if($user->isChatter())
                    <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200">
                        <div class="p-6 border-b border-gray-200 dark:border-zinc-700">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Recent <span class="text-transparent bg-clip-text bg-gradient-to-r from-green-600 to-emerald-600">Applications</span></h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Your latest job applications</p>
                                    </div>
                                </div>
                                <a href="{{ route('marketplace.jobs.index') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white text-sm font-semibold rounded-lg hover:from-green-700 hover:to-emerald-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    Find Jobs
                                </a>
                            </div>
                        </div>
                        <div class="p-6">
                            @if(($recentApplications ?? collect())->count() > 0)
                                <div class="space-y-4">
                                    @foreach($recentApplications ?? [] as $application)
                                        <div class="group p-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border border-green-200 dark:border-green-800 rounded-xl hover:shadow-md transition-all duration-200">
                                            <div class="flex items-center justify-between">
                                                <div class="flex-1">
                                                    <div class="flex items-center space-x-3 mb-2">
                                                        <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center">
                                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                            </svg>
                                                        </div>
                                                        <h4 class="font-bold text-gray-900 dark:text-gray-100 group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors">{{ $application->jobPost->title }}</h4>
                                                    </div>
                                                    <div class="flex items-center space-x-4 text-sm text-gray-600 dark:text-gray-400 mb-2">
                                                        <div class="flex items-center">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                            </svg>
                                                            <span>{{ $application->jobPost->user->name }}</span>
                                                        </div>
                                                        <div class="flex items-center">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                            <span class="capitalize">{{ str_replace('_', ' ', $application->jobPost->market) }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-500">
                                                        <div class="flex items-center">
                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                            <span>Applied {{ $application->created_at->diffForHumans() }}</span>
                                                        </div>
                                                        @if($application->jobPost->rate_type && $application->proposed_rate)
                                                            <div class="flex items-center">
                                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                                                </svg>
                                                                <span class="font-medium text-green-600 dark:text-green-400">
                                                                    @if($application->jobPost->rate_type === 'hourly')
                                                                        ${{ $application->proposed_rate }}/hr
                                                                    @elseif($application->jobPost->rate_type === 'fixed')
                                                                        ${{ $application->proposed_rate }}
                                                                    @else
                                                                        {{ $application->proposed_rate }}%
                                                                    @endif
                                                                </span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="ml-4 flex flex-col items-end space-y-2">
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                                                        @if($application->status == 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300
                                                        @elseif($application->status == 'accepted' || $application->status == 'hired') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300
                                                        @elseif($application->status == 'rejected') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300
                                                        @else bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300 @endif">
                                                        @if($application->status == 'pending')
                                                            ⏳ Pending
                                                        @elseif($application->status == 'accepted')
                                                            ✅ Accepted
                                                        @elseif($application->status == 'hired')
                                                            🎉 Hired
                                                        @elseif($application->status == 'rejected')
                                                            ❌ Rejected
                                                        @else
                                                            {{ ucfirst($application->status) }}
                                                        @endif
                                                    </span>
                                                    <a href="{{ route('marketplace.jobs.show', $application->jobPost) }}" class="inline-flex items-center px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 text-xs font-medium rounded-md hover:bg-green-200 dark:hover:bg-green-900/50 transition-colors">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                        View Job
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <!-- Show More Applications Link -->
                                @if(($recentApplications ?? collect())->count() >= 3)
                                    <div class="mt-6 text-center">
                                        <a href="{{ route('marketplace.applications.index') }}" class="inline-flex items-center text-sm font-medium text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 transition-colors">
                                            <span>View all your applications</span>
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </a>
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-12">
                                    <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-10 h-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">No applications yet</h4>
                                    <p class="text-gray-500 dark:text-gray-400 mb-4">Start by applying to jobs that match your skills</p>
                                    <a href="{{ route('marketplace.jobs.index') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold rounded-lg hover:from-green-700 hover:to-emerald-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                        Browse available jobs
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
                </div>
            </div>
        </div>

            <!-- Quick Actions -->
            <div class="mb-8">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Quick <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-pink-600">Actions</span></h2>
                    <p class="text-gray-600 dark:text-gray-400">Frequently used features and shortcuts</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    <a href="{{ route('marketplace.jobs.index') }}" class="group bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl p-5 shadow-sm hover:shadow-md hover:border-blue-300 dark:hover:border-blue-600 transition-all duration-200">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">Browse Jobs</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Find opportunities</p>
                            </div>
                        </div>
                    </a>
                    
                    <a href="{{ route('marketplace.profiles') }}" class="group bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl p-5 shadow-sm hover:shadow-md hover:border-green-300 dark:hover:border-green-600 transition-all duration-200">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100 group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors">Find Talent</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Browse profiles</p>
                            </div>
                        </div>
                    </a>
                    
                    <a href="{{ route('messages.index') }}" class="group bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl p-5 shadow-sm hover:shadow-md hover:border-purple-300 dark:hover:border-purple-600 transition-all duration-200">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">Messages</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Chat with contacts</p>
                            </div>
                        </div>
                    </a>
                    
                    <a href="{{ route('contracts.index') }}" class="group bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl p-5 shadow-sm hover:shadow-md hover:border-indigo-300 dark:hover:border-indigo-600 transition-all duration-200">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">Contracts</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Manage contracts</p>
                            </div>
                        </div>
                    </a>
                    
                    <a href="{{ route('profile.show') }}" class="group bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-xl p-5 shadow-sm hover:shadow-md hover:border-yellow-300 dark:hover:border-yellow-600 transition-all duration-200">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100 group-hover:text-yellow-600 dark:group-hover:text-yellow-400 transition-colors">Profile</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">View your profile</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
