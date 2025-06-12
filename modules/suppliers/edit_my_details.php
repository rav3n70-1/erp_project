<?php
session_start();
if (!isset($_SESSION['supplier_id'])) {
    header('Location: /erp_project/supplier_login.php');
    exit();
}

include('../../includes/db.php');
$conn = connect_db();
$supplier_id = $_SESSION['supplier_id'];
$supplier_name = $_SESSION['supplier_name'];

// Fetch the supplier's current data
$sql = "SELECT * FROM suppliers WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $supplier_id);
$stmt->execute();
$supplier = $stmt->get_result()->fetch_assoc();

$page_title = "Update My Details";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="portal.php">Supplier Portal</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><span class="navbar-text me-3">Welcome, <?php echo htmlspecialchars($supplier_name); ?></span></li>
                    <li class="nav-item"><a class="btn btn-outline-light" href="/erp_project/supplier_logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="portal.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Update My Details</li>
            </ol>
        </nav>
        <h1 class="mb-4"><?php echo $page_title; ?></h1>

        <div class="card">
            <div class="card-header"><h5>Your Information</h5></div>
            <div class="card-body">
                <p class="text-muted">Submit a request to change your contact or banking information. All changes will be reviewed by an administrator before they are applied.</p>
                <form action="handle_update_my_details.php" method="POST">
                    <fieldset class="mb-3">
                        <legend>Banking Information</legend>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="bank_name" class="form-label">Bank Name</label>
                                <input type="text" class="form-control" id="bank_name" name="bank_name" value="<?php echo htmlspecialchars($supplier['bank_name']); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="bank_account_number" class="form-label">Bank Account Number</label>
                                <input type="text" class="form-control" id="bank_account_number" name="bank_account_number" value="<?php echo htmlspecialchars($supplier['bank_account_number']); ?>">
                            </div>
                        </div>
                    </fieldset>
                    <hr>
                    <button type="submit" class="btn btn-primary">Submit Changes for Review</button>
                    <a href="portal.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>