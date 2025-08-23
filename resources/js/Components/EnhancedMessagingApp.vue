<template>
  <div class="messaging-app h-full flex flex-col bg-gray-50">
    <!-- Loading Screen -->
    <div v-if="store.loading.initializing" class="flex items-center justify-center h-full">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mb-4"></div>
        <p class="text-gray-600">Initializing messaging...</p>
      </div>
    </div>

    <!-- Main App -->
    <div v-else class="flex h-full">
      <!-- Sidebar -->
      <aside class="w-80 bg-white border-r border-gray-200 flex flex-col">
        <!-- Header -->
        <header class="p-4 border-b border-gray-200">
          <div class="flex items-center justify-between mb-4">
            <h1 class="text-xl font-semibold text-gray-900">Messages</h1>
            <button @click="showNewConversationModal = true" 
                    class="p-2 rounded-lg hover:bg-gray-100 text-gray-600">
              <PlusIcon class="w-5 h-5" />
            </button>
          </div>
          
          <!-- Search -->
          <div class="relative">
            <SearchIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
            <input
              v-model="searchQuery"
              @input="handleSearchInput"
              type="text"
              placeholder="Search conversations..."
              class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>
          
          <!-- Filters -->
          <div class="flex mt-3 space-x-2">
            <button
              @click="toggleFilter('unread')"
              :class="[
                'px-3 py-1 text-sm rounded-full transition-colors',
                store.filters.unread 
                  ? 'bg-blue-100 text-blue-800' 
                  : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
              ]"
            >
              Unread {{ store.totalUnreadCount > 0 ? `(${store.totalUnreadCount})` : '' }}
            </button>
            <button
              @click="toggleFilter('starred')"
              :class="[
                'px-3 py-1 text-sm rounded-full transition-colors',
                store.filters.starred 
                  ? 'bg-yellow-100 text-yellow-800' 
                  : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
              ]"
            >
              <StarIcon class="w-4 h-4 inline mr-1" />
              Starred
            </button>
          </div>
        </header>

        <!-- Conversations List -->
        <div class="flex-1 overflow-y-auto">
          <div v-if="store.loading.conversations" class="p-4">
            <div class="animate-pulse space-y-3">
              <div v-for="i in 5" :key="i" class="flex space-x-3">
                <div class="rounded-full bg-gray-200 h-12 w-12"></div>
                <div class="flex-1 space-y-2">
                  <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                  <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                </div>
              </div>
            </div>
          </div>

          <div v-else-if="store.filteredConversations.length === 0" class="p-4 text-center text-gray-500">
            <ChatBubbleLeftIcon class="w-12 h-12 mx-auto mb-2 text-gray-300" />
            <p>No conversations found</p>
          </div>

          <div v-else>
            <ConversationItem
              v-for="conversation in store.filteredConversations"
              :key="conversation.id"
              :conversation="conversation"
              :is-active="conversation.id === store.activeConversationId"
              :is-online="store.isOnline(conversation.other_user?.id)"
              @click="selectConversation(conversation.id)"
            />
          </div>
        </div>

        <!-- Connection Status -->
        <div v-if="!store.isConnected" class="p-3 bg-red-50 border-t border-red-200">
          <div class="flex items-center text-sm text-red-600">
            <ExclamationTriangleIcon class="w-4 h-4 mr-2" />
            Connection lost. Reconnecting...
          </div>
        </div>
      </aside>

      <!-- Main Chat Area -->
      <main class="flex-1 flex flex-col">
        <!-- No Conversation Selected -->
        <div v-if="!store.activeConversation" class="flex-1 flex items-center justify-center bg-gray-50">
          <div class="text-center text-gray-500">
            <ChatBubbleLeftIcon class="w-16 h-16 mx-auto mb-4 text-gray-300" />
            <h2 class="text-xl font-medium mb-2">Select a conversation</h2>
            <p>Choose a conversation from the sidebar to start messaging</p>
          </div>
        </div>

        <!-- Active Conversation -->
        <div v-else class="flex-1 flex flex-col">
          <!-- Chat Header -->
          <header class="bg-white border-b border-gray-200 p-4">
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <div class="relative">
                  <img
                    :src="store.activeConversation.avatar"
                    :alt="store.activeConversation.title"
                    class="w-10 h-10 rounded-full"
                  />
                  <div
                    v-if="store.isOnline(store.activeConversation.other_user?.id)"
                    class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"
                  ></div>
                </div>
                <div class="ml-3">
                  <h2 class="text-lg font-medium text-gray-900">{{ store.activeConversation.title }}</h2>
                  <p v-if="typingUsers.length > 0" class="text-sm text-blue-600">
                    {{ formatTypingUsers(typingUsers) }}
                  </p>
                  <p v-else-if="store.activeConversation.other_user" class="text-sm text-gray-500">
                    {{ store.isOnline(store.activeConversation.other_user.id) ? 'Online' : 'Offline' }}
                  </p>
                </div>
              </div>
              
              <div class="flex items-center space-x-2">
                <button class="p-2 rounded-lg hover:bg-gray-100 text-gray-600">
                  <PhoneIcon class="w-5 h-5" />
                </button>
                <button class="p-2 rounded-lg hover:bg-gray-100 text-gray-600">
                  <VideoCameraIcon class="w-5 h-5" />
                </button>
                <button class="p-2 rounded-lg hover:bg-gray-100 text-gray-600">
                  <EllipsisVerticalIcon class="w-5 h-5" />
                </button>
              </div>
            </div>
          </header>

          <!-- Messages Area -->
          <div 
            ref="messagesContainer"
            class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50"
            @scroll="handleScroll"
          >
            <!-- Load More Button -->
            <div v-if="hasMoreMessages" class="text-center">
              <button
                @click="loadMoreMessages"
                :disabled="store.loading.messages"
                class="px-4 py-2 text-sm text-blue-600 hover:bg-blue-50 rounded-lg disabled:opacity-50"
              >
                {{ store.loading.messages ? 'Loading...' : 'Load more messages' }}
              </button>
            </div>

            <!-- Messages -->
            <MessageBubble
              v-for="message in store.activeMessages"
              :key="message.id"
              :message="message"
              :is-selected="store.selectedMessages.has(message.id)"
              @click="handleMessageClick(message)"
              @reply="handleReply(message)"
            />
          </div>

          <!-- Message Input -->
          <footer class="bg-white border-t border-gray-200 p-4">
            <MessageInput
              :conversation-id="store.activeConversationId"
              :is-sending="store.loading.sending"
              @send="handleSendMessage"
              @typing="handleTyping"
            />
          </footer>
        </div>
      </main>
    </div>

    <!-- Error Toast -->
    <ErrorToast
      v-if="store.lastError"
      :error="store.lastError"
      @close="store.clearAllErrors()"
    />

    <!-- New Conversation Modal -->
    <NewConversationModal
      v-if="showNewConversationModal"
      @close="showNewConversationModal = false"
      @create="handleCreateConversation"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'
import { useEnhancedMessagingStore } from '../stores/enhanced-messaging'
import { 
  PlusIcon, 
  SearchIcon, 
  StarIcon,
  ChatBubbleLeftIcon,
  ExclamationTriangleIcon,
  PhoneIcon,
  VideoCameraIcon,
  EllipsisVerticalIcon
} from '@heroicons/vue/24/outline'

// Components
import ConversationItem from './messaging/ConversationItem.vue'
import MessageBubble from './messaging/MessageBubble.vue'
import MessageInput from './messaging/MessageInput.vue'
import ErrorToast from './messaging/ErrorToast.vue'
import NewConversationModal from './messaging/NewConversationModal.vue'

// Store
const store = useEnhancedMessagingStore()

// Reactive data
const searchQuery = ref('')
const showNewConversationModal = ref(false)
const messagesContainer = ref(null)
const searchTimeout = ref(null)

// Computed properties
const typingUsers = computed(() => {
  return store.activeConversationId 
    ? store.isTyping(store.activeConversationId)
    : []
})

const hasMoreMessages = computed(() => {
  const pagination = store.pagination.get(store.activeConversationId)
  return pagination ? pagination.has_more_pages : false
})

// Methods
const selectConversation = async (conversationId) => {
  store.setActiveConversation(conversationId)
  
  // Fetch messages if not already loaded
  if (!store.messages.has(conversationId)) {
    try {
      await store.fetchMessages(conversationId)
      await nextTick()
      scrollToBottom()
    } catch (error) {
      console.error('Failed to fetch messages:', error)
    }
  } else {
    await nextTick()
    scrollToBottom()
  }
}

const handleSearchInput = () => {
  // Debounce search
  if (searchTimeout.value) {
    clearTimeout(searchTimeout.value)
  }
  
  searchTimeout.value = setTimeout(() => {
    store.setSearchQuery(searchQuery.value)
  }, 300)
}

const toggleFilter = (filterType) => {
  const currentFilters = { ...store.filters }
  currentFilters[filterType] = !currentFilters[filterType]
  store.setFilters(currentFilters)
}

const handleSendMessage = async (messageData) => {
  if (!store.activeConversationId) return
  
  try {
    await store.sendMessage(store.activeConversationId, messageData)
    await nextTick()
    scrollToBottom()
  } catch (error) {
    console.error('Failed to send message:', error)
  }
}

const handleTyping = (isTyping) => {
  // Implement typing indicator logic
  if (store.activeConversationId) {
    // Send typing status to server
    // This would be implemented with your real-time system
  }
}

const handleMessageClick = (message) => {
  store.toggleMessageSelection(message.id)
}

const handleReply = (message) => {
  // Implement reply functionality
  console.log('Reply to message:', message)
}

const loadMoreMessages = async () => {
  if (!store.activeConversationId || store.loading.messages) return
  
  try {
    const currentScrollHeight = messagesContainer.value.scrollHeight
    await store.fetchMessages(store.activeConversationId, getCurrentPage() + 1)
    
    // Maintain scroll position after loading older messages
    await nextTick()
    const newScrollHeight = messagesContainer.value.scrollHeight
    messagesContainer.value.scrollTop = newScrollHeight - currentScrollHeight
  } catch (error) {
    console.error('Failed to load more messages:', error)
  }
}

const getCurrentPage = () => {
  const pagination = store.pagination.get(store.activeConversationId)
  return pagination ? pagination.current_page : 1
}

const handleScroll = () => {
  const container = messagesContainer.value
  if (!container) return
  
  // Load more messages when scrolled to top
  if (container.scrollTop === 0 && hasMoreMessages.value && !store.loading.messages) {
    loadMoreMessages()
  }
}

const scrollToBottom = () => {
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
  }
}

const formatTypingUsers = (userIds) => {
  if (userIds.length === 0) return ''
  if (userIds.length === 1) return 'typing...'
  if (userIds.length === 2) return 'typing...'
  return 'several people are typing...'
}

const handleCreateConversation = async (conversationData) => {
  try {
    const newConversation = await store.createConversation(conversationData)
    showNewConversationModal.value = false
    selectConversation(newConversation.id)
  } catch (error) {
    console.error('Failed to create conversation:', error)
  }
}

// Lifecycle
onMounted(async () => {
  try {
    await store.initialize()
  } catch (error) {
    console.error('Failed to initialize messaging:', error)
  }
})

onUnmounted(() => {
  store.cleanup()
  if (searchTimeout.value) {
    clearTimeout(searchTimeout.value)
  }
})

// Request notification permission
if ('Notification' in window && Notification.permission === 'default') {
  Notification.requestPermission()
}
</script>

<style scoped>
.messaging-app {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Custom scrollbar */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: #f1f1f1;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #a1a1a1;
}
</style>
