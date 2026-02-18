if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/sw.js')
    .catch((error) => {
      console.error('Service Worker registration failed:', error);
    });
}

// Define buttons for later.
let deferredPrompt;
const pwaContainer = document.getElementById('pwaPromo');
const installBtn = document.getElementById('pwa-install-btn');
const closeBtn = document.getElementById('pwa-dismiss-btn');

// Slide out display function for tweening PWA.
function slideOut() {
  pwaContainer.classList.add('slide-out');

  setTimeout(() => {
    // Hide PWA.
    pwaContainer.style.display = 'none';
    pwaContainer.classList.remove('slide-out');
  }, 600);
}

window.addEventListener('beforeinstallprompt', (e) => {
  e.preventDefault();
  deferredPrompt = e;

  // Check local and session storage for installation or dismissal so it doesn't repeat.
  const isInstalled = localStorage.getItem('pwaInstalled') === 'true';
  const isDismissed = sessionStorage.getItem('pwaDismissed') === 'true';

  // Make PWA prompt visible.
  if (!isInstalled && !isDismissed) {
    pwaContainer.style.display = 'flex';
  }
});

installBtn.addEventListener('click', async () => {
  if (deferredPrompt) {
    deferredPrompt.prompt();
    const { outcome } = await deferredPrompt.userChoice;

    // Check if user has accepted PWA.
    if (outcome === 'accepted') {
      // Mark as installed.
      localStorage.setItem('pwaInstalled', 'true');
      slideOut();
      deferredPrompt = null;
    }
  } else {
    location.reload();
  }
});

closeBtn.addEventListener('click', () => {
  // Mark as dismissed.
  sessionStorage.setItem('pwaDismissed', true);
  slideOut();
});

window.addEventListener('appinstalled', () => {
  // Mark as installed.
  localStorage.setItem('pwaInstalled', 'true');
  // Hide PWA.
  pwaContainer.style.display = 'none'
  deferredPrompt = null;
});
