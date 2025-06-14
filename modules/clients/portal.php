<?php
session_start();

// Security Check: Ensure a client is logged in
if (!isset($_SESSION['client_id'])) {
    header('Location: /erp_project/client_login.php');
    exit();
}

include('../../includes/db.php');
$conn = connect_db();
$client_id = $_SESSION['client_id'];
$client_name = $_SESSION['client_name'];

// Fetch all projects for this specific client
$sql = "SELECT project_name, status, start_date, end_date FROM projects WHERE client_id = ? ORDER BY start_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $client_id);
$stmt->execute();
$result = $stmt->get_result();

$page_title = "Client Portal";
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
            <a class="navbar-brand" href="#">Client Portal</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><span class="navbar-text me-3">Welcome, <?php echo htmlspecialchars($client_name); ?></span></li>
                    <li class="nav-item"><a class="btn btn-outline-light" href="/erp_project/client_logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="mb-4">Your Project Status</h1>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead><tr><th>Project Name</th><th>Start Date</th><th>Expected End Date</th><th>Status</th></tr></thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['project_name']); ?></td>
                                        <td><?php echo date("d M, Y", strtotime($row['start_date'])); ?></td>
                                        <td><?php echo $row['end_date'] ? date("d M, Y", strtotime($row['end_date'])) : 'N/A'; ?></td>
                                        <td><span class="badge bg-primary"><?php echo htmlspecialchars($row['status']); ?></span></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center text-muted">You are not currently associated with any projects.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>