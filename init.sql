CREATE TABLE IF NOT EXISTS types (
  type_id INT AUTO_INCREMENT PRIMARY KEY,
  type_name VARCHAR(50) NOT NULL
);

INSERT INTO types (type_name) VALUES 
('admin'),
('teacher'),
('guardian'),
('pupil');

CREATE TABLE IF NOT EXISTS users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  type_id INT,
  FOREIGN KEY (type_id) REFERENCES types(type_id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS classes (
  class_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL,
  capacity INT NOT NULL
);

INSERT INTO classes (name, capacity) VALUES
('Reception', 5),
('Year One', 5),
('Year Two', 5),
('Year Three', 5),
('Year Four', 5),
('Year Five', 5),
('Year Six', 5);

CREATE TABLE IF NOT EXISTS job_type (
  job_id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  annual_salary DECIMAL(10, 2) NOT NULL
);

INSERT INTO job_type (name, annual_salary) VALUES
('Trainee Teacher', 25000.00),
('Teacher', 35000.00),
('Senior Teacher', 40000.00);

CREATE TABLE IF NOT EXISTS teachers (
  teacher_id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  address TEXT NOT NULL,
  email VARCHAR(100) NOT NULL,
  phone_number VARCHAR(20) NOT NULL,
  background_check BOOLEAN DEFAULT FALSE NOT NULL,
  class_id INT,
  FOREIGN KEY (class_id) REFERENCES classes(class_id) ON DELETE SET NULL,
  user_id INT,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
  job_id INT,
  FOREIGN KEY (job_id) REFERENCES job_type(job_id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS pupils (
  pupil_id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  address TEXT NOT NULL,
  birthday DATE NOT NULL,
  medical_info TEXT,
  class_id INT,
  FOREIGN KEY (class_id) REFERENCES classes(class_id) ON DELETE SET NULL,
  user_id INT,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS guardians (
  guardian_id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  address TEXT NOT NULL,
  email VARCHAR(100) NOT NULL,
  phone_number VARCHAR(20) NOT NULL,
  user_id INT,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS guardian_pupil (
  pupil_id INT,
  guardian_id INT,
  PRIMARY KEY (pupil_id, guardian_id),
  FOREIGN KEY (pupil_id) REFERENCES pupils(pupil_id) ON DELETE CASCADE,
  FOREIGN KEY (guardian_id) REFERENCES guardians(guardian_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS notices (
  notice_id INT AUTO_INCREMENT PRIMARY KEY,
  description TEXT NOT NULL,
  notice_date DATE NOT NULL,
  title VARCHAR(20)
);

CREATE TABLE IF NOT EXISTS behavioral_notices (
  bnotice_id INT AUTO_INCREMENT PRIMARY KEY,
  description TEXT NOT NULL,
  notice_date DATE NOT NULL,
  title VARCHAR(20),
  pupil_id INT,
  FOREIGN KEY (pupil_id) REFERENCES pupils(pupil_id) ON DELETE CASCADE
);

INSERT INTO users (username, password_hash, type_id) VALUES 
('admin', '$2y$10$iMQJAMvOl51LtxI6.8lx1uxrri8fxUoa0lHqA9t6GLc8.VG2mrxpu', 1);

INSERT INTO users (username, password_hash, type_id) VALUES 
('scollins', '$2y$10$iMQJAMvOl51LtxI6.8lx1uxrri8fxUoa0lHqA9t6GLc8.VG2mrxpu', 2),
('dmwangi', '$2y$10$iMQJAMvOl51LtxI6.8lx1uxrri8fxUoa0lHqA9t6GLc8.VG2mrxpu', 2),
('eplatt', '$2y$10$iMQJAMvOl51LtxI6.8lx1uxrri8fxUoa0lHqA9t6GLc8.VG2mrxpu', 2),
('jschofield', '$2y$10$iMQJAMvOl51LtxI6.8lx1uxrri8fxUoa0lHqA9t6GLc8.VG2mrxpu', 2),
('fali', '$2y$10$iMQJAMvOl51LtxI6.8lx1uxrri8fxUoa0lHqA9t6GLc8.VG2mrxpu', 2),
('lgallagher', '$2y$10$iMQJAMvOl51LtxI6.8lx1uxrri8fxUoa0lHqA9t6GLc8.VG2mrxpu', 2),
('ghenderson', '$2y$10$iMQJAMvOl51LtxI6.8lx1uxrri8fxUoa0lHqA9t6GLc8.VG2mrxpu', 2);

INSERT INTO teachers (full_name, address, email, phone_number, background_check, class_id, user_id, job_id) VALUES
('Sarah Collins', '42 Beech Road, Chorlton, M21 9EL', 's.collins@st-alphonsus.sch.uk', '07700900101', TRUE, 1, (SELECT user_id FROM users WHERE username = 'scollins'), 1),
('David Mwangi', '12 Upper Chorlton Rd, Whalley Range, M16 7RN', 'd.mwangi@st-alphonsus.sch.uk', '07700900102', TRUE, 2, (SELECT user_id FROM users WHERE username = 'dmwangi'), 2),
('Emma Platt', '88 Washway Road, Sale, M33 7RF', 'e.platt@st-alphonsus.sch.uk', '07700900103', TRUE, 3, (SELECT user_id FROM users WHERE username = 'eplatt'), 1),
('James Schofield', '5 Barlow Moor Rd, Didsbury, M20 6TR', 'j.schofield@st-alphonsus.sch.uk', '07700900104', TRUE, 4, (SELECT user_id FROM users WHERE username = 'jschofield'), 2),
('Fatima Ali', '19 Kings Road, Old Trafford, M16 0GR', 'f.ali@st-alphonsus.sch.uk', '07700900105', TRUE, 5, (SELECT user_id FROM users WHERE username = 'fali'), 3),
('Liam Gallagher', '10 Burnage Lane, Burnage, M19 1ER', 'l.gallagher@st-alphonsus.sch.uk', '07700900106', TRUE, 6, (SELECT user_id FROM users WHERE username = 'lgallagher'), 2),
('Grace Henderson', 'Apartment 4B, Deansgate Square, M15 4UP', 'g.henderson@st-alphonsus.sch.uk', '07700900107', TRUE, 7, (SELECT user_id FROM users WHERE username = 'ghenderson'), 2);

INSERT INTO users (username, password_hash, type_id) VALUES 
('gjroberts', '$2y$10$iMQJAMvOl51LtxI6.8lx1uxrri8fxUoa0lHqA9t6GLc8.VG2mrxpu', 3),
('gikhan', '$2y$10$iMQJAMvOl51LtxI6.8lx1uxrri8fxUoa0lHqA9t6GLc8.VG2mrxpu', 3),
('gtadebayo', '$2y$10$iMQJAMvOl51LtxI6.8lx1uxrri8fxUoa0lHqA9t6GLc8.VG2mrxpu', 3);

INSERT INTO guardians (full_name, address, email, phone_number, user_id) VALUES
('Julie Roberts', '14 Ayres Road, Old Trafford, M16 9WA', 'julie.roberts@email.test', '07700111222', (SELECT user_id FROM users WHERE username = 'gjroberts')),
('Imran Khan', '22 Seymour Grove, Old Trafford, M16 0LH', 'i.khan@email.test', '07700111333', (SELECT user_id FROM users WHERE username = 'gikhan')),
('Tunde Adebayo', '56 Shrewsbury Street, Old Trafford, M16 9AP', 't.adebayo@email.test', '07700111444', (SELECT user_id FROM users WHERE username = 'gtadebayo'));

INSERT INTO users (username, password_hash, type_id) VALUES 
('aroberts19', '$2y$10$iMQJAMvOl51LtxI6.8lx1uxrri8fxUoa0lHqA9t6GLc8.VG2mrxpu', 4),
('akhan17', '$2y$10$iMQJAMvOl51LtxI6.8lx1uxrri8fxUoa0lHqA9t6GLc8.VG2mrxpu', 4),
('ladebayo15', '$2y$10$iMQJAMvOl51LtxI6.8lx1uxrri8fxUoa0lHqA9t6GLc8.VG2mrxpu', 4),
('zkhan13', '$2y$10$iMQJAMvOl51LtxI6.8lx1uxrri8fxUoa0lHqA9t6GLc8.VG2mrxpu', 4);

INSERT INTO pupils (full_name, address, birthday, medical_info, class_id, user_id) VALUES
('Alfie Roberts', '14 Ayres Road, Old Trafford, M16 9WA', '2019-05-12', 'Peanut allergy', 1, (SELECT user_id FROM users WHERE username = 'aroberts19')),
('Aisha Khan', '22 Seymour Grove, Old Trafford, M16 0LH', '2017-11-23', NULL, 3, (SELECT user_id FROM users WHERE username = 'akhan17')),
('Lucas Adebayo', '56 Shrewsbury Street, Old Trafford, M16 9AP', '2015-02-14', 'Mild asthma', 5, (SELECT user_id FROM users WHERE username = 'ladebayo15')),
('Zara Khan', '22 Seymour Grove, Old Trafford, M16 0LH', '2013-08-30', 'Requires glasses', 7, (SELECT user_id FROM users WHERE username = 'zkhan13'));

INSERT INTO guardian_pupil (pupil_id, guardian_id) VALUES
((SELECT pupil_id FROM pupils WHERE full_name = 'Alfie Roberts'), (SELECT guardian_id FROM guardians WHERE full_name = 'Julie Roberts')),
((SELECT pupil_id FROM pupils WHERE full_name = 'Aisha Khan'), (SELECT guardian_id FROM guardians WHERE full_name = 'Imran Khan')),
((SELECT pupil_id FROM pupils WHERE full_name = 'Zara Khan'), (SELECT guardian_id FROM guardians WHERE full_name = 'Imran Khan')),
((SELECT pupil_id FROM pupils WHERE full_name = 'Lucas Adebayo'), (SELECT guardian_id FROM guardians WHERE full_name = 'Tunde Adebayo'));

INSERT INTO notices (description, notice_date, title) VALUES
('This is a test notice, please ignore.', '1970-01-01', NULL),
('School is closed today due to bad weather.', '2026-12-12', 'Closure'),
('Christmas holidays begin!', '2026-12-19', NULL);
