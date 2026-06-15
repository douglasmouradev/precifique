const PRECIFIQUE_CACHE = 'precifique-static-v1';
const PRECIFIQUE_ASSETS = [
    '/images/favicon.svg',
    '/apple-touch-icon.png',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(PRECIFIQUE_CACHE).then((cache) => cache.addAll(PRECIFIQUE_ASSETS)).catch(() => {})
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(self.clients.claim());
});

self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') {
        return;
    }

    const url = new URL(event.request.url);
    if (!url.pathname.startsWith('/images/') && !url.pathname.includes('apple-touch-icon')) {
        return;
    }

    event.respondWith(
        caches.match(event.request).then((cached) => cached || fetch(event.request))
    );
});
