@extends('theme::app')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Job Details</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Review and manage job posting</p>
                </div>
                <a href="{{ route('admin.jobs.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                    Back to Jobs
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Job Information -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $job->title }}</h3>
                            <span class="inline-block px-3 py-1 text-sm rounded-full 
                                {{ $job->status === 'active' ? 'bg-green-100 text-green-800' : 
                                   ($job->status === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($job->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Budget</label>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($job->budget) }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                                <p class="text-gray-900 dark:text-white">{{ $job->category ?? 'Not specified' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Location</label>
                                <p class="text-gray-900 dark:text-white">{{ $job->location ?? 'Remote' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Posted</label>
                                <p class="text-gray-900 dark:text-white">{{ $job->created_at->format('M d, Y H:i') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Duration</label>
                                <p class="text-gray-900 dark:text-white">{{ $job->duration ?? 'Not specified' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Experience Level</label>
                                <p class="text-gray-900 dark:text-white">{{ ucfirst($job->experience_level ?? 'Any') }}</p>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <p class="text-gray-900 dark:text-white whitespace-pre-wrap">{{ $job->description }}</p>
                            </div>
                        </div>

                        @if($job->requirements)
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Requirements</label>
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <p class="text-gray-900 dark:text-white whitespace-pre-wrap">{{ $job->requirements }}</p>
                                </div>
                            </div>
                        @endif

                        @if($job->skills && is_array($job->skills))
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Skills Required</label>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($job->skills as $skill)
                                        <span class="inline-block bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full">
                                            {{ $skill }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Job Owner -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Job Owner</h3>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center">
                            <img src="{{ $job->user->avatar() }}" alt="{{ $job->user->name }}" class="w-12 h-12 rounded-full mr-4">
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white">{{ $job->user->name }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $job->user->email }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-500">{{ $job->user->userType->name ?? 'No type' }}</p>
                            </div>
                            <div class="ml-auto">
                                <a href="{{ route('admin.users.show', $job->user) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    View Profile
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Applications -->
                @if($job->applications->count() > 0)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                Applications ({{ $job->applications->count() }})
                            </h3>
                        </div>
                        <div class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($job->applications as $application)
                                <div class="p-6">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <img src="{{ $application->user->avatar() }}" alt="{{ $application->user->name }}" class="w-10 h-10 rounded-full mr-3">
                                            <div>
                                                <h4 class="font-medium text-gray-900 dark:text-white">{{ $application->user->name }}</h4>
                                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $application->user->email }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="inline-block px-2 py-1 text-xs rounded-full 
                                                {{ $application->status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                                   ($application->status === 'accepted' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                                {{ ucfirst($application->status) }}
                                            </span>
                                            <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">{{ $application->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    @if($application->cover_letter)
                                        <div class="mt-4 bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                                            <p class="text-sm text-gray-900 dark:text-white">{{ $application->cover_letter }}</p>
                                        </div>
                                    @endif
                                    @if($application->proposed_rate)
                                        <div class="mt-2">
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Proposed Rate: </span>
                                            <span class="text-sm text-gray-900 dark:text-white">${{ number_format($application->proposed_rate) }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                        <div class="p-6 text-center">
                            <p class="text-gray-500 dark:text-gray-400">No applications yet</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div>
                <!-- Quick Actions -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Actions</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <a href="{{ route('marketplace.jobs.show', $job) }}" target="_blank" 
                           class="w-full inline-block text-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg">
                            View Public Job
                        </a>
                        
                        <form method="POST" action="{{ route('admin.jobs.delete', $job) }}" 
                              onsubmit="return confirm('Are you sure you want to delete this job? This action cannot be undone.')" class="w-full">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg">
                                Delete Job
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Job Stats -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Statistics</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Total Applications</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $job->applications->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Pending Applications</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $job->applications->where('status', 'pending')->count() }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Accepted Applications</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $job->applications->where('status', 'accepted')->count() }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Days Active</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $job->created_at->diffInDays(now()) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Job Timeline -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Timeline</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">Job Posted</p>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ $job->created_at->format('M d, Y H:i') }}</p>
                                </div>
                            </div>
                            @if($job->applications->count() > 0)
                                <div class="flex items-center">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">First Application</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">
                                            {{ $job->applications->sortBy('created_at')->first()->created_at->format('M d, Y H:i') }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                            @if($job->applications->where('status', 'accepted')->count() > 0)
                                <div class="flex items-center">
                                    <div class="w-2 h-2 bg-purple-500 rounded-full mr-3"></div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">Application Accepted</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">
                                            {{ $job->applications->where('status', 'accepted')->sortBy('updated_at')->first()->updated_at->format('M d, Y H:i') }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                            @if($job->status === 'completed')
                                <div class="flex items-center">
                                    <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">Job Completed</p>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">{{ $job->updated_at->format('M d, Y H:i') }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
