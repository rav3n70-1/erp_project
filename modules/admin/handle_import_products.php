<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');

if (!has_permission('user_manage')) { // Using user_manage as a proxy permission for this admin task
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: import_products.php');
    exit();
}

// Check if file was uploaded without errors
if (!isset($_FILES['product_csv']) || $_FILES['product_csv']['error'] != 0) {
    header("Location: import_products.php?status=error&msg=upload_failed");
    exit();
}

$file = $_FILES['product_csv'];
$allowed_mime_types = ['text/csv', 'text/plain', 'application/csv'];

// Validate file type
if (!in_array($file['type'], $allowed_mime_types)) {
    header("Location: import_products.php?status=error&msg=file_type");
    exit();
}

$conn = connect_db();
$conn->begin_transaction();

try {
    // --- Pre-load existing data for faster validation ---
    // Get all existing SKUs to check for duplicates
    $existing_skus = [];
    $sku_result = $conn->query("SELECT sku FROM products");
    while ($row = $sku_result->fetch_assoc()) {
        $existing_skus[] = $row['sku'];
    }

    // Get all existing categories to map names to IDs
    $categories = [];
    $cat_result = $conn->query("SELECT id, category_name FROM product_categories");
    while ($row = $cat_result->fetch_assoc()) {
        $categories[strtolower($row['category_name'])] = $row['id'];
    }

    $file_handle = fopen($file['tmp_name'], 'r');
    $row_number = 0;
    $products_to_insert = [];

    // --- First Pass: Read and Validate the entire CSV file ---
    while (($data = fgetcsv($file_handle, 1000, ',')) !== FALSE) {
        $row_number++;
        if ($row_number == 1) {
            // Skip header row
            continue;
        }

        // Validate column count
        if (count($data) != 5) {
            throw new Exception("Incorrect column count.", $row_number);
        }

        $sku = trim($data[0]);
        $product_name = trim($data[1]);
        $description = trim($data[2]);
        $price = trim($data[3]);
        $category_name = trim($data[4]);

        // --- Data Validation Checks ---
        if (empty($sku) || empty($product_name) || empty($price) || empty($category_name)) {
            throw new Exception("Required fields (SKU, ProductName, Price, CategoryName) cannot be empty.", $row_number);
        }
        if (in_array($sku, $existing_skus)) {
            throw new Exception("SKU '" . $sku . "' already exists.", $row_number);
        }
        if (!is_numeric($price)) {
            throw new Exception("Price must be a number.", $row_number);
        }
        $category_name_lower = strtolower($category_name);
        if (!isset($categories[$category_name_lower])) {
            throw new Exception("Category '" . $category_name . "' not found.", $row_number);
        }
        
        // If all validation passes, add the product to our array for insertion
        $products_to_insert[] = [
            'sku' => $sku,
            'product_name' => $product_name,
            'description' => $description,
            'price' => $price,
            'category_id' => $categories[$category_name_lower]
        ];
        
        // Add the new SKU to our list to prevent duplicates within the same file
        $existing_skus[] = $sku;
    }
    fclose($file_handle);

    // --- Second Pass: If validation passed for all rows, insert into database ---
    if (!empty($products_to_insert)) {
        $sql = "INSERT INTO products (sku, product_name, description, price, category_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        foreach ($products_to_insert as $product) {
            $stmt->bind_param(
                "sssdi", 
                $product['sku'], 
                $product['product_name'], 
                $product['description'], 
                $product['price'], 
                $product['category_id']
            );
            $stmt->execute();
        }
        $stmt->close();
    }

    // If we reached here, everything is okay. Commit the transaction.
    $conn->commit();
    $imported_count = count($products_to_insert);
    header("Location: import_products.php?status=success&count=" . $imported_count);

} catch (Exception $e) {
    // If any validation step fails, roll back the entire transaction
    $conn->rollback();
    $line_number = $e->getCode();
    $reason = urlencode($e->getMessage());
    header("Location: import_products.php?status=error&msg=validation&line=" . $line_number . "&reason=" . $reason);
}

$conn->close();
exit();
?>