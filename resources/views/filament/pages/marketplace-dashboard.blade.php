<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Recent Jobs -->
        <div class="bg-white rounded-lg shadow p-6 md:col-span-2">
            <h3 class="text-lg font-semibold mb-4">Recent Job Posts</h3>
            <div class="space-y-4">
                @php
                    $recentJobs = \App\Models\JobPost::with('user')->latest()->limit(5)->get();
                @endphp
                @foreach($recentJobs as $job)
                    <div class="border-l-4 border-blue-500 pl-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-medium">{{ $job->title }}</h4>
                                <p class="text-sm text-gray-600">by {{ $job->user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $job->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if($job->status === 'active') bg-green-100 text-green-800 
                                    @elseif($job->status === 'draft') bg-yellow-100 text-yellow-800 
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($job->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- User Activity -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">User Activity</h3>
            @php
                $recentUsers = \App\Models\User::with('userType')->latest()->limit(5)->get();
            @endphp
            <div class="space-y-3">
                @foreach($recentUsers as $user)
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-bold">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $user->userType->display_name ?? 'User' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <a href="{{ route('filament.admin.resources.job-posts.index') }}" 
                   class="block w-full bg-blue-500 text-white text-center py-2 px-4 rounded hover:bg-blue-600">
                    Manage Jobs
                </a>
                <a href="{{ route('filament.admin.resources.users.index') }}" 
                   class="block w-full bg-green-500 text-white text-center py-2 px-4 rounded hover:bg-green-600">
                    Manage Users
                </a>
                <a href="{{ route('filament.admin.resources.job-applications.index') }}" 
                   class="block w-full bg-purple-500 text-white text-center py-2 px-4 rounded hover:bg-purple-600">
                    View Applications
                </a>
                <a href="{{ route('filament.admin.resources.messages.index') }}" 
                   class="block w-full bg-orange-500 text-white text-center py-2 px-4 rounded hover:bg-orange-600">
                    View Messages
                </a>
            </div>
        </div>

        <!-- Revenue Overview -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Revenue Overview</h3>
            @php
                $featuredJobs = \App\Models\JobPost::where('is_featured', true)->count();
                $urgentJobs = \App\Models\JobPost::where('is_urgent', true)->count();
                $potentialRevenue = ($featuredJobs * 10) + ($urgentJobs * 5);
            @endphp
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Featured Jobs</span>
                    <span class="text-sm font-medium">{{ $featuredJobs }} × $10</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Urgent Jobs</span>
                    <span class="text-sm font-medium">{{ $urgentJobs }} × $5</span>
                </div>
                <div class="border-t pt-3">
                    <div class="flex justify-between font-semibold">
                        <span>Potential Revenue</span>
                        <span class="text-green-600">${{ $potentialRevenue }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Status -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">System Status</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Database</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Online
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Messaging</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Active
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Job Queue</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Running
                    </span>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
