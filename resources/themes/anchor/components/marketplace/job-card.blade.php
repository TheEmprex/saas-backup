@props(['job'])

<div class="relative @if($job->is_featured) bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-blue-950/50 dark:via-indigo-950/50 dark:to-purple-950/50 border-2 border-blue-300 dark:border-blue-600 shadow-xl hover:shadow-2xl ring-2 ring-blue-500/20 @elseif($job->is_urgent) bg-gradient-to-br from-orange-50 via-white to-red-50 dark:from-orange-900/10 dark:via-zinc-800 dark:to-red-900/10 border-2 border-orange-200 dark:border-red-700 shadow-lg hover:shadow-xl @else bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm @endif rounded-lg h-full flex flex-col transition-all duration-300 ease-out">
    @if($job->is_featured)
        <!-- Premium featured badge -->
        <div class="absolute -top-2 -right-2 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 text-white text-xs font-bold px-4 py-1.5 rounded-full shadow-lg border-2 border-white dark:border-gray-800 z-10">
            ⭐ FEATURED
        </div>
        <!-- Premium accent line -->
        <div class="absolute top-4 left-4 w-1 h-8 bg-gradient-to-b from-blue-500 via-indigo-500 to-purple-500 rounded-full shadow-sm"></div>
        <!-- Premium top border highlight -->
        <div class="absolute top-0 left-4 right-4 h-0.5 bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500 rounded-full"></div>
    @elseif($job->is_urgent)
        <!-- Professional Urgent Badge -->
        <div class="absolute -top-2 -right-2 bg-gradient-to-r from-orange-600 to-red-600 text-white text-xs font-bold px-3 py-1.5 rounded-full shadow-lg border-2 border-white dark:border-gray-800">
            🔥 URGENT
        </div>
        <!-- Subtle urgent indicator -->
        <div class="absolute top-4 left-4 w-1 h-8 bg-gradient-to-b from-orange-500 to-red-500 rounded-full animate-pulse"></div>
    @endif
    <div class="p-6 flex-1 flex flex-col">
        <!-- Header with company info -->
        <div class="flex items-center space-x-3 mb-4">
            <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center text-white font-semibold text-lg shrink-0">
                {{ substr($job->user->name, 0, 1) }}
            </div>
            <div class="min-w-0">
                <h3 class="font-semibold text-gray-900 dark:text-white text-sm truncate">{{ $job->user->name }}</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $job->user->userType->display_name ?? 'Agency' }}</p>
            </div>
        </div>

        <!-- Job title and description -->
        <div class="mb-4 flex-1">
            <h4 class="@if($job->is_featured) text-lg font-bold text-blue-900 dark:text-blue-100 @elseif($job->is_urgent) text-lg font-bold text-orange-900 dark:text-orange-100 @else text-lg font-semibold text-gray-900 dark:text-white @endif mb-2 leading-tight">
                {{ $job->title }}
            </h4>
            <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed">
                {{ Str::limit($job->description, 120) }}
            </p>
        </div>

        <!-- Tags -->
        <div class="flex flex-wrap gap-2 mb-4">
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"/>
                </svg>
                {{ ucfirst(str_replace('_', ' ', $job->market)) }}
            </span>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ ucfirst(str_replace('_', ' ', $job->experience_level)) }}
            </span>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                </svg>
                {{ ucfirst(str_replace('_', ' ', $job->contract_type)) }}
            </span>
            
            @if($job->required_timezone || $job->timezone_flexible)
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/20 dark:text-indigo-400">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    @if($job->timezone_flexible)
                        🌐 Flexible
                    @else
                        @php
                            $timezoneLabels = [
                                'UTC' => 'UTC',
                                'America/New_York' => 'EST',
                                'America/Chicago' => 'CST',
                                'America/Denver' => 'MST',
                                'America/Los_Angeles' => 'PST',
                                'Europe/London' => 'GMT',
                                'Europe/Paris' => 'CET',
                                'Europe/Berlin' => 'CET',
                                'Asia/Tokyo' => 'JST',
                                'Asia/Shanghai' => 'CST',
                                'Australia/Sydney' => 'AEST'
                            ];
                        @endphp
                        {{ $timezoneLabels[$job->required_timezone] ?? $job->required_timezone }}
                    @endif
                </span>
            @endif
        </div>

        <!-- Rate and applications info -->
        <div class="flex items-center justify-between mb-4">
            <div class="flex flex-col">
                <div class="text-xl font-bold text-emerald-600 dark:text-emerald-400">
                    @if($job->rate_type === 'hourly')
                        ${{ number_format($job->hourly_rate, 0) }}
                    @elseif($job->rate_type === 'fixed')
                        ${{ number_format($job->fixed_rate, 0) }}
                    @else
                        {{ $job->commission_percentage }}%
                    @endif
                </div>
                <div class="text-xs font-medium text-gray-500 dark:text-gray-400 mt-0.5">
                    @if($job->rate_type === 'hourly')
                        Per hour
                    @elseif($job->rate_type === 'fixed')
                        Fixed rate
                    @elseif($job->rate_type === 'commission')
                        Commission
                    @endif
                </div>
            </div>
            <div class="text-right">
                <div class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ $job->applications->count() }} applications
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $job->created_at->diffForHumans() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Action buttons -->
    <div class="px-6 pb-6 mt-auto">
        @if($job->is_featured)
            <!-- Premium Featured Buttons -->
            <div class="flex space-x-2">
                <a href="{{ route('marketplace.jobs.show', $job) }}" class="flex-1 bg-gradient-to-r from-indigo-600 via-blue-600 to-purple-600 hover:from-indigo-700 hover:via-blue-700 hover:to-purple-700 text-white text-center px-4 py-3 rounded-lg font-semibold transition-all duration-200 shadow-lg hover:shadow-xl border border-indigo-500/50">
                    View Featured Job
                </a>
                @auth
                    @if(auth()->user()->id !== $job->user_id)
                        <a href="{{ route('messages.create', $job->user_id) }}" class="px-4 py-3 bg-gradient-to-r from-indigo-50 to-purple-50 hover:from-indigo-100 hover:to-purple-100 dark:from-indigo-900/30 dark:to-purple-900/30 dark:hover:from-indigo-900/50 dark:hover:to-purple-900/50 text-indigo-700 dark:text-indigo-300 rounded-lg font-medium transition-all duration-200 flex items-center justify-center border border-indigo-200 dark:border-indigo-700">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                            </svg>
                        </a>
                    @endif
                @endauth
            </div>
        @elseif($job->is_urgent)
            <!-- Professional Urgent Buttons -->
            <div class="flex space-x-2">
                <a href="{{ route('marketplace.jobs.show', $job) }}" class="flex-1 bg-gradient-to-r from-orange-600 to-red-600 hover:from-orange-700 hover:to-red-700 text-white text-center px-4 py-3 rounded-lg font-semibold transition-all duration-200 shadow-md hover:shadow-lg">
                    View Urgent Job
                </a>
                @auth
                    @if(auth()->user()->id !== $job->user_id)
                        <a href="{{ route('messages.create', $job->user_id) }}" class="px-4 py-3 bg-orange-50 hover:bg-orange-100 dark:bg-orange-900/30 dark:hover:bg-orange-900/50 text-orange-700 dark:text-orange-300 rounded-lg font-medium transition-all duration-200 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                            </svg>
                        </a>
                    @endif
                @endauth
            </div>
        @else
            <!-- Standard Buttons -->
            <div class="flex space-x-2">
                <a href="{{ route('marketplace.jobs.show', $job) }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center px-4 py-2.5 rounded-lg font-medium transition-colors">
                    View Details
                </a>
                @auth
                    @if(auth()->user()->id !== $job->user_id)
                        <a href="{{ route('messages.create', $job->user_id) }}" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors flex items-center justify-center">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                            </svg>
                        </a>
                    @endif
                @endauth
            </div>
        @endif
    </div>
</div>
