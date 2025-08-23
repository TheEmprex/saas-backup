@php
$seoData = [
        'title'         => 'Browse Jobs - OnlyFans Ecosystem',
        'description'   => 'Find your next opportunity in the OnlyFans management ecosystem.',
    'image'         => url('/og_image.png'),
    'type'          => 'website'
];
@endphp

<x-theme::layouts.app>

<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white">
                        Browse 
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">Jobs</span>
                    </h1>
                    <p class="mt-1 text-base text-gray-600 dark:text-gray-300">{{ $jobs->total() }} opportunities available</p>
                </div>
                <div class="flex items-center space-x-3">
                    @auth
                        <a href="{{ route('marketplace.jobs.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Post Job
                        </a>
                    @endauth
                    <!-- View Toggle -->
                    <div class="flex bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-1">
                        <button onclick="toggleView('grid')" id="grid-btn" class="flex items-center px-3 py-1.5 text-sm font-medium rounded-md transition-colors bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M3 3h7v7H3V3zm0 11h7v7H3v-7zm11-11h7v7h-7V3zm0 11h7v7h-7v-7z"/>
                            </svg>
                        </button>
                        <button onclick="toggleView('list')" id="list-btn" class="flex items-center px-3 py-1.5 text-sm font-medium rounded-md transition-colors text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
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

        <!-- Advanced Search and Filters -->
        <x-theme::marketplace.search-filters />

        <!-- Results Summary -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 text-sm text-gray-600 dark:text-gray-400">
            <div>
                @if($jobs->total() > 0)
                    Showing {{ $jobs->firstItem() }}-{{ $jobs->lastItem() }} of {{ $jobs->total() }} jobs
                    @if(request('search'))
                        for "<span class="font-medium text-gray-900 dark:text-white">{{ request('search') }}</span>"
                    @endif
                @else
                    No jobs found
                    @if(request()->hasAny(['search', 'market', 'experience_level', 'contract_type', 'rate_type']))
                        matching your criteria
                    @endif
                @endif
            </div>
            @if($jobs->total() > 0)
                <div class="mt-2 sm:mt-0">
                    Page {{ $jobs->currentPage() }} of {{ $jobs->lastPage() }}
                </div>
            @endif
        </div>

        <!-- Jobs Grid -->
        <div id="jobs-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 transition-all duration-300">
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
                            <a href="{{ route('marketplace.jobs.index') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Clear Filters
                            </a>
                            @auth
                                <a href="{{ route('marketplace.jobs.create') }}" class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-zinc-600 text-base font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-zinc-800 hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors">
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

<script>
// View toggle functionality for jobs
function toggleView(view) {
    const container = document.getElementById('jobs-container');
    const gridBtn = document.getElementById('grid-btn');
    const listBtn = document.getElementById('list-btn');
    
    if (view === 'list') {
        container.className = 'space-y-4 transition-all duration-300';
        // Update button states
        gridBtn.className = 'flex items-center px-3 py-1.5 text-sm font-medium rounded-md transition-colors text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300';
        listBtn.className = 'flex items-center px-3 py-1.5 text-sm font-medium rounded-md transition-colors bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300';
        
        // Transform cards to list view
        const cards = container.querySelectorAll('> div');
        cards.forEach(card => {
            if (!card.classList.contains('col-span-full')) {
                card.className = card.className.replace(/rounded-lg p-6/, 'rounded-lg p-4 flex flex-row items-center space-x-4');
            }
        });
    } else {
        container.className = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 transition-all duration-300';
        // Update button states
        gridBtn.className = 'flex items-center px-3 py-1.5 text-sm font-medium rounded-md transition-colors bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300';
        listBtn.className = 'flex items-center px-3 py-1.5 text-sm font-medium rounded-md transition-colors text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300';
        
        // Transform cards back to grid view
        const cards = container.querySelectorAll('> div');
        cards.forEach(card => {
            if (!card.classList.contains('col-span-full')) {
                card.className = card.className.replace(/rounded-lg p-4 flex flex-row items-center space-x-4/, 'rounded-lg p-6');
            }
        });
    }
    
    // Save preference
    localStorage.setItem('jobsView', view);
}

// Clear search for jobs
function clearJobsSearch() {
    document.getElementById('search').value = '';
    document.getElementById('jobsSearchForm').submit();
}

// Remove individual filter for jobs
function removeJobsFilter(filterName) {
    const form = document.getElementById('jobsSearchForm');
    const input = form.querySelector(`[name="${filterName}"]`);
    if (input) {
        input.value = '';
        form.submit();
    }
}

// Auto-submit on filter change for jobs
document.addEventListener('DOMContentLoaded', function() {
    // Restore view preference
    const savedView = localStorage.getItem('jobsView');
    if (savedView === 'list') {
        toggleView('list');
    }
    
    // Auto-submit filters on change
    const autoSubmitFields = ['sort', 'per_page', 'market', 'experience_level', 'contract_type', 'rate_type', 'timezone'];
    autoSubmitFields.forEach(fieldName => {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (field) {
            field.addEventListener('change', function() {
                document.getElementById('jobsSearchForm').submit();
            });
        }
    });
    
    // Search on Enter key
    const searchField = document.getElementById('search');
    if (searchField) {
        searchField.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('jobsSearchForm').submit();
            }
        });
        
        // Live search with debounce (optional - commented out for performance)
        // let searchTimeout;
        // searchField.addEventListener('input', function() {
        //     clearTimeout(searchTimeout);
        //     searchTimeout = setTimeout(() => {
        //         if (this.value.length >= 3 || this.value.length === 0) {
        //             document.getElementById('jobsSearchForm').submit();
        //         }
        //     }, 500);
        // });
    }
    
    // Smooth scroll to results after form submission
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('search') || urlParams.has('market') || urlParams.has('experience_level')) {
        setTimeout(() => {
            const resultsSection = document.getElementById('jobs-container');
            if (resultsSection) {
                resultsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }, 100);
    }
    
    // Add loading states
    const form = document.getElementById('jobsSearchForm');
    if (form) {
        form.addEventListener('submit', function() {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '<svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Searching...';
                submitBtn.disabled = true;
            }
        });
    }
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + K to focus search
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            document.getElementById('search').focus();
        }
        
        // Escape to clear search
        if (e.key === 'Escape' && document.getElementById('search') === document.activeElement) {
            clearJobsSearch();
        }
    });
});
</script>

</x-theme::layouts.app>
