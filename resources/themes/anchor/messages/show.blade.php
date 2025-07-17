<x-layouts.app>

<div class="bg-white dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <a href="{{ route('messages.web.index') }}" class="inline-flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Messages
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg">
            <!-- Chat Header -->
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('profile.public', $contact->username) }}" class="hover:opacity-80 transition-opacity">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                            {{ substr($contact->name, 0, 1) }}
                        </div>
                    </a>
                    <div>
                        <a href="{{ route('profile.public', $contact->username) }}" class="hover:text-blue-600 transition-colors">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $contact->name }}</h3>
                        </a>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $contact->userType->display_name ?? 'User' }}</p>
                    </div>
                </div>
                    <div class="flex items-center space-x-2">
                        <button class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Messages Area -->
            <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50 dark:bg-gray-900" id="messages-container" style="height: 500px;">
                @forelse($messages as $message)
                    <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-xs lg:max-w-md">
                            <div class="flex items-end space-x-2 {{ $message->sender_id === auth()->id() ? 'flex-row-reverse space-x-reverse' : '' }}">
                                <a href="{{ route('profile.public', $message->sender->username) }}" class="hover:opacity-80 transition-opacity">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-bold
                                        {{ $message->sender_id === auth()->id() ? 'bg-gradient-to-br from-blue-500 to-purple-600' : 'bg-gradient-to-br from-gray-500 to-gray-600' }}">
                                        {{ substr($message->sender->name, 0, 1) }}
                                    </div>
                                </a>
                                <div class="px-4 py-2 rounded-lg shadow-sm {{ $message->sender_id === auth()->id() ? 'bg-gradient-to-r from-blue-500 to-purple-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 border border-gray-200 dark:border-gray-700' }}">
                                    <p class="text-sm font-medium">{{ $message->message_content }}</p>
                                    <p class="text-xs mt-1 {{ $message->sender_id === auth()->id() ? 'text-blue-100' : 'text-gray-500 dark:text-gray-400' }}">
                                        {{ $message->created_at->format('H:i') }}
                                    </p>
                                </div>
                            </div>
                        </div>
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
            <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                <form id="message-form" action="{{ route('messages.web.store', $contact->id) }}" method="POST" class="flex space-x-2">
                    @csrf
                    <div class="flex-1">
                        <textarea 
                            id="message-textarea"
                            name="content" 
                            rows="2" 
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 resize-none placeholder-gray-500 dark:placeholder-gray-400"
                            placeholder="Type your message..."
                            required
                        ></textarea>
                    </div>
                    <button id="send-button" type="submit" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-4 py-2 rounded-md hover:from-blue-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        <!-- Contact Info Sidebar -->
        <div class="mt-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-6 shadow-sm">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Contact Information</h3>
                <div class="space-y-4">
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('profile.public', $contact->username) }}" class="hover:opacity-80 transition-opacity">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                                {{ substr($contact->name, 0, 1) }}
                            </div>
                        </a>
                        <div>
                            <a href="{{ route('profile.public', $contact->username) }}" class="hover:text-blue-600 transition-colors">
                                <p class="font-medium text-gray-900 dark:text-white">{{ $contact->name }}</p>
                            </a>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $contact->userType->display_name ?? 'User' }}</p>
                        </div>
                    </div>
                
                @if($contact->userProfile)
                    @if($contact->userProfile->bio)
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bio</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $contact->userProfile->bio }}</p>
                        </div>
                    @endif
                    
                    @if($contact->userProfile->experience_years)
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Experience</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $contact->userProfile->experience_years }} years</p>
                        </div>
                    @endif
                    
                    @if($contact->userProfile->average_rating > 0)
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rating</p>
                            <div class="flex items-center">
                                <span class="text-yellow-400">⭐</span>
                                <span class="text-sm text-gray-600 dark:text-gray-400 ml-1">
                                    {{ number_format($contact->userProfile->average_rating, 1) }} 
                                    ({{ $contact->userProfile->total_ratings }} reviews)
                                </span>
                            </div>
                        </div>
                    @endif
                @endif
                
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Member Since</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $contact->created_at->format('F Y') }}</p>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex space-x-2 mt-4">
                    <a href="{{ route('profile.public', $contact->username) }}" class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors text-center text-sm font-medium">
                        View Profile
                    </a>
                    <button onclick="openContractModal()" class="flex-1 bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition-colors text-center text-sm font-medium">
                        Create Contract
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const messagesContainer = document.getElementById('messages-container');
    
    // Auto-scroll to bottom
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
    
    // Handle form submission
    const form = document.querySelector('form');
    const textarea = document.querySelector('textarea[name="content"]');
    
    form.addEventListener('submit', function(e) {
        if (textarea.value.trim() === '') {
            e.preventDefault();
            return;
        }
        
        // Auto-scroll after new message
        setTimeout(() => {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }, 100);
    });
    
    // Handle Enter key (Shift+Enter for new line)
    textarea.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            form.submit();
        }
    });
    // Sound notifications using Web Audio API
    const AudioContext = window.AudioContext || window.webkitAudioContext;
    const audioContext = new AudioContext();
    
    // Create send message sound (higher pitched beep)
    const playSendSound = () => {
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
        oscillator.type = 'sine';
        
        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
        
        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.5);
    };
    
    // Create receive message sound (lower pitched notification)
    const playReceiveSound = () => {
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        oscillator.frequency.setValueAtTime(400, audioContext.currentTime);
        oscillator.frequency.setValueAtTime(600, audioContext.currentTime + 0.1);
        oscillator.type = 'sine';
        
        gainNode.gain.setValueAtTime(0.2, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
        
        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.3);
    };

    // Override the original form submit handler for AJAX submission
    form.removeEventListener('submit', arguments.callee);
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (textarea.value.trim() === '') {
            return;
        }
        
        const sendButton = document.getElementById('send-button');
        const originalContent = sendButton.innerHTML;
        
        // Disable button and show loading state
        sendButton.disabled = true;
        sendButton.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
        
        // Prepare form data
        const formData = new FormData(form);
        
        // Send AJAX request
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Play send sound
                try {
                    playSendSound();
                } catch (error) {
                    console.log('Audio not supported');
                }
                
                // Clear textarea
                textarea.value = '';
                
                // Add message to UI immediately
                const messageHtml = `
                    <div class="flex justify-end">
                        <div class="max-w-xs lg:max-w-md">
                            <div class="flex items-end space-x-2 flex-row-reverse space-x-reverse">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-bold bg-gradient-to-br from-blue-500 to-purple-600">
                                    ${data.message.sender.name.charAt(0)}
                                </div>
                                <div class="px-4 py-2 rounded-lg shadow-sm bg-gradient-to-r from-blue-500 to-purple-600 text-white">
                                    <p class="text-sm">${data.message.message_content}</p>
                                    <p class="text-xs mt-1 text-blue-100">
                                        ${new Date().toLocaleTimeString('en-US', {hour: '2-digit', minute:'2-digit', hour12: false})}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
                
                // Update message count
                lastMessageCount++;
            } else {
                alert('Error sending message: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error sending message. Please try again.');
        })
        .finally(() => {
            // Re-enable button and restore original content
            sendButton.disabled = false;
            sendButton.innerHTML = originalContent;
        });
    });

    // Auto-refresh messages every 5 seconds and play receive sound for new messages
    let lastMessageCount = {{ $messages->count() }};
    
    setInterval(() => {
        fetch(window.location.href)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newMessagesContainer = doc.getElementById('messages-container');
                
                if (newMessagesContainer) {
                    const newMessageCount = newMessagesContainer.children.length;
                    
                    if (newMessageCount > lastMessageCount) {
                        // New message received, play sound
                        try {
                            playReceiveSound();
                        } catch (error) {
                            console.log('Audio not supported');
                        }
                        
                        // Update messages container
                        messagesContainer.innerHTML = newMessagesContainer.innerHTML;
                        messagesContainer.scrollTop = messagesContainer.scrollHeight;
                    }
                    
                    lastMessageCount = newMessageCount;
                }
            })
            .catch(error => console.log('Error refreshing messages:', error));
    }, 5000);

    textarea.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            form.submit();
        }
    });
});

// Contract modal functionality
function openContractModal() {
    const modal = document.getElementById('contract-modal');
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeContractModal() {
    const modal = document.getElementById('contract-modal');
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    const modal = document.getElementById('contract-modal');
    if (e.target === modal) {
        closeContractModal();
    }
});
</script>

<!-- Contract Modal -->
<div id="contract-modal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <div class="absolute top-0 right-0 pt-4 pr-4">
                <button type="button" onclick="closeContractModal()" class="bg-white dark:bg-gray-800 rounded-md text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                        Create Contract with {{ $contact->name }}
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Create a work contract to formalize your collaboration and track earnings.
                        </p>
                    </div>
                    
                    <form id="contract-form" class="mt-4 space-y-4">
                        @csrf
                        <input type="hidden" name="contractor_id" value="{{ $contact->id }}">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contract Type</label>
                            <select name="contract_type" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="hourly">Hourly Rate</option>
                                <option value="fixed">Fixed Price</option>
                                <option value="commission">Commission Based</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Rate/Amount</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">$</span>
                                </div>
                                <input type="number" name="rate" step="0.01" class="pl-7 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="0.00">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                            <textarea name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Describe the work scope and expectations..."></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date</label>
                            <input type="date" name="start_date" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="createContract()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Create Contract
                </button>
                <button type="button" onclick="closeContractModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function createContract() {
    const form = document.getElementById('contract-form');
    const formData = new FormData(form);
    
    // Show loading state
    const createBtn = event.target;
    createBtn.disabled = true;
    createBtn.textContent = 'Creating...';
    
    fetch('/contracts', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeContractModal();
            alert('Contract created successfully!');
            // Optionally redirect to contracts page
            window.location.href = '/contracts';
        } else {
            alert('Error creating contract: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error creating contract. Please try again.');
    })
    .finally(() => {
        createBtn.disabled = false;
        createBtn.textContent = 'Create Contract';
    });
}
</script>

</x-layouts.app>
