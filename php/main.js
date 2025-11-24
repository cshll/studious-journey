// Handles form control within the website.

// Wait for the document object model to load.
document.addEventListener('DOMContentLoaded', () => {
  // Grab content location.
  const content = document.getElementById('content');

  // Grab back and return buttons within the navigation section.
  const backButton = document.getElementById('back');
  const returnButton = document.getElementById('return');

  // Save current operations for the navigation.
  var lastOperation = 'login';
  var thisOperation = 'login';

  const fetchOperation = async (operation) => {
    console.log(`Attempting to load '${operation}'`)

    // Set operations accordingly.
    lastOperation = thisOperation;
    thisOperation = operation;

    try {
      // Attempt to fetch the required operation.
      const response = await fetch(`operations/${operation}.html`);
      // If the operation can't be found it will throw an error.
      if (!response.ok) {
        throw new Error(`HTTP ${response.status}`);
      }

      // Set the page content to the correct operation form.
      const htmlContent = await response.text();
      content.innerHTML = htmlContent;
    } catch (error) {
      // Throw a more verbose error for the user to see.
      console.error(`Error loading '${operation}': `, error);
      content.innerHTML = `<div id="container"><label id="btext" style="color: red">${error.message}!</label><br><label class="error">Error loading '${operation}'<br>Please try again later.</label></div>`;
    }
  };

  const chooseClass = async () => {
    // Fetch the choose class operation and wait for it.
    await fetchOperation('choose_class');

    // Find the classes element.
    const selectElement = document.getElementById('classes');
    const handleSelectionChange = (event) => {
      // Get the value of the dropdown object.
      const selectedValue = event.target.value;

      // Switch statement to send user to the corresponding form they clicked.
      switch (selectedValue) {
        case 'reception':
          fetchOperation('show_rows');
          break;

        case 'one':
          fetchOperation('show_rows');
          break;

        case 'two':
          fetchOperation('show_rows');
          break;

        case 'three':
          fetchOperation('show_rows');
          break;

        case 'four':
          fetchOperation('show_rows');
          break;

        case 'five':
          fetchOperation('show_rows');
          break;

        case 'six':
          fetchOperation('show_rows');
          break;
      }
    };

    // Wait for any changes to the dropdown selection.
    selectElement.addEventListener('change', handleSelectionChange);
  };

  const selectKey = async () => {
    // Fetch and wait for the select key operation.
    await fetchOperation('select_key');

    // Find the types element.
    const selectElementTypes = document.getElementById('types');
    const handleSelectionChangeTypes = (event) => {
      // Grab the users input within the dropdown.
      const selectedValue = event.target.value;

      // Switch statement to show the user their corresponding selection.
      switch (selectedValue) {
        case 'pupil':
          fetchOperation('show_rows');
          break;

        case 'class':
          chooseClass();
          break;

        case 'teacher':
          fetchOperation('show_rows')
          break;
      }
    };

    const selectElementObjects = document.getElementById('objects');
    const handleSelectionChangeObjects = (event) => {
      const selectedValue = event.target.value;

      switch (selectedValue) {
        case 'pupil':
          fetchOperation('create_pupil');
          break;

        case 'teacher':
          fetchOperation('create_teacher');
          break;
      }
    };

    // Check for any changes to the dropdown selection.
    selectElementTypes.addEventListener('change', handleSelectionChangeTypes);
    selectElementObjects.addEventListener('change', handleSelectionChangeObjects);
  };

  const login = async () => {
    await fetchOperation('login');

    const loginForm = document.getElementById('login');
    const errorLogin = document.getElementById('errorLogin');

    const handleSubmit = async (event) => {
      event.preventDefault();

      const formData = new FormData(loginForm);

      try {
        const response = await fetch('auth.php', {
          method: 'POST',
          body: formData
        });

        const data = await response.json();

        if (data.success) {
          // Override back button to prevent going back to login page.
          lastOperation = 'select_key';
          thisOperation = 'select_key';

          selectKey();
        } else {
          errorLogin.innerText = 'Invalid username or password';
        }
      } catch (error) {
        errorLogin.innerText = 'Unknown error';
      }
    };

    loginForm.addEventListener('submit', handleSubmit);
  };

  const logout = async () => {
    lastOperation = 'login';
    thisOperation = 'login';

    // logout code

    login();
  };

  // Show the login page before anything else.
  login();

  backButton.addEventListener('click', () => { fetchOperation(lastOperation); });
  returnButton.addEventListener('click', logout);
});
