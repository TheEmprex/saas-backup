@props(['currentFilters' => []])

<!-- Advanced Search and Filters -->
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm mb-6">
    <form method="GET" action="{{ route('marketplace.jobs') }}" id="jobsSearchForm" class="p-6">
        <!-- Search Bar -->
        <div class="mb-6">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" 
                       name="search" 
                       id="search"
                       class="block w-full pl-10 pr-12 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white text-base" 
                       placeholder="Search jobs by title, description, company, or skills..." 
                       value="{{ request('search') }}">
                @if(request('search'))
                    <button type="button" onclick="clearJobsSearch()" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <svg class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                @endif
            </div>
        </div>

        <!-- Filters Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 mb-4">
            <!-- Market Filter -->
            <div>
                <label for="market" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                    Market
                </label>
                <select name="market" id="market" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white text-sm">
                    <option value="">All Markets</option>
                    <option value="english" {{ request('market') == 'english' ? 'selected' : '' }}>English</option>
                    <option value="spanish" {{ request('market') == 'spanish' ? 'selected' : '' }}>Spanish</option>
                    <option value="french" {{ request('market') == 'french' ? 'selected' : '' }}>French</option>
                    <option value="german" {{ request('market') == 'german' ? 'selected' : '' }}>German</option>
                </select>
            </div>

            <!-- Experience Level Filter -->
            <div>
                <label for="experience_level" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                    Experience
                </label>
                <select name="experience_level" id="experience_level" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white text-sm">
                    <option value="">All Levels</option>
                    <option value="beginner" {{ request('experience_level') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                    <option value="intermediate" {{ request('experience_level') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                    <option value="advanced" {{ request('experience_level') == 'advanced' ? 'selected' : '' }}>Advanced</option>
                    <option value="expert" {{ request('experience_level') == 'expert' ? 'selected' : '' }}>Expert</option>
                </select>
            </div>

            <!-- Contract Type Filter -->
            <div>
                <label for="contract_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z"/>
                    </svg>
                    Contract
                </label>
                <select name="contract_type" id="contract_type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white text-sm">
                    <option value="">All Types</option>
                    <option value="full_time" {{ request('contract_type') == 'full_time' ? 'selected' : '' }}>Full Time</option>
                    <option value="part_time" {{ request('contract_type') == 'part_time' ? 'selected' : '' }}>Part Time</option>
                    <option value="contract" {{ request('contract_type') == 'contract' ? 'selected' : '' }}>Contract</option>
                    <option value="freelance" {{ request('contract_type') == 'freelance' ? 'selected' : '' }}>Freelance</option>
                </select>
            </div>

            <!-- Rate Type Filter -->
            <div>
                <label for="rate_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M7,15H9C9,16.08 10.37,17 12,17C13.63,17 15,16.08 15,15C15,13.9 13.96,13.5 11.76,12.97C9.64,12.44 7,11.78 7,9C7,7.21 8.47,5.69 10.5,5.18V3H13.5V5.18C15.53,5.69 17,7.21 17,9H15C15,7.92 13.63,7 12,7C10.37,7 9,7.92 9,9C9,10.1 10.04,10.5 12.24,11.03C14.36,11.56 17,12.22 17,15C17,16.79 15.53,18.31 13.5,18.82V21H10.5V18.82C8.47,18.31 7,16.79 7,15Z"/>
                    </svg>
                    Rate Type
                </label>
                <select name="rate_type" id="rate_type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white text-sm">
                    <option value="">All Rates</option>
                    <option value="hourly" {{ request('rate_type') == 'hourly' ? 'selected' : '' }}>Hourly</option>
                    <option value="fixed" {{ request('rate_type') == 'fixed' ? 'selected' : '' }}>Fixed</option>
                    <option value="commission" {{ request('rate_type') == 'commission' ? 'selected' : '' }}>Commission</option>
                </select>
            </div>

            <!-- Sort By -->
            <div>
                <label for="sort" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M3 18h6v-2H3v2zM3 6v2h18V6H3zm0 7h12v-2H3v2z"/>
                    </svg>
                    Sort By
                </label>
                <select name="sort" id="sort" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white text-sm">
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                    <option value="featured" {{ request('sort') == 'featured' ? 'selected' : '' }}>Featured First</option>
                    <option value="rate_high" {{ request('sort') == 'rate_high' ? 'selected' : '' }}>Highest Rate</option>
                    <option value="rate_low" {{ request('sort') == 'rate_low' ? 'selected' : '' }}>Lowest Rate</option>
                    <option value="applications" {{ request('sort') == 'applications' ? 'selected' : '' }}>Most Applied</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                </select>
            </div>

            <!-- Per Page -->
            <div>
                <label for="per_page" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Per Page
                </label>
                <select name="per_page" id="per_page" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white text-sm">
                    <option value="12" {{ request('per_page', 12) == 12 ? 'selected' : '' }}>12</option>
                    <option value="24" {{ request('per_page') == 24 ? 'selected' : '' }}>24</option>
                    <option value="48" {{ request('per_page') == 48 ? 'selected' : '' }}>48</option>
                    <option value="96" {{ request('per_page') == 96 ? 'selected' : '' }}>96</option>
                </select>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-between pt-4">
            <div class="flex space-x-3">
                <button 
                    type="submit" 
                    class="inline-flex items-center px-6 py-2.5 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-sm hover:shadow-md"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Search Jobs
                </button>
                <a 
                    href="{{ route('marketplace.jobs') }}" 
                    class="inline-flex items-center px-6 py-2.5 border border-gray-300 dark:border-zinc-600 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Clear Filters
                </a>
            </div>
        </div>

        <!-- Active Filters Display -->
        @if(request()->hasAny(['search', 'market', 'experience_level', 'contract_type', 'rate_type']))
            <div class="border-t border-gray-200 dark:border-zinc-600 pt-4">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-sm text-gray-700 dark:text-gray-300">Active filters:</span>
                    @if(request('search'))
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                            Search: "{{ request('search') }}"
                            <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="ml-1 text-blue-600 hover:text-blue-800">×</a>
                        </span>
                    @endif
                    @if(request('market'))
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                            Market: {{ ucfirst(request('market')) }}
                            <a href="{{ request()->fullUrlWithQuery(['market' => null]) }}" class="ml-1 text-green-600 hover:text-green-800">×</a>
                        </span>
                    @endif
                    @if(request('experience_level'))
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400">
                            Level: {{ ucfirst(request('experience_level')) }}
                            <a href="{{ request()->fullUrlWithQuery(['experience_level' => null]) }}" class="ml-1 text-purple-600 hover:text-purple-800">×</a>
                        </span>
                    @endif
                    @if(request('contract_type'))
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900/20 dark:text-orange-400">
                            Type: {{ ucfirst(str_replace('_', ' ', request('contract_type'))) }}
                            <a href="{{ request()->fullUrlWithQuery(['contract_type' => null]) }}" class="ml-1 text-orange-600 hover:text-orange-800">×</a>
                        </span>
                    @endif
                    @if(request('rate_type'))
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-pink-100 text-pink-800 dark:bg-pink-900/20 dark:text-pink-400">
                            Rate: {{ ucfirst(request('rate_type')) }}
                            <a href="{{ request()->fullUrlWithQuery(['rate_type' => null]) }}" class="ml-1 text-pink-600 hover:text-pink-800">×</a>
                        </span>
                    @endif
                </div>
            </div>
        @endif
    </form>
</div>
