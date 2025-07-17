@extends('theme::layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white dark:bg-zinc-800 rounded-lg shadow-lg overflow-hidden">
    <div class="px-4 py-5 sm:px-6 border-b">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Profile of {{ $user->name }}</h1>
    </div>
    <div class="p-6">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">About</h2>
        <p class="text-gray-600 dark:text-gray-400 mt-2">
            {{ $user->userProfile->bio ?? 'No bio available.' }}
        </p>

        <h2 class="mt-4 text-lg font-semibold text-gray-800 dark:text-gray-200">Details</h2>
        <ul class="text-gray-600 dark:text-gray-400">
            <li><strong>Location:</strong> {{ $user->userProfile->location ?? 'Not specified' }}</li>
            <li><strong>Website:</strong> 
                @if($user->userProfile->website)
                    <a class="text-blue-600 dark:text-blue-400 hover:underline" href="{{ $user->userProfile->website }}">
                        {{ $user->userProfile->website }}
                    </a>
                @else
                    Not specified
                @endif
            </li>
        </ul>
    <h2 class="mt-4 text-lg font-semibold text-gray-800 dark:text-gray-200">Reviews</h2>
    <div class="mt-2 space-y-4">
        @forelse($user->contractReviewsReceived as $review)
            <div class="bg-gray-50 dark:bg-zinc-700 p-4 rounded-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <img class="h-8 w-8 rounded-full" src="{{ $review->reviewer->profile_picture_url }}" alt="">
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $review->reviewer->name }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $review->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <span class="text-yellow-400">{{ $review->stars }}</span>
                    </div>
                </div>
                <div class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                    {{ $review->comment ?? 'No comment provided.' }}
                </div>
            </div>
        @empty
            <p class="text-gray-600 dark:text-gray-400">No reviews yet.</p>
        @endforelse
    </div>
</div>
</div>
@endsection
