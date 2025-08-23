<x-theme::layouts.app>
<div class="py-12 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">📋 Complete Your Requirements</h1>
            <p class="text-xl text-gray-600">You need to complete these requirements before taking tests</p>
        </div>

        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-8">
                <div class="space-y-6">
                    <!-- Email Verification -->
                    <div class="flex items-center justify-between p-6 border rounded-lg {{ $requirements['email_verified'] ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50' }}">
                        <div class="flex items-center">
                            @if($requirements['email_verified'])
                                <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            @else
                                <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            @endif
                            <div>
                                <h3 class="text-lg font-semibold {{ $requirements['email_verified'] ? 'text-green-800' : 'text-red-800' }}">
                                    Email Verification
                                </h3>
                                <p class="text-sm {{ $requirements['email_verified'] ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $requirements['email_verified'] ? 'Your email has been verified' : 'Please verify your email address' }}
                                </p>
                            </div>
                        </div>
                        @unless($requirements['email_verified'])
                            <a href="{{ route('verification.notice') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                Verify Email
                            </a>
                        @endunless
                    </div>

                    <!-- KYC Verification -->
                    <div class="flex items-center justify-between p-6 border rounded-lg {{ $requirements['kyc_completed'] ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50' }}">
                        <div class="flex items-center">
                            @if($requirements['kyc_completed'])
                                <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            @else
                                <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            @endif
                            <div>
                                <h3 class="text-lg font-semibold {{ $requirements['kyc_completed'] ? 'text-green-800' : 'text-red-800' }}">
                                    KYC Verification
                                </h3>
                                <p class="text-sm {{ $requirements['kyc_completed'] ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $requirements['kyc_completed'] ? 'Your identity has been verified' : 'Please complete your identity verification' }}
                                </p>
                            </div>
                        </div>
                        @unless($requirements['kyc_completed'])
                            <a href="{{ route('kyc.create') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                Complete KYC
                            </a>
                        @endunless
                    </div>
                </div>

                <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <h4 class="text-blue-800 font-semibold">Why do I need to complete these requirements?</h4>
                            <p class="text-blue-700 text-sm mt-1">
                                Email verification and KYC are required for security and compliance purposes before you can access training materials and typing tests.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</x-theme::layouts.app>
