@extends('theme::app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-xl me-3" style="background-image: url('{{ $profile->avatar ?? '/images/default-avatar.png' }}')"></div>
                            <div>
                                <h1 class="card-title mb-1">{{ $user->name }}</h1>
                                <div class="text-muted">{{ $user->userType->display_name }}</div>
                                @if($profile->location)
                                    <div class="text-muted small">{{ $profile->location }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="text-end">
                            @if($profile->is_available)
                                <span class="badge bg-success">Available</span>
                            @else
                                <span class="badge bg-secondary">Busy</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Bio -->
                    @if($profile->bio)
                    <div class="mb-4">
                        <h5>About</h5>
                        <p>{!! nl2br(e($profile->bio)) !!}</p>
                    </div>
                    @endif
                    
                    <!-- Skills -->
                    @if($profile->skills)
                    <div class="mb-4">
                        <h5>Skills</h5>
                        <div>
                            @foreach(explode(',', $profile->skills) as $skill)
                                <span class="badge bg-light text-dark me-1 mb-1">{{ trim($skill) }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    <!-- Services -->
                    @if($profile->services)
                    <div class="mb-4">
                        <h5>Services</h5>
                        <p>{!! nl2br(e($profile->services)) !!}</p>
                    </div>
                    @endif
                    
                    <!-- Experience -->
                    @if($profile->experience || $profile->experience_years)
                    <div class="mb-4">
                        <h5>Experience</h5>
                        @if($profile->experience_years)
                            <div class="mb-2">
                                <strong>{{ $profile->experience_years }} years</strong> of experience
                            </div>
                        @endif
                        @if($profile->experience)
                            <div>{!! nl2br(e($profile->experience)) !!}</div>
                        @endif
                    </div>
                    @endif
                    
                    <!-- Education -->
                    @if($profile->education)
                    <div class="mb-4">
                        <h5>Education</h5>
                        <div>{!! nl2br(e($profile->education)) !!}</div>
                    </div>
                    @endif
                    
                    <!-- Portfolio -->
                    @if($profile->portfolio_url)
                    <div class="mb-4">
                        <h5>Portfolio</h5>
                        <a href="{{ $profile->portfolio_url }}" target="_blank" class="btn btn-outline-primary">
                            <i class="ti ti-external-link me-2"></i>View Portfolio
                        </a>
                    </div>
                    @endif
                    
                    <!-- Languages -->
                    @if($profile->languages)
                    <div class="mb-4">
                        <h5>Languages</h5>
                        <div>
                            @foreach(explode(',', $profile->languages) as $language)
                                <span class="badge bg-info me-1 mb-1">{{ trim($language) }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Reviews -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Reviews ({{ $profile->total_ratings }})</h3>
                </div>
                <div class="card-body">
                    @forelse($reviews as $review)
                    <div class="mb-4 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-3" style="background-image: url('/images/default-avatar.png')"></div>
                                <div>
                                    <div class="font-weight-medium">{{ $review->reviewer->name }}</div>
                                    <div class="text-muted small">{{ $review->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $review->rating)
                                        <i class="ti ti-star-filled text-warning"></i>
                                    @else
                                        <i class="ti ti-star text-muted"></i>
                                    @endif
                                @endfor
                            </div>
                        </div>
                        @if($review->comment)
                            <p class="mb-0">{!! nl2br(e($review->comment)) !!}</p>
                        @endif
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <i class="ti ti-star-off text-muted" style="font-size: 2rem;"></i>
                        <p class="text-muted mt-2">No reviews yet</p>
                    </div>
                    @endforelse
                    
                    @if($reviews->hasPages())
                        <div class="mt-3">
                            {{ $reviews->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Profile Stats</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <div class="text-muted small">Rating</div>
                                <div class="d-flex align-items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $profile->average_rating)
                                            <i class="ti ti-star-filled text-warning"></i>
                                        @else
                                            <i class="ti ti-star text-muted"></i>
                                        @endif
                                    @endfor
                                    <span class="ms-2 font-weight-medium">{{ number_format($profile->average_rating, 1) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <div class="text-muted small">Reviews</div>
                                <div class="h5 mb-0">{{ $profile->total_ratings }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <div class="text-muted small">Jobs Completed</div>
                                <div class="h5 mb-0">{{ $profile->jobs_completed }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <div class="text-muted small">Response Time</div>
                                <div class="h5 mb-0">{{ $profile->response_time ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                    
                    @if($profile->hourly_rate)
                    <div class="mb-3">
                        <div class="text-muted small">Hourly Rate</div>
                        <div class="h5 text-success mb-0">${{ $profile->hourly_rate }}/hr</div>
                    </div>
                    @endif
                    
                    <div class="mb-3">
                        <div class="text-muted small">Member Since</div>
                        <div>{{ $user->created_at->format('F Y') }}</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="text-muted small">Last Seen</div>
                        <div>
                            @if($user->last_seen_at && $user->last_seen_at->diffInMinutes() < 10)
                                <span class="badge bg-success">Online now</span>
                            @elseif($user->last_seen_at)
                                {{ $user->last_seen_at->diffForHumans() }}
                            @else
                                Never
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contact Actions -->
            @if($user->id !== auth()->id())
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Contact</h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('marketplace.messages.create', $user) }}" class="btn btn-primary">
                            <i class="ti ti-message me-2"></i>Send Message
                        </a>
                        <a href="{{ route('marketplace.jobs.create', ['user' => $user->id]) }}" class="btn btn-outline-primary">
                            <i class="ti ti-briefcase me-2"></i>Hire for Job
                        </a>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Verification Status -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Verification</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Email Verified</span>
                            @if($user->email_verified_at)
                                <span class="badge bg-success">Verified</span>
                            @else
                                <span class="badge bg-danger">Not Verified</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>KYC Status</span>
                            @if($user->kyc_status === 'approved')
                                <span class="badge bg-success">Approved</span>
                            @elseif($user->kyc_status === 'pending')
                                <span class="badge bg-warning">Pending</span>
                            @else
                                <span class="badge bg-danger">Not Verified</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Profile Complete</span>
                            @if($profile->is_complete)
                                <span class="badge bg-success">Complete</span>
                            @else
                                <span class="badge bg-warning">Incomplete</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
