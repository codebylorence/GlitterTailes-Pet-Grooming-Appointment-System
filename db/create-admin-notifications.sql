-- Create admin notifications table for cancelled appointments
USE appointment_db;

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
