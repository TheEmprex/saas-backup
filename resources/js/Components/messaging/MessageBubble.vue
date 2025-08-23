<template>
  <div
    :class="[
      'flex mb-4 message-bubble',
      isOwnMessage ? 'justify-end' : 'justify-start',
      { 'selected': isSelected }
    ]"
    @click="$emit('click', message)"
  >
    <!-- Other user's avatar (left side) -->
    <div v-if="!isOwnMessage && showAvatar" class="flex-shrink-0 mr-3">
      <img
        :src="message.sender?.avatar || '/default-avatar.png'"
        :alt="message.sender?.name"
        class="w-8 h-8 rounded-full object-cover"
      />
    </div>
    <div v-else-if="!isOwnMessage" class="w-11 flex-shrink-0"></div>

    <!-- Message content -->
    <div :class="[
      'max-w-xs lg:max-w-md xl:max-w-lg flex flex-col',
      isOwnMessage ? 'items-end' : 'items-start'
    ]">
      <!-- Sender name (for group chats) -->
      <div v-if="showSenderName" class="mb-1 px-1">
        <span class="text-xs text-gray-600 font-medium">
          {{ message.sender?.name }}
        </span>
      </div>

      <!-- Message bubble -->
      <div
        :class="[
          'px-4 py-2 rounded-2xl shadow-sm cursor-pointer transition-all duration-200',
          isOwnMessage 
            ? 'bg-blue-500 text-white rounded-br-md' 
            : 'bg-white text-gray-900 border rounded-bl-md',
          { 'ring-2 ring-blue-300': isSelected },
          { 'hover:shadow-md': !isSelected }
        ]"
        @contextmenu="showContextMenu"
      >
        <!-- Reply to message (if any) -->
        <div v-if="message.reply_to" class="mb-2 pb-2 border-b border-gray-200 border-opacity-30">
          <div :class="[
            'text-xs opacity-75 p-2 rounded-lg',
            isOwnMessage ? 'bg-blue-600' : 'bg-gray-100'
          ]">
            <div class="font-medium mb-1">{{ message.reply_to.sender?.name }}</div>
            <div class="truncate">{{ formatReplyContent(message.reply_to) }}</div>
          </div>
        </div>

        <!-- Text message -->
        <div v-if="message.type === 'text'" class="message-content">
          <p class="whitespace-pre-wrap break-words text-sm">{{ message.content }}</p>
        </div>

        <!-- Image message -->
        <div v-else-if="message.type === 'image'" class="message-content">
          <div class="relative">
            <img
              :src="message.file_url"
              :alt="message.file_name"
              class="max-w-full rounded-lg cursor-pointer hover:opacity-90 transition-opacity"
              @click.stop="openImageModal"
            />
            <div v-if="message.content" class="mt-2">
              <p class="text-sm">{{ message.content }}</p>
            </div>
          </div>
        </div>

        <!-- File message -->
        <div v-else-if="message.type === 'file'" class="message-content">
          <div :class="[
            'flex items-center p-3 rounded-lg',
            isOwnMessage ? 'bg-blue-600' : 'bg-gray-50'
          ]">
            <DocumentIcon class="w-6 h-6 mr-3 flex-shrink-0" />
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium truncate">{{ message.file_name }}</p>
              <p class="text-xs opacity-75">{{ formatFileSize(message.file_size) }}</p>
            </div>
            <button
              @click.stop="downloadFile"
              class="ml-2 p-1 rounded hover:bg-gray-200 hover:bg-opacity-20"
            >
              <ArrowDownTrayIcon class="w-4 h-4" />
            </button>
          </div>
        </div>

        <!-- Voice message -->
        <div v-else-if="message.type === 'voice'" class="message-content">
          <div class="flex items-center space-x-3 py-1">
            <button
              @click.stop="togglePlayback"
              :class="[
                'p-2 rounded-full transition-colors',
                isOwnMessage ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-200 hover:bg-gray-300'
              ]"
            >
              <PlayIcon v-if="!isPlaying" class="w-4 h-4" />
              <PauseIcon v-else class="w-4 h-4" />
            </button>
            <div class="flex-1">
              <div :class="[
                'h-1 rounded-full',
                isOwnMessage ? 'bg-blue-300' : 'bg-gray-300'
              ]">
                <div
                  :class="[
                    'h-full rounded-full transition-all duration-100',
                    isOwnMessage ? 'bg-white' : 'bg-blue-500'
                  ]"
                  :style="{ width: `${playbackProgress}%` }"
                ></div>
              </div>
            </div>
            <span class="text-xs opacity-75">{{ formatDuration(message.duration) }}</span>
          </div>
        </div>
      </div>

      <!-- Message metadata -->
      <div :class="[
        'flex items-center mt-1 px-1 text-xs space-x-2',
        isOwnMessage ? 'flex-row-reverse space-x-reverse' : 'flex-row'
      ]">
        <!-- Timestamp -->
        <time class="text-gray-500">
          {{ formatTimestamp(message.created_at) }}
        </time>

        <!-- Message status (for own messages) -->
        <div v-if="isOwnMessage" class="flex items-center">
          <!-- Sending -->
          <ClockIcon
            v-if="message.status === 'sending'"
            class="w-3 h-3 text-gray-400 animate-pulse"
          />
          <!-- Delivered -->
          <CheckIcon
            v-else-if="message.status === 'delivered'"
            class="w-3 h-3 text-gray-400"
          />
          <!-- Read -->
          <div v-else-if="message.status === 'read'" class="flex">
            <CheckIcon class="w-3 h-3 text-blue-500" />
            <CheckIcon class="w-3 h-3 text-blue-500 -ml-1" />
          </div>
          <!-- Failed -->
          <ExclamationCircleIcon
            v-else-if="message.status === 'failed'"
            class="w-3 h-3 text-red-500 cursor-pointer"
            @click.stop="retryMessage"
          />
        </div>

        <!-- Edited indicator -->
        <span v-if="message.edited_at" class="text-gray-400 text-xs">
          edited
        </span>
      </div>
    </div>

    <!-- Own avatar (right side) -->
    <div v-if="isOwnMessage && showAvatar" class="flex-shrink-0 ml-3">
      <img
        :src="currentUser?.avatar || '/default-avatar.png'"
        :alt="currentUser?.name"
        class="w-8 h-8 rounded-full object-cover"
      />
    </div>
    <div v-else-if="isOwnMessage" class="w-11 flex-shrink-0"></div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import {
  DocumentIcon,
  ArrowDownTrayIcon,
  PlayIcon,
  PauseIcon,
  CheckIcon,
  ClockIcon,
  ExclamationCircleIcon
} from '@heroicons/vue/24/outline'
import { useAuthStore } from '../../stores/auth'

// Props
const props = defineProps({
  message: {
    type: Object,
    required: true
  },
  isSelected: {
    type: Boolean,
    default: false
  },
  showAvatar: {
    type: Boolean,
    default: true
  },
  showSenderName: {
    type: Boolean,
    default: false
  }
})

// Emits
defineEmits(['click', 'reply', 'retry'])

// Store
const authStore = useAuthStore()
const currentUser = computed(() => authStore.user)

// Reactive data
const isPlaying = ref(false)
const playbackProgress = ref(0)

// Computed properties
const isOwnMessage = computed(() => {
  return props.message.sender_id === currentUser.value?.id
})

// Methods
const formatTimestamp = (timestamp) => {
  if (!timestamp) return ''
  
  const date = new Date(timestamp)
  const now = new Date()
  const isToday = date.toDateString() === now.toDateString()
  
  if (isToday) {
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
  } else {
    const yesterday = new Date(now)
    yesterday.setDate(yesterday.getDate() - 1)
    
    if (date.toDateString() === yesterday.toDateString()) {
      return `Yesterday ${date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}`
    } else {
      return date.toLocaleDateString()
    }
  }
}

const formatFileSize = (bytes) => {
  if (!bytes) return '0 B'
  
  const sizes = ['B', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(1024))
  
  return `${(bytes / Math.pow(1024, i)).toFixed(1)} ${sizes[i]}`
}

const formatDuration = (seconds) => {
  if (!seconds) return '0:00'
  
  const mins = Math.floor(seconds / 60)
  const secs = seconds % 60
  
  return `${mins}:${secs.toString().padStart(2, '0')}`
}

const formatReplyContent = (replyMessage) => {
  if (replyMessage.type === 'text') {
    return replyMessage.content
  } else if (replyMessage.type === 'image') {
    return '📷 Photo'
  } else if (replyMessage.type === 'file') {
    return `📎 ${replyMessage.file_name}`
  } else if (replyMessage.type === 'voice') {
    return '🎤 Voice message'
  }
  return 'Message'
}

const showContextMenu = (event) => {
  event.preventDefault()
  // Implement context menu logic
  console.log('Show context menu for message:', props.message.id)
}

const openImageModal = () => {
  // Implement image modal logic
  console.log('Open image modal for:', props.message.file_url)
}

const downloadFile = () => {
  // Implement file download logic
  const link = document.createElement('a')
  link.href = props.message.file_url
  link.download = props.message.file_name
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

const togglePlayback = () => {
  // Implement voice playback logic
  isPlaying.value = !isPlaying.value
  
  if (isPlaying.value) {
    // Start playing audio
    // This would integrate with your audio player
    console.log('Start playing voice message:', props.message.file_url)
  } else {
    // Pause audio
    console.log('Pause voice message')
  }
}

const retryMessage = () => {
  // Emit retry event for failed messages
  $emit('retry', props.message)
}
</script>

<style scoped>
.message-bubble {
  animation: slideIn 0.3s ease-out;
}

.message-bubble.selected {
  transform: scale(1.02);
}

.message-content {
  word-wrap: break-word;
  overflow-wrap: break-word;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Custom scrollbar for long text messages */
.message-content::-webkit-scrollbar {
  width: 4px;
}

.message-content::-webkit-scrollbar-track {
  background: transparent;
}

.message-content::-webkit-scrollbar-thumb {
  background: rgba(0, 0, 0, 0.2);
  border-radius: 2px;
}
</style>
