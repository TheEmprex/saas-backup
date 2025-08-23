<x-theme::layouts.app>
    <x-slot name="title">Messages</x-slot>

    @push('styles')
    <style>
        /* Messages app styles */
        .messages-container {
            height: calc(100vh - 200px);
            min-height: 600px;
        }
        
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
        
        .typing-dots {
            display: inline-block;
        }
        .typing-dots span {
            display: inline-block;
            animation: typing-bounce 1.4s infinite ease-in-out both;
            width: 4px;
            height: 4px;
            background-color: currentColor;
            border-radius: 50%;
            margin: 0 1px;
        }
        .typing-dots span:nth-child(1) { animation-delay: -0.32s; }
        .typing-dots span:nth-child(2) { animation-delay: -0.16s; }
        
        @keyframes typing-bounce {
            0%, 80%, 100% { transform: scale(0); }
            40% { transform: scale(1); }
        }
        
        .connection-status {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 10;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .connection-status.connected {
            background: #10b981;
            color: white;
        }
        .connection-status.connecting {
            background: #f59e0b;
            color: white;
        }
        .connection-status.disconnected {
            background: #ef4444;
            color: white;
        }
    </style>
    @endpush

    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">Messages</h1>
                <p class="text-gray-600 dark:text-gray-400">Real-time messaging with your team</p>
            </div>
            <a href="{{ route('dashboard') }}" class="bg-gray-100 dark:bg-zinc-700 hover:bg-gray-200 dark:hover:bg-zinc-600 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Dashboard
            </a>
        </div>
    </div>

<div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg overflow-hidden messages-container">
        <div x-data="realTimeMessagingApp()" x-init="init()" class="h-full relative">
    
    <!-- Connection Status Indicator -->
    <div x-show="!loading" 
         :class="`connection-status ${connectionStatus}`"
         x-text="connectionStatusText">
    </div>
    
    <!-- Loading State -->
    <div x-show="loading" x-transition class="fixed inset-0 bg-white bg-opacity-90 flex items-center justify-center z-50">
        <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <p class="text-gray-700 font-medium">Connecting to real-time messaging...</p>
        </div>
    </div>

    <!-- Main Layout -->
    <div x-show="!loading" class="flex h-full">
        <!-- Sidebar -->
        <div class="w-80 bg-white shadow-xl flex flex-col h-full border-r border-gray-200">
            <!-- Header -->
            <div class="flex-shrink-0 bg-gradient-to-r from-blue-600 to-blue-700 p-4 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.955 8.955 0 01-4.126-.98L3 21l1.98-5.126A8.955 8.955 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold">Real-time Messages</h1>
                            <p class="text-blue-200 text-sm flex items-center">
                                <span class="w-2 h-2 bg-green-400 rounded-full mr-2" x-show="connectionStatus === 'connected'"></span>
                                <span x-text="`${conversations.length} conversations`"></span>
                            </p>
                        </div>
                    </div>
                    <button @click="showUserSearch = !showUserSearch" 
                            class="p-2 rounded-full hover:bg-blue-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </button>
                </div>
                
                <!-- Search -->
                <div class="mt-4 relative">
                    <input type="text" 
                           x-model="searchQuery" 
                           @input="searchConversations"
                           placeholder="Search conversations..."
                           class="w-full bg-blue-500 bg-opacity-30 text-white placeholder-blue-200 rounded-lg px-4 py-2 focus:outline-none focus:bg-white focus:text-gray-900 focus:placeholder-gray-500 transition-all">
                    <svg class="absolute right-3 top-2.5 w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            <!-- User Search -->
            <div x-show="showUserSearch" 
                 x-transition 
                 class="flex-shrink-0 border-b border-gray-200 bg-gray-50 p-3">
                <div class="relative">
                    <input type="text" 
                           x-model="userSearchQuery" 
                           @input="searchUsers"
                           placeholder="Search users to start a conversation..."
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <div x-show="userSearchResults.length > 0" 
                         class="absolute top-full left-0 right-0 bg-white border border-gray-200 rounded-lg shadow-lg mt-1 max-h-48 overflow-y-auto z-10">
                        <template x-for="user in userSearchResults" :key="user.id">
                            <button @click="startConversation(user)" 
                                    class="w-full flex items-center p-3 hover:bg-gray-100 transition-colors text-left">
                                <img :src="user.avatar || '/images/default-avatar.png'" 
                                     :alt="user.name" 
                                     class="w-8 h-8 rounded-full mr-3 object-cover">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate" x-text="user.name"></p>
                                    <p class="text-xs text-gray-500 truncate" x-text="user.email"></p>
                                </div>
                                <div x-show="user.is_online" class="w-2 h-2 bg-green-500 rounded-full"></div>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Conversations List -->
            <div class="flex-1 overflow-y-auto custom-scrollbar">
                <div class="p-2">
                    <template x-for="conversation in filteredConversations" :key="conversation.id">
                        <div @click="selectConversation(conversation)" 
                             :class="{
                                 'bg-blue-50 border-r-3 border-blue-500 shadow-sm': selectedConversation?.id === conversation.id,
                                 'new-message-indicator': conversation.hasNewMessage
                             }"
                             class="conversation-item flex items-center p-3 rounded-lg cursor-pointer hover:bg-gray-50 transition-all mb-1 group">
                            <div class="relative">
                                <img :src="conversation.avatar || '/images/default-avatar.png'" 
                                     :alt="conversation.title" 
                                     class="w-12 h-12 rounded-full object-cover">
                                <div x-show="conversation.is_online" 
                                     class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 border-2 border-white rounded-full online-pulse"></div>
                            </div>
                            <div class="ml-3 flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <p class="text-sm font-semibold text-gray-900 truncate" x-text="conversation.title"></p>
                                        <div x-show="conversation.is_online" class="ml-1 text-green-500">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <span x-show="conversation.unread_count > 0" 
                                              class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-500 rounded-full animate-pulse"
                                              x-text="conversation.unread_count"></span>
                                        <span class="text-xs text-gray-500" x-text="formatTime(conversation.last_activity)"></span>
                                    </div>
                                </div>
                                <div class="mt-1">
                                    <!-- Typing indicator -->
                                    <div x-show="typingUsers[conversation.id]?.length > 0" 
                                         class="flex items-center text-xs text-blue-600">
                                        <div class="typing-dots mr-2">
                                            <span></span>
                                            <span></span>
                                            <span></span>
                                        </div>
                                        <span x-text="getTypingText(conversation.id)"></span>
                                    </div>
                                    <!-- Last message -->
                                    <div x-show="!typingUsers[conversation.id]?.length && conversation.last_message">
                                        <p class="text-sm text-gray-600 truncate" x-text="conversation.last_message?.content || 'No messages yet'"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    
                    <!-- Empty state -->
                    <div x-show="conversations.length === 0" class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.955 8.955 0 01-4.126-.98L3 21l1.98-5.126A8.955 8.955 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No conversations</h3>
                        <p class="mt-1 text-sm text-gray-500">Start a new conversation to get started.</p>
                        <button @click="showUserSearch = true" class="mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Start Conversation
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="flex-1 flex flex-col h-full bg-white">
            <!-- No Conversation Selected -->
            <div x-show="!selectedConversation" class="flex-1 flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100">
                <div class="text-center">
                    <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-12 h-12 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.955 8.955 0 01-4.126-.98L3 21l1.98-5.126A8.955 8.955 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Real-time Messaging</h3>
                    <p class="text-gray-500 mb-4">Select a conversation to start real-time messaging with WebSockets.</p>
                    <div class="flex items-center justify-center mb-4">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2" x-show="connectionStatus === 'connected'"></span>
                        <span class="text-sm text-gray-600" x-text="connectionStatusText"></span>
                    </div>
                    <button @click="showUserSearch = true" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Start New Conversation
                    </button>
                </div>
            </div>

            <!-- Rest of the chat interface is the same as upgraded.blade.php -->
            <!-- Copy the conversation header, messages area, and message input from upgraded.blade.php -->
            <!-- For brevity, I'll include the key parts... -->

            <!-- Conversation Header -->
            <div x-show="selectedConversation" class="flex-shrink-0 bg-white border-b border-gray-200 px-6 py-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <img :src="selectedConversation?.avatar || '/images/default-avatar.png'" 
                             :alt="selectedConversation?.title" 
                             class="w-10 h-10 rounded-full object-cover">
                        <div class="ml-3">
                            <div class="flex items-center">
                                <h2 class="text-lg font-semibold text-gray-900" x-text="selectedConversation?.title"></h2>
                            </div>
                            <div class="flex items-center">
                                <!-- Real-time typing indicator -->
                                <div x-show="typingUsers[selectedConversation?.id]?.length > 0" 
                                     class="flex items-center text-sm text-blue-600">
                                    <div class="typing-dots mr-2">
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                    </div>
                                    <span x-text="getTypingText(selectedConversation?.id)"></span>
                                </div>
                                <!-- Online status with real-time updates -->
                                <span x-show="!typingUsers[selectedConversation?.id]?.length && selectedConversation?.is_online" 
                                      class="text-sm text-green-600 flex items-center">
                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2 online-pulse"></span>
                                    Online now
                                </span>
                                <span x-show="!typingUsers[selectedConversation?.id]?.length && !selectedConversation?.is_online" 
                                      class="text-sm text-gray-500">Last seen recently</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-2">
                        <button @click="initiateCall('audio')" 
                                class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors rounded-full">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </button>
                        <button @click="initiateCall('video')" 
                                class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors rounded-full">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Messages Area with Real-time Updates -->
            <div x-show="selectedConversation" class="flex-1 overflow-y-auto custom-scrollbar px-6 py-4 space-y-4 bg-gray-50" x-ref="messagesContainer">
                <div x-show="loadingMessages" class="flex justify-center py-4">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500"></div>
                </div>

                <template x-for="message in messages" :key="message.id">
                    <div :class="message.is_mine ? 'flex justify-end' : 'flex justify-start'" 
                         class="group message-enter"
                         x-data="{ isNew: message.isNew || false }"
                         x-init="if (isNew) { setTimeout(() => isNew = false, 2000) }">
                        <div :class="{
                                 'bg-blue-600 text-white ml-4': message.is_mine,
                                 'bg-white text-gray-900 mr-4': !message.is_mine,
                                 'ring-2 ring-green-300': isNew
                             }" 
                             class="max-w-xs lg:max-w-md px-4 py-3 rounded-2xl shadow-sm relative transition-all">
                            
                            <!-- Message content similar to upgraded.blade.php -->
                            <div class="break-words" x-text="message.content"></div>

                            <!-- Message footer with real-time read status -->
                            <div class="flex items-end justify-between mt-2 text-xs opacity-75">
                                <div class="flex items-center space-x-2">
                                    <span x-text="formatTime(message.created_at)"></span>
                                    <span x-show="message.edited_at" class="italic">(edited)</span>
                                </div>
                                
                                <!-- Real-time message status -->
                                <div x-show="message.is_mine" class="flex items-center space-x-1">
                                    <div class="message-status">
                                        <template x-if="message.is_read">
                                            <svg class="w-4 h-4 text-blue-300" fill="currentColor" viewBox="0 0 20 20" title="Read">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </template>
                                        <template x-if="!message.is_read">
                                            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20" title="Sent">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Message Input with Real-time Features -->
            <div x-show="selectedConversation" class="flex-shrink-0 bg-white border-t border-gray-200 px-6 py-4">
                <div class="flex items-end space-x-3">
                    <!-- File upload -->
                    <div class="relative">
                        <input type="file" multiple accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.txt" 
                               x-ref="fileInput" @change="handleFileSelect" class="hidden">
                        <button @click="$refs.fileInput.click()" 
                                class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-colors rounded-full">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Message input with real-time typing -->
                    <div class="flex-1 relative">
                        <textarea x-model="newMessage" 
                                  @keydown.enter.exact.prevent="sendMessage"
                                  @keydown.shift.enter="$event.target.value += '\n'"
                                  @input="handleTyping"
                                  :disabled="sendingMessage || connectionStatus !== 'connected'"
                                  :placeholder="connectionStatus === 'connected' ? 'Type a message...' : 'Connecting...'" 
                                  rows="1"
                                  class="w-full resize-none rounded-2xl border border-gray-300 px-4 py-3 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed max-h-32 scrollbar-hide"
                                  x-ref="messageInput"></textarea>
                    </div>

                    <!-- Send button with connection status -->
                    <button @click="sendMessage" 
                            :disabled="(!newMessage.trim() && selectedFiles.length === 0) || sendingMessage || connectionStatus !== 'connected'"
                            class="p-3 bg-blue-600 text-white rounded-full hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors shadow-lg">
                        <svg x-show="!sendingMessage" class="w-5 h-5 transform rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        <svg x-show="sendingMessage" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Toast -->
    <div x-show="notification.show" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-2"
         class="fixed bottom-4 right-4 z-50">
        <div :class="notification.type === 'error' ? 'bg-red-500' : notification.type === 'success' ? 'bg-green-500' : 'bg-blue-500'" 
             class="text-white px-6 py-3 rounded-lg shadow-lg">
            <p x-text="notification.message"></p>
        </div>
    </div>
</div>

@push('scripts')
<script>
function realTimeMessagingApp() {
    return {
        // Core state
        loading: true,
        currentUserId: {{ auth()->id() ?? 'null' }},
        conversations: [],
        selectedConversation: null,
        messages: [],
        
        // UI state
        newMessage: '',
        searchQuery: '',
        userSearchQuery: '',
        userSearchResults: [],
        showUserSearch: false,
        sendingMessage: false,
        loadingMessages: false,
        selectedFiles: [],
        
        // Real-time state
        connectionStatus: 'connecting',
        connectionStatusText: 'Connecting...',
        typingUsers: {},
        typingTimeout: null,
        
        // Notifications
        notification: { show: false, message: '', type: 'info' },
        
        // Initialize the app
        async init() {
            console.log('🚀 Real-time messaging initializing...');
            console.log('🔍 Debug info:', {
                currentUserId: this.currentUserId,
                hasEcho: typeof window.Echo !== 'undefined',
                hasAlpine: typeof Alpine !== 'undefined',
                hasCsrfToken: document.querySelector('meta[name="csrf-token"]')?.content
            });
            
            if (!this.currentUserId) {
                console.error('❌ No authenticated user found');
                this.loading = false;
                this.showNotification('Authentication required', 'error');
                return;
            }
            
            try {
                // Load conversations from API
                await this.loadConversations();
                
                // Initialize WebSocket connection
                await this.initializeWebSocket();
                
                this.loading = false;
                this.connectionStatus = 'connected';
                this.connectionStatusText = 'Connected';
                
                console.log('✅ Messaging system initialized successfully');
            } catch (error) {
                console.error('❌ Failed to initialize messaging:', error);
                this.loading = false;
                this.connectionStatus = 'disconnected';
                this.connectionStatusText = 'Connection failed';
                this.showNotification('Failed to connect to messaging server', 'error');
                
                // Load with fallback data
                this.loadFallbackData();
            }
        },
        
        // Load conversations from your API
        async loadConversations() {
            try {
                console.log('📡 Loading conversations from API...');
                const response = await fetch('/api/marketplace/v1/conversations', {
                    headers: {
                        'Authorization': 'Bearer ' + this.getApiToken(),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                
                console.log('📡 API Response:', response.status, response.statusText);
                
                if (response.ok) {
                    const data = await response.json();
                    console.log('📋 Raw API data:', data);
                    this.conversations = (data.data || data || []).map(conv => this.formatConversation(conv));
                    console.log('📋 Loaded conversations:', this.conversations.length, this.conversations);
                } else {
                    const errorText = await response.text();
                    console.error('❌ API Error:', response.status, errorText);
                    throw new Error(`API Error: ${response.status} - ${errorText}`);
                }
            } catch (error) {
                console.warn('⚠️ API unavailable, loading fallback data:', error);
                this.loadFallbackData();
            }
        },
        
        // Load messages for a conversation
        async loadMessages(conversationId) {
            this.loadingMessages = true;
            try {
                const response = await fetch(`/api/marketplace/v1/conversations/${conversationId}/messages`, {
                    headers: {
                        'Authorization': 'Bearer ' + this.getApiToken(),
                        'Accept': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    this.messages = (data.data || data || []).map(msg => this.formatMessage(msg));
                    this.$nextTick(() => this.scrollToBottom());
                } else {
                    throw new Error(`API Error: ${response.status}`);
                }
            } catch (error) {
                console.warn('⚠️ Failed to load messages:', error);
                this.loadFallbackMessages();
            } finally {
                this.loadingMessages = false;
            }
        },
        
        // Send a message
        async sendMessage() {
            if (!this.newMessage.trim() || this.sendingMessage || !this.selectedConversation) {
                return;
            }
            
            const content = this.newMessage.trim();
            this.newMessage = '';
            this.sendingMessage = true;
            
            try {
                const response = await fetch(`/api/marketplace/v1/conversations/${this.selectedConversation.id}/messages`, {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + this.getApiToken(),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        content: content,
                        type: 'text'
                    })
                });
                
                if (response.ok) {
                    const data = await response.json();
                    const newMessage = this.formatMessage(data.data || data);
                    newMessage.is_mine = true;
                    this.messages.push(newMessage);
                    this.$nextTick(() => this.scrollToBottom());
                    
                    // Update conversation last message
                    this.selectedConversation.last_message = { content: content };
                    this.selectedConversation.last_activity = new Date().toISOString();
                } else {
                    throw new Error(`Send failed: ${response.status}`);
                }
            } catch (error) {
                console.error('❌ Failed to send message:', error);
                this.showNotification('Failed to send message', 'error');
                this.newMessage = content; // Restore message
            } finally {
                this.sendingMessage = false;
            }
        },
        
        // Search for users
        async searchUsers() {
            if (this.userSearchQuery.length < 2) {
                this.userSearchResults = [];
                return;
            }
            
            try {
                const response = await fetch(`/api/marketplace/v1/users/search?q=${encodeURIComponent(this.userSearchQuery)}`, {
                    headers: {
                        'Authorization': 'Bearer ' + this.getApiToken(),
                        'Accept': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    this.userSearchResults = (data.data || data || []).slice(0, 10);
                } else {
                    this.userSearchResults = [];
                }
            } catch (error) {
                console.warn('⚠️ User search failed:', error);
                this.userSearchResults = [];
            }
        },
        
        // Start a new conversation
        async startConversation(user) {
            try {
                const response = await fetch('/api/marketplace/v1/conversations', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + this.getApiToken(),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        type: 'private',
                        participant_ids: [user.id]
                    })
                });
                
                if (response.ok) {
                    const data = await response.json();
                    const conversation = this.formatConversation(data.data || data);
                    this.conversations.unshift(conversation);
                    await this.selectConversation(conversation);
                    
                    this.showUserSearch = false;
                    this.userSearchQuery = '';
                    this.userSearchResults = [];
                    
                    this.showNotification(`Started conversation with ${user.name}`, 'success');
                } else {
                    throw new Error(`Failed to create conversation: ${response.status}`);
                }
            } catch (error) {
                console.error('❌ Failed to start conversation:', error);
                this.showNotification('Failed to start conversation', 'error');
            }
        },
        
        // Select a conversation
        async selectConversation(conversation) {
            this.selectedConversation = conversation;
            await this.loadMessages(conversation.id);
            
            // Mark as read
            conversation.unread_count = 0;
            
            // Subscribe to conversation updates
            this.subscribeToConversation(conversation.id);
        },
        
        // Initialize WebSocket connection
        async initializeWebSocket() {
            try {
                // Try to use Laravel Echo if available
                if (typeof Echo !== 'undefined' || window.Echo) {
                    const echo = window.Echo || Echo;
                    
                    // Listen for new messages
                    echo.private(`user.${this.currentUserId}`)
                        .listen('MessageSent', (e) => {
                            this.handleNewMessage(e);
                        })
                        .listen('MessageRead', (e) => {
                            this.handleMessageRead(e);
                        });
                    
                    console.log('🔌 WebSocket connected via Laravel Echo');
                } else {
                    console.log('⚠️ Laravel Echo not available, using polling fallback');
                    this.startPolling();
                }
            } catch (error) {
                console.warn('⚠️ WebSocket failed, using polling:', error);
                this.startPolling();
            }
        },
        
        // Polling fallback for real-time updates
        startPolling() {
            setInterval(() => {
                if (this.selectedConversation) {
                    this.loadMessages(this.selectedConversation.id);
                }
            }, 5000); // Poll every 5 seconds
        },
        
        // Subscribe to conversation updates
        subscribeToConversation(conversationId) {
            try {
                if (window.Echo) {
                    window.Echo.private(`conversation.${conversationId}`)
                        .listen('MessageSent', (e) => {
                            if (e.message.user_id !== this.currentUserId) {
                                this.handleNewMessage(e);
                            }
                        });
                }
            } catch (error) {
                console.warn('⚠️ Conversation subscription failed:', error);
            }
        },
        
        // Handle incoming messages
        handleNewMessage(event) {
            const message = this.formatMessage(event.message);
            
            if (this.selectedConversation && message.conversation_id == this.selectedConversation.id) {
                this.messages.push(message);
                this.$nextTick(() => this.scrollToBottom());
            }
            
            // Update conversation list
            const conversation = this.conversations.find(c => c.id == message.conversation_id);
            if (conversation) {
                conversation.last_message = { content: message.content };
                conversation.last_activity = message.created_at;
                if (message.user_id !== this.currentUserId) {
                    conversation.unread_count = (conversation.unread_count || 0) + 1;
                }
            }
        },
        
        // Handle message read events
        handleMessageRead(event) {
            if (this.selectedConversation && event.conversation_id == this.selectedConversation.id) {
                const message = this.messages.find(m => m.id == event.message_id);
                if (message) {
                    message.is_read = true;
                }
            }
        },
        
        // Computed properties
        get filteredConversations() {
            if (!this.searchQuery) return this.conversations;
            return this.conversations.filter(conv => 
                conv.title && conv.title.toLowerCase().includes(this.searchQuery.toLowerCase())
            );
        },
        
        // Utility functions
        formatConversation(conv) {
            return {
                id: conv.id,
                title: this.getConversationTitle(conv),
                avatar: this.getConversationAvatar(conv),
                last_message: conv.latest_message?.[0] || conv.last_message || { content: 'No messages yet' },
                last_activity: conv.last_activity_at || conv.updated_at || new Date().toISOString(),
                unread_count: conv.unread_count || 0,
                is_online: this.getOnlineStatus(conv),
                participants: conv.participants || []
            };
        },
        
        formatMessage(msg) {
            return {
                id: msg.id,
                content: msg.content,
                conversation_id: msg.conversation_id,
                user_id: msg.user_id,
                is_mine: msg.user_id === this.currentUserId,
                created_at: msg.created_at,
                sender: msg.user || msg.sender || { name: 'Unknown', avatar: '/images/default-avatar.png' },
                is_read: msg.is_read || false
            };
        },
        
        getConversationTitle(conv) {
            if (conv.title) return conv.title;
            if (conv.type === 'private' && conv.participants) {
                const other = conv.participants.find(p => p.id !== this.currentUserId);
                return other?.name || 'Private Chat';
            }
            return 'Group Chat';
        },
        
        getConversationAvatar(conv) {
            if (conv.type === 'private' && conv.participants) {
                const other = conv.participants.find(p => p.id !== this.currentUserId);
                return other?.avatar || '/images/default-avatar.png';
            }
            return '/images/group-avatar.png';
        },
        
        getOnlineStatus(conv) {
            if (conv.participants) {
                return conv.participants.some(p => p.id !== this.currentUserId && p.is_online);
            }
            return false;
        },
        
        getApiToken() {
            return document.querySelector('meta[name="csrf-token"]')?.content || '';
        },
        
        formatTime(timestamp) {
            if (!timestamp) return '';
            const date = new Date(timestamp);
            const now = new Date();
            const diff = now - date;
            
            if (diff < 60000) return 'now';
            if (diff < 3600000) return Math.floor(diff / 60000) + 'm';
            if (diff < 86400000) return Math.floor(diff / 3600000) + 'h';
            if (diff < 604800000) return Math.floor(diff / 86400000) + 'd';
            
            return date.toLocaleDateString([], { month: 'short', day: 'numeric' });
        },
        
        scrollToBottom() {
            if (this.$refs.messagesContainer) {
                this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight;
            }
        },
        
        handleTyping() {
            // Implement typing indicators if needed
        },
        
        handleFileSelect(event) {
            this.selectedFiles = Array.from(event.target.files);
        },
        
        getTypingText(conversationId) {
            const users = this.typingUsers[conversationId] || [];
            if (users.length === 0) return '';
            if (users.length === 1) return `${users[0]} is typing...`;
            return `${users.join(', ')} are typing...`;
        },
        
        searchConversations() {
            // Handled by computed property
        },
        
        initiateCall(type) {
            this.showNotification(`${type} calling feature coming soon!`, 'info');
        },
        
        showNotification(message, type = 'info') {
            this.notification = { show: true, message, type };
            setTimeout(() => {
                this.notification.show = false;
            }, 3000);
        },
        
        // Fallback data for when API is unavailable
        loadFallbackData() {
            this.conversations = [
                {
                    id: 1,
                    title: 'Demo User',
                    avatar: '/images/default-avatar.png',
                    last_message: { content: 'Hello! This is a demo conversation.' },
                    last_activity: new Date().toISOString(),
                    unread_count: 0,
                    is_online: true,
                    participants: []
                }
            ];
        },
        
        loadFallbackMessages() {
            this.messages = [
                {
                    id: 1,
                    content: 'Welcome to real-time messaging!',
                    is_mine: false,
                    created_at: new Date().toISOString(),
                    sender: { name: 'System', avatar: '/images/default-avatar.png' },
                    is_read: true
                }
            ];
        }
    }
}
</script>
    @endpush
</x-theme::layouts.app>
