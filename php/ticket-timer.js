document.addEventListener('DOMContentLoaded', function() {
  const timers = document.querySelectorAll('.countdown');

  // Run over each timer and check their expiry times.
  timers.forEach(timer => {
    // Grab all times including server and client time for exploit prevention.
    const expireTime = new Date(timer.dataset.expires).getTime();
    const serverStart = new Date(timer.dataset.serverNow).getTime();
    const clientStart = new Date().getTime();

    const clockOffset = clientStart - serverStart;

    function update() {
      // Calculate differences in times.
      const now = new Date().getTime() - clockOffset;
      const diff = expireTime - now;

      if (diff <= 0) {
        // Mark ticket as expired.
        timer.innerHTML = "EXPIRED";
        timer.style.color = "red";
        return;
      } else {
        // Show hours, minutes, and seconds.
        const hours = Math.floor(diff / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((diff % (1000 * 60)) / 1000);

        // Set timer HTML.
        timer.innerHTML =
          (hours < 10 ? "0" + hours : hours) + ":" +
          (minutes < 10 ? "0" + minutes : minutes) + ":" +
          (seconds < 10 ? "0" + seconds : seconds);
      }
    }

    setInterval(update, 1000);
  });
});
