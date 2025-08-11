<?php
// Financial Dashboard Widget - Reusable component for financial metrics
// This file provides a compact financial overview that can be embedded in various pages

include_once(dirname(__DIR__, 2) . '/includes/db.php');
include_once(dirname(__DIR__, 2) . '/includes/accounts_schema.php');

if (!function_exists('connect_db')) {
    throw new Exception('Database connection function not available');
}

$conn = connect_db();
ensure_accounts_schema($conn);

// Quick Financial Metrics
$financial_metrics = [
    'total_assets' => 0,
    'total_liabilities' => 0,
    'total_equity' => 0,
    'monthly_revenue' => 0,
    'monthly_expenses' => 0,
    'cash_flow' => 0
];

// Calculate total assets (positive balances)
$sql_assets = "SELECT SUM(opening_balance) as total_assets FROM chart_of_accounts WHERE account_type = 'Asset' AND is_active = 1";
$result = $conn->query($sql_assets);
if ($result && $row = $result->fetch_assoc()) {
    $financial_metrics['total_assets'] = floatval($row['total_assets'] ?? 0);
}

// Calculate total liabilities (positive balances)
$sql_liabilities = "SELECT SUM(opening_balance) as total_liabilities FROM chart_of_accounts WHERE account_type = 'Liability' AND is_active = 1";
$result = $conn->query($sql_liabilities);
if ($result && $row = $result->fetch_assoc()) {
    $financial_metrics['total_liabilities'] = floatval($row['total_liabilities'] ?? 0);
}

// Calculate total equity
$sql_equity = "SELECT SUM(opening_balance) as total_equity FROM chart_of_accounts WHERE account_type = 'Equity' AND is_active = 1";
$result = $conn->query($sql_equity);
if ($result && $row = $result->fetch_assoc()) {
    $financial_metrics['total_equity'] = floatval($row['total_equity'] ?? 0);
}

// Calculate monthly revenue from AR invoices
$sql_revenue = "SELECT SUM(total) as monthly_revenue FROM ar_invoices WHERE MONTH(invoice_date) = MONTH(CURDATE()) AND YEAR(invoice_date) = YEAR(CURDATE())";
$result = $conn->query($sql_revenue);
if ($result && $row = $result->fetch_assoc()) {
    $financial_metrics['monthly_revenue'] = floatval($row['monthly_revenue'] ?? 0);
}

// Calculate monthly expenses from AP bills
$sql_expenses = "SELECT SUM(total) as monthly_expenses FROM ap_bills WHERE MONTH(bill_date) = MONTH(CURDATE()) AND YEAR(bill_date) = YEAR(CURDATE())";
$result = $conn->query($sql_expenses);
if ($result && $row = $result->fetch_assoc()) {
    $financial_metrics['monthly_expenses'] = floatval($row['monthly_expenses'] ?? 0);
}

// Calculate cash flow (revenue - expenses)
$financial_metrics['cash_flow'] = $financial_metrics['monthly_revenue'] - $financial_metrics['monthly_expenses'];

// Get recent transactions summary
$sql_recent = "
    SELECT 'AR' as type, invoice_date as date, total as amount, 'Invoice' as description
    FROM ar_invoices 
    WHERE invoice_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAYS)
    
    UNION ALL
    
    SELECT 'AP' as type, bill_date as date, total as amount, 'Bill' as description  
    FROM ap_bills
    WHERE bill_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAYS)
    
    ORDER BY date DESC
    LIMIT 5";

$recent_transactions = [];
$result = $conn->query($sql_recent);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $recent_transactions[] = $row;
    }
}

$conn->close();
?>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Financial Overview</h5>
        <small class="text-muted">As of <?php echo date('M j, Y'); ?></small>
    </div>
    <div class="card-body">
        <!-- Financial Health Indicators -->
        <div class="row text-center mb-4">
            <div class="col-md-4">
                <div class="border-end">
                    <h6 class="text-success">Assets</h6>
                    <h4 class="fw-bold">$<?php echo number_format($financial_metrics['total_assets'], 0); ?></h4>
                </div>
            </div>
            <div class="col-md-4">
                <div class="border-end">
                    <h6 class="text-warning">Liabilities</h6>
                    <h4 class="fw-bold">$<?php echo number_format($financial_metrics['total_liabilities'], 0); ?></h4>
                </div>
            </div>
            <div class="col-md-4">
                <h6 class="text-info">Equity</h6>
                <h4 class="fw-bold">$<?php echo number_format($financial_metrics['total_equity'], 0); ?></h4>
            </div>
        </div>

        <!-- Monthly Performance -->
        <div class="row text-center mb-4">
            <div class="col-md-4">
                <div class="bg-light rounded p-3">
                    <i class="bi bi-arrow-up-circle text-success fs-3"></i>
                    <div class="mt-2">
                        <small class="text-muted">Revenue This Month</small>
                        <h5 class="text-success fw-bold">$<?php echo number_format($financial_metrics['monthly_revenue'], 0); ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bg-light rounded p-3">
                    <i class="bi bi-arrow-down-circle text-danger fs-3"></i>
                    <div class="mt-2">
                        <small class="text-muted">Expenses This Month</small>
                        <h5 class="text-danger fw-bold">$<?php echo number_format($financial_metrics['monthly_expenses'], 0); ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bg-light rounded p-3">
                    <i class="bi bi-graph-<?php echo $financial_metrics['cash_flow'] >= 0 ? 'up' : 'down'; ?> text-<?php echo $financial_metrics['cash_flow'] >= 0 ? 'success' : 'danger'; ?> fs-3"></i>
                    <div class="mt-2">
                        <small class="text-muted">Net Cash Flow</small>
                        <h5 class="text-<?php echo $financial_metrics['cash_flow'] >= 0 ? 'success' : 'danger'; ?> fw-bold">
                            $<?php echo number_format(abs($financial_metrics['cash_flow']), 0); ?>
                        </h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <?php if (!empty($recent_transactions)): ?>
        <div class="border-top pt-3">
            <h6 class="mb-3">Recent Activity (Last 7 Days)</h6>
            <div class="list-group list-group-flush">
                <?php foreach ($recent_transactions as $transaction): ?>
                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <div>
                        <span class="badge bg-<?php echo $transaction['type'] === 'AR' ? 'success' : 'warning'; ?> me-2">
                            <?php echo $transaction['type']; ?>
                        </span>
                        <?php echo $transaction['description']; ?>
                        <small class="text-muted d-block"><?php echo date('M j, Y', strtotime($transaction['date'])); ?></small>
                    </div>
                    <strong class="text-<?php echo $transaction['type'] === 'AR' ? 'success' : 'warning'; ?>">
                        $<?php echo number_format($transaction['amount'], 0); ?>
                    </strong>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Quick Actions -->
        <div class="border-top pt-3 mt-3">
            <div class="row text-center">
                <?php if (has_permission('budget_manage')): ?>
                <div class="col-6 col-md-3 mb-2">
                    <a href="/erp_project/modules/accounts/add_account.php" class="btn btn-outline-primary btn-sm w-100">
                        <i class="bi bi-plus-circle me-1"></i>Add Account
                    </a>
                </div>
                <?php endif; ?>
                <div class="col-6 col-md-3 mb-2">
                    <a href="/erp_project/modules/accounts/general_ledger.php" class="btn btn-outline-secondary btn-sm w-100">
                        <i class="bi bi-ledger me-1"></i>Ledger
                    </a>
                </div>
                <div class="col-6 col-md-3 mb-2">
                    <a href="/erp_project/modules/accounts/accounts_receivable.php" class="btn btn-outline-success btn-sm w-100">
                        <i class="bi bi-receipt me-1"></i>A/R
                    </a>
                </div>
                <div class="col-6 col-md-3 mb-2">
                    <a href="/erp_project/modules/accounts/accounts_payable.php" class="btn btn-outline-warning btn-sm w-100">
                        <i class="bi bi-receipt-cutoff me-1"></i>A/P
                    </a>
                </div>
            </div>
        </div>
    </div>
</div> 