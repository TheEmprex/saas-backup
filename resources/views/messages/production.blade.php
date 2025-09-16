@extends('layouts.app')

@section('title', 'Messages')

@section('content')
<div id="messaging-app" class="h-screen bg-gray-50 overflow-hidden">
    <!-- Loading State -->
    <div x-show="loading" x-transition class="fixed inset-0 bg-white bg-opacity-75 flex items-center justify-center z-50">
        <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto mb-4"></div>
            <p class="text-gray-600">Loading messages...</p>
        </div>
    </div>

    <!-- Main Layout -->
    <div x-show="!loading" class="flex h-full">
        <!-- Sidebar -->
        <div class="w-80 bg-white border-r border-gray-200 flex flex-col h-full">
            <!-- Header -->
            <div class="flex-shrink-0 bg-blue-600 p-4 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <img src="/images/onlyverified-logo.svg" alt="OnlyVerified Logo" class="h-8 w-8 mr-3" />
                        <h1 class="text-xl font-semibold">Messages</h1>
                    </div>
                    <button @click="showNewConversation = !showNewConversation" 
                            class="p-2 rounded-full hover:bg-blue-700 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </button>
                </div>
                
                <!-- Search -->
                <div class="mt-4 relative">
                    <input type="text" 
                           x-model="searchQuery" 
                           @input="searchUsers"
                           placeholder="Search conversations or users..."
                           class="w-full bg-blue-500 bg-opacity-50 text-white placeholder-blue-200 rounded-lg px-4 py-2 focus:outline-none focus:bg-white focus:text-gray-900 focus:placeholder-gray-500 transition-all">
                    <svg class="absolute right-3 top-2.5 w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Search Results -->
            <div x-show="searchQuery.length > 0 && searchResults.length > 0" 
                 x-transition 
                 class="flex-shrink-0 border-b border-gray-200 max-h-48 overflow-y-auto">
                <div class="p-2">
                    <p class="text-xs text-gray-500 uppercase tracking-wide px-2 py-1">Search Results</p>
                    <template x-for="user in searchResults" :key="user.id">
                        <button @click="startConversation(user)" 
                                class="w-full flex items-center p-2 rounded hover:bg-gray-100 transition-colors text-left">
                            <img :src="user.avatar || '/images/default-avatar.png'" 
                                 :alt="user.name" 
                                 class="w-8 h-8 rounded-full mr-3 object-cover">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate" x-text="user.name"></p>
                                <p class="text-xs text-gray-500 truncate" x-text="user.user_type"></p>
                            </div>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Conversations List -->
            <div class="flex-1 overflow-y-auto">
                <div class="p-2">
                    <template x-for="conversation in filteredConversations" :key="conversation.id">
                        <div @click="selectConversation(conversation)" 
                             :class="{'bg-blue-50 border-r-2 border-blue-500': selectedConversation?.id === conversation.id}"
                             class="flex items-center p-3 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors mb-1">
                            <div class="relative">
                                <img :src="conversation.avatar || '/images/default-avatar.png'" 
                                     :alt="conversation.title" 
                                     class="w-12 h-12 rounded-full object-cover">
                                <div x-show="conversation.is_online" 
                                     class="absolute bottom-0 right-0 w-3 h-3 bg-green-400 border-2 border-white rounded-full"></div>
                            </div>
                            <div class="ml-3 flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <img src="/images/onlyverified-logo.svg" alt="OnlyVerified" class="h-4 w-4 mr-1" />
                                        <p class="text-sm font-semibold text-gray-900 truncate" x-text="conversation.title"></p>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <span x-show="conversation.unread_count > 0" 
                                              class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-500 rounded-full"
                                              x-text="conversation.unread_count"></span>
                                        <span class="text-xs text-gray-500" x-text="formatTime(conversation.last_activity)"></span>
                                    </div>
                                </div>
                                <div x-show="conversation.last_message" class="flex items-center mt-1">
                                    <p class="text-sm text-gray-600 truncate" x-text="conversation.last_message?.content || 'No messages yet'"></p>
                                </div>
                                <!-- Typing indicator -->
                                <div x-show="typingUsers[conversation.id]?.length > 0" 
                                     class="flex items-center mt-1 text-xs text-blue-500">
                                    <div class="flex space-x-1 mr-2">
                                        <div class="w-1 h-1 bg-blue-500 rounded-full animate-bounce"></div>
                                        <div class="w-1 h-1 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                        <div class="w-1 h-1 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                    </div>
                                    <span x-text="getTypingText(conversation.id)"></span>
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
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="flex-1 flex flex-col h-full bg-white">
            <!-- No Conversation Selected -->
            <div x-show="!selectedConversation" class="flex-1 flex items-center justify-center bg-gray-50">
                <div class="text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.955 8.955 0 01-4.126-.98L3 21l1.98-5.126A8.955 8.955 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"/>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Select a conversation</h3>
                    <p class="mt-2 text-sm text-gray-500">Choose a conversation from the sidebar to start messaging.</p>
                </div>
            </div>

            <!-- Conversation Header -->
            <div x-show="selectedConversation" class="flex-shrink-0 bg-white border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <img :src="selectedConversation?.avatar || '/images/default-avatar.png'" 
                             :alt="selectedConversation?.title" 
                             class="w-10 h-10 rounded-full object-cover">
                        <div class="ml-3">
                            <div class="flex items-center">
                                <img src="/images/onlyverified-logo.svg" alt="OnlyVerified" class="h-5 w-5 mr-2" />
                                <h2 class="text-lg font-semibold text-gray-900" x-text="selectedConversation?.title"></h2>
                            </div>
                            <div class="flex items-center">
                                <div x-show="typingUsers[selectedConversation?.id]?.length > 0" 
                                     class="flex items-center text-sm text-blue-600">
                                    <div class="flex space-x-1 mr-2">
                                        <div class="w-1 h-1 bg-blue-500 rounded-full animate-bounce"></div>
                                        <div class="w-1 h-1 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                        <div class="w-1 h-1 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                    </div>
                                    <span x-text="getTypingText(selectedConversation?.id)"></span>
                                </div>
                                <span x-show="!typingUsers[selectedConversation?.id]?.length && selectedConversation?.is_online" 
                                      class="text-sm text-green-600">Online</span>
                                <span x-show="!typingUsers[selectedConversation?.id]?.length && !selectedConversation?.is_online" 
                                      class="text-sm text-gray-500">Offline</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex items-center space-x-2">
                        <button @click="initiateCall('audio')" 
                                class="p-2 text-gray-400 hover:text-blue-600 transition-colors rounded-full hover:bg-gray-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </button>
                        <button @click="initiateCall('video')" 
                                class="p-2 text-gray-400 hover:text-blue-600 transition-colors rounded-full hover:bg-gray-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Messages Area -->
            <div x-show="selectedConversation" class="flex-1 overflow-y-auto px-6 py-4 space-y-4" x-ref="messagesContainer">
                <div x-show="loadingMessages" class="flex justify-center py-4">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500"></div>
                </div>

                <template x-for="message in messages" :key="message.id">
                    <div :class="message.is_mine ? 'flex justify-end' : 'flex justify-start'">
                        <div :class="message.is_mine ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-900'" 
                             class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg shadow">
                            <!-- Reply to -->
                            <div x-show="message.reply_to" 
                                 class="mb-2 p-2 rounded bg-black bg-opacity-10 text-sm">
                                <p class="font-medium" x-text="message.reply_to?.user_name"></p>
                                <p class="truncate opacity-75" x-text="message.reply_to?.content"></p>
                            </div>

                            <!-- Message content -->
                            <div>
                                <!-- Text message -->
                                <div x-show="message.type === 'text'" x-text="message.content"></div>
                                
                                <!-- Image message -->
                                <div x-show="message.type === 'image'">
                                    <template x-for="attachment in message.attachments" :key="attachment.path">
                                        <img :src="attachment.url" :alt="attachment.name" 
                                             class="max-w-full h-auto rounded cursor-pointer"
                                             @click="showImageModal(attachment.url)">
                                    </template>
                                </div>
                                
                                <!-- File message -->
                                <div x-show="['file', 'audio', 'video'].includes(message.type)">
                                    <template x-for="attachment in message.attachments" :key="attachment.path">
                                        <div class="flex items-center space-x-2 p-2 rounded bg-black bg-opacity-10">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <div class="flex-1 min-w-0">
                                                <p class="font-medium truncate" x-text="attachment.name"></p>
                                                <p class="text-sm opacity-75" x-text="formatFileSize(attachment.size)"></p>
                                            </div>
                                            <a :href="attachment.url" download 
                                               class="p-1 rounded hover:bg-black hover:bg-opacity-10">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Message info -->
                            <div class="flex items-center justify-between mt-2 text-xs opacity-75">
                                <div class="flex items-center space-x-2">
                                    <span x-text="formatTime(message.created_at)"></span>
                                    <span x-show="message.is_edited" class="italic">(edited)</span>
                                </div>
                                <div class="flex items-center space-x-1">
                                    <!-- Reactions -->
                                    <div x-show="message.reactions?.length > 0" class="flex space-x-1">
                                        <template x-for="reaction in message.reactions" :key="reaction.emoji">
                                            <button @click="toggleReaction(message.id, reaction.emoji)"
                                                    :class="reaction.reacted_by_me ? 'bg-blue-200 text-blue-800' : 'bg-gray-200 text-gray-700'"
                                                    class="px-1 py-0.5 rounded text-xs hover:bg-opacity-75">
                                                <span x-text="reaction.emoji"></span>
                                                <span x-text="reaction.count"></span>
                                            </button>
                                        </template>
                                    </div>
                                    <!-- Add reaction -->
                                    <button @click="showEmojiPicker = message.id" 
                                            class="opacity-0 group-hover:opacity-100 hover:bg-black hover:bg-opacity-10 rounded p-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Quick emoji reactions -->
                            <div x-show="showEmojiPicker === message.id" 
                                 x-transition
                                 class="mt-2 flex space-x-1">
                                <template x-for="emoji in quickEmojis" :key="emoji">
                                    <button @click="addReaction(message.id, emoji); showEmojiPicker = null"
                                            class="hover:bg-black hover:bg-opacity-10 rounded p-1 text-lg"
                                            x-text="emoji"></button>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Load more messages -->
                <div x-show="hasMoreMessages" class="text-center py-4">
                    <button @click="loadMoreMessages" 
                            :disabled="loadingMessages"
                            class="px-4 py-2 text-blue-600 hover:text-blue-800 disabled:opacity-50">
                        Load previous messages
                    </button>
                </div>
            </div>

            <!-- Message Input -->
            <div x-show="selectedConversation" class="flex-shrink-0 bg-white border-t border-gray-200 px-6 py-4">
                <div class="flex items-end space-x-3">
                    <!-- File upload -->
                    <div class="relative">
                        <input type="file" multiple accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.txt" 
                               x-ref="fileInput" @change="handleFileSelect" class="hidden">
                        <button @click="$refs.fileInput.click()" 
                                class="p-2 text-gray-400 hover:text-blue-600 transition-colors rounded-full hover:bg-gray-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Message input -->
                    <div class="flex-1 relative">
                        <textarea x-model="newMessage" 
                                  @keydown.enter.exact.prevent="sendMessage"
                                  @keydown.shift.enter="$event.target.value += '\\n'"
                                  @input="handleTyping"
                                  :disabled="sendingMessage"
                                  placeholder="Type a message..." 
                                  rows="1"
                                  class="w-full resize-none rounded-lg border border-gray-300 px-4 py-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"></textarea>
                        
                        <!-- File previews -->
                        <div x-show="selectedFiles.length > 0" class="mt-2 flex flex-wrap gap-2">
                            <template x-for="(file, index) in selectedFiles" :key="index">
                                <div class="relative bg-gray-100 rounded p-2 flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span class="text-sm text-gray-700" x-text="file.name"></span>
                                    <button @click="removeFile(index)" 
                                            class="text-red-500 hover:text-red-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Send button -->
                    <button @click="sendMessage" 
                            :disabled="(!newMessage.trim() && selectedFiles.length === 0) || sendingMessage"
                            class="p-2 bg-blue-600 text-white rounded-full hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        <svg x-show="!sendingMessage" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

    <!-- Image Modal -->
    <div x-show="imageModal.show" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50"
         @click="imageModal.show = false">
        <div class="relative max-w-4xl max-h-full p-4">
            <img :src="imageModal.src" class="max-w-full max-h-full rounded-lg">
            <button @click="imageModal.show = false" 
                    class="absolute top-2 right-2 text-white hover:text-gray-300 bg-black bg-opacity-50 rounded-full p-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('messagingApp', () => ({
        loading: true,
        loadingMessages: false,
        sendingMessage: false,
        conversations: [],
        selectedConversation: null,
        messages: [],
        newMessage: '',
        selectedFiles: [],
        searchQuery: '',
        searchResults: [],
        typingUsers: {},
        hasMoreMessages: false,
        currentPage: 1,
        showEmojiPicker: null,
        quickEmojis: ['😀', '😂', '❤️', '👍', '👎', '😮', '😢', '😡'],
        imageModal: {
            show: false,
            src: ''
        },
        typingTimer: null,
        
        async init() {
            await this.loadConversations();
            this.setupEventListeners();
            this.loading = false;
        },

        formatMessage(msg) {
            return {
                id: msg.id,
                content: msg.content,
                type: msg.type || msg.message_type || 'text',
                message_type: msg.message_type || msg.type || 'text',
                created_at: msg.created_at,
                is_mine: !!msg.is_mine || (msg.sender_id && (msg.sender_id === (this.currentUserId || 0))),
                sender_id: msg.sender_id,
                sender_name: msg.sender_name || (msg.sender && msg.sender.name) || 'User',
                attachments: msg.file_url ? [{ url: msg.file_url, name: msg.file_name || 'file', size: msg.file_size || 0, path: msg.file_url }] : (msg.attachments || []),
                reactions: msg.reactions || [],
                reply_to: msg.reply_to || null
            };
        },

        async loadConversations() {
            try {
                const { data } = await window.apiFetch('/messages/conversations');
                this.conversations = data?.conversations || data?.data || data || [];
            } catch (error) {
                console.error('Error loading conversations:', error);
                this.showNotification('Failed to load conversations', 'error');
            }
        },

        async selectConversation(conversation) {
            this.selectedConversation = conversation;
            this.messages = [];
            this.currentPage = 1;
            this.hasMoreMessages = false;
            await this.loadMessages();
        },

        async loadMessages() {
            if (!this.selectedConversation) return;
            
            this.loadingMessages = true;
            try {
                const { data } = await window.apiFetch(`/messages/conversations/${this.selectedConversation.id}/messages?page=${this.currentPage}`);
                const list = (data?.messages || data?.data || []);
                if (this.currentPage === 1) {
                    this.messages = list.map(m => this.formatMessage(m));
                } else {
                    this.messages = [...list.map(m => this.formatMessage(m)), ...this.messages];
                }
                
                this.hasMoreMessages = (data?.pagination && typeof data.pagination.has_more !== 'undefined') ? !!data.pagination.has_more : (list.length > 0);
                
                this.$nextTick(() => {
                    if (this.currentPage === 1) {
                        this.scrollToBottom();
                    }
                });
            } catch (error) {
                console.error('Error loading messages:', error);
                this.showNotification('Failed to load messages', 'error');
            }
            this.loadingMessages = false;
        },

        async loadMoreMessages() {
            this.currentPage++;
            await this.loadMessages();
        },

        async sendMessage() {
            if ((!this.newMessage.trim() && this.selectedFiles.length === 0) || this.sendingMessage) return;
            
            this.sendingMessage = true;
            const formData = new FormData();
            
            if (this.selectedConversation) {
                formData.append('conversation_id', this.selectedConversation.id);
            }
            
            if (this.newMessage.trim()) {
                formData.append('content', this.newMessage.trim());
            }
            
            // Add files
            this.selectedFiles.forEach((file) => {
                formData.append('files[]', file);
            });
            
            try {
                const { data } = await window.apiFetch('/messages/send', {
                    method: 'POST',
                    body: formData
                });
                const created = this.formatMessage(data?.message || data);
                this.messages.push(created);
                this.newMessage = '';
                this.selectedFiles = [];
                this.scrollToBottom();
                
                // Update conversation in list
                const convIndex = this.conversations.findIndex(c => c.id === this.selectedConversation.id);
                if (convIndex !== -1) {
                    this.conversations[convIndex].last_message = created;
                    this.conversations[convIndex].last_activity = created.created_at || new Date().toISOString();
                }
            } catch (error) {
                console.error('Error sending message:', error);
                this.showNotification('Failed to send message', 'error');
            }
            this.sendingMessage = false;
        },

        async searchUsers() {
            if (this.searchQuery.length < 2) {
                this.searchResults = [];
                return;
            }
            
            try {
                const { data } = await window.apiFetch(`/messages/search-users?q=${encodeURIComponent(this.searchQuery)}`);
                this.searchResults = (data?.data || data || []);
            } catch (error) {
                console.error('Search error:', error);
                this.searchResults = [];
            }
        },

        async startConversation(user) {
            try {
                await window.apiFetch('/messages/send', {
                    method: 'POST',
                    body: {
                        recipient_id: user.id,
                        content: 'Hello! 👋'
                    }
                });
                
                await this.loadConversations();
                this.searchQuery = '';
                this.searchResults = [];
                
                // Find and select the new conversation
                const newConv = this.conversations.find(c => c.other_user_id === user.id);
                if (newConv) {
                    await this.selectConversation(newConv);
                }
            } catch (error) {
                console.error('Error starting conversation:', error);
                this.showNotification('Failed to start conversation', 'error');
            }
        },

        async toggleReaction(messageId, emoji) {
            try {
                await window.apiFetch(`/messages/messages/${messageId}/reaction`, {
                    method: 'POST',
                    body: { emoji }
                });
                // Update message reactions in UI
                const messageIndex = this.messages.findIndex(m => m.id === messageId);
                if (messageIndex !== -1) {
                    await this.loadMessages(); // Reload to get updated reactions
                }
            } catch (error) {
                console.error('Error toggling reaction:', error);
                this.showNotification('Failed to add reaction', 'error');
            }
        },

        async addReaction(messageId, emoji) {
            await this.toggleReaction(messageId, emoji);
        },

        handleFileSelect(event) {
            this.selectedFiles = Array.from(event.target.files);
        },

        removeFile(index) {
            this.selectedFiles.splice(index, 1);
        },

        async handleTyping() {
            if (!this.selectedConversation) return;
            
            // Clear previous timer
            if (this.typingTimer) {
                clearTimeout(this.typingTimer);
            }
            
            // Send typing start
            await this.updateTypingStatus(true);
            
            // Set timer to stop typing after 3 seconds
            this.typingTimer = setTimeout(() => {
                this.updateTypingStatus(false);
            }, 3000);
        },

        async updateTypingStatus(isTyping) {
            if (!this.selectedConversation) return;
            
            try {
                await window.apiFetch('/messages/typing', {
                    method: 'POST',
                    body: {
                        conversation_id: this.selectedConversation.id,
                        is_typing: isTyping
                    }
                });
            } catch (error) {
                console.error('Error updating typing status:', error);
            }
        },

        showImageModal(src) {
            this.imageModal.src = src;
            this.imageModal.show = true;
        },

        initiateCall(type) {
            alert(`${type === 'video' ? 'Video' : 'Audio'} call feature coming soon!`);
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const container = this.$refs.messagesContainer;
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        },

        setupEventListeners() {
            // Setup WebSocket or polling for real-time updates here
            // For now, we'll use polling every 5 seconds
            setInterval(() => {
                if (this.selectedConversation) {
                    this.loadMessages();
                }
            }, 5000);
        },

        formatTime(timestamp) {
            return new Date(timestamp).toLocaleTimeString([], { 
                hour: '2-digit', 
                minute: '2-digit' 
            });
        },

        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        },

        getTypingText(conversationId) {
            const users = this.typingUsers[conversationId] || [];
            if (users.length === 0) return '';
            if (users.length === 1) return `${users[0]} is typing...`;
            return `${users.length} people are typing...`;
        },

        get filteredConversations() {
            if (!this.searchQuery) return this.conversations;
            return this.conversations.filter(conv => 
                conv.title.toLowerCase().includes(this.searchQuery.toLowerCase())
            );
        },

        showNotification(message, type = 'info') {
            // Implementation for showing notifications
            console.log(`${type}: ${message}`);
        }
    }))
})
</script>
@endpush
@endsection
