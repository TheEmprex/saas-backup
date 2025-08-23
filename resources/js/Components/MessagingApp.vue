<template>
    <div class="flex w-full h-full">
        <!-- PWA Components -->
        <PWAPrompts />
        
        <!-- Main App Content -->
        <aside class="w-1/3 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-700">
            <!-- Folders and Conversations -->
            <h2 class="text-lg font-semibold p-4 text-gray-900 dark:text-white">Folders</h2>
            <ul class="space-y-1 px-2">
                <li v-for="folder in folders" :key="folder.id" @click="selectFolder(folder)" 
                    class="p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 cursor-pointer text-gray-700 dark:text-gray-300 transition-colors">
                    {{ folder.name }} ({{ folder.unread_count }})
                </li>
            </ul>
            <h2 class="text-lg font-semibold p-4 text-gray-900 dark:text-white border-t border-gray-200 dark:border-gray-700 mt-4">Conversations</h2>
            <ul class="space-y-1 px-2">
                <li v-for="conversation in conversations" :key="conversation.id" @click="selectConversation(conversation)"
                    class="p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 cursor-pointer text-gray-700 dark:text-gray-300 transition-colors">
                    {{ conversation.other_user.name }}
                </li>
            </ul>
        </aside>
        <div class="flex-1 flex flex-col">
        <header class="bg-gray-100 dark:bg-gray-800 p-4 flex items-center justify-between">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ selectedConversation?.other_user.name }}</h3>
            <div class="flex items-center space-x-2">
                <ThemeSwitcher variant="toggle" />
            </div>
        </header>
            <main class="flex-1 overflow-y-auto p-4 bg-white dark:bg-gray-900">
                <div v-for="message in messages" :key="message.id" :class="{'text-right': message.is_mine}" class="mb-4">
                    <span :class="{
                        'bg-indigo-500 dark:bg-indigo-600 text-white': message.is_mine, 
                        'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white': !message.is_mine
                    }"
                          class="inline-block rounded-lg px-4 py-2 max-w-xs break-words">
                        {{ message.content }}
                    </span>
                </div>
            </main>
            <footer class="p-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 space-y-4">
                <div class="flex items-center space-x-2">
                    <button
                        @click="showFileUpload = !showFileUpload"
                        class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                        title="Attach files"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                        </svg>
                    </button>
                    <input type="text" v-model="newMessage" @keyup.enter="sendMessage" placeholder="Type a message..."
                           class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors" />
                    <button
                        @click="sendMessage"
                        :disabled="!newMessage.trim()"
                        class="p-2 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-lg transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </button>
                </div>
                
                <!-- File Upload Section -->
                <Transition name="slide-down">
                    <div v-if="showFileUpload" class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <FileUpload
                            :accept="'image/*,video/*,audio/*,.pdf,.doc,.docx'"
                            :max-files="3"
                            :max-size="5 * 1024 * 1024"
                            :upload-url="`${apiBaseUrl}/messages/upload-files`"
                            :conversation-id="selectedConversation?.id"
                            @files-added="onFilesAdded"
                            @file-uploaded="onFileUploaded"
                            @upload-error="onUploadError"
                        />
                    </div>
                </Transition>
            </footer>
        </div>
    </div>
</template>
<script setup>
import { ref, computed, onMounted } from 'vue'
import PWAPrompts from './PWAPrompts.vue'
import ThemeSwitcher from './ThemeSwitcher.vue'
import FileUpload from './FileUpload.vue'

// Reactive data
const folders = ref([])
const conversations = ref([])
const messages = ref([])
const selectedConversation = ref(null)
const newMessage = ref('')
const showFileUpload = ref(false)

// Computed properties
const apiBaseUrl = computed(() => window.MessagingApp.apiBaseUrl)
const userId = computed(() => window.MessagingApp.user.id)

// Methods
const fetchFolders = () => {
    fetch(`${apiBaseUrl.value}/folders`)
        .then((res) => res.json())
        .then((data) => (folders.value = data.folders))
}

const fetchConversations = () => {
    fetch(`${apiBaseUrl.value}/conversations`)
        .then((res) => res.json())
        .then((data) => (conversations.value = data.conversations))
}

const selectFolder = (folder) => {
    console.log('Selected Folder:', folder)
    // Fetch conversations or messages based on folder
}

const selectConversation = (conversation) => {
    selectedConversation.value = conversation
    fetchMessages(conversation.id)
}

const fetchMessages = (conversationId) => {
    fetch(`${apiBaseUrl.value}/conversations/${conversationId}`)
        .then((res) => res.json())
        .then((data) => (messages.value = data.messages))
}

const sendMessage = () => {
    if (!newMessage.value) return

    fetch(`${apiBaseUrl.value}/conversations/${selectedConversation.value.id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.MessagingApp.csrfToken,
        },
        body: JSON.stringify({
            content: newMessage.value,
        }),
    })
        .then((res) => res.json())
        .then((data) => {
            if (data.success) {
                messages.value.push(data.message)
                newMessage.value = ''
            }
        })
}

// File Upload handlers
const onFilesAdded = (files) => {
    console.log('Files added to upload:', files)
    // Show notification or preview of added files
}

const onFileUploaded = (file, response) => {
    console.log('File uploaded successfully:', file.name, response)
    
    // If the file upload returns a message or attachment data, add it to the conversation
    if (response?.message) {
        messages.value.push(response.message)
    }
    
    // Auto-hide upload panel after successful upload
    if (response?.success) {
        showFileUpload.value = false
    }
}

const onUploadError = (file, error) => {
    console.error('File upload error:', file.name, error)
    // Could show a toast notification here
    alert(`Failed to upload ${file.name}: ${error.message}`)
}

// Lifecycle
onMounted(() => {
    fetchFolders()
    fetchConversations()
    // Connect to real-time updates
    window.connectToMessaging(userId.value)
})
</script>
<style scoped>
aside {
    overflow-y: auto;
}

/* File Upload Transition */
.slide-down-enter-active,
.slide-down-leave-active {
    transition: all 0.3s ease;
    transform-origin: top;
}

.slide-down-enter-from {
    opacity: 0;
    transform: translateY(-20px) scaleY(0.8);
}

.slide-down-leave-to {
    opacity: 0;
    transform: translateY(-10px) scaleY(0.9);
}
</style>

