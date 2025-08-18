<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Representative Portal - Coming Soon</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
        }
        
        .coming-soon-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .coming-soon-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 3rem;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            max-width: 600px;
            animation: slideInUp 0.8s ease-out;
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .portal-icon {
            font-size: 5rem;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 2rem;
        }
        
        .coming-soon-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: #2c3e50;
            margin-bottom: 1rem;
        }
        
        .coming-soon-subtitle {
            font-size: 1.2rem;
            color: #7f8c8d;
            margin-bottom: 2rem;
        }
        
        .features-list {
            list-style: none;
            padding: 0;
            margin: 2rem 0;
        }
        
        .features-list li {
            padding: 0.5rem 0;
            color: #34495e;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .features-list li i {
            margin-right: 0.75rem;
            color: #f5576c;
            font-size: 1.2rem;
        }
        
        .back-btn {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            color: white;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
            margin-top: 1rem;
        }
        
        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(245, 87, 108, 0.3);
            color: white;
        }
    </style>
</head>
<body>
    <div class="coming-soon-container">
        <div class="coming-soon-card">
            <div class="portal-icon">
                <i class="bi bi-person-badge-fill"></i>
            </div>
            
            <h1 class="coming-soon-title">Sales Representative Portal</h1>
            <p class="coming-soon-subtitle">This portal is currently under development and will be available soon!</p>
            
            <div class="alert alert-info mb-4">
                <i class="bi bi-info-circle-fill me-2"></i>
                <strong>Coming Soon!</strong> We're working hard to bring you an amazing sales management experience.
            </div>
            
            <h4 class="mb-3" style="color: #2c3e50;">Planned Features:</h4>
            <ul class="features-list">
                <li><i class="bi bi-check-circle-fill"></i>Advanced Lead Management System</li>
                <li><i class="bi bi-check-circle-fill"></i>Real-time Sales Pipeline Tracking</li>
                <li><i class="bi bi-check-circle-fill"></i>Commission Calculator & Reports</li>
                <li><i class="bi bi-check-circle-fill"></i>Customer Relationship Management</li>
                <li><i class="bi bi-check-circle-fill"></i>Mobile-First Responsive Design</li>
                <li><i class="bi bi-check-circle-fill"></i>Performance Analytics Dashboard</li>
            </ul>
            
            <div class="mt-4">
                <p class="text-muted mb-3">Expected Launch: <strong>Coming Soon</strong></p>
                <a href="portal_login.php" class="back-btn">
                    <i class="bi bi-arrow-left me-2"></i>Back to Portal Selection
                </a>
            </div>
        </div>
    </div>
</body>
</html> 