<?php
$page_title = "Financial Reports";
include('../../includes/header.php');
if (!has_permission('finance_view')) { header('Location: /erp_project/index.php?status=access_denied'); exit(); }
?>
<div class="d-flex justify-content-between align-items-center mb-4"><h1><?php echo $page_title; ?></h1></div>
<div class="row g-3">
  <div class="col-md-4">
    <div class="card h-100">
      <div class="card-body">
        <h5>Trial Balance</h5>
        <p class="text-secondary">Assets, liabilities, equity, revenues, and expenses consolidated by account.</p>
        <a class="btn btn-outline-primary" href="/erp_project/modules/accounts/report_trial_balance.php">View</a>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card h-100">
      <div class="card-body">
        <h5>Income Statement</h5>
        <p class="text-secondary">Revenue and expense summary to compute net income for a period.</p>
        <a class="btn btn-outline-primary" href="/erp_project/modules/accounts/report_income_statement.php">View</a>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card h-100">
      <div class="card-body">
        <h5>Balance Sheet</h5>
        <p class="text-secondary">Financial position snapshot: assets, liabilities, equity.</p>
        <a class="btn btn-outline-primary" href="/erp_project/modules/accounts/report_balance_sheet.php">View</a>
      </div>
    </div>
  </div>
</div>
<?php include('../../includes/footer.php'); ?> 