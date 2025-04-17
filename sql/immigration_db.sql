-- Create the database
CREATE DATABASE IF NOT EXISTS immigration_db;
USE immigration_db;

-- Users table for authentication
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone VARCHAR(20),
    role ENUM('admin', 'client') DEFAULT 'client',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Visa types
CREATE TABLE IF NOT EXISTS visa_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    requirements TEXT,
    processing_time VARCHAR(100),
    image_path VARCHAR(255)
);

-- Success stories
CREATE TABLE IF NOT EXISTS success_stories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_name VARCHAR(100) NOT NULL,
    visa_type_id INT,
    story_content TEXT,
    image_path VARCHAR(255),
    publish_date DATE,
    FOREIGN KEY (visa_type_id) REFERENCES visa_types(id)
);

-- Appointment bookings
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    purpose VARCHAR(255) NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Document uploads
CREATE TABLE IF NOT EXISTS documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    document_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    document_type VARCHAR(100),
    FOREIGN KEY (user_id) REFERENCES users(id)
);