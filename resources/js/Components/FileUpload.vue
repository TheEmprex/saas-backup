<template>
  <div class="file-upload">
    <!-- Drop Zone -->
    <div
      class="relative border-2 border-dashed rounded-lg p-6 transition-colors duration-200"
      :class="[
        isDragOver 
          ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' 
          : 'border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500',
        !canAddMore && 'opacity-50 cursor-not-allowed'
      ]"
      @dragenter="upload.handleDragEnter"
      @dragleave="upload.handleDragLeave"
      @dragover="upload.handleDragOver"
      @drop="upload.handleDrop"
    >
      <!-- Hidden file input -->
      <input
        ref="fileInput"
        type="file"
        :accept="upload.config.accept"
        :multiple="upload.config.multiple"
        @change="upload.handleFileInput"
        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
        :disabled="!canAddMore"
      />
      
      <!-- Drop zone content -->
      <div class="text-center">
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
          <svg v-if="isDragOver" class="h-8 w-8 text-indigo-500 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3-3m0 0l3 3m-3-3v12" />
          </svg>
          <svg v-else class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
          </svg>
        </div>
        
        <div class="mb-2">
          <p class="text-xl font-medium text-gray-900 dark:text-white">
            {{ isDragOver ? 'Drop files here' : 'Upload files' }}
          </p>
          <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            {{ isDragOver ? 'Release to upload' : 'Drag & drop files here, or click to browse' }}
          </p>
        </div>
        
        <!-- Upload constraints -->
        <div class="text-xs text-gray-400 dark:text-gray-500 space-y-1">
          <p v-if="upload.config.accept !== '*/*'">
            Accepted types: {{ upload.config.accept }}
          </p>
          <p>
            Max {{ upload.config.maxFiles }} files, {{ upload.formatFileSize(upload.config.maxSize) }} each
          </p>
          <p v-if="hasFiles">
            {{ files.length }}/{{ upload.config.maxFiles }} files selected ({{ totalSizeFormatted }})
          </p>
        </div>
      </div>
    </div>
    
    <!-- File List -->
    <Transition name="fade">
      <div v-if="hasFiles" class="mt-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-medium text-gray-900 dark:text-white">
            Files ({{ files.length }})
          </h3>
          <div class="flex items-center space-x-2">
            <button
              v-if="!upload.config.autoUpload && upload.getFilesByStatus('pending').length > 0"
              @click="upload.uploadFiles()"
              :disabled="isUploading"
              class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white text-sm rounded-md font-medium transition-colors"
            >
              <span v-if="isUploading" class="flex items-center">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="m15.84 12.34.13-.71c1.36-.09 2.19.49 2.19 1.55 0 1.06-.83 1.64-2.19 1.55l-.13-.71z"></path>
                </svg>
                Uploading...
              </span>
              <span v-else>Upload All</span>
            </button>
            <button
              @click="upload.clearFiles"
              class="px-3 py-2 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 text-sm rounded-md font-medium transition-colors"
            >
              Clear All
            </button>
          </div>
        </div>
        
        <!-- Files Grid -->
        <div class="grid grid-cols-1 gap-4">
          <TransitionGroup name="file-list">
            <div
              v-for="fileInfo in files"
              :key="fileInfo.id"
              class="flex items-center p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm"
            >
              <!-- File Preview/Icon -->
              <div class="flex-shrink-0 mr-4">
                <div v-if="fileInfo.preview" class="h-12 w-12 rounded-lg overflow-hidden">
                  <img :src="fileInfo.preview" :alt="fileInfo.name" class="h-full w-full object-cover">
                </div>
                <div v-else class="h-12 w-12 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-xl">
                  {{ upload.getFileIcon(fileInfo.type) }}
                </div>
              </div>
              
              <!-- File Info -->
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between">
                  <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                    {{ fileInfo.name }}
                  </p>
                  <div class="flex items-center space-x-2">
                    <!-- Status Badge -->
                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full"
                          :class="{
                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300': fileInfo.status === 'pending',
                            'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300': fileInfo.status === 'uploading',
                            'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300': fileInfo.status === 'completed',
                            'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300': fileInfo.status === 'failed' || fileInfo.status === 'invalid'
                          }">
                      {{ fileInfo.status }}
                    </span>
                    
                    <!-- Actions -->
                    <div class="flex items-center space-x-1">
                      <button
                        v-if="fileInfo.status === 'failed'"
                        @click="upload.retryUpload(fileInfo.id)"
                        class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                        title="Retry upload"
                      >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                      </button>
                      <button
                        @click="upload.removeFile(fileInfo.id)"
                        class="p-1 text-gray-400 hover:text-red-600 dark:hover:text-red-400"
                        title="Remove file"
                      >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
                
                <!-- File Details -->
                <div class="mt-1 flex items-center text-sm text-gray-500 dark:text-gray-400">
                  <span>{{ fileInfo.sizeFormatted }}</span>
                  <span class="mx-2">•</span>
                  <span>{{ fileInfo.type || 'Unknown type' }}</span>
                </div>
                
                <!-- Upload Progress -->
                <div v-if="fileInfo.status === 'uploading'" class="mt-2">
                  <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div 
                      class="bg-indigo-600 h-2 rounded-full transition-all duration-300"
                      :style="{ width: fileInfo.progress + '%' }"
                    ></div>
                  </div>
                  <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ fileInfo.progress }}% uploaded
                  </p>
                </div>
                
                <!-- Errors -->
                <div v-if="fileInfo.errors.length > 0" class="mt-2">
                  <div v-for="error in fileInfo.errors" :key="error" 
                       class="text-sm text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 rounded px-2 py-1">
                    {{ error }}
                  </div>
                </div>
              </div>
            </div>
          </TransitionGroup>
        </div>
      </div>
    </Transition>
    
    <!-- Global Upload Progress -->
    <Transition name="fade">
      <div v-if="isUploading" class="fixed bottom-4 right-4 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 p-4 max-w-sm">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <svg class="animate-spin h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="m15.84 12.34.13-.71c1.36-.09 2.19.49 2.19 1.55 0 1.06-.83 1.64-2.19 1.55l-.13-.71z"></path>
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium text-gray-900 dark:text-white">
              Uploading files...
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400">
              {{ upload.getFilesByStatus('uploading').length }} of {{ files.length }} files
            </p>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { computed, watch } from 'vue'
import { useFileUpload } from '@/composables/useFileUpload'

const props = defineProps({
  accept: {
    type: String,
    default: 'image/*,video/*,audio/*,.pdf,.doc,.docx'
  },
  multiple: {
    type: Boolean,
    default: true
  },
  maxFiles: {
    type: Number,
    default: 5
  },
  maxSize: {
    type: Number,
    default: 10 * 1024 * 1024 // 10MB
  },
  autoUpload: {
    type: Boolean,
    default: false
  },
  uploadUrl: {
    type: String,
    default: '/api/upload'
  },
  formData: {
    type: Object,
    default: () => ({})
  },
  conversationId: {
    type: [Number, String],
    default: null
  }
})

const emit = defineEmits(['files-added', 'file-uploaded', 'upload-complete', 'upload-error'])

// Setup file upload composable with conversation_id in formData
const formDataWithConversation = computed(() => ({
  ...props.formData,
  ...(props.conversationId && { conversation_id: props.conversationId })
}))

const upload = useFileUpload({
  accept: props.accept,
  multiple: props.multiple,
  maxFiles: props.maxFiles,
  maxSize: props.maxSize,
  autoUpload: props.autoUpload,
  uploadUrl: props.uploadUrl,
  formData: formDataWithConversation.value,
  onComplete: (fileInfo, result) => {
    emit('file-uploaded', { fileInfo, result })
  },
  onError: (fileInfo, error) => {
    emit('upload-error', { fileInfo, error })
  }
})

// Expose reactive properties
const {
  isDragOver,
  isUploading,
  files,
  hasFiles,
  canAddMore,
  totalSizeFormatted
} = upload

// Watch for files added
watch(() => files.value.length, (newLength, oldLength) => {
  if (newLength > oldLength) {
    emit('files-added', files.value.slice(oldLength))
  }
})

// Watch for upload completion
watch(() => upload.completedUploads.value.length, (newLength) => {
  if (newLength > 0) {
    emit('upload-complete', upload.completedUploads.value)
  }
})
</script>

<style scoped>
/* Transitions */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.file-list-enter-active, .file-list-leave-active {
  transition: all 0.3s ease;
}

.file-list-enter-from {
  opacity: 0;
  transform: translateX(-30px);
}

.file-list-leave-to {
  opacity: 0;
  transform: translateX(30px);
}

.file-list-move {
  transition: transform 0.3s ease;
}

/* Drag over animation */
@keyframes bounce {
  0%, 20%, 50%, 80%, 100% {
    transform: translateY(0);
  }
  40% {
    transform: translateY(-10px);
  }
  60% {
    transform: translateY(-5px);
  }
}

.animate-bounce {
  animation: bounce 1s infinite;
}

/* Custom scrollbar */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: transparent;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: rgba(156, 163, 175, 0.5);
  border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: rgba(156, 163, 175, 0.7);
}
</style>
