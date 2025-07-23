<x-layouts.app>
    <div class="min-h-screen bg-white dark:bg-zinc-900">
        <div class="max-w-7xl mx-auto px-4 py-8">
            <!-- Header -->
            <div class="mb-10">
                <div class="text-center">
                    <h1 class="text-5xl font-black text-gray-900 dark:text-white mb-4">
                        Browse 
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">Jobs</span>
                    </h1>
                    <p class="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">Find your next opportunity in the OnlyFans ecosystem marketplace</p>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="mb-8">
                <div class="bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg p-6">
                    <form method="GET" action="{{ route('marketplace.jobs') }}">
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                            <div class="col-span-2">
                                <input type="text" name="search" class="w-full px-3 py-2 border border-gray-300 dark:border-zinc-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-zinc-700 dark:text-white" placeholder="Search jobs..." value="{{ request('search') }}">
                            </div>
                            <div>
                                <select name="market" class="w-full px-3 py-2 border border-gray-300 dark:border-zinc-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-zinc-700 dark:text-white">
                                    <option value="">All Markets</option>
                                    <option value="management" {{ request('market') == 'management' ? 'selected' : '' }}>Management</option>
                                    <option value="chatting" {{ request('market') == 'chatting' ? 'selected' : '' }}>Chatting</option>
                                    <option value="content_creation" {{ request('market') == 'content_creation' ? 'selected' : '' }}>Content Creation</option>
                                    <option value="marketing" {{ request('market') == 'marketing' ? 'selected' : '' }}>Marketing</option>
                                    <option value="design" {{ request('market') == 'design' ? 'selected' : '' }}>Design</option>
                                </select>
                            </div>
                            <div>
                                <select name="experience_level" class="w-full px-3 py-2 border border-gray-300 dark:border-zinc-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-zinc-700 dark:text-white">
                                    <option value="">All Levels</option>
                                    <option value="beginner" {{ request('experience_level') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                    <option value="intermediate" {{ request('experience_level') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                    <option value="advanced" {{ request('experience_level') == 'advanced' ? 'selected' : '' }}>Advanced</option>
                                </select>
                            </div>
                            <div>
                                <select name="rate_type" class="w-full px-3 py-2 border border-gray-300 dark:border-zinc-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-zinc-700 dark:text-white">
                                    <option value="">All Rate Types</option>
                                    <option value="hourly" {{ request('rate_type') == 'hourly' ? 'selected' : '' }}>Hourly</option>
                                    <option value="fixed" {{ request('rate_type') == 'fixed' ? 'selected' : '' }}>Fixed</option>
                                    <option value="commission" {{ request('rate_type') == 'commission' ? 'selected' : '' }}>Commission</option>
                                </select>
                            </div>
                            <div>
                                <button type="submit" class="w-full bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors">Search</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Jobs Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-6">
                @forelse($jobs as $job)
                <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg p-4 hover:shadow-md transition-shadow h-full flex flex-col justify-between">
                    <div>
                        <div class="flex items-center mb-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mr-3">
                                <span class="text-white font-medium text-sm">{{ substr($job->user->name, 0, 2) }}</span>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900 dark:text-gray-100 text-sm">{{ $job->user->name }}</div>
                                <div class="text-gray-500 dark:text-gray-400 text-xs">{{ $job->user->userType->display_name }}</div>
                            </div>
                        </div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ $job->title }}</h4>
                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">{{ Str::limit($job->description, 100) }}</p>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs">{{ ucfirst($job->market) }}</span>
                            <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs">{{ ucfirst($job->experience_level) }}</span>
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">{{ ucfirst($job->contract_type) }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                @if($job->rate_type === 'hourly')
                                    <div class="text-lg font-bold text-green-600">${{ $job->hourly_rate }}/hr</div>
                                @elseif($job->rate_type === 'fixed')
                                    <div class="text-lg font-bold text-green-600">${{ $job->fixed_rate }}</div>
                                @else
                                    <div class="text-lg font-bold text-green-600">{{ $job->commission_percentage }}%</div>
                                @endif
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $job->current_applications }} applications</div>
                            </div>
                        </div>
                        <a href="{{ route('marketplace.jobs.show', $job) }}" class="block bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors text-center text-sm">View Details</a>
                        <div class="text-gray-500 dark:text-gray-400 text-xs text-center mt-2">Posted {{ $job->created_at->diffForHumans() }}</div>
                    </div>
                </div>
                @empty
                <div class="col-span-full text-center py-12">
                    <div class="mb-4">
                        <i class="fas fa-briefcase text-gray-400 text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">No jobs found</h3>
                    <p class="text-gray-600 dark:text-gray-400">Try adjusting your search filters or check back later for new opportunities.</p>
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
</x-layouts.app>
