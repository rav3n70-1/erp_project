<?php
function ensure_accounts_schema(mysqli $conn): void {
    $queries = [
        // Chart of Accounts - Create if not exists
        "CREATE TABLE IF NOT EXISTS chart_of_accounts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            account_code VARCHAR(32) UNIQUE NOT NULL,
            account_name VARCHAR(255) NOT NULL,
            account_type ENUM('Asset','Liability','Equity','Revenue','Expense') NOT NULL,
            parent_account_code VARCHAR(32) NULL,
            is_posting TINYINT(1) DEFAULT 1,
            opening_balance DECIMAL(12,2) DEFAULT 0,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_parent (parent_account_code)
        ) ENGINE=InnoDB",

        // Add missing columns to chart_of_accounts if they don't exist
        "ALTER TABLE chart_of_accounts 
         ADD COLUMN IF NOT EXISTS parent_account_code VARCHAR(32) NULL AFTER account_type",
        "ALTER TABLE chart_of_accounts 
         ADD COLUMN IF NOT EXISTS is_posting TINYINT(1) DEFAULT 1 AFTER parent_account_code",
        "ALTER TABLE chart_of_accounts 
         ADD COLUMN IF NOT EXISTS opening_balance DECIMAL(12,2) DEFAULT 0 AFTER is_posting",
        "ALTER TABLE chart_of_accounts 
         ADD INDEX IF NOT EXISTS idx_parent (parent_account_code)",

        // Journal Entries
        "CREATE TABLE IF NOT EXISTS journal_entries (
            id INT AUTO_INCREMENT PRIMARY KEY,
            entry_date DATE NOT NULL,
            reference VARCHAR(64) NULL,
            memo VARCHAR(255) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB",
        "CREATE TABLE IF NOT EXISTS journal_entry_lines (
            id INT AUTO_INCREMENT PRIMARY KEY,
            journal_entry_id INT NOT NULL,
            account_code VARCHAR(32) NOT NULL,
            description VARCHAR(255) NULL,
            debit DECIMAL(12,2) DEFAULT 0,
            credit DECIMAL(12,2) DEFAULT 0,
            FOREIGN KEY (journal_entry_id) REFERENCES journal_entries(id) ON DELETE CASCADE,
            INDEX idx_account_code (account_code)
        ) ENGINE=InnoDB",

        // Taxes
        "CREATE TABLE IF NOT EXISTS tax_codes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tax_code VARCHAR(32) UNIQUE NOT NULL,
            description VARCHAR(255) NULL,
            rate DECIMAL(5,2) NOT NULL,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB",

        // Accounts Receivable
        "CREATE TABLE IF NOT EXISTS ar_customers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            customer_name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB",
        "CREATE TABLE IF NOT EXISTS ar_invoices (
            id INT AUTO_INCREMENT PRIMARY KEY,
            customer_id INT NOT NULL,
            invoice_no VARCHAR(64) UNIQUE NOT NULL,
            invoice_date DATE NOT NULL,
            due_date DATE NOT NULL,
            subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
            tax_code_id INT NULL,
            tax_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
            total DECIMAL(12,2) NOT NULL,
            paid DECIMAL(12,2) NOT NULL DEFAULT 0,
            status ENUM('Unpaid','Partially Paid','Paid','Overdue') DEFAULT 'Unpaid',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (customer_id) REFERENCES ar_customers(id),
            FOREIGN KEY (tax_code_id) REFERENCES tax_codes(id)
        ) ENGINE=InnoDB",
        "CREATE TABLE IF NOT EXISTS ar_payments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            invoice_id INT NOT NULL,
            payment_date DATE NOT NULL,
            amount DECIMAL(12,2) NOT NULL,
            method VARCHAR(64) NULL,
            reference VARCHAR(64) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (invoice_id) REFERENCES ar_invoices(id) ON DELETE CASCADE
        ) ENGINE=InnoDB",

        // Accounts Payable
        "CREATE TABLE IF NOT EXISTS ap_vendors (
            id INT AUTO_INCREMENT PRIMARY KEY,
            vendor_name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB",
        "CREATE TABLE IF NOT EXISTS ap_bills (
            id INT AUTO_INCREMENT PRIMARY KEY,
            vendor_id INT NOT NULL,
            bill_no VARCHAR(64) UNIQUE NOT NULL,
            bill_date DATE NOT NULL,
            due_date DATE NOT NULL,
            subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
            tax_code_id INT NULL,
            tax_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
            total DECIMAL(12,2) NOT NULL,
            paid DECIMAL(12,2) NOT NULL DEFAULT 0,
            status ENUM('Unpaid','Partially Paid','Paid','Overdue') DEFAULT 'Unpaid',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (vendor_id) REFERENCES ap_vendors(id),
            FOREIGN KEY (tax_code_id) REFERENCES tax_codes(id)
        ) ENGINE=InnoDB",
        "CREATE TABLE IF NOT EXISTS ap_payments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            bill_id INT NOT NULL,
            payment_date DATE NOT NULL,
            amount DECIMAL(12,2) NOT NULL,
            method VARCHAR(64) NULL,
            reference VARCHAR(64) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (bill_id) REFERENCES ap_bills(id) ON DELETE CASCADE
        ) ENGINE=InnoDB",

        // Bank & Cash
        "CREATE TABLE IF NOT EXISTS bank_accounts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            account_name VARCHAR(255) NOT NULL,
            account_number VARCHAR(64) NULL,
            bank_name VARCHAR(255) NULL,
            balance DECIMAL(12,2) NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB",
        "CREATE TABLE IF NOT EXISTS bank_transactions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            bank_account_id INT NOT NULL,
            txn_date DATE NOT NULL,
            type ENUM('Deposit','Withdrawal','Transfer In','Transfer Out') NOT NULL,
            amount DECIMAL(12,2) NOT NULL,
            memo VARCHAR(255) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (bank_account_id) REFERENCES bank_accounts(id) ON DELETE CASCADE
        ) ENGINE=InnoDB",
    ];

    foreach ($queries as $sql) {
        try {
            $conn->query($sql);
        } catch (Exception $e) {
            // Continue if query fails (might be duplicate column, etc.)
            error_log("Schema query failed: " . $e->getMessage());
        }
    }
}
?> 