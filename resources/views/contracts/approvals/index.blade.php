<x-layouts.app>

<div class="bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-900 dark:to-gray-800 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">
                        Pending 
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-600 to-orange-600">Approvals</span>
                    </h1>
                    <p class="text-gray-600 dark:text-gray-300">Contracts waiting for your approval</p>
                </div>
                <div>
                    <a href="{{ route('contracts.index') }}" class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 shadow transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        All Contracts
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4 mb-6">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-green-800 dark:text-green-300">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 mb-6">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-red-800 dark:text-red-300">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if($pendingContracts->isEmpty())
            <div class="text-center py-12">
                <svg class="mx-auto h-16 w-16 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">No pending approvals</h3>
                <p class="mt-1 text-gray-500 dark:text-gray-400">You have no contracts waiting for approval.</p>
            </div>
        @else
            <!-- Pending Contracts Grid -->
            <div class="space-y-6">
                @foreach($pendingContracts as $contract)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border-2 border-yellow-200 dark:border-yellow-700 bg-gradient-to-r from-yellow-50/30 to-white dark:from-yellow-900/10 dark:to-gray-800 overflow-hidden">
                        <!-- Status Strip -->
                        <div class="h-1 w-full bg-gradient-to-r from-yellow-400 to-orange-500"></div>
                        
                        <div class="p-6">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-lg">
                                        {{ substr($contract->employer->name, 0, 1) }}
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                            Contract from {{ $contract->employer->name }}
                                        </h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">
                                            {{ ucfirst($contract->contract_type) }} Contract • {{ $contract->formatted_rate }}
                                        </p>
                                        @if($contract->jobPost)
                                            <p class="text-xs text-indigo-600 dark:text-indigo-400 mt-1">
                                                Related to: {{ $contract->jobPost->title }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Pending Badge -->
                                <div class="flex-shrink-0">
                                    <div class="inline-flex items-center px-3 py-2 rounded-xl text-sm font-bold shadow-lg bg-yellow-500 text-white">
                                        <div class="w-2 h-2 bg-white rounded-full mr-2 animate-pulse"></div>
                                        PENDING APPROVAL
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Contract Details -->
                            <div class="mb-6">
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description:</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                                        {{ $contract->description }}
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Contract Info -->
                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div class="bg-gradient-to-r from-gray-50 to-blue-50 dark:from-gray-700/50 dark:to-blue-900/20 rounded-lg p-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Start Date</span>
                                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $contract->start_date->format('M j, Y') }}</span>
                                    </div>
                                </div>
                                
                                <div class="bg-gradient-to-r from-gray-50 to-blue-50 dark:from-gray-700/50 dark:to-blue-900/20 rounded-lg p-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Created</span>
                                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $contract->created_at->format('M j, Y') }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex items-center justify-between">
                                <a href="{{ route('contracts.approvals.show', $contract) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    View Details
                                </a>
                                
                                <div class="flex space-x-3">
                                    <!-- Reject Button -->
                                    <button onclick="showRejectModal({{ $contract->id }})" class="inline-flex items-center px-4 py-2 text-sm font-medium text-red-700 bg-red-50 border border-red-300 rounded-lg hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Reject
                                    </button>
                                    
                                    <!-- Accept Button -->
                                    <form method="POST" action="{{ route('contracts.approvals.accept', $contract) }}" class="inline">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Are you sure you want to accept this contract? This action cannot be undone.')" class="inline-flex items-center px-6 py-2 text-sm font-medium text-white bg-gradient-to-r from-green-600 to-green-700 border border-transparent rounded-lg hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 shadow-lg transition-all duration-200 transform hover:scale-105">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Accept Contract
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <div class="mt-2 px-7 py-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white text-center">Reject Contract</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 text-center">
                    Please provide a reason for rejecting this contract (optional).
                </p>
                <form id="rejectForm" method="POST" class="mt-4">
                    @csrf
                    <div class="mb-4">
                        <label for="rejection_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Reason for rejection
                        </label>
                        <textarea name="rejection_reason" id="rejection_reason" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white"
                                  placeholder="Optional: Let the employer know why you're rejecting this contract..."></textarea>
                    </div>
                    <div class="flex justify-center space-x-3">
                        <button type="button" onclick="hideRejectModal()" 
                                class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                            Reject Contract
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function showRejectModal(contractId) {
    document.getElementById('rejectForm').action = `/contracts/approvals/${contractId}/reject`;
    document.getElementById('rejectModal').classList.remove('hidden');
}

function hideRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejection_reason').value = '';
}

// Close modal when clicking outside
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideRejectModal();
    }
});
</script>

</x-layouts.app>
