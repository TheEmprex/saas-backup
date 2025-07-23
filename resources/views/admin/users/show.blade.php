@extends('theme::app')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">User Details</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Manage user account and information</p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                    Back to Users
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- User Information -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center">
                            <img src="{{ $user->avatar() }}" alt="{{ $user->name }}" class="w-16 h-16 rounded-full mr-4">
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $user->name }}</h3>
                                <p class="text-gray-600 dark:text-gray-400">{{ $user->email }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Username</label>
                                <p class="text-gray-900 dark:text-white">{{ $user->username ?? 'Not Set' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">User Type</label>
                                <p class="text-gray-900 dark:text-white">{{ $user->userType->name ?? 'Not Set' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Verified</label>
                                <div class="flex items-center">
                                    @if($user->email_verified_at)
                                        <span class="inline-block w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                        <span class="text-green-600 dark:text-green-400">Verified</span>
                                        <span class="text-gray-500 dark:text-gray-400 ml-2 text-sm">
                                            ({{ $user->email_verified_at->format('M d, Y') }})
                                        </span>
                                    @else
                                        <span class="inline-block w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                        <span class="text-red-600 dark:text-red-400">Not Verified</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                                <div class="flex items-center">
                                    @if($user->is_banned)
                                        <span class="inline-block w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                        <span class="text-red-600 dark:text-red-400">Banned</span>
                                    @else
                                        <span class="inline-block w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                        <span class="text-green-600 dark:text-green-400">Active</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Joined</label>
                                <p class="text-gray-900 dark:text-white">{{ $user->created_at->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Last Seen</label>
                                <p class="text-gray-900 dark:text-white">{{ $user->last_seen_at ? $user->last_seen_at->diffForHumans() : 'Never' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Profile -->
                @if($user->userProfile)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Profile Information</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Location</label>
                                    <p class="text-gray-900 dark:text-white">{{ $user->userProfile->location ?? 'Not specified' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Languages</label>
                                    <p class="text-gray-900 dark:text-white">{{ $user->userProfile->languages ?? 'Not specified' }}</p>
                                </div>
                            </div>
                            @if($user->userProfile->bio)
                                <div class="mt-6">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bio</label>
                                    <p class="text-gray-900 dark:text-white">{{ $user->userProfile->bio }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Verification Status -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Verification Status</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">KYC Verification</label>
                                @if($user->kycVerification)
                                    @if($user->kycVerification->status === 'pending')
                                        <span class="inline-block bg-yellow-100 text-yellow-800 text-sm px-2 py-1 rounded-full">Pending</span>
                                    @elseif($user->kycVerification->status === 'approved')
                                        <span class="inline-block bg-green-100 text-green-800 text-sm px-2 py-1 rounded-full">Approved</span>
                                    @elseif($user->kycVerification->status === 'rejected')
                                        <span class="inline-block bg-red-100 text-red-800 text-sm px-2 py-1 rounded-full">Rejected</span>
                                    @endif
                                    <a href="{{ route('admin.kyc.show', $user->kycVerification) }}" class="text-blue-600 hover:text-blue-800 text-sm ml-2">View</a>
                                @else
                                    <span class="text-gray-500 dark:text-gray-400">Not submitted</span>
                                @endif
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Earnings Verification</label>
                                @if($user->earningsVerification)
                                    @if($user->earningsVerification->status === 'pending')
                                        <span class="inline-block bg-yellow-100 text-yellow-800 text-sm px-2 py-1 rounded-full">Pending</span>
                                    @elseif($user->earningsVerification->status === 'approved')
                                        <span class="inline-block bg-green-100 text-green-800 text-sm px-2 py-1 rounded-full">Approved</span>
                                    @elseif($user->earningsVerification->status === 'rejected')
                                        <span class="inline-block bg-red-100 text-red-800 text-sm px-2 py-1 rounded-full">Rejected</span>
                                    @endif
                                    <a href="{{ route('admin.earnings.show', $user->earningsVerification) }}" class="text-blue-600 hover:text-blue-800 text-sm ml-2">View</a>
                                @else
                                    <span class="text-gray-500 dark:text-gray-400">Not submitted</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activity Stats -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Activity Statistics</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $user->job_posts_count }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Jobs Posted</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $user->job_applications_count }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Applications</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $user->sent_messages_count }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Messages</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $user->ratingsReceived->count() }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Reviews</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Jobs -->
                @if($user->jobPosts->count() > 0)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Jobs ({{ $user->jobPosts->count() }})</h3>
                        </div>
                        <div class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($user->jobPosts as $job)
                                <div class="p-6">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="font-medium text-gray-900 dark:text-white">{{ $job->title }}</h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ Str::limit($job->description, 100) }}</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">{{ $job->created_at->diffForHumans() }}</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="inline-block px-2 py-1 text-xs rounded-full 
                                                {{ $job->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ ucfirst($job->status) }}
                                            </span>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">${{ number_format($job->budget) }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div>
                <!-- Quick Actions -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Actions</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        @if($user->is_banned)
                            <form method="POST" action="{{ route('admin.users.unban', $user) }}" class="w-full">
                                @csrf
                                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg">
                                    Unban User
                                </button>
                            </form>
                        @else
                            <button type="button" onclick="document.getElementById('ban-modal').style.display='block'" 
                                    class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg">
                                Ban User
                            </button>
                        @endif

                        @if($user->email_verified_at)
                            <form method="POST" action="{{ route('admin.users.unverify-email', $user) }}" class="w-full">
                                @csrf
                                <button type="submit" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-lg">
                                    Unverify Email
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.users.verify-email', $user) }}" class="w-full">
                                @csrf
                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg">
                                    Verify Email
                                </button>
                            </form>
                        @endif

                        @if(!$user->isAdmin())
                            <form method="POST" action="{{ route('admin.users.impersonate', $user) }}" class="w-full">
                                @csrf
                                <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-lg">
                                    Impersonate User
                                </button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('admin.users.delete', $user) }}" 
                              onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')" class="w-full">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg">
                                Delete User
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Subscriptions -->
                @if($user->subscriptions->count() > 0)
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Subscriptions</h3>
                        </div>
                        <div class="p-6">
                            @foreach($user->subscriptions as $subscription)
                                <div class="mb-4 last:mb-0">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-white">{{ $subscription->subscriptionPlan->name }}</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                @if($subscription->expires_at)
                                                    Expires: {{ $subscription->expires_at->format('M d, Y') }}
                                                @else
                                                    Never expires
                                                @endif
                                            </p>
                                        </div>
                                        <span class="inline-block px-2 py-1 text-xs rounded-full 
                                            {{ $subscription->expires_at && $subscription->expires_at->isPast() ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                            {{ $subscription->expires_at && $subscription->expires_at->isPast() ? 'Expired' : 'Active' }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Ban User Modal -->
<div id="ban-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg max-w-md w-full">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Ban User</h3>
                <form method="POST" action="{{ route('admin.users.ban', $user) }}">
                    @csrf
                    <div class="mb-4">
                        <label for="ban-reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Reason for ban</label>
                        <textarea name="reason" id="ban-reason" rows="3" required
                                  class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" 
                                  placeholder="Please provide a reason for banning this user..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="document.getElementById('ban-modal').style.display='none'" 
                                class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                            Cancel
                        </button>
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg">
                            Ban User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
