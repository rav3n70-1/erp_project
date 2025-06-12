<?php
// This script handles AJAX requests to update compliance status

include('../../includes/db.php');

// We expect data to be sent as JSON, so we decode the input
$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($data['supplier_id'], $data['checklist_id'], $data['status'])) {
    // Invalid request
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'Invalid input.']);
    exit();
}

$supplier_id = $data['supplier_id'];
$checklist_id = $data['checklist_id'];
$status = $data['status'];

$conn = connect_db();

// This SQL command is very efficient. It tries to INSERT a new row.
// If a row with the same UNIQUE key (supplier_id, checklist_id) already exists,
// it will UPDATE the existing row instead.
$sql = "INSERT INTO supplier_compliance_status (supplier_id, checklist_id, status)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE status = VALUES(status)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $supplier_id, $checklist_id, $status);

if ($stmt->execute()) {
    // Success
    http_response_code(200); // OK
    echo json_encode(['status' => 'success', 'message' => 'Compliance status updated.']);
} else {
    // Failure
    http_response_code(500); // Internal Server Error
    echo json_encode(['status' => 'error', 'message' => 'Database update failed.']);
}

$stmt->close();
$conn->close();
?>