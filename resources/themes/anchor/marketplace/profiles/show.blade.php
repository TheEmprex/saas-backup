<x-layouts.app>

<div class="min-h-screen bg-gray-100 dark:bg-gray-900 py-10">
    <div class="max-w-4xl mx-auto px-5 sm:px-6 lg:px-8">
        <!-- Profile Header -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 mb-6 p-6">
            <!-- Profile Info -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="flex flex-col sm:flex-row sm:items-center space-y-4 sm:space-y-0 sm:space-x-6">
                    <!-- Avatar -->
                    <div class="relative">
                        <div class="w-16 h-16 rounded-full border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 overflow-hidden">
                            @if($user->avatar ?? false)
                                <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover rounded-full">
                            @else
                                <div class="w-full h-full bg-blue-500 flex items-center justify-center">
                                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <!-- Online Status -->
                        @if($user->last_seen_at && $user->last_seen_at->diffInMinutes() < 10)
                            <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 rounded-full border-2 border-white"></div>
                        @endif
                    </div>
                        
                        <!-- Name & Title -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-3 mb-2">
                                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h1>
                                @if($user->email_verified_at)
                                    <div class="flex items-center space-x-1 bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 px-2 py-1 rounded-full text-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span>Verified</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex flex-wrap items-center space-x-4 text-gray-600 dark:text-gray-400">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="font-medium">{{ $user->userType->display_name ?? 'Professional' }}</span>
                                </div>
                                @if($profile->location ?? false)
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span>{{ $profile->location }}</span>
                                    </div>
                                @endif
                                @if($profile->hourly_rate ?? false)
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                        <span class="font-semibold text-green-600 dark:text-green-400">${{ $profile->hourly_rate }}/hr</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    @if($user->id !== auth()->id())
                        <div class="flex space-x-3 mt-4 lg:mt-0">
                            @auth
                                <a href="{{ route('messages.create', $user) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                    Message
                                </a>
                                <button type="button" onclick="openContractModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->userType->display_name }}')" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors font-medium">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    Hire
                                </button>
                            @else
                                <a href="{{ route('custom.login') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                    Login to Message
                                </a>
                                <a href="{{ route('custom.login') }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors font-medium">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    Login to Hire
                                </a>
                            @endauth
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- About Section -->
                @if($profile->bio ?? false)
                <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-sm border border-gray-200 dark:border-zinc-700 p-8">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">About</h2>
                    </div>
                    <div class="prose prose-gray dark:prose-invert max-w-none">
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{!! nl2br(e($profile->bio)) !!}</p>
                    </div>
                </div>
                @endif
                
                <!-- Skills Section -->
                @if(($profile->skills ?? false) && (is_array($profile->skills) ? !empty($profile->skills) : !empty(trim($profile->skills))))
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Skills</h2>
                    <div class="flex flex-wrap gap-2">
                        @php
                            $skillsArray = is_array($profile->skills) ? $profile->skills : explode(',', $profile->skills);
                        @endphp
                        @foreach($skillsArray as $skill)
                            @if(trim($skill))
                                <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 rounded-full text-sm">
                                    {{ trim($skill) }}
                                </span>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif
                
                <!-- Experience Section -->
                @if($profile->experience || $profile->experience_years)
                <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-sm border border-gray-200 dark:border-zinc-700 p-8">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Experience</h2>
                    </div>
                    @if($profile->experience_years)
                        <div class="mb-4 p-4 bg-purple-50 dark:bg-purple-900/20 rounded-xl border border-purple-200 dark:border-purple-800">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-lg font-semibold text-purple-700 dark:text-purple-300">{{ $profile->experience_years }} years</span>
                                <span class="text-purple-600 dark:text-purple-400">of professional experience</span>
                            </div>
                        </div>
                    @endif
                    @if($profile->experience)
                        <div class="prose prose-gray dark:prose-invert max-w-none">
                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{!! nl2br(e($profile->experience)) !!}</p>
                        </div>
                    @endif
                </div>
                @endif
                
                <!-- Services Section -->
                @if(($profile->services ?? false) && (is_array($profile->services) ? !empty($profile->services) : !empty(trim($profile->services))))
                <div class="bg-white dark:bg-zinc-800 rounded-3xl shadow-lg border border-gray-200 dark:border-zinc-700 p-8 hover:shadow-xl transition-shadow duration-300">
                    <div class="flex items-center space-x-3 mb-8">
                        <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-red-500 rounded-2xl flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Services Offered</h2>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        @php
                            $servicesArray = is_array($profile->services) ? $profile->services : explode(',', $profile->services);
                        @endphp
                        @foreach($servicesArray as $service)
                            @if(trim($service))
                                <span class="group inline-flex items-center px-4 py-3 bg-gradient-to-r from-orange-50 to-red-50 dark:from-orange-900/20 dark:to-red-900/20 text-orange-700 dark:text-orange-300 rounded-2xl text-sm font-semibold border border-orange-200 dark:border-orange-800 hover:from-orange-100 hover:to-red-100 dark:hover:from-orange-800/30 dark:hover:to-red-800/30 transition-all duration-200">
                                    <svg class="w-4 h-4 mr-2 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5a2 2 0 012 2v6a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h2z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5h2a2 2 0 012 2v6a2 2 0 01-2 2h-2a2 2 0 01-2-2V7a2 2 0 012-2z"></path>
                                    </svg>
                                    {{ trim($service) }}
                                </span>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif
                
                <!-- Education Section -->
                @if($profile->education ?? false)
                <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-sm border border-gray-200 dark:border-zinc-700 p-8">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Education</h2>
                    </div>
                    <div class="prose prose-gray dark:prose-invert max-w-none">
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{!! nl2br(e($profile->education)) !!}</p>
                    </div>
                </div>
                @endif
                
                <!-- Portfolio Section -->
                @if($profile->portfolio_url ?? false)
                <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-sm border border-gray-200 dark:border-zinc-700 p-8">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Portfolio</h2>
                    </div>
                    <a href="{{ $profile->portfolio_url }}" target="_blank" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-pink-500 to-pink-600 text-white font-semibold rounded-xl hover:from-pink-600 hover:to-pink-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                        View Portfolio
                    </a>
                </div>
                @endif
                
                <!-- Languages Section -->
                @if(($profile->languages ?? false) && (is_array($profile->languages) ? !empty($profile->languages) : !empty(trim($profile->languages))))
                <div class="bg-white dark:bg-zinc-800 rounded-3xl shadow-lg border border-gray-200 dark:border-zinc-700 p-8 hover:shadow-xl transition-shadow duration-300">
                    <div class="flex items-center space-x-3 mb-8">
                        <div class="w-12 h-12 bg-gradient-to-br from-teal-500 to-cyan-500 rounded-2xl flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Languages</h2>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        @php
                            $languagesArray = is_array($profile->languages) ? $profile->languages : explode(',', $profile->languages);
                        @endphp
                        @foreach($languagesArray as $language)
                            @if(trim($language))
                                <span class="group inline-flex items-center px-4 py-3 bg-gradient-to-r from-teal-50 to-cyan-50 dark:from-teal-900/20 dark:to-cyan-900/20 text-teal-700 dark:text-teal-300 rounded-2xl text-sm font-semibold border border-teal-200 dark:border-teal-800 hover:from-teal-100 hover:to-cyan-100 dark:hover:from-teal-800/30 dark:hover:to-cyan-800/30 transition-all duration-200">
                                    <svg class="w-4 h-4 mr-2 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                    </svg>
                                    {{ trim($language) }}
                                </span>
                            @endif
                        @endforeach
                    </div>
                </div>
@endif
                
                <!-- Reviews -->
                @if($user->contractReviewsReceived->count() > 0)
                <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-sm border border-gray-200 dark:border-zinc-700 p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Reviews ({{ $user->contractReviewsReceived->count() }})</h2>
                                <p class="text-sm text-gray-500 dark:text-gray-400">All reviews received from contracts</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-6">
                        @foreach($user->contractReviewsReceived as $contractReview)
                            <div class="border-b border-gray-200 dark:border-zinc-700 pb-6 last:border-b-0 last:pb-0">
                                <div class="flex items-start space-x-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-gray-400 to-gray-500 rounded-xl flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-2">
                                            <div>
                                                <div class="font-semibold text-gray-900 dark:text-white">{{ $contractReview->reviewer->name }}</div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $contractReview->created_at->diffForHumans() }}</div>
                                            </div>
                                            <div class="flex items-center space-x-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $contractReview->rating)
                                                        <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976-2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                                        </svg>
                                                    @else
                                                        <svg class="w-4 h-4 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-1.176 0l-3.976-2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                                        </svg>
                                                    @endif
                                                @endfor
                                            </div>
                                        </div>
                                        @if($contractReview->comment)
                                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $contractReview->comment }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-8">
                <!-- Profile Stats -->
                <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-sm border border-gray-200 dark:border-zinc-700 p-8">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Profile Stats</h2>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <!-- Rating -->
                        <div class="text-center">
                            <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">Rating</div>
                            <div class="flex items-center justify-center space-x-1 mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= ($stats['average_rating'] ?? 0))
                                        <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976-2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976-2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                        </svg>
                                    @endif
                                @endfor
                            </div>
                            <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $stats['average_rating'] ?? '0.0' }}</div>
                        </div>
                        
                        <!-- Reviews -->
                        <div class="text-center">
                            <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">Reviews</div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ $stats['total_reviews'] ?? 0 }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">total reviews</div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <!-- Jobs Completed -->
                        <div class="text-center">
                            <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">Jobs Completed</div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ $stats['jobs_completed'] ?? 0 }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">successfully</div>
                        </div>
                        
                        <!-- Response Time -->
                        <div class="text-center">
                            <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">Response Time</div>
                            <div class="text-lg font-bold text-gray-900 dark:text-white mb-1">{{ $stats['response_time'] ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">avg response</div>
                        </div>
                    </div>
                    
                    <!-- Member Since -->
                    <div class="border-t border-gray-200 dark:border-zinc-700 pt-6">
                        @if($user->isChatter() || $user->isVA())
                            <!-- Training and Performance Metrics -->
                            <div class="mb-6">
                                <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-3">Training & Performance</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    @if($user->isChatter())
                                        <!-- WPM -->
                                        <div class="text-center">
                                            <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">Typing Speed</div>
                                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400 mb-1">
                                                {{ $user->userTestResults()->typingTests()->passed()->latest()->first()->wpm ?? 'N/A' }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">WPM</div>
                                        </div>
                                    @endif
                                    
                                    <!-- Training Modules -->
                                    <div class="text-center">
                                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">Training Progress</div>
                                        <div class="text-2xl font-bold text-green-600 dark:text-green-400 mb-1">
                                            {{ $user->trainingProgress()->completed()->count() }}/{{ App\Models\TrainingModule::where('is_active', true)->count() }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">modules completed</div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Member Since</span>
                            </div>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $user->created_at->format('F Y') }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <div class="w-2 h-2 bg-{{ $user->last_seen_at && $user->last_seen_at->diffInMinutes() < 10 ? 'green' : 'gray' }}-500 rounded-full"></div>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Last Seen</span>
                            </div>
                            <span class="font-semibold text-gray-900 dark:text-white text-sm">
                                @if($user->last_seen_at && $user->last_seen_at->diffInMinutes() < 10)
                                    Online now
                                @elseif($user->last_seen_at)
                                    {{ $user->last_seen_at->diffForHumans() }}
                                @else
                                    Never
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Verification Status -->
                <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-sm border border-gray-200 dark:border-zinc-700 p-8">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Verification</h2>
                    </div>
                    
                    <div class="space-y-4">
                        <!-- Email Verified -->
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-zinc-900 rounded-xl">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-{{ $user->email_verified_at ? 'green' : 'red' }}-500 rounded-lg flex items-center justify-center">
                                    @if($user->email_verified_at)
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    @endif
                                </div>
                                <span class="font-medium text-gray-900 dark:text-white">Email Verified</span>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $user->email_verified_at ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300' }}">
                                {{ $user->email_verified_at ? 'Verified' : 'Not Verified' }}
                            </span>
                        </div>
                        
                        <!-- KYC Status -->
                        @php
                            $kycStatus = $user->kycVerification?->status ?? 'not_submitted';
                            $kycStatusLabel = match($kycStatus) {
                                'approved' => 'Verified',
                                'pending' => 'Pending',
                                'rejected' => 'Rejected',
                                'requires_review' => 'Under Review',
                                default => 'Not Submitted'
                            };
                            $kycStatusColor = match($kycStatus) {
                                'approved' => 'green',
                                'pending' => 'yellow',
                                'rejected' => 'red',
                                'requires_review' => 'orange',
                                default => 'red'
                            };
                        @endphp
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-zinc-900 rounded-xl">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-{{ $kycStatusColor }}-500 rounded-lg flex items-center justify-center">
                                    @if($kycStatus === 'approved')
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    @elseif(in_array($kycStatus, ['pending', 'requires_review']))
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    @endif
                                </div>
                                <span class="font-medium text-gray-900 dark:text-white">KYC Status</span>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $kycStatus === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300' : (in_array($kycStatus, ['pending', 'requires_review']) ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300' : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300') }}">
                                {{ $kycStatusLabel }}
                            </span>
                        </div>
                        
                        <!-- Profile Complete -->
                        @php
                            $profileComplete = $stats['profile_complete'] ?? false;
                            $completenessPercentage = $stats['profile_completeness_percentage'] ?? 0;
                        @endphp
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-zinc-900 rounded-xl">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-{{ $profileComplete ? 'green' : 'yellow' }}-500 rounded-lg flex items-center justify-center">
                                    @if($profileComplete)
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @endif
                                </div>
                                <span class="font-medium text-gray-900 dark:text-white">Profile Complete</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $completenessPercentage }}%</span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $profileComplete ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300' }}">
                                    {{ $profileComplete ? 'Complete' : 'Incomplete' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Contract Modal -->
<div id="contractModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black bg-opacity-50">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-2xl">
            <!-- Modal Content -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Create Contract
                    </h3>
                    <button type="button" onclick="document.getElementById('contractModal').style.display='none'" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <form id="contractForm" action="{{ route('contracts.store') }}" method="POST">
                @csrf
                <div class="px-6 py-4 space-y-6">
                    <input type="hidden" name="contractor_id" id="contractContractorId">
                    
                    <!-- Contractor Display -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Contractor</label>
                        <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="w-10 h-10 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white" id="contractContractorName"></div>
                                <div class="text-sm text-gray-500 dark:text-gray-400" id="contractContractorType"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contract Type -->
                    <div>
                        <label for="contractType" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Contract Type</label>
                        <select name="contract_type" id="contractType" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white" required>
                            <option value="">Select contract type</option>
                            <option value="hourly">Hourly</option>
                            <option value="fixed">Fixed</option>
                            <option value="commission">Commission</option>
                        </select>
                    </div>
                    
                    <!-- Rate -->
                    <div>
                        <label for="contractRate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rate</label>
                        <input type="number" name="rate" id="contractRate" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white" step="0.01" min="0" placeholder="0.00" required>
                    </div>
                    
                    <!-- Start Date -->
                    <div>
                        <label for="contractStartDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Date</label>
                        <input type="date" name="start_date" id="contractStartDate" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white" required>
                    </div>
                    
                    <!-- Contract Description -->
                    <div>
                        <label for="contractDescription" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                        <textarea name="description" id="contractDescription" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white" rows="4" placeholder="Describe the work to be done..." required></textarea>
                    </div>
                </div>
                
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end space-x-3">
                    <button type="button" onclick="document.getElementById('contractModal').style.display='none'" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors" id="createContractBtn">
                        Create Contract
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>

// Contract Modal Functions
function openContractModal(userId, userName, userType) {
    // Set contractor information
    document.getElementById('contractContractorId').value = userId;
    document.getElementById('contractContractorName').textContent = userName;
    document.getElementById('contractContractorType').textContent = userType;
    
    // Clear form
    document.getElementById('contractType').value = '';
    document.getElementById('contractRate').value = '';
    document.getElementById('contractStartDate').value = '';
    document.getElementById('contractDescription').value = '';
    
    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('contractStartDate').setAttribute('min', today);
    
    // Show modal
    document.getElementById('contractModal').style.display = 'block';
}

// Contract form submission with loading state
document.getElementById('contractForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('createContractBtn');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.innerHTML = '<i class="ti ti-loader-2 me-1 spinner-border spinner-border-sm"></i> Creating...';
    submitBtn.disabled = true;
    
    // Form will submit normally, but we provide visual feedback
    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 3000);
});

// Close modals when clicking outside
document.addEventListener('click', function(e) {
    if (e.target.id === 'contractModal') {
        document.getElementById('contractModal').style.display = 'none';
    }
});

// Close modal buttons
document.querySelectorAll('.btn-close, [data-bs-dismiss="modal"]').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.getElementById('contractModal').style.display = 'none';
    });
});

// View All Reviews function
function viewAllReviews() {
    const userId = {{ $user->id }};
    window.location.href = '/marketplace/profiles/' + userId + '/reviews';
}
</script>

</x-layouts.app>
