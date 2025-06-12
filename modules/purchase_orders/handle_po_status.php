<?php
echo "<strong>DEBUG STEP 1:</strong> Script started.<br>";

// We need to include the database connection and session first
include('../../includes/db.php');
echo "<strong>DEBUG STEP 2:</strong> db.php included.<br>";

include('../../includes/session_check.php');
echo "<strong>DEBUG STEP 3:</strong> session_check.php included.<br>";

// THIS IS THE CRUCIAL LINE that makes the functions available
include('../../includes/permissions.php');
echo "<strong>DEBUG STEP 4:</strong> permissions.php included.<br>";


// This check will now tell us definitively if the function exists
if (function_exists('log_audit_trail')) {
    echo "<strong>DEBUG STEP 5 (SUCCESS):</strong> The function log_audit_trail() was found!<br>";
} else {
    // If you see this message, the problem is that the permissions.php file is empty or incorrect.
    die("<strong>DEBUG STEP 5 (FAILURE):</strong> The function log_audit_trail() DOES NOT EXIST. Please check the contents of your includes/permissions.php file.");
}


if (!has_permission('po_approve')) {
    // This is not the error, but we leave the check in.
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: view_pos.php');
    exit();
}

// ... (The rest of the script is the same)
if (!isset($_POST['po_id']) || !is_numeric($_POST['po_id']) || !isset($_POST['new_status'])) {
    header('Location: view_pos.php?status=error');
    exit();
}
$po_id = $_POST['po_id'];
$new_status = $_POST['new_status'];
$allowed_statuses = ['Approved', 'Rejected'];
if (!in_array($new_status, $allowed_statuses)) {
    header("Location: view_po_details.php?id=" . $po_id . "&status=invalid_status");
    exit();
}
$conn = connect_db();
$sql = "UPDATE purchase_orders SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $new_status, $po_id);
if ($stmt->execute()) {
    $action_description = "PO #" . $po_id . " status changed to " . $new_status;
    log_audit_trail($conn, $action_description, 'Purchase Order', $po_id);
    header("Location: view_po_details.php?id=" . $po_id . "&status_updated=true");
} else {
    header("Location: view_po_details.php?id=" . $po_id . "&status=error");
}
$stmt->close();
$conn->close();
exit();
?>