<x-layouts.app>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8 border border-gray-200 dark:border-gray-700">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">Edit Rating</h1>

            <form method="POST" action="{{ route('ratings.update', $rating->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rating for:</label>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $rating->rated->name }}</p>
                    @if($rating->jobPost)
                        <p class="text-sm text-gray-600 dark:text-gray-400">Job: {{ $rating->jobPost->title }}</p>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Overall Rating -->
                    <div>
                        <label for="overall_rating" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Overall Rating *</label>
                        <select name="overall_rating" id="overall_rating" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" required>
                            @for ($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" {{ $rating->overall_rating == $i ? 'selected' : '' }}>{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                            @endfor
                        </select>
                        @error('overall_rating')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Communication Rating -->
                    <div>
                        <label for="communication_rating" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Communication Rating</label>
                        <select name="communication_rating" id="communication_rating" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Select Rating</option>
                            @for ($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" {{ $rating->communication_rating == $i ? 'selected' : '' }}>{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                            @endfor
                        </select>
                        @error('communication_rating')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Professionalism Rating -->
                    <div>
                        <label for="professionalism_rating" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Professionalism Rating</label>
                        <select name="professionalism_rating" id="professionalism_rating" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Select Rating</option>
                            @for ($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" {{ $rating->professionalism_rating == $i ? 'selected' : '' }}>{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                            @endfor
                        </select>
                        @error('professionalism_rating')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Timeliness Rating -->
                    <div>
                        <label for="timeliness_rating" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Timeliness Rating</label>
                        <select name="timeliness_rating" id="timeliness_rating" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Select Rating</option>
                            @for ($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" {{ $rating->timeliness_rating == $i ? 'selected' : '' }}>{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                            @endfor
                        </select>
                        @error('timeliness_rating')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Quality Rating -->
                    <div class="md:col-span-2">
                        <label for="quality_rating" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quality Rating</label>
                        <select name="quality_rating" id="quality_rating" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Select Rating</option>
                            @for ($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" {{ $rating->quality_rating == $i ? 'selected' : '' }}>{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                            @endfor
                        </select>
                        @error('quality_rating')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Review Title -->
                <div class="mb-6">
                    <label for="review_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Review Title</label>
                    <input type="text" name="review_title" id="review_title" value="{{ old('review_title', $rating->review_title) }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="Enter a title for your review (optional)">
                    @error('review_title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Review Content -->
                <div class="mb-6">
                    <label for="review_content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Review Content</label>
                    <textarea name="review_content" id="review_content" rows="4" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" placeholder="Write your detailed review here (optional)">{{ old('review_content', $rating->review_content) }}</textarea>
                    @error('review_content')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Public Rating -->
                <div class="mb-6">
                    <div class="flex items-center">
                        <input type="checkbox" name="is_public" id="is_public" value="1" {{ old('is_public', $rating->is_public) ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 rounded">
                        <label for="is_public" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                            Make this rating public (visible to other users)
                        </label>
                    </div>
                    @error('is_public')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-between">
                    <a href="{{ route('ratings.index') }}" class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500 transition duration-200">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                        Update Rating
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

</x-layouts.app>
