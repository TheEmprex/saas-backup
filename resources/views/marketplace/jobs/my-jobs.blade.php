<x-theme::layouts.app>
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="container mx-auto px-6 py-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">
                            My 
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">Jobs</span>
                        </h1>
                        <p class="mt-2 text-lg text-gray-600 dark:text-gray-300">Manage and track all your job postings</p>
                    </div>
                    <a href="{{ route('marketplace.jobs.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Post New Job
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-200">
                    <div class="flex items-center">
                        <div class="p-3 rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6.294a2 2 0 01-.78 1.63l-1.473 1.105A2 2 0 0112 16.5v-2.294A2 2 0 0111.22 12.5L9.747 11.395A2 2 0 019 10.106V4a2 2 0 012-2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Jobs</h3>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $jobs->total() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-200">
                    <div class="flex items-center">
                        <div class="p-3 rounded-xl bg-gradient-to-r from-green-500 to-green-600 text-white shadow-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Jobs</h3>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $jobs->where('status', 'active')->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-200">
                    <div class="flex items-center">
                        <div class="p-3 rounded-xl bg-gradient-to-r from-yellow-500 to-orange-500 text-white shadow-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Applications</h3>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $jobs->sum('applications_count') }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-200">
                    <div class="flex items-center">
                        <div class="p-3 rounded-xl bg-gradient-to-r from-purple-500 to-purple-600 text-white shadow-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Views</h3>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $jobs->sum('views') ?: '0' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Jobs Grid -->
            @if($jobs->count() > 0)
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
                    @foreach($jobs as $job)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-200 overflow-hidden">
                            <!-- Job Header -->
                            <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 line-clamp-2">
                                            <a href="{{ route('marketplace.jobs.show', $job) }}" class="hover:text-blue-600 transition-colors">
                                                {{ $job->title }}
                                            </a>
                                        </h3>
                                        <div class="flex flex-wrap items-center gap-2">
                                            @if($job->status === 'active')
                                                <span class="px-3 py-1 text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-full">
                                                    ✅ Active
                                                </span>
                                            @elseif($job->status === 'paused')
                                                <span class="px-3 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 rounded-full">
                                                    ⏸️ Paused
                                                </span>
                                            @elseif($job->status === 'closed')
                                                <span class="px-3 py-1 text-xs font-semibold bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 rounded-full">
                                                    🔒 Closed
                                                </span>
                                            @endif

                                            @if($job->is_featured)
                                                <span class="px-3 py-1 text-xs font-semibold bg-gradient-to-r from-purple-100 to-pink-100 text-purple-800 dark:from-purple-900 dark:to-pink-900 dark:text-purple-200 rounded-full">
                                                    ⭐ Featured
                                                </span>
                                            @endif

                                            @if($job->is_urgent)
                                                <span class="px-3 py-1 text-xs font-semibold bg-gradient-to-r from-red-100 to-orange-100 text-red-800 dark:from-red-900 dark:to-orange-900 dark:text-red-200 rounded-full">
                                                    🚨 Urgent
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <p class="text-gray-600 dark:text-gray-300 text-sm line-clamp-3 mb-4">
                                    {{ Str::limit($job->description, 120) }}
                                </p>

                                <!-- Job Info Grid -->
                                <div class="grid grid-cols-2 gap-3 text-sm">
                                    <div class="flex items-center text-gray-500 dark:text-gray-400">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        {{ ucfirst(str_replace('_', ' ', $job->market)) }}
                                    </div>
                                    <div class="flex items-center text-gray-500 dark:text-gray-400">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $job->created_at->diffForHumans() }}
                                    </div>
                                    <div class="flex items-center text-green-600 dark:text-green-400 font-semibold">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                        @if($job->rate_type === 'hourly')
                                            ${{ number_format($job->hourly_rate, 2) }}/hr
                                        @elseif($job->rate_type === 'fixed')
                                            ${{ number_format($job->fixed_rate, 2) }}
                                        @else
                                            {{ $job->commission_percentage }}%
                                        @endif
                                    </div>
                                    <div class="flex items-center text-blue-600 dark:text-blue-400">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        {{ $job->applications->count() }} applications
                                    </div>
                                    <div class="flex items-center text-purple-600 dark:text-purple-400">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        {{ number_format($job->views ?? 0) }} views
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="p-6 bg-gray-50 dark:bg-gray-700/50">
                                <div class="grid grid-cols-4 gap-2">
                                    <a href="{{ route('marketplace.jobs.show', $job) }}" class="inline-flex items-center justify-center px-3 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 616 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View
                                    </a>
                                    <a href="{{ route('marketplace.jobs.applications', $job) }}" class="inline-flex items-center justify-center px-3 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-colors {{ $job->applications->count() === 0 ? 'opacity-50 cursor-not-allowed' : '' }}">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 515.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 919.288 0M15 7a3 3 0 11-6 0 3 3 0 616 0zm6 3a2 2 0 11-4 0 2 2 0 414 0zM7 10a2 2 0 11-4 0 2 2 0 414 0z"></path>
                                        </svg>
                                        Apps ({{ $job->applications->count() }})
                                    </a>
                                    <a href="{{ route('marketplace.jobs.edit', $job) }}" class="inline-flex items-center justify-center px-3 py-2 bg-gray-600 text-white text-sm font-semibold rounded-lg hover:bg-gray-700 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit
                                    </a>
                                    @if(!$job->is_featured)
                                        <button onclick="promoteJob({{ $job->id }}, 'featured', '{{ addslashes($job->title) }}')" class="inline-flex items-center justify-center px-3 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white text-sm font-semibold rounded-lg hover:from-purple-700 hover:to-pink-700 transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                            </svg>
                                            Feature +$10
                                        </button>
                                    @else
                                        <button onclick="confirmDelete({{ $job->id }}, '{{ addslashes($job->title) }}')" class="inline-flex items-center justify-center px-3 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Delete
                                        </button>
                                    @endif
                                </div>
                                
                                <!-- Secondary Row - Delete button when not featured -->
                                @if(!$job->is_featured)
                                    <div class="mt-2">
                                        <button onclick="confirmDelete({{ $job->id }}, '{{ addslashes($job->title) }}')" class="w-full inline-flex items-center justify-center px-3 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Delete Job
                                        </button>
                                    </div>
                                @endif
                                
                                <!-- Secondary Actions -->
                                @if(!$job->is_urgent)
                                    <div class="mt-2">
                                        <button onclick="promoteJob({{ $job->id }}, 'urgent', '{{ addslashes($job->title) }}')" class="w-full inline-flex items-center justify-center px-3 py-2 bg-gradient-to-r from-red-600 to-orange-600 text-white text-sm font-semibold rounded-lg hover:from-red-700 hover:to-orange-700 transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                            </svg>
                                            Make Urgent (+$5)
                                        </button>
                                    </div>
                                @endif
                                
                                <!-- Expires info -->
                                <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 4V7"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Expires {{ $job->expires_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="flex justify-center">
                    {{ $jobs->links() }}
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-12 text-center">
                    <div class="w-24 h-24 bg-gradient-to-r from-blue-100 to-purple-100 dark:from-blue-900/30 dark:to-purple-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-12 h-12 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6.294a2 2 0 01-.78 1.63l-1.473 1.105A2 2 0 0112 16.5v-2.294A2 2 0 0111.22 12.5L9.747 11.395A2 2 0 019 10.106V4a2 2 0 012-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">No jobs posted yet</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto">Ready to find your next superstar? Post your first job and connect with talented chatters who can help grow your OnlyFans business.</p>
                    <a href="{{ route('marketplace.jobs.create') }}" class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Post Your First Job
                    </a>
                </div>
            @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden z-50" onclick="closeDeleteModal()">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full p-6" onclick="event.stopPropagation()">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Delete Job</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">This action cannot be undone</p>
                </div>
            </div>
            
            <p class="text-gray-700 dark:text-gray-300 mb-6">
                Are you sure you want to delete "<span id="jobTitleToDelete" class="font-semibold"></span>"? This will permanently remove the job and all associated applications.
            </p>
            
            <div class="flex gap-3">
                <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-semibold hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors">
                    Cancel
                </button>
                <button onclick="submitDelete()" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition-colors">
                    Delete Job
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Promotion Confirmation Modal -->
<div id="promoteModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden z-50" onclick="closePromoteModal()">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full p-6" onclick="event.stopPropagation()">
            <div class="flex items-center mb-6">
                <div id="promoteIconContainer" class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center mr-4">
                    <svg id="promoteIcon" class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                </div>
                <div>
                    <h3 id="promoteTitle" class="text-xl font-bold text-gray-900 dark:text-white">Promote Job</h3>
                    <p id="promoteSubtitle" class="text-sm text-gray-500 dark:text-gray-400">Boost your job visibility</p>
                </div>
            </div>
            
            <p class="text-gray-700 dark:text-gray-300 mb-6">
                Are you sure you want to make "<span id="jobTitleToPromote" class="font-semibold"></span>" <span id="promotionTypeText"></span>?
                <br><br>
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    Cost: <span id="promotionCost" class="font-semibold text-green-600 dark:text-green-400"></span>
                </span>
            </p>
            
            <div class="flex gap-3">
                <button onclick="closePromoteModal()" class="flex-1 px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-semibold hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors">
                    Cancel
                </button>
                <button onclick="submitPromotion()" class="flex-1 px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg font-semibold hover:from-purple-700 hover:to-pink-700 transition-colors">
                    <span id="promoteButtonText">Promote Job</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Delete Form -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<!-- Hidden Promote Form -->
<form id="promoteForm" method="POST" style="display: none;">
    @csrf
    @method('PATCH')
</form>

<script>
let jobIdToDelete = null;
let jobIdToPromote = null;
let promotionType = null;

function confirmDelete(jobId, jobTitle) {
    jobIdToDelete = jobId;
    document.getElementById('jobTitleToDelete').textContent = jobTitle;
    document.getElementById('deleteModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    jobIdToDelete = null;
}

function submitDelete() {
    if (jobIdToDelete) {
        const form = document.getElementById('deleteForm');
        form.action = `/marketplace/jobs/${jobIdToDelete}`;
        form.submit();
    }
}

function promoteJob(jobId, type, jobTitle) {
    jobIdToPromote = jobId;
    promotionType = type;
    
    document.getElementById('jobTitleToPromote').textContent = jobTitle;
    
    const iconContainer = document.getElementById('promoteIconContainer');
    const icon = document.getElementById('promoteIcon');
    const title = document.getElementById('promoteTitle');
    const subtitle = document.getElementById('promoteSubtitle');
    const typeText = document.getElementById('promotionTypeText');
    const cost = document.getElementById('promotionCost');
    const buttonText = document.getElementById('promoteButtonText');
    
    if (type === 'featured') {
        iconContainer.className = 'w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center mr-4';
        icon.className = 'w-6 h-6 text-purple-600 dark:text-purple-400';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>';
        title.textContent = 'Make Job Featured';
        subtitle.textContent = 'Boost visibility and attract more applicants';
        typeText.textContent = 'featured';
        cost.textContent = '$10.00';
        buttonText.textContent = 'Make Featured';
    } else if (type === 'urgent') {
        iconContainer.className = 'w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mr-4';
        icon.className = 'w-6 h-6 text-red-600 dark:text-red-400';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>';
        title.textContent = 'Make Job Urgent';
        subtitle.textContent = 'Show urgency and get faster responses';
        typeText.textContent = 'urgent';
        cost.textContent = '$5.00';
        buttonText.textContent = 'Make Urgent';
    }
    
    document.getElementById('promoteModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closePromoteModal() {
    document.getElementById('promoteModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    jobIdToPromote = null;
    promotionType = null;
}

function submitPromotion() {
    if (jobIdToPromote && promotionType) {
        const form = document.getElementById('promoteForm');
        form.action = `/marketplace/jobs/${jobIdToPromote}/promote`;
        
        // Add hidden input for promotion type
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'type';
        input.value = promotionType;
        form.appendChild(input);
        
        form.submit();
    }
}

// Close modal on escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeDeleteModal();
        closePromoteModal();
    }
});
</script>

</x-theme::layouts.app>
