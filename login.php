<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERP System Login - Employee Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="login-page-wrapper">
        <div class="login-image-side">
            <i class="bi bi-building-fill display-2 mb-4"></i>
            <h1 class="display-4 fw-bold">ERP System</h1>
            <p class="lead">Access your comprehensive business management dashboard with full system capabilities and analytics.</p>
        </div>
        <div class="login-form-side">
            <div class="login-form-container glass-card">
                <h3 class="mb-4">ERP System Login</h3>
                <?php
                if (isset($_GET['error'])) {
                    echo '<div class="alert alert-danger">Invalid username or password.</div>';
                }
                if (isset($_GET['status']) && $_GET['status'] == 'loggedout') {
                    echo '<div class="alert alert-success">You have been logged out successfully.</div>';
                }
                ?>
                <form action="handle_login.php" method="POST">
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                    </div>
                    <div class="input-group mb-3">
                         <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-lg text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">Login to ERP</button>
                    </div>
                </form>
                
                <!-- Additional Portal Links -->
                <div class="mt-4 pt-3 border-top">
                    <h6 class="text-center text-muted mb-3">Other Portals</h6>
                    <div class="d-grid gap-2">
                        <a href="supplier_login.php" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-buildings me-2"></i>Supplier Portal
                        </a>
                        <a href="client_login.php" class="btn btn-outline-info btn-sm">
                            <i class="bi bi-briefcase me-2"></i>Client Portal
                        </a>
                    </div>
                </div>
                
                <div class="text-center mt-3">
                    <a href="index.html" class="small">« Back to Home</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>