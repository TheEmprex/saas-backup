<x-layouts.app>

<div class="bg-white dark:bg-gray-900 min-h-screen">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Profile Header -->
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 mb-6">
            <div class="flex flex-col sm:flex-row items-center sm:items-start space-y-4 sm:space-y-0 sm:space-x-6">
                <!-- Avatar -->
                <div class="flex-shrink-0">
                    <img 
                        class="h-24 w-24 rounded-full border-2 border-gray-200 dark:border-gray-600 object-cover" 
                        src="{{ auth()->user()->getProfilePictureUrl() }}"
                        alt="{{ auth()->user()->name }}"
                    >
                </div>
                
                <!-- Profile Info -->
                <div class="flex-1 text-center sm:text-left">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ auth()->user()->name }}</h1>
                    <div class="text-gray-600 dark:text-gray-400 mb-4">
                        @if(auth()->user()->userType)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 mr-2">
                                {{ auth()->user()->userType->name }}
                            </span>
                        @endif
                        @if(auth()->user()->userProfile && auth()->user()->userProfile->location)
                            <span class="text-sm">{{ auth()->user()->userProfile->location }}</span>
                        @endif
                    </div>
                    
                    @if(auth()->user()->userProfile && auth()->user()->userProfile->bio)
                        <p class="text-gray-700 dark:text-gray-300 text-sm">{{ auth()->user()->userProfile->bio }}</p>
                    @endif
                </div>
                
                <!-- Edit Button -->
                <div class="flex-shrink-0">
                    <a href="{{ route('profile.edit') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                        Edit Profile
                    </a>
                </div>
            </div>
        </div>

        <!-- Featured Profile Status -->
        @if(auth()->user()->userProfile && auth()->user()->userProfile->is_featured && auth()->user()->userProfile->featured_until && auth()->user()->userProfile->featured_until->isFuture())
        <div class="bg-gradient-to-r from-yellow-50 to-amber-50 dark:from-yellow-900/20 dark:to-amber-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 bg-gradient-to-r from-yellow-400 to-amber-500 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            ✨ Featured Profile
                            <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                PREMIUM
                            </span>
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            @php
                                $featuredUntil = auth()->user()->userProfile->featured_until;
                                if ($featuredUntil && $featuredUntil->isFuture()) {
                                    $daysRemaining = max(1, ceil($featuredUntil->diffInDays(now())));
                                    $daysText = $daysRemaining . ' days remaining';
                                } else {
                                    $daysText = 'Expired';
                                }
                            @endphp
                            Your profile is featured until {{ auth()->user()->userProfile->featured_until->format('M j, Y') }} 
                            ({{ $daysText }})
                        </p>
                    </div>
                </div>
                <a href="{{ route('profile.feature') }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                    Extend Featured
                </a>
            </div>
        </div>
        @endif


        <!-- Profile Completion Status -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div class="relative w-16 h-16">
                            <!-- Circular Progress -->
                            <svg class="w-16 h-16 transform -rotate-90" viewBox="0 0 100 100">
                                <circle cx="50" cy="50" r="45" stroke="currentColor" stroke-width="8" fill="none" class="text-gray-200 dark:text-gray-700" />
                                <circle cx="50" cy="50" r="45" stroke="currentColor" stroke-width="8" fill="none" stroke-linecap="round" class="text-blue-600" stroke-dasharray="283" stroke-dashoffset="{{ 283 - (283 * 0.75) }}" />
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">75%</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Profile Completion</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Complete your profile to attract more clients</p>
                    </div>
                </div>
                <a href="{{ route('profile.edit') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                    Complete Profile
                </a>
            </div>
            <div class="mt-4">
                <div class="text-xs text-gray-600 dark:text-gray-400 mb-2">Missing: Add portfolio items, complete skills section</div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: 75%"></div>
                </div>
            </div>
        </div>

        <!-- Find Talents Requirements (for Agencies/Managers) -->
        @if(auth()->user()->isAgency() || (auth()->user()->userType && in_array(strtolower(auth()->user()->userType->name), ['manager', 'agency', 'ofm_agency', 'chatting_agency'])))
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Find Talents</h2>
                <a href="{{ route('marketplace.profiles') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                    Browse All Talents →
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-medium text-gray-900 dark:text-white mb-1">Developers</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Find skilled developers</p>
                </div>
                <div class="text-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <h3 class="font-medium text-gray-900 dark:text-white mb-1">Chat Support</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Customer service agents</p>
                </div>
                <div class="text-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                    </div>
                    <h3 class="font-medium text-gray-900 dark:text-white mb-1">Designers</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Creative professionals</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Profile Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <!-- Availability Status -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Availability</h3>
                @if(auth()->user()->userProfile && auth()->user()->userProfile->is_available)
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                        Available
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                        Busy
                    </span>
                @endif
            </div>
            
            <!-- Member Since -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Member Since</h3>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ auth()->user()->created_at->format('M Y') }}</p>
            </div>
            
            <!-- Jobs Completed -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Jobs Completed</h3>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ auth()->user()->userProfile ? auth()->user()->userProfile->jobs_completed : 0 }}</p>
            </div>
            
            <!-- Rating -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Rating</h3>
                @php
                    // Get real-time rating data from contract reviews
                    $contractReviews = auth()->user()->contractReviewsReceived();
                    $totalRatings = $contractReviews->count();
                    $avgRating = $totalRatings > 0 ? $contractReviews->avg('rating') : 0;
                    $avgRating = floatval($avgRating);
                @endphp
                <div class="flex items-center">
                    <div class="flex text-yellow-400">
                        <!-- Dynamic star rating display -->
                        @for($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4 {{ $i <= $avgRating ? 'fill-current' : 'text-gray-300 dark:text-gray-600' }}" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        @endfor
                    </div>
                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ number_format($avgRating, 1) }} ({{ $totalRatings }})</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Professional Information -->
                @if(auth()->user()->userProfile)
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Professional Information</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @if(auth()->user()->userProfile->experience_years)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Experience</dt>
                            <dd class="text-sm text-gray-900 dark:text-white flex items-center">
                                <svg class="w-4 h-4 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                {{ auth()->user()->userProfile->experience_years }} years
                            </dd>
                        </div>
                        @endif
                        
                        @if(auth()->user()->userProfile->typing_speed_wpm && auth()->user()->isChatter())
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Typing Speed</dt>
                            <dd class="text-sm text-gray-900 dark:text-white flex items-center">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                                {{ auth()->user()->userProfile->typing_speed_wpm }} WPM
                                @if(auth()->user()->userProfile->typing_accuracy)
                                    <span class="ml-2 text-xs text-gray-500">({{ auth()->user()->userProfile->typing_accuracy }}% accuracy)</span>
                                @endif
                            </dd>
                        </div>
                        @endif
                        
                        @if(auth()->user()->userProfile->hourly_rate)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Hourly Rate</dt>
                            <dd class="text-sm text-gray-900 dark:text-white flex items-center">
                                <svg class="w-4 h-4 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                </svg>
                                ${{ auth()->user()->userProfile->hourly_rate }}/hour
                            </dd>
                        </div>
                        @endif
                        
                        @if(auth()->user()->userProfile->response_time)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Response Time</dt>
                            <dd class="text-sm text-gray-900 dark:text-white flex items-center">
                                <svg class="w-4 h-4 text-purple-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ ucwords(str_replace('_', ' ', auth()->user()->userProfile->response_time)) }}
                            </dd>
                        </div>
                        @endif
                        
                        @if(auth()->user()->userProfile->timezone)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Timezone</dt>
                            <dd class="text-sm text-gray-900 dark:text-white flex items-center">
                                <svg class="w-4 h-4 text-indigo-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ auth()->user()->userProfile->timezone }}
                            </dd>
                        </div>
                        @endif
                        
                        @if(auth()->user()->userProfile->education_level)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Education</dt>
                            <dd class="text-sm text-gray-900 dark:text-white flex items-center">
                                <svg class="w-4 h-4 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                                </svg>
                                {{ ucwords(str_replace('_', ' ', auth()->user()->userProfile->education_level)) }}
                            </dd>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Skills -->
                @if(auth()->user()->userProfile && auth()->user()->userProfile->skills)
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Skills</h2>
                    <div class="flex flex-wrap gap-2">
                        @php
                            $skills = auth()->user()->userProfile->skills;
                            if(is_string($skills)) {
                                $skills = json_decode($skills, true) ?? [];
                            }
                        @endphp
                        @foreach($skills as $skill)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ $skill }}
                            </span>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Services -->
                @if(auth()->user()->userProfile && auth()->user()->userProfile->services)
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Services</h2>
                    <div class="flex flex-wrap gap-2">
                        @php
                            $services = auth()->user()->userProfile->services;
                            if(is_string($services)) {
                                $services = json_decode($services, true) ?? [];
                            }
                        @endphp
                        @foreach($services as $service)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                {{ $service }}
                            </span>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Portfolio -->
                @if(auth()->user()->userProfile && auth()->user()->userProfile->portfolio_links)
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Portfolio</h2>
                    <div class="space-y-4">
                        @php
                            $portfolioLinks = auth()->user()->userProfile->portfolio_links;
                            if(is_string($portfolioLinks)) {
                                $portfolioLinks = json_decode($portfolioLinks, true) ?? [];
                            }
                        @endphp
                        @foreach($portfolioLinks as $item)
                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h3 class="font-medium text-gray-900 dark:text-white mb-2">{{ $item['title'] ?? 'Portfolio Item' }}</h3>
                                        @if(isset($item['description']) && $item['description'])
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ $item['description'] }}</p>
                                        @endif
                                        @if(isset($item['url']) && $item['url'])
                                            <a href="{{ $item['url'] }}" target="_blank" class="inline-flex items-center text-sm text-blue-600 dark:text-blue-400 hover:underline">
                                                View Project
                                                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Reviews Received -->
                @php
                    $reviewsReceived = auth()->user()->contractReviewsReceived()->latest()->take(5)->get();
                    $totalReviewsReceived = auth()->user()->contractReviewsReceived()->count();
                @endphp
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Reviews Received</h2>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $totalReviewsReceived }} reviews</span>
                    </div>
                    
                    @if($reviewsReceived->count() > 0)
                        <div class="space-y-4">
                            @foreach($reviewsReceived as $review)
                                <div class="border-b border-gray-200 dark:border-gray-700 pb-4 last:border-b-0">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center mb-2">
                                                <div class="flex text-yellow-400 mr-2">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <svg class="w-3 h-3 {{ $i <= $review->rating ? 'fill-current' : 'text-gray-300 dark:text-gray-600' }}" viewBox="0 0 24 24">
                                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                        </svg>
                                                    @endfor
                                                </div>
                                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $review->reviewer->name ?? 'Anonymous' }}</span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">{{ $review->created_at->diffForHumans() }}</span>
                                            </div>
                                            @if($review->review_text)
                                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $review->review_text }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            
                            @if($totalReviewsReceived > 5)
                                <div class="text-center pt-4">
                                    <span class="text-sm text-blue-600 dark:text-blue-400 hover:underline cursor-pointer">
                                        View all {{ $totalReviewsReceived }} reviews
                                    </span>
                                </div>
                            @endif
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                </svg>
                            </div>
                            <p class="text-gray-500 dark:text-gray-400 mb-2">No reviews yet</p>
                            <p class="text-sm text-gray-400 dark:text-gray-500">Complete your first project to receive reviews</p>
                        </div>
                    @endif
                </div>

                <!-- Reviews Given -->
                @php
                    $reviewsGiven = auth()->user()->contractReviewsGiven()->latest()->take(5)->get();
                    $totalReviewsGiven = auth()->user()->contractReviewsGiven()->count();
                @endphp
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Reviews Given</h2>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $totalReviewsGiven }} reviews</span>
                    </div>
                    
                    @if($reviewsGiven->count() > 0)
                        <div class="space-y-4">
                            @foreach($reviewsGiven as $review)
                                <div class="border-b border-gray-200 dark:border-gray-700 pb-4 last:border-b-0">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center mb-2">
                                                <div class="flex text-yellow-400 mr-2">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <svg class="w-3 h-3 {{ $i <= $review->rating ? 'fill-current' : 'text-gray-300 dark:text-gray-600' }}" viewBox="0 0 24 24">
                                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                        </svg>
                                                    @endfor
                                                </div>
                                                <span class="text-sm font-medium text-gray-900 dark:text-white">Review for {{ $review->reviewedUser->name ?? 'User' }}</span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">{{ $review->created_at->diffForHumans() }}</span>
                                            </div>
                                            @if($review->review_text)
                                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $review->review_text }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            
                            @if($totalReviewsGiven > 5)
                                <div class="text-center pt-4">
                                    <span class="text-sm text-blue-600 dark:text-blue-400 hover:underline cursor-pointer">
                                        View all {{ $totalReviewsGiven }} reviews
                                    </span>
                                </div>
                            @endif
                        </div>
                    @else
                        <!-- Empty State -->
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                            </div>
                            <p class="text-gray-500 dark:text-gray-400 mb-2">No reviews written yet</p>
                            <p class="text-sm text-gray-400 dark:text-gray-500">Complete projects to leave reviews for others</p>
                        </div>
                    @endif
                </div>
                
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Verification Status -->
                @php
                    $needsKyc = !auth()->user()->isKycVerified();
                    $needsTyping = auth()->user()->isChatter() && (!auth()->user()->userProfile?->typing_speed_wpm || !auth()->user()->userProfile?->typing_test_taken_at);
                    $needsEarnings = auth()->user()->isAgency() && !auth()->user()->isEarningsVerified();
                    $hasAnyIncompleteVerification = $needsKyc || $needsTyping || $needsEarnings;
                @endphp
                <div class="bg-gradient-to-r {{ $hasAnyIncompleteVerification ? 'from-red-50 to-orange-50 dark:from-red-900/20 dark:to-orange-900/20 border-red-200 dark:border-red-700' : 'from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border-green-200 dark:border-green-700' }} border rounded-lg p-6">
                    <!-- Header -->
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r {{ $hasAnyIncompleteVerification ? 'from-red-400 to-orange-500' : 'from-green-400 to-emerald-500' }} rounded-full flex items-center justify-center">
                                @if($hasAnyIncompleteVerification)
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.864-.833-2.634 0L4.168 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                    </svg>
                                @else
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @endif
                            </div>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $hasAnyIncompleteVerification ? 'Action Required' : 'Verification Complete' }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $hasAnyIncompleteVerification ? 'Complete verification to unlock full access' : 'All verification requirements completed' }}
                            </p>
                        </div>
                    </div>
                    
                    <!-- Verifications List -->
                    @if($hasAnyIncompleteVerification)
                        <div class="space-y-3">
                            @if($needsKyc)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-700 dark:text-gray-300">KYC Verification</span>
                                    <span class="text-red-600 dark:text-red-400">Required</span>
                                </div>
                            @endif
                            @if($needsTyping)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-700 dark:text-gray-300">Typing Test</span>
                                    <span class="text-orange-600 dark:text-orange-400">Required</span>
                                </div>
                            @endif
                            @if($needsEarnings)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-700 dark:text-gray-300">Earnings Verification</span>
                                    <span class="text-purple-600 dark:text-purple-400">Required</span>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center">
                            <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                ✓ All Verified
                            </div>
                        </div>
                    @endif
                </div>
                
                
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Contact Information</h2>
                    <div class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center">
                                <svg class="w-4 h-4 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                Email
                            </dt>
                            <dd class="text-sm text-gray-900 dark:text-white ml-6">
                                <a href="mailto:{{ auth()->user()->email }}" class="text-blue-600 hover:underline">
                                    {{ auth()->user()->email }}
                                </a>
                            </dd>
                        </div>
                        
                        @if(auth()->user()->userProfile && auth()->user()->userProfile->phone)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                                Phone
                            </dt>
                            <dd class="text-sm text-gray-900 dark:text-white ml-6">
                                <a href="tel:{{ auth()->user()->userProfile->phone }}" class="text-blue-600 hover:underline">
                                    {{ auth()->user()->userProfile->phone }}
                                </a>
                            </dd>
                        </div>
                        @endif
                        
                        @if(auth()->user()->userProfile && auth()->user()->userProfile->website)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center">
                                <svg class="w-4 h-4 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                </svg>
                                Website
                            </dt>
                            <dd class="text-sm text-blue-600 dark:text-blue-400 ml-6">
                                <a href="{{ auth()->user()->userProfile->website }}" target="_blank" class="hover:underline">
                                    {{ auth()->user()->userProfile->website }}
                                </a>
                            </dd>
                        </div>
                        @endif
                        
                        @if(auth()->user()->userProfile && auth()->user()->userProfile->linkedin_url)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center">
                                <svg class="w-4 h-4 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                </svg>
                                LinkedIn
                            </dt>
                            <dd class="text-sm text-blue-600 dark:text-blue-400 ml-6">
                                <a href="{{ auth()->user()->userProfile->linkedin_url }}" target="_blank" class="hover:underline">
                                    LinkedIn Profile
                                </a>
                            </dd>
                        </div>
                        @endif
                        
                        @if(auth()->user()->userProfile && auth()->user()->userProfile->skype)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center">
                                <svg class="w-4 h-4 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.069 18.874c-4.023 0-5.82-1.979-5.82-3.464 0-.765.561-1.296 1.333-1.296 1.723 0 1.273 2.477 4.487 2.477 1.641 0 2.55-.895 2.55-1.811 0-.551-.269-1.16-1.354-1.429l-3.576-.895c-2.88-.724-3.403-2.286-3.403-3.751 0-3.047 2.861-4.191 5.549-4.191 2.471 0 5.393 1.373 5.393 3.199 0 .784-.688 1.24-1.453 1.24-1.469 0-1.198-2.037-4.164-2.037-1.469 0-2.292.664-2.292 1.617 0 .587.382 1.005 1.386 1.24l4.019.956c2.861.724 3.273 2.286 3.273 3.700.001 2.861-2.189 4.445-5.929 4.445zm8.665-11.542c-.492-1.325-1.211-2.513-2.135-3.53-.924-1.017-2.024-1.818-3.267-2.378-1.287-.583-2.656-.878-4.062-.878-1.503 0-2.964.327-4.34.972-1.329.622-2.52 1.51-3.537 2.637-1.017 1.127-1.818 2.413-2.378 3.82-.583 1.462-.878 3.013-.878 4.608 0 1.503.327 2.964.972 4.34.622 1.329 1.51 2.52 2.637 3.537 1.127 1.017 2.413 1.818 3.82 2.378 1.462.583 3.013.878 4.608.878 1.503 0 2.964-.327 4.34-.972 1.329-.622 2.52-1.51 3.537-2.637 1.017-1.127 1.818-2.413 2.378-3.82.583-1.462.878-3.013.878-4.608 0-1.503-.327-2.964-.972-4.34-.622-1.329-1.51-2.52-2.637-3.537z"/>
                                </svg>
                                Skype
                            </dt>
                            <dd class="text-sm text-gray-900 dark:text-white ml-6">
                                <a href="skype:{{ auth()->user()->userProfile->skype }}?chat" class="text-blue-600 hover:underline">
                                    {{ auth()->user()->userProfile->skype }}
                                </a>
                            </dd>
                        </div>
                        @endif
                        
                        @if(auth()->user()->userProfile && auth()->user()->userProfile->telegram)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center">
                                <svg class="w-4 h-4 text-blue-400 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                                </svg>
                                Telegram
                            </dt>
                            <dd class="text-sm text-gray-900 dark:text-white ml-6">
                                <a href="https://t.me/{{ auth()->user()->userProfile->telegram }}" target="_blank" class="text-blue-600 hover:underline">
                                    @{{ auth()->user()->userProfile->telegram }}
                                </a>
                            </dd>
                        </div>
                        @endif
                        
                        @if(auth()->user()->userProfile && auth()->user()->userProfile->location)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 flex items-center">
                                <svg class="w-4 h-4 text-purple-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Location
                            </dt>
                            <dd class="text-sm text-gray-900 dark:text-white ml-6">
                                {{ auth()->user()->userProfile->location }}
                            </dd>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Languages -->
                @if(auth()->user()->userProfile && auth()->user()->userProfile->languages)
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Languages</h2>
                    <div class="flex flex-wrap gap-2">
                        @php
                            $languages = auth()->user()->userProfile->languages;
                            if(is_string($languages)) {
                                $languages = json_decode($languages, true) ?? [];
                            }
                        @endphp
                        @foreach($languages as $language)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                {{ $language }}
                            </span>
                        @endforeach
                    </div>
                </div>
                @endif
                
            </div>
        </div>
        
    </div>
</div>

</x-layouts.app>

