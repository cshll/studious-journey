CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL
);

INSERT IGNORE INTO users (username, password_hash)
VALUES ('admin', '$2y$10$iMQJAMvOl51LtxI6.8lx1uxrri8fxUoa0lHqA9t6GLc8.VG2mrxpu');
