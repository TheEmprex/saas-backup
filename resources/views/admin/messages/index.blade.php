@extends('theme::app')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Message Management</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Monitor platform communications</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                    Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6">
            <div class="p-6">
                <form method="GET" class="flex flex-wrap items-end gap-4">
                    <div class="flex-1 min-w-0">
                        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                               class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                               placeholder="Search messages, sender, or recipient...">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                            Search
                        </button>
                        @if(request()->hasAny(['search']))
                            <a href="{{ route('admin.messages.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                                Clear
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Messages List -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Messages ({{ $messages->total() }})
                </h3>
            </div>
            
            @if($messages->count() > 0)
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($messages as $message)
                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start space-x-4 flex-1">
                                    <!-- Sender Avatar -->
                                    <img src="{{ $message->sender->avatar() }}" 
                                         alt="{{ $message->sender->name }}" 
                                         class="w-10 h-10 rounded-full flex-shrink-0">
                                    
                                    <div class="flex-1 min-w-0">
                                        <!-- Message Header -->
                                        <div class="flex items-center space-x-2 mb-2">
                                            <h4 class="font-medium text-gray-900 dark:text-white">
                                                {{ $message->sender->name }}
                                            </h4>
                                            <span class="text-gray-500 dark:text-gray-400">→</span>
                                            <span class="text-gray-700 dark:text-gray-300">
                                                {{ $message->recipient->name }}
                                            </span>
                                            <span class="text-sm text-gray-500 dark:text-gray-500">
                                                {{ $message->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                        
                                        <!-- User Types -->
                                        <div class="flex items-center space-x-4 mb-3 text-xs">
                                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                                From: {{ $message->sender->userType->name ?? 'No type' }}
                                            </span>
                                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded">
                                                To: {{ $message->recipient->userType->name ?? 'No type' }}
                                            </span>
                                        </div>
                                        
                                        <!-- Message Content -->
                                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                            <p class="text-gray-900 dark:text-white text-sm">
                                                {{ Str::limit($message->content, 300) }}
                                            </p>
                                        </div>
                                        
                                        @if(strlen($message->content) > 300)
                                            <button onclick="toggleFullMessage('message-{{ $message->id }}')" 
                                                    class="text-blue-600 hover:text-blue-800 text-sm mt-2">
                                                Show full message
                                            </button>
                                            <div id="message-{{ $message->id }}" class="hidden mt-3 bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                                <p class="text-gray-900 dark:text-white text-sm whitespace-pre-wrap">{{ $message->content }}</p>
                                            </div>
                                        @endif
                                        
                                        <!-- Message Metadata -->
                                        <div class="mt-3 flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-400">
                                            <span>Sender: {{ $message->sender->email }}</span>
                                            <span>Recipient: {{ $message->recipient->email }}</span>
                                            <span>ID: #{{ $message->id }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Actions -->
                                <div class="flex flex-col space-y-2 ml-4">
                                    <form method="POST" action="{{ route('admin.messages.delete', $message) }}" 
                                          onsubmit="return confirm('Are you sure you want to delete this message?')"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-800 text-sm font-medium">
                                            Delete
                                        </button>
                                    </form>
                                    
                                    <a href="{{ route('admin.users.show', $message->sender) }}" 
                                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        View Sender
                                    </a>
                                    
                                    <a href="{{ route('admin.users.show', $message->recipient) }}" 
                                       class="text-green-600 hover:text-green-800 text-sm font-medium">
                                        View Recipient
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                @if($messages->hasPages())
                    <div class="p-6 border-t border-gray-200 dark:border-gray-700">
                        {{ $messages->links() }}
                    </div>
                @endif
            @else
                <div class="p-12 text-center">
                    <svg class="w-12 h-12 text-gray-400 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No messages found</h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        @if(request()->hasAny(['search']))
                            No messages match your search criteria.
                        @else
                            No messages have been sent on the platform yet.
                        @endif
                    </p>
                    @if(request()->hasAny(['search']))
                        <a href="{{ route('admin.messages.index') }}" class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                            View All Messages
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function toggleFullMessage(messageId) {
    const element = document.getElementById(messageId);
    const button = element.previousElementSibling;
    
    if (element.classList.contains('hidden')) {
        element.classList.remove('hidden');
        button.textContent = 'Show less';
    } else {
        element.classList.add('hidden');
        button.textContent = 'Show full message';
    }
}
</script>
@endsection
