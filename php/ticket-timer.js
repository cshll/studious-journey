document.addEventListener('DOMContentLoaded', function() {
  const timers = document.querySelectorAll('.countdown');

  function updateTimers() {
    const now = new Date();

    timers.forEach(timer => {
      const expireTime = new Date(timer.dataset.expires);
      const diff = expireTime - now;

      if (diff <= 0) {
        timer.innerHTML = "EXPIRED";
        timer.style.color = "red";
        return;
      }

      const hours = Math.floor(diff / (1000 * 60 * 60));
      const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
      const seconds = Math.floor((diff % (1000 * 60)) / 1000);

      timer.innerHTML =
        (hours < 10 ? "0" + hours : hours) + ":" +
        (minutes < 10 ? "0" + minutes : minutes) + ":" +
        (seconds < 10 ? "0" + seconds : seconds);
    });
  }

  updateTimers();
  setInterval(updateTimers, 1000);
});
