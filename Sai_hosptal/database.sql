-- Create Database
CREATE DATABASE IF NOT EXISTS sai_hospital_db;
USE sai_hospital_db;
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
(1, 'Sai Hospital', 'admin', 'password123', 'Sai', 'Main Road, Sai');

INSERT IGNORE INTO blood_inventory (hospital_id, blood_group, units_available) VALUES
(1, 'A+', 10), (1, 'B+', 5), (1, 'O+', 2), (1, 'AB+', 8);

INSERT IGNORE INTO donors (id, name, mobile, password, city, blood_group, is_available, last_donation_date) VALUES
(1, 'Amit Sharma', '9876543210', 'donor123', 'Sai', 'O+', 1, '2023-01-01'),
(2, 'Rohit Singh', '9876543211', 'donor123', 'Sai', 'A+', 1, '2022-12-15'),
(3, 'Priya Patel', '9876543212', 'donor123', 'Sai', 'O+', 0, '2026-03-01'); -- Recently donated


