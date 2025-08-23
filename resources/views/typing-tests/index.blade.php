@extends('theme::app')

@section('content')
<div class="py-12 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Typing Tests</h1>
            <p class="text-xl text-gray-600">Test your typing speed and accuracy in multiple languages</p>
        </div>

        <div class="grid md:grid-cols-2 gap-8">
            @foreach($availableLanguages as $language)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                <div class="p-8">
                    <div class="flex items-center mb-6">
                        @if($language['code'] === 'en')
                            <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-xl mr-4">
                                EN
                            </div>
                        @elseif($language['code'] === 'fr')
                            <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center text-white font-bold text-xl mr-4">
                                FR
                            </div>
                        @endif
                        <h3 class="text-2xl font-bold text-gray-900">{{ $language['name'] }}</h3>
                    </div>
                    
                    <p class="text-gray-600 mb-6">
                        Test your typing skills in {{ $language['name'] }}. Improve your speed and accuracy for better job applications.
                    </p>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                            </svg>
                            Real-time accuracy tracking
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                            </svg>
                            WPM (Words Per Minute) calculation
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Pass/fail results saved
                        </div>
                    </div>
                    
                    <a href="{{ route('typing-tests.show', $language['code']) }}" 
                       class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg text-center transition-colors">
                        Take {{ $language['name'] }} Test
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        @if(auth()->user()->userTestResults()->whereHasMorph('testable', [\App\Models\TypingTest::class])->exists())
        <div class="mt-12 bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Your Recent Results</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach(auth()->user()->userTestResults()->with('testable')->whereHasMorph('testable', [\App\Models\TypingTest::class])->latest()->take(5)->get() as $result)
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white mr-3
                                {{ $result->testable->language === 'en' ? 'bg-blue-500' : 'bg-red-500' }}">
                                {{ strtoupper($result->testable->language) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $result->testable->title }}</p>
                                <p class="text-sm text-gray-500">{{ $result->completed_at->format('M j, Y') }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="text-center">
                                <p class="text-sm font-medium text-gray-900">{{ $result->wpm }} WPM</p>
                                <p class="text-xs text-gray-500">Speed</p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm font-medium text-gray-900">{{ number_format($result->accuracy, 1) }}%</p>
                                <p class="text-xs text-gray-500">Accuracy</p>
                            </div>
                            <div class="text-center">
                                @if($result->passed)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Passed
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Failed
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
