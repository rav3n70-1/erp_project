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

// Fetch all Approved/Completed POs for this supplier to be used in the upload form
$sql = "SELECT * FROM purchase_orders WHERE supplier_id = ? AND status IN ('Approved', 'Partially Delivered', 'Completed') ORDER BY order_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $supplier_id);
$stmt->execute();
$result = $stmt->get_result();

$page_title = "Supplier Portal";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Supplier Portal</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><span class="navbar-text me-3">Welcome, <?php echo htmlspecialchars($supplier_name); ?></span></li>
                    <li class="nav-item"><a class="btn btn-outline-light" href="/erp_project/supplier_logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php 
        if (isset($_GET['status'])) {
            $message = ''; $alert_type = 'info';
            if ($_GET['status'] == 'success_upload') { $message = 'Invoice uploaded successfully and is awaiting approval.'; $alert_type = 'success'; }
            if ($_GET['status'] == 'change_request_success') { $message = 'Your change request has been submitted for review.'; $alert_type = 'success'; }
            if ($_GET['status'] == 'error_duplicate') { $message = 'Error: An invoice with that number has already been submitted.'; $alert_type = 'danger'; }
            if ($_GET['status'] == 'error_missing_file') { $message = 'Error: An invoice file attachment is required.'; $alert_type = 'danger'; }
            if ($message) { echo '<div class="alert alert-'. $alert_type .' alert-dismissible fade show" role="alert">'. htmlspecialchars($message) .'<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>'; }
        }
        ?>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Your Purchase Orders</h1>
            <div>
                <a href="edit_my_details.php" class="btn btn-info text-white me-2"><i class="bi bi-person-lines-fill"></i> Update My Details</a>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadInvoiceModal">
                    <i class="bi bi-upload me-2"></i>Upload New Invoice
                </button>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead><tr><th>PO Number</th><th>Order Date</th><th class="text-end">Total Amount</th><th>Status</th></tr></thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php mysqli_data_seek($result, 0); while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['po_number']); ?></td>
                                        <td><?php echo date("d M, Y", strtotime($row['order_date'])); ?></td>
                                        <td class="text-end">$<?php echo number_format($row['total_amount'], 2); ?></td>
                                        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($row['status']); ?></span></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center text-muted">You have no active purchase orders to invoice.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="uploadInvoiceModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Upload Invoice</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form action="handle_supplier_invoice_upload.php" method="POST" enctype="multipart/form-data">
              <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="po_id" class="form-label">Related Purchase Order <span class="text-danger">*</span></label>
                        <select class="form-select" id="po_id" name="po_id" required>
                            <option value="">Select a PO</option>
                            <?php mysqli_data_seek($result, 0); while($po = $result->fetch_assoc()): ?>
                                <option value="<?php echo $po['id']; ?>"><?php echo htmlspecialchars($po['po_number']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="invoice_number" class="form-label">Your Invoice Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="invoice_number" name="invoice_number" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="invoice_date" class="form-label">Invoice Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="invoice_date" name="invoice_date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="due_date" class="form-label">Due Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="due_date" required name="due_date">
                    </div>
                     <div class="col-md-4 mb-3">
                        <label for="total_amount" class="form-label">Invoice Amount ($) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="total_amount" name="total_amount" step="0.01" min="0" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="invoice_file" class="form-label">Attach Invoice PDF/Image <span class="text-danger">*</span></label>
                    <input class="form-control" type="file" id="invoice_file" name="invoice_file" accept=".pdf,.jpg,.jpeg,.png" required>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Submit Invoice</button>
              </div>
          </form>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>