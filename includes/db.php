<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Your XAMPP MySQL password is blank by default
define('DB_NAME', 'erp_db');

/**
 * Establishes a connection to the database.
 * @return mysqli|false The mysqli connection object on success, or false on failure.
 */
function connect_db() {
    // Create connection
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Check connection
    if ($conn->connect_error) {
        // In a real application, you would log this error, not display it publicly
        die("Connection failed: " . $conn->connect_error);
    }

    // Set character set to utf8mb4 for full Unicode support (including Bangla)
    $conn->set_charset("utf8mb4");

    return $conn;
}

// You can create the database 'erp_db' in phpMyAdmin
// and then test the connection by uncommenting the line below:
// connect_db();
?>