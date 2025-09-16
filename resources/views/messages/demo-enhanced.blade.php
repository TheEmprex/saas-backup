<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Messages - Demo Enhanced</title>
    <x-favicon />
    
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
                                    <h3 class="text-sm font-medium text-gray-900 truncate" x-text="conversation.title"></h3>
                                    <div class="text-xs text-gray-500" x-text="formatTime(conversation.last_activity)"></div>
                                </div>
                                <div class="flex items-center justify-between mt-1">
                                    <p class="text-sm text-gray-600 truncate" x-text="getLastMessagePreview(conversation)"></p>
                                    <div x-show="conversation.unread_count > 0" 
                                         class="bg-blue-500 text-white text-xs rounded-full h-5 min-w-5 px-1.5 flex items-center justify-center">
                                        <span x-text="conversation.unread_count"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
        
        <!-- Main Chat Area -->
        <div class="flex-1 flex flex-col">
            <!-- Chat Header -->
            <template x-if="selectedConversation">
                <div class="bg-white border-b border-gray-200 p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <img :src="selectedConversation.avatar" 
                                 :alt="selectedConversation.title"
                                 class="w-10 h-10 rounded-full object-cover"
                                 :class="{ 'online-dot': selectedConversation.is_online }">
                            <div>
                                <h2 class="font-semibold text-gray-900" x-text="selectedConversation.title"></h2>
                                <div class="flex items-center space-x-2 text-sm text-gray-500">
                                    <span x-show="selectedConversation.is_online" class="text-green-500">● Online</span>
                                    <span x-show="!selectedConversation.is_online">● Last seen 2h ago</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action buttons -->
                        <div class="flex space-x-2">
                            <button @click="startVoiceCall()" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </button>
                            <button @click="startVideoCall()" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Typing Indicator -->
                    <div x-show="typingUsers.length > 0" class="mt-2 text-sm text-gray-500">
                        <span x-text="getTypingText()"></span> 
                        <span>is typing</span>
                        <div class="typing-dots inline-block ml-1">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                </div>
            </template>
            
            <!-- Empty State -->
            <template x-if="!selectedConversation">
                <div class="flex-1 flex items-center justify-center bg-gray-50">
                    <div class="text-center">
                        <div class="mx-auto h-12 w-12 text-gray-400">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 48 48">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M34 40h10v-4a6 6 0 00-10.712-3.714M34 40H14m20 0v-4a9.971 9.971 0 00-.712-3.714M14 40H4v-4a6 6 0 0110.713-3.714M14 40v-4c0-1.313.253-2.566.713-3.714m0 0A9.971 9.971 0 0124 30c4.75 0 8.652 3.516 9.287 8.286"/>
                            </svg>
                        </div>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No conversation selected</h3>
                        <p class="mt-1 text-sm text-gray-500">Choose a conversation from the sidebar to start messaging</p>
                        <div class="mt-6">
                            <button @click="showNewConversation = true" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                Start new conversation
                            </button>
                        </div>
                    </div>
                </div>
            </template>
            
            <!-- Messages Area -->
            <template x-if="selectedConversation">
                <div class="flex-1 overflow-y-auto p-4 space-y-4 messages-container" x-ref="messagesContainer" @scroll="handleScroll">
                    <!-- Load more messages -->
                    <div x-show="hasMoreMessages && loadingMessages" class="text-center py-4">
                        <div class="inline-flex items-center text-sm text-gray-500">
                            <svg class="animate-spin -ml-1 mr-3 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Loading messages...
                        </div>
                    </div>
                    
                    <!-- Messages -->
                    <template x-for="message in messages" :key="message.id">
                        <div class="message-enter" @mouseenter="hoveredMessage = message.id" @mouseleave="hoveredMessage = null">
                            <!-- My message -->
                            <template x-if="message.is_mine">
                                <div class="flex justify-end">
                                    <div class="max-w-xs lg:max-w-md px-4 py-2 bg-blue-500 text-white rounded-l-lg rounded-br-lg">
                                        <div x-show="message.reply_to" class="text-xs opacity-75 mb-1 p-1 bg-black bg-opacity-20 rounded">
                                            Replying to: <span x-text="message.reply_to?.content"></span>
                                        </div>
                                        <p class="text-sm" x-html="formatMessageContent(message.content)"></p>
                                        <div class="flex items-center justify-end mt-1 space-x-1">
                                            <span class="text-xs opacity-75" x-text="formatTime(message.created_at)"></span>
                                            <!-- Message status -->
                                            <div x-show="message.status === 'sent'">
                                                <svg class="w-3 h-3 opacity-75" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                                                </svg>
                                            </div>
                                            <div x-show="message.status === 'delivered'">
                                                <svg class="w-3 h-3 opacity-75" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M0 11l2-2 5 5L18 3l2 2L7 18z"/>
                                                </svg>
                                            </div>
                                            <div x-show="message.status === 'read'">
                                                <div class="w-3 h-3 bg-blue-300 rounded-full"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            
                            <!-- Their message -->
                            <template x-if="!message.is_mine">
                                <div class="flex">
                                    <div class="max-w-xs lg:max-w-md px-4 py-2 bg-gray-200 text-gray-800 rounded-r-lg rounded-bl-lg relative">
                                        <div x-show="message.reply_to" class="text-xs text-gray-600 mb-1 p-1 bg-gray-300 rounded">
                                            Replying to: <span x-text="message.reply_to?.content"></span>
                                        </div>
                                        <p class="text-sm" x-html="formatMessageContent(message.content)"></p>
                                        <div class="flex items-center justify-between mt-1">
                                            <span class="text-xs text-gray-500" x-text="formatTime(message.created_at)"></span>
                                            
                                            <!-- Message actions (visible on hover) -->
                                            <div x-show="hoveredMessage === message.id" class="flex space-x-1">
                                                <button @click="replyToMessage(message)" class="text-gray-400 hover:text-gray-600">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M7.707 3.293a1 1 0 010 1.414L5.414 7H11a7 7 0 017 7v2a1 1 0 11-2 0v-2a5 5 0 00-5-5H5.414l2.293 2.293a1 1 0 11-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"/>
                                                    </path>
                                                </svg>
                                            </button>
                                            <button @click="toggleReaction(message.id, '👍')" class="text-gray-400 hover:text-gray-600">
                                                <span class="text-sm">👍</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            
                            <!-- Reactions -->
                            <div x-show="message.reactions && message.reactions.length > 0" class="flex space-x-1 mt-1">
                                <template x-for="reaction in message.reactions" :key="reaction.emoji">
                                    <button @click="toggleReaction(message.id, reaction.emoji)" 
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-gray-100 hover:bg-gray-200 transition-colors">
                                        <span x-text="reaction.emoji"></span>
                                        <span class="ml-1 text-gray-600" x-text="reaction.count"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
            
            <!-- Message Input Area -->
            <template x-if="selectedConversation">
                <div class="bg-white border-t border-gray-200 p-4">
                    <!-- Reply indicator -->
                    <div x-show="replyingTo" class="mb-2 p-2 bg-gray-50 rounded-lg flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            <span class="font-medium">Replying to:</span>
                            <span x-text="replyingTo?.content"></span>
                        </div>
                        <button @click="cancelReply()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- File preview -->
                    <div x-show="selectedFile" class="mb-2 p-2 bg-gray-50 rounded-lg flex items-center justify-between">
                        <div class="flex items-center space-x-2 text-sm text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                            </svg>
                            <span x-text="selectedFile?.name"></span>
                        </div>
                        <button @click="clearSelectedFile()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="flex items-end space-x-3">
                        <!-- File upload button -->
                        <button class="flex-shrink-0 p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg">
                            <input type="file" class="hidden" x-ref="fileInput" @change="handleFileSelect">
                            <svg @click="$refs.fileInput.click()" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                            </svg>
                        </button>
                        
                        <!-- Text input -->
                        <div class="flex-1">
                            <textarea 
                                x-ref="messageTextarea"
                                x-model="messageInput"
                                @input="handleTyping"
                                @keydown.enter.prevent="handleEnterKey($event)"
                                placeholder="Type a message..."
                                class="w-full px-4 py-2 max-h-32 bg-gray-100 border-0 rounded-2xl text-sm focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all resize-none"
                                rows="1"></textarea>
                        </div>
                        
                        <!-- Send button -->
                        <button 
                            @click="sendMessage"
                            :disabled="!canSendMessage"
                            :class="{
                                'bg-blue-500 hover:bg-blue-600 text-white': canSendMessage,
                                'bg-gray-300 text-gray-500 cursor-not-allowed': !canSendMessage
                            }"
                            class="flex-shrink-0 p-2 rounded-full transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
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
                    
                    <!-- Demo search results -->
                    <div x-show="userSearchQuery.length > 0" class="max-h-48 overflow-y-auto space-y-2">
                        <div @click="startConversationWithUser({id: 99, name: 'John Doe', email: 'john@example.com', avatar: 'https://ui-avatars.com/api/?name=John+Doe&background=0D8ABC&color=fff', is_online: true})"
                             class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer">
                            <img src="https://ui-avatars.com/api/?name=John+Doe&background=0D8ABC&color=fff" alt="John Doe" 
                                 class="w-8 h-8 rounded-full object-cover mr-3">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">John Doe</p>
                                <p class="text-xs text-gray-500">john@example.com</p>
                            </div>
                            <div class="text-xs text-green-500">Online</div>
                        </div>
                        
                        <div @click="startConversationWithUser({id: 98, name: 'Jane Smith', email: 'jane@example.com', avatar: 'https://ui-avatars.com/api/?name=Jane+Smith&background=9333EA&color=fff', is_online: false})"
                             class="flex items-center p-2 hover:bg-gray-100 rounded cursor-pointer">
                            <img src="https://ui-avatars.com/api/?name=Jane+Smith&background=9333EA&color=fff" alt="Jane Smith" 
                                 class="w-8 h-8 rounded-full object-cover mr-3">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Jane Smith</p>
                                <p class="text-xs text-gray-500">jane@example.com</p>
                            </div>
                            <div class="text-xs text-gray-400">Offline</div>
                        </div>
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
                // Demo data with realistic conversations and messages
                conversations: [
                    {
                        id: 1,
                        title: "Sarah Wilson",
                        avatar: "https://ui-avatars.com/api/?name=Sarah+Wilson&background=10B981&color=fff",
                        is_online: true,
                        unread_count: 3,
                        other_user_id: 2,
                        last_message: {
                            content: "Hey! How's the project going?",
                            created_at: new Date().toISOString()
                        },
                        last_activity: new Date(Date.now() - 300000).toISOString() // 5 min ago
                    },
                    {
                        id: 2,
                        title: "Mike Johnson",
                        avatar: "https://ui-avatars.com/api/?name=Mike+Johnson&background=F59E0B&color=fff",
                        is_online: false,
                        unread_count: 0,
                        other_user_id: 3,
                        last_message: {
                            content: "Thanks for the feedback! 👍",
                            created_at: new Date(Date.now() - 3600000).toISOString() // 1 hour ago
                        },
                        last_activity: new Date(Date.now() - 3600000).toISOString()
                    },
                    {
                        id: 3,
                        title: "Team Chat",
                        avatar: "https://ui-avatars.com/api/?name=Team+Chat&background=8B5CF6&color=fff",
                        is_online: true,
                        unread_count: 7,
                        other_user_id: 4,
                        last_message: {
                            content: "Meeting rescheduled to 3 PM",
                            created_at: new Date(Date.now() - 1800000).toISOString() // 30 min ago
                        },
                        last_activity: new Date(Date.now() - 1800000).toISOString()
                    }
                ],
                
                // Demo messages data
                demoMessages: {
                    1: [
                        {
                            id: 1,
                            content: "Hi there! How are you today?",
                            is_mine: false,
                            sender_name: "Sarah Wilson",
                            created_at: new Date(Date.now() - 600000).toISOString(), // 10 min ago
                            status: 'read',
                            reactions: []
                        },
                        {
                            id: 2,
                            content: "I'm doing great, thanks! Working on the new design mockups.",
                            is_mine: true,
                            sender_name: "You",
                            created_at: new Date(Date.now() - 480000).toISOString(), // 8 min ago
                            status: 'read',
                            reactions: [{emoji: '👍', count: 1}]
                        },
                        {
                            id: 3,
                            content: "That sounds awesome! Can't wait to see them. 🎨",
                            is_mine: false,
                            sender_name: "Sarah Wilson",
                            created_at: new Date(Date.now() - 360000).toISOString(), // 6 min ago
                            status: 'delivered',
                            reactions: []
                        },
                        {
                            id: 4,
                            content: "Hey! How's the project going?",
                            is_mine: false,
                            sender_name: "Sarah Wilson",
                            created_at: new Date(Date.now() - 300000).toISOString(), // 5 min ago
                            status: 'sent',
                            reactions: []
                        }
                    ],
                    2: [
                        {
                            id: 5,
                            content: "Could you review the latest changes?",
                            is_mine: true,
                            sender_name: "You",
                            created_at: new Date(Date.now() - 7200000).toISOString(), // 2 hours ago
                            status: 'read',
                            reactions: []
                        },
                        {
                            id: 6,
                            content: "Sure thing! I'll take a look now.",
                            is_mine: false,
                            sender_name: "Mike Johnson",
                            created_at: new Date(Date.now() - 3900000).toISOString(), // ~1 hour ago
                            status: 'read',
                            reactions: []
                        },
                        {
                            id: 7,
                            content: "Thanks for the feedback! 👍",
                            is_mine: false,
                            sender_name: "Mike Johnson",
                            created_at: new Date(Date.now() - 3600000).toISOString(), // 1 hour ago
                            status: 'read',
                            reactions: [{emoji: '❤️', count: 1}]
                        }
                    ],
                    3: [
                        {
                            id: 8,
                            content: "Good morning team! 🌅",
                            is_mine: false,
                            sender_name: "Team Lead",
                            created_at: new Date(Date.now() - 21600000).toISOString(), // 6 hours ago
                            status: 'read',
                            reactions: [{emoji: '☀️', count: 3}]
                        },
                        {
                            id: 9,
                            content: "Morning! Ready for today's sprint.",
                            is_mine: true,
                            sender_name: "You",
                            created_at: new Date(Date.now() - 21000000).toISOString(), // ~6 hours ago
                            status: 'read',
                            reactions: []
                        },
                        {
                            id: 10,
                            content: "Meeting rescheduled to 3 PM",
                            is_mine: false,
                            sender_name: "Team Lead",
                            created_at: new Date(Date.now() - 1800000).toISOString(), // 30 min ago
                            status: 'delivered',
                            reactions: []
                        }
                    ]
                },
                
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
                onlineUsers: [],
                isTyping: false,
                typingTimer: null,
                
                // Loading states
                loadingMessages: false,
                hasMoreMessages: false,
                sendingMessage: false,
                
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
                    console.log('🚀 Demo Messaging app initialized');
                    console.log('📦 Loaded conversations:', this.conversations.length);
                    
                    // Auto-select first conversation if available
                    if (this.conversations.length > 0) {
                        this.selectConversation(this.conversations[0]);
                    }
                    
                    // Simulate typing indicator occasionally
                    this.simulateTypingIndicator();
                },
                
                // Demo method to simulate typing
                simulateTypingIndicator() {
                    setInterval(() => {
                        if (this.selectedConversation && Math.random() > 0.7) {
                            this.typingUsers = [{name: this.selectedConversation.title}];
                            setTimeout(() => {
                                this.typingUsers = [];
                            }, 3000);
                        }
                    }, 10000);
                },
                
                // Conversation management
                selectConversation(conversation) {
                    console.log('🔄 Selecting conversation:', conversation.title);
                    this.selectedConversation = conversation;
                    this.messages = this.demoMessages[conversation.id] || [];
                    conversation.unread_count = 0; // Mark as read
                    this.scrollToBottom();
                },
                
                // Message sending (demo)
                sendMessage() {
                    if (!this.canSendMessage || !this.selectedConversation) return;
                    
                    console.log('📤 Sending message:', this.messageInput);
                    
                    const newMessage = {
                        id: Date.now(),
                        content: this.messageInput.trim(),
                        is_mine: true,
                        sender_name: "You",
                        created_at: new Date().toISOString(),
                        status: 'sending',
                        reactions: []
                    };
                    
                    this.messages.push(newMessage);
                    this.messageInput = '';
                    this.replyingTo = null;
                    this.scrollToBottom();
                    
                    // Update conversation last message
                    this.selectedConversation.last_message = {
                        content: newMessage.content,
                        created_at: newMessage.created_at
                    };
                    this.selectedConversation.last_activity = newMessage.created_at;
                    
                    // Simulate message status updates
                    setTimeout(() => {
                        newMessage.status = 'sent';
                        setTimeout(() => {
                            newMessage.status = 'delivered';
                            setTimeout(() => {
                                newMessage.status = 'read';
                            }, 1000);
                        }, 500);
                    }, 500);
                    
                    // Simulate reply after a delay
                    if (Math.random() > 0.5) {
                        this.simulateReply();
                    }
                },
                
                // Simulate incoming message
                simulateReply() {
                    setTimeout(() => {
                        const replies = [
                            "That's interesting! 🤔",
                            "Got it, thanks!",
                            "Sounds good to me 👍",
                            "Let me check on that",
                            "Perfect! 🎉",
                            "I agree completely",
                            "That makes sense"
                        ];
                        
                        const replyMessage = {
                            id: Date.now() + 1,
                            content: replies[Math.floor(Math.random() * replies.length)],
                            is_mine: false,
                            sender_name: this.selectedConversation.title,
                            created_at: new Date().toISOString(),
                            status: 'read',
                            reactions: []
                        };
                        
                        this.messages.push(replyMessage);
                        this.scrollToBottom();
                        
                        // Update conversation
                        this.selectedConversation.last_message = {
                            content: replyMessage.content,
                            created_at: replyMessage.created_at
                        };
                        this.selectedConversation.last_activity = replyMessage.created_at;
                        
                    }, 2000 + Math.random() * 3000); // Random delay 2-5 seconds
                },
                
                // User search (demo)
                searchUsers() {
                    // Already handled by the template with demo users
                    console.log('🔍 Searching users:', this.userSearchQuery);
                },
                
                startConversationWithUser(user) {
                    console.log('💬 Starting conversation with:', user.name);
                    this.showNewConversation = false;
                    this.userSearchQuery = '';
                    
                    // Check if conversation already exists
                    const existingConversation = this.conversations.find(conv => 
                        conv.other_user_id === user.id
                    );
                    
                    if (existingConversation) {
                        this.selectConversation(existingConversation);
                        return;
                    }
                    
                    // Create new conversation
                    const newConversation = {
                        id: Date.now(),
                        title: user.name,
                        avatar: user.avatar,
                        other_user_id: user.id,
                        is_online: user.is_online,
                        unread_count: 0,
                        last_message: null,
                        last_activity: new Date().toISOString()
                    };
                    
                    this.conversations.unshift(newConversation);
                    this.demoMessages[newConversation.id] = []; // Initialize empty messages
                    this.selectConversation(newConversation);
                },
                
                // File handling (demo)
                handleFileSelect(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.selectedFile = file;
                        console.log('📎 File selected:', file.name);
                    }
                },
                
                clearSelectedFile() {
                    this.selectedFile = null;
                    if (this.$refs.fileInput) {
                        this.$refs.fileInput.value = '';
                    }
                },
                
                // Message reactions (demo)
                toggleReaction(messageId, emoji) {
                    console.log('❤️ Toggling reaction:', emoji, 'on message:', messageId);
                    const message = this.messages.find(m => m.id === messageId);
                    if (!message) return;
                    
                    if (!message.reactions) message.reactions = [];
                    
                    const existingReaction = message.reactions.find(r => r.emoji === emoji);
                    if (existingReaction) {
                        existingReaction.count++;
                    } else {
                        message.reactions.push({emoji, count: 1});
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
                
                formatMessageContent(content) {
                    // Basic formatting
                    return content
                        .replace(/\n/g, '<br>')
                        .replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank" class="text-blue-500 underline">$1</a>');
                },
                
                getLastMessagePreview(conversation) {
                    if (!conversation.last_message) return 'No messages yet';
                    
                    const msg = conversation.last_message;
                    return msg.content || 'Message';
                },
                
                getTypingText() {
                    const count = this.typingUsers.length;
                    if (count === 0) return '';
                    if (count === 1) return this.typingUsers[0].name;
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
                    // Demo - no actual pagination
                },
                
                handleEnterKey(event) {
                    if (event.shiftKey) {
                        // Shift+Enter = new line
                        return;
                    }
                    
                    // Enter = send message
                    this.sendMessage();
                },
                
                // Demo methods for buttons
                startVoiceCall() {
                    alert('🎤 Voice call feature - Demo mode');
                    console.log('📞 Starting voice call with:', this.selectedConversation?.title);
                },
                
                startVideoCall() {
                    alert('📹 Video call feature - Demo mode');
                    console.log('📺 Starting video call with:', this.selectedConversation?.title);
                },
                
                replyToMessage(message) {
                    console.log('↩️ Replying to message:', message.content);
                    this.replyingTo = message;
                    if (this.$refs.messageTextarea) {
                        this.$refs.messageTextarea.focus();
                    }
                },
                
                cancelReply() {
                    this.replyingTo = null;
                },
                
                handleTyping() {
                    // Demo typing indicator
                    console.log('⌨️ User is typing...');
                },
                
                searchConversations() {
                    // Handled by computed property
                }
            }
        }
    </script>
</body>
</html>
