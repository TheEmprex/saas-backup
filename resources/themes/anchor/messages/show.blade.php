<x-layouts.app>
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('messages.web.index') }}" class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-all duration-200 bg-white dark:bg-slate-800/50 px-4 py-2 rounded-lg shadow-sm hover:shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Messages
            </a>
        </div>

        <div class="bg-white dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-lg flex flex-col" style="height: 80vh;" data-contact-id="{{ $contact->id }}">
            <!-- Chat Header -->
            <div class="p-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 rounded-t-2xl flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        @if($contact->avatar)
                            <div class="w-12 h-12 rounded-full overflow-hidden shadow-inner">
                                <img src="{{ Storage::url($contact->avatar) }}" 
                                     alt="{{ $contact->name }}" 
                                     class="w-full h-full object-cover">
                            </div>
                        @else
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-xl shadow-inner">
                                {{ substr($contact->name, 0, 1) }}
                            </div>
                        @endif
                        <div id="contact-online-indicator" class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-slate-400 rounded-full border-2 border-white dark:border-slate-800"></div>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ $contact->name }}</h3>
                        <p id="contact-status-text" class="text-sm text-slate-500 dark:text-slate-400">Offline</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <button onclick="startAudioCall()" class="p-2 text-slate-500 hover:text-green-600 dark:hover:text-green-400 rounded-full hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors" title="Start voice call">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                    </button>
                    <button onclick="startVideoCall()" class="p-2 text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 rounded-full hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors" title="Start video call">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Messages Area -->
            <div class="flex-1 overflow-y-auto p-6 bg-slate-100/50 dark:bg-slate-900" id="messages-container">
                @forelse($messages as $message)
                    <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }} mb-4" data-message-id="{{ $message->id }}">
                        @if($message->sender_id !== auth()->id())
                            <div class="flex-shrink-0 mr-3">
                                @if($message->sender->avatar)
                                    <div class="w-8 h-8 rounded-full overflow-hidden">
                                        <img src="{{ Storage::url($message->sender->avatar) }}" 
                                             alt="{{ $message->sender->name }}" 
                                             class="w-full h-full object-cover">
                                    </div>
                                @else
                                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                        {{ substr($message->sender->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                        @endif
                        <div class="max-w-md {{ $message->sender_id === auth()->id() ? 'mr-3' : '' }}">
                            <div class="px-4 py-3 rounded-2xl shadow-md {{ $message->sender_id === auth()->id() ? 'bg-gradient-to-r from-blue-500 to-purple-600 text-white' : 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white' }}">
                                @if($message->jobPost)
                                    <div class="mb-3 p-3 rounded-lg {{ $message->sender_id === auth()->id() ? 'bg-white/20' : 'bg-blue-50 dark:bg-blue-900/30' }}">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <svg class="w-4 h-4 {{ $message->sender_id === auth()->id() ? 'text-white' : 'text-blue-600 dark:text-blue-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6.294a2 2 0 01-.786 1.588C16.416 17.882 12.364 19 8 19c-4.364 0-8.416-1.118-9.214-3.118A2 2 0 01-2 14.294V8a2 2 0 012-2h4"></path>
                                            </svg>
                                            <span class="text-xs font-semibold {{ $message->sender_id === auth()->id() ? 'text-white/90' : 'text-blue-600 dark:text-blue-400' }}">Job Reference</span>
                                        </div>
                                        <h4 class="text-sm font-medium {{ $message->sender_id === auth()->id() ? 'text-white' : 'text-slate-900 dark:text-white' }} mb-1">{{ $message->jobPost->title }}</h4>
                                        <div class="flex items-center space-x-2 text-xs {{ $message->sender_id === auth()->id() ? 'text-white/80' : 'text-slate-600 dark:text-slate-400' }}">
                                            <span>
                                                @if($message->jobPost->rate_type === 'hourly')
                                                    ${{ number_format($message->jobPost->hourly_rate, 2) }}/hr
                                                @elseif($message->jobPost->rate_type === 'fixed')
                                                    ${{ number_format($message->jobPost->fixed_rate, 2) }}
                                                @else
                                                    {{ $message->jobPost->commission_percentage }}%
                                                @endif
                                            </span>
                                            <span>•</span>
                                            <span>{{ ucfirst($message->jobPost->market) }}</span>
                                        </div>
                                    </div>
                                @endif
                                @if($message->message_content)
                                    <p class="text-base font-medium {{ $message->sender_id === auth()->id() ? 'text-white' : 'text-slate-900 dark:text-slate-100' }}">{{ $message->message_content }}</p>
                                @endif
                                
                                @if($message->attachments && count($message->attachments) > 0)
                                    <div class="mt-2">
                                        @foreach($message->attachments as $attachment)
                                            @if(str_starts_with($attachment['type'] ?? '', 'image/'))
                                                <a href="{{ asset('storage/' . $attachment['path']) }}" target="_blank">
                                                    <img src="{{ asset('storage/' . $attachment['path']) }}" class="mt-2 rounded-lg max-w-xs cursor-pointer" alt="{{ $attachment['name'] }}" onerror="this.style.display='none'">
                                                </a>
                                            @else
                                                <a href="{{ asset('storage/' . $attachment['path']) }}" target="_blank" class="mt-2 flex items-center bg-slate-200 dark:bg-slate-600/50 p-2 rounded-lg hover:bg-slate-300 dark:hover:bg-slate-600">
                                                    <span class="text-2xl mr-2">📎</span>
                                                    <div>
                                                        <p class="font-semibold text-sm text-slate-900 dark:text-white">{{ $attachment['name'] }}</p>
                                                        <p class="text-xs text-slate-600 dark:text-slate-400">{{ number_format($attachment['size'] / 1024, 1) }} KB</p>
                                                    </div>
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                                
                        <p class="text-xs mt-2 {{ $message->sender_id === auth()->id() ? 'text-blue-100/80' : 'text-slate-600 dark:text-slate-400' }} text-right">
                                    {{ $message->created_at->format('H:i') }}
                                </p>
                            </div>
                        </div>
                        @if($message->sender_id === auth()->id())
                            <div class="flex-shrink-0 ml-3">
                                @if(auth()->user()->avatar)
                                    <div class="w-8 h-8 rounded-full overflow-hidden">
                                        <img src="{{ Storage::url(auth()->user()->avatar) }}" 
                                             alt="{{ auth()->user()->name }}" 
                                             class="w-full h-full object-cover">
                                    </div>
                                @else
                                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-12">
                        <svg class="mx-auto h-16 w-16 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">No messages yet</h3>
                        <p class="mt-1 text-gray-500 dark:text-gray-400">Start the conversation by sending a message!</p>
                    </div>
                @endforelse
            </div>

            <!-- Message Input -->
            <div class="p-4 border-t border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 rounded-b-2xl">
                <div id="file-preview" class="mb-3 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2"></div>
                <form id="message-form" action="{{ route('messages.web.store', $contact->id) }}" method="POST" enctype="multipart/form-data" class="flex items-center space-x-3">
                    @csrf
                    <label for="file-input" class="p-2 text-slate-500 hover:text-purple-600 rounded-full hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors cursor-pointer" title="Attach files">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                        </svg>
                        <input type="file" id="file-input" name="attachments[]" multiple accept="image/*,application/pdf,.doc,.docx" class="hidden">
                    </label>
                    <div class="flex-1 relative">
                        <textarea id="message-textarea" name="content" rows="1" class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 resize-none placeholder-slate-400 dark:placeholder-slate-500" placeholder="Type a message..."></textarea>
                    </div>
                    <button id="send-button" type="submit" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white p-3 rounded-full hover:from-blue-700 hover:to-purple-700 transition-all duration-300 shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Call Modal -->
<div id="call-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-75">
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 max-w-2xl w-full mx-4 shadow-2xl">
        <!-- Video Call Container -->
        <div id="video-container" class="hidden">
            <div class="text-center mb-4">
                <h3 class="text-xl font-semibold text-slate-900 dark:text-white">Video Call with {{ $contact->name }}</h3>
            </div>
            <div class="relative">
                <video id="remote-video" autoplay class="w-full h-96 bg-slate-900 rounded-lg object-cover"></video>
                <video id="local-video" autoplay muted class="absolute bottom-4 right-4 w-32 h-24 bg-slate-700 rounded-lg object-cover border-2 border-white"></video>
            </div>
        </div>
        
        <!-- Audio Call Container -->
        <div id="audio-container" class="hidden text-center">
            <div class="mb-8">
                <h3 class="text-xl font-semibold text-slate-900 dark:text-white mb-2">Audio Call with {{ $contact->name }}</h3>
                @if($contact->avatar)
                    <div class="w-24 h-24 rounded-full overflow-hidden mx-auto mb-4">
                        <img src="{{ Storage::url($contact->avatar) }}" 
                             alt="{{ $contact->name }}" 
                             class="w-full h-full object-cover">
                    </div>
                @else
                    <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-3xl mx-auto mb-4">
                        {{ substr($contact->name, 0, 1) }}
                    </div>
                @endif
                <p class="text-slate-600 dark:text-slate-400">Call in progress...</p>
            </div>
        </div>
        
        <!-- Call Controls -->
        <div class="flex justify-center space-x-4 mt-6">
            <button id="mute-button" onclick="toggleAudio()" class="bg-slate-500 hover:bg-slate-600 text-white p-3 rounded-full transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                </svg>
            </button>
            
            <button id="video-button" onclick="toggleVideo()" class="bg-slate-500 hover:bg-slate-600 text-white p-3 rounded-full transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
            </button>
            
            <button onclick="endCall()" class="bg-red-500 hover:bg-red-600 text-white p-3 rounded-full transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8l2-2m0 0l2-2m-2 2l-2-2m2 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h12a2 2 0 012 2z"></path>
                </svg>
            </button>
        </div>
    </div>
</div>

<script>
// Real-time messaging functionality
let messagesContainer = document.getElementById('messages-container');
let messageForm = document.getElementById('message-form');
let messageTextarea = document.getElementById('message-textarea');
let fileInput = document.getElementById('file-input');
let filePreview = document.getElementById('file-preview');
let sendButton = document.getElementById('send-button');
let selectedFiles = [];

// Auto-resize textarea
messageTextarea.addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = this.scrollHeight + 'px';
});

// Handle Enter key submission (Enter to send, Shift+Enter for new line)
messageTextarea.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        if (messageTextarea.value.trim() || selectedFiles.length > 0) {
            messageForm.submit();
        }
    }
});

// Handle file selection
fileInput.addEventListener('change', function(e) {
    const files = Array.from(e.target.files);
    selectedFiles = [...selectedFiles, ...files];
    updateFilePreview();
});

// Update file preview
function updateFilePreview() {
    filePreview.innerHTML = '';
    selectedFiles.forEach((file, index) => {
        const previewItem = document.createElement('div');
        
        if (file.type.startsWith('image/')) {
            // Image preview with larger display
            previewItem.className = 'relative bg-slate-100 dark:bg-slate-700 rounded-lg p-2';
            
            const imgContainer = document.createElement('div');
            imgContainer.className = 'relative';
            
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.className = 'w-full h-32 rounded object-cover';
            imgContainer.appendChild(img);
            
            const removeButton = document.createElement('button');
            removeButton.className = 'absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors';
            removeButton.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
            removeButton.onclick = () => removeFile(index);
            imgContainer.appendChild(removeButton);
            
            const fileName = document.createElement('p');
            fileName.className = 'text-xs font-medium text-slate-900 dark:text-white mt-1 truncate';
            fileName.textContent = file.name;
            
            previewItem.appendChild(imgContainer);
            previewItem.appendChild(fileName);
        } else {
            // File preview with icon
            previewItem.className = 'relative bg-slate-100 dark:bg-slate-700 rounded-lg p-3 flex items-center space-x-2';
            
            const icon = document.createElement('div');
            icon.className = 'w-10 h-10 bg-slate-300 dark:bg-slate-600 rounded flex items-center justify-center text-slate-600 dark:text-slate-300';
            icon.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>';
            previewItem.appendChild(icon);
            
            const info = document.createElement('div');
            info.className = 'flex-1 min-w-0';
            info.innerHTML = `
                <p class="text-sm font-medium text-slate-900 dark:text-white truncate">${file.name}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">${(file.size / 1024).toFixed(1)} KB</p>
            `;
            previewItem.appendChild(info);
            
            const removeButton = document.createElement('button');
            removeButton.className = 'text-red-500 hover:text-red-700 p-1';
            removeButton.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
            removeButton.onclick = () => removeFile(index);
            previewItem.appendChild(removeButton);
        }
        
        filePreview.appendChild(previewItem);
    });
}

// Remove file from selection
function removeFile(index) {
    selectedFiles.splice(index, 1);
    updateFilePreview();
}

// Send message
messageForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const content = messageTextarea.value.trim();
    
    if (!content && selectedFiles.length === 0) return;
    
    const formData = new FormData();
    formData.append('content', content);
    formData.append('_token', document.querySelector('[name="_token"]').value);
    
    selectedFiles.forEach((file, index) => {
        formData.append('attachments[]', file);
    });
    
    sendButton.disabled = true;
    sendButton.innerHTML = '<svg class="w-6 h-6 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>';
    
    fetch('{{ route('messages.web.store', $contact->id) }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            messageTextarea.value = '';
            selectedFiles = [];
            updateFilePreview();
            addMessageToChat(data.message);
            scrollToBottom();
        } else {
            alert('Error sending message: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error sending message');
    })
    .finally(() => {
        sendButton.disabled = false;
        sendButton.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>';
    });
});

// Add message to chat
function addMessageToChat(message) {
    const messageElement = document.createElement('div');
    const isOwn = message.sender_id === {{ auth()->id() }};
    
    messageElement.className = `flex ${isOwn ? 'justify-end' : 'justify-start'} mb-4`;
    messageElement.setAttribute('data-message-id', message.id);
    messageElement.innerHTML = `
        <div class="max-w-md">
            <div class="px-4 py-3 rounded-2xl shadow-md ${isOwn ? 'bg-gradient-to-r from-blue-500 to-purple-600 text-white' : 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white'}">
                ${message.message_content ? `<p class="text-base font-medium ${isOwn ? 'text-white' : 'text-slate-900 dark:text-slate-100'}">${message.message_content}</p>` : ''}
                
                ${message.attachments && message.attachments.length > 0 ? `
                    <div class="mt-2">
                        ${message.attachments.map(attachment => {
                            if (attachment.type && attachment.type.startsWith('image/')) {
                                return `<a href="{{ asset('storage') }}/${attachment.path}" target="_blank">
                                    <img src="{{ asset('storage') }}/${attachment.path}" class="mt-2 rounded-lg max-w-xs cursor-pointer" alt="${attachment.name}" onerror="this.style.display='none'">
                                </a>`;
                            } else {
                                return `<a href="{{ asset('storage') }}/${attachment.path}" target="_blank" class="mt-2 flex items-center bg-slate-200 dark:bg-slate-600/50 p-2 rounded-lg hover:bg-slate-300 dark:hover:bg-slate-600">
                                    <span class="text-2xl mr-2">📎</span>
                                    <div>
                                        <p class="font-semibold text-sm text-slate-900 dark:text-slate-100">${attachment.name}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">${(attachment.size / 1024).toFixed(1)} KB</p>
                                    </div>
                                </a>`;
                            }
                        }).join('')}
                    </div>
                ` : ''}
                
                <p class="text-xs mt-2 ${isOwn ? 'text-blue-100/80' : 'text-slate-400'} text-right">
                    ${new Date(message.created_at).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}
                </p>
            </div>
        </div>
    `;
    
    messagesContainer.appendChild(messageElement);
}

// Scroll to bottom
function scrollToBottom() {
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

// Fetch new messages
function fetchNewMessages() {
    fetch(`{{ route('messages.web.show', $contact->id) }}?ajax=1&last_id=${getLastMessageId()}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.messages && data.messages.length > 0) {
            data.messages.forEach(message => addMessageToChat(message));
            scrollToBottom();
        }
    })
    .catch(error => console.error('Error fetching messages:', error));
}

// Get last message ID
function getLastMessageId() {
    const messages = messagesContainer.querySelectorAll('[data-message-id]');
    return messages.length > 0 ? messages[messages.length - 1].dataset.messageId : 0;
}

// Update online status
function updateOnlineStatus() {
    fetch(`{{ route('users.status', $contact->id) }}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        const indicator = document.getElementById('contact-online-indicator');
        const statusText = document.getElementById('contact-status-text');
        
        if (data.online) {
            indicator.className = 'absolute bottom-0 right-0 w-3.5 h-3.5 bg-green-400 rounded-full border-2 border-white dark:border-slate-800';
            statusText.textContent = 'Online';
        } else {
            indicator.className = 'absolute bottom-0 right-0 w-3.5 h-3.5 bg-slate-400 rounded-full border-2 border-white dark:border-slate-800';
            statusText.textContent = 'Offline';
        }
    })
    .catch(error => console.error('Error updating status:', error));
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    scrollToBottom();
    
    // Set up intervals for real-time updates
    setInterval(fetchNewMessages, 5000); // Fetch new messages every 5 seconds
    setInterval(updateOnlineStatus, 15000); // Update online status every 15 seconds
    
    // Initial status check
    updateOnlineStatus();
});
</script>

@vite(['resources/themes/anchor/assets/js/webrtc.js'])
</x-layouts.app>
