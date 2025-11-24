import './bootstrap';

import Alpine from 'alpinejs';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// Only initialize Echo if Reverb is configured
if (import.meta.env.VITE_REVERB_APP_KEY) {
    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST || '127.0.0.1',
        wsPort: import.meta.env.VITE_REVERB_PORT ?? 8081,
        wssPort: import.meta.env.VITE_REVERB_PORT ?? 8443,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
        enabledTransports: ['ws', 'wss'],
    });
} else {
    // Fallback: Echo will not be available, but app won't crash
    console.warn('Laravel Echo not configured. Real-time features will not work.');
    window.Echo = null;
}

window.Alpine = Alpine;

Alpine.start();
