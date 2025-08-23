import { ref, watch, onMounted, computed } from 'vue'

export function useTheme() {
    // Theme state - 'light', 'dark', 'system'
    const theme = ref('system')
    
    // Resolved theme (actual theme being applied)
    const resolvedTheme = ref('light')
    
    // System preference
    const systemTheme = ref('light')
    
    // Check if dark mode is active
    const isDark = computed(() => resolvedTheme.value === 'dark')
    
    // Available themes
    const themes = [
        { 
            value: 'light', 
            label: 'Light',
            icon: '☀️',
            description: 'Clean and bright'
        },
        { 
            value: 'dark', 
            label: 'Dark',
            icon: '🌙',
            description: 'Easy on the eyes'
        },
        { 
            value: 'system', 
            label: 'System',
            icon: '💻',
            description: 'Match device preference'
        }
    ]
    
    // Detect system theme preference
    const detectSystemTheme = () => {
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)')
        systemTheme.value = mediaQuery.matches ? 'dark' : 'light'
        return systemTheme.value
    }
    
    // Apply theme to DOM
    const applyTheme = (themeName) => {
        const root = document.documentElement
        
        // Remove all theme classes
        root.classList.remove('light', 'dark')
        
        // Add the new theme class
        root.classList.add(themeName)
        
        // Set data attribute for CSS
        root.setAttribute('data-theme', themeName)
        
        // Update meta theme-color for mobile browsers
        updateMetaThemeColor(themeName)
        
        resolvedTheme.value = themeName
    }
    
    // Update meta theme color for mobile browsers
    const updateMetaThemeColor = (themeName) => {
        const themeColorMeta = document.querySelector('meta[name="theme-color"]')
        const colors = {
            light: '#ffffff',
            dark: '#1f2937'
        }
        
        if (themeColorMeta) {
            themeColorMeta.content = colors[themeName] || colors.light
        } else {
            // Create meta tag if it doesn't exist
            const meta = document.createElement('meta')
            meta.name = 'theme-color'
            meta.content = colors[themeName] || colors.light
            document.head.appendChild(meta)
        }
    }
    
    // Resolve theme (handle 'system' preference)
    const resolveTheme = (themeName) => {
        if (themeName === 'system') {
            return detectSystemTheme()
        }
        return themeName
    }
    
    // Set theme
    const setTheme = (themeName) => {
        if (!themes.find(t => t.value === themeName)) {
            console.warn(`Invalid theme: ${themeName}`)
            return
        }
        
        theme.value = themeName
        
        // Save to localStorage
        try {
            localStorage.setItem('onlyverified-theme', themeName)
        } catch (error) {
            console.warn('Failed to save theme preference:', error)
        }
        
        // Apply the resolved theme
        const resolved = resolveTheme(themeName)
        applyTheme(resolved)
        
        // Emit custom event for other components
        window.dispatchEvent(new CustomEvent('theme-changed', {
            detail: { theme: themeName, resolvedTheme: resolved }
        }))
    }
    
    // Toggle between light and dark (skip system)
    const toggleTheme = () => {
        const currentResolved = resolveTheme(theme.value)
        const newTheme = currentResolved === 'dark' ? 'light' : 'dark'
        setTheme(newTheme)
    }
    
    // Cycle through all themes
    const cycleTheme = () => {
        const currentIndex = themes.findIndex(t => t.value === theme.value)
        const nextIndex = (currentIndex + 1) % themes.length
        setTheme(themes[nextIndex].value)
    }
    
    // Load theme from storage
    const loadSavedTheme = () => {
        try {
            const saved = localStorage.getItem('onlyverified-theme')
            if (saved && themes.find(t => t.value === saved)) {
                return saved
            }
        } catch (error) {
            console.warn('Failed to load theme preference:', error)
        }
        return 'system' // Default to system preference
    }
    
    // Initialize theme
    const initializeTheme = () => {
        // Detect initial system preference
        detectSystemTheme()
        
        // Load saved theme or default to system
        const initialTheme = loadSavedTheme()
        theme.value = initialTheme
        
        // Apply theme immediately
        const resolved = resolveTheme(initialTheme)
        applyTheme(resolved)
    }
    
    // Get theme info
    const getThemeInfo = (themeName = theme.value) => {
        return themes.find(t => t.value === themeName) || themes[0]
    }
    
    // Check if theme is available
    const isThemeSupported = () => {
        return window.matchMedia && window.localStorage
    }
    
    // Get system theme preference changes
    const watchSystemTheme = () => {
        if (!window.matchMedia) return
        
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)')
        
        const handleChange = (e) => {
            systemTheme.value = e.matches ? 'dark' : 'light'
            
            // If current theme is 'system', update the applied theme
            if (theme.value === 'system') {
                applyTheme(systemTheme.value)
            }
        }
        
        // Modern browsers
        if (mediaQuery.addEventListener) {
            mediaQuery.addEventListener('change', handleChange)
        } else {
            // Legacy browsers
            mediaQuery.addListener(handleChange)
        }
        
        return () => {
            if (mediaQuery.removeEventListener) {
                mediaQuery.removeEventListener('change', handleChange)
            } else {
                mediaQuery.removeListener(handleChange)
            }
        }
    }
    
    // Auto theme based on time (optional feature)
    const autoThemeByTime = (enabled = false) => {
        if (!enabled) return
        
        const updateThemeByTime = () => {
            const hour = new Date().getHours()
            const shouldBeDark = hour < 7 || hour > 19 // 7 PM to 7 AM
            const newTheme = shouldBeDark ? 'dark' : 'light'
            
            if (theme.value === 'system' && resolvedTheme.value !== newTheme) {
                setTheme(newTheme)
            }
        }
        
        // Check every hour
        const interval = setInterval(updateThemeByTime, 60 * 60 * 1000)
        updateThemeByTime() // Check immediately
        
        return () => clearInterval(interval)
    }
    
    // Setup watchers and listeners
    onMounted(() => {
        initializeTheme()
        
        // Watch for system theme changes
        const cleanupSystemWatch = watchSystemTheme()
        
        // Cleanup function
        return () => {
            if (cleanupSystemWatch) cleanupSystemWatch()
        }
    })
    
    // Reactive watch for theme changes
    watch(theme, (newTheme) => {
        const resolved = resolveTheme(newTheme)
        applyTheme(resolved)
    })
    
    return {
        // State
        theme,
        resolvedTheme,
        systemTheme,
        isDark,
        
        // Methods
        setTheme,
        toggleTheme,
        cycleTheme,
        getThemeInfo,
        
        // Utils
        themes,
        isThemeSupported,
        autoThemeByTime,
        
        // Internal (for advanced use)
        applyTheme,
        detectSystemTheme
    }
}
