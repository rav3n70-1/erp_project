<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');

if (!has_permission('po_edit')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['po_id'])) {
    header('Location: view_pos.php');
    exit();
}

$po_id = $_POST['po_id'];
$new_status = ($_POST['action'] == 'submit_approval') ? 'Pending' : 'Draft';

// --- Capture all data from the form ---
$supplier_id = $_POST['supplier_id'];
$order_date = $_POST['order_date'];
$budget_id = !empty($_POST['budget_id']) ? $_POST['budget_id'] : NULL;
// This is the new field we need to save
$expected_delivery_date = !empty($_POST['expected_delivery_date']) ? $_POST['expected_delivery_date'] : NULL;


$conn = connect_db();
$conn->begin_transaction();
try {
    $grand_total = 0;
    foreach ($_POST['product_id'] as $key => $product_id) {
        if(empty($product_id)) continue;
        $grand_total += $_POST['quantity'][$key] * $_POST['unit_price'][$key];
    }

    // --- THIS IS THE UPDATED SQL QUERY ---
    // It now includes the expected_delivery_date
    $sql_update_po = "UPDATE purchase_orders 
                      SET supplier_id = ?, budget_id = ?, order_date = ?, expected_delivery_date = ?, total_amount = ?, status = ? 
                      WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update_po);
    // Update the bind_param to include the new date string
    $stmt_update->bind_param("iissdsi", $supplier_id, $budget_id, $order_date, $expected_delivery_date, $grand_total, $new_status, $po_id);
    $stmt_update->execute();

    // Delete old items and insert new ones
    $conn->query("DELETE FROM po_items WHERE po_id = $po_id");
    
    $sql_item = "INSERT INTO po_items (po_id, product_id, quantity, unit_price, total_price) VALUES (?, ?, ?, ?, ?)";
    $stmt_item = $conn->prepare($sql_item);
    foreach ($_POST['product_id'] as $key => $product_id) {
        if(empty($product_id)) continue;
        $total_price = $_POST['quantity'][$key] * $_POST['unit_price'][$key];
        $stmt_item->bind_param("iiidd", $po_id, $product_id, $_POST['quantity'][$key], $_POST['unit_price'][$key], $total_price);
        $stmt_item->execute();
    }

    if ($new_status == 'Pending') {
        // (Notification logic is unchanged)
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