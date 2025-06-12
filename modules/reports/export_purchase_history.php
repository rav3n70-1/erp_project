<?php
// This script generates a CSV file from the purchase history report data.
// It uses the same filters as the main report page.

include('../../includes/db.php');
include('../../includes/session_check.php');

$conn = connect_db();

// --- Build the exact same query as the report page ---
$sql = "SELECT 
            po.order_date,
            po.po_number,
            s.supplier_name,
            p.product_name,
            p.sku,
            poi.quantity,
            poi.unit_price,
            poi.total_price
        FROM po_items poi
        JOIN purchase_orders po ON poi.po_id = po.id
        JOIN products p ON poi.product_id = p.id
        JOIN suppliers s ON po.supplier_id = s.id
        WHERE po.status IN ('Approved', 'Partially Delivered', 'Completed')";

$params = [];
$types = '';

if (!empty($_GET['start_date'])) {
    $sql .= " AND po.order_date >= ?";
    $types .= 's';
    $params[] = $_GET['start_date'];
}
if (!empty($_GET['end_date'])) {
    $sql .= " AND po.order_date <= ?";
    $types .= 's';
    $params[] = $_GET['end_date'];
}
if (!empty($_GET['supplier_id'])) {
    $sql .= " AND po.supplier_id = ?";
    $types .= 'i';
    $params[] = $_GET['supplier_id'];
}

$sql .= " ORDER BY po.order_date DESC";
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// --- Generate CSV File ---

$filename = "purchase_history_" . date('Y-m-d') . ".csv";

// Set headers to force download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Open the output stream
$output = fopen('php://output', 'w');

// Add the header row to the CSV file
fputcsv($output, [
    'Order Date', 
    'PO Number', 
    'Supplier', 
    'SKU', 
    'Product', 
    'Quantity', 
    'Unit Price', 
    'Total Price'
]);

// Loop through the database results and write each row to the CSV
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
}

fclose($output);
$conn->close();
exit();
?>