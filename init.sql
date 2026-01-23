-- How to start the server:
-- - First download Docker Desktop on Windows: https://www.docker.com/products/docker-desktop/
-- - Install it as well as WSL 2 (if asked).
-- - Open Docker Desktop.
-- - Navigate to this project folder where 'docker-compose.yml' is.
-- - Right-click and open in terminal.
-- - Run this command below:
-- ```
-- docker-compose up -d --build --force-recreate
-- ```
-- - If it doesn't work run the below:
-- ```
-- docker compose up -d --build --force-recreate
-- ```
-- - Verify it's up using:
-- ```
-- docker ps
-- ```
-- - Visit localhost on your browser for the site and localhost:8080 for phpMyAdmin.
-- This can also be done on Linux (**run using sudo**) using the above commands in a terminal (if docker is installed).

CREATE TABLE IF NOT EXISTS users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL
);

INSERT INTO users (email, password_hash) VALUES 
('admin@localhost', '$2y$10$w.twbxazasehpTWPJ3dL1OyvZCxmKCFYU6SnvexzPaAEs0BWorCem');
