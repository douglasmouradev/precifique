const PRECIFIQUE_CACHE = 'precifique-static-v3';
const PRECIFIQUE_ASSETS = [
    '/images/favicon.svg',
    '/apple-touch-icon.png',
    '/manifest.json',
    '/offline.html',
];

const PRECIFIQUE_SHELL = [
    '/app/dashboard',
    '/app/menu',
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(PRECIFIQUE_CACHE).then((cache) => cache.addAll([...PRECIFIQUE_ASSETS]).catch(() => {}))
    );
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(keys.filter((k) => k !== PRECIFIQUE_CACHE).map((k) => caches.delete(k)))
        ).then(() => self.clients.claim())
    );
});

function cacheFirst(request) {
    return caches.match(request).then((cached) => {
        if (cached) {
            return cached;
        }

        return fetch(request).then((response) => {
            if (response.ok) {
                const clone = response.clone();
                caches.open(PRECIFIQUE_CACHE).then((cache) => cache.put(request, clone));
            }

            return response;
        });
    });
}

self.addEventListener('fetch', (event) => {
    if (event.request.method !== 'GET') {
        return;
    }

    const url = new URL(event.request.url);

    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request).catch(() => caches.match('/offline.html'))
        );

        return;
    }

    if (url.pathname.startsWith('/build/assets/')) {
        event.respondWith(cacheFirst(event.request));

        return;
    }

    if (PRECIFIQUE_SHELL.some((path) => url.pathname === path)) {
        event.respondWith(
            fetch(event.request)
                .then((response) => {
                    const clone = response.clone();
                    caches.open(PRECIFIQUE_CACHE).then((cache) => cache.put(event.request, clone));

                    return response;
                })
                .catch(() => caches.match(event.request))
        );

        return;
    }

    if (url.pathname.startsWith('/images/') || url.pathname.includes('apple-touch-icon')) {
        event.respondWith(cacheFirst(event.request));
    }
});
