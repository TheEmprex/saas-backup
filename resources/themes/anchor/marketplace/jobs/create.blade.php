<x-layouts.app
    :seo="[
        'title'         => 'Post a Job - OnlyFans Management Marketplace',
        'description'   => 'Find qualified chatters for your OnlyFans management agency.',
        'image'         => url('/og_image.png'),
        'type'          => 'website'
    ]"
>

<div class="bg-white dark:bg-zinc-800">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Post a Job</h1>
            <p class="text-gray-600 dark:text-gray-300">Find the perfect chatter for your OnlyFans management agency</p>
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
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <h3 class="text-lg font-semibold text-blue-900 mb-2">Your Subscription Usage</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-blue-700">Current Plan: <span class="font-semibold">{{ $plan->name }}</span></p>
                        <p class="text-sm text-blue-700">Job Posts This Month: <span class="font-semibold">{{ $usedJobPosts }}/{{ $totalJobPosts === null ? '∞' : $totalJobPosts }}</span></p>
                        <p class="text-sm text-blue-700">Remaining Posts: <span class="font-semibold">{{ $remainingJobPosts === 999 ? '∞' : $remainingJobPosts }}</span></p>
                    </div>
                    <div>
                        @if($user->canUseFeaturedForFree())
                            <p class="text-sm text-green-700">✓ Featured jobs included in your plan</p>
                        @else
                            <p class="text-sm text-orange-700">Featured jobs cost $10 each</p>
                        @endif
                        <p class="text-sm text-blue-700">Urgent badge: $5 per job</p>
                    </div>
                </div>
                @if($remainingJobPosts <= 0)
                    <div class="mt-4 p-3 bg-red-100 border border-red-200 rounded">
                        <p class="text-sm text-red-700">You've reached your job posting limit. <a href="{{ route('subscription.plans') }}" class="underline">Upgrade your plan</a> to post more jobs.</p>
                    </div>
                @endif
            </div>
        @endif

        <form action="{{ route('marketplace.jobs.store') }}" method="POST" class="space-y-6" {{ auth()->user()->requiresVerification() ? 'style=display:none;' : '' }}>
            @csrf
            
            <div class="bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-6 text-gray-900 dark:text-white">Job Details</h2>
                
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
                            max="80"
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
                            value="{{ old('start_date') }}"
                            class="w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
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

            <div class="bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-6 text-gray-900 dark:text-white">Compensation</h2>
                
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
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Hourly Rate ($)</label>
                        <input 
                            type="number" 
                            name="hourly_rate" 
                            value="{{ old('hourly_rate') }}"
                            class="w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            step="0.01"
                            min="0"
                        >
                    </div>

                    <div id="fixed-rate" class="rate-input" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Fixed Rate ($)</label>
                        <input 
                            type="number" 
                            name="fixed_rate" 
                            value="{{ old('fixed_rate') }}"
                            class="w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            step="0.01"
                            min="0"
                        >
                    </div>

                    <div id="commission-rate" class="rate-input" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Commission Percentage (%)</label>
                        <input 
                            type="number" 
                            name="commission_percentage" 
                            value="{{ old('commission_percentage') }}"
                            class="w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            step="0.01"
                            min="0"
                            max="100"
                        >
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-700 rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-6 text-gray-900 dark:text-white">Job Visibility</h2>
                
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
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    Post Job
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const rateTypeInputs = document.querySelectorAll('input[name="rate_type"]');
    const rateInputs = document.querySelectorAll('.rate-input');
    
    function showRateInput() {
        // Hide all rate inputs
        rateInputs.forEach(input => {
            input.style.display = 'none';
        });
        
        // Show selected rate input
        const selectedType = document.querySelector('input[name="rate_type"]:checked');
        if (selectedType) {
            const targetInput = document.getElementById(selectedType.value + '-rate');
            if (targetInput) {
                targetInput.style.display = 'block';
            }
        }
    }
    
    // Show initial rate input based on old value
    showRateInput();
    
    // Add event listeners to all rate type inputs
    rateTypeInputs.forEach(input => {
        input.addEventListener('change', showRateInput);
        input.addEventListener('click', showRateInput);
    });
    
    // Add event listener to the entire document for radio button clicks
    document.addEventListener('click', function(e) {
        if (e.target.type === 'radio' && e.target.name === 'rate_type') {
            setTimeout(showRateInput, 10);
        }
    });
    
    // Add event listener to the entire document for changes
    document.addEventListener('change', function(e) {
        if (e.target.name === 'rate_type') {
            showRateInput();
        }
    });
});
</script>

</x-layouts.app>
