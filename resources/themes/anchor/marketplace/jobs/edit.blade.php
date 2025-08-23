<x-layouts.app
    :seo="[
        'title'         => 'Edit Job - OnlyFans Management Marketplace',
        'description'   => 'Update your job posting on the OnlyFans management marketplace.',
        'image'         => url('/og_image.png'),
        'type'          => 'website'
    ]"
>

<div class="bg-white dark:bg-zinc-800">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
            </div>
            <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-3">Edit Job</h1>
            <p class="text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">Update your job posting details and requirements</p>
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

        <form action="{{ route('marketplace.jobs.update', $job) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
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
                            value="{{ old('title', $job->title) }}"
                            class="w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="e.g., English Market Chatter Needed"
                            required
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Market *</label>
                        <select name="market" class="w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="">Select Market</option>
                            <option value="english" {{ old('market', $job->market) === 'english' ? 'selected' : '' }}>English</option>
                            <option value="spanish" {{ old('market', $job->market) === 'spanish' ? 'selected' : '' }}>Spanish</option>
                            <option value="french" {{ old('market', $job->market) === 'french' ? 'selected' : '' }}>French</option>
                            <option value="german" {{ old('market', $job->market) === 'german' ? 'selected' : '' }}>German</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Experience Level *</label>
                        <select name="experience_level" class="w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="">Select Level</option>
                            <option value="beginner" {{ old('experience_level', $job->experience_level) === 'beginner' ? 'selected' : '' }}>Beginner</option>
                            <option value="intermediate" {{ old('experience_level', $job->experience_level) === 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                            <option value="advanced" {{ old('experience_level', $job->experience_level) === 'advanced' ? 'selected' : '' }}>Advanced</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Contract Type *</label>
                        <select name="contract_type" class="w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="">Select Type</option>
                            <option value="full_time" {{ old('contract_type', $job->contract_type) === 'full_time' ? 'selected' : '' }}>Full Time</option>
                            <option value="part_time" {{ old('contract_type', $job->contract_type) === 'part_time' ? 'selected' : '' }}>Part Time</option>
                            <option value="contract" {{ old('contract_type', $job->contract_type) === 'contract' ? 'selected' : '' }}>Contract</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Expected Hours per Week *</label>
                        <input 
                            type="number" 
                            name="expected_hours_per_week" 
                            value="{{ old('expected_hours_per_week', $job->expected_hours_per_week) }}"
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
                            value="{{ old('duration_months', $job->duration_months) }}"
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
                            value="{{ old('min_typing_speed', $job->min_typing_speed) }}"
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
                            value="{{ old('start_date', $job->start_date ? $job->start_date->format('Y-m-d') : '') }}"
                            class="w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Maximum Applications *</label>
                        <input 
                            type="number" 
                            name="max_applications" 
                            value="{{ old('max_applications', $job->max_applications) }}"
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
                    >{{ old('description', $job->description) }}</textarea>
                </div>

                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Requirements</label>
                    <textarea 
                        name="requirements" 
                        rows="4"
                        class="w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="List specific requirements and qualifications..."
                    >{{ old('requirements', $job->requirements) }}</textarea>
                </div>

                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Benefits</label>
                    <textarea 
                        name="benefits" 
                        rows="4"
                        class="w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="What benefits do you offer? (flexible hours, bonuses, etc.)"
                    >{{ old('benefits', $job->benefits) }}</textarea>
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
                                    {{ old('rate_type', $job->rate_type) === 'hourly' ? 'checked' : '' }}
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
                                    {{ old('rate_type', $job->rate_type) === 'fixed' ? 'checked' : '' }}
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
                                    {{ old('rate_type', $job->rate_type) === 'commission' ? 'checked' : '' }}
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
                            value="{{ old('hourly_rate', $job->hourly_rate) }}"
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
                            value="{{ old('fixed_rate', $job->fixed_rate) }}"
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
                            value="{{ old('commission_percentage', $job->commission_percentage) }}"
                            class="w-full rounded-md border-gray-300 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            step="0.01"
                            min="0"
                            max="100"
                        >
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
                                {{ old('is_featured', $job->is_featured) ? 'checked' : '' }}
                                class="mr-2"
                            >
                            <span class="text-gray-900 dark:text-white">Feature this job (+$10)</span>
                        </label>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Featured jobs appear at the top of search results</p>
                    </div>

                    <div>
                        <label class="flex items-center text-gray-900 dark:text-white">
                            <input 
                                type="checkbox" 
                                name="is_urgent" 
                                value="1"
                                {{ old('is_urgent', $job->is_urgent) ? 'checked' : '' }}
                                class="mr-2"
                            >
                            <span class="text-gray-900 dark:text-white">Mark as urgent (+$5)</span>
                        </label>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">Urgent jobs get an eye-catching badge</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('marketplace.my-jobs') }}" class="px-6 py-2 border border-gray-300 dark:border-zinc-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-zinc-700">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-md hover:from-blue-700 hover:to-purple-700 transition-all shadow-lg">
                    <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Update Job
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
