@extends('theme::layouts.app')

@section('title', 'Messages - Enhanced')

@section('content')
<div id="enhanced-messaging-app" class="h-screen">
    <enhanced-messaging-app></enhanced-messaging-app>
</div>
@endsection

@section('javascript')
<script type="module">
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import EnhancedMessagingApp from '@/Components/EnhancedMessagingApp.vue'

// Create Vue app
const app = createApp({
    components: {
        EnhancedMessagingApp
    }
})

// Add Pinia store
app.use(createPinia())

// Global error handler
app.config.errorHandler = (err, instance, info) => {
    console.error('Vue error:', err, info)
    
    // Send error to monitoring service
    if (window.monitoring) {
        window.monitoring.logError(err, {
            component: instance?.$options.name || 'Unknown',
            info: info,
            timestamp: new Date().toISOString()
        })
    }
}

// Global properties
app.config.globalProperties.$http = window.axios
app.config.globalProperties.$echo = window.Echo
app.config.globalProperties.$user = @json(auth()->user())
app.config.globalProperties.$csrf = '{{ csrf_token() }}'

// Mount app
app.mount('#enhanced-messaging-app')
</script>

<style>
/* Enhanced messaging specific styles */
#enhanced-messaging-app {
    height: 100vh;
    overflow: hidden;
    background: #f8fafc;
}

/* Ensure proper height for messaging interface */
.messaging-app {
    height: 100vh;
}

/* Custom scrollbar for webkit browsers */
.messaging-app ::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

.messaging-app ::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

.messaging-app ::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.messaging-app ::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Animation for message appearance */
@keyframes messageSlideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.message-bubble {
    animation: messageSlideIn 0.3s ease-out;
}

/* Typing indicator animation */
@keyframes typing {
    0%, 60%, 100% {
        transform: scale(1);
        opacity: 0.5;
    }
    30% {
        transform: scale(1.2);
        opacity: 1;
    }
}

.typing-indicator .dot {
    animation: typing 1.4s infinite ease-in-out;
}

.typing-indicator .dot:nth-child(1) {
    animation-delay: 0s;
}

.typing-indicator .dot:nth-child(2) {
    animation-delay: 0.2s;
}

.typing-indicator .dot:nth-child(3) {
    animation-delay: 0.4s;
}

/* Connection status indicator */
.connection-status {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1000;
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.connection-status.connected {
    background: #10b981;
    color: white;
}

.connection-status.connecting {
    background: #f59e0b;
    color: white;
}

.connection-status.disconnected {
    background: #ef4444;
    color: white;
}

/* Mobile responsive */
@media (max-width: 768px) {
    .messaging-app aside {
        width: 100% !important;
        position: absolute;
        z-index: 10;
        height: 100%;
        transform: translateX(-100%);
        transition: transform 0.3s ease;
    }
    
    .messaging-app aside.show {
        transform: translateX(0);
    }
    
    .messaging-app main {
        width: 100%;
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    #enhanced-messaging-app {
        background: #1f2937;
    }
    
    .messaging-app ::-webkit-scrollbar-track {
        background: #374151;
    }
    
    .messaging-app ::-webkit-scrollbar-thumb {
        background: #6b7280;
    }
    
    .messaging-app ::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }
}

/* Print styles */
@media print {
    #enhanced-messaging-app {
        display: none;
    }
}
</style>
@endsection

@push('head')
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="user-id" content="{{ auth()->id() }}">
<meta name="api-url" content="{{ url('/api') }}">

<!-- Prefetch DNS for faster connections -->
<link rel="dns-prefetch" href="{{ config('app.url') }}">

<!-- Preload critical fonts -->
<link rel="preload" href="/fonts/inter-var.woff2" as="font" type="font/woff2" crossorigin>

<!-- PWA manifest for enhanced mobile experience -->
<link rel="manifest" href="/messaging-manifest.json">
<meta name="theme-color" content="#3b82f6">

<!-- iOS specific meta tags -->
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="Messages">

<!-- Microsoft tiles -->
<meta name="msapplication-TileColor" content="#3b82f6">
<meta name="msapplication-config" content="/browserconfig.xml">
@endpush

@push('scripts')
<!-- Performance monitoring -->
<script>
// Performance monitoring
window.addEventListener('load', () => {
    // Measure page load performance
    const perfData = performance.getEntriesByType('navigation')[0]
    
    if (perfData && window.monitoring) {
        window.monitoring.logPerformance('page_load', {
            load_time: perfData.loadEventEnd - perfData.loadEventStart,
            dom_content_loaded: perfData.domContentLoadedEventEnd - perfData.domContentLoadedEventStart,
            first_byte: perfData.responseStart - perfData.requestStart,
            page: 'enhanced-messaging'
        })
    }
})

// Error monitoring
window.addEventListener('error', (event) => {
    if (window.monitoring) {
        window.monitoring.logError(event.error, {
            type: 'javascript_error',
            filename: event.filename,
            lineno: event.lineno,
            colno: event.colno,
            page: 'enhanced-messaging'
        })
    }
})

// Unhandled promise rejection monitoring
window.addEventListener('unhandledrejection', (event) => {
    if (window.monitoring) {
        window.monitoring.logError(event.reason, {
            type: 'unhandled_promise_rejection',
            page: 'enhanced-messaging'
        })
    }
})
</script>

<!-- Service Worker for offline capabilities -->
<script>
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then((registration) => {
                console.log('SW registered: ', registration)
            })
            .catch((registrationError) => {
                console.log('SW registration failed: ', registrationError)
            })
    })
}
</script>
@endpush
