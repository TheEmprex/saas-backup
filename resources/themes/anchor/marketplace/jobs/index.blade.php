<x-layouts.app>

<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-zinc-900 dark:to-zinc-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header Section -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                Discover Your Next 
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">Opportunity</span>
            </h1>
            <p class="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                Connect with top-tier agencies and chatters in the OnlyFans management marketplace
            </p>
        </div>

        <!-- Statistics Bar -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 text-center border border-gray-100 dark:border-zinc-700">
                <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $jobs->total() }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Active Jobs</div>
            </div>
            <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 text-center border border-gray-100 dark:border-zinc-700">
                <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $jobs->where('rate_type', 'hourly')->count() }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Hourly Jobs</div>
            </div>
            <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 text-center border border-gray-100 dark:border-zinc-700">
                <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $jobs->where('rate_type', 'fixed')->count() }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Fixed Rate</div>
            </div>
            <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 text-center border border-gray-100 dark:border-zinc-700">
                <div class="text-3xl font-bold text-orange-600 dark:text-orange-400">{{ $jobs->where('rate_type', 'commission')->count() }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Commission</div>
            </div>
        </div>

        <!-- Search and Filters -->
        <x-theme::marketplace.search-filters />

        <!-- Jobs Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($jobs as $job)
                <x-theme::marketplace.job-card :job="$job" />
            @empty
                <div class="col-span-full">
                    <div class="text-center py-16">
                        <div class="mx-auto w-24 h-24 bg-gray-100 dark:bg-zinc-700 rounded-full flex items-center justify-center mb-6">
                            <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6.99A23.931 23.931 0 0120 15"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No jobs found</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-6 max-w-md mx-auto">
                            We couldn't find any jobs matching your criteria. Try adjusting your search filters or check back later for new opportunities.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-3 justify-center">
                            <a href="{{ route('marketplace.jobs') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Clear Filters
                            </a>
                            @auth
                                <a href="{{ route('marketplace.jobs.create') }}" class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-zinc-600 text-base font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Post a Job
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($jobs->hasPages())
            <div class="mt-12 flex justify-center">
                <div class="bg-white dark:bg-zinc-800 rounded-xl border border-gray-100 dark:border-zinc-700 p-4">
                    {{ $jobs->links() }}
                </div>
            </div>
        @endif

        <!-- Call to Action -->
        @guest
            <div class="mt-16 text-center">
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-8 text-white">
                    <h3 class="text-2xl font-bold mb-4">Ready to Get Started?</h3>
                    <p class="text-blue-100 mb-6 max-w-2xl mx-auto">
                        Join thousands of professionals already using our platform to find their perfect match
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-lg text-blue-600 bg-white hover:bg-gray-50 transition-all duration-200">
                            Sign Up Now
                        </a>
                        <a href="{{ route('login') }}" class="inline-flex items-center px-8 py-3 border border-white text-base font-medium rounded-lg text-white hover:bg-white hover:text-blue-600 transition-all duration-200">
                            Login
                        </a>
                    </div>
                </div>
            </div>
        @endguest
    </div>
</div>

</x-layouts.app>
