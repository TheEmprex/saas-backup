<x-theme::layouts.app>
<div class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900">My Test Results</h1>
            <p class="text-xl text-gray-600">Review your performance on all tests</p>
        </div>

        <!-- Typing Test Results -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
            <div class="bg-blue-600 px-6 py-4">
                <h2 class="text-2xl font-bold text-white">⌨️ Typing Tests</h2>
            </div>
            <div class="p-6">
                @if($typingTestResults->count() > 0)
                    <div class="space-y-4">
                        @foreach($typingTestResults as $result)
                            <div class="border border-gray-200 rounded-lg p-4 flex items-center justify-between">
                                <div>
                                    <span class="font-semibold text-gray-900">{{ $result->testable->title }}</span>
                                    <span class="text-sm text-gray-500">({{ strtoupper($result->testable->language) }})</span>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <span class="text-gray-600">WPM: {{ $result->wpm }}</span>
                                    <span class="text-gray-600">Accuracy: {{ number_format($result->accuracy, 1) }}%</span>
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $result->passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $result->passed ? 'Passed' : 'Failed' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-600 text-center py-4">You have not taken any typing tests yet.</p>
                @endif
            </div>
        </div>

        <!-- Training Module Results -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="bg-purple-600 px-6 py-4">
                <h2 class="text-2xl font-bold text-white">🎓 Training Modules</h2>
            </div>
            <div class="p-6">
                @if($trainingTestResults->count() > 0)
                    <div class="space-y-4">
                        @foreach($trainingTestResults as $result)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-semibold text-gray-900">{{ $result->testable->title }}</span>
                                    <span class="text-sm text-gray-500">Module: {{ $result->testable->trainingModule->title }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600">Score: {{ number_format($result->score, 1) }}%</span>
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $result->passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $result->passed ? 'Passed' : 'Failed' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-600 text-center py-4">You have not taken any training tests yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>
</x-theme::layouts.app>
