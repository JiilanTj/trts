import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { VitePWA } from 'vite-plugin-pwa';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        VitePWA({
            registerType: 'autoUpdate',
            includeAssets: ['favicon.ico','robots.txt','apple-touch-icon.png'],
            manifest: {
                name: 'TT Shop Global',
                short_name: 'TT Shop',
                description: 'TT Shop Global Progressive Web App',
                start_url: '/',
                scope: '/',
                display: 'standalone',
                background_color: '#ffffff',
                theme_color: '#0f172a',
                orientation: 'portrait-primary',
                icons: [
                    { src: '/icons/icon-192.png', sizes: '192x192', type: 'image/png' },
                    { src: '/icons/icon-512.png', sizes: '512x512', type: 'image/png' },
                    { src: '/icons/maskable-icon-192.png', sizes: '192x192', type: 'image/png', purpose: 'maskable' },
                    { src: '/icons/maskable-icon-512.png', sizes: '512x512', type: 'image/png', purpose: 'maskable' }
                ]
            },
            workbox: {
                globPatterns: ['**/*.{js,css,html,ico,png,svg,jpg,jpeg,webp}'],
                runtimeCaching: [
                    {
                        urlPattern: /\/api\/.*$/,
                        handler: 'NetworkFirst',
                        options: {
                            cacheName: 'api-cache',
                            networkTimeoutSeconds: 10,
                            expiration: { maxEntries: 50, maxAgeSeconds: 300 },
                            cacheableResponse: { statuses: [0, 200] }
                        }
                    },
                    {
                        urlPattern: /.*\.(?:png|jpg|jpeg|svg|gif|webp)/,
                        handler: 'CacheFirst',
                        options: {
                            cacheName: 'image-cache',
                            expiration: { maxEntries: 60, maxAgeSeconds: 7 * 24 * 60 * 60 },
                            cacheableResponse: { statuses: [0, 200] }
                        }
                    },
                    {
                        urlPattern: /.*\.(?:css|js)/,
                        handler: 'StaleWhileRevalidate',
                        options: {
                            cacheName: 'asset-cache',
                            expiration: { maxEntries: 60, maxAgeSeconds: 24 * 60 * 60 }
                        }
                    }
                ]
            },
            devOptions: {
                enabled: true,
                suppressWarnings: true,
                navigateFallback: '/',
                type: 'module'
            }
        })
    ],
});
