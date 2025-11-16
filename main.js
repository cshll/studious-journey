document.addEventListener('DOMContentLoaded', () => {
  const content = document.getElementById('content');

  const backButton = document.getElementById('back');
  const returnButton = document.getElementById('return');

  var lastOperation = 'select_key';
  var thisOperation = 'select_key';

  const fetchOperation = async (operation) => {
    console.log(`Attempting to load '${operation}'`)

    lastOperation = thisOperation;
    thisOperation = operation;

    try {
      const response = await fetch(`operations/${operation}.html`);
      if (!response.ok) {
        throw new Error(`HTTP ${response.status}`);
      }

      const htmlContent = await response.text();
      content.innerHTML = htmlContent;
    } catch (error) {
      console.error(`Error loading '${operation}': `, error);
      content.innerHTML = `<div id="container"><label id="btext" style="color: lightcoral">${error.message}!</label><br><label id="error">Error loading '${operation}'<br>Please try again later.</label></div>`;
    }
  };

  const chooseClass = async () => {
    await fetchOperation('choose_class');

    const selectElement = document.getElementById('classes');
    const handleSelectionChange = (event) => {
      const selectedValue = event.target.value;

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

    selectElement.addEventListener('change', handleSelectionChange);
  };

  const selectKey = async () => {
    await fetchOperation('select_key');

    const selectElement = document.getElementById('types');
    const handleSelectionChange = (event) => {
      const selectedValue = event.target.value;

      switch (selectedValue) {
        case 'pupil':
          fetchOperation('show_rows');
          break;

        case 'guardian':
          fetchOperation('show_rows')
          break;

        case 'class':
          chooseClass();
          break;

        case 'teacher':
          fetchOperation('show_rows')
          break;
      }
    };

    selectElement.addEventListener('change', handleSelectionChange);
  };

  selectKey();

  backButton.addEventListener('click', () => { selectKey(lastOperation); });
  returnButton.addEventListener('click', selectKey);
});
