import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import react from '@vitejs/plugin-react';
import { VitePWA } from 'vite-plugin-pwa';
import { resolve } from 'path';
import fs from 'fs';

const themeFilePath = resolve(__dirname, 'theme.json');
const activeTheme = fs.existsSync(themeFilePath) ? JSON.parse(fs.readFileSync(themeFilePath, 'utf8')).name : 'anchor';
console.log(`Active theme: ${activeTheme}`);

export default defineConfig(({ command, mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    
    return {
        plugins: [
            vue({
                template: {
                    transformAssetUrls: {
                        base: null,
                        includeAbsolute: false,
                    },
                },
            }),
            react(),
            laravel({
                input: [
                    `resources/themes/${activeTheme}/assets/css/app.css`,
                    `resources/themes/${activeTheme}/assets/js/app.js`,
                    `resources/themes/${activeTheme}/assets/js/webrtc.js`,
                    'resources/css/filament/admin/theme.css',
                    'resources/js/app.js',
                    'resources/js/react-messaging-app.jsx',
                    'resources/js/messaging-app.js',
                    'resources/js/stores/enhanced-messaging.js',
                ],
                refresh: [
                    `resources/themes/${activeTheme}/**/*`,
                    'resources/views/**/*.blade.php',
                ],
            }),
            VitePWA({
                registerType: 'autoUpdate',
                workbox: {
                    globPatterns: ['**/*.{js,css,html,ico,png,svg,jpg,jpeg,gif,webp,woff,woff2}'],
                    runtimeCaching: [
                        {
                            urlPattern: /^https:\/\/api\./,
                            handler: 'NetworkFirst',
                            options: {
                                cacheName: 'api-cache',
                                networkTimeoutSeconds: 10,
                                cacheableResponse: {
                                    statuses: [0, 200]
                                }
                            }
                        },
                        {
                            urlPattern: /^https:\/\/.*\.(png|jpg|jpeg|svg|gif|webp)$/,
                            handler: 'CacheFirst',
                            options: {
                                cacheName: 'images-cache',
                                expiration: {
                                    maxEntries: 60,
                                    maxAgeSeconds: 30 * 24 * 60 * 60 // 30 days
                                }
                            }
                        }
                    ]
                },
                includeAssets: ['favicon.ico', 'apple-touch-icon.png', 'masked-icon.svg'],
                manifest: {
                    name: 'OnlyVerified - Premium Talent Platform',
                    short_name: 'OnlyVerified',
                    description: 'Premium talent platform for the adult content industry with real-time messaging and job marketplace.',
                    theme_color: '#6366f1',
                    background_color: '#ffffff',
                    display: 'standalone',
                    orientation: 'portrait',
                    scope: '/',
                    start_url: '/',
                    icons: [
                        {
                            src: 'pwa-64x64.png',
                            sizes: '64x64',
                            type: 'image/png'
                        },
                        {
                            src: 'pwa-192x192.png',
                            sizes: '192x192',
                            type: 'image/png'
                        },
                        {
                            src: 'pwa-512x512.png',
                            sizes: '512x512',
                            type: 'image/png',
                            purpose: 'any'
                        },
                        {
                            src: 'maskable-icon-512x512.png',
                            sizes: '512x512',
                            type: 'image/png',
                            purpose: 'maskable'
                        }
                    ],
                    shortcuts: [
                        {
                            name: 'Messages',
                            short_name: 'Chat',
                            url: '/messages',
                            icons: [{ src: 'pwa-192x192.png', sizes: '192x192' }]
                        },
                        {
                            name: 'Marketplace',
                            short_name: 'Jobs',
                            url: '/marketplace',
                            icons: [{ src: 'pwa-192x192.png', sizes: '192x192' }]
                        }
                    ]
                },
                devOptions: {
                    enabled: true
                }
            }),
        ],

        resolve: {
            alias: {
                '@': resolve(__dirname, 'resources/js'),
                '~': resolve(__dirname, 'resources'),
                'ziggy': resolve(__dirname, 'vendor/tightenco/ziggy/dist'),
            },
        },

        define: {
            __VUE_OPTIONS_API__: true,
            __VUE_PROD_DEVTOOLS__: false,
            __VUE_PROD_HYDRATION_MISMATCH_DETAILS__: false,
        },

        build: {
            target: 'es2015',
            outDir: 'public/build',
            assetsDir: 'assets',
            manifest: true,
            rollupOptions: {
                output: {
                    manualChunks: {
                        'vue-vendor': ['vue', 'pinia'],
                        'react-vendor': ['react', 'react-dom'],
                        'ui-vendor': ['@headlessui/vue', '@heroicons/vue'],
                        'utils': ['axios', 'date-fns', 'clsx'],
                        'realtime': ['laravel-echo', 'pusher-js'],
                    },
                },
            },
            sourcemap: command === 'serve',
            minify: command === 'build' ? 'terser' : false,
            terserOptions: {
                compress: {
                    drop_console: true,
                    drop_debugger: true,
                },
            },
            chunkSizeWarningLimit: 1000,
        },

        server: {
            host: '0.0.0.0',
            port: 5174,
            strictPort: false,
            hmr: {
                host: 'localhost',
            },
            proxy: {
                '/api': {
                    target: env.APP_URL || 'http://localhost:8000',
                    changeOrigin: true,
                    secure: false,
                },
                '/broadcasting/auth': {
                    target: env.APP_URL || 'http://localhost:8000',
                    changeOrigin: true,
                    secure: false,
                },
            },
        },

        css: {
            devSourcemap: true,
        },

        optimizeDeps: {
            include: [
                'vue',
                'pinia',
                'axios',
                'laravel-echo',
                'pusher-js',
                '@headlessui/vue',
                '@heroicons/vue',
            ],
            exclude: ['@vite/client', '@vite/env'],
        },

        envPrefix: ['VITE_', 'MIX_'],

        esbuild: {
            drop: command === 'build' ? ['console', 'debugger'] : [],
        },
    };
});
