import { defineStore } from 'pinia'
import axios from 'axios'

export const useMessagingStore = defineStore('messaging', {
  state: () => ({
    conversations: [],
    messages: {},
    folders: [],
    contacts: [],
    onlineUsers: [],
    typingUsers: [],
    currentUser: null,
    loading: {
      conversations: false,
      messages: false,
      sending: false
    },
    errors: {}
  }),

  getters: {
    getConversation: (state) => (conversationId) => {
      return state.conversations.find(conv => conv.id === conversationId)
    },

    getMessages: (state) => (conversationId) => {
      return state.messages[conversationId] || []
    },

    getUnreadCount: (state) => {
      return state.conversations.reduce((count, conv) => count + (conv.unread_count || 0), 0)
    },

    getSortedConversations: (state) => {
      return [...state.conversations].sort((a, b) => {
        const aTime = new Date(a.last_message?.created_at || a.updated_at)
        const bTime = new Date(b.last_message?.created_at || b.updated_at)
        return bTime - aTime
      })
    }
  },

  actions: {
    // Initialize store
    async initialize() {
      this.currentUser = window.authUser || { id: null, name: '', email: '' }
      await Promise.all([
        this.fetchConversations(),
        this.fetchContacts(),
        this.fetchFolders()
      ])
    },

    // Fetch conversations
    async fetchConversations() {
      this.loading.conversations = true
      try {
        const response = await axios.get('/api/conversations')
        this.conversations = response.data.conversations || []
        return this.conversations
      } catch (error) {
        console.error('Failed to fetch conversations:', error)
        this.errors.conversations = error.message
        throw error
      } finally {
        this.loading.conversations = false
      }
    },

    // Fetch messages for a conversation
    async fetchMessages(conversationId, page = 1) {
      this.loading.messages = true
      try {
        const response = await axios.get(`/api/conversations/${conversationId}/messages`, {
          params: { page }
        })
        
        const messages = response.data.messages || []
        
        if (page === 1) {
          this.messages[conversationId] = messages
        } else {
          // Prepend older messages for pagination
          this.messages[conversationId] = [...messages, ...(this.messages[conversationId] || [])]
        }
        
        return messages
      } catch (error) {
        console.error('Failed to fetch messages:', error)
        this.errors.messages = error.message
        throw error
      } finally {
        this.loading.messages = false
      }
    },

    // Load more messages (pagination)
    async loadMoreMessages(conversationId) {
      const currentMessages = this.messages[conversationId] || []
      const currentPage = Math.ceil(currentMessages.length / 50) + 1
      return await this.fetchMessages(conversationId, currentPage)
    },

    // Send a message
    async sendMessage(conversationId, messageData) {
      this.loading.sending = true
      
      // Optimistic update
      const optimisticMessage = {
        id: `temp-${Date.now()}`,
        conversation_id: conversationId,
        sender_id: this.currentUser.id,
        content: messageData.content,
        type: messageData.type || 'text',
        created_at: new Date().toISOString(),
        sender: this.currentUser,
        attachments: messageData.attachments || [],
        sending: true
      }

      // Add to local state immediately
      if (!this.messages[conversationId]) {
        this.messages[conversationId] = []
      }
      this.messages[conversationId].push(optimisticMessage)

      try {
        const response = await axios.post(`/api/conversations/${conversationId}/messages`, {
          content: messageData.content,
          type: messageData.type || 'text',
          attachments: messageData.attachments || [],
          reply_to_id: messageData.reply_to_id
        })

        // Replace optimistic message with server response
        const messageIndex = this.messages[conversationId].findIndex(
          m => m.id === optimisticMessage.id
        )
        if (messageIndex !== -1) {
          this.messages[conversationId][messageIndex] = response.data.message
        }

        // Update conversation's last message
        const conversation = this.conversations.find(c => c.id === conversationId)
        if (conversation) {
          conversation.last_message = response.data.message
          conversation.updated_at = response.data.message.created_at
        }

        return response.data.message
      } catch (error) {
        // Remove optimistic message on error
        this.messages[conversationId] = this.messages[conversationId].filter(
          m => m.id !== optimisticMessage.id
        )
        console.error('Failed to send message:', error)
        this.errors.sending = error.message
        throw error
      } finally {
        this.loading.sending = false
      }
    },

    // Create a new conversation
    async createConversation(conversationData) {
      try {
        const response = await axios.post('/api/conversations', {
          participant_ids: conversationData.participantIds,
          group_name: conversationData.groupName,
          initial_message: conversationData.initialMessage
        })

        const newConversation = response.data.conversation
        this.conversations.unshift(newConversation)
        
        return newConversation
      } catch (error) {
        console.error('Failed to create conversation:', error)
        throw error
      }
    },

    // Mark conversation as read
    async markAsRead(conversationId) {
      try {
        await axios.post(`/api/conversations/${conversationId}/read`)
        
        // Update local state
        const conversation = this.conversations.find(c => c.id === conversationId)
        if (conversation) {
          conversation.unread_count = 0
        }
      } catch (error) {
        console.error('Failed to mark as read:', error)
      }
    },

    // Delete a message
    async deleteMessage(messageId) {
      try {
        await axios.delete(`/api/messages/${messageId}`)
        
        // Remove from local state
        Object.keys(this.messages).forEach(conversationId => {
          this.messages[conversationId] = this.messages[conversationId].filter(
            m => m.id !== messageId
          )
        })
      } catch (error) {
        console.error('Failed to delete message:', error)
        throw error
      }
    },

    // Add reaction to message
    async addReaction(messageId, emoji) {
      try {
        const response = await axios.post(`/api/messages/${messageId}/reactions`, {
          emoji
        })

        // Update local message
        Object.keys(this.messages).forEach(conversationId => {
          const messageIndex = this.messages[conversationId].findIndex(m => m.id === messageId)
          if (messageIndex !== -1) {
            this.messages[conversationId][messageIndex].reactions = response.data.reactions
          }
        })
      } catch (error) {
        console.error('Failed to add reaction:', error)
        throw error
      }
    },

    // Fetch contacts
    async fetchContacts() {
      try {
        const response = await axios.get('/api/contacts')
        this.contacts = response.data.contacts || []
        return this.contacts
      } catch (error) {
        console.error('Failed to fetch contacts:', error)
        this.errors.contacts = error.message
        throw error
      }
    },

    // Fetch folders
    async fetchFolders() {
      try {
        const response = await axios.get('/api/message-folders')
        this.folders = response.data.folders || []
        return this.folders
      } catch (error) {
        console.error('Failed to fetch folders:', error)
        this.errors.folders = error.message
        throw error
      }
    },

    // Real-time updates
    addMessage(message) {
      const conversationId = message.conversation_id
      if (!this.messages[conversationId]) {
        this.messages[conversationId] = []
      }
      
      // Check if message already exists (avoid duplicates)
      const existingMessage = this.messages[conversationId].find(m => m.id === message.id)
      if (!existingMessage) {
        this.messages[conversationId].push(message)
        
        // Update conversation's last message
        const conversation = this.conversations.find(c => c.id === conversationId)
        if (conversation) {
          conversation.last_message = message
          conversation.updated_at = message.created_at
          
          // Increment unread count if not sender
          if (message.sender_id !== this.currentUser.id) {
            conversation.unread_count = (conversation.unread_count || 0) + 1
          }
        }
      }
    },

    updateMessage(message) {
      const conversationId = message.conversation_id
      if (this.messages[conversationId]) {
        const messageIndex = this.messages[conversationId].findIndex(m => m.id === message.id)
        if (messageIndex !== -1) {
          this.messages[conversationId][messageIndex] = message
        }
      }
    },

    removeMessage(messageId) {
      Object.keys(this.messages).forEach(conversationId => {
        this.messages[conversationId] = this.messages[conversationId].filter(
          m => m.id !== messageId
        )
      })
    },

    updateOnlineUsers(users) {
      this.onlineUsers = users
    },

    addOnlineUser(user) {
      if (!this.onlineUsers.find(u => u.id === user.id)) {
        this.onlineUsers.push(user)
      }
    },

    removeOnlineUser(userId) {
      this.onlineUsers = this.onlineUsers.filter(u => u.id !== userId)
    },

    updateTypingUsers(conversationId, users) {
      this.typingUsers = users.filter(u => u.id !== this.currentUser.id)
    },

    addTypingUser(conversationId, user) {
      if (user.id !== this.currentUser.id && !this.typingUsers.find(u => u.id === user.id)) {
        this.typingUsers.push(user)
      }
    },

    removeTypingUser(conversationId, userId) {
      this.typingUsers = this.typingUsers.filter(u => u.id !== userId)
    },

    sendTypingIndicator(conversationId, userId) {
      // This would be handled by the real-time composable
      // Just keeping the method for consistency
    },

    // Clear all data (for logout)
    clearAll() {
      this.conversations = []
      this.messages = {}
      this.folders = []
      this.contacts = []
      this.onlineUsers = []
      this.typingUsers = []
      this.currentUser = null
      this.loading = {
        conversations: false,
        messages: false,
        sending: false
      }
      this.errors = {}
    }
  }
})
