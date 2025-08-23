@extends('theme::app')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">User Management</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Manage all platform users</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium">
                    Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
            <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Search Users
                    </label>
                    <input type="text" 
                           id="search" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Name, email, username..."
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- User Type Filter -->
                <div>
                    <label for="user_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        User Type
                    </label>
                    <select name="user_type" id="user_type" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Types</option>
                        @foreach(\App\Models\UserType::all() as $userType)
                            <option value="{{ $userType->name }}" {{ request('user_type') === $userType->name ? 'selected' : '' }}>
                                {{ $userType->display_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Status
                    </label>
                    <select name="status" id="status" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="banned" {{ request('status') === 'banned' ? 'selected' : '' }}>Banned</option>
                        <option value="email_verified" {{ request('status') === 'email_verified' ? 'selected' : '' }}>Email Verified</option>
                        <option value="email_unverified" {{ request('status') === 'email_unverified' ? 'selected' : '' }}>Email Unverified</option>
                        <option value="kyc_verified" {{ request('status') === 'kyc_verified' ? 'selected' : '' }}>KYC Verified</option>
                        <option value="earnings_verified" {{ request('status') === 'earnings_verified' ? 'selected' : '' }}>Earnings Verified</option>
                    </select>
                </div>

                <!-- Sort -->
                <div>
                    <label for="sort" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Sort By
                    </label>
                    <div class="flex space-x-2">
                        <select name="sort" id="sort" class="flex-1 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                            <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Created Date</option>
                            <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Name</option>
                            <option value="email" {{ request('sort') === 'email' ? 'selected' : '' }}>Email</option>
                            <option value="last_seen_at" {{ request('sort') === 'last_seen_at' ? 'selected' : '' }}>Last Seen</option>
                        </select>
                        <select name="direction" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring-blue-500">
                            <option value="desc" {{ request('direction') === 'desc' ? 'selected' : '' }}>Desc</option>
                            <option value="asc" {{ request('direction') === 'asc' ? 'selected' : '' }}>Asc</option>
                        </select>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="md:col-span-4 flex justify-end space-x-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
                        Apply Filters
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Users Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Users ({{ $users->total() }})
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                User
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Type & Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Verification Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Activity
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($users as $user)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <!-- User Info -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <img src="{{ $user->avatar() }}" alt="{{ $user->name }}" class="h-10 w-10 rounded-full">
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $user->name }}
                                                @if($user->is_banned)
                                                    <span class="inline-block bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full ml-2">
                                                        Banned
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                                            @if($user->username)
                                                <div class="text-xs text-gray-400">@{{ $user->username }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Type & Status -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        @if($user->userType)
                                            <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                                {{ $user->userType->display_name }}
                                            </span>
                                        @else
                                            <span class="text-gray-500">No Type</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        @if($user->isOnline())
                                            <span class="text-green-600">Online</span>
                                        @elseif($user->last_seen_at)
                                            Last seen {{ $user->last_seen_at->diffForHumans() }}
                                        @else
                                            Never logged in
                                        @endif
                                    </div>
                                </td>

                                <!-- Verification Status -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="space-y-1">
                                        <!-- Email Verification -->
                                        <div class="flex items-center">
                                            @if($user->email_verified_at)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Email ✓
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Email
                                                </span>
                                            @endif
                                        </div>

                                        <!-- KYC Status -->
                                        @if($user->kycVerification)
                                            @php
                                                $kycStatus = $user->kycVerification->status;
                                                $statusColor = match($kycStatus) {
                                                    'approved' => 'green',
                                                    'pending' => 'yellow',
                                                    'rejected' => 'red',
                                                    default => 'gray'
                                                };
                                            @endphp
                                            <div class="flex items-center">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800">
                                                    KYC: {{ ucfirst($kycStatus) }}
                                                </span>
                                            </div>
                                        @endif

                                        <!-- Earnings Status -->
                                        @if($user->earningsVerification)
                                            @php
                                                $earningsStatus = $user->earningsVerification->status;
                                                $statusColor = match($earningsStatus) {
                                                    'approved' => 'green',
                                                    'pending' => 'yellow',
                                                    'rejected' => 'red',
                                                    default => 'gray'
                                                };
                                            @endphp
                                            <div class="flex items-center">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800">
                                                    Earnings: {{ ucfirst($earningsStatus) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                <!-- Activity -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        <div>{{ $user->job_posts_count }} jobs</div>
                                        <div>{{ $user->job_applications_count }} applications</div>
                                        <div>{{ $user->sent_messages_count }} messages</div>
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        Joined {{ $user->created_at->format('M d, Y') }}
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <!-- View User -->
                                        <a href="{{ route('admin.users.show', $user) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs">
                                            View
                                        </a>

                                        <!-- Ban/Unban Toggle -->
                                        @if(!$user->isAdmin())
                                            @if($user->is_banned)
                                                <form method="POST" action="{{ route('admin.users.unban', $user) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs"
                                                            onclick="return confirm('Are you sure you want to unban this user?')">
                                                        Unban
                                                    </button>
                                                </form>
                                            @else
                                                <button onclick="openBanModal('{{ $user->id }}', '{{ $user->name }}')" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs">
                                                    Ban
                                                </button>
                                            @endif

                                            <!-- Impersonate -->
                                            <form method="POST" action="{{ route('admin.users.impersonate', $user) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded text-xs"
                                                        onclick="return confirm('Are you sure you want to impersonate this user?')">
                                                    Login As
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    No users found matching your criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-white dark:bg-gray-800 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $users->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Ban User Modal -->
<div id="banModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black bg-opacity-50">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Ban User
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1" id="banModalUserName">
                    You are about to ban this user.
                </p>
            </div>

            <form id="banForm" method="POST" class="px-6 py-4">
                @csrf
                <div class="mb-4">
                    <label for="banReason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Ban Reason <span class="text-red-500">*</span>
                    </label>
                    <textarea id="banReason" 
                              name="reason" 
                              rows="3" 
                              required
                              placeholder="Please provide a reason for banning this user..."
                              class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-red-500 focus:ring-red-500"></textarea>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeBanModal()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                        Cancel
                    </button>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                        Ban User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openBanModal(userId, userName) {
    document.getElementById('banModal').classList.remove('hidden');
    document.getElementById('banModalUserName').textContent = `You are about to ban ${userName}.`;
    document.getElementById('banForm').action = `/admin/users/${userId}/ban`;
    document.getElementById('banReason').value = '';
    document.getElementById('banReason').focus();
}

function closeBanModal() {
    document.getElementById('banModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('banModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeBanModal();
    }
});
</script>
@endsection
