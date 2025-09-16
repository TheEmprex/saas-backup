<template>
  <div
    :id="`message-${message.id}`"
    :class="[
      'flex mb-4 message-bubble group',
      isOwnMessage ? 'justify-end' : 'justify-start',
      { 'selected': isSelected },
      { 'highlighted': highlight }
    ]"
    @click="$emit('click', message)"
    @mouseenter="showActions = true"
    @mouseleave="() => { showActions = false; showEmojiPicker = false }"
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
          <template v-if="isEditing">
            <textarea
              v-model="editText"
              class="w-full text-sm p-2 rounded border focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white text-gray-900"
              rows="3"
            ></textarea>
            <div class="mt-2 flex items-center space-x-2">
              <button @click.stop="saveEdit" class="px-2 py-1 text-xs rounded bg-blue-600 text-white hover:bg-blue-700">Save</button>
              <button @click.stop="cancelEdit" class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700 hover:bg-gray-200">Cancel</button>
            </div>
          </template>
          <template v-else>
            <p class="whitespace-pre-wrap break-words text-sm">{{ message.content }}</p>
          </template>
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
              <p class="text-sm font-medium truncate">
                <a :href="message.file_url" target="_blank" rel="noopener" class="underline decoration-white/40 hover:decoration-white">
                  {{ message.file_name }}
                </a>
              </p>
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

      <!-- Reactions row -->
      <div v-if="(message.reactions && message.reactions.length)" class="mt-1 px-1 flex flex-wrap gap-1">
        <button
          v-for="r in message.reactions"
          :key="r.emoji"
          class="text-xs px-2 py-0.5 rounded-full border bg-white/70 hover:bg-white transition"
          @click.stop="$emit('react', r.emoji)"
        >
          <span class="mr-1">{{ r.emoji }}</span>{{ r.count }}
        </button>
      </div>

      <!-- Quick actions -->
      <div
        class="mt-1 px-1 flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity"
        v-show="showActions"
      >
        <button
          @click.stop="$emit('reply')"
          class="text-gray-500 hover:text-gray-700 px-2 py-1 text-xs rounded hover:bg-gray-100"
        >
          <ArrowUturnLeftIcon class="w-4 h-4 inline-block mr-1" /> Reply
        </button>
        <div class="relative">
          <button
            @click.stop="toggleEmojiPicker"
            class="text-gray-500 hover:text-gray-700 px-2 py-1 text-xs rounded hover:bg-gray-100"
          >
            <FaceSmileIcon class="w-4 h-4 inline-block mr-1" /> React
          </button>
          <div
            v-if="showEmojiPicker"
            v-click-outside="() => showEmojiPicker = false"
            :class="[
              'absolute z-10 mt-2 p-2 rounded-lg shadow border bg-white',
              isOwnMessage ? 'right-0' : 'left-0'
            ]"
          >
            <div class="grid grid-cols-8 gap-1">
              <button
                v-for="emoji in commonEmojis"
                :key="emoji"
                class="p-1 hover:bg-gray-100 rounded"
                @click.stop="onEmojiSelect(emoji)"
              >{{ emoji }}</button>
            </div>
          </div>
        </div>
        <button
          v-if="isOwnMessage && message.type === 'text'"
          @click.stop="beginEdit"
          class="text-gray-500 hover:text-gray-800 px-2 py-1 text-xs rounded hover:bg-gray-100"
        >
          <PencilSquareIcon class="w-4 h-4 inline-block mr-1" /> Edit
        </button>
        <button
          v-if="isOwnMessage"
          @click.stop="$emit('delete')"
          class="text-gray-500 hover:text-red-600 px-2 py-1 text-xs rounded hover:bg-red-50"
        >
          <TrashIcon class="w-4 h-4 inline-block mr-1" /> Delete
        </button>
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
  ExclamationCircleIcon,
  FaceSmileIcon,
  TrashIcon,
  ArrowUturnLeftIcon,
  PencilSquareIcon
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
  },
  highlight: {
    type: Boolean,
    default: false
  }
})

// Emits
const emit = defineEmits(['click', 'reply', 'retry', 'react', 'delete', 'open-image'])

// Store
const authStore = useAuthStore()
const currentUser = computed(() => authStore.user)

// Reactive data
const isPlaying = ref(false)
const playbackProgress = ref(0)
const showActions = ref(false)
const showEmojiPicker = ref(false)

const commonEmojis = [
  '👍','❤️','😂','😮','😢','👏',
  '🔥','🎉','🙏','💯','😁','🤔',
  '🎈','✅','❌','😎','🤝','🙌'
]

// Computed properties
const isOwnMessage = computed(() => {
  return props.message.sender_id === currentUser.value?.id
})

// Inline editing
const isEditing = ref(false)
const editText = ref('')

const beginEdit = () => {
  editText.value = props.message.content || ''
  isEditing.value = true
}

const saveEdit = () => {
  if (!editText.value.trim()) {
    isEditing.value = false
    return
  }
  emit('edit', editText.value.trim())
  isEditing.value = false
}

const cancelEdit = () => {
  isEditing.value = false
}

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

const toggleEmojiPicker = () => {
  showEmojiPicker.value = !showEmojiPicker.value
}

const onEmojiSelect = (emoji) => {
  emit('react', emoji)
  showEmojiPicker.value = false
}

const openImageModal = () => {
  if (props.message.file_url) {
    emit('open-image', props.message.file_url)
  }
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

// Custom directive for click outside in <script setup>
const vClickOutside = {
  mounted(el, binding) {
    el._clickOutsideHandler = (event) => {
      if (!(el === event.target || el.contains(event.target))) {
        binding.value(event)
      }
    }
    document.addEventListener('click', el._clickOutsideHandler)
  },
  unmounted(el) {
    document.removeEventListener('click', el._clickOutsideHandler)
  }
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
.message-bubble.highlighted {
  animation: highlightFade 2.2s ease-out;
}

@keyframes highlightFade {
  0% { box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.55); }
  50% { box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.3); }
  100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
}
</style>
