<?php
session_start();
// If a regular user is somehow here, log them out
if (isset($_SESSION['user_id'])) {
    session_destroy();
    header("Location: supplier_login.php");
    exit();
}
// If a supplier is already logged in, send them to their portal
if (isset($_SESSION['supplier_id'])) {
    header('Location: modules/suppliers/portal.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supplier Portal Login - ERP System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="login-page-wrapper">
        <div class="login-image-side">
            <h1 class="display-4 fw-bold">Supplier Portal</h1>
            <p class="lead">Access your purchase orders and submit invoices directly to our finance team.</p>
        </div>
        <div class="login-form-side">
            <div class="login-form-container">
                <h3 class="mb-4">Supplier Login</h3>
                <?php if (isset($_GET['error'])) { echo '<div class="alert alert-danger">Invalid username or password.</div>'; } ?>
                <form action="handle_supplier_login.php" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success btn-lg">Login as Supplier</button>
                    </div>
                </form>
                <div class="text-center mt-3">
                    <a href="portal_login.php" class="small">« Back to Portal Selection</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>