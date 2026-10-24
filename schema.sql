CREATE DATABASE IF NOT EXISTS coaching_db;
USE coaching_db;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    is_active TINYINT(1) DEFAULT 1
);

CREATE TABLE time_slots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slot_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    capacity INT NOT NULL
);

CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    slot_id INT NOT NULL,
    note TEXT,
    status ENUM('Pending', 'Confirmed', 'Rejected', 'Cancelled') DEFAULT 'Pending',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (slot_id) REFERENCES time_slots(id) ON DELETE RESTRICT
);

-- Sample future coaching slots
INSERT INTO time_slots (slot_date, start_time, end_time, capacity) VALUES
(DATE_ADD(CURDATE(), INTERVAL 1 DAY), '10:00:00', '11:00:00', 3),
(DATE_ADD(CURDATE(), INTERVAL 1 DAY), '14:00:00', '15:00:00', 2),
(DATE_ADD(CURDATE(), INTERVAL 2 DAY), '11:00:00', '12:00:00', 1);