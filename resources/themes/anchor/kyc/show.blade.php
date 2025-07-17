<x-layouts.marketing
    :seo="[
        'title'         => 'KYC Verification Status - OnlyFans Management Marketplace',
        'description'   => 'View your KYC verification status and details.',
        'image'         => url('/og_image.png'),
        'type'          => 'website'
    ]"
>

<div class="bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">KYC Verification Status</h1>
            <p class="text-gray-600">View your identity verification status and details.</p>
        </div>

        <!-- Status Overview -->
        <div class="bg-white border border-gray-200 rounded-lg p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Verification Status</h2>
            
            <div class="flex items-center space-x-4 mb-4">
                <div class="flex-shrink-0">
                    @if($kycVerification->status === 'approved')
                        <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-full">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    @elseif($kycVerification->status === 'rejected')
                        <div class="flex items-center justify-center w-12 h-12 bg-red-100 rounded-full">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                    @else
                        <div class="flex items-center justify-center w-12 h-12 bg-yellow-100 rounded-full">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    @endif
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">{{ $kycVerification->status_label }}</h3>
                    <p class="text-sm text-gray-600">
                        @if($kycVerification->status === 'approved')
                            Your identity has been verified successfully.
                        @elseif($kycVerification->status === 'rejected')
                            Your verification was rejected. Please review the feedback and resubmit.
                        @else
                            Your verification is being reviewed. This typically takes 24-48 hours.
                        @endif
                    </p>
                </div>
            </div>

            @if($kycVerification->status === 'rejected' && $kycVerification->rejection_reason)
                <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-4">
                    <h4 class="text-sm font-medium text-red-800 mb-2">Rejection Reason</h4>
                    <p class="text-sm text-red-700">{{ $kycVerification->rejection_reason }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="font-medium text-gray-900">Submitted:</span>
                    <span class="text-gray-600">{{ $kycVerification->submitted_at ? $kycVerification->submitted_at->format('M j, Y \a\t g:i A') : 'Not submitted' }}</span>
                </div>
                @if($kycVerification->reviewed_at)
                    <div>
                        <span class="font-medium text-gray-900">Reviewed:</span>
                        <span class="text-gray-600">{{ $kycVerification->reviewed_at->format('M j, Y \a\t g:i A') }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Personal Information -->
        <div class="bg-white border border-gray-200 rounded-lg p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Personal Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="font-medium text-gray-900">Full Name:</span>
                    <span class="text-gray-600">{{ $kycVerification->full_name }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-900">Date of Birth:</span>
                    <span class="text-gray-600">{{ $kycVerification->date_of_birth->format('M j, Y') }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-900">Phone Number:</span>
                    <span class="text-gray-600">{{ $kycVerification->phone_number }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-900">Country:</span>
                    <span class="text-gray-600">{{ $kycVerification->country }}</span>
                </div>
            </div>
        </div>

        <!-- Address Information -->
        <div class="bg-white border border-gray-200 rounded-lg p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Address Information</h2>
            
            <div class="space-y-2 text-sm">
                <div>
                    <span class="font-medium text-gray-900">Address:</span>
                    <span class="text-gray-600">{{ $kycVerification->address }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-900">City:</span>
                    <span class="text-gray-600">{{ $kycVerification->city }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-900">State/Province:</span>
                    <span class="text-gray-600">{{ $kycVerification->state }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-900">Postal Code:</span>
                    <span class="text-gray-600">{{ $kycVerification->postal_code }}</span>
                </div>
            </div>
        </div>

        <!-- Document Information -->
        <div class="bg-white border border-gray-200 rounded-lg p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Document Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="font-medium text-gray-900">Document Type:</span>
                    <span class="text-gray-600">{{ $kycVerification->document_type_label }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-900">Document Number:</span>
                    <span class="text-gray-600">{{ $kycVerification->id_document_number }}</span>
                </div>
            </div>
        </div>

        <!-- Uploaded Documents -->
        <div class="bg-white border border-gray-200 rounded-lg p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Uploaded Documents</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @if($kycVerification->id_document_front_path)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="font-medium text-gray-900 mb-2">ID Document Front</h3>
                        <a href="{{ route('kyc.download', [$kycVerification->id, 'id_front']) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                            Download File
                        </a>
                    </div>
                @endif

                @if($kycVerification->id_document_back_path)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="font-medium text-gray-900 mb-2">ID Document Back</h3>
                        <a href="{{ route('kyc.download', [$kycVerification->id, 'id_back']) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                            Download File
                        </a>
                    </div>
                @endif

                @if($kycVerification->selfie_path)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="font-medium text-gray-900 mb-2">Selfie</h3>
                        <a href="{{ route('kyc.download', [$kycVerification->id, 'selfie']) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                            Download File
                        </a>
                    </div>
                @endif

                @if($kycVerification->proof_of_address_path)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <h3 class="font-medium text-gray-900 mb-2">Proof of Address</h3>
                        <a href="{{ route('kyc.download', [$kycVerification->id, 'proof']) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                            Download File
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-center space-x-4">
            <a href="{{ route('dashboard') }}" class="bg-gray-600 text-white px-6 py-2 rounded-md hover:bg-gray-700 transition-colors">
                Back to Dashboard
            </a>
            
            @if($kycVerification->status === 'rejected')
                <a href="{{ route('kyc.create') }}" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors">
                    Resubmit KYC
                </a>
            @endif
        </div>
    </div>
</div>

</x-layouts.marketing>
