# ERP Project – Comprehensive Documentation

## 1. Overview
A modular, web-based Enterprise Resource Planning (ERP) system built with PHP and MySQL, designed for small to medium businesses. It consolidates Procurement, Inventory, Finance, HR, Projects, and Reporting. The UI is Bootstrap 5-based with jQuery/DataTables, and PDFs are generated using Dompdf. Background jobs handle email alerts, KPI calculations, and purchase suggestions.

Key highlights:
- Role-based access control (RBAC) with server-side permission checks
- Supplier and Client read-only portals
- Purchase Order (PO) life-cycle with PDF generation and deliveries tracking
- Accounts and finance dashboards integrated with charts and KPIs
- Notifications and audit logging for critical actions


## 2. Tech Stack
- Backend: PHP 8+ (XAMPP compatible)
- Database: MySQL/MariaDB
- Frontend: HTML5, CSS3, JavaScript (ES6), Bootstrap 5, jQuery, DataTables, Chart.js
- Composer Libraries (see `composer.json`):
  - `dompdf/dompdf` (PDF generation)
  - `phpmailer/phpmailer` (Email notifications)
  - `smalot/pdfparser` (PDF text extraction for product import workflows)
  - HTML5 parsing and supporting libs via vendor packages


## 3. Repository Structure (key paths)
- Root
  - `index.php`: Main dashboard; queries KPIs and renders charts
  - `portal_login.php`: Unified portal selector (Staff / Supplier / Client)
  - `login.php`, `supplier_login.php`, `client_login.php`: Auth entry points per role
  - `handle_login.php`, `handle_supplier_login.php`, `handle_client_login.php`: Auth handlers
  - `logout.php`, `supplier_logout.php`, `client_logout.php`: Session clear endpoints
  - `README.md`: Quickstart overview
  - `ERP_Project_Comprehensive_Report.md`: This document
  - `erp_db.sql`, `erp_db (1).sql`: Database schema and sample data dumps
  - `composer.json`, `composer.lock`, `vendor/`: Dependency management
- `includes/`
  - `header.php`, `footer.php`: Layout, navigation, shared scripts
  - `db.php`: MySQL connection helper (`connect_db()`), UTF-8 setup
  - `session_check.php`: Enforces logged-in session; redirects to `portal_login.php`
  - `permissions.php`: RBAC helper `has_permission()` and `log_audit_trail()`
  - `accounts_schema.php`: Ensures core accounting tables exist on load
  - `fetch_notifications.php`, `mark_notifications_read.php`: Notification APIs (JSON)
- `modules/` (feature modules)
  - `products/`: Product CRUD, categories, stock adjustments, import/export (CSV/PDF)
  - `purchase_orders/`: Create/Edit/View/Print POs, PDF generation
  - `deliveries/`: Record deliveries, update status (AJAX assist)
  - `finance/`: Budgets, invoices/payments listing and management
  - `accounts/`: Chart of accounts, journal, GL, AR/AP, taxes, bank & cash
  - `hr/`: Employee management
  - `projects/`: Project CRUD, printable views, PDF export
  - `reports/`: Purchase history and supplier performance
  - `clients/`, `suppliers/`, `assets/`, `admin/`, `profile/`: Portals and admin tools
- `scripts/`: Background/cron jobs (KPI calc, PO suggestions, contract expiry mailer)
- `assets/`
  - `css/style.css`: Theme and UI styling
  - `js/script.js`: Global UI behaviors, notifications, utilities
- `uploads/`: User uploads
  - `contracts/`, `grn/`, `invoices/`: Organized upload storage


## 4. Installation & Setup
1. Prerequisites
   - XAMPP (Apache, MySQL, PHP 8+)
   - Composer
2. Database
   - Create DB `erp_db` in phpMyAdmin
   - Import `erp_db.sql` (or `erp_db (1).sql`)
3. Configuration
   - Update credentials in `includes/db.php` if needed:
     - `DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME`
4. Dependencies
   - Run: `composer install`
5. Run
   - Start Apache & MySQL in XAMPP
   - Visit: `http://localhost/erp_project/portal_login.php`

Default roles and demo accounts are listed in `README.md` under “Default Login Credentials”.


## 5. Authentication & RBAC
- Sessions
  - `includes/session_check.php` ensures authenticated access to protected views; redirects unauthenticated users to `portal_login.php`.
- Staff Login
  - Form: `login.php` → Handler: `handle_login.php`
  - Verifies user exists, `is_active = 1`, and `password_verify()`
  - Loads `role_name` and associated `permissions` into `$_SESSION`
- Supplier & Client Portals
  - Supplier: `supplier_login.php` → `handle_supplier_login.php`; sets `$_SESSION['supplier_id']`
  - Client: `client_login.php` → `handle_client_login.php`; sets `$_SESSION['client_id']`
- Permissions
  - `includes/permissions.php::has_permission($required_permissions)` accepts a string or array of permission keys and checks `$_SESSION['permissions']`
  - `System Admin` bypasses checks
- Audit Logging
  - `log_audit_trail($conn, $action, $target_type, $target_id)` inserts records into `audit_log`


## 6. Layout, Navigation, and UI
- Shared Layout
  - `includes/header.php` loads Bootstrap, Icons, DataTables CSS, app stylesheet, and renders the left sidebar and top navbar
  - Sidebar items shown conditionally using `has_permission(...)`
  - Output buffer replaces `$` with Bangla currency sign `৳` for presentation
- Shared Footer
  - `includes/footer.php` loads jQuery, Bootstrap JS bundle, DataTables, and `assets/js/script.js`
- Global JS (`assets/js/script.js`)
  - Theme toggling (light/dark), layout density toggle, sidebar persistence
  - DataTables defaults, tooltips, keyboard shortcuts
  - Top progress bar, toast notifications, smooth reveal animations, ripple effect
  - Notification dropdown logic with periodic polling and “Mark all as read”
  - Delivery status update flow (save button triggers AJAX POST)


## 7. Dashboard (`index.php`)
- Ensures accounting schema exists (`ensure_accounts_schema($conn)`) on load
- KPIs
  - Pending POs, Total Suppliers, In-Progress Projects, Spend This Month
  - Financial overview (Total Cash & Bank, Outstanding AR/AP, Monthly Revenue)
- Charts (via CDN `Chart.js`)
  - Spend by Supplier (bar)
  - Account Types Distribution (doughnut)
  - AR vs AP Trends (line)


## 8. Modules Overview (selected files)
- Products (`modules/products/`)
  - `view_products.php`: Listing with DataTables
  - `add_product.php`, `edit_product.php` and handlers: CRUD
  - `manage_categories.php` and handlers: Category CRUD
  - `adjust_stock.php`: Stock adjustments
  - Import/Export:
    - `bulk_import.php`, `handle_bulk_import_csv.php`: CSV workflow
    - `preview_pdf_import.php`, `handle_bulk_import_pdf.php`, `confirm_pdf_import.php`: PDF extraction and preview (uses `smalot/pdfparser`)
    - `export_products_csv.php`: CSV export
- Purchase Orders (`modules/purchase_orders/`)
  - `create_po.php`, `edit_po.php`, `view_pos.php`, `view_po_details.php`: PO lifecycle
  - `print_po.php`, `generate_po_pdf.php`: Printable view and PDF
  - `handle_create_po.php`, `handle_edit_po.php`, `handle_po_status.php`: Server-side actions
- Deliveries (`modules/deliveries/`)
  - `record_delivery.php`, `handle_record_delivery.php`: Record deliveries
  - `handle_update_delivery_status.php`: Update status (invoked by global JS)
- Finance (`modules/finance/`)
  - Invoices and payments: `view_invoices.php`, `edit_invoice.php`, `view_payments.php`
  - Budgets: `manage_budgets.php`, `handle_add_budget.php`, `edit_budget.php`
- Accounts (`modules/accounts/`)
  - `chart_of_accounts.php`, `journal_entries.php`, `general_ledger.php`
  - AR/AP: `accounts_receivable.php`, `accounts_payable.php`
  - `bank_accounts.php`, `taxes.php`, `financial_reports.php`
- HR (`modules/hr/`)
  - `add_employee.php`, `edit_employee.php`, `view_employees.php`
- Projects (`modules/projects/`)
  - CRUD/screens and `generate_project_pdf.php` for project PDF export
- Reports (`modules/reports/`)
  - `purchase_history.php`, `supplier_performance.php`
- Admin (`modules/admin/`)
  - `manage_users.php`, `review_supplier_changes.php`, `view_audit_log.php`, `import_products.php`
- Assets (`modules/assets/`)
  - `add_asset.php`, `edit_asset.php`, `view_assets.php` and handlers
- Clients & Suppliers (`modules/clients/`, `modules/suppliers/`)
  - Portal dashboards and profile editing (subject to permission and scope)


## 9. Accounting Schema Bootstrap (`includes/accounts_schema.php`)
On application load, `ensure_accounts_schema(mysqli $conn)` ensures the existence of core accounting tables and adds missing columns/indexes where defined:
- Chart of Accounts (`chart_of_accounts`): hierarchical accounts with `account_type`, `parent_account_code`, `opening_balance`, `is_posting`
- Journal (`journal_entries`, `journal_entry_lines`): debits/credits with account codes
- Taxes (`tax_codes`)
- Accounts Receivable: `ar_customers`, `ar_invoices`, `ar_payments`
- Accounts Payable: `ap_vendors`, `ap_bills`, `ap_payments`
- Bank & Cash: `bank_accounts`, `bank_transactions`

This allows the dashboard and accounts pages to function without manual pre-provisioning.


## 10. Notifications System
- Backend
  - `includes/fetch_notifications.php`: Returns the 5 most recent unread notifications (and total count) for the current user as JSON
  - `includes/mark_notifications_read.php`: Marks single or all notifications as read (accepts JSON body with optional `notification_id`)
- Frontend
  - Global JS polls `/includes/fetch_notifications.php` every 20s to update the navbar bell and dropdown
  - Opening the dropdown triggers auto “mark as read” after a short delay; user can also click “Mark all”


## 11. PDF Generation
- Purchase Orders
  - `modules/purchase_orders/generate_po_pdf.php` includes `print_po.php`, captures HTML via output buffering, renders with Dompdf (A4 portrait), and streams a filename derived from the PO number
- Projects
  - `modules/projects/generate_project_pdf.php` mirrors the approach for project print views
- Notes
  - Dompdf is autoloaded from Composer (`vendor/autoload.php`)
  - Remote assets and HTML5 parsing are enabled via options


## 12. Background Jobs (`scripts/`)
- `generate_po_suggestions.php`
  - Finds products below `reorder_point` without an existing open/draft PO and creates draft POs
  - Uses default reorder quantity (e.g., 50) and selects a linked supplier from `supplier_products`
  - Runs in a database transaction and reports created/ skipped items
- `calculate_supplier_kpis.php`
  - Computes on-time delivery rate per supplier based on last delivery dates of completed POs vs expected dates
  - Updates `suppliers.on_time_delivery_rate`
- `check_contract_expiries.php`
  - Uses PHPMailer to email a summary of supplier contracts expiring within 30 days
  - SMTP configuration is in-file; see Security section for best practices

Scheduling examples:
- Linux (cron): `0 6 * * * /usr/bin/php /path/to/scripts/generate_po_suggestions.php >> /var/log/erp_cron.log 2>&1`
- Windows (Task Scheduler): Create a daily task running `php.exe` with the script path, set “Run whether user is logged on or not”


## 13. Data and Uploads
- Upload directories
  - `uploads/contracts/`: Contracts uploaded by admins/users (PDF)
  - `uploads/grn/`: Goods Received Note images (`grn_*.png|jpeg`)
  - `uploads/invoices/`: Supplier invoice PDFs/images (`invoice_supp_*`)
- Naming: Files are saved with unique suffixes to avoid collisions


## 14. Security & Compliance
- Passwords
  - All logins use `password_hash()`/`password_verify()` compatible hashes (see handlers)
  - Staff accounts must be `is_active = 1` to log in
- Sessions
  - Always validated by `includes/session_check.php` for protected pages
- RBAC
  - Ensure menu and endpoint access both check `has_permission(...)` server-side
- SMTP Secrets
  - `scripts/check_contract_expiries.php` contains hardcoded SMTP credentials. Move to environment variables or a secure config not committed to VCS:
    - Example: read from `$_ENV`/`.env` (via `vlucas/phpdotenv`), or system env vars
- File Uploads
  - Validate size/type, store outside webroot if possible, and serve via controlled endpoints
- SQL
  - Use prepared statements for inputs (present across login, notifications, PO scripts, etc.)


## 15. Troubleshooting
- “Cannot connect to database”
  - Verify MySQL is running and `includes/db.php` credentials
- Blank charts or zeroed KPIs
  - Ensure tables exist (first visit to `index.php` calls `ensure_accounts_schema`)
  - Verify sample data imported from `erp_db.sql`
- PDF generation fails
  - Confirm `vendor/autoload.php` exists (`composer install`)
  - For external images, ensure `isRemoteEnabled` is true (already set)
- Notifications don’t appear
  - Ensure `notifications` table has rows for the user and cron/logic is inserting records
- Contract expiry mailer not sending
  - Validate SMTP credentials and network egress; switch to app password and STARTTLS/SMTPS as required


## 16. Extensibility Guidelines
- Add new modules under `modules/<feature>/` with clear separation of views and handlers
- Gate all views and actions with `has_permission(...)`
- Add DB migrations/ensurance in `includes/accounts_schema.php` or a dedicated migration runner
- Use Composer for third-party libraries; prefer CDN for browser libs via `header.php`/`footer.php`


## 17. Roadmap (from `README.md` and extras)
- PO → Bill conversion workflow
- Advanced delivery tracking and scheduled payments
- Bulk supplier import via CSV
- Multi-language support (English & Bangla)
- Harden PDF import flow and finalize parsing rules for product ingestion


## 18. Licensing
- The `README.md` states MIT License. Include a `LICENSE` file at the root to formalize terms if missing.


## 19. Quick Reference (files mentioned)
- Auth: `login.php`, `handle_login.php`, `supplier_login.php`, `handle_supplier_login.php`, `client_login.php`, `handle_client_login.php`
- Layout & Core: `includes/header.php`, `includes/footer.php`, `includes/session_check.php`, `includes/db.php`, `includes/permissions.php`
- Dashboard: `index.php`
- Notifications: `includes/fetch_notifications.php`, `includes/mark_notifications_read.php`, `assets/js/script.js`
- POs: `modules/purchase_orders/*` (create/edit/view/print/pdf)
- Products: `modules/products/*` (CRUD, categories, CSV/PDF import/export)
- Accounts: `modules/accounts/*` (+ `includes/accounts_schema.php`)
- Scripts: `scripts/*` (KPI, PO suggestions, contract mailer)
- PDFs: `modules/purchase_orders/generate_po_pdf.php`, `modules/projects/generate_project_pdf.php`


## 20. Appendix – Notable Implementation Details
- Output buffering in `includes/header.php` performs a currency symbol replacement to display Bangla Taka (৳) where `$` appears before digits
- `assets/js/script.js` enhances UX with:
  - Persisted theme/density, active link highlighting, keyboard shortcuts
  - Global toast and confirm modal helpers
  - Centralized modal population (`setupModalListener`) for delete/confirm flows
- `modules/products/preview_pdf_import.php` contains verbose debug output to trace PDF upload and parsing issues; use it while stabilizing the import pipeline
