<x-layouts.app>

<div class="bg-white dark:bg-gray-900 min-h-screen">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Feature Your Profile</h1>
            <p class="text-gray-600 dark:text-gray-400">Stand out to potential clients with a featured profile for just $5.</p>
        </div>

        @if($errors->any())
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Please fix the following errors:</h3>
                        <ul class="mt-2 text-sm text-red-700 dark:text-red-300 list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        @if($isFeaturedActive)
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-6 mb-6">
                <div class="flex items-center mb-4">
                    <svg class="w-6 h-6 text-blue-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <div>
                        <h3 class="text-lg font-semibold text-blue-800 dark:text-blue-200">Your profile is already featured!</h3>
                        <p class="text-sm text-blue-600 dark:text-blue-300 mt-1">
                            @php
                                $featuredUntil = $profile->featured_until;
                                if ($featuredUntil && $featuredUntil->isFuture()) {
                                    $daysRemaining = max(1, ceil($featuredUntil->diffInDays(now())));
                                    $daysText = $daysRemaining . ' days remaining';
                                } else {
                                    $daysText = 'Expired';
                                }
                            @endphp
                            Featured until {{ $profile->featured_until->format('M j, Y') }} 
                            ({{ $daysText }})
                        </p>
                    </div>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('profile.show') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                        View My Profile
                    </a>
                    <a href="{{ route('profile.edit') }}" class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-6 py-2 rounded-lg font-medium transition-colors">
                        Edit Profile
                    </a>
                </div>
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 mb-6">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Feature Your Profile</h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">Get premium visibility and stand out to potential clients</p>
                    
                    <div class="text-center mb-6">
                        <span class="text-4xl font-bold text-green-600 dark:text-green-400">${{ number_format($featuredCost, 2) }}</span>
                        <span class="text-lg text-gray-500 dark:text-gray-400 ml-2">for 30 days</span>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-blue-800 dark:text-blue-200 mb-3">✨ What you get with a featured profile:</h3>
                    <ul class="text-sm text-blue-600 dark:text-blue-300 space-y-2">
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Premium visual styling with gradients and enhanced design
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Featured badge and corner ribbon to stand out
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Higher visibility in search results and listings
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            30 full days of premium exposure
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Professional appearance that builds trust
                        </li>
                    </ul>
                </div>

                <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 mb-6">
                    <h3 class="font-medium text-gray-900 dark:text-white mb-2">Profile Preview:</h3>
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <img src="{{ $user->getProfilePictureUrl() }}" alt="{{ $user->name }}" class="w-12 h-12 rounded-full object-cover">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900 dark:text-white">{{ $user->name }}</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-300">{{ $user->userType->name ?? 'Professional' }}</p>
                        </div>
                        <div class="text-xs bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-2 py-1 rounded-full">
                            ⭐ FEATURED
                        </div>
                    </div>
                </div>

                <form action="{{ route('profile.feature.process') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-600 dark:text-gray-300">Featured Profile (30 days)</span>
                            <span class="font-semibold text-gray-900 dark:text-white">${{ number_format($featuredCost, 2) }}</span>
                        </div>
                        <hr class="border-gray-200 dark:border-gray-600 mb-2">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-gray-900 dark:text-white">Total</span>
                            <span class="text-xl font-bold text-green-600 dark:text-green-400">${{ number_format($featuredCost, 2) }}</span>
                        </div>
                    </div>

                    <div class="flex space-x-4">
                        <a href="{{ route('profile.edit') }}" class="flex-1 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-center px-6 py-3 rounded-lg font-medium transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-6 py-3 rounded-lg font-bold transition-all duration-200 shadow-lg hover:shadow-xl">
                            Feature My Profile
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>

</x-layouts.app>
