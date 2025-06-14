<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');

if (!has_permission('asset_manage')) { // Inventory Officer permission
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /erp_project/modules/purchase_orders/view_pos.php');
    exit();
}

if (empty($_POST['po_id']) || empty($_POST['delivery_date'])) {
    die("Missing required data.");
}

$po_id = $_POST['po_id'];
$delivery_date = $_POST['delivery_date'];
$notes = $_POST['notes'] ?? NULL;
$po_item_ids = $_POST['po_item_id'];
$quantities_received = $_POST['quantity_received'];
$grn_file_path = NULL;

$conn = connect_db();
$conn->begin_transaction();

try {
    // File Upload Handling
    if (isset($_FILES['grn_file']) && $_FILES['grn_file']['error'] == 0) {
        $upload_dir = '../../uploads/grn/';
        if (!is_dir($upload_dir)) { mkdir($upload_dir, 0755, true); }
        $file_extension = pathinfo($_FILES['grn_file']['name'], PATHINFO_EXTENSION);
        $unique_name = 'grn_' . $po_id . '_' . time() . '.' . $file_extension;
        if (move_uploaded_file($_FILES['grn_file']['tmp_name'], $upload_dir . $unique_name)) {
            $grn_file_path = 'uploads/grn/' . $unique_name;
        }
    }

    // 1. Create the main delivery record. We set its default status to 'Delivered'.
    // The status on the *delivery* itself is managed on the view_deliveries.php page.
    $sql_delivery = "INSERT INTO deliveries (po_id, delivery_date, status, notes, grn_file_path) VALUES (?, ?, 'Delivered', ?, ?)";
    $stmt_delivery = $conn->prepare($sql_delivery);
    $stmt_delivery->bind_param("isss", $po_id, $delivery_date, $notes, $grn_file_path);
    $stmt_delivery->execute();
    $delivery_id = $conn->insert_id;

    // 2. Insert each received item and update stock levels
    $sql_item = "INSERT INTO delivery_items (delivery_id, po_item_id, quantity_received) VALUES (?, ?, ?)";
    $stmt_item = $conn->prepare($sql_item);
    $sql_stock = "UPDATE products SET quantity_in_stock = quantity_in_stock + ? WHERE id = (SELECT product_id FROM po_items WHERE id = ?)";
    $stmt_stock = $conn->prepare($sql_stock);
    $items_recorded = 0;
    foreach ($quantities_received as $key => $qty) {
        if (!empty($qty) && is_numeric($qty) && $qty > 0) {
            $po_item_id = $po_item_ids[$key];
            $stmt_item->bind_param("iii", $delivery_id, $po_item_id, $qty);
            $stmt_item->execute();
            $stmt_stock->bind_param("ii", $qty, $po_item_id);
            $stmt_stock->execute();
            $items_recorded++;
        }
    }
    
    if ($items_recorded == 0) {
        throw new Exception("No quantities were entered.");
    }
    
    // --- THIS IS THE CRUCIAL LOGIC ---
    // 3. After recording the delivery, we check the main PO's status.
    $sql_check_status = "SELECT 
                            (SELECT SUM(quantity) FROM po_items WHERE po_id = ?) as total_ordered, 
                            (SELECT SUM(quantity_received) FROM delivery_items di JOIN po_items pi ON di.po_item_id = pi.id WHERE pi.po_id = ?) as total_received";
    $stmt_check = $conn->prepare($sql_check_status);
    $stmt_check->bind_param("ii", $po_id, $po_id);
    $stmt_check->execute();
    $result_status = $stmt_check->get_result()->fetch_assoc();

    $new_po_status = 'Approved'; // Default if nothing has been received yet
    if ($result_status['total_received'] > 0) {
        if ($result_status['total_received'] >= $result_status['total_ordered']) {
            $new_po_status = 'Completed'; // If all items are received, mark as completed
        } else {
            $new_po_status = 'Partially Delivered'; // If some but not all are received
        }
    }

    $sql_update_po = "UPDATE purchase_orders SET status = ? WHERE id = ?";
    $stmt_update_po = $conn->prepare($sql_update_po);
    $stmt_update_po->bind_param("si", $new_po_status, $po_id);
    $stmt_update_po->execute();
    
    $conn->commit();
    header("Location: /erp_project/modules/purchase_orders/view_po_details.php?id=" . $po_id . "&status=delivery_recorded");

} catch (Exception $e) {
    $conn->rollback();
    error_log($e->getMessage());
    header("Location: /erp_project/modules/deliveries/record_delivery.php?po_id=" . $po_id . "&status=error");
}

$conn->close();
exit();
?>