<x-layouts.marketing
    :seo="[
        'title'         => 'Typing Test - OnlyFans Management Marketplace',
        'description'   => 'Take a typing test to showcase your skills.',
        'image'         => url('/og_image.png'),
        'type'          => 'website'
    ]"
>

<div class="bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Typing Test</h1>
            <p class="text-gray-600">Test your typing speed and accuracy to showcase your skills to potential employers.</p>
        </div>

        @if($user->userProfile && $user->userProfile->typing_speed_wpm)
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-green-800">Previous Result: {{ $user->userProfile->typing_speed_wpm }} WPM</span>
                </div>
                <p class="text-sm text-green-700 mt-1">You can retake the test to improve your score.</p>
            </div>
        @endif

        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <div id="typing-test-container">
                <!-- Instructions -->
                <div id="instructions" class="mb-6">
                    <h2 class="text-xl font-semibold mb-4">Instructions</h2>
                    <ul class="list-disc list-inside space-y-2 text-gray-700">
                        <li>The test will last for 60 seconds</li>
                        <li>Type the text exactly as shown, including punctuation</li>
                        <li>Your WPM (Words Per Minute) and accuracy will be calculated</li>
                        <li>Press the spacebar to move to the next word</li>
                        <li>Click "Start Test" when you're ready to begin</li>
                    </ul>
                </div>

                <!-- Test Area -->
                <div id="test-area" class="hidden">
                    <div class="mb-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">Time Remaining:</span>
                            <span id="timer" class="text-2xl font-bold text-blue-600">60</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div id="progress-bar" class="bg-blue-600 h-2 rounded-full transition-all duration-1000" style="width: 100%"></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>WPM: <span id="current-wpm" class="font-semibold">0</span></span>
                            <span>Accuracy: <span id="current-accuracy" class="font-semibold">100%</span></span>
                        </div>
                    </div>

                    <div id="text-to-type" class="bg-gray-50 p-4 rounded-lg mb-4 text-lg font-mono leading-relaxed">
                        <!-- Test text will be inserted here -->
                    </div>

                    <input 
                        id="typing-input" 
                        type="text" 
                        class="w-full p-4 text-lg border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:outline-none"
                        placeholder="Start typing here..."
                        autocomplete="off"
                        autocorrect="off"
                        autocapitalize="off"
                        spellcheck="false"
                        disabled
                    >
                </div>

                <!-- Results -->
                <div id="results" class="hidden">
                    <div class="text-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Test Complete!</h2>
                        <p class="text-gray-600">Here are your results:</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-center">
                            <div class="text-3xl font-bold text-blue-600" id="final-wpm">0</div>
                            <div class="text-sm text-blue-800">Words Per Minute</div>
                        </div>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                            <div class="text-3xl font-bold text-green-600" id="final-accuracy">0%</div>
                            <div class="text-sm text-green-800">Accuracy</div>
                        </div>
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 text-center">
                            <div class="text-3xl font-bold text-purple-600" id="final-time">60</div>
                            <div class="text-sm text-purple-800">Seconds</div>
                        </div>
                    </div>

                    <form id="save-results-form" action="{{ route('profile.typing-test.submit') }}" method="POST">
                        @csrf
                        <input type="hidden" name="wpm" id="wpm-input">
                        <input type="hidden" name="accuracy" id="accuracy-input">
                        <input type="hidden" name="time_taken" id="time-input">
                        
                        <div class="flex justify-center space-x-4">
                            <button type="button" id="retake-test" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                Retake Test
                            </button>
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Save Results
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Control Buttons -->
                <div class="flex justify-center mt-6">
                    <button id="start-test" class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">
                        Start Test
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const testTexts = [
        "The quick brown fox jumps over the lazy dog. This pangram contains every letter of the alphabet at least once. It has been used for typing practice and font testing for many years. The sentence is short enough to be memorable but long enough to provide a good typing challenge.",
        "In the modern world of digital communication, typing skills have become increasingly important. Whether you're writing emails, creating documents, or engaging in online conversations, your ability to type quickly and accurately can significantly impact your productivity and professional success.",
        "Practice makes perfect when it comes to developing typing skills. Regular typing exercises can help improve both speed and accuracy. Many professional typists recommend practicing for at least 15-30 minutes daily to see significant improvement in typing performance over time.",
        "The art of touch typing involves using all ten fingers to type without looking at the keyboard. This skill allows typists to achieve much higher speeds than the traditional hunt-and-peck method. Learning proper finger placement and developing muscle memory are key components of effective typing."
    ];

    let testText = '';
    let currentIndex = 0;
    let startTime = 0;
    let timeLeft = 60;
    let timer = null;
    let correctChars = 0;
    let totalChars = 0;
    
    const instructionsDiv = document.getElementById('instructions');
    const testAreaDiv = document.getElementById('test-area');
    const resultsDiv = document.getElementById('results');
    const startButton = document.getElementById('start-test');
    const retakeButton = document.getElementById('retake-test');
    const textToTypeDiv = document.getElementById('text-to-type');
    const typingInput = document.getElementById('typing-input');
    const timerSpan = document.getElementById('timer');
    const progressBar = document.getElementById('progress-bar');
    const currentWpmSpan = document.getElementById('current-wpm');
    const currentAccuracySpan = document.getElementById('current-accuracy');
    
    function startTest() {
        testText = testTexts[Math.floor(Math.random() * testTexts.length)];
        currentIndex = 0;
        correctChars = 0;
        totalChars = 0;
        timeLeft = 60;
        startTime = Date.now();
        
        instructionsDiv.classList.add('hidden');
        testAreaDiv.classList.remove('hidden');
        resultsDiv.classList.add('hidden');
        startButton.classList.add('hidden');
        
        textToTypeDiv.innerHTML = generateTextDisplay();
        typingInput.value = '';
        typingInput.disabled = false;
        typingInput.focus();
        
        timer = setInterval(updateTimer, 1000);
        updateStats();
    }
    
    function generateTextDisplay() {
        let html = '';
        for (let i = 0; i < testText.length; i++) {
            if (i === currentIndex) {
                html += `<span class="bg-blue-200 text-blue-800">${testText[i]}</span>`;
            } else if (i < currentIndex) {
                html += `<span class="text-green-600">${testText[i]}</span>`;
            } else {
                html += `<span class="text-gray-600">${testText[i]}</span>`;
            }
        }
        return html;
    }
    
    function updateTimer() {
        timeLeft--;
        timerSpan.textContent = timeLeft;
        progressBar.style.width = (timeLeft / 60) * 100 + '%';
        
        if (timeLeft <= 0) {
            endTest();
        }
    }
    
    function updateStats() {
        const timeElapsed = (Date.now() - startTime) / 1000;
        const wordsTyped = totalChars / 5; // Standard: 5 characters = 1 word
        const wpm = Math.round((wordsTyped / timeElapsed) * 60);
        const accuracy = totalChars > 0 ? Math.round((correctChars / totalChars) * 100) : 100;
        
        currentWpmSpan.textContent = wpm || 0;
        currentAccuracySpan.textContent = accuracy + '%';
    }
    
    function endTest() {
        clearInterval(timer);
        typingInput.disabled = true;
        
        const timeElapsed = (Date.now() - startTime) / 1000;
        const wordsTyped = totalChars / 5;
        const wpm = Math.round((wordsTyped / timeElapsed) * 60);
        const accuracy = totalChars > 0 ? Math.round((correctChars / totalChars) * 100) : 100;
        
        document.getElementById('final-wpm').textContent = wpm;
        document.getElementById('final-accuracy').textContent = accuracy + '%';
        document.getElementById('final-time').textContent = Math.round(timeElapsed);
        
        document.getElementById('wpm-input').value = wpm;
        document.getElementById('accuracy-input').value = accuracy;
        document.getElementById('time-input').value = Math.round(timeElapsed);
        
        testAreaDiv.classList.add('hidden');
        resultsDiv.classList.remove('hidden');
    }
    
    function resetTest() {
        clearInterval(timer);
        instructionsDiv.classList.remove('hidden');
        testAreaDiv.classList.add('hidden');
        resultsDiv.classList.add('hidden');
        startButton.classList.remove('hidden');
        typingInput.disabled = true;
    }
    
    // Event listeners
    startButton.addEventListener('click', startTest);
    retakeButton.addEventListener('click', resetTest);
    
    typingInput.addEventListener('input', function(e) {
        const typed = e.target.value;
        
        if (typed.length > testText.length) {
            e.target.value = typed.substring(0, testText.length);
            return;
        }
        
        totalChars = typed.length;
        correctChars = 0;
        currentIndex = typed.length;
        
        for (let i = 0; i < typed.length; i++) {
            if (typed[i] === testText[i]) {
                correctChars++;
            }
        }
        
        textToTypeDiv.innerHTML = generateTextDisplay();
        updateStats();
        
        if (typed.length === testText.length) {
            endTest();
        }
    });
    
    // Prevent paste
    typingInput.addEventListener('paste', function(e) {
        e.preventDefault();
    });
});
</script>

</x-layouts.marketing>
