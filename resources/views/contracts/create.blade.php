<x-layouts.app>
    <div class="bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-900 dark:to-gray-800 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center mb-6">
                    <a href="{{ route('contracts.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Back to Contracts
                    </a>
                </div>
                <div class="text-center">
                    <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">
                        Create 
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">Contract</span>
                    </h1>
                    <p class="text-gray-600 dark:text-gray-300 text-lg">Initiate a new contract with detailed terms</p>
                </div>
            </div>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 mb-6">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-400">Please correct the following errors:</h3>
                    </div>
                    <ul class="text-sm text-red-700 dark:text-red-300 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li class="flex items-start">
                                <span class="w-1 h-1 bg-red-500 rounded-full mt-2 mr-2 flex-shrink-0"></span>
                                {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Contract Form -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <form action="{{ route('contracts.store') }}" method="POST">
                    @csrf
                    <div class="p-8">
                        <div class="space-y-8">
                            <!-- Job Post Section -->
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-6 border border-blue-200 dark:border-blue-800">
                                <div class="flex items-center mb-4">
                                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/50 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Job Post (Optional)</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">Link this contract to an existing job posting</p>
                                    </div>
                                </div>
                                <select id="job_post_id" name="job_post_id" class="w-full px-4 py-3 text-base border-2 border-blue-200 dark:border-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 rounded-lg dark:bg-gray-700 dark:text-white transition-all duration-200" onchange="populateFromJobPost()">
                                    <option value="">Select a job post to pre-populate contract details</option>
                                    @foreach ($jobPosts as $jobPost)
                                        <option value="{{ $jobPost->id }}" 
                                            data-title="{{ $jobPost->title }}"
                                            data-description="{{ $jobPost->description }}"
                                            data-rate-type="{{ $jobPost->rate_type }}"
                                            data-hourly-rate="{{ $jobPost->hourly_rate }}"
                                            data-fixed-rate="{{ $jobPost->fixed_rate }}"
                                            data-commission-percentage="{{ $jobPost->commission_percentage }}"
                                        >{{ $jobPost->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Contract Details -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Contractor Selection -->
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6 border border-gray-200 dark:border-gray-600">
                                    <div class="flex items-center mb-4">
                                        <div class="w-8 h-8 bg-green-100 dark:bg-green-900/50 rounded-lg flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Contractor</h3>
                                            <p class="text-sm text-gray-600 dark:text-gray-300">Who will work on this contract</p>
                                        </div>
                                    </div>
                                    <select id="contractor_id" name="contractor_id" autocomplete="contractor_id" class="w-full px-4 py-3 text-base border-2 border-gray-200 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 rounded-lg dark:bg-gray-700 dark:text-white transition-all duration-200">
                                        @if($users->isEmpty())
                                            <option value="" disabled>No users available - you need to message someone first</option>
                                        @else
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @if($users->isEmpty())
                                        <div class="mt-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                                <p class="text-sm text-yellow-800 dark:text-yellow-300">You can only create contracts with users you have messaged. Start by messaging someone from the marketplace.</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Contract Type -->
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6 border border-gray-200 dark:border-gray-600">
                                    <div class="flex items-center mb-4">
                                        <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/50 rounded-lg flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Contract Type</h3>
                                            <p class="text-sm text-gray-600 dark:text-gray-300">How will payment be structured</p>
                                        </div>
                                    </div>
                                    <select id="contract_type" name="contract_type" autocomplete="contract_type" class="w-full px-4 py-3 text-base border-2 border-gray-200 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 rounded-lg dark:bg-gray-700 dark:text-white transition-all duration-200">
                                        <option value="hourly">💰 Hourly Rate</option>
                                        <option value="fixed">💵 Fixed Price</option>
                                        <option value="commission">📈 Commission Based</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Rate and Date -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Rate -->
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6 border border-gray-200 dark:border-gray-600">
                                    <div class="flex items-center mb-4">
                                        <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900/50 rounded-lg flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Rate</h3>
                                            <p class="text-sm text-gray-600 dark:text-gray-300">Payment amount or percentage</p>
                                        </div>
                                    </div>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 font-semibold">$</span>
                                        <input type="number" name="rate" id="rate" step="0.01" min="0" placeholder="0.00" class="w-full pl-8 pr-4 py-3 text-base border-2 border-gray-200 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 rounded-lg dark:bg-gray-700 dark:text-white transition-all duration-200">
                                    </div>
                                </div>

                                <!-- Start Date -->
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6 border border-gray-200 dark:border-gray-600">
                                    <div class="flex items-center mb-4">
                                        <div class="w-8 h-8 bg-red-100 dark:bg-red-900/50 rounded-lg flex items-center justify-center mr-3">
                                            <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Start Date</h3>
                                            <p class="text-sm text-gray-600 dark:text-gray-300">When the contract begins</p>
                                        </div>
                                    </div>
                                    <input type="date" name="start_date" id="start_date" class="w-full px-4 py-3 text-base border-2 border-gray-200 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 rounded-lg dark:bg-gray-700 dark:text-white transition-all duration-200">
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-6 border border-gray-200 dark:border-gray-600">
                                <div class="flex items-center mb-4">
                                    <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900/50 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Description</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">Contract terms and project details</p>
                                    </div>
                                </div>
                                <textarea id="description" name="description" rows="4" placeholder="Describe the work to be done, deliverables, timeline, and any specific requirements..." class="w-full px-4 py-3 text-base border-2 border-gray-200 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 rounded-lg dark:bg-gray-700 dark:text-white resize-none transition-all duration-200"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Submit Footer -->
                    <div class="bg-gradient-to-r from-gray-50 to-blue-50 dark:from-gray-700/50 dark:to-blue-900/20 px-8 py-6 border-t border-gray-200 dark:border-gray-600">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-600 dark:text-gray-300">
                                <p>By creating this contract, you agree to the terms of service.</p>
                            </div>
                            <div class="flex space-x-3">
                                <a href="{{ route('contracts.index') }}" class="inline-flex items-center px-6 py-3 border-2 border-gray-300 dark:border-gray-600 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                                    Cancel
                                </a>
                                <button type="submit" class="inline-flex items-center px-8 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-lg transition-all duration-200 transform hover:scale-105">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Create Contract
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function populateFromJobPost() {
            const jobPostSelect = document.getElementById('job_post_id');
            const selectedOption = jobPostSelect.options[jobPostSelect.selectedIndex];
            
            if (selectedOption.value) {
                const description = selectedOption.getAttribute('data-description');
                const rateType = selectedOption.getAttribute('data-rate-type');
                const hourlyRate = selectedOption.getAttribute('data-hourly-rate');
                const fixedRate = selectedOption.getAttribute('data-fixed-rate');
                const commissionPercentage = selectedOption.getAttribute('data-commission-percentage');
                
                // Pre-populate description
                document.getElementById('description').value = description;
                
                // Pre-populate contract type and rate
                const contractTypeSelect = document.getElementById('contract_type');
                const rateInput = document.getElementById('rate');
                
                contractTypeSelect.value = rateType;
                
                if (rateType === 'hourly' && hourlyRate) {
                    rateInput.value = hourlyRate;
                } else if (rateType === 'fixed' && fixedRate) {
                    rateInput.value = fixedRate;
                } else if (rateType === 'commission' && commissionPercentage) {
                    rateInput.value = commissionPercentage;
                }
            } else {
                // Clear pre-populated values
                document.getElementById('description').value = '';
                document.getElementById('contract_type').selectedIndex = 0;
                document.getElementById('rate').value = '';
            }
        }
    </script>
</x-layouts.app>

