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
-- This can also be done on Linux (**run using sudo**) using the above commands in a terminal (if docker is installed)

-- ----------- --
-- USER SCHEMA --
-- ----------- --

CREATE TABLE IF NOT EXISTS users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  full_name VARCHAR(100),
  address TEXT,
  date_of_birth DATE,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (email, password_hash) VALUES 
('admin@localhost', '$2y$10$w.twbxazasehpTWPJ3dL1OyvZCxmKCFYU6SnvexzPaAEs0BWorCem');

-- ---------------- --
-- TRANSPORT SCHEMA --
-- ---------------- --

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
  direction TINYINT DEFAULT 0,
  trip_headsign VARCHAR(100),
  FOREIGN KEY (route_id) REFERENCES routes(route_id)
);

CREATE TABLE IF NOT EXISTS stop_times (
  stop_time_id INT AUTO_INCREMENT PRIMARY KEY,
  trip_id INT NOT NULL,
  stop_id INT NOT NULL,
  arrival_time TIME NOT NULL,
  stop_sequence INT NOT NULL,
  FOREIGN KEY (trip_id) REFERENCES trips(trip_id),
  FOREIGN KEY (stop_id) REFERENCES stops(stop_id)
);

-- ------------- --
-- TICKET SCHEMA --
-- ------------- --

CREATE TABLE IF NOT EXISTS ticket_scopes (
  scope_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL,
  description VARCHAR(255),
  validity_seconds INT NOT NULL,
  is_return BOOLEAN DEFAULT FALSE,
  ui_color_hex VARCHAR(7) DEFAULT '#000000'
);

CREATE TABLE IF NOT EXISTS passenger_types (
  passenger_type_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL,
  verification_required BOOLEAN DEFAULT FALSE
);

CREATE TABLE IF NOT EXISTS ticket_prices (
  price_id INT AUTO_INCREMENT PRIMARY KEY,
  scope_id INT NOT NULL,
  passenger_type_id INT NOT NULL,
  price DECIMAL(10, 2) NOT NULL,
  FOREIGN KEY (scope_id) REFERENCES ticket_scopes(scope_id),
  FOREIGN KEY (passenger_type_id) REFERENCES passenger_types(passenger_type_id),
  UNIQUE KEY unique_product (scope_id, passenger_type_id)
);

CREATE TABLE IF NOT EXISTS user_tickets (
  ticket_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  price_id INT NOT NULL,
  purchase_date DATETIME DEFAULT CURRENT_TIMESTAMP,
  activated_at DATETIME NULL,
  expires_at DATETIME NULL,
  ticket_hash VARCHAR(64) NOT NULL,
  status ENUM('unused', 'active', 'expired') DEFAULT 'unused',
  FOREIGN KEY (user_id) REFERENCES users(user_id),
  FOREIGN KEY (price_id) REFERENCES ticket_prices(price_id)
);

-- ----------- --
-- TICKET DATA --
-- ----------- --

INSERT INTO ticket_scopes (name, description, validity_seconds, is_return, ui_color_hex) VALUES 
('Single', 'One-way direct trip', 0, 0, '#3498db'),
('Return', 'There and back again', 0, 1, '#2980b9'),
('Explorer Pass', 'Unlimited travel for 24 hours', 86400, 0, '#e67e22'),
('7-Day Saver', 'Unlimited travel for one week', 604800, 0, '#9b59b6'),
('Commuter 30', 'Unlimited travel for 30 days', 2592000, 0, '#27ae60'),
('Freedom 365', 'Annual unlimited pass', 31536000, 0, '#f1c40f');

INSERT INTO passenger_types (name, verification_required) VALUES 
('Adult', 0), ('Child', 0), ('Student', 1), ('Senior', 1);

INSERT INTO ticket_prices (scope_id, passenger_type_id, price) VALUES 
(1, 1, 2.00), (2, 1, 3.80), (3, 1, 5.50), (4, 1, 22.00), (5, 1, 80.00), (6, 1, 850.00),
(1, 2, 1.00), (2, 2, 1.90), (3, 2, 2.75), (4, 2, 11.00), (5, 2, 40.00), (6, 2, 425.00),
(1, 3, 1.50), (2, 3, 2.85), (3, 3, 4.10), (4, 3, 16.50), (5, 3, 60.00), (6, 3, 630.00),
(1, 4, 1.20), (2, 4, 2.00), (3, 4, 3.50), (4, 4, 10.00), (5, 4, 30.00), (6, 4, 300.00);

-- -------------- --
-- TRANSPORT DATA --
-- -------------- --

-- 1-10: The Hub (Trafford Interchange)
INSERT INTO stops (stop_name, latitude, longitude) VALUES 
('Trafford Interchange (Stand A)', 53.46011, -2.28202),
('Trafford Interchange (Stand B)', 53.46013, -2.28208),
('Trafford Interchange (Stand C)', 53.46015, -2.28214),
('Trafford Interchange (Stand D)', 53.46017, -2.28220),
('Trafford Interchange (Stand E)', 53.46019, -2.28226),
('Trafford Interchange (Stand F)', 53.46021, -2.28232),
('Trafford Interchange (Stand G)', 53.46023, -2.28238),
('Trafford Interchange (Stand H)', 53.46025, -2.28244),
('Trafford Interchange (Stand I)', 53.46027, -2.28250),
('Trafford Interchange (Stand J)', 53.46029, -2.28256);

-- 11-20: Terminals (The Destinations)
INSERT INTO stops (stop_name, latitude, longitude) VALUES 
('The Trafford Centre', 53.467, -2.348),
('Old Trafford Cricket Ground', 53.456, -2.286),
('MediaCityUK', 53.472, -2.299),
('Sale Water Park', 53.431, -2.308),
('Altrincham Interchange', 53.387, -2.349),
('Urmston Library', 53.447, -2.356),
('Stretford Mall', 53.445, -2.311),
('Eccles Interchange', 53.483, -2.336),
('Manchester Piccadilly', 53.477, -2.230),
('Chorlton Tram Stop', 53.442, -2.275);

-- 21-40: Intermediate Stops (Connecting the Hub to Destinations)
INSERT INTO stops (stop_name, latitude, longitude) VALUES 
-- Route T1 intermediates
('White City Retail Park', 53.462, -2.280),
('Parkway', 53.465, -2.300),
-- Route T2 intermediates
('Talbot Road', 53.458, -2.290),
('Seymour Grove', 53.455, -2.288),
-- Route T3 intermediates
('Trafford Bar', 53.461, -2.282),
('Exchange Quay', 53.468, -2.295),
-- Route T4 intermediates
('Chester Road', 53.450, -2.300),
('Stretford Grammar', 53.440, -2.305),
-- Route T5 intermediates
('Brooklands', 53.420, -2.320),
('Timperley Village', 53.400, -2.330),
-- Route T6 intermediates
('Lostock Circle', 53.450, -2.330),
('Trafford General Hospital', 53.448, -2.345),
-- Route T7 intermediates
('Edge Lane', 53.450, -2.310),
('Longford Park', 53.448, -2.315),
-- Route T8 intermediates
('Trafford Park Hotel', 53.470, -2.310),
('Centenary Bridge', 53.480, -2.320),
-- Route T9 intermediates
('Cornbrook', 53.470, -2.260),
('Deansgate Castlefield', 53.474, -2.250),
-- Route T10 intermediates
('Firswood', 53.450, -2.280),
('Wilbraham Road', 53.445, -2.278);

INSERT INTO routes (route_number, route_name) VALUES 
('T1', 'Trafford Centre Express'),
('T2', 'Old Trafford Circular'),
('T3', 'MediaCityUK Connection'),
('T4', 'Sale Leisure Line'),
('T5', 'Altrincham Direct'),
('T6', 'Urmston Connector'),
('T7', 'Stretford Local'),
('T8', 'Eccles via Park'),
('T9', 'City Centre Express'),
('T10', 'Chorlton Shopper');


-- code horrible enough to make a grown man cry
DELIMITER $$

CREATE PROCEDURE GenerateSchedule()
BEGIN
  DECLARE h INT DEFAULT 0; -- Hour
  DECLARE r INT DEFAULT 1; -- Route ID
  DECLARE stop_start INT;
  DECLARE stop_end INT;
  DECLARE stop_mid1 INT;
  DECLARE stop_mid2 INT;
  DECLARE route_label VARCHAR(100);
  DECLARE trip_id_val INT;

  WHILE h < 24 DO
    SET r = 1;

    WHILE r <= 10 DO
      IF (r = 9) OR (h >= 7 AND h <= 22) THEN
        SET stop_start = r;
        SET stop_end = 10 + r;
        SET stop_mid1 = 20 + (2 * r) - 1;
        SET stop_mid2 = 20 + (2 * r);

        SET route_label = (SELECT stop_name FROM stops WHERE stop_id = stop_end);

        INSERT INTO trips (route_id, direction, trip_headsign) VALUES (r, 0, route_label);
        SET trip_id_val = LAST_INSERT_ID();

        INSERT INTO stop_times (trip_id, stop_id, arrival_time, stop_sequence) VALUES
        (trip_id_val, stop_start, MAKETIME(h, 00, 00), 1),
        (trip_id_val, stop_mid1, MAKETIME(h, 10, 00), 2),
        (trip_id_val, stop_mid2, MAKETIME(h, 20, 00), 3),
        (trip_id_val, stop_end, MAKETIME(h, 30, 00), 4);

        INSERT INTO trips (route_id, direction, trip_headsign) VALUES (r, 1, 'Trafford Interchange');
        SET trip_id_val = LAST_INSERT_ID();

        INSERT INTO stop_times (trip_id, stop_id, arrival_time, stop_sequence) VALUES
        (trip_id_val, stop_end, MAKETIME(h, 35, 00), 1),
        (trip_id_val, stop_mid2, MAKETIME(h, 45, 00), 2),
        (trip_id_val, stop_mid1, MAKETIME(h, 55, 00), 3),
        (trip_id_val, stop_start, ADDTIME(MAKETIME(h, 00, 00), '01:05:00'), 4);

      END IF;

      SET r = r + 1;
    END WHILE;

    SET h = h + 1;
  END WHILE;
END $$

DELIMITER ;

CALL GenerateSchedule();

DROP PROCEDURE GenerateSchedule();
