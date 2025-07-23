<x-layouts.marketing
    :seo="[
        'title'         => 'New Message - OnlyFans Management Marketplace',
        'description'   => 'Send a message to connect with agencies and chatters.',
        'image'         => url('/og_image.png'),
        'type'          => 'website'
    ]"
>

<div class="bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <a href="{{ route('messages.web.index') }}" class="text-blue-600 hover:text-blue-800">
                ← Back to Messages
            </a>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">New Message</h1>

            <!-- Recipient Info -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                        {{ substr($recipient->name, 0, 1) }}
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">{{ $recipient->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $recipient->userType->display_name ?? 'User' }}</p>
                    </div>
                </div>
            </div>

            @if(isset($job) && $job)
                <!-- Job Context -->
                <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6.294a2 2 0 01-.786 1.588C16.416 17.882 12.364 19 8 19c-4.364 0-8.416-1.118-9.214-3.118A2 2 0 01-2 14.294V8a2 2 0 012-2h4"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-semibold text-blue-900 mb-1">About this job</h3>
                            <h4 class="font-medium text-gray-900 mb-2">{{ $job->title }}</h4>
                            <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600">
                                <span class="inline-flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    @if($job->rate_type === 'hourly')
                                        ${{ number_format($job->hourly_rate, 2) }}/hour
                                    @elseif($job->rate_type === 'fixed')
                                        ${{ number_format($job->fixed_rate, 2) }} fixed
                                    @else
                                        {{ $job->commission_percentage }}% commission
                                    @endif
                                </span>
                                <span class="inline-flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $job->expected_hours_per_week ?? $job->hours_per_week }}h/week
                                </span>
                                <span class="inline-flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6.294a2 2 0 01-.786 1.588C16.416 17.882 12.364 19 8 19c-4.364 0-8.416-1.118-9.214-3.118A2 2 0 01-2 14.294V8a2 2 0 012-2h4"></path>
                                    </svg>
                                    {{ ucfirst($job->market) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Message Form -->
            <form action="{{ route('messages.web.store', $recipient->id) }}" method="POST" class="space-y-6">
                @csrf
                
                @if(isset($job) && $job)
                    <input type="hidden" name="job_post_id" value="{{ $job->id }}">
                @endif

                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                        Subject (Optional)
                    </label>
                    <input 
                        type="text" 
                        id="subject"
                        name="subject" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="@if(isset($job) && $job)Interested in: {{ $job->title }}@elsee.g., Interested in your job posting@endif"
                        value="{{ old('subject', isset($job) && $job ? 'Interested in: ' . $job->title : '') }}"
                    >
                    @error('subject')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                        Message *
                    </label>
                    <textarea 
                        id="content"
                        name="content" 
                        rows="8"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Write your message here..."
                        required
                    >{{ old('content') }}</textarea>
                    @error('content')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-800">
                                <strong>Tips for effective messaging:</strong>
                            </p>
                            <ul class="mt-2 text-sm text-blue-700 list-disc list-inside">
                                <li>Be professional and clear about your intentions</li>
                                <li>Include relevant details about your experience or requirements</li>
                                <li>Ask specific questions to get better responses</li>
                                <li>Be respectful of the recipient's time</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="{{ route('messages.web.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Send Message
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

</x-layouts.marketing>
