<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');
if (!has_permission(['Manager','Procurement Officer'])) { header('Location: /erp_project/dashboard.php?status=access_denied'); exit(); }

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['csv_file'])) { header('Location: bulk_import.php'); exit(); }

$conn = connect_db();
$tmp = $_FILES['csv_file']['tmp_name'];
if (!is_uploaded_file($tmp)) { header('Location: bulk_import.php?status=error'); exit(); }

$handle = fopen($tmp, 'r');
if (!$handle) { header('Location: bulk_import.php?status=error'); exit(); }

$header = fgetcsv($handle);
$map = array_flip($header);
$required = ['product_name','sku','category_name','price','quantity_in_stock'];
foreach ($required as $col) { if (!isset($map[$col])) { fclose($handle); header('Location: bulk_import.php?status=error_cols'); exit(); } }

$created = 0; $updated = 0; $skipped = 0;
while (($row = fgetcsv($handle)) !== false) {
    $product_name = trim($row[$map['product_name']] ?? '');
    $sku = trim($row[$map['sku']] ?? '');
    $category_name = trim($row[$map['category_name']] ?? 'Default');
    $price = floatval($row[$map['price']] ?? 0);
    $qty = intval($row[$map['quantity_in_stock']] ?? 0);
    $description = isset($map['description']) ? trim($row[$map['description']] ?? '') : null;
    $reorder_point = isset($map['reorder_point']) ? intval($row[$map['reorder_point']] ?? 0) : 0;
    if ($product_name === '' || $sku === '') { $skipped++; continue; }

    // ensure category
    $cat_id = null;
    $stmt = $conn->prepare('SELECT id FROM product_categories WHERE category_name=?');
    $stmt->bind_param('s', $category_name);
    $stmt->execute();
    $stmt->bind_result($cat_id);
    if (!$stmt->fetch()) {
        $stmt->close();
        $insCat = $conn->prepare('INSERT INTO product_categories (category_name) VALUES (?)');
        $insCat->bind_param('s', $category_name);
        if ($insCat->execute()) { $cat_id = $insCat->insert_id; }
        $insCat->close();
    } else { $stmt->close(); }
    if (!$cat_id) { $skipped++; continue; }

    // upsert by SKU
    $check = $conn->prepare('SELECT id FROM products WHERE sku=?');
    $check->bind_param('s', $sku);
    $check->execute();
    $check->bind_result($pid);
    if ($check->fetch()) {
        $check->close();
        $upd = $conn->prepare('UPDATE products SET product_name=?, category_id=?, price=?, quantity_in_stock=?, description=?, reorder_point=? WHERE id=?');
        $upd->bind_param('sidisii', $product_name, $cat_id, $price, $qty, $description, $reorder_point, $pid);
        if ($upd->execute()) { $updated++; } else { $skipped++; }
        $upd->close();
    } else {
        $check->close();
        $ins = $conn->prepare('INSERT INTO products (product_name, sku, category_id, price, quantity_in_stock, description, reorder_point) VALUES (?,?,?,?,?,?,?)');
        $ins->bind_param('ssidisi', $product_name, $sku, $cat_id, $price, $qty, $description, $reorder_point);
        if ($ins->execute()) { $created++; } else { $skipped++; }
        $ins->close();
    }
}
fclose($handle);
header('Location: view_products.php?status=bulk_done&created='.$created.'&updated='.$updated.'&skipped='.$skipped);
exit(); 