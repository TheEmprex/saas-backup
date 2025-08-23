{{-- Chatter Test Results Badge Component --}}
@if($user && ($user->hasPassedTypingTest() || $user->hasCompletedAllTraining()))
<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            Chatter Certification
        </h3>
        @if($user->meetsMarketplaceRequirements())
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                ✅ Fully Certified
            </span>
        @else
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                🔄 In Progress
            </span>
        @endif
    </div>
    
    <div class="grid md:grid-cols-2 gap-4">
        {{-- Typing Test Results --}}
        @if($user->hasPassedTypingTest())
            @php
                $bestTypingResults = $user->userTestResults()
                    ->where('testable_type', 'App\\Models\\TypingTest')
                    ->where('passed', true)
                    ->with('testable')
                    ->orderByDesc('wpm')
                    ->take(3)
                    ->get();
            @endphp
            <div class="bg-blue-50 rounded-lg p-4">
                <h4 class="font-medium text-blue-900 mb-2 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Typing Tests Passed
                </h4>
                <div class="space-y-2">
                    @foreach($bestTypingResults as $result)
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-blue-700">
                                {{ strtoupper($result->testable->language) }} 
                                ({{ $result->testable->difficulty_level }}/3)
                            </span>
                            <div class="text-blue-800 font-medium">
                                {{ $result->wpm }} WPM • {{ number_format($result->accuracy, 1) }}%
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        
        {{-- Training Progress --}}
        @if($user->hasCompletedAllTraining())
            @php
                $completedModules = $user->trainingProgress()
                    ->where('status', 'completed')
                    ->with('trainingModule')
                    ->get();
            @endphp
            <div class="bg-purple-50 rounded-lg p-4">
                <h4 class="font-medium text-purple-900 mb-2 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Training Completed
                </h4>
                <div class="space-y-1">
                    @foreach($completedModules as $progress)
                        <div class="text-sm text-purple-700">
                            ✓ {{ $progress->trainingModule->title }}
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
    
    {{-- Certification Date --}}
    @if($user->meetsMarketplaceRequirements())
        @php
            $latestCompletion = collect([
                $user->userTestResults()->where('passed', true)->latest()->first()?->completed_at,
                $user->trainingProgress()->where('status', 'completed')->latest()->first()?->completed_at
            ])->filter()->max();
        @endphp
        @if($latestCompletion)
            <div class="mt-4 pt-4 border-t border-gray-200">
                <p class="text-sm text-gray-600">
                    <span class="font-medium">Certified:</span> {{ $latestCompletion->format('M j, Y') }}
                </p>
            </div>
        @endif
    @endif
</div>
@endif
