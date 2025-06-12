<?php
$page_title = "All Deliveries";
include('../../includes/header.php');
include('../../includes/db.php');

$conn = connect_db();

// Fetch all deliveries, joining with POs and Suppliers to get relevant info
$sql = "SELECT 
            d.id as delivery_id,
            d.delivery_date,
            d.status as delivery_status,
            po.po_number,
            s.supplier_name
        FROM deliveries d
        JOIN purchase_orders po ON d.po_id = po.id
        JOIN suppliers s ON po.supplier_id = s.id
        ORDER BY d.delivery_date DESC";
$result = $conn->query($sql);
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/erp_project/index.php">Home</a></li>
    <li class="breadcrumb-item active" aria-current="page">Deliveries</li>
  </ol>
</nav>

<h1 class="mt-4"><?php echo $page_title; ?></h1>
<p class="lead">A central log of all deliveries received from suppliers.</p>

<div class="card">
    <div class="card-header">
        <h5>Delivery Log</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Delivery ID</th>
                        <th>Delivery Date</th>
                        <th>PO Number</th>
                        <th>Supplier</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['delivery_id']; ?></td>
                                <td><?php echo date("d M, Y", strtotime($row['delivery_date'])); ?></td>
                                <td><?php echo htmlspecialchars($row['po_number']); ?></td>
                                <td><?php echo htmlspecialchars($row['supplier_name']); ?></td>
                                <td>
                                    <span class="badge bg-success"><?php echo htmlspecialchars($row['delivery_status']); ?></span>
                                </td>
                                <td>
                                    <a href="/erp_project/modules/purchase_orders/view_po_details.php?id=<?php echo $conn->query("SELECT id FROM purchase_orders WHERE po_number = '".$row['po_number']."'")->fetch_assoc()['id']; ?>" class="btn btn-sm btn-info"><i class="bi bi-eye"></i> View on PO</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No deliveries have been recorded yet.</td>
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