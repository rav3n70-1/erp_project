<?php
session_start();
if (isset($_SESSION['user_id'])) {
    session_destroy();
    header("Location: supplier_login.php");
    exit();
}
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { display: flex; align-items: center; justify-content: center; height: 100vh; background-color: #f8f9fa; }
        .login-card { width: 100%; max-width: 400px; }
    </style>
</head>
<body>
    <div class="card login-card shadow-sm">
        <div class="card-body">
            <h3 class="card-title text-center mb-4">Supplier Portal Login</h3>
            <?php if (isset($_GET['error'])) { echo '<div class="alert alert-danger">Invalid username or password.</div>'; } ?>
            <form action="handle_supplier_login.php" method="POST">
                <div class="mb-3"><label for="username" class="form-label">Username</label><input type="text" class="form-control" id="username" name="username" required></div>
                <div class="mb-3"><label for="password" class="form-label">Password</label><input type="password" class="form-control" id="password" name="password" required></div>
                <div class="d-grid"><button type="submit" class="btn btn-success">Login as Supplier</button></div>
            </form>
            <div class="text-center mt-3">
                <a href="portal_login.php" class="small">« Back to Portal Selection</a>
            </div>
        </div>
    </div>
</body>
</html>