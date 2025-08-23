import { defineStore } from 'pinia'
import axios from 'axios'

export const useEnhancedMessagingStore = defineStore('enhanced-messaging', {
  state: () => ({
    // Core data
    conversations: new Map(),
    messages: new Map(), // conversationId -> messages array
    users: new Map(), // userId -> user data
    
    // UI state
    activeConversationId: null,
    selectedMessages: new Set(),
    searchQuery: '',
    filters: {
      unread: false,
      starred: false,
      folder: null,
    },
    
    // Real-time state
    onlineUsers: new Set(),
    typingUsers: new Map(), // conversationId -> Set of user IDs
    
    // Loading states
    loading: {
      conversations: false,
      messages: false,
      sending: false,
      initializing: false,
    },
    
    // Error handling
    errors: new Map(),
    lastError: null,
    
    // Performance tracking
    pagination: new Map(), // conversationId -> pagination info
    messageCache: new Map(), // messageId -> message
    
    // Connection state
    isConnected: true,
    reconnectAttempts: 0,
    maxReconnectAttempts: 5,
    
    // User preferences
    settings: {
      notifications: true,
      sounds: true,
      theme: 'light',
      compactMode: false,
    }
  }),

  getters: {
    // Conversation getters
    conversationsList: (state) => Array.from(state.conversations.values()),
    
    sortedConversations: (state) => {
      const conversations = Array.from(state.conversations.values())
      return conversations.sort((a, b) => {
        const aTime = new Date(a.last_message?.created_at || a.updated_at)
        const bTime = new Date(b.last_message?.created_at || b.updated_at)
        return bTime - aTime
      })
    },
    
    filteredConversations() {
      let conversations = this.sortedConversations
      
      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase()
        conversations = conversations.filter(conv => 
          conv.title?.toLowerCase().includes(query) ||
          conv.last_message?.content?.toLowerCase().includes(query)
        )
      }
      
      if (this.filters.unread) {
        conversations = conversations.filter(conv => (conv.unread_count || 0) > 0)
      }
      
      if (this.filters.starred) {
        conversations = conversations.filter(conv => conv.is_starred)
      }
      
      return conversations
    },

    // Message getters
    getConversationMessages: (state) => (conversationId) => {
      return state.messages.get(conversationId) || []
    },
    
    activeConversation: (state) => {
      return state.activeConversationId 
        ? state.conversations.get(state.activeConversationId)
        : null
    },
    
    activeMessages() {
      return this.activeConversationId 
        ? this.getConversationMessages(this.activeConversationId)
        : []
    },

    // Statistics
    totalUnreadCount: (state) => {
      return Array.from(state.conversations.values())
        .reduce((count, conv) => count + (conv.unread_count || 0), 0)
    },
    
    isTyping: (state) => (conversationId) => {
      const typingUsers = state.typingUsers.get(conversationId)
      return typingUsers ? Array.from(typingUsers) : []
    },
    
    hasError: (state) => (key) => {
      return state.errors.has(key)
    },
    
    getError: (state) => (key) => {
      return state.errors.get(key)
    },
    
    isOnline: (state) => (userId) => {
      return state.onlineUsers.has(userId)
    }
  },

  actions: {
    // Initialization
    async initialize() {
      this.loading.initializing = true
      this.clearError('initialization')
      
      try {
        // Load user preferences from localStorage
        this.loadSettings()
        
        // Fetch initial data
        await Promise.allSettled([
          this.fetchConversations(),
          this.fetchOnlineUsers(),
        ])
        
        // Initialize real-time connection
        this.initializeRealTime()
        
      } catch (error) {
        this.setError('initialization', error)
        throw error
      } finally {
        this.loading.initializing = false
      }
    },

    // Settings management
    loadSettings() {
      try {
        const saved = localStorage.getItem('messaging-settings')
        if (saved) {
          this.settings = { ...this.settings, ...JSON.parse(saved) }
        }
      } catch (error) {
        console.warn('Failed to load settings:', error)
      }
    },
    
    saveSettings() {
      try {
        localStorage.setItem('messaging-settings', JSON.stringify(this.settings))
      } catch (error) {
        console.warn('Failed to save settings:', error)
      }
    },
    
    updateSettings(newSettings) {
      this.settings = { ...this.settings, ...newSettings }
      this.saveSettings()
    },

    // API calls with error handling
    async apiCall(fn, errorKey) {
      this.clearError(errorKey)
      
      try {
        return await fn()
      } catch (error) {
        this.setError(errorKey, error)
        
        // Check if it's a network error
        if (!error.response) {
          this.handleConnectionError()
        }
        
        throw error
      }
    },

    // Conversation management
    async fetchConversations() {
      return this.apiCall(async () => {
        this.loading.conversations = true
        
        const response = await axios.get('/api/conversations')
        const conversations = response.data.data?.conversations || []
        
        // Store conversations in Map for better performance
        conversations.forEach(conv => {
          this.conversations.set(conv.id, {
            ...conv,
            last_fetched: Date.now(),
          })
          
          // Cache user data
          if (conv.other_user) {
            this.users.set(conv.other_user.id, conv.other_user)
          }
        })
        
        return conversations
      }, 'conversations').finally(() => {
        this.loading.conversations = false
      })
    },

    async fetchMessages(conversationId, page = 1) {
      return this.apiCall(async () => {
        this.loading.messages = true
        
        const response = await axios.get(`/api/conversations/${conversationId}/messages`, {
          params: { page, per_page: 50 }
        })
        
        const { messages, pagination } = response.data.data
        
        // Store pagination info
        this.pagination.set(conversationId, pagination)
        
        if (page === 1) {
          // Replace messages
          this.messages.set(conversationId, messages)
        } else {
          // Prepend older messages
          const existing = this.messages.get(conversationId) || []
          this.messages.set(conversationId, [...messages, ...existing])
        }
        
        // Cache individual messages
        messages.forEach(message => {
          this.messageCache.set(message.id, message)
          
          // Cache sender data
          if (message.sender) {
            this.users.set(message.sender.id, message.sender)
          }
        })
        
        return messages
      }, `messages-${conversationId}`).finally(() => {
        this.loading.messages = false
      })
    },

    async sendMessage(conversationId, messageData) {
      return this.apiCall(async () => {
        this.loading.sending = true
        
        // Create optimistic message
        const optimisticMessage = {
          id: `temp-${Date.now()}`,
          conversation_id: conversationId,
          content: messageData.content,
          message_type: messageData.type || 'text',
          sender_id: window.authUser?.id,
          sender: window.authUser,
          created_at: new Date().toISOString(),
          is_mine: true,
          sending: true,
          ...messageData
        }
        
        // Add to local state immediately
        this.addMessageToConversation(conversationId, optimisticMessage)
        
        try {
          // Prepare form data for file uploads
          const formData = new FormData()
          formData.append('content', messageData.content || '')
          formData.append('message_type', messageData.type || 'text')
          
          if (messageData.file) {
            formData.append('file', messageData.file)
          }
          
          if (messageData.reply_to_id) {
            formData.append('reply_to_id', messageData.reply_to_id)
          }
          
          const response = await axios.post(
            `/api/conversations/${conversationId}/messages`,
            formData,
            {
              headers: {
                'Content-Type': 'multipart/form-data'
              }
            }
          )
          
          const serverMessage = response.data.data
          
          // Replace optimistic message with server response
          this.replaceMessage(conversationId, optimisticMessage.id, serverMessage)
          
          // Update conversation last message
          this.updateConversationLastMessage(conversationId, serverMessage)
          
          return serverMessage
          
        } catch (error) {
          // Remove optimistic message on error
          this.removeMessage(conversationId, optimisticMessage.id)
          throw error
        }
      }, 'sending').finally(() => {
        this.loading.sending = false
      })
    },

    async markAsRead(conversationId) {
      return this.apiCall(async () => {
        await axios.post(`/api/conversations/${conversationId}/read`)
        
        // Update local state
        const conversation = this.conversations.get(conversationId)
        if (conversation) {
          conversation.unread_count = 0
          this.conversations.set(conversationId, conversation)
        }
        
        // Mark messages as read
        const messages = this.messages.get(conversationId) || []
        messages.forEach(message => {
          if (!message.is_mine && !message.is_read_by_me) {
            message.is_read_by_me = true
          }
        })
      }, `read-${conversationId}`)
    },

    // Message management helpers
    addMessageToConversation(conversationId, message) {
      const messages = this.messages.get(conversationId) || []
      messages.push(message)
      this.messages.set(conversationId, messages)
      
      // Cache the message
      this.messageCache.set(message.id, message)
    },
    
    replaceMessage(conversationId, oldMessageId, newMessage) {
      const messages = this.messages.get(conversationId) || []
      const index = messages.findIndex(m => m.id === oldMessageId)
      
      if (index !== -1) {
        messages[index] = newMessage
        this.messages.set(conversationId, messages)
        
        // Update cache
        this.messageCache.delete(oldMessageId)
        this.messageCache.set(newMessage.id, newMessage)
      }
    },
    
    removeMessage(conversationId, messageId) {
      const messages = this.messages.get(conversationId) || []
      const filtered = messages.filter(m => m.id !== messageId)
      this.messages.set(conversationId, filtered)
      
      // Remove from cache
      this.messageCache.delete(messageId)
    },
    
    updateConversationLastMessage(conversationId, message) {
      const conversation = this.conversations.get(conversationId)
      if (conversation) {
        conversation.last_message = message
        conversation.updated_at = message.created_at
        this.conversations.set(conversationId, conversation)
      }
    },

    // Real-time methods
    initializeRealTime() {
      if (window.Echo) {
        // Listen for new messages
        window.Echo.private(`user.${window.authUser?.id}`)
          .listen('MessageSent', (event) => {
            this.handleNewMessage(event.message)
          })
          .listen('MessageRead', (event) => {
            this.handleMessageRead(event.message)
          })
          .listen('UserOnlineStatusChanged', (event) => {
            this.handleUserOnlineStatus(event.user, event.isOnline)
          })
          .listen('UserTyping', (event) => {
            this.handleTypingStatus(event.conversationId, event.user, event.isTyping)
          })
      }
    },
    
    handleNewMessage(message) {
      // Don't add our own messages (they're already there from optimistic updates)
      if (message.sender_id === window.authUser?.id) {
        return
      }
      
      this.addMessageToConversation(message.conversation_id, message)
      this.updateConversationLastMessage(message.conversation_id, message)
      
      // Increment unread count
      const conversation = this.conversations.get(message.conversation_id)
      if (conversation && this.activeConversationId !== message.conversation_id) {
        conversation.unread_count = (conversation.unread_count || 0) + 1
        this.conversations.set(message.conversation_id, conversation)
      }
      
      // Show notification if enabled
      if (this.settings.notifications && 'Notification' in window) {
        this.showNotification(message)
      }
    },
    
    handleMessageRead(message) {
      // Update message read status
      const cachedMessage = this.messageCache.get(message.id)
      if (cachedMessage) {
        cachedMessage.is_read = true
        this.messageCache.set(message.id, cachedMessage)
      }
    },
    
    handleUserOnlineStatus(user, isOnline) {
      if (isOnline) {
        this.onlineUsers.add(user.id)
      } else {
        this.onlineUsers.delete(user.id)
      }
      
      // Update cached user data
      this.users.set(user.id, { ...this.users.get(user.id), ...user })
    },
    
    handleTypingStatus(conversationId, user, isTyping) {
      if (!this.typingUsers.has(conversationId)) {
        this.typingUsers.set(conversationId, new Set())
      }
      
      const typingSet = this.typingUsers.get(conversationId)
      
      if (isTyping) {
        typingSet.add(user.id)
      } else {
        typingSet.delete(user.id)
      }
    },

    // Utility methods
    async fetchOnlineUsers() {
      return this.apiCall(async () => {
        const response = await axios.get('/api/users/online')
        const users = response.data.data?.online_users || []
        
        users.forEach(user => {
          this.onlineUsers.add(user.id)
          this.users.set(user.id, user)
        })
        
        return users
      }, 'online-users')
    },
    
    showNotification(message) {
      if (Notification.permission === 'granted') {
        new Notification(message.sender?.name || 'New message', {
          body: message.content,
          icon: message.sender?.avatar || '/images/default-avatar.png'
        })
      }
    },
    
    // Error handling
    setError(key, error) {
      const errorInfo = {
        message: error.response?.data?.message || error.message,
        code: error.response?.status,
        timestamp: Date.now(),
        details: error.response?.data
      }
      
      this.errors.set(key, errorInfo)
      this.lastError = errorInfo
      
      console.error(`Error in ${key}:`, error)
    },
    
    clearError(key) {
      this.errors.delete(key)
      if (this.lastError && this.errors.size === 0) {
        this.lastError = null
      }
    },
    
    clearAllErrors() {
      this.errors.clear()
      this.lastError = null
    },
    
    handleConnectionError() {
      this.isConnected = false
      this.reconnectAttempts++
      
      if (this.reconnectAttempts < this.maxReconnectAttempts) {
        setTimeout(() => {
          this.attemptReconnection()
        }, Math.pow(2, this.reconnectAttempts) * 1000) // Exponential backoff
      }
    },
    
    async attemptReconnection() {
      try {
        await axios.get('/api/health')
        this.isConnected = true
        this.reconnectAttempts = 0
        this.clearError('connection')
      } catch (error) {
        this.handleConnectionError()
      }
    },

    // UI state management
    setActiveConversation(conversationId) {
      this.activeConversationId = conversationId
      
      // Mark as read when opened
      if (conversationId) {
        this.markAsRead(conversationId)
      }
    },
    
    toggleMessageSelection(messageId) {
      if (this.selectedMessages.has(messageId)) {
        this.selectedMessages.delete(messageId)
      } else {
        this.selectedMessages.add(messageId)
      }
    },
    
    clearSelectedMessages() {
      this.selectedMessages.clear()
    },
    
    setSearchQuery(query) {
      this.searchQuery = query
    },
    
    setFilters(filters) {
      this.filters = { ...this.filters, ...filters }
    },

    // Cleanup
    cleanup() {
      // Close WebSocket connections
      if (window.Echo) {
        window.Echo.leaveChannel(`user.${window.authUser?.id}`)
      }
      
      // Clear sensitive data
      this.conversations.clear()
      this.messages.clear()
      this.messageCache.clear()
    }
  }
})
