-- Add status column to appointments table
ALTER TABLE appointments 
ADD COLUMN IF NOT EXISTS Status VARCHAR(20) DEFAULT 'Pending',
ADD COLUMN IF NOT EXISTS Approved_Date DATETIME NULL,
ADD COLUMN IF NOT EXISTS Rejection_Reason TEXT NULL;

-- Update existing appointments to 'Approved' status
UPDATE appointments SET Status = 'Approved' WHERE Status IS NULL OR Status = '';

-- The history table will store completed appointments
-- Make sure history table has a status column too
ALTER TABLE history 
ADD COLUMN IF NOT EXISTS Status VARCHAR(20) DEFAULT 'Completed';

-- Create notifications table for customer notifications
CREATE TABLE IF NOT EXISTS notifications (
    Notification_id INT AUTO_INCREMENT PRIMARY KEY,
    UserId INT NOT NULL,
    Appointment_id INT NULL,
    Message TEXT NOT NULL,
    Type VARCHAR(20) NOT NULL,
    Is_Read TINYINT(1) DEFAULT 0,
    Created_Date DATETIME NOT NULL,
    FOREIGN KEY (UserId) REFERENCES useraccounts(UserId) ON DELETE CASCADE,
    FOREIGN KEY (Appointment_id) REFERENCES appointments(Appointment_id) ON DELETE SET NULL
);
