<template>
  <teleport to="body">
    <div class="fixed inset-0 z-[60] flex items-center justify-center">
      <div class="absolute inset-0 bg-black/70" @click="$emit('close')"></div>
      <img :src="src" alt="Image" class="relative max-h-[90vh] max-w-[90vw] rounded shadow-lg" />
      <button class="absolute top-6 right-6 p-2 bg-white/80 rounded-full hover:bg-white" @click="$emit('close')">
        ✕
      </button>
    </div>
  </teleport>
</template>

<script setup>
const props = defineProps({
  src: { type: String, required: true }
})
</script>

<style scoped>
</style>

