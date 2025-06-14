<?php
require_once 'session_check.php';
require_once 'permissions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERP System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    
    <link rel="stylesheet" href="/erp_project/assets/css/style.css">
</head>
<body>

<div class="d-flex" id="wrapper">
    <div class="bg-dark border-right" id="sidebar-wrapper">
        <div class="sidebar-heading text-white">ERP System </div>
        <div class="list-group list-group-flush">
            <a href="/erp_project/index.php" class="list-group-item list-group-item-action bg-dark text-white"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
            
            <?php if (has_permission('hr_view') || has_permission('hr_manage')): ?>
            <a href="/erp_project/modules/hr/view_employees.php" class="list-group-item list-group-item-action bg-dark text-white"><i class="bi bi-person-rolodex me-2"></i>HR</a>
            <?php endif; ?>

            <?php if (has_permission('supplier_view') || has_permission('po_create')): ?>
            <a href="/erp_project/modules/suppliers/view_suppliers.php" class="list-group-item list-group-item-action bg-dark text-white"><i class="bi bi-people-fill me-2"></i>Suppliers</a>
            <?php endif; ?>
            <?php if (has_permission('client_manage')): ?>
            <a href="/erp_project/modules/clients/view_clients.php" class="list-group-item list-group-item-action bg-dark text-white"><i class="bi bi-person-square me-2"></i>Clients</a>
            <?php endif; ?>
            <?php if (has_permission('inventory_view') || has_permission('asset_view') || has_permission('product_view') || has_permission('po_create')): ?>
            <a href="#productSubmenu" data-bs-toggle="collapse" class="list-group-item list-group-item-action bg-dark text-white"><i class="bi bi-box-seam me-2"></i>Products & Inventory</a>
            <div class="collapse" id="productSubmenu">
                <a href="/erp_project/modules/products/view_products.php" class="list-group-item list-group-item-action bg-secondary text-white ps-5">Products</a>
                <a href="/erp_project/modules/products/manage_categories.php" class="list-group-item list-group-item-action bg-secondary text-white ps-5">Categories</a>
                <?php if (has_permission('asset_view') || has_permission('asset_manage')): ?>
                    <a href="/erp_project/modules/assets/view_assets.php" class="list-group-item list-group-item-action bg-secondary text-white ps-5">Manage Assets</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if (has_permission('po_view') || has_permission('po_create')): ?>
            <a href="/erp_project/modules/purchase_orders/view_pos.php" class="list-group-item list-group-item-action bg-dark text-white"><i class="bi bi-receipt me-2"></i>Purchase Orders</a>
            <?php endif; ?>

            <?php if (has_permission('inventory_view') || has_permission('procurement_view')): ?>
            <a href="/erp_project/modules/deliveries/view_deliveries.php" class="list-group-item list-group-item-action bg-dark text-white"><i class="bi bi-truck me-2"></i>Deliveries</a>
            <?php endif; ?>
            
            <?php if (has_permission('finance_view') || has_permission('payment_manage') || has_permission('budget_manage')): ?>
            <a href="#financeSubmenu" data-bs-toggle="collapse" class="list-group-item list-group-item-action bg-dark text-white"><i class="bi bi-cash-coin me-2"></i>Finance</a>
            <div class="collapse" id="financeSubmenu">
                <a href="/erp_project/modules/finance/manage_budgets.php" class="list-group-item list-group-item-action bg-secondary text-white ps-5">Manage Budgets</a>
                <a href="/erp_project/modules/finance/view_payments.php" class="list-group-item list-group-item-action bg-secondary text-white ps-5">All Payments</a>
                <a href="/erp_project/modules/finance/view_invoices.php" class="list-group-item list-group-item-action bg-secondary text-white ps-5">Manage Invoices</a>
            </div>
            <?php endif; ?>
            
            <?php if (has_permission('project_full_access') || has_permission('project_create')): ?>
            <a href="/erp_project/modules/projects/view_projects.php" class="list-group-item list-group-item-action bg-dark text-white"><i class="bi bi-kanban-fill me-2"></i>Projects</a>
            <?php endif; ?>

            <?php if (has_permission('reports_full_access') || has_permission('reports_po_only')): ?>
            <a href="#reportsSubmenu" data-bs-toggle="collapse" class="list-group-item list-group-item-action bg-dark text-white"><i class="bi bi-bar-chart-line-fill me-2"></i>Reports</a>
            <div class="collapse" id="reportsSubmenu">
                <a href="/erp_project/modules/reports/purchase_history.php" class="list-group-item list-group-item-action bg-secondary text-white ps-5">Purchase History</a>
                <a href="/erp_project/modules/reports/supplier_performance.php" class="list-group-item list-group-item-action bg-secondary text-white ps-5">Supplier Performance</a>
            </div>
            <?php endif; ?>
            
            <?php if (has_permission('user_manage') || has_permission('supplier_info_approve')): ?>
            <a href="#adminSubmenu" data-bs-toggle="collapse" class="list-group-item list-group-item-action bg-dark text-white"><i class="bi bi-person-badge-fill me-2"></i>Administration</a>
            <div class="collapse" id="adminSubmenu">
                <?php if (has_permission('user_manage')): ?><a href="/erp_project/modules/admin/manage_users.php" class="list-group-item list-group-item-action bg-secondary text-white ps-5">Manage Users</a><?php endif; ?>
                <?php if (has_permission('supplier_info_approve')): ?><a href="/erp_project/modules/admin/review_supplier_changes.php" class="list-group-item list-group-item-action bg-secondary text-white ps-5">Supplier Changes</a><?php endif; ?>
                <a href="/erp_project/modules/admin/view_audit_log.php" class="list-group-item list-group-item-action bg-secondary text-white ps-5">Audit Log</a>
                <a href="/erp_project/modules/admin/import_products.php" class="list-group-item list-group-item-action bg-secondary text-white ps-5">Import Products</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <div id="page-content-wrapper" class="flex-grow-1">
        <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
            <div class="container-fluid">
                <button class="btn btn-primary" id="menu-toggle"><i class="bi bi-list"></i></button>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"><span class="navbar-toggler-icon"></span></button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
                        <li class="nav-item dropdown">
                            <a class="nav-link" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-bell-fill"></i><span class="position-absolute top-1 start-100 translate-middle badge rounded-pill bg-danger" id="notification-count" style="display: none;"></span></a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown" id="notification-list">
                                <li><a class="dropdown-item text-muted text-center" href="#">No new notifications</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['username']); ?> (<?php echo htmlspecialchars($_SESSION['role_name']); ?>)
                            </a>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="/erp_project/modules/profile/">My Profile</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="/erp_project/logout.php">Logout</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="container-fluid">