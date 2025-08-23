<template>
  <Teleport to="body">
    <div class="fixed top-4 right-4 z-50 max-w-sm">
      <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 translate-y-2 scale-95"
        enter-to-class="opacity-100 translate-y-0 scale-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 translate-y-0 scale-100"
        leave-to-class="opacity-0 translate-y-2 scale-95"
      >
        <div
          v-if="show"
          :class="[
            'rounded-lg p-4 shadow-lg border',
            toastClasses[error?.type || 'error']
          ]"
        >
          <div class="flex items-start">
            <!-- Icon -->
            <div class="flex-shrink-0">
              <ExclamationCircleIcon
                v-if="error?.type === 'error'"
                class="w-5 h-5 text-red-500"
              />
              <ExclamationTriangleIcon
                v-else-if="error?.type === 'warning'"
                class="w-5 h-5 text-yellow-500"
              />
              <InformationCircleIcon
                v-else-if="error?.type === 'info'"
                class="w-5 h-5 text-blue-500"
              />
              <CheckCircleIcon
                v-else-if="error?.type === 'success'"
                class="w-5 h-5 text-green-500"
              />
              <ExclamationCircleIcon
                v-else
                class="w-5 h-5 text-red-500"
              />
            </div>

            <!-- Content -->
            <div class="ml-3 flex-1">
              <h3
                v-if="error?.title"
                :class="[
                  'text-sm font-medium',
                  titleClasses[error?.type || 'error']
                ]"
              >
                {{ error.title }}
              </h3>
              <p
                :class="[
                  'text-sm mt-1',
                  messageClasses[error?.type || 'error']
                ]"
              >
                {{ error?.message || 'An unexpected error occurred' }}
              </p>

              <!-- Action buttons -->
              <div v-if="error?.actions?.length > 0" class="mt-3 flex space-x-2">
                <button
                  v-for="action in error.actions"
                  :key="action.label"
                  @click="handleAction(action)"
                  :class="[
                    'text-xs font-medium rounded px-2 py-1 transition-colors',
                    action.primary
                      ? buttonClasses.primary[error?.type || 'error']
                      : buttonClasses.secondary[error?.type || 'error']
                  ]"
                >
                  {{ action.label }}
                </button>
              </div>

              <!-- Retry button for connection errors -->
              <div v-else-if="error?.type === 'connection'" class="mt-3">
                <button
                  @click="handleRetry"
                  class="text-xs font-medium text-red-700 hover:text-red-600 transition-colors"
                >
                  Retry Connection
                </button>
              </div>
            </div>

            <!-- Close button -->
            <div class="ml-4 flex-shrink-0">
              <button
                @click="close"
                :class="[
                  'rounded-md p-1 transition-colors',
                  closeButtonClasses[error?.type || 'error']
                ]"
              >
                <XMarkIcon class="w-4 h-4" />
              </button>
            </div>
          </div>

          <!-- Progress bar for auto-dismiss -->
          <div
            v-if="autoDismiss && remainingTime > 0"
            class="mt-3 h-1 bg-gray-200 rounded-full overflow-hidden"
          >
            <div
              class="h-full transition-all duration-100 ease-linear"
              :class="progressClasses[error?.type || 'error']"
              :style="{ width: `${(remainingTime / dismissTime) * 100}%` }"
            ></div>
          </div>
        </div>
      </Transition>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import {
  ExclamationCircleIcon,
  ExclamationTriangleIcon,
  InformationCircleIcon,
  CheckCircleIcon,
  XMarkIcon
} from '@heroicons/vue/24/outline'

// Props
const props = defineProps({
  error: {
    type: Object,
    default: null
  },
  autoDismiss: {
    type: Boolean,
    default: true
  },
  dismissTime: {
    type: Number,
    default: 5000 // 5 seconds
  }
})

// Emits
const emit = defineEmits(['close', 'action', 'retry'])

// Reactive data
const show = ref(false)
const remainingTime = ref(props.dismissTime)
const dismissTimer = ref(null)
const progressTimer = ref(null)

// Style classes
const toastClasses = {
  error: 'bg-red-50 border-red-200',
  warning: 'bg-yellow-50 border-yellow-200',
  info: 'bg-blue-50 border-blue-200',
  success: 'bg-green-50 border-green-200',
  connection: 'bg-red-50 border-red-200'
}

const titleClasses = {
  error: 'text-red-800',
  warning: 'text-yellow-800',
  info: 'text-blue-800',
  success: 'text-green-800',
  connection: 'text-red-800'
}

const messageClasses = {
  error: 'text-red-700',
  warning: 'text-yellow-700',
  info: 'text-blue-700',
  success: 'text-green-700',
  connection: 'text-red-700'
}

const closeButtonClasses = {
  error: 'text-red-400 hover:text-red-500 hover:bg-red-100',
  warning: 'text-yellow-400 hover:text-yellow-500 hover:bg-yellow-100',
  info: 'text-blue-400 hover:text-blue-500 hover:bg-blue-100',
  success: 'text-green-400 hover:text-green-500 hover:bg-green-100',
  connection: 'text-red-400 hover:text-red-500 hover:bg-red-100'
}

const buttonClasses = {
  primary: {
    error: 'bg-red-600 text-white hover:bg-red-700',
    warning: 'bg-yellow-600 text-white hover:bg-yellow-700',
    info: 'bg-blue-600 text-white hover:bg-blue-700',
    success: 'bg-green-600 text-white hover:bg-green-700',
    connection: 'bg-red-600 text-white hover:bg-red-700'
  },
  secondary: {
    error: 'bg-red-100 text-red-700 hover:bg-red-200',
    warning: 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200',
    info: 'bg-blue-100 text-blue-700 hover:bg-blue-200',
    success: 'bg-green-100 text-green-700 hover:bg-green-200',
    connection: 'bg-red-100 text-red-700 hover:bg-red-200'
  }
}

const progressClasses = {
  error: 'bg-red-400',
  warning: 'bg-yellow-400',
  info: 'bg-blue-400',
  success: 'bg-green-400',
  connection: 'bg-red-400'
}

// Methods
const close = () => {
  show.value = false
  clearTimers()
  emit('close')
}

const handleAction = (action) => {
  if (action.handler && typeof action.handler === 'function') {
    action.handler()
  }
  
  emit('action', action)
  
  if (action.closeOnClick !== false) {
    close()
  }
}

const handleRetry = () => {
  emit('retry')
  close()
}

const clearTimers = () => {
  if (dismissTimer.value) {
    clearTimeout(dismissTimer.value)
    dismissTimer.value = null
  }
  
  if (progressTimer.value) {
    clearInterval(progressTimer.value)
    progressTimer.value = null
  }
}

const startDismissTimer = () => {
  if (!props.autoDismiss) return
  
  remainingTime.value = props.dismissTime
  
  // Update progress every 100ms
  progressTimer.value = setInterval(() => {
    remainingTime.value -= 100
    if (remainingTime.value <= 0) {
      close()
    }
  }, 100)
  
  // Auto dismiss
  dismissTimer.value = setTimeout(() => {
    close()
  }, props.dismissTime)
}

const pauseDismissTimer = () => {
  clearTimers()
}

const resumeDismissTimer = () => {
  if (!props.autoDismiss || remainingTime.value <= 0) return
  
  startDismissTimer()
}

// Watch for error prop changes
watch(() => props.error, (newError) => {
  if (newError) {
    show.value = true
    startDismissTimer()
  } else {
    show.value = false
    clearTimers()
  }
}, { immediate: true })

// Lifecycle
onMounted(() => {
  if (props.error) {
    show.value = true
    startDismissTimer()
  }
})

onUnmounted(() => {
  clearTimers()
})
</script>

<style scoped>
/* Toast animations */
.transition {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Hover to pause auto-dismiss */
.toast:hover {
  /* This would pause the timer, implemented via event handlers */
}
</style>
