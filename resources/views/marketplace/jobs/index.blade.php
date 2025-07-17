@extends('theme::app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <h1 class="page-title">Browse Jobs</h1>
                <div class="page-pretitle">Find your next opportunity</div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('marketplace.jobs') }}">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="Search jobs..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="market" class="form-select">
                                    <option value="">All Markets</option>
                                    <option value="management" {{ request('market') == 'management' ? 'selected' : '' }}>Management</option>
                                    <option value="chatting" {{ request('market') == 'chatting' ? 'selected' : '' }}>Chatting</option>
                                    <option value="content_creation" {{ request('market') == 'content_creation' ? 'selected' : '' }}>Content Creation</option>
                                    <option value="marketing" {{ request('market') == 'marketing' ? 'selected' : '' }}>Marketing</option>
                                    <option value="design" {{ request('market') == 'design' ? 'selected' : '' }}>Design</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="experience_level" class="form-select">
                                    <option value="">All Levels</option>
                                    <option value="beginner" {{ request('experience_level') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                    <option value="intermediate" {{ request('experience_level') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                    <option value="advanced" {{ request('experience_level') == 'advanced' ? 'selected' : '' }}>Advanced</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="rate_type" class="form-select">
                                    <option value="">All Rate Types</option>
                                    <option value="hourly" {{ request('rate_type') == 'hourly' ? 'selected' : '' }}>Hourly</option>
                                    <option value="fixed" {{ request('rate_type') == 'fixed' ? 'selected' : '' }}>Fixed</option>
                                    <option value="commission" {{ request('rate_type') == 'commission' ? 'selected' : '' }}>Commission</option>
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

    <!-- Jobs Grid -->
    <div class="row">
        @forelse($jobs as $job)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-sm me-3" style="background-image: url('/images/default-avatar.png')"></div>
                        <div>
                            <div class="font-weight-medium">{{ $job->user->name }}</div>
                            <div class="text-muted small">{{ $job->user->userType->display_name }}</div>
                        </div>
                    </div>
                    
                    <h4 class="card-title">{{ $job->title }}</h4>
                    <p class="text-muted">{{ Str::limit($job->description, 120) }}</p>
                    
                    <div class="mb-3">
                        <span class="badge bg-primary">{{ ucfirst($job->market) }}</span>
                        <span class="badge bg-secondary">{{ ucfirst($job->experience_level) }}</span>
                        <span class="badge bg-info">{{ ucfirst($job->contract_type) }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            @if($job->rate_type === 'hourly')
                                <div class="h5 mb-0 text-success">${{ $job->hourly_rate }}/hr</div>
                            @elseif($job->rate_type === 'fixed')
                                <div class="h5 mb-0 text-success">${{ $job->fixed_rate }}</div>
                            @else
                                <div class="h5 mb-0 text-success">{{ $job->commission_percentage }}%</div>
                            @endif
                        </div>
                        <div>
                            <small class="text-muted">{{ $job->current_applications }} applications</small>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <a href="{{ route('marketplace.jobs.show', $job) }}" class="btn btn-primary btn-sm">View Details</a>
                        <div class="text-muted small mt-1">Posted {{ $job->created_at->diffForHumans() }}</div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <div class="mb-3">
                    <i class="ti ti-briefcase-off text-muted" style="font-size: 3rem;"></i>
                </div>
                <h3 class="text-muted">No jobs found</h3>
                <p class="text-muted">Try adjusting your search filters or check back later for new opportunities.</p>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($jobs->hasPages())
    <div class="row mt-4">
        <div class="col-12">
            {{ $jobs->links() }}
        </div>
    </div>
    @endif
</div>
@endsection
