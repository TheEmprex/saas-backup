<div class="bg-white shadow-lg rounded-lg overflow-hidden">
    <div class="p-6 md:p-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $test->title }}</h1>
            <p class="text-md text-gray-600">{{ $test->description }}</p>
            <div class="mt-2 text-sm text-gray-500">
                Module: {{ $test->trainingModule->title }}
                @if($test->time_limit_minutes)
                    • Time limit: {{ $test->time_limit_minutes }} minutes
                @endif
                @if($test->passing_score)
                    • Passing score: {{ $test->passing_score }}%
                @endif
            </div>
        </div>

        <form id="test-form" action="{{ route('training.test.submit', $test) }}" method="POST">
            @csrf
            <input type="hidden" name="time_taken" id="time-taken" value="">
            
            <div class="space-y-6">
                @foreach($test->questions as $index => $question)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-3">
                            Question {{ $index + 1 }}: {{ $question['question'] }}
                        </h3>
                        <div class="space-y-2">
                            @foreach($question['options'] as $optionIndex => $option)
                                <label class="flex items-center">
                                    <input type="radio" name="answers[{{ $index }}]" value="{{ $optionIndex }}" 
                                           class="mr-2 text-blue-600 focus:ring-blue-500" required>
                                    <span class="text-gray-700">{{ $option }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8 flex justify-between items-center">
                <a href="{{ route('training.module', $test->trainingModule) }}" 
                   class="text-gray-600 hover:text-gray-800">← Back to Module</a>
                
                <div class="flex items-center space-x-4">
                    @if($test->time_limit_minutes)
                        <div class="text-lg font-mono text-gray-700">
                            Time: <span id="timer">{{ $test->time_limit_minutes }}:00</span>
                        </div>
                    @endif
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg">
                        Submit Test
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@if($test->time_limit_minutes)
<script>
document.addEventListener('DOMContentLoaded', function() {
    let timeLeft = {{ $test->time_limit_minutes }} * 60; // Convert to seconds
    const startTime = Date.now();
    const timer = document.getElementById('timer');
    const form = document.getElementById('test-form');
    const timeInput = document.getElementById('time-taken');
    
    const updateTimer = () => {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timer.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        
        if (timeLeft <= 0) {
            alert('Time is up! Submitting your test...');
            const timeTaken = Math.floor((Date.now() - startTime) / 1000);
            timeInput.value = timeTaken;
            form.submit();
            return;
        }
        
        timeLeft--;
    };
    
    const interval = setInterval(updateTimer, 1000);
    
    form.addEventListener('submit', function() {
        clearInterval(interval);
        const timeTaken = Math.floor((Date.now() - startTime) / 1000);
        timeInput.value = timeTaken;
    });
    
    updateTimer();
});
</script>
@endif
