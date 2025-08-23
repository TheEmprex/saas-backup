@extends('theme::app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h2 mb-2">Browse Jobs</h1>
                    <p class="text-muted">Find your next opportunity</p>
                </div>
                
                <!-- Show Post Job button only for agencies -->
                @auth
                    @if(auth()->user()->isAgency())
                        <a href="{{ route('marketplace.jobs.create') }}" class="btn btn-primary">
                            <i class="ti ti-plus me-2"></i>
                            Post a Job
                        </a>
                    @endif
                @endauth
            </div>

            <!-- Role-specific messaging -->
            @auth
                <div class="alert alert-info mb-4">
                    @if(auth()->user()->isAgency())
                        <i class="ti ti-briefcase me-2"></i>
                        <strong>Agency Dashboard:</strong> Post jobs and manage applications to find the best talent for your projects.
                    @elseif(auth()->user()->isChatter() || auth()->user()->isVA())
                        <i class="ti ti-user me-2"></i>
                        <strong>Talent Dashboard:</strong> Browse and apply to jobs that match your skills and experience.
                    @else
                        <i class="ti ti-info-circle me-2"></i>
                        Welcome! Browse available job opportunities below.
                    @endif
                </div>
            @endauth

            <!-- Job Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Market</label>
                            <select name="market" class="form-select">
                                <option value="">All Markets</option>
                                <option value="english" {{ request('market') == 'english' ? 'selected' : '' }}>English</option>
                                <option value="spanish" {{ request('market') == 'spanish' ? 'selected' : '' }}>Spanish</option>
                                <option value="french" {{ request('market') == 'french' ? 'selected' : '' }}>French</option>
                                <option value="german" {{ request('market') == 'german' ? 'selected' : '' }}>German</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">Experience Level</label>
                            <select name="experience" class="form-select">
                                <option value="">All Levels</option>
                                <option value="beginner" {{ request('experience') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                <option value="intermediate" {{ request('experience') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                <option value="advanced" {{ request('experience') == 'advanced' ? 'selected' : '' }}>Advanced</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">Contract Type</label>
                            <select name="contract_type" class="form-select">
                                <option value="">All Types</option>
                                <option value="full_time" {{ request('contract_type') == 'full_time' ? 'selected' : '' }}>Full Time</option>
                                <option value="part_time" {{ request('contract_type') == 'part_time' ? 'selected' : '' }}>Part Time</option>
                                <option value="contract" {{ request('contract_type') == 'contract' ? 'selected' : '' }}>Contract</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">Timezone</label>
                            <select name="timezone" class="form-select">
                                <option value="">Any Timezone</option>
                                @if(auth()->check() && auth()->user()->timezone)
                                    <option value="{{ auth()->user()->timezone }}" 
                                        {{ request('timezone') == auth()->user()->timezone ? 'selected' : '' }}>
                                        My Timezone ({{ auth()->user()->timezone_display ?? auth()->user()->timezone }})
                                    </option>
                                @endif
                                @php
                                    $commonTimezones = \App\Models\UserAvailabilitySchedule::getCommonTimezones();
                                @endphp
                                @foreach($commonTimezones as $tz => $label)
                                    <option value="{{ $tz }}" {{ request('timezone') == $tz ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-outline-primary w-100">
                                <i class="ti ti-search me-2"></i>Filter Jobs
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Jobs List -->
            @if(isset($jobs) && $jobs->count() > 0)
                <div class="row">
                    @foreach($jobs as $job)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 {{ $job->is_featured ? 'border-primary' : '' }}">
                                @if($job->is_featured)
                                    <div class="card-header bg-primary text-white py-2">
                                        <small><i class="ti ti-star me-1"></i> Featured Job</small>
                                    </div>
                                @endif
                                
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title mb-1">{{ $job->title }}</h5>
                                        @if($job->is_urgent)
                                            <span class="badge bg-danger">Urgent</span>
                                        @endif
                                    </div>
                                    
                                    <p class="text-muted small mb-2">
                                        by {{ $job->user->name ?? 'Anonymous' }}
                                    </p>
                                    
                                    <p class="card-text">{{ Str::limit($job->description, 120) }}</p>
                                    
                                    <div class="mb-3">
                                        <span class="badge bg-secondary me-1">{{ ucfirst(str_replace('_', ' ', $job->market)) }}</span>
                                        <span class="badge bg-outline-secondary me-1">{{ ucfirst($job->experience_level) }}</span>
                                        <span class="badge bg-outline-secondary me-1">{{ ucfirst(str_replace('_', ' ', $job->contract_type)) }}</span>
                                        
                                        @if($job->timezone_flexible)
                                            <span class="badge bg-success me-1"><i class="ti ti-world"></i> Timezone Flexible</span>
                                        @elseif($job->required_timezone)
                                            @php
                                                $timezones = \App\Models\UserAvailabilitySchedule::getCommonTimezones();
                                                $timezoneLabel = $timezones[$job->required_timezone] ?? $job->required_timezone;
                                            @endphp
                                            <span class="badge bg-info me-1"><i class="ti ti-clock"></i> {{ $timezoneLabel }}</span>
                                        @endif
                                        
                                        @if($job->shift_start_time && $job->shift_end_time && !$job->timezone_flexible)
                                            @php
                                                $userTimezone = auth()->check() && auth()->user()->timezone ? auth()->user()->timezone : 'UTC';
                                                $shiftInfo = $job->getShiftTimeInTimezone($userTimezone);
                                            @endphp
                                            @if($shiftInfo && !isset($shiftInfo['error']))
                                                <span class="badge bg-warning text-dark me-1">
                                                    <i class="ti ti-clock"></i> {{ $shiftInfo['start_time'] }}-{{ $shiftInfo['end_time'] }}
                                                    @if($userTimezone !== $job->required_timezone)
                                                        ({{ $userTimezone }})
                                                    @endif
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                    
                                    <div class="row text-center mb-3">
                                        <div class="col-4">
                                            <small class="text-muted">Rate</small>
                                            <div class="fw-bold">
                                                @if($job->rate_type === 'hourly' && $job->hourly_rate)
                                                    ${{ $job->hourly_rate }}/hr
                                                @elseif($job->rate_type === 'fixed' && $job->fixed_rate)
                                                    ${{ number_format($job->fixed_rate) }}
                                                @elseif($job->rate_type === 'commission' && $job->commission_percentage)
                                                    {{ $job->commission_percentage }}%
                                                @else
                                                    Negotiable
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted">Hours/Week</small>
                                            <div class="fw-bold">{{ $job->expected_hours_per_week }}h</div>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted">Applications</small>
                                            <div class="fw-bold">{{ $job->current_applications }}/{{ $job->max_applications }}</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card-footer bg-transparent">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            Posted {{ $job->created_at->diffForHumans() }}
                                        </small>
                                        
                                        @auth
                                            @if(auth()->user()->isAgency())
                                                <!-- Agencies see job management options -->
                                                @if($job->user_id === auth()->id())
                                                    <a href="{{ route('marketplace.jobs.edit', $job) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="ti ti-edit"></i> Manage
                                                    </a>
                                                @else
                                                    <a href="{{ route('marketplace.jobs.show', $job) }}" class="btn btn-sm btn-outline-secondary">
                                                        <i class="ti ti-eye"></i> View
                                                    </a>
                                                @endif
                                            @else
                                                <!-- Talents see apply button -->
                                                @if($job->current_applications < $job->max_applications)
                                                    <a href="{{ route('marketplace.jobs.show', $job) }}" class="btn btn-sm btn-primary">
                                                        <i class="ti ti-send"></i> Apply Now
                                                    </a>
                                                @else
                                                    <span class="badge bg-secondary">Applications Full</span>
                                                @endif
                                            @endif
                                        @else
                                            <!-- Guest users -->
                                            <a href="{{ route('custom.login') }}" class="btn btn-sm btn-outline-primary">
                                                Login to Apply
                                            </a>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                @if(method_exists($jobs, 'links'))
                    <div class="d-flex justify-content-center">
                        {{ $jobs->links() }}
                    </div>
                @endif
            @else
                <!-- No Jobs Found -->
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="ti ti-briefcase-off display-1 text-muted mb-3"></i>
                        <h4>No Jobs Found</h4>
                        <p class="text-muted mb-4">There are currently no jobs matching your criteria.</p>
                        
                        @auth
                            @if(auth()->user()->isAgency())
                                <a href="{{ route('marketplace.jobs.create') }}" class="btn btn-primary">
                                    <i class="ti ti-plus me-2"></i>Post the First Job
                                </a>
                            @else
                                <a href="{{ route('marketplace.jobs.index') }}" class="btn btn-outline-primary">
                                    <i class="ti ti-refresh me-2"></i>Clear Filters
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
