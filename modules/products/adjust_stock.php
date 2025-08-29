<?php
$page_title = "Adjust Stock";
include('../../includes/header.php');
include('../../includes/db.php');
include('../../includes/permissions.php');
if (!has_permission(['Manager','Procurement Officer'])) { header('Location: /erp_project/dashboard.php?status=access_denied'); exit(); }
$conn = connect_db();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id<=0) { header('Location: view_products.php'); exit(); }
$prod = $conn->query("SELECT id, product_name, quantity_in_stock FROM products WHERE id=".(int)$id)->fetch_assoc();
if (!$prod) { header('Location: view_products.php'); exit(); }
$success=''; $error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $delta = (int)($_POST['delta'] ?? 0);
  $reason = trim($_POST['reason'] ?? 'Manual adjustment');
  $upd = $conn->prepare('UPDATE products SET quantity_in_stock = quantity_in_stock + ? WHERE id=?');
  $upd->bind_param('ii', $delta, $id);
  if ($upd->execute()) { $success='Stock updated.'; $prod['quantity_in_stock'] += $delta; } else { $error='Failed to update.'; }
  $upd->close();
}
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h1><?php echo $page_title; ?></h1>
  <a href="/erp_project/modules/products/view_products.php" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a>
</div>
<?php if ($success): ?><div class="alert alert-success alert-dismissible fade show" role="alert"><?php echo $success; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger alert-dismissible fade show" role="alert"><?php echo $error; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<div class="card"><div class="card-header"><h5><?php echo htmlspecialchars($prod['product_name']); ?></h5></div><div class="card-body">
  <div class="mb-3"><strong>Current Stock:</strong> <?php echo (int)$prod['quantity_in_stock']; ?></div>
  <form method="POST">
    <div class="row">
      <div class="col-md-4"><div class="mb-3"><label class="form-label">Change Quantity (+/-)</label><input type="number" name="delta" class="form-control" required></div></div>
      <div class="col-md-8"><div class="mb-3"><label class="form-label">Reason</label><input type="text" name="reason" class="form-control" value="Manual adjustment"></div></div>
    </div>
    <div class="d-grid d-md-flex justify-content-md-end"><button class="btn btn-primary" type="submit">Apply</button></div>
  </form>
</div></div>
<?php include('../../includes/footer.php'); ?> 