@extends('theme::app')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-zinc-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Profile Header -->
        <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-sm border border-gray-200 dark:border-zinc-700 mb-8 overflow-hidden">
            <!-- Cover Photo -->
            <div class="h-32 bg-gradient-to-r from-blue-500 to-purple-600 relative">
                <div class="absolute inset-0 bg-black bg-opacity-20"></div>
                <div class="absolute top-4 right-4">
                    @if($profile->is_available ?? true)
                        <div class="flex items-center space-x-2 bg-green-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                            <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
                            <span>Available</span>
                        </div>
                    @else
                        <div class="flex items-center space-x-2 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                            <div class="w-2 h-2 bg-white rounded-full"></div>
                            <span>Busy</span>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Profile Info -->
            <div class="px-8 pb-8">
                <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between">
                    <div class="flex flex-col sm:flex-row sm:items-end space-y-4 sm:space-y-0 sm:space-x-6 -mt-16">
                        <!-- Avatar -->
                        <div class="relative">
                            <div class="w-32 h-32 rounded-2xl border-4 border-white dark:border-zinc-800 bg-white dark:bg-zinc-800 shadow-lg overflow-hidden">
                                @if($profile->avatar ?? false)
                                    <img src="{{ $profile->avatar }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                                        <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <!-- Online Status -->
                            @if($user->last_seen_at && $user->last_seen_at->diffInMinutes() < 10)
                                <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-green-500 rounded-full border-4 border-white dark:border-zinc-800 flex items-center justify-center">
                                    <div class="w-3 h-3 bg-white rounded-full animate-pulse"></div>
                                </div>
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
                        <div class="flex space-x-3 mt-6 lg:mt-0">
                            <button type="button" onclick="openMessageModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->userType->display_name }}')" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-colors duration-200 shadow-lg hover:shadow-xl">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                Send Message
                            </button>
                            <a href="{{ route('marketplace.jobs.create', ['user' => $user->id]) }}" class="inline-flex items-center px-6 py-3 bg-white dark:bg-zinc-700 text-gray-900 dark:text-white font-semibold rounded-xl border border-gray-200 dark:border-zinc-600 hover:bg-gray-50 dark:hover:bg-zinc-600 transition-colors duration-200 shadow-lg hover:shadow-xl">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                Hire for Job
                            </a>
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
                @if($profile->skills ?? false)
                <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-sm border border-gray-200 dark:border-zinc-700 p-8">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Skills & Expertise</h2>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        @foreach(explode(',', $profile->skills) as $skill)
                            <span class="inline-flex items-center px-4 py-2 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300 rounded-full text-sm font-medium border border-green-200 dark:border-green-800">
                                {{ trim($skill) }}
                            </span>
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
                @if($profile->services ?? false)
                <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-sm border border-gray-200 dark:border-zinc-700 p-8">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Services Offered</h2>
                    </div>
                    <div class="prose prose-gray dark:prose-invert max-w-none">
                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{!! nl2br(e($profile->services)) !!}</p>
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
                @if($profile->languages ?? false)
                <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-sm border border-gray-200 dark:border-zinc-700 p-8">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Languages</h2>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        @foreach(explode(',', $profile->languages) as $language)
                            <span class="inline-flex items-center px-4 py-2 bg-teal-50 dark:bg-teal-900/20 text-teal-700 dark:text-teal-300 rounded-full text-sm font-medium border border-teal-200 dark:border-teal-800">
                                {{ trim($language) }}
                            </span>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <!-- Reviews Section -->
                <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-sm border border-gray-200 dark:border-zinc-700 p-8">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-10 h-10 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Reviews ({{ $profile->total_ratings ?? 0 }})</h2>
                    </div>
                    
                    @forelse($reviews ?? [] as $review)
                        <div class="border-b border-gray-200 dark:border-zinc-700 pb-6 mb-6 last:border-b-0 last:pb-0 last:mb-0">
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-gray-400 to-gray-500 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-2">
                                        <div>
                                            <div class="font-semibold text-gray-900 dark:text-white">{{ $review->reviewer->name }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $review->created_at->diffForHumans() }}</div>
                                        </div>
                                        <div class="flex items-center space-x-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $review->rating)
                                                    <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                                    </svg>
                                                @else
                                                    <svg class="w-4 h-4 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                                    </svg>
                                                @endif
                                            @endfor
                                        </div>
                                    </div>
                                    @if($review->comment)
                                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{!! nl2br(e($review->comment)) !!}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-gradient-to-br from-gray-400 to-gray-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">No reviews yet</h3>
                            <p class="text-gray-600 dark:text-gray-400">Be the first to leave a review for this professional</p>
                        </div>
                    @endforelse
                    
                    @if(isset($reviews) && $reviews->hasPages())
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-zinc-700">
                            {{ $reviews->links() }}
                        </div>
                    @endif
                </div>
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
                                    @if($i <= ($profile->average_rating ?? 0))
                                        <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                        </svg>
                                    @endif
                                @endfor
                            </div>
                            <div class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($profile->average_rating ?? 0, 1) }}</div>
                        </div>
                        
                        <!-- Reviews -->
                        <div class="text-center">
                            <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">Reviews</div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ $profile->total_ratings ?? 0 }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">total reviews</div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <!-- Jobs Completed -->
                        <div class="text-center">
                            <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">Jobs Completed</div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ $profile->jobs_completed ?? 0 }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">successfully</div>
                        </div>
                        
                        <!-- Response Time -->
                        <div class="text-center">
                            <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">Response Time</div>
                            <div class="text-lg font-bold text-gray-900 dark:text-white mb-1">{{ $profile->response_time ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">avg response</div>
                        </div>
                    </div>
                    
                    <!-- Member Since -->
                    <div class="border-t border-gray-200 dark:border-zinc-700 pt-6">
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
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-zinc-900 rounded-xl">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-{{ ($user->kyc_status ?? 'none') === 'approved' ? 'green' : (($user->kyc_status ?? 'none') === 'pending' ? 'yellow' : 'red') }}-500 rounded-lg flex items-center justify-center">
                                    @if(($user->kyc_status ?? 'none') === 'approved')
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    @elseif(($user->kyc_status ?? 'none') === 'pending')
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
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ ($user->kyc_status ?? 'none') === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300' : (($user->kyc_status ?? 'none') === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300' : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300') }}">
                                {{ ucfirst($user->kyc_status ?? 'Not Verified') }}
                            </span>
                        </div>
                        
                        <!-- Profile Complete -->
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-zinc-900 rounded-xl">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-{{ ($profile->is_complete ?? false) ? 'green' : 'yellow' }}-500 rounded-lg flex items-center justify-center">
                                    @if($profile->is_complete ?? false)
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
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ ($profile->is_complete ?? false) ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300' }}">
                                {{ ($profile->is_complete ?? false) ? 'Complete' : 'Incomplete' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Message Modal -->
<div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageModalLabel">
                    <i class="ti ti-message-circle me-2"></i>
                    Send Message
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="messageForm" action="{{ route('marketplace.messages.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="conversation_id" id="recipientId">
                    
                    <!-- Recipient Display -->
                    <div class="mb-3">
                        <label class="form-label">To</label>
                        <div class="d-flex align-items-center p-2 bg-light rounded">
                            <div class="avatar avatar-sm me-3" style="background-image: url('/images/default-avatar.png')"></div>
                            <div>
                                <div class="fw-medium" id="recipientName"></div>
                                <div class="text-muted small" id="recipientType"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Message Input -->
                    <div class="mb-3">
                        <label for="messageContent" class="form-label">Message</label>
                        <textarea name="content" id="messageContent" class="form-control" rows="6" placeholder="Type your message..." required></textarea>
                        <div class="form-text">Be professional and clear in your communication.</div>
                    </div>
                    
                    <!-- File Attachment -->
                    <div class="mb-3">
                        <label for="messageAttachment" class="form-label">
                            <i class="ti ti-paperclip me-1"></i>
                            Attachment (Optional)
                        </label>
                        <input type="file" name="attachment" id="messageAttachment" class="form-control" accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.gif,.zip">
                        <div class="form-text">Supported formats: PDF, DOC, DOCX, TXT, JPG, PNG, GIF, ZIP. Max size: 10MB</div>
                    </div>
                    
                    <!-- File Preview -->
                    <div id="filePreview" class="mb-3" style="display: none;">
                        <div class="alert alert-info d-flex align-items-center">
                            <i class="ti ti-file me-2"></i>
                            <span id="fileName"></span>
                            <button type="button" class="btn-close ms-auto" onclick="clearFilePreview()"></button>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ti ti-x me-1"></i>
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="sendMessageBtn">
                        <i class="ti ti-send me-1"></i>
                        Send Message
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openMessageModal(userId, userName, userType) {
    // Set recipient information
    document.getElementById('recipientId').value = userId;
    document.getElementById('recipientName').textContent = userName;
    document.getElementById('recipientType').textContent = userType;
    
    // Clear form
    document.getElementById('messageContent').value = '';
    document.getElementById('messageAttachment').value = '';
    clearFilePreview();
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('messageModal'));
    modal.show();
}

function clearFilePreview() {
    document.getElementById('filePreview').style.display = 'none';
    document.getElementById('fileName').textContent = '';
    document.getElementById('messageAttachment').value = '';
}

// File attachment preview
document.getElementById('messageAttachment').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        document.getElementById('fileName').textContent = file.name + ' (' + formatFileSize(file.size) + ')';
        document.getElementById('filePreview').style.display = 'block';
    } else {
        clearFilePreview();
    }
});

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Form submission with loading state
document.getElementById('messageForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('sendMessageBtn');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.innerHTML = '<i class="ti ti-loader-2 me-1 spinner-border spinner-border-sm"></i> Sending...';
    submitBtn.disabled = true;
    
    // Form will submit normally, but we provide visual feedback
    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 3000);
});
</script>
@endpush
@endsection
