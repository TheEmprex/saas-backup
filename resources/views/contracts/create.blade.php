<x-layouts.app>
    <div class="bg-white dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Create Contract</h1>
                <p class="text-gray-600 dark:text-gray-300">Initiate a new contract</p>
            </div>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('contracts.store') }}" method="POST">
                @csrf
                <div class="shadow overflow-hidden sm:rounded-md">
                    <div class="px-4 py-5 bg-white dark:bg-gray-800 sm:p-6">
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-3">
                                <label for="job_post_id" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Job Post (Optional)</label>
                                <select id="job_post_id" name="job_post_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700" onchange="populateFromJobPost()">
                                    <option value="">Select a job post to pre-populate</option>
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
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Select a job post to pre-populate contract details.</p>
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="contractor_id" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Contractor</label>
                                <select id="contractor_id" name="contractor_id" autocomplete="contractor_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700">
                                    @if($users->isEmpty())
                                        <option value="" disabled>No users available - you need to message someone first</option>
                                    @else
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                        @endforeach
                                    @endif
                                </select>
                                @if($users->isEmpty())
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">You can only create contracts with users you have messaged. Start by messaging someone from the marketplace.</p>
                                @endif
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="contract_type" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Contract Type</label>
                                <select id="contract_type" name="contract_type" autocomplete="contract_type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700">
                                    <option value="hourly">Hourly</option>
                                    <option value="fixed">Fixed</option>
                                    <option value="commission">Commission</option>
                                </select>
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="rate" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Rate</label>
                                <input type="number" name="rate" id="rate" step="0.01" min="0" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700">
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Start Date</label>
                                <input type="date" name="start_date" id="start_date" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700">
                            </div>

                            <div class="col-span-6">
                                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Description</label>
                                <textarea id="description" name="description" rows="3" class="shadow-sm mt-1 block w-full sm:text-sm border border-gray-300 rounded-md dark:bg-gray-700"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800 text-right sm:px-6">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Create
                        </button>
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

