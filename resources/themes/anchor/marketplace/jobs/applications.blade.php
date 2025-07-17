<x-layouts.marketing
    :seo="[
        'title'         => 'Job Applications - ' . $job->title,
        'description'   => 'Manage applications for your job posting.',
        'image'         => url('/og_image.png'),
        'type'          => 'website'
    ]"
>

<div class="bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <a href="{{ route('jobs.show', $job->id) }}" class="text-blue-600 hover:text-blue-800">
                ← Back to Job
            </a>
        </div>

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Applications for "{{ $job->title }}"</h1>
            <p class="text-gray-600">{{ $job->applications->count() }} applications received</p>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="space-y-6">
            @forelse($job->applications as $application)
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-start justify-between">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                            {{ substr($application->user->name, 0, 1) }}
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $application->user->name }}</h3>
                            <div class="flex items-center text-sm text-gray-600">
                                @if($application->user->userProfile && $application->user->userProfile->average_rating > 0)
                                    <span class="mr-4">
                                        ⭐ {{ number_format($application->user->userProfile->average_rating, 1) }}
                                        ({{ $application->user->userProfile->total_ratings }} reviews)
                                    </span>
                                @endif
                                <span>Applied {{ $application->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <div class="text-lg font-semibold text-green-600">
                                @if($job->rate_type === 'hourly')
                                    ${{ $application->proposed_rate }}/hr
                                @elseif($job->rate_type === 'fixed')
                                    ${{ $application->proposed_rate }}
                                @else
                                    {{ $application->proposed_rate }}%
                                @endif
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $application->available_hours }} hours/week
                            </div>
                        </div>
                        
                        <div class="flex items-center">
                            @if($application->status === 'pending')
                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-sm">Pending</span>
                            @elseif($application->status === 'accepted')
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">Accepted</span>
                            @elseif($application->status === 'rejected')
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-sm">Rejected</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <h4 class="font-medium text-gray-900 mb-2">Cover Letter</h4>
                    <p class="text-gray-700 whitespace-pre-line">{{ $application->cover_letter }}</p>
                </div>

                @if($application->user->userProfile)
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <h4 class="font-medium text-gray-900 mb-2">Profile Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            @if($application->user->userProfile->bio)
                                <div class="md:col-span-2">
                                    <span class="font-medium text-gray-700">Bio:</span>
                                    <p class="text-gray-600 mt-1">{{ Str::limit($application->user->userProfile->bio, 150) }}</p>
                                </div>
                            @endif
                            @if($application->user->userProfile->typing_speed_wpm)
                                <div>
                                    <span class="font-medium text-gray-700">Typing Speed:</span>
                                    <p class="text-gray-600">{{ $application->user->userProfile->typing_speed_wpm }} WPM</p>
                                </div>
                            @endif
                            @if($application->user->userProfile->experience_years)
                                <div>
                                    <span class="font-medium text-gray-700">Experience:</span>
                                    <p class="text-gray-600">{{ $application->user->userProfile->experience_years }} years</p>
                                </div>
                            @endif
                            @if($application->user->userProfile->languages)
                                <div>
                                    <span class="font-medium text-gray-700">Languages:</span>
                                    <p class="text-gray-600">{{ implode(', ', json_decode($application->user->userProfile->languages, true) ?? []) }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                @if($application->status === 'pending')
                    <div class="mt-6 pt-4 border-t border-gray-200">
                        <div class="flex space-x-4">
                            <form action="{{ route('jobs.applications.update', [$job->id, $application->id]) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="accepted">
                                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                                    Accept Application
                                </button>
                            </form>
                            
                            <form action="{{ route('jobs.applications.update', [$job->id, $application->id]) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors">
                                    Reject Application
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
            @empty
            <div class="text-center py-12">
                <div class="text-gray-400 mb-4">
                    <svg class="mx-auto h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No applications yet</h3>
                <p class="text-gray-600">Applications will appear here once candidates apply to your job.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

</x-layouts.marketing>
