<x-layouts.app
    :seo="[
        'title'         => 'Post a Job - OnlyVerified',
        'description'   => 'Find qualified chatters for your OnlyFans ecosystem business.',
        'image'         => url('/og_image.png'),
        'type'          => 'website'
    ]"
>

<div class="bg-white dark:bg-zinc-800">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
<div class="mb-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6.294a2 2 0 01-.78 1.63l-1.473 1.105A2 2 0 0112 16.5v-2.294A2 2 0 0111.22 12.5L9.747 11.395A2 2 0 019 10.106V4a2 2 0 012-2z"></path>
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">
                Post a 
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">Job</span>
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">Connect with qualified chatters and grow your OnlyFans ecosystem business with the perfect talent</p>
        </div>

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                        <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        @if(auth()->user()->requiresVerification())
            <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-6">
                <div class="flex items-center mb-4">
                    <svg class="w-6 h-6 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-red-800">Verification Required</h3>
                </div>
                @if(auth()->user()->isChatter())
                    <p class="text-red-700 mb-4">You must complete KYC verification before you can post jobs on our marketplace.</p>
                    @if(!auth()->user()->hasKycSubmitted())
                        <a href="{{ route('profile.kyc') }}" class="inline-block bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors">
                            <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Complete KYC Verification
                        </a>
                    @else
                        <div class="bg-yellow-100 border border-yellow-200 rounded-lg p-4 mt-4">
                            <div class="flex items-center text-gray-900 dark:text-white">
                                <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-yellow-800">KYC Verification Pending</span>
                            </div>
                            <p class="text-yellow-700 mt-2">Your KYC verification is being reviewed. You'll be able to post jobs once it's approved.</p>
                        </div>
                    @endif
                @else
                    <p class="text-red-700 mb-4">You must complete earnings verification before you can post jobs on our marketplace.</p>
                    @if(!auth()->user()->hasEarningsSubmitted())
                        <a href="{{ route('profile.earnings-verification') }}" class="inline-block bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors">
                            <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Complete Earnings Verification
                        </a>
                    @else
                        <div class="bg-yellow-100 border border-yellow-200 rounded-lg p-4 mt-4">
                            <div class="flex items-center text-gray-900 dark:text-white">
                                <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-yellow-800">Earnings Verification Pending</span>
                            </div>
                            <p class="text-yellow-700 mt-2">Your earnings verification is being reviewed. You'll be able to post jobs once it's approved.</p>
                        </div>
                    @endif
                @endif
            </div>
        @else
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <div class="flex items-center text-gray-900 dark:text-white">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                    </svg>
                    <span class="text-green-800 font-medium">Verified - Ready to Post Jobs</span>
                </div>
            </div>
        @endif
        
        <!-- Subscription Usage Info -->
        @if(isset($plan) && $plan)
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-200 dark:border-blue-700 rounded-2xl p-6 mb-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="inline-flex items-center justify-center w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl mr-3">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Your Subscription</h3>
                    </div>
                    <div class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-sm font-semibold rounded-full">
                        {{ $plan->name }} Plan
                    </div>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
                    <!-- Job Posts Usage -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Job Posts</span>
                            <div class="flex items-center space-x-1">
                                <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $usedJobPosts }}</span>
                                <span class="text-gray-500 dark:text-gray-400">/</span>
                                <span class="text-gray-600 dark:text-gray-300">{{ $totalJobPosts === null ? '∞' : $totalJobPosts }}</span>
                            </div>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            @if($totalJobPosts === null)
                                <div class="bg-gradient-to-r from-green-400 to-blue-500 h-2 rounded-full" style="width: 100%"></div>
                            @else
                                <div class="bg-gradient-to-r from-blue-400 to-blue-600 h-2 rounded-full" style="width: {{ min(($usedJobPosts / max($totalJobPosts, 1)) * 100, 100) }}%"></div>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ $remainingJobPosts === 999 ? 'Unlimited' : $remainingJobPosts }} remaining this month
                        </p>
                    </div>
                    
                    <!-- Featured Jobs -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center mb-2">
                            <svg class="w-4 h-4 text-yellow-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Featured Jobs</span>
                        </div>
                        @if($user->canUseFeaturedForFree())
                            <div class="flex items-center">
                                <span class="text-lg font-bold text-green-600 dark:text-green-400">FREE</span>
                                <svg class="w-4 h-4 text-green-500 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <p class="text-xs text-green-600 dark:text-green-400 mt-1">Included in your plan</p>
                        @else
                            <div class="flex items-center">
                                <span class="text-lg font-bold text-gray-900 dark:text-white">$10</span>
                                <span class="text-gray-500 dark:text-gray-400 text-sm ml-1">each</span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Per featured job</p>
                        @endif
                    </div>
                    
                    <!-- Urgent Badge -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center mb-2">
                            <svg class="w-4 h-4 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Urgent Badge</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-lg font-bold text-gray-900 dark:text-white">$5</span>
                            <span class="text-gray-500 dark:text-gray-400 text-sm ml-1">each</span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Per urgent job</p>
                    </div>
                </div>
                
                @if($remainingJobPosts <= 0)
                    <div class="bg-gradient-to-r from-red-50 to-pink-50 dark:from-red-900/20 dark:to-pink-900/20 border border-red-200 dark:border-red-700 rounded-xl p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="inline-flex items-center justify-center w-8 h-8 bg-red-100 dark:bg-red-900/30 rounded-full mr-3">
                                    <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-red-800 dark:text-red-200">Job Post Limit Reached</h4>
                                    <p class="text-sm text-red-600 dark:text-red-300">You've used all your job posts for this month</p>
                                </div>
                            </div>
                            <a href="{{ route('subscription.plans') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-600 to-pink-600 text-white text-sm font-semibold rounded-lg hover:from-red-700 hover:to-pink-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                Upgrade Plan
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <form id="jobPostForm" action="{{ route('marketplace.jobs.store') }}" method="POST" class="space-y-6" {{ auth()->user()->requiresVerification() ? 'style=display:none;' : '' }}>
            @csrf
            
            <div class="bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-xl shadow-lg p-8">
                <div class="flex items-center mb-6">
                    <div class="inline-flex items-center justify-center w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Job Details</h2>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Job Title *</label>
                        <input 
                            type="text" 
                            name="title" 
                            value="{{ old('title') }}"
                            class="w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="e.g., English Market Chatter Needed"
                            required
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Market *</label>
                        <select name="market" class="w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="">Select Market</option>
                            <option value="english" {{ old('market') === 'english' ? 'selected' : '' }}>English</option>
                            <option value="spanish" {{ old('market') === 'spanish' ? 'selected' : '' }}>Spanish</option>
                            <option value="french" {{ old('market') === 'french' ? 'selected' : '' }}>French</option>
                            <option value="german" {{ old('market') === 'german' ? 'selected' : '' }}>German</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Experience Level *</label>
                        <select name="experience_level" class="w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="">Select Level</option>
                            <option value="beginner" {{ old('experience_level') === 'beginner' ? 'selected' : '' }}>Beginner</option>
                            <option value="intermediate" {{ old('experience_level') === 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                            <option value="advanced" {{ old('experience_level') === 'advanced' ? 'selected' : '' }}>Advanced</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Contract Type *</label>
                        <select name="contract_type" class="w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="">Select Type</option>
                            <option value="full_time" {{ old('contract_type') === 'full_time' ? 'selected' : '' }}>Full Time</option>
                            <option value="part_time" {{ old('contract_type') === 'part_time' ? 'selected' : '' }}>Part Time</option>
                            <option value="contract" {{ old('contract_type') === 'contract' ? 'selected' : '' }}>Contract</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Expected Hours per Week *</label>
                        <input 
                            type="number" 
                            name="expected_hours_per_week" 
                            value="{{ old('expected_hours_per_week') }}"
                            class="w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            min="1"
                            max="160"
                            required
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Duration (months) *</label>
                        <input 
                            type="number" 
                            name="duration_months" 
                            value="{{ old('duration_months') }}"
                            class="w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            min="1"
                            max="36"
                            required
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Minimum Typing Speed (WPM)</label>
                        <input 
                            type="number" 
                            name="min_typing_speed" 
                            value="{{ old('min_typing_speed') }}"
                            class="w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            min="20"
                            max="150"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Start Date</label>
                        <input 
                            type="date" 
                            name="start_date" 
                            value="{{ old('start_date', date('Y-m-d')) }}"
                            min="{{ date('Y-m-d') }}"
                            class="w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">When do you want the work to start? (Defaults to today)</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Maximum Applications *</label>
                        <input 
                            type="number" 
                            name="max_applications" 
                            value="{{ old('max_applications', 50) }}"
                            class="w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            min="1"
                            max="200"
                            required
                        >
                    </div>
                </div>

                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Job Description *</label>
                    <textarea 
                        name="description" 
                        rows="6"
                        class="w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Describe the role, responsibilities, and what you're looking for..."
                        required
                    >{{ old('description') }}</textarea>
                </div>

                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Requirements</label>
                    <textarea 
                        name="requirements" 
                        rows="4"
                        class="w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="List specific requirements and qualifications..."
                    >{{ old('requirements') }}</textarea>
                </div>

                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Benefits</label>
                    <textarea 
                        name="benefits" 
                        rows="4"
                        class="w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="What benefits do you offer? (flexible hours, bonuses, etc.)"
                    >{{ old('benefits') }}</textarea>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-xl shadow-lg p-8">
                <div class="flex items-center mb-8">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl mr-4 shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">🌍 Timezone & Availability</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Define when and where you need your team member to work</p>
                    </div>
                </div>
                
                <!-- Required Working Hours -->
                <div class="bg-gradient-to-br from-indigo-50 to-blue-50 dark:from-indigo-900/20 dark:to-blue-900/20 rounded-lg p-6 mb-6 border border-indigo-200 dark:border-indigo-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Required Working Hours
                    </h3>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Your Timezone -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                                <span class="w-2 h-2 bg-indigo-500 rounded-full mr-2"></span>
                                Your Timezone
                            </label>
                            <div class="relative">
                                <select name="job_timezone" class="w-full rounded-lg border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 pr-10" required>
                                    <option value="">Select Your Timezone</option>
                                    <option value="UTC" {{ old('job_timezone') === 'UTC' ? 'selected' : '' }}>🌐 UTC (Coordinated Universal Time)</option>
                                    <option value="America/New_York" {{ old('job_timezone') === 'America/New_York' ? 'selected' : '' }}>🇺🇸 Eastern Time (UTC-5/4)</option>
                                    <option value="America/Chicago" {{ old('job_timezone') === 'America/Chicago' ? 'selected' : '' }}>🇺🇸 Central Time (UTC-6/5)</option>
                                    <option value="America/Denver" {{ old('job_timezone') === 'America/Denver' ? 'selected' : '' }}>🇺🇸 Mountain Time (UTC-7/6)</option>
                                    <option value="America/Los_Angeles" {{ old('job_timezone') === 'America/Los_Angeles' ? 'selected' : '' }}>🇺🇸 Pacific Time (UTC-8/7)</option>
                                    <option value="Europe/London" {{ old('job_timezone') === 'Europe/London' ? 'selected' : '' }}>🇬🇧 London (UTC+0/1)</option>
                                    <option value="Europe/Paris" {{ old('job_timezone') === 'Europe/Paris' ? 'selected' : '' }}>🇫🇷 Paris (UTC+1/2)</option>
                                    <option value="Europe/Berlin" {{ old('job_timezone') === 'Europe/Berlin' ? 'selected' : '' }}>🇩🇪 Berlin (UTC+1/2)</option>
                                    <option value="Asia/Manila" {{ old('job_timezone') === 'Asia/Manila' ? 'selected' : '' }}>🇵🇭 Philippines (UTC+8)</option>
                                    <option value="Asia/Tokyo" {{ old('job_timezone') === 'Asia/Tokyo' ? 'selected' : '' }}>🇯🇵 Tokyo (UTC+9)</option>
                                    <option value="Asia/Shanghai" {{ old('job_timezone') === 'Asia/Shanghai' ? 'selected' : '' }}>🇨🇳 Shanghai (UTC+8)</option>
                                    <option value="Australia/Sydney" {{ old('job_timezone') === 'Australia/Sydney' ? 'selected' : '' }}>🇦🇺 Sydney (UTC+10/11)</option>
                                    <option value="Asia/Kolkata" {{ old('job_timezone') === 'Asia/Kolkata' ? 'selected' : '' }}>🇮🇳 India (UTC+5:30)</option>
                                    <option value="Europe/Amsterdam" {{ old('job_timezone') === 'Europe/Amsterdam' ? 'selected' : '' }}>🇳🇱 Amsterdam (UTC+1/2)</option>
                                    <option value="America/Toronto" {{ old('job_timezone') === 'America/Toronto' ? 'selected' : '' }}>🇨🇦 Toronto (UTC-5/4)</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                📍 What timezone are you posting from?
                            </p>
                        </div>
                        
                        <!-- Start Time -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                Start Time
                            </label>
                            <div class="relative">
                                <input 
                                    type="time" 
                                    name="required_start_time" 
                                    value="{{ old('required_start_time') }}"
                                    class="w-full rounded-lg border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-500 text-lg font-mono"
                                    required
                                >
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                🌅 Earliest time chatters should be available
                            </p>
                        </div>
                        
                        <!-- End Time -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                                <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                End Time
                            </label>
                            <div class="relative">
                                <input 
                                    type="time" 
                                    name="required_end_time" 
                                    value="{{ old('required_end_time') }}"
                                    class="w-full rounded-lg border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-red-500 focus:ring-2 focus:ring-red-500 text-lg font-mono"
                                    required
                                >
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                🌇 Latest time chatters should be available
                            </p>
                        </div>
                    </div>
                    
                    <!-- Example & Help Text -->
                    <div class="mt-6 p-4 bg-white dark:bg-zinc-800 rounded-lg border border-indigo-200 dark:border-indigo-700">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">How it works:</h4>
                                <p class="text-xs text-gray-600 dark:text-gray-300 mb-2">
                                    <strong>Example:</strong> If you're in Paris and need chatters available from 11am-4pm Paris time, 
                                    a chatter in Philippines (4pm-10pm PH time) would be a perfect match!
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    💡 The system automatically converts timezones to match you with available chatters worldwide.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Working Days -->
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Required Working Days
                    </h3>
                    
                    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3">
                        @php
                            $days = [
                                'monday' => ['name' => 'Monday', 'short' => 'Mon', 'emoji' => '📅'],
                                'tuesday' => ['name' => 'Tuesday', 'short' => 'Tue', 'emoji' => '📅'],
                                'wednesday' => ['name' => 'Wednesday', 'short' => 'Wed', 'emoji' => '📅'],
                                'thursday' => ['name' => 'Thursday', 'short' => 'Thu', 'emoji' => '📅'],
                                'friday' => ['name' => 'Friday', 'short' => 'Fri', 'emoji' => '📅'],
                                'saturday' => ['name' => 'Saturday', 'short' => 'Sat', 'emoji' => '📅'],
                                'sunday' => ['name' => 'Sunday', 'short' => 'Sun', 'emoji' => '📅']
                            ];
                        @endphp
                        @foreach($days as $value => $day)
                            <label class="relative group cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    name="required_days[]" 
                                    value="{{ $value }}"
                                    {{ in_array($value, old('required_days', [])) ? 'checked' : '' }}
                                    class="sr-only peer"
                                >
                                <div class="bg-white dark:bg-zinc-800 border-2 border-gray-200 dark:border-zinc-600 rounded-lg p-3 text-center transition-all duration-200 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/30 peer-checked:border-blue-500 peer-checked:text-blue-700 dark:peer-checked:text-blue-300 hover:border-blue-300 group-hover:shadow-md">
                                    <div class="text-lg mb-1">{{ $day['emoji'] }}</div>
                                    <div class="text-xs font-medium text-gray-900 dark:text-white peer-checked:text-blue-700 dark:peer-checked:text-blue-300">
                                        <span class="hidden sm:inline">{{ $day['name'] }}</span>
                                        <span class="sm:hidden">{{ $day['short'] }}</span>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-3 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Select specific days if you need guaranteed coverage, or leave empty for flexible scheduling
                    </p>
                </div>
                
                <!-- Working Hours -->
                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Preferred Working Hours
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                                <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                Start Time
                            </label>
                            <div class="relative">
                                <input 
                                    type="time" 
                                    name="preferred_start_time" 
                                    value="{{ old('preferred_start_time') }}"
                                    class="w-full rounded-lg border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-500 text-lg font-mono"
                                >
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                ⏰ When should work begin each day?
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                                <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                                End Time
                            </label>
                            <div class="relative">
                                <input 
                                    type="time" 
                                    name="preferred_end_time" 
                                    value="{{ old('preferred_end_time') }}"
                                    class="w-full rounded-lg border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-500 text-lg font-mono"
                                >
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                🏃‍♂️ When should work end each day?
                            </p>
                        </div>
                    </div>
                    
                    <div class="mt-4 p-4 bg-white dark:bg-zinc-800 rounded-lg border border-green-200 dark:border-green-800">
                        <p class="text-xs text-gray-600 dark:text-gray-300 flex items-start">
                            <svg class="w-4 h-4 mr-2 mt-0.5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>
                                <strong>Pro Tip:</strong> Leave time fields empty if you prefer flexible hours. 
                                Times are based on the selected timezone above.
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-xl shadow-lg p-8">
                <div class="flex items-center mb-6">
                    <div class="inline-flex items-center justify-center w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Compensation</h2>
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Type *</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="flex items-center text-gray-900 dark:text-white">
                                <input 
                                    type="radio" 
                                    name="rate_type" 
                                    value="hourly"
                                    {{ old('rate_type') === 'hourly' ? 'checked' : '' }}
                                    class="mr-2"
                                    required
                                >
                                <span class="text-gray-900 dark:text-white">Hourly Rate</span>
                            </label>
                        </div>
                        <div>
                            <label class="flex items-center text-gray-900 dark:text-white">
                                <input 
                                    type="radio" 
                                    name="rate_type" 
                                    value="fixed"
                                    {{ old('rate_type') === 'fixed' ? 'checked' : '' }}
                                    class="mr-2"
                                    required
                                >
                                <span class="text-gray-900 dark:text-white">Fixed Price</span>
                            </label>
                        </div>
                        <div>
                            <label class="flex items-center text-gray-900 dark:text-white">
                                <input 
                                    type="radio" 
                                    name="rate_type" 
                                    value="commission"
                                    {{ old('rate_type') === 'commission' ? 'checked' : '' }}
                                    class="mr-2"
                                    required
                                >
                                <span class="text-gray-900 dark:text-white">Commission %</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div id="hourly-rate" class="rate-input" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Hourly Rate</label>
                        <div class="space-y-3">
                            <div>
                                <label class="flex items-center">
                                    <input type="radio" name="hourly_rate_type" value="amount" class="mr-2" checked>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Specific Amount</span>
                                </label>
                                <div class="mt-2 relative">
                                    <span class="absolute left-3 top-2 text-gray-500">$</span>
                                    <input 
                                        type="number" 
                                        name="hourly_rate" 
                                        value="{{ old('hourly_rate') }}"
                                        class="w-full pl-8 rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        step="0.01"
                                        min="0"
                                        id="hourly_amount_input"
                                    >
                                </div>
                            </div>
                            <div>
                                <label class="flex items-center">
                                    <input type="radio" name="hourly_rate_type" value="tbd" class="mr-2" {{ old('hourly_rate_type') === 'tbd' ? 'checked' : '' }}>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">TBD (To be decided with agency)</span>
                                </label>
                                <input type="hidden" name="hourly_rate_tbd" value="0" id="hourly_tbd_hidden">
                            </div>
                        </div>
                    </div>

                    <div id="fixed-rate" class="rate-input" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Fixed Price</label>
                        <div class="space-y-3">
                            <div>
                                <label class="flex items-center">
                                    <input type="radio" name="fixed_rate_type" value="amount" class="mr-2" checked>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Specific Amount</span>
                                </label>
                                <div class="mt-2 space-y-2">
                                    <div class="relative">
                                        <span class="absolute left-3 top-2 text-gray-500">$</span>
                                        <input 
                                            type="number" 
                                            name="fixed_rate" 
                                            value="{{ old('fixed_rate') }}"
                                            class="w-full pl-8 rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            step="0.01"
                                            min="0"
                                            id="fixed_amount_input"
                                        >
                                    </div>
                                    <select name="fixed_rate_period" class="w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500" id="fixed_period_select">
                                        <option value="total" {{ old('fixed_rate_period') === 'total' ? 'selected' : '' }}>Total Project</option>
                                        <option value="weekly" {{ old('fixed_rate_period') === 'weekly' ? 'selected' : '' }}>Per Week</option>
                                        <option value="monthly" {{ old('fixed_rate_period') === 'monthly' ? 'selected' : '' }}>Per Month</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="flex items-center">
                                    <input type="radio" name="fixed_rate_type" value="tbd" class="mr-2">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">TBD (To be decided with agency)</span>
                                </label>
                                <input type="hidden" name="fixed_rate_tbd" value="0" id="fixed_tbd_hidden">
                            </div>
                        </div>
                    </div>

                    <div id="commission-rate" class="rate-input" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Commission</label>
                        <div class="space-y-3">
                            <div>
                                <label class="flex items-center">
                                    <input type="radio" name="commission_rate_type" value="amount" class="mr-2" checked>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Specific Percentage</span>
                                </label>
                                <div class="mt-2 relative">
                                    <input 
                                        type="number" 
                                        name="commission_percentage" 
                                        value="{{ old('commission_percentage') }}"
                                        class="w-full pr-8 rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        step="0.01"
                                        min="0"
                                        max="100"
                                        id="commission_amount_input"
                                    >
                                    <span class="absolute right-3 top-2 text-gray-500">%</span>
                                </div>
                            </div>
                            <div>
                                <label class="flex items-center">
                                    <input type="radio" name="commission_rate_type" value="tbd" class="mr-2">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">TBD (To be decided with agency)</span>
                                </label>
                                <input type="hidden" name="commission_rate_tbd" value="0" id="commission_tbd_hidden">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-xl shadow-lg p-8">
                <div class="flex items-center mb-6">
                    <div class="inline-flex items-center justify-center w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg mr-3">
                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Job Visibility</h2>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="flex items-center text-gray-900 dark:text-white">
                            <input 
                                type="checkbox" 
                                name="is_featured" 
                                value="1"
                                {{ old('is_featured') ? 'checked' : '' }}
                                class="mr-2"
                            >
                            @if(isset($user) && $user->canUseFeaturedForFree())
                                <span class="text-gray-900 dark:text-white">Feature this job <span class="text-green-600 font-medium">(FREE)</span></span>
                            @else
                                <span class="text-gray-900 dark:text-white">Feature this job (+$10)</span>
                            @endif
                        </label>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Featured jobs appear at the top of search results</p>
                    </div>

                    <div>
                        <label class="flex items-center text-gray-900 dark:text-white">
                            <input 
                                type="checkbox" 
                                name="is_urgent" 
                                value="1"
                                {{ old('is_urgent') ? 'checked' : '' }}
                                class="mr-2"
                            >
                            <span class="text-gray-900 dark:text-white">Mark as urgent (+$5)</span>
                        </label>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Urgent jobs get an eye-catching badge</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('marketplace.index') }}" class="px-6 py-2 border border-gray-300 dark:border-zinc-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-zinc-700">
                    Cancel
                </a>
                <button type="submit" id="submitBtn" class="px-6 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-md hover:from-blue-700 hover:to-purple-700 transition-all shadow-lg">
                    <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    Post Job
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Success Notification Popup -->
<div id="successModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white dark:bg-zinc-800 rounded-2xl p-8 max-w-md w-full mx-4 shadow-2xl transform scale-95 transition-transform duration-300" id="successModalContent">
        <div class="text-center">
            <!-- Success Icon -->
            <div class="mx-auto w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            
            <!-- Success Message -->
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Job Posted Successfully! 🎉</h3>
            <p class="text-gray-600 dark:text-gray-300 mb-6" id="jobTitle">Your job listing is now live and visible to chatters.</p>
            
            <!-- Action Buttons -->
            <div class="space-y-3">
                <a href="#" id="viewJobBtn" class="block w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold py-3 px-6 rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                    <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    View My Job
                </a>
                
                <button onclick="closeSuccessModal()" class="block w-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold py-3 px-6 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    Post Another Job
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Error Notification Popup -->
<div id="errorModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden">
    <div class="bg-white dark:bg-zinc-800 rounded-2xl p-8 max-w-md w-full mx-4 shadow-2xl transform scale-95 transition-transform duration-300" id="errorModalContent">
        <div class="text-center">
            <!-- Error Icon -->
            <div class="mx-auto w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            
            <!-- Error Message -->
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Unable to Post Job</h3>
            <p class="text-gray-600 dark:text-gray-300 mb-6" id="errorMessage">An error occurred while posting your job.</p>
            
            <!-- Action Buttons -->
            <div class="space-y-3">
                <a href="{{ route('subscription.plans') }}" id="upgradeBtn" class="block w-full bg-gradient-to-r from-orange-600 to-red-600 text-white font-semibold py-3 px-6 rounded-lg hover:from-orange-700 hover:to-red-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 hidden">
                    <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Upgrade Subscription
                </a>
                
                <button type="button" onclick="closeErrorModal()" class="block w-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold py-3 px-6 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    Try Again
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // === DAY BUTTON FUNCTIONALITY ===
    const dayButtons = document.querySelectorAll('input[name="required_days[]"]');
    const timezoneFlexibleCheckbox = document.querySelector('input[name="timezone_flexible"]');
    const timezoneSelect = document.querySelector('select[name="required_timezone"]');
    const workingDaysSection = document.querySelector('.bg-blue-50');
    const workingHoursSection = document.querySelector('.bg-green-50');
    
    // Enhanced day button functionality
    dayButtons.forEach(button => {
        button.addEventListener('change', function() {
            const label = this.closest('label');
            const dayCard = label.querySelector('div');
            
            if (this.checked) {
                // Add selected styling
                dayCard.classList.add('bg-blue-50', 'border-blue-500', 'text-blue-700');
                dayCard.classList.add('dark:bg-blue-900/30', 'dark:border-blue-500', 'dark:text-blue-300');
                dayCard.classList.remove('bg-white', 'border-gray-200', 'dark:bg-zinc-800', 'dark:border-zinc-600');
                
                // Add pulse animation
                dayCard.classList.add('animate-pulse');
                setTimeout(() => dayCard.classList.remove('animate-pulse'), 200);
            } else {
                // Remove selected styling
                dayCard.classList.remove('bg-blue-50', 'border-blue-500', 'text-blue-700');
                dayCard.classList.remove('dark:bg-blue-900/30', 'dark:border-blue-500', 'dark:text-blue-300');
                dayCard.classList.add('bg-white', 'border-gray-200', 'dark:bg-zinc-800', 'dark:border-zinc-600');
            }
        });
        
        // Handle keyboard interaction
        button.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.checked = !this.checked;
                this.dispatchEvent(new Event('change'));
            }
        });
    });
    
    // Quick day selection shortcuts
    function selectWeekdays() {
        ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'].forEach(day => {
            const checkbox = document.querySelector(`input[value="${day}"]`);
            if (checkbox) {
                checkbox.checked = true;
                checkbox.dispatchEvent(new Event('change'));
            }
        });
    }
    
    function selectWeekends() {
        ['saturday', 'sunday'].forEach(day => {
            const checkbox = document.querySelector(`input[value="${day}"]`);
            if (checkbox) {
                checkbox.checked = true;
                checkbox.dispatchEvent(new Event('change'));
            }
        });
    }
    
    function selectAllDays() {
        dayButtons.forEach(button => {
            button.checked = true;
            button.dispatchEvent(new Event('change'));
        });
    }
    
    function clearAllDays() {
        dayButtons.forEach(button => {
            button.checked = false;
            button.dispatchEvent(new Event('change'));
        });
    }
    
    // Add quick selection buttons
    const quickSelectDiv = document.createElement('div');
    quickSelectDiv.className = 'mt-4 flex flex-wrap gap-2';
    quickSelectDiv.innerHTML = `
        <button type="button" onclick="selectWeekdays()" class="px-3 py-1 text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-full hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors">
            📅 Weekdays (Mon-Fri)
        </button>
        <button type="button" onclick="selectWeekends()" class="px-3 py-1 text-xs bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-full hover:bg-green-200 dark:hover:bg-green-900/50 transition-colors">
            🎯 Weekends Only
        </button>
        <button type="button" onclick="selectAllDays()" class="px-3 py-1 text-xs bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded-full hover:bg-purple-200 dark:hover:bg-purple-900/50 transition-colors">
            🌟 All Days
        </button>
        <button type="button" onclick="clearAllDays()" class="px-3 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
            🗑️ Clear All
        </button>
    `;
    
    // Insert quick select buttons after the day grid
    const dayGrid = document.querySelector('.grid.grid-cols-2.sm\\:grid-cols-4.lg\\:grid-cols-7');
    if (dayGrid && dayGrid.parentNode) {
        dayGrid.parentNode.insertBefore(quickSelectDiv, dayGrid.nextSibling);
    }
    
    // Make functions globally available
    window.selectWeekdays = selectWeekdays;
    window.selectWeekends = selectWeekends;
    window.selectAllDays = selectAllDays;
    window.clearAllDays = clearAllDays;
    
    // Note: Removed timezone flexible checkbox functionality as we now use a simpler timezone + time range approach
    
    // === RATE TYPE FUNCTIONALITY ===
    const rateTypeInputs = document.querySelectorAll('input[name="rate_type"]');
    const rateInputs = document.querySelectorAll('.rate-input');
    
    function showRateInput() {
        console.log('🎯 showRateInput called');
        
        // Hide all rate inputs first
        rateInputs.forEach(input => {
            input.style.display = 'none';
            console.log('🙈 Hiding:', input.id);
        });
        
        // Show selected rate input
        const selectedType = document.querySelector('input[name="rate_type"]:checked');
        console.log('🎯 Selected rate type:', selectedType ? selectedType.value : 'none');
        
        if (selectedType) {
            const targetInput = document.getElementById(selectedType.value + '-rate');
            console.log('🎯 Target input:', targetInput ? targetInput.id : 'not found');
            if (targetInput) {
                targetInput.style.display = 'block';
                console.log('👀 Showing:', targetInput.id);
            }
        }
    }
    
    // More robust function to ensure rate inputs are shown
    function ensureRateInputVisible() {
        console.log('🔄 ensureRateInputVisible called');
        
        const checkedRadio = document.querySelector('input[name="rate_type"]:checked');
        if (checkedRadio) {
            console.log('✅ Found checked radio:', checkedRadio.value);
            showRateInput();
        } else {
            console.log('❌ No checked radio found');
            // If no radio is checked, hide all inputs
            rateInputs.forEach(input => {
                input.style.display = 'none';
            });
        }
    }
    
    // Handle TBD/Amount radio buttons within each rate type
    function handleRateTypeRadios() {
        // Hourly rate TBD/amount toggle
        const hourlyRateTypeRadios = document.querySelectorAll('input[name="hourly_rate_type"]');
        const hourlyAmountInput = document.getElementById('hourly_amount_input');
        
        hourlyRateTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                const hourlyTbdHidden = document.getElementById('hourly_tbd_hidden');
                if (this.value === 'tbd') {
                    hourlyAmountInput.disabled = true;
                    hourlyAmountInput.value = '';
                    hourlyAmountInput.style.opacity = '0.5';
                    if (hourlyTbdHidden) hourlyTbdHidden.value = '1';
                } else {
                    hourlyAmountInput.disabled = false;
                    hourlyAmountInput.style.opacity = '1';
                    if (hourlyTbdHidden) hourlyTbdHidden.value = '0';
                }
            });
        });
        
        // Fixed rate TBD/amount toggle
        const fixedRateTypeRadios = document.querySelectorAll('input[name="fixed_rate_type"]');
        const fixedAmountInput = document.getElementById('fixed_amount_input');
        const fixedPeriodSelect = document.getElementById('fixed_period_select');
        
        fixedRateTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                const fixedTbdHidden = document.getElementById('fixed_tbd_hidden');
                if (this.value === 'tbd') {
                    fixedAmountInput.disabled = true;
                    fixedAmountInput.value = '';
                    fixedAmountInput.style.opacity = '0.5';
                    fixedPeriodSelect.disabled = true;
                    fixedPeriodSelect.style.opacity = '0.5';
                    if (fixedTbdHidden) fixedTbdHidden.value = '1';
                } else {
                    fixedAmountInput.disabled = false;
                    fixedAmountInput.style.opacity = '1';
                    fixedPeriodSelect.disabled = false;
                    fixedPeriodSelect.style.opacity = '1';
                    if (fixedTbdHidden) fixedTbdHidden.value = '0';
                }
            });
        });
        
        // Commission rate TBD/amount toggle
        const commissionRateTypeRadios = document.querySelectorAll('input[name="commission_rate_type"]');
        const commissionAmountInput = document.getElementById('commission_amount_input');
        
        commissionRateTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'tbd') {
                    commissionAmountInput.disabled = true;
                    commissionAmountInput.value = '';
                    commissionAmountInput.style.opacity = '0.5';
                } else {
                    commissionAmountInput.disabled = false;
                    commissionAmountInput.style.opacity = '1';
                }
            });
        });
        
        // Initialize states based on checked radios
        const checkedHourlyRateType = document.querySelector('input[name="hourly_rate_type"]:checked');
        if (checkedHourlyRateType && checkedHourlyRateType.value === 'tbd') {
            checkedHourlyRateType.dispatchEvent(new Event('change'));
        }
        
        const checkedFixedRateType = document.querySelector('input[name="fixed_rate_type"]:checked');
        if (checkedFixedRateType && checkedFixedRateType.value === 'tbd') {
            checkedFixedRateType.dispatchEvent(new Event('change'));
        }
        
        const checkedCommissionRateType = document.querySelector('input[name="commission_rate_type"]:checked');
        if (checkedCommissionRateType && checkedCommissionRateType.value === 'tbd') {
            checkedCommissionRateType.dispatchEvent(new Event('change'));
        }
    }
    
    // Initialize TBD/Amount radio functionality
    handleRateTypeRadios();
    
    // Multiple approaches to ensure rate input visibility
    
    // 1. Direct event listeners on rate type radios
    rateTypeInputs.forEach(input => {
        console.log('🔧 Adding listeners to:', input.value);
        input.addEventListener('change', function() {
            console.log('📻 Radio changed to:', this.value);
            setTimeout(() => {
                showRateInput();
                ensureRateInputVisible();
            }, 10);
        });
        
        input.addEventListener('click', function() {
            console.log('🖱️ Radio clicked:', this.value);
            setTimeout(() => {
                showRateInput();
                ensureRateInputVisible();
            }, 10);
        });
    });
    
    // 2. Document-level event delegation for radio buttons
    document.addEventListener('click', function(e) {
        if (e.target.type === 'radio' && e.target.name === 'rate_type') {
            console.log('🎯 Document click on rate_type radio:', e.target.value);
            setTimeout(() => {
                showRateInput();
                ensureRateInputVisible();
            }, 10);
        }
    });
    
    // 3. Document-level change listener
    document.addEventListener('change', function(e) {
        if (e.target.name === 'rate_type') {
            console.log('🔄 Document change on rate_type:', e.target.value);
            setTimeout(() => {
                showRateInput();
                ensureRateInputVisible();
            }, 10);
        }
    });
    
    // 4. MutationObserver to catch any DOM changes
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'checked') {
                const target = mutation.target;
                if (target.name === 'rate_type' && target.checked) {
                    console.log('🔍 MutationObserver detected checked change:', target.value);
                    setTimeout(() => {
                        showRateInput();
                        ensureRateInputVisible();
                    }, 10);
                }
            }
        });
    });
    
    // Observe all rate type radio buttons
    rateTypeInputs.forEach(input => {
        observer.observe(input, { attributes: true, attributeFilter: ['checked'] });
    });
    
    // 5. Periodic check to ensure visibility (fallback)
    let visibilityCheckCount = 0;
    const visibilityInterval = setInterval(() => {
        visibilityCheckCount++;
        const checkedRadio = document.querySelector('input[name="rate_type"]:checked');
        if (checkedRadio) {
            const targetDiv = document.getElementById(checkedRadio.value + '-rate');
            if (targetDiv && targetDiv.style.display === 'none') {
                console.log('🚨 Periodic check found hidden rate input, fixing...', checkedRadio.value);
                ensureRateInputVisible();
            }
        }
        
        // Stop periodic check after 30 seconds to avoid performance issues
        if (visibilityCheckCount > 60) {
            clearInterval(visibilityInterval);
            console.log('⏰ Stopped periodic visibility checks');
        }
    }, 500);
    
    // 6. Initial setup with delays to ensure DOM is ready
    setTimeout(() => {
        console.log('⏰ Initial setup (100ms delay)');
        showRateInput();
        ensureRateInputVisible();
    }, 100);
    
    setTimeout(() => {
        console.log('⏰ Secondary setup (500ms delay)');
        ensureRateInputVisible();
    }, 500);
    
    setTimeout(() => {
        console.log('⏰ Final setup (1000ms delay)');
        ensureRateInputVisible();
    }, 1000);
    
    // Handle form submission with AJAX for popup notification
    const form = document.getElementById('jobPostForm');
    console.log('🚀 FORM FOUND:', form ? 'YES' : 'NO');
    
    if (form) {
        console.log('🎯 FORM ACTION:', form.action);
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('🔥 FORM SUBMITTED! Intercepting with AJAX...');
            
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Posting Job...';
            
            // Add multiple AJAX indicators
            formData.append('ajax', '1');
            
            console.log('📦 FORM DATA PREPARED');
            
            // Use fetch for better error handling
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => {
                console.log('📡 RESPONSE RECEIVED:', response.status);
                console.log('📡 RESPONSE TYPE:', response.headers.get('content-type'));
                
                return response.text().then(text => {
                    console.log('📄 RAW RESPONSE:', text.substring(0, 500));
                    
                    try {
                        const data = JSON.parse(text);
                        console.log('✅ JSON PARSED:', data);
                        return data;
                    } catch (e) {
                        console.log('❌ JSON PARSE FAILED:', e);
                        console.log('❌ FULL RAW RESPONSE:', text);
                        throw new Error('Invalid JSON response');
                    }
                });
            })
            .then(data => {
                console.log('🎯 PROCESSING DATA:', data);
                
                // Reset button state
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                
                if (data.success) {
                    console.log('🎉 SUCCESS! Showing modal...');
                    showSuccessModal(data.job);
                } else {
                    console.log('❌ ERROR! Showing error modal:', data.error);
                    showErrorModal(data.error || 'An error occurred while posting your job.');
                }
            })
            .catch(error => {
                console.error('💥 FETCH ERROR:', error);
                
                // Reset button state
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                
                // Show error modal
                showErrorModal('Network error. Please try again.');
            });
        });
    } else {
        console.log('❌ NO FORM FOUND!');
    }
});

// Show success modal
function showSuccessModal(jobData) {
    const modal = document.getElementById('successModal');
    const modalContent = document.getElementById('successModalContent');
    const jobTitle = document.getElementById('jobTitle');
    const viewJobBtn = document.getElementById('viewJobBtn');
    
    if (!modal) {
        alert('SUCCESS: Job "' + jobData.title + '" posted successfully!');
        return;
    }
    
    // Update modal content
    if (jobTitle) {
        jobTitle.textContent = `"${jobData.title}" is now live and visible to chatters.`;
    }
    
    if (viewJobBtn) {
        viewJobBtn.href = jobData.url || '#';
    }
    
    // Show modal with proper animation
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
    
    // Trigger animation after a brief moment
    requestAnimationFrame(() => {
        if (modalContent) {
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
        }
    });
}

// Close success modal
function closeSuccessModal() {
    const modal = document.getElementById('successModal');
    const modalContent = document.getElementById('successModalContent');
    
    // Hide with animation
    modalContent.classList.add('scale-95');
    modalContent.classList.remove('scale-100');
    
    setTimeout(() => {
        modal.classList.add('hidden');
        // Reset form for posting another job
        window.location.reload();
    }, 300);
}

// Close modal when clicking outside
document.getElementById('successModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeSuccessModal();
    }
});

// Test popup function
function testPopup() {
    console.log('Testing popup...');
    const testJobData = {
        title: 'Test Job Title',
        url: '#'
    };
    showSuccessModal(testJobData);
}

// Test AJAX call function
function testAjaxCall() {
    console.log('Testing AJAX call...');
    
    fetch('/test-ajax', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value
        },
        body: JSON.stringify({})
    })
    .then(response => {
        console.log('AJAX Response status:', response.status);
        console.log('AJAX Response headers:', response.headers.get('content-type'));
        return response.json();
    })
    .then(data => {
        console.log('AJAX Response data:', data);
        if (data.success) {
            alert('AJAX test successful! Backend responded: ' + data.message);
        } else {
            alert('AJAX test failed: ' + JSON.stringify(data));
        }
    })
    .catch(error => {
        console.error('AJAX Error:', error);
        alert('AJAX test error: ' + error.message);
    });
}

// Show error modal
function showErrorModal(errorMessage) {
    const modal = document.getElementById('errorModal');
    const modalContent = document.getElementById('errorModalContent');
    const errorMessageEl = document.getElementById('errorMessage');
    const upgradeBtn = document.getElementById('upgradeBtn');
    
    if (!modal) {
        alert('ERROR: ' + errorMessage);
        return;
    }
    
    // Update modal content
    if (errorMessageEl) {
        errorMessageEl.textContent = errorMessage;
    }
    
    // Show upgrade button if it's a subscription limit error
    if (upgradeBtn) {
        if (errorMessage.toLowerCase().includes('subscription') || errorMessage.toLowerCase().includes('limit') || errorMessage.toLowerCase().includes('upgrade')) {
            upgradeBtn.classList.remove('hidden');
        } else {
            upgradeBtn.classList.add('hidden');
        }
    }
    
    // Show modal with proper animation
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
    
    // Trigger animation after a brief moment
    requestAnimationFrame(() => {
        if (modalContent) {
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
        }
    });
}

// Close error modal
function closeErrorModal() {
    console.log('🔴 Closing error modal...');
    const modal = document.getElementById('errorModal');
    const modalContent = document.getElementById('errorModalContent');
    
    if (!modal) {
        console.log('❌ Error modal not found!');
        return;
    }
    
    if (!modalContent) {
        console.log('❌ Error modal content not found!');
        modal.classList.add('hidden');
        modal.style.display = 'none';
        return;
    }
    
    // Hide with animation
    modalContent.classList.add('scale-95');
    modalContent.classList.remove('scale-100');
    
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.style.display = 'none';
        console.log('✅ Error modal closed');
    }, 300);
}

// Close error modal when clicking outside
document.getElementById('errorModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeErrorModal();
    }
});

// Test job posting with popup
function testJobPostAjax() {
    console.log('Testing job post AJAX with popup...');
    
    // Get some basic form data
    const formData = new FormData();
    formData.append('title', 'Test Job from AJAX');
    formData.append('_token', document.querySelector('input[name="_token"]')?.value || '');
    formData.append('ajax', '1');
    
    fetch('/test-job-post', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => {
        console.log('Job Post Response status:', response.status);
        console.log('Job Post Response headers:', response.headers.get('content-type'));
        return response.json();
    })
    .then(data => {
        console.log('Job Post Response data:', data);
        if (data.success) {
            alert('Job post test successful! Now showing popup...');
            showSuccessModal(data.job);
        } else {
            alert('Job post test failed: Now showing error popup...');
            showErrorModal(data.error || 'Job post test failed');
        }
    })
    .catch(error => {
        console.error('Job Post AJAX Error:', error);
        showErrorModal('Job post test error: ' + error.message);
    });
}

// Test error popup
function testErrorPopup() {
    showErrorModal('You have reached your job posting limit for this month. Please upgrade your subscription.');
}
</script>

</x-layouts.app>
