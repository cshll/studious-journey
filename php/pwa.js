// AI NEEDS REFERENCES - Service Worker Registration
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/sw.js')
    .catch((error) => {
      // AI NEEDS REFERENCES - console.error for service worker registration failure
      console.error('Service Worker registration failed:', error);
    });
}

let deferredPrompt;
const pwaContainer = document.getElementById('pwaPromo');
const installBtn = document.getElementById('pwa-install-btn');
const closeBtn = document.getElementById('pwa-dismiss-btn');

function slideOut() {
  pwaContainer.classList.add('slide-out');

  setTimeout(() => {
    pwaContainer.style.display = 'none';
    pwaContainer.classList.remove('slide-out');
  }, 600);
}

window.addEventListener('beforeinstallprompt', (e) => {
  e.preventDefault();
  deferredPrompt = e;

  const isInstalled = localStorage.getItem('pwaInstalled') === 'true';
  const isDismissed = sessionStorage.getItem('pwaDismissed') === 'true';

  if (!isInstalled && !isDismissed) {
    pwaContainer.style.display = 'flex';
  }
});

installBtn.addEventListener('click', async () => {
  if (deferredPrompt) {
    deferredPrompt.prompt();
    const { outcome } = await deferredPrompt.userChoice;

    if (outcome === 'accepted') {
      localStorage.setItem('pwaInstalled', 'true');
      slideOut();
      deferredPrompt = null;
    }
  } else {
    location.reload();
  }
});

closeBtn.addEventListener('click', () => {
  sessionStorage.setItem('pwaDismissed', true);
  slideOut();
});

window.addEventListener('appinstalled', () => {
  localStorage.setItem('pwaInstalled', 'true');
  pwaContainer.style.display = 'none'
  deferredPrompt = null;
});
