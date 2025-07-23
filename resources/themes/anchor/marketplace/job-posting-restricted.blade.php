<x-layouts.app
    :seo="[
        'title'         => 'Job Posting Restricted - OnlyVerified',
        'description'   => 'Information about job posting permissions on OnlyVerified.',
        'image'         => url('/og_image.png'),
        'type'          => 'website'
    ]"
>

<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-800">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Header -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-r from-orange-500 to-red-600 rounded-full mb-6 shadow-lg">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                Job Posting 
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-600 to-red-600">Restricted</span>
            </h1>
            <p class="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                Only verified agencies can post jobs on OnlyVerified marketplace
            </p>
        </div>

        <!-- Main Content Card -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="p-8">
                <!-- User Info Section -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-2xl p-6 mb-8 border border-blue-200 dark:border-blue-700">
                    <div class="flex items-center mb-4">
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl mr-4">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Your Account Type</h3>
                            <p class="text-blue-600 dark:text-blue-400 font-medium">{{ $userType }}</p>
                        </div>
                    </div>
                    <p class="text-gray-700 dark:text-gray-300">
                        Your account is currently registered as a <strong>{{ $userType }}</strong>. 
                        This account type is designed for 
                        @if($canApply)
                            finding and applying to job opportunities rather than posting them.
                        @else
                            specialized marketplace participation.
                        @endif
                    </p>
                </div>

                <!-- Explanation Section -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Why This Restriction Exists</h2>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-6 border border-gray-200 dark:border-slate-600">
                            <div class="flex items-center mb-3">
                                <div class="inline-flex items-center justify-center w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg mr-3">
                                    <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <h3 class="font-semibold text-gray-900 dark:text-white">Quality Control</h3>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300 text-sm">
                                We ensure only verified agencies post jobs to maintain high-quality opportunities and prevent spam.
                            </p>
                        </div>

                        <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-6 border border-gray-200 dark:border-slate-600">
                            <div class="flex items-center mb-3">
                                <div class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg mr-3">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <h3 class="font-semibold text-gray-900 dark:text-white">Trust & Safety</h3>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300 text-sm">
                                Agency verification helps protect both job seekers and legitimate businesses in our marketplace.
                            </p>
                        </div>
                    </div>
                </div>

                @if($canApply)
                    <!-- What You Can Do Instead -->
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-2xl p-6 mb-8 border border-green-200 dark:border-green-700">
                        <div class="flex items-center mb-4">
                            <div class="inline-flex items-center justify-center w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl mr-4">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6.294a2 2 0 01-.786 1.588l-.214.143c-.194.130-.527.264-.893.264h-8.186c-.366 0-.699-.134-.893-.264l-.214-.143A2 2 0 014 14.294V8a2 2 0 012-2h4"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">What You Can Do Instead</h3>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 mb-4">
                            As a {{ $userType }}, you have access to powerful features designed for your role:
                        </p>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-gray-700 dark:text-gray-300">Browse available jobs</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-gray-700 dark:text-gray-300">Apply to opportunities</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-gray-700 dark:text-gray-300">Message with agencies</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-gray-700 dark:text-gray-300">Build your profile</span>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Become an Agency Section -->
                <div class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-2xl p-6 mb-8 border border-purple-200 dark:border-purple-700">
                    <div class="flex items-center mb-4">
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl mr-4">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h4M9 7h6m-6 4h6m-6 4h6"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Want to Post Jobs?</h3>
                    </div>
                    <p class="text-gray-700 dark:text-gray-300 mb-6">
                        If you're running an OnlyFans management agency or chatting business and need to post job opportunities, 
                        you can apply to become a verified agency on our platform.
                    </p>
                    
                    <div class="grid md:grid-cols-3 gap-4 mb-6">
                        <div class="text-center">
                            <div class="inline-flex items-center justify-center w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-full mb-2">
                                <span class="text-purple-600 dark:text-purple-400 font-bold">1</span>
                            </div>
                            <h4 class="font-semibold text-gray-900 dark:text-white text-sm">Verify Business</h4>
                            <p class="text-gray-600 dark:text-gray-400 text-xs mt-1">Provide business documentation and verification</p>
                        </div>
                        <div class="text-center">
                            <div class="inline-flex items-center justify-center w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-full mb-2">
                                <span class="text-purple-600 dark:text-purple-400 font-bold">2</span>
                            </div>
                            <h4 class="font-semibold text-gray-900 dark:text-white text-sm">Review Process</h4>
                            <p class="text-gray-600 dark:text-gray-400 text-xs mt-1">Our team reviews your application</p>
                        </div>
                        <div class="text-center">
                            <div class="inline-flex items-center justify-center w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-full mb-2">
                                <span class="text-purple-600 dark:text-purple-400 font-bold">3</span>
                            </div>
                            <h4 class="font-semibold text-gray-900 dark:text-white text-sm">Start Posting</h4>
                            <p class="text-gray-600 dark:text-gray-400 text-xs mt-1">Access agency features and post jobs</p>
                        </div>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold rounded-lg hover:from-purple-700 hover:to-pink-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Apply for Agency Status
                        </a>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-4 justify-center">
                    @if($canApply)
                        <a href="{{ route('marketplace.jobs') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6.294a2 2 0 01-.786 1.588l-.214.143c-.194.130-.527.264-.893.264h-8.186c-.366 0-.699-.134-.893-.264l-.214-.143A2 2 0 014 14.294V8a2 2 0 012-2h4"></path>
                            </svg>
                            Browse Available Jobs
                        </a>
                    @endif
                    
                    <a href="{{ route('marketplace.profiles') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold rounded-lg hover:from-green-700 hover:to-emerald-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Explore Talent Profiles
                    </a>

                    <a href="{{ route('marketplace.index') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-gray-600 to-gray-700 text-white font-semibold rounded-lg hover:from-gray-700 hover:to-gray-800 transition-all duration-200 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Marketplace
                    </a>
                </div>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="mt-12 bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="p-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 text-center">Frequently Asked Questions</h2>
                <div class="space-y-4">
                    <details class="group border border-gray-200 dark:border-slate-600 rounded-lg">
                        <summary class="flex justify-between items-center p-4 cursor-pointer hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                            <span class="font-semibold text-gray-900 dark:text-white">Why can't I post jobs?</span>
                            <svg class="w-5 h-5 text-gray-500 transform group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </summary>
                        <div class="p-4 pt-0 text-gray-600 dark:text-gray-300">
                            Job posting is restricted to verified agencies to ensure quality and protect both job seekers and employers. This helps maintain the integrity of our marketplace.
                        </div>
                    </details>

                    <details class="group border border-gray-200 dark:border-slate-600 rounded-lg">
                        <summary class="flex justify-between items-center p-4 cursor-pointer hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                            <span class="font-semibold text-gray-900 dark:text-white">How do I become a verified agency?</span>
                            <svg class="w-5 h-5 text-gray-500 transform group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </summary>
                        <div class="p-4 pt-0 text-gray-600 dark:text-gray-300">
                            Contact our support team with your business documentation, including business registration, tax information, and proof of operation in the OnlyFans management or adult content industry.
                        </div>
                    </details>

                    <details class="group border border-gray-200 dark:border-slate-600 rounded-lg">
                        <summary class="flex justify-between items-center p-4 cursor-pointer hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                            <span class="font-semibold text-gray-900 dark:text-white">Can I change my account type?</span>
                            <svg class="w-5 h-5 text-gray-500 transform group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </summary>
                        <div class="p-4 pt-0 text-gray-600 dark:text-gray-300">
                            Yes, you can request an account type change by contacting support. However, agency status requires verification of your business credentials.
                        </div>
                    </details>
                </div>
            </div>
        </div>
    </div>
</div>

</x-layouts.app>
