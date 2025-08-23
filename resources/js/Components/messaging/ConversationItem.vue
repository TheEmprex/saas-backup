<template>
  <div
    @click="$emit('click')"
    :class="[
      'flex items-center p-3 hover:bg-gray-50 cursor-pointer transition-colors relative',
      { 'bg-blue-50 border-r-2 border-blue-500': isActive },
      { 'bg-white': !isActive }
    ]"
  >
    <!-- Avatar -->
    <div class="relative flex-shrink-0">
      <img
        :src="conversation.avatar || '/default-avatar.png'"
        :alt="conversation.title"
        class="w-12 h-12 rounded-full object-cover"
      />
      <!-- Online indicator -->
      <div
        v-if="isOnline"
        class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"
      ></div>
      <!-- Unread count badge -->
      <div
        v-if="conversation.unread_count > 0"
        class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center"
      >
        {{ conversation.unread_count > 99 ? '99+' : conversation.unread_count }}
      </div>
    </div>

    <!-- Content -->
    <div class="ml-3 flex-1 min-w-0">
      <div class="flex items-center justify-between">
        <h3 :class="[
          'text-sm font-medium truncate',
          conversation.unread_count > 0 ? 'text-gray-900' : 'text-gray-700'
        ]">
          {{ conversation.title }}
        </h3>
        <div class="flex items-center space-x-1 flex-shrink-0 ml-2">
          <!-- Starred indicator -->
          <StarIcon
            v-if="conversation.is_starred"
            class="w-4 h-4 text-yellow-500"
          />
          <!-- Timestamp -->
          <time :class="[
            'text-xs',
            conversation.unread_count > 0 ? 'text-blue-600 font-medium' : 'text-gray-500'
          ]">
            {{ formatTimestamp(conversation.updated_at) }}
          </time>
        </div>
      </div>
      
      <!-- Last message preview -->
      <div class="mt-1 flex items-center">
        <p :class="[
          'text-sm truncate',
          conversation.unread_count > 0 ? 'text-gray-900 font-medium' : 'text-gray-600'
        ]">
          <span v-if="conversation.last_message?.sender_id === currentUserId" class="text-gray-500">
            You: 
          </span>
          {{ formatLastMessage(conversation.last_message) }}
        </p>
        
        <!-- Message status indicators -->
        <div v-if="conversation.last_message?.sender_id === currentUserId" class="flex-shrink-0 ml-2">
          <!-- Delivered -->
          <CheckIcon
            v-if="conversation.last_message?.status === 'delivered'"
            class="w-4 h-4 text-gray-400"
          />
          <!-- Read -->
          <CheckIcon
            v-else-if="conversation.last_message?.status === 'read'"
            class="w-4 h-4 text-blue-500"
          />
          <!-- Sending -->
          <ClockIcon
            v-else-if="conversation.last_message?.status === 'sending'"
            class="w-4 h-4 text-gray-400 animate-pulse"
          />
          <!-- Failed -->
          <ExclamationCircleIcon
            v-else-if="conversation.last_message?.status === 'failed'"
            class="w-4 h-4 text-red-500"
          />
        </div>
      </div>

      <!-- Typing indicator -->
      <div v-if="isTyping" class="mt-1">
        <div class="flex items-center text-blue-600 text-sm">
          <div class="flex space-x-1">
            <div class="w-1 h-1 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
            <div class="w-1 h-1 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
            <div class="w-1 h-1 bg-blue-600 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
          </div>
          <span class="ml-2 text-xs">typing...</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { 
  StarIcon,
  CheckIcon,
  ClockIcon,
  ExclamationCircleIcon
} from '@heroicons/vue/24/outline'
import { useAuthStore } from '../../stores/auth'

// Props
const props = defineProps({
  conversation: {
    type: Object,
    required: true
  },
  isActive: {
    type: Boolean,
    default: false
  },
  isOnline: {
    type: Boolean,
    default: false
  },
  isTyping: {
    type: Boolean,
    default: false
  }
})

// Emits
defineEmits(['click'])

// Store
const authStore = useAuthStore()
const currentUserId = computed(() => authStore.user?.id)

// Methods
const formatTimestamp = (timestamp) => {
  if (!timestamp) return ''
  
  const date = new Date(timestamp)
  const now = new Date()
  const diffInMs = now - date
  const diffInMinutes = Math.floor(diffInMs / (1000 * 60))
  const diffInHours = Math.floor(diffInMs / (1000 * 60 * 60))
  const diffInDays = Math.floor(diffInMs / (1000 * 60 * 60 * 24))

  if (diffInMinutes < 1) {
    return 'now'
  } else if (diffInMinutes < 60) {
    return `${diffInMinutes}m`
  } else if (diffInHours < 24) {
    return `${diffInHours}h`
  } else if (diffInDays < 7) {
    return `${diffInDays}d`
  } else {
    return date.toLocaleDateString()
  }
}

const formatLastMessage = (message) => {
  if (!message) return 'No messages yet'
  
  if (message.type === 'text') {
    return message.content || 'Message'
  } else if (message.type === 'image') {
    return '📷 Photo'
  } else if (message.type === 'file') {
    return '📎 File'
  } else if (message.type === 'voice') {
    return '🎤 Voice message'
  } else {
    return 'Message'
  }
}
</script>

<style scoped>
/* Smooth hover transitions */
.transition-colors {
  transition-property: background-color, border-color;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}

/* Typing animation */
@keyframes bounce {
  0%, 80%, 100% {
    transform: scale(0);
  }
  40% {
    transform: scale(1);
  }
}
</style>
