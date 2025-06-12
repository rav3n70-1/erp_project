<?php
$page_title = "Import Products";
include('../../includes/header.php');

if (!has_permission('user_manage')) { // Using user_manage as a proxy for this admin-level task
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/erp_project/index.php">Home</a></li>
    <li class="breadcrumb-item">Administration</li>
    <li class="breadcrumb-item active" aria-current="page">Import Products</li>
  </ol>
</nav>

<h1 class="mt-4"><?php echo $page_title; ?></h1>

<?php
// Handle status messages from the handler script
if (isset($_GET['status'])) {
    $message = ''; $alert_type = 'info';
    if ($_GET['status'] == 'success') {
        $count = isset($_GET['count']) ? (int)$_GET['count'] : 0;
        $message = "Successfully imported " . $count . " products!";
        $alert_type = 'success';
    } elseif ($_GET['status'] == 'error') {
        $error_msg = 'An error occurred.';
        if (isset($_GET['msg'])) {
            if ($_GET['msg'] == 'file_type') {
                $error_msg = 'Invalid file type. Please upload a CSV file.';
            } elseif ($_GET['msg'] == 'validation') {
                $line = isset($_GET['line']) ? (int)$_GET['line'] : 0;
                $reason = isset($_GET['reason']) ? htmlspecialchars($_GET['reason']) : 'unknown';
                $error_msg = "Validation error on row " . $line . ": " . $reason;
            }
        }
        $message = $error_msg;
        $alert_type = 'danger';
    }
    echo '<div class="alert alert-'. $alert_type .' alert-dismissible fade show" role="alert">'. $message .'<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
}
?>

<div class="card">
    <div class="card-header">
        <h5>Upload CSV File</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <form action="handle_import_products.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="product_csv" class="form-label">Product CSV File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="product_csv" name="product_csv" accept=".csv" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Upload and Import</button>
                </form>
            </div>
            <div class="col-md-4">
                <h6>Instructions:</h6>
                <p class="small">Your CSV file must have the following columns in this exact order:</p>
                <ol class="list-group list-group-numbered list-group-flush small">
                    <li class="list-group-item">SKU (Must be unique)</li>
                    <li class="list-group-item">ProductName</li>
                    <li class="list-group-item">Description</li>
                    <li class="list-group-item">Price (Numbers only)</li>
                    <li class="list-group-item">CategoryName (Must match an existing category exactly)</li>
                </ol>
                <p class="small mt-3">The first row of the CSV should be the header row with these exact names.</p>
            </div>
        </div>
    </div>
</div>

<?php
include('../../includes/footer.php');
?>