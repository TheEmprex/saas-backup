<x-theme::layouts.app>
    <x-slot name="title">Messages</x-slot>

    @push('styles')
    <style>
        /* Ensure cloaked elements are hidden until Alpine initializes */
        [x-cloak] { display: none !important; }
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

        /* Thin top progress bar while connecting/loading */
        .ov-progress {
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            overflow: hidden;
            background: transparent;
        }
        .ov-progress::before {
            content: '';
            display: block;
            height: 100%;
            width: 40%;
            background: linear-gradient(90deg, transparent, rgba(99,102,241,0.6), transparent);
            animation: ov-progress 1.2s ease-in-out infinite;
        }
        @keyframes ov-progress {
            0% { transform: translateX(-50%); }
            50% { transform: translateX(120%); }
            100% { transform: translateX(200%); }
        }

        /* Skeleton helpers (use with Tailwind animate-pulse) */
        .skeleton-line { border-radius: 6px; }
        .skeleton-circle { border-radius: 9999px; }
        .skeleton-bubble-left { border-top-left-radius: 16px; border-top-right-radius: 16px; border-bottom-right-radius: 16px; border-bottom-left-radius: 4px; }
        .skeleton-bubble-right { border-top-left-radius: 16px; border-top-right-radius: 16px; border-bottom-left-radius: 16px; border-bottom-right-radius: 4px; }

        /* Subtle message enter animation */
        .message-enter { animation: msg-enter 180ms ease-out both; }
        @keyframes msg-enter {
            0% { opacity: 0; transform: translateY(4px) scale(0.98); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* Scroll to bottom button */
        .scroll-to-bottom {
            position: absolute;
            right: 1rem;
            bottom: 1rem;
            width: 36px;
            height: 36px;
            border-radius: 9999px;
            background: #4f46e5; /* primary-600 fallback */
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
            transition: transform .15s ease;
        }
        .scroll-to-bottom:hover { transform: translateY(-2px); }

        /* Online pulse indicator */
        .online-pulse { position: absolute; }
        .online-pulse::after {
            content: '';
            position: absolute;
            inset: -6px;
            border-radius: 9999px;
            border: 2px solid rgba(16,185,129,0.4); /* emerald-400 */
            animation: online-pulse 1.6s ease-out infinite;
        }
        @keyframes online-pulse {
            0% { transform: scale(0.9); opacity: 0.7; }
            70% { transform: scale(1.2); opacity: 0; }
            100% { transform: scale(1.2); opacity: 0; }
        }
        
        /* Emergency safety: hide unexpected full-screen overlays outside the messaging app (page-scoped) */
        body > .fixed.inset-0:not(.ov-allow-overlay) { display: none !important; }
        body > .fixed.top-0.left-0.right-0.bottom-0:not(.ov-allow-overlay) { display: none !important; }
        body > [class*="fixed"][class*="inset-0"]:not(.ov-allow-overlay) { display: none !important; }
        body > [class*="fixed"][class*="top-0"][class*="left-0"][class*="right-0"][class*="bottom-0"]:not(.ov-allow-overlay) { display: none !important; }
        /* If any page-transition state leaked here, re-enable pointer events locally */
        [data-page-transition-root].ov-animating { pointer-events: auto !important; }
    </style>
    @endpush

    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">Messages</h1>
                <p class="text-gray-600 dark:text-gray-400">Real-time messaging with your team</p>
            </div>
            <a href="{{ route('marketplace.index') }}" class="bg-gray-100 dark:bg-zinc-700 hover:bg-gray-200 dark:hover:bg-zinc-600 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-lg transition-colors duration-200 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Dashboard
            </a>
        </div>
    </div>

<div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg overflow-hidden messages-container">
        <div x-data="realTimeMessagingApp()" x-init="init()" class="h-full relative">
            <!-- Thin progress bar while loading/connecting -->
            <div x-show="loading || connectionStatus !== 'connected'" class="ov-progress"></div>
    
    <!-- Connection Status Indicator -->
    <div x-show="!loading" 
         :class="`connection-status ${connectionStatus}`"
         x-text="connectionStatusText">
    </div>
    

    <!-- Create Folder Modal: mount only when open to prevent default blocking -->
    <template x-if="showCreateFolderModal">
        <div class="fixed inset-0 z-50 flex items-center justify-center ov-allow-overlay">
            <div class="absolute inset-0 bg-black/40" @click="closeCreateFolderModal()"></div>
            <div class="relative bg-white dark:bg-zinc-800 rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-zinc-700 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Create folder</h3>
                    <button class="text-gray-400 hover:text-gray-600" @click="closeCreateFolderModal()">
                        <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 8.586l4.95-4.95a1 1 0 111.414 1.415L11.414 10l4.95 4.95a1 1 0 01-1.414 1.414L10 11.414l-4.95 4.95a1 1 0 01-1.414-1.414L8.586 10l-4.95-4.95A1 1 0 115.05 3.636L10 8.586z" clip-rule="evenodd"/></svg>
                    </button>
                </div>
                <div class="px-6 py-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Folder name</label>
                        <input type="text" x-model="newFolder.name" @keyup.enter="createFolder" placeholder="e.g. Important"
                               class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500" />
                        <p x-show="folderNameError" class="mt-1 text-xs text-red-600" x-text="folderNameError"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Color</label>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="color in folderColors" :key="color">
                                <button type="button" @click="newFolder.color = color"
                                        :class="newFolder.color === color ? 'ring-2 ring-offset-2 ring-primary-500' : ''"
                                        class="w-8 h-8 rounded-full border border-gray-200" :style="`background:${color}`"></button>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 dark:bg-zinc-900 border-t border-gray-200 dark:border-zinc-700 flex items-center justify-end space-x-3">
                    <button class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:text-gray-900" @click="closeCreateFolderModal()">Cancel</button>
                    <button class="px-4 py-2 text-sm bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50"
                            :disabled="creatingFolder || !newFolder.name.trim()"
                            @click="createFolder">
                        <span x-show="!creatingFolder">Create</span>
                        <span x-show="creatingFolder" class="flex items-center"><svg class="w-4 h-4 mr-2 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>Creating...</span>
                    </button>
                </div>
            </div>
        </div>
    </template>

    <!-- Main Layout -->
    <div x-show="!loading" class="flex h-full">
        <!-- Sidebar -->
        <div class="w-80 bg-white shadow-xl flex flex-col h-full border-r border-gray-200">
            <!-- Header -->
            <div class="flex-shrink-0 bg-gradient-to-r from-primary-600 to-primary-700 p-4 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.955 8.955 0 01-4.126-.98L3 21l1.98-5.126A8.955 8.955 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold">Real-time Messages</h1>
                            <p class="text-primary-200 text-sm flex items-center">
                                <span class="w-2 h-2 bg-green-400 rounded-full mr-2" x-show="connectionStatus === 'connected'"></span>
                                <span x-text="`${conversations.length} conversations`"></span>
                            </p>
                        </div>
                    </div>
                    <button @click="showUserSearch = !showUserSearch" 
                            class="p-2 rounded-full hover:bg-primary-600 transition-colors">
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
                           class="w-full bg-primary-500 bg-opacity-30 text-white placeholder-primary-200 rounded-lg px-4 py-2 focus:outline-none focus:bg-white focus:text-gray-900 focus:placeholder-gray-500 transition-all">
                    <svg class="absolute right-3 top-2.5 w-4 h-4 text-primary-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Folders Section -->
            <div class="flex-shrink-0 border-b border-gray-200 bg-gray-50 p-3">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-xs uppercase tracking-wider text-gray-500">Folders</h3>
                    <button @click="openCreateFolderModal()" class="text-xs text-primary-600 hover:text-primary-700">+ New</button>
                </div>
                <div class="space-y-1">
                    <button @click="activeFolder = 'all'" 
                            :class="activeFolder === 'all' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-700'"
                            class="w-full px-3 py-2 rounded-lg text-sm text-left transition">
                        All
                    </button>
                    <button @click="activeFolder = 'inbox'" 
                            :class="activeFolder === 'inbox' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-700'"
                            class="w-full px-3 py-2 rounded-lg text-sm text-left transition">
                        Inbox
                    </button>
                    <template x-for="folder in folders" :key="folder.id">
                        <div class="flex items-center">
                            <button @click="selectFolder(folder)"
                                    :class="activeFolder === folder.id ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-700'"
                                    class="flex-1 px-3 py-2 rounded-lg text-sm text-left transition">
                                <span class="inline-block w-2 h-2 rounded-full mr-2" :style="`background:${folder.color || '#3B82F6'}`"></span>
                                <span x-text="folder.name"></span>
                            </button>
                            <button class="text-xs text-gray-400 hover:text-primary-600 ml-1" @click="openMoveDialogForFolder(folder)">⋯</button>
                        </div>
                    </template>
                </div>
            </div>

            <!-- User Search -->
            <div x-cloak x-show="showUserSearch" 
                 x-transition 
                 class="flex-shrink-0 border-b border-gray-200 bg-gray-50 p-3">
                <div class="relative">
                    <input type="text" 
                           x-model="userSearchQuery" 
                           @input="searchUsers"
                           placeholder="Search users to start a conversation..."
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
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
                    <!-- Conversations skeleton while loading -->
                    <template x-if="loading && conversations.length === 0">
                        <div class="space-y-2">
                            <template x-for="i in [1,2,3,4,5,6]" :key="i">
                                <div class="flex items-center p-3 rounded-lg">
                                    <div class="w-12 h-12 bg-gray-200/80 dark:bg-zinc-700/60 skeleton-circle animate-pulse"></div>
                                    <div class="ml-3 flex-1 min-w-0">
                                        <div class="h-3 w-1/2 bg-gray-200/80 dark:bg-zinc-700/60 skeleton-line animate-pulse"></div>
                                        <div class="mt-2 h-3 w-3/4 bg-gray-100 dark:bg-zinc-800 skeleton-line animate-pulse"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                    <template x-for="conversation in filteredConversations" :key="conversation.id">
                        <div @click="selectConversation(conversation)" 
                             :class="{
                                 'bg-primary-50 border-r-3 border-primary-500 shadow-sm': selectedConversation?.id === conversation.id,
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
                                         class="flex items-center text-xs text-primary-600">
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
                        <button @click="showUserSearch = true" class="mt-3 px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
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
                    <div class="w-24 h-24 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-12 h-12 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.955 8.955 0 01-4.126-.98L3 21l1.98-5.126A8.955 8.955 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Real-time Messaging</h3>
                    <p class="text-gray-500 mb-4">Select a conversation to start real-time messaging with WebSockets.</p>
                    <div class="flex items-center justify-center mb-4">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2" x-show="connectionStatus === 'connected'"></span>
                        <span class="text-sm text-gray-600" x-text="connectionStatusText"></span>
                    </div>
                    <button @click="showUserSearch = true" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                        Start New Conversation
                    </button>
                </div>
            </div>

            <!-- Messaging UI -->

            <!-- Conversation Header -->
            <div x-cloak x-show="selectedConversation" class="flex-shrink-0 bg-white border-b border-gray-200 px-6 py-4 shadow-sm">
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
                                     class="flex items-center text-sm text-primary-600">
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
                                class="p-2 text-gray-400 hover:text-primary-600 hover:bg-primary-50 transition-colors rounded-full">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </button>
                        <button @click="initiateCall('video')" 
                                class="p-2 text-gray-400 hover:text-primary-600 hover:bg-primary-50 transition-colors rounded-full">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Messages Area with Real-time Updates -->
            <div x-cloak x-show="selectedConversation" class="flex-1 overflow-y-auto custom-scrollbar px-6 py-4 space-y-4 bg-gray-50 relative" x-ref="messagesContainer">
                <!-- Scroll to bottom button -->
                <button x-show="selectedConversation && messages?.length > 0 && !nearBottom()" @click="scrollToBottom()" type="button"
                        class="scroll-to-bottom" aria-label="Scroll to bottom">
                    <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                    </svg>
                </button>

                <div x-show="loadingMessages" class="space-y-3 py-2">
                    <div class="flex justify-start">
                        <div class="w-2/3 h-5 bg-gray-200/80 dark:bg-zinc-700/60 skeleton-bubble-left animate-pulse"></div>
                    </div>
                    <div class="flex justify-end">
                        <div class="w-1/2 h-5 bg-gray-200/80 dark:bg-zinc-700/60 skeleton-bubble-right animate-pulse"></div>
                    </div>
                    <div class="flex justify-start">
                        <div class="w-3/5 h-5 bg-gray-200/80 dark:bg-zinc-700/60 skeleton-bubble-left animate-pulse"></div>
                    </div>
                    <div class="flex justify-end">
                        <div class="w-2/3 h-5 bg-gray-200/80 dark:bg-zinc-700/60 skeleton-bubble-right animate-pulse"></div>
                    </div>
                    <div class="flex justify-start">
                        <div class="w-1/3 h-5 bg-gray-200/80 dark:bg-zinc-700/60 skeleton-bubble-left animate-pulse"></div>
                    </div>
                </div>

                <template x-for="(message, i) in messages" :key="message.id">
                    <!-- Date separator -->
                    <div class="flex justify-center" x-show="i === 0 || !sameDay(messages[i-1]?.created_at, message.created_at)">
                        <div class="text-xs text-gray-500 bg-gray-200/60 px-3 py-1 rounded-full mb-2" x-text="formatDateHeader(message.created_at)"></div>
                    </div>
                    <div :class="message.is_mine ? 'flex justify-end' : 'flex justify-start'" 
                         class="group message-enter"
                         x-data="{ isNew: message.isNew || false, touchStartX: 0, touchEndX: 0 }"
                         x-init="if (isNew) { setTimeout(() => isNew = false, 2000) }"
                         @touchstart.passive="touchStartX = $event.changedTouches[0]?.clientX || 0"
                         @touchend.passive="touchEndX = $event.changedTouches[0]?.clientX || 0; if (touchEndX - touchStartX > 60) { setReplyTo(message) }">
                        <div :class="{
'bg-primary-600 text-white ml-4': message.is_mine,
                                 'bg-white text-gray-900 mr-4': !message.is_mine,
                                 'ring-2 ring-green-300': isNew
                             }" 
                             :id="'msg-' + message.id" class="message-bubble max-w-xs lg:max-w-md px-4 py-3 rounded-2xl shadow-sm relative transition-all">
                            
                            <!-- Message content similar to upgraded.blade.php -->
                            <!-- Attachments / media preview -->
                            <template x-if="message.file_url">
                                <div class="mt-1">
                                    <template x-if="message.message_type === 'image'">
                                        <img :src="message.file_url" alt="image" class="rounded-lg max-h-64 object-contain cursor-zoom-in" @click.stop="openLightbox(message.file_url)">
                                    </template>
                                    <template x-if="message.message_type === 'video'">
                                        <video :src="message.file_url" class="rounded-lg max-h-64" controls playsinline></video>
                                    </template>
                                    <template x-if="message.message_type === 'audio'">
                                        <audio :src="message.file_url" controls class="w-64"></audio>
                                    </template>
                                    <template x-if="message.message_type === 'file'">
                                        <a :href="message.file_url" target="_blank" class="inline-flex items-center text-sm text-primary-600 hover:text-primary-700 hover:underline">
                                            <svg class="w-4 h-4 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585"/></svg>
                                            <span x-text="message.file_name || 'Download file'"></span>
                                            <span class="text-gray-300 mx-1" x-show="message.formatted_file_size">•</span>
                                            <span class="text-gray-600" x-text="message.formatted_file_size || ''"></span>
                                        </a>
                                    </template>
                                </div>
                            </template>

                            <!-- Reply snippet -->
                            <template x-if="message.reply_to">
                                <div class="mb-2 border-l-2 pl-3 border-gray-200 text-xs text-gray-600 cursor-pointer hover:bg-gray-50 rounded" @click="jumpToMessage(message.reply_to.id)" :title="`View replied message`">
                                    <div class="font-medium text-gray-700" x-text="message.reply_to.sender_name || 'User'"></div>
                                    <div class="truncate" x-text="message.reply_to.content || 'Attachment'"></div>
                                </div>
                            </template>

                            <!-- Text content -->
                            <div class="break-words" x-show="message.content" x-text="message.content"></div>

                            <!-- Reactions summary -->
                            <div x-show="message.reactions && Object.keys(message.reactions).length > 0" class="mt-2 flex flex-wrap gap-1">
                                <template x-for="(count, emoji) in message.reactions" :key="emoji">
                                    <div class="inline-flex items-center px-2 py-0.5 bg-white/80 border border-gray-200 rounded-full text-xs shadow-sm"
                                         :class="message.my_reactions && message.my_reactions.has(emoji) ? 'border-primary-300 bg-primary-50' : ''"
                                         :title="message.reaction_users && message.reaction_users[emoji] ? ('Reacted by: ' + message.reaction_users[emoji].join(', ')) : ''">
                                        <span class="mr-1" x-text="emoji"></span>
                                        <span x-text="count"></span>
                                    </div>
                                </template>
                            </div>

                            <!-- Actions: reactions + reply (shown on hover) -->
                            <div class="hidden group-hover:flex absolute -top-4 right-0 bg-white border border-gray-200 rounded-full shadow px-1 py-0.5 space-x-1 items-center">
                                <template x-for="emoji in ['👍','❤️','😂','🎉','😮','🙏']" :key="emoji">
                                    <button class="px-1.5 py-0.5 hover:bg-gray-100 rounded-full" @click.stop="toggleReaction(message, emoji)" :title="`React ${emoji}`">
                                        <span x-text="emoji"></span>
                                    </button>
                                </template>
                                <div class="w-px h-4 bg-gray-200"></div>
                                <button class="px-1.5 py-0.5 hover:bg-gray-100 rounded-full" @click.stop="setReplyTo(message)" title="Reply">
                                    ↩︎
                                </button>
                            </div>

                            <!-- Message footer with real-time read status -->
                            <div class="flex items-end justify-between mt-2 text-xs opacity-75">
                                <div class="flex items-center space-x-2">
                                    <span x-text="formatTime(message.created_at)"></span>
                                    <span x-show="message.edited_at" class="italic">(edited)</span>
                                </div>
                                
                                <!-- Message status / read receipts -->
                                <div class="flex items-center space-x-2">
                                    <!-- Sending / failed -->
                                    <template x-if="message.is_mine && message.status === 'sending'">
                                        <svg class="w-4 h-4 text-gray-400 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                    </template>
                                    <template x-if="message.is_mine && message.status === 'failed'">
                                        <button class="flex items-center text-red-500 hover:text-red-600" @click.stop="retrySend(message)" title="Failed. Tap to retry">
                                            <svg class="w-4 h-4 mr-1" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Retry
                                        </button>
                                    </template>

                                    <!-- Read receipts for my messages -->
                                    <template x-if="message.is_mine && message.status !== 'sending' && message.status !== 'failed'">
                                        <div class="message-status" :title="message.read_by && message.read_by.length ? ('Seen by ' + (message.read_by.length - 1) + ' others') : ''">
                                            <template x-if="message.is_read || (message.read_by && message.read_by.length > 1)">
                                                <svg class="w-4 h-4 text-primary-300" fill="currentColor" viewBox="0 0 20 20" title="Read">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            </template>
                                            <template x-if="!(message.is_read || (message.read_by && message.read_by.length > 1))">
                                                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20" title="Sent">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Lightbox overlay: only mount when open to avoid any default blocking -->
            <template x-if="lightbox.show">
                <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 ov-allow-overlay" @click="closeLightbox">
                    <img :src="lightbox.url" alt="preview" class="max-w-[95vw] max-h-[90vh] object-contain" @click.stop>
                    <button class="absolute top-4 right-4 text-white bg-black/40 hover:bg-black/60 rounded-full p-2" @click="closeLightbox" aria-label="Close">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </template>

            <!-- Message Input with Real-time Features -->
            <div x-cloak x-show="selectedConversation" class="flex-shrink-0 bg-white border-t border-gray-200 px-6 py-4">
                <!-- Selected files preview -->
                <div x-show="selectedFiles.length > 0" class="mb-3">
                    <div class="flex flex-wrap gap-2">
                        <template x-for="(f, i) in selectedFiles" :key="i">
                            <div class="flex items-center bg-gray-100 rounded-lg px-2 py-1">
                                <template x-if="f.type && f.type.startsWith('image/')">
                                    <img :src="URL.createObjectURL(f)" class="w-10 h-10 object-cover rounded mr-2" alt="preview" />
                                </template>
                                <template x-if="!f.type || !f.type.startsWith('image/')">
                                    <svg class="w-5 h-5 text-gray-500 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585"/></svg>
                                </template>
                                <div class="text-xs text-gray-700 max-w-[200px] truncate" x-text="f.name"></div>
                                <button class="ml-2 text-gray-500 hover:text-red-600" @click="removeSelectedFile(i)">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
                <!-- Reply preview -->
                <div x-show="replyTo" class="mb-3 p-2 bg-gray-50 border border-gray-200 rounded-lg text-sm">
                    <div class="flex items-center justify-between">
                        <div class="text-gray-600">
                            Replying to <span class="font-medium" x-text="replyTo?.sender?.name || 'User'"></span>
                        </div>
                        <button class="text-gray-400 hover:text-gray-600" @click="clearReplyTo" title="Cancel reply">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="mt-1 text-gray-500 truncate" x-text="replyTo?.content"></div>
                </div>

                <div class="flex items-end space-x-3" @dragover.prevent.stop @drop.prevent.stop="handleDrop($event)">
                    <!-- File upload -->
                    <div class="relative">
                        <input type="file" multiple accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.txt" 
                               x-ref="fileInput" @change="handleFileSelect" class="hidden">
                        <button @click="$refs.fileInput.click()" 
                                class="p-2 text-gray-400 hover:text-primary-600 hover:bg-primary-50 transition-colors rounded-full">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585"/>
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
                                  class="w-full resize-none rounded-2xl border border-gray-300 px-4 py-3 focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed max-h-32 scrollbar-hide"
                                  x-ref="messageInput"></textarea>
                    </div>

                    <!-- Send button with connection status -->
                    <button @click="sendMessage" 
                            :disabled="(!newMessage.trim() && selectedFiles.length === 0) || sendingMessage || connectionStatus !== 'connected'"
                            class="p-3 bg-primary-600 text-white rounded-full hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors shadow-lg">
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
    <div x-cloak x-show="notification.show" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform translate-y-2"
         class="fixed bottom-4 right-4 z-50">
        <div :class="notification.type === 'error' ? 'bg-red-500' : notification.type === 'success' ? 'bg-green-500' : 'bg-primary-600'" 
             class="text-white px-6 py-3 rounded-lg shadow-lg">
            <p x-text="notification.message"></p>
        </div>
    </div>

</div>

@push('scripts')
<script>
// Safety: neutralize global overlays that might block Messages interactions
(function() {
    try {
        // Remove any stuck page-transition blocker
        const rt = document.querySelector('[data-page-transition-root]');
        if (rt && rt.classList && rt.classList.contains('ov-animating')) {
            rt.classList.remove('ov-animating');
            rt.style.pointerEvents = '';
        }
        // Hide any full-screen fixed overlays not whitelisted
        const covers = (el) => {
            const r = el.getBoundingClientRect();
            return r.left <= 1 && r.top <= 1 && r.right >= (innerWidth - 1) && r.bottom >= (innerHeight - 1);
        };
        Array.from(document.body.children).forEach((el) => {
            const s = getComputedStyle(el);
            if (s.position === 'fixed' && covers(el) && !el.classList.contains('ov-allow-overlay')) {
                el.style.display = 'none';
            }
        });
    } catch (_) {}
})();
</script>
<script>
function realTimeMessagingApp() {
    return {
        // Core state
        loading: true,
        currentUserId: {{ auth()->id() ?? 'null' }},
        conversations: [],
        folders: [],
        activeFolder: 'all',
        selectedConversation: null,
        messages: [],

        // internals
        _abort: { conv: null, msgs: null, search: null },
        _readTimer: null,
        _typingTimer: null,
        _typingStopTimer: null,
        _searchTimer: null,
        _recentlyRead: new Set(),
        _currentSubscribedConvId: null,
        
        // UI state
        newMessage: '',
        searchQuery: '',
        userSearchQuery: '',
        userSearchResults: [],
        showUserSearch: false,
        sendingMessage: false,
        loadingMessages: false,
        selectedFiles: [],

        // pagination
        messagesPage: 1,
        messagesHasMore: false,

        // Folder modal state
        showCreateFolderModal: false,
        creatingFolder: false,
        folderNameError: '',
        newFolder: { name: '', color: '#3B82F6' },
        folderColors: ['#3B82F6','#10B981','#F59E0B','#EF4444','#8B5CF6','#06B6D4','#84CC16','#F97316','#DC2626','#0EA5E9'],
        
        // Real-time state
        connectionStatus: 'connecting',
        connectionStatusText: 'Connecting...',
        typingUsers: {},
        typingTimeout: null,
        
        // Notifications
        notification: { show: false, message: '', type: 'info' },
        
        // Lightbox
        lightbox: { show: false, url: null },
        
        // Initialize the app
        async init() {
            console.log('🚀 Real-time messaging initializing...');

            // Listen to Echo connection events if available
            window.addEventListener('echo:connected', () => {
                this.connectionStatus = 'connected'
                this.connectionStatusText = 'Connected'
            })
            window.addEventListener('echo:disconnected', () => {
                this.connectionStatus = 'disconnected'
                this.connectionStatusText = 'Disconnected'
            })
            window.addEventListener('echo:error', () => {
                this.connectionStatus = 'error'
                this.connectionStatusText = 'Connection error'
            })
            window.addEventListener('auth:required', () => {
                this.showNotification('Authentication required. Please refresh the page.', 'error')
            })
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
                await this.loadFolders();

                // Auto-select most recent conversation if any
                if (this.conversations.length && !this.selectedConversation) {
                    await this.selectConversation(this.conversations[0]);
                }
                
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
console.log('📡 Loading conversations...');
const { data } = await (window.apiFetch ? window.apiFetch('/messages/conversations') : fetch('/messages/conversations', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content || '' }, credentials: 'same-origin' }).then(r => r.json().then(d => ({ data: d }))));
console.log('📋 Raw data:', data);
this.conversations = (data.conversations || data.data?.conversations || data || [])
    .map(conv => this.formatConversation(conv))
    .sort((a,b) => new Date(b.last_activity) - new Date(a.last_activity));
console.log('📋 Loaded conversations:', this.conversations.length, this.conversations);
            } catch (error) {
                console.warn('⚠️ API unavailable, loading fallback data:', error);
                this.loadFallbackData();
            }
        },
        
// Load messages for a conversation
        async loadMessages(conversationId) {
            this.loadingMessages = true;
            this.messagesPage = 1;
            this.messagesHasMore = true;
            try {
                if (this._abort.msgs) this._abort.msgs.abort();
                this._abort.msgs = new AbortController();
const signal = this._abort.msgs.signal;
const { data } = await window.apiFetch(`/messages/conversations/${conversationId}/messages`, { signal });
const list = (data.messages || data.data?.messages || data || []);
this.messages = list.map(msg => this.formatMessage(msg));
const perPage = (data.per_page || data.meta?.per_page || data.pagination?.per_page || 20);
const hasMore = (data.pagination?.has_more !== undefined) ? !!data.pagination.has_more : (list.length >= perPage);
this.messagesHasMore = hasMore;
this.messagesPage = 1;
this.$nextTick(() => {
    this.scrollToBottom();
    this.scheduleMarkRead();
});
            } catch (error) {
                if (error.name === 'AbortError') return;
                console.warn('⚠️ Failed to load messages:', error);
                this.loadFallbackMessages();
            } finally {
                this.loadingMessages = false;
            }
        },
        
// Send a message
        async sendMessage() {
            if ((!this.newMessage.trim() && this.selectedFiles.length === 0) || this.sendingMessage || !this.selectedConversation) {
                return;
            }
            
            const content = this.newMessage.trim();
            const hasFiles = (this.selectedFiles && this.selectedFiles.length > 0);
            this.newMessage = '';
            this.sendingMessage = true;
            
            try {
                let response;
                // Create optimistic placeholder for text-only messages
                let placeholder = null;
                if (!hasFiles && content) {
                    const tempId = 'temp-' + Date.now() + '-' + Math.floor(Math.random()*1e6);
                    placeholder = {
                        id: tempId,
                        local_id: tempId,
                        conversation_id: this.selectedConversation.id,
                        content: content,
                        message_type: 'text',
                        created_at: new Date().toISOString(),
                        sender_id: this.currentUserId,
                        sender: { name: 'Me', avatar: '' },
                        is_mine: true,
                        is_read: false,
                        status: 'sending',
                        reply_to: this.replyTo ? { id: this.replyTo.id, content: this.replyTo.content, sender_name: this.replyTo.sender?.name || 'User' } : null,
                        reactions: {},
                        my_reactions: new Set(),
                        reaction_users: {}
                    };
                    this.messages.push(placeholder);
                    this.$nextTick(() => this.scrollToBottom());
                }
                if (hasFiles) {
                    const form = new FormData();
                    form.append('conversation_id', this.selectedConversation.id);
                    if (content) form.append('content', content);
                    form.append('message_type', 'attachment');
                    if (this.replyTo?.id) form.append('reply_to_id', this.replyTo.id);
                    this.selectedFiles.slice(0, 3).forEach((f,i) => form.append('files[]', f));
                    const { data } = await window.apiFetch(`/messages/send`, { method: 'POST', body: form });
                    response = { ok: true, data };
                } else {
                    const { data } = await window.apiFetch(`/messages/send`, {
                        method: 'POST',
                        body: {
                            conversation_id: this.selectedConversation.id,
                            content: content,
                            message_type: 'text',
                            reply_to_id: this.replyTo?.id || null
                        }
                    });
                    response = { ok: true, data };
                }
                
                {
                    const data = response.data || {};
                    const serverMsg = this.formatMessage((data.message) || data);
                    serverMsg.is_mine = true;
                    // Reconcile placeholder if exists
                    if (placeholder) {
                        const idx = this.messages.findIndex(m => m.id === placeholder.id);
                        if (idx !== -1) {
                            this.messages[idx] = { ...serverMsg, status: 'sent' };
                        } else {
                            this.messages.push({ ...serverMsg, status: 'sent' });
                        }
                    } else {
                        this.messages.push({ ...serverMsg, status: 'sent' });
                    }
                    this.$nextTick(() => this.scrollToBottom());
                    
                    // clear files and reply state
                    this.selectedFiles = [];
                    this.replyTo = null;
                    
                    // Update conversation last message
                    this.selectedConversation.last_message = { content: content };
                    this.selectedConversation.last_activity = new Date().toISOString();
                }
            } catch (error) {
                console.error('❌ Failed to send message:', error);
                this.showNotification('Failed to send message', 'error');
                // Mark placeholder failed
                const failed = this.messages.slice().reverse().find(m => m.status === 'sending' && m.is_mine && m.content === content);
                if (failed) { failed.status = 'failed'; }
                else { this.newMessage = content; }
            } finally {
                this.sendingMessage = false;
            }
        },
        
        // Search for users (debounced + abortable)
        async searchUsers() {
            const q = this.userSearchQuery || '';
            if (q.length < 2) {
                this.userSearchResults = [];
                if (this._abort.search) { try { this._abort.search.abort(); } catch (_) {} }
                clearTimeout(this._searchTimer);
                return;
            }

            clearTimeout(this._searchTimer);
            this._searchTimer = setTimeout(async () => {
                // Abort any ongoing request
                if (this._abort.search) { try { this._abort.search.abort(); } catch (_) {} }
                this._abort.search = new AbortController();
                const signal = this._abort.search.signal;
                try {
                    const { data } = await window.apiFetch(`/messages/search-users?q=${encodeURIComponent(q)}`, { signal });
                    const list = (data?.data?.users ?? data?.users ?? data?.data ?? data) || [];
                    this.userSearchResults = Array.isArray(list) ? list.slice(0, 10) : [];
                } catch (error) {
                    if (error?.name === 'AbortError') return;
                    console.warn('⚠️ User search failed:', error);
                    this.userSearchResults = [];
                }
            }, 250);
        },
        
        // Start a new conversation
        async startConversation(user) {
            try {
const { data } = await window.apiFetch('/messages/send', {
                    method: 'POST',
                    body: {
                        recipient_id: user.id,
                        content: 'Hi!'
                    }
                });
const created = data.message || data.data || data;
                    // Refresh conversations and select the new one
                    await this.loadConversations();
const targetId = (created.conversation_id || created.conversation?.id);
                    let convo = this.conversations.find(c => c.id == targetId);
                    if (!convo && user?.id) {
                        convo = this.conversations.find(c => c.other_user_id == user.id);
                    }
                    if (convo) {
                        await this.selectConversation(convo);
                    }
                    
                    this.showUserSearch = false;
                    this.userSearchQuery = '';
                    this.userSearchResults = [];
                    
                    this.showNotification(`Started conversation with ${user.name}`, 'success');
            } catch (error) {
                console.error('❌ Failed to start conversation:', error);
                this.showNotification('Failed to start conversation', 'error');
            }
        },
        
        // Select a conversation
        async selectConversation(conversation) {
            // Leave previous channel
            if (this._currentSubscribedConvId && window.Echo) {
                try { window.Echo.leave(`conversation.${this._currentSubscribedConvId}`); } catch (_) {}
            }
            this.selectedConversation = conversation;
            await this.loadMessages(conversation.id);
            this.scheduleMarkRead();
            
            // Mark as read on the conversation object
            conversation.unread_count = 0;
            
            // Subscribe to conversation updates
            this.subscribeToConversation(conversation.id);
            this._currentSubscribedConvId = conversation.id;

            // Attach scroll listener for read tracking
            this.$nextTick(() => {
                const el = this.$refs.messagesContainer;
                if (!el) return;
                el.removeEventListener('scroll', this.onMessagesScroll);
                el.addEventListener('scroll', this.onMessagesScroll, { passive: true });
            });
        },
        
// Initialize WebSocket connection
        async initializeWebSocket() {
            try {
                // Try to use Laravel Echo if available
                if (typeof Echo !== 'undefined' || window.Echo) {
                    const echo = window.Echo || Echo;
                    
                    // Listen for new messages and read receipts on user channel
                    echo.private(`user.${this.currentUserId}`)
                        .listen('.message.sent', (e) => {
                            this.handleNewMessage(e);
                        })
                        .listen('.message.read', (e) => {
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
                        .listen('.message.sent', (e) => {
                            if (e.message.sender_id !== this.currentUserId) {
                                this.handleNewMessage(e);
                            }
                        })
                        .listen('.user.typing', (e) => {
                            const convId = conversationId;
                            const name = e?.user_name || e?.user?.name || 'Someone';
                            const arr = this.typingUsers[convId] || [];
                            if (!arr.includes(name)) arr.push(name);
                            this.typingUsers = { ...this.typingUsers, [convId]: arr };
                            clearTimeout(this._typingTimer);
                            this._typingTimer = setTimeout(() => {
                                this.typingUsers = { ...this.typingUsers, [convId]: [] };
                            }, 2500);
                        })
                        .listenForWhisper('typing', (e) => {
                            const convId = conversationId;
                            const name = e?.user?.name || 'Someone';
                            const arr = this.typingUsers[convId] || [];
                            if (!arr.includes(name)) arr.push(name);
                            this.typingUsers = { ...this.typingUsers, [convId]: arr };
                            clearTimeout(this._typingTimer);
                            this._typingTimer = setTimeout(() => {
                                this.typingUsers = { ...this.typingUsers, [convId]: [] };
                            }, 1500);
                        });
                }
            } catch (error) {
                console.warn('⚠️ Conversation subscription failed:', error);
            }
        },
        
        // Handle incoming messages
        handleNewMessage(event) {
            const payload = event.message || event.data?.message || event;
            if (event.sender && !payload.sender) payload.sender = event.sender;
            const message = this.formatMessage(payload);
            
            if (this.selectedConversation && message.conversation_id == this.selectedConversation.id) {
                this.messages.push(message);
                this.$nextTick(() => {
                    this.scrollToBottom();
                    this.scheduleMarkRead();
                });
            }
            
            // Update conversation list and reorder by activity
            const idx = this.conversations.findIndex(c => c.id == message.conversation_id);
            if (idx !== -1) {
                const conversation = this.conversations[idx];
                conversation.last_message = { content: message.content };
                conversation.last_activity = message.created_at;
                if (message.sender_id !== this.currentUserId && (!this.selectedConversation || this.selectedConversation.id !== conversation.id)) {
                    conversation.unread_count = (conversation.unread_count || 0) + 1;
                }
                // move to top
                this.conversations.splice(idx,1);
                this.conversations.unshift(conversation);
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
            let list = this.conversations;
            if (this.activeFolder !== 'all') {
                if (this.activeFolder === 'inbox') {
                    list = list.filter(c => !(c.folder_ids && c.folder_ids.length));
                } else {
                    list = list.filter(c => (c.folder_ids || []).includes(this.activeFolder));
                }
            }
            if (!this.searchQuery) return list;
            const q = this.searchQuery.toLowerCase();
            const scored = list.map(c => {
                let score = 0;
                if (c.title) {
                    const t = c.title.toLowerCase();
                    if (t.startsWith(q)) score += 5;
                    if (t.includes(q)) score += 3;
                }
                const last = c.last_message?.content?.toLowerCase?.() || '';
                if (last.includes(q)) score += 2;
                return { c, score };
            });
            return scored
                .filter(x => x.score > 0)
                .sort((a,b) => b.score - a.score || (new Date(b.c.last_activity) - new Date(a.c.last_activity)))
                .map(x => x.c);
        },
        
        // Utility functions
formatConversation(conv) {
            // Derive other_user_id if not provided, for private chats
            let otherId = conv.other_user_id;
            if (!otherId && Array.isArray(conv.participants)) {
                const other = conv.participants.find(p => p.id !== this.currentUserId);
                if (other) otherId = other.id;
            }
            return {
                id: conv.id,
                other_user_id: otherId,
                title: conv.title || this.getConversationTitle(conv),
                avatar: conv.avatar || this.getConversationAvatar(conv),
                last_message: conv.last_message || { content: 'No messages yet' },
                last_activity: conv.last_activity || conv.updated_at || new Date().toISOString(),
                unread_count: conv.unread_count || 0,
                is_online: typeof conv.is_online === 'boolean' ? conv.is_online : this.getOnlineStatus(conv),
                'participants': conv.participants || [],
                folder_ids: conv.folder_ids || []
            };
        },
        
formatMessage(msg) {
            return {
                id: msg.id,
                content: msg.content,
                conversation_id: msg.conversation_id,
                sender_id: msg.sender_id,
                is_mine: msg.sender_id === this.currentUserId,
                created_at: msg.created_at,
                sender: msg.sender || { name: msg.sender_name || 'Unknown', avatar: msg.sender_avatar || '/images/default-avatar.png' },
                is_read: msg.is_read || false,
                // attachments/media fields
                message_type: msg.message_type || msg.type || null,
                file_url: msg.file_url || (msg.file?.path ?? null),
                file_name: msg.file_name || (msg.file?.name ?? null),
                file_size: msg.file_size || (msg.file?.size ?? null),
                formatted_file_size: msg.formatted_file_size || null,
                reply_to: (msg.reply_to || msg.replied_to) ? {
                    id: (msg.reply_to || msg.replied_to).id,
                    content: (msg.reply_to || msg.replied_to).content,
                    sender_name: (msg.reply_to || msg.replied_to).sender_name || (msg.reply_to || msg.replied_to).sender?.name || 'User'
                } : null,
                read_by: Array.isArray(msg.read_by) ? msg.read_by : (Array.isArray(msg.reads) ? msg.reads.map(r => r.user_id) : []),
                reactions: (() => {
                    const raw = msg.reactions;
                    const map = {};
                    if (Array.isArray(raw)) {
                        raw.forEach(r => {
                            const emoji = r.emoji || r.key || r.name;
                            if (!emoji) return;
                            if (Array.isArray(r.user_ids)) map[emoji] = r.user_ids.length;
                            else if (Array.isArray(r.users)) map[emoji] = r.users.length;
                            else if (typeof r.count === 'number') map[emoji] = r.count;
                        });
                    } else if (raw && typeof raw === 'object') {
                        Object.entries(raw).forEach(([emoji, v]) => {
                            if (Array.isArray(v)) map[emoji] = v.length;
                            else if (typeof v === 'number') map[emoji] = v;
                            else if (v && typeof v === 'object') {
                                const cnt = v.count || (Array.isArray(v.users) ? v.users.length : 0);
                                map[emoji] = cnt;
                            }
                        });
                    }
                    return map;
                })(),
                reaction_users: (() => {
                    const raw = msg.reactions;
                    const usersMap = {};
                    if (Array.isArray(raw)) {
                        raw.forEach(r => {
                            const emoji = r.emoji || r.key || r.name;
                            const usersArr = r.users || [];
                            if (emoji && Array.isArray(usersArr)) usersMap[emoji] = usersArr.map(u => u.name || u);
                        });
                    } else if (raw && typeof raw === 'object') {
                        Object.entries(raw).forEach(([emoji, v]) => {
                            if (v && typeof v === 'object' && Array.isArray(v.users)) usersMap[emoji] = v.users.map(u => u.name || u);
                        });
                    }
                    return usersMap;
                })(),
                my_reactions: (() => {
                    const raw = msg.reactions;
                    const mine = new Set();
                    if (Array.isArray(raw)) {
                        raw.forEach(r => {
                            const emoji = r.emoji || r.key || r.name;
                            if (!emoji) return;
                            if (Array.isArray(r.user_ids) && r.user_ids.includes(this.currentUserId)) mine.add(emoji);
                            if (Array.isArray(r.users) && r.users.some(u => (u.id ?? u) === this.currentUserId)) mine.add(emoji);
                        });
                    } else if (raw && typeof raw === 'object') {
                        Object.entries(raw).forEach(([emoji, v]) => {
                            if (Array.isArray(v) && v.includes && v.includes(this.currentUserId)) mine.add(emoji);
                            if (v && typeof v === 'object' && Array.isArray(v.users) && v.users.some(u => (u.id ?? u) === this.currentUserId)) mine.add(emoji);
                        });
                    }
                    return mine;
                })()
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
        
        async loadFolders() {
            try {
const { data } = await window.apiFetch('/messages/api/message-folders')
                this.folders = (data.data || data || [])
                        .sort((a,b) => (a.order ?? a.sort_order ?? 0) - (b.order ?? b.sort_order ?? 0));
            } catch (e) {
                console.warn('Folders load failed', e)
            }
        },

        selectFolder(folder) {
            this.activeFolder = folder.id
            this.showUserSearch = false
        },

        openCreateFolderModal() {
            this.folderNameError = '';
            this.newFolder = { name: '', color: this.folderColors[0] };
            this.showCreateFolderModal = true;
        },

        closeCreateFolderModal() {
            if (!this.creatingFolder) this.showCreateFolderModal = false;
        },

        async createFolder() {
            if (this.creatingFolder) return;
            this.folderNameError = '';
            const name = (this.newFolder.name || '').trim();
            if (!name) {
                this.folderNameError = 'Please enter a folder name.';
                return;
            }
            this.creatingFolder = true;
            try {
const { data } = await window.apiFetch('/api/message-folders', {
                    method: 'POST',
                    body: { name: name, color: this.newFolder.color }
                });
                await this.loadFolders();
                this.showNotification('Folder created', 'success');
                // Optionally set active folder to the new one if returned
                const created = data.data || data;
                if (created && created.id) {
                    this.activeFolder = created.id;
                }
                this.showCreateFolderModal = false;
            } catch (e) {
                console.error(e);
                this.folderNameError = 'Could not create folder. Please try again.';
            } finally {
                this.creatingFolder = false;
            }
        },

        openMoveDialogForFolder(folder) {
            if (!this.selectedConversation) {
                this.showNotification('Select a conversation first', 'info');
                return;
            }
            const confirmMove = confirm(`Move current conversation to "${folder.name}"?`);
            if (confirmMove) {
                this.moveConversationToFolder(folder.id, this.selectedConversation.id);
            }
        },

        async moveConversationToFolder(folderId, conversationId) {
try {
            await window.apiFetch(`/messages/api/message-folders/${folderId}/conversations`, {
                method: 'POST',
                body: { conversation_id: conversationId }
            })
            {
                // Update local state
                const conv = this.conversations.find(c => c.id === conversationId)
                if (conv) {
                    conv.folder_ids = conv.folder_ids || []
                    if (!conv.folder_ids.includes(folderId)) conv.folder_ids.push(folderId)
                }
            }
            } catch (_) {}
        },
        
        getConversationAvatar(conv) {
            if (conv.type === 'private' && conv.participants) {
                const other = conv.participants.find(p => p.id !== this.currentUserId);
                return other?.avatar || '/images/default-avatar.png';
            }
            return '/images/group-avatar.png';
        },
        
getOnlineStatus(conv) {
            if (typeof conv.is_online === 'boolean') {
                return conv.is_online;
            }
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

        sameDay(a, b) {
            if (!a || !b) return false;
            const da = new Date(a), db = new Date(b);
            return da.getFullYear() === db.getFullYear() && da.getMonth() === db.getMonth() && da.getDate() === db.getDate();
        },
        formatDateHeader(timestamp) {
            const d = new Date(timestamp);
            const today = new Date();
            const yesterday = new Date(Date.now() - 86400000);
            if (this.sameDay(d, today)) return 'Today';
            if (this.sameDay(d, yesterday)) return 'Yesterday';
            return d.toLocaleDateString([], { weekday: 'short', month: 'short', day: 'numeric' });
        },
        
        scrollToBottom() {
            if (this.$refs.messagesContainer) {
                this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight;
            }
        },

        nearBottom() {
            const el = this.$refs.messagesContainer;
            if (!el) return true;
            return (el.scrollHeight - el.scrollTop - el.clientHeight) < 64;
        },

        onMessagesScroll: function() {
            const el = this.$refs.messagesContainer;
            if (!el) return;
            // Load older when near top
            if (el.scrollTop < 40 && !this.loadingMessages && this.messagesHasMore) {
                this.loadOlderMessages();
            }
            this.scheduleMarkRead();
        },

        async loadOlderMessages() {
            if (!this.selectedConversation) return;
            this.loadingMessages = true;
            const el = this.$refs.messagesContainer;
            const prevHeight = el?.scrollHeight || 0;
            try {
                const nextPage = (this.messagesPage || 1) + 1;
const resp = await fetch(`/messages/conversations/${this.selectedConversation.id}/messages?page=${nextPage}`, {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin'
                });
                if (resp.ok) {
                    const data = await resp.json();
                    const list = (data.messages || data.data?.messages || []);
                    const perPage = (data.per_page || data.meta?.per_page || data.pagination?.per_page || 20);
                    const hasMore = (data.pagination?.has_more !== undefined) ? !!data.pagination.has_more : (list.length >= perPage);
                    if (list.length === 0) {
                        this.messagesHasMore = false;
                    } else {
                        const older = list.map(m => this.formatMessage(m));
                        this.messages = older.concat(this.messages);
                        this.messagesPage = nextPage;
                        this.messagesHasMore = hasMore;
                        this.$nextTick(() => {
                            const newHeight = el?.scrollHeight || 0;
                            if (el) el.scrollTop = newHeight - prevHeight;
                        });
                    }
                } else {
                    this.messagesHasMore = false;
                }
            } catch (_) {
                this.messagesHasMore = false;
            } finally {
                this.loadingMessages = false;
            }
        },

        // Jump to a specific message by id; will load older pages until found (up to 5 attempts)
        async jumpToMessage(id, attempts = 0) {
            const anchor = document.getElementById('msg-'+id);
            if (anchor) {
                anchor.scrollIntoView({ behavior: 'smooth', block: 'center' });
                anchor.classList.add('ring-2', 'ring-primary-300');
                setTimeout(() => anchor.classList.remove('ring-2', 'ring-primary-300'), 1200);
                return;
            }
            if (this.messagesHasMore && attempts < 5) {
                await this.loadOlderMessages();
                // wait for DOM
                this.$nextTick(() => this.jumpToMessage(id, attempts + 1));
            } else {
                this.showNotification('Original message not loaded', 'info');
            }
        },

        scheduleMarkRead() {
            if (this._readTimer) cancelAnimationFrame(this._readTimer);
            this._readTimer = requestAnimationFrame(() => this.markVisibleMessagesRead());
        },

        async markVisibleMessagesRead() {
            if (!this.selectedConversation || !Array.isArray(this.messages)) return;
            const el = this.$refs.messagesContainer;
            if (!el) return;
            const nearBottom = (el.scrollHeight - el.scrollTop - el.clientHeight) < 48;
            if (!nearBottom) return;
            // mark last few incoming messages as read
            const toMark = this.messages
                .filter(m => !m.is_mine && !m.is_read && !this._recentlyRead.has(m.id))
                .slice(-5);
            await Promise.allSettled(toMark.map(async (m) => {
                try {
const resp = await fetch(`/messages/messages/${m.id}/read`, {
                        method: 'POST',
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content || '' },
                        credentials: 'same-origin'
                    });
                    if (resp.ok) {
                        m.is_read = true;
                        this._recentlyRead.add(m.id);
                    }
                } catch (e) {}
            }));
        },
        
        handleTyping() {
            if (!this.selectedConversation) return;
            // Whisper if Echo available
            try {
                if (window.Echo) {
                    window.Echo.private(`conversation.${this.selectedConversation.id}`).whisper('typing', {
                        user: { id: this.currentUserId, name: document.querySelector('meta[name="user-name"]').content || 'Me' }
                    });
                }
            } catch (_) {}
            // Server typing update (debounced start)
            clearTimeout(this._typingTimer);
            this._typingTimer = setTimeout(async () => {
                try {
await fetch('/messages/typing', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content || '' },
                        credentials: 'same-origin',
                        body: JSON.stringify({ conversation_id: this.selectedConversation.id, is_typing: true })
                    });
                } catch (_) {}
            }, 120);
            // Schedule typing stop after idle
            clearTimeout(this._typingStopTimer);
            this._typingStopTimer = setTimeout(async () => {
                try {
await fetch('/messages/typing', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content || '' },
                        credentials: 'same-origin',
                        body: JSON.stringify({ conversation_id: this.selectedConversation.id, is_typing: false })
                    });
                } catch (_) {}
            }, 1500);
        },
        
        handleFileSelect(event) {
            const incoming = Array.from(event.target.files || []);
            this.addFiles(incoming);
            // reset input to allow re-selecting the same file
            if (this.$refs.fileInput) this.$refs.fileInput.value = '';
        },

        handleDrop(e) {
            const dt = e.dataTransfer;
            if (!dt || !dt.files || dt.files.length === 0) return;
            const files = Array.from(dt.files);
            this.addFiles(files);
        },

        addFiles(files) {
            const current = this.selectedFiles || [];
            // Prevent duplicates by name+size
            const existingKeys = new Set(current.map(f => `${f.name}-${f.size}`));
            const toAdd = [];
            for (const f of files) {
                const key = `${f.name}-${f.size}`;
                if (!existingKeys.has(key)) {
                    toAdd.push(f);
                    existingKeys.add(key);
                }
                if (current.length + toAdd.length >= 3) break;
            }
            this.selectedFiles = current.concat(toAdd).slice(0, 3);
        },

        removeSelectedFile(index) {
            if (!Array.isArray(this.selectedFiles)) return;
            this.selectedFiles.splice(index, 1);
            this.selectedFiles = [...this.selectedFiles];
        },
        
        getTypingText(conversationId) {
            const users = this.typingUsers[conversationId] || [];
            if (users.length === 0) return '';
            if (users.length === 1) return `${users[0]} is typing...`;
            return `${users.join(', ')} are typing...`;
        },
        
        // Reply handling
        replyTo: null,
        setReplyTo(message) {
            this.replyTo = message;
            this.$nextTick(() => {
                this.$refs.messageInput?.focus();
            });
        },
        clearReplyTo() { this.replyTo = null; },

        initiateCall(type) {
            this.showNotification(`${type} calling feature coming soon!`, 'info');
        },
        
        toggleReaction(message, emoji) {
            if (!message.reactions) message.reactions = {};
            const mine = message.my_reactions || new Set();
            const hasIt = mine.has(emoji);
            // optimistic update
            const prev = message.reactions[emoji] || 0;
            message.reactions[emoji] = hasIt ? Math.max(0, prev - 1) : prev + 1;
            if (hasIt) mine.delete(emoji); else mine.add(emoji);
            message.my_reactions = mine;
            // server call (best-effort)
fetch(`/messages/messages/${message.id}/reaction`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content || '' },
                credentials: 'same-origin',
                body: JSON.stringify({ emoji })
            }).catch(() => {});
        },

        retrySend(message) {
            // Remove the failed placeholder and resend its content
            const idx = this.messages.findIndex(m => m.id === message.id);
            if (idx !== -1) this.messages.splice(idx, 1);
            this.replyTo = message.reply_to ? { id: message.reply_to.id, content: message.reply_to.content, sender: { name: message.reply_to.sender_name } } : null;
            this.newMessage = message.content || '';
            this.$nextTick(() => this.sendMessage());
        },

        openLightbox(url) {
            this.lightbox = { show: true, url };
        },
        closeLightbox() {
            this.lightbox = { show: false, url: null };
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
