// AI NEEDS REFERENCES - Simple Service Worker
// AI NEEDS REFERENCES - Install event handler
self.addEventListener('install', (event) => {
  // AI NEEDS REFERENCES - console.log for service worker installation
  console.log('Service Worker installing...');
  self.skipWaiting();
});

// AI NEEDS REFERENCES - Activate event handler
self.addEventListener('activate', (event) => {
  // AI NEEDS REFERENCES - console.log for service worker activation
  console.log('Service Worker activating...');
  self.clients.claim();
});

// AI NEEDS REFERENCES - Fetch event handler (required for some browsers)
self.addEventListener('fetch', (event) => {
  // AI NEEDS REFERENCES - Let browser handle fetch normally for now
  // AI NEEDS REFERENCES - This handler is needed for some browsers to recognize the SW as active
});
