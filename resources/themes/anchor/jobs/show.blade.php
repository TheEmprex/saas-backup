<x-layouts.app>

<div class="bg-white dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <a href="{{ route('jobs.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                ← Back to My Jobs
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 mb-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-3">
                                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $job->title }}</h1>
                                @if($job->is_featured)
                                    <span class="bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 px-2 py-1 rounded text-xs font-medium">Featured</span>
                                @endif
                                @if($job->is_urgent)
                                    <span class="bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 px-2 py-1 rounded text-xs font-medium">Urgent</span>
                                @endif
                            </div>
                            
                            <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400 mb-4">
                                <span class="font-medium">{{ $job->user->name }}</span>
                                @if($job->user->userProfile && $job->user->userProfile->average_rating > 0)
                                    <span>
                                        ⭐ {{ number_format($job->user->userProfile->average_rating, 1) }}
                                        ({{ $job->user->userProfile->total_ratings }} reviews)
                                    </span>
                                @endif
                                <span>Posted {{ $job->created_at->diffForHumans() }}</span>
                            </div>

                            <div class="text-2xl font-bold text-green-600 dark:text-green-400 mb-4">
                                @if($job->rate_type === 'hourly')
                                    ${{ $job->hourly_rate }}/hr
                                @elseif($job->rate_type === 'fixed')
                                    ${{ $job->fixed_rate }}
                                @else
                                    {{ $job->commission_percentage }}% commission
                                @endif
                            </div>

                            <div class="flex flex-wrap gap-2 mb-6">
                                <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-3 py-1 rounded-full text-sm">
                                    {{ ucfirst($job->market) }}
                                </span>
                                <span class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 px-3 py-1 rounded-full text-sm">
                                    {{ ucfirst($job->experience_level) }}
                                </span>
                                <span class="bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 px-3 py-1 rounded-full text-sm">
                                    {{ ucfirst($job->contract_type) }}
                                </span>
                                @if($job->min_typing_speed)
                                    <span class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-3 py-1 rounded-full text-sm">
                                        {{ $job->min_typing_speed }}+ WPM
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="prose max-w-none">
                        <h2 class="text-xl font-semibold mb-4 text-gray-900 dark:text-white">Job Description</h2>
                        <div class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $job->description }}</div>
                    </div>

                    @if($job->requirements)
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold mb-3 text-gray-900 dark:text-white">Requirements</h3>
                        <div class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $job->requirements }}</div>
                    </div>
                    @endif

                    @if($job->benefits)
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold mb-3 text-gray-900 dark:text-white">Benefits</h3>
                        <div class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $job->benefits }}</div>
                    </div>
                    @endif

                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-900 dark:text-white">Applications:</span>
                                <span class="text-gray-600 dark:text-gray-400">{{ $job->current_applications }}/{{ $job->max_applications }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-900 dark:text-white">Expected Hours:</span>
                                <span class="text-gray-600 dark:text-gray-400">{{ $job->expected_hours_per_week ?? $job->hours_per_week }} hours/week</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-900 dark:text-white">Start Date:</span>
                                <span class="text-gray-600 dark:text-gray-400">{{ $job->start_date ? $job->start_date->format('M j, Y') : 'Immediate' }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-900 dark:text-white">Duration:</span>
                                <span class="text-gray-600 dark:text-gray-400">{{ $job->duration_months ?? 'Not specified' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 sticky top-6">
                    @auth
                        @if(auth()->user()->id === $job->user_id)
                            <div class="text-center">
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">This is your job posting.</p>
                                <div class="space-y-3">
                                    <a href="{{ route('jobs.applications', $job->id) }}" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors inline-block text-center">
                                        View Applications ({{ $job->current_applications }})
                                    </a>
                                    <a href="{{ route('jobs.edit', $job->id) }}" class="w-full bg-gray-600 text-white py-2 px-4 rounded-md hover:bg-gray-700 transition-colors inline-block text-center">
                                        Edit Job
                                    </a>
                                </div>
                            </div>
                        @else
                            @php
                                $hasApplied = $job->applications()->where('user_id', auth()->id())->exists();
                            @endphp
                            
                            @if($hasApplied)
                                <div class="text-center">
                                    <div class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 px-4 py-2 rounded-md mb-4">
                                        ✓ Application Submitted
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">You have already applied to this job.</p>
                                </div>
                            @elseif($job->current_applications >= $job->max_applications)
                                <div class="text-center">
                                    <div class="bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 px-4 py-2 rounded-md mb-4">
                                        Job Full
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">This job has reached its maximum applications.</p>
                                </div>
                            @else
                                <div class="text-center">
                                    <a href="{{ route('marketplace.jobs.show', $job->id) }}" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors inline-block text-center">
                                        Apply for This Job
                                    </a>
                                </div>
                            @endif
                        @endif
                    @else
                        <div class="text-center">
                            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Ready to apply?</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Join our marketplace to apply for this job.</p>
                            <a href="{{ route('login') }}" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors inline-block text-center">
                                Login to Apply
                            </a>
                        </div>
                    @endauth
                </div>

                <!-- Employer Info -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 mt-6">
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">About the Employer</h3>
                    <div class="flex items-center mb-3">
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                            {{ substr($job->user->name, 0, 1) }}
                        </div>
                        <div class="ml-3">
                            <div class="font-medium text-gray-900 dark:text-white">{{ $job->user->name }}</div>
                            @if($job->user->userProfile && $job->user->userProfile->average_rating > 0)
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    ⭐ {{ number_format($job->user->userProfile->average_rating, 1) }}
                                    ({{ $job->user->userProfile->total_ratings }} reviews)
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    @if($job->user->userProfile && $job->user->userProfile->bio)
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ Str::limit($job->user->userProfile->bio, 150) }}</p>
                    @endif
                    
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        <div>Member since {{ $job->user->created_at->format('M Y') }}</div>
                        <div>{{ $job->user->jobPosts()->count() }} jobs posted</div>
                    </div>
                    
                    @auth
                        @if(auth()->user()->id !== $job->user_id)
                            <div class="mt-4">
                                <a href="{{ route('messages.create', $job->user_id) }}" class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition-colors text-center inline-block">
                                    Send Message
                                </a>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>

</x-layouts.app>
