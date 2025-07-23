@php
$seoData = [
        'title'         => 'Find Talent - OnlyFans Ecosystem',
        'description'   => 'Discover talented professionals in the OnlyFans management ecosystem.',
    'image'         => url('/og_image.png'),
    'type'          => 'website'
];
@endphp

<x-layouts.app>

<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white">
                        Find 
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">Talent</span>
                    </h1>
                    <p class="mt-1 text-base text-gray-600 dark:text-gray-300">{{ $profiles->total() }} professionals ready to help</p>
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
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 text-center border border-gray-100 dark:border-gray-700">
                <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $profiles->total() }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Total Profiles</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 text-center border border-gray-100 dark:border-gray-700">
                <div class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $profiles->where('is_available', true)->count() }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Available</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 text-center border border-gray-100 dark:border-gray-700">
                <div class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $profiles->filter(function($p) { return $p->user->isProfileFeatured(); })->count() }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">⭐ Featured</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 text-center border border-gray-100 dark:border-gray-700">
                <div class="text-3xl font-bold text-orange-600 dark:text-orange-400">{{ $profiles->where('calculated_average_rating', '>=', 4)->count() }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Top Rated</div>
            </div>
        </div>

        <!-- Advanced Search and Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm mb-6">
            <form method="GET" action="{{ route('marketplace.profiles') }}" id="searchForm" class="p-6">
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
                               placeholder="Search professionals by name, skills, bio, or expertise..." 
                               value="{{ request('search') }}">
                        @if(request('search'))
                            <button type="button" onclick="clearSearch()" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <svg class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Filters Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 mb-4">
                    <!-- User Type -->
                    <div class="lg:col-span-2">
                        <label for="user_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Role
                        </label>
                        <select name="user_type" id="user_type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white text-sm">
                            <option value="">All Roles</option>
                            @foreach($userTypes as $type)
                                <option value="{{ $type->name }}" {{ request('user_type') == $type->name ? 'selected' : '' }}>{{ $type->display_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Rating Filter -->
                    <div>
                        <label for="min_rating" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                            Rating
                        </label>
                        <select name="min_rating" id="min_rating" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white text-sm">
                            <option value="">Any Rating</option>
                            <option value="4.5" {{ request('min_rating') == '4.5' ? 'selected' : '' }}>4.5+ Stars</option>
                            <option value="4" {{ request('min_rating') == '4' ? 'selected' : '' }}>4+ Stars</option>
                            <option value="3" {{ request('min_rating') == '3' ? 'selected' : '' }}>3+ Stars</option>
                            <option value="2" {{ request('min_rating') == '2' ? 'selected' : '' }}>2+ Stars</option>
                        </select>
                    </div>

                    <!-- Availability -->
                    <div>
                        <label for="availability" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Status
                        </label>
                        <select name="availability" id="availability" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white text-sm">
                            <option value="">Any Status</option>
                            <option value="available" {{ request('availability') == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="busy" {{ request('availability') == 'busy' ? 'selected' : '' }}>Busy</option>
                        </select>
                    </div>

                    <!-- Sort By -->
                    <div>
                        <label for="sort" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2zM3 16a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2z"/>
                            </svg>
                            Sort By
                        </label>
                        <select name="sort" id="sort" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white text-sm">
                            <option value="featured" {{ request('sort') == 'featured' ? 'selected' : '' }}>Featured First</option>
                            <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Highest Rated</option>
                            <option value="reviews" {{ request('sort') == 'reviews' ? 'selected' : '' }}>Most Reviews</option>
                            <option value="recent" {{ request('sort') == 'recent' ? 'selected' : '' }}>Recently Active</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
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
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <div class="flex flex-wrap gap-2">
                        @if(request()->hasAny(['search', 'user_type', 'min_rating', 'availability', 'sort']))
                            <!-- Active Filters -->
                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <span class="mr-2">Active filters:</span>
                                @if(request('search'))
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 mr-2">
                                        Search: "{{ Str::limit(request('search'), 20) }}"
                                        <button type="button" onclick="removeFilter('search')" class="ml-1 text-blue-600 hover:text-blue-800">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                                    </span>
                                @endif
                                @if(request('user_type'))
                                    @php $selectedType = $userTypes->firstWhere('name', request('user_type')); @endphp
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 mr-2">
                                        {{ $selectedType ? $selectedType->display_name : request('user_type') }}
                                        <button type="button" onclick="removeFilter('user_type')" class="ml-1 text-purple-600 hover:text-purple-800">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                                    </span>
                                @endif
                                @if(request('min_rating'))
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 mr-2">
                                        {{ request('min_rating') }}+ Stars
                                        <button type="button" onclick="removeFilter('min_rating')" class="ml-1 text-yellow-600 hover:text-yellow-800">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                    
                    <div class="flex space-x-3">
                        @if(request()->hasAny(['search', 'user_type', 'min_rating', 'availability', 'sort']))
                            <a href="{{ route('marketplace.profiles') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Clear All
                            </a>
                        @endif
                        <button type="submit" class="inline-flex items-center px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Apply Filters
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Results Summary -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 text-sm text-gray-600 dark:text-gray-400">
            <div>
                @if($profiles->total() > 0)
                    Showing {{ $profiles->firstItem() }}-{{ $profiles->lastItem() }} of {{ $profiles->total() }} professionals
                    @if(request('search'))
                        for "<span class="font-medium text-gray-900 dark:text-white">{{ request('search') }}</span>"
                    @endif
                @else
                    No professionals found
                    @if(request()->hasAny(['search', 'user_type', 'min_rating', 'availability']))
                        matching your criteria
                    @endif
                @endif
            </div>
            @if($profiles->total() > 0)
                <div class="mt-2 sm:mt-0">
                    Page {{ $profiles->currentPage() }} of {{ $profiles->lastPage() }}
                </div>
            @endif
        </div>

        <!-- Profiles Grid -->
        <div id="profiles-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 transition-all duration-300">
            @forelse($profiles as $profile)
            @php
                $isFeatured = $profile->user->isProfileFeatured();
                $cardClass = $isFeatured ? 
                    'bg-gradient-to-br from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20 border-2 border-yellow-400 dark:border-yellow-500 shadow-lg' : 
                    'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700';
            @endphp
            <div class="{{ $cardClass }} rounded-lg p-6 hover:shadow-lg transition-shadow h-full flex flex-col relative">
                <div class="flex-1">
                    <!-- Profile Header -->
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                            @if($profile->user->avatar)
                                <img src="{{ Storage::url($profile->user->avatar) }}" alt="{{ $profile->user->name }}" class="w-12 h-12 rounded-full object-cover">
                            @else
                                <span class="text-white font-semibold text-sm">{{ strtoupper(substr($profile->user->name, 0, 2)) }}</span>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <h3 class="font-semibold text-gray-900 dark:text-white truncate">{{ $profile->user->name }}</h3>
                                @if($isFeatured)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gradient-to-r from-yellow-400 to-yellow-600 text-white shadow-sm flex-shrink-0">
                                        ⭐ Featured
                                    </span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $profile->user->userType->display_name }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                @if($profile->is_verified)
                                    <div class="flex items-center">
                                        <svg class="w-3 h-3 text-blue-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-xs text-blue-500 font-medium">Verified</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Bio -->
                    @if($profile->bio)
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-3">{{ Str::limit($profile->bio, 120) }}</p>
                    @endif
                    
                    <!-- Rating -->
                    <div class="flex items-center mb-4">
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $profile->calculated_average_rating)
                                    <span class="text-yellow-400 text-sm">★</span>
                                @else
                                    <span class="text-gray-300 dark:text-gray-600 text-sm">★</span>
                                @endif
                            @endfor
                        </div>
                        <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">{{ number_format($profile->calculated_average_rating, 1) }}</span>
                        <span class="ml-1 text-sm text-gray-500 dark:text-gray-400">({{ $profile->calculated_total_reviews }})</span>
                    </div>
                    
                    <!-- Key Stats -->
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        @if($profile->hourly_rate)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 text-center">
                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Hourly Rate</div>
                                <div class="text-sm font-semibold text-green-600 dark:text-green-400">${{ $profile->hourly_rate }}/hr</div>
                            </div>
                        @endif
                        @if($profile->experience_years)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 text-center">
                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Experience</div>
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $profile->experience_years }} years</div>
                            </div>
                        @endif
                        @if($profile->typing_speed_wpm)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 text-center">
                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Typing Speed</div>
                                <div class="text-sm font-semibold text-blue-600 dark:text-blue-400">{{ $profile->typing_speed_wpm }} WPM</div>
                            </div>
                        @endif
                        @if($profile->views)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 text-center">
                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Views</div>
                                <div class="text-sm font-semibold text-purple-600 dark:text-purple-400">{{ $profile->views }}</div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Skills -->
                    @if($profile->skills)
                        <div class="mb-4">
                            <div class="flex flex-wrap gap-2">
                                @foreach(array_slice(explode(',', $profile->skills), 0, 3) as $skill)
                                    <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-2 py-1 rounded-full text-xs">{{ trim($skill) }}</span>
                                @endforeach
                                @if(count(explode(',', $profile->skills)) > 3)
                                    <span class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 px-2 py-1 rounded-full text-xs">+{{ count(explode(',', $profile->skills)) - 3 }}</span>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
                
                <div class="mt-auto pt-4 border-t border-gray-100 dark:border-gray-700">
                    <!-- Availability -->
                    <div class="mb-3">
                        @if($profile->is_available)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-1"></span>
                                Available
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                <span class="w-2 h-2 bg-gray-500 rounded-full mr-1"></span>
                                Busy
                            </span>
                        @endif
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex space-x-2">
                        <a href="{{ route('marketplace.profiles.show', $profile->user) }}" 
                           class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg transition-colors text-center text-sm font-medium">
                            View Profile
                        </a>
                        <a href="{{ route('marketplace.messages.create', $profile->user) }}" 
                           class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-3 py-2 rounded-lg transition-colors text-center text-sm font-medium">
                            Message
                        </a>
                    </div>
                </div>
            </div>
            @empty
                <div class="col-span-full text-center py-16">
                    <div class="mx-auto w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No profiles found</h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-6 max-w-md mx-auto">
                        We couldn't find any profiles matching your criteria. Try adjusting your search filters or check back later for new talent.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <a href="{{ route('marketplace.profiles') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Clear Filters
                        </a>
                        @auth
                            <a href="{{ route('marketplace.jobs.create') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Post a Job
                            </a>
                        @endauth
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($profiles->hasPages())
            <div class="mt-8 flex justify-center">
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                    {{ $profiles->links() }}
                </div>
            </div>
        @endif

        <!-- Call to Action -->
        @guest
            <div class="mt-12 text-center">
                <div class="bg-blue-600 rounded-lg p-8 text-white">
                    <h3 class="text-xl font-bold mb-4">Ready to Get Started?</h3>
                    <p class="text-blue-100 mb-6 max-w-2xl mx-auto">
                        Join thousands of professionals already using our platform to find their perfect match
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-blue-600 bg-white hover:bg-gray-50 transition-colors">
                            Sign Up Now
                        </a>
                        <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 border border-white text-sm font-medium rounded-lg text-white hover:bg-white hover:text-blue-600 transition-colors">
                            Login
                        </a>
                    </div>
                </div>
            </div>
        @endguest
    </div>
</div>

<script>
// View toggle functionality
function toggleView(view) {
    const container = document.getElementById('profiles-container');
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
    localStorage.setItem('profileView', view);
}

// Clear search
function clearSearch() {
    document.getElementById('search').value = '';
    document.getElementById('searchForm').submit();
}

// Remove individual filter
function removeFilter(filterName) {
    const form = document.getElementById('searchForm');
    const input = form.querySelector(`[name="${filterName}"]`);
    if (input) {
        input.value = '';
        form.submit();
    }
}

// Auto-submit on filter change
document.addEventListener('DOMContentLoaded', function() {
    // Restore view preference
    const savedView = localStorage.getItem('profileView');
    if (savedView === 'list') {
        toggleView('list');
    }
    
    // Auto-submit filters on change
    const autoSubmitFields = ['sort', 'per_page', 'availability'];
    autoSubmitFields.forEach(fieldName => {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (field) {
            field.addEventListener('change', function() {
                document.getElementById('searchForm').submit();
            });
        }
    });
    
    // Search on Enter key
    const searchField = document.getElementById('search');
    if (searchField) {
        searchField.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('searchForm').submit();
            }
        });
        
        // Live search with debounce (optional - commented out for performance)
        // let searchTimeout;
        // searchField.addEventListener('input', function() {
        //     clearTimeout(searchTimeout);
        //     searchTimeout = setTimeout(() => {
        //         if (this.value.length >= 3 || this.value.length === 0) {
        //             document.getElementById('searchForm').submit();
        //         }
        //     }, 500);
        // });
    }
    
    // Smooth scroll to results after form submission
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('search') || urlParams.has('user_type') || urlParams.has('min_rating')) {
        setTimeout(() => {
            const resultsSection = document.getElementById('profiles-container');
            if (resultsSection) {
                resultsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }, 100);
    }
    
    // Add loading states
    const form = document.getElementById('searchForm');
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
            clearSearch();
        }
    });
});
</script>

</x-layouts.app>
