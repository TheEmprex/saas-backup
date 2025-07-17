<x-layouts.marketing
    :seo="[
        'title'         => 'Dashboard - OnlyFans Management Marketplace',
        'description'   => 'Manage your jobs and applications.',
        'image'         => url('/og_image.png'),
        'type'          => 'website'
    ]"
>

<div class="bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Dashboard</h1>
            <p class="text-gray-600">Welcome back, {{ auth()->user()->name }}!</p>
        </div>
        
        <!-- Verification Status & Profile Completion Alerts -->
        <div class="mb-8 space-y-4">
            @if($verificationStatus['required'] && !$verificationStatus['verified'])
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-red-800">{{ $verificationStatus['title'] }}</h3>
                            <p class="text-sm text-red-700 mt-1">{{ $verificationStatus['description'] }}</p>
                        </div>
                        @if($verificationStatus['url'])
                        <div class="ml-auto">
                            <a href="{{ $verificationStatus['url'] }}" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors">
                                {{ $verificationStatus['button_text'] }}
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            @elseif($verificationStatus['required'] && $verificationStatus['status'] === 'pending')
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-yellow-800">{{ $verificationStatus['type'] === 'earnings' ? 'Earnings Verification Pending' : 'KYC Verification Pending' }}</h3>
                            <p class="text-sm text-yellow-700 mt-1">Your {{ $verificationStatus['type'] === 'earnings' ? 'earnings verification' : 'KYC verification' }} is being reviewed. You'll be notified once approved.</p>
                        </div>
                    </div>
                </div>
            @elseif($verificationStatus['required'] && $verificationStatus['status'] === 'rejected')
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-red-800">{{ $verificationStatus['type'] === 'earnings' ? 'Earnings Verification Rejected' : 'KYC Verification Rejected' }}</h3>
                            <p class="text-sm text-red-700 mt-1">{{ $verificationStatus['rejection_reason'] ?? 'Please resubmit with correct information.' }}</p>
                        </div>
                        @if($verificationStatus['url'])
                        <div class="ml-auto">
                            <a href="{{ $verificationStatus['url'] }}" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors">
                                {{ $verificationStatus['button_text'] }}
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            @elseif($verificationStatus['required'] && $verificationStatus['verified'])
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-green-800">{{ $verificationStatus['type'] === 'earnings' ? 'Earnings Verification Approved' : 'KYC Verification Approved' }}</h3>
                            <p class="text-sm text-green-700 mt-1">Your account is fully verified. You can now access all features.</p>
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Profile Completion -->
            @if($profileCompletion['percentage'] < 100)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <div>
                                <h3 class="text-sm font-medium text-blue-800">Complete Your Profile</h3>
                                <p class="text-sm text-blue-700 mt-1">{{ $profileCompletion['completed_fields'] }}/{{ $profileCompletion['total_fields'] }} fields completed ({{ $profileCompletion['percentage'] }}%)</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-32 bg-blue-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $profileCompletion['percentage'] }}%"></div>
                            </div>
                            <a href="{{ route('profile.edit') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                                Complete Profile
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6m0 0v6m0-6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2m8 0H8">
                        </svg>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-blue-600">Posted Jobs</p>
                        <p class="text-3xl font-bold text-blue-900">{{ $postedJobs->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </svg>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-green-600">My Applications</p>
                        <p class="text-3xl font-bold text-green-900">{{ $myApplications->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                        </svg>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-yellow-600">Pending Applications</p>
                        <p class="text-3xl font-bold text-yellow-900">{{ $pendingApplications }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-purple-50 border border-purple-200 rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z">
                        </svg>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-purple-600">Total Received</p>
                        <p class="text-3xl font-bold text-purple-900">{{ $totalApplicationsReceived }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- My Applications Status Boxes -->
        <div class="bg-white border border-gray-200 rounded-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">My Applications Status</h3>
            <div class="flex flex-wrap gap-4 sm:gap-6">
                <!-- Total Applications -->
                <div class="flex-1 min-w-0 sm:min-w-[200px] bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </svg>
                        </div>
                        <div class="ml-3 min-w-0">
                            <p class="text-xs font-medium text-blue-600 truncate">Total</p>
                            <p class="text-2xl font-bold text-blue-900">{{ $myApplications->count() }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Accepted Applications -->
                <div class="flex-1 min-w-0 sm:min-w-[200px] bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                            </svg>
                        </div>
                        <div class="ml-3 min-w-0">
                            <p class="text-xs font-medium text-green-600 truncate">Accepted</p>
                            <p class="text-2xl font-bold text-green-900">{{ $myApplications->where('status', 'accepted')->count() }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Pending Applications -->
                <div class="flex-1 min-w-0 sm:min-w-[200px] bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                            </svg>
                        </div>
                        <div class="ml-3 min-w-0">
                            <p class="text-xs font-medium text-yellow-600 truncate">Pending</p>
                            <p class="text-2xl font-bold text-yellow-900">{{ $myApplications->where('status', 'pending')->count() }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Rejected Applications -->
                <div class="flex-1 min-w-0 sm:min-w-[200px] bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z">
                            </svg>
                        </div>
                        <div class="ml-3 min-w-0">
                            <p class="text-xs font-medium text-red-600 truncate">Rejected</p>
                            <p class="text-2xl font-bold text-red-900">{{ $myApplications->where('status', 'rejected')->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Posted Jobs -->
            <div class="bg-white border border-gray-200 rounded-lg p-6">
            <div class="flex flex-col items-center mb-6 gap-4">
                <div class="flex flex-wrap gap-2 justify-center">
                    <a href="{{ route('marketplace.jobs.create') }}" class="inline-flex items-center justify-center bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors" onclick="console.log('Button clicked, going to:', this.href); return true;">
                        <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Post New Job
                    </a>
                    <a href="{{ route('profile.show') }}" class="inline-flex items-center justify-center bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                        <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        View Profile
                    </a>
                    <a href="{{ route('messages.web.index') }}" class="inline-flex items-center justify-center bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 transition-colors">
                        <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        Messages
                    </a>
                </div>
                <h2 class="text-xl font-semibold text-gray-900">My Posted Jobs</h2>
            </div>

                <div class="space-y-4">
                    @forelse($postedJobs as $job)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-900">{{ $job->title }}</h3>
                                <p class="text-sm text-gray-600 mt-1">{{ Str::limit($job->description, 100) }}</p>
                                <div class="flex items-center mt-2 text-xs text-gray-500">
                                    <span>{{ $job->current_applications }}/{{ $job->max_applications }} applications</span>
                                    <span class="mx-2">•</span>
                                    <span>{{ $job->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <div class="ml-4 flex space-x-2">
                                <a href="{{ route('jobs.show', $job->id) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                    View
                                </a>
                                <a href="{{ route('jobs.applications', $job->id) }}" class="text-green-600 hover:text-green-800 text-sm">
                                    Applications
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <p class="text-gray-500">You haven't posted any jobs yet.</p>
                        <a href="{{ route('marketplace.jobs.create') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                            Post your first job
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- My Applications -->
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">My Applications</h2>
                    <a href="{{ route('marketplace.jobs') }}" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                        Browse Jobs
                    </a>
                </div>

                <div class="space-y-4">
                    @forelse($myApplications as $application)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-900">{{ $application->jobPost->title }}</h3>
                                <p class="text-sm text-gray-600 mt-1">{{ $application->jobPost->user->name }}</p>
                                <div class="flex items-center mt-2 text-xs text-gray-500">
                                    <span>Applied {{ $application->created_at->diffForHumans() }}</span>
                                    <span class="mx-2">•</span>
                                    <span>
                                        @if($application->jobPost->rate_type === 'hourly')
                                            ${{ $application->proposed_rate }}/hr
                                        @elseif($application->jobPost->rate_type === 'fixed')
                                            ${{ $application->proposed_rate }}
                                        @else
                                            {{ $application->proposed_rate }}%
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <div class="ml-4 flex items-center space-x-2">
                                @if($application->status === 'pending')
                                    <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Pending</span>
                                @elseif($application->status === 'accepted')
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Accepted</span>
                                @elseif($application->status === 'rejected')
                                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Rejected</span>
                                @endif
                                <a href="{{ route('jobs.show', $application->jobPost->id) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                    View Job
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <p class="text-gray-500">You haven't applied to any jobs yet.</p>
                        <a href="{{ route('marketplace.jobs') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">
                            Browse available jobs
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@if(auth()->user()->isAdmin() && $adminStats)
    <!-- Admin Verification Management Section -->
    <div class="bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-white mb-2">Admin - Verification Management</h2>
                <p class="text-gray-400">Manage user verifications and approvals</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                <!-- KYC Verifications -->
                <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                    <h3 class="text-lg font-semibold text-white mb-4">KYC Verifications</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-yellow-400">Pending:</span>
                            <span class="font-bold text-yellow-400">{{ $adminStats['kyc_pending'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-green-400">Approved:</span>
                            <span class="font-bold text-green-400">{{ $adminStats['kyc_approved'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-red-400">Rejected:</span>
                            <span class="font-bold text-red-400">{{ $adminStats['kyc_rejected'] }}</span>
                        </div>
                    </div>
                    <a href="{{ route('admin.kyc.index') }}" class="inline-block mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors">
                        Manage KYC
                    </a>
                </div>
                
                <!-- Earnings Verifications -->
                <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                    <h3 class="text-lg font-semibold text-white mb-4">Earnings Verifications</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-yellow-400">Pending:</span>
                            <span class="font-bold text-yellow-400">{{ $adminStats['earnings_pending'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-green-400">Approved:</span>
                            <span class="font-bold text-green-400">{{ $adminStats['earnings_approved'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-red-400">Rejected:</span>
                            <span class="font-bold text-red-400">{{ $adminStats['earnings_rejected'] }}</span>
                        </div>
                    </div>
                    <a href="{{ route('admin.earnings.index') }}" class="inline-block mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors">
                        Manage Earnings
                    </a>
                </div>
                
                <!-- Quick Actions -->
                <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
                    <h3 class="text-lg font-semibold text-white mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        @if($adminStats['kyc_pending'] > 0)
                            <a href="{{ route('admin.kyc.index', ['status' => 'pending']) }}" class="block text-yellow-400 hover:text-yellow-300 text-sm">
                                Review {{ $adminStats['kyc_pending'] }} Pending KYC
                            </a>
                        @endif
                        @if($adminStats['earnings_pending'] > 0)
                            <a href="{{ route('admin.earnings.index', ['status' => 'pending']) }}" class="block text-yellow-400 hover:text-yellow-300 text-sm">
                                Review {{ $adminStats['earnings_pending'] }} Pending Earnings
                            </a>
                        @endif
                        <a href="{{ route('admin.dashboard') }}" class="block text-blue-400 hover:text-blue-300 text-sm">
                            Full Admin Dashboard
                        </a>
                        <a href="{{ route('filament.admin.pages.dashboard') }}" class="block text-gray-400 hover:text-gray-300 text-sm">
                            Filament Admin Panel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@if(auth()->user()->isAdmin())
<!-- Discreet admin notification -->
<div class="fixed bottom-4 right-4 bg-gray-800 text-white px-3 py-2 rounded-lg shadow-lg text-xs opacity-75 hover:opacity-100 transition-opacity">
    <div class="flex items-center space-x-2">
        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
        </svg>
        <span>Admin: Press Ctrl+Shift+A or visit /system/admin-access</span>
    </div>
</div>
@endif

</x-layouts.marketing>
