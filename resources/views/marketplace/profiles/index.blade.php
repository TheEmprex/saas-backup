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
                                <button type="submit" class="btn btn-primary">Search</button>
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
                        <a href="{{ route('marketplace.messages.create', $profile->user) }}" class="btn btn-outline-secondary btn-sm">Message</a>
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
@endsection
