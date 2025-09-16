<x-layouts.app>
<div class="h-screen bg-gray-50" x-data="messagingApp()" x-init="init()"
>
    <div class="bg-white border-b border-gray-200 px-6 py-4 shadow-sm">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl font-semibold text-gray-900">Messages</h1>
                    <p class="text-sm text-gray-500">Stay connected with your team</p>
                </div>
            </div>
            <button @click="showNewMessageModal = true" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 hover:scale-105 shadow-md hover:shadow-lg">
                <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                New Chat
            </button>
        </div>
    </div>

    <!-- Main Layout -->
    <div class="messaging-main">
        <!-- Conversations Sidebar -->
        <aside class="conversations-sidebar">
            <!-- Search -->
            <div class="sidebar-header">
                <div class="search-container">
                    <input type="text" 
                           x-model="searchQuery" 
                           class="search-input"
                           placeholder="Search conversations..."
                    >
                    <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
            
            <!-- Conversations List -->
            <div class="conversations-list" id="conversations-list">
                <template x-for="conversation in filteredConversations" :key="conversation.id">
                    <div class="conversation-item" 
                         :class="{ 'active': selectedConversation && selectedConversation.id === conversation.id, 'unread': conversation.unread_count > 0 }"
                         @click="selectConversation(conversation)">
                        
                        <div class="conversation-avatar">
                            <div class="avatar">
                                <div x-text="conversation.title.charAt(0).toUpperCase()"></div>
                            </div>
                            <div class="status-indicator" :class="conversation.is_online ? 'status-online' : 'status-offline'"></div>
                        </div>
                        
                        <div class="conversation-content">
                            <div class="conversation-header">
                                <h3 class="conversation-name" x-text="conversation.title"></h3>
                                <span class="conversation-time" x-text="formatMessageTime(conversation.last_activity)"></span>
                            </div>
                            <div class="conversation-preview">
                                <p class="conversation-text">
                                    <span x-show="conversation.last_message?.is_mine">You: </span>
                                    <span x-text="conversation.last_message?.content || 'No messages yet'"></span>
                                </p>
                                <span x-show="conversation.unread_count > 0" class="unread-badge" x-text="conversation.unread_count"></span>
                            </div>
                        </div>
                    </div>
                </template>
                
                <!-- Loading State -->
                <div x-show="loading && conversations.length === 0" class="p-4 text-center text-gray-500">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-4"></div>
                    <p>Loading conversations...</p>
                </div>
                
                <!-- Empty State -->
                <div x-show="!loading && filteredConversations.length === 0" class="p-4 text-center text-gray-500">
                    <p>No conversations yet</p>
                    <button @click="showNewMessageModal = true" class="mt-2 text-blue-600 hover:text-blue-700">
                        Start your first conversation
                    </button>
                </div>
            </div>
        </aside>
        
        <!-- Chat Area -->
        <div class="chat-area">
            <!-- Chat Header -->
            <div x-show="selectedConversation" class="chat-header">
                <div class="chat-user">
                    <div class="avatar">
                        <div x-text="selectedConversation?.title?.charAt(0).toUpperCase()"></div>
                    </div>
                    <div class="chat-user-info">
                        <h3 x-text="selectedConversation?.title"></h3>
                        <div class="chat-user-status">
                            <div class="status-indicator" :class="selectedConversation?.is_online ? 'status-online' : 'status-offline'"></div>
                            <p x-text="selectedConversation?.is_online ? 'Online' : 'Offline'"></p>
                            <span x-show="isTyping" class="typing-indicator">typing...</span>
                        </div>
                    </div>
                </div>
                
                <div class="chat-actions">
                    <button @click="loadMessages(selectedConversation.other_user_id)" class="chat-action" title="Refresh">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Messages Area -->
            <div x-show="selectedConversation" class="messages-container" x-ref="messagesContainer">
                <div class="messages-list">
                    <template x-for="message in messages" :key="message.id">
                        <div class="message" :class="message.is_mine ? 'sent' : 'received'">
                            <div x-show="!message.is_mine" class="message-avatar">
                                <div class="avatar">
                                    <div x-text="message.sender_name?.charAt(0).toUpperCase()"></div>
                                </div>
                            </div>
                            
                            <div class="message-bubble">
                                <p class="message-content" x-text="message.content"></p>
                                
                                <div class="message-meta">
                                    <span class="message-time" x-text="formatMessageTime(message.created_at)"></span>
                                    <div x-show="message.is_mine" class="message-status">
                                        <span x-show="message.is_read" class="text-blue-500">✓✓</span>
                                        <span x-show="!message.is_read" class="text-gray-400">✓</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    
                    <!-- Typing indicator -->
                    <div x-show="isTyping" class="typing-indicator">
                        <div class="typing-dots">
                            <span></span><span></span><span></span>
                        </div>
                        <span class="typing-text">Someone is typing...</span>
                    </div>
                </div>
            </div>
            
            <!-- Welcome State -->
            <div x-show="!selectedConversation" class="welcome-state">
                <div class="welcome-content">
                    <div class="welcome-icon">
                        💬
                    </div>
                    <h3 class="welcome-title">Welcome to Messages</h3>
                    <p class="welcome-text">Select a conversation to start messaging, or create a new conversation to connect with your colleagues.</p>
                </div>
            </div>
            
            <!-- Message Input -->
            <div x-show="selectedConversation" class="message-input-area">
                <form @submit.prevent="sendMessage()" class="input-container">
                    <div class="input-actions">
                        <button type="button" class="input-action" title="Attach file">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <textarea x-model="newMessage" 
                              @keydown="handleKeydown($event)"
                              @input="handleTyping()"
                              class="message-textarea" 
                              placeholder="Type a message..."
                              rows="1"
                    ></textarea>
                    
                    <button type="submit" 
                            :disabled="!newMessage.trim()"
                            class="send-button"
                            :class="{ 'animate-pulse': sending }"
                    >
                        <svg x-show="!sending" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        <svg x-show="sending" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
</x-layouts.app>

@push('scripts')
<script>
// Alpine.js messaging function
function messagingApp() {
    return {
        conversations: [],
        selectedConversation: null,
        messages: [],
        newMessage: '',
        searchQuery: '',
        loading: false,
        sending: false,
        isTyping: false,
        currentUserId: {{ auth()->user() ? auth()->user()->id : 'null' }},
        currentUserName: '{{ auth()->user() ? auth()->user()->name : '' }}',

        init() {
            if (window.messagingAppInstance) {
                // Use existing instance
                this.bindToExistingApp();
            } else {
                // Create new instance
                window.messagingAppInstance = new MessagingApp();
                this.bindToExistingApp();
            }
        },

        bindToExistingApp() {
            const app = window.messagingAppInstance;
            this.conversations = app.conversations;
            this.messages = app.messages;
            this.selectedConversation = app.selectedConversation;
            this.loading = app.loading;
        },

        async selectConversation(conversation) {
            if (window.messagingAppInstance) {
                await window.messagingAppInstance.selectConversation(conversation);
                this.selectedConversation = conversation;
                this.messages = window.messagingAppInstance.messages;
            }
        },

        async sendMessage() {
            if (window.messagingAppInstance && this.newMessage.trim()) {
                await window.messagingAppInstance.sendMessage(this.newMessage.trim());
                this.newMessage = '';
                this.messages = window.messagingAppInstance.messages;
            }
        },


        handleKeydown(event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                this.sendMessage();
            }
        },

        handleTyping() {
            if (window.messagingAppInstance) {
                window.messagingAppInstance.sendTypingIndicator();
            }
        },

        formatMessageTime(timestamp) {
            if (!timestamp) return '';
            const date = new Date(timestamp);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);
            
            if (diffMins < 1) return 'now';
            if (diffMins < 60) return `${diffMins}m`;
            if (diffHours < 24) return `${diffHours}h`;
            if (diffDays < 7) return `${diffDays}d`;
            
            return date.toLocaleDateString();
        },

        get filteredConversations() {
            if (!this.searchQuery) return this.conversations;
            const query = this.searchQuery.toLowerCase();
            return this.conversations.filter(conv => 
                conv.title && conv.title.toLowerCase().includes(query)
            );
        }
    }
}

// Real-time Messaging Application Class
class MessagingApp {
    constructor() {
        this.messages = [];
        this.conversations = [];
        this.selectedConversation = null;
        this.newMessage = '';
        this.loading = false;
        this.sending = false;
        this.currentUserId = {{ auth()->user()->id }};
        this.currentUserName = '{{ auth()->user()->name }}';
        this.attachments = [];
        this.typingUsers = new Set();
        this.onlineUsers = new Set();
        this.typingTimeout = null;
        this.searchQuery = '';
        this.echo = null;
        this.presenceChannel = null;
        
        this.init();
    }
    
    init() {
        this.initializeEcho();
        this.loadConversations();
        this.setupEventListeners();
        this.updateOnlineStatus(true);
    }
    
// Initialize Laravel Echo for real-time features
    initializeEcho() {
        if (window.Echo) {
            this.echo = window.Echo;

            // Listen for new messages and read receipts on user channel
            this.echo.private(`user.${this.currentUserId}`)
                .listen('.message.sent', (e) => {
                    this.handleNewMessage(e.message);
                })
                .listen('.message.read', (e) => {
                    this.handleMessageRead(e);
                });
        }
    }
    
    // Load conversations from API
    async loadConversations() {
        this.loading = true;
        try {
const response = await this.apiCall('/api/conversations');
            this.conversations = response.conversations || [];
            this.renderConversations();
        } catch (error) {
            console.error('Failed to load conversations:', error);
        } finally {
            this.loading = false;
        }
    }
    
    // Load messages for a conversation
    async loadMessages(conversationId) {
        if (!conversationId) return;
        
        this.loading = true;
        try {
const response = await this.apiCall(`/api/conversations/${conversationId}/messages`);
            this.messages = response.messages || [];
            this.renderMessages();
            this.scrollToBottom();
            
            // Mark conversation as read
            await this.markConversationAsRead(conversationId);
        } catch (error) {
            console.error('Failed to load messages:', error);
        } finally {
            this.loading = false;
        }
    }
    
    // Send a new message
    async sendMessage(content, recipientId = null) {
        if (!content.trim() && this.attachments.length === 0) return;
        if (this.sending) return;
        
        this.sending = true;
        const sendButton = document.getElementById('send-button');
        if (sendButton) {
            sendButton.disabled = true;
            sendButton.textContent = 'Sending...';
        }
        
        try {
            const formData = new FormData();
            
            if (this.selectedConversation) {
                formData.append('conversation_id', this.selectedConversation.id);
            } else if (recipientId) {
                formData.append('recipient_id', recipientId);
            }
            
            formData.append('content', content);
            formData.append('message_type', this.attachments.length > 0 ? 'file' : 'text');
            
            // Add attachments
            this.attachments.forEach((file, index) => {
                formData.append('file', file);
            });
            
const response = await this.apiCall(`/api/conversations/${this.selectedConversation ? this.selectedConversation.id : ''}/messages`, 'POST', formData, false);
            
            if (response.success) {
                // Clear input
                document.getElementById('message-input').value = '';
                this.attachments = [];
                this.newMessage = '';
                
                // Refresh messages and conversations
                if (this.selectedConversation) {
                    await this.loadMessages(this.selectedConversation.id);
                }
                await this.loadConversations();
            }
        } catch (error) {
            console.error('Failed to send message:', error);
            alert('Failed to send message. Please try again.');
        } finally {
            this.sending = false;
            if (sendButton) {
                sendButton.disabled = false;
                sendButton.textContent = 'Send';
            }
        }
    }
    
    // Handle new message from websocket
    handleNewMessage(message) {
        // Add to messages if it's for the current conversation
        if (this.selectedConversation && 
            message.conversation_id === this.selectedConversation.id) {
            this.messages.push(message);
            this.renderMessages();
            this.scrollToBottom();
        }
        
        // Refresh conversations to update last message
        this.loadConversations();
        
        // Show notification if not from current user
        if (message.sender_id !== this.currentUserId) {
            this.showNotification(`New message from ${message.sender_name}`, message.content);
        }
    }
    
    // Select a conversation
    async selectConversation(conversation) {
        this.selectedConversation = conversation;
        
        // Update UI
        document.getElementById('welcome-state').style.display = 'none';
        document.getElementById('chat-header').style.display = 'block';
        document.getElementById('messages-area').style.display = 'block';
        document.getElementById('message-input-area').style.display = 'block';
        
        // Update header
        document.getElementById('chat-user-name').textContent = conversation.title;
        document.getElementById('chat-user-status').textContent = 
            conversation.is_online ? 'Online' : 'Offline';
        
        // Load messages
        await this.loadMessages(conversation.id);
        
        // Update conversation selection
        document.querySelectorAll('.conversation-item').forEach(item => {
            item.classList.remove('active');
        });
        document.querySelector(`[data-conversation-id="${conversation.id}"]`)
            ?.classList.add('active');
        
        // Setup real-time for this conversation
        this.setupConversationRealTime(conversation.id);
    }
    
// Setup real-time for specific conversation
    setupConversationRealTime(conversationId) {
        if (this.echo) {
            // Listen for typing indicators and new messages
            this.echo.private(`conversation.${conversationId}`)
                .listenForWhisper('typing', (e) => {
                    this.handleTypingIndicator(e);
                })
                .listen('.message.sent', (e) => {
                    if (e.message?.conversation_id === conversationId) {
                        this.handleNewMessage(e.message);
                    }
                });
        }
    }
    
    // Render conversations list
    renderConversations() {
        const conversationsList = document.getElementById('conversations-list');
        if (!conversationsList) return;
        
        if (this.conversations.length === 0) {
            conversationsList.innerHTML = `
                <div class="p-4 text-center text-gray-500">
                    <p>No conversations yet</p>
                    <button onclick="window.messagingApp.showNewMessageModal()" 
                            class="mt-2 text-blue-600 hover:text-blue-700">
                        Start your first conversation
                    </button>
                </div>`;
            return;
        }
        
        const filteredConversations = this.conversations.filter(conv => 
            conv.title.toLowerCase().includes(this.searchQuery.toLowerCase())
        );
        
        conversationsList.innerHTML = filteredConversations.map(conversation => {
            const isOnline = this.onlineUsers.has(conversation.other_user_id);
            const lastMessage = conversation.last_message;
            
            return `
                <div class="conversation-item ${conversation.unread_count > 0 ? 'unread' : ''}" 
                     data-conversation-id="${conversation.id}"
                     onclick="window.messagingApp.selectConversation(${JSON.stringify(conversation).replace(/"/g, '&quot;')})">
                    <div class="conversation-avatar">
                        <div class="avatar">
                            <img src="${conversation.avatar}" alt="${conversation.title}" 
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
                            <div class="avatar-fallback" style="display: none;">
                                ${conversation.title.charAt(0).toUpperCase()}
                            </div>
                        </div>
                        <div class="status-indicator ${isOnline ? 'status-online' : 'status-offline'}"></div>
                    </div>
                    <div class="conversation-content">
                        <div class="conversation-header">
                            <h3 class="conversation-name">${conversation.title}</h3>
                            <span class="conversation-time">
                                ${this.formatMessageTime(conversation.last_activity)}
                            </span>
                        </div>
                        <div class="conversation-preview">
                            <p class="last-message">
                                ${lastMessage ? 
                                    (lastMessage.is_mine ? 'You: ' : '') + 
                                    this.truncateMessage(lastMessage.content, 50) 
                                    : 'No messages yet'}
                            </p>
                            ${conversation.unread_count > 0 ? 
                                `<span class="unread-badge">${conversation.unread_count}</span>` : ''}
                        </div>
                    </div>
                </div>`;
        }).join('');
    }
    
    // Render messages
    renderMessages() {
        const messagesArea = document.getElementById('messages-area');
        if (!messagesArea || !this.messages.length) return;
        
        messagesArea.innerHTML = this.messages.map(message => {
            const isMine = message.sender_id === this.currentUserId;
            const messageTime = this.formatMessageTime(message.created_at);
            
            return `
                <div class="message-group ${isMine ? 'message-mine' : 'message-theirs'}">
                    ${!isMine ? `
                        <div class="message-avatar">
                            <img src="${message.sender_avatar}" alt="${message.sender_name}" 
                                 class="w-8 h-8 rounded-full">
                        </div>` : ''}
                    <div class="message-content">
                        <div class="message-bubble">
                            ${message.file_url ? `
                                <div class="message-attachment">
                                    ${this.renderAttachment(message)}
                                </div>` : ''}
                            ${message.content ? `<div class="message-text">${message.content}</div>` : ''}
                        </div>
                        <div class="message-meta">
                            ${!isMine ? `<span class="sender-name">${message.sender_name}</span>` : ''}
                            <span class="message-time">${messageTime}</span>
                            ${message.is_read ? '<span class="read-indicator">✓✓</span>' : '<span class="sent-indicator">✓</span>'}
                        </div>
                    </div>
                </div>`;
        }).join('');
    }
    
    // Render attachment based on type
    renderAttachment(message) {
        if (!message.file_url) return '';
        
        const fileUrl = message.file_url;
        const fileName = message.file_name || 'File';
        const fileSize = message.formatted_file_size || '';
        
        if (message.message_type === 'image') {
            return `<img src="${fileUrl}" alt="${fileName}" class="max-w-sm rounded-lg cursor-pointer" 
                          onclick="window.open('${fileUrl}', '_blank')">`;
        } else if (message.message_type === 'video') {
            return `<video src="${fileUrl}" controls class="max-w-sm rounded-lg"></video>`;
        } else if (message.message_type === 'audio') {
            return `<audio src="${fileUrl}" controls class="w-full"></audio>`;
        } else {
            return `
                <div class="file-attachment">
                    <div class="file-icon">📎</div>
                    <div class="file-info">
                        <div class="file-name">${fileName}</div>
                        <div class="file-size">${fileSize}</div>
                    </div>
                    <a href="${fileUrl}" download="${fileName}" class="download-btn">↓</a>
                </div>`;
        }
    }
    
    // Show new message modal
    showNewMessageModal() {
        document.getElementById('new-message-modal').style.display = 'flex';
        document.getElementById('recipient-input').focus();
    }
    
    // Hide new message modal
    hideNewMessageModal() {
        document.getElementById('new-message-modal').style.display = 'none';
        document.getElementById('recipient-input').value = '';
        document.getElementById('message-content').value = '';
    }
    
    // Start new conversation
    async startNewConversation(event) {
        event.preventDefault();
        
        const recipientInput = document.getElementById('recipient-input').value.trim();
        const messageContent = document.getElementById('message-content').value.trim();
        
        if (!recipientInput || !messageContent) {
            alert('Please fill in all fields');
            return;
        }
        
        try {
            // Find user by username or email
            const userResponse = await this.apiCall('/messages/search-users?q=' + encodeURIComponent(recipientInput));
            
            if (!userResponse.users || userResponse.users.length === 0) {
                alert('User not found');
                return;
            }
            
            const recipient = userResponse.users[0];
            
            // Send message
            await this.sendMessage(messageContent, recipient.id);
            
            // Hide modal
            this.hideNewMessageModal();
            
            // Refresh conversations
            await this.loadConversations();
            
        } catch (error) {
            console.error('Failed to start conversation:', error);
            alert('Failed to start conversation. Please try again.');
        }
    }
    
    // Filter conversations
    filterConversations() {
        this.searchQuery = document.getElementById('search-conversations').value;
        this.renderConversations();
    }
    
    // Update online status
    async updateOnlineStatus(isOnline) {
        try {
            await this.apiCall('/messages/status', 'POST', {
                is_online: isOnline
            });
        } catch (error) {
            console.error('Failed to update online status:', error);
        }
    }
    
    // Send typing indicator
    sendTypingIndicator() {
        if (!this.selectedConversation) return;
        
        if (this.echo) {
            this.echo.private(`conversation.${this.selectedConversation.id}`)
                .whisper('typing', {
                    user_id: this.currentUserId,
                    user_name: this.currentUserName,
                    is_typing: true
                });
        }
        
        // Clear existing timeout
        if (this.typingTimeout) {
            clearTimeout(this.typingTimeout);
        }
        
        // Stop typing after 2 seconds of inactivity
        this.typingTimeout = setTimeout(() => {
            this.stopTypingIndicator();
        }, 2000);
    }
    
    // Stop typing indicator
    stopTypingIndicator() {
        if (!this.selectedConversation || !this.echo) return;
        
        this.echo.private(`conversation.${this.selectedConversation.id}`)
            .whisper('typing', {
                user_id: this.currentUserId,
                user_name: this.currentUserName,
                is_typing: false
            });
    }
    
    // Handle typing indicator from others
    handleTypingIndicator(data) {
        if (data.user_id === this.currentUserId) return;
        
        if (data.is_typing) {
            this.typingUsers.add(data.user_name);
        } else {
            this.typingUsers.delete(data.user_name);
        }
        
        this.updateTypingIndicator();
    }
    
    // Update typing indicator UI
    updateTypingIndicator() {
        const typingDiv = document.getElementById('typing-indicator');
        if (!typingDiv) {
            // Create typing indicator if it doesn't exist
            const messagesArea = document.getElementById('messages-area');
            if (messagesArea) {
                const typingHtml = '<div id="typing-indicator" class="typing-indicator"></div>';
                messagesArea.insertAdjacentHTML('beforeend', typingHtml);
            }
        }
        
        const indicator = document.getElementById('typing-indicator');
        if (indicator) {
            if (this.typingUsers.size > 0) {
                const users = Array.from(this.typingUsers);
                const text = users.length === 1 
                    ? `${users[0]} is typing...`
                    : `${users.slice(0, -1).join(', ')} and ${users[users.length - 1]} are typing...`;
                
                indicator.innerHTML = `
                    <div class="typing-dots">
                        <span></span><span></span><span></span>
                    </div>
                    <span class="typing-text">${text}</span>`;
                indicator.style.display = 'block';
            } else {
                indicator.style.display = 'none';
            }
        }
    }
    
    // Update UI with online status
    updateUIWithOnlineStatus() {
        document.querySelectorAll('.conversation-item').forEach(item => {
            const conversationId = parseInt(item.dataset.conversationId);
            const conversation = this.conversations.find(c => c.id === conversationId);
            if (conversation) {
                const statusIndicator = item.querySelector('.status-indicator');
                const isOnline = this.onlineUsers.has(conversation.other_user_id);
                statusIndicator.className = `status-indicator ${isOnline ? 'status-online' : 'status-offline'}`;
            }
        });
    }
    
    // Show desktop notification
    showNotification(title, body) {
        if (Notification.permission === 'granted') {
            new Notification(title, {
                body: body,
                icon: '/favicon.ico'
            });
        }
    }
    
    // Request notification permission
    requestNotificationPermission() {
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
    }
    
    // Setup event listeners
    setupEventListeners() {
        // Message input
        const messageInput = document.getElementById('message-input');
        if (messageInput) {
            messageInput.addEventListener('input', () => {
                this.sendTypingIndicator();
            });
            
            messageInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.handleSendMessage();
                }
            });
        }
        
        // Search input
        const searchInput = document.getElementById('search-conversations');
        if (searchInput) {
            searchInput.addEventListener('input', () => {
                this.filterConversations();
            });
        }
        
        // Update online status when page becomes visible/hidden
        document.addEventListener('visibilitychange', () => {
            this.updateOnlineStatus(!document.hidden);
        });
        
        // Update online status when window gains/loses focus
        window.addEventListener('focus', () => this.updateOnlineStatus(true));
        window.addEventListener('blur', () => this.updateOnlineStatus(false));
        
        // Request notification permission
        this.requestNotificationPermission();
    }
    
    // Handle send message button/enter
    handleSendMessage() {
        const messageInput = document.getElementById('message-input');
        const content = messageInput.value.trim();
        if (content) {
            this.sendMessage(content);
        }
    }
    
    // Mark conversation as read
    async markConversationAsRead(conversationId) {
        try {
            await this.apiCall(`/messages/conversations/${conversationId}/read`, 'POST');
        } catch (error) {
            console.error('Failed to mark as read:', error);
        }
    }
    
    // Scroll to bottom of messages
    scrollToBottom() {
        const messagesArea = document.getElementById('messages-area');
        if (messagesArea) {
            setTimeout(() => {
                messagesArea.scrollTop = messagesArea.scrollHeight;
            }, 100);
        }
    }
    
    // Format message time
    formatMessageTime(timestamp) {
        const date = new Date(timestamp);
        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);
        
        if (diffMins < 1) return 'now';
        if (diffMins < 60) return `${diffMins}m`;
        if (diffHours < 24) return `${diffHours}h`;
        if (diffDays < 7) return `${diffDays}d`;
        
        return date.toLocaleDateString();
    }
    
    // Truncate message
    truncateMessage(text, maxLength) {
        if (!text) return '';
        return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
    }
    
    // API call helper
    async apiCall(url, method = 'GET', data = null, json = true) {
        const options = {
            method,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        };
        
        if (data) {
            if (json && !(data instanceof FormData)) {
                options.headers['Content-Type'] = 'application/json';
                options.body = JSON.stringify(data);
            } else {
                options.body = data;
            }
        }
        
        // Always send cookies with same-origin requests
        options.credentials = 'same-origin';
        const response = await fetch(url, options);
        
        if (!response.ok) {
            throw new Error(`API call failed: ${response.statusText}`);
        }
        
        return await response.json();
    }
}

// Global functions for HTML onclick events
window.showNewMessageModal = function() {
    window.messagingApp.showNewMessageModal();
};

window.hideNewMessageModal = function() {
    window.messagingApp.hideNewMessageModal();
};

window.startNewConversation = function(event) {
    window.messagingApp.startNewConversation(event);
};

window.sendMessage = function(event) {
    event.preventDefault();
    window.messagingApp.handleSendMessage();
};

window.filterConversations = function() {
    window.messagingApp.filterConversations();
};

window.refreshMessages = function() {
    if (window.messagingApp.selectedConversation) {
        window.messagingApp.loadMessages(window.messagingApp.selectedConversation.id);
    }
};

// Initialize app when page loads
document.addEventListener('DOMContentLoaded', function() {
    window.messagingApp = new MessagingApp();
});

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (window.messagingApp) {
        window.messagingApp.updateOnlineStatus(false);
    }
});
</script>
@endpush

@push('styles')
<style>
.dark {
    --bg-app: #0f172a;
    --bg-surface: #1e293b;
    --bg-elevated: #334155;
    --text-primary: #f8fafc;
    --text-secondary: #cbd5e1;
    --text-tertiary: #64748b;
    --border: #334155;
    --border-light: #1e293b;
}

/* Global Styles */
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

html, body {
    height: 100%;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    font-feature-settings: 'cv11', 'ss01';
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

/* Scrollbar Styles */
.scrollbar-thin {
    scrollbar-width: thin;
    scrollbar-color: var(--border) transparent;
}

.scrollbar-thin::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

.scrollbar-thin::-webkit-scrollbar-track {
    background: transparent;
}

.scrollbar-thin::-webkit-scrollbar-thumb {
    background-color: var(--border);
    border-radius: var(--radius-full);
    transition: background-color var(--duration-normal) var(--ease);
}

.scrollbar-thin::-webkit-scrollbar-thumb:hover {
    background-color: var(--text-tertiary);
}

/* Professional Button System */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: var(--space-sm);
    padding: var(--space-md) var(--space-xl);
    border: none;
    border-radius: var(--radius);
    font-size: 0.875rem;
    font-weight: 500;
    line-height: 1;
    cursor: pointer;
    transition: all var(--duration-normal) var(--ease);
    text-decoration: none;
    position: relative;
    overflow: hidden;
    white-space: nowrap;
}

.btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
}

.btn-sm {
    padding: var(--space-sm) var(--space-md);
    font-size: 0.75rem;
}

.btn-lg {
    padding: var(--space-lg) var(--space-2xl);
    font-size: 1rem;
}

.btn-primary {
    background: var(--primary);
    color: var(--text-inverse);
    box-shadow: var(--shadow-sm);
}

.btn-primary:hover {
    background: var(--primary-hover);
    box-shadow: var(--shadow-md);
    transform: translateY(-1px);
}

.btn-secondary {
    background: var(--bg-surface);
    color: var(--text-primary);
    border: 1px solid var(--border);
    box-shadow: var(--shadow-sm);
}

.btn-secondary:hover {
    background: var(--bg-elevated);
    border-color: var(--text-tertiary);
    transform: translateY(-1px);
}

.btn-ghost {
    background: transparent;
    color: var(--text-secondary);
}

.btn-ghost:hover {
    background: var(--bg-elevated);
    color: var(--text-primary);
}

.btn-icon {
    width: 2.5rem;
    height: 2.5rem;
    padding: 0;
    border-radius: var(--radius-full);
}

/* Professional Form Elements */
.form-input {
    width: 100%;
    padding: var(--space-md) var(--space-lg);
    background: var(--bg-surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    font-size: 0.875rem;
    color: var(--text-primary);
    transition: all var(--duration-normal) var(--ease);
}

.form-input:focus {
    outline: none;
    border-color: var(--border-focus);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.form-input::placeholder {
    color: var(--text-tertiary);
}

/* Messaging App Layout */
.messaging-app {
    height: calc(100vh - 80px);
    background: var(--bg-app);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    position: relative;
}

/* Professional Header */
.messaging-header {
    background: var(--bg-surface);
    border-bottom: 1px solid var(--border);
    padding: var(--space-lg) var(--space-2xl);
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: var(--shadow-sm);
    z-index: 20;
    position: relative;
}

.header-brand {
    display: flex;
    align-items: center;
    gap: var(--space-lg);
}

.brand-icon {
    width: 3rem;
    height: 3rem;
    background: linear-gradient(135deg, var(--primary), #3b82f6);
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-inverse);
    font-weight: 600;
    font-size: 1.125rem;
    box-shadow: var(--shadow-md);
}

.brand-text h1 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
}

.brand-text p {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin: 0;
}

.header-actions {
    display: flex;
    align-items: center;
    gap: var(--space-md);
}

.connection-status {
    display: flex;
    align-items: center;
    gap: var(--space-sm);
    padding: var(--space-sm) var(--space-md);
    border-radius: var(--radius-full);
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.connection-status.connected {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success);
}

.connection-status.connecting {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning);
}

.connection-status.disconnected {
    background: rgba(239, 68, 68, 0.1);
    color: var(--error);
}

.status-dot {
    width: 0.5rem;
    height: 0.5rem;
    border-radius: var(--radius-full);
    background: currentColor;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

/* Main Layout */
.messaging-main {
    flex: 1;
    display: flex;
    overflow: hidden;
    position: relative;
}

/* Conversations Sidebar */
.conversations-sidebar {
    width: 22rem;
    background: var(--bg-surface);
    border-right: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    position: relative;
    z-index: 10;
}

.sidebar-header {
    padding: var(--space-xl);
    border-bottom: 1px solid var(--border);
    background: var(--bg-surface);
}

.search-container {
    position: relative;
    margin-bottom: var(--space-lg);
}

.search-input {
    width: 100%;
    padding: var(--space-md) var(--space-lg) var(--space-md) 2.75rem;
    background: var(--bg-elevated);
    border: 1px solid var(--border);
    border-radius: var(--radius-full);
    font-size: 0.875rem;
    color: var(--text-primary);
    transition: all var(--duration-normal) var(--ease);
}

.search-input:focus {
    outline: none;
    border-color: var(--border-focus);
    background: var(--bg-surface);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.search-icon {
    position: absolute;
    left: var(--space-lg);
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-tertiary);
    width: 1rem;
    height: 1rem;
    pointer-events: none;
}

.filter-tabs {
    display: flex;
    background: var(--bg-elevated);
    border-radius: var(--radius-md);
    padding: var(--space-xs);
    gap: var(--space-xs);
}

.filter-tab {
    flex: 1;
    padding: var(--space-sm) var(--space-md);
    text-align: center;
    border: none;
    background: transparent;
    color: var(--text-secondary);
    font-size: 0.75rem;
    font-weight: 500;
    border-radius: var(--radius-sm);
    cursor: pointer;
    transition: all var(--duration-fast) var(--ease);
    position: relative;
}

.filter-tab.active {
    background: var(--bg-surface);
    color: var(--text-primary);
    box-shadow: var(--shadow-sm);
}

.filter-tab .badge {
    position: absolute;
    top: -0.25rem;
    right: -0.25rem;
    background: var(--error);
    color: var(--text-inverse);
    font-size: 0.625rem;
    padding: 0.125rem 0.375rem;
    border-radius: var(--radius-full);
    min-width: 1rem;
    text-align: center;
    line-height: 1;
}

/* Conversations List */
.conversations-list {
    flex: 1;
    overflow-y: auto;
    padding: var(--space-md);
}

.conversation-item {
    display: flex;
    align-items: flex-start;
    gap: var(--space-lg);
    padding: var(--space-lg);
    border-radius: var(--radius-md);
    cursor: pointer;
    transition: all var(--duration-normal) var(--ease);
    border: 1px solid transparent;
    margin-bottom: var(--space-sm);
    position: relative;
    background: var(--bg-surface);
}

.conversation-item:hover {
    background: var(--bg-elevated);
    border-color: var(--border);
    transform: translateY(-1px);
    box-shadow: var(--shadow-sm);
}

.conversation-item.active {
    background: var(--primary-light);
    border-color: var(--primary);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

.conversation-item.unread {
    background: rgba(37, 99, 235, 0.05);
    border-color: rgba(37, 99, 235, 0.2);
}

.conversation-avatar {
    position: relative;
    flex-shrink: 0;
}

.avatar {
    width: 3rem;
    height: 3rem;
    border-radius: var(--radius-full);
    background: linear-gradient(135deg, var(--primary), #3b82f6);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-inverse);
    font-weight: 600;
    font-size: 1rem;
    position: relative;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
}

.avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.status-indicator {
    position: absolute;
    bottom: -0.125rem;
    right: -0.125rem;
    width: 0.875rem;
    height: 0.875rem;
    border-radius: var(--radius-full);
    border: 2px solid var(--bg-surface);
    z-index: 2;
}

.status-online {
    background: var(--success);
    animation: pulse-success 2s infinite;
}

.status-away {
    background: var(--warning);
}

.status-offline {
    background: var(--text-tertiary);
}

@keyframes pulse-success {
    0%, 100% {
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    }
    50% {
        box-shadow: 0 0 0 0.5rem rgba(16, 185, 129, 0);
    }
}

.conversation-content {
    flex: 1;
    min-width: 0;
}

.conversation-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: var(--space-xs);
}

.conversation-name {
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
    font-size: 0.875rem;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.conversation-time {
    font-size: 0.75rem;
    color: var(--text-tertiary);
    flex-shrink: 0;
    margin-left: var(--space-sm);
}

.conversation-preview {
    display: flex;
    align-items: center;
    gap: var(--space-sm);
    margin-bottom: var(--space-xs);
}

.conversation-text {
    font-size: 0.75rem;
    color: var(--text-secondary);
    margin: 0;
    flex: 1;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    line-height: 1.3;
}

.conversation-item.unread .conversation-text {
    font-weight: 500;
    color: var(--text-primary);
}

.conversation-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.unread-badge {
    background: var(--primary);
    color: var(--text-inverse);
    font-size: 0.625rem;
    font-weight: 600;
    padding: 0.25rem 0.5rem;
    border-radius: var(--radius-full);
    min-width: 1.25rem;
    text-align: center;
    line-height: 1;
}

.conversation-item.active .unread-badge {
    background: var(--primary-hover);
}

/* Chat Area */
.chat-area {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: var(--bg-app);
    position: relative;
}

.chat-header {
    background: var(--bg-surface);
    border-bottom: 1px solid var(--border);
    padding: var(--space-lg) var(--space-2xl);
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: sticky;
    top: 0;
    z-index: 5;
}

.chat-user {
    display: flex;
    align-items: center;
    gap: var(--space-lg);
}

.chat-user-info h3 {
    margin: 0;
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
}

.chat-user-status {
    display: flex;
    align-items: center;
    gap: var(--space-sm);
    font-size: 0.75rem;
    color: var(--text-secondary);
    margin-top: var(--space-xs);
}

.typing-indicator {
    color: var(--primary);
    font-style: italic;
    animation: fade-in-out 1.5s infinite;
}

@keyframes fade-in-out {
    0%, 100% { opacity: 0.6; }
    50% { opacity: 1; }
}

.chat-actions {
    display: flex;
    align-items: center;
    gap: var(--space-sm);
}

/* Messages Container */
.messages-container {
    flex: 1;
    overflow-y: auto;
    padding: var(--space-2xl) var(--space-2xl) var(--space-lg);
    position: relative;
    background: linear-gradient(to bottom, 
        rgba(248, 250, 252, 0.5) 0%,
        rgba(248, 250, 252, 0.3) 50%,
        rgba(248, 250, 252, 0) 100%);
}

.messages-list {
    display: flex;
    flex-direction: column;
    gap: var(--space-xl);
    max-width: 48rem;
    margin: 0 auto;
    width: 100%;
}

.message-date {
    text-align: center;
    margin: var(--space-2xl) 0;
}

.date-badge {
    background: var(--bg-surface);
    color: var(--text-secondary);
    font-size: 0.75rem;
    font-weight: 500;
    padding: var(--space-sm) var(--space-lg);
    border-radius: var(--radius-full);
    border: 1px solid var(--border);
    display: inline-block;
    box-shadow: var(--shadow-sm);
}

.message-group {
    display: flex;
    flex-direction: column;
    gap: var(--space-sm);
}

.message {
    display: flex;
    gap: var(--space-md);
    max-width: 75%;
    position: relative;
    animation: slide-in var(--duration-normal) var(--ease);
}

.message.sent {
    margin-left: auto;
    flex-direction: row-reverse;
}

@keyframes slide-in {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.message-avatar {
    flex-shrink: 0;
}

.message-avatar .avatar {
    width: 2rem;
    height: 2rem;
    font-size: 0.75rem;
}

.message-bubble {
    background: var(--bg-surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-xl);
    padding: var(--space-lg) var(--space-xl);
    position: relative;
    box-shadow: var(--shadow-sm);
    transition: all var(--duration-normal) var(--ease);
    max-width: 100%;
    word-wrap: break-word;
}

.message-bubble:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-1px);
}

.message.sent .message-bubble {
    background: var(--primary);
    color: var(--text-inverse);
    border-color: var(--primary);
    border-bottom-right-radius: var(--radius-sm);
}

.message.received .message-bubble {
    border-bottom-left-radius: var(--radius-sm);
}

.message-content {
    font-size: 0.875rem;
    line-height: 1.5;
    margin: 0;
    color: inherit;
}

.message-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: var(--space-md);
    font-size: 0.75rem;
    opacity: 0.7;
}

.message-time {
    color: inherit;
}

.message-status {
    display: flex;
    align-items: center;
    gap: var(--space-xs);
}

.message.sent .message-status {
    color: rgba(255, 255, 255, 0.8);
}

/* Message Input Area */
.message-input-area {
    background: var(--bg-surface);
    border-top: 1px solid var(--border);
    padding: var(--space-2xl);
    position: sticky;
    bottom: 0;
    z-index: 5;
}

.input-container {
    display: flex;
    align-items: end;
    gap: var(--space-lg);
    background: var(--bg-elevated);
    border: 2px solid var(--border);
    border-radius: var(--radius-xl);
    padding: var(--space-md);
    transition: all var(--duration-normal) var(--ease);
    max-width: 48rem;
    margin: 0 auto;
    box-shadow: var(--shadow-sm);
}

.input-container:focus-within {
    border-color: var(--border-focus);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1), var(--shadow-md);
}

.input-actions {
    display: flex;
    align-items: center;
    gap: var(--space-sm);
}

.input-action {
    width: 2.25rem;
    height: 2.25rem;
    border: none;
    background: transparent;
    color: var(--text-tertiary);
    border-radius: var(--radius);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all var(--duration-fast) var(--ease);
}

.input-action:hover {
    background: var(--bg-surface);
    color: var(--text-primary);
    transform: scale(1.05);
}

.message-textarea {
    flex: 1;
    border: none;
    background: transparent;
    resize: none;
    font-family: inherit;
    font-size: 0.875rem;
    color: var(--text-primary);
    line-height: 1.5;
    max-height: 8rem;
    min-height: 1.5rem;
    padding: var(--space-sm);
}

.message-textarea:focus {
    outline: none;
}

.message-textarea::placeholder {
    color: var(--text-tertiary);
}

.send-button {
    width: 2.75rem;
    height: 2.75rem;
    background: var(--primary);
    color: var(--text-inverse);
    border: none;
    border-radius: var(--radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all var(--duration-normal) var(--ease);
    flex-shrink: 0;
    box-shadow: var(--shadow-sm);
}

.send-button:hover {
    background: var(--primary-hover);
    transform: scale(1.05);
    box-shadow: var(--shadow-md);
}

.send-button:active {
    transform: scale(0.95);
}

.send-button:disabled {
    background: var(--text-tertiary);
    cursor: not-allowed;
    transform: none;
}

/* Welcome State */
.welcome-state {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: var(--space-2xl);
    background: linear-gradient(135deg, 
        rgba(37, 99, 235, 0.05) 0%,
        rgba(59, 130, 246, 0.05) 50%,
        rgba(99, 102, 241, 0.05) 100%);
}

.welcome-content {
    max-width: 24rem;
    margin: 0 auto;
}

.welcome-icon {
    width: 5rem;
    height: 5rem;
    background: linear-gradient(135deg, var(--primary), #3b82f6);
    border-radius: var(--radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-inverse);
    font-size: 2rem;
    margin: 0 auto var(--space-2xl);
    box-shadow: var(--shadow-lg);
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

.welcome-title {
    font-size: 1.875rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 var(--space-md);
}

.welcome-text {
    color: var(--text-secondary);
    margin: 0 0 var(--space-2xl);
    line-height: 1.6;
    font-size: 1rem;
}

/* Modals */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: var(--bg-overlay);
    backdrop-filter: blur(4px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 50;
    padding: var(--space-lg);
    animation: fade-in var(--duration-normal) var(--ease);
}

@keyframes fade-in {
    from { opacity: 0; }
    to { opacity: 1; }
}

.modal {
    background: var(--bg-surface);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-xl);
    width: 100%;
    max-width: 32rem;
    max-height: 90vh;
    overflow-y: auto;
    animation: slide-up var(--duration-normal) var(--ease);
}

@keyframes slide-up {
    from {
        opacity: 0;
        transform: translateY(20px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.modal-header {
    padding: var(--space-2xl) var(--space-2xl) 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.modal-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
}

.modal-close {
    width: 2rem;
    height: 2rem;
    border: none;
    background: transparent;
    color: var(--text-tertiary);
    border-radius: var(--radius);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all var(--duration-fast) var(--ease);
}

.modal-close:hover {
    background: var(--bg-elevated);
    color: var(--text-primary);
}

.modal-body {
    padding: var(--space-2xl);
}

.form-group {
    margin-bottom: var(--space-xl);
}

.form-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--text-primary);
    margin-bottom: var(--space-sm);
}

.modal-footer {
    padding: 0 var(--space-2xl) var(--space-2xl);
    display: flex;
    justify-content: flex-end;
    gap: var(--space-md);
}

/* Utilities */
.loading {
    opacity: 0.6;
    pointer-events: none;
}

.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.animate-bounce {
    animation: bounce 1s infinite;
}

@keyframes bounce {
    0%, 20%, 53%, 80%, 100% {
        transform: translate3d(0,0,0);
    }
    40%, 43% {
        transform: translate3d(0, -30px, 0);
    }
    70% {
        transform: translate3d(0, -15px, 0);
    }
    90% {
        transform: translate3d(0, -4px, 0);
    }
}

/* Responsive Design */
@media (max-width: 1024px) {
    .conversations-sidebar {
        width: 20rem;
    }
    
    .message {
        max-width: 85%;
    }
}

@media (max-width: 768px) {
    .messaging-header {
        padding: var(--space-lg);
    }
    
    .brand-text h1 {
        font-size: 1.25rem;
    }
    
    .conversations-sidebar {
        width: 18rem;
    }
    
    .message {
        max-width: 90%;
    }
    
    .message-bubble {
        padding: var(--space-md) var(--space-lg);
    }
    
    .input-container {
        padding: var(--space-sm);
    }
}

@media (max-width: 640px) {
    .messaging-main {
        flex-direction: column;
    }
    
    .conversations-sidebar {
        width: 100%;
        height: 40vh;
    }
    
    .chat-area {
        height: 60vh;
    }
    
    .welcome-icon {
        width: 4rem;
        height: 4rem;
        font-size: 1.5rem;
    }
    
    .welcome-title {
        font-size: 1.5rem;
    }
}

/* Accessibility */
@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

.btn:focus,
.form-input:focus,
.conversation-item:focus {
    outline: 2px solid var(--primary);
    outline-offset: 2px;
}

/* Main Layout */
.messaging-main {
    flex: 1;
    display: flex;
    overflow: hidden;
}

/* Sidebar */
.messaging-sidebar {
    width: 360px;
    background: var(--surface);
    border-right: 1px solid var(--border);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.sidebar-header {
    padding: 1.5rem;
    border-bottom: 1px solid var(--border);
}

.sidebar-search {
    position: relative;
    margin-bottom: 1rem;
}

.sidebar-search input {
    width: 100%;
    padding: 0.75rem 1rem 0.75rem 2.5rem;
    background: var(--background);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    font-size: 0.875rem;
    color: var(--text-primary);
    transition: all 0.2s ease;
}

.sidebar-search input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgb(79 70 229 / 0.1);
}

.sidebar-search .search-icon {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    width: 1rem;
    height: 1rem;
}

/* Folder Management */
.folder-section {
    margin-bottom: 1.5rem;
}

.folder-header {
    display: flex;
    align-items: center;
    justify-content: between;
    margin-bottom: 0.75rem;
}

.folder-title {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--text-muted);
    margin: 0;
}

.folder-list {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.folder-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    border-radius: var(--radius);
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
}

.folder-item:hover {
    background: var(--background);
}

.folder-item.active {
    background: var(--primary);
    color: white;
}

.folder-icon {
    width: 1.25rem;
    height: 1.25rem;
    flex-shrink: 0;
}

.folder-name {
    font-weight: 500;
    flex: 1;
}

.folder-count {
    font-size: 0.75rem;
    background: var(--text-muted);
    color: white;
    padding: 0.125rem 0.375rem;
    border-radius: 0.75rem;
    min-width: 1.25rem;
    text-align: center;
}

.folder-item.active .folder-count {
    background: rgba(255, 255, 255, 0.2);
}

/* Filter Tabs */
.filter-tabs {
    display: flex;
    background: var(--background);
    border-radius: var(--radius);
    padding: 0.25rem;
    margin-bottom: 1rem;
}

.filter-tab {
    flex: 1;
    padding: 0.5rem 0.75rem;
    text-align: center;
    border: none;
    background: transparent;
    color: var(--text-secondary);
    font-size: 0.875rem;
    font-weight: 500;
    border-radius: calc(var(--radius) - 0.25rem);
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
}

.filter-tab.active {
    background: var(--surface);
    color: var(--text-primary);
    box-shadow: var(--shadow);
}

.filter-tab .badge {
    position: absolute;
    top: -0.25rem;
    right: -0.25rem;
    background: var(--error);
    color: white;
    font-size: 0.625rem;
    padding: 0.125rem 0.25rem;
    border-radius: 0.75rem;
    min-width: 1rem;
    text-align: center;
}

/* Conversations List */
.conversations-list {
    flex: 1;
    overflow-y: auto;
    padding: 0 1rem 1rem;
}

.conversation-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    border-radius: var(--radius);
    cursor: pointer;
    transition: all 0.2s ease;
    border: 1px solid transparent;
    margin-bottom: 0.5rem;
    position: relative;
}

.conversation-item:hover {
    background: var(--background);
    border-color: var(--border);
}

.conversation-item.active {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
}

.conversation-item.unread {
    background: rgba(79, 70, 229, 0.05);
    border-color: var(--primary);
}

.conversation-avatar {
    position: relative;
    flex-shrink: 0;
}

.avatar {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary), var(--accent));
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 1rem;
}

.avatar img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
}

.status-indicator {
    position: absolute;
    bottom: -1px;
    right: -1px;
    width: 0.75rem;
    height: 0.75rem;
    border-radius: 50%;
    border: 2px solid var(--surface);
}

.status-online {
    background: var(--success);
}

.status-away {
    background: var(--warning);
}

.status-offline {
    background: var(--text-muted);
}

.conversation-content {
    flex: 1;
    min-width: 0;
}

.conversation-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 0.25rem;
}

.conversation-name {
    font-weight: 600;
    color: inherit;
    margin: 0;
    truncate;
}

.conversation-time {
    font-size: 0.75rem;
    color: var(--text-muted);
    flex-shrink: 0;
}

.conversation-item.active .conversation-time {
    color: rgba(255, 255, 255, 0.8);
}

.conversation-preview {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.conversation-text {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin: 0;
    flex: 1;
    truncate;
}

.conversation-item.active .conversation-text {
    color: rgba(255, 255, 255, 0.9);
}

.conversation-item.unread .conversation-text {
    font-weight: 500;
    color: var(--text-primary);
}

.conversation-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-shrink: 0;
}

.unread-badge {
    background: var(--primary);
    color: white;
    font-size: 0.625rem;
    font-weight: 600;
    padding: 0.25rem 0.5rem;
    border-radius: 0.75rem;
    min-width: 1.25rem;
    text-align: center;
}

.conversation-item.active .unread-badge {
    background: rgba(255, 255, 255, 0.2);
}

/* Chat Area */
.chat-area {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: var(--background);
}

.chat-header {
    background: var(--surface);
    border-bottom: 1px solid var(--border);
    padding: 1rem 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.chat-user {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.chat-user-info h3 {
    margin: 0;
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
}

.chat-user-status {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin-top: 0.125rem;
}

.typing-indicator {
    color: var(--accent);
    font-style: italic;
}

.chat-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.chat-action {
    width: 2.5rem;
    height: 2.5rem;
    border: none;
    background: transparent;
    color: var(--text-secondary);
    border-radius: var(--radius);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

.chat-action:hover {
    background: var(--background);
    color: var(--text-primary);
}

/* Messages */
.messages-container {
    flex: 1;
    overflow-y: auto;
    padding: 1rem;
    scroll-behavior: smooth;
}

.messages-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.message-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.message-date {
    text-align: center;
    margin: 1.5rem 0;
}

.date-badge {
    background: var(--surface);
    color: var(--text-secondary);
    font-size: 0.75rem;
    padding: 0.5rem 1rem;
    border-radius: var(--radius-lg);
    border: 1px solid var(--border);
    display: inline-block;
}

.message {
    display: flex;
    gap: 0.75rem;
    max-width: 70%;
}

.message.sent {
    margin-left: auto;
    flex-direction: row-reverse;
}

.message-avatar {
    flex-shrink: 0;
}

.message-avatar .avatar {
    width: 2rem;
    height: 2rem;
    font-size: 0.875rem;
}

.message-bubble {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1.125rem;
    padding: 0.75rem 1rem;
    position: relative;
    box-shadow: var(--shadow);
}

.message.sent .message-bubble {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
}

.message-content {
    font-size: 0.875rem;
    line-height: 1.5;
    margin: 0;
    word-wrap: break-word;
}

.message-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 0.5rem;
    font-size: 0.75rem;
    opacity: 0.7;
}

.message-time {
    color: inherit;
}

.message-status {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.message.sent .message-status {
    color: rgba(255, 255, 255, 0.8);
}

/* Message Input */
.message-input {
    background: var(--surface);
    border-top: 1px solid var(--border);
    padding: 1rem 1.5rem;
}

.input-container {
    display: flex;
    align-items: end;
    gap: 0.75rem;
    background: var(--background);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 0.75rem;
    transition: all 0.2s ease;
}

.input-container:focus-within {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgb(79 70 229 / 0.1);
}

.input-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.input-action {
    width: 2rem;
    height: 2rem;
    border: none;
    background: transparent;
    color: var(--text-secondary);
    border-radius: var(--radius);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

.input-action:hover {
    background: var(--surface);
    color: var(--text-primary);
}

.message-textarea {
    flex: 1;
    border: none;
    background: transparent;
    resize: none;
    font-family: inherit;
    font-size: 0.875rem;
    color: var(--text-primary);
    line-height: 1.5;
    max-height: 120px;
    min-height: 1.5rem;
}

.message-textarea:focus {
    outline: none;
}

.message-textarea::placeholder {
    color: var(--text-muted);
}

.send-button {
    width: 2.5rem;
    height: 2.5rem;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    flex-shrink: 0;
}

.send-button:hover {
    background: var(--primary-light);
    transform: scale(1.05);
}

.send-button:disabled {
    background: var(--text-muted);
    cursor: not-allowed;
    transform: none;
}

/* Welcome State */
.welcome-state {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 2rem;
}

.welcome-content {
    max-width: 400px;
}

.welcome-icon {
    width: 4rem;
    height: 4rem;
    background: linear-gradient(135deg, var(--primary), var(--accent));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    margin: 0 auto 1.5rem;
}

.welcome-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 0.5rem;
}

.welcome-text {
    color: var(--text-secondary);
    margin: 0 0 1.5rem;
    line-height: 1.6;
}

/* Modals */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 50;
    padding: 1rem;
}

.modal {
    background: var(--surface);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
    width: 100%;
    max-width: 500px;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    padding: 1.5rem 1.5rem 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.modal-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
}

.modal-close {
    width: 2rem;
    height: 2rem;
    border: none;
    background: transparent;
    color: var(--text-secondary);
    border-radius: var(--radius);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

.modal-body {
    padding: 1.5rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.form-input {
    width: 100%;
    padding: 0.75rem;
    background: var(--background);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    font-size: 0.875rem;
    color: var(--text-primary);
    transition: all 0.2s ease;
}

.form-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgb(79 70 229 / 0.1);
}

.form-textarea {
    resize: vertical;
    min-height: 80px;
}

.modal-footer {
    padding: 0 1.5rem 1.5rem;
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
}

/* Utilities */
.truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.loading {
    opacity: 0.5;
    pointer-events: none;
}

.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.animate-bounce {
    animation: bounce 1s infinite;
}

@keyframes bounce {
    0%, 20%, 53%, 80%, 100% {
        transform: translate3d(0,0,0);
    }
    40%, 43% {
        transform: translate3d(0, -30px, 0);
    }
    70% {
        transform: translate3d(0, -15px, 0);
    }
    90% {
        transform: translate3d(0, -4px, 0);
    }
}

/* Responsive */
@media (max-width: 768px) {
    .messaging-sidebar {
        width: 100%;
        max-width: 400px;
    }
    
    .message {
        max-width: 85%;
    }
    
    .conversation-item {
        padding: 0.75rem;
    }
    
    .messaging-header {
        padding: 0.75rem 1rem;
    }
}

@media (max-width: 480px) {
    .messaging-sidebar {
        max-width: none;
    }
    
    .message {
        max-width: 90%;
    }
    
    .sidebar-header {
        padding: 1rem;
    }
    
    .conversations-list {
        padding: 0 0.75rem 0.75rem;
    }
}

/* Hide scrollbars but keep functionality */
.conversations-list::-webkit-scrollbar,
.messages-container::-webkit-scrollbar {
    width: 6px;
}

.conversations-list::-webkit-scrollbar-track,
.messages-container::-webkit-scrollbar-track {
    background: transparent;
}

.conversations-list::-webkit-scrollbar-thumb,
.messages-container::-webkit-scrollbar-thumb {
    background: var(--border);
    border-radius: 3px;
}

.conversations-list::-webkit-scrollbar-thumb:hover,
.messages-container::-webkit-scrollbar-thumb:hover {
    background: var(--text-muted);
}
</style>
@endpush

@push('styles')
<style>
/* Elite Messaging System - Professional Design */
:root {
    --primary-gradient: linear-gradient(135deg, #6366f1 0%, #8b5cf6 25%, #a855f7 50%, #d946ef 75%, #ec4899 100%);
    --message-mine: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    --message-theirs: #ffffff;
    --surface: rgba(255, 255, 255, 0.95);
    --surface-dark: rgba(15, 23, 42, 0.95);
    --border-light: rgba(148, 163, 184, 0.2);
    --text-primary: #0f172a;
    --text-secondary: #64748b;
    --shadow-soft: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-medium: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --shadow-large: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

.dark {
    --message-theirs: rgba(30, 41, 59, 0.95);
    --surface: rgba(15, 23, 42, 0.95);
    --border-light: rgba(71, 85, 105, 0.3);
    --text-primary: #f8fafc;
    --text-secondary: #94a3b8;
}

/* Base Container */
.messaging-app {
    height: calc(100vh - 80px);
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    background: var(--primary-gradient);
    position: relative;
    overflow: hidden;
}

.messaging-app::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
        radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.3) 0%, transparent 50%),
        radial-gradient(circle at 40% 40%, rgba(120, 219, 255, 0.3) 0%, transparent 50%);
    pointer-events: none;
}

/* Scrollbar Styling */
.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: rgba(148, 163, 184, 0.4) transparent;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(148, 163, 184, 0.4);
    border-radius: 3px;
    transition: background 0.2s ease;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(148, 163, 184, 0.6);
}

/* Glass Panel Effects */
.glass-panel {
    background: var(--surface);
    backdrop-filter: blur(20px) saturate(180%);
    border: 1px solid var(--border-light);
    box-shadow: var(--shadow-medium);
}

/* Professional Header */
.app-header {
    background: var(--surface);
    backdrop-filter: blur(20px) saturate(180%);
    border-bottom: 1px solid var(--border-light);
    box-shadow: var(--shadow-soft);
    position: relative;
    z-index: 10;
}

.stats-card {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 16px;
    padding: 16px 20px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.stats-card:hover {
    background: rgba(255, 255, 255, 0.95);
    transform: translateY(-2px);
    box-shadow: var(--shadow-medium);
}

/* Conversations Sidebar */
.conversations-sidebar {
    background: var(--surface);
    backdrop-filter: blur(20px) saturate(180%);
    border-right: 1px solid var(--border-light);
    box-shadow: var(--shadow-soft);
}

.conversation-item {
    background: rgba(255, 255, 255, 0.6);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 16px;
    margin: 8px 16px;
    padding: 16px;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.conversation-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
    transition: left 0.6s ease;
}

.conversation-item:hover::before {
    left: 100%;
}

.conversation-item:hover {
    background: rgba(255, 255, 255, 0.8);
    transform: translateY(-4px) scale(1.02);
    box-shadow: var(--shadow-large);
}

.conversation-item.active {
    background: var(--primary-gradient);
    color: white;
    transform: translateY(-4px) scale(1.02);
    box-shadow: var(--shadow-large);
}

.conversation-item.unread {
    border-left: 4px solid #3b82f6;
    background: rgba(59, 130, 246, 0.1);
}

/* Avatar System */
.avatar {
    position: relative;
    display: inline-block;
    border-radius: 50%;
    overflow: hidden;
    box-shadow: var(--shadow-soft);
    transition: all 0.3s ease;
}

.avatar:hover {
    box-shadow: var(--shadow-medium);
    transform: scale(1.05);
}

.avatar-ring {
    position: absolute;
    inset: -3px;
    border-radius: 50%;
    background: var(--primary-gradient);
    opacity: 0;
    transition: opacity 0.3s ease;
    animation: ring-pulse 2s infinite ease-in-out;
}

.avatar:hover .avatar-ring {
    opacity: 1;
}

@keyframes ring-pulse {
    0%, 100% { transform: scale(1); opacity: 0.7; }
    50% { transform: scale(1.1); opacity: 1; }
}

.status-indicator {
    position: absolute;
    bottom: -2px;
    right: -2px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 3px solid white;
    z-index: 2;
}

.status-indicator.online {
    background: linear-gradient(135deg, #10b981, #059669);
    animation: status-pulse 2s infinite;
}

.status-indicator.away {
    background: linear-gradient(135deg, #f59e0b, #d97706);
}

.status-indicator.offline {
    background: #9ca3af;
}

@keyframes status-pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
    50% { box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
}

/* Professional Message Bubbles */
.message-group {
    margin: 24px 0;
}

.message-bubble {
    max-width: 70%;
    padding: 16px 20px;
    border-radius: 20px;
    position: relative;
    margin: 8px 0;
    word-wrap: break-word;
    line-height: 1.5;
    font-size: 15px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: var(--shadow-soft);
}

.message-bubble:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-medium);
}

.message-bubble.mine {
    background: var(--message-mine);
    color: white;
    margin-left: auto;
    border-bottom-right-radius: 6px;
}

.message-bubble.theirs {
    background: var(--message-theirs);
    color: var(--text-primary);
    margin-right: auto;
    border-bottom-left-radius: 6px;
    border: 1px solid var(--border-light);
}

/* Message Tails */
.message-bubble::after {
    content: '';
    position: absolute;
    bottom: 0;
    width: 0;
    height: 0;
    border-style: solid;
}

.message-bubble.mine::after {
    right: -8px;
    border-width: 8px 0 0 8px;
    border-color: #1d4ed8 transparent transparent transparent;
}

.message-bubble.theirs::after {
    left: -8px;
    border-width: 8px 8px 0 0;
    border-color: var(--message-theirs) transparent transparent transparent;
}

/* Message Content */
.message-content {
    position: relative;
    z-index: 1;
}

.message-text {
    margin: 0;
    font-weight: 400;
    letter-spacing: 0.01em;
}

.message-time {
    font-size: 12px;
    opacity: 0.7;
    margin-top: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.message-status {
    display: inline-flex;
    align-items: center;
    gap: 2px;
}

.message-status.read {
    color: #10b981;
}

.message-status.delivered {
    color: #3b82f6;
}

.message-status.sending {
    color: #f59e0b;
}

/* Message Actions */
.message-actions {
    position: absolute;
    top: -20px;
    right: 16px;
    display: flex;
    gap: 4px;
    opacity: 0;
    transform: translateY(10px);
    transition: all 0.3s ease;
    background: white;
    padding: 8px;
    border-radius: 12px;
    box-shadow: var(--shadow-medium);
    border: 1px solid var(--border-light);
}

.message-bubble:hover .message-actions {
    opacity: 1;
    transform: translateY(0);
}

.action-button {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    border: none;
    background: transparent;
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

.action-button:hover {
    background: var(--primary-gradient);
    color: white;
    transform: scale(1.1);
}

/* Typing Indicator */
.typing-indicator {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px 20px;
    background: var(--message-theirs);
    border-radius: 20px;
    border-bottom-left-radius: 6px;
    margin: 8px 0;
    max-width: 200px;
    border: 1px solid var(--border-light);
    box-shadow: var(--shadow-soft);
    animation: typing-fade-in 0.3s ease;
}

@keyframes typing-fade-in {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.typing-dots {
    display: flex;
    gap: 4px;
}

.typing-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--text-secondary);
    animation: typing-bounce 1.4s infinite ease-in-out;
}

.typing-dot:nth-child(1) { animation-delay: -0.32s; }
.typing-dot:nth-child(2) { animation-delay: -0.16s; }
.typing-dot:nth-child(3) { animation-delay: 0s; }

@keyframes typing-bounce {
    0%, 80%, 100% {
        transform: scale(0.8);
        opacity: 0.5;
    }
    40% {
        transform: scale(1.2);
        opacity: 1;
    }
}

/* Message Input Area */
.message-input-area {
    background: var(--surface);
    backdrop-filter: blur(20px) saturate(180%);
    border-top: 1px solid var(--border-light);
    padding: 24px;
    box-shadow: var(--shadow-soft);
}

.input-container {
    position: relative;
    background: white;
    border: 2px solid var(--border-light);
    border-radius: 24px;
    padding: 4px;
    transition: all 0.3s ease;
    box-shadow: var(--shadow-soft);
}

.input-container:focus-within {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1), var(--shadow-medium);
}

.message-textarea {
    width: 100%;
    border: none;
    outline: none;
    padding: 16px 60px 16px 20px;
    background: transparent;
    resize: none;
    font-size: 15px;
    line-height: 1.5;
    max-height: 120px;
    color: var(--text-primary);
    font-family: inherit;
}

.message-textarea::placeholder {
    color: var(--text-secondary);
}

.send-button {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    width: 44px;
    height: 44px;
    border-radius: 50%;
    border: none;
    background: var(--primary-gradient);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: var(--shadow-soft);
}

.send-button:hover {
    transform: translateY(-50%) scale(1.1);
    box-shadow: var(--shadow-medium);
}

.send-button:active {
    transform: translateY(-50%) scale(0.95);
}

.send-button:disabled {
    background: #9ca3af;
    cursor: not-allowed;
    transform: translateY(-50%) scale(1);
}

/* File Attachments */
.attachment-preview {
    display: flex;
    gap: 8px;
    margin-bottom: 16px;
    flex-wrap: wrap;
}

.attachment-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background: rgba(59, 130, 246, 0.1);
    border: 1px solid rgba(59, 130, 246, 0.2);
    border-radius: 12px;
    font-size: 13px;
    color: #1e40af;
    transition: all 0.2s ease;
}

.attachment-item:hover {
    background: rgba(59, 130, 246, 0.15);
}

.remove-attachment {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: rgba(239, 68, 68, 0.2);
    color: #dc2626;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

.remove-attachment:hover {
    background: #dc2626;
    color: white;
}

/* Connection Status */
.connection-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.connection-status.connected {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    animation: pulse-connected 2s infinite;
}

.connection-status.disconnected {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
}

.connection-status.connecting {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
    animation: pulse-connecting 1s infinite;
}

@keyframes pulse-connected {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.8; }
}

@keyframes pulse-connecting {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.6; }
}

/* Loading States */
.loading-skeleton {
    background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
    background-size: 200% 100%;
    animation: skeleton-loading 1.5s infinite;
    border-radius: 8px;
}

@keyframes skeleton-loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

.dark .loading-skeleton {
    background: linear-gradient(90deg, #334155 25%, #475569 50%, #334155 75%);
    background-size: 200% 100%;
}

/* Button Styles */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 24px;
    border: none;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    position: relative;
    overflow: hidden;
}

.btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s ease;
}

.btn:hover::before {
    left: 100%;
}

.btn-primary {
    background: var(--primary-gradient);
    color: white;
    box-shadow: var(--shadow-soft);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-medium);
}

.btn-secondary {
    background: white;
    color: var(--text-primary);
    border: 1px solid var(--border-light);
    box-shadow: var(--shadow-soft);
}

.btn-secondary:hover {
    background: #f8fafc;
    transform: translateY(-1px);
}

/* Modal Styles */
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    z-index: 50;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.modal-content {
    background: white;
    border-radius: 20px;
    padding: 32px;
    max-width: 500px;
    width: 100%;
    box-shadow: var(--shadow-large);
    border: 1px solid var(--border-light);
    position: relative;
    animation: modal-appear 0.3s ease;
}

@keyframes modal-appear {
    from {
        opacity: 0;
        transform: scale(0.9) translateY(20px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

/* Form Styles */
.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 8px;
}

.form-input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid var(--border-light);
    border-radius: 12px;
    font-size: 15px;
    transition: all 0.3s ease;
    background: white;
    color: var(--text-primary);
}

.form-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Responsive Design */
@media (max-width: 768px) {
    .messaging-app {
        height: 100vh;
    }
    
    .conversations-sidebar {
        width: 100%;
        max-width: 320px;
    }
    
    .message-bubble {
        max-width: 85%;
        font-size: 14px;
        padding: 12px 16px;
    }
    
    .stats-card {
        padding: 12px 16px;
    }
    
    .btn {
        padding: 10px 20px;
        font-size: 13px;
    }
}

@media (max-width: 480px) {
    .message-bubble {
        max-width: 90%;
        font-size: 14px;
        padding: 10px 14px;
    }
    
    .conversation-item {
        margin: 6px 12px;
        padding: 12px;
    }
}

/* Accessibility */
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}

/* Focus styles for keyboard navigation */
.btn:focus,
.conversation-item:focus,
.message-textarea:focus,
.form-input:focus {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
}
</style>
@endpush

@section('content')
<div class="messaging-container" x-data="messagingApp()" x-init="init()">
    <!-- Modern Header -->
    <header class="messaging-header">
        <div class="header-brand">
            <div class="brand-icon">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
            </div>
            <div class="brand-text">
                <h1>Messages Pro</h1>
                <p>Enterprise Communication</p>
            </div>
        </div>
        
        <div class="header-actions">
            <button @click="showFoldersModal = true" class="btn btn-secondary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                </svg>
                Folders
            </button>
            <button @click="showFiltersModal = true" class="btn btn-secondary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
                Filter
            </button>
            <button @click="showNewMessageModal = true" class="btn btn-primary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                New Chat
            </button>
        </div>
    </header>

    <!-- Main Layout -->
    <div class="flex h-full" style="height: calc(100vh - 160px);">
        <!-- Conversations Sidebar -->
        <aside class="conversations-sidebar w-96 flex flex-col custom-scrollbar">
            <!-- Filters & Search -->
            <div class="p-6 border-b border-gray-200 dark:border-zinc-700">
                <!-- Filter Tabs -->
                <div class="flex space-x-1 mb-4 bg-gray-100 dark:bg-zinc-700 rounded-xl p-1">
                    <button @click="activeFilter = 'all'" 
                            :class="activeFilter === 'all' ? 'bg-white dark:bg-zinc-600 text-gray-900 dark:text-zinc-100 shadow-sm' : 'text-gray-500 dark:text-zinc-400'"
                            class="flex-1 px-4 py-2 text-sm font-semibold rounded-lg transition-all">
                        All
                    </button>
                    <button @click="activeFilter = 'unread'" 
                            :class="activeFilter === 'unread' ? 'bg-white dark:bg-zinc-600 text-gray-900 dark:text-zinc-100 shadow-sm' : 'text-gray-500 dark:text-zinc-400'"
                            class="flex-1 px-4 py-2 text-sm font-semibold rounded-lg transition-all relative">
                        Unread
                        <span x-show="totalUnreadCount > 0" class="absolute -top-1 -right-1 text-xs bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center" x-text="totalUnreadCount"></span>
                    </button>
                </div>
            
            <!-- Search -->
            <div class="relative">
                <input type="text" 
                       x-model="searchQuery" 
                       @input="debouncedSearch()"
                       class="w-full pl-9 pr-8 py-2.5 text-sm border border-zinc-200 dark:border-zinc-600 rounded-lg bg-zinc-50 dark:bg-zinc-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-zinc-900 dark:text-zinc-100 transition-all" 
                       placeholder="Search conversations...">
                <svg class="absolute left-3 top-3 w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <button x-show="searchQuery" @click="searchQuery = ''" class="absolute right-2 top-2.5 p-1 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Conversations List -->
        <div class="flex-1 overflow-y-auto scrollbar-thin scrollbar-thumb-zinc-300 dark:scrollbar-thumb-zinc-600">
            <template x-for="conversation in filteredConversations" :key="conversation.id">
                <div class="relative p-4 border-b border-zinc-100 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 cursor-pointer transition-all group" 
                     :class="{ 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800': selectedConversation && selectedConversation.id === conversation.id }"
                     @click="selectConversation(conversation)"
                     @contextmenu.prevent="showConversationMenu($event, conversation)">
                    
                    <!-- Online indicator -->
                    <div x-show="conversation.other_user.is_online" class="absolute top-2 right-2 w-2 h-2 bg-green-500 rounded-full"></div>
                    
                    <div class="flex items-start space-x-3">
                        <!-- Avatar with status -->
                        <div class="flex-shrink-0 relative">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold shadow-lg"
                                 x-text="conversation.other_user.name.charAt(0).toUpperCase()"></div>
                            <div x-show="conversation.other_user.is_online" class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white dark:border-zinc-800 rounded-full"></div>
                        </div>
                        
                        <!-- Conversation Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 truncate" x-text="conversation.other_user.name"></p>
                                <div class="flex items-center space-x-2">
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400" x-text="formatRelativeTime(conversation.updated_at)"></p>
                                    <span x-show="conversation.unread_count > 0" 
                                          class="inline-flex items-center justify-center min-w-[20px] h-5 text-xs font-bold leading-none text-white bg-blue-600 rounded-full px-1.5 animate-pulse"
                                          x-text="conversation.unread_count"></span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-1 flex-1 min-w-0">
                                    <span x-show="conversation.last_message?.is_mine" class="text-xs text-zinc-400">You:</span>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400 truncate" 
                                       :class="{ 'font-medium text-zinc-900 dark:text-zinc-100': conversation.unread_count > 0 }"
                                       x-text="conversation.last_message?.content || 'No messages yet'"></p>
                                </div>
                                <div class="flex items-center space-x-1 ml-2">
                                    <span x-show="conversation.last_message?.is_mine && conversation.last_message?.is_read" class="text-blue-500">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </span>
                                    <span x-show="conversation.priority === 'high'" class="text-red-500" title="High Priority">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            
            <!-- Loading State -->
            <div x-show="loading && conversations.length === 0" class="p-8 text-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-4"></div>
                <p class="text-zinc-500 dark:text-zinc-400">Loading conversations...</p>
            </div>
            
            <!-- Empty State -->
            <div x-show="!loading && filteredConversations.length === 0" class="p-8 text-center text-zinc-500 dark:text-zinc-400">
                <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-blue-100 to-purple-100 dark:from-blue-900/20 dark:to-purple-900/20 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-zinc-400 dark:text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <p class="text-lg font-medium mb-2" x-text="searchQuery ? 'No conversations found' : 'No conversations yet'"></p>
                <p class="text-sm mb-4" x-text="searchQuery ? 'Try adjusting your search terms' : 'Start a conversation to see it here'"></p>
                <button x-show="!searchQuery" @click="showNewMessageModal = true" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Start New Conversation
                </button>
            </div>
        </div>
    </div>
    
    <!-- Enhanced Chat Area -->
    <div class="flex-1 flex flex-col bg-white dark:bg-zinc-800">
        <!-- Chat Header -->
        <div x-show="selectedConversation" class="p-4 border-b border-zinc-200 dark:border-zinc-700 bg-gradient-to-r from-white to-zinc-50 dark:from-zinc-800 dark:to-zinc-900">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold shadow-lg"
                             x-text="selectedConversation?.other_user.name.charAt(0).toUpperCase()"></div>
                        <div x-show="selectedConversation?.other_user.is_online" class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white dark:border-zinc-800 rounded-full animate-pulse"></div>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100" x-text="selectedConversation?.other_user.name"></h3>
                        <div class="flex items-center space-x-2">
                            <div class="flex items-center space-x-1">
                                <div class="w-2 h-2 rounded-full" :class="selectedConversation?.other_user.is_online ? 'bg-green-500' : 'bg-zinc-400'"></div>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400" 
                                   x-text="selectedConversation?.other_user.is_online ? 'Online' : selectedConversation?.other_user.last_seen || 'Offline'"></p>
                            </div>
                            <span x-show="isTyping" class="text-sm text-blue-500 animate-pulse">typing...</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <button @click="toggleCallModal()" class="p-2 text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-700 rounded-lg transition-colors" title="Start Call">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                    </button>
                    <button @click="toggleSearchInChat()" class="p-2 text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-700 rounded-lg transition-colors" title="Search in Chat">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="p-2 text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-700 rounded-lg transition-colors" title="More Options">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white dark:bg-zinc-800 rounded-lg shadow-lg border border-zinc-200 dark:border-zinc-700 py-1 z-50">
                            <button @click="viewProfile(); open = false" class="w-full text-left px-4 py-2 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-700">View Profile</button>
                            <button @click="muteConversation(); open = false" class="w-full text-left px-4 py-2 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-700">Mute Notifications</button>
                            <button @click="clearHistory(); open = false" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">Clear History</button>
                            <button @click="blockUser(); open = false" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">Block User</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- In-Chat Search -->
            <div x-show="showChatSearch" x-transition class="mt-4 relative">
                <input type="text" x-model="chatSearchQuery" class="w-full pl-9 pr-8 py-2 text-sm border border-zinc-200 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Search in this conversation...">
                <svg class="absolute left-3 top-2.5 w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <button @click="showChatSearch = false; chatSearchQuery = ''" class="absolute right-2 top-2.5 p-1 text-zinc-400 hover:text-zinc-600">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Messages Area -->
        <div x-show="selectedConversation" class="flex-1 overflow-y-auto p-4 space-y-4 bg-gradient-to-b from-zinc-50/30 to-transparent dark:from-zinc-900/30" x-ref="messagesContainer" @scroll="handleScroll">
            <!-- Date Divider -->
            <template x-for="(group, date) in groupedMessages" :key="date">
                <div>
                    <div class="flex items-center justify-center my-6">
                        <div class="bg-zinc-200 dark:bg-zinc-700 text-zinc-500 dark:text-zinc-400 text-xs px-3 py-1 rounded-full" x-text="formatDate(date)"></div>
                    </div>
                    
                    <template x-for="message in group" :key="message.id">
                        <div class="flex" :class="message.is_mine ? 'justify-end' : 'justify-start'" 
                             :id="'message-' + message.id"
                             :class="{ 'highlight-message': highlightedMessageId === message.id }">
                            <div class="flex items-end space-x-2 max-w-xs lg:max-w-md" :class="message.is_mine ? 'flex-row-reverse space-x-reverse' : ''">
                                <!-- Avatar for received messages -->
                                <div x-show="!message.is_mine" class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-xs shadow-md"
                                     x-text="message.sender.name.charAt(0).toUpperCase()"></div>
                                
                                <!-- Message Bubble -->
                                <div class="group relative">
                                    <div class="px-4 py-3 rounded-2xl shadow-sm" 
                                         :class="message.is_mine ? 'bg-gradient-to-r from-blue-600 to-blue-700 text-white' : 'bg-white dark:bg-zinc-700 text-zinc-900 dark:text-zinc-100 border border-zinc-200 dark:border-zinc-600'">
                                        
                                        <!-- Reply context -->
                                        <div x-show="message.reply_to" class="bg-black/10 rounded-lg p-2 mb-2 text-xs border-l-2 border-white/50">
                                            <p class="opacity-80" x-text="'Replying to: ' + (message.reply_to?.content || '')"></p>
                                        </div>
                                        
                                        <!-- Message content -->
                                        <div class="space-y-2">
                                            <p class="text-sm leading-relaxed" x-text="message.content"></p>
                                            
                                            <!-- Attachments -->
                                            <template x-if="message.attachments && message.attachments.length > 0">
                                                <div class="space-y-2 mt-2">
                                                    <template x-for="attachment in message.attachments" :key="attachment.id">
                                                        <div class="bg-black/10 rounded-lg p-2">
                                                            <div class="flex items-center space-x-2">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                                                </svg>
                                                                <span class="text-xs" x-text="attachment.filename"></span>
                                                                <button @click="downloadAttachment(attachment)" class="text-xs underline">Download</button>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                        
                                        <!-- Message footer -->
                                        <div class="flex items-center justify-between mt-2">
                                            <p class="text-xs opacity-70" x-text="formatMessageTime(message.created_at)"></p>
                                            <div class="flex items-center space-x-1">
                                                <span x-show="message.is_mine" class="text-xs opacity-70">
                                                    <span x-show="message.is_read" class="text-blue-300">✓✓</span>
                                                    <span x-show="!message.is_read" class="text-zinc-300">✓</span>
                                                </span>
                                                <button @click="showMessageActions(message)" class="opacity-0 group-hover:opacity-100 p-1 hover:bg-black/10 rounded transition-all">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
            
            <!-- Typing indicator -->
            <div x-show="isTyping" class="flex justify-start">
                <div class="flex items-end space-x-2">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-xs"
                         x-text="selectedConversation?.other_user.name.charAt(0).toUpperCase()"></div>
                    <div class="bg-white dark:bg-zinc-700 border border-zinc-200 dark:border-zinc-600 rounded-2xl px-4 py-3">
                        <div class="flex space-x-1">
                            <div class="w-2 h-2 bg-zinc-400 rounded-full animate-bounce"></div>
                            <div class="w-2 h-2 bg-zinc-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                            <div class="w-2 h-2 bg-zinc-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Load more messages -->
            <div x-show="hasMoreMessages" class="text-center py-4">
                <button @click="loadMoreMessages()" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                    Load earlier messages
                </button>
            </div>
        </div>
        
        <!-- Welcome State -->
        <div x-show="!selectedConversation" class="flex-1 flex items-center justify-center text-zinc-500 dark:text-zinc-400">
            <div class="text-center max-w-md">
                <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-blue-100 to-purple-100 dark:from-blue-900/20 dark:to-purple-900/20 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-3 text-zinc-900 dark:text-zinc-100">Welcome to Messages</h3>
                <p class="text-sm mb-6 leading-relaxed">Select a conversation from the sidebar to start messaging, or create a new conversation to connect with your colleagues.</p>
                <div class="space-y-3">
                    <div class="text-xs text-zinc-400 dark:text-zinc-500">
                        <p>💡 Tips: Use Ctrl+Enter to send messages, and right-click for more options</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Enhanced Message Input -->
        <div x-show="selectedConversation" class="border-t border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800">
            <!-- Reply context -->
            <div x-show="replyingTo" class="px-4 py-2 bg-zinc-50 dark:bg-zinc-700/50 border-b border-zinc-200 dark:border-zinc-600">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                        </svg>
                        <span class="text-sm text-zinc-600 dark:text-zinc-400">Replying to:</span>
                        <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100" x-text="replyingTo?.content?.substring(0, 50) + '...'"></span>
                    </div>
                    <button @click="replyingTo = null" class="p-1 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Input area -->
            <div class="p-4">
                <form @submit.prevent="sendMessage()" class="space-y-3">
                    <!-- File attachment preview -->
                    <div x-show="attachments.length > 0" class="flex flex-wrap gap-2">
                        <template x-for="(attachment, index) in attachments" :key="index">
                            <div class="flex items-center space-x-2 bg-zinc-100 dark:bg-zinc-700 rounded-lg px-3 py-2">
                                <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                </svg>
                                <span class="text-sm" x-text="attachment.name"></span>
                                <button @click="removeAttachment(index)" type="button" class="p-1 text-zinc-400 hover:text-red-500">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>
                    
                    <!-- Main input -->
                    <div class="flex items-end space-x-3">
                        <!-- Attachment button -->
                        <div class="relative">
                            <input type="file" x-ref="fileInput" @change="handleFileSelect" multiple class="hidden" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.txt">
                            <button @click="$refs.fileInput.click()" type="button" class="p-2 text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200 hover:bg-zinc-100 dark:hover:bg-zinc-700 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <!-- Text input -->
                        <div class="flex-1 relative">
                            <textarea x-model="newMessage" 
                                      @keydown="handleKeydown($event)"
                                      @input="handleTyping()"
                                      x-ref="messageInput"
                                      class="w-full px-4 py-3 text-sm border border-zinc-200 dark:border-zinc-600 rounded-2xl bg-zinc-50 dark:bg-zinc-700 focus:ring-2 focus:ring-blue-500 focus:border-transparent text-zinc-900 dark:text-zinc-100 resize-none transition-all" 
                                      placeholder="Type a message... (Ctrl+Enter to send)" 
                                      rows="1"
                                      style="max-height: 120px;"></textarea>
                            
                            <!-- Emoji picker trigger -->
                            <button @click="showEmojiPicker = !showEmojiPicker" type="button" class="absolute right-3 top-3 p-1 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <!-- Send button -->
                        <button type="submit" 
                                :disabled="!newMessage.trim() && attachments.length === 0"
                                class="p-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 disabled:from-zinc-300 disabled:to-zinc-400 dark:disabled:from-zinc-600 dark:disabled:to-zinc-700 text-white rounded-full transition-all shadow-lg disabled:shadow-none transform hover:scale-105 disabled:scale-100"
                                :class="{ 'animate-pulse': sending }">
                            <svg x-show="!sending" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            <svg x-show="sending" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Quick actions -->
                    <div class="flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400">
                        <div class="flex items-center space-x-4">
                            <span>Shift+Enter for new line</span>
                            <span x-show="newMessage.length > 0" x-text="newMessage.length + '/2000'"></span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button @click="startVoiceRecording()" type="button" class="p-1 hover:text-zinc-700 dark:hover:text-zinc-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function messagingApp() {
    return {
        // Core data
        conversations: [],
        selectedConversation: null,
        messages: [],
        newMessage: '',
        searchQuery: '',
        chatSearchQuery: '',
        loading: false,
        sending: false,
        
        // UI states
        showNewMessageModal: false,
        showChatSearch: false,
        showEmojiPicker: false,
        activeFilter: 'all',
        
        // Advanced features
        replyingTo: null,
        attachments: [],
        isTyping: false,
        hasMoreMessages: false,
        highlightedMessageId: null,
        typingTimeout: null,
        lastSeenMessageId: null,
        
        // New message modal
        newMessageRecipient: '',
        newMessageContent: '',
        
        init() {
            this.loadConversations();
            this.startPolling();
            this.setupKeyboardShortcuts();
            this.autoResizeTextarea();
        },
        
        // Computed properties
        get filteredConversations() {
            let filtered = this.conversations;
            
            // Apply search filter
            if (this.searchQuery) {
                filtered = filtered.filter(conv => 
                    conv.other_user.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                    conv.last_message?.content.toLowerCase().includes(this.searchQuery.toLowerCase())
                );
            }
            
            // Apply active filter
            if (this.activeFilter === 'unread') {
                filtered = filtered.filter(conv => conv.unread_count > 0);
            }
            
            return filtered;
        },
        
        get totalUnreadCount() {
            return this.conversations.reduce((total, conv) => total + conv.unread_count, 0);
        },
        
        get groupedMessages() {
            const groups = {};
            this.messages.forEach(message => {
                const date = new Date(message.created_at).toDateString();
                if (!groups[date]) {
                    groups[date] = [];
                }
                groups[date].push(message);
            });
            return groups;
        },
        
        // Conversation management
        async loadConversations() {
            if (this.loading) return;
            this.loading = true;
            
            try {
                const response = await fetch('/api/conversations');
                const data = await response.json();
                if (data.success) {
                    this.conversations = data.conversations || [];
                }
            } catch (error) {
                console.error('Failed to load conversations:', error);
                this.showNotification('Failed to load conversations', 'error');
            } finally {
                this.loading = false;
            }
        },
        
        async selectConversation(conversation) {
            this.selectedConversation = conversation;
            this.messages = [];
            await this.loadMessages(conversation.other_user_id);
            this.scrollToBottom();
            this.resetChatState();
            
            // Mark conversation as read
            conversation.unread_count = 0;
        },
        
        async loadMessages(userId, page = 1) {
            try {
                const response = await fetch(`/api/messages/${userId}?page=${page}`);
                const data = await response.json();
                if (data.success) {
                    if (page === 1) {
                        this.messages = data.data || [];
                    } else {
                        this.messages = [...data.data, ...this.messages];
                    }
                    this.hasMoreMessages = data.pagination?.has_more || false;
                }
            } catch (error) {
                console.error('Failed to load messages:', error);
                this.showNotification('Failed to load messages', 'error');
            }
        },
        
        async loadMoreMessages() {
            if (!this.hasMoreMessages || this.loading) return;
            
            const currentPage = Math.ceil(this.messages.length / 30) + 1;
            await this.loadMessages(this.selectedConversation.other_user_id, currentPage);
        },
        
        // Message sending
        async sendMessage() {
            if ((!this.newMessage.trim() && this.attachments.length === 0) || !this.selectedConversation || this.sending) {
                return;
            }
            
            const messageContent = this.newMessage.trim();
            const attachmentsToSend = [...this.attachments];
            const replyTo = this.replyingTo;
            
            // Clear input immediately for better UX
            this.newMessage = '';
            this.attachments = [];
            this.replyingTo = null;
            this.sending = true;
            
            try {
                const formData = new FormData();
                formData.append('content', messageContent);
                
                if (replyTo) {
                    formData.append('reply_to_id', replyTo.id);
                }
                
                // Add attachments
                attachmentsToSend.forEach((file, index) => {
                    formData.append(`attachments[${index}]`, file);
                });
                
                const response = await fetch(`/api/messages/${this.selectedConversation.other_user_id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });
                
                const data = await response.json();
                if (data.success) {
                    this.messages.push(data.message);
                    this.scrollToBottom();
                    await this.loadConversations();
                    this.showNotification('Message sent!', 'success');
                } else {
                    throw new Error(data.error || 'Failed to send message');
                }
            } catch (error) {
                console.error('Failed to send message:', error);
                // Restore message on error
                this.newMessage = messageContent;
                this.attachments = attachmentsToSend;
                this.replyingTo = replyTo;
                this.showNotification('Failed to send message', 'error');
            } finally {
                this.sending = false;
            }
        },
        
        async startNewConversation() {
            if (!this.newMessageRecipient.trim() || !this.newMessageContent.trim()) return;
            
            try {
                const response = await fetch('/api/messages/new', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        recipient: this.newMessageRecipient,
                        content: this.newMessageContent
                    })
                });
                
                const data = await response.json();
                if (data.success) {
                    this.showNewMessageModal = false;
                    this.newMessageRecipient = '';
                    this.newMessageContent = '';
                    await this.loadConversations();
                    
                    // Auto-select the new conversation
                    const newConv = this.conversations.find(c => c.other_user_id === data.user_id);
                    if (newConv) {
                        this.selectConversation(newConv);
                    }
                    
                    this.showNotification('Message sent!', 'success');
                }
            } catch (error) {
                console.error('Failed to start conversation:', error);
                this.showNotification('Failed to send message', 'error');
            }
        },
        
        // File handling
        handleFileSelect(event) {
            const files = Array.from(event.target.files);
            const maxSize = 25 * 1024 * 1024; // 25MB
            const maxFiles = 5;
            
            const validFiles = files.filter(file => {
                if (file.size > maxSize) {
                    this.showNotification(`File ${file.name} is too large (max 25MB)`, 'error');
                    return false;
                }
                return true;
            });
            
            if (this.attachments.length + validFiles.length > maxFiles) {
                this.showNotification(`Maximum ${maxFiles} files allowed`, 'error');
                return;
            }
            
            this.attachments = [...this.attachments, ...validFiles];
            event.target.value = ''; // Reset input
        },
        
        removeAttachment(index) {
            this.attachments.splice(index, 1);
        },
        
        downloadAttachment(attachment) {
            window.open(attachment.file_path, '_blank');
        },
        
        // Keyboard shortcuts and input handling
        handleKeydown(event) {
            if (event.key === 'Enter') {
                if (event.ctrlKey || event.metaKey) {
                    event.preventDefault();
                    this.sendMessage();
                } else if (!event.shiftKey) {
                    event.preventDefault();
                    this.sendMessage();
                }
            }
            
            if (event.key === 'Escape') {
                this.replyingTo = null;
                this.showChatSearch = false;
            }
        },
        
        handleTyping() {
            // Auto-resize textarea
            this.$nextTick(() => {
                const textarea = this.$refs.messageInput;
                if (textarea) {
                    textarea.style.height = 'auto';
                    textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
                }
            });
            
            // Typing indicator (simplified)
            clearTimeout(this.typingTimeout);
            this.typingTimeout = setTimeout(() => {
                // Stop typing indicator
            }, 1000);
        },
        
        setupKeyboardShortcuts() {
            document.addEventListener('keydown', (event) => {
                // Ctrl/Cmd + K for search
                if ((event.ctrlKey || event.metaKey) && event.key === 'k') {
                    event.preventDefault();
                    this.$refs.searchInput?.focus();
                }
                
                // Escape to close modals
                if (event.key === 'Escape') {
                    this.showNewMessageModal = false;
                    this.showEmojiPicker = false;
                }
            });
        },
        
        autoResizeTextarea() {
            this.$watch('newMessage', () => {
                this.handleTyping();
            });
        },
        
        // Search and filtering
        debouncedSearch() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                // Perform search
            }, 300);
        },
        
        toggleSearchInChat() {
            this.showChatSearch = !this.showChatSearch;
            if (this.showChatSearch) {
                this.$nextTick(() => {
                    this.$refs.chatSearchInput?.focus();
                });
            }
        },
        
        // Message actions
        showMessageActions(message) {
            // Context menu for message actions
            console.log('Message actions for:', message.id);
        },
        
        replyToMessage(message) {
            this.replyingTo = message;
            this.$refs.messageInput?.focus();
        },
        
        // Conversation actions
        showConversationMenu(event, conversation) {
            // Right-click context menu
            console.log('Conversation menu for:', conversation.id);
        },
        
        viewProfile() {
            if (this.selectedConversation) {
                window.open(`/u/${this.selectedConversation.other_user.username}`, '_blank');
            }
        },
        
        muteConversation() {
            // Implement mute functionality
            this.showNotification('Conversation muted', 'success');
        },
        
        clearHistory() {
            if (confirm('Are you sure you want to clear this conversation history?')) {
                // Implement clear history
                this.showNotification('History cleared', 'success');
            }
        },
        
        blockUser() {
            if (confirm('Are you sure you want to block this user?')) {
                // Implement block functionality
                this.showNotification('User blocked', 'success');
            }
        },
        
        // Voice recording (placeholder)
        startVoiceRecording() {
            this.showNotification('Voice recording coming soon!', 'info');
        },
        
        toggleCallModal() {
            this.showNotification('Video calling coming soon!', 'info');
        },
        
        // Utility functions
        formatTime(timestamp) {
            return new Date(timestamp).toLocaleTimeString('en-US', { 
                hour: 'numeric', 
                minute: '2-digit',
                hour12: true 
            });
        },
        
        formatRelativeTime(timestamp) {
            const now = new Date();
            const date = new Date(timestamp);
            const diffInSeconds = Math.floor((now - date) / 1000);
            
            if (diffInSeconds < 60) return 'now';
            if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)}m`;
            if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)}h`;
            if (diffInSeconds < 604800) return `${Math.floor(diffInSeconds / 86400)}d`;
            
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        },
        
        formatDate(dateString) {
            const date = new Date(dateString);
            const today = new Date();
            const yesterday = new Date(today.getTime() - 86400000);
            
            if (date.toDateString() === today.toDateString()) {
                return 'Today';
            } else if (date.toDateString() === yesterday.toDateString()) {
                return 'Yesterday';
            } else {
                return date.toLocaleDateString('en-US', { 
                    weekday: 'long', 
                    month: 'short', 
                    day: 'numeric' 
                });
            }
        },
        
        formatMessageTime(timestamp) {
            return new Date(timestamp).toLocaleTimeString('en-US', { 
                hour: 'numeric', 
                minute: '2-digit',
                hour12: true 
            });
        },
        
        scrollToBottom() {
            this.$nextTick(() => {
                if (this.$refs.messagesContainer) {
                    this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight;
                }
            });
        },
        
        handleScroll(event) {
            const container = event.target;
            if (container.scrollTop === 0 && this.hasMoreMessages) {
                this.loadMoreMessages();
            }
        },
        
        resetChatState() {
            this.replyingTo = null;
            this.showChatSearch = false;
            this.chatSearchQuery = '';
            this.highlightedMessageId = null;
        },
        
        showNotification(message, type = 'info') {
            // Simple notification system
            console.log(`[${type.toUpperCase()}] ${message}`);
            
            // You can integrate with a toast notification library here
            const colors = {
                success: 'bg-green-500',
                error: 'bg-red-500',
                info: 'bg-blue-500',
                warning: 'bg-yellow-500'
            };
            
            // Create and show notification
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 ${colors[type]} text-white px-4 py-2 rounded-lg shadow-lg z-50 transition-all duration-300`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        },
        
        // Real-time WebSocket/Broadcasting Features
        connectionStatus: 'connecting',
        connectionStatusText: 'Connecting...',
        onlineUsersCount: 0,
        refreshing: false,
        echo: null,
        presenceChannel: null,
        
        // Initialize real-time features
        initializeRealTime() {
            try {
                // Initialize Laravel Echo for real-time features
                if (typeof Echo !== 'undefined') {
                    this.echo = window.Echo;
                    this.setupPresenceChannel();
                    this.setupMessageListeners();
                    this.connectionStatus = 'connected';
                    this.connectionStatusText = 'Connected';
                } else {
                    console.warn('Laravel Echo not available, falling back to polling');
                    this.startPolling();
                    this.connectionStatus = 'disconnected';
                    this.connectionStatusText = 'Offline Mode';
                }
            } catch (error) {
                console.error('Failed to initialize real-time features:', error);
                this.startPolling();
                this.connectionStatus = 'disconnected';
                this.connectionStatusText = 'Connection Failed';
            }
        },
        
        setupPresenceChannel() {
            if (!this.echo) return;
            
            try {
                this.presenceChannel = this.echo.join('messaging.presence')
                    .here((users) => {
                        this.onlineUsersCount = users.length;
                        console.log('Currently online users:', users.length);
                    })
                    .joining((user) => {
                        this.onlineUsersCount++;
                        this.updateUserOnlineStatus(user.id, true);
                        console.log('User joined:', user.name);
                    })
                    .leaving((user) => {
                        this.onlineUsersCount--;
                        this.updateUserOnlineStatus(user.id, false);
                        console.log('User left:', user.name);
                    })
                    .error((error) => {
                        console.error('Presence channel error:', error);
                        this.connectionStatus = 'disconnected';
                        this.connectionStatusText = 'Connection Error';
                    });
            } catch (error) {
                console.error('Failed to setup presence channel:', error);
            }
        },
        
        setupMessageListeners() {
            if (!this.echo) return;
            
            // Listen for new messages
            this.echo.private(`messages.${this.getCurrentUserId()}`)
                .listen('MessageSent', (event) => {
                    this.handleNewMessage(event.message);
                })
                .listen('MessageRead', (event) => {
                    this.handleMessageRead(event.messageId);
                })
                .listen('UserTyping', (event) => {
                    this.handleUserTyping(event.userId, event.isTyping);
                });
        },
        
        handleNewMessage(message) {
            // Add message to current conversation if it matches
            if (this.selectedConversation && 
                (message.sender_id === this.selectedConversation.other_user_id || 
                 message.recipient_id === this.selectedConversation.other_user_id)) {
                this.messages.push(this.transformMessage(message));
                this.scrollToBottom();
                
                // Mark as read if conversation is active
                if (message.sender_id === this.selectedConversation.other_user_id) {
                    this.markMessageAsRead(message.id);
                }
            }
            
            // Update conversations list
            this.refreshConversations();
            
            // Show notification if not in current conversation
            if (!this.selectedConversation || 
                message.sender_id !== this.selectedConversation.other_user_id) {
                this.showDesktopNotification(message);
            }
        },
        
        handleMessageRead(messageId) {
            // Update message read status in current conversation
            const messageIndex = this.messages.findIndex(m => m.id === messageId);
            if (messageIndex !== -1) {
                this.messages[messageIndex].is_read = true;
            }
        },
        
        handleUserTyping(userId, isTyping) {
            if (this.selectedConversation && userId === this.selectedConversation.other_user_id) {
                this.isTyping = isTyping;
                
                if (isTyping) {
                    // Auto-hide typing indicator after 3 seconds
                    setTimeout(() => {
                        this.isTyping = false;
                    }, 3000);
                }
            }
        },
        
        updateUserOnlineStatus(userId, isOnline) {
            // Update online status in conversations
            this.conversations.forEach(conv => {
                if (conv.other_user_id === userId) {
                    conv.other_user.is_online = isOnline;
                }
            });
            
            // Update selected conversation
            if (this.selectedConversation && this.selectedConversation.other_user_id === userId) {
                this.selectedConversation.other_user.is_online = isOnline;
            }
        },
        
        // Advanced messaging features
        async markMessageAsRead(messageId) {
            try {
                await fetch(`/api/messages/${messageId}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                });
            } catch (error) {
                console.error('Failed to mark message as read:', error);
            }
        },
        
        async refreshConversations() {
            if (this.refreshing) return;
            
            this.refreshing = true;
            try {
                await this.loadConversations();
            } finally {
                this.refreshing = false;
            }
        },
        
        transformMessage(message) {
            return {
                id: message.id,
                content: message.message_content || message.content,
                sender_id: message.sender_id,
                recipient_id: message.recipient_id,
                is_mine: message.sender_id === this.getCurrentUserId(),
                is_read: message.is_read,
                created_at: message.created_at,
                sender: message.sender || {
                    id: message.sender_id,
                    name: 'Unknown User'
                },
                attachments: message.attachments || []
            };
        },
        
        getCurrentUserId() {
            // Get current user ID from meta tag or global variable
            const userMeta = document.querySelector('meta[name="user-id"]');
            return userMeta ? parseInt(userMeta.content) : null;
        },
        
        // Desktop notifications
        async requestNotificationPermission() {
            if ('Notification' in window) {
                const permission = await Notification.requestPermission();
                return permission === 'granted';
            }
            return false;
        },
        
        showDesktopNotification(message) {
            if ('Notification' in window && Notification.permission === 'granted') {
                const notification = new Notification(`New message from ${message.sender?.name || 'Unknown'}`, {
                    body: message.message_content || message.content,
                    icon: '/favicon.ico',
                    tag: `message-${message.id}`
                });
                
                notification.onclick = () => {
                    window.focus();
                    // Find and select the conversation
                    const conversation = this.conversations.find(c => 
                        c.other_user_id === message.sender_id
                    );
                    if (conversation) {
                        this.selectConversation(conversation);
                    }
                    notification.close();
                };
                
                // Auto-close after 5 seconds
                setTimeout(() => notification.close(), 5000);
            }
        },
        
        // Enhanced typing indicators
        sendTypingIndicator() {
            if (this.selectedConversation && this.echo) {
                this.echo.private(`messages.${this.selectedConversation.other_user_id}`)
                    .whisper('typing', {
                        user_id: this.getCurrentUserId(),
                        is_typing: true
                    });
            }
        },
        
        stopTypingIndicator() {
            if (this.selectedConversation && this.echo) {
                this.echo.private(`messages.${this.selectedConversation.other_user_id}`)
                    .whisper('typing', {
                        user_id: this.getCurrentUserId(),
                        is_typing: false
                    });
            }
        },
        
        // Professional message handling
        async sendMessageWithOptimisticUI() {
            if ((!this.newMessage.trim() && this.attachments.length === 0) || !this.selectedConversation || this.sending) {
                return;
            }
            
            const messageContent = this.newMessage.trim();
            const attachmentsToSend = [...this.attachments];
            const tempId = 'temp_' + Date.now();
            
            // Optimistic UI - add message immediately
            const optimisticMessage = {
                id: tempId,
                content: messageContent,
                sender_id: this.getCurrentUserId(),
                recipient_id: this.selectedConversation.other_user_id,
                is_mine: true,
                is_read: false,
                created_at: new Date().toISOString(),
                sender: {
                    id: this.getCurrentUserId(),
                    name: 'You'
                },
                attachments: [],
                sending: true
            };
            
            this.messages.push(optimisticMessage);
            this.scrollToBottom();
            
            // Clear input immediately
            this.newMessage = '';
            this.attachments = [];
            this.replyingTo = null;
            this.sending = true;
            
            try {
                const formData = new FormData();
                formData.append('content', messageContent);
                
                // Add attachments
                attachmentsToSend.forEach((file, index) => {
                    formData.append(`attachments[${index}]`, file);
                });
                
                const response = await fetch(`/api/messages/${this.selectedConversation.other_user_id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });
                
                const data = await response.json();
                if (data.success) {
                    // Replace optimistic message with real message
                    const messageIndex = this.messages.findIndex(m => m.id === tempId);
                    if (messageIndex !== -1) {
                        this.messages[messageIndex] = this.transformMessage(data.message);
                    }
                    
                    // Refresh conversations to update last message
                    this.refreshConversations();
                } else {
                    throw new Error(data.error || 'Failed to send message');
                }
            } catch (error) {
                console.error('Failed to send message:', error);
                
                // Remove optimistic message and restore input
                const messageIndex = this.messages.findIndex(m => m.id === tempId);
                if (messageIndex !== -1) {
                    this.messages.splice(messageIndex, 1);
                }
                
                this.newMessage = messageContent;
                this.attachments = attachmentsToSend;
                this.showNotification('Failed to send message', 'error');
            } finally {
                this.sending = false;
            }
        },
        
        // Enhanced typing handler with real-time indicators
        handleTypingEnhanced() {
            this.handleTyping(); // Original resize logic
            
            // Send typing indicator
            this.sendTypingIndicator();
            
            // Clear typing timeout and set new one
            clearTimeout(this.typingTimeout);
            this.typingTimeout = setTimeout(() => {
                this.stopTypingIndicator();
            }, 1000);
        },
        
        // Connection management
        handleConnectionLost() {
            this.connectionStatus = 'disconnected';
            this.connectionStatusText = 'Disconnected';
            
            // Try to reconnect
            setTimeout(() => {
                this.attemptReconnection();
            }, 3000);
        },
        
        attemptReconnection() {
            this.connectionStatus = 'connecting';
            this.connectionStatusText = 'Reconnecting...';
            
            try {
                this.initializeRealTime();
            } catch (error) {
                console.error('Reconnection failed:', error);
                this.connectionStatus = 'disconnected';
                this.connectionStatusText = 'Connection Failed';
            }
        },
        
        // Enhanced initialization
        init() {
            this.loadConversations();
            this.initializeRealTime();
            this.setupKeyboardShortcuts();
            this.autoResizeTextarea();
            this.requestNotificationPermission();
            
            // Override sendMessage to use optimistic UI
            this.sendMessage = this.sendMessageWithOptimisticUI;
            
            // Override handleTyping to include real-time indicators
            this.handleTyping = this.handleTypingEnhanced;
        },
        
        startPolling() {
            // Fallback polling for when WebSocket is not available
            setInterval(() => {
                if (this.selectedConversation && !this.sending) {
                    this.loadMessages(this.selectedConversation.other_user_id);
                }
                if (!this.loading) {
                    this.loadConversations();
                }
            }, 3000); // More frequent polling as fallback
        },
        
        // Cleanup on page unload
        cleanup() {
            if (this.presenceChannel) {
                this.echo.leave('messaging.presence');
            }
            
            if (this.selectedConversation && this.echo) {
                this.echo.leave(`messages.${this.getCurrentUserId()}`);
            }
        }
    }
}

// Initialize cleanup on page unload
window.addEventListener('beforeunload', () => {
    if (window.messagingAppInstance) {
        window.messagingAppInstance.cleanup();
    }
});
</script>
@endpush
@endsection
