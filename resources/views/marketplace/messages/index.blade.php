<x-layouts.marketing
    :seo="[
        'title'         => 'Messages - OnlyFans Ecosystem',
        'description'   => 'Manage your conversations with professionals and clients',
        'image'         => url('/og_image.png'),
        'type'          => 'website'
    ]"
>

<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
    <!-- Header Section -->
    <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
<div class="flex items-center justify-between text-left">
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">
                    My 
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">Messages</span>
                </h1>
                <a href="{{ route('marketplace.messages.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    New Message
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="flex" style="height: 600px;">
                <!-- Conversations List -->
                <div class="w-1/3 border-r border-gray-200 flex flex-col">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-4 py-3 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            Conversations
                        </h3>
                    </div>
                    
                    <div class="flex-1 overflow-y-auto">
                        @forelse($conversations as $conversation)
                        <div class="conversation-item p-4 border-b border-gray-100 {{ $selectedConversation && $selectedConversation->id == $conversation->id ? 'bg-blue-50 border-l-4 border-l-blue-500' : 'hover:bg-gray-50' }} cursor-pointer transition-colors" 
                             onclick="window.location.href='{{ route('marketplace.messages.show', $conversation->id) }}'">
                            <div class="flex items-start">
                                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-lg mr-3 flex-shrink-0">
                                    {{ substr($conversation->otherParticipant->name, 0, 1) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start mb-1">
                                        <div>
                                            <div class="font-semibold text-gray-900 truncate">{{ $conversation->otherParticipant->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $conversation->otherParticipant->userType->display_name }}</div>
                                        </div>
                                        <div class="text-right ml-2 flex-shrink-0">
                                            <div class="text-xs text-gray-400">{{ $conversation->updated_at->diffForHumans() }}</div>
                                            @if($conversation->unread_count > 0)
                                                <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded-full mt-1 inline-block">{{ $conversation->unread_count }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    @if($conversation->latest_message)
                                        <div class="text-sm text-gray-600 truncate">
                                            {{ Str::limit($conversation->latest_message->message_content, 50) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="flex flex-col items-center justify-center h-full py-12">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                            <p class="text-gray-500 text-center mb-4">No conversations yet</p>
                            <a href="{{ route('marketplace.messages.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition-colors">
                                Start a conversation
                            </a>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Message Thread -->
                <div class="flex-1 flex flex-col">
                    @if($selectedConversation)
                        <!-- Chat Header -->
                        <div class="bg-gradient-to-r from-gray-50 to-blue-50 px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold mr-4">
                                    {{ substr($selectedConversation->otherParticipant->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900">{{ $selectedConversation->otherParticipant->name }}</div>
                                    <div class="text-sm text-gray-600">{{ $selectedConversation->otherParticipant->userType->display_name }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Messages Container -->
                        <div class="flex-1 overflow-y-auto p-6 space-y-4" id="messages-container">
                            @forelse($messages as $message)
                            <div class="flex {{ $message->sender_id == auth()->id() ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-xs lg:max-w-md">
                                    <div class="{{ $message->sender_id == auth()->id() ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-900' }} rounded-lg px-4 py-2">
                                        <div class="break-words">{{ $message->message_content }}</div>
                                        @if($message->attachments && count($message->attachments) > 0)
                                            <div class="mt-2 pt-2 border-t {{ $message->sender_id == auth()->id() ? 'border-blue-500' : 'border-gray-200' }}">
                                                @foreach($message->attachments as $attachment)
                                                    <a href="{{ asset('storage/' . $attachment['path']) }}" target="_blank" class="flex items-center {{ $message->sender_id == auth()->id() ? 'text-blue-100 hover:text-white' : 'text-gray-600 hover:text-gray-900' }} text-sm">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                        </svg>
                                                        {{ $attachment['name'] }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1 {{ $message->sender_id == auth()->id() ? 'text-right' : 'text-left' }}">
                                        {{ $message->created_at->format('M j, Y g:i A') }}
                                        @if($message->sender_id == auth()->id())
                                            @if($message->read_at)
                                                <svg class="inline w-3 h-3 ml-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            @else
                                                <svg class="inline w-3 h-3 ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="flex flex-col items-center justify-center h-full">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-500 text-center">No messages yet</p>
                                <p class="text-gray-400 text-sm text-center mt-1">Start the conversation below</p>
                            </div>
                            @endforelse
                        </div>
                        
                        <!-- Message Input -->
                        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                            <form action="{{ route('marketplace.messages.store') }}" method="POST" enctype="multipart/form-data" class="flex items-end space-x-4">
                                @csrf
                                <input type="hidden" name="conversation_id" value="{{ $selectedConversation->id }}">
                                
                                <div class="flex-1">
                                    <textarea name="content" rows="1" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 resize-none" placeholder="Type your message..." required style="min-height: 38px; max-height: 120px;"></textarea>
                                </div>
                                
                                <div class="flex items-center space-x-2">
                                    <label for="attachment" class="p-2 text-gray-500 hover:text-gray-700 cursor-pointer rounded-lg hover:bg-gray-100 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                        </svg>
                                    </label>
                                    <input type="file" name="attachment" id="attachment" class="hidden" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif">
                                    
                                    <button type="submit" class="bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                        </svg>
                                    </button>
                                </div>
                            </form>
                            
                            <!-- File attachment preview -->
                            <div id="attachment-preview" class="mt-2 hidden">
                                <div class="flex items-center justify-between bg-white border border-gray-200 rounded-lg px-3 py-2">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                        </svg>
                                        <span class="text-sm text-gray-700" id="attachment-name"></span>
                                    </div>
                                    <button type="button" onclick="removeAttachment()" class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- No Conversation Selected -->
                        <div class="flex flex-col items-center justify-center h-full">
                            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Select a conversation</h3>
                            <p class="text-gray-600 text-center mb-6">Choose a conversation from the list to start messaging</p>
                            <a href="{{ route('marketplace.messages.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Start New Conversation
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-scroll to bottom of messages
document.addEventListener('DOMContentLoaded', function() {
    const messagesContainer = document.getElementById('messages-container');
    if (messagesContainer) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
});

// Handle textarea auto-resize
const textarea = document.querySelector('textarea[name="content"]');
if (textarea) {
    textarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    });
}

// Handle file attachment preview
document.getElementById('attachment').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('attachment-preview');
    const name = document.getElementById('attachment-name');
    
    if (file) {
        name.textContent = file.name;
        preview.classList.remove('hidden');
    } else {
        preview.classList.add('hidden');
    }
});

function removeAttachment() {
    document.getElementById('attachment').value = '';
    document.getElementById('attachment-preview').classList.add('hidden');
}

// Auto-refresh messages every 30 seconds if in a conversation
@if($selectedConversation)
setInterval(function() {
    // Only refresh if user hasn't typed anything recently
    const textarea = document.querySelector('textarea[name="content"]');
    if (!textarea.value.trim()) {
        window.location.reload();
    }
}, 30000);
@endif
</script>

</x-layouts.marketing>
