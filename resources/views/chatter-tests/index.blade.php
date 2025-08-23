@extends('theme::app')

@section('content')
<div class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">🎯 Chatter Test Center</h1>
            <p class="text-xl text-gray-600">Complete all tests to unlock your profile visibility in the marketplace</p>
        </div>

        <!-- Overall Progress -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-2xl font-bold text-gray-900">Overall Progress</h2>
                @if($allTestsCompleted)
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        ✅ All Tests Completed
                    </span>
                @else
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                        ⏳ Tests Pending
                    </span>
                @endif
            </div>
            
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Typing Tests Progress -->
                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-lg font-semibold text-blue-900">Typing Tests</h3>
                        @if($hasPassedTypingTest)
                            <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        @else
                            <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        @endif
                    </div>
                    <p class="text-blue-700">
                        {{ $hasPassedTypingTest ? 'You have passed at least one typing test' : 'You need to pass at least one typing test' }}
                    </p>
                </div>
                
                <!-- Training Modules Progress -->
                <div class="bg-purple-50 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-lg font-semibold text-purple-900">Training Modules</h3>
                        @if($hasCompletedAllTraining)
                            <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        @else
                            <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                        @endif
                    </div>
                    <p class="text-purple-700">{{ $completedModulesCount }}/{{ $totalModulesCount }} modules completed</p>
                    <div class="w-full bg-purple-200 rounded-full h-2 mt-2">
                        <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $totalModulesCount > 0 ? ($completedModulesCount / $totalModulesCount) * 100 : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Test Sections -->
        <div class="grid lg:grid-cols-2 gap-8">
            <!-- Typing Tests Section -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="bg-blue-600 px-6 py-4">
                    <h2 class="text-2xl font-bold text-white">⌨️ Typing Tests</h2>
                    <p class="text-blue-100">Test your typing speed and accuracy</p>
                </div>
                
                <div class="p-6">
                    @if($availableLanguages->count() > 0)
                        <div class="space-y-4">
                            @foreach($availableLanguages as $language)
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm mr-3
                                            {{ $language['code'] === 'en' ? 'bg-blue-500' : 'bg-red-500' }}">
                                            {{ strtoupper($language['code']) }}
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-gray-900">{{ $language['name'] }}</h3>
                                            <p class="text-sm text-gray-600">Test your typing skills</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('chatter.take-test', ['type' => 'typing', 'language' => $language['code']]) }}" 
                                       class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors text-sm">
                                        Take Test
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <!-- Recent Typing Test Results -->
                        @if($typingTestResults->count() > 0)
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Results</h3>
                            <div class="space-y-2">
                                @foreach($typingTestResults as $result)
                                <div class="flex items-center justify-between py-2 text-sm">
                                    <div class="flex items-center">
                                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold text-white mr-2
                                            {{ $result->testable->language === 'en' ? 'bg-blue-500' : 'bg-red-500' }}">
                                            {{ strtoupper($result->testable->language) }}
                                        </div>
                                        <span class="text-gray-700">{{ $result->testable->title }}</span>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <span class="text-gray-600">{{ $result->wpm }} WPM</span>
                                        <span class="text-gray-600">{{ number_format($result->accuracy, 1) }}%</span>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            {{ $result->passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $result->passed ? 'Passed' : 'Failed' }}
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    @else
                        <p class="text-gray-600 text-center py-4">No typing tests available at the moment.</p>
                    @endif
                </div>
            </div>

            <!-- Training Modules Section -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="bg-purple-600 px-6 py-4">
                    <h2 class="text-2xl font-bold text-white">🎓 Training Modules</h2>
                    <p class="text-purple-100">Complete training to become a certified chatter</p>
                </div>
                
                <div class="p-6">
                    @if($modules->count() > 0)
                        <div class="space-y-4">
                            @foreach($modules as $module)
                            @php
                                $progress = $userProgress->get($module->id);
                                $status = $progress ? $progress->status : 'not_started';
                                $statusColors = [
                                    'completed' => 'bg-green-100 text-green-800',
                                    'in_progress' => 'bg-yellow-100 text-yellow-800',
                                    'not_started' => 'bg-gray-100 text-gray-800'
                                ];
                                $statusText = [
                                    'completed' => 'Completed',
                                    'in_progress' => 'In Progress',
                                    'not_started' => 'Not Started'
                                ];
                            @endphp
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="font-semibold text-gray-900">{{ $module->title }}</h3>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusColors[$status] }}">
                                        {{ $statusText[$status] }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mb-3">{{ Str::limit($module->description, 100) }}</p>
                                <div class="flex items-center justify-between">
                                    <div class="text-xs text-gray-500">
                                        @if($module->duration_minutes)
                                            ⏱️ {{ $module->duration_minutes }} min •
                                        @endif
                                        📝 {{ $module->tests_count }} test{{ $module->tests_count != 1 ? 's' : '' }}
                                    </div>
                                    @if($module->tests->first())
                                    <a href="{{ route('chatter.take-test', ['type' => 'training', 'id' => $module->tests->first()->id]) }}" 
                                       class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded transition-colors text-sm">
                                        {{ $status === 'completed' ? 'Review' : ($status === 'in_progress' ? 'Continue' : 'Start') }}
                                    </a>
                                    @else
                                    <span class="bg-gray-400 text-white font-bold py-2 px-4 rounded text-sm cursor-not-allowed">No Test Available</span>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <!-- Quick Access to Training Dashboard -->
                        <div class="mt-6 pt-6 border-t border-gray-200 text-center">
                            <a href="{{ route('training.index') }}" 
                               class="inline-flex items-center text-purple-600 hover:text-purple-800 font-medium mr-4">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                View Full Training Dashboard
                            </a>
                        </div>
                    @else
                        <p class="text-gray-600 text-center py-4">No training modules available at the moment.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- View All Results -->
        <div class="mt-8 text-center">
            <a href="{{ route('chatter.results') }}" 
               class="inline-flex items-center px-6 py-3 bg-gray-800 hover:bg-gray-700 text-white font-semibold rounded-lg transition-colors shadow-lg hover:shadow-xl">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                View All My Results
            </a>
        </div>

        <!-- Success Message -->
        @if($allTestsCompleted)
        <div class="mt-8 bg-green-50 border border-green-200 rounded-lg p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-green-800">🎉 Congratulations!</h3>
                    <p class="text-green-700">
                        You have completed all required tests! Your profile is now visible in the marketplace and you can apply to jobs.
                    </p>
                    <div class="mt-2">
                        <a href="{{ route('profile.show') }}" class="text-green-800 hover:text-green-900 font-medium underline">
                            View Your Profile →
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
