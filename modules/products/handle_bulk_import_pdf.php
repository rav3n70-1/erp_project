<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');
if (!has_permission(['Manager','Procurement Officer'])) { header('Location: /erp_project/dashboard.php?status=access_denied'); exit(); }

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['pdf_file'])) { header('Location: bulk_import.php'); exit(); }

$conn = connect_db();
$tmp = $_FILES['pdf_file']['tmp_name'];
if (!is_uploaded_file($tmp)) { header('Location: bulk_import.php?status=error'); exit(); }

// Try to use Smalot\PdfParser if available via Composer
$text = '';
try {
    if (file_exists(__DIR__.'/../../vendor/autoload.php')) {
        require_once __DIR__.'/../../vendor/autoload.php';
        if (class_exists('Smalot\\PdfParser\\Parser')) {
            $parser = new Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($tmp);
            $text = $pdf->getText();
        }
    }
} catch (Exception $e) {
    $text = '';
}

if ($text === '') {
    // Fallback: use shell pdftotext if available (quietly ignore errors)
    $out = tempnam(sys_get_temp_dir(), 'pdf');
    @exec('pdftotext '.escapeshellarg($tmp).' '.escapeshellarg($out));
    if (file_exists($out)) { $text = @file_get_contents($out); @unlink($out); }
}

if ($text === '' || strlen(trim($text)) === 0) {
    header('Location: bulk_import.php?status=pdf_parse_error'); exit();
}

$lines = preg_split('/\r\n|\n|\r/', $text);
$created=0; $updated=0; $skipped=0;
foreach ($lines as $line) {
    $line = trim(preg_replace('/\s+/', ' ', $line));
    if ($line === '') continue;
    // Heuristic: expecting format like "SKU ProductName Category Price Qty [Description...]"
    $parts = explode(' ', $line);
    if (count($parts) < 5) { continue; }
    // Try to find last two numeric columns as price and qty
    $qty = null; $price = null;
    for ($i=count($parts)-1; $i>=0; $i--) {
        if ($qty === null && preg_match('/^\d+$/', $parts[$i])) { $qty = (int)$parts[$i]; unset($parts[$i]); continue; }
        if ($price === null && preg_match('/^\d+(\.\d+)?$/', $parts[$i])) { $price = (float)$parts[$i]; unset($parts[$i]); break; }
    }
    if ($price === null || $qty === null) { $skipped++; continue; }
    $parts = array_values($parts);
    $sku = array_shift($parts);
    $category_name = array_pop($parts);
    $product_name = implode(' ', $parts);
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
        $upd = $conn->prepare('UPDATE products SET product_name=?, category_id=?, price=?, quantity_in_stock=? WHERE id=?');
        $upd->bind_param('sidii', $product_name, $cat_id, $price, $qty, $pid);
        $ok = $upd->execute();
        $upd->close();
        $updated += $ok ? 1 : 0; $skipped += $ok ? 0 : 1;
    } else {
        $check->close();
        $ins = $conn->prepare('INSERT INTO products (product_name, sku, category_id, price, quantity_in_stock) VALUES (?,?,?,?,?)');
        $ins->bind_param('ssidi', $product_name, $sku, $cat_id, $price, $qty);
        $ok = $ins->execute();
        $ins->close();
        $created += $ok ? 1 : 0; $skipped += $ok ? 0 : 1;
    }
}
header('Location: view_products.php?status=pdf_bulk_done&created='.$created.'&updated='.$updated.'&skipped='.$skipped);
exit(); 