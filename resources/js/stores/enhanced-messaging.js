import { defineStore } from 'pinia'
import axios from 'axios'

// Centralize API base for v1 endpoints under marketplace prefix
const API_BASE = '/api/marketplace/v1'

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
    conversationChannels: new Map(), // conversationId -> Echo channel
    
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
        
const response = await axios.get(`${API_BASE}/conversations`)
        const conversations = (response.data.data?.conversations) || (response.data.conversations) || []
        
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
        
const response = await axios.get(`${API_BASE}/conversations/${conversationId}/messages`, {
          params: { page, per_page: 50 }
        })
        
        const apiData = response.data.data || response.data
        const { messages, pagination } = apiData
        
        const normalized = (messages || []).map((m) => this.normalizeMessage(m))
        
        // Store pagination info
        this.pagination.set(conversationId, pagination)
        
        if (page === 1) {
          // Replace messages
          this.messages.set(conversationId, normalized)
        } else {
          // Prepend older messages
          const existing = this.messages.get(conversationId) || []
          this.messages.set(conversationId, [...normalized, ...existing])
        }
        
        // Cache individual messages
        normalized.forEach(message => {
          this.messageCache.set(message.id, message)
          
          // Cache sender data
          if (message.sender) {
            this.users.set(message.sender.id, message.sender)
          }
        })
        
        return normalized
      }, `messages-${conversationId}`).finally(() => {
        this.loading.messages = false
      })
    },

    async sendMessage(conversationId, messageData) {
      return this.apiCall(async () => {
        this.loading.sending = true
        
        // Determine file and type
        let fileObj = null
        if (messageData.file) {
          fileObj = messageData.file
        } else if (Array.isArray(messageData.files) && messageData.files.length > 0) {
          const first = messageData.files[0]
          fileObj = first.file || first
        }
        
        let derivedType = messageData.type || 'text'
        if (!derivedType || derivedType === 'mixed') {
          if (fileObj && typeof fileObj.type === 'string') {
            if (fileObj.type.startsWith('image/')) derivedType = 'image'
            else if (fileObj.type.startsWith('audio/')) derivedType = 'voice'
            else derivedType = 'file'
          }
        }
        
        // Create optimistic message in UI-friendly shape
        const optimisticMessage = {
          id: `temp-${Date.now()}`,
          conversation_id: conversationId,
          content: messageData.content || '',
          type: derivedType,
          sender_id: window.authUser?.id,
          sender: window.authUser || null,
          created_at: new Date().toISOString(),
          is_mine: true,
          sending: true,
          status: 'sending',
          reply_to_id: messageData.reply_to_id || null,
        }
        
        // Best-effort preview for images
        if (derivedType === 'image' && Array.isArray(messageData.files) && messageData.files[0]?.preview) {
          optimisticMessage.file_url = messageData.files[0].preview
          optimisticMessage.file_name = messageData.files[0].name
          optimisticMessage.file_size = messageData.files[0].size
        } else if (derivedType === 'file' && Array.isArray(messageData.files) && messageData.files[0]) {
          const f = messageData.files[0]
          optimisticMessage.file_name = f.name
          optimisticMessage.file_size = f.size
        }
        
        // Add to local state immediately
        this.addMessageToConversation(conversationId, optimisticMessage)
        
        try {
          // Prepare form data for file uploads
          const formData = new FormData()
          formData.append('content', messageData.content || '')
          formData.append('type', derivedType)
          
          if (fileObj) {
            formData.append('file', fileObj)
          }
          
          if (messageData.reply_to_id) {
            formData.append('reply_to_id', messageData.reply_to_id)
          }
          
const response = await axios.post(
            `${API_BASE}/conversations/${conversationId}/messages`,
            formData,
            {
              headers: {
                'Content-Type': 'multipart/form-data'
              }
            }
          )
          
          const serverMessage = response.data.message || response.data.data || response.data
          const normalized = this.normalizeMessage(serverMessage)
          
          // Replace optimistic message with server response
          this.replaceMessage(conversationId, optimisticMessage.id, normalized)
          
          // Update conversation last message
          this.updateConversationLastMessage(conversationId, normalized)
          
          return normalized
          
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
await axios.patch(`${API_BASE}/conversations/${conversationId}/messages/read`, { mark_all: true })
        
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

    async markMessagesAsRead(conversationId, messageIds = []) {
      if (!Array.isArray(messageIds) || messageIds.length === 0) return
      return this.apiCall(async () => {
await axios.patch(`${API_BASE}/conversations/${conversationId}/messages/read`, { message_ids: messageIds })
        // Update local state for specific messages
        const messages = this.messages.get(conversationId) || []
        messages.forEach(m => {
          if (messageIds.includes(m.id)) {
            m.is_read_by_me = true
          }
        })
        this.messages.set(conversationId, messages)
      }, `read-some-${conversationId}`)
    },

    // Message management helpers
    addMessageToConversation(conversationId, message) {
      const messages = this.messages.get(conversationId) || []
      messages.push(message)
      this.messages.set(conversationId, messages)
      
      // Cache the message
      this.messageCache.set(message.id, message)
    },

    normalizeMessage(serverMessage) {
      // Accept both v1-shaped and legacy-shaped messages and normalize for UI
      const m = { ...serverMessage }
      
      // Flatten file info
      if (serverMessage.file && typeof serverMessage.file === 'object') {
        m.file_url = serverMessage.file.path
        m.file_name = serverMessage.file.name
        m.file_size = serverMessage.file.size
        m.file_type = serverMessage.file.type
      }
      
      // Ensure sender_id for UI checks
      if (!m.sender_id && serverMessage.sender && serverMessage.sender.id) {
        m.sender_id = serverMessage.sender.id
      }
      
      // Keep reactions as-is if present
      m.reactions = serverMessage.reactions || m.reactions || []
      
      return m
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

    // Add or update a conversation in local state
    upsertConversation(conversation) {
      if (!conversation || !conversation.id) return
      this.conversations.set(conversation.id, {
        ...conversation,
        last_fetched: Date.now(),
      })
      if (conversation.other_user) {
        this.users.set(conversation.other_user.id, conversation.other_user)
      }
    },

    async editMessage(messageId, newContent) {
      return this.apiCall(async () => {
const response = await axios.patch(`${API_BASE}/messages/${messageId}`, { content: newContent })
        const serverMessage = response.data.message || response.data.data || response.data
        const normalized = this.normalizeMessage(serverMessage)

        // Update cache
        this.messageCache.set(normalized.id, normalized)
        // Update in conversation messages
        for (const [cid, msgs] of this.messages.entries()) {
          const idx = (msgs || []).findIndex(m => m.id === normalized.id)
          if (idx !== -1) {
            msgs.splice(idx, 1, normalized)
            this.messages.set(cid, msgs)
            break
          }
        }
        return normalized
      }, `edit-${messageId}`)
    },

    // Real-time methods
initializeRealTime() {
      if (window.Echo) {
        window.Echo.private(`user.${window.authUser?.id}`)
          .listen('.message.sent', (event) => {
            this.handleNewMessage(this.normalizeMessage(event.message))
          })
          .listen('.message.read', (event) => {
            this.handleMessageRead(event)
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
    
handleMessageRead(event) {
      // Update message read status from payload
      const cachedMessage = this.messageCache.get(event.message_id)
      if (cachedMessage) {
        cachedMessage.is_read = !!event.is_read
        cachedMessage.read_by = event.read_by
        this.messageCache.set(event.message_id, cachedMessage)
      }
    },

    connectToConversationChannel(conversationId) {
      if (!window.Echo || !conversationId) return
      if (this.conversationChannels.has(conversationId)) return

      const channelName = `conversation.${conversationId}`
      const channel = window.Echo.private(channelName)

      // Typing whispers
      channel.listenForWhisper('typing', (event) => {
        if (!event?.user?.id) return
        if (event.user.id === window.authUser?.id) return
        this.handleTypingStatus(conversationId, event.user, true)
        setTimeout(() => {
          this.handleTypingStatus(conversationId, event.user, false)
        }, 3000)
      })

      channel.listenForWhisper('stop-typing', (event) => {
        if (!event?.user?.id) return
        this.handleTypingStatus(conversationId, event.user, false)
      })

      // Optional: read receipts on channel
      channel.listen('.message.read', (event) => {
        this.handleMessageRead(event)
      })

      this.conversationChannels.set(conversationId, channel)
    },

    leaveConversationChannel(conversationId) {
      if (!conversationId) return
      const channel = this.conversationChannels.get(conversationId)
      if (channel && window.Echo) {
        window.Echo.leave(`conversation.${conversationId}`)
      }
      this.conversationChannels.delete(conversationId)
    },

    sendTypingStatus(conversationId, isTyping) {
      const hasChannel = this.conversationChannels.has(conversationId)
      if (!hasChannel) {
        this.connectToConversationChannel(conversationId)
      }
      const channel = this.conversationChannels.get(conversationId)
      if (!channel) return
      const payload = { user: window.authUser, conversationId }
      if (isTyping) {
        channel.whisper('typing', payload)
      } else {
        channel.whisper('stop-typing', payload)
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
const response = await axios.get('/api/marketplace/users/online')
        const users = response.data.online_users || []
        
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
await axios.get(`${API_BASE}/system/health`)
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
      
      // Join real-time channel for this conversation
      if (conversationId) {
        this.connectToConversationChannel(conversationId)
      }
      
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

    // Reaction and deletion helpers
    async addReaction(messageId, emoji) {
      return this.apiCall(async () => {
const response = await axios.post(`${API_BASE}/messages/${messageId}/reactions`, { emoji })
        const reactions = response.data.reactions || response.data.data?.reactions || []
        this.updateMessageReactions(messageId, reactions)
        return reactions
      }, `reaction-${messageId}`)
    },

    async deleteMessage(messageId) {
      return this.apiCall(async () => {
await axios.delete(`${API_BASE}/messages/${messageId}`)
        // Find the conversation containing this message
        let convId = null
        for (const [cid, msgs] of this.messages.entries()) {
          if ((msgs || []).some(m => m.id === messageId)) {
            convId = cid
            break
          }
        }
        if (convId) {
          this.removeMessage(convId, messageId)
        }
      }, `delete-${messageId}`)
    },

    updateMessageReactions(messageId, reactions) {
      // Update cache
      const cached = this.messageCache.get(messageId)
      if (cached) {
        cached.reactions = reactions
        this.messageCache.set(messageId, cached)
      }
      // Update in conversation lists
      for (const [cid, msgs] of this.messages.entries()) {
        const idx = (msgs || []).findIndex(m => m.id === messageId)
        if (idx !== -1) {
          const updated = { ...msgs[idx], reactions }
          msgs.splice(idx, 1, updated)
          this.messages.set(cid, msgs)
          break
        }
      }
    },

    // Cleanup
    cleanup() {
      // Close WebSocket connections
      if (window.Echo) {
        window.Echo.leaveChannel(`user.${window.authUser?.id}`)
        // Leave all conversation channels
        for (const cid of this.conversationChannels.keys()) {
          window.Echo.leave(`conversation.${cid}`)
        }
      }
      this.conversationChannels.clear()
      
      // Clear sensitive data
      this.conversations.clear()
      this.messages.clear()
      this.messageCache.clear()
    }
  }
})
