<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Selection - ERP System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --portal-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            --portal-hover-shadow: 0 25px 80px rgba(0, 0, 0, 0.25);
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Poppins', sans-serif;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated background particles */
        .bg-particle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 20s infinite linear;
            pointer-events: none;
        }

        @keyframes float {
            from {
                transform: translateY(100vh) rotate(0deg);
            }
            to {
                transform: translateY(-100px) rotate(360deg);
            }
        }

        .dashboard-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
            z-index: 10;
        }

        .dashboard-header {
            text-align: center;
            margin-bottom: 3rem;
            color: white;
        }

        .dashboard-header h1 {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            animation: slideInDown 1s ease-out;
        }

        .dashboard-header p {
            font-size: 1.3rem;
            font-weight: 300;
            opacity: 0.9;
            animation: slideInUp 1s ease-out 0.3s both;
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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

        .portal-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .portal-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 2.5rem;
            text-align: center;
            text-decoration: none;
            color: #333;
            box-shadow: var(--portal-shadow);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
            animation: fadeInScale 0.8s ease-out both;
        }

        .portal-card:nth-child(1) {
            animation-delay: 0.1s;
        }

        .portal-card:nth-child(2) {
            animation-delay: 0.3s;
        }

        .portal-card:nth-child(3) {
            animation-delay: 0.5s;
        }

        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.8) translateY(30px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .portal-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .portal-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: var(--portal-hover-shadow);
            color: #333;
            text-decoration: none;
        }

        .portal-card:hover::before {
            opacity: 1;
        }

        .portal-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            padding: 1.5rem;
            border-radius: 50%;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .portal-card:nth-child(1) .portal-icon {
            background: var(--primary-gradient);
            color: white;
        }

        .portal-card:nth-child(2) .portal-icon {
            background: var(--secondary-gradient);
            color: white;
        }

        .portal-card:nth-child(3) .portal-icon {
            background: var(--success-gradient);
            color: white;
        }

        .portal-card:hover .portal-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .portal-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #2c3e50;
        }

        .portal-description {
            font-size: 1rem;
            color: #7f8c8d;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .portal-features {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .portal-features li {
            padding: 0.5rem 0;
            color: #34495e;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .portal-features li i {
            margin-right: 0.5rem;
            color: #27ae60;
        }

        .footer-info {
            position: fixed;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
            z-index: 10;
        }

        @media (max-width: 768px) {
            .dashboard-header h1 {
                font-size: 2.5rem;
            }

            .dashboard-header p {
                font-size: 1.1rem;
            }

            .portal-cards {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .portal-card {
                padding: 2rem 1.5rem;
            }

            .portal-icon {
                font-size: 3rem;
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Animated background particles -->
    <div class="bg-particle" style="width: 80px; height: 80px; left: 10%; animation-delay: 0s;"></div>
    <div class="bg-particle" style="width: 120px; height: 120px; left: 20%; animation-delay: 2s;"></div>
    <div class="bg-particle" style="width: 60px; height: 60px; left: 70%; animation-delay: 4s;"></div>
    <div class="bg-particle" style="width: 90px; height: 90px; left: 80%; animation-delay: 6s;"></div>
    <div class="bg-particle" style="width: 70px; height: 70px; left: 50%; animation-delay: 8s;"></div>
    
    <div class="dashboard-container">
        <div class="container">
            <div class="dashboard-header">
                <h1>Welcome to ERP System</h1>
                <p>Choose your portal to access your personalized dashboard and tools</p>
            </div>
            
            <div class="portal-cards">
                <!-- ERP Portal -->
                <a href="login.php" class="portal-card">
                    <div class="portal-icon">
                        <i class="bi bi-building-fill"></i>
                    </div>
                    <h3 class="portal-title">ERP System</h3>
                    <p class="portal-description">Access the main Enterprise Resource Planning system for comprehensive business management</p>
                    <ul class="portal-features">
                        <li><i class="bi bi-check-circle-fill"></i>Full System Access</li>
                        <li><i class="bi bi-check-circle-fill"></i>Analytics & Reports</li>
                        <li><i class="bi bi-check-circle-fill"></i>User Management</li>
                    </ul>
                </a>

                <!-- Sales Representative Portal -->
                <a href="sales_representative_portal.php" class="portal-card">
                    <div class="portal-icon">
                        <i class="bi bi-person-badge-fill"></i>
                    </div>
                    <h3 class="portal-title">Sales Representative</h3>
                    <p class="portal-description">Dedicated portal for sales representatives to manage leads, orders, and customer relationships</p>
                    <ul class="portal-features">
                        <li><i class="bi bi-check-circle-fill"></i>Lead Management</li>
                        <li><i class="bi bi-check-circle-fill"></i>Order Tracking</li>
                        <li><i class="bi bi-check-circle-fill"></i>Commission Reports</li>
                    </ul>
                </a>

                <!-- Dealership Management Portal -->
                <a href="dealership_management_portal.php" class="portal-card">
                    <div class="portal-icon">
                        <i class="bi bi-shop-window"></i>
                    </div>
                    <h3 class="portal-title">Dealership Management</h3>
                    <p class="portal-description">Comprehensive dealership portal for inventory management, sales tracking, and business operations</p>
                    <ul class="portal-features">
                        <li><i class="bi bi-check-circle-fill"></i>Inventory Control</li>
                        <li><i class="bi bi-check-circle-fill"></i>Sales Analytics</li>
                        <li><i class="bi bi-check-circle-fill"></i>Performance Metrics</li>
                    </ul>
                </a>
            </div>
        </div>
    </div>

    <div class="footer-info">
        <p>&copy; 2024 ERP System. All rights reserved. | Built with modern technology</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add some interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            // Add ripple effect to cards
            const cards = document.querySelectorAll('.portal-card');
            
            cards.forEach(card => {
                card.addEventListener('click', function(e) {
                    const ripple = document.createElement('span');
                    const rect = card.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;
                    
                    ripple.style.cssText = `
                        position: absolute;
                        border-radius: 50%;
                        background: rgba(255, 255, 255, 0.6);
                        width: ${size}px;
                        height: ${size}px;
                        left: ${x}px;
                        top: ${y}px;
                        pointer-events: none;
                        transform: scale(0);
                        animation: ripple 0.6s ease-out;
                    `;
                    
                    card.appendChild(ripple);
                    
                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });
        });

        // Add ripple animation keyframes
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>