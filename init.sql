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
INSERT INTO routes (route_number, route_name) VALUES 
('X50', 'Manchester to Trafford');

/*3 Stops*/
INSERT INTO stops (stop_name, latitude, longitude) VALUES 
('Trafford Centre', 53.4668, -2.3488), 
('Old Trafford', 53.4563, -2.2882),
('Piccadilly Gardens', 53.4811, -2.2369);

/*24 Trips*/
INSERT INTO trips (route_id, trip_headsign) VALUES 
(1, 'Piccadilly'), (1, 'Piccadilly'), (1, 'Piccadilly'), (1, 'Piccadilly'),
(1, 'Piccadilly'), (1, 'Piccadilly'), (1, 'Piccadilly'), (1, 'Piccadilly'),
(1, 'Piccadilly'), (1, 'Piccadilly'), (1, 'Piccadilly'), (1, 'Piccadilly'),
(1, 'Piccadilly'), (1, 'Piccadilly'), (1, 'Piccadilly'), (1, 'Piccadilly'),
(1, 'Piccadilly'), (1, 'Piccadilly'), (1, 'Piccadilly'), (1, 'Piccadilly'),
(1, 'Piccadilly'), (1, 'Piccadilly'), (1, 'Piccadilly'), (1, 'Piccadilly');

INSERT INTO stop_times (trip_id, stop_id, arrival_time, stop_sequence) VALUES
/* Trip 1: 00:00 Midnight */
(1, 1, '00:00', 1), (1, 2, '00:20', 2), (1, 3, '00:45', 3),
/* Trip 2: 01:00 AM */
(2, 1, '01:00', 1), (2, 2, '01:20', 2), (2, 3, '01:45', 3),
/* Trip 3: 02:00 AM */
(3, 1, '02:00', 1), (3, 2, '02:20', 2), (3, 3, '02:45', 3),
/* Trip 4: 03:00 AM */
(4, 1, '03:00', 1), (4, 2, '03:20', 2), (4, 3, '03:45', 3),
/* Trip 5: 04:00 AM */
(5, 1, '04:00', 1), (5, 2, '04:20', 2), (5, 3, '04:45', 3),
/* Trip 6: 05:00 AM */
(6, 1, '05:00', 1), (6, 2, '05:20', 2), (6, 3, '05:45', 3),
/* Trip 7: 06:00 AM */
(7, 1, '06:00', 1), (7, 2, '06:20', 2), (7, 3, '06:45', 3),
/* Trip 8: 07:00 AM */
(8, 1, '07:00', 1), (8, 2, '07:20', 2), (8, 3, '07:45', 3),
/* Trip 9: 08:00 AM */
(9, 1, '08:00', 1), (9, 2, '08:20', 2), (9, 3, '08:45', 3),
/* Trip 10: 09:00 AM */
(10, 1, '09:00', 1), (10, 2, '09:20', 2), (10, 3, '09:45', 3),
/* Trip 11: 10:00 AM */
(11, 1, '10:00', 1), (11, 2, '10:20', 2), (11, 3, '10:45', 3),
/* Trip 12: 11:00 AM */
(12, 1, '11:00', 1), (12, 2, '11:20', 2), (12, 3, '11:45', 3),
/* Trip 13: 12:00 PM */
(13, 1, '12:00', 1), (13, 2, '12:20', 2), (13, 3, '12:45', 3),
/* Trip 14: 13:00 PM */
(14, 1, '13:00', 1), (14, 2, '13:20', 2), (14, 3, '13:45', 3),
/* Trip 15: 14:00 PM */
(15, 1, '14:00', 1), (15, 2, '14:20', 2), (15, 3, '14:45', 3),
/* Trip 16: 15:00 PM */
(16, 1, '15:00', 1), (16, 2, '15:20', 2), (16, 3, '15:45', 3),
/* Trip 17: 16:00 PM */
(17, 1, '16:00', 1), (17, 2, '16:20', 2), (17, 3, '16:45', 3),
/* Trip 18: 17:00 PM */
(18, 1, '17:00', 1), (18, 2, '17:20', 2), (18, 3, '17:45', 3),
/* Trip 19: 18:00 PM */
(19, 1, '18:00', 1), (19, 2, '18:20', 2), (19, 3, '18:45', 3),
/* Trip 20: 19:00 PM */
(20, 1, '19:00', 1), (20, 2, '19:20', 2), (20, 3, '19:45', 3),
/* Trip 21: 20:00 PM */
(21, 1, '20:00', 1), (21, 2, '20:20', 2), (21, 3, '20:45', 3),
/* Trip 22: 21:00 PM */
(22, 1, '21:00', 1), (22, 2, '21:20', 2), (22, 3, '21:45', 3),
/* Trip 23: 22:00 PM */
(23, 1, '22:00', 1), (23, 2, '22:20', 2), (23, 3, '22:45', 3),
/* Trip 24: 23:00 PM */
(24, 1, '23:00', 1), (24, 2, '23:20', 2), (24, 3, '23:45', 3);

/* 1. New Route: Altrincham to Trafford */
/* This will be Route ID 2 */
INSERT INTO routes (route_number, route_name) VALUES 
('263', 'Altrincham to Trafford');

/* 2. Three Stops (Ordered Start to End) */
/* Stop IDs 4, 5, 6 */
INSERT INTO stops (stop_name, latitude, longitude) VALUES 
('Altrincham Interchange', 53.3872, -2.3482),  /* Start */
('Sale Tram Stop', 53.4251, -2.3167),          /* Middle */
('Trafford Centre', 53.4668, -2.3488);         /* End */

/* 3. Two Trips */
/* Trip IDs 3, 4 linked to Route ID 2 */
/* Headsign shows where the bus is GOING (Trafford) */
INSERT INTO trips (route_id, trip_headsign) VALUES 
(2, 'Trafford Centre'), 
(2, 'Trafford Centre');

/* 4. Trip 1 Times (Starts 10:00) */
/* Uses Stop IDs 4, 5, 6 */
INSERT INTO stop_times (trip_id, stop_id, arrival_time, stop_sequence) VALUES
(3, 4, '10:00', 1), /* Depart Altrincham */
(3, 5, '10:20', 2), /* Sale */
(3, 6, '10:45', 3); /* Arrive Trafford */

/* 5. Trip 2 Times (Starts 11:00) */
/* Uses Stop IDs 4, 5, 6 */
INSERT INTO stop_times (trip_id, stop_id, arrival_time, stop_sequence) VALUES
(4, 4, '11:00', 1), /* Depart Altrincham */
(4, 5, '11:20', 2), /* Sale */
(4, 6, '11:45', 3); /* Arrive Trafford */
