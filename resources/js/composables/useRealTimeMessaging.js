import { ref, onMounted, onUnmounted } from 'vue'
import { useMessagingStore } from '@/stores/messaging'

// Use the shared Echo instance initialized in resources/js/echo.js

export function useRealTimeMessaging() {
  const messagingStore = useMessagingStore()
  
  // Reactive state
  const isConnected = ref(false)
  const connectionStatus = ref('disconnected')
  const onlineUsers = ref([])
  const typingUsers = ref([])
  const presenceChannels = ref({})

  // Private channels for real-time updates
  let userChannel = null
  let conversationChannels = {}

  // Connection management
  const connect = async () => {
    try {
      connectionStatus.value = 'connecting'
      
      // Listen to user's private channel for personal notifications
      await connectUserChannel()
      
      // Connect to presence channel for online users
      await connectPresenceChannel()
      
      // Connect to conversation channels for active conversations
      await connectConversationChannels()
      
      isConnected.value = true
      connectionStatus.value = 'connected'
      
      console.log('Real-time messaging connected')
    } catch (error) {
      console.error('Failed to connect to real-time messaging:', error)
      connectionStatus.value = 'error'
      
      // Retry connection after 5 seconds
      setTimeout(() => {
        if (!isConnected.value) {
          connect()
        }
      }, 5000)
    }
  }

  const disconnect = () => {
    // Leave all channels
    if (userChannel) {
      window.Echo.leave(userChannel.name)
      userChannel = null
    }

    Object.keys(conversationChannels).forEach(channelName => {
      window.Echo.leave(channelName)
    })
    conversationChannels = {}

    Object.keys(presenceChannels.value).forEach(channelName => {
      window.Echo.leave(channelName)
    })
    presenceChannels.value = {}

    isConnected.value = false
    connectionStatus.value = 'disconnected'
    onlineUsers.value = []
    typingUsers.value = []
    
    console.log('Real-time messaging disconnected')
  }

  // Connect to user's private channel
  const connectUserChannel = async () => {
    if (!messagingStore.currentUser?.id) return

const channelName = `user.${messagingStore.currentUser.id}`
    userChannel = window.Echo.private(channelName)

    // Listen for new messages
    userChannel.listen('.message.sent', (event) => {
      messagingStore.addMessage(event.message)
      
      // Show desktop notification if permission granted
      if (Notification.permission === 'granted') {
        const notification = new Notification('New Message', {
          body: `${event.sender?.name || 'New message'}: ${event.message?.content || ''}`,
          icon: event.sender?.avatar || '/favicon.ico',
          tag: `message-${event.message?.id}`
        })
        
        notification.onclick = () => {
          window.focus()
          notification.close()
        }
      }
    })

    // Listen for read receipts
    userChannel.listen('.message.read', (event) => {
      // Optionally handle read status globally
    })
  }

  // Connect to presence channel for online users
const connectPresenceChannel = async () => {
    // Presence channel not configured on the server; no-op for now
    return
  }

  // Connect to conversation channels
  const connectConversationChannels = async () => {
    const conversations = messagingStore.conversations
    
    conversations.forEach(conversation => {
      connectToConversation(conversation.id)
    })
  }

  // Connect to a specific conversation channel
  const connectToConversation = (conversationId) => {
    if (conversationChannels[conversationId]) return // Already connected

const channelName = `conversation.${conversationId}`
    const conversationChannel = window.Echo.private(channelName)
    conversationChannels[conversationId] = conversationChannel

    // Typing via broadcast events
    conversationChannel.listen('.user.typing', (event) => {
      if (event.user_id !== messagingStore.currentUser.id) {
        if (event.is_typing) {
          messagingStore.addTypingUser(conversationId, { id: event.user_id, name: event.user_name })
          setTimeout(() => {
            messagingStore.removeTypingUser(conversationId, event.user_id)
          }, 3000)
        } else {
          messagingStore.removeTypingUser(conversationId, event.user_id)
        }
      }
    })

    // Whisper fallback
    conversationChannel.listenForWhisper('typing', (event) => {
      if (event.user.id !== messagingStore.currentUser.id) {
        messagingStore.addTypingUser(conversationId, event.user)
        setTimeout(() => {
          messagingStore.removeTypingUser(conversationId, event.user.id)
        }, 3000)
      }
    })

    conversationChannel.listenForWhisper('stop-typing', (event) => {
      messagingStore.removeTypingUser(conversationId, event.user.id)
    })

    // Read receipts
    conversationChannel.listen('.message.read', (event) => {
      const messages = messagingStore.getMessages(conversationId)
      messages.forEach(message => {
        if (message.id === event.message_id) {
          message.is_read = true
        }
      })
    })
  }

  // Disconnect from a conversation channel
  const disconnectFromConversation = (conversationId) => {
const channelName = `conversation.${conversationId}`
    if (conversationChannels[conversationId]) {
      window.Echo.leave(channelName)
      delete conversationChannels[conversationId]
    }
  }

  // Send typing indicator
  const sendTypingIndicator = (conversationId) => {
    if (!conversationChannels[conversationId]) {
      connectToConversation(conversationId)
    }

    const channel = conversationChannels[conversationId]
    if (channel) {
      channel.whisper('typing', {
        user: messagingStore.currentUser,
        conversationId
      })
    }
  }

  // Stop typing indicator
  const stopTypingIndicator = (conversationId) => {
    const channel = conversationChannels[conversationId]
    if (channel) {
      channel.whisper('stop-typing', {
        user: messagingStore.currentUser,
        conversationId
      })
    }
  }

  // Request notification permission
  const requestNotificationPermission = async () => {
    if ('Notification' in window) {
      const permission = await Notification.requestPermission()
      return permission === 'granted'
    }
    return false
  }

  // Connection retry logic
  const retryConnection = () => {
    if (!isConnected.value && connectionStatus.value !== 'connecting') {
      connect()
    }
  }

  // Echo connection event listeners
  const setupConnectionListeners = () => {
    window.Echo.connector.pusher.connection.bind('connected', () => {
      isConnected.value = true
      connectionStatus.value = 'connected'
    })

    window.Echo.connector.pusher.connection.bind('disconnected', () => {
      isConnected.value = false
      connectionStatus.value = 'disconnected'
    })

    window.Echo.connector.pusher.connection.bind('failed', () => {
      isConnected.value = false
      connectionStatus.value = 'error'
      
      // Retry after 5 seconds
      setTimeout(retryConnection, 5000)
    })
  }

  // Lifecycle hooks
  onMounted(() => {
    setupConnectionListeners()
    requestNotificationPermission()
  })

  onUnmounted(() => {
    disconnect()
  })

  // Handle window focus/blur for better UX
  const handleVisibilityChange = () => {
    if (document.hidden) {
      // User switched away, reduce activity
    } else {
      // User is back, ensure connection is active
      if (!isConnected.value) {
        retryConnection()
      }
    }
  }

  // Setup visibility change listener
  onMounted(() => {
    document.addEventListener('visibilitychange', handleVisibilityChange)
  })

  onUnmounted(() => {
    document.removeEventListener('visibilitychange', handleVisibilityChange)
  })

  return {
    // State
    isConnected,
    connectionStatus,
    onlineUsers,
    typingUsers,

    // Methods
    connect,
    disconnect,
    connectToConversation,
    disconnectFromConversation,
    sendTypingIndicator,
    stopTypingIndicator,
    retryConnection,
    requestNotificationPermission
  }
}
