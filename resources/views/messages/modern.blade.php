@extends('theme::app')

@section('title', 'Messages')

@section('content')
<div class="h-screen bg-slate-50 overflow-hidden" x-data="modernMessaging()" x-init="init()">
    <!-- Navigation Header -->
    <div class="h-16 bg-white border-b border-slate-200 flex items-center px-6 shadow-sm">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 4v-4z"></path>
                </svg>
            </div>
            <h1 class="text-lg font-semibold text-slate-900">Messages</h1>
        </div>
        
        <div class="ml-auto flex items-center space-x-3">
            <div class="flex items-center space-x-2 text-sm text-slate-500">
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                <span>Online</span>
            </div>
        </div>
    </div>

    <div class="flex h-full">
        <!-- Sidebar des conversations -->
        <div class="w-80 bg-white border-r border-slate-200 flex flex-col">
            <!-- Search & Actions -->
            <div class="p-4 border-b border-slate-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="relative flex-1">
                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input 
                            type="text" 
                            placeholder="Rechercher des conversations..." 
                            x-model="searchQuery"
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border-0 rounded-lg text-sm placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:ring-opacity-20 transition-all"
                        />
                    </div>
                    <button 
                        @click="showNewMessageModal = true"
                        class="ml-3 w-10 h-10 bg-blue-500 hover:bg-blue-600 text-white rounded-lg flex items-center justify-center transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </button>
                </div>

                <!-- Filter tabs -->
                <div class="flex space-x-1 bg-slate-100 p-1 rounded-lg">
                    <button class="flex-1 px-3 py-2 text-xs font-medium rounded-md bg-white text-slate-700 shadow-sm">
                        Tous
                    </button>
                    <button class="flex-1 px-3 py-2 text-xs font-medium rounded-md text-slate-500 hover:text-slate-700 hover:bg-white transition-colors">
                        Non lus
                    </button>
                    <button class="flex-1 px-3 py-2 text-xs font-medium rounded-md text-slate-500 hover:text-slate-700 hover:bg-white transition-colors">
                        Favoris
                    </button>
                </div>
            </div>

            <!-- Conversations List -->
            <div class="flex-1 overflow-y-auto">
                <template x-for="conversation in filteredConversations" :key="conversation.id">
                    <div 
                        class="px-4 py-3 hover:bg-slate-50 cursor-pointer border-l-4 transition-all"
                        :class="{ 
                            'bg-blue-50 border-l-blue-500': selectedConversation && selectedConversation.id === conversation.id,
                            'border-l-transparent': !selectedConversation || selectedConversation.id !== conversation.id,
                            'bg-blue-50/50': conversation.unread_count > 0
                        }"
                        @click="selectConversation(conversation)"
                    >
                        <div class="flex items-start space-x-3">
                            <!-- Avatar -->
                            <div class="relative flex-shrink-0">
                                <div class="w-11 h-11 bg-gradient-to-br from-slate-200 to-slate-300 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-semibold text-slate-600" x-text="conversation.title.charAt(0).toUpperCase()"></span>
                                </div>
                                <div 
                                    class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full border-2 border-white"
                                    :class="conversation.is_online ? 'bg-green-500' : 'bg-slate-300'"
                                ></div>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-1">
                                    <p class="text-sm font-medium text-slate-900 truncate" x-text="conversation.title"></p>
                                    <div class="flex items-center space-x-2">
                                        <template x-if="conversation.unread_count > 0">
                                            <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-blue-500 rounded-full" x-text="conversation.unread_count"></span>
                                        </template>
                                        <span class="text-xs text-slate-400" x-text="formatTime(conversation.last_activity)"></span>
                                    </div>
                                </div>
                                <p class="text-sm text-slate-500 truncate leading-5">
                                    <span x-show="conversation.last_message?.is_mine">Vous: </span>
                                    <span x-text="conversation.last_message?.content || 'Aucun message'"></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Loading state -->
                <template x-if="loading && conversations.length === 0">
                    <div class="p-8 text-center">
                        <div class="inline-block h-6 w-6 animate-spin rounded-full border-2 border-solid border-blue-500 border-r-transparent"></div>
                        <p class="mt-2 text-sm text-slate-500">Chargement des conversations...</p>
                    </div>
                </template>

                <!-- Empty state -->
                <template x-if="!loading && conversations.length === 0">
                    <div class="p-8 text-center">
                        <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-slate-900 mb-1">Aucune conversation</p>
                        <p class="text-xs text-slate-500 mb-4">Commencez votre première conversation</p>
                        <button 
                            @click="showNewMessageModal = true"
                            class="text-xs text-blue-500 hover:text-blue-600 font-medium"
                        >
                            Nouveau message
                        </button>
                    </div>
                </template>
            </div>
        </div>

        <!-- Zone de chat -->
        <div class="flex-1 flex flex-col bg-white">
            <template x-if="selectedConversation">
                <div class="flex flex-col h-full">
                    <!-- Header du chat -->
                    <div class="px-6 py-4 border-b border-slate-200 bg-white">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="relative">
                                    <div class="w-10 h-10 bg-gradient-to-br from-slate-200 to-slate-300 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-semibold text-slate-600" x-text="selectedConversation.title.charAt(0).toUpperCase()"></span>
                                    </div>
                                    <div 
                                        class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2 border-white"
                                        :class="selectedConversation.is_online ? 'bg-green-500' : 'bg-slate-300'"
                                    ></div>
                                </div>
                                <div>
                                    <h2 class="text-sm font-semibold text-slate-900" x-text="selectedConversation.title"></h2>
                                    <p class="text-xs text-slate-500">
                                        <span x-show="selectedConversation.is_online">En ligne</span>
                                        <span x-show="!selectedConversation.is_online">Hors ligne</span>
                                        <template x-if="isTyping">
                                            <span class="text-blue-500"> • en train d'écrire...</span>
                                        </template>
                                    </p>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center space-x-2">
                                <button class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                </button>
                                <button class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                </button>
                                <button class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Messages -->
                    <div class="flex-1 overflow-y-auto px-6 py-4 bg-slate-50" x-ref="messagesContainer">
                        <div class="space-y-4 max-w-4xl">
                            <template x-for="message in messages" :key="message.id">
                                <div class="flex" :class="message.is_mine ? 'justify-end' : 'justify-start'">
                                    <div class="flex items-end space-x-2 max-w-xs lg:max-w-md">
                                        <template x-if="!message.is_mine">
                                            <div class="w-6 h-6 bg-gradient-to-br from-slate-200 to-slate-300 rounded-full flex items-center justify-center flex-shrink-0">
                                                <span class="text-xs font-medium text-slate-600" x-text="message.sender_name?.charAt(0).toUpperCase()"></span>
                                            </div>
                                        </template>

                                        <div>
                                            <div 
                                                class="px-4 py-2.5 rounded-2xl shadow-sm"
                                                :class="message.is_mine 
                                                    ? 'bg-blue-500 text-white rounded-br-md' 
                                                    : 'bg-white text-slate-900 rounded-bl-md border border-slate-200'"
                                            >
                                                <p class="text-sm leading-relaxed" x-text="message.content"></p>
                                            </div>
                                            <div class="flex items-center mt-1 space-x-1">
                                                <span class="text-xs text-slate-400" x-text="formatTime(message.created_at)"></span>
                                                <template x-if="message.is_mine">
                                                    <div class="flex items-center">
                                                        <svg 
                                                            class="w-3 h-3 ml-1" 
                                                            :class="message.is_read ? 'text-blue-500' : 'text-slate-400'"
                                                            fill="currentColor" 
                                                            viewBox="0 0 20 20"
                                                        >
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        <template x-if="message.is_read">
                                                            <svg class="w-3 h-3 -ml-1 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                            </svg>
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>

                                        <template x-if="message.is_mine">
                                            <div class="w-6 h-6 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                                                <span class="text-xs font-medium text-white">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <!-- Typing indicator -->
                            <template x-if="isTyping">
                                <div class="flex justify-start">
                                    <div class="flex items-end space-x-2">
                                        <div class="w-6 h-6 bg-gradient-to-br from-slate-200 to-slate-300 rounded-full flex items-center justify-center flex-shrink-0">
                                            <span class="text-xs font-medium text-slate-600" x-text="selectedConversation.title.charAt(0).toUpperCase()"></span>
                                        </div>
                                        <div class="bg-white rounded-2xl rounded-bl-md border border-slate-200 px-4 py-2.5">
                                            <div class="flex space-x-1">
                                                <div class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce"></div>
                                                <div class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                                <div class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Input area -->
                    <div class="px-6 py-4 bg-white border-t border-slate-200">
                        <div class="flex items-end space-x-3">
                            <!-- Attachment button -->
                            <button class="p-2.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                </svg>
                            </button>

                            <!-- Message input -->
                            <div class="flex-1">
                                <textarea 
                                    x-model="newMessage"
                                    @keydown="handleKeydown($event)"
                                    @input="handleTyping()"
                                    placeholder="Tapez votre message..."
                                    rows="1"
                                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl resize-none focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-sm"
                                    style="max-height: 120px;"
                                ></textarea>
                            </div>

                            <!-- Send button -->
                            <button 
                                @click="sendMessage()"
                                :disabled="!newMessage.trim()"
                                class="p-2.5 rounded-lg transition-all flex-shrink-0"
                                :class="newMessage.trim() 
                                    ? 'bg-blue-500 hover:bg-blue-600 text-white shadow-sm' 
                                    : 'bg-slate-100 text-slate-400 cursor-not-allowed'"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Empty state -->
            <template x-if="!selectedConversation">
                <div class="flex-1 flex items-center justify-center bg-slate-50">
                    <div class="text-center max-w-sm">
                        <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 mb-2">Bienvenue dans Messages</h3>
                        <p class="text-slate-500 text-sm leading-relaxed mb-6">Sélectionnez une conversation pour commencer à échanger ou créez une nouvelle conversation.</p>
                        <button 
                            @click="showNewMessageModal = true"
                            class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition-colors"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Nouveau message
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Modal nouveau message -->
    <div x-show="showNewMessageModal" x-cloak class="fixed inset-0 bg-slate-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg" @click.away="showNewMessageModal = false" x-transition>
            <div class="p-6 border-b border-slate-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-slate-900">Nouveau message</h3>
                    <button @click="showNewMessageModal = false" class="p-1 text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <form @submit.prevent="startNewConversation()" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">À:</label>
                    <input 
                        type="text" 
                        x-model="newMessageRecipient"
                        placeholder="Nom d'utilisateur ou email..."
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-sm"
                        required
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Message:</label>
                    <textarea 
                        x-model="newMessageContent"
                        placeholder="Tapez votre message..."
                        rows="3"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-lg resize-none focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-sm"
                        required
                    ></textarea>
                </div>
                <div class="flex justify-end space-x-3 pt-2">
                    <button 
                        type="button" 
                        @click="showNewMessageModal = false"
                        class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors"
                    >
                        Annuler
                    </button>
                    <button 
                        type="submit"
                        class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition-colors"
                    >
                        Envoyer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function modernMessaging() {
    return {
        conversations: [],
        selectedConversation: null,
        messages: [],
        newMessage: '',
        searchQuery: '',
        loading: false,
        sending: false,
        isTyping: false,
        typingTimeout: null,
        showNewMessageModal: false,
        newMessageRecipient: '',
        newMessageContent: '',
        currentUserId: {{ auth()->id() }},

        init() {
            this.loadConversations();
            this.setupEventListeners();
        },

        setupEventListeners() {
            // Auto-resize textarea
            this.$watch('newMessage', () => {
                this.$nextTick(() => {
                    const textarea = this.$el.querySelector('textarea');
                    if (textarea) {
                        textarea.style.height = 'auto';
                        textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
                    }
                });
            });
        },

        async loadConversations() {
            this.loading = true;
            try {
                const response = await fetch('/messages/conversations', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    this.conversations = data.conversations || [];
                } else {
                    // Demo data
                    this.conversations = [
                        {
                            id: 1,
                            title: 'Bot de bienvenue',
                            last_message: { content: 'Bienvenue dans votre nouvelle interface de messagerie !', is_mine: false },
                            last_activity: new Date().toISOString(),
                            unread_count: 1,
                            is_online: true
                        },
                        {
                            id: 2,
                            title: 'Support technique',
                            last_message: { content: 'Comment puis-je vous aider aujourd\'hui ?', is_mine: false },
                            last_activity: new Date(Date.now() - 3600000).toISOString(),
                            unread_count: 0,
                            is_online: false
                        }
                    ];
                }
            } catch (error) {
                console.error('Erreur lors du chargement des conversations:', error);
            }
            this.loading = false;
        },

        async selectConversation(conversation) {
            this.selectedConversation = conversation;
            this.loadMessages(conversation.id);
            
            // Mark as read
            if (conversation.unread_count > 0) {
                conversation.unread_count = 0;
            }
        },

        async loadMessages(conversationId) {
            try {
                const response = await fetch(`/messages/conversations/${conversationId}/messages`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (response.ok) {
                    const data = await response.json();
                    this.messages = data.messages || [];
                } else {
                    // Demo messages
                    this.messages = [
                        {
                            id: 1,
                            content: 'Bienvenue dans votre nouvelle interface de messagerie ! 🎉',
                            sender_name: 'Bot',
                            is_mine: false,
                            is_read: true,
                            created_at: new Date(Date.now() - 300000).toISOString()
                        },
                        {
                            id: 2,
                            content: 'Cette interface est moderne et professionnelle.',
                            sender_name: 'Bot',
                            is_mine: false,
                            is_read: true,
                            created_at: new Date(Date.now() - 240000).toISOString()
                        },
                        {
                            id: 3,
                            content: 'Merci pour cette belle interface !',
                            sender_name: 'Vous',
                            is_mine: true,
                            is_read: false,
                            created_at: new Date(Date.now() - 120000).toISOString()
                        }
                    ];
                }
                
                this.$nextTick(() => {
                    this.scrollToBottom();
                });
            } catch (error) {
                console.error('Erreur lors du chargement des messages:', error);
            }
        },

        async sendMessage() {
            if (!this.newMessage.trim() || !this.selectedConversation) return;
            
            const messageContent = this.newMessage.trim();
            this.newMessage = '';
            
            // Add message optimistically
            const newMessage = {
                id: Date.now(),
                content: messageContent,
                sender_name: 'Vous',
                is_mine: true,
                is_read: false,
                created_at: new Date().toISOString()
            };
            
            this.messages.push(newMessage);
            this.$nextTick(() => {
                this.scrollToBottom();
            });
            
            // Update conversation
            this.selectedConversation.last_message = newMessage;
            this.selectedConversation.last_activity = newMessage.created_at;
            
            try {
                const response = await fetch('/messages/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        conversation_id: this.selectedConversation.id,
                        content: messageContent
                    })
                });
                
                if (!response.ok) {
                    throw new Error('Erreur lors de l\'envoi');
                }
            } catch (error) {
                console.error('Erreur lors de l\'envoi du message:', error);
            }
        },

        async startNewConversation() {
            if (!this.newMessageRecipient.trim() || !this.newMessageContent.trim()) return;
            
            try {
                const response = await fetch('/messages/conversations', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        recipient: this.newMessageRecipient.trim(),
                        message: this.newMessageContent.trim()
                    })
                });
                
                if (response.ok) {
                    const data = await response.json();
                    this.showNewMessageModal = false;
                    this.newMessageRecipient = '';
                    this.newMessageContent = '';
                    this.loadConversations();
                } else {
                    // Demo: create fake conversation
                    const newConversation = {
                        id: Date.now(),
                        title: this.newMessageRecipient,
                        last_message: { content: this.newMessageContent, is_mine: true },
                        last_activity: new Date().toISOString(),
                        unread_count: 0,
                        is_online: false
                    };
                    this.conversations.unshift(newConversation);
                    this.selectConversation(newConversation);
                    this.showNewMessageModal = false;
                    this.newMessageRecipient = '';
                    this.newMessageContent = '';
                }
            } catch (error) {
                console.error('Erreur lors de la création de la conversation:', error);
            }
        },

        handleKeydown(event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                this.sendMessage();
            }
        },

        handleTyping() {
            if (this.typingTimeout) {
                clearTimeout(this.typingTimeout);
            }
            
            this.typingTimeout = setTimeout(() => {
                // Stop typing indicator
            }, 2000);
        },

        scrollToBottom() {
            const container = this.$refs.messagesContainer;
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        },

        formatTime(timestamp) {
            const date = new Date(timestamp);
            const now = new Date();
            const diff = now - date;
            
            if (diff < 60000) return 'À l\'instant';
            if (diff < 3600000) return Math.floor(diff / 60000) + 'm';
            if (diff < 86400000) return Math.floor(diff / 3600000) + 'h';
            return date.toLocaleDateString('fr-FR');
        },

        get filteredConversations() {
            if (!this.searchQuery) return this.conversations;
            
            const query = this.searchQuery.toLowerCase();
            return this.conversations.filter(conv => 
                conv.title.toLowerCase().includes(query) ||
                conv.last_message?.content?.toLowerCase().includes(query)
            );
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }

.animate-bounce {
    animation: bounce 1s infinite;
}

@keyframes bounce {
    0%, 20%, 53%, 80%, 100% {
        animation-timing-function: cubic-bezier(0.215, 0.61, 0.355, 1);
        transform: translate3d(0, 0, 0);
    }
    40%, 43% {
        animation-timing-function: cubic-bezier(0.755, 0.05, 0.855, 0.06);
        transform: translate3d(0, -10px, 0);
    }
    70% {
        animation-timing-function: cubic-bezier(0.755, 0.05, 0.855, 0.06);
        transform: translate3d(0, -5px, 0);
    }
    90% {
        transform: translate3d(0, -2px, 0);
    }
}

/* Custom scrollbar */
.overflow-y-auto::-webkit-scrollbar {
    width: 4px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: transparent;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 2px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
@endsection
