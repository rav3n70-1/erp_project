<?php
// This script is designed to be run automatically by a server on a schedule (a "cron job").
// It calculates the on-time delivery rate for all suppliers.

require_once dirname(__DIR__) . '/includes/db.php';

echo "Running Supplier KPI Calculation Script at " . date('Y-m-d H:i:s') . "\n<br>";

$conn = connect_db();

// 1. Get all suppliers
$sql_suppliers = "SELECT id, supplier_name FROM suppliers";
$suppliers_result = $conn->query($sql_suppliers);

if ($suppliers_result->num_rows === 0) {
    echo "No suppliers found to process.\n";
    exit();
}

// Prepare the statement to update the supplier's KPI
$sql_update = "UPDATE suppliers SET on_time_delivery_rate = ? WHERE id = ?";
$stmt_update = $conn->prepare($sql_update);

while ($supplier = $suppliers_result->fetch_assoc()) {
    $supplier_id = $supplier['id'];
    
    // 2. For each supplier, get all their 'Completed' POs that had an expected delivery date.
    $sql_pos = "SELECT po.id, po.expected_delivery_date 
                FROM purchase_orders po
                WHERE po.supplier_id = ? 
                AND po.status = 'Completed'
                AND po.expected_delivery_date IS NOT NULL";
                
    $stmt_pos = $conn->prepare($sql_pos);
    $stmt_pos->bind_param("i", $supplier_id);
    $stmt_pos->execute();
    $pos_result = $stmt_pos->get_result();

    $total_completed_pos = 0;
    $on_time_pos = 0;

    if ($pos_result->num_rows > 0) {
        while ($po = $pos_result->fetch_assoc()) {
            $total_completed_pos++;
            
            // 3. For each completed PO, find the date of the LAST delivery made for it.
            $sql_delivery = "SELECT MAX(delivery_date) as last_delivery_date 
                             FROM deliveries 
                             WHERE po_id = ?";
            $stmt_delivery = $conn->prepare($sql_delivery);
            $stmt_delivery->bind_param("i", $po['id']);
            $stmt_delivery->execute();
            $delivery = $stmt_delivery->get_result()->fetch_assoc();

            // 4. Compare the final delivery date to the expected date.
            if ($delivery && $delivery['last_delivery_date']) {
                if (strtotime($delivery['last_delivery_date']) <= strtotime($po['expected_delivery_date'])) {
                    $on_time_pos++;
                }
            }
        }
    }

    // 5. Calculate the on-time percentage
    $on_time_rate = 0;
    if ($total_completed_pos > 0) {
        $on_time_rate = ($on_time_pos / $total_completed_pos) * 100;
    }
    
    // 6. Update the supplier's record in the database
    $stmt_update->bind_param("di", $on_time_rate, $supplier_id);
    $stmt_update->execute();
    
    echo "Processed supplier: " . htmlspecialchars($supplier['supplier_name']) . " - On-Time Rate: " . number_format($on_time_rate, 2) . "%\n<br>";
}

echo "KPI calculation complete.\n";

$stmt_update->close();
$conn->close();
?>