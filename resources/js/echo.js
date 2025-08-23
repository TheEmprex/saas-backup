import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

// Single, shared Echo instance using Pusher
window.Pusher = Pusher
Pusher.logToConsole = !!import.meta.env.DEV

window.Echo = new Echo({
  broadcaster: 'pusher',
  key: import.meta.env.VITE_PUSHER_APP_KEY,
  cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
  forceTLS: (import.meta.env.VITE_PUSHER_FORCE_TLS ?? 'true') === 'true',
  // Optional self-hosted compatibility (leave undefined if not used)
  wsHost: import.meta.env.VITE_PUSHER_HOST || undefined,
  wsPort: import.meta.env.VITE_PUSHER_PORT ? Number(import.meta.env.VITE_PUSHER_PORT) : undefined,
  wssPort: import.meta.env.VITE_PUSHER_PORT ? Number(import.meta.env.VITE_PUSHER_PORT) : undefined,
  enabledTransports: ['ws', 'wss'],
  auth: {
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
      'Accept': 'application/json',
    }
  },
  // Debug in development
  enabledLogging: !!import.meta.env.DEV,
  logToConsole: !!import.meta.env.DEV,
})

// Handle connection states
window.Echo.connector.pusher.connection.bind('connected', () => {
  console.log('✅ WebSocket connected successfully')
})

window.Echo.connector.pusher.connection.bind('disconnected', () => {
  console.log('❌ WebSocket disconnected')
})

window.Echo.connector.pusher.connection.bind('error', (error) => {
  console.error('🚨 WebSocket connection error:', error)
})

// Export for use in other modules
export default window.Echo
