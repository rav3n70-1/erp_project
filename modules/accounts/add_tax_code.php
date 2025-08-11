<?php
$page_title = "Add Tax Code";
include('../../includes/header.php');
include('../../includes/db.php');
include('../../includes/accounts_schema.php');

if (!has_permission('budget_manage')) { 
    header('Location: /erp_project/index.php?status=access_denied'); 
    exit(); 
}

$conn = connect_db();
ensure_accounts_schema($conn);

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tax_code = trim($_POST['tax_code']);
    $description = trim($_POST['description']);
    $rate = floatval($_POST['rate']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    if (empty($tax_code) || $rate < 0 || $rate > 100) {
        $error = "Tax code is required and rate must be between 0 and 100.";
    } else {
        $stmt = $conn->prepare("INSERT INTO tax_codes (tax_code, description, rate, is_active) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssdi", $tax_code, $description, $rate, $is_active);
        
        if ($stmt->execute()) {
            $success = "Tax code created successfully!";
            // Clear form
            $tax_code = $description = '';
            $rate = 0;
            $is_active = 1;
        } else {
            $error = "Error creating tax code: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?php echo $page_title; ?></h1>
    <a href="/erp_project/modules/accounts/taxes.php" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Tax Codes
    </a>
</div>

<?php if ($success): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?php echo $success; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?php echo $error; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h5>Tax Code Information</h5>
    </div>
    <div class="card-body">
        <form method="POST">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="tax_code" class="form-label">Tax Code *</label>
                        <input type="text" class="form-control" id="tax_code" name="tax_code" 
                               value="<?php echo htmlspecialchars($tax_code ?? ''); ?>" required>
                        <div class="form-text">Unique identifier for the tax code (e.g., VAT, GST, SALES)</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="rate" class="form-label">Tax Rate (%) *</label>
                        <input type="number" step="0.01" min="0" max="100" class="form-control" 
                               id="rate" name="rate" value="<?php echo htmlspecialchars($rate ?? 0); ?>" required>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($description ?? ''); ?></textarea>
                <div class="form-text">Optional description of the tax code</div>
            </div>
            
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                           <?php echo ($is_active ?? 1) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="is_active">
                        Active
                    </label>
                </div>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="reset" class="btn btn-secondary me-md-2">Reset</button>
                <button type="submit" class="btn btn-primary">Create Tax Code</button>
            </div>
        </form>
    </div>
</div>

<?php include('../../includes/footer.php'); ?> 