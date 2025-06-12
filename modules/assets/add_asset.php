<?php
$page_title = "Add New Asset";
include('../../includes/header.php');

if (!has_permission('asset_manage')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

include('../../includes/db.php');
$conn = connect_db();

// Fetch asset types for the dropdown
$sql_types = "SELECT id, type_name FROM asset_types ORDER BY type_name ASC";
$types_result = $conn->query($sql_types);

// Fetch employees for the 'assign to' dropdown
$sql_employees = "SELECT id, first_name, last_name FROM employees ORDER BY last_name ASC";
$employees_result = $conn->query($sql_employees);

$asset_statuses = ['In Stock', 'In Use', 'Under Maintenance', 'Retired'];
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/erp_project/index.php">Home</a></li>
    <li class="breadcrumb-item"><a href="view_assets.php">Manage Assets</a></li>
    <li class="breadcrumb-item active" aria-current="page">Add Asset</li>
  </ol>
</nav>

<h1 class="mt-4"><?php echo $page_title; ?></h1>

<div class="card">
    <div class="card-header"><h5>Asset Details</h5></div>
    <div class="card-body">
        <form action="handle_add_asset.php" method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="asset_name" class="form-label">Asset Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="asset_name" name="asset_name" placeholder="e.g., Dell XPS 15 Laptop" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="asset_tag" class="form-label">Asset Tag / Serial Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="asset_tag" name="asset_tag" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="asset_type_id" class="form-label">Asset Type <span class="text-danger">*</span></label>
                    <select class="form-select" id="asset_type_id" name="asset_type_id" required>
                        <option value="">Select a type</option>
                        <?php while($type = $types_result->fetch_assoc()): ?>
                            <option value="<?php echo $type['id']; ?>"><?php echo htmlspecialchars($type['type_name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-select" id="status" name="status" required>
                        <?php foreach($asset_statuses as $status): ?>
                            <option value="<?php echo $status; ?>"><?php echo $status; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="purchase_date" class="form-label">Purchase Date</label>
                    <input type="date" class="form-control" id="purchase_date" name="purchase_date">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="purchase_cost" class="form-label">Purchase Cost ($)</label>
                    <input type="number" class="form-control" id="purchase_cost" name="purchase_cost" step="0.01" min="0">
                </div>
            </div>
            <div class="mb-3">
                <label for="assigned_to_employee_id" class="form-label">Assigned To (Optional)</label>
                <select class="form-select" id="assigned_to_employee_id" name="assigned_to_employee_id">
                    <option value="">None (In Stock)</option>
                    <?php while($emp = $employees_result->fetch_assoc()): ?>
                        <option value="<?php echo $emp['id']; ?>"><?php echo htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="notes" class="form-label">Notes</label>
                <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
            </div>
            <hr>
            <button type="submit" class="btn btn-primary">Save Asset</button>
            <a href="view_assets.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php
$conn->close();
include('../../includes/footer.php');
?>