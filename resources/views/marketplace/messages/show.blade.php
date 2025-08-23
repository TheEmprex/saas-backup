@extends('layouts.app')

@section('content')
<div class="container py-6">
    <div class="flex flex-col bg-white rounded-lg shadow-sm">
        <!-- Header -->
        <div class="p-4 border-b">
            <div class="flex items-center space-x-4">
                <a href="{{ route('messages.web.index') }}" class="text-gray-600 hover:text-gray-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div class="flex items-center space-x-3">
                    <div class="relative">
                        @if($contact->profile_photo_url)
                            <img src="{{ $contact->profile_photo_url }}" alt="{{ $contact->name }}" class="w-10 h-10 rounded-full">
                        @else
                            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-500 text-lg font-medium">{{ substr($contact->name, 0, 1) }}</span>
                            </div>
                        @endif
                        <span id="online-status-{{ $contact->id }}" class="absolute bottom-0 right-0 w-3 h-3 rounded-full" data-user-id="{{ $contact->id }}"></span>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold">{{ $contact->name }}</h2>
                        <p id="last-seen-{{ $contact->id }}" class="text-sm text-gray-500"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages -->
        <div id="messages-container" class="flex-1 p-4 space-y-4 overflow-y-auto" style="height: calc(100vh - 300px);">
            @foreach($messages as $message)
                <div class="flex {{ $message->sender_id === Auth::id() ? 'justify-end' : 'justify-start' }} mb-4">
                    <div class="max-w-lg {{ $message->sender_id === Auth::id() ? 'bg-blue-500 text-white' : 'bg-gray-100' }} rounded-lg px-4 py-2 shadow">
                        @if($message->job_post_id)
                            <div class="mb-2 text-sm {{ $message->sender_id === Auth::id() ? 'text-blue-100' : 'text-gray-500' }}">
                                Re: <a href="{{ route('jobs.show', $message->job_post_id) }}" class="underline">{{ $message->jobPost->title }}</a>
                            </div>
                        @endif
                        
                        <p class="text-sm">{{ $message->message_content }}</p>
                        
                        @if($message->attachments)
                            <div class="mt-2 space-y-1">
                                @foreach($message->attachments as $attachment)
                                    <a href="{{ $attachment['url'] }}" target="_blank" class="flex items-center text-sm {{ $message->sender_id === Auth::id() ? 'text-blue-100' : 'text-blue-500' }} hover:underline">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                        </svg>
                                        {{ $attachment['name'] }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                        
                        <div class="mt-1 text-xs {{ $message->sender_id === Auth::id() ? 'text-blue-100' : 'text-gray-500' }}">
                            {{ $message->created_at->format('M j, Y g:i A') }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Message Input -->
        <div class="p-4 border-t">
            <form id="message-form" class="flex space-x-4" enctype="multipart/form-data">
                @csrf
                <div class="flex-1">
                    <textarea 
                        name="content" 
                        id="message-content"
                        rows="1"
                        class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Type your message..."
                    ></textarea>
                </div>
                <div class="flex items-center space-x-2">
                    <label class="cursor-pointer text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                        <input type="file" name="attachments[]" multiple class="hidden" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.zip,.txt,.mp3,.mp4,.wav">
                    </label>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Send
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messagesContainer = document.getElementById('messages-container');
    const messageForm = document.getElementById('message-form');
    const messageContent = document.getElementById('message-content');
    let lastMessageId = '{{ $messages->last()?->id }}';

    // Scroll to bottom on load
    messagesContainer.scrollTop = messagesContainer.scrollHeight;

    // Auto-resize textarea
    messageContent.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    // Handle form submission
    messageForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('_token', '{{ csrf_token() }}');
        
        try {
            const response = await fetch('{{ route("messages.web.store", $contact->id) }}', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Clear form
                messageForm.reset();
                messageContent.style.height = 'auto';
                
                // Append new message
                appendMessage(result.message);
                
                // Update last message ID
                lastMessageId = result.message_id;
                
                // Scroll to bottom
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            } else {
                alert('Failed to send message. Please try again.');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Failed to send message. Please try again.');
        }
    });

    // Poll for new messages
    setInterval(async function() {
        try {
            const response = await fetch(`{{ route("messages.web.show", $contact->id) }}?last_id=${lastMessageId}`, {
                headers: {
                    'Accept': 'application/json'
                }
            });
            
            const result = await response.json();
            
            if (result.messages && result.messages.length > 0) {
                result.messages.forEach(message => {
                    appendMessage(message);
                    lastMessageId = message.id;
                });
                
                // Scroll to bottom if user is already at bottom
                if (messagesContainer.scrollHeight - messagesContainer.scrollTop === messagesContainer.clientHeight) {
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                }
            }
        } catch (error) {
            console.error('Error polling messages:', error);
        }
    }, 5000);

    // Poll for user status
    setInterval(async function() {
        try {
            const response = await fetch('{{ route("messages.web.user.status", $contact->id) }}');
            const result = await response.json();
            
            const statusDot = document.getElementById(`online-status-${contact.id}`);
            const lastSeen = document.getElementById(`last-seen-${contact.id}`);
            
            if (result.online) {
                statusDot.classList.add('bg-green-500');
                statusDot.classList.remove('bg-gray-500');
                lastSeen.textContent = 'Online';
            } else {
                statusDot.classList.add('bg-gray-500');
                statusDot.classList.remove('bg-green-500');
                lastSeen.textContent = `Last seen ${result.last_seen}`;
            }
        } catch (error) {
            console.error('Error polling user status:', error);
        }
    }, 30000);

    function appendMessage(message) {
        const isOwnMessage = message.sender_id === {{ Auth::id() }};
        const messageHtml = `
            <div class="flex ${isOwnMessage ? 'justify-end' : 'justify-start'} mb-4">
                <div class="max-w-lg ${isOwnMessage ? 'bg-blue-500 text-white' : 'bg-gray-100'} rounded-lg px-4 py-2 shadow">
                    ${message.job_post_id ? `
                        <div class="mb-2 text-sm ${isOwnMessage ? 'text-blue-100' : 'text-gray-500'}">
                            Re: <a href="/jobs/${message.job_post_id}" class="underline">${message.job_post.title}</a>
                        </div>
                    ` : ''}
                    <p class="text-sm">${message.message_content}</p>
                    ${message.attachments ? `
                        <div class="mt-2 space-y-1">
                            ${message.attachments.map(attachment => `
                                <a href="${attachment.url}" target="_blank" class="flex items-center text-sm ${isOwnMessage ? 'text-blue-100' : 'text-blue-500'} hover:underline">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                    </svg>
                                    ${attachment.name}
                                </a>
                            `).join('')}
                        </div>
                    ` : ''}
                    <div class="mt-1 text-xs ${isOwnMessage ? 'text-blue-100' : 'text-gray-500'}">
                        ${new Date(message.created_at).toLocaleString()}
                    </div>
                </div>
            </div>
        `;
        
        messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
    }
});
</script>
@endpush
