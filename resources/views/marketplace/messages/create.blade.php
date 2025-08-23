<x-layouts.marketing
    :seo="[
        'title'         => 'Send Message - OnlyFans Management Marketplace',
        'description'   => 'Send a message to connect with professionals',
        'image'         => url('/og_image.png'),
        'type'          => 'website'
    ]"
>

<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
    <!-- Header Section -->
    <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('messages.index') }}" class="flex items-center text-gray-600 hover:text-blue-600 transition-colors duration-200 group">
                        <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Messages
                    </a>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Send New Message</h1>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    <h2 class="text-lg font-semibold text-gray-900">New Message</h2>
                </div>
            </div>

            <form action="{{ route('messages.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                
                @if(isset($job) && $job)
                    <input type="hidden" name="job_post_id" value="{{ $job->id }}">
                @endif
                
                <!-- Recipient Section -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">To</label>
                    @if(isset($user))
                        <input type="hidden" name="conversation_id" value="{{ $user->id }}">
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-lg mr-4">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-600">{{ $user->userType?->display_name ?? 'User' }}</div>
                                </div>
                            </div>
                        </div>
                    @else
                        <select name="conversation_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="">Select a user...</option>
                            @foreach($users as $availableUser)
                                <option value="{{ $availableUser->id }}">
                                    {{ $availableUser->name }} ({{ $availableUser->userType?->display_name ?? 'User' }})
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>
                
                @if(isset($job) && $job)
                    <!-- Job Context -->
                    <div class="mb-6">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 mr-4">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6.5"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-blue-900 mb-1">About this job</h4>
                                    <h5 class="font-medium text-gray-900 mb-2">{{ $job->title }}</h5>
                                    <div class="flex flex-wrap gap-3 text-sm text-gray-600">
                                        <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 rounded-full">
                                            @if($job->rate_type === 'hourly')
                                                ${{ number_format($job->hourly_rate, 2) }}/hour
                                            @elseif($job->rate_type === 'fixed')
                                                ${{ number_format($job->fixed_rate, 2) }} fixed
                                            @else
                                                {{ $job->commission_percentage }}% commission
                                            @endif
                                        </span>
                                        <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 rounded-full">
                                            {{ ucfirst($job->market) }}
                                        </span>
                                        <span class="inline-flex items-center px-2 py-1 bg-purple-100 text-purple-800 rounded-full">
                                            {{ $job->expected_hours_per_week ?? $job->hours_per_week }}h/week
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                
                <!-- Message Content -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Message</label>
                    <textarea name="content" rows="6" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Type your message..." required></textarea>
                </div>
                
                <!-- Attachment -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Attachment (Optional)</label>
                    <div class="relative">
                        <input type="file" name="attachment" id="attachment" class="hidden" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif">
                        <label for="attachment" class="flex items-center justify-center w-full px-4 py-3 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer hover:border-gray-400 transition-colors">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                            </svg>
                            <span class="text-gray-500" id="attachment-label">Choose a file or drag it here</span>
                        </label>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Max file size: 10MB. Supported formats: PDF, DOC, DOCX, JPG, JPEG, PNG, GIF</p>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    <a href="{{ route('messages.index') }}" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        Send Message
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// File attachment handling
document.getElementById('attachment').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const label = document.getElementById('attachment-label');
    
    if (file) {
        label.textContent = file.name;
        label.parentElement.classList.add('border-blue-400', 'bg-blue-50');
        label.classList.add('text-blue-600');
    } else {
        label.textContent = 'Choose a file or drag it here';
        label.parentElement.classList.remove('border-blue-400', 'bg-blue-50');
        label.classList.remove('text-blue-600');
    }
});
</script>

</x-layouts.marketing>
