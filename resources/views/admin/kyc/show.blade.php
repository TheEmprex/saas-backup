@extends('theme::app')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">KYC Verification Details</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Review and manage KYC verification</p>
                </div>
                <a href="{{ route('admin.kyc.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                    Back to KYC List
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- User Information -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">User Information</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name</label>
                                <p class="text-gray-900 dark:text-white">{{ $verification->user->name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                                <p class="text-gray-900 dark:text-white">{{ $verification->user->email }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">User Type</label>
                                <p class="text-gray-900 dark:text-white">{{ $verification->user->userType->name ?? 'Not Set' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Account Created</label>
                                <p class="text-gray-900 dark:text-white">{{ $verification->user->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KYC Information -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">KYC Verification Details</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Full Name</label>
                                <p class="text-gray-900 dark:text-white">{{ $verification->first_name }} {{ $verification->last_name }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date of Birth</label>
                                <p class="text-gray-900 dark:text-white">{{ $verification->date_of_birth ? $verification->date_of_birth->format('Y-m-d') : 'Not provided' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ID Type</label>
                                <p class="text-gray-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $verification->id_document_type ?? '')) }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ID Number</label>
                                <p class="text-gray-900 dark:text-white">{{ $verification->id_document_number }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone Number</label>
                                <p class="text-gray-900 dark:text-white">{{ $verification->phone_number }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">City</label>
                                <p class="text-gray-900 dark:text-white">{{ $verification->city }}, {{ $verification->state }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Postal Code</label>
                                <p class="text-gray-900 dark:text-white">{{ $verification->postal_code }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Country</label>
                                <p class="text-gray-900 dark:text-white">{{ $verification->country }}</p>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Address</label>
                            <p class="text-gray-900 dark:text-white">{{ $verification->address }}</p>
                        </div>

                        <!-- ID Documents -->
                        <div class="mb-6">
                            <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Verification Documents</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @if($verification->id_document_front_path)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ID Document (Front)</label>
                                        <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-2">
                                            <img src="{{ route('admin.kyc.preview', [$verification, 'id_document_front']) }}" 
                                                 alt="ID Document Front" 
                                                 class="w-full h-48 object-cover rounded cursor-pointer hover:opacity-80"
                                                 onclick="openImageModal(this.src)">
                                        </div>
                                        <div class="mt-2 text-center">
                                            <a href="{{ route('admin.kyc.download', [$verification, 'id_document_front']) }}" 
                                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                Download
                                            </a>
                                        </div>
                                    </div>
                                @endif

                                @if($verification->id_document_back_path)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ID Document (Back)</label>
                                        <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-2">
                                            <img src="{{ route('admin.kyc.preview', [$verification, 'id_document_back']) }}" 
                                                 alt="ID Document Back" 
                                                 class="w-full h-48 object-cover rounded cursor-pointer hover:opacity-80"
                                                 onclick="openImageModal(this.src)">
                                        </div>
                                        <div class="mt-2 text-center">
                                            <a href="{{ route('admin.kyc.download', [$verification, 'id_document_back']) }}" 
                                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                Download
                                            </a>
                                        </div>
                                    </div>
                                @endif

                                @if($verification->selfie_path)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Selfie</label>
                                        <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-2">
                                            <img src="{{ route('admin.kyc.preview', [$verification, 'selfie']) }}" 
                                                 alt="Selfie" 
                                                 class="w-full h-48 object-cover rounded cursor-pointer hover:opacity-80"
                                                 onclick="openImageModal(this.src)">
                                        </div>
                                        <div class="mt-2 text-center">
                                            <a href="{{ route('admin.kyc.download', [$verification, 'selfie']) }}" 
                                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                Download
                                            </a>
                                        </div>
                                    </div>
                                @endif

                                @if($verification->proof_of_address_path)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Proof of Address</label>
                                        <div class="border border-gray-300 dark:border-gray-600 rounded-lg p-2">
                                            <img src="{{ route('admin.kyc.preview', [$verification, 'proof_of_address']) }}" 
                                                 alt="Proof of Address" 
                                                 class="w-full h-48 object-cover rounded cursor-pointer hover:opacity-80"
                                                 onclick="openImageModal(this.src)">
                                        </div>
                                        <div class="mt-2 text-center">
                                            <a href="{{ route('admin.kyc.download', [$verification, 'proof_of_address']) }}" 
                                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                Download
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if($verification->rejection_reason)
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rejection Reason</label>
                                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                                    <p class="text-red-800 dark:text-red-200">{{ $verification->rejection_reason }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div>
                <!-- Status Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Status</h3>
                    </div>
                    <div class="p-6">
                        <div class="mb-4">
                            @if($verification->status === 'pending')
                                <span class="inline-block bg-yellow-100 text-yellow-800 text-sm px-3 py-1 rounded-full">Pending</span>
                            @elseif($verification->status === 'approved')
                                <span class="inline-block bg-green-100 text-green-800 text-sm px-3 py-1 rounded-full">Approved</span>
                            @elseif($verification->status === 'rejected')
                                <span class="inline-block bg-red-100 text-red-800 text-sm px-3 py-1 rounded-full">Rejected</span>
                            @endif
                        </div>
                        
                        <div class="text-sm text-gray-600 dark:text-gray-400 space-y-2">
                            <p><strong>Submitted:</strong> {{ $verification->created_at->format('M d, Y H:i') }}</p>
                            @if($verification->verified_at)
                                <p><strong>Verified:</strong> {{ $verification->verified_at->format('M d, Y H:i') }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                @if($verification->status === 'pending')
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Actions</h3>
                        </div>
                        <div class="p-6">
                            <form method="POST" action="{{ route('admin.kyc.update-status', $verification) }}">
                                @csrf
                                @method('PATCH')
                                
                                <div class="mb-4">
                                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                                    <select name="status" id="status" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        <option value="pending" {{ $verification->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved">Approved</option>
                                        <option value="rejected">Rejected</option>
                                    </select>
                                </div>
                                
                                <div class="mb-4" id="rejection-reason-field" style="display: none;">
                                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rejection Reason</label>
                                    <textarea name="rejection_reason" id="rejection_reason" rows="3" 
                                              class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" 
                                              placeholder="Please provide a reason for rejection..."></textarea>
                                </div>
                                
                                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg">
                                    Update Status
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50" onclick="closeImageModal()">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="max-w-4xl max-h-full">
            <img id="modalImage" src="" alt="Full size" class="max-w-full max-h-full object-contain rounded">
        </div>
    </div>
</div>

<script>
document.getElementById('status').addEventListener('change', function() {
    const rejectionField = document.getElementById('rejection-reason-field');
    if (this.value === 'rejected') {
        rejectionField.style.display = 'block';
        document.getElementById('rejection_reason').required = true;
    } else {
        rejectionField.style.display = 'none';
        document.getElementById('rejection_reason').required = false;
    }
});

function openImageModal(src) {
    document.getElementById('modalImage').src = src;
    document.getElementById('imageModal').classList.remove('hidden');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
}

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});
</script>
@endsection
