-- GlitterTails Service Pricing System Update
-- Run this SQL to add Dog_Size and Price columns to your database

USE appointment_db;

-- Add Dog_Size column to appointments table

ALTER TABLE appointments 
ADD COLUMN Price DECIMAL(10,2) AFTER Dog_Size;

-- Add Dog_Size column to history table
ALTER TABLE history 
ADD COLUMN Dog_Size VARCHAR(255) AFTER Service_Type;

-- Add Price column to history table
ALTER TABLE history 
ADD COLUMN Price DECIMAL(10,2) AFTER Dog_Size;

-- Display success message
SELECT 'Database updated successfully! Dog_Size and Price columns added.' AS Status;
