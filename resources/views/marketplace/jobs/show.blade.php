@extends('theme::app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h1 class="card-title">{{ $job->title }}</h1>
                            <div class="text-muted">Posted {{ $job->created_at->diffForHumans() }}</div>
                        </div>
                        <div class="text-end">
                            @if($job->rate_type === 'hourly')
                                <div class="h4 text-success mb-0">${{ $job->hourly_rate }}/hr</div>
                            @elseif($job->rate_type === 'fixed')
                                <div class="h4 text-success mb-0">${{ $job->fixed_rate }}</div>
                            @else
                                <div class="h4 text-success mb-0">{{ $job->commission_percentage }}%</div>
                            @endif
                            <div class="small text-muted">{{ ucfirst($job->rate_type) }}</div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Market</label>
                                <span class="badge bg-primary">{{ ucfirst($job->market) }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Experience Level</label>
                                <span class="badge bg-secondary">{{ ucfirst($job->experience_level) }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Contract Type</label>
                                <span class="badge bg-info">{{ ucfirst($job->contract_type) }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Applications</label>
                                <span class="badge bg-warning">{{ $job->current_applications }} received</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h5>Job Description</h5>
                        <div class="content">{!! nl2br(e($job->description)) !!}</div>
                    </div>
                    
                    @if($job->requirements)
                    <div class="mb-4">
                        <h5>Requirements</h5>
                        <div class="content">{!! nl2br(e($job->requirements)) !!}</div>
                    </div>
                    @endif
                    
                    @if($job->preferred_qualifications)
                    <div class="mb-4">
                        <h5>Preferred Qualifications</h5>
                        <div class="content">{!! nl2br(e($job->preferred_qualifications)) !!}</div>
                    </div>
                    @endif
                    
                    @if($job->duration)
                    <div class="mb-4">
                        <h5>Duration</h5>
                        <p>{{ $job->duration }}</p>
                    </div>
                    @endif
                </div>
            </div>
            
            @if($job->user_id !== auth()->id())
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Apply for this Job</h3>
                </div>
                <div class="card-body">
                    @php
                        $user = auth()->user()->load('userProfile', 'userType');
                        $requiresTypingTest = ($job->min_typing_speed && $job->min_typing_speed > 0) || 
                                              ($user->userType && in_array($user->userType->name, ['chatter', 'chatting_agency']));
                        $hasApplied = \App\Models\JobApplication::where('job_post_id', $job->id)
                                                                  ->where('user_id', auth()->id())
                                                                  ->exists();
                    @endphp
                    
                    @if($hasApplied)
                    <div class="alert alert-info">
                        <i class="ti ti-info-circle me-2"></i>
                        You have already applied for this job.
                    </div>
                    @else
                    
                    @if($requiresTypingTest)
                    <div class="alert alert-warning mb-4">
                        <i class="ti ti-typing me-2"></i>
                        <strong>Typing Test Required:</strong> This position requires a typing test with minimum {{ $job->min_typing_speed ?? 30 }} WPM and 85% accuracy.
                    </div>
                    @endif
                    
                    <form id="job-application-form" action="{{ route('marketplace.jobs.apply', $job) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Basic Application Fields -->
                        <div class="mb-3">
                            <label class="form-label">Cover Letter <span class="text-danger">*</span></label>
                            <textarea name="cover_letter" class="form-control" rows="5" placeholder="Tell the employer why you're the right person for this job..." required></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Proposed Rate <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="proposed_rate" class="form-control" step="0.01" placeholder="Your rate" required>
                                        <span class="input-group-text">
                                            @if($job->rate_type === 'hourly')
                                                /hr
                                            @elseif($job->rate_type === 'commission')
                                                %
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Available Hours/Week <span class="text-danger">*</span></label>
                                    <input type="number" name="available_hours" class="form-control" min="1" max="80" placeholder="Hours per week" required>
                                </div>
                            </div>
                        </div>
                        
                        @if($requiresTypingTest)
                        <!-- Typing Test Section -->
                        <div class="border rounded p-4 mb-4" id="typing-test-section">
                            <h5 class="text-primary mb-3">
                                <i class="ti ti-keyboard me-2"></i>
                                Typing Test
                            </h5>
                            
                            <div id="typing-test-container">
                                <!-- Instructions -->
                                <div id="typing-instructions" class="mb-4">
                                    <div class="alert alert-info">
                                        <h6>Instructions:</h6>
                                        <ul class="mb-0">
                                            <li>The test will last for 60 seconds</li>
                                            <li>Type the text exactly as shown</li>
                                            <li>Minimum required: {{ $job->min_typing_speed ?? 30 }} WPM with 85% accuracy</li>
                                            <li>Click "Start Typing Test" when ready</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="text-center">
                                        <button type="button" id="start-typing-test" class="btn btn-success">
                                            <i class="ti ti-play me-2"></i>Start Typing Test
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Test Area -->
                                <div id="typing-test-area" class="d-none">
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <div class="h4 text-primary mb-0" id="test-timer">60</div>
                                                <small class="text-muted">Seconds</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <div class="h4 text-success mb-0" id="test-wpm">0</div>
                                                <small class="text-muted">WPM</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <div class="h4 text-info mb-0" id="test-accuracy">100%</div>
                                                <small class="text-muted">Accuracy</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <div class="progress">
                                            <div id="test-progress" class="progress-bar" role="progressbar" style="width: 100%"></div>
                                        </div>
                                    </div>
                                    
                                    <div id="test-text" class="bg-light p-3 rounded mb-3" style="font-family: monospace; font-size: 16px; line-height: 1.5;"></div>
                                    
                                    <input type="text" id="test-input" class="form-control" placeholder="Start typing here..." autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" disabled>
                                </div>
                                
                                <!-- Results -->
                                <div id="typing-results" class="d-none">
                                    <div class="alert alert-success">
                                        <h6>Test Complete!</h6>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <strong>WPM:</strong> <span id="final-wpm">0</span>
                                            </div>
                                            <div class="col-md-4">
                                                <strong>Accuracy:</strong> <span id="final-accuracy">0%</span>
                                            </div>
                                            <div class="col-md-4">
                                                <strong>Status:</strong> <span id="test-status" class="badge">-</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="text-center">
                                        <button type="button" id="retake-test" class="btn btn-outline-secondary me-2">
                                            <i class="ti ti-refresh me-2"></i>Retake Test
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Hidden inputs for typing test data -->
                            <input type="hidden" name="typing_test_wpm" id="typing-wpm-input">
                            <input type="hidden" name="typing_test_accuracy" id="typing-accuracy-input">
                            <input type="hidden" name="typing_test_results" id="typing-results-input">
                        </div>
                        @endif
                        
                        <div class="mb-3">
                            <label class="form-label">Portfolio/Resume (Optional)</label>
                            <input type="file" name="portfolio" class="form-control" accept=".pdf,.doc,.docx">
                        </div>
                        
                        <button type="submit" class="btn btn-primary" id="submit-application" @if($requiresTypingTest) disabled @endif>
                            <i class="ti ti-send me-2"></i>Submit Application
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @endif
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Job Posted By</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-md me-3" style="background-image: url('/images/default-avatar.png')"></div>
                        <div>
                            <div class="font-weight-medium">{{ $job->user->name }}</div>
                            <div class="text-muted">{{ $job->user->userType->display_name }}</div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="text-muted small">Member since</div>
                        <div>{{ $job->user->created_at->format('F Y') }}</div>
                    </div>
                    
                    @if($job->user->userProfile)
                    <div class="mb-3">
                        <div class="text-muted small">Rating</div>
                        <div>
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $job->user->userProfile->average_rating)
                                    <i class="ti ti-star-filled text-warning"></i>
                                @else
                                    <i class="ti ti-star text-muted"></i>
                                @endif
                            @endfor
                            <span class="ms-2">{{ number_format($job->user->userProfile->average_rating, 1) }}</span>
                        </div>
                    </div>
                    @endif
                    
                    <div class="mb-3">
                        <a href="{{ route('marketplace.profiles.show', $job->user) }}" class="btn btn-outline-primary btn-sm">View Profile</a>
                        <a href="{{ route('marketplace.messages.create', $job->user) }}" class="btn btn-outline-secondary btn-sm">Send Message</a>
                    </div>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Job Details</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <div class="text-muted small">Job ID</div>
                                <div class="font-mono">#{{ $job->id }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <div class="text-muted small">Status</div>
                                <span class="badge bg-{{ $job->status === 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($job->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="text-muted small">Location</div>
                        <div>{{ $job->location ?? 'Remote' }}</div>
                    </div>
                    
                    @if($job->urgency)
                    <div class="mb-3">
                        <div class="text-muted small">Urgency</div>
                        <span class="badge bg-{{ $job->urgency === 'urgent' ? 'danger' : 'info' }}">
                            {{ ucfirst($job->urgency) }}
                        </span>
                    </div>
                    @endif
                    
                    @if($job->max_applications)
                    <div class="mb-3">
                        <div class="text-muted small">Max Applications</div>
                        <div>{{ $job->max_applications }}</div>
                    </div>
                    @endif
                </div>
            </div>
            
            @if($job->user_id === auth()->id())
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Manage Job</h3>
                </div>
                <div class="card-body">
                    <a href="{{ route('marketplace.jobs.applications', $job) }}" class="btn btn-primary btn-sm mb-2">
                        View Applications ({{ $job->current_applications }})
                    </a>
                    <a href="{{ route('marketplace.jobs.edit', $job) }}" class="btn btn-outline-primary btn-sm mb-2">
                        Edit Job
                    </a>
                    <form action="{{ route('marketplace.jobs.destroy', $job) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure?')">
                            Delete Job
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const requiresTypingTest = {{ $requiresTypingTest ?? false ? 'true' : 'false' }};
    const minWpm = {{ $job->min_typing_speed ?? 30 }};
    const minAccuracy = 85;
    
    if (!requiresTypingTest) return;
    
    // Test texts
    const testTexts = [
        "In the modern digital age, effective communication through written text has become increasingly important. Professional typists understand that speed and accuracy are equally crucial for success in any role that requires extensive typing. Whether you're responding to customer inquiries, creating content, or managing correspondence, your typing skills directly impact your productivity and professional effectiveness.",
        "The art of customer service in online environments requires both technical proficiency and interpersonal skills. Successful chat representatives must be able to type quickly while maintaining a friendly and professional tone. They need to understand customer needs, provide accurate information, and resolve issues efficiently through written communication.",
        "Time management and multitasking are essential skills for chat-based roles. Professionals in this field often handle multiple conversations simultaneously while maintaining high quality responses. This requires excellent typing speed, attention to detail, and the ability to switch between different topics and customer personalities seamlessly.",
        "Building rapport with customers through text-based communication is a unique skill that combines writing ability with emotional intelligence. Effective chat agents use proper grammar, clear language, and empathetic responses to create positive customer experiences. They understand how to convey tone and emotion through written words."
    ];
    
    let testText = '';
    let currentIndex = 0;
    let startTime = 0;
    let timeLeft = 60;
    let timer = null;
    let correctChars = 0;
    let totalChars = 0;
    let testCompleted = false;
    
    // DOM elements
    const instructionsDiv = document.getElementById('typing-instructions');
    const testAreaDiv = document.getElementById('typing-test-area');
    const resultsDiv = document.getElementById('typing-results');
    const startButton = document.getElementById('start-typing-test');
    const retakeButton = document.getElementById('retake-test');
    const testTextDiv = document.getElementById('test-text');
    const testInput = document.getElementById('test-input');
    const timerSpan = document.getElementById('test-timer');
    const progressBar = document.getElementById('test-progress');
    const wpmSpan = document.getElementById('test-wpm');
    const accuracySpan = document.getElementById('test-accuracy');
    const submitButton = document.getElementById('submit-application');
    
    // Hidden inputs
    const wpmInput = document.getElementById('typing-wpm-input');
    const accuracyInput = document.getElementById('typing-accuracy-input');
    const resultsInput = document.getElementById('typing-results-input');
    
    function startTest() {
        testText = testTexts[Math.floor(Math.random() * testTexts.length)];
        currentIndex = 0;
        correctChars = 0;
        totalChars = 0;
        timeLeft = 60;
        startTime = Date.now();
        testCompleted = false;
        
        instructionsDiv.classList.add('d-none');
        testAreaDiv.classList.remove('d-none');
        resultsDiv.classList.add('d-none');
        
        testTextDiv.innerHTML = generateTextDisplay();
        testInput.value = '';
        testInput.disabled = false;
        testInput.focus();
        
        timer = setInterval(updateTimer, 1000);
        updateStats();
    }
    
    function generateTextDisplay() {
        let html = '';
        for (let i = 0; i < testText.length; i++) {
            if (i === currentIndex) {
                html += `<span class="bg-primary text-white">${testText[i]}</span>`;
            } else if (i < currentIndex) {
                const isCorrect = i < testInput.value.length && testInput.value[i] === testText[i];
                html += `<span class="${isCorrect ? 'text-success' : 'text-danger bg-danger bg-opacity-25'}">${testText[i]}</span>`;
            } else {
                html += `<span class="text-muted">${testText[i]}</span>`;
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
        
        wpmSpan.textContent = wpm || 0;
        accuracySpan.textContent = accuracy + '%';
    }
    
    function endTest() {
        clearInterval(timer);
        testInput.disabled = true;
        testCompleted = true;
        
        const timeElapsed = (Date.now() - startTime) / 1000;
        const wordsTyped = totalChars / 5;
        const wpm = Math.round((wordsTyped / timeElapsed) * 60);
        const accuracy = totalChars > 0 ? Math.round((correctChars / totalChars) * 100) : 100;
        
        const passed = wpm >= minWpm && accuracy >= minAccuracy;
        
        // Update results display
        document.getElementById('final-wpm').textContent = wpm;
        document.getElementById('final-accuracy').textContent = accuracy + '%';
        
        const statusBadge = document.getElementById('test-status');
        if (passed) {
            statusBadge.textContent = 'PASSED';
            statusBadge.className = 'badge bg-success';
        } else {
            statusBadge.textContent = 'FAILED';
            statusBadge.className = 'badge bg-danger';
        }
        
        // Update hidden inputs
        wpmInput.value = wpm;
        accuracyInput.value = accuracy;
        resultsInput.value = JSON.stringify({
            wpm: wpm,
            accuracy: accuracy,
            timeElapsed: Math.round(timeElapsed),
            totalChars: totalChars,
            correctChars: correctChars,
            passed: passed,
            testText: testText.substring(0, 100) + '...', // First 100 chars for reference
            timestamp: new Date().toISOString()
        });
        
        // Enable/disable submit button based on results
        if (passed) {
            submitButton.disabled = false;
            submitButton.classList.remove('btn-secondary');
            submitButton.classList.add('btn-primary');
        } else {
            submitButton.disabled = true;
            submitButton.classList.remove('btn-primary');
            submitButton.classList.add('btn-secondary');
        }
        
        testAreaDiv.classList.add('d-none');
        resultsDiv.classList.remove('d-none');
        
        // Update results alert class based on pass/fail
        const alertDiv = resultsDiv.querySelector('.alert');
        if (passed) {
            alertDiv.classList.remove('alert-danger');
            alertDiv.classList.add('alert-success');
        } else {
            alertDiv.classList.remove('alert-success');
            alertDiv.classList.add('alert-danger');
        }
    }
    
    function resetTest() {
        clearInterval(timer);
        instructionsDiv.classList.remove('d-none');
        testAreaDiv.classList.add('d-none');
        resultsDiv.classList.add('d-none');
        testInput.disabled = true;
        testCompleted = false;
        
        // Reset form data
        wpmInput.value = '';
        accuracyInput.value = '';
        resultsInput.value = '';
        
        // Disable submit button again
        submitButton.disabled = true;
        submitButton.classList.remove('btn-primary');
        submitButton.classList.add('btn-secondary');
    }
    
    // Event listeners
    startButton.addEventListener('click', startTest);
    retakeButton.addEventListener('click', resetTest);
    
    testInput.addEventListener('input', function(e) {
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
        
        testTextDiv.innerHTML = generateTextDisplay();
        updateStats();
        
        if (typed.length === testText.length) {
            endTest();
        }
    });
    
    // Prevent paste
    testInput.addEventListener('paste', function(e) {
        e.preventDefault();
    });
    
    // Prevent form submission if test is required but not completed
    document.getElementById('job-application-form').addEventListener('submit', function(e) {
        if (requiresTypingTest && !testCompleted) {
            e.preventDefault();
            alert('Please complete the typing test before submitting your application.');
            return false;
        }
        
        if (requiresTypingTest && testCompleted) {
            const wpm = parseInt(wpmInput.value);
            const accuracy = parseInt(accuracyInput.value);
            
            if (wpm < minWpm || accuracy < minAccuracy) {
                e.preventDefault();
                alert(`Your typing test results do not meet the minimum requirements (${minWpm} WPM, ${minAccuracy}% accuracy). Please retake the test.`);
                return false;
            }
        }
    });
});
</script>
@endpush

@endsection
