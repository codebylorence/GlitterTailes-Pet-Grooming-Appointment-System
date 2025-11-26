-- GlitterTails Database Setup
-- Run this in MySQL Workbench to create the database and tables

CREATE DATABASE IF NOT EXISTS appointment_db;
USE appointment_db;

-- Admin table for admin login
CREATE TABLE IF NOT EXISTS admin (
    AdminId INT AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(100) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- User accounts table for pet owners
CREATE TABLE IF NOT EXISTS useraccounts (
    UserId INT AUTO_INCREMENT PRIMARY KEY,
    Firstname VARCHAR(100) NOT NULL,
    Lastname VARCHAR(100) NOT NULL,
    Username VARCHAR(100) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin account (username: admin, password: admin123)
INSERT INTO admin (Username, Password) VALUES ('admin', 'admin123');

-- Appointments table for current/upcoming appointments
CREATE TABLE IF NOT EXISTS appointments (
    Appointment_id INT AUTO_INCREMENT PRIMARY KEY,
    UserId INT NOT NULL,
    Email_Address VARCHAR(255) NOT NULL,
    Phone_Number VARCHAR(20) NOT NULL,
    Address TEXT NOT NULL,
    Pet_Name VARCHAR(100) NOT NULL,
    Pet_Breed VARCHAR(100) NOT NULL,
    Pet_Age VARCHAR(50) NOT NULL,
    Date DATE NOT NULL,
    Time VARCHAR(20) NOT NULL,
    Service_Type VARCHAR(100) NOT NULL,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (UserId) REFERENCES useraccounts(UserId) ON DELETE CASCADE
);

-- History table for past appointments
CREATE TABLE IF NOT EXISTS history (
    History_id INT AUTO_INCREMENT PRIMARY KEY,
    UserId INT NOT NULL,
    Email_Address VARCHAR(255) NOT NULL,
    Phone_Number VARCHAR(20) NOT NULL,
    Address TEXT NOT NULL,
    Pet_Name VARCHAR(100) NOT NULL,
    Pet_Breed VARCHAR(100) NOT NULL,
    Pet_Age VARCHAR(50) NOT NULL,
    Date DATE NOT NULL,
    Time VARCHAR(20) NOT NULL,
    Service_Type VARCHAR(100) NOT NULL,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (UserId) REFERENCES useraccounts(UserId) ON DELETE CASCADE
);

-- Optional: Insert a test user account
-- Password is hashed version of 'password123'
INSERT INTO useraccounts (Firstname, Lastname, Username, Password) 
VALUES ('Test', 'User', 'testuser', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
