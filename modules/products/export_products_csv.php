<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');
if (!has_permission(['Manager','Procurement Officer'])) { header('Location: /erp_project/dashboard.php?status=access_denied'); exit(); }
$conn = connect_db();
$sql = "SELECT p.id, p.product_name, p.sku, pc.category_name, p.price, p.quantity_in_stock, p.description, p.reorder_point FROM products p JOIN product_categories pc ON p.category_id=pc.id ORDER BY p.product_name";
$res = $conn->query($sql);
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="products_export.csv"');
$out=fopen('php://output','w');
fputcsv($out,['id','product_name','sku','category','price','quantity_in_stock','description','reorder_point']);
if ($res) while($r=$res->fetch_assoc()){ fputcsv($out,[$r['id'],$r['product_name'],$r['sku'],$r['category_name'],$r['price'],$r['quantity_in_stock'],$r['description'],$r['reorder_point']]); }
fclose($out); exit(); 