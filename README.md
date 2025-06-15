Web-Based ERP System
A comprehensive, modular Enterprise Resource Planning (ERP) system built with PHP and MySQL. This application is designed to manage the core business workflows of a small to medium-sized enterprise, from procurement and inventory management to finance and human resources.
✨ Key Features
This application is built from the ground up and includes a wide range of professional features:
Modular Design: The system is organized into distinct, interconnected modules for different business functions.
Role-Based Access Control: A powerful, database-driven permission system controls what each user can see and do, based on their assigned role1111.
Secure Authentication: Separate, secure login portals for internal employees, external suppliers, and clients, with all passwords securely hashed.
Interactive UI: A modern, responsive user interface built with Bootstrap 5, featuring interactive data tables (sorting, searching, pagination)2, dynamic charts, and AJAX-powered updates for a smooth user experience.
Automated Processes: The system includes automated features like:
Email alerts for expiring supplier contracts3.
In-app notifications for PO and Project approvals4.
Automated generation of draft Purchase Orders based on product reorder points5.
Automated KPI calculations for supplier performance6.
Financial Controls: Robust tools for managing departmental and project budgets, with real-time validation to prevent overspending.
Data Utilities: Features for exporting key reports to CSV 7 and generating print-friendly or PDF versions of documents like Purchase Orders and Project Summaries8.
Comprehensive Logging: A detailed audit log tracks all critical user actions for security and accountability9.
Core Modules Implemented:
Supplier & Contract Management: Full CRUD for suppliers, contacts, and contracts with file uploads and expiry alerts. 10101010
Product & Inventory Management: A complete product catalog with category management and an integrated inventory system that automatically updates stock levels. 11111111
Asset Management: A system for tracking and calculating the depreciation of fixed company assets.
Purchase Order Management: A dynamic PO creation form with a full, role-based approval workflow.
Delivery & Receiving: Functionality to record partial deliveries and attach Goods Receipt Notes (GRNs). 12
Finance & Payment: Manage budgets, log invoices, and record full or partial payments.
HR Management: A module to manage employee records and information.
Project Management: A system to create and manage high-level projects, assign managers, link budgets, and track task statuses.
Supplier & Client Portals: Secure, read-only portals for external partners to view their relevant information (POs for suppliers, project statuses for clients).
🚀 Technology Stack
Backend: PHP 8+
Database: MySQL / MariaDB
Frontend:
HTML5
CSS3
JavaScript (ES6+)
Bootstrap 5 (UI Framework)
jQuery (for DataTables)
Chart.js (for dashboard charts)
DataTables.net (for interactive tables)
PHP Libraries (via Composer):
dompdf/dompdf (for PDF generation)
Development Environment: XAMPP (Apache, MySQL, PHP)
🔧 Installation & Setup
Follow these steps to get the project running on a local development machine.
1. Prerequisites:
A local server environment like XAMPP installed.
Composer installed for managing PHP dependencies.
2. Clone the Repository:

Bash


git clone https://github.com/your-username/your-repo-name.git
cd your-repo-name


3. Database Setup:
Open phpMyAdmin (usually at http://localhost/phpmyadmin).
Create a new database named erp_db.
Select the erp_db database and go to the Import tab.
Import the database_schema.sql file (you will need to export this from your own working database) to create all the necessary tables.
4. Configuration:
Navigate to the includes/ directory.
Open db.php and verify that the database credentials ($servername, $username, $password, $dbname) match your local XAMPP setup.
5. Install Dependencies:
Open a terminal in the project's root directory.
Run the following command to install the required PHP libraries (like Dompdf):
Bash
composer install


6. Running the Application:
Ensure your Apache and MySQL services are running in the XAMPP Control Panel.
Navigate to http://localhost/erp_project/portal_login.php in your web browser.
🔑 Default Login Credentials
You can use the following accounts to test the different user roles:
Username
Password
Role
admin
admin123
System Admin
superadmin
super123
Super Admin / ED
procofficer
officer123
Procurement Officer
your_supplier_user
password
Vendor / Supplier
your_client_user
password
Customer / Client

Note: Supplier and Client accounts must be created first in the main application by the System Admin.
📈 Future Development Roadmap
This project has a solid foundation, but there are many exciting advanced features that can be added next:
[ ] PO to Bill Conversion: Add a feature to automatically convert a completed Purchase Order into a bill within the Finance module. 13
[ ] Advanced Delivery Tracking: Enhance the deliveries module to manage statuses like 'Shipped', 'In Transit', and 'Delayed'. 14
[ ] Scheduled Payments & Alerts: Build the backend system for scheduling future payments and creating automated alerts for budgets and invoice due dates. 15
[ ] Data Import: Create a utility for administrators to bulk-import Suppliers from a CSV file. 16
[ ] Multi-Language Support: Refactor the application to support both English and Bangla. 17
