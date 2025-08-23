import Alpine from 'alpinejs';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Configure Laravel Echo for real-time messaging
window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY || '31jsurrkclawcmem9bow',
    wsHost: import.meta.env.VITE_REVERB_HOST || 'localhost',
    wsPort: import.meta.env.VITE_REVERB_PORT || 8080,
    wssPort: import.meta.env.VITE_REVERB_PORT || 8080,
    forceTLS: false,
    enabledTransports: ['ws', 'wss'],
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            'Accept': 'application/json',
        },
    },
});

// Start Alpine.js
window.Alpine = Alpine;
Alpine.start();

window.demoButtonClickMessage = function(event){
    event.preventDefault(); new FilamentNotification().title('Modify this button in your theme folder').icon('heroicon-o-pencil-square').iconColor('info').send()
}

// Import WebRTC functionality
import './webrtc.js';

// Debug logging for Echo
console.log('🔌 Laravel Echo initialized with Reverb');
window.Echo.connector.pusher.connection.bind('connected', () => {
    console.log('✅ WebSocket connected successfully');
});
window.Echo.connector.pusher.connection.bind('disconnected', () => {
    console.log('❌ WebSocket disconnected');
});
window.Echo.connector.pusher.connection.bind('error', (error) => {
    console.error('❌ WebSocket error:', error);
});
