const CACHE_NAME = 'trafford-bus-v1';
const ASSETS_TO_CACHE = [
  '/',
  //'/index.php',
  //'/journeys.php',
  //'/livemap.php',
  //'/login.php',
  //'/logout.php',
  //'/signup.php',
  //'/tickets.php',
  //'/timetable.php',
  //'/style.css',
  '/pwa.js',
  '/icon-192.png',
  '/icon-512.png'
];

self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(ASSETS_TO_CACHE);
    })
  );
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  event.respondWith(
    caches.match(event.request).then((response) => {
      return response || fetch(event.request);
    })
  );
});
