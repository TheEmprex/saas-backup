<x-layouts.app>
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-zinc-900 dark:to-zinc-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">
                    My 
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">Applications</span>
                </h1>
                <p class="text-gray-600 dark:text-gray-300">Track your job applications and their status</p>
            </div>
            <div class="flex space-x-3 mt-4 sm:mt-0">
                <a href="{{ route('marketplace.jobs') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Browse Jobs
                </a>
                <a href="{{ route('jobs.index') }}" class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-zinc-600 text-base font-medium rounded-xl text-gray-700 dark:text-gray-300 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6.99A23.931 23.931 0 0120 15"/>
                    </svg>
                    My Jobs
                </a>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 text-center border border-gray-100 dark:border-zinc-700">
                <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $applications->count() }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Total Applications</div>
            </div>
            <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 text-center border border-gray-100 dark:border-zinc-700">
                <div class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $applications->where('status', 'pending')->count() }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Pending</div>
            </div>
            <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 text-center border border-gray-100 dark:border-zinc-700">
                <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $applications->where('status', 'accepted')->count() }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Accepted</div>
            </div>
            <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 text-center border border-gray-100 dark:border-zinc-700">
                <div class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $applications->where('status', 'rejected')->count() }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Rejected</div>
            </div>
        </div>

        <!-- Applications List -->
        @if(count($applications) > 0)
            <div class="space-y-6">
                @foreach($applications as $application)
                    <x-theme::marketplace.application-card :application="$application" />
                @endforeach
            </div>
            
            <!-- Pagination -->
            @if($applications->hasPages())
                <div class="mt-12 flex justify-center">
                    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-gray-100 dark:border-zinc-700 p-4">
                        {{ $applications->links() }}
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-16">
                <div class="mx-auto w-24 h-24 bg-gray-100 dark:bg-zinc-700 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">No applications yet</h3>
                <p class="text-gray-600 dark:text-gray-300 mb-8 max-w-md mx-auto">
                    You haven't applied to any jobs yet. Start by browsing available opportunities and find your perfect match!
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('marketplace.jobs') }}" class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-xl text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Browse Jobs
                    </a>
                    <a href="{{ route('profile.show') }}" class="inline-flex items-center px-8 py-3 border border-gray-300 dark:border-zinc-600 text-base font-medium rounded-xl text-gray-700 dark:text-gray-300 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Complete Profile
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
</x-layouts.app>
