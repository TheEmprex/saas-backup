@extends('theme::app')

@section('content')
<div class="py-12 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="p-6 md:p-8">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg mr-4 bg-blue-500">
                        {{ strtoupper($language) }}
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $language_name }} Typing Test</h1>
                        <p class="text-md text-gray-600">{{ $test->title }}</p>
                    </div>
                </div>

                @if($recentResult)
                <div class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-400">
                    <h3 class="font-bold text-yellow-800">Recent Result</h3>
                    <p class="text-sm text-yellow-700">WPM: {{ $recentResult->wpm }}, Accuracy: {{ $recentResult->accuracy }}%, Status: {{ $recentResult->passed ? 'Passed' : 'Failed' }}</p>
                </div>
                @endif

                <div id="test-container" class="relative p-6 bg-gray-100 rounded-lg">
                    <div id="test-content" class="text-lg text-gray-800 leading-relaxed select-none">{{ $test->content }}</div>
                    <textarea id="user-input" class="absolute top-0 left-0 w-full h-full p-6 text-lg text-gray-800 leading-relaxed bg-transparent opacity-50 focus:opacity-100 transition-opacity duration-300 resize-none" placeholder="Start typing here..." disabled></textarea>
                </div>
                
                <div class="mt-6 flex items-center justify-between">
                    <div id="timer" class="text-2xl font-mono font-bold text-gray-800">0:00</div>
                    <div id="stats" class="text-right">
                        <span class="font-semibold">WPM:</span> <span id="wpm-stat">0</span> | 
                        <span class="font-semibold">Accuracy:</span> <span id="accuracy-stat">100%</span>
                    </div>
                </div>

                <div class="mt-8 text-center">
                    <button id="start-button" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg transition-transform transform hover:scale-105">Start Test</button>
                    <button id="reset-button" class="hidden bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-8 rounded-lg">Try Again</button>
                </div>

                <form id="typing-test-form" action="{{ route('typing-tests.submit', $language) }}" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="test_id" value="{{ $test->id }}">
                    <input type="hidden" name="wpm" id="form-wpm">
                    <input type="hidden" name="accuracy" id="form-accuracy">
                    <input type="hidden" name="time_taken" id="form-time_taken">
                    <input type="hidden" name="user_input" id="form-user_input">
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const testContent = "{{ $test->content }}";
    const testContainer = document.getElementById('test-container');
    const testDiv = document.getElementById('test-content');
    const userInput = document.getElementById('user-input');
    const startButton = document.getElementById('start-button');
    const resetButton = document.getElementById('reset-button');
    const timerDiv = document.getElementById('timer');
    const wpmStat = document.getElementById('wpm-stat');
    const accuracyStat = document.getElementById('accuracy-stat');
    const form = document.getElementById('typing-test-form');

    let startTime, timerInterval, timeElapsed = 0;

    function initializeTest() {
        testDiv.innerHTML = testContent.split('').map(char => `<span class="text-gray-400">${char}</span>`).join('');
        userInput.value = '';
        userInput.disabled = true;
        startButton.disabled = false;
        startButton.classList.remove('hidden');
        resetButton.classList.add('hidden');
        timerDiv.textContent = "0:00";
        wpmStat.textContent = "0";
        accuracyStat.textContent = "100%";
    }

    function startTest() {
        userInput.disabled = false;
        userInput.focus();
        startButton.classList.add('hidden');
        resetButton.classList.remove('hidden');
        startTime = Date.now();
        timerInterval = setInterval(updateTimer, 1000);
        userInput.addEventListener('input', onInput);
    }

    function updateTimer() {
        timeElapsed++;
        const minutes = Math.floor(timeElapsed / 60);
        const seconds = timeElapsed % 60;
        timerDiv.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
    }

    function onInput() {
        const typedText = userInput.value;
        const chars = testDiv.querySelectorAll('span');
        let correctChars = 0;

        typedText.split('').forEach((char, index) => {
            if (index < testContent.length) {
                if (char === testContent[index]) {
                    chars[index].className = 'text-green-500';
                    correctChars++;
                } else {
                    chars[index].className = 'text-red-500 bg-red-100';
                }
            } 
        });

        for (let i = typedText.length; i < testContent.length; i++) {
            chars[i].className = 'text-gray-400';
        }

        const accuracy = typedText.length > 0 ? (correctChars / typedText.length) * 100 : 100;
        accuracyStat.textContent = `${accuracy.toFixed(0)}%`;

        const wordsTyped = typedText.trim().split(/\s+/).length;
        const wpm = timeElapsed > 0 ? (wordsTyped / timeElapsed) * 60 : 0;
        wpmStat.textContent = wpm.toFixed(0);

        if (typedText.length === testContent.length) {
            endTest();
        }
    }

    function endTest() {
        clearInterval(timerInterval);
        userInput.disabled = true;
        startButton.classList.add('hidden');
        
        const finalWpm = parseFloat(wpmStat.textContent);
        const finalAccuracy = parseFloat(accuracyStat.textContent.replace('%', ''));

        document.getElementById('form-wpm').value = finalWpm;
        document.getElementById('form-accuracy').value = finalAccuracy;
        document.getElementById('form-time_taken').value = timeElapsed;
        document.getElementById('form-user_input').value = userInput.value;
        
        // Use a small delay to allow user to see final stats before submitting.
        setTimeout(() => form.submit(), 500);
    }

    startButton.addEventListener('click', startTest);
    resetButton.addEventListener('click', initializeTest);
    
    initializeTest();
});
</script>
@endsection

