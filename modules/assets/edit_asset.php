<?php
$page_title = "Edit Asset";
include('../../includes/header.php');

if (!has_permission('asset_manage')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

include('../../includes/db.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) { die("Invalid Asset ID."); }

$asset_id = $_GET['id'];
$conn = connect_db();

// Fetch existing asset data
$sql_asset = "SELECT * FROM assets WHERE id = ?";
$stmt_asset = $conn->prepare($sql_asset);
$stmt_asset->bind_param("i", $asset_id);
$stmt_asset->execute();
$result_asset = $stmt_asset->get_result();
if ($result_asset->num_rows === 0) { die("Asset not found."); }
$asset = $result_asset->fetch_assoc();

// Fetch asset types and employees for dropdowns
$sql_types = "SELECT id, type_name FROM asset_types ORDER BY type_name ASC";
$types_result = $conn->query($sql_types);

$sql_employees = "SELECT id, first_name, last_name FROM employees WHERE is_active = 1 ORDER BY last_name ASC";
$employees_result = $conn->query($sql_employees);

$asset_statuses = ['In Stock', 'In Use', 'Under Maintenance', 'Retired'];
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/erp_project/index.php">Home</a></li>
    <li class="breadcrumb-item"><a href="view_assets.php">Manage Assets</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit Asset</li>
  </ol>
</nav>

<h1 class="mt-4">Edit Asset: <?php echo htmlspecialchars($asset['asset_name']); ?></h1>

<div class="card">
    <div class="card-header"><h5>Asset Details</h5></div>
    <div class="card-body">
        <form action="handle_edit_asset.php" method="POST">
            <input type="hidden" name="asset_id" value="<?php echo $asset['id']; ?>">
            <fieldset class="mb-3">
                <legend>Asset Information</legend>
                <div class="row">
                    <div class="col-md-6 mb-3"><label for="asset_name" class="form-label">Asset Name <span class="text-danger">*</span></label><input type="text" class="form-control" id="asset_name" name="asset_name" value="<?php echo htmlspecialchars($asset['asset_name']); ?>" required></div>
                    <div class="col-md-6 mb-3"><label for="asset_tag" class="form-label">Asset Tag <span class="text-danger">*</span></label><input type="text" class="form-control" id="asset_tag" name="asset_tag" value="<?php echo htmlspecialchars($asset['asset_tag']); ?>" required></div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label for="asset_type_id" class="form-label">Asset Type <span class="text-danger">*</span></label><select class="form-select" id="asset_type_id" name="asset_type_id" required><?php while($type = $types_result->fetch_assoc()): $selected = ($type['id'] == $asset['asset_type_id']) ? 'selected' : '';?><option value="<?php echo $type['id']; ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($type['type_name']); ?></option><?php endwhile; ?></select></div>
                    <div class="col-md-6 mb-3"><label for="status" class="form-label">Status <span class="text-danger">*</span></label><select class="form-select" id="status" name="status" required><?php foreach($asset_statuses as $status): $selected = ($status == $asset['status']) ? 'selected' : '';?><option value="<?php echo $status; ?>" <?php echo $selected; ?>><?php echo $status; ?></option><?php endforeach; ?></select></div>
                </div>
                <div class="mb-3"><label for="notes" class="form-label">Notes</label><textarea class="form-control" id="notes" name="notes" rows="2"><?php echo htmlspecialchars($asset['notes']); ?></textarea></div>
            </fieldset>
            
            <fieldset class="mb-3">
                <legend>Financial & Depreciation Details</legend>
                <div class="row">
                    <div class="col-md-3 mb-3"><label for="purchase_date" class="form-label">Purchase Date</label><input type="date" class="form-control" id="purchase_date" name="purchase_date" value="<?php echo htmlspecialchars($asset['purchase_date']); ?>"></div>
                    <div class="col-md-3 mb-3"><label for="purchase_cost" class="form-label">Purchase Cost ($)</label><input type="number" class="form-control" id="purchase_cost" name="purchase_cost" value="<?php echo htmlspecialchars($asset['purchase_cost']); ?>" step="0.01" min="0"></div>
                    <div class="col-md-3 mb-3"><label for="useful_life_years" class="form-label">Useful Life (Years)</label><input type="number" class="form-control" id="useful_life_years" name="useful_life_years" value="<?php echo htmlspecialchars($asset['useful_life_years']); ?>" min="0"></div>
                    <div class="col-md-3 mb-3"><label for="salvage_value" class="form-label">Salvage Value ($)</label><input type="number" class="form-control" id="salvage_value" name="salvage_value" value="<?php echo htmlspecialchars($asset['salvage_value']); ?>" step="0.01" min="0"></div>
                </div>
            </fieldset>
            
            <fieldset>
                <legend>Assignment</legend>
                <div class="mb-3"><label for="assigned_to_employee_id" class="form-label">Assigned To (Optional)</label><select class="form-select" id="assigned_to_employee_id" name="assigned_to_employee_id"><option value="">None (In Stock)</option><?php while($emp = $employees_result->fetch_assoc()): $selected = ($emp['id'] == $asset['assigned_to_employee_id']) ? 'selected' : ''; ?><option value="<?php echo $emp['id']; ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']); ?></option><?php endwhile; ?></select></div>
            </fieldset>

            <hr>
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="view_assets.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php
$conn->close();
include('../../includes/footer.php');
?>