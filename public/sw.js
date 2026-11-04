/**
 * SINDEN Progressive Web App - Service Worker
 * Version: 1.0.0
 */

const CACHE_NAME = 'sinden-cache-v1.0.1';

// Aset statis inti yang dicache saat instalasi
const CORE_ASSETS = [
    '/manifest.json',
    '/assets/logo/icon-192.png',
    '/assets/logo/icon-512.png',
    '/assets/logo/apple-touch-icon.png',
    '/assets/logo/sinden-og.png',
    '/assets/fonts/Ubuntu-Regular.ttf',
    '/assets/fonts/Ubuntu-Bold.ttf'
];

// 1. Install Event: Cache Core Assets
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return cache.addAll(CORE_ASSETS).catch((err) => {
                console.warn('[SW] Core assets pre-caching non-fatal warning:', err);
            });
        }).then(() => self.skipWaiting())
    );
});

// 2. Activate Event: Bersihkan cache lama & klaim clients
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cache) => {
                    if (cache !== CACHE_NAME) {
                        return caches.delete(cache);
                    }
                })
            );
        }).then(() => self.clients.claim())
    );
});

// 3. Fetch Event: Strategi Cerdas
self.addEventListener('fetch', (event) => {
    const request = event.request;

    // Jangan sentuh request selain GET (POST, PUT, DELETE, Upload tetap langsung ke network)
    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);

    // Jangan cache endpoint dynamic API / AJAX / debugbar
    if (
        url.pathname.includes('data') ||
        url.pathname.includes('save') ||
        url.pathname.includes('update') ||
        url.pathname.includes('delete') ||
        url.pathname.includes('export') ||
        url.search.includes('debugbar') ||
        request.headers.get('accept')?.includes('application/json') ||
        request.headers.get('x-requested-with') === 'XMLHttpRequest'
    ) {
        return; // Lewatkan langsung ke server
    }

    // Strategi Navigasi Halaman HTML: Network First (Prioritaskan server agar data selalu realtime)
    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request).catch(() => {
                return caches.match(request).then((cachedResponse) => {
                    if (cachedResponse) return cachedResponse;
                    // Fallback jika benar-benar offline
                    return new Response(
                        `<!DOCTYPE html>
                        <html lang="id">
                        <head>
                            <meta charset="utf-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1">
                            <title>Offline — SINDEN</title>
                            <style>
                                @font-face { font-family: Ubuntu; src: url("/assets/fonts/Ubuntu-Regular.ttf") format("truetype"); font-weight: 400; font-style: normal; font-display: swap; }
                                @font-face { font-family: Ubuntu; src: url("/assets/fonts/Ubuntu-Bold.ttf") format("truetype"); font-weight: 700; font-style: normal; font-display: swap; }
                                * { box-sizing: border-box; }
                                body { display: grid; place-items: center; min-height: 100vh; min-height: 100dvh; margin: 0; padding: 24px; font-family: Ubuntu, sans-serif; text-align: center; background: #2EC4B6; color: #333; }
                                .card { width: min(100%, 400px); margin: 0; background: #fff; border-radius: 12px; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
                                h2 { color: #2EC4B6; margin-top: 0; }
                                p { color: #666; font-size: 14px; line-height: 1.6; }
                                .btn { display: inline-block; padding: 10px 24px; background: #2EC4B6; color: #fff; text-decoration: none; border-radius: 6px; font-weight: bold; margin-top: 15px; }
                            </style>
                        </head>
                        <body>
                            <div class="card">
                                <h2>Koneksi Terputus</h2>
                                <p>Aplikasi SINDEN tidak dapat terhubung ke server. Periksa koneksi internet Anda dan coba muat ulang.</p>
                                <a href="javascript:location.reload()" class="btn">Muat Ulang</a>
                            </div>
                        </body>
                        </html>`,
                        { headers: { 'Content-Type': 'text/html; charset=utf-8' } }
                    );
                });
            })
        );
        return;
    }

    // Strategi Static Assets (Images, CSS, JS, Fonts): Stale While Revalidate
    if (
        request.destination === 'style' ||
        request.destination === 'script' ||
        request.destination === 'image' ||
        request.destination === 'font'
    ) {
        event.respondWith(
            caches.match(request).then((cachedResponse) => {
                const fetchPromise = fetch(request).then((networkResponse) => {
                    if (networkResponse && networkResponse.status === 200 && networkResponse.type === 'basic') {
                        const responseToCache = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(request, responseToCache);
                        });
                    }
                    return networkResponse;
                }).catch(() => cachedResponse);

                return cachedResponse || fetchPromise;
            })
        );
    }
});
