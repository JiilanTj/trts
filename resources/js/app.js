import './bootstrap';
import './chat'; // Chat real-time functionality

import Alpine from 'alpinejs';
import { registerSW } from 'virtual:pwa-register'; // PWA auto update helper

window.Alpine = Alpine;

Alpine.start();

// Gunakan helper resmi vite-plugin-pwa agar update SW otomatis & aman
const updateSW = registerSW({
    immediate: true,
    onNeedRefresh() {
        // Bisa tampilkan toast optional nanti
        console.log('Update PWA tersedia. Refresh untuk memuat versi terbaru.');
    },
    onOfflineReady() {
        console.log('Aplikasi siap offline.');
    }
});
