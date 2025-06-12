<?php
include('../../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /erp_project/modules/purchase_orders/view_pos.php');
    exit();
}

// Validation
if (empty($_POST['po_id']) || empty($_POST['delivery_date'])) {
    die("Missing required data.");
}

$po_id = $_POST['po_id'];
$delivery_date = $_POST['delivery_date'];
$notes = $_POST['notes'] ?? NULL;
$po_item_ids = $_POST['po_item_id'];
$quantities_received = $_POST['quantity_received'];
$grn_file_path = NULL; // Default to NULL

// --- NEW: File Upload Handling ---
if (isset($_FILES['grn_file']) && $_FILES['grn_file']['error'] == 0) {
    $allowed_types = ['application/pdf', 'image/jpeg', 'image/png'];
    $max_size = 5 * 1024 * 1024; // 5 MB

    if (in_array($_FILES['grn_file']['type'], $allowed_types) && $_FILES['grn_file']['size'] <= $max_size) {
        $file_extension = pathinfo($_FILES['grn_file']['name'], PATHINFO_EXTENSION);
        $unique_name = 'grn_' . $po_id . '_' . time() . '.' . $file_extension;
        $upload_dir = '../../uploads/grn/';
        
        if (move_uploaded_file($_FILES['grn_file']['tmp_name'], $upload_dir . $unique_name)) {
            $grn_file_path = 'uploads/grn/' . $unique_name;
        }
    }
}
// --- End of File Upload Handling ---


$conn = connect_db();
$conn->begin_transaction();

try {
    // 1. Create the main delivery record, now including the file path
    $sql_delivery = "INSERT INTO deliveries (po_id, delivery_date, notes, grn_file_path) VALUES (?, ?, ?, ?)";
    $stmt_delivery = $conn->prepare($sql_delivery);
    $stmt_delivery->bind_param("isss", $po_id, $delivery_date, $notes, $grn_file_path);
    $stmt_delivery->execute();
    $delivery_id = $conn->insert_id;

    // The rest of the script is unchanged...
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
    
    // Update PO status logic... (unchanged)
    $sql_check_status = "SELECT SUM(pi.quantity) as total_ordered, (SELECT SUM(di.quantity_received) FROM delivery_items di JOIN po_items pi_sub ON di.po_item_id = pi_sub.id WHERE pi_sub.po_id = ?) as total_received FROM po_items pi WHERE pi.po_id = ?";
    $stmt_check = $conn->prepare($sql_check_status);
    $stmt_check->bind_param("ii", $po_id, $po_id);
    $stmt_check->execute();
    $result_status = $stmt_check->get_result()->fetch_assoc();
    $new_po_status = 'Partially Delivered';
    if ($result_status['total_received'] >= $result_status['total_ordered']) {
        $new_po_status = 'Completed';
    }
    $sql_update_po = "UPDATE purchase_orders SET status = ? WHERE id = ?";
    $stmt_update_po = $conn->prepare($sql_update_po);
    $stmt_update_po->bind_param("si", $new_po_status, $po_id);
    $stmt_update_po->execute();
    
    $conn->commit();
    header("Location: /erp_project/modules/purchase_orders/view_po_details.php?id=" . $po_id . "&status=delivery_recorded");

} catch (Exception $e) {
    $conn->rollback();
    header("Location: /erp_project/modules/deliveries/record_delivery.php?po_id=" . $po_id . "&status=error");
}

$conn->close();
exit();
?>