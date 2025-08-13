<?php
$page_title = "Preview PDF Import";
include('../../includes/header.php');
include('../../includes/db.php');
include('../../includes/permissions.php');
if (!has_permission(['Manager','Procurement Officer'])) { header('Location: /erp_project/index.php?status=access_denied'); exit(); }

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['pdf_file'])) { header('Location: bulk_import.php'); exit(); }

// Validate upload
$err = $_FILES['pdf_file']['error'] ?? UPLOAD_ERR_NO_FILE;
if ($err !== UPLOAD_ERR_OK) {
  $msg = 'Unknown error.';
  switch ($err) {
    case UPLOAD_ERR_INI_SIZE:
    case UPLOAD_ERR_FORM_SIZE: $msg = 'File too large. Increase upload_max_filesize/post_max_size or upload a smaller file.'; break;
    case UPLOAD_ERR_PARTIAL: $msg = 'Upload interrupted. Please try again.'; break;
    case UPLOAD_ERR_NO_FILE: $msg = 'No file uploaded.'; break;
    default: $msg = 'Upload error code: '.$err; break;
  }
  echo '<div class="alert alert-danger">'.$msg.'</div><a class="btn btn-secondary" href="/erp_project/modules/products/bulk_import.php">Back</a>';
  include('../../includes/footer.php');
  exit();
}

$tmp = $_FILES['pdf_file']['tmp_name'];
if (!is_uploaded_file($tmp)) { echo '<div class="alert alert-danger">Upload failed or was blocked by server policy.</div>'; include('../../includes/footer.php'); exit(); }

// Try to load parser
$text = '';
$haveParser = false;
try {
  if (@include_once __DIR__.'/../../vendor/autoload.php') {
    if (class_exists('Smalot\\PdfParser\\Parser')) {
      $haveParser = true;
      $parser = new Smalot\PdfParser\Parser();
      $pdf = $parser->parseFile($tmp);
      $text = $pdf->getText();
    }
  }
} catch (Throwable $e) { $text = ''; }

// Fallback to system pdftotext (if installed), try with -layout to preserve columns
if ($text === '' || strlen(trim($text)) === 0) {
  $out = tempnam(sys_get_temp_dir(), 'pdf');
  @exec('pdftotext -layout '.escapeshellarg($tmp).' '.escapeshellarg($out));
  if (file_exists($out)) { $alt = @file_get_contents($out); @unlink($out); if ($alt) $text = $alt; }
}

if ($text === '' || strlen(trim($text)) === 0) {
  echo '<div class="alert alert-warning"><strong>Could not extract text from the PDF.</strong> ';
  if (!$haveParser) echo 'PDF parser is not loaded. ';
  echo 'If this is a scanned PDF (images), please run OCR first or export as a text-based/table PDF. Installing <code>pdftotext</code> on the server also helps.</div>';
  echo '<a class="btn btn-secondary" href="/erp_project/modules/products/bulk_import.php"><i class="bi bi-arrow-left me-2"></i>Back</a>';
  include('../../includes/footer.php');
  exit();
}

// Normalize whitespace
$text = preg_replace("/\xC2\xA0/", ' ', $text); // non-breaking spaces
$text = preg_replace('/\t+/', ' ', $text);

$lines = preg_split('/\r\n|\n|\r/', $text);
$rows = [];
foreach ($lines as $line) {
  $line = trim(preg_replace('/\s+/', ' ', $line));
  if ($line === '' || strlen($line) < 5) continue;
  $rows[] = explode(' ', $line);
  if (count($rows) >= 50) break; // cap preview
}
// determine max columns to build a simple preview table
$maxCols = 0; foreach ($rows as $r) { $maxCols = max($maxCols, count($r)); }
if ($maxCols === 0) {
  echo '<div class="alert alert-warning">No tabular text could be detected in the first 50 lines of the PDF. Please verify the file or try another export format.</div>';
  echo '<a class="btn btn-secondary" href="/erp_project/modules/products/bulk_import.php"><i class="bi bi-arrow-left me-2"></i>Back</a>';
  include('../../includes/footer.php');
  exit();
}
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h1><?php echo $page_title; ?></h1>
  <a href="/erp_project/modules/products/bulk_import.php" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a>
</div>
<div class="card mb-3">
  <div class="card-header"><h5>Step 1 of 2: Map Columns</h5></div>
  <div class="card-body">
    <p class="text-secondary">Map the detected columns to product fields. You can skip columns that aren't needed.</p>
    <form action="confirm_pdf_import.php" method="POST">
      <input type="hidden" name="token" value="<?php echo htmlspecialchars(bin2hex(random_bytes(8))); ?>">
      <div class="table-responsive">
        <table class="table table-sm table-bordered align-middle">
          <thead>
            <tr>
              <?php for ($c=0; $c<$maxCols; $c++): ?>
                <th>
                  <select name="map[<?php echo $c; ?>]" class="form-select form-select-sm">
                    <option value="">-- Skip --</option>
                    <option value="sku">SKU</option>
                    <option value="product_name">Product Name</option>
                    <option value="category_name">Category</option>
                    <option value="price">Price</option>
                    <option value="quantity">Quantity</option>
                    <option value="description">Description</option>
                  </select>
                </th>
              <?php endfor; ?>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rows as $r): ?>
              <tr>
                <?php for ($c=0; $c<$maxCols; $c++): ?>
                  <td><code class="small"><?php echo htmlspecialchars($r[$c] ?? ''); ?></code></td>
                <?php endfor; ?>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="mb-3">
        <label class="form-label">Assume first column is SKU and last two numeric are Price & Quantity</label>
        <div class="form-text">You can override using the dropdowns above.</div>
      </div>
      <div class="d-grid d-md-flex justify-content-md-end">
        <button class="btn btn-primary" type="submit"><i class="bi bi-check2-circle me-2"></i>Confirm Mapping</button>
      </div>
      <textarea name="raw_text" class="d-none"><?php echo htmlspecialchars($text); ?></textarea>
    </form>
  </div>
</div>
<?php include('../../includes/footer.php'); ?> 