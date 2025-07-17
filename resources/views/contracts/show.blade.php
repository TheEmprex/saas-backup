<x-layouts.app>
    <div class="bg-white dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Contract Details</h1>
                        <p class="text-gray-600 dark:text-gray-300">Manage your contract and track progress</p>
                    </div>
                    <div class="flex space-x-3">
                        @if($contract->status === 'active' && ($contract->employer_id === auth()->id() || $contract->contractor_id === auth()->id()))
                            <button onclick="openTerminateModal()" class="inline-flex items-center px-4 py-2 border border-yellow-300 text-sm font-medium rounded-md text-yellow-700 bg-white hover:bg-yellow-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                <svg class="-ml-1 mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" clip-rule="evenodd"/>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2h8a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 2a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                                </svg>
                                Terminate & Review
                            </button>
                        @endif
                        @if($contract->canBeEditedBy(auth()->user()))
                            <a href="{{ route('contracts.edit', $contract) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Edit
                            </a>
                        @endif
                        @if($contract->canBeDeletedBy(auth()->user()))
                            <form action="{{ route('contracts.destroy', $contract) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Are you sure you want to delete this contract?')" class="inline-flex items-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    Delete
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Contract Overview -->
            <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg mb-6">
                <div class="px-4 py-5 sm:px-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Contract Overview</h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">Key details about this contract</p>
                </div>
                <div class="border-t border-gray-200 dark:border-gray-700">
                    <dl>
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Parties</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                                <strong>Employer:</strong> {{ $contract->employer->name }}<br>
                                <strong>Contractor:</strong> {{ $contract->contractor->name }}
                            </dd>
                        </div>
                        <div class="bg-white dark:bg-gray-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Contract Type</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">{{ ucfirst($contract->contract_type) }}</dd>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Rate</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">{{ $contract->formatted_rate }}</dd>
                        </div>
                        <div class="bg-white dark:bg-gray-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                            <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $contract->status_color }}-100 text-{{ $contract->status_color }}-800">
                                    {{ ucfirst($contract->status) }}
                                </span>
                            </dd>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Duration</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                                {{ $contract->start_date->format('M j, Y') }} - {{ $contract->end_date ? $contract->end_date->format('M j, Y') : 'Ongoing' }}
                            </dd>
                        </div>
                        <div class="bg-white dark:bg-gray-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Earned</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                                ${{ number_format($contract->total_earned, 2) }} ({{ $contract->hours_worked }} hours)
                            </dd>
                        </div>
                        @if($contract->jobPost)
                            <div class="bg-white dark:bg-gray-800 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Related Job Post</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">
                                    <a href="#" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">{{ $contract->jobPost->title }}</a>
                                    <p class="mt-1 text-gray-500 dark:text-gray-400">{{ Str::limit($contract->jobPost->description, 100) }}</p>
                                </dd>
                            </div>
                        @endif
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white sm:mt-0 sm:col-span-2">{{ $contract->description }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Earnings Section -->
            @if($contract->employer_id === auth()->id())
                <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg mb-6">
                    <div class="px-4 py-5 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Add Earnings</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">Record work completed and payments</p>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-5">
                        <form action="{{ route('contracts.add-earning', $contract) }}" method="POST">
                            @csrf
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                                <div>
                                    <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Amount ($)</label>
                                    <input type="number" name="amount" id="amount" step="0.01" min="0" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700">
                                </div>
                                <div>
                                    <label for="hours" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Hours (optional)</label>
                                    <input type="number" name="hours" id="hours" min="0" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700">
                                </div>
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Description</label>
                                    <input type="text" name="description" id="description" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700">
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Add Earnings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Earnings History -->
            @if($contract->earnings_log && count($contract->earnings_log) > 0)
                <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg mb-6">
                    <div class="px-4 py-5 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Earnings History</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">Record of all earnings for this contract</p>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-700">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Hours</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                                        @if($contract->employer_id === auth()->id())
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($contract->earnings_log as $index => $earning)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($earning['date'])->format('M j, Y') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $earning['description'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $earning['hours'] ?? '-' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${{ number_format($earning['amount'], 2) }}</td>
                                            @if($contract->employer_id === auth()->id())
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <form action="{{ route('contracts.remove-earning', [$contract, $index]) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" onclick="return confirm('Are you sure you want to remove this earning?')" class="text-red-600 hover:text-red-900">
                                                            Remove
                                                        </button>
                                                    </form>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Reviews Section -->
            @if($contract->canBeReviewedBy(auth()->user()))
                <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg mb-6">
                    <div class="px-4 py-5 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Leave a Review</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">Share your experience working with {{ $contract->getOtherParty(auth()->user())->name }}</p>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-5">
                        <form action="{{ route('contracts.reviews.store', $contract) }}" method="POST">
                            @csrf
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label for="rating" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Overall Rating</label>
                                    <select name="rating" id="rating" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700">
                                        <option value="">Select a rating</option>
                                        <option value="5">5 stars - Excellent</option>
                                        <option value="4">4 stars - Good</option>
                                        <option value="3">3 stars - Average</option>
                                        <option value="2">2 stars - Poor</option>
                                        <option value="1">1 star - Terrible</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="comment" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Comment</label>
                                    <textarea name="comment" id="comment" rows="3" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700"></textarea>
                                </div>
                                <div class="flex items-center space-x-6">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="would_work_again" value="1" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <label for="would_work_again" class="ml-2 block text-sm text-gray-900 dark:text-white">Would work with again</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" name="recommend_to_others" value="1" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <label for="recommend_to_others" class="ml-2 block text-sm text-gray-900 dark:text-white">Recommend to others</label>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Submit Review
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Existing Reviews -->
            @if($contract->reviews && $contract->reviews->count() > 0)
                <div class="bg-white dark:bg-gray-800 shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Reviews</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">Feedback from both parties</p>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-700">
                        @foreach($contract->reviews as $review)
                            <div class="px-4 py-5 border-b border-gray-200 dark:border-gray-700 last:border-b-0">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2">
                                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ $review->reviewer->name }}</h4>
                                            <span class="text-sm text-gray-500 dark:text-gray-400">reviewed</span>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $review->reviewedUser->name }}</span>
                                        </div>
                                        <div class="mt-1">
                                            <div class="flex items-center">
                                                <span class="text-yellow-400">{{ $review->stars }}</span>
                                                <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                        @if($review->comment)
                                            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">{{ $review->comment }}</p>
                                        @endif
                                        <div class="mt-2 flex space-x-4 text-xs text-gray-500 dark:text-gray-400">
                                            @if($review->would_work_again)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-green-100 text-green-800">Would work again</span>
                                            @endif
                                            @if($review->recommend_to_others)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-blue-100 text-blue-800">Recommends</span>
                                            @endif
                                        </div>
                                    </div>
                                    @if($review->reviewer_id === auth()->id())
                                        <div class="ml-4 flex space-x-2">
                                            <a href="{{ route('contracts.reviews.edit', [$contract, $review]) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</a>
                                            <form action="{{ route('contracts.reviews.destroy', [$contract, $review]) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Are you sure you want to delete this review?')" class="text-red-600 hover:text-red-900 text-sm">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Terminate Contract Modal -->
    <div id="terminateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" style="z-index: 1000;">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <div class="flex items-center justify-between pb-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Terminate Contract & Leave Review</h3>
                    <button onclick="closeTerminateModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <form action="{{ route('contracts.terminate-and-review', $contract) }}" method="POST" class="mt-6">
                    @csrf
                    <div class="space-y-6">
                        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                            <div class="flex">
                                <svg class="flex-shrink-0 w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Contract Termination</h3>
                                    <p class="mt-1 text-sm text-yellow-700">This action will set the contract status to 'completed' and cannot be undone. Please leave a review for {{ $contract->getOtherParty(auth()->user())->name }}.</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="terminate_rating" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Overall Rating</label>
                            <select name="rating" id="terminate_rating" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700">
                                <option value="">Select a rating</option>
                                <option value="5">5 stars - Excellent</option>
                                <option value="4">4 stars - Good</option>
                                <option value="3">3 stars - Average</option>
                                <option value="2">2 stars - Poor</option>
                                <option value="1">1 star - Terrible</option>
                            </select>
                        </div>

                        <div>
                            <label for="terminate_comment" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Review Comment</label>
                            <textarea name="comment" id="terminate_comment" rows="3" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700" placeholder="Share your experience working with {{ $contract->getOtherParty(auth()->user())->name }}..."></textarea>
                        </div>

                        <div class="flex items-center space-x-6">
                            <div class="flex items-center">
                                <input type="checkbox" name="would_work_again" value="1" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label class="ml-2 block text-sm text-gray-900 dark:text-white">Would work with again</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="recommend_to_others" value="1" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <label class="ml-2 block text-sm text-gray-900 dark:text-white">Recommend to others</label>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" onclick="closeTerminateModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Terminate Contract & Submit Review
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openTerminateModal() {
            document.getElementById('terminateModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeTerminateModal() {
            document.getElementById('terminateModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        document.getElementById('terminateModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeTerminateModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeTerminateModal();
            }
        });
    </script>
</x-layouts.app>
