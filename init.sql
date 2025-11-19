-- Initiates needed tables within the SQL file.

-- Creates the users table, allows for username and password_hash with an auto increment for id.
CREATE TABLE IF NOT EXISTS users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL
);

-- Inserts the default user 'admin' with the password 'school' into the users table.
INSERT IGNORE INTO users (username, password_hash)
VALUES ('admin', '$2y$10$iMQJAMvOl51LtxI6.8lx1uxrri8fxUoa0lHqA9t6GLc8.VG2mrxpu');

-- Creates the pupils table.
CREATE TABLE IF NOT EXISTS classes (
  class_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL,
  capacity INT NOT NULL
);

CREATE TABLE IF NOT EXISTS teachers (
  teacher_id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  address TEXT NOT NULL,
  email VARCHAR(100) NOT NULL,
  phone_number VARCHAR(20) NOT NULL,
  annual_salary DECIMAL(10, 2) NOT NULL,
  background_check BOOLEAN DEFAULT FALSE NOT NULL,
  class_id INT,
  FOREIGN KEY (class_id) REFERENCES classes(class_id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS pupils (
  pupil_id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  address TEXT NOT NULL,
  birthday DATE NOT NULL,
  medical_info TEXT,
  class_id INT,
  FOREIGN KEY (class_id) REFERENCES classes(class_id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS guardians (
  guardian_id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  address TEXT NOT NULL,
  email VARCHAR(100) NOT NULL,
  phone_number VARCHAR(20) NOT NULL
);

CREATE TABLE IF NOT EXISTS guardian_pupil (
  pupil_id INT,
  guardian_id INT,
  PRIMARY KEY (pupil_id, guardian_id),
  FOREIGN KEY (pupil_id) REFERENCES pupils(pupil_id) ON DELETE CASCADE,
  FOREIGN KEY (guardian_id) REFERENCES guardians(guardian_id) ON DELETE CASCADE
);
