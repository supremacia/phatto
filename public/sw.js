//---------------------------------------------- CACHE -----------------
const CACHE = 'phatto-0.0.2';
const FILES = [
    '/',
    '/manual',
    '/about',
    '/privacy',

    'https://fonts.googleapis.com/css?family=Material+Icons',
    'https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/css/materialize.min.css',
    '/css/style.css',

    'https://code.jquery.com/jquery-3.3.1.min.js',
    'https://cdnjs.cloudflare.com/ajax/libs/materialize/0.100.2/js/materialize.min.js',
    '/js/main.js',

    '/img/frame.png',
    '/img/favicon/android-chrome-512x512.png'
];

//---------------------------------------------- INSTALL  ------------------------------
self.addEventListener('install', function(e) {
    
    console.log('[SWORKER install]');
    
    e.waitUntil(
        caches.open(CACHE).then(function(cache) {            
            console.log('[SWORKER caching "'+CACHE+'"]');
            return cache.addAll(FILES);
        })
    );
});

//---------------------------------------------- ACTIVATE ------------------------------
self.addEventListener('activate', function(e) {
    
    console.log('[SWORKER activate]');

    e.waitUntil(
        caches.keys().then(function(keyList) {
            return Promise.all(keyList.map(function(key) {
                if (key !== CACHE) {                    
                    console.log('[SWORKER removing "'+key+'" cache]');
                    return caches.delete(key);
                }
            }));
        })
    );
    return self.clients.claim();
});

//---------------------------------------------- FETCH   ------------------------------
self.addEventListener('fetch', function(e) {
    
    console.log('[SWORKER fetch]', e.request.url);

    e.respondWith(
        caches.match(e.request).then(function(response) {
            return response || fetch(e.request)
        })
    );
});