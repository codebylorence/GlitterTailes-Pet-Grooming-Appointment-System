# Admin Notification System for Cancelled Appointments

## Overview
This feature notifies admins when customers cancel their appointments.

## Features Added

### 1. Admin Notifications Table
- New database table: `admin_notifications`
- Stores cancellation notifications for admin
- Tracks read/unread status

### 2. User Cancellation Flow
When a user cancels an appointment:
1. System captures appointment details (customer name, pet name, date, time, service)
2. Creates a notification message for admin
3. Stores notification in `admin_notifications` table
4. Deletes the appointment

### 3. Admin Dashboard Notifications
- Yellow notification banners appear at the top of the Appointments page
- Shows customer name, pet name, service type, and scheduled date/time
- Dismissible with an "×" button
- Notification badge on "Appointment" sidebar menu shows unread count

### 4. Notification Message Format
Example: "Customer John Doe cancelled appointment for Buddy (Full Grooming) scheduled on December 01, 2025 at 8:00 AM."

## Installation

### Step 1: Run Setup Script
Access in your browser:
```
http://localhost/GlitterTails/setup-service-pricing.php
```

This will:
- Add Dog_Size and Price columns (if not already added)
- Create admin_notifications table
- Set up all necessary database structures

### Step 2: Test the Feature
1. Login as a user
2. Create an appointment
3. Cancel the appointment
4. Login as admin
5. Go to Appointments page
6. You should see a yellow notification banner

## Database Schema

### admin_notifications Table
```sql
CREATE TABLE admin_notifications (
    Admin_Notification_id INT AUTO_INCREMENT PRIMARY KEY,
    Appointment_id INT NULL,
    Message TEXT NOT NULL,
    Type VARCHAR(20) NOT NULL,
    Is_Read TINYINT(1) DEFAULT 0,
    Created_Date DATETIME NOT NULL
);
```

## Files Modified

1. **petowner/user-bookings.php**
   - Added logic to create admin notification before deleting appointment
   - Captures appointment details for notification message

2. **admin/appointment.php**
   - Displays unread notifications at the top
   - Shows notification badge on sidebar
   - Added JavaScript to dismiss notifications

3. **admin/mark-admin-notification-read.php** (NEW)
   - Handles marking notifications as read
   - Returns JSON response

4. **setup-service-pricing.php**
   - Added admin_notifications table creation
   - Checks if table exists before creating

5. **db/create-admin-notifications.sql** (NEW)
   - SQL script for manual table creation if needed

## How It Works

### User Side:
1. User clicks "Cancel" button on their booking
2. Confirmation dialog appears
3. Upon confirmation:
   - Appointment details are captured
   - Admin notification is created
   - Appointment is deleted
   - Success message shown

### Admin Side:
1. Admin logs in and goes to Appointments page
2. Yellow notification banners appear for each cancellation
3. Notification shows full details of cancelled appointment
4. Admin can dismiss notification by clicking "×"
5. Dismissed notifications are marked as read
6. Sidebar shows count of unread notifications

## Notification Types
- **Type**: "cancelled"
- **Status**: Read (1) or Unread (0)
- **Display**: Yellow banner with warning icon (⚠️)

## Future Enhancements
- Email notifications to admin
- Notification history page
- Filter notifications by date
- Bulk mark as read
- Sound alerts for new notifications

## Troubleshooting

### Notifications not appearing?
1. Check if `admin_notifications` table exists
2. Run the setup script again
3. Check browser console for JavaScript errors

### Table doesn't exist error?
Run this SQL manually:
```sql
USE appointment_db;
CREATE TABLE admin_notifications (
    Admin_Notification_id INT AUTO_INCREMENT PRIMARY KEY,
    Appointment_id INT NULL,
    Message TEXT NOT NULL,
    Type VARCHAR(20) NOT NULL,
    Is_Read TINYINT(1) DEFAULT 0,
    Created_Date DATETIME NOT NULL
);
```

## Testing Checklist
- [ ] Setup script runs successfully
- [ ] User can cancel appointment
- [ ] Admin sees notification banner
- [ ] Notification shows correct details
- [ ] Dismiss button works
- [ ] Notification badge shows count
- [ ] Badge updates after dismissing
