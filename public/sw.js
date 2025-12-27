const APP_VERSION = '1.0.1';
const CACHE_NAME = 'safehavun-v' + APP_VERSION;

// Static assets to cache (cache-first)
const STATIC_CACHE = [
    '/manifest.json',
    '/js/pwa-app.js',
    '/icons/icon-192.png',
    '/icons/icon-512.png'
];

// Pages to cache (network-first with fallback)
const PAGE_CACHE = [
    '/pwa'
];

// Install event
self.addEventListener('install', event => {
    console.log('[SW] Installing version', APP_VERSION);
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll([...STATIC_CACHE, ...PAGE_CACHE]))
            .then(() => {
                console.log('[SW] Cached all resources');
                // Don't skip waiting immediately - let the app control this
            })
    );
});

// Activate event - clean old caches and notify clients
self.addEventListener('activate', event => {
    console.log('[SW] Activating version', APP_VERSION);
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(cacheName => cacheName.startsWith('safehavun-') && cacheName !== CACHE_NAME)
                    .map(cacheName => {
                        console.log('[SW] Deleting old cache', cacheName);
                        return caches.delete(cacheName);
                    })
            );
        }).then(() => {
            // Notify all clients about the update
            return self.clients.matchAll().then(clients => {
                clients.forEach(client => {
                    client.postMessage({
                        type: 'SW_UPDATED',
                        version: APP_VERSION
                    });
                });
            });
        }).then(() => self.clients.claim())
    );
});

// Fetch event - different strategies for different resources
self.addEventListener('fetch', event => {
    // Skip non-GET requests
    if (event.request.method !== 'GET') return;

    // Skip chrome-extension and other non-http requests
    if (!event.request.url.startsWith('http')) return;

    const url = new URL(event.request.url);

    // API calls - network only, no caching
    if (url.pathname.startsWith('/api/')) {
        event.respondWith(
            fetch(event.request).catch(() => {
                return new Response(JSON.stringify({ error: 'Offline' }), {
                    status: 503,
                    headers: { 'Content-Type': 'application/json' }
                });
            })
        );
        return;
    }

    // Auth routes - network only
    if (url.pathname.startsWith('/auth/') || url.pathname === '/login' || url.pathname === '/logout') {
        event.respondWith(fetch(event.request));
        return;
    }

    // Static assets - cache first
    if (STATIC_CACHE.some(path => url.pathname === path) ||
        url.pathname.endsWith('.js') ||
        url.pathname.endsWith('.css') ||
        url.pathname.endsWith('.png') ||
        url.pathname.endsWith('.jpg') ||
        url.pathname.endsWith('.svg')) {
        event.respondWith(
            caches.match(event.request).then(response => {
                if (response) {
                    // Return cached version, but update cache in background
                    fetch(event.request).then(freshResponse => {
                        caches.open(CACHE_NAME).then(cache => {
                            cache.put(event.request, freshResponse);
                        });
                    }).catch(() => {});
                    return response;
                }
                return fetch(event.request).then(response => {
                    const responseClone = response.clone();
                    caches.open(CACHE_NAME).then(cache => {
                        cache.put(event.request, responseClone);
                    });
                    return response;
                });
            })
        );
        return;
    }

    // PWA page - network first with cache fallback
    if (url.pathname === '/pwa') {
        event.respondWith(
            fetch(event.request)
                .then(response => {
                    const responseClone = response.clone();
                    caches.open(CACHE_NAME).then(cache => {
                        cache.put(event.request, responseClone);
                    });
                    return response;
                })
                .catch(() => {
                    return caches.match(event.request).then(response => {
                        if (response) return response;
                        // Return offline page
                        return new Response(`
                            <!DOCTYPE html>
                            <html>
                            <head>
                                <meta charset="UTF-8">
                                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                                <title>SafeHavun - Offline</title>
                                <style>
                                    body {
                                        background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%);
                                        color: white;
                                        font-family: system-ui;
                                        min-height: 100vh;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        text-align: center;
                                        padding: 20px;
                                    }
                                    button {
                                        background: #10b981;
                                        color: white;
                                        border: none;
                                        padding: 12px 24px;
                                        border-radius: 12px;
                                        font-size: 16px;
                                        cursor: pointer;
                                        margin-top: 20px;
                                    }
                                </style>
                            </head>
                            <body>
                                <div>
                                    <h1>Je bent offline</h1>
                                    <p>Controleer je internetverbinding</p>
                                    <button onclick="location.reload()">Opnieuw proberen</button>
                                </div>
                            </body>
                            </html>
                        `, { headers: { 'Content-Type': 'text/html' } });
                    });
                })
        );
        return;
    }

    // Default - network first
    event.respondWith(
        fetch(event.request)
            .then(response => {
                const responseClone = response.clone();
                caches.open(CACHE_NAME).then(cache => {
                    cache.put(event.request, responseClone);
                });
                return response;
            })
            .catch(() => {
                return caches.match(event.request);
            })
    );
});

// Listen for skip waiting message
self.addEventListener('message', event => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        console.log('[SW] Skip waiting triggered');
        self.skipWaiting();
    }
});

// Background sync for portfolio (if supported)
self.addEventListener('sync', event => {
    if (event.tag === 'sync-portfolio') {
        console.log('[SW] Background sync: portfolio');
        // This would sync portfolio data when back online
    }
});
