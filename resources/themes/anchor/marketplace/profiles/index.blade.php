<x-layouts.app>

<div class="bg-white dark:bg-zinc-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Find Talent</h1>
            <p class="text-gray-600 dark:text-gray-300">Discover talented professionals in the marketplace</p>
        </div>

        <!-- Search and Filters -->
<div class="bg-white  border border-gray-200 dark:border-zinc-700 rounded-lg p-6 mb-8">
            <form method="GET" action="{{ route('marketplace.profiles') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    <div class="md:col-span-2">
                        <input type="text" 
                               name="search" 
                               class="w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                               placeholder="Search profiles..." 
                               value="{{ request('search') }}">
                    </div>
                    <div>
                        <select name="user_type" class="w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Types</option>
                            <option value="model" {{ request('user_type') == 'model' ? 'selected' : '' }}>Model</option>
                            <option value="manager" {{ request('user_type') == 'manager' ? 'selected' : '' }}>Manager</option>
                            <option value="chatter" {{ request('user_type') == 'chatter' ? 'selected' : '' }}>Chatter</option>
                            <option value="agency" {{ request('user_type') == 'agency' ? 'selected' : '' }}>Agency</option>
                        </select>
                    </div>
                    <div>
                        <select name="min_rating" class="w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Ratings</option>
<option value="4" {{ request('min_rating') == '4' ? 'selected' : '' }} class="">4+ Stars</option>
<option value="3" {{ request('min_rating') == '3' ? 'selected' : '' }} class="">3+ Stars</option>
<option value="2" {{ request('min_rating') == '2' ? 'selected' : '' }} class="">2+ Stars</option>
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                            Search
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Profiles Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($profiles as $profile)
<div class="bg-white  border border-gray-200 dark:border-zinc-700 rounded-lg p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center text-white text-xl font-bold">
                            @if($profile->user->avatar)
                                <img src="{{ Storage::url($profile->user->avatar) }}" alt="{{ $profile->user->name }}" class="w-16 h-16 rounded-full object-cover">
                            @else
                                {{ substr($profile->user->name, 0, 1) }}
                            @endif
                        </div>
                        <div>
<h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $profile->user->name }}</h3>
                            <p class="text-gray-600 dark:text-gray-300">{{ $profile->user->userType->display_name }}</p>
                            @if($profile->location)
                                <p class="text-sm text-gray-500 dark:text-gray-400">📍 {{ $profile->location }}</p>
                            @endif
                        </div>
                    </div>
                    
                    @if($profile->bio)
                        <p class="text-gray-700 dark:text-gray-300 mb-4">{{ Str::limit($profile->bio, 100) }}</p>
                    @endif
                    
                    <!-- Skills -->
                    @if($profile->skills)
                        <div class="mb-4">
                            <div class="flex flex-wrap gap-2">
                                @foreach(explode(',', $profile->skills) as $skill)
                                    <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-2 py-1 rounded text-sm">{{ trim($skill) }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    <!-- Rating -->
                    <div class="mb-4">
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $profile->average_rating)
                                    <span class="text-yellow-400">⭐</span>
                                @else
                                    <span class="text-gray-300">⭐</span>
                                @endif
                            @endfor
                            <span class="ml-2 text-gray-600 dark:text-gray-300">{{ number_format($profile->average_rating, 1) }}</span>
                            <span class="text-gray-500 dark:text-gray-400 ml-1">({{ $profile->total_ratings }} reviews)</span>
                        </div>
                    </div>
                    
                    <!-- Rate and Experience -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        @if($profile->hourly_rate)
                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Hourly Rate</div>
                                <div class="text-lg font-semibold text-green-600">${{ $profile->hourly_rate }}/hr</div>
                            </div>
                        @endif
                        @if($profile->experience_years)
                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Experience</div>
                                <div class="text-lg font-semibold dark:text-white">{{ $profile->experience_years }} years</div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Availability -->
                    <div class="mb-4">
                        @if($profile->is_available)
                            <span class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 px-2 py-1 rounded text-sm">Available</span>
                        @else
                            <span class="bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 px-2 py-1 rounded text-sm">Busy</span>
                        @endif
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex space-x-3">
                        <a href="{{ route('marketplace.profiles.show', $profile->user) }}" 
                           class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors text-center">
                            View Profile
                        </a>
                        <a href="{{ route('marketplace.messages.create', $profile->user) }}" 
                           class="flex-1 bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors text-center">
                            Message
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <div class="mb-4">
                        <svg class="mx-auto h-16 w-16 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No profiles found</h3>
                    <p class="text-gray-600 dark:text-gray-300">Try adjusting your search filters or check back later for new profiles.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($profiles->hasPages())
            <div class="mt-8">
                {{ $profiles->links() }}
            </div>
        @endif
    </div>
</div>

</x-layouts.app>
