<template>
  <div class="pwa-prompts">
    <!-- Install Prompt -->
    <Transition name="slide-up">
      <div 
        v-if="pwa.isInstallable.value && !showInstallPrompt" 
        class="fixed bottom-4 left-4 right-4 md:left-auto md:right-4 md:w-80 z-50"
      >
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 p-4">
          <div class="flex items-start space-x-3">
            <div class="flex-shrink-0">
              <img src="/images/onlyverified-logo.svg" alt="OnlyVerified" class="w-8 h-8">
            </div>
            <div class="flex-1 min-w-0">
              <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                Install OnlyVerified
              </h3>
              <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Get the full app experience with offline access and notifications
              </p>
            </div>
          </div>
          
          <div class="mt-4 flex space-x-2">
            <button
              @click="installApp"
              class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-3 py-2 rounded-md font-medium transition-colors"
            >
              Install
            </button>
            <button
              @click="dismissInstallPrompt"
              class="flex-1 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm px-3 py-2 rounded-md font-medium transition-colors"
            >
              Not now
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Update Available Prompt -->
    <Transition name="slide-up">
      <div 
        v-if="pwa.needRefresh.value" 
        class="fixed top-4 left-4 right-4 md:left-auto md:right-4 md:w-80 z-50"
      >
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800 p-4">
          <div class="flex items-start space-x-3">
            <div class="flex-shrink-0">
              <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                Update Available
              </h3>
              <p class="text-sm text-blue-600 dark:text-blue-300 mt-1">
                A new version of OnlyVerified is ready to install
              </p>
            </div>
          </div>
          
          <div class="mt-4 flex space-x-2">
            <button
              @click="updateApp"
              class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm px-3 py-2 rounded-md font-medium transition-colors"
            >
              Update Now
            </button>
            <button
              @click="pwa.needRefresh.value = false"
              class="flex-1 bg-blue-100 dark:bg-blue-800 hover:bg-blue-200 dark:hover:bg-blue-700 text-blue-700 dark:text-blue-200 text-sm px-3 py-2 rounded-md font-medium transition-colors"
            >
              Later
            </button>
          </div>
        </div>
      </div>
    </Transition>

    <!-- Offline Ready -->
    <Transition name="slide-up">
      <div 
        v-if="showOfflineReady" 
        class="fixed top-4 left-4 right-4 md:left-auto md:right-4 md:w-80 z-50"
      >
        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800 p-4">
          <div class="flex items-start space-x-3">
            <div class="flex-shrink-0">
              <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <h3 class="text-sm font-medium text-green-800 dark:text-green-200">
                Ready for Offline Use
              </h3>
              <p class="text-sm text-green-600 dark:text-green-300 mt-1">
                OnlyVerified is now available offline
              </p>
            </div>
          </div>
          
          <button
            @click="showOfflineReady = false"
            class="mt-3 text-sm text-green-600 dark:text-green-400 hover:text-green-500 font-medium"
          >
            Dismiss
          </button>
        </div>
      </div>
    </Transition>

    <!-- Installing indicator -->
    <Transition name="fade">
      <div 
        v-if="isInstalling" 
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
      >
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 m-4 text-center">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto mb-4"></div>
          <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
            Installing OnlyVerified
          </h3>
          <p class="text-sm text-gray-500 dark:text-gray-400">
            Please wait while the app is being installed...
          </p>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue'
import { usePWA } from '@/composables/usePWA'

const pwa = usePWA()

// State
const showInstallPrompt = ref(false)
const showOfflineReady = ref(false)
const isInstalling = ref(false)

// Install app
const installApp = async () => {
  isInstalling.value = true
  try {
    const success = await pwa.installPWA()
    if (success) {
      showInstallPrompt.value = true
      // Show success notification after a short delay
      setTimeout(() => {
        pwa.showNotification('OnlyVerified Installed!', {
          body: 'The app is now available from your home screen',
          icon: '/pwa-192x192.png'
        })
      }, 1000)
    }
  } catch (error) {
    console.error('Installation failed:', error)
  } finally {
    isInstalling.value = false
  }
}

// Update app
const updateApp = async () => {
  try {
    await pwa.updatePWA()
  } catch (error) {
    console.error('Update failed:', error)
  }
}

// Dismiss install prompt
const dismissInstallPrompt = () => {
  showInstallPrompt.value = true
  // Don't show again for 24 hours
  localStorage.setItem('pwa-install-dismissed', Date.now().toString())
}

// Check if install prompt was dismissed recently
const checkDismissedPrompt = () => {
  const dismissed = localStorage.getItem('pwa-install-dismissed')
  if (dismissed) {
    const dismissTime = parseInt(dismissed)
    const dayInMs = 24 * 60 * 60 * 1000
    if (Date.now() - dismissTime < dayInMs) {
      showInstallPrompt.value = true
    }
  }
}

// Watch offline ready
watch(() => pwa.offlineReady.value, (isReady) => {
  if (isReady) {
    showOfflineReady.value = true
    // Auto hide after 5 seconds
    setTimeout(() => {
      showOfflineReady.value = false
    }, 5000)
  }
})

// Request notification permission when PWA is standalone
watch(() => pwa.isStandalone.value, async (isStandalone) => {
  if (isStandalone) {
    // Wait a bit for the app to settle
    setTimeout(async () => {
      await pwa.requestNotificationPermission()
    }, 2000)
  }
})

onMounted(() => {
  checkDismissedPrompt()
})

onUnmounted(() => {
  pwa.cleanup()
})
</script>

<style scoped>
/* Transitions */
.slide-up-enter-active, .slide-up-leave-active {
  transition: all 0.3s ease;
}

.slide-up-enter-from {
  transform: translateY(100%);
  opacity: 0;
}

.slide-up-leave-to {
  transform: translateY(100%);
  opacity: 0;
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
  .dark\:bg-gray-800 {
    background-color: #1f2937;
  }
  
  .dark\:text-white {
    color: #ffffff;
  }
  
  .dark\:text-gray-400 {
    color: #9ca3af;
  }
  
  .dark\:border-gray-700 {
    border-color: #374151;
  }
}
</style>
