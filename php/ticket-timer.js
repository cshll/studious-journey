document.addEventListener('DOMContentLoaded', function() {
  const timers = document.querySelectorAll('.countdown');

  timers.forEach(timer => {
    const expireTime = new Date(timer.dataset.expires).getTime();
    const serverStart = new Date(timer.dataset.serverNow).getTime();
    const clientStart = new Date().getTime();

    const clockOffset = clientStart - serverStart;

    function update() {
      const now = new Date().getTime() - clockOffset;
      const diff = expireTime - now;

      if (diff <= 0) {
        timer.innerHTML = "EXPIRED";
        timer.style.color = "red";
        return;
      } else {
        const hours = Math.floor(diff / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((diff % (1000 * 60)) / 1000);

        timer.innerHTML =
          (hours < 10 ? "0" + hours : hours) + ":" +
          (minutes < 10 ? "0" + minutes : minutes) + ":" +
          (seconds < 10 ? "0" + seconds : seconds);
      }
    }

    setInterval(update, 1000);
  });
});
