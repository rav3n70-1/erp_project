<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');

if (!has_permission('delivery_status_update')) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Permission Denied.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($data['delivery_id'], $data['new_status'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid input.']);
    exit();
}

$delivery_id = $data['delivery_id'];
$new_status = $data['new_status'];
$allowed_statuses = ['Shipped', 'In Transit', 'Delivered', 'Delayed'];

if (!in_array($new_status, $allowed_statuses)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid status value.']);
    exit();
}

$conn = connect_db();
$sql = "UPDATE deliveries SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $new_status, $delivery_id);

if ($stmt->execute()) {
    log_audit_trail($conn, "Updated delivery status to " . $new_status, 'Delivery', $delivery_id);
    http_response_code(200);
    echo json_encode(['status' => 'success']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database update failed.']);
}

$stmt->close();
$conn->close();
?>
        $rating_quality = !empty($_POST['rating_quality']) ? $_POST['rating_quality'] : NULL;
        $rating_communication = !empty($_POST['rating_communication']) ? $_POST['rating_communication'] : NULL;
        
        $sql_ratings = "UPDATE supplier_ratings SET delivery_time = ?, quality = ?, communication = ? WHERE supplier_id = ?";
        $stmt_ratings = $conn->prepare($sql_ratings);
        $stmt_ratings->bind_param("iiii", $rating_delivery_time, $rating_quality, $rating_communication, $supplier_id);
        $stmt_ratings->execute();
    }
    
    // --- COMMIT TRANSACTION ---
    $conn->commit();
    header('Location: view_supplier_details.php?id=' . $supplier_id . '&status=success');
} catch (Exception $e) {
    $conn->rollback();
    header('Location: view_supplier_details.php?id=' . $supplier_id . '&status=error');
}   