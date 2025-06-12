<?php
$page_title = "Supplier Performance Report";
include('../../includes/header.php');

// Ensure user has permission to view reports
if (!has_permission('reports_full_access')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

include('../../includes/db.php');
$conn = connect_db();

// Fetch all suppliers with their scorecard ratings and on-time delivery KPI
$sql = "SELECT 
            supplier_name,
            rating_delivery_time,
            rating_quality,
            rating_communication,
            on_time_delivery_rate
        FROM suppliers
        ORDER BY supplier_name ASC";
$result = $conn->query($sql);
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/erp_project/index.php">Home</a></li>
    <li class="breadcrumb-item">Reports</li>
    <li class="breadcrumb-item active" aria-current="page">Supplier Performance</li>
  </ol>
</nav>

<h1 class="mt-4"><?php echo $page_title; ?></h1>
<p class="lead">An overview of supplier ratings and on-time delivery performance.</p>

<div class="card">
    <div class="card-header">
        <h5>Performance Scorecards</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover data-table">
                <thead class="table-dark">
                    <tr>
                        <th>Supplier</th>
                        <th class="text-center">Delivery Time Rating</th>
                        <th class="text-center">Quality Rating</th>
                        <th class="text-center">Communication Rating</th>
                        <th class="text-center">On-Time Delivery Rate</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['supplier_name']); ?></td>
                                <td class="text-center"><?php echo $row['rating_delivery_time'] ? number_format($row['rating_delivery_time'], 1) . ' / 5.0' : 'N/A'; ?></td>
                                <td class="text-center"><?php echo $row['rating_quality'] ? number_format($row['rating_quality'], 1) . ' / 5.0' : 'N/A'; ?></td>
                                <td class="text-center"><?php echo $row['rating_communication'] ? number_format($row['rating_communication'], 1) . ' / 5.0' : 'N/A'; ?></td>
                                <td class="text-center">
                                    <?php if (isset($row['on_time_delivery_rate'])): ?>
                                        <span class="badge <?php echo $row['on_time_delivery_rate'] >= 95 ? 'bg-success' : ($row['on_time_delivery_rate'] >= 80 ? 'bg-warning' : 'bg-danger'); ?>">
                                            <?php echo number_format($row['on_time_delivery_rate'], 2); ?>%
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">No completed orders</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
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