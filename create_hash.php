<?php
// This script is for debugging purposes only.
// It generates a secure password hash for a given password.

$passwordToHash = 'admin123';

echo "<h1>Password Hash Generator</h1>";
echo "<p><strong>Password:</strong> " . htmlspecialchars($passwordToHash) . "</p>";

// Generate the hash using PHP's standard password hashing function
$hashedPassword = password_hash($passwordToHash, PASSWORD_DEFAULT);

echo "<p><strong>Generated Hash:</strong></p>";
echo "<textarea rows='3' cols='80' readonly>" . htmlspecialchars($hashedPassword) . "</textarea>";
echo "<p><strong style='color:red;'>Important: Delete this file after you are done!</strong></p>";
?>