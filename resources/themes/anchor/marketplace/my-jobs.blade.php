@extends('theme::layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">My Jobs</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-300">Manage and track all your job postings</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                        <x-phosphor-briefcase class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Jobs</h3>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $jobs->total() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                        <x-phosphor-check-circle class="w-6 h-6 text-green-600 dark:text-green-400" />
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Jobs</h3>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $jobs->where('status', 'active')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900">
                        <x-phosphor-file-text class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Applications</h3>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $jobs->sum('current_applications') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900">
                        <x-phosphor-eye class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Views</h3>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">-</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jobs List -->
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Your Job Postings</h2>
                <a href="{{ route('marketplace.jobs.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <x-phosphor-plus class="w-4 h-4 mr-2" />
                    Post New Job
                </a>
            </div>

            @if($jobs->count() > 0)
                <!-- Jobs Grid -->
                <div class="grid gap-6">
                    @foreach($jobs as $job)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow">
                            <div class="p-6">
                                <!-- Job Header -->
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                <a href="{{ route('marketplace.jobs.show', $job) }}" class="hover:text-blue-600 transition-colors">
                                                    {{ $job->title ?: 'Untitled Job' }}
                                                </a>
                                            </h3>
                                            
                                            <!-- Status Badge -->
                                            @if($job->status === 'active')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                    ✅ Active
                                                </span>
                                            @elseif($job->status === 'paused')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                    ⏸️ Paused
                                                </span>
                                            @elseif($job->status === 'closed')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                                    🔒 Closed
                                                </span>
                                            @endif

                                            @if($job->is_featured)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                                    ⭐ Featured
                                                </span>
                                            @endif

                                            @if($job->is_urgent)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                    🚨 Urgent
                                                </span>
                                            @endif
                                        </div>
                                        
                                        <!-- Job Description -->
                                        <p class="text-gray-600 dark:text-gray-300 text-sm mb-4 line-clamp-2">
                                            {{ $job->description ? Str::limit($job->description, 120) : 'No description provided' }}
                                        </p>
                                    </div>
                                </div>

                <!-- Job Details Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <!-- Market -->
                    <div class="flex flex-col items-center justify-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg h-20">
                        <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium mb-1">Market</div>
                        <div class="text-sm font-semibold text-gray-900 dark:text-white text-center">
                            {{ ucfirst($job->market) }}
                        </div>
                    </div>
                    
                    <!-- Posted Date -->
                    <div class="flex flex-col items-center justify-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg h-20">
                        <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium mb-1">Posted</div>
                        <div class="text-sm font-semibold text-gray-900 dark:text-white text-center">
                            {{ $job->created_at->diffForHumans() }}
                        </div>
                    </div>
                    
                    <!-- Rate -->
                    <div class="flex flex-col items-center justify-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg h-20">
                        <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium mb-1">Rate</div>
                        <div class="text-sm font-semibold text-green-600 dark:text-green-400 text-center">
                            @if($job->rate_type === 'hourly' && $job->hourly_rate)
                                ${{ number_format($job->hourly_rate, 2) }}/hr
                            @elseif($job->rate_type === 'fixed' && $job->fixed_rate)
                                ${{ number_format($job->fixed_rate, 2) }}
                            @elseif($job->rate_type === 'commission' && $job->commission_percentage)
                                {{ $job->commission_percentage }}%
                            @else
                                ${{ number_format($job->hourly_rate ?: $job->fixed_rate ?: 0, 2) }}
                            @endif
                        </div>
                    </div>
                    
                    <!-- Applications -->
                    <div class="flex flex-col items-center justify-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg h-20">
                        <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium mb-1">Applications</div>
                        <div class="text-sm font-semibold text-blue-600 dark:text-blue-400 text-center">
                            {{ $job->current_applications ?? 0 }}
                        </div>
                    </div>
                </div>

                <!-- Additional Info -->
                <div class="flex flex-wrap items-center justify-between gap-4 mb-6 text-sm">
                    <div class="flex items-center text-gray-600 dark:text-gray-300">
                        <x-phosphor-eye class="w-4 h-4 mr-2 flex-shrink-0" />
                        <span>{{ $job->views ?? 0 }} views</span>
                    </div>
                    <div class="flex items-center text-gray-600 dark:text-gray-300">
                        <x-phosphor-calendar class="w-4 h-4 mr-2 flex-shrink-0" />
                        <span>Expires {{ $job->expires_at->diffForHumans() }}</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('marketplace.jobs.show', $job) }}" 
                       class="inline-flex items-center justify-center px-3 py-2 bg-blue-50 hover:bg-blue-100 text-blue-600 text-xs font-medium rounded-md transition-colors min-w-[80px]">
                        <x-phosphor-eye class="w-3 h-3 mr-1 flex-shrink-0" />
                        <span>View</span>
                    </a>
                    
                    @if(($job->current_applications ?? 0) > 0)
                        <a href="{{ route('marketplace.jobs.applications', $job) }}" 
                           class="inline-flex items-center justify-center px-3 py-2 bg-green-50 hover:bg-green-100 text-green-600 text-xs font-medium rounded-md transition-colors min-w-[80px]">
                            <x-phosphor-file-text class="w-3 h-3 mr-1 flex-shrink-0" />
                            <span>Apps ({{ $job->current_applications ?? 0 }})</span>
                        </a>
                    @endif
                    
                    <a href="{{ route('marketplace.jobs.edit', $job) }}" 
                       class="inline-flex items-center justify-center px-3 py-2 bg-gray-50 hover:bg-gray-100 text-gray-600 text-xs font-medium rounded-md transition-colors min-w-[80px]">
                        <x-phosphor-pencil class="w-3 h-3 mr-1 flex-shrink-0" />
                        <span>Edit</span>
                    </a>
                    
                    @if(!$job->is_featured)
                        <button class="inline-flex items-center justify-center px-3 py-2 bg-purple-50 hover:bg-purple-100 text-purple-600 text-xs font-medium rounded-md transition-colors min-w-[100px]">
                            <x-phosphor-star class="w-3 h-3 mr-1 flex-shrink-0" />
                            <span>Feature +$10</span>
                        </button>
                    @endif
                    
                    <form method="POST" action="{{ route('marketplace.jobs.delete', $job) }}" class="inline-block" 
                          onsubmit="return confirm('Are you sure you want to delete this job?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="inline-flex items-center justify-center px-3 py-2 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-medium rounded-md transition-colors min-w-[80px]">
                            <x-phosphor-trash class="w-3 h-3 mr-1 flex-shrink-0" />
                            <span>Delete</span>
                        </button>
                    </form>
                    
                    @if(!$job->is_urgent)
                        <button class="inline-flex items-center justify-center px-3 py-2 bg-orange-50 hover:bg-orange-100 text-orange-600 text-xs font-medium rounded-md transition-colors min-w-[120px]">
                            <x-phosphor-warning class="w-3 h-3 mr-1 flex-shrink-0" />
                            <span>Make Urgent (+$5)</span>
                        </button>
                    @endif
                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700">
                    {{ $jobs->links() }}
                </div>
            @else
                <div class="p-12 text-center">
                    <x-phosphor-briefcase class="w-12 h-12 text-gray-400 mx-auto mb-4" />
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No jobs posted yet</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">Start by posting your first job to find talented candidates.</p>
                    <a href="{{ route('marketplace.jobs.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium">
                        Post Your First Job
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
