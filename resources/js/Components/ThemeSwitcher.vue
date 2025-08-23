<template>
  <div class="theme-switcher">
    <!-- Quick Toggle Button (for toolbar/header) -->
    <button
      v-if="variant === 'toggle'"
      @click="theme.toggleTheme()"
      class="p-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors"
      :title="`Switch to ${theme.isDark.value ? 'light' : 'dark'} mode`"
    >
      <svg v-if="theme.isDark.value" class="w-5 h-5 text-gray-700 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20">
        <!-- Sun icon -->
        <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd" />
      </svg>
      <svg v-else class="w-5 h-5 text-gray-700 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20">
        <!-- Moon icon -->
        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
      </svg>
    </button>

    <!-- Dropdown Menu (for settings) -->
    <div v-else-if="variant === 'dropdown'" class="relative">
      <button
        @click="isOpen = !isOpen"
        class="flex items-center space-x-2 px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors"
      >
        <span class="text-xl">{{ currentTheme.icon }}</span>
        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ currentTheme.label }}</span>
        <svg class="w-4 h-4 text-gray-500" :class="{ 'rotate-180': isOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </button>

      <!-- Dropdown Content -->
      <Transition name="dropdown">
        <div 
          v-if="isOpen"
          class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-50"
        >
          <button
            v-for="themeOption in theme.themes"
            :key="themeOption.value"
            @click="selectTheme(themeOption.value)"
            class="w-full flex items-center px-4 py-2 text-left hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
            :class="{ 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400': theme.theme.value === themeOption.value }"
          >
            <span class="text-lg mr-3">{{ themeOption.icon }}</span>
            <div>
              <div class="text-sm font-medium text-gray-900 dark:text-white">{{ themeOption.label }}</div>
              <div class="text-xs text-gray-500 dark:text-gray-400">{{ themeOption.description }}</div>
            </div>
            <svg 
              v-if="theme.theme.value === themeOption.value"
              class="ml-auto w-4 h-4 text-indigo-600 dark:text-indigo-400" 
              fill="currentColor" 
              viewBox="0 0 20 20"
            >
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
          </button>
        </div>
      </Transition>
    </div>

    <!-- Radio Button Group (for settings page) -->
    <div v-else-if="variant === 'radio'" class="space-y-3">
      <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Appearance</h3>
      <div class="grid grid-cols-1 gap-2">
        <label
          v-for="themeOption in theme.themes"
          :key="themeOption.value"
          class="relative flex cursor-pointer rounded-lg border p-4 focus:outline-none"
          :class="[
            theme.theme.value === themeOption.value
              ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-900/20 dark:border-indigo-400'
              : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700'
          ]"
        >
          <input
            type="radio"
            :value="themeOption.value"
            :checked="theme.theme.value === themeOption.value"
            @change="selectTheme(themeOption.value)"
            class="sr-only"
          />
          <div class="flex items-center">
            <div class="text-2xl mr-4">{{ themeOption.icon }}</div>
            <div class="flex-1">
              <div class="flex items-center">
                <div class="text-sm">
                  <p class="font-medium text-gray-900 dark:text-white">{{ themeOption.label }}</p>
                  <div class="text-gray-500 dark:text-gray-400">
                    <p class="text-sm">{{ themeOption.description }}</p>
                  </div>
                </div>
              </div>
            </div>
            <div 
              v-if="theme.theme.value === themeOption.value"
              class="ml-4 text-indigo-600 dark:text-indigo-400"
            >
              <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
            </div>
          </div>
        </label>
      </div>
    </div>

    <!-- Inline Buttons (for compact spaces) -->
    <div v-else class="flex items-center space-x-1 bg-gray-100 dark:bg-gray-800 rounded-lg p-1">
      <button
        v-for="themeOption in theme.themes"
        :key="themeOption.value"
        @click="selectTheme(themeOption.value)"
        class="flex items-center justify-center px-3 py-2 rounded-md text-sm font-medium transition-colors"
        :class="[
          theme.theme.value === themeOption.value
            ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm'
            : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300'
        ]"
        :title="themeOption.description"
      >
        <span class="text-base mr-1">{{ themeOption.icon }}</span>
        <span class="hidden sm:inline">{{ themeOption.label }}</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useTheme } from '@/composables/useTheme'

defineProps({
  variant: {
    type: String,
    default: 'inline', // 'toggle', 'dropdown', 'radio', 'inline'
    validator: (value) => ['toggle', 'dropdown', 'radio', 'inline'].includes(value)
  }
})

const theme = useTheme()
const isOpen = ref(false)

// Current theme info
const currentTheme = computed(() => theme.getThemeInfo())

// Select a theme
const selectTheme = (themeName) => {
  theme.setTheme(themeName)
  isOpen.value = false
}

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
  if (!event.target.closest('.theme-switcher')) {
    isOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
/* Dropdown animation */
.dropdown-enter-active, .dropdown-leave-active {
  transition: all 0.2s ease;
}

.dropdown-enter-from {
  opacity: 0;
  transform: translateY(-10px) scale(0.95);
}

.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-10px) scale(0.95);
}

/* Smooth rotation for dropdown arrow */
.rotate-180 {
  transform: rotate(180deg);
}

/* Transition for theme changes */
* {
  transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
}
</style>
