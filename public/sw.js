const CACHE_NAME = 'pharmaco-pos-v1';
const ASSETS_TO_CACHE = [
    '/pos/terminal',           // The POS URL
    '/build/assets/app.css',   // Your compiled CSS (Check your actual file name in public/build)
    '/build/assets/app.js',    // Your compiled JS
    'https://cdn.tailwindcss.com', // Tailwind CDN
    '//unpkg.com/alpinejs'     // Alpine CDN
];

// 1. Install Event: Cache files
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(ASSETS_TO_CACHE);
        })
    );
});

// 2. Fetch Event: Serve from Cache if Offline
self.addEventListener('fetch', (event) => {
    event.respondWith(
        fetch(event.request).catch(() => {
            return caches.match(event.request);
        })
    );
});

// 3. Activate Event: Clean old caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) => {
            return Promise.all(
                keys.filter(key => key !== CACHE_NAME)
                    .map(key => caches.delete(key))
            );
        })
    );
});