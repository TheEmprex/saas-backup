@extends('theme::layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">My Applications</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-300">Track all your job applications and their status</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                        <x-phosphor-file-text class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Applications</h3>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $applications->total() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900">
                        <x-phosphor-clock class="w-6 h-6 text-yellow-600 dark:text-yellow-400" />
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending</h3>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $applications->where('status', 'pending')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                        <x-phosphor-check-circle class="w-6 h-6 text-green-600 dark:text-green-400" />
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Accepted</h3>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $applications->where('status', 'accepted')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 dark:bg-red-900">
                        <x-phosphor-x-circle class="w-6 h-6 text-red-600 dark:text-red-400" />
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Rejected</h3>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $applications->where('status', 'rejected')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Applications List -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Your Applications</h2>
            </div>

            @if($applications->count() > 0)
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($applications as $application)
                        <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white truncate">
                                            <a href="{{ route('marketplace.jobs.show', $application->jobPost) }}" class="hover:text-blue-600">
                                                {{ $application->jobPost->title }}
                                            </a>
                                        </h3>
                                        
                                        @if($application->status === 'pending')
                                            <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 rounded-full">
                                                Pending
                                            </span>
                                        @elseif($application->status === 'accepted' || $application->status === 'hired')
                                            <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-full">
                                                {{ ucfirst($application->status) }}
                                            </span>
                                        @elseif($application->status === 'rejected')
                                            <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 rounded-full">
                                                Rejected
                                            </span>
                                        @elseif($application->status === 'withdrawn')
                                            <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 rounded-full">
                                                Withdrawn
                                            </span>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-6 text-sm text-gray-500 dark:text-gray-400 mb-3">
                                        <div class="flex items-center">
                                            <x-phosphor-buildings class="w-4 h-4 mr-1" />
                                            {{ $application->jobPost->user->name }}
                                        </div>
                                        <div class="flex items-center">
                                            <x-phosphor-map-pin class="w-4 h-4 mr-1" />
                                            {{ ucfirst(str_replace('_', ' ', $application->jobPost->market)) }}
                                        </div>
                                        <div class="flex items-center">
                                            <x-phosphor-clock class="w-4 h-4 mr-1" />
                                            Applied {{ $application->created_at->diffForHumans() }}
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-6 text-sm mb-3">
                                        <div class="flex items-center text-green-600 dark:text-green-400">
                                            <x-phosphor-currency-dollar class="w-4 h-4 mr-1" />
                                            @if($application->jobPost->rate_type === 'hourly')
                                                ${{ number_format($application->jobPost->hourly_rate, 2) }}/hour
                                            @else
                                                ${{ number_format($application->jobPost->fixed_rate, 2) }} fixed
                                            @endif
                                        </div>
                                        <div class="flex items-center text-gray-500 dark:text-gray-400">
                                            <x-phosphor-chart-bar class="w-4 h-4 mr-1" />
                                            {{ ucfirst($application->jobPost->experience_level) }}
                                        </div>
                                        <div class="flex items-center text-blue-600 dark:text-blue-400">
                                            <x-phosphor-calendar class="w-4 h-4 mr-1" />
                                            {{ ucfirst($application->jobPost->contract_type) }}
                                        </div>
                                    </div>

                                    @if($application->cover_letter)
                                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 mb-3">
                                            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Your Cover Letter:</h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-300">{{ Str::limit($application->cover_letter, 200) }}</p>
                                        </div>
                                    @endif

                                    @if($application->proposed_rate)
                                        <div class="text-sm text-gray-600 dark:text-gray-300">
                                            <span class="font-medium">Your proposed rate:</span> 
                                            ${{ number_format($application->proposed_rate, 2) }}@if($application->jobPost->rate_type === 'hourly')/hour @endif
                                        </div>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 ml-4">
                                    <a href="{{ route('marketplace.jobs.show', $application->jobPost) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                        View Job
                                    </a>
                                    @if($application->status === 'pending')
                                        <button class="text-red-600 hover:text-red-700 text-sm font-medium">
                                            Withdraw
                                        </button>
                                    @endif
                                    @if($application->jobPost->user_id !== auth()->id())
                                        <a href="{{ route('messages.create', $application->jobPost->user) }}?job_id={{ $application->jobPost->id }}" class="text-green-600 hover:text-green-700 text-sm font-medium">
                                            Message
                                        </a>
                                    @endif
                                </div>
                            </div>

                            @if($application->status === 'accepted' && $application->jobPost->status === 'active')
                                <div class="mt-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                                    <div class="flex items-center">
                                        <x-phosphor-check-circle class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" />
                                        <span class="text-sm font-medium text-green-800 dark:text-green-200">
                                            Congratulations! Your application has been accepted. The employer may contact you soon.
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700">
                    {{ $applications->links() }}
                </div>
            @else
                <div class="p-12 text-center">
                    <x-phosphor-file-text class="w-12 h-12 text-gray-400 mx-auto mb-4" />
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No applications yet</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">Start applying to jobs to track your applications here.</p>
                    <a href="{{ route('marketplace.jobs') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium">
                        Browse Jobs
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
