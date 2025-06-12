<?php
include('../../includes/db.php');

// Redirect URL in case of error or success
$redirect_url = "view_supplier_details.php?id=";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Only allow POST requests
    header('Location: view_suppliers.php'); // Redirect to main list if accessed directly
    exit();
}

// 1. Validate basic form data
if (!isset($_POST['supplier_id'], $_POST['contract_title'], $_POST['start_date'], $_POST['end_date'])) {
    die("Required form data is missing.");
}

$supplier_id = $_POST['supplier_id'];
$redirect_url .= $supplier_id; // Append supplier id for redirects

// 2. File Upload Handling and Validation
if (isset($_FILES['contract_file']) && $_FILES['contract_file']['error'] == 0) {
    $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    $max_size = 5 * 1024 * 1024; // 5 MB

    $file_type = $_FILES['contract_file']['type'];
    $file_size = $_FILES['contract_file']['size'];

    if (!in_array($file_type, $allowed_types)) {
        header("Location: " . $redirect_url . "&status=contract_error");
        exit('Invalid file type.');
    }

    if ($file_size > $max_size) {
        header("Location: " . $redirect_url . "&status=contract_error");
        exit('File size exceeds the 5MB limit.');
    }

    // 3. Create a unique filename and define the path
    $original_name = $_FILES['contract_file']['name'];
    $file_extension = pathinfo($original_name, PATHINFO_EXTENSION);
    $unique_name = uniqid('contract_', true) . '.' . $file_extension;
    $upload_path = '../../uploads/contracts/' . $unique_name;
    $db_path = 'uploads/contracts/' . $unique_name; // Path to store in DB

    // 4. Move the file to the uploads directory
    if (move_uploaded_file($_FILES['contract_file']['tmp_name'], $upload_path)) {
        // 5. If file move is successful, insert record into the database
        $conn = connect_db();
        $sql = "INSERT INTO supplier_contracts (supplier_id, contract_title, file_path, start_date, end_date) VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "issss", 
            $supplier_id, 
            $_POST['contract_title'], 
            $db_path, 
            $_POST['start_date'], 
            $_POST['end_date']
        );

        if ($stmt->execute()) {
            // Success
            $stmt->close();
            $conn->close();
            header("Location: " . $redirect_url . "&status=contract_success");
            exit();
        }
    }
}

// If we reach here, something went wrong
header("Location: " . $redirect_url . "&status=contract_error");
exit();

?>