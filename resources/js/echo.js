import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

// Configure Pusher
window.Pusher = Pusher

// Create Echo instance for Reverb WebSocket server
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
    
    // Authentication headers
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            'Accept': 'application/json',
        }
    },
    
    // Cluster configuration (for compatibility)
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
    
    // Enable debugging in development
    enableLogging: import.meta.env.DEV,
    logToConsole: import.meta.env.DEV,
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
