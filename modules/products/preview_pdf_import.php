<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$page_title = "Preview PDF Import";
include('../../includes/header.php');
include('../../includes/db.php');
include('../../includes/permissions.php');

echo "<h1>Debug: Page loaded successfully</h1>";

if (!has_permission(['Manager','Procurement Officer'])) { 
    echo "<div class='alert alert-danger'>Permission denied</div>";
    header('Location: /erp_project/dashboard.php?status=access_denied'); 
    exit(); 
}

echo "<p>Debug: Permissions OK</p>";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<div class='alert alert-warning'>No POST request</div>";
    echo '<a class="btn btn-secondary" href="/erp_project/modules/products/bulk_import.php">Back to Bulk Import</a>';
    include('../../includes/footer.php');
    exit();
}

echo "<p>Debug: POST request received</p>";

if (!isset($_FILES['pdf_file'])) {
    echo "<div class='alert alert-warning'>No file uploaded</div>";
    echo '<a class="btn btn-secondary" href="/erp_project/modules/products/bulk_import.php">Back to Bulk Import</a>';
    include('../../includes/footer.php');
    exit();
}

echo "<p>Debug: File upload detected</p>";
echo "<pre>File info: " . print_r($_FILES['pdf_file'], true) . "</pre>";

// Check upload error
$err = $_FILES['pdf_file']['error'] ?? UPLOAD_ERR_NO_FILE;
echo "<p>Upload error code: $err</p>";

if ($err !== UPLOAD_ERR_OK) {
    $msg = 'Unknown error.';
    switch ($err) {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE: 
            $msg = 'File too large. Current limits: ' . ini_get('upload_max_filesize') . ' / ' . ini_get('post_max_size'); 
            break;
        case UPLOAD_ERR_PARTIAL: 
            $msg = 'Upload interrupted. Please try again.'; 
            break;
        case UPLOAD_ERR_NO_FILE: 
            $msg = 'No file uploaded.'; 
            break;
        default: 
            $msg = 'Upload error code: '.$err; 
            break;
    }
    echo '<div class="alert alert-danger">' . $msg . '</div>';
    echo '<a class="btn btn-secondary" href="/erp_project/modules/products/bulk_import.php">Back</a>';
    include('../../includes/footer.php');
    exit();
}

echo "<p>Debug: Upload successful, proceeding with file processing...</p>";

$tmp = $_FILES['pdf_file']['tmp_name'];
echo "<p>Temp file: $tmp</p>";
echo "<p>File exists: " . (file_exists($tmp) ? 'YES' : 'NO') . "</p>";
echo "<p>File size: " . filesize($tmp) . " bytes</p>";

if (!is_uploaded_file($tmp)) { 
    echo '<div class="alert alert-danger">Upload validation failed</div>'; 
    echo '<a class="btn btn-secondary" href="/erp_project/modules/products/bulk_import.php">Back</a>';
    include('../../includes/footer.php'); 
    exit(); 
}

echo "<p>Debug: File validation passed</p>";

// Try to load composer autoloader
echo "<p>Checking for vendor/autoload.php...</p>";
$autoload_path = __DIR__.'/../../vendor/autoload.php';
echo "<p>Autoload path: $autoload_path</p>";
echo "<p>Autoload exists: " . (file_exists($autoload_path) ? 'YES' : 'NO') . "</p>";

$text = '';
$haveParser = false;

try {
    if (file_exists($autoload_path)) {
        require_once $autoload_path;
        echo "<p>Autoloader loaded</p>";
        
        if (class_exists('Smalot\\PdfParser\\Parser')) {
            echo "<p>PDF Parser class found</p>";
            $haveParser = true;
            $parser = new Smalot\PdfParser\Parser();
            echo "<p>Parser instantiated</p>";
            $pdf = $parser->parseFile($tmp);
            echo "<p>PDF parsed</p>";
            $text = $pdf->getText();
            echo "<p>Text extracted, length: " . strlen($text) . "</p>";
        } else {
            echo "<p>PDF Parser class NOT found</p>";
        }
    } else {
        echo "<p>Autoloader file does not exist</p>";
    }
} catch (Throwable $e) { 
    echo "<p>Exception: " . $e->getMessage() . "</p>";
    $text = ''; 
}

echo "<p>Extracted text preview (first 500 chars):</p>";
echo "<pre>" . htmlspecialchars(substr($text, 0, 500)) . "</pre>";

if (empty(trim($text))) {
    echo '<div class="alert alert-warning">No text could be extracted from the PDF</div>';
    echo '<a class="btn btn-secondary" href="/erp_project/modules/products/bulk_import.php">Back</a>';
} else {
    echo '<div class="alert alert-success">Text extraction successful!</div>';
    echo '<a class="btn btn-secondary" href="/erp_project/modules/products/bulk_import.php">Back</a>';
}

include('../../includes/footer.php');
?> 