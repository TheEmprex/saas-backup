<template>
  <div ref="container" class="relative w-full h-full overflow-auto" @scroll="onScroll">
    <div :style="spacerStyle"></div>
    <div
      v-for="(item, idx) in visibleItems"
      :key="getKey(item, startIndex + idx)"
      class="absolute left-0 w-full"
      :style="getItemStyle(startIndex + idx)"
    >
      <slot :item="item" :index="startIndex + idx"></slot>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'

const props = defineProps({
  items: { type: Array, required: true },
  itemHeight: { type: Number, default: 72 }, // average row height
  overscan: { type: Number, default: 6 },
  keyField: { type: String, default: 'id' },
})

const emit = defineEmits(['reach-top', 'reach-bottom'])

const container = ref(null)
const scrollTop = ref(0)
const viewportHeight = ref(0)

const totalHeight = computed(() => props.items.length * props.itemHeight)
const startIndex = computed(() => Math.max(0, Math.floor(scrollTop.value / props.itemHeight) - props.overscan))
const endIndex = computed(() => Math.min(props.items.length, Math.ceil((scrollTop.value + viewportHeight.value) / props.itemHeight) + props.overscan))

const visibleItems = computed(() => props.items.slice(startIndex.value, endIndex.value))

const spacerStyle = computed(() => ({ height: `${totalHeight.value}px`, position: 'relative' }))

const getItemStyle = (index) => ({
  top: `${index * props.itemHeight}px`
})

const getKey = (item, idx) => item?.[props.keyField] ?? idx

const onScroll = (e) => {
  const el = container.value
  if (!el) return
  scrollTop.value = el.scrollTop
  // Top/bottom detection
  if (el.scrollTop === 0) emit('reach-top')
  if (el.scrollTop + el.clientHeight >= el.scrollHeight) emit('reach-bottom')
}

onMounted(() => {
  const el = container.value
  if (el) {
    viewportHeight.value = el.clientHeight
  }
})

watch(container, el => {
  if (el) viewportHeight.value = el.clientHeight
})

// Expose the scrollable container for parent methods
defineExpose({ container })
</script>

<style scoped>
</style>

