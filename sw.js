// Nombre y versión de la caché
const CACHE_NAME = 'clientmanager-cache-v1';

// Archivos del "App Shell" que queremos pre-cachear
const assetsToCache = [
    '/proyectos/ClientManager/offline.html',
    '/proyectos/ClientManager/public/css/style.css',
    '/proyectos/ClientManager/manifest.json',
    // Asegúrate de que estas rutas de iconos sean correctas, 
    // en el manifest pusiste logo.jpg, aquí dice icons/icon-xxx.png
    // Si usas logo.jpg para todo, cambia estas líneas:
    '/proyectos/ClientManager/public/img/logo.jpg', 
    
    // URLs de CDNs
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css',
    'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css',
    'https://code.jquery.com/jquery-3.7.1.min.js',
    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js',
    'https://cdn.datatables.net/v/bs5/dt-2.0.8/datatables.min.css',
    'https://cdn.datatables.net/v/bs5/dt-2.0.8/datatables.min.js'
];

// --- Evento 1: Install ---
self.addEventListener('install', (event) => {
    console.log('[Service Worker] Instalando Client Manager...');
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('[Service Worker] Pre-cacheando App Shell...');
                return cache.addAll(assetsToCache);
            })
            .then(() => {
                return self.skipWaiting();
            })
    );
});

// --- Evento 2: Activate ---
self.addEventListener('activate', (event) => {
    console.log('[Service Worker] Activando...');
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        console.log('[Service Worker] Borrando caché antigua:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        }).then(() => {
            return self.clients.claim();
        })
    );
});

// --- Evento 3: Fetch (Estrategia Híbrida) ---
self.addEventListener('fetch', (event) => {
    // 1. Solo GET
    if (event.request.method !== 'GET') return;

    const url = new URL(event.request.url);

    // 2. EXCEPCIÓN: Las APIs nunca se cachean
    if (url.pathname.includes('/api/')) return;

    // 3. ESTRATEGIA PARA HTML (Navegación): Network First (Red primero)
    // Esto asegura que siempre veas la versión real (logueado o deslogueado)
    if (event.request.mode === 'navigate') {
        event.respondWith(
            fetch(event.request)
                .then((networkResponse) => {
                    return networkResponse; // Si hay internet, devolvemos la página fresca
                })
                .catch(() => {
                    // Si NO hay internet, mostramos la página offline
                    return caches.match('/proyectos/ClientManager/offline.html');
                })
        );
        return;
    }

    // 4. ESTRATEGIA PARA RECURSOS (CSS, JS, Imágenes): Cache First
    // Para que cargue rápido lo visual
    event.respondWith(
        caches.match(event.request)
            .then((cachedResponse) => {
                if (cachedResponse) {
                    return cachedResponse;
                }
                return fetch(event.request)
                    .then((networkResponse) => {
                        const responseToCache = networkResponse.clone();
                        caches.open(CACHE_NAME)
                            .then((cache) => {
                                cache.put(event.request, responseToCache);
                            });
                        return networkResponse;
                    });
            })
    );
});