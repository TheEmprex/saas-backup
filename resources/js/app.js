import axios from 'axios'
import './echo'
import apiFetch from './lib/apiFetch'
import { registerSW } from 'virtual:pwa-register'

// Configure axios
window.axios = axios
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

// Configure CSRF token
const token = document.head.querySelector('meta[name="csrf-token"]')
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content
} else {
    console.error('CSRF token not found')
}

// Expose apiFetch globally for inline scripts
window.apiFetch = apiFetch

// If an API token is present (Wave auth), attach it to axios for /api/marketplace/* calls
try {
    const API_TOKEN = localStorage.getItem('api_token')
    if (API_TOKEN) {
        window.axios.defaults.headers.common['Authorization'] = `Bearer ${API_TOKEN}`
    }
} catch (e) {
    // no-op
}

// Register Service Worker globally for PWA (auto-update)
registerSW({
    immediate: true,
    onRegistered(r) {
        console.log('SW registered', r)
    },
    onRegisterError(error) {
        console.error('SW registration error', error)
    }
})
