<?php
$page_title = "Bulk Import Products";
include('../../includes/header.php');
include('../../includes/db.php');

if (!has_permission(['Manager','Procurement Officer'])) { header('Location: /erp_project/dashboard.php?status=access_denied'); exit(); }

$status = $_GET['status'] ?? '';
$created = $_GET['created'] ?? '';
$updated = $_GET['updated'] ?? '';
$skipped = $_GET['skipped'] ?? '';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h1><?php echo $page_title; ?></h1>
  <a href="/erp_project/modules/products/view_products.php" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a>
</div>
<?php if ($status): ?>
  <?php if ($status==='error_cols'): ?>
    <div class="alert alert-danger">CSV columns missing. Expected: product_name, sku, category_name, price, quantity_in_stock[, description, reorder_point]</div>
  <?php elseif ($status==='error'): ?>
    <div class="alert alert-danger">Upload failed. File may be too large or invalid. Check PHP upload_max_filesize/post_max_size and try again.</div>
  <?php elseif ($status==='pdf_parse_error'): ?>
    <div class="alert alert-warning">Could not extract text from the PDF. If this is a scanned PDF, please OCR it first or export a text-based PDF. Installing <code>pdftotext</code> also helps.</div>
  <?php elseif ($status==='bulk_done' || $status==='pdf_bulk_done' || $status==='pdf_confirm_done'): ?>
    <div class="alert alert-success">Import complete. Created: <strong><?php echo htmlspecialchars($created); ?></strong>, Updated: <strong><?php echo htmlspecialchars($updated); ?></strong>, Skipped: <strong><?php echo htmlspecialchars($skipped); ?></strong></div>
  <?php endif; ?>
<?php endif; ?>
<div class="row g-4">
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header"><h5>Upload CSV</h5></div>
      <div class="card-body">
        <p class="text-secondary">Download the sample template, fill in your product data, then upload the CSV file.</p>
        <a href="/erp_project/modules/products/sample_products_template.csv" class="btn btn-outline-secondary btn-sm mb-3"><i class="bi bi-filetype-csv me-2"></i>Download Sample CSV</a>
        <form action="handle_bulk_import_csv.php" method="POST" enctype="multipart/form-data">
          <div class="mb-3">
            <label class="form-label">CSV File *</label>
            <input type="file" name="csv_file" accept=".csv" class="form-control" required>
          </div>
          <div class="form-text">Columns: product_name, sku, category_name, price, quantity_in_stock, description, reorder_point</div>
          <div class="d-grid d-md-flex justify-content-md-end"><button class="btn btn-success" type="submit"><i class="bi bi-upload me-2"></i>Import CSV</button></div>
        </form>
      </div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header"><h5>Upload PDF (Preview → Confirm)</h5></div>
      <div class="card-body">
        <p class="text-secondary">Upload a PDF product list. You'll preview and map columns before import.</p>
        <form action="preview_pdf_import.php" method="POST" enctype="multipart/form-data">
          <div class="mb-3">
            <label class="form-label">PDF File *</label>
            <input type="file" name="pdf_file" accept="application/pdf" class="form-control" required>
          </div>
          <div class="d-grid d-md-flex justify-content-md-end"><button class="btn btn-primary" type="submit"><i class="bi bi-eye me-2"></i>Preview</button></div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include('../../includes/footer.php'); ?> 