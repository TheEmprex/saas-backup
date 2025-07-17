<x-layouts.app>
    <div class="bg-white dark:bg-gray-900 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Edit Review</h1>
                <p class="text-gray-600 dark:text-gray-300">Update your review for {{ $review->reviewedUser->name }}</p>
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

            <form action="{{ route('contracts.reviews.update', [$contract, $review]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="shadow overflow-hidden sm:rounded-md">
                    <div class="px-4 py-5 bg-white dark:bg-gray-800 sm:p-6">
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label for="rating" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Overall Rating</label>
                                <select name="rating" id="rating" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700">
                                    <option value="">Select a rating</option>
                                    <option value="5" @if($review->rating == 5) selected @endif>5 stars - Excellent</option>
                                    <option value="4" @if($review->rating == 4) selected @endif>4 stars - Good</option>
                                    <option value="3" @if($review->rating == 3) selected @endif>3 stars - Average</option>
                                    <option value="2" @if($review->rating == 2) selected @endif>2 stars - Poor</option>
                                    <option value="1" @if($review->rating == 1) selected @endif>1 star - Terrible</option>
                                </select>
                            </div>
                            <div>
                                <label for="comment" class="block text-sm font-medium text-gray-700 dark:text-gray-400">Comment</label>
                                <textarea name="comment" id="comment" rows="3" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md dark:bg-gray-700">{{ $review->comment }}</textarea>
                            </div>
                            <div class="flex items-center space-x-6">
                                <div class="flex items-center">
                                    <input type="checkbox" name="would_work_again" value="1" @if($review->would_work_again) checked @endif class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label for="would_work_again" class="ml-2 block text-sm text-gray-900 dark:text-white">Would work with again</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" name="recommend_to_others" value="1" @if($review->recommend_to_others) checked @endif class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label for="recommend_to_others" class="ml-2 block text-sm text-gray-900 dark:text-white">Recommend to others</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800 text-right sm:px-6">
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('contracts.show', $contract) }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Update Review
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
