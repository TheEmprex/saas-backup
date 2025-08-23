import Alpine from 'alpinejs';
// Use the shared Echo instance initialized elsewhere if available

// Start Alpine.js
window.Alpine = Alpine;
Alpine.start();

window.demoButtonClickMessage = function(event){
    event.preventDefault(); new FilamentNotification().title('Modify this button in your theme folder').icon('heroicon-o-pencil-square').iconColor('info').send()
}

// Import WebRTC functionality
import './webrtc.js';

// Debug logging for Echo
if (window.Echo && window.Echo.connector?.pusher?.connection) {
    console.log('🔌 Laravel Echo available');
    window.Echo.connector.pusher.connection.bind('connected', () => {
        console.log('✅ WebSocket connected successfully');
    });
    window.Echo.connector.pusher.connection.bind('disconnected', () => {
        console.log('❌ WebSocket disconnected');
    });
    window.Echo.connector.pusher.connection.bind('error', (error) => {
        console.error('❌ WebSocket error:', error);
    });
} else {
    console.warn('⚠️ Laravel Echo not initialized on this page');
}
