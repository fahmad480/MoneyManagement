const CACHE_NAME = 'jangan-boros-v4';
const urlsToCache = [
    'https://cdn.tailwindcss.com',
    'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
    'https://code.jquery.com/jquery-3.7.1.min.js',
    'https://cdn.jsdelivr.net/npm/chart.js',
    'https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js'
];

// Install Service Worker - Cache only external assets
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log('Caching external JS/CSS assets');
                return cache.addAll(urlsToCache.map(url => new Request(url, {
                    credentials: 'same-origin',
                    mode: 'no-cors'
                })));
            })
            .catch(err => {
                console.error('Cache installation failed:', err);
            })
    );
    self.skipWaiting();
});

// Fetch strategy: Cache only JS/CSS assets, Network Only for pages
self.addEventListener('fetch', event => {
    const url = new URL(event.request.url);
    const isAsset = url.pathname.endsWith('.js') || 
                    url.pathname.endsWith('.css') ||
                    url.pathname.includes('cdn.tailwindcss.com') ||
                    url.pathname.includes('cdnjs.cloudflare.com') ||
                    url.pathname.includes('code.jquery.com') ||
                    url.pathname.includes('cdn.jsdelivr.net');
    
    // Cache First for JS/CSS assets
    if (isAsset && event.request.method === 'GET') {
        event.respondWith(
            caches.match(event.request)
                .then(response => {
                    if (response) {
                        return response;
                    }
                    
                    return fetch(event.request).then(response => {
                        if (!response || response.status !== 200) {
                            return response;
                        }
                        
                        const responseToCache = response.clone();
                        caches.open(CACHE_NAME).then(cache => {
                            cache.put(event.request, responseToCache);
                        });
                        
                        return response;
                    });
                })
                .catch(err => {
                    console.error('Asset fetch failed:', err);
                    return new Response('Asset not available', {
                        status: 503,
                        headers: { 'Content-Type': 'text/plain' }
                    });
                })
        );
        return;
    }
    
    // Network Only for Laravel pages and API calls
    event.respondWith(
        fetch(event.request)
            .catch(err => {
                console.error('Fetch failed:', err);
                if (event.request.mode === 'navigate') {
                    return caches.match('/offline.html');
                }
                return new Response('Network error', {
                    status: 408,
                    headers: { 'Content-Type': 'text/plain' }
                });
            })
    );
});

// Activate Service Worker - Clean up old caches except current
self.addEventListener('activate', event => {
    const cacheWhitelist = [CACHE_NAME];
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheWhitelist.indexOf(cacheName) === -1) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    return self.clients.claim();
});

// Background Sync
self.addEventListener('sync', event => {
    if (event.tag === 'sync-transactions') {
        event.waitUntil(syncTransactions());
    }
});

async function syncTransactions() {
    try {
        // Implement your sync logic here
        console.log('Syncing transactions...');
    } catch (error) {
        console.error('Sync failed:', error);
    }
}

// Push Notifications
self.addEventListener('push', event => {
    const options = {
        body: event.data ? event.data.text() : 'New notification',
        icon: '/icons/icon-192x192.png',
        badge: '/icons/icon-72x72.png',
        vibrate: [200, 100, 200]
    };
    
    event.waitUntil(
        self.registration.showNotification('Jangan Boros', options)
    );
});

// Notification Click
self.addEventListener('notificationclick', event => {
    event.notification.close();
    
    event.waitUntil(
        clients.openWindow('/')
    );
});
