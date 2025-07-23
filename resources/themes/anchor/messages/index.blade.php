<x-layouts.app>

<div class="min-h-screen bg-gray-50 dark:bg-zinc-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-10">
            <div class="text-center">
                <h1 class="text-5xl font-black text-gray-900 dark:text-white mb-4">
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">Messages</span>
                </h1>
                <p class="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">Manage your conversations and stay connected with the OnlyFans ecosystem community</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Conversations List -->
            <div class="lg:col-span-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Conversations</h2>
                        <div class="flex items-center space-x-2">
                            <button class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700 max-h-96 overflow-y-auto">
                    @forelse($conversations as $conversation)
                    <a href="{{ route('messages.web.show', $conversation->id) }}" 
                       class="block p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <div class="flex items-center space-x-3">
                            <div class="relative">
                                @if($conversation->otherParticipant->avatar)
                                    <div class="w-10 h-10 rounded-full overflow-hidden">
                                        <img src="{{ Storage::url($conversation->otherParticipant->avatar) }}" 
                                             alt="{{ $conversation->otherParticipant->name }}" 
                                             class="w-full h-full object-cover">
                                    </div>
                                @else
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                                        {{ substr($conversation->otherParticipant->name ?? 'N/A', 0, 1) }}
                                    </div>
                                @endif
                                @if($conversation->unread_count > 0)
                                    <div class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 rounded-full flex items-center justify-center">
                                        <span class="text-xs text-white font-medium">{{ $conversation->unread_count > 9 ? '9+' : $conversation->unread_count }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ $conversation->otherParticipant->name ?? 'Unknown User' }}
                                    </p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">
                                        {{ \Carbon\Carbon::parse($conversation->updated_at)->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="flex items-center justify-between">
                                    <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                        {{ $conversation->otherParticipant->userType->display_name ?? 'User' }}
                                    </p>
                                    @if($conversation->unread_count > 0)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ $conversation->unread_count }} new
                                        </span>
                                    @endif
                                </div>
                                @if($conversation->latest_message)
                                    <p class="text-sm text-gray-500 dark:text-gray-400 truncate mt-1">
                                        {{ Str::limit($conversation->latest_message->message_content, 50) }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </a>
                    @empty
                    <div class="p-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No conversations yet</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Start messaging agencies or chatters to build your network.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Welcome Message -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm">
                <div class="p-8 text-center">
                    <div class="mx-auto w-24 h-24 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Welcome to Messages</h3>
                    <p class="text-gray-600 dark:text-gray-300 mb-8 max-w-md mx-auto">Select a conversation from the left to start messaging, or browse jobs to connect with new people in the OnlyFans management community.</p>
                    <div class="space-y-3">
                        <a href="{{ route('marketplace.jobs') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 112 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 112-2z"></path>
                            </svg>
                            Browse Jobs
                        </a>
                        <br>
                        <a href="{{ route('marketplace.profiles') }}" class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-base font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                            Browse Profiles
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Auto-refresh conversations every 30 seconds -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh conversations every 30 seconds
    setInterval(function() {
        fetch('{{ route("messages.web.index") }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(response => {
            if (response.ok) {
                // Optionally update the conversation list without full page reload
                console.log('Conversations refreshed');
            }
        }).catch(error => {
            console.error('Error refreshing conversations:', error);
        });
    }, 30000);
});
</script>

</x-layouts.app>
