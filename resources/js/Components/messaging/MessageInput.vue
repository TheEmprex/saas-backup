<template>
  <div class="message-input-container">
    <!-- Reply Preview -->
    <div v-if="replyingTo" class="reply-preview bg-gray-50 border-t border-gray-200 px-4 py-2">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-2">
          <ReplyIcon class="w-4 h-4 text-gray-500" />
          <div class="text-sm">
            <div class="font-medium text-gray-700">
              Replying to {{ replyingTo.sender?.name }}
            </div>
            <div class="text-gray-600 truncate max-w-md">
              {{ formatReplyContent(replyingTo) }}
            </div>
          </div>
        </div>
        <button
          @click="clearReply"
          class="p-1 rounded hover:bg-gray-200 text-gray-500"
        >
          <XMarkIcon class="w-4 h-4" />
        </button>
      </div>
    </div>

    <!-- Attached Files Preview -->
    <div v-if="attachedFiles.length > 0" class="attachments-preview bg-gray-50 border-t border-gray-200 p-4">
      <div class="space-y-2">
        <div
          v-for="(file, index) in attachedFiles"
          :key="file.id || index"
          class="flex items-center justify-between bg-white rounded-lg p-3 shadow-sm"
        >
          <div class="flex items-center space-x-3">
            <!-- File icon based on type -->
            <div class="flex-shrink-0">
              <img
                v-if="file.type?.startsWith('image/')"
                :src="file.preview"
                class="w-12 h-12 object-cover rounded"
                :alt="file.name"
              />
              <DocumentIcon
                v-else
                class="w-8 h-8 text-gray-500"
              />
            </div>
            <div>
              <p class="text-sm font-medium text-gray-900 truncate max-w-xs">{{ file.name }}</p>
              <p class="text-xs text-gray-500">{{ formatFileSize(file.size) }}</p>
            </div>
          </div>
          <button
            @click="removeFile(index)"
            class="p-1 rounded hover:bg-gray-100 text-gray-500"
          >
            <XMarkIcon class="w-4 h-4" />
          </button>
        </div>
      </div>
    </div>

    <!-- Main Input Area -->
    <div class="flex items-end space-x-3 p-4">
      <!-- Attachment button -->
      <div class="flex-shrink-0">
        <button
          @click="openFileDialog"
          class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 transition-colors"
          :disabled="isSending"
        >
          <PaperClipIcon class="w-5 h-5" />
        </button>
      </div>

      <!-- Message Input -->
      <div class="flex-1 relative">
        <div class="relative">
          <textarea
            ref="messageTextarea"
            v-model="messageText"
            @keydown="handleKeydown"
            @input="handleInput"
            @paste="handlePaste"
            placeholder="Type a message..."
            :disabled="isSending"
            :class="[
              'w-full resize-none border border-gray-300 rounded-2xl px-4 py-3 pr-12',
              'focus:ring-2 focus:ring-blue-500 focus:border-transparent',
              'placeholder-gray-500 text-gray-900 text-sm',
              'disabled:opacity-50 disabled:cursor-not-allowed',
              'max-h-32 min-h-[44px]'
            ]"
            :style="{ height: textareaHeight }"
          ></textarea>

          <!-- Emoji button -->
          <button
            @click="toggleEmojiPicker"
            class="absolute right-3 bottom-3 p-1 rounded hover:bg-gray-100 text-gray-500"
            :disabled="isSending"
          >
            <FaceSmileIcon class="w-5 h-5" />
          </button>
        </div>

        <!-- Emoji Picker -->
        <div
          v-if="showEmojiPicker"
          v-click-outside="closeEmojiPicker"
          class="absolute bottom-full right-0 mb-2 bg-white border border-gray-200 rounded-lg shadow-lg p-4 z-50"
        >
          <div class="grid grid-cols-8 gap-2 max-w-sm">
            <button
              v-for="emoji in commonEmojis"
              :key="emoji"
              @click="insertEmoji(emoji)"
              class="p-2 hover:bg-gray-100 rounded text-lg"
            >
              {{ emoji }}
            </button>
          </div>
        </div>
      </div>

      <!-- Voice recording button / Send button -->
      <div class="flex-shrink-0">
        <!-- Voice recording -->
        <button
          v-if="!messageText.trim() && attachedFiles.length === 0 && !replyingTo"
          @mousedown="startVoiceRecording"
          @mouseup="stopVoiceRecording"
          @mouseleave="cancelVoiceRecording"
          :class="[
            'p-3 rounded-full transition-colors',
            isRecording 
              ? 'bg-red-500 text-white animate-pulse' 
              : 'bg-blue-500 hover:bg-blue-600 text-white'
          ]"
          :disabled="isSending"
        >
          <MicrophoneIcon class="w-5 h-5" />
        </button>

        <!-- Send button -->
        <button
          v-else
          @click="sendMessage"
          :disabled="isSending || (!messageText.trim() && attachedFiles.length === 0)"
          :class="[
            'p-3 rounded-full transition-colors',
            (messageText.trim() || attachedFiles.length > 0) && !isSending
              ? 'bg-blue-500 hover:bg-blue-600 text-white'
              : 'bg-gray-300 text-gray-500 cursor-not-allowed'
          ]"
        >
          <PaperAirplaneIcon v-if="!isSending" class="w-5 h-5 transform rotate-45" />
          <div v-else class="w-5 h-5 animate-spin rounded-full border-2 border-white border-t-transparent"></div>
        </button>
      </div>
    </div>

    <!-- Voice Recording UI -->
    <div
      v-if="isRecording"
      class="recording-ui bg-red-50 border-t border-red-200 px-4 py-3"
    >
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
          <div class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
          <span class="text-red-700 font-medium">Recording... {{ recordingDuration }}s</span>
        </div>
        <div class="flex items-center space-x-2">
          <button
            @click="cancelVoiceRecording"
            class="px-3 py-1 text-sm text-red-600 hover:bg-red-100 rounded"
          >
            Cancel
          </button>
          <button
            @click="stopVoiceRecording"
            class="px-3 py-1 text-sm bg-red-500 text-white hover:bg-red-600 rounded"
          >
            Send
          </button>
        </div>
      </div>
    </div>

    <!-- Hidden file input -->
    <input
      ref="fileInput"
      type="file"
      multiple
      accept="image/*,video/*,.pdf,.doc,.docx,.txt"
      @change="handleFileSelect"
      class="hidden"
    />
  </div>
</template>

<script setup>
import { ref, computed, nextTick, onMounted, onUnmounted } from 'vue'
import {
  PaperClipIcon,
  PaperAirplaneIcon,
  MicrophoneIcon,
  FaceSmileIcon,
  DocumentIcon,
  XMarkIcon,
  ReplyIcon
} from '@heroicons/vue/24/outline'

// Props
const props = defineProps({
  conversationId: {
    type: [String, Number],
    required: true
  },
  isSending: {
    type: Boolean,
    default: false
  },
  replyingTo: {
    type: Object,
    default: null
  }
})

// Emits
const emit = defineEmits(['send', 'typing', 'clear-reply'])

// Refs
const messageTextarea = ref(null)
const fileInput = ref(null)

// Reactive data
const messageText = ref('')
const attachedFiles = ref([])
const textareaHeight = ref('44px')
const showEmojiPicker = ref(false)
const isRecording = ref(false)
const recordingDuration = ref(0)
const mediaRecorder = ref(null)
const recordingTimer = ref(null)
const typingTimer = ref(null)
const isTyping = ref(false)

// Common emojis
const commonEmojis = [
  '😀', '😃', '😄', '😁', '😆', '😅', '🤣', '😂',
  '🙂', '🙃', '😉', '😊', '😇', '🥰', '😍', '🤩',
  '😘', '😗', '☺️', '😚', '😙', '🥲', '😋', '😛',
  '😜', '🤪', '😝', '🤑', '🤗', '🤭', '🤫', '🤔'
]

// Methods
const handleKeydown = (event) => {
  // Send on Enter (but not Shift+Enter)
  if (event.key === 'Enter' && !event.shiftKey) {
    event.preventDefault()
    sendMessage()
  }
  
  // Handle other shortcuts
  if (event.key === 'Escape') {
    clearReply()
  }
}

const handleInput = () => {
  // Auto-resize textarea
  adjustTextareaHeight()
  
  // Handle typing indicator
  if (!isTyping.value) {
    isTyping.value = true
    emit('typing', true)
  }
  
  // Clear existing timer
  if (typingTimer.value) {
    clearTimeout(typingTimer.value)
  }
  
  // Set new timer to stop typing after 2 seconds
  typingTimer.value = setTimeout(() => {
    isTyping.value = false
    emit('typing', false)
  }, 2000)
}

const handlePaste = async (event) => {
  const items = event.clipboardData?.items
  if (!items) return
  
  for (let item of items) {
    if (item.type.indexOf('image') !== -1) {
      const file = item.getAsFile()
      if (file) {
        await addFileToAttachments(file)
        event.preventDefault()
      }
    }
  }
}

const adjustTextareaHeight = async () => {
  await nextTick()
  if (messageTextarea.value) {
    messageTextarea.value.style.height = '44px'
    const scrollHeight = messageTextarea.value.scrollHeight
    const maxHeight = 128 // max-h-32 = 128px
    
    textareaHeight.value = `${Math.min(scrollHeight, maxHeight)}px`
  }
}

const sendMessage = async () => {
  const text = messageText.value.trim()
  if (!text && attachedFiles.value.length === 0) return
  
  const messageData = {
    content: text,
    type: attachedFiles.value.length > 0 ? 'mixed' : 'text',
    files: attachedFiles.value,
    reply_to_id: props.replyingTo?.id || null
  }
  
  // Clear input
  messageText.value = ''
  attachedFiles.value = []
  clearReply()
  adjustTextareaHeight()
  
  // Stop typing indicator
  if (isTyping.value) {
    isTyping.value = false
    emit('typing', false)
  }
  
  // Emit send event
  emit('send', messageData)
}

const openFileDialog = () => {
  fileInput.value?.click()
}

const handleFileSelect = async (event) => {
  const files = Array.from(event.target.files || [])
  
  for (const file of files) {
    await addFileToAttachments(file)
  }
  
  // Clear the input
  event.target.value = ''
}

const addFileToAttachments = async (file) => {
  const fileData = {
    id: Date.now() + Math.random(),
    name: file.name,
    size: file.size,
    type: file.type,
    file: file
  }
  
  // Add preview for images
  if (file.type.startsWith('image/')) {
    fileData.preview = URL.createObjectURL(file)
  }
  
  attachedFiles.value.push(fileData)
}

const removeFile = (index) => {
  const file = attachedFiles.value[index]
  
  // Clean up object URL
  if (file.preview) {
    URL.revokeObjectURL(file.preview)
  }
  
  attachedFiles.value.splice(index, 1)
}

const formatFileSize = (bytes) => {
  if (!bytes) return '0 B'
  
  const sizes = ['B', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(1024))
  
  return `${(bytes / Math.pow(1024, i)).toFixed(1)} ${sizes[i]}`
}

const formatReplyContent = (message) => {
  if (message.type === 'text') {
    return message.content
  } else if (message.type === 'image') {
    return '📷 Photo'
  } else if (message.type === 'file') {
    return `📎 ${message.file_name || 'File'}`
  } else if (message.type === 'voice') {
    return '🎤 Voice message'
  }
  return 'Message'
}

const clearReply = () => {
  emit('clear-reply')
}

const toggleEmojiPicker = () => {
  showEmojiPicker.value = !showEmojiPicker.value
}

const closeEmojiPicker = () => {
  showEmojiPicker.value = false
}

const insertEmoji = (emoji) => {
  const textarea = messageTextarea.value
  const cursorPos = textarea.selectionStart
  const textBefore = messageText.value.substring(0, cursorPos)
  const textAfter = messageText.value.substring(cursorPos)
  
  messageText.value = textBefore + emoji + textAfter
  
  // Move cursor after emoji
  nextTick(() => {
    textarea.focus()
    textarea.setSelectionRange(cursorPos + emoji.length, cursorPos + emoji.length)
  })
  
  closeEmojiPicker()
}

const startVoiceRecording = async () => {
  try {
    const stream = await navigator.mediaDevices.getUserMedia({ audio: true })
    
    mediaRecorder.value = new MediaRecorder(stream)
    const audioChunks = []
    
    mediaRecorder.value.ondataavailable = (event) => {
      audioChunks.push(event.data)
    }
    
    mediaRecorder.value.onstop = () => {
      const audioBlob = new Blob(audioChunks, { type: 'audio/wav' })
      
      if (recordingDuration.value >= 1) { // Only send if recorded for at least 1 second
        const voiceMessage = {
          content: '',
          type: 'voice',
          files: [{
            id: Date.now(),
            name: `voice-${Date.now()}.wav`,
            size: audioBlob.size,
            type: 'audio/wav',
            file: audioBlob
          }]
        }
        
        emit('send', voiceMessage)
      }
      
      // Clean up
      stream.getTracks().forEach(track => track.stop())
    }
    
    // Start recording
    isRecording.value = true
    recordingDuration.value = 0
    mediaRecorder.value.start()
    
    // Start timer
    recordingTimer.value = setInterval(() => {
      recordingDuration.value++
    }, 1000)
    
  } catch (error) {
    console.error('Failed to start voice recording:', error)
  }
}

const stopVoiceRecording = () => {
  if (mediaRecorder.value && isRecording.value) {
    mediaRecorder.value.stop()
  }
  cleanupRecording()
}

const cancelVoiceRecording = () => {
  if (mediaRecorder.value && isRecording.value) {
    mediaRecorder.value.stop()
    // Don't send the recording
  }
  cleanupRecording()
}

const cleanupRecording = () => {
  isRecording.value = false
  recordingDuration.value = 0
  
  if (recordingTimer.value) {
    clearInterval(recordingTimer.value)
    recordingTimer.value = null
  }
}

// Lifecycle
onMounted(() => {
  // Focus textarea
  messageTextarea.value?.focus()
})

onUnmounted(() => {
  // Cleanup
  if (typingTimer.value) {
    clearTimeout(typingTimer.value)
  }
  
  if (recordingTimer.value) {
    clearInterval(recordingTimer.value)
  }
  
  // Clean up file previews
  attachedFiles.value.forEach(file => {
    if (file.preview) {
      URL.revokeObjectURL(file.preview)
    }
  })
})

// Custom directive for click outside
const vClickOutside = {
  mounted(el, binding) {
    el._clickOutsideHandler = (event) => {
      if (!(el === event.target || el.contains(event.target))) {
        binding.value()
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
.message-input-container {
  background: white;
  border-top: 1px solid #e5e7eb;
}

.recording-ui {
  animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Smooth transitions */
.transition-colors {
  transition-property: background-color, border-color, color;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}

/* Custom scrollbar for textarea */
textarea::-webkit-scrollbar {
  width: 6px;
}

textarea::-webkit-scrollbar-track {
  background: transparent;
}

textarea::-webkit-scrollbar-thumb {
  background: #d1d5db;
  border-radius: 3px;
}

textarea::-webkit-scrollbar-thumb:hover {
  background: #9ca3af;
}
</style>
