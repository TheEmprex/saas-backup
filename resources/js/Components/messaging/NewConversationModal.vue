<template>
  <Teleport to="body">
    <div class="fixed inset-0 z-50 flex items-center justify-center">
      <!-- Backdrop -->
      <div
        class="absolute inset-0 bg-black bg-opacity-50 transition-opacity"
        @click="close"
      ></div>

      <!-- Modal -->
      <div
        class="relative bg-white rounded-lg shadow-xl max-w-md w-full mx-4 transform transition-all"
        @click.stop
      >
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">New Conversation</h2>
            <button
              @click="close"
              class="p-1 rounded-md hover:bg-gray-100 text-gray-400 hover:text-gray-500 transition-colors"
            >
              <XMarkIcon class="w-5 h-5" />
            </button>
          </div>
        </div>

        <!-- Body -->
        <div class="px-6 py-4">
          <!-- Search Users -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Search Users
            </label>
            <div class="relative">
              <MagnifyingGlassIcon class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
              <input
                v-model="searchQuery"
                @input="handleSearch"
                type="text"
                placeholder="Enter name or email..."
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                :disabled="isLoading"
              />
            </div>
          </div>

          <!-- Search Results -->
          <div v-if="searchResults.length > 0" class="mb-4">
            <div class="max-h-48 overflow-y-auto border border-gray-200 rounded-lg">
              <div
                v-for="user in searchResults"
                :key="user.id"
                @click="toggleUserSelection(user)"
                :class="[
                  'flex items-center p-3 hover:bg-gray-50 cursor-pointer transition-colors',
                  { 'bg-blue-50 border-blue-200': isUserSelected(user.id) }
                ]"
              >
                <div class="flex-shrink-0">
                  <img
                    :src="user.avatar || '/default-avatar.png'"
                    :alt="user.name"
                    class="w-10 h-10 rounded-full object-cover"
                  />
                </div>
                <div class="ml-3 flex-1 min-w-0">
                  <p class="text-sm font-medium text-gray-900 truncate">{{ user.name }}</p>
                  <p class="text-sm text-gray-500 truncate">{{ user.email }}</p>
                </div>
                <div v-if="isUserSelected(user.id)" class="flex-shrink-0">
                  <CheckIcon class="w-5 h-5 text-blue-500" />
                </div>
              </div>
            </div>
          </div>

          <!-- No Results -->
          <div v-else-if="searchQuery && !isLoading && searchResults.length === 0" class="mb-4">
            <div class="text-center py-8 text-gray-500">
              <UserIcon class="w-12 h-12 mx-auto mb-2 text-gray-300" />
              <p class="text-sm">No users found matching "{{ searchQuery }}"</p>
            </div>
          </div>

          <!-- Selected Users -->
          <div v-if="selectedUsers.length > 0" class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Selected Users ({{ selectedUsers.length }})
            </label>
            <div class="flex flex-wrap gap-2">
              <div
                v-for="user in selectedUsers"
                :key="user.id"
                class="inline-flex items-center bg-blue-100 text-blue-800 rounded-full px-3 py-1 text-sm"
              >
                <img
                  :src="user.avatar || '/default-avatar.png'"
                  :alt="user.name"
                  class="w-5 h-5 rounded-full mr-2"
                />
                {{ user.name }}
                <button
                  @click="removeUser(user)"
                  class="ml-2 p-0.5 rounded-full hover:bg-blue-200 transition-colors"
                >
                  <XMarkIcon class="w-3 h-3" />
                </button>
              </div>
            </div>
          </div>

          <!-- Conversation Title (for group chats) -->
          <div v-if="selectedUsers.length > 1" class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Group Name (optional)
            </label>
            <input
              v-model="conversationTitle"
              type="text"
              placeholder="Enter group name..."
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>

          <!-- Initial Message (optional) -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Initial Message (optional)
            </label>
            <textarea
              v-model="initialMessage"
              placeholder="Type a message to start the conversation..."
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
            ></textarea>
          </div>

          <!-- Error Message -->
          <div v-if="errorMessage" class="mb-4">
            <div class="bg-red-50 border border-red-200 rounded-lg p-3">
              <div class="flex items-center">
                <ExclamationCircleIcon class="w-5 h-5 text-red-500 mr-2 flex-shrink-0" />
                <p class="text-sm text-red-700">{{ errorMessage }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
          <button
            @click="close"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"
            :disabled="isCreating"
          >
            Cancel
          </button>
          <button
            @click="createConversation"
            :disabled="selectedUsers.length === 0 || isCreating"
            :class="[
              'px-4 py-2 text-sm font-medium rounded-lg transition-colors',
              selectedUsers.length > 0 && !isCreating
                ? 'bg-blue-600 hover:bg-blue-700 text-white'
                : 'bg-gray-300 text-gray-500 cursor-not-allowed'
            ]"
          >
            <div v-if="isCreating" class="flex items-center">
              <div class="w-4 h-4 animate-spin rounded-full border-2 border-white border-t-transparent mr-2"></div>
              Creating...
            </div>
            <span v-else>
              Create Conversation
            </span>
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import {
  XMarkIcon,
  MagnifyingGlassIcon,
  CheckIcon,
  UserIcon,
  ExclamationCircleIcon
} from '@heroicons/vue/24/outline'

// Props
const props = defineProps({
  // Could accept initial users or other configuration
})

// Emits
const emit = defineEmits(['close', 'create'])

// Reactive data
const searchQuery = ref('')
const searchResults = ref([])
const selectedUsers = ref([])
const conversationTitle = ref('')
const initialMessage = ref('')
const isLoading = ref(false)
const isCreating = ref(false)
const errorMessage = ref('')
const searchTimeout = ref(null)

// Computed properties
const isGroupConversation = computed(() => selectedUsers.value.length > 1)

// Methods
const close = () => {
  emit('close')
}

const handleSearch = () => {
  // Clear existing timeout
  if (searchTimeout.value) {
    clearTimeout(searchTimeout.value)
  }

  // Debounce search
  searchTimeout.value = setTimeout(async () => {
    if (!searchQuery.value.trim()) {
      searchResults.value = []
      return
    }

    isLoading.value = true
    errorMessage.value = ''

    try {
const token = (typeof localStorage !== 'undefined') ? localStorage.getItem('api_token') : null
      const response = await fetch(`/api/marketplace/v1/users/search?q=${encodeURIComponent(searchQuery.value)}`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
          ...(token ? { 'Authorization': `Bearer ${token}` } : {})
        },
        credentials: 'same-origin'
      })

      if (!response.ok) {
        throw new Error('Failed to search users')
      }

      const data = await response.json()
      searchResults.value = data.users || []
    } catch (error) {
      console.error('Search error:', error)
      errorMessage.value = 'Failed to search users. Please try again.'
      searchResults.value = []
    } finally {
      isLoading.value = false
    }
  }, 300)
}

const toggleUserSelection = (user) => {
  const index = selectedUsers.value.findIndex(u => u.id === user.id)
  
  if (index > -1) {
    selectedUsers.value.splice(index, 1)
  } else {
    selectedUsers.value.push(user)
  }
}

const isUserSelected = (userId) => {
  return selectedUsers.value.some(user => user.id === userId)
}

const removeUser = (userToRemove) => {
  const index = selectedUsers.value.findIndex(u => u.id === userToRemove.id)
  if (index > -1) {
    selectedUsers.value.splice(index, 1)
  }
}

const createConversation = async () => {
  if (selectedUsers.value.length === 0) return

  isCreating.value = true
  errorMessage.value = ''

  try {
    const conversationData = {
      participant_ids: selectedUsers.value.map(user => user.id),
      title: isGroupConversation.value ? conversationTitle.value.trim() : null,
      initial_message: initialMessage.value.trim() || null,
      type: isGroupConversation.value ? 'group' : 'direct'
    }

const token = (typeof localStorage !== 'undefined') ? localStorage.getItem('api_token') : null
    const response = await fetch('/api/marketplace/v1/conversations', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        ...(token ? { 'Authorization': `Bearer ${token}` } : {})
      },
      credentials: 'same-origin',
      body: JSON.stringify(conversationData)
    })

    if (!response.ok) {
      const errorData = await response.json().catch(() => ({}))
      throw new Error(errorData.message || 'Failed to create conversation')
    }

    const data = await response.json()
    
    // Emit success with the created conversation
    emit('create', data.data)
  } catch (error) {
    console.error('Create conversation error:', error)
    errorMessage.value = error.message || 'Failed to create conversation. Please try again.'
  } finally {
    isCreating.value = false
  }
}

// Lifecycle
onMounted(() => {
  // Focus the search input
  // Note: We could add a ref to the input and focus it here
})
</script>

<style scoped>
/* Modal animations */
.modal-enter-active, .modal-leave-active {
  transition: all 0.3s ease;
}

.modal-enter-from, .modal-leave-to {
  opacity: 0;
  transform: scale(0.95) translateY(-10px);
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
