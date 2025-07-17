<x-layouts.app>

<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">My Ratings</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Average Rating</h3>
            <div class="flex items-center">
                <div class="flex">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= $averageRating)
                            <svg class="w-5 h-5 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-gray-300 fill-current" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endif
                    @endfor
                </div>
                <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">{{ number_format($averageRating, 1) }}</span>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Total Ratings</h3>
            <p class="text-2xl font-bold text-blue-600">{{ $totalRatings }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Given Ratings</h3>
            <p class="text-2xl font-bold text-green-600">{{ $givenRatings->total() }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Received Ratings -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Ratings Received</h2>
            
            @if($receivedRatings->count() > 0)
                <div class="space-y-4">
                    @foreach($receivedRatings as $rating)
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-4 last:border-b-0">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $rating->reviewer->name ?? 'Unknown Reviewer' }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ $rating->created_at->diffForHumans() }}</p>
                                </div>
                                <div class="flex">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $rating->rating)
                                            <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 text-gray-300 fill-current" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                            
                            @if($rating->comment)
                                <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">{{ $rating->comment }}</p>
                            @endif
                            
                            @if($rating->contract)
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    For contract: <span class="font-medium">{{ $rating->contract->title ?? 'Contract #' . $rating->contract->id }}</span>
                                </p>
                            @endif
                            
                            @if($rating->would_work_again || $rating->recommend_to_others)
                                <div class="mt-2 flex space-x-2">
                                    @if($rating->would_work_again)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Would work again</span>
                                    @endif
                                    @if($rating->recommend_to_others)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Recommends</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-4">
                    {{ $receivedRatings->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-gray-600 dark:text-gray-300">No ratings received yet.</p>
                </div>
            @endif
        </div>

        <!-- Given Ratings -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Ratings Given</h2>
            
            @if($givenRatings->count() > 0)
                <div class="space-y-4">
                    @foreach($givenRatings as $rating)
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-4 last:border-b-0">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $rating->reviewedUser->name ?? 'Unknown User' }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ $rating->created_at->diffForHumans() }}</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <div class="flex">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $rating->rating)
                                                <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4 text-gray-300 fill-current" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            
                            @if($rating->comment)
                                <p class="text-sm text-gray-600 dark:text-gray-300 mb-2">{{ $rating->comment }}</p>
                            @endif
                            
                            @if($rating->contract)
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    For contract: <span class="font-medium">{{ $rating->contract->title ?? 'Contract #' . $rating->contract->id }}</span>
                                </p>
                            @endif
                            
                            @if($rating->would_work_again || $rating->recommend_to_others)
                                <div class="mt-2 flex space-x-2">
                                    @if($rating->would_work_again)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Would work again</span>
                                    @endif
                                    @if($rating->recommend_to_others)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Recommends</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-4">
                    {{ $givenRatings->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-gray-600 dark:text-gray-300">No ratings given yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>

</x-layouts.app>
