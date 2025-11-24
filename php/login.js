document.addEventListener('DOMContentLoaded', () => {
  const loginForm = document.getElementById('login');
  const errorLogin = document.getElementById('errorLogin');

  const handleSubmit = async (event) => {
    event.preventDefault();

    const formData = new FormData(loginForm);

    try {
      const response = await fetch('login.php', {
        method: 'POST',
        body: formData
      });

      const data = await response.json();

      if (data.success) {
        window.location.href = 'index.php';
      } else {
        errorLogin.innerText = 'Invalid username or password';
      }
    } catch (error) {
      errorLogin.innerText = 'Unknown error';
    }
  };

  loginForm.addEventListener('submit', handleSubmit);
});
