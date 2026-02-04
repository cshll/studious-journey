<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Trafford Bus</title>
  <link rel="stylesheet" href="style.css">
  <link rel="manifest" href="manifest.json">
  <?php
    $current_page = basename($_SERVER['PHP_SELF']);

    if ($current_page == 'livemap.php') {
      require 'leaflet-js.php';
    }
  ?>
</head>
