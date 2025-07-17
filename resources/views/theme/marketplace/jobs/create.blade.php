@extends('theme::app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h2 mb-2">Post a New Job</h1>
                        <p class="text-muted">Find the perfect talent for your project</p>
                    </div>
                    <a href="{{ route('marketplace.jobs') }}" class="btn btn-secondary">
                        Back to Jobs
                    </a>
                </div>
            </div>

            <!-- Subscription Usage Info -->
            @if(auth()->user()->currentSubscription())
                @php
                    $stats = app(App\Services\SubscriptionService::class)->getSubscriptionStats(auth()->user());
                @endphp
                <div class="card mb-4">
                    <div class="card-header">
                        <h4 class="card-title">Subscription Usage</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Current Plan</label>
                                    <p class="mb-1"><strong>{{ $stats['plan_name'] }}</strong></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Job Posts Used</label>
                                    <p class="mb-1">{{ $stats['job_posts_used'] }} / {{ $stats['job_posts_limit'] ?: 'Unlimited' }}</p>
                                    @if($stats['job_posts_limit'])
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: {{ min(($stats['job_posts_used'] / $stats['job_posts_limit']) * 100, 100) }}%"></div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Featured Jobs</label>
                                    <p class="mb-1">
                                        @if($stats['features']['featured_status'])
                                            <span class="badge bg-success">Free Featured Jobs</span>
                                        @else
                                            <span class="badge bg-secondary">$10 per featured job</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        @if($stats['job_posts_limit'] && $stats['job_posts_used'] >= $stats['job_posts_limit'])
                            <div class="alert alert-warning mt-3">
                                <i class="ti ti-alert-triangle me-2"></i>
                                You have reached your job posting limit. Please upgrade your plan to post more jobs.
                                <a href="{{ route('subscription.plans') }}" class="btn btn-sm btn-primary ms-2">Upgrade Plan</a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Job Creation Form -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Job Details</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('marketplace.jobs.store') }}">
                        @csrf
                        
                        <!-- Job Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Job Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Job Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Job Description <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" rows="6" 
                                      class="form-control @error('description') is-invalid @enderror" 
                                      required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Market and Experience Level -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="market" class="form-label">Market <span class="text-danger">*</span></label>
                                    <select name="market" id="market" 
                                            class="form-select @error('market') is-invalid @enderror" 
                                            required>
                                        <option value="">Select Market</option>
                                        <option value="english" {{ old('market') == 'english' ? 'selected' : '' }}>English</option>
                                        <option value="spanish" {{ old('market') == 'spanish' ? 'selected' : '' }}>Spanish</option>
                                        <option value="french" {{ old('market') == 'french' ? 'selected' : '' }}>French</option>
                                        <option value="german" {{ old('market') == 'german' ? 'selected' : '' }}>German</option>
                                    </select>
                                    @error('market')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="experience_level" class="form-label">Experience Level <span class="text-danger">*</span></label>
                                    <select name="experience_level" id="experience_level" 
                                            class="form-select @error('experience_level') is-invalid @enderror" 
                                            required>
                                        <option value="">Select Experience Level</option>
                                        <option value="beginner" {{ old('experience_level') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                        <option value="intermediate" {{ old('experience_level') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                        <option value="advanced" {{ old('experience_level') == 'advanced' ? 'selected' : '' }}>Advanced</option>
                                    </select>
                                    @error('experience_level')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Contract Type and Rate Type -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="contract_type" class="form-label">Contract Type <span class="text-danger">*</span></label>
                                    <select name="contract_type" id="contract_type" 
                                            class="form-select @error('contract_type') is-invalid @enderror" 
                                            required>
                                        <option value="">Select Contract Type</option>
                                        <option value="full_time" {{ old('contract_type') == 'full_time' ? 'selected' : '' }}>Full Time</option>
                                        <option value="part_time" {{ old('contract_type') == 'part_time' ? 'selected' : '' }}>Part Time</option>
                                        <option value="contract" {{ old('contract_type') == 'contract' ? 'selected' : '' }}>Contract</option>
                                    </select>
                                    @error('contract_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="rate_type" class="form-label">Rate Type <span class="text-danger">*</span></label>
                                    <select name="rate_type" id="rate_type" 
                                            class="form-select @error('rate_type') is-invalid @enderror" 
                                            required>
                                        <option value="">Select Rate Type</option>
                                        <option value="hourly" {{ old('rate_type') == 'hourly' ? 'selected' : '' }}>Hourly</option>
                                        <option value="fixed" {{ old('rate_type') == 'fixed' ? 'selected' : '' }}>Fixed</option>
                                        <option value="commission" {{ old('rate_type') == 'commission' ? 'selected' : '' }}>Commission</option>
                                    </select>
                                    @error('rate_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Rate Fields -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="hourly_rate" class="form-label">Hourly Rate ($)</label>
                                    <input type="number" name="hourly_rate" id="hourly_rate" value="{{ old('hourly_rate') }}" 
                                           step="0.01" min="0"
                                           class="form-control @error('hourly_rate') is-invalid @enderror">
                                    @error('hourly_rate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="fixed_rate" class="form-label">Fixed Rate ($)</label>
                                    <input type="number" name="fixed_rate" id="fixed_rate" value="{{ old('fixed_rate') }}" 
                                           step="0.01" min="0"
                                           class="form-control @error('fixed_rate') is-invalid @enderror">
                                    @error('fixed_rate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="commission_percentage" class="form-label">Commission (%)</label>
                                    <input type="number" name="commission_percentage" id="commission_percentage" value="{{ old('commission_percentage') }}" 
                                           step="0.01" min="0" max="100"
                                           class="form-control @error('commission_percentage') is-invalid @enderror">
                                    @error('commission_percentage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Hours and Duration -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="expected_hours_per_week" class="form-label">Expected Hours per Week <span class="text-danger">*</span></label>
                                    <input type="number" name="expected_hours_per_week" id="expected_hours_per_week" value="{{ old('expected_hours_per_week') }}" 
                                           min="1" max="80"
                                           class="form-control @error('expected_hours_per_week') is-invalid @enderror" 
                                           required>
                                    @error('expected_hours_per_week')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="duration_months" class="form-label">Duration (Months) <span class="text-danger">*</span></label>
                                    <input type="number" name="duration_months" id="duration_months" value="{{ old('duration_months') }}" 
                                           min="1" max="36"
                                           class="form-control @error('duration_months') is-invalid @enderror" 
                                           required>
                                    @error('duration_months')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Typing Speed and Start Date -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="min_typing_speed" class="form-label">Minimum Typing Speed (WPM)</label>
                                    <input type="number" name="min_typing_speed" id="min_typing_speed" value="{{ old('min_typing_speed') }}" 
                                           min="20" max="150"
                                           class="form-control @error('min_typing_speed') is-invalid @enderror">
                                    @error('min_typing_speed')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" 
                                           class="form-control @error('start_date') is-invalid @enderror">
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Requirements -->
                        <div class="mb-3">
                            <label for="requirements" class="form-label">Requirements</label>
                            <textarea name="requirements" id="requirements" rows="4" 
                                      class="form-control @error('requirements') is-invalid @enderror">{{ old('requirements') }}</textarea>
                            @error('requirements')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Benefits -->
                        <div class="mb-3">
                            <label for="benefits" class="form-label">Benefits</label>
                            <textarea name="benefits" id="benefits" rows="4" 
                                      class="form-control @error('benefits') is-invalid @enderror">{{ old('benefits') }}</textarea>
                            @error('benefits')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Max Applications -->
                        <div class="mb-3">
                            <label for="max_applications" class="form-label">Maximum Applications <span class="text-danger">*</span></label>
                            <input type="number" name="max_applications" id="max_applications" value="{{ old('max_applications', 50) }}" 
                                   min="1" max="200"
                                   class="form-control @error('max_applications') is-invalid @enderror" 
                                   required>
                            @error('max_applications')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Featured and Urgent -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="is_featured" id="is_featured" 
                                               class="form-check-input"
                                               {{ old('is_featured') ? 'checked' : '' }}>
                                        <label for="is_featured" class="form-check-label">
                                            Featured Job (Additional cost may apply)
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="is_urgent" id="is_urgent" 
                                               class="form-check-input"
                                               {{ old('is_urgent') ? 'checked' : '' }}>
                                        <label for="is_urgent" class="form-check-label">
                                            Urgent Job (Additional cost may apply)
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="ti ti-plus me-2"></i>
                                Post Job
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
