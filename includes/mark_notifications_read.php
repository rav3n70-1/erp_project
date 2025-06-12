<?php
// This script now handles marking all notifications as read OR a single one.
require_once 'session_check.php';
require_once 'db.php';

$user_id = $_SESSION['user_id'];
$conn = connect_db();

// Check if a specific notification ID was sent via POST
$data = json_decode(file_get_contents('php://input'), true);
$notification_id = $data['notification_id'] ?? null;

if ($notification_id && is_numeric($notification_id)) {
    // If a specific ID is provided, mark only that one as read
    $sql = "UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $notification_id, $user_id);
} else {
    // If no specific ID is provided, mark all as read (original behavior)
    $sql = "UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$conn->close();

// Return a success response
header('Content-Type: application/json');
echo json_encode(['status' => 'success']);
?>