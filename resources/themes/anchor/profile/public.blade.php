<x-layouts.app>

<div class="bg-white dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Profile Info -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 mb-6">
                    <div class="flex items-start justify-between mb-6">
                        <div class="flex items-center space-x-4">
                            <div class="w-20 h-20 bg-blue-500 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                                @if($user->avatar)
                                    <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="w-20 h-20 rounded-full object-cover">
                                @else
                                    {{ substr($user->name, 0, 1) }}
                                @endif
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h1>
                                <p class="text-gray-600 dark:text-gray-300">{{ $user->userType->display_name ?? 'User' }}</p>
                                @if($user->userProfile && $user->userProfile->location)
                                    <p class="text-sm text-gray-500 dark:text-gray-400">📍 {{ $user->userProfile->location }}</p>
                                @endif
                            </div>
                        </div>
                        
                        @if(auth()->check() && auth()->id() !== $user->id)
                            <a href="{{ route('messages.create', $user->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                Send Message
                            </a>
                        @endif
                    </div>

                    @if($user->userProfile && $user->userProfile->bio)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-white">About</h3>
                            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $user->userProfile->bio }}</p>
                        </div>
                    @endif

                    <!-- Skills & Experience -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($user->userProfile && $user->userProfile->experience_years)
                            <div>
                                <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-white">Experience</h3>
                                <p class="text-gray-700 dark:text-gray-300">{{ $user->userProfile->experience_years }} years</p>
                            </div>
                        @endif

                        @if($user->userProfile && $user->userProfile->typing_speed_wpm)
                            <div>
                                <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-white">Typing Speed</h3>
                                <p class="text-gray-700 dark:text-gray-300">{{ $user->userProfile->typing_speed_wpm }} WPM</p>
                            </div>
                        @endif

                        @if($user->userProfile && $user->userProfile->languages)
                            <div>
                                <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-white">Languages</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach(json_decode($user->userProfile->languages, true) ?? [] as $language)
                                        <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-2 py-1 rounded text-sm">{{ $language }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($user->userProfile && $user->userProfile->skills)
                            <div>
                                <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-white">Skills</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach(json_decode($user->userProfile->skills, true) ?? [] as $skill)
                                        <span class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 px-2 py-1 rounded text-sm">{{ $skill }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Contract Reviews -->
                @if($user->contractReviewsReceived->count() > 0)
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Contract Reviews</h3>
                        <div class="space-y-4">
                            @foreach($user->contractReviewsReceived->take(5) as $review)
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                @if($review->reviewer->avatar)
                                                    <img class="h-8 w-8 rounded-full" src="{{ Storage::url($review->reviewer->avatar) }}" alt="{{ $review->reviewer->name }}">
                                                @else
                                                    <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-sm font-bold">
                                                        {{ substr($review->reviewer->name, 0, 1) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $review->reviewer->name }}</p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $review->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="text-yellow-400">⭐ {{ $review->rating }}</span>
                                        </div>
                                    </div>
                                    <div class="text-sm text-gray-700 dark:text-gray-300">
                                        {{ $review->comment ?? 'No comment provided.' }}
                                    </div>
                                    @if($review->would_work_again || $review->recommend_to_others)
                                        <div class="mt-2 flex space-x-2">
                                            @if($review->would_work_again)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Would work again</span>
                                            @endif
                                            @if($review->recommend_to_others)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Recommends</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                            
                            @if($user->contractReviewsReceived->count() > 5)
                                <div class="text-center">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Showing 5 of {{ $user->contractReviewsReceived->count() }} reviews</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Contact Info -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Contact Information</h3>
                    <div class="space-y-3">
                        <div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Email:</span>
                            <p class="text-sm text-gray-600 dark:text-gray-300">{{ $user->email }}</p>
                        </div>
                        
                        @if($user->userProfile && $user->userProfile->phone)
                            <div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Phone:</span>
                                <p class="text-sm text-gray-600 dark:text-gray-300">{{ $user->userProfile->phone }}</p>
                            </div>
                        @endif
                        
                        @if($user->userProfile && $user->userProfile->website)
                            <div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Website:</span>
                                <a href="{{ $user->userProfile->website }}" target="_blank" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                    {{ $user->userProfile->website }}
                                </a>
                            </div>
                        @endif
                        
                        @if($user->userProfile && $user->userProfile->linkedin_url)
                            <div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">LinkedIn:</span>
                                <a href="{{ $user->userProfile->linkedin_url }}" target="_blank" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                    View Profile
                                </a>
                            </div>
                        @endif
                        
                        <div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Member since:</span>
                            <p class="text-sm text-gray-600 dark:text-gray-300">{{ $user->created_at->format('F Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Statistics</h3>
                    <div class="space-y-3">
                        @if($user->total_contract_reviews > 0)
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Average Contract Rating</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">
                                    ⭐ {{ number_format($user->average_contract_rating, 1) }}
                                    ({{ $user->total_contract_reviews }} reviews)
                                </span>
                            </div>
                        @else
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Average Contract Rating</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">
                                    No reviews yet.
                                </span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Reviews Received</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->contractReviewsReceived->count() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                @if(auth()->check() && auth()->id() !== $user->id)
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Actions</h3>
                        <div class="space-y-3">
                            <a href="{{ route('messages.create', $user->id) }}" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 text-center block">
                                Send Message
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

</x-layouts.app>
