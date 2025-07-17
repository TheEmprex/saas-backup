<x-layouts.marketing
    :seo="[
        'title'         => 'Browse Jobs - OnlyFans Management Marketplace',
        'description'   => 'Find chatting jobs with verified OnlyFans management agencies.',
        'image'         => url('/og_image.png'),
        'type'          => 'website'
    ]"
>

<div class="bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Browse Jobs</h1>
                <p class="text-gray-600 mt-1">{{ $jobs->total() }} jobs available</p>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('marketplace.index') }}" class="text-blue-600 hover:text-blue-800">
                    ← Back to Marketplace
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-gray-50 p-6 rounded-lg mb-8">
            <form method="GET" action="{{ route('marketplace.jobs') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Market</label>
                    <select name="market" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Markets</option>
                        <option value="english" {{ request('market') == 'english' ? 'selected' : '' }}>English</option>
                        <option value="spanish" {{ request('market') == 'spanish' ? 'selected' : '' }}>Spanish</option>
                        <option value="french" {{ request('market') == 'french' ? 'selected' : '' }}>French</option>
                        <option value="german" {{ request('market') == 'german' ? 'selected' : '' }}>German</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Experience Level</label>
                    <select name="experience_level" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Levels</option>
                        <option value="beginner" {{ request('experience_level') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                        <option value="intermediate" {{ request('experience_level') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                        <option value="advanced" {{ request('experience_level') == 'advanced' ? 'selected' : '' }}>Advanced</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Type</label>
                    <select name="rate_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Types</option>
                        <option value="hourly" {{ request('rate_type') == 'hourly' ? 'selected' : '' }}>Hourly</option>
                        <option value="fixed" {{ request('rate_type') == 'fixed' ? 'selected' : '' }}>Fixed Price</option>
                        <option value="commission" {{ request('rate_type') == 'commission' ? 'selected' : '' }}>Commission</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">
                        Filter Jobs
                    </button>
                </div>
            </form>
        </div>

        <!-- Jobs Grid -->
        <div class="grid gap-6">
            @forelse($jobs as $job)
            <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-3">
                            <h3 class="text-xl font-semibold text-gray-900">{{ $job->title }}</h3>
                            @if($job->is_featured)
                                <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-medium">Featured</span>
                            @endif
                            @if($job->is_urgent)
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-medium">Urgent</span>
                            @endif
                        </div>
                        
                        <div class="text-sm text-gray-600 mb-3">
                            <span class="font-medium">{{ $job->user->name }}</span>
                            @if($job->user->userProfile && $job->user->userProfile->average_rating > 0)
                                <span class="ml-2">
                                    ⭐ {{ number_format($job->user->userProfile->average_rating, 1) }}
                                    ({{ $job->user->userProfile->total_ratings }} reviews)
                                </span>
                            @endif
                        </div>

                        <p class="text-gray-700 mb-4">{{ Str::limit($job->description, 200) }}</p>

                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">
                                {{ ucfirst($job->market) }}
                            </span>
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">
                                {{ ucfirst($job->experience_level) }}
                            </span>
                            <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded text-xs">
                                {{ ucfirst($job->contract_type) }}
                            </span>
                            @if($job->min_typing_speed)
                                <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs">
                                    {{ $job->min_typing_speed }}+ WPM
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-500">
                                <span>{{ $job->current_applications }}/{{ $job->max_applications }} applications</span>
                                <span class="mx-2">•</span>
                                <span>Posted {{ $job->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="text-xl font-bold text-green-600">
                                @if($job->rate_type === 'hourly')
                                    ${{ $job->hourly_rate }}/hr
                                @elseif($job->rate_type === 'fixed')
                                    ${{ $job->fixed_rate }}
                                @else
                                    {{ $job->commission_percentage }}%
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 flex gap-3">
                    <a href="{{ route('jobs.show', $job->id) }}" class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors text-center">
                        View Details
                    </a>
                    @auth
                        <button class="bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition-colors">
                            Quick Apply
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition-colors">
                            Login to Apply
                        </a>
                    @endauth
                </div>
            </div>
            @empty
            <div class="text-center py-12">
                <div class="text-gray-400 mb-4">
                    <svg class="mx-auto h-16 w-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6m0 0v6m0-6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2m8 0H8"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No jobs found</h3>
                <p class="text-gray-600">Try adjusting your filters or check back later for new opportunities.</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($jobs->hasPages())
        <div class="mt-8">
            {{ $jobs->links() }}
        </div>
        @endif
    </div>
</div>

</x-layouts.marketing>
