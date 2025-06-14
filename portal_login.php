<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - ERP System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }
        .login-page-wrapper {
            display: flex;
            width: 100%;
            height: 100vh;
            overflow: hidden;
        }
        .login-image-side {
            flex: 1;
            /* This combines a semi-transparent color overlay with a background image */
            background: linear-gradient(to right, rgba(4, 38, 92, 0.85), rgba(2, 84, 163, 0.85)), url('https://images.unsplash.com/photo-1556742502-ec7c0e9f34b1?q=80&w=1974&auto=format&fit=crop') no-repeat center center;
            background-size: cover;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem;
        }
        .login-form-side {
            flex: 1;
            background-color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-form-container {
            width: 100%;
            max-width: 420px;
            padding: 2rem;
        }
    </style>
</head>
<body>
    <div class="login-page-wrapper">
        <div class="login-image-side">
            <i class="bi bi-buildings-fill display-2 mb-4"></i> 
            <h1 class="display-4 fw-bold">ERP System</h1>
            <p class="lead">A unified platform to manage your entire business workflow efficiently.</p>
        </div>
        <div class="login-form-side">
            <div class="login-form-container">
                <h3 class="mb-4">Select Your Portal</h3>
                <div class="d-grid gap-3">
                    <a href="login.php" class="btn btn-primary btn-lg"><i class="bi bi-person-badge-fill me-2"></i>Employee / Staff Login</a>
                    <a href="supplier_login.php" class="btn btn-success btn-lg"><i class="bi bi-box-seam-fill me-2"></i>Supplier Portal Login</a>
                    <a href="client_login.php" class="btn btn-info btn-lg text-white"><i class="bi bi-briefcase-fill me-2"></i>Customer Portal</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>