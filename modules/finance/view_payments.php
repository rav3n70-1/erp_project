<?php
$page_title = "All Payments";
include('../../includes/header.php');
include('../../includes/db.php');

$conn = connect_db();

// Fetch all payments, joining with POs and Suppliers
$sql = "SELECT 
            p.id as payment_id,
            p.payment_date,
            p.amount_paid,
            p.payment_method,
            po.po_number,
            s.supplier_name
        FROM payments p
        JOIN purchase_orders po ON p.po_id = po.id
        JOIN suppliers s ON po.supplier_id = s.id
        ORDER BY p.payment_date DESC";
$result = $conn->query($sql);
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/erp_project/index.php">Home</a></li>
    <li class="breadcrumb-item active" aria-current="page">Payments</li>
  </ol>
</nav>

<h1 class="mt-4"><?php echo $page_title; ?></h1>
<p class="lead">A central log of all payments made to suppliers.</p>

<div class="card">
    <div class="card-header">
        <h5>Payment History</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Payment ID</th>
                        <th>Payment Date</th>
                        <th>PO Number</th>
                        <th>Supplier</th>
                        <th>Method</th>
                        <th class="text-end">Amount Paid</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['payment_id']; ?></td>
                                <td><?php echo date("d M, Y", strtotime($row['payment_date'])); ?></td>
                                <td><?php echo htmlspecialchars($row['po_number']); ?></td>
                                <td><?php echo htmlspecialchars($row['supplier_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                                <td class="text-end">$<?php echo number_format($row['amount_paid'], 2); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No payments have been recorded yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$conn->close();
include('../../includes/footer.php');
?>