<?php
$page_title = "Manage Assets";
include('../../includes/header.php');

if (!has_permission('asset_view') && !has_permission('asset_manage')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

include('../../includes/db.php');
$conn = connect_db();

// Fetch all assets, joining with asset_types and employees to get their names
$sql = "SELECT 
            a.*, 
            at.type_name,
            CONCAT(e.first_name, ' ', e.last_name) as assigned_to_name
        FROM assets a
        JOIN asset_types at ON a.asset_type_id = at.id
        LEFT JOIN employees e ON a.assigned_to_employee_id = e.id
        ORDER BY a.is_active DESC, a.asset_name ASC";
$result = $conn->query($sql);

$can_manage = has_permission('asset_manage');
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/erp_project/index.php">Home</a></li>
    <li class="breadcrumb-item">Inventory</li>
    <li class="breadcrumb-item active" aria-current="page">Manage Assets</li>
  </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?php echo $page_title; ?></h1>
    <?php if ($can_manage): ?>
        <a href="add_asset.php" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Add New Asset</a>
    <?php endif; ?>
</div>

<?php 
// Handle status messages from redirects
if (isset($_GET['status'])) {
    $message = ''; $alert_type = 'info';
    if ($_GET['status'] == 'success') { $message = 'Asset added successfully!'; $alert_type = 'success'; }
    if ($_GET['status'] == 'updated') { $message = 'Asset updated successfully!'; $alert_type = 'success'; }
    if ($_GET['status'] == 'deleted') { $message = 'Asset has been deleted.'; $alert_type = 'warning'; }
    if ($_GET['status'] == 'toggled') { $message = 'Asset status has been changed.'; $alert_type = 'success'; }
    if ($message) {
        echo '<div class="alert alert-'. $alert_type .' alert-dismissible fade show" role="alert">'. htmlspecialchars($message) .'<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    }
}
?>

<div class="card">
    <div class="card-header"><h5>All Company Assets</h5></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover data-table">
                <thead class="table-dark">
                    <tr>
                        <th>Asset Name / Tag</th>
                        <th>Type</th>
                        <th>Assigned To</th>
                        <th>Status</th>
                        <th class="text-end">Current Value</th>
                        <?php if ($can_manage): ?><th>Actions</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): 
                            // Depreciation Calculation Logic
                            $current_value = $row['purchase_cost'];
                            if ($row['purchase_cost'] > 0 && $row['useful_life_years'] > 0 && $row['purchase_date']) {
                                $depreciable_cost = $row['purchase_cost'] - $row['salvage_value'];
                                $annual_depreciation = $depreciable_cost / $row['useful_life_years'];
                                $age_in_days = (new DateTime())->diff(new DateTime($row['purchase_date']))->days;
                                $age_in_years = $age_in_days / 365.25;
                                $accumulated_depreciation = $annual_depreciation * $age_in_years;
                                $current_value = max($row['purchase_cost'] - $accumulated_depreciation, $row['salvage_value']);
                            }
                        ?>
                            <tr class="<?php echo $row['is_active'] ? '' : 'table-secondary text-muted'; ?>">
                                <td>
                                    <?php echo htmlspecialchars($row['asset_name']); ?>
                                    <small class="d-block text-muted"><?php echo htmlspecialchars($row['asset_tag']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($row['type_name']); ?></td>
                                <td><?php echo $row['assigned_to_name'] ? htmlspecialchars($row['assigned_to_name']) : '<span class="text-muted">In Stock</span>'; ?></td>
                                <td><?php echo $row['is_active'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>'; ?></td>
                                <td class="text-end fw-bold">$<?php echo number_format($current_value, 2); ?></td>
                                <?php if ($can_manage): ?>
                                <td>
                                    <a href="edit_asset.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAssetModal" data-id="<?php echo $row['id']; ?>" title="Permanently Delete"><i class="bi bi-trash"></i></button>
                                </td>
                                <?php endif; ?>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteAssetModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Confirm Deletion</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">Are you sure you want to permanently delete this asset record? <p class="text-danger small">This action cannot be undone.</p></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form action="handle_delete_asset.php" method="POST" class="d-inline">
            <input type="hidden" name="id" id="delete_asset_id">
            <button type="submit" class="btn btn-danger">Delete</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php
$conn->close();
include('../../includes/footer.php');
?>