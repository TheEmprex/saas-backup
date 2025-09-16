<template>
  <teleport to="body">
    <div class="fixed inset-0 pointer-events-none z-[70] flex flex-col items-end p-4 space-y-2">
      <div v-for="t in toasts" :key="t.id" :class="toastClass(t)" class="pointer-events-auto flex items-center px-4 py-2 rounded shadow-lg">
        <span class="mr-3 text-lg" v-if="t.type==='success'">✅</span>
        <span class="mr-3 text-lg" v-else-if="t.type==='error'">⚠️</span>
        <span class="mr-3 text-lg" v-else>🔔</span>
        <span class="text-sm">{{ t.message }}</span>
        <button class="ml-3 text-xs text-gray-600 hover:text-gray-900" @click="remove(t.id)">Close</button>
      </div>
    </div>
  </teleport>
</template>

<script setup>
import { computed } from 'vue'
import { useToasts } from '../../composables/useToasts'

const { toasts, remove } = useToasts()

const toastClass = (t) => {
  if (t.type === 'success') return 'bg-white border border-green-200 text-green-900'
  if (t.type === 'error') return 'bg-white border border-red-200 text-red-900'
  return 'bg-white border border-gray-200 text-gray-900'
}
</script>

<style scoped>
</style>

