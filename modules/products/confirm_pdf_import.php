<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');
if (!has_permission(['Manager','Procurement Officer'])) { header('Location: /erp_project/index.php?status=access_denied'); exit(); }

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['raw_text']) || !isset($_POST['map'])) {
  header('Location: bulk_import.php'); exit();
}

$text = $_POST['raw_text'];
$map = $_POST['map']; // column index => field

$conn = connect_db();
$lines = preg_split('/\r\n|\n|\r/', $text);
$created=0; $updated=0; $skipped=0;
foreach ($lines as $line) {
  $line = trim(preg_replace('/\s+/', ' ', $line));
  if ($line === '' || strlen($line) < 3) continue;
  $parts = explode(' ', $line);
  $record = [
    'product_name' => '',
    'sku' => '',
    'category_name' => 'Default',
    'price' => 0.0,
    'quantity' => 0,
    'description' => ''
  ];
  foreach ($map as $idx=>$field) {
    if ($field === '' || !isset($parts[$idx])) continue;
    $val = $parts[$idx];
    switch ($field) {
      case 'price': $record['price'] = floatval(preg_replace('/[^0-9.]/','',$val)); break;
      case 'quantity': $record['quantity'] = intval(preg_replace('/[^0-9]/','',$val)); break;
      default: $record[$field] = ($record[$field]??'') === '' ? $val : ($record[$field].' '.$val); break;
    }
  }
  if ($record['product_name'] === '' || $record['sku'] === '') { $skipped++; continue; }

  // ensure category
  $cat_id = null;
  $stmt = $conn->prepare('SELECT id FROM product_categories WHERE category_name=?');
  $stmt->bind_param('s', $record['category_name']);
  $stmt->execute();
  $stmt->bind_result($cat_id);
  if (!$stmt->fetch()) {
    $stmt->close();
    $insCat = $conn->prepare('INSERT INTO product_categories (category_name) VALUES (?)');
    $insCat->bind_param('s', $record['category_name']);
    if ($insCat->execute()) { $cat_id = $insCat->insert_id; }
    $insCat->close();
  } else { $stmt->close(); }
  if (!$cat_id) { $skipped++; continue; }

  // upsert by SKU
  $check = $conn->prepare('SELECT id FROM products WHERE sku=?');
  $check->bind_param('s', $record['sku']);
  $check->execute();
  $check->bind_result($pid);
  if ($check->fetch()) {
    $check->close();
    $upd = $conn->prepare('UPDATE products SET product_name=?, category_id=?, price=?, quantity_in_stock=?, description=? WHERE id=?');
    $upd->bind_param('sidisi', $record['product_name'], $cat_id, $record['price'], $record['quantity'], $record['description'], $pid);
  } else {
    $check->close();
    $upd = $conn->prepare('INSERT INTO products (product_name, sku, category_id, price, quantity_in_stock, description) VALUES (?,?,?,?,?,?)');
    $upd->bind_param('ssidis', $record['product_name'], $record['sku'], $cat_id, $record['price'], $record['quantity'], $record['description']);
  }
  if ($upd->execute()) {
    if ($upd->insert_id) $created++; else $updated++;
  } else { $skipped++; }
  $upd->close();
}
header('Location: view_products.php?status=pdf_confirm_done&created='.$created.'&updated='.$updated.'&skipped='.$skipped);
exit(); 