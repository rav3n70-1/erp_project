<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');

if (!has_permission('po_edit')) { header('Location: /erp_project/index.php?status=access_denied'); exit(); }

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['po_id'])) {
    header('Location: view_pos.php');
    exit();
}

$po_id = $_POST['po_id'];
// Determine the new status based on which button was clicked
$new_status = ($_POST['action'] == 'submit_approval') ? 'Pending' : 'Draft';

$conn = connect_db();
$conn->begin_transaction();
try {
    $grand_total = 0;
    foreach ($_POST['product_id'] as $key => $product_id) {
        if(empty($product_id)) continue;
        $grand_total += $_POST['quantity'][$key] * $_POST['unit_price'][$key];
    }

    $sql_update_po = "UPDATE purchase_orders SET supplier_id = ?, budget_id = ?, order_date = ?, total_amount = ?, status = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update_po);
    $budget_id = !empty($_POST['budget_id']) ? $_POST['budget_id'] : NULL;
    $stmt_update->bind_param("iisdsi", $_POST['supplier_id'], $budget_id, $_POST['order_date'], $grand_total, $new_status, $po_id);
    $stmt_update->execute();

    // Delete old items and then re-insert them to handle any changes
    $conn->query("DELETE FROM po_items WHERE po_id = $po_id");
    
    $sql_item = "INSERT INTO po_items (po_id, product_id, quantity, unit_price, total_price) VALUES (?, ?, ?, ?, ?)";
    $stmt_item = $conn->prepare($sql_item);
    foreach ($_POST['product_id'] as $key => $product_id) {
        if(empty($product_id)) continue;
        $total_price = $_POST['quantity'][$key] * $_POST['unit_price'][$key];
        $stmt_item->bind_param("iiidd", $po_id, $product_id, $_POST['quantity'][$key], $_POST['unit_price'][$key], $total_price);
        $stmt_item->execute();
    }

    // Send a notification ONLY if the PO was submitted for approval
    if ($new_status == 'Pending') {
        $po_number_sql = "SELECT po_number FROM purchase_orders WHERE id = ?";
        $stmt_po_num = $conn->prepare($po_number_sql);
        $stmt_po_num->bind_param("i", $po_id);
        $stmt_po_num->execute();
        $po_number = $stmt_po_num->get_result()->fetch_assoc()['po_number'];

        $sql_users = "SELECT DISTINCT u.id FROM users u LEFT JOIN role_permissions rp ON u.role_id = rp.role_id LEFT JOIN roles r ON u.role_id = r.id WHERE rp.permission_key = 'po_approve' OR r.role_name = 'System Admin'";
        $users_result = $conn->query($sql_users);
        
        if ($users_result && $users_result->num_rows > 0) {
            $notification_message = "Draft PO ".htmlspecialchars($po_number)." has been submitted for approval.";
            $notification_link = "/erp_project/modules/purchase_orders/view_po_details.php?id=" . $po_id;
            
            $sql_notification = "INSERT INTO notifications (user_id, message, link) VALUES (?, ?, ?)";
            $stmt_notification = $conn->prepare($sql_notification);

            while ($user = $users_result->fetch_assoc()) {
                $stmt_notification->bind_param("iss", $user['id'], $notification_message, $notification_link);
                $stmt_notification->execute();
            }
        }
    }
    
    log_audit_trail($conn, "Edited PO and set status to " . $new_status, 'Purchase Order', $po_id);
    $conn->commit();
    header("Location: view_pos.php?status=po_updated");
} catch (Exception $e) {
    $conn->rollback();
    error_log($e->getMessage());
    header("Location: edit_po.php?id=" . $po_id . "&status=error");
}
$conn->close();
exit();
?>