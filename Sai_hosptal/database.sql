-- Create Database
CREATE DATABASE IF NOT EXISTS sai_hospital_db;
USE sai_hospital_db;

-- 1. Hospitals Table (Replacing Admin logic with general hospitals logic)
CREATE TABLE IF NOT EXISTS hospitals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Donors Table (Smart features added)
CREATE TABLE IF NOT EXISTS donors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    mobile VARCHAR(20) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    blood_group VARCHAR(5) NOT NULL,
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    is_available TINYINT(1) DEFAULT 1,
    last_donation_date DATE NULL,
    last_active_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Blood Requests Table
CREATE TABLE IF NOT EXISTS blood_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hospital_id INT,
    blood_group VARCHAR(5) NOT NULL,
    city VARCHAR(100) NOT NULL,
    urgency_level ENUM('High', 'Medium', 'Low') DEFAULT 'Medium',
    status ENUM('Pending', 'Completed') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (hospital_id) REFERENCES hospitals(id) ON DELETE CASCADE
);

-- 4. Blood Inventory Tracking
CREATE TABLE IF NOT EXISTS blood_inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hospital_id INT,
    blood_group VARCHAR(5) NOT NULL,
    units_available INT DEFAULT 0,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (hospital_id) REFERENCES hospitals(id) ON DELETE CASCADE,
    UNIQUE KEY (hospital_id, blood_group)
);

-- Indexing for fast Search
CREATE INDEX idx_blood_group_city ON donors(blood_group, city);
CREATE INDEX idx_is_available ON donors(is_available);

-- Insert Mock Data
INSERT IGNORE INTO hospitals (id, name, username, password, city, address) VALUES
(1, 'Sai Hospital', 'admin', 'password123', 'Sai', 'Main Road, Sai'),
(2, 'City Care Hospital', 'cityadmin', 'password123', 'Sai', 'South Street, Sai');

INSERT IGNORE INTO blood_inventory (hospital_id, blood_group, units_available) VALUES
(1, 'A+', 15), (1, 'B+', 8), (1, 'O+', 4), (1, 'AB+', 10),
(2, 'A-', 2), (2, 'O-', 1), (2, 'O+', 12);

INSERT IGNORE INTO donors (id, name, mobile, password, city, blood_group, latitude, longitude, is_available, last_donation_date) VALUES
(1, 'Amit Sharma', '9876543210', 'donor123', 'Sai', 'O+', 24.5310, 81.3010, 1, '2023-01-01'), -- Extremely close (0.1km)
(2, 'Rohit Singh', '9876543211', 'donor123', 'Sai', 'A+', 24.5500, 81.3200, 1, '2022-12-15'), -- Further away (3km)
(3, 'Priya Patel', '9876543212', 'donor123', 'Sai', 'O+', 24.5305, 81.3005, 0, '2026-03-01'), -- Blocked (Recently donated)
(4, 'Vikram Verma', '9876543213', 'donor123', 'Sai', 'O+', 24.5800, 81.3500, 1, '2021-05-20'), -- Far away (7km)
(5, 'Neha Gupta', '9876543214', 'donor123', 'Sai', 'B+', 24.5350, 81.3050, 1, NULL),       -- Very close
(6, 'Rahul Dev', '9876543215', 'donor123', 'Sai', 'O+', 24.6300, 81.4000, 1, '2023-11-10'); -- Farthest match (14km)
