-- Tạo mới database LoginReg
CREATE DATABASE blood_donation;


USE blood_donation;

CREATE TABLE IF NOT EXISTS donors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(255),
    name VARCHAR(255),
    blood_type VARCHAR(10),
    phone_number VARCHAR(20),
    status VARCHAR(20)
);
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);