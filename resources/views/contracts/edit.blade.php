<x-layouts.app>
    <div class="bg-white dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Edit Contract</h1>
                <p class="text-gray-600 dark:text-gray-300">Modify your contract details</p>
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

            <form action="{{ route('contracts.update', $contract) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="shadow overflow-hidden sm:rounded-md">
                    <div class="px-4 py-5 bg-white dark:bg-gray-800 sm:p-6">
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-3">
                                <label for="contract_type" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Contract Type</label>
                                <select id="contract_type" name="contract_type" autocomplete="contract_type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700">
                                    <option value="hourly" @if($contract->contract_type == 'hourly') selected @endif>Hourly</option>
                                    <option value="fixed" @if($contract->contract_type == 'fixed') selected @endif>Fixed</option>
                                    <option value="commission" @if($contract->contract_type == 'commission') selected @endif>Commission</option>
                                </select>
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="rate" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Rate</label>
                                <input type="number" name="rate" id="rate" step="0.01" min="0" value="{{ $contract->rate ?? $contract->commission_percentage }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700">
                            </div>

                            <div class="col-span-6">
                                <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-400">End Date</label>
                                <input type="date" name="end_date" id="end_date" value="{{ $contract->end_date?->toDateString() }}" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700">
                            </div>

                            <div class="col-span-6">
                                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Description</label>
                                <textarea id="description" name="description" rows="3" class="shadow-sm mt-1 block w-full sm:text-sm border border-gray-300 rounded-md dark:bg-gray-700">{{ $contract->description }}</textarea>
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Status</label>
                                <select id="status" name="status" autocomplete="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700">
                                    <option value="active" @if($contract->status == 'active') selected @endif>Active</option>
                                    <option value="completed" @if($contract->status == 'completed') selected @endif>Completed</option>
                                    <option value="cancelled" @if($contract->status == 'cancelled') selected @endif>Cancelled</option>
                                    <option value="suspended" @if($contract->status == 'suspended') selected @endif>Suspended</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800 text-right sm:px-6">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Update
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>

