<x-layouts.app>

<x-app.container>
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">Messages</h1>
        <p class="text-gray-600 dark:text-gray-400">Stay connected with your network</p>
    </div>

    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg overflow-hidden">
        <div class="flex h-96">
            <!-- Conversations List -->
            <div class="w-1/3 border-r border-gray-200 dark:border-zinc-700">
                <div class="p-4 border-b border-gray-200 dark:border-zinc-700">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Conversations</h3>
                        <a href="{{ route('marketplace.messages.create') }}" class="bg-blue-600 text-white px-3 py-1 rounded-md text-sm hover:bg-blue-700">
                            <i class="ti ti-plus"></i> New Message
                        </a>
                    </div>
                </div>
                
                <div class="conversation-list overflow-y-auto" style="height: 500px;">
                    @forelse($conversations as $conversation)
                    <div class="conversation-item p-3 border-b border-gray-200 dark:border-zinc-700 {{ $selectedConversation && $selectedConversation->id == $conversation->id ? 'bg-gray-50 dark:bg-zinc-700' : '' }} hover:bg-gray-50 dark:hover:bg-zinc-700 cursor-pointer" 
                         onclick="window.location.href='{{ route('marketplace.messages.show', $conversation) }}'">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gray-300 dark:bg-zinc-600 rounded-full mr-3 flex items-center justify-center">
                                <i class="ti ti-user text-gray-600 dark:text-gray-300"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-gray-100">{{ $conversation->otherParticipant->name }}</div>
                                        <div class="text-gray-500 dark:text-gray-400 text-sm">{{ $conversation->otherParticipant->userType->display_name }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-gray-500 dark:text-gray-400 text-sm">{{ $conversation->updated_at->diffForHumans() }}</div>
                                        @if($conversation->unread_count > 0)
                                            <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded-full">{{ $conversation->unread_count }}</span>
                                        @endif
                                    </div>
                                </div>
                                @if($conversation->latest_message)
                                    <div class="text-gray-500 dark:text-gray-400 text-sm mt-1">
                                        {{ Str::limit($conversation->latest_message->content, 50) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <i class="ti ti-message-off text-gray-400 dark:text-gray-500 text-3xl"></i>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">No conversations yet</p>
                        <a href="{{ route('marketplace.messages.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700 mt-3 inline-block">
                            Start a conversation
                        </a>
                    </div>
                    @endforelse
                </div>
                        </div>

                        <!-- Message Thread -->
                        <div class="col-lg-8">
                            @if($selectedConversation)
                                <div class="p-3 border-bottom">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-md me-3" style="background-image: url('/images/default-avatar.png')"></div>
                                        <div>
                                            <div class="font-weight-medium">{{ $selectedConversation->otherParticipant->name }}</div>
                                            <div class="text-muted small">{{ $selectedConversation->otherParticipant->userType->display_name }}</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="messages-container p-3" style="height: 400px; overflow-y: auto;">
                                    @forelse($messages as $message)
                                    <div class="message-item mb-3 {{ $message->sender_id == auth()->id() ? 'text-end' : 'text-start' }}">
                                        <div class="d-inline-block">
                                            <div class="message-bubble p-2 rounded {{ $message->sender_id == auth()->id() ? 'bg-primary text-white' : 'bg-light' }}" 
                                                 style="max-width: 300px;">
                                                <div class="message-content">{{ $message->content }}</div>
                                                @if($message->attachment)
                                                    <div class="mt-2">
                                                        <a href="{{ asset('storage/' . $message->attachment) }}" target="_blank" class="btn btn-sm btn-outline-light">
                                                            <i class="ti ti-paperclip"></i> Attachment
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="text-muted small mt-1">
                                                {{ $message->created_at->format('M j, Y g:i A') }}
                                                @if($message->sender_id == auth()->id())
                                                    @if($message->read_at)
                                                        <i class="ti ti-checks text-success"></i>
                                                    @else
                                                        <i class="ti ti-check text-muted"></i>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="text-center py-5">
                                        <i class="ti ti-message text-muted" style="font-size: 2rem;"></i>
                                        <p class="text-muted mt-2">No messages yet</p>
                                    </div>
                                    @endforelse
                                </div>
                                
                                <div class="p-3 border-top">
                                    <form action="{{ route('marketplace.messages.store') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="conversation_id" value="{{ $selectedConversation->id }}">
                                        <div class="input-group">
                                            <input type="text" name="content" class="form-control" placeholder="Type your message..." required>
                                            <label class="input-group-text" for="attachment">
                                                <i class="ti ti-paperclip"></i>
                                            </label>
                                            <input type="file" name="attachment" id="attachment" class="d-none">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="ti ti-send"></i>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="ti ti-message-circle text-muted" style="font-size: 3rem;"></i>
                                    <h3 class="text-muted mt-3">Select a conversation</h3>
                                    <p class="text-muted">Choose a conversation from the list to start messaging</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-scroll to bottom of messages
    document.addEventListener('DOMContentLoaded', function() {
        const messagesContainer = document.querySelector('.messages-container');
        if (messagesContainer) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    });
    
    // Handle file attachment preview
    document.getElementById('attachment').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const label = document.querySelector('label[for="attachment"]');
            label.innerHTML = '<i class="ti ti-file"></i> ' + file.name;
        }
    });
</script>
@endpush
@endsection
