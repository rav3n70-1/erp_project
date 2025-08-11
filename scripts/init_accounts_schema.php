<?php
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/accounts_schema.php';

echo "Initializing Accounts schema...\n";
$conn = connect_db();
ensure_accounts_schema($conn);
echo "Accounts schema ensured/updated.\n";
$conn->close();
?> 