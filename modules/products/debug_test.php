<?php
// Simple diagnostic test
echo "1. Basic PHP working<br>";

// Test error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
echo "2. Error reporting enabled<br>";

// Test include paths
echo "3. Current directory: " . __DIR__ . "<br>";
echo "4. Header path exists: " . (file_exists('../../includes/header.php') ? 'YES' : 'NO') . "<br>";
echo "5. DB path exists: " . (file_exists('../../includes/db.php') ? 'YES' : 'NO') . "<br>";

// Test basic includes
try {
    echo "6. Attempting to include header...<br>";
    include('../../includes/header.php');
    echo "7. Header included successfully<br>";
} catch (Exception $e) {
    echo "7. Header include failed: " . $e->getMessage() . "<br>";
}

try {
    echo "8. Attempting to include db...<br>";
    include('../../includes/db.php');
    echo "9. DB included successfully<br>";
} catch (Exception $e) {
    echo "9. DB include failed: " . $e->getMessage() . "<br>";
}

// Test database connection
try {
    echo "10. Attempting database connection...<br>";
    $conn = connect_db();
    echo "11. Database connected successfully<br>";
    
    // Test basic query
    $result = $conn->query("SELECT COUNT(*) as count FROM products");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "12. Products table query successful - count: " . $row['count'] . "<br>";
    } else {
        echo "12. Products table query failed: " . $conn->error . "<br>";
    }
    
    $conn->close();
} catch (Exception $e) {
    echo "11. Database connection failed: " . $e->getMessage() . "<br>";
}

echo "13. Test completed<br>";
?> 