<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');

if (!has_permission('po_create')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: create_po.php');
    exit();
}

if (empty($_POST['supplier_id']) || empty($_POST['order_date']) || empty($_POST['product_id'])) {
    header('Location: create_po.php?status=error&msg=missing_fields');
    exit();
}

$po_number = $_POST['po_number'];
$supplier_id = $_POST['supplier_id'];
$order_date = $_POST['order_date'];
$budget_id = !empty($_POST['budget_id']) ? $_POST['budget_id'] : NULL;

$conn = connect_db();
$conn->begin_transaction();
try {
    $grand_total = 0;
    foreach ($_POST['product_id'] as $key => $product_id) {
        if (empty($product_id)) continue; 
        $quantity = is_numeric($_POST['quantity'][$key]) ? $_POST['quantity'][$key] : 0;
        $unit_price = is_numeric($_POST['unit_price'][$key]) ? $_POST['unit_price'][$key] : 0;
        $grand_total += $quantity * $unit_price;
    }

    if ($budget_id) {
        $sql_budget = "SELECT allocated_amount, (SELECT COALESCE(SUM(total_amount), 0) FROM purchase_orders WHERE budget_id = ? AND status IN ('Approved', 'Partially Delivered', 'Completed')) as spent_amount FROM budgets WHERE id = ?";
        $stmt_budget = $conn->prepare($sql_budget);
        $stmt_budget->bind_param("ii", $budget_id, $budget_id);
        $stmt_budget->execute();
        $budget = $stmt_budget->get_result()->fetch_assoc();
        $remaining_budget = $budget['allocated_amount'] - $budget['spent_amount'];
        if ($grand_total > $remaining_budget) {
            $conn->rollback();
            header("Location: create_po.php?status=error&msg=budget_exceeded");
            exit();
        }
    }

    $sql_po = "INSERT INTO purchase_orders (po_number, supplier_id, budget_id, order_date, total_amount) VALUES (?, ?, ?, ?, ?)";
    $stmt_po = $conn->prepare($sql_po);
    $stmt_po->bind_param("siisd", $po_number, $supplier_id, $budget_id, $order_date, $grand_total);
    $stmt_po->execute();
    
    $po_id = $conn->insert_id;
    
    $sql_item = "INSERT INTO po_items (po_id, product_id, quantity, unit_price, total_price) VALUES (?, ?, ?, ?, ?)";
    $stmt_item = $conn->prepare($sql_item);
    foreach ($_POST['product_id'] as $key => $product_id) {
        if (empty($product_id)) continue; 
        $quantity = $_POST['quantity'][$key];
        $unit_price = $_POST['unit_price'][$key];
        $total_price = $quantity * $unit_price;
        $stmt_item->bind_param("iiidd", $po_id, $product_id, $quantity, $unit_price, $total_price);
        $stmt_item->execute();
    }
    
    // CORRECTED NOTIFICATION LOGIC
    $sql_users = "SELECT DISTINCT u.id 
                  FROM users u 
                  LEFT JOIN role_permissions rp ON u.role_id = rp.role_id
                  LEFT JOIN roles r ON u.role_id = r.id
                  WHERE rp.permission_key = 'po_approve' OR r.role_name = 'System Admin'";
    $users_result = $conn->query($sql_users);
    
    if ($users_result && $users_result->num_rows > 0) {
        $notification_message = "New PO ".htmlspecialchars($po_number)." requires approval.";
        $notification_link = "/erp_project/modules/purchase_orders/view_po_details.php?id=" . $po_id;
        
        $sql_notification = "INSERT INTO notifications (user_id, message, link) VALUES (?, ?, ?)";
        $stmt_notification = $conn->prepare($sql_notification);
        while ($user = $users_result->fetch_assoc()) {
            $stmt_notification->bind_param("iss", $user['id'], $notification_message, $notification_link);
            $stmt_notification->execute();
        }
    }
    
    $conn->commit();
    header("Location: view_pos.php?status=success");

} catch (Exception $e) {
    $conn->rollback();
    error_log($e->getMessage());
    header("Location: create_po.php?status=error&msg=db_error");
}

$conn->close();
exit();
?>