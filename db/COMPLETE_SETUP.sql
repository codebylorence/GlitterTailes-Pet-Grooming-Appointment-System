-- ============================================
-- GlitterTails Complete Setup Script
-- Run this entire script in MySQL Workbench or phpMyAdmin
-- ============================================

USE appointment_db;

-- Step 1: Add Dog_Size column to appointments table (ignore error if exists)
SET @query1 = IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = 'appointment_db' 
     AND TABLE_NAME = 'appointments' 
     AND COLUMN_NAME = 'Dog_Size') = 0,
    'ALTER TABLE appointments ADD COLUMN Dog_Size VARCHAR(255) AFTER Service_Type',
    'SELECT "Dog_Size already exists in appointments" AS Info'
);
PREPARE stmt1 FROM @query1;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;

-- Step 2: Add Price column to appointments table (ignore error if exists)
SET @query2 = IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = 'appointment_db' 
     AND TABLE_NAME = 'appointments' 
     AND COLUMN_NAME = 'Price') = 0,
    'ALTER TABLE appointments ADD COLUMN Price DECIMAL(10,2) AFTER Dog_Size',
    'SELECT "Price already exists in appointments" AS Info'
);
PREPARE stmt2 FROM @query2;
EXECUTE stmt2;
DEALLOCATE PREPARE stmt2;

-- Step 3: Add Dog_Size column to history table (ignore error if exists)
SET @query3 = IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = 'appointment_db' 
     AND TABLE_NAME = 'history' 
     AND COLUMN_NAME = 'Dog_Size') = 0,
    'ALTER TABLE history ADD COLUMN Dog_Size VARCHAR(255) AFTER Service_Type',
    'SELECT "Dog_Size already exists in history" AS Info'
);
PREPARE stmt3 FROM @query3;
EXECUTE stmt3;
DEALLOCATE PREPARE stmt3;

-- Step 4: Add Price column to history table (ignore error if exists)
SET @query4 = IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = 'appointment_db' 
     AND TABLE_NAME = 'history' 
     AND COLUMN_NAME = 'Price') = 0,
    'ALTER TABLE history ADD COLUMN Price DECIMAL(10,2) AFTER Dog_Size',
    'SELECT "Price already exists in history" AS Info'
);
PREPARE stmt4 FROM @query4;
EXECUTE stmt4;
DEALLOCATE PREPARE stmt4;

-- Step 5: Create admin_notifications table
CREATE TABLE IF NOT EXISTS admin_notifications (
    Admin_Notification_id INT AUTO_INCREMENT PRIMARY KEY,
    Appointment_id INT NULL,
    Message TEXT NOT NULL,
    Type VARCHAR(20) NOT NULL,
    Is_Read TINYINT(1) DEFAULT 0,
    Created_Date DATETIME NOT NULL,
    INDEX idx_is_read (Is_Read),
    INDEX idx_created_date (Created_Date)
);

-- Display success message
SELECT 'Setup completed successfully!' AS Status;

-- Show table structures to verify
SELECT 'Appointments table columns:' AS Info;
SHOW COLUMNS FROM appointments;

SELECT 'History table columns:' AS Info;
SHOW COLUMNS FROM history;

SELECT 'Admin notifications table columns:' AS Info;
SHOW COLUMNS FROM admin_notifications;
