<?php
$page_title = "Add Account";
include('../../includes/header.php');
include('../../includes/db.php');
include('../../includes/accounts_schema.php');

if (!has_permission('budget_manage')) { 
    header('Location: /erp_project/index.php?status=access_denied'); 
    exit(); 
}

$conn = connect_db();
ensure_accounts_schema($conn);

// Get parent accounts for dropdown
$parent_accounts = $conn->query("SELECT account_code, account_name FROM chart_of_accounts WHERE is_active = 1 ORDER BY account_code");

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $account_code = trim($_POST['account_code']);
    $account_name = trim($_POST['account_name']);
    $account_type = $_POST['account_type'];
    $parent_account_code = !empty($_POST['parent_account_code']) ? $_POST['parent_account_code'] : null;
    $is_posting = isset($_POST['is_posting']) ? 1 : 0;
    $opening_balance = floatval($_POST['opening_balance']);
    
    if (empty($account_code) || empty($account_name) || empty($account_type)) {
        $error = "Account code, name, and type are required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO chart_of_accounts (account_code, account_name, account_type, parent_account_code, is_posting, opening_balance) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssid", $account_code, $account_name, $account_type, $parent_account_code, $is_posting, $opening_balance);
        
        if ($stmt->execute()) {
            $success = "Account created successfully!";
            // Clear form
            $account_code = $account_name = $account_type = $parent_account_code = '';
            $is_posting = 1;
            $opening_balance = 0;
        } else {
            $error = "Error creating account: " . $conn->error;
        }
        $stmt->close();
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?php echo $page_title; ?></h1>
    <a href="/erp_project/modules/accounts/chart_of_accounts.php" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Chart of Accounts
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
        <h5>Account Information</h5>
    </div>
    <div class="card-body">
        <form method="POST">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="account_code" class="form-label">Account Code *</label>
                        <input type="text" class="form-control" id="account_code" name="account_code" 
                               value="<?php echo htmlspecialchars($account_code ?? ''); ?>" required>
                        <div class="form-text">Unique identifier for the account</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="account_name" class="form-label">Account Name *</label>
                        <input type="text" class="form-control" id="account_name" name="account_name" 
                               value="<?php echo htmlspecialchars($account_name ?? ''); ?>" required>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="account_type" class="form-label">Account Type *</label>
                        <select class="form-select" id="account_type" name="account_type" required>
                            <option value="">Select Type</option>
                            <option value="Asset" <?php echo ($account_type ?? '') === 'Asset' ? 'selected' : ''; ?>>Asset</option>
                            <option value="Liability" <?php echo ($account_type ?? '') === 'Liability' ? 'selected' : ''; ?>>Liability</option>
                            <option value="Equity" <?php echo ($account_type ?? '') === 'Equity' ? 'selected' : ''; ?>>Equity</option>
                            <option value="Revenue" <?php echo ($account_type ?? '') === 'Revenue' ? 'selected' : ''; ?>>Revenue</option>
                            <option value="Expense" <?php echo ($account_type ?? '') === 'Expense' ? 'selected' : ''; ?>>Expense</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="parent_account_code" class="form-label">Parent Account</label>
                        <select class="form-select" id="parent_account_code" name="parent_account_code">
                            <option value="">None (Top Level)</option>
                            <?php if ($parent_accounts && $parent_accounts->num_rows > 0): ?>
                                <?php while($row = $parent_accounts->fetch_assoc()): ?>
                                    <option value="<?php echo htmlspecialchars($row['account_code']); ?>" 
                                            <?php echo ($parent_account_code ?? '') === $row['account_code'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($row['account_code'] . ' - ' . $row['account_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="opening_balance" class="form-label">Opening Balance</label>
                        <input type="number" step="0.01" class="form-control" id="opening_balance" name="opening_balance" 
                               value="<?php echo htmlspecialchars($opening_balance ?? 0); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" id="is_posting" name="is_posting" 
                                   <?php echo ($is_posting ?? 1) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="is_posting">
                                Allow posting to this account
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="reset" class="btn btn-secondary me-md-2">Reset</button>
                <button type="submit" class="btn btn-primary">Create Account</button>
            </div>
        </form>
    </div>
</div>

<?php include('../../includes/footer.php'); ?> 