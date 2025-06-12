<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');

if (!has_permission('supplier_info_approve')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['change_id'], $_POST['action'])) {
    header('Location: review_supplier_changes.php');
    exit();
}

$change_id = $_POST['change_id'];
$action = $_POST['action'];
$current_user_id = $_SESSION['user_id'];
$reviewed_at = date('Y-m-d H:i:s');

$conn = connect_db();
$conn->begin_transaction();

try {
    if ($action == 'approve') {
        // 1. Get the pending change data
        $sql_get = "SELECT supplier_id, change_data FROM supplier_info_changes WHERE id = ? AND status = 'Pending'";
        $stmt_get = $conn->prepare($sql_get);
        $stmt_get->bind_param("i", $change_id);
        $stmt_get->execute();
        $result = $stmt_get->get_result();
        if ($result->num_rows === 0) { throw new Exception("Change request not found or already processed."); }
        $change = $result->fetch_assoc();
        
        $supplier_id = $change['supplier_id'];
        $change_data = json_decode($change['change_data'], true);

        // 2. Build and execute the UPDATE query for the main suppliers table
        $sql_update = "UPDATE suppliers SET ";
        $update_parts = [];
        $update_params = [];
        $types = '';
        foreach ($change_data as $field => $value) {
            $allowed_fields = ['bank_name', 'bank_account_number', 'bank_branch_code'];
            if (in_array($field, $allowed_fields)) {
                $update_parts[] = "$field = ?";
                $types .= 's';
                $update_params[] = $value;
            }
        }

        if (!empty($update_parts)) {
            $sql_update .= implode(', ', $update_parts);
            $sql_update .= " WHERE id = ?";
            $types .= 'i';
            $update_params[] = $supplier_id;
            
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param($types, ...$update_params);
            $stmt_update->execute();
        }

        // 3. Update the status of the change request to 'Approved'
        $sql_status = "UPDATE supplier_info_changes SET status = 'Approved', reviewed_by_user_id = ?, reviewed_at = ? WHERE id = ?";
        $stmt_status = $conn->prepare($sql_status);
        $stmt_status->bind_param("isi", $current_user_id, $reviewed_at, $change_id);
        $stmt_status->execute();
        
        log_audit_trail($conn, "Approved supplier info change", 'Supplier', $supplier_id);
        $conn->commit();
        header("Location: review_supplier_changes.php?status=approved");

    } elseif ($action == 'reject') {
        // If rejected, just update the status
        $sql_status = "UPDATE supplier_info_changes SET status = 'Rejected', reviewed_by_user_id = ?, reviewed_at = ? WHERE id = ?";
        $stmt_status = $conn->prepare($sql_status);
        $stmt_status->bind_param("isi", $current_user_id, $reviewed_at, $change_id);
        $stmt_status->execute();

        log_audit_trail($conn, "Rejected supplier info change", 'Supplier Change Request', $change_id);
        $conn->commit();
        header("Location: review_supplier_changes.php?status=rejected");
    }

} catch (Exception $e) {
    $conn->rollback();
    error_log($e->getMessage());
    header("Location: review_supplier_changes.php?status=error");
}

$conn->close();
exit();
?>