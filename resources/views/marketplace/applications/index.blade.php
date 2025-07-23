<x-theme::layouts.app>
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <div class="container mx-auto px-6 py-8">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">
                            My 
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">Applications</span>
                        </h1>
                        <p class="mt-2 text-lg text-gray-600 dark:text-gray-300">Track all your job applications and their status</p>
                    </div>
                    <a href="{{ route('marketplace.jobs') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Browse Jobs
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-200">
                    <div class="flex items-center">
                        <div class="p-3 rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Applications</h3>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $applications->total() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-200">
                    <div class="flex items-center">
                        <div class="p-3 rounded-xl bg-gradient-to-r from-yellow-500 to-orange-500 text-white shadow-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending</h3>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $applications->where('status', 'pending')->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-200">
                    <div class="flex items-center">
                        <div class="p-3 rounded-xl bg-gradient-to-r from-green-500 to-green-600 text-white shadow-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Accepted</h3>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $applications->where('status', 'accepted')->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-200">
                    <div class="flex items-center">
                        <div class="p-3 rounded-xl bg-gradient-to-r from-red-500 to-red-600 text-white shadow-lg">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Rejected</h3>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $applications->where('status', 'rejected')->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Applications Grid -->
            @if($applications->count() > 0)
                <div class="grid grid-cols-1 gap-6 mb-8">
                    @foreach($applications as $application)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-all duration-200 overflow-hidden">
                            <!-- Application Header -->
                            <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex-1">
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                                            <a href="{{ route('marketplace.jobs.show', $application->jobPost) }}" class="hover:text-blue-600 transition-colors">
                                                {{ $application->jobPost->title }}
                                            </a>
                                        </h3>
                                        <div class="flex flex-wrap items-center gap-3 mb-3">
                                            @if($application->status === 'pending')
                                                <span class="px-3 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 rounded-full">
                                                    ⏳ Pending
                                                </span>
                                            @elseif($application->status === 'accepted' || $application->status === 'hired')
                                                <span class="px-3 py-1 text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-full">
                                                    ✅ {{ ucfirst($application->status) }}
                                                </span>
                                            @elseif($application->status === 'rejected')
                                                <span class="px-3 py-1 text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 rounded-full">
                                                    ❌ Rejected
                                                </span>
                                            @elseif($application->status === 'withdrawn')
                                                <span class="px-3 py-1 text-xs font-semibold bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 rounded-full">
                                                    🚫 Withdrawn
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Job Info Grid -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                    <div class="flex items-center text-gray-500 dark:text-gray-400">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        {{ $application->jobPost->user->name }}
                                    </div>
                                    <div class="flex items-center text-gray-500 dark:text-gray-400">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Applied {{ $application->created_at->diffForHumans() }}
                                    </div>
                                    <div class="flex items-center text-green-600 dark:text-green-400 font-semibold">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                        @if($application->jobPost->rate_type === 'hourly')
                                            ${{ number_format($application->jobPost->hourly_rate, 2) }}/hr
                                        @elseif($application->jobPost->rate_type === 'fixed')
                                            ${{ number_format($application->jobPost->fixed_rate, 2) }}
                                        @else
                                            {{ $application->jobPost->commission_percentage }}%
                                        @endif
                                    </div>
                                </div>

                                @if($application->cover_letter)
                                    <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                        <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-200 mb-2">Your Cover Letter:</h4>
                                        <p class="text-sm text-blue-800 dark:text-blue-300">{{ Str::limit($application->cover_letter, 200) }}</p>
                                    </div>
                                @endif

                                @if($application->proposed_rate)
                                    <div class="mt-3 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                                        <span class="text-sm font-semibold text-green-900 dark:text-green-200">Your proposed rate:</span>
                                        <span class="text-sm text-green-800 dark:text-green-300 ml-2">
                                            ${{ number_format($application->proposed_rate, 2) }}{{ $application->jobPost->rate_type === 'hourly' ? '/hour' : '' }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <!-- Action Buttons -->
                            <div class="p-6 bg-gray-50 dark:bg-gray-700/50">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('marketplace.jobs.show', $application->jobPost) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 616 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            View Job
                                        </a>
                                        @if($application->status === 'pending')
                                            <button onclick="withdrawApplication({{ $application->id }}, '{{ addslashes($application->jobPost->title) }}')" class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition-colors">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                Withdraw
                                            </button>
                                        @endif
                                        @if($application->jobPost->user_id !== auth()->id())
                                            <a href="{{ route('marketplace.messages.create', $application->jobPost->user) }}?job_id={{ $application->jobPost->id }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-colors">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                                </svg>
                                                Message
                                            </a>
                                        @endif
                                    </div>
                                </div>

                                @if($application->status === 'accepted' && $application->jobPost->status === 'active')
                                    <div class="mt-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="text-sm font-medium text-green-800 dark:text-green-200">
                                                🎉 Congratulations! Your application has been accepted. The employer may contact you soon.
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="flex justify-center">
                    {{ $applications->links() }}
                </div>
            @else
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No applications yet</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">Start applying to jobs to track your applications here.</p>
                    <a href="{{ route('marketplace.jobs') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium">
                        Browse Jobs
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Withdraw Application Modal -->
<div id="withdrawModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="relative mx-auto p-6 border max-w-md w-full mx-4 shadow-xl rounded-lg bg-white dark:bg-gray-800 dark:border-gray-600">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-red-500 to-red-600 rounded-t-lg"></div>
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white" id="withdrawModalTitle">
                    Withdraw Application
                </h3>
                <button onclick="closeWithdrawModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500 dark:text-gray-400" id="withdrawModalContent">
                    Are you sure you want to withdraw your application?
                </p>
            </div>
            <div class="flex items-center justify-end px-4 py-3 space-x-3">
                <button onclick="closeWithdrawModal()" class="px-4 py-2 bg-gray-300 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-400 transition-colors">
                    Cancel
                </button>
                <button onclick="confirmWithdraw()" id="withdrawButton" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 transition-colors">
                    Withdraw
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentApplicationId = null;

function withdrawApplication(applicationId, jobTitle) {
    currentApplicationId = applicationId;
    document.getElementById('withdrawModalTitle').textContent = 'Withdraw Application';
    document.getElementById('withdrawModalContent').textContent = 
        `Are you sure you want to withdraw your application for "${jobTitle}"? This action cannot be undone.`;
    document.getElementById('withdrawModal').classList.remove('hidden');
}

function closeWithdrawModal() {
    document.getElementById('withdrawModal').classList.add('hidden');
    currentApplicationId = null;
}

function confirmWithdraw() {
    if (!currentApplicationId) return;
    
    const withdrawButton = document.getElementById('withdrawButton');
    withdrawButton.disabled = true;
    withdrawButton.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Withdrawing...';
    
    fetch(`/marketplace/applications/${currentApplicationId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        credentials: 'same-origin'
    })
    .then(response => {
        if (response.ok) {
            return response.json();
        }
        throw new Error('Failed to withdraw application');
    })
    .then(data => {
        closeWithdrawModal();
        // Show success message
        showNotification('Application withdrawn successfully!', 'success');
        // Reload the page to update the applications list
        setTimeout(() => window.location.reload(), 1000);
    })
    .catch(error => {
        console.error('Error:', error);
        withdrawButton.disabled = false;
        withdrawButton.innerHTML = 'Withdraw';
        showNotification('Failed to withdraw application. Please try again.', 'error');
    });
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transition-all duration-300 ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Close modal when clicking outside
document.getElementById('withdrawModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeWithdrawModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeWithdrawModal();
    }
});
</script>
</x-theme::layouts.app>
