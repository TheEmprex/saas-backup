<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Messages - {{ config('app.name', 'Laravel') }}</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Custom Styles -->
    <style>
        [x-cloak] { display: none !important; }
        
        /* Scrollbar styling */
        .scrollbar-thin::-webkit-scrollbar {
            width: 6px;
        }
        .scrollbar-thin::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 3px;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 3px;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.3);
        }
        
        /* Message animations */
        .message-enter {
            animation: messageEnter 0.3s ease-out;
        }
        
        @keyframes messageEnter {
            from {
                opacity: 0;
                transform: translateY(10px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        /* Typing indicator animation */
        .typing-dots {
            display: inline-block;
        }
        
        .typing-dots span {
            display: inline-block;
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background-color: #94a3b8;
            margin: 0 1px;
            animation: typingAnimation 1.4s infinite ease-in-out;
        }
        
        .typing-dots span:nth-child(1) { animation-delay: -0.32s; }
        .typing-dots span:nth-child(2) { animation-delay: -0.16s; }
        
        @keyframes typingAnimation {
            0%, 80%, 100% {
                transform: scale(0);
                opacity: 0.5;
            }
            40% {
                transform: scale(1);
                opacity: 1;
            }
        }
        
        /* Gradient backgrounds */
        .gradient-blue {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .gradient-green {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        }
        
        /* Message status indicators */
        .message-status {
            transition: all 0.2s ease;
        }
        
        /* Online status indicator */
        .online-dot {
            position: relative;
        }
        
        .online-dot::after {
            content: '';
            position: absolute;
            bottom: 0;
            right: 0;
            width: 12px;
            height: 12px;
            background: #10b981;
            border: 2px solid white;
            border-radius: 50%;
        }
        
        /* File drop zone */
        .file-drop-zone {
            border: 2px dashed #d1d5db;
            transition: all 0.2s ease;
        }
        
        .file-drop-zone.drag-over {
            border-color: #3b82f6;
            background-color: #eff6ff;
        }
        
        /* Custom scrollbar for messages */
        .messages-container {
            scrollbar-width: thin;
            scrollbar-color: rgba(0, 0, 0, 0.2) transparent;
        }
        
        .messages-container::-webkit-scrollbar {
            width: 8px;
        }
        
        .messages-container::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .messages-container::-webkit-scrollbar-thumb {
            background-color: rgba(0, 0, 0, 0.2);
            border-radius: 4px;
        }
        
        .messages-container::-webkit-scrollbar-thumb:hover {
            background-color: rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body class="h-full bg-gray-50 font-sans">
    <div class="h-full flex" x-data="messagingApp()" x-cloak>
        <!-- Sidebar - Conversations List -->
        <div class="w-1/3 bg-white border-r border-gray-200 flex flex-col">
            <!-- Header -->
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h1 class="text-xl font-semibold text-gray-900">Messages</h1>
                    <button 
                        @click="showNewConversation = true"
                        class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                        title="New conversation">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </button>
                </div>
                
                <!-- Search -->
                <div class="mt-4 relative">
                    <input 
                        type="text" 
                        x-model="searchQuery"
                        @input="searchConversations"
                        placeholder="Search conversations..."
                        class="w-full pl-10 pr-4 py-2 bg-gray-100 border-0 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
                    <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>
            
            <!-- Conversations List -->
            <div class="flex-1 overflow-y-auto scrollbar-thin">
                <template x-for="conversation in filteredConversations" :key="conversation.id">
                    <div 
                        @click="selectConversation(conversation)"
                        :class="{
                            'bg-blue-50 border-r-2 border-blue-500': selectedConversation?.id === conversation.id,
                            'hover:bg-gray-50': selectedConversation?.id !== conversation.id
                        }"
                        class="p-4 border-b border-gray-100 cursor-pointer transition-colors">
                        
                        <div class="flex items-center space-x-3">
                            <!-- Avatar -->
                            <div class="relative">
                                <img 
                                    :src="conversation.avatar" 
                                    :alt="conversation.title"
                                    class="w-12 h-12 rounded-full object-cover"
                                    :class="{ 'online-dot': conversation.is_online }">
                            </div>
                            
                            <!-- Conversation Info -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <h3 class="font-medium text-gray-900 truncate" x-text="conversation.title"></h3>
                                    <span class="text-xs text-gray-500" x-text="formatTime(conversation.last_activity)"></span>
                                </div>
                                
                                <div class="flex items-center justify-between mt-1">
                                    <p class="text-sm text-gray-600 truncate" x-text="getLastMessagePreview(conversation)"></p>
                                    <div class="flex items-center space-x-1">
                                        <!-- Unread count -->
                                        <template x-if="conversation.unread_count > 0">
                                            <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-medium text-white bg-blue-500 rounded-full" 
                                                  x-text="conversation.unread_count"></span>
                                        </template>
                                        
                                        <!-- Message status -->
                                        <template x-if="conversation.last_message?.is_mine">
                                            <div class="message-status">
                                                <svg x-show="conversation.last_message?.status === 'sent'" class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                                <svg x-show="conversation.last_message?.status === 'delivered'" class="w-4 h-4 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                                <svg x-show="conversation.last_message?.status === 'read'" class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                
                <!-- Empty state -->
                <template x-if="conversations.length === 0">
                    <div class="p-8 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No conversations</h3>
                        <p class="mt-1 text-sm text-gray-500">Start a new conversation to get started.</p>
                    </div>
                </template>
            </div>
        </div>
        
        <!-- Main Chat Area -->
        <div class="flex-1 flex flex-col">
            <template x-if="selectedConversation">
                <div class="h-full flex flex-col">
                    <!-- Chat Header -->
                    <div class="p-4 bg-white border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="relative">
                                    <img 
                                        :src="selectedConversation.avatar" 
                                        :alt="selectedConversation.title"
                                        class="w-10 h-10 rounded-full object-cover"
                                        :class="{ 'online-dot': selectedConversation.is_online }">
                                </div>
                                <div>
                                    <h2 class="font-semibold text-gray-900" x-text="selectedConversation.title"></h2>
                                    <p class="text-sm text-gray-500">
                                        <span x-show="selectedConversation.is_online" class="text-green-500">Online</span>
                                        <span x-show="!selectedConversation.is_online">
                                            Last seen <span x-text="formatTime(selectedConversation.last_activity)"></span>
                                        </span>
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="flex items-center space-x-2">
                                <button 
                                    @click="startVoiceCall()"
                                    class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                                    title="Voice call">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </button>
                                
                                <button 
                                    @click="startVideoCall()"
                                    class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                                    title="Video call">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </button>
                                
                                <div x-data="{ open: false }" class="relative">
                                    <button 
                                        @click="open = !open"
                                        class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                                        title="More options">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                        </svg>
                                    </button>
                                    
                                    <!-- Dropdown menu -->
                                    <div x-show="open" @click.away="open = false" 
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="transform opacity-0 scale-95"
                                         x-transition:enter-end="transform opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="transform opacity-100 scale-100"
                                         x-transition:leave-end="transform opacity-0 scale-95"
                                         class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10">
                                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">View profile</a>
                                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Mute notifications</a>
                                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Clear history</a>
                                        <a href="#" class="block px-4 py-2 text-sm text-red-700 hover:bg-gray-100">Block user</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Messages Container -->
                    <div class="flex-1 overflow-y-auto messages-container bg-gray-50 p-4" 
                         x-ref="messagesContainer"
                         @scroll="handleScroll">
                        
                        <!-- Load more indicator -->
                        <template x-if="hasMoreMessages">
                            <div class="text-center py-4">
                                <button 
                                    @click="loadMoreMessages()"
                                    :disabled="loadingMessages"
                                    class="text-sm text-blue-600 hover:text-blue-800 disabled:opacity-50">
                                    <span x-show="!loadingMessages">Load older messages</span>
                                    <span x-show="loadingMessages">Loading...</span>
                                </button>
                            </div>
                        </template>
                        
                        <!-- Messages -->
                        <template x-for="message in messages" :key="message.id">
                            <div class="mb-4 message-enter" 
                                 :class="{ 'flex justify-end': message.is_mine, 'flex justify-start': !message.is_mine }">
                                
                                <!-- Message bubble -->
                                <div class="max-w-xs lg:max-w-md">
                                    <!-- Reply indicator -->
                                    <template x-if="message.reply_to">
                                        <div class="mb-2 p-2 bg-gray-200 rounded-lg border-l-4 border-gray-400">
                                            <p class="text-xs text-gray-600" x-text="message.reply_to.sender_name"></p>
                                            <p class="text-sm text-gray-800 truncate" x-text="message.reply_to.content"></p>
                                        </div>
                                    </template>
                                    
                                    <div 
                                        :class="{
                                            'bg-blue-500 text-white': message.is_mine,
                                            'bg-white text-gray-900 border border-gray-200': !message.is_mine
                                        }"
                                        class="px-4 py-2 rounded-2xl shadow-sm">
                                        
                                        <!-- File/Media content -->
                                        <template x-if="message.message_type === 'image'">
                                            <div class="mb-2">
                                                <img :src="message.file_url" :alt="message.file_name" 
                                                     class="rounded-lg max-w-full h-auto cursor-pointer"
                                                     @click="openImageModal(message.file_url)">
                                            </div>
                                        </template>
                                        
                                        <template x-if="message.message_type === 'file'">
                                            <div class="flex items-center space-x-2 mb-2">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium truncate" x-text="message.file_name"></p>
                                                    <p class="text-xs opacity-75" x-text="message.formatted_file_size"></p>
                                                </div>
                                                <a :href="message.file_url" download 
                                                   class="p-1 hover:bg-black hover:bg-opacity-10 rounded">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-4-4m4 4l4-4m-4 4V3"/>
                                                    </svg>
                                                </a>
                                            </div>
                                        </template>
                                        
                                        <!-- Text content -->
                                        <template x-if="message.content">
                                            <p class="text-sm" x-html="formatMessageContent(message.content)"></p>
                                        </template>
                                        
                                        <!-- Message reactions -->
                                        <template x-if="message.reactions && message.reactions.length > 0">
                                            <div class="flex flex-wrap gap-1 mt-2">
                                                <template x-for="reaction in message.reactions" :key="reaction.emoji">
                                                    <button 
                                                        @click="toggleReaction(message.id, reaction.emoji)"
                                                        class="inline-flex items-center space-x-1 px-2 py-1 bg-gray-200 rounded-full text-xs hover:bg-gray-300 transition-colors">
                                                        <span x-text="reaction.emoji"></span>
                                                        <span x-text="reaction.user_ids.length"></span>
                                                    </button>
                                                </template>
                                            </div>
                                        </template>
                                        
                                        <!-- Message time and status -->
                                        <div class="flex items-center justify-between mt-1">
                                            <span class="text-xs opacity-75" x-text="formatTime(message.created_at)"></span>
                                            <template x-if="message.is_mine">
                                                <div class="message-status ml-2">
                                                    <svg x-show="message.status === 'sent'" class="w-3 h-3 opacity-75" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                    <svg x-show="message.status === 'delivered'" class="w-3 h-3 opacity-75" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                    <svg x-show="message.status === 'read'" class="w-3 h-3 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                    
                                    <!-- Quick reactions -->
                                    <div class="flex items-center space-x-1 mt-2" 
                                         x-show="hoveredMessage === message.id"
                                         @mouseenter="hoveredMessage = message.id"
                                         @mouseleave="hoveredMessage = null">
                                        <button 
                                            v-for="emoji in ['👍', '❤️', '😂', '😮', '😢', '😡']"
                                            @click="toggleReaction(message.id, emoji)"
                                            class="w-6 h-6 text-sm hover:bg-gray-200 rounded-full transition-colors"
                                            x-text="emoji">
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                        
                        <!-- Typing indicators -->
                        <template x-if="typingUsers.length > 0">
                            <div class="flex justify-start mb-4">
                                <div class="bg-gray-200 px-4 py-2 rounded-2xl">
                                    <div class="flex items-center space-x-2">
                                        <div class="typing-dots">
                                            <span></span>
                                            <span></span>
                                            <span></span>
                                        </div>
                                        <span class="text-xs text-gray-600">
                                            <span x-text="getTypingText()"></span> typing...
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                    
                    <!-- Message Input -->
                    <div class="p-4 bg-white border-t border-gray-200">
                        <!-- Reply indicator -->
                        <template x-if="replyingTo">
                            <div class="mb-3 p-2 bg-gray-100 rounded-lg border-l-4 border-blue-500">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-xs text-gray-600">Replying to <span x-text="replyingTo.sender_name"></span></p>
                                        <p class="text-sm text-gray-800 truncate" x-text="replyingTo.content"></p>
                                    </div>
                                    <button @click="cancelReply()" class="p-1 hover:bg-gray-200 rounded">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                        
                        <!-- File upload preview -->
                        <template x-if="selectedFile">
                            <div class="mb-3 p-2 bg-blue-50 rounded-lg border border-blue-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium" x-text="selectedFile.name"></p>
                                            <p class="text-xs text-gray-600" x-text="formatFileSize(selectedFile.size)"></p>
                                        </div>
                                    </div>
                                    <button @click="clearSelectedFile()" class="p-1 hover:bg-blue-100 rounded">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                        
                        <div class="flex items-end space-x-2">
                            <!-- File upload -->
                            <input type="file" x-ref="fileInput" @change="handleFileSelect" class="hidden" multiple>
                            <button 
                                @click="$refs.fileInput.click()"
                                class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                </svg>
                            </button>
                            
                            <!-- Message input -->
                            <div class="flex-1 relative">
                                <textarea
                                    x-model="messageInput"
                                    @keydown.enter.prevent="handleEnterKey($event)"
                                    @input="handleTyping"
                                    @focus="isTyping = true"
                                    @blur="isTyping = false"
                                    placeholder="Type a message..."
                                    rows="1"
                                    x-ref="messageTextarea"
                                    class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-2xl resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"></textarea>
                                
                                <!-- Emoji picker button -->
                                <button 
                                    @click="showEmojiPicker = !showEmojiPicker"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 p-1 text-gray-500 hover:text-gray-700 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </button>
                            </div>
                            
                            <!-- Send button -->
                            <button 
                                @click="sendMessage()"
                                :disabled="!canSendMessage"
                                :class="{
                                    'bg-blue-500 hover:bg-blue-600 text-white': canSendMessage,
                                    'bg-gray-300 text-gray-500 cursor-not-allowed': !canSendMessage
                                }"
                                class="p-3 rounded-full transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </template>
            
            <!-- Empty state when no conversation selected -->
            <template x-if="!selectedConversation">
                <div class="flex-1 flex items-center justify-center bg-gray-50">
                    <div class="text-center">
                        <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Select a conversation</h3>
                        <p class="text-gray-500 mb-6">Choose a conversation from the sidebar to start messaging.</p>
                        <button 
                            @click="showNewConversation = true"
                            class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Start new conversation
                        </button>
                    </div>
                </div>
            </template>
        </div>
        
        <!-- New Conversation Modal -->
        <div x-show="showNewConversation" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
            
            <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4"
                 @click.away="showNewConversation = false">
                
                <h3 class="text-lg font-medium text-gray-900 mb-4">Start new conversation</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search users</label>
                        <input 
                            type="text"
                            x-model="userSearchQuery"
                            @input="searchUsers"
                            placeholder="Type name or email..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <!-- Search results -->
                    <div x-show="searchedUsers.length > 0" class="max-h-48 overflow-y-auto">
                        <template x-for="user in searchedUsers" :key="user.id">
                            <div @click="startConversationWithUser(user)"
                                 class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer">
                                <img :src="user.avatar" :alt="user.name" 
                                     class="w-8 h-8 rounded-full object-cover mr-3">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900" x-text="user.name"></p>
                                    <p class="text-xs text-gray-500" x-text="user.email"></p>
                                </div>
                                <div class="text-xs text-gray-400">
                                    <span x-show="user.is_online" class="text-green-500">Online</span>
                                    <span x-show="!user.is_online">Offline</span>
                                </div>
                            </div>
                        </template>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button @click="showNewConversation = false"
                                class="px-4 py-2 text-sm text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Alpine.js Data -->
    <script>
        function messagingApp() {
            return {
                // State
                conversations: @json($conversations ?? []),
                selectedConversation: null,
                messages: [],
                messageInput: '',
                searchQuery: '',
                userSearchQuery: '',
                searchedUsers: [],
                showNewConversation: false,
                showEmojiPicker: false,
                selectedFile: null,
                replyingTo: null,
                hoveredMessage: null,
                
                // Real-time state
                typingUsers: [],
                onlineUsers: @json($onlineUsers ?? []),
                isTyping: false,
                typingTimer: null,
                
                // Loading states
                loadingMessages: false,
                hasMoreMessages: true,
                sendingMessage: false,
                
                // Pagination
                currentPage: 1,
                
                // Computed
                get filteredConversations() {
                    if (!this.searchQuery) return this.conversations;
                    
                    return this.conversations.filter(conv => 
                        conv.title.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                        (conv.last_message?.content || '').toLowerCase().includes(this.searchQuery.toLowerCase())
                    );
                },
                
                get canSendMessage() {
                    return (this.messageInput.trim() || this.selectedFile) && !this.sendingMessage;
                },
                
                // Initialization
                init() {
                    console.log('🚀 Messaging app initialized');
                    this.setupEventListeners();
                    this.startOnlineStatusUpdates();
                    
                    // Auto-select first conversation if available
                    if (this.conversations.length > 0) {
                        this.selectConversation(this.conversations[0]);
                    }
                },
                
                // Event listeners
                setupEventListeners() {
                    // Listen for real-time updates via WebSocket/Pusher
                    // This would integrate with Laravel Echo
                    // Example: Echo.private('user.${userId}').listen('MessageSent', ...)
                },
                
                // Conversation management
                async selectConversation(conversation) {
                    this.selectedConversation = conversation;
                    this.messages = [];
                    this.currentPage = 1;
                    this.hasMoreMessages = true;
                    
                    await this.loadMessages();
                    this.scrollToBottom();
                    this.markConversationAsRead(conversation.id);
                },
                
                async loadMessages() {
                    if (!this.selectedConversation || this.loadingMessages) return;
                    
                    this.loadingMessages = true;
                    
                    try {
                        const response = await fetch(`/messages/conversations/${this.selectedConversation.id}/messages?page=${this.currentPage}`, {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });
                        
                        const data = await response.json();
                        
                        if (this.currentPage === 1) {
                            this.messages = data.messages || [];
                        } else {
                            this.messages = [...(data.messages || []), ...this.messages];
                        }
                        
                        this.hasMoreMessages = data.pagination?.has_more || false;
                    } catch (error) {
                        console.error('Failed to load messages:', error);
                    } finally {
                        this.loadingMessages = false;
                    }
                },
                
                async loadMoreMessages() {
                    if (!this.hasMoreMessages || this.loadingMessages) return;
                    
                    const scrollPosition = this.$refs.messagesContainer.scrollHeight - this.$refs.messagesContainer.scrollTop;
                    
                    this.currentPage++;
                    await this.loadMessages();
                    
                    // Maintain scroll position
                    this.$nextTick(() => {
                        this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight - scrollPosition;
                    });
                },
                
                // Message sending
                async sendMessage() {
                    if (!this.canSendMessage || !this.selectedConversation) return;
                    
                    this.sendingMessage = true;
                    const content = this.messageInput.trim();
                    const file = this.selectedFile;
                    
                    // Clear input immediately for better UX
                    this.messageInput = '';
                    this.selectedFile = null;
                    this.replyingTo = null;
                    this.stopTyping();
                    
                    // Create optimistic message for immediate UI update
                    const optimisticMessage = {
                        id: 'temp-' + Date.now(),
                        content,
                        is_mine: true,
                        sender_name: 'You',
                        created_at: new Date().toISOString(),
                        status: 'sending',
                        message_type: file ? this.getFileType(file) : 'text'
                    };
                    
                    this.messages.push(optimisticMessage);
                    this.scrollToBottom();
                    
                    try {
                        const formData = new FormData();
                        formData.append('conversation_id', this.selectedConversation.id);
                        if (content) formData.append('content', content);
                        if (file) formData.append('file', file);
                        if (this.replyingTo) formData.append('reply_to_id', this.replyingTo.id);
                        
                        const response = await fetch('/messages/send', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: formData
                        });
                        
                        const data = await response.json();
                        
                        if (response.ok) {
                            // Replace optimistic message with real one
                            const index = this.messages.findIndex(m => m.id === optimisticMessage.id);
                            if (index !== -1) {
                                this.messages[index] = data.message;
                            }
                        } else {
                            throw new Error(data.message || 'Failed to send message');
                        }
                    } catch (error) {
                        console.error('Failed to send message:', error);
                        
                        // Mark optimistic message as failed
                        const index = this.messages.findIndex(m => m.id === optimisticMessage.id);
                        if (index !== -1) {
                            this.messages[index].status = 'failed';
                        }
                    } finally {
                        this.sendingMessage = false;
                    }
                },
                
                // Typing indicators
                handleTyping() {
                    if (!this.selectedConversation) return;
                    
                    this.startTyping();
                    
                    // Clear existing timer
                    if (this.typingTimer) {
                        clearTimeout(this.typingTimer);
                    }
                    
                    // Set new timer to stop typing
                    this.typingTimer = setTimeout(() => {
                        this.stopTyping();
                    }, 2000);
                },
                
                async startTyping() {
                    if (!this.selectedConversation) return;
                    
                    try {
                        await fetch('/messages/typing', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                conversation_id: this.selectedConversation.id,
                                is_typing: true
                            })
                        });
                    } catch (error) {
                        console.error('Failed to update typing status:', error);
                    }
                },
                
                async stopTyping() {
                    if (!this.selectedConversation) return;
                    
                    try {
                        await fetch('/messages/typing', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                conversation_id: this.selectedConversation.id,
                                is_typing: false
                            })
                        });
                    } catch (error) {
                        console.error('Failed to update typing status:', error);
                    }
                },
                
                // User search
                async searchUsers() {
                    if (this.userSearchQuery.length < 2) {
                        this.searchedUsers = [];
                        return;
                    }
                    
                    try {
                        const response = await fetch(`/messages/search-users?q=${encodeURIComponent(this.userSearchQuery)}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                        
                        const data = await response.json();
                        this.searchedUsers = data.users || [];
                    } catch (error) {
                        console.error('Failed to search users:', error);
                    }
                },
                
                async startConversationWithUser(user) {
                    this.showNewConversation = false;
                    this.userSearchQuery = '';
                    this.searchedUsers = [];
                    
                    // Check if conversation already exists
                    const existingConversation = this.conversations.find(conv => 
                        conv.other_user_id === user.id
                    );
                    
                    if (existingConversation) {
                        this.selectConversation(existingConversation);
                        return;
                    }
                    
                    // Create new conversation by sending message
                    const newConversation = {
                        id: 'temp-' + Date.now(),
                        title: user.name,
                        avatar: user.avatar,
                        other_user_id: user.id,
                        is_online: user.is_online,
                        unread_count: 0,
                        last_message: null,
                        last_activity: new Date().toISOString()
                    };
                    
                    this.conversations.unshift(newConversation);
                    this.selectConversation(newConversation);
                },
                
                // File handling
                handleFileSelect(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.selectedFile = file;
                    }
                },
                
                clearSelectedFile() {
                    this.selectedFile = null;
                    this.$refs.fileInput.value = '';
                },
                
                getFileType(file) {
                    const type = file.type;
                    if (type.startsWith('image/')) return 'image';
                    if (type.startsWith('video/')) return 'video';
                    if (type.startsWith('audio/')) return 'audio';
                    return 'file';
                },
                
                // Message reactions
                async toggleReaction(messageId, emoji) {
                    try {
                        const response = await fetch(`/messages/messages/${messageId}/reaction`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ emoji })
                        });
                        
                        const data = await response.json();
                        
                        if (response.ok) {
                            const message = this.messages.find(m => m.id === messageId);
                            if (message) {
                                message.reactions = data.reactions;
                            }
                        }
                    } catch (error) {
                        console.error('Failed to toggle reaction:', error);
                    }
                },
                
                // Utility functions
                formatTime(timestamp) {
                    const date = new Date(timestamp);
                    const now = new Date();
                    const diff = now - date;
                    
                    // Less than 1 minute
                    if (diff < 60000) return 'now';
                    
                    // Less than 1 hour
                    if (diff < 3600000) {
                        const minutes = Math.floor(diff / 60000);
                        return `${minutes}m ago`;
                    }
                    
                    // Less than 24 hours
                    if (diff < 86400000) {
                        const hours = Math.floor(diff / 3600000);
                        return `${hours}h ago`;
                    }
                    
                    // More than 24 hours
                    return date.toLocaleDateString();
                },
                
                formatFileSize(bytes) {
                    if (bytes === 0) return '0 Bytes';
                    const k = 1024;
                    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                },
                
                formatMessageContent(content) {
                    // Basic formatting - you can extend this
                    return content
                        .replace(/\n/g, '<br>')
                        .replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank" class="text-blue-500 underline">$1</a>');
                },
                
                getLastMessagePreview(conversation) {
                    if (!conversation.last_message) return 'No messages yet';
                    
                    const msg = conversation.last_message;
                    if (msg.message_type === 'image') return '📷 Photo';
                    if (msg.message_type === 'file') return '📎 File';
                    if (msg.message_type === 'video') return '🎥 Video';
                    if (msg.message_type === 'audio') return '🎵 Audio';
                    
                    return msg.content || 'Message';
                },
                
                getTypingText() {
                    const count = this.typingUsers.length;
                    if (count === 0) return '';
                    if (count === 1) return this.typingUsers[0].name;
                    if (count === 2) return `${this.typingUsers[0].name} and ${this.typingUsers[1].name}`;
                    return `${this.typingUsers[0].name} and ${count - 1} others`;
                },
                
                scrollToBottom() {
                    this.$nextTick(() => {
                        const container = this.$refs.messagesContainer;
                        if (container) {
                            container.scrollTop = container.scrollHeight;
                        }
                    });
                },
                
                handleScroll() {
                    const container = this.$refs.messagesContainer;
                    if (container.scrollTop === 0 && this.hasMoreMessages) {
                        this.loadMoreMessages();
                    }
                },
                
                handleEnterKey(event) {
                    if (event.shiftKey) {
                        // Shift+Enter = new line
                        return;
                    }
                    
                    // Enter = send message
                    this.sendMessage();
                },
                
                async markConversationAsRead(conversationId) {
                    try {
                        // Mark conversation as read in UI immediately
                        const conversation = this.conversations.find(c => c.id === conversationId);
                        if (conversation) {
                            conversation.unread_count = 0;
                        }
                        
                        // Update server
                        await fetch(`/messages/conversations/${conversationId}/mark-read`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                    } catch (error) {
                        console.error('Failed to mark conversation as read:', error);
                    }
                },
                
                searchConversations() {
                    // This is handled by the computed property filteredConversations
                },
                
                startOnlineStatusUpdates() {
                    // Update online status every 30 seconds
                    setInterval(() => {
                        this.updateOnlineStatus();
                    }, 30000);
                },
                
                async updateOnlineStatus() {
                    try {
                        await fetch('/messages/status', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                is_online: true
                            })
                        });
                    } catch (error) {
                        console.error('Failed to update online status:', error);
                    }
                },
                
                // Call functionality (placeholder)
                startVoiceCall() {
                    console.log('Starting voice call...');
                },
                
                startVideoCall() {
                    console.log('Starting video call...');
                },
                
                // Reply functionality
                replyToMessage(message) {
                    this.replyingTo = message;
                    this.$refs.messageTextarea.focus();
                },
                
                cancelReply() {
                    this.replyingTo = null;
                },
                
                // Image modal
                openImageModal(imageUrl) {
                    // You could implement a full-screen image viewer here
                    window.open(imageUrl, '_blank');
                }
            }
        }
    </script>
</body>
</html>
