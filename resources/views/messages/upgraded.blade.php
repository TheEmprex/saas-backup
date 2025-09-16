@extends('layouts.app')

@section('title', 'Messages')

@push('styles')
<style>
    /* Custom scrollbar */
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
    
    /* Message animations */
    .message-enter {
        opacity: 0;
        transform: translateY(10px);
    }
    .message-enter-active {
        opacity: 1;
        transform: translateY(0);
        transition: opacity 0.3s, transform 0.3s;
    }
    
    /* Typing animation */
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
    
    /* Message status */
    .message-status {
        font-size: 10px;
        opacity: 0.6;
    }
    
    /* Conversation item hover */
    .conversation-item:hover {
        background-color: #f8fafc;
        transform: translateX(2px);
        transition: all 0.2s;
    }
    
    /* Online indicator pulse */
    .online-pulse {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
        70% { box-shadow: 0 0 0 4px rgba(34, 197, 94, 0); }
        100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
    }
</style>
@endpush

@section('content')
<div id="messaging-app" 
     x-data="messagingApp()" 
     x-init="init()"
     class="h-screen bg-gradient-to-br from-blue-50 to-indigo-100 overflow-hidden">
    
    <!-- Loading State -->
    <div x-show="loading" x-transition class="fixed inset-0 bg-white bg-opacity-90 flex items-center justify-center z-50">
        <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <p class="text-gray-700 font-medium">Loading your conversations...</p>
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
                            <h1 class="text-xl font-bold">Messages</h1>
                            <p class="text-blue-200 text-sm" x-text="`${conversations.length} conversations`"></p>
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
                             :class="{'bg-blue-50 border-r-3 border-blue-500 shadow-sm': selectedConversation?.id === conversation.id}"
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
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Welcome to Messages</h3>
                    <p class="text-gray-500 mb-4">Select a conversation from the sidebar to start messaging.</p>
                    <button @click="showUserSearch = true" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Start New Conversation
                    </button>
                </div>
            </div>

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
                                <!-- Typing indicator -->
                                <div x-show="typingUsers[selectedConversation?.id]?.length > 0" 
                                     class="flex items-center text-sm text-blue-600">
                                    <div class="typing-dots mr-2">
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                    </div>
                                    <span x-text="getTypingText(selectedConversation?.id)"></span>
                                </div>
                                <!-- Online status -->
                                <span x-show="!typingUsers[selectedConversation?.id]?.length && selectedConversation?.is_online" 
                                      class="text-sm text-green-600 flex items-center">
                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                                    Online
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
                        <button @click="showConversationInfo = !showConversationInfo" 
                                class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-50 transition-colors rounded-full">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Messages Area -->
            <div x-show="selectedConversation" class="flex-1 overflow-y-auto custom-scrollbar px-6 py-4 space-y-4 bg-gray-50" x-ref="messagesContainer">
                <div x-show="loadingMessages" class="flex justify-center py-4">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500"></div>
                </div>

                <!-- Load more messages -->
                <div x-show="hasMoreMessages && !loadingMessages" class="text-center py-2">
                    <button @click="loadMoreMessages" 
                            class="px-4 py-2 text-blue-600 hover:text-blue-800 font-medium transition-colors">
                        Load previous messages
                    </button>
                </div>

                <template x-for="message in messages" :key="message.id">
                    <div :class="message.is_mine ? 'flex justify-end' : 'flex justify-start'" class="group">
                        <div :class="message.is_mine ? 'bg-blue-600 text-white ml-4' : 'bg-white text-gray-900 mr-4'" 
                             class="max-w-xs lg:max-w-md px-4 py-3 rounded-2xl shadow-sm relative">
                            
                            <!-- Reply to -->
                            <div x-show="message.reply_to" 
                                 class="mb-2 p-2 rounded-lg bg-black bg-opacity-10 text-sm border-l-2 border-current">
                                <p class="font-medium text-xs opacity-75" x-text="message.reply_to?.sender_name"></p>
                                <p class="truncate" x-text="message.reply_to?.content"></p>
                            </div>

                            <!-- Message content -->
                            <div>
                                <!-- Text message -->
                                <div x-show="message.message_type === 'text'" 
                                     x-text="message.content"
                                     class="break-words"></div>
                                
                                <!-- File message -->
                                <div x-show="message.file_url">
                                    <!-- Image -->
                                    <template x-if="message.message_type === 'image'">
                                        <img :src="message.file_url" :alt="message.file_name" 
                                             class="max-w-full h-auto rounded-lg cursor-pointer"
                                             @click="showImageModal(message.file_url)">
                                    </template>
                                    
                                    <!-- Other files -->
                                    <template x-if="['file', 'audio', 'video'].includes(message.message_type)">
                                        <div class="flex items-center space-x-3 p-3 rounded-lg bg-black bg-opacity-10">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <div class="flex-1 min-w-0">
                                                <p class="font-medium truncate" x-text="message.file_name"></p>
                                                <p class="text-xs opacity-75" x-text="message.formatted_file_size"></p>
                                            </div>
                                            <a :href="message.file_url" download 
                                               class="p-2 rounded-full hover:bg-black hover:bg-opacity-10">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Message footer -->
                            <div class="flex items-end justify-between mt-2 text-xs opacity-75">
                                <div class="flex items-center space-x-2">
                                    <span x-text="formatTime(message.created_at)"></span>
                                    <span x-show="message.edited_at" class="italic">(edited)</span>
                                </div>
                                
                                <!-- Message status for sent messages -->
                                <div x-show="message.is_mine" class="flex items-center space-x-1">
                                    <!-- Reactions -->
                                    <div x-show="message.reactions && Object.keys(message.reactions).length > 0" 
                                         class="flex space-x-1 mr-2">
                                        <template x-for="[emoji, count] in Object.entries(message.reactions)" :key="emoji">
                                            <span class="px-1 py-0.5 bg-black bg-opacity-10 rounded text-xs" x-text="`${emoji} ${count}`"></span>
                                        </template>
                                    </div>
                                    
                                    <!-- Read status -->
                                    <div class="message-status">
                                        <template x-if="message.is_read">
                                            <svg class="w-4 h-4 text-blue-300" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </template>
                                        <template x-if="!message.is_read">
                                            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick reaction buttons (show on hover) -->
                            <div class="absolute -top-2 right-0 opacity-0 group-hover:opacity-100 transition-opacity">
                                <div class="flex space-x-1 bg-white border border-gray-200 rounded-full px-2 py-1 shadow-sm">
                                    <template x-for="emoji in ['👍', '❤️', '😂', '😮']" :key="emoji">
                                        <button @click="addReaction(message.id, emoji)"
                                                class="hover:scale-110 transition-transform"
                                                x-text="emoji"></button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Message Input -->
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

                    <!-- Message input -->
                    <div class="flex-1 relative">
                        <textarea x-model="newMessage" 
                                  @keydown.enter.exact.prevent="sendMessage"
                                  @keydown.shift.enter="$event.target.value += '\n'"
                                  @input="handleTyping"
                                  :disabled="sendingMessage"
                                  placeholder="Type a message..." 
                                  rows="1"
                                  class="w-full resize-none rounded-2xl border border-gray-300 px-4 py-3 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed max-h-32 scrollbar-hide"
                                  x-ref="messageInput"></textarea>
                        
                        <!-- File previews -->
                        <div x-show="selectedFiles.length > 0" class="mt-2 flex flex-wrap gap-2">
                            <template x-for="(file, index) in selectedFiles" :key="index">
                                <div class="relative bg-gray-100 rounded-lg p-2 flex items-center space-x-2">
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

    <!-- Notification Toast -->
    <div x-show="notification.show" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-2"
         class="fixed top-4 right-4 z-50">
        <div :class="notification.type === 'error' ? 'bg-red-500' : 'bg-green-500'" 
             class="text-white px-6 py-3 rounded-lg shadow-lg">
            <p x-text="notification.message"></p>
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
        userSearchQuery: '',
        userSearchResults: [],
        showUserSearch: false,
        typingUsers: {},
        hasMoreMessages: false,
        currentPage: 1,
        imageModal: { show: false, src: '' },
        notification: { show: false, message: '', type: 'info' },
        typingTimer: null,
        realTimeEnabled: false,
        
        async init() {
            await this.loadConversations();
            this.setupRealTime();
            this.loading = false;
        },

        async loadConversations() {
            try {
                const response = await fetch('/messages/conversations');
                if (!response.ok) throw new Error('Failed to load conversations');
                
                const data = await response.json();
                this.conversations = data.conversations || [];
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
            
            // Mark as read
            this.markConversationAsRead(conversation.id);
        },

        async loadMessages() {
            if (!this.selectedConversation) return;
            
            this.loadingMessages = true;
            try {
                const response = await fetch(`/messages/conversations/${this.selectedConversation.id}/messages?page=${this.currentPage}`);
                if (!response.ok) throw new Error('Failed to load messages');
                
                const data = await response.json();
                if (this.currentPage === 1) {
                    this.messages = data.messages || [];
                } else {
                    this.messages = [...(data.messages || []), ...this.messages];
                }
                
                this.hasMoreMessages = data.pagination?.has_more || false;
                
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
            
            this.selectedFiles.forEach((file, index) => {
                formData.append('file', file);
            });
            
            try {
                const response = await fetch('/messages/send', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });
                
                if (!response.ok) throw new Error('Failed to send message');
                
                const data = await response.json();
                
                if (data.message) {
                    // Add message with animation
                    this.messages.push(data.message);
                    this.newMessage = '';
                    this.selectedFiles = [];
                    this.scrollToBottom();
                    
                    // Update conversation in list
                    await this.loadConversations();
                }
            } catch (error) {
                console.error('Error sending message:', error);
                this.showNotification('Failed to send message', 'error');
            }
            this.sendingMessage = false;
        },

        async searchUsers() {
            if (this.userSearchQuery.length < 2) {
                this.userSearchResults = [];
                return;
            }
            
            try {
                const response = await fetch(`/messages/search-users?q=${encodeURIComponent(this.userSearchQuery)}`, { credentials: 'same-origin' });
                if (!response.ok) throw new Error('Search failed');
                
                const data = await response.json();
                this.userSearchResults = data.users || [];
            } catch (error) {
                console.error('Search error:', error);
                this.userSearchResults = [];
            }
        },

        async startConversation(user) {
            try {
                const response = await fetch('/messages/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        recipient_id: user.id,
                        content: `Hello ${user.name}! 👋`
                    })
                });
                
                if (!response.ok) throw new Error('Failed to start conversation');
                
                await this.loadConversations();
                this.userSearchQuery = '';
                this.userSearchResults = [];
                this.showUserSearch = false;
                
                // Find and select the new conversation
                const newConv = this.conversations.find(c => c.other_user_id === user.id);
                if (newConv) {
                    await this.selectConversation(newConv);
                }
                
                this.showNotification('Conversation started!', 'success');
            } catch (error) {
                console.error('Error starting conversation:', error);
                this.showNotification('Failed to start conversation', 'error');
            }
        },

        async addReaction(messageId, emoji) {
            try {
                const response = await fetch(`/messages/messages/${messageId}/reaction`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ emoji })
                });
                
                if (!response.ok) throw new Error('Failed to add reaction');
                
                const data = await response.json();
                if (data.reactions) {
                    // Update message reactions in UI
                    const messageIndex = this.messages.findIndex(m => m.id === messageId);
                    if (messageIndex !== -1) {
                        this.messages[messageIndex].reactions = data.reactions;
                    }
                }
            } catch (error) {
                console.error('Error adding reaction:', error);
            }
        },

        handleFileSelect(event) {
            this.selectedFiles = Array.from(event.target.files);
        },

        removeFile(index) {
            this.selectedFiles.splice(index, 1);
        },

        async handleTyping() {
            if (!this.selectedConversation) return;
            
            if (this.typingTimer) clearTimeout(this.typingTimer);
            
            await this.updateTypingStatus(true);
            
            this.typingTimer = setTimeout(() => {
                this.updateTypingStatus(false);
            }, 3000);
        },

        async updateTypingStatus(isTyping) {
            if (!this.selectedConversation) return;
            
            try {
                await fetch('/messages/typing', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        conversation_id: this.selectedConversation.id,
                        is_typing: isTyping
                    })
                });
            } catch (error) {
                console.error('Error updating typing status:', error);
            }
        },

        async markConversationAsRead(conversationId) {
            // Update unread count locally
            const convIndex = this.conversations.findIndex(c => c.id === conversationId);
            if (convIndex !== -1) {
                this.conversations[convIndex].unread_count = 0;
            }
        },

        showImageModal(src) {
            this.imageModal.src = src;
            this.imageModal.show = true;
        },

        initiateCall(type) {
            this.showNotification(`${type === 'video' ? 'Video' : 'Audio'} call feature coming soon!`, 'info');
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const container = this.$refs.messagesContainer;
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        },

        setupRealTime() {
            // TODO: Setup Laravel Echo WebSocket connection
            // For now, use polling every 3 seconds for real-time updates
            setInterval(() => {
                if (this.selectedConversation && !this.loadingMessages) {
                    this.loadMessages();
                }
                this.loadConversations();
            }, 3000);
        },

        searchConversations() {
            // Filtering handled by computed property
        },

        formatTime(timestamp) {
            const date = new Date(timestamp);
            const now = new Date();
            const diffInHours = (now - date) / (1000 * 60 * 60);
            
            if (diffInHours < 1) {
                return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            } else if (diffInHours < 24) {
                return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            } else {
                return date.toLocaleDateString();
            }
        },

        getTypingText(conversationId) {
            const users = this.typingUsers[conversationId] || [];
            if (users.length === 0) return '';
            if (users.length === 1) return `${users[0]} is typing`;
            return `${users.length} people are typing`;
        },

        get filteredConversations() {
            if (!this.searchQuery) return this.conversations;
            return this.conversations.filter(conv => 
                conv.title.toLowerCase().includes(this.searchQuery.toLowerCase())
            );
        },

        showNotification(message, type = 'info') {
            this.notification = { show: true, message, type };
            setTimeout(() => {
                this.notification.show = false;
            }, 3000);
        }
    }))
})
</script>
@endpush

@endsection
