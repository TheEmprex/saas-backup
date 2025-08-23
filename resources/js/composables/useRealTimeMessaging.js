import { ref, onMounted, onUnmounted } from 'vue'
import { useMessagingStore } from '@/stores/messaging'
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

// Configure Laravel Echo
window.Pusher = Pusher
window.Echo = new Echo({
  broadcaster: 'pusher',
  key: import.meta.env.VITE_PUSHER_APP_KEY,
  cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
  forceTLS: true,
  auth: {
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
      'Authorization': `Bearer ${window.authToken}`
    }
  }
})

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

    const channelName = `private-user.${messagingStore.currentUser.id}`
    userChannel = window.Echo.private(channelName)

    // Listen for new messages
    userChannel.listen('MessageSent', (event) => {
      messagingStore.addMessage(event.message)
      
      // Show desktop notification if permission granted
      if (Notification.permission === 'granted') {
        const notification = new Notification('New Message', {
          body: `${event.message.sender.name}: ${event.message.content}`,
          icon: '/favicon.ico',
          tag: `message-${event.message.id}`
        })
        
        notification.onclick = () => {
          window.focus()
          notification.close()
        }
      }
    })

    // Listen for message updates (edited, deleted, reactions)
    userChannel.listen('MessageUpdated', (event) => {
      messagingStore.updateMessage(event.message)
    })

    userChannel.listen('MessageDeleted', (event) => {
      messagingStore.removeMessage(event.messageId)
    })

    // Listen for conversation updates
    userChannel.listen('ConversationUpdated', (event) => {
      const conversation = messagingStore.conversations.find(c => c.id === event.conversation.id)
      if (conversation) {
        Object.assign(conversation, event.conversation)
      }
    })
  }

  // Connect to presence channel for online users
  const connectPresenceChannel = async () => {
    const channelName = 'presence-messaging'
    const presenceChannel = window.Echo.join(channelName)
    presenceChannels.value[channelName] = presenceChannel

    // Track online users
    presenceChannel.here((users) => {
      onlineUsers.value = users
      messagingStore.updateOnlineUsers(users)
    })

    presenceChannel.joining((user) => {
      onlineUsers.value.push(user)
      messagingStore.addOnlineUser(user)
    })

    presenceChannel.leaving((user) => {
      onlineUsers.value = onlineUsers.value.filter(u => u.id !== user.id)
      messagingStore.removeOnlineUser(user.id)
    })
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

    const channelName = `private-conversation.${conversationId}`
    const conversationChannel = window.Echo.private(channelName)
    conversationChannels[conversationId] = conversationChannel

    // Listen for typing indicators
    conversationChannel.listenForWhisper('typing', (event) => {
      if (event.user.id !== messagingStore.currentUser.id) {
        messagingStore.addTypingUser(conversationId, event.user)
        
        // Remove typing indicator after 3 seconds
        setTimeout(() => {
          messagingStore.removeTypingUser(conversationId, event.user.id)
        }, 3000)
      }
    })

    // Listen for stop typing
    conversationChannel.listenForWhisper('stop-typing', (event) => {
      messagingStore.removeTypingUser(conversationId, event.user.id)
    })

    // Listen for read receipts
    conversationChannel.listen('MessageRead', (event) => {
      // Update message read status
      const messages = messagingStore.getMessages(conversationId)
      messages.forEach(message => {
        if (message.id <= event.lastReadMessageId) {
          message.read_by = message.read_by || []
          if (!message.read_by.find(r => r.user_id === event.user.id)) {
            message.read_by.push({
              user_id: event.user.id,
              user: event.user,
              read_at: event.readAt
            })
          }
        }
      })
    })
  }

  // Disconnect from a conversation channel
  const disconnectFromConversation = (conversationId) => {
    const channelName = `private-conversation.${conversationId}`
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
