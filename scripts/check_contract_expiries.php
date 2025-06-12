<?php
// This script is designed to be run automatically by a server on a schedule (a "cron job")

// Include the Composer autoloader to load PHPMailer
require '../vendor/autoload.php';
// Include our database connection
require '../includes/db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// --- SMTP Configuration (Update with your own details) ---
// It's highly recommended to use an app-specific password if you're using Gmail.
// --- SMTP Configuration (Update with your own details) ---
// It's highly recommended to use an app-specific password if you're using Gmail.
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USERNAME', 'rohan15-5910@diu.edu.bd'); // This is correct.
define('SMTP_PASSWORD', 'sxomtqclhljhkpgm'); // CORRECT: The App Password with spaces removed.
define('SMTP_PORT', 587); // Or 465 for SSL
define('SMTP_SECURE', PHPMailer::ENCRYPTION_STARTTLS); // Or PHPMailer::ENCRYPTION_SMTPS

// Who the email is from and who it should be sent to
define('EMAIL_FROM_ADDRESS', 'rohan15-5910@diu.edu.bd'); // CORRECT: Must match SMTP_USERNAME.
define('EMAIL_FROM_NAME', 'ERP System Alert');
define('EMAIL_TO_ADDRESS', 'forinsta5910@gmail.com'); // IMPORTANT: Set this to the email address where you want to RECEIVE the alerts.

echo "Running Contract Expiry Check at " . date('Y-m-d H:i:s') . "\n";

$conn = connect_db();

// Find contracts expiring in the next 30 days that haven't expired yet
$sql = "SELECT s.supplier_name, c.contract_title, c.end_date
        FROM supplier_contracts c
        JOIN suppliers s ON c.supplier_id = s.id
        WHERE c.end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $mail = new PHPMailer(true);

    try {
        // --- Server settings ---
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port       = SMTP_PORT;

        // --- Recipients ---
        $mail->setFrom(EMAIL_FROM_ADDRESS, EMAIL_FROM_NAME);
        $mail->addAddress(EMAIL_TO_ADDRESS);

        // --- Content ---
        $mail->isHTML(true);
        $mail->Subject = 'Upcoming Contract Expiry Notification';
        
        $email_body = "<h3>The following supplier contracts are expiring soon:</h3><ul>";

        while ($row = $result->fetch_assoc()) {
            $expiry_date = date("F j, Y", strtotime($row['end_date']));
            $email_body .= "<li><strong>" . htmlspecialchars($row['contract_title']) . "</strong> for supplier <em>" . htmlspecialchars($row['supplier_name']) . "</em> will expire on <strong>" . $expiry_date . "</strong>.</li>";
        }
        $email_body .= "</ul><p>Please take the necessary action.</p>";

        $mail->Body = $email_body;

        $mail->send();
        echo 'Alert email has been sent successfully!';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
} else {
    echo 'No expiring contracts found in the next 30 days.';
}

$conn->close();
?>