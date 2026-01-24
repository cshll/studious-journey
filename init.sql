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

CREATE TABLE IF NOT EXISTS routes (
  route_id INT AUTO_INCREMENT PRIMARY KEY,
  route_number VARCHAR(10) NOT NULL,
  route_name VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS stops (
  stop_id INT AUTO_INCREMENT PRIMARY KEY,
  stop_name VARCHAR(100) NOT NULL,
  latitude DECIMAL(10, 8),
  longitude DECIMAL(11, 8)
);

CREATE TABLE IF NOT EXISTS trips (
  trip_id INT AUTO_INCREMENT PRIMARY KEY,
  route_id INT,
  trip_headsign VARCHAR(100),
  FOREIGN KEY (route_id) REFERENCES routes(route_id)
);

CREATE TABLE IF NOT EXISTS stop_times (
  id INT AUTO_INCREMENT PRIMARY KEY,
  trip_id INT,
  stop_id INT,
  arrival_time VARCHAR(5),
  stop_sequence INT,
  FOREIGN KEY (trip_id) REFERENCES trips(trip_id),
  FOREIGN KEY (stop_id) REFERENCES stops(stop_id)
);

/*DUMMY DATA*/

/*Route X50*/
INSERT INTO routes (route_number, route_name) VALUES ('X50', 'Manchester to Trafford');

/*3 Stops*/
INSERT INTO stops (stop_name, latitude, longitude) VALUES 
('Trafford Centre', 53.4668, -2.3488), 
('Old Trafford', 53.4563, -2.2882),
('Piccadilly Gardens', 53.4811, -2.2369);

/*2 Trips*/
INSERT INTO trips (route_id, trip_headsign) VALUES (1, 'Picadilly'), (1, 'Picadilly');

/*Trip 1 (ID 1)*/
INSERT INTO stop_times (trip_id, stop_id, arrival_time, stop_sequence) VALUES
(1, 1, '08:00', 1),
(1, 2, '08:20', 2),
(1, 3, '08:45', 3);

/*Trip 2 (ID 2)*/
INSERT INTO stop_times (trip_id, stop_id, arrival_time, stop_sequence) VALUES
(2, 1, '09:00', 1),
(2, 2, '09:20', 2),
(2, 3, '09:45', 3);
