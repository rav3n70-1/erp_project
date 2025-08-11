<?php
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/accounts_schema.php';

echo "Initializing sample accounts data...\n";

$conn = connect_db();
ensure_accounts_schema($conn);

// Sample Chart of Accounts
$sample_accounts = [
    ['1000', 'Cash', 'Asset', null, 1, 10000.00],
    ['1010', 'Checking Account', 'Asset', '1000', 1, 8000.00],
    ['1020', 'Savings Account', 'Asset', '1000', 1, 2000.00],
    ['1100', 'Accounts Receivable', 'Asset', null, 1, 15000.00],
    ['1200', 'Inventory', 'Asset', null, 1, 25000.00],
    ['1500', 'Equipment', 'Asset', null, 1, 50000.00],
    ['2000', 'Accounts Payable', 'Liability', null, 1, 8000.00],
    ['2100', 'Accrued Expenses', 'Liability', null, 1, 3000.00],
    ['2200', 'Notes Payable', 'Liability', null, 1, 20000.00],
    ['3000', 'Owner\'s Equity', 'Equity', null, 1, 50000.00],
    ['3100', 'Retained Earnings', 'Equity', null, 1, 19000.00],
    ['4000', 'Sales Revenue', 'Revenue', null, 1, 0.00],
    ['4100', 'Service Revenue', 'Revenue', null, 1, 0.00],
    ['5000', 'Cost of Goods Sold', 'Expense', null, 1, 0.00],
    ['6000', 'Operating Expenses', 'Expense', null, 0, 0.00],
    ['6010', 'Rent Expense', 'Expense', '6000', 1, 0.00],
    ['6020', 'Utilities Expense', 'Expense', '6000', 1, 0.00],
    ['6030', 'Office Supplies', 'Expense', '6000', 1, 0.00],
    ['6040', 'Marketing Expense', 'Expense', '6000', 1, 0.00],
];

echo "Adding chart of accounts...\n";
foreach ($sample_accounts as $account) {
    $stmt = $conn->prepare("INSERT IGNORE INTO chart_of_accounts (account_code, account_name, account_type, parent_account_code, is_posting, opening_balance) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssid", $account[0], $account[1], $account[2], $account[3], $account[4], $account[5]);
    $stmt->execute();
    $stmt->close();
}

// Sample Tax Codes
$sample_tax_codes = [
    ['VAT', 'Value Added Tax', 10.00, 1],
    ['GST', 'Goods and Services Tax', 8.50, 1],
    ['SALES', 'Sales Tax', 6.25, 1],
    ['EXEMPT', 'Tax Exempt', 0.00, 1],
];

echo "Adding tax codes...\n";
foreach ($sample_tax_codes as $tax) {
    $stmt = $conn->prepare("INSERT IGNORE INTO tax_codes (tax_code, description, rate, is_active) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssdi", $tax[0], $tax[1], $tax[2], $tax[3]);
    $stmt->execute();
    $stmt->close();
}

// Sample Bank Accounts
$sample_bank_accounts = [
    ['Primary Checking', '1234567890', 'First National Bank', 25000.00],
    ['Business Savings', '0987654321', 'First National Bank', 50000.00],
    ['Payroll Account', '1122334455', 'Second Bank', 15000.00],
];

echo "Adding bank accounts...\n";
foreach ($sample_bank_accounts as $bank) {
    $stmt = $conn->prepare("INSERT IGNORE INTO bank_accounts (account_name, account_number, bank_name, balance) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssd", $bank[0], $bank[1], $bank[2], $bank[3]);
    $stmt->execute();
    $stmt->close();
}

// Sample AR Customers
$sample_customers = [
    ['ABC Corporation', 'billing@abc-corp.com'],
    ['XYZ Company', 'finance@xyz-company.com'],
    ['Tech Solutions Ltd', 'accounts@techsolutions.com'],
];

echo "Adding AR customers...\n";
foreach ($sample_customers as $customer) {
    $stmt = $conn->prepare("INSERT IGNORE INTO ar_customers (customer_name, email) VALUES (?, ?)");
    $stmt->bind_param("ss", $customer[0], $customer[1]);
    $stmt->execute();
    $stmt->close();
}

// Sample AP Vendors
$sample_vendors = [
    ['Office Supplies Co', 'billing@officesupplies.com'],
    ['Equipment Rental Inc', 'accounts@equipmentrent.com'],
    ['Marketing Agency', 'finance@marketingagency.com'],
];

echo "Adding AP vendors...\n";
foreach ($sample_vendors as $vendor) {
    $stmt = $conn->prepare("INSERT IGNORE INTO ap_vendors (vendor_name, email) VALUES (?, ?)");
    $stmt->bind_param("ss", $vendor[0], $vendor[1]);
    $stmt->execute();
    $stmt->close();
}

// Sample AR Invoices
echo "Adding sample AR invoices...\n";
$customers = $conn->query("SELECT id FROM ar_customers LIMIT 3");
$customer_ids = [];
while ($row = $customers->fetch_assoc()) {
    $customer_ids[] = $row['id'];
}

if (!empty($customer_ids)) {
    $sample_invoices = [
        [$customer_ids[0], 'INV-001', '2024-01-15', '2024-02-14', 1500.00, 1, 150.00, 1650.00, 0.00, 'Unpaid'],
        [$customer_ids[1], 'INV-002', '2024-01-20', '2024-02-19', 2500.00, 1, 250.00, 2750.00, 2750.00, 'Paid'],
        [$customer_ids[2], 'INV-003', '2024-01-25', '2024-02-24', 3200.00, 1, 320.00, 3520.00, 1000.00, 'Partially Paid'],
    ];
    
    foreach ($sample_invoices as $invoice) {
        $stmt = $conn->prepare("INSERT IGNORE INTO ar_invoices (customer_id, invoice_no, invoice_date, due_date, subtotal, tax_code_id, tax_amount, total, paid, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssidddds", $invoice[0], $invoice[1], $invoice[2], $invoice[3], $invoice[4], $invoice[5], $invoice[6], $invoice[7], $invoice[8], $invoice[9]);
        $stmt->execute();
        $stmt->close();
    }
}

// Sample AP Bills
echo "Adding sample AP bills...\n";
$vendors = $conn->query("SELECT id FROM ap_vendors LIMIT 3");
$vendor_ids = [];
while ($row = $vendors->fetch_assoc()) {
    $vendor_ids[] = $row['id'];
}

if (!empty($vendor_ids)) {
    $sample_bills = [
        [$vendor_ids[0], 'BILL-001', '2024-01-10', '2024-02-09', 800.00, 1, 80.00, 880.00, 0.00, 'Unpaid'],
        [$vendor_ids[1], 'BILL-002', '2024-01-15', '2024-02-14', 1200.00, 1, 120.00, 1320.00, 1320.00, 'Paid'],
        [$vendor_ids[2], 'BILL-003', '2024-01-20', '2024-02-19', 950.00, 1, 95.00, 1045.00, 500.00, 'Partially Paid'],
    ];
    
    foreach ($sample_bills as $bill) {
        $stmt = $conn->prepare("INSERT IGNORE INTO ap_bills (vendor_id, bill_no, bill_date, due_date, subtotal, tax_code_id, tax_amount, total, paid, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssidddds", $bill[0], $bill[1], $bill[2], $bill[3], $bill[4], $bill[5], $bill[6], $bill[7], $bill[8], $bill[9]);
        $stmt->execute();
        $stmt->close();
    }
}

// Sample Journal Entries
echo "Adding sample journal entries...\n";
$sample_journal_entries = [
    ['2024-01-15', 'JE-001', 'Opening balances'],
    ['2024-01-20', 'JE-002', 'Office supplies purchase'],
    ['2024-01-25', 'JE-003', 'Monthly rent payment'],
];

foreach ($sample_journal_entries as $entry) {
    $stmt = $conn->prepare("INSERT IGNORE INTO journal_entries (entry_date, reference, memo) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $entry[0], $entry[1], $entry[2]);
    if ($stmt->execute()) {
        $journal_id = $conn->insert_id;
        
        // Add sample journal entry lines based on the entry
        if ($entry[1] === 'JE-001') {
            // Opening balances
            $lines = [
                [$journal_id, '1010', 'Opening cash balance', 8000.00, 0.00],
                [$journal_id, '3000', 'Owner equity opening', 0.00, 8000.00],
            ];
        } elseif ($entry[1] === 'JE-002') {
            // Office supplies purchase
            $lines = [
                [$journal_id, '6030', 'Office supplies expense', 500.00, 0.00],
                [$journal_id, '1010', 'Cash payment', 0.00, 500.00],
            ];
        } else {
            // Monthly rent
            $lines = [
                [$journal_id, '6010', 'Rent expense', 2000.00, 0.00],
                [$journal_id, '1010', 'Cash payment', 0.00, 2000.00],
            ];
        }
        
        foreach ($lines as $line) {
            $line_stmt = $conn->prepare("INSERT INTO journal_entry_lines (journal_entry_id, account_code, description, debit, credit) VALUES (?, ?, ?, ?, ?)");
            $line_stmt->bind_param("issdd", $line[0], $line[1], $line[2], $line[3], $line[4]);
            $line_stmt->execute();
            $line_stmt->close();
        }
    }
    $stmt->close();
}

echo "Sample accounts data initialization complete!\n";
$conn->close();
?> 