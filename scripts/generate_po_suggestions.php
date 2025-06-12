<?php
// This script is designed to be run automatically by a server on a schedule (a "cron job").
// It finds products below their reorder point and creates draft POs.

require_once dirname(__DIR__) . '/includes/db.php';

echo "Running Automated PO Suggestion Script at " . date('Y-m-d H:i:s') . "\n";

$conn = connect_db();

// --- Find products that need reordering ---
$sql_products_to_order = "
    SELECT id, product_name, reorder_point, quantity_in_stock, price
    FROM products
    WHERE reorder_point IS NOT NULL 
    AND quantity_in_stock <= reorder_point
    AND id NOT IN (
        SELECT poi.product_id
        FROM po_items poi
        JOIN purchase_orders po ON poi.po_id = po.id
        WHERE po.status IN ('Pending', 'Approved', 'Partially Delivered', 'Draft')
    )
";
$products_result = $conn->query($sql_products_to_order);

if ($products_result->num_rows === 0) {
    echo "No products to reorder at this time.\n";
    exit();
}

$products_to_reorder = $products_result->fetch_all(MYSQLI_ASSOC);
$created_pos = 0;
$skipped_products = [];

// Prepare statements outside the loop
$sql_po = "INSERT INTO purchase_orders (po_number, supplier_id, order_date, total_amount, status) VALUES (?, ?, ?, ?, ?)";
$stmt_po = $conn->prepare($sql_po);

$sql_item = "INSERT INTO po_items (po_id, product_id, quantity, unit_price, total_price) VALUES (?, ?, ?, ?, ?)";
$stmt_item = $conn->prepare($sql_item);

$sql_supplier = "SELECT supplier_id FROM supplier_products WHERE product_id = ? LIMIT 1";
$stmt_supplier = $conn->prepare($sql_supplier);

$conn->begin_transaction();
try {
    foreach ($products_to_reorder as $product) {
        // --- NEW LOGIC: Find a valid supplier for this product ---
        $stmt_supplier->bind_param("i", $product['id']);
        $stmt_supplier->execute();
        $supplier_result = $stmt_supplier->get_result();

        if ($supplier_result->num_rows === 0) {
            // If no supplier is linked to this product, skip it and log the name
            $skipped_products[] = $product['product_name'];
            continue; // Move to the next product
        }
        $valid_supplier_id = $supplier_result->fetch_assoc()['supplier_id'];

        // Assume a default reorder quantity (e.g., 50 or based on a new field)
        $reorder_quantity = 50; 
        $total_amount = $reorder_quantity * $product['price'];
        
        // --- Create a new Purchase Order with 'Draft' status ---
        $po_number = 'DRAFT-' . time() . '-' . $product['id'];
        $order_date = date('Y-m-d');
        $status = 'Draft';

        $stmt_po->bind_param("sisds", $po_number, $valid_supplier_id, $order_date, $total_amount, $status);
        $stmt_po->execute();
        $po_id = $conn->insert_id;

        // --- Add the line item to the new PO ---
        $stmt_item->bind_param("iiidd", $po_id, $product['id'], $reorder_quantity, $product['price'], $total_amount);
        $stmt_item->execute();
        
        echo "Created Draft PO #{$po_number} for product '{$product['product_name']}'.\n";
        $created_pos++;
    }

    $conn->commit();
    echo "Successfully created {$created_pos} new draft POs.\n";
    if (!empty($skipped_products)) {
        echo "Skipped " . count($skipped_products) . " products because they are not linked to any supplier: " . implode(', ', $skipped_products) . "\n";
    }

} catch (Exception $e) {
    $conn->rollback();
    echo "An error occurred: " . $e->getMessage() . "\n";
}

$conn->close();
?>