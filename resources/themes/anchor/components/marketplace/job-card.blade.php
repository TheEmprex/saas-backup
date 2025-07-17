@props(['job'])

<div class="group bg-white dark:bg-zinc-800 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-zinc-700 overflow-hidden">
    <div class="p-6">
        <!-- Header with company info -->
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center text-white font-semibold text-lg shadow-lg">
                    {{ substr($job->user->name, 0, 1) }}
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $job->user->name }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $job->user->userType->display_name ?? 'Agency' }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                @if($job->is_featured)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        Featured
                    </span>
                @endif
                @if($job->is_urgent)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        Urgent
                    </span>
                @endif
            </div>
        </div>

        <!-- Job title and description -->
        <div class="mb-4">
            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
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
                {{ ucfirst($job->market) }}
            </span>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ ucfirst($job->experience_level) }}
            </span>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                </svg>
                {{ ucfirst($job->contract_type) }}
            </span>
        </div>

        <!-- Rate and applications -->
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-2">
                <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">
                    @if($job->rate_type === 'hourly')
                        ${{ number_format($job->hourly_rate, 0) }}/hr
                    @elseif($job->rate_type === 'fixed')
                        ${{ number_format($job->fixed_rate, 0) }}
                    @else
                        {{ $job->commission_percentage }}%
                    @endif
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    @if($job->rate_type === 'commission')
                        commission
                    @endif
                </div>
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $job->current_applications }} applications
                </div>
                <div class="text-xs text-gray-400 dark:text-gray-500">
                    {{ $job->created_at->diffForHumans() }}
                </div>
            </div>
        </div>

        <!-- Action buttons -->
        <div class="flex space-x-2">
            <a href="{{ route('marketplace.jobs.show', $job) }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center px-4 py-2.5 rounded-lg font-medium transition-colors duration-200 shadow-sm hover:shadow-md">
                View Details
            </a>
            @auth
                @if(auth()->user()->id !== $job->user_id)
                    <a href="{{ route('messages.create', $job->user_id) }}" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 dark:bg-zinc-700 dark:hover:bg-zinc-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium transition-colors duration-200 shadow-sm">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                        </svg>
                    </a>
                @endif
            @endauth
        </div>
    </div>
</div>
