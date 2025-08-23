import { ref, onMounted, watch } from 'vue'
import { useRegisterSW } from 'virtual:pwa-register/vue'

export function usePWA() {
    // Installation PWA
    const deferredPrompt = ref(null)
    const isInstallable = ref(false)
    const isInstalled = ref(false)
    const isStandalone = ref(false)

    // Service Worker
    const {
        needRefresh,
        updateServiceWorker,
        offlineReady
    } = useRegisterSW({
        onRegistered(r) {
            console.log('SW Registered: ' + r)
        },
        onRegisterError(error) {
            console.log('SW registration error', error)
        },
    })

    // Détection du mode PWA
    const checkPWAMode = () => {
        // Check si c'est déjà installé
        isStandalone.value = window.matchMedia('(display-mode: standalone)').matches ||
                           window.navigator.standalone ||
                           document.referrer.includes('android-app://');
        
        isInstalled.value = isStandalone.value
    }

    // Gestion de l'événement d'installation
    const handleBeforeInstallPrompt = (e) => {
        // Prevent the mini-infobar from appearing on mobile
        e.preventDefault()
        // Save the event so it can be triggered later.
        deferredPrompt.value = e
        isInstallable.value = true
        
        console.log('PWA install prompt available')
    }

    // Installer la PWA
    const installPWA = async () => {
        if (!deferredPrompt.value) {
            console.log('PWA install prompt not available')
            return false
        }

        try {
            // Show the install prompt
            deferredPrompt.value.prompt()
            
            // Wait for the user to respond to the prompt
            const result = await deferredPrompt.value.userChoice
            
            if (result.outcome === 'accepted') {
                console.log('User accepted the PWA install prompt')
                isInstalled.value = true
                isInstallable.value = false
            } else {
                console.log('User dismissed the PWA install prompt')
            }
            
            // Clear the saved prompt since it can't be used again
            deferredPrompt.value = null
            
            return result.outcome === 'accepted'
        } catch (error) {
            console.error('Error during PWA installation:', error)
            return false
        }
    }

    // Mettre à jour la PWA
    const updatePWA = async () => {
        try {
            await updateServiceWorker()
            // Refresh the page after update
            window.location.reload()
        } catch (error) {
            console.error('Error updating PWA:', error)
        }
    }

    // Notifications PWA
    const requestNotificationPermission = async () => {
        if (!('Notification' in window)) {
            console.log('This browser does not support notifications')
            return false
        }

        if (Notification.permission === 'granted') {
            return true
        }

        if (Notification.permission !== 'denied') {
            const permission = await Notification.requestPermission()
            return permission === 'granted'
        }

        return false
    }

    // Show native notification
    const showNotification = (title, options = {}) => {
        if (Notification.permission !== 'granted') {
            console.log('Notifications not permitted')
            return
        }

        const notification = new Notification(title, {
            icon: '/pwa-192x192.png',
            badge: '/pwa-64x64.png',
            tag: 'onlyverified-notification',
            renotify: true,
            ...options
        })

        // Auto close after 5 seconds
        setTimeout(() => notification.close(), 5000)

        return notification
    }

    // Share API
    const canShare = ref(false)
    
    const share = async (shareData) => {
        if (!navigator.share) {
            // Fallback to clipboard
            if (navigator.clipboard && shareData.url) {
                try {
                    await navigator.clipboard.writeText(shareData.url)
                    showNotification('Link copied to clipboard!', {
                        body: 'You can now paste it anywhere',
                        icon: '/pwa-64x64.png'
                    })
                    return true
                } catch (error) {
                    console.error('Failed to copy to clipboard:', error)
                    return false
                }
            }
            return false
        }

        try {
            await navigator.share(shareData)
            return true
        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error('Error sharing:', error)
            }
            return false
        }
    }

    // Storage quota
    const storageQuota = ref({ used: 0, quota: 0, percentage: 0 })
    
    const checkStorageQuota = async () => {
        if ('storage' in navigator && 'estimate' in navigator.storage) {
            try {
                const estimate = await navigator.storage.estimate()
                storageQuota.value = {
                    used: estimate.usage || 0,
                    quota: estimate.quota || 0,
                    percentage: estimate.quota ? Math.round((estimate.usage / estimate.quota) * 100) : 0
                }
            } catch (error) {
                console.error('Error getting storage estimate:', error)
            }
        }
    }

    // Setup event listeners
    onMounted(() => {
        checkPWAMode()
        
        // Check if share is available
        canShare.value = 'share' in navigator

        // Listen for PWA install prompt
        window.addEventListener('beforeinstallprompt', handleBeforeInstallPrompt)
        
        // Listen for app installed event
        window.addEventListener('appinstalled', () => {
            console.log('PWA was installed')
            isInstalled.value = true
            isInstallable.value = false
            deferredPrompt.value = null
            
            // Show success notification
            showNotification('OnlyVerified installed!', {
                body: 'The app is now available from your home screen',
                icon: '/pwa-192x192.png'
            })
        })

        // Check storage periodically
        checkStorageQuota()
        setInterval(checkStorageQuota, 30000) // Every 30 seconds
    })

    // Cleanup
    const cleanup = () => {
        window.removeEventListener('beforeinstallprompt', handleBeforeInstallPrompt)
    }

    return {
        // Installation
        isInstallable,
        isInstalled,
        isStandalone,
        installPWA,
        
        // Updates
        needRefresh,
        offlineReady,
        updatePWA,
        
        // Notifications
        requestNotificationPermission,
        showNotification,
        
        // Sharing
        canShare,
        share,
        
        // Storage
        storageQuota,
        checkStorageQuota,
        
        // Utils
        cleanup
    }
}
