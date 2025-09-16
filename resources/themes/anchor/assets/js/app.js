// Do not statically import Alpine to avoid duplicate instances with Livewire
// We'll register plugins on 'alpine:init' and dynamically import Alpine only if needed.
import persist from '@alpinejs/persist';
import collapse from '@alpinejs/collapse';
// Use the shared Echo instance initialized elsewhere if available

// Safely integrate Alpine to avoid multiple instances
// - If Livewire or another script has already loaded Alpine, just attach our plugins on alpine:init
// - Otherwise, start Alpine ourselves (once)
const setupAlpinePlugins = () => {
    try {
        const A = window.Alpine;
        if (!A) return;
        if (!A.__onlyverified_persist) { A.plugin(persist); A.__onlyverified_persist = true; }
        if (!A.__onlyverified_collapse) { A.plugin(collapse); A.__onlyverified_collapse = true; }
    } catch (_) {}
};

document.addEventListener('alpine:init', setupAlpinePlugins);

(async () => {
    if (!window.Alpine) {
        try {
            const mod = await import('alpinejs');
            window.Alpine = mod.default;
            setupAlpinePlugins();
            window.Alpine.start();
        } catch (e) {
            console.warn('Failed to dynamically load Alpine:', e);
        }
    }
})();

// Define global Alpine component for real-time messaging before Alpine starts
window.realTimeMessagingApp = function () {
    return {
        // State
        loading: true,
        connectionStatus: 'connecting',
        connectionStatusText: 'Connecting...',
        conversations: [],
        searchQuery: '',
        showUserSearch: false,
        userSearchQuery: '',
        userSearchResults: [],
        selectedConversation: null,
        typingUsers: {}, // { [conversationId]: [{ id, name }] }
        messages: [],
        loadingMessages: false,
        newMessage: '',
        selectedFiles: [],
        sendingMessage: false,
        notification: { show: false, type: 'info', message: '' },

        // Computed
        get filteredConversations() {
            if (!this.searchQuery) return this.conversations
            const q = this.searchQuery.toLowerCase()
            return this.conversations.filter(c =>
                (c.title || '').toLowerCase().includes(q) ||
                (c.last_message?.content || '').toLowerCase().includes(q)
            )
        },

        // Lifecycle
        async init() {
            // Connection indicator (initial check)
            const isConnected = !!(window.Echo && window.Echo.connector?.pusher?.connection?.state === 'connected')
            this.connectionStatus = isConnected ? 'connected' : 'connecting'
            this.connectionStatusText = isConnected ? 'Connected' : 'Connecting...'

            // React to Echo connection events
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

            // Load conversations (best-effort)
            await this.fetchConversations()
            this.loading = false
        },

        // Data loaders
        async fetchConversations() {
            try {
                const resp = await fetch('/messages/conversations', {
                    headers: { 'Accept': 'application/json' }
                })
                if (resp.ok) {
                    const data = await resp.json()
                    this.conversations = data.conversations || data.data?.conversations || []
                }
            } catch (e) {
                this.notify('error', 'Failed to load conversations')
            }
        },

        async loadMessages(page = 1) {
            if (!this.selectedConversation) return
            this.loadingMessages = true
            try {
                const resp = await fetch(`/messages/conversations/${this.selectedConversation.id}/messages?page=${page}`, {
                    headers: { 'Accept': 'application/json' }
                })
                const data = await resp.json()
                this.messages = data.messages || data.data?.messages || []
            } catch (e) {
                this.notify('error', 'Failed to load messages')
            } finally {
                this.loadingMessages = false
            }
        },

        // UI actions
        async selectConversation(conversation) {
            this.selectedConversation = conversation
            await this.loadMessages()
            this.scrollToBottom()
        },

        async sendMessage() {
            const content = (this.newMessage || '').trim()
            if (!this.selectedConversation || (!content && this.selectedFiles.length === 0)) return
            this.sendingMessage = true
            try {
                const form = new FormData()
                form.append('conversation_id', this.selectedConversation.id)
                if (content) form.append('content', content)
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                const resp = await fetch('/messages/send', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    body: form,
                })
                const data = await resp.json()
                if (resp.ok) {
                    const msg = data.message || data.data || data
                    this.messages.push(msg)
                    this.newMessage = ''
                    this.scrollToBottom()
                } else {
                    this.notify('error', data.message || 'Failed to send message')
                }
            } catch (e) {
                this.notify('error', 'Failed to send message')
            } finally {
                this.sendingMessage = false
            }
        },

        searchConversations() {},

        async searchUsers() {
            if (!this.userSearchQuery || this.userSearchQuery.length < 2) {
                this.userSearchResults = []
                return
            }
            try {
                const resp = await fetch(`/messages/search-users?q=${encodeURIComponent(this.userSearchQuery)}`, {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin'
                })
                const data = await resp.json()
                this.userSearchResults = data.users || []
            } catch (e) {
                this.userSearchResults = []
            }
        },

        async startConversation(user) {
            try {
                // Try to find an existing conversation with this user
                let existing = (this.conversations || []).find(c =>
                    c.other_user_id === user.id || (c.title && c.title === user.name)
                )
                if (existing) {
                    await this.selectConversation(existing)
                    this.showUserSearch = false
                    return
                }

                // Ensure we have a valid CSRF token
                const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''

                // Ask backend to create/find a conversation without sending a first message
                // Controller will create the conversation when recipient_id is provided
                const resp = await fetch('/messages/send', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ recipient_id: user.id, message_type: 'text', content: '' }),
                    credentials: 'same-origin'
                })
                // Ignore response body; conversation may be created even if no message

                // Refresh list and select
                await this.fetchConversations()
                existing = (this.conversations || []).find(c => c.other_user_id === user.id || (c.title && c.title === user.name))
                if (existing) {
                    await this.selectConversation(existing)
                    this.showUserSearch = false
                } else {
                    this.notify('error', 'Could not open conversation')
                }
            } catch (e) {
                this.notify('error', 'Failed to start conversation')
            }
        },

        handleTyping() {},

        // Helpers
        getTypingText(conversationId) {
            const list = this.typingUsers[conversationId] || []
            if (list.length === 0) return ''
            if (list.length === 1) return list[0].name || 'Someone'
            if (list.length === 2) return `${list[0].name} and ${list[1].name}`
            return `${list[0].name} and ${list.length - 1} others`
        },

        scrollToBottom() {
            requestAnimationFrame(() => {
                const el = this.$refs?.messagesContainer
                if (el) el.scrollTop = el.scrollHeight
            })
        },

        notify(type, message) {
            this.notification = { show: true, type, message }
            setTimeout(() => (this.notification.show = false), 3000)
        },
    }
}

// Alpine is already started above if it wasn't present. Do not start again to avoid duplicates.

window.demoButtonClickMessage = function(event){
    event.preventDefault(); new FilamentNotification().title('Modify this button in your theme folder').icon('heroicon-o-pencil-square').iconColor('info').send()
}

// Import WebRTC functionality
import './webrtc.js';

// Global page transitions across dashboard pages
import './page-transitions.js';

// Debug logging for Echo
if (window.Echo && window.Echo.connector?.pusher?.connection) {
    console.log('🔌 Laravel Echo available');
    window.Echo.connector.pusher.connection.bind('connected', () => {
        console.log('✅ WebSocket connected successfully');
    });
    window.Echo.connector.pusher.connection.bind('disconnected', () => {
        console.log('❌ WebSocket disconnected');
    });
    window.Echo.connector.pusher.connection.bind('error', (error) => {
        console.error('❌ WebSocket error:', error);
    });
} else {
    console.warn('⚠️ Laravel Echo not initialized on this page');
}
