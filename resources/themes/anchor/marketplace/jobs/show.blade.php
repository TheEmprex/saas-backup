<x-layouts.marketing
    :seo="[
        'title'         => $job->title . ' - OnlyFans Management Marketplace',
        'description'   => Str::limit($job->description, 160),
        'image'         => url('/og_image.png'),
        'type'          => 'website'
    ]"
>

<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
    <!-- Header Section -->
    <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('marketplace.jobs.index') }}" class="flex items-center text-gray-600 hover:text-blue-600 transition-colors duration-200 group">
                        <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Jobs
                    </a>
                </div>
                <div class="flex items-center space-x-2">
                    @if($job->is_featured)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gradient-to-r from-yellow-400 to-orange-500 text-white shadow-lg">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            Featured
                        </span>
                    @endif
                    @if($job->is_urgent)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gradient-to-r from-red-500 to-pink-600 text-white shadow-lg animate-pulse">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            Urgent
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Job Header Card -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-8 mb-6">
                    <div class="mb-6">
                        <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $job->title }}</h1>
                        
                        <div class="flex items-center flex-wrap gap-4 mb-6">
                            <div class="flex items-center text-gray-600">
                                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold mr-3">
                                    {{ substr($job->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900">{{ $job->user->name }}</div>
                                    <div class="text-sm text-gray-500">Posted {{ $job->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                            
                            @if($job->user->userProfile && $job->user->userProfile->average_rating > 0)
                                <div class="flex items-center bg-yellow-50 px-3 py-1 rounded-full">
                                    <div class="flex text-yellow-400 mr-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($job->user->userProfile->average_rating))
                                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4 fill-current text-gray-300" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">
                                        {{ number_format($job->user->userProfile->average_rating, 1) }}
                                        ({{ $job->user->userProfile->total_ratings }} reviews)
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Rate Display -->
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-6 mb-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Compensation</h3>
                                    <div class="text-3xl font-bold text-green-600">
                                        @if($job->rate_type === 'hourly')
                                            ${{ number_format($job->hourly_rate, 2) }}<span class="text-lg text-gray-500">/hour</span>
                                        @elseif($job->rate_type === 'fixed')
                                            ${{ number_format($job->fixed_rate, 2) }}<span class="text-lg text-gray-500"> fixed</span>
                                        @else
                                            {{ $job->commission_percentage }}%<span class="text-lg text-gray-500"> commission</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-gray-600">Contract Type</div>
                                    <div class="font-semibold text-gray-900 capitalize">{{ $job->contract_type }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Tags -->
                        <div class="flex flex-wrap gap-3 mb-6">
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                                    <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z"></path>
                                </svg>
                                {{ ucfirst($job->market) }}
                            </span>
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-gradient-to-r from-green-500 to-green-600 text-white shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ ucfirst($job->experience_level) }}
                            </span>
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-gradient-to-r from-purple-500 to-purple-600 text-white shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                </svg>
                                {{ ucfirst($job->contract_type) }}
                            </span>
                            @if($job->min_typing_speed)
                                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-gradient-to-r from-gray-500 to-gray-600 text-white shadow-sm">
                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 5a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2h-2.22l.123.489.804.804A1 1 0 0113 18H7a1 1 0 01-.707-1.707l.804-.804L7.22 15H5a2 2 0 01-2-2V5zm5.771 7H5V5h10v7H8.771z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $job->min_typing_speed }}+ WPM
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Job Description -->
                    <div class="bg-gray-50 rounded-xl p-6 mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Job Description
                        </h2>
                        <div class="text-gray-700 leading-relaxed whitespace-pre-line prose max-w-none">
                            {{ $job->description }}
                        </div>
                    </div>

                    @if($job->requirements)
                    <div class="bg-blue-50 rounded-xl p-6 mb-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Requirements
                        </h3>
                        <div class="text-gray-700 leading-relaxed whitespace-pre-line prose max-w-none">
                            {{ $job->requirements }}
                        </div>
                    </div>
                    @endif

                    @if($job->benefits)
                    <div class="bg-green-50 rounded-xl p-6 mb-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Benefits
                        </h3>
                        <div class="text-gray-700 leading-relaxed whitespace-pre-line prose max-w-none">
                            {{ $job->benefits }}
                        </div>
                    </div>
                    @endif

                    <!-- Job Details Grid -->
                    <div class="bg-white border border-gray-200 rounded-xl p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                            <svg class="w-6 h-6 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Job Details
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <span class="font-semibold text-gray-900">Applications</span>
                                </div>
                                <div class="text-2xl font-bold text-gray-900">
                                    {{ $job->current_applications }}<span class="text-gray-500 text-lg">/{{ $job->max_applications }}</span>
                                </div>
                                <div class="text-sm text-gray-600 mt-1">
                                    @php
                                        $percentage = $job->max_applications > 0 ? ($job->current_applications / $job->max_applications) * 100 : 0;
                                    @endphp
                                    {{ number_format($percentage, 1) }}% filled
                                </div>
                            </div>
                            
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="font-semibold text-gray-900">Expected Hours</span>
                                </div>
                                <div class="text-2xl font-bold text-gray-900">
                                    {{ $job->expected_hours_per_week ?? $job->hours_per_week }}<span class="text-gray-500 text-lg">/week</span>
                                </div>
                                <div class="text-sm text-gray-600 mt-1">Weekly commitment</div>
                            </div>
                            
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4m-6 0v1h6V7M8 7h8M5 10h14l-1 7a1 1 0 01-1 1H7a1 1 0 01-1-1l-1-7z"></path>
                                    </svg>
                                    <span class="font-semibold text-gray-900">Start Date</span>
                                </div>
                                <div class="text-lg font-bold text-gray-900">
                                    {{ $job->start_date ? $job->start_date->format('M j, Y') : 'Immediate' }}
                                </div>
                                <div class="text-sm text-gray-600 mt-1">
                                    @if($job->start_date)
                                        {{ $job->start_date->diffForHumans() }}
                                    @else
                                        Ready to start
                                    @endif
                                </div>
                            </div>
                            
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <svg class="w-5 h-5 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    <span class="font-semibold text-gray-900">Duration</span>
                                </div>
                                <div class="text-lg font-bold text-gray-900">
                                    {{ $job->duration_months ? $job->duration_months . ' months' : 'Not specified' }}
                                </div>
                                <div class="text-sm text-gray-600 mt-1">
                                    @if($job->duration_months)
                                        {{ $job->duration_months }} month{{ $job->duration_months > 1 ? 's' : '' }} project
                                    @else
                                        Flexible duration
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Timezone & Availability Section -->
                    @if($job->required_timezone || $job->timezone_flexible || $job->required_days || $job->preferred_start_time || $job->preferred_end_time)
                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 border border-indigo-200 rounded-xl p-6 mt-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                            <svg class="w-6 h-6 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Timezone & Availability
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Timezone Requirements -->
                            @if($job->required_timezone || $job->timezone_flexible)
                            <div class="bg-white/70 backdrop-blur-sm rounded-lg p-4">
                                <div class="flex items-center mb-3">
                                    <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="font-semibold text-gray-900">Timezone Requirements</span>
                                </div>
                                
                                @if($job->required_timezone)
                                    <div class="mb-2">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                                            @php
                                                $timezoneLabels = [
                                                    'UTC' => '🌐 UTC',
                                                    'America/New_York' => '🇺🇸 Eastern Time',
                                                    'America/Chicago' => '🇺🇸 Central Time',
                                                    'America/Denver' => '🇺🇸 Mountain Time',
                                                    'America/Los_Angeles' => '🇺🇸 Pacific Time',
                                                    'Europe/London' => '🇬🇧 London',
                                                    'Europe/Paris' => '🇫🇷 Paris',
                                                    'Europe/Berlin' => '🇩🇪 Berlin',
                                                    'Asia/Tokyo' => '🇯🇵 Tokyo',
                                                    'Asia/Shanghai' => '🇨🇳 Shanghai',
                                                    'Australia/Sydney' => '🇦🇺 Sydney'
                                                ];
                                            @endphp
                                            {{ $timezoneLabels[$job->required_timezone] ?? $job->required_timezone }}
                                        </span>
                                    </div>
                                @endif
                                
                                @if($job->timezone_flexible)
                                    <div class="flex items-center text-green-700">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-sm font-medium">🌐 Timezone Flexible</span>
                                    </div>
                                @endif
                            </div>
                            @endif
                            
                            <!-- Working Hours -->
                            @if($job->preferred_start_time || $job->preferred_end_time)
                            <div class="bg-white/70 backdrop-blur-sm rounded-lg p-4">
                                <div class="flex items-center mb-3">
                                    <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="font-semibold text-gray-900">Preferred Hours</span>
                                </div>
                                
                                <div class="space-y-2">
                                    @if($job->preferred_start_time)
                                        <div class="flex items-center">
                                            <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                            <span class="text-sm text-gray-700">
                                                <strong>Start:</strong> {{ $job->preferred_start_time->format('g:i A') }}
                                            </span>
                                        </div>
                                    @endif
                                    
                                    @if($job->preferred_end_time)
                                        <div class="flex items-center">
                                            <span class="w-2 h-2 bg-red-500 rounded-full mr-3"></span>
                                            <span class="text-sm text-gray-700">
                                                <strong>End:</strong> {{ $job->preferred_end_time->format('g:i A') }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                
                                @if($job->preferred_start_time && $job->preferred_end_time)
                                    @php
                                        $start = \Carbon\Carbon::parse($job->preferred_start_time);
                                        $end = \Carbon\Carbon::parse($job->preferred_end_time);
                                        $duration = $start->diffInHours($end);
                                    @endphp
                                    <div class="mt-2 text-xs text-gray-500">
                                        ⏱️ {{ $duration }} hour{{ $duration != 1 ? 's' : '' }} daily
                                    </div>
                                @endif
                            </div>
                            @endif
                        </div>
                        
                        <!-- Working Days -->
                        @if($job->required_days)
                            <div class="mt-6">
                                <div class="flex items-center mb-3">
                                    <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="font-semibold text-gray-900">Required Working Days</span>
                                </div>
                                
                                <div class="flex flex-wrap gap-2">
                                    @php
                                        $dayLabels = [
                                            'monday' => 'Monday',
                                            'tuesday' => 'Tuesday',
                                            'wednesday' => 'Wednesday',
                                            'thursday' => 'Thursday',
                                            'friday' => 'Friday',
                                            'saturday' => 'Saturday',
                                            'sunday' => 'Sunday'
                                        ];
                                        $requiredDays = is_array($job->required_days) ? $job->required_days : json_decode($job->required_days, true) ?? [];
                                    @endphp
                                    
                                    @foreach($dayLabels as $day => $label)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ in_array($day, $requiredDays) ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-400' }}">
                                            📅 {{ $label }}
                                        </span>
                                    @endforeach
                                </div>
                                
                                <div class="mt-2 text-xs text-gray-500">
                                    {{ count($requiredDays) }} day{{ count($requiredDays) != 1 ? 's' : '' }} per week required
                                </div>
                            </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white border border-gray-200 rounded-lg p-6 sticky top-6">
                    @auth
                        @if(auth()->user()->id !== $job->user_id)
                            @php
                                $userApplication = $job->applications()->where('user_id', auth()->id())->first();
                                $hasApplied = $userApplication !== null;
                            @endphp
                            
                            
                            @if($hasApplied)
                                <div class="text-center">
                                    @if($userApplication->status === 'withdrawn')
                                        <div class="bg-orange-100 text-orange-800 px-4 py-2 rounded-md mb-4">
                                            ⚠️ Application Withdrawn
                                        </div>
                                        <p class="text-sm text-gray-600">You have withdrawn your application and cannot re-apply to this job.</p>
                                    @else
                                        <div class="bg-green-100 text-green-800 px-4 py-2 rounded-md mb-4">
                                            ✓ Application Submitted
                                        </div>
                                        <p class="text-sm text-gray-600">You have already applied to this job.</p>
                                    @endif
                                </div>
                            @elseif($job->current_applications >= $job->max_applications)
                                <div class="text-center">
                                    <div class="bg-red-100 text-red-800 px-4 py-2 rounded-md mb-4">
                                        Job Full
                                    </div>
                                    <p class="text-sm text-gray-600">This job has reached its maximum applications.</p>
                                </div>
                            @else
                                @if(auth()->user()->requiresVerification())
                                    <div class="text-center">
                                        @if(auth()->user()->isChatter())
                                            <div class="bg-red-100 text-red-800 px-4 py-2 rounded-md mb-4">
                                                <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                </svg>
                                                KYC Verification Required
                                            </div>
                                            <p class="text-sm text-gray-600 mb-4">You must complete KYC verification before applying to jobs.</p>
                                            @if(!auth()->user()->hasKycSubmitted())
                                                <a href="{{ route('profile.kyc') }}" class="inline-block bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors">
                                                    Complete KYC Verification
                                                </a>
                                            @else
                                                <div class="bg-yellow-100 text-yellow-800 px-4 py-2 rounded-md mb-4">
                                                    <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    KYC Verification Pending
                                                </div>
                                                <p class="text-sm text-gray-600">Your KYC verification is being reviewed. You'll be able to apply once it's approved.</p>
                                            @endif
                                        @elseif(auth()->user()->isAgency())
                                            <div class="bg-red-100 text-red-800 px-4 py-2 rounded-md mb-4">
                                                <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                </svg>
                                                Earnings Verification Required
                                            </div>
                                            <p class="text-sm text-gray-600 mb-4">You must complete earnings verification before applying to jobs.</p>
                                            @if(!auth()->user()->hasEarningsSubmitted())
                                                <a href="{{ route('profile.earnings-verification') }}" class="inline-block bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors">
                                                    Complete Earnings Verification
                                                </a>
                                            @else
                                                <div class="bg-yellow-100 text-yellow-800 px-4 py-2 rounded-md mb-4">
                                                    <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Earnings Verification Pending
                                                </div>
                                                <p class="text-sm text-gray-600">Your earnings verification is being reviewed. You'll be able to apply once it's approved.</p>
                                            @endif
                                        @endif
                                    </div>
                                @else
                                    <form id="job-application-form" action="{{ route('marketplace.jobs.apply', $job->id) }}" method="POST">
                                        @csrf
                                        <h3 class="text-lg font-semibold mb-4">Apply for this job</h3>
                                        
                                        <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-4">
                                            <div class="flex items-center">
                                                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span class="text-sm text-green-800">
                                                    @if(auth()->user()->isChatter())
                                                        KYC Verified - Ready to Apply
                                                    @elseif(auth()->user()->isAgency())
                                                        Earnings Verified - Ready to Apply
                                                    @else
                                                        Verified - Ready to Apply
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Cover Letter</label>
                                            <textarea 
                                                name="cover_letter" 
                                                rows="4" 
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                placeholder="Tell the employer why you're perfect for this job..."
                                                required
                                            ></textarea>
                                        </div>

                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Proposed Rate
                                                @if($job->rate_type === 'hourly')
                                                    (per hour)
                                                @elseif($job->rate_type === 'fixed')
                                                    (total)
                                                @else
                                                    (commission %)
                                                @endif
                                            </label>
                                            <div class="relative">
                                                @if($job->rate_type !== 'commission')
                                                    <span class="absolute left-3 top-2 text-gray-500">$</span>
                                                    <input 
                                                        type="number" 
                                                        name="proposed_rate" 
                                                        class="w-full pl-8 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                        step="0.01"
                                                        required
                                                    >
                                                @else
                                                    <input 
                                                        type="number" 
                                                        name="proposed_rate" 
                                                        class="w-full pr-8 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                        step="0.01"
                                                        max="100"
                                                        required
                                                    >
                                                    <span class="absolute right-3 top-2 text-gray-500">%</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Available Hours per Week</label>
                                            <input 
                                                type="number" 
                                                name="available_hours" 
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                min="1"
                                                max="160"
                                                required
                                            >
                                        </div>

                                        <button type="submit" id="submit-application" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">
                                            <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Submit Application
                                        </button>
                                    </form>
                                @endif
                            @endif
                        @else
                            <div class="text-center">
                                <p class="text-sm text-gray-600 mb-4">This is your job posting.</p>
                                <a href="{{ route('marketplace.jobs.applications', $job->id) }}" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors inline-block text-center">
                                    View Applications ({{ $job->applications->count() }})
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center">
                            <h3 class="text-lg font-semibold mb-4">Ready to apply?</h3>
                            <p class="text-sm text-gray-600 mb-4">Join our marketplace to apply for this job.</p>
                            <a href="{{ route('login') }}" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors inline-block text-center">
                                Login to Apply
                            </a>
                        </div>
                    @endauth
                </div>

                <!-- Employer Info -->
                <div class="bg-white border border-gray-200 rounded-lg p-6 mt-6">
                    <h3 class="text-lg font-semibold mb-4">About the Employer</h3>
                    <div class="flex items-center mb-3">
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                            {{ substr($job->user->name, 0, 1) }}
                        </div>
                        <div class="ml-3">
                            <div class="font-medium">{{ $job->user->name }}</div>
                            @if($job->user->userProfile && $job->user->userProfile->average_rating > 0)
                                <div class="text-sm text-gray-600">
                                    ⭐ {{ number_format($job->user->userProfile->average_rating, 1) }}
                                    ({{ $job->user->userProfile->total_ratings }} reviews)
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    @if($job->user->userProfile && $job->user->userProfile->bio)
                        <p class="text-sm text-gray-600 mb-3">{{ Str::limit($job->user->userProfile->bio, 150) }}</p>
                    @endif
                    
                    <div class="text-sm text-gray-500">
                        <div>Member since {{ $job->user->created_at->format('M Y') }}</div>
                        <div>{{ $job->user->jobPosts()->count() }} jobs posted</div>
                    </div>
                    
                    @auth
                        @if(auth()->user()->id !== $job->user_id)
                            <div class="mt-4">
                                <a href="{{ route('messages.create', $job->user_id) }}?job_id={{ $job->id }}" class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition-colors text-center inline-block">
                                    Send Message
                                </a>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Notification Popup -->
<div id="notification-popup" class="fixed top-6 left-1/2 transform -translate-x-1/2 z-50 hidden">
    <div class="max-w-md mx-auto bg-white border border-gray-200 rounded-xl shadow-lg">
        <div class="p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg id="popup-icon" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3 w-0 flex-1">
                    <p id="popup-title" class="text-sm font-semibold text-gray-900"></p>
                    <p id="popup-message" class="mt-1 text-sm text-gray-500"></p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button type="button" onclick="closeNotification()" class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none">
                        <span class="sr-only">Close</span>
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showNotification(title, message, type = 'success') {
    const popup = document.getElementById('notification-popup');
    const popupTitle = document.getElementById('popup-title');
    const popupMessage = document.getElementById('popup-message');
    const popupIcon = document.getElementById('popup-icon');
    
    // Set colors based on type
    if (type === 'success') {
        popupIcon.className = 'w-6 h-6 text-green-500';
        popupIcon.innerHTML = '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>';
    } else if (type === 'error') {
        popupIcon.className = 'w-6 h-6 text-red-500';
        popupIcon.innerHTML = '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>';
    }
    
    popupTitle.textContent = title;
    popupMessage.textContent = message;
    
    popup.classList.remove('hidden');
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        closeNotification();
    }, 5000);
}

function closeNotification() {
    const popup = document.getElementById('notification-popup');
    popup.classList.add('hidden');
}

// Simple form submission with loading state
document.addEventListener('DOMContentLoaded', function() {
    const applicationForm = document.getElementById('job-application-form');
    if (applicationForm) {
        applicationForm.addEventListener('submit', function(e) {
            const submitButton = document.getElementById('submit-application');
            
            // Disable submit button and show loading state
            submitButton.disabled = true;
            submitButton.innerHTML = `
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Submitting...
            `;
            
            // Let the form submit normally - no preventDefault
        });
    }
});
</script>

</x-layouts.marketing>
