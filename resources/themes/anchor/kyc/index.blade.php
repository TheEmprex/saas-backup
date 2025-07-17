<x-layouts.marketing
    :seo="[
        'title'         => 'KYC Verification - OnlyFans Management Marketplace',
        'description'   => 'Verify your identity to access premium features.',
        'image'         => url('/og_image.png'),
        'type'          => 'website'
    ]"
>

<div class="bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">KYC Verification</h1>
            <p class="text-gray-600">Identity verification is required to access premium features and ensure marketplace security.</p>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(!$kycVerification)
            <!-- No KYC Submitted -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.232 15.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-medium text-yellow-900">Verification Required</h3>
                        <p class="text-yellow-800 mt-1">Complete your KYC verification to unlock premium features and increase your trustworthiness.</p>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Benefits of KYC Verification</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-8 w-8 rounded-md bg-blue-500 text-white">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-gray-900">Trusted Profile</h3>
                            <p class="text-sm text-gray-600">Display a verified badge on your profile</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-8 w-8 rounded-md bg-green-500 text-white">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-gray-900">Enhanced Security</h3>
                            <p class="text-sm text-gray-600">Access to premium security features</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-8 w-8 rounded-md bg-purple-500 text-white">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-gray-900">Priority Support</h3>
                            <p class="text-sm text-gray-600">Get priority customer support</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-center h-8 w-8 rounded-md bg-yellow-500 text-white">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-gray-900">Higher Rates</h3>
                            <p class="text-sm text-gray-600">Access to higher paying opportunities</p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-center">
                    <a href="{{ route('kyc.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition-colors font-medium">
                        Start KYC Verification
                    </a>
                </div>
            </div>
        @else
            <!-- KYC Submitted -->
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Verification Status</h2>
                    <span class="bg-{{ $kycVerification->status_color }}-100 text-{{ $kycVerification->status_color }}-800 px-3 py-1 rounded-full text-sm font-medium">
                        {{ $kycVerification->status_label }}
                    </span>
                </div>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Full Name</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $kycVerification->full_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Document Type</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $kycVerification->document_type_label }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Submitted</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $kycVerification->submitted_at?->format('M d, Y \a\t g:i A') ?? 'Not submitted' }}</p>
                        </div>
                        @if($kycVerification->reviewed_at)
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Reviewed</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $kycVerification->reviewed_at->format('M d, Y \a\t g:i A') }}</p>
                        </div>
                        @endif
                    </div>

                    @if($kycVerification->isRejected() && $kycVerification->rejection_reason)
                    <div class="bg-red-50 border border-red-200 rounded-md p-4">
                        <h4 class="text-sm font-medium text-red-800 mb-2">Rejection Reason</h4>
                        <p class="text-sm text-red-700">{{ $kycVerification->rejection_reason }}</p>
                    </div>
                    @endif

                    @if($kycVerification->isPending())
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                        <h4 class="text-sm font-medium text-blue-800 mb-2">Under Review</h4>
                        <p class="text-sm text-blue-700">Your KYC verification is being reviewed by our team. This typically takes 24-48 hours.</p>
                    </div>
                    @endif

                    @if($kycVerification->isApproved())
                    <div class="bg-green-50 border border-green-200 rounded-md p-4">
                        <h4 class="text-sm font-medium text-green-800 mb-2">Verification Complete</h4>
                        <p class="text-sm text-green-700">Congratulations! Your identity has been verified. You now have access to all premium features.</p>
                    </div>
                    @endif
                </div>

                <div class="mt-6 flex justify-center">
                    <a href="{{ route('kyc.show', $kycVerification) }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors">
                        View Details
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

</x-layouts.marketing>
