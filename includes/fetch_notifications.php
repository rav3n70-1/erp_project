<?php
// This script fetches unread notifications for the logged-in user and returns them as JSON.
require_once 'session_check.php';
require_once 'db.php';

$user_id = $_SESSION['user_id'];
$conn = connect_db();

// Query to get the 5 most recent unread notifications
$sql_notifications = "SELECT * FROM notifications 
                      WHERE user_id = ? AND is_read = 0 
                      ORDER BY created_at DESC 
                      LIMIT 5";
$stmt = $conn->prepare($sql_notifications);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$notifications = $result->fetch_all(MYSQLI_ASSOC);

// Query to get the total count of unread notifications
$sql_count = "SELECT COUNT(id) as unread_count FROM notifications WHERE user_id = ? AND is_read = 0";
$stmt_count = $conn->prepare($sql_count);
$stmt_count->bind_param("i", $user_id);
$stmt_count->execute();
$unread_count = $stmt_count->get_result()->fetch_assoc()['unread_count'];

// Combine the data into a single array
$response = [
    'notifications' => $notifications,
    'unread_count' => $unread_count
];

$conn->close();

// Set the content type to JSON and output the response
header('Content-Type: application/json');
echo json_encode($response);
?>