@extends('theme::app')


@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1 class="page-title">Browse Profiles</h1>
                <div class="page-pretitle">Find talented professionals</div>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="row">
        <div class="col-12">
            <div class="alert alert-success alert-dismissible" role="alert">
                <div class="d-flex">
                    <div>
                        <i class="ti ti-check alert-icon"></i>
                    </div>
                    <div>
                        {{ session('success') }}
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    @endif

    <!-- Search and Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('marketplace.profiles') }}">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="Search profiles..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="user_type" class="form-select">
                                    <option value="">All Types</option>
                                    <option value="model" {{ request('user_type') == 'model' ? 'selected' : '' }}>Model</option>
                                    <option value="manager" {{ request('user_type') == 'manager' ? 'selected' : '' }}>Manager</option>
                                    <option value="chatter" {{ request('user_type') == 'chatter' ? 'selected' : '' }}>Chatter</option>
                                    <option value="agency" {{ request('user_type') == 'agency' ? 'selected' : '' }}>Agency</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="location" class="form-select">
                                    <option value="">All Locations</option>
                                    <option value="remote" {{ request('location') == 'remote' ? 'selected' : '' }}>Remote</option>
                                    <option value="us" {{ request('location') == 'us' ? 'selected' : '' }}>United States</option>
                                    <option value="uk" {{ request('location') == 'uk' ? 'selected' : '' }}>United Kingdom</option>
                                    <option value="ca" {{ request('location') == 'ca' ? 'selected' : '' }}>Canada</option>
                                    <option value="au" {{ request('location') == 'au' ? 'selected' : '' }}>Australia</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="min_rating" class="form-select">
                                    <option value="">All Ratings</option>
                                    <option value="4" {{ request('min_rating') == '4' ? 'selected' : '' }}>4+ Stars</option>
                                    <option value="3" {{ request('min_rating') == '3' ? 'selected' : '' }}>3+ Stars</option>
                                    <option value="2" {{ request('min_rating') == '2' ? 'selected' : '' }}>2+ Stars</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="timezone" class="form-select">
                                    <option value="">Any Timezone</option>
                                    @if(auth()->check() && auth()->user()->timezone)
                                        <option value="{{ auth()->user()->timezone }}" {{ request('timezone') == auth()->user()->timezone ? 'selected' : '' }}>My Timezone</option>
                                    @endif
                                    @php
                                        $commonTimezones = \App\Models\UserAvailabilitySchedule::getCommonTimezones();
                                    @endphp
                                    @foreach($commonTimezones as $tz => $label)
                                        <option value="{{ $tz }}" {{ request('timezone') == $tz ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-md-2">
                                <select name="availability" class="form-select">
                                    <option value="">Any Availability</option>
                                    <option value="available" {{ request('availability') == 'available' ? 'selected' : '' }}>Available Now</option>
                                    <option value="busy" {{ request('availability') == 'busy' ? 'selected' : '' }}>Currently Busy</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="day_filter" class="form-select">
                                    <option value="">Any Day</option>
                                    <option value="monday" {{ request('day_filter') == 'monday' ? 'selected' : '' }}>Monday</option>
                                    <option value="tuesday" {{ request('day_filter') == 'tuesday' ? 'selected' : '' }}>Tuesday</option>
                                    <option value="wednesday" {{ request('day_filter') == 'wednesday' ? 'selected' : '' }}>Wednesday</option>
                                    <option value="thursday" {{ request('day_filter') == 'thursday' ? 'selected' : '' }}>Thursday</option>
                                    <option value="friday" {{ request('day_filter') == 'friday' ? 'selected' : '' }}>Friday</option>
                                    <option value="saturday" {{ request('day_filter') == 'saturday' ? 'selected' : '' }}>Saturday</option>
                                    <option value="sunday" {{ request('day_filter') == 'sunday' ? 'selected' : '' }}>Sunday</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex gap-2">
                                    <input type="time" name="start_time" class="form-control" value="{{ request('start_time') }}" placeholder="Start time">
                                    <input type="time" name="end_time" class="form-control" value="{{ request('end_time') }}" placeholder="End time">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Search</button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('marketplace.timezone-availability') }}" class="btn btn-info w-100">
                                    <i class="ti ti-world me-1"></i>Advanced Search
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Profiles Grid -->
    <div class="row">
        @forelse($profiles as $profile)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-lg me-3" style="background-image: url('{{ $profile->avatar ?? '/images/default-avatar.png' }}')"></div>
                        <div>
                            <h4 class="card-title mb-1">{{ $profile->user->name }}</h4>
                            <div class="text-muted">{{ $profile->user->userType->display_name }}</div>
                            @if($profile->location)
                                <div class="text-muted small">{{ $profile->location }}</div>
                            @endif
                        </div>
                    </div>
                    
                    @if($profile->bio)
                    <p class="text-muted mb-3">{{ Str::limit($profile->bio, 100) }}</p>
                    @endif
                    
                    <!-- Skills -->
                    @if($profile->skills)
                    <div class="mb-3">
                        @foreach(explode(',', $profile->skills) as $skill)
                            <span class="badge bg-light text-dark me-1">{{ trim($skill) }}</span>
                        @endforeach
                    </div>
                    @endif
                    
                    <!-- Rating -->
                    <div class="mb-3">
                        <div class="d-flex align-items-center">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $profile->average_rating)
                                    <i class="ti ti-star-filled text-warning"></i>
                                @else
                                    <i class="ti ti-star text-muted"></i>
                                @endif
                            @endfor
                            <span class="ms-2">{{ number_format($profile->average_rating, 1) }}</span>
                            <span class="text-muted ms-1">({{ $profile->total_ratings }} reviews)</span>
                        </div>
                    </div>
                    
                    <!-- Rate and Experience -->
                    <div class="row mb-3">
                        @if($profile->hourly_rate)
                        <div class="col-6">
                            <div class="text-muted small">Hourly Rate</div>
                            <div class="h6 text-success">${{ $profile->hourly_rate }}/hr</div>
                        </div>
                        @endif
                        @if($profile->experience_years)
                        <div class="col-6">
                            <div class="text-muted small">Experience</div>
                            <div class="h6">{{ $profile->experience_years }} years</div>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Online Status -->
                    <div class="mb-3">
                        @if($profile->is_available)
                            <span class="badge bg-success">Available</span>
                        @else
                            <span class="badge bg-secondary">Busy</span>
                        @endif

                        @if($profile->user->last_seen_at && $profile->user->last_seen_at->diffInMinutes() < 10)
                            <span class="badge bg-info">Online</span>
                        @elseif($profile->user->last_seen_at)
                            <span class="badge bg-light text-dark">Last seen {{ $profile->user->last_seen_at->diffForHumans() }}</span>
                        @endif
                    </div>
                    
                    <!-- Actions -->
                    <div class="d-flex gap-2">
                        <a href="{{ route('marketplace.profiles.show', $profile->user) }}" class="btn btn-primary btn-sm">View Profile</a>
                        @auth
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="openMessageModal({{ $profile->user->id }}, '{{ $profile->user->name }}', '{{ $profile->user->userType->display_name }}')">
                                <i class="ti ti-message"></i> Message
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="ti ti-message"></i> Message
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="ti ti-users-off text-muted" style="font-size: 3rem;"></i>
                </div>
                <h3 class="text-muted">No profiles found</h3>
                <p class="text-muted">Try adjusting your search filters or check back later for new profiles.</p>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($profiles->hasPages())
    <div class="row mt-4">
        <div class="col-12">
            {{ $profiles->links() }}
        </div>
    </div>
    @endif
</div>

<!-- Message Modal -->
<div class="modal modal-blur fade" id="messageModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-message-circle me-2"></i>
                    Send Message
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="messageForm" action="{{ route('marketplace.messages.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="conversation_id" id="recipientId">
                    
                    <!-- Recipient Display -->
                    <div class="mb-3">
                        <label class="form-label">To</label>
                        <div class="card card-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3">
                                        <img src="/images/default-avatar.png" alt="Avatar" class="rounded">
                                    </div>
                                    <div>
                                        <div class="fw-medium" id="recipientName"></div>
                                        <div class="text-muted small" id="recipientType"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Message Input -->
                    <div class="mb-3">
                        <label for="messageContent" class="form-label">Message</label>
                        <textarea name="content" id="messageContent" class="form-control" rows="6" placeholder="Type your message..." required></textarea>
                        <small class="form-hint">Be professional and clear in your communication.</small>
                    </div>
                    
                    <!-- File Attachment -->
                    <div class="mb-3">
                        <label for="messageAttachment" class="form-label">
                            <i class="ti ti-paperclip me-1"></i>
                            Attachment (Optional)
                        </label>
                        <input type="file" name="attachment" id="messageAttachment" class="form-control" accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.gif,.zip">
                        <small class="form-hint">Supported formats: PDF, DOC, DOCX, TXT, JPG, PNG, GIF, ZIP. Max size: 10MB</small>
                    </div>
                    
                    <!-- File Preview -->
                    <div id="filePreview" class="mb-3" style="display: none;">
                        <div class="alert alert-info">
                            <div class="d-flex align-items-center">
                                <i class="ti ti-file me-2"></i>
                                <span id="fileName"></span>
                                <button type="button" class="btn-close ms-auto" onclick="clearFilePreview()"></button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="sendMessageBtn">
                        <i class="ti ti-send me-1"></i>
                        Send Message
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openMessageModal(userId, userName, userType) {
    console.log('Opening modal for user:', userId, userName, userType);
    
    // Check if modal element exists
    const modalElement = document.getElementById('messageModal');
    if (!modalElement) {
        console.error('Modal element not found');
        alert('Modal element not found');
        return;
    }
    
    // Set recipient information
    document.getElementById('recipientId').value = userId;
    document.getElementById('recipientName').textContent = userName;
    document.getElementById('recipientType').textContent = userType;
    
    // Clear form
    document.getElementById('messageContent').value = '';
    document.getElementById('messageAttachment').value = '';
    clearFilePreview();
    
    // Check if Bootstrap is available
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap not available, trying alternative methods');
        // Try jQuery if available
        if (typeof $ !== 'undefined') {
            $(modalElement).modal('show');
        } else {
            // Fallback: manually show modal
            modalElement.style.display = 'block';
            modalElement.classList.add('show');
            document.body.classList.add('modal-open');
        }
    } else {
        // Show modal using Bootstrap
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    }
}

function clearFilePreview() {
    document.getElementById('filePreview').style.display = 'none';
    document.getElementById('fileName').textContent = '';
    document.getElementById('messageAttachment').value = '';
}

// File attachment preview
document.getElementById('messageAttachment').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        document.getElementById('fileName').textContent = file.name + ' (' + formatFileSize(file.size) + ')';
        document.getElementById('filePreview').style.display = 'block';
    } else {
        clearFilePreview();
    }
});

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Form submission with loading state
document.getElementById('messageForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('sendMessageBtn');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.innerHTML = '<i class="ti ti-loader-2 me-1 spinner-border spinner-border-sm"></i> Sending...';
    submitBtn.disabled = true;
    
    // Form will submit normally, but we provide visual feedback
    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 3000);
});
</script>
@endpush
@endsection
