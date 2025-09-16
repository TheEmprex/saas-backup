<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center">
    <div class="absolute inset-0 bg-black/40" @click="$emit('close')"></div>
    <div class="relative bg-white w-full max-w-2xl rounded-xl shadow-lg overflow-hidden">
      <header class="p-4 border-b border-gray-200 flex items-center justify-between">
        <h3 class="text-lg font-semibold">Search Messages</h3>
        <button class="p-2 rounded hover:bg-gray-100" @click="$emit('close')">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
        </button>
      </header>

      <div class="p-4 space-y-4">
        <div class="flex items-center space-x-2">
          <input
            v-model="query"
            type="text"
            placeholder="Search messages..."
            class="flex-1 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            @keyup.enter="performSearch"
          />
          <select v-model="type" class="px-2 py-2 border rounded-lg">
            <option value="all">All</option>
            <option value="text">Text</option>
            <option value="image">Images</option>
            <option value="file">Files</option>
            <option value="voice">Voice</option>
          </select>
          <label class="inline-flex items-center space-x-2 text-sm text-gray-600">
            <input type="checkbox" v-model="inConversation" />
            <span>Current conversation</span>
          </label>
          <button @click="performSearch" class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Search</button>
        </div>

        <div v-if="loading" class="text-sm text-gray-500">Searching...</div>
        <div v-if="error" class="text-sm text-red-600">{{ error }}</div>

        <div v-if="results.length > 0" class="divide-y divide-gray-100 border rounded-lg">
          <div v-for="item in results" :key="item.id" class="p-3 hover:bg-gray-50 cursor-pointer" @click="navigateTo(item)">
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <div class="text-xs text-gray-500">{{ formatTimestamp(item.created_at) }} • {{ item.sender?.name || 'Unknown' }}</div>
                <div class="text-sm text-gray-900 truncate" v-if="item.type === 'text'">{{ item.content }}</div>
                <div class="text-sm text-gray-700" v-else-if="item.type === 'image'">📷 Image • {{ item.file?.name }}</div>
                <div class="text-sm text-gray-700" v-else-if="item.type === 'file'">📎 File • {{ item.file?.name }}</div>
                <div class="text-sm text-gray-700" v-else-if="item.type === 'voice'">🎤 Voice message</div>
              </div>
              <div class="text-xs text-gray-500 ml-3">Conv #{{ item.conversation?.id || item.conversation_id }}</div>
            </div>
          </div>
        </div>

        <div v-if="!loading && results.length === 0 && hasSearched" class="text-sm text-gray-500">No results found.</div>
      </div>

      <footer class="p-3 border-t border-gray-200 text-right">
        <button class="px-3 py-2 text-gray-700 rounded-lg hover:bg-gray-100" @click="$emit('close')">Close</button>
      </footer>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const props = defineProps({
  conversationId: {
    type: [Number, String],
    default: null
  }
})

const emit = defineEmits(['close', 'navigate'])

const query = ref('')
const type = ref('all')
const inConversation = ref(!!props.conversationId)
const loading = ref(false)
const error = ref('')
const results = ref([])
const hasSearched = ref(false)

const performSearch = async () => {
  error.value = ''
  results.value = []
  hasSearched.value = true
  if (!query.value.trim()) return

  loading.value = true
  try {
    const params = {
      query: query.value.trim(),
      type: type.value,
      per_page: 20
    }
    if (inConversation.value && props.conversationId) {
      params.conversation_id = props.conversationId
    }

const response = await axios.get('/api/marketplace/v1/messages/search', { params })
    const data = response.data.data || response.data
    results.value = data.data || data.messages || []
  } catch (e) {
    error.value = e.response?.data?.message || e.message
  } finally {
    loading.value = false
  }
}

const navigateTo = (item) => {
  emit('navigate', item)
}

const formatTimestamp = (ts) => {
  if (!ts) return ''
  const d = new Date(ts)
  return d.toLocaleString()
}
</script>

<style scoped>
</style>

