@props(['application'])

<div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-sm border border-gray-100 dark:border-zinc-700 p-6 hover:shadow-md transition-all duration-300">
    <!-- Header -->
    <div class="flex items-start justify-between mb-6">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center text-white font-semibold text-lg shadow-lg">
                {{ substr($application->jobPost->user->name ?? 'U', 0, 1) }}
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $application->jobPost->title ?? 'Job Title' }}
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ $application->jobPost->user->name ?? 'Company' }} • 
                    {{ $application->jobPost->user->userType->display_name ?? 'Agency' }}
                </p>
            </div>
        </div>
        
        <!-- Status Badge -->
        <div class="flex items-center space-x-2">
            @php
                $statusColors = [
                    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
                    'accepted' => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
                    'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
                ];
                $status = $application->status ?? 'pending';
            @endphp
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusColors[$status] ?? $statusColors['pending'] }}">
                @if($status === 'pending')
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                @elseif($status === 'accepted')
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                @elseif($status === 'rejected')
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                @endif
                {{ ucfirst($status) }}
            </span>
        </div>
    </div>
    
    <!-- Job Details -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-gray-50 dark:bg-zinc-700/50 rounded-lg p-4">
            <div class="flex items-center space-x-2 mb-2">
                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Proposed Rate</span>
            </div>
            <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">
                ${{ number_format($application->proposed_rate ?? 0, 2) }}
                @if($application->jobPost && $application->jobPost->rate_type === 'hourly')
                    /hr
                @elseif($application->jobPost && $application->jobPost->rate_type === 'commission')
                    %
                @endif
            </div>
        </div>
        
        <div class="bg-gray-50 dark:bg-zinc-700/50 rounded-lg p-4">
            <div class="flex items-center space-x-2 mb-2">
                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Available Hours</span>
            </div>
            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                {{ $application->available_hours ?? 0 }}
                <span class="text-sm text-gray-500 dark:text-gray-400">hrs/week</span>
            </div>
        </div>
        
        <div class="bg-gray-50 dark:bg-zinc-700/50 rounded-lg p-4">
            <div class="flex items-center space-x-2 mb-2">
                <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Applied</span>
            </div>
            <div class="text-lg font-semibold text-purple-600 dark:text-purple-400">
                {{ $application->created_at->diffForHumans() }}
            </div>
        </div>
    </div>
    
    <!-- Cover Letter -->
    @if($application->cover_letter)
        <div class="mb-6">
            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Cover Letter</h4>
            <div class="bg-gray-50 dark:bg-zinc-700/50 rounded-lg p-4">
                <p class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed">
                    {{ Str::limit($application->cover_letter, 200) }}
                </p>
                @if(strlen($application->cover_letter) > 200)
                    <button class="text-blue-600 dark:text-blue-400 text-sm mt-2 hover:underline">
                        Read more
                    </button>
                @endif
            </div>
        </div>
    @endif
    
    <!-- Job Tags -->
    @if($application->jobPost)
        <div class="flex flex-wrap gap-2 mb-6">
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                {{ ucfirst($application->jobPost->market) }}
            </span>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                {{ ucfirst($application->jobPost->experience_level) }}
            </span>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400">
                {{ ucfirst($application->jobPost->contract_type) }}
            </span>
        </div>
    @endif
    
    <!-- Actions -->
    <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-zinc-600">
        <div class="flex space-x-3">
            @if($application->jobPost)
                <a href="{{ route('marketplace.jobs.show', $application->jobPost->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-zinc-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-600 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    View Job
                </a>
                <a href="{{ route('messages.create', $application->jobPost->user_id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-zinc-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-600 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    Message
                </a>
            @endif
        </div>
        
        @if($status === 'pending')
            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                <svg class="w-4 h-4 mr-1 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Under Review
            </div>
        @elseif($status === 'accepted')
            <div class="flex items-center text-sm text-green-600 dark:text-green-400">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                Congratulations!
            </div>
        @elseif($status === 'rejected')
            <div class="flex items-center text-sm text-red-600 dark:text-red-400">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                Not selected
            </div>
        @endif
    </div>
</div>
