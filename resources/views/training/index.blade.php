<x-theme::layouts.app>
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Training Modules</h1>
        <p class="text-gray-600">Complete all training modules to unlock your profile visibility in the talent marketplace.</p>
    </div>

    <!-- Progress Overview -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900">Your Progress</h2>
            <span class="text-sm text-gray-500">{{ $completedModulesCount }}/{{ $totalModulesCount }} Completed</span>
        </div>
        
        <div class="w-full bg-gray-200 rounded-full h-3 mb-2">
            <div class="bg-blue-600 h-3 rounded-full" style="width: {{ $totalModulesCount > 0 ? ($completedModulesCount / $totalModulesCount) * 100 : 0 }}%"></div>
        </div>
        
        @if($completedModulesCount === $totalModulesCount && $totalModulesCount > 0)
            <p class="text-sm text-green-600 font-medium">🎉 Congratulations! You've completed all training modules.</p>
        @else
            <p class="text-sm text-gray-600">Complete {{ $totalModulesCount - $completedModulesCount }} more modules to finish your training.</p>
        @endif
    </div>

    <!-- Training Modules Grid -->
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($modules as $module)
            @php
                $userProgress = $userProgress->where('training_module_id', $module->id)->first();
                $isCompleted = $userProgress && $userProgress->status === 'completed';
                $isInProgress = $userProgress && $userProgress->status === 'in_progress';
            @endphp
            
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow duration-200">
                <div class="p-6">
                    <!-- Status Badge -->
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center">
                            @if($isCompleted)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Completed
                                </span>
                            @elseif($isInProgress)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <svg class="w-3 h-3 mr-1 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    In Progress
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Not Started
                                </span>
                            @endif
                        </div>
                        
                        @if($module->difficulty_level)
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">
                                {{ ucfirst($module->difficulty_level) }}
                            </span>
                        @endif
                    </div>

                    <!-- Module Info -->
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $module->title }}</h3>
                    <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ $module->description }}</p>
                    
                    <!-- Module Stats -->
                    <div class="flex justify-between items-center text-sm text-gray-500 mb-4">
                        @if($module->duration_minutes)
                            <span>⏱️ {{ $module->duration_minutes }} min</span>
                        @endif
                        
                        @if($module->tests_count > 0)
                            <span>📝 {{ $module->tests_count }} test{{ $module->tests_count > 1 ? 's' : '' }}</span>
                        @endif
                    </div>
                    
                    <!-- Action Button -->
                    <div class="pt-4 border-t border-gray-100">
                        @if($isCompleted)
                            <div class="flex justify-between items-center">
                                <a href="{{ route('training.module', $module) }}" 
                                   class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                                    Review Module
                                </a>
                                @if($userProgress->completed_at)
                                    <span class="text-xs text-gray-500">
                                        Completed {{ $userProgress->completed_at->diffForHumans() }}
                                    </span>
                                @endif
                            </div>
                        @elseif($isInProgress)
                            <a href="{{ route('training.module', $module) }}" 
                               class="w-full bg-yellow-600 hover:bg-yellow-700 text-white text-center py-2 px-4 rounded-md font-medium text-sm transition-colors duration-200">
                                Continue Training
                            </a>
                        @else
                            <a href="{{ route('training.module', $module) }}" 
                               class="w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-md font-medium text-sm transition-colors duration-200">
                                Start Training
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No training modules available</h3>
                    <p class="mt-1 text-sm text-gray-500">Training modules will appear here when they become available.</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Requirements Notice -->
    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Get a better chance to get hired!</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>Completing your training modules and typing test will increase your chances of getting hired.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Global CSRF token refresh function for training pages
function refreshCSRFToken() {
    return fetch('/csrf-token', { method: 'GET' })
        .then(response => response.json())
        .then(data => {
            document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.token);
            return data.token;
        })
        .catch(error => {
            console.error('Error refreshing CSRF token:', error);
            return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        });
}

// Auto-refresh CSRF token every 5 minutes to prevent expiration
setInterval(refreshCSRFToken, 5 * 60 * 1000);
</script>
</x-theme::layouts.app>
