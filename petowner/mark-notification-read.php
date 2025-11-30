<?php
require '../db/database.php';
require '../session.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notification_id'])) {
    $notification_id = (int)$_POST['notification_id'];
    
    // Verify the notification belongs to the logged-in user
    $username = $_SESSION['Username'];
    $queryUserId = "SELECT UserId FROM useraccounts WHERE Username = ?";
    $stmtUserId = mysqli_prepare($connection, $queryUserId);
    mysqli_stmt_bind_param($stmtUserId, "s", $username);
    mysqli_stmt_execute($stmtUserId);
    $resultUserId = mysqli_stmt_get_result($stmtUserId);
    
    if ($row = mysqli_fetch_assoc($resultUserId)) {
        $userId = $row['UserId'];
        
        // Mark notification as read
        $updateSql = "UPDATE notifications SET Is_Read = 1 WHERE Notification_id = ? AND UserId = ?";
        $stmt = mysqli_prepare($connection, $updateSql);
        mysqli_stmt_bind_param($stmt, "ii", $notification_id, $userId);
        
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to update notification']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'User not found']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}

mysqli_close($connection);
?>
