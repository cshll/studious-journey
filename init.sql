-- Initiates needed tables within the SQL file.

-- Creates the users table, allows for username and password_hash with an auto increment for id.
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL
);

-- Inserts the default user 'admin' with the password 'school' into the users table.
INSERT IGNORE INTO users (username, password_hash)
VALUES ('admin', '$2y$10$iMQJAMvOl51LtxI6.8lx1uxrri8fxUoa0lHqA9t6GLc8.VG2mrxpu');
