@extends('theme::app')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Admin Dashboard</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Manage your platform from here</p>
                </div>
                
                <!-- Impersonation notice -->
                @if(session('impersonating_admin'))
                    <div class="bg-yellow-100 border border-yellow-300 rounded-lg px-4 py-2">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <span class="text-yellow-800">You are impersonating a user</span>
                            <form method="POST" action="{{ route('admin.stop-impersonating') }}" class="ml-3">
                                @csrf
                                <button type="submit" class="text-yellow-600 hover:text-yellow-800 font-medium underline">
                                    Stop Impersonating
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <!-- Total Users -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-500 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['total_users']) }}</p>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Total Users</p>
                    </div>
                </div>
                <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                    +{{ $stats['new_users_today'] }} today
                </div>
            </div>

            <!-- Active Jobs -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-500 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['active_jobs']) }}</p>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Active Jobs</p>
                    </div>
                </div>
                <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                    +{{ $stats['jobs_today'] }} today
                </div>
            </div>

            <!-- Pending Verifications -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-500 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['kyc_pending'] + $stats['earnings_pending'] }}</p>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Pending Verifications</p>
                    </div>
                </div>
                <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                    {{ $stats['kyc_pending'] }} KYC, {{ $stats['earnings_pending'] }} Earnings
                </div>
            </div>

            <!-- Review Contests -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-orange-500 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['contests_pending'] ?? 0 }}</p>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Review Contests</p>
                    </div>
                </div>
                <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                    {{ ($stats['contests_total'] ?? 0) }} total submitted
                </div>
            </div>

            <!-- Banned Users -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-red-500 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($stats['banned_users']) }}</p>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Banned Users</p>
                    </div>
                </div>
                <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                    {{ number_format($stats['email_unverified_users']) }} unverified emails
                </div>
            </div>
        </div>

        <!-- Navigation Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- User Management -->
            <a href="{{ route('admin.users.index') }}" class="block bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">User Management</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Manage users, bans, and permissions</p>
                    </div>
                </div>
            </a>

            <!-- Job Management -->
            <a href="{{ route('admin.jobs.index') }}" class="block bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                        <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Job Management</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Manage job posts and applications</p>
                    </div>
                </div>
            </a>

            <!-- KYC Verification -->
            <a href="{{ route('admin.kyc.index') }}" class="block bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 dark:bg-yellow-900 rounded-lg">
                        <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">KYC Verification</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Review and approve KYC submissions</p>
                        @if($stats['kyc_pending'] > 0)
                            <span class="inline-block bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full mt-1">
                                {{ $stats['kyc_pending'] }} pending
                            </span>
                        @endif
                    </div>
                </div>
            </a>

            <!-- Earnings Verification -->
            <a href="{{ route('admin.earnings.index') }}" class="block bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-lg">
                        <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Earnings Verification</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Review agency earnings submissions</p>
                        @if($stats['earnings_pending'] > 0)
                            <span class="inline-block bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full mt-1">
                                {{ $stats['earnings_pending'] }} pending
                            </span>
                        @endif
                    </div>
                </div>
            </a>

            <!-- Message Management -->
            <a href="{{ route('admin.messages.index') }}" class="block bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-indigo-100 dark:bg-indigo-900 rounded-lg">
                        <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Messages</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Monitor platform communications</p>
                    </div>
                </div>
            </a>

            <!-- Contest Management -->
            <a href="{{ route('admin.contests.index') }}" class="block bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-lg">
                        <svg class="w-8 h-8 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Review Contests</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">Manage review contest submissions</p>
                        @if(isset($stats['contests_pending']) && $stats['contests_pending'] > 0)
                            <span class="inline-block bg-orange-100 text-orange-800 text-xs px-2 py-1 rounded-full mt-1">
                                {{ $stats['contests_pending'] }} pending
                            </span>
                        @endif
                    </div>
                </div>
            </a>

            <!-- System Analytics -->
            <a href="{{ route('platform.analytics') }}" class="block bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                        <svg class="w-8 h-8 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Analytics</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm">View detailed system analytics</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Users -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Users</h3>
                </div>
                <div class="p-0">
                    @if($recentUsers->count() > 0)
                        @foreach($recentUsers as $user)
                            <div class="flex items-center justify-between p-4 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                                <div class="flex items-center">
                                    <img src="{{ $user->avatar() }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full">
                                    <div class="ml-3">
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="flex items-center space-x-2">
                                        @if($user->email_verified_at)
                                            <span class="inline-block w-2 h-2 bg-green-500 rounded-full" title="Email verified"></span>
                                        @else
                                            <span class="inline-block w-2 h-2 bg-red-500 rounded-full" title="Email not verified"></span>
                                        @endif
                                        @if($user->is_banned)
                                            <span class="inline-block w-2 h-2 bg-red-500 rounded-full" title="Banned"></span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $user->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                            No recent users
                        </div>
                    @endif
                </div>
            </div>

            <!-- Users Requiring Attention -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Requires Attention</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Users with pending actions</p>
                </div>
                <div class="p-0">
                    @if($usersRequiringAttention->count() > 0)
                        @foreach($usersRequiringAttention as $user)
                            <div class="flex items-center justify-between p-4 border-b border-gray-100 dark:border-gray-700 last:border-b-0">
                                <div class="flex items-center">
                                    <img src="{{ $user->avatar() }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full">
                                    <div class="ml-3">
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</p>
                                        <div class="flex items-center space-x-2 mt-1">
                                            @if($user->is_banned)
                                                <span class="inline-block bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">Banned</span>
                                            @endif
                                            @if(!$user->email_verified_at)
                                                <span class="inline-block bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">Unverified Email</span>
                                            @endif
                                            @if($user->kycVerification && $user->kycVerification->status === 'pending')
                                                <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">KYC Pending</span>
                                            @endif
                                            @if($user->earningsVerification && $user->earningsVerification->status === 'pending')
                                                <span class="inline-block bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full">Earnings Pending</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <a href="{{ route('admin.users.show', $user) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        View
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                            No users requiring attention
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
