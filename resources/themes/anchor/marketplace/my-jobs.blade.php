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
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $jobs->sum('applications_count') }}</p>
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
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Your Jobs</h2>
                    <a href="{{ route('marketplace.jobs.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                        <x-phosphor-plus class="w-4 h-4 inline mr-1" />
                        Post New Job
                    </a>
                </div>
            </div>

            @if($jobs->count() > 0)
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($jobs as $job)
                        <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white truncate">
                                            <a href="{{ route('marketplace.jobs.show', $job) }}" class="hover:text-blue-600">
                                                {{ $job->title }}
                                            </a>
                                        </h3>
                                        
                                        @if($job->status === 'active')
                                            <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-full">
                                                Active
                                            </span>
                                        @elseif($job->status === 'paused')
                                            <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 rounded-full">
                                                Paused
                                            </span>
                                        @elseif($job->status === 'closed')
                                            <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 rounded-full">
                                                Closed
                                            </span>
                                        @endif

                                        @if($job->is_featured)
                                            <span class="px-2 py-1 text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 rounded-full">
                                                Featured
                                            </span>
                                        @endif

                                        @if($job->is_urgent)
                                            <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 rounded-full">
                                                Urgent
                                            </span>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-6 text-sm text-gray-500 dark:text-gray-400 mb-3">
                                        <div class="flex items-center">
                                            <x-phosphor-map-pin class="w-4 h-4 mr-1" />
                                            {{ ucfirst(str_replace('_', ' ', $job->market)) }}
                                        </div>
                                        <div class="flex items-center">
                                            <x-phosphor-clock class="w-4 h-4 mr-1" />
                                            {{ $job->created_at->diffForHumans() }}
                                        </div>
                                        <div class="flex items-center">
                                            <x-phosphor-calendar class="w-4 h-4 mr-1" />
                                            Expires {{ $job->expires_at->diffForHumans() }}
                                        </div>
                                    </div>

                                    <p class="text-gray-600 dark:text-gray-300 text-sm line-clamp-2 mb-3">
                                        {{ Str::limit($job->description, 150) }}
                                    </p>

                                    <div class="flex items-center gap-6 text-sm">
                                        <div class="flex items-center text-green-600 dark:text-green-400">
                                            <x-phosphor-currency-dollar class="w-4 h-4 mr-1" />
                                            @if($job->rate_type === 'hourly')
                                                ${{ number_format($job->hourly_rate, 2) }}/hour
                                            @else
                                                ${{ number_format($job->fixed_rate, 2) }} fixed
                                            @endif
                                        </div>
                                        <div class="flex items-center text-blue-600 dark:text-blue-400">
                                            <x-phosphor-file-text class="w-4 h-4 mr-1" />
                                            {{ $job->applications->count() }} applications
                                        </div>
                                        <div class="flex items-center text-gray-500 dark:text-gray-400">
                                            <x-phosphor-chart-bar class="w-4 h-4 mr-1" />
                                            {{ ucfirst($job->experience_level) }}
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 ml-4">
                                    <a href="{{ route('marketplace.jobs.show', $job) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                        View
                                    </a>
                                    @if($job->applications->count() > 0)
                                        <a href="{{ route('marketplace.jobs.applications', $job) }}" class="text-green-600 hover:text-green-700 text-sm font-medium">
                                            Applications ({{ $job->applications->count() }})
                                        </a>
                                    @endif
                                    <a href="{{ route('marketplace.jobs.edit', $job) }}" class="text-gray-600 hover:text-gray-700 text-sm font-medium">
                                        Edit
                                    </a>
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
