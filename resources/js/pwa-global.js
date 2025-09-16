import { createApp } from 'vue'
import PWAPrompts from './Components/PWAPrompts.vue'
import { usePWA } from '@/composables/usePWA'

// Expose PWA composable for non-module scripts (e.g., Blade partials)
try { window.__usePWA = usePWA } catch (_) {}

// Mount PWA prompts globally on every page
const mountGlobalPWA = () => {
  try {
    // Avoid duplicate mounts
    if (document.getElementById('pwa-prompts-global')) return

    const container = document.createElement('div')
    container.id = 'pwa-prompts-global'
    document.body.appendChild(container)

    const app = createApp(PWAPrompts)
    app.mount(container)
  } catch (e) {
    console.warn('Failed to mount global PWA prompts:', e)
  }
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', mountGlobalPWA)
} else {
  mountGlobalPWA()
}

