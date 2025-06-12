<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - ERP System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(to right, #6a11cb 0%, #2575fc 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            color: white;
        }
        .portal-container {
            text-align: center;
        }
        .portal-card {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            padding: 2rem;
            margin: 0 1rem;
            transition: all 0.3s ease;
            color: white;
            text-decoration: none;
        }
        .portal-card:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-5px);
            color: white;
        }
        .portal-card i {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .portal-card.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .portal-card.disabled:hover {
            transform: none;
            background: rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body>
    <div class="portal-container">
        <h1 class="mb-3">Welcome to the ERP System</h1>
        <p class="lead mb-5">Please select your login portal.</p>
        <div class="d-flex justify-content-center">
            
            <a href="login.php" class="portal-card">
                <i class="bi bi-person-badge-fill"></i>
                <h5>Employee / Staff Portal</h5>
            </a>

            <a href="supplier_login.php" class="portal-card">
                <i class="bi bi-box-seam-fill"></i>
                <h5>Supplier Portal</h5>
            </a>
            
            <a href="#" class="portal-card disabled" onclick="return false;">
                <i class="bi bi-people-fill"></i>
                <h5>Customer Portal</h5>
                <small>(Coming Soon)</small>
            </a>

        </div>
    </div>
</body>
</html>