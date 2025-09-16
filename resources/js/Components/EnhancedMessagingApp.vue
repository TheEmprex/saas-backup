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
                    <span v-if="store.isOnline(store.activeConversation.other_user.id)">Online</span>
                    <span v-else>
                      Offline
                      <span v-if="getOtherUserLastSeen()"> • Last seen {{ formatLastSeen(getOtherUserLastSeen()) }}</span>
                    </span>
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
                <button class="p-2 rounded-lg hover:bg-gray-100 text-gray-600" @click="showSearchModal = true" title="Search messages">
                  <SearchIcon class="w-5 h-5" />
                </button>
                <button class="p-2 rounded-lg hover:bg-gray-100 text-gray-600">
                  <EllipsisVerticalIcon class="w-5 h-5" />
                </button>
              </div>
            </div>
          </header>

          <!-- Bulk selection toolbar -->
          <div v-if="selectedMessageIds.length > 0" class="bg-blue-50 border-b border-blue-200 px-4 py-2 flex items-center justify-between">
            <div class="text-sm text-blue-900">{{ selectedMessageIds.length }} selected</div>
            <div class="space-x-2">
              <button class="px-2 py-1 text-xs rounded bg-blue-600 text-white hover:bg-blue-700" @click="bulkMarkRead">Mark read</button>
              <button class="px-2 py-1 text-xs rounded bg-red-600 text-white hover:bg-red-700" @click="bulkDelete">Delete</button>
              <button class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700 hover:bg-gray-200" @click="store.clearSelectedMessages()">Clear</button>
            </div>
          </div>

          <!-- Messages Area (virtualized) -->
          <div ref="messagesContainer" class="flex-1 bg-gray-50">
            <VirtualList
              ref="virtualListRef"
              :items="store.activeMessages"
              :item-height="84"
              :overscan="8"
              key-field="id"
              class="h-full p-4"
              @reach-top="loadMoreMessages"
            >
              <template #default="{ item }">
                <div class="mb-4">
                  <MessageBubble
                    :message="item"
                    :is-selected="store.selectedMessages.has(item.id)"
                    :highlight="item.id === highlightMessageId"
                    @click="handleMessageClick(item)"
                    @reply="handleReply(item)"
                    @react="handleReact(item, $event)"
                    @delete="handleDelete(item)"
                    @edit="handleEdit(item, $event)"
                    @open-image="openLightbox"
                  />
                </div>
              </template>
            </VirtualList>
          </div>

          <!-- Message Input -->
          <footer class="bg-white border-t border-gray-200 p-4">
            <MessageInput
              :conversation-id="store.activeConversationId"
              :is-sending="store.loading.sending"
              :replying-to="replyingTo"
              @send="handleSendMessage"
              @typing="handleTyping"
              @clear-reply="replyingTo = null"
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

  <!-- Search Modal -->
  <MessageSearchModal
    v-if="showSearchModal"
    :conversation-id="store.activeConversationId"
    @close="showSearchModal = false"
    @navigate="onNavigateToResult"
  />

  <ImageLightbox v-if="lightboxUrl" :src="lightboxUrl" @close="lightboxUrl = null" />
  <NotificationToasts />
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
import MessageSearchModal from './messaging/MessageSearchModal.vue'
import NotificationToasts from './messaging/NotificationToasts.vue'
import ImageLightbox from './messaging/ImageLightbox.vue'
import VirtualList from './messaging/VirtualList.vue'
import { useToasts } from '../composables/useToasts'

// Store
const store = useEnhancedMessagingStore()

// Reactive data
const searchQuery = ref('')
const showNewConversationModal = ref(false)
const messagesContainer = ref(null)
const searchTimeout = ref(null)
const replyingTo = ref(null)
const showSearchModal = ref(false)
const highlightMessageId = ref(null)
const selectedMessageIds = computed(() => Array.from(store.selectedMessages))
const lightboxUrl = ref(null)
const virtualListRef = ref(null)

const { toasts, success: toastSuccess, error: toastError } = useToasts()

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
    // If replying, attach reply_to_id to each sent message
    const replyId = replyingTo.value?.id || null
    if (replyingTo.value) replyingTo.value = null

    const files = Array.isArray(messageData.files) ? messageData.files : []
    const text = (messageData.content || '').trim()

    // 1) If text exists, send as a standalone text message
    if (text) {
      await store.sendMessage(store.activeConversationId, {
        content: text,
        type: 'text',
        reply_to_id: replyId
      })
    }

    // 2) Send each file as its own message
    for (const f of files) {
      await store.sendMessage(store.activeConversationId, {
        content: '',
        file: f.file || f,
        // type is inferred in store from MIME
        reply_to_id: replyId
      })
    }

    await nextTick()
    scrollToBottom()
  } catch (error) {
    console.error('Failed to send message:', error)
  }
}

const handleTyping = (isTyping) => {
  if (store.activeConversationId) {
    store.sendTypingStatus(store.activeConversationId, isTyping)
  }
}

const handleMessageClick = (message) => {
  store.toggleMessageSelection(message.id)
}

const handleReply = (message) => {
  replyingTo.value = message
}

const loadMoreMessages = async () => {
  if (!store.activeConversationId || store.loading.messages) return
  
  try {
    const el = virtualListRef.value?.container
    const prevHeight = el ? el.scrollHeight : 0
    await store.fetchMessages(store.activeConversationId, getCurrentPage() + 1)
    
    // Maintain scroll position after loading older messages
    await nextTick()
    if (el) {
      const newHeight = el.scrollHeight
      el.scrollTop = newHeight - prevHeight
    }
  } catch (error) {
    console.error('Failed to load more messages:', error)
  }
}

const handleReact = async (message, emoji) => {
  try {
    await store.addReaction(message.id, emoji)
  } catch (e) {
    console.error('Failed to react:', e)
  }
}

const handleDelete = async (message) => {
  try {
    await store.deleteMessage(message.id)
  } catch (e) {
    console.error('Failed to delete message:', e)
  }
}

const handleEdit = async (message, newContent) => {
  try {
    await store.editMessage(message.id, newContent)
    toastSuccess('Message edited')
  } catch (e) {
    console.error('Failed to edit message:', e)
    toastError('Failed to edit message')
  }
}

const openLightbox = (url) => {
  lightboxUrl.value = url
}

const bulkMarkRead = async () => {
  if (!store.activeConversationId) return
  const ids = selectedMessageIds.value
  if (ids.length === 0) return
  try {
    await store.markMessagesAsRead(store.activeConversationId, ids)
    store.clearSelectedMessages()
    toastSuccess('Marked selected as read')
  } catch (e) {
    console.error('Failed to mark messages read:', e)
    toastError('Failed to mark selected as read')
  }
}

const bulkDelete = async () => {
  if (!store.activeConversationId) return
  const ids = selectedMessageIds.value
  if (ids.length === 0) return
  try {
    for (const id of ids) {
      await store.deleteMessage(id)
    }
    store.clearSelectedMessages()
    toastSuccess('Deleted selected messages')
  } catch (e) {
    console.error('Failed to delete selected:', e)
    toastError('Failed to delete selected')
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
  const el = virtualListRef.value?.container
  if (el) {
    el.scrollTop = el.scrollHeight
  }
}

const formatTypingUsers = (userIds) => {
  if (userIds.length === 0) return ''
  if (userIds.length === 1) return 'typing...'
  if (userIds.length === 2) return 'typing...'
  return 'several people are typing...'
}

const formatLastSeen = (ts) => {
  if (!ts) return 'Offline'
  const date = new Date(ts)
  const now = new Date()
  const diffMs = now - date
  const minutes = Math.floor(diffMs / 60000)
  if (minutes < 1) return 'Just now'
  if (minutes < 60) return `${minutes} min ago`
  const hours = Math.floor(minutes / 60)
  if (hours < 24) return `${hours} hr${hours>1?'s':''} ago`
  const days = Math.floor(hours / 24)
  return `${days} day${days>1?'s':''} ago`
}

const getOtherUserLastSeen = () => {
  const other = store.activeConversation?.other_user
  if (!other) return null
  const cached = store.users.get(other.id)
  return cached?.last_seen || other.last_seen || null
}

const handleCreateConversation = async (createdConversation) => {
  try {
    // Modal already created the conversation via API and emitted it here
    store.upsertConversation(createdConversation)
    showNewConversationModal.value = false
    selectConversation(createdConversation.id)
  } catch (error) {
    console.error('Failed to handle created conversation:', error)
  }
}

// Global keyboard shortcuts
const keyHandler = (e) => {
  if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
    e.preventDefault()
    showSearchModal.value = true
    return
  }
  if (e.key === 'Escape') {
    if (showSearchModal.value) {
      showSearchModal.value = false
      return
    }
    if (selectedMessageIds.value.length > 0) {
      store.clearSelectedMessages()
      return
    }
    if (replyingTo.value) {
      replyingTo.value = null
      return
    }
  }
}

// Lifecycle
onMounted(async () => {
  try {
    await store.initialize()
  } catch (error) {
    console.error('Failed to initialize messaging:', error)
  }
  window.addEventListener('keydown', keyHandler)
})

const ensureMessageLoaded = async (conversationId, messageId, maxPages = 20) => {
  let found = !!(store.getConversationMessages(conversationId) || []).find(m => m.id === messageId)
  let pagesTried = 0
  while (!found && pagesTried < maxPages) {
    const pagination = store.pagination.get(conversationId)
    const currentPage = pagination ? pagination.current_page : 1
    if (!pagination || !pagination.has_more_pages) break
    await store.fetchMessages(conversationId, currentPage + 1)
    found = !!(store.getConversationMessages(conversationId) || []).find(m => m.id === messageId)
    pagesTried++
  }
  return found
}

const scrollToMessage = async (messageId) => {
  await nextTick()
  const el = document.getElementById(`message-${messageId}`)
  if (el && messagesContainer.value) {
    el.scrollIntoView({ behavior: 'smooth', block: 'center' })
  }
}

const onNavigateToResult = async (result) => {
  try {
    const convId = result?.conversation?.id || result?.conversation_id
    const msgId = result?.id
    if (convId) {
      await selectConversation(convId)
      if (msgId) {
        const available = await ensureMessageLoaded(convId, msgId)
        if (available) {
          highlightMessageId.value = msgId
          await scrollToMessage(msgId)
          setTimeout(() => { highlightMessageId.value = null }, 2500)
        }
      }
    }
    showSearchModal.value = false
  } catch (e) {
    console.error('Failed to navigate to result:', e)
  }
})

onUnmounted(() => {
  window.removeEventListener('keydown', keyHandler)
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
