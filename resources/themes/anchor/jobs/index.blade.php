<x-layouts.app>

<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
        <div>
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">
                My 
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">Jobs</span>
            </h1>
            <p class="text-gray-600 dark:text-gray-300">Track and manage your job postings</p>
        </div>
        <div class="flex space-x-3 mt-4 sm:mt-0">
            <a href="{{ route('marketplace.jobs.create') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-lg hover:shadow-xl">
                Post New Job
            </a>
        </div>
    </div>

    @if($jobs->count() > 0)
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($jobs as $job)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                                <a href="{{ route('jobs.show', $job->id) }}" class="hover:text-blue-600">
                                    {{ $job->title }}
                                </a>
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 mb-2">{{ Str::limit($job->description, 150) }}</p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                @if($job->status === 'active') bg-green-100 text-green-800 
                                @elseif($job->status === 'closed') bg-red-100 text-red-800 
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($job->status) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Market</span>
                            <p class="font-medium text-gray-900 dark:text-white">{{ ucfirst($job->market) }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Experience</span>
                            <p class="font-medium text-gray-900 dark:text-white">{{ ucfirst($job->experience_level) }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Applications</span>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $job->current_applications }} / {{ $job->max_applications }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">Posted</span>
                            <p class="font-medium text-gray-900 dark:text-white">{{ $job->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="flex space-x-2">
                            @if($job->is_featured)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Featured
                                </span>
                            @endif
                            @if($job->is_urgent)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Urgent
                                </span>
                            @endif
                        </div>
                        <div class="flex space-x-2">
                            @if($job->current_applications > 0)
                                <a href="{{ route('jobs.applications', $job->id) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                    View Applications
                                </a>
                            @endif
                            <a href="{{ route('jobs.edit', $job->id) }}" class="text-gray-600 hover:text-gray-800 text-sm">
                                Edit
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-8">
            {{ $jobs->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <div class="mb-4">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6.99A23.931 23.931 0 0120 15" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No jobs posted yet</h3>
            <p class="text-gray-600 dark:text-gray-300 mb-4">You haven't posted any jobs yet. Start by posting your first job!</p>
            <a href="{{ route('marketplace.jobs.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                Post Your First Job
            </a>
        </div>
    @endif
</div>
</x-layouts.app>
