@props(['currentFilters' => []])

<div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-700 p-6 mb-8">
    <form method="GET" action="{{ route('marketplace.jobs') }}" class="space-y-4">
        <!-- Search Input -->
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input 
                type="text" 
                name="search" 
                class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-zinc-600 rounded-xl bg-gray-50 dark:bg-zinc-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                placeholder="Search jobs by title, description, or company..." 
                value="{{ request('search') }}"
            >
        </div>

        <!-- Filter Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Market Filter -->
            <div class="relative">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Market</label>
                <select name="market" class="block w-full px-3 py-2.5 border border-gray-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                    <option value="">All Markets</option>
                    <option value="english" {{ request('market') == 'english' ? 'selected' : '' }}>English</option>
                    <option value="spanish" {{ request('market') == 'spanish' ? 'selected' : '' }}>Spanish</option>
                    <option value="french" {{ request('market') == 'french' ? 'selected' : '' }}>French</option>
                    <option value="german" {{ request('market') == 'german' ? 'selected' : '' }}>German</option>
                </select>
            </div>

            <!-- Experience Level Filter -->
            <div class="relative">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Experience Level</label>
                <select name="experience_level" class="block w-full px-3 py-2.5 border border-gray-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                    <option value="">All Levels</option>
                    <option value="beginner" {{ request('experience_level') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                    <option value="intermediate" {{ request('experience_level') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                    <option value="advanced" {{ request('experience_level') == 'advanced' ? 'selected' : '' }}>Advanced</option>
                </select>
            </div>

            <!-- Contract Type Filter -->
            <div class="relative">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Contract Type</label>
                <select name="contract_type" class="block w-full px-3 py-2.5 border border-gray-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                    <option value="">All Types</option>
                    <option value="full_time" {{ request('contract_type') == 'full_time' ? 'selected' : '' }}>Full Time</option>
                    <option value="part_time" {{ request('contract_type') == 'part_time' ? 'selected' : '' }}>Part Time</option>
                    <option value="contract" {{ request('contract_type') == 'contract' ? 'selected' : '' }}>Contract</option>
                </select>
            </div>

            <!-- Rate Type Filter -->
            <div class="relative">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rate Type</label>
                <select name="rate_type" class="block w-full px-3 py-2.5 border border-gray-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                    <option value="">All Rates</option>
                    <option value="hourly" {{ request('rate_type') == 'hourly' ? 'selected' : '' }}>Hourly</option>
                    <option value="fixed" {{ request('rate_type') == 'fixed' ? 'selected' : '' }}>Fixed</option>
                    <option value="commission" {{ request('rate_type') == 'commission' ? 'selected' : '' }}>Commission</option>
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
            
            <!-- Sort Options -->
            <div class="flex items-center space-x-2">
                <label class="text-sm text-gray-700 dark:text-gray-300">Sort by:</label>
                <select name="sort" class="px-3 py-2 border border-gray-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    <option value="rate_high" {{ request('sort') == 'rate_high' ? 'selected' : '' }}>Highest Rate</option>
                    <option value="rate_low" {{ request('sort') == 'rate_low' ? 'selected' : '' }}>Lowest Rate</option>
                </select>
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
