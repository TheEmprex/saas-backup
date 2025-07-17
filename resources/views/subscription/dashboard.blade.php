<x-layouts.marketing>
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900 mb-4">Subscription Dashboard</h1>
        </div>

        @if ($stats['has_subscription'])
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8">
                <div class="md:col-span-2">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="px-4 sm:px-6 py-4 bg-gradient-to-r from-blue-500 to-purple-600 text-white">
                            <h3 class="text-xl sm:text-2xl font-bold">Current Plan: {{ $stats['plan_name'] }}</h3>
                        </div>
                        <div class="px-4 sm:px-6 py-6">
                            <div>
                                <h5 class="text-lg font-semibold mb-4">Usage Statistics</h5>
                                @if ($user->isAgency())
                                    <p class="text-gray-700">Job Posts: {{ $stats['job_posts_used'] }} / {{ $stats['job_posts_limit'] ?? 'Unlimited' }}</p>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5 mb-4">
                                        @php $percentage = $stats['job_posts_limit'] ? ($stats['job_posts_used'] / $stats['job_posts_limit']) * 100 : 0; @endphp
                                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ min($percentage, 100) }}%"></div>
                                    </div>
                                @endif

                                @if ($user->isChatter())
                                    <p class="text-gray-700">Applications: {{ $stats['applications_used'] }} / {{ $stats['applications_limit'] ?? 'Unlimited' }}</p>
                                    @if ($stats['applications_limit'])
                                        <div class="w-full bg-gray-200 rounded-full h-2.5 mb-4">
                                            @php $percentage = ($stats['applications_used'] / $stats['applications_limit']) * 100; @endphp
                                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ min($percentage, 100) }}%"></div>
                                        </div>
                                    @endif
                                @endif

                                @if ($stats['expires_at'])
                                    <p class="text-gray-700">Expires: {{ \Carbon\Carbon::parse($stats['expires_at'])->format('M d, Y') }}</p>
                                @endif
                            </div>

                            <div class="mt-8">
                                <h5 class="text-lg font-semibold mb-4">Plan Features</h5>
                                <ul class="list-disc list-inside space-y-2">
                                    <li class="text-gray-700">Unlimited Chats: <span class="font-medium">{{ $stats['features']['unlimited_chats'] ? 'Yes' : 'No' }}</span></li>
                                    <li class="text-gray-700">Advanced Filters: <span class="font-medium">{{ $stats['features']['advanced_filters'] ? 'Yes' : 'No' }}</span></li>
                                    <li class="text-gray-700">Analytics: <span class="font-medium">{{ $stats['features']['analytics'] ? 'Yes' : 'No' }}</span></li>
                                    <li class="text-gray-700">Priority Listings: <span class="font-medium">{{ $stats['features']['priority_listings'] ? 'Yes' : 'No' }}</span></li>
                                    <li class="text-gray-700">Featured Status: <span class="font-medium">{{ $stats['features']['featured_status'] ? 'Yes' : 'No' }}</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h4 class="text-lg font-bold mb-4">Actions</h4>
                        <a href="{{ route('subscription.plans') }}" class="block w-full bg-blue-500 text-white text-center py-2 rounded-md hover:bg-blue-600 mb-2">
                            Change Plan
                        </a>
                        <a href="{{ route('subscription.plans') }}" class="block w-full bg-blue-100 text-blue-700 text-center py-2 rounded-md hover:bg-blue-200 mb-2">
                            View All Plans
                        </a>
                        <a href="{{ route('dashboard') }}" class="block w-full bg-green-500 text-white text-center py-2 rounded-md hover:bg-green-600 mb-2">
                            Go to Dashboard
                        </a>
                        @if ($stats['plan_name'] !== 'Agency Free' && $stats['plan_name'] !== 'Chatter Free')
                            <form method="POST" action="{{ route('subscription.cancel') }}">
                                @csrf
                                <button type="submit" class="w-full bg-red-500 text-white text-center py-2 rounded-md hover:bg-red-600"
                                        onclick="return confirm('Are you sure you want to cancel your subscription?')">
                                    Cancel Subscription
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4" role="alert">
                <p class="font-bold">No Active Subscription</p>
                <p>You don't have an active subscription. Please choose a plan to get started.</p>
                <a href="{{ route('subscription.plans') }}" class="block mt-3 text-blue-700 underline">View Plans</a>
            </div>
        @endif
    </div>
</x-layouts.marketing>
