-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 29, 2025 at 09:15 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `erp_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `ap_bills`
--

CREATE TABLE `ap_bills` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `bill_no` varchar(64) NOT NULL,
  `bill_date` date NOT NULL,
  `due_date` date NOT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax_code_id` int(11) DEFAULT NULL,
  `tax_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL,
  `paid` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` enum('Unpaid','Partially Paid','Paid','Overdue') DEFAULT 'Unpaid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `ap_bills`
--

INSERT INTO `ap_bills` (`id`, `vendor_id`, `bill_no`, `bill_date`, `due_date`, `subtotal`, `tax_code_id`, `tax_amount`, `total`, `paid`, `status`, `created_at`) VALUES
(1, 1, 'BILL-001', '2024-01-10', '2024-02-09', 800.00, 1, 80.00, 880.00, 880.00, 'Paid', '2025-08-11 18:17:30'),
(2, 2, 'BILL-002', '2024-01-15', '2024-02-14', 1200.00, 1, 120.00, 1320.00, 1320.00, 'Paid', '2025-08-11 18:17:30'),
(3, 3, 'BILL-003', '2024-01-20', '2024-02-19', 950.00, 1, 95.00, 1045.00, 1045.00, 'Paid', '2025-08-11 18:17:30'),
(4, 2, '789654', '2025-08-15', '2025-08-15', 50000.00, 3, 3125.00, 53125.00, 53125.00, 'Paid', '2025-08-15 16:04:14');

-- --------------------------------------------------------

--
-- Table structure for table `ap_payments`
--

CREATE TABLE `ap_payments` (
  `id` int(11) NOT NULL,
  `bill_id` int(11) NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `method` varchar(64) DEFAULT NULL,
  `reference` varchar(64) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `ap_payments`
--

INSERT INTO `ap_payments` (`id`, `bill_id`, `payment_date`, `amount`, `method`, `reference`, `created_at`) VALUES
(1, 4, '2025-08-15', 53125.00, 'Cash', '', '2025-08-15 16:04:29'),
(2, 1, '2025-08-18', 880.00, '', '', '2025-08-18 15:30:16'),
(3, 3, '2025-08-18', 545.00, '', '', '2025-08-18 15:30:25');

-- --------------------------------------------------------

--
-- Table structure for table `ap_vendors`
--

CREATE TABLE `ap_vendors` (
  `id` int(11) NOT NULL,
  `vendor_name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `ap_vendors`
--

INSERT INTO `ap_vendors` (`id`, `vendor_name`, `email`, `created_at`) VALUES
(1, 'Office Supplies Co', 'billing@officesupplies.com', '2025-08-11 18:17:29'),
(2, 'Equipment Rental Inc', 'accounts@equipmentrent.com', '2025-08-11 18:17:30'),
(3, 'Marketing Agency', 'finance@marketingagency.com', '2025-08-11 18:17:30');

-- --------------------------------------------------------

--
-- Table structure for table `ar_customers`
--

CREATE TABLE `ar_customers` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `ar_customers`
--

INSERT INTO `ar_customers` (`id`, `customer_name`, `email`, `created_at`) VALUES
(1, 'ABC Corporation', 'billing@abc-corp.com', '2025-08-11 18:17:29'),
(2, 'XYZ Company', 'finance@xyz-company.com', '2025-08-11 18:17:29'),
(3, 'Tech Solutions Ltd', 'accounts@techsolutions.com', '2025-08-11 18:17:29');

-- --------------------------------------------------------

--
-- Table structure for table `ar_invoices`
--

CREATE TABLE `ar_invoices` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `invoice_no` varchar(64) NOT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date NOT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax_code_id` int(11) DEFAULT NULL,
  `tax_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL,
  `paid` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` enum('Unpaid','Partially Paid','Paid','Overdue') DEFAULT 'Unpaid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `ar_invoices`
--

INSERT INTO `ar_invoices` (`id`, `customer_id`, `invoice_no`, `invoice_date`, `due_date`, `subtotal`, `tax_code_id`, `tax_amount`, `total`, `paid`, `status`, `created_at`) VALUES
(1, 1, 'INV-001', '2024-01-15', '2024-02-14', 1500.00, 1, 150.00, 1650.00, 1650.00, 'Paid', '2025-08-11 18:17:30'),
(2, 2, 'INV-002', '2024-01-20', '2024-02-19', 2500.00, 1, 250.00, 2750.00, 2750.00, 'Paid', '2025-08-11 18:17:30'),
(3, 3, 'INV-003', '2024-01-25', '2024-02-24', 3200.00, 1, 320.00, 3520.00, 3520.00, 'Paid', '2025-08-11 18:17:30'),
(4, 1, '9638527', '2025-08-15', '2025-08-15', 15000.00, 2, 1275.00, 16275.00, 16275.00, 'Paid', '2025-08-15 16:03:20');

-- --------------------------------------------------------

--
-- Table structure for table `ar_payments`
--

CREATE TABLE `ar_payments` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `method` varchar(64) DEFAULT NULL,
  `reference` varchar(64) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `ar_payments`
--

INSERT INTO `ar_payments` (`id`, `invoice_id`, `payment_date`, `amount`, `method`, `reference`, `created_at`) VALUES
(1, 4, '2025-08-15', 16275.00, 'Bank', 'r', '2025-08-15 16:03:43'),
(2, 1, '2025-08-18', 1650.00, '', '', '2025-08-18 15:29:36'),
(3, 3, '2025-08-18', 2520.00, '', '', '2025-08-18 15:30:01');

-- --------------------------------------------------------

--
-- Table structure for table `assets`
--

CREATE TABLE `assets` (
  `id` int(11) NOT NULL,
  `asset_name` varchar(255) NOT NULL,
  `asset_tag` varchar(100) NOT NULL COMMENT 'A unique identifying tag or serial number',
  `asset_type_id` int(11) NOT NULL,
  `purchase_date` date DEFAULT NULL,
  `purchase_cost` decimal(10,2) DEFAULT NULL,
  `useful_life_years` int(3) DEFAULT NULL,
  `salvage_value` decimal(10,2) NOT NULL DEFAULT 0.00,
  `assigned_to_employee_id` int(11) DEFAULT NULL COMMENT 'Which employee currently has this asset',
  `status` varchar(50) NOT NULL DEFAULT 'In Stock',
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `assets`
--

INSERT INTO `assets` (`id`, `asset_name`, `asset_tag`, `asset_type_id`, `purchase_date`, `purchase_cost`, `useful_life_years`, `salvage_value`, `assigned_to_employee_id`, `status`, `notes`, `is_active`, `created_at`) VALUES
(3, 'DCL', '9875', 1, NULL, 1200.00, 5, 500.00, 2, 'In Stock', 'He will use this', 1, '2025-06-13 07:18:21');

-- --------------------------------------------------------

--
-- Table structure for table `asset_types`
--

CREATE TABLE `asset_types` (
  `id` int(11) NOT NULL,
  `type_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `asset_types`
--

INSERT INTO `asset_types` (`id`, `type_name`) VALUES
(1, 'IT Equipment'),
(2, 'Office Furniture'),
(3, 'Vehicle'),
(4, 'Machinery'),
(5, 'Other');

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `target_type` varchar(50) NOT NULL,
  `target_id` int(11) NOT NULL,
  `log_timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `audit_log`
--

INSERT INTO `audit_log` (`id`, `user_id`, `action`, `target_type`, `target_id`, `log_timestamp`) VALUES
(11, 5, 'PO #21 status changed to Approved', 'Purchase Order', 21, '2025-06-11 06:42:53'),
(12, 5, 'PO #28 status changed to Rejected', 'Purchase Order', 28, '2025-06-11 06:43:00'),
(13, 1, 'PO #32 status changed to Approved', 'Purchase Order', 32, '2025-06-11 06:51:07'),
(14, 5, 'PO #33 status changed to Approved', 'Purchase Order', 33, '2025-06-11 06:52:11'),
(15, 5, 'PO #34 status changed to Rejected', 'Purchase Order', 34, '2025-06-11 06:59:20'),
(16, 1, 'Created new supplier', 'Supplier', 7, '2025-06-11 09:11:24'),
(17, 1, 'Deleted supplier', 'Supplier', 7, '2025-06-11 09:37:31'),
(18, 1, 'Created new product: tin', 'Product', 6, '2025-06-11 09:38:59'),
(19, 5, 'PO #35 status changed to Approved', 'Purchase Order', 35, '2025-06-11 09:40:44'),
(20, 1, 'PO #35 status changed to Approved', 'Purchase Order', 35, '2025-06-11 09:45:27'),
(21, 1, 'Created new product: Nut', 'Product', 7, '2025-06-11 09:46:29'),
(22, 1, 'Created new asset: HP Pavilion', 'Asset', 1, '2025-06-11 13:37:41'),
(23, 1, 'Created new asset: DCl', 'Asset', 2, '2025-06-11 13:38:12'),
(24, 1, 'Created new user account: inven', 'User', 12, '2025-06-11 14:46:11'),
(25, 1, 'Edited supplier details', 'Supplier', 6, '2025-06-11 15:08:13'),
(26, 1, 'Created new user account: supl2', 'User', 13, '2025-06-11 15:22:24'),
(27, 1, 'PO #36 status changed to Approved', 'Purchase Order', 36, '2025-06-11 15:23:20'),
(28, 1, 'Created new supplier', 'Supplier', 8, '2025-06-11 15:31:37'),
(29, 1, 'Created new user account: supl2', 'User', 14, '2025-06-11 15:31:56'),
(30, 1, 'Edited supplier details', 'Supplier', 8, '2025-06-11 15:35:39'),
(31, 1, 'Created new supplier', 'Supplier', 9, '2025-06-11 15:36:43'),
(32, 1, 'Created new user account: testsup3', 'User', 15, '2025-06-11 15:36:59'),
(33, 1, 'Edited supplier details', 'Supplier', 9, '2025-06-11 15:37:10'),
(34, 1, 'Created new user account: deptman', 'User', 16, '2025-06-11 15:51:22'),
(35, 16, 'Created new project: Marketing', 'Project', 1, '2025-06-11 15:54:59'),
(36, 1, 'Deleted project: Marketing', 'Project', 1, '2025-06-11 15:59:02'),
(37, 16, 'Created new project: Marketing', 'Project', 2, '2025-06-11 15:59:32'),
(38, 16, 'Edited project: Marketing', 'Project', 2, '2025-06-11 16:00:30'),
(39, 16, 'Created new project: Marketing1', 'Project', 3, '2025-06-11 16:07:03'),
(40, 16, 'Created new project (Awaiting Approval): Marketing2', 'Project', 4, '2025-06-11 16:15:15'),
(41, 16, 'Project #4 status changed to Rejected', 'Project', 4, '2025-06-11 16:15:20'),
(42, 16, 'Created new project (Awaiting Approval): Marketing2', 'Project', 5, '2025-06-11 16:15:40'),
(43, 1, 'Project #5 status changed to Approved', 'Project', 5, '2025-06-11 16:16:05'),
(44, 16, 'Created new project (Awaiting Approval): Marketing3', 'Project', 6, '2025-06-11 16:19:55'),
(45, 1, 'Deleted project: Marketing2', 'Project', 4, '2025-06-11 16:20:26'),
(46, 16, 'Created new project: Marketing4', 'Project', 7, '2025-06-11 16:28:05'),
(47, 1, 'Project #7 status changed to Approved', 'Project', 7, '2025-06-11 16:28:31'),
(48, 1, 'Deleted project: Marketing3', 'Project', 6, '2025-06-11 16:28:37'),
(49, 1, 'Deleted project: Marketing', 'Project', 2, '2025-06-11 16:28:40'),
(50, 1, 'Deleted project: Marketing1', 'Project', 3, '2025-06-11 16:28:42'),
(51, 1, 'Deleted project: Marketing2', 'Project', 5, '2025-06-11 16:28:45'),
(52, 16, 'Created new project: Marketing', 'Project', 8, '2025-06-11 16:29:26'),
(53, 1, 'Project #8 status changed to Rejected', 'Project', 8, '2025-06-11 16:29:55'),
(54, 16, 'Created new project (Awaiting Approval): Marketing2', 'Project', 9, '2025-06-11 16:37:30'),
(55, 1, 'Project #9 status changed to Approved', 'Project', 9, '2025-06-11 16:37:59'),
(56, 16, 'Created new project (Awaiting Approval): Marketing3', 'Project', 10, '2025-06-11 16:41:15'),
(57, 1, 'Project #10 status changed to Approved', 'Project', 10, '2025-06-11 16:41:50'),
(58, 16, 'Created new project (Awaiting Approval): Marketing5', 'Project', 11, '2025-06-11 16:42:48'),
(59, 16, 'Created new project (Awaiting Approval): Buy1', 'Project', 12, '2025-06-11 16:53:22'),
(60, 1, 'Project #12 status changed to Approved', 'Project', 12, '2025-06-11 16:53:29'),
(61, 16, 'Created new project (Awaiting Approval): buy2', 'Project', 13, '2025-06-11 16:54:47'),
(62, 1, 'Project #13 status changed to Approved', 'Project', 13, '2025-06-11 16:54:54'),
(63, 16, 'Created new project (Awaiting Approval): buy3', 'Project', 14, '2025-06-11 16:56:14'),
(64, 1, 'Project #11 status changed to Rejected', 'Project', 11, '2025-06-11 17:06:27'),
(65, 1, 'Project #14 status changed to Approved', 'Project', 14, '2025-06-11 17:06:41'),
(66, 1, 'Edited product: Coffee Mug', 'Product', 16, '2025-06-11 17:33:28'),
(67, 1, 'Edited product: tin', 'Product', 6, '2025-06-11 17:36:04'),
(68, 1, 'PO #39 status changed to Approved', 'Purchase Order', 39, '2025-06-11 17:41:15'),
(69, 1, 'Edited product: Coffee Mug', 'Product', 16, '2025-06-11 17:41:55'),
(70, 1, 'Edited product: Coffee Mug', 'Product', 16, '2025-06-11 17:42:08'),
(71, 11, 'Edited PO', 'Purchase Order', 40, '2025-06-11 18:08:57'),
(72, 1, 'Edited product: Sports Shoes', 'Product', 17, '2025-06-11 18:13:58'),
(73, 11, 'Edited PO and set status to Pending', 'Purchase Order', 41, '2025-06-11 18:14:38'),
(74, 11, 'Edited PO and set status to Pending', 'Purchase Order', 40, '2025-06-11 18:14:41'),
(75, 1, 'Edited PO and set status to Pending', 'Purchase Order', 41, '2025-06-11 18:15:00'),
(76, 1, 'PO #41 status changed to Approved', 'Purchase Order', 41, '2025-06-11 18:15:05'),
(77, 1, 'Edited product: Logitech G502 Hero Mouse', 'Product', 10, '2025-06-11 18:22:51'),
(78, 11, 'Edited PO and set status to Pending', 'Purchase Order', 42, '2025-06-11 18:23:10'),
(79, 1, 'PO #42 status changed to Approved', 'Purchase Order', 42, '2025-06-11 18:23:16'),
(80, 1, 'Edited product: Cotton T-Shirt', 'Product', 13, '2025-06-11 18:24:50'),
(81, 11, 'Edited PO and set status to Draft', 'Purchase Order', 40, '2025-06-11 18:28:48'),
(82, 11, 'Edited PO and set status to Pending', 'Purchase Order', 40, '2025-06-11 18:28:55'),
(83, 1, 'PO #40 status changed to Approved', 'Purchase Order', 40, '2025-06-11 18:29:03'),
(84, 1, 'Edited product: Cushion Cover Set', 'Product', 20, '2025-06-12 02:46:31'),
(85, 1, 'Edited product: Logitech G502 Hero Mouse', 'Product', 10, '2025-06-12 02:47:40'),
(86, 6, 'Edited PO and set status to Pending', 'Purchase Order', 43, '2025-06-12 02:48:13'),
(87, 1, 'PO #43 status changed to Rejected', 'Purchase Order', 43, '2025-06-12 02:48:20'),
(88, 6, 'Edited PO and set status to Pending', 'Purchase Order', 44, '2025-06-12 02:48:33'),
(89, 1, 'PO #44 status changed to Rejected', 'Purchase Order', 44, '2025-06-12 02:48:44'),
(90, 1, 'PO #45 status changed to Approved', 'Purchase Order', 45, '2025-06-12 02:50:16'),
(91, 1, 'Edited PO and set status to Pending', 'Purchase Order', 46, '2025-06-12 03:16:16'),
(92, 1, 'PO #46 status changed to Approved', 'Purchase Order', 46, '2025-06-12 03:16:40'),
(93, 1, 'PO #47 status changed to Approved', 'Purchase Order', 47, '2025-06-12 03:17:34'),
(94, 1, 'PO #48 status changed to Approved', 'Purchase Order', 48, '2025-06-12 03:31:11'),
(95, 1, 'Invoice #3 status changed to Approved for Payment', 'Invoice', 3, '2025-06-12 03:48:15'),
(96, 1, 'Invoice #1 status changed to Approved for Payment', 'Invoice', 1, '2025-06-12 03:48:17'),
(97, 1, 'Invoice #2 status changed to Disputed', 'Invoice', 2, '2025-06-12 03:48:22'),
(98, 1, 'Created new supplier', 'Supplier', 10, '2025-06-12 05:01:29'),
(99, 1, 'Created new supplier', 'Supplier', 11, '2025-06-12 05:02:01'),
(100, 1, 'Edited project: Marketing4', 'Project', 7, '2025-06-12 05:56:01'),
(101, 1, 'PO #49 status changed to Approved', 'Purchase Order', 49, '2025-06-12 06:37:04'),
(102, 1, 'PO #50 status changed to Approved', 'Purchase Order', 50, '2025-06-12 06:38:17'),
(103, 1, 'Approved supplier info change', 'Supplier', 8, '2025-06-12 10:05:10'),
(104, 1, 'Rejected supplier info change', 'Supplier Change Request', 1, '2025-06-12 10:42:29'),
(105, 1, 'Rejected supplier info change', 'Supplier Change Request', 2, '2025-06-12 10:42:30'),
(106, 1, 'PO #51 status changed to Approved', 'Purchase Order', 51, '2025-06-12 10:47:51'),
(107, 1, 'PO #52 status changed to Approved', 'Purchase Order', 52, '2025-06-12 10:58:14'),
(108, 1, 'PO #53 status changed to Approved', 'Purchase Order', 53, '2025-06-12 11:00:11'),
(109, 1, 'Updated delivery status to Delayed', 'Delivery', 1, '2025-06-12 12:52:21'),
(110, 1, 'Updated delivery status to Delayed', 'Delivery', 1, '2025-06-12 12:52:31'),
(111, 1, 'Updated delivery status to Delayed', 'Delivery', 5, '2025-06-12 12:52:39'),
(112, 1, 'Updated delivery status to In Transit', 'Delivery', 3, '2025-06-12 12:52:47'),
(113, 1, 'Updated delivery status to In Transit', 'Delivery', 2, '2025-06-12 12:55:19'),
(114, 1, 'Updated delivery status to Delivered', 'Delivery', 2, '2025-06-12 12:58:05'),
(115, 1, 'Updated delivery status to In Transit', 'Delivery', 7, '2025-06-12 13:03:31'),
(116, 1, 'Updated delivery status to Delayed', 'Delivery', 8, '2025-06-12 13:03:39'),
(117, 1, 'Updated delivery status to Shipped', 'Delivery', 2, '2025-06-12 13:03:44'),
(118, 1, 'Edited project: Marketing4', 'Project', 7, '2025-06-12 13:30:43'),
(119, 1, 'Edited project: Marketing2', 'Project', 9, '2025-06-12 13:31:05'),
(120, 1, 'Approved supplier info change', 'Supplier', 6, '2025-06-12 13:36:07'),
(121, 1, 'Edited supplier details', 'Supplier', 8, '2025-06-12 17:38:29'),
(122, 1, 'Created new user account: tofa', 'User', 17, '2025-06-13 02:25:20'),
(123, 1, 'Created new user account: Shahinur', 'User', 18, '2025-06-13 02:28:37'),
(124, 1, 'Created new user account: audit', 'User', 19, '2025-06-13 02:30:14'),
(125, 1, 'Created new user account: superad', 'User', 20, '2025-06-13 02:37:44'),
(126, 1, 'Created new supplier', 'Supplier', 12, '2025-06-13 02:43:11'),
(127, 1, 'Toggled active status for user', 'User', 5, '2025-06-13 02:52:31'),
(128, 1, 'Toggled active status for user ID #5', 'User', 5, '2025-06-13 02:58:43'),
(129, 1, 'Toggled active status for supplier', 'Supplier', 5, '2025-06-13 03:14:55'),
(130, 1, 'Created new supplier', 'Supplier', 13, '2025-06-13 03:15:31'),
(131, 1, 'Edited supplier details', 'Supplier', 13, '2025-06-13 03:16:06'),
(132, 1, 'Toggled active status for employee', 'Employee', 1, '2025-06-13 03:34:20'),
(133, 1, 'Toggled active status for employee', 'Employee', 1, '2025-06-13 03:34:24'),
(134, 9, 'Toggled active status for employee', 'Employee', 1, '2025-06-13 03:34:35'),
(135, 1, 'Toggled active status for employee', 'Employee', 3, '2025-06-13 03:50:59'),
(136, 1, 'Toggled active status for user ID #18', 'User', 18, '2025-06-13 03:51:21'),
(137, 1, 'Edited supplier details', 'Supplier', 5, '2025-06-13 03:51:50'),
(138, 1, 'Toggled active status for employee', 'Employee', 3, '2025-06-13 03:52:13'),
(139, 1, 'Toggled active status for employee', 'Employee', 3, '2025-06-13 03:52:15'),
(140, 1, 'Toggled active status for user ID #18', 'User', 18, '2025-06-13 03:52:44'),
(141, 1, 'Toggled active status for client', 'Client', 1, '2025-06-13 04:29:14'),
(142, 1, 'Toggled active status for client', 'Client', 1, '2025-06-13 04:29:16'),
(143, 1, 'Created new project (Awaiting Approval): Marketing7', 'Project', 15, '2025-06-13 05:04:21'),
(144, 1, 'Edited project: Marketing7', 'Project', 15, '2025-06-13 05:04:50'),
(145, 1, 'Created new user account: moshi', 'User', 21, '2025-06-13 05:05:43'),
(146, 1, 'Edited client: Mosi', 'Client', 1, '2025-06-13 05:06:13'),
(147, 1, 'Toggled active status for client', 'Client', 1, '2025-06-13 05:14:39'),
(148, 1, 'Toggled active status for client', 'Client', 1, '2025-06-13 05:14:42'),
(149, 16, 'Created new project (Awaiting Approval): DD', 'Project', 16, '2025-06-13 05:21:19'),
(150, 1, 'Edited project: DD', 'Project', 16, '2025-06-13 05:21:48'),
(151, 16, 'Created new project (Awaiting Approval): DD2', 'Project', 17, '2025-06-13 05:31:23'),
(152, 1, 'Project #17 status changed to Rejected', 'Project', 17, '2025-06-13 05:46:50'),
(153, 1, 'Edited asset: DCl', 'Asset', 2, '2025-06-13 06:09:32'),
(154, 1, 'Edited asset: DCl', 'Asset', 2, '2025-06-13 06:23:14'),
(155, 1, 'Edited asset: DCl', 'Asset', 2, '2025-06-13 06:23:32'),
(156, 1, 'Deleted asset: HP Pavilion', 'Asset', 1, '2025-06-13 06:35:28'),
(157, 1, 'Deleted asset: DCl', 'Asset', 2, '2025-06-13 07:17:29'),
(158, 1, 'Created new asset: DCL', 'Asset', 3, '2025-06-13 07:18:21'),
(159, 16, 'Created new project (Awaiting Approval): Marketing8', 'Project', 18, '2025-06-14 15:19:26'),
(160, 1, 'Project #18 status changed to Rejected', 'Project', 18, '2025-06-14 15:20:16'),
(161, 1, 'Approved supplier info change', 'Supplier', 8, '2025-06-14 15:21:52'),
(162, 1, 'Rejected supplier info change', 'Supplier Change Request', 6, '2025-06-14 15:22:07'),
(163, 1, 'Invoice #4 status changed to Disputed', 'Invoice', 4, '2025-06-14 15:23:15'),
(164, 1, 'Edited project: DD', 'Project', 16, '2025-06-14 15:25:03'),
(165, 1, 'Toggled active status for employee', 'Employee', 3, '2025-06-14 15:28:37'),
(166, 1, 'Deleted project: DD2', 'Project', 17, '2025-06-15 19:19:53'),
(167, 1, 'Deleted project: Marketing5', 'Project', 11, '2025-06-15 19:19:59'),
(168, 1, 'Deleted project: Marketing8', 'Project', 18, '2025-06-15 19:20:02'),
(169, 1, 'Deleted project: Marketing4', 'Project', 7, '2025-06-15 19:20:12'),
(170, 1, 'Created new user account: raven', 'User', 22, '2025-06-16 04:11:08'),
(171, 1, 'Toggled active status for supplier', 'Supplier', 5, '2025-08-06 05:17:16'),
(172, 1, 'Toggled active status for supplier', 'Supplier', 10, '2025-08-06 05:17:22'),
(173, 1, 'PO #56 status changed to Rejected', 'Purchase Order', 56, '2025-08-06 05:17:56'),
(174, 1, 'Edited PO and set status to Pending', 'Purchase Order', 57, '2025-08-06 05:39:52'),
(175, 1, 'PO #57 status changed to Approved', 'Purchase Order', 57, '2025-08-06 05:39:55'),
(176, 1, 'Toggled active status for user ID #5', 'User', 5, '2025-08-06 06:16:06'),
(177, 1, 'Toggled active status for user ID #20', 'User', 20, '2025-08-06 06:16:10'),
(178, 1, 'Toggled active status for user ID #5', 'User', 5, '2025-08-06 06:16:23'),
(179, 1, 'Toggled active status for user ID #20', 'User', 20, '2025-08-06 06:16:26'),
(180, 1, 'Created new user account: supplier', 'User', 23, '2025-08-06 06:49:30'),
(181, 1, 'Created new supplier', 'Supplier', 14, '2025-08-06 06:50:07'),
(182, 1, 'PO #58 status changed to Approved', 'Purchase Order', 58, '2025-08-06 06:52:11'),
(183, 1, 'Toggled active status for supplier', 'Supplier', 10, '2025-08-18 15:25:21'),
(184, 1, 'Created new product: wsegawgawgkjhwgfjgwegiuhwiufhiugfhqiugheqiughewgiu`:`', 'Product', 171, '2025-08-19 02:47:25'),
(185, 1, 'Created new supplier', 'Supplier', 15, '2025-08-19 03:01:41');

-- --------------------------------------------------------

--
-- Table structure for table `bank_accounts`
--

CREATE TABLE `bank_accounts` (
  `id` int(11) NOT NULL,
  `account_name` varchar(255) NOT NULL,
  `account_number` varchar(64) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `balance` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `bank_accounts`
--

INSERT INTO `bank_accounts` (`id`, `account_name`, `account_number`, `bank_name`, `balance`, `created_at`) VALUES
(1, 'Primary Checking', '1234567890', 'First National Bank', 25000.00, '2025-08-11 18:17:29'),
(2, 'Business Savings', '0987654321', 'First National Bank', 100000.00, '2025-08-11 18:17:29'),
(3, 'Payroll Account', '1122334455', 'Second Bank', 15000.00, '2025-08-11 18:17:29');

-- --------------------------------------------------------

--
-- Table structure for table `bank_transactions`
--

CREATE TABLE `bank_transactions` (
  `id` int(11) NOT NULL,
  `bank_account_id` int(11) NOT NULL,
  `txn_date` date NOT NULL,
  `type` enum('Deposit','Withdrawal','Transfer In','Transfer Out') NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `memo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `bank_transactions`
--

INSERT INTO `bank_transactions` (`id`, `bank_account_id`, `txn_date`, `type`, `amount`, `memo`, `created_at`) VALUES
(1, 2, '2025-08-18', 'Deposit', 50000.00, '', '2025-08-18 15:29:19');

-- --------------------------------------------------------

--
-- Table structure for table `budgets`
--

CREATE TABLE `budgets` (
  `id` int(11) NOT NULL,
  `budget_name` varchar(255) NOT NULL,
  `department_id` int(11) NOT NULL,
  `allocated_amount` decimal(12,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `budgets`
--

INSERT INTO `budgets` (`id`, `budget_name`, `department_id`, `allocated_amount`, `start_date`, `end_date`, `created_at`) VALUES
(3, 'Buying', 4, 100000.00, '2025-06-11', '2025-07-11', '2025-06-11 05:13:57'),
(4, 'Marketing', 3, 201000.00, '2025-06-11', '2025-06-30', '2025-06-11 05:14:31'),
(5, 'Buy', 4, 10000.00, '2025-06-12', '2025-06-13', '2025-06-12 06:37:39'),
(7, 'Buying', 4, 50000.00, '2025-08-06', '2025-08-31', '2025-08-06 05:39:14');

-- --------------------------------------------------------

--
-- Table structure for table `chart_of_accounts`
--

CREATE TABLE `chart_of_accounts` (
  `id` int(11) NOT NULL,
  `account_code` varchar(32) NOT NULL,
  `account_name` varchar(255) NOT NULL,
  `account_type` enum('Asset','Liability','Equity','Revenue','Expense') NOT NULL,
  `parent_account_code` varchar(32) DEFAULT NULL,
  `is_posting` tinyint(1) DEFAULT 1,
  `opening_balance` decimal(12,2) DEFAULT 0.00,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `chart_of_accounts`
--

INSERT INTO `chart_of_accounts` (`id`, `account_code`, `account_name`, `account_type`, `parent_account_code`, `is_posting`, `opening_balance`, `is_active`, `created_at`) VALUES
(1, '1000', 'Cash', 'Asset', NULL, 1, 10000.00, 1, '2025-08-11 18:17:29'),
(2, '1010', 'Checking Account', 'Asset', '1000', 1, 8000.00, 1, '2025-08-11 18:17:29'),
(3, '1020', 'Savings Account', 'Asset', '1000', 1, 2000.00, 1, '2025-08-11 18:17:29'),
(4, '1100', 'Accounts Receivable', 'Asset', NULL, 1, 15000.00, 1, '2025-08-11 18:17:29'),
(5, '1200', 'Inventory', 'Asset', NULL, 1, 25000.00, 1, '2025-08-11 18:17:29'),
(6, '1500', 'Equipment', 'Asset', NULL, 1, 50000.00, 1, '2025-08-11 18:17:29'),
(7, '2000', 'Accounts Payable', 'Liability', NULL, 1, 8000.00, 1, '2025-08-11 18:17:29'),
(8, '2100', 'Accrued Expenses', 'Liability', NULL, 1, 3000.00, 1, '2025-08-11 18:17:29'),
(9, '2200', 'Notes Payable', 'Liability', NULL, 1, 20000.00, 1, '2025-08-11 18:17:29'),
(10, '3000', 'Owner\'s Equity', 'Equity', NULL, 1, 50000.00, 1, '2025-08-11 18:17:29'),
(11, '3100', 'Retained Earnings', 'Equity', NULL, 1, 19000.00, 1, '2025-08-11 18:17:29'),
(12, '4000', 'Sales Revenue', 'Revenue', NULL, 1, 0.00, 1, '2025-08-11 18:17:29'),
(13, '4100', 'Service Revenue', 'Revenue', NULL, 1, 0.00, 1, '2025-08-11 18:17:29'),
(14, '5000', 'Cost of Goods Sold', 'Expense', NULL, 1, 0.00, 1, '2025-08-11 18:17:29'),
(15, '6000', 'Operating Expenses', 'Expense', NULL, 0, 0.00, 1, '2025-08-11 18:17:29'),
(16, '6010', 'Rent Expense', 'Expense', '6000', 1, 0.00, 1, '2025-08-11 18:17:29'),
(17, '6020', 'Utilities Expense', 'Expense', '6000', 1, 0.00, 1, '2025-08-11 18:17:29'),
(18, '6030', 'Office Supplies', 'Expense', '6000', 1, 0.00, 1, '2025-08-11 18:17:29'),
(19, '6040', 'Marketing Expense', 'Expense', '6000', 1, 0.00, 1, '2025-08-11 18:17:29');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `client_name` varchar(255) NOT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(50) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `client_name`, `contact_person`, `email`, `phone_number`, `username`, `password`, `is_active`, `created_at`) VALUES
(1, 'Mosi', 'Mosha', 'mo@gail.com', '79632146', 'moshi', '$2y$10$cj5j4gSwfVPyaUWDTbzLAeSTnKe04mz8PHPlds7MQLHh/etBtGFRm', 1, '2025-06-13 04:19:12');

-- --------------------------------------------------------

--
-- Table structure for table `compliance_checklists`
--

CREATE TABLE `compliance_checklists` (
  `id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `compliance_checklists`
--

INSERT INTO `compliance_checklists` (`id`, `item_name`, `item_description`) VALUES
(1, 'ISO 9001 Certification', 'Quality Management System Certification'),
(2, 'Safety Standards Compliance', 'Compliance with local and international safety regulations'),
(3, 'Environmental Policy', 'Supplier has a documented environmental policy'),
(4, 'Data Privacy Agreement', 'A signed agreement regarding data privacy and protection is on file');

-- --------------------------------------------------------

--
-- Table structure for table `deliveries`
--

CREATE TABLE `deliveries` (
  `id` int(11) NOT NULL,
  `po_id` int(11) NOT NULL,
  `delivery_date` date NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Delivered',
  `notes` text DEFAULT NULL,
  `grn_file_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `deliveries`
--

INSERT INTO `deliveries` (`id`, `po_id`, `delivery_date`, `status`, `notes`, `grn_file_path`, `created_at`) VALUES
(1, 2, '2025-06-10', 'Delayed', '1 Deffective', NULL, '2025-06-10 10:26:48'),
(2, 2, '2025-06-10', 'Shipped', '1 recieved', NULL, '2025-06-10 10:27:30'),
(3, 3, '2025-06-10', 'In Transit', '', NULL, '2025-06-10 10:33:46'),
(4, 3, '2025-06-10', 'Delivered', '', NULL, '2025-06-10 10:33:51'),
(5, 4, '2025-06-10', 'Delayed', '', NULL, '2025-06-10 10:34:31'),
(6, 6, '2025-06-10', 'Delivered', '', 'uploads/grn/grn_6_1749556447.png', '2025-06-10 11:54:07'),
(7, 7, '2025-06-10', 'In Transit', '', NULL, '2025-06-10 15:49:51'),
(8, 9, '2025-06-10', 'Delayed', '', NULL, '2025-06-10 16:08:39'),
(9, 8, '2025-06-10', 'Delivered', '', NULL, '2025-06-10 16:12:34'),
(10, 10, '2025-06-10', 'Delivered', '', NULL, '2025-06-10 16:13:45'),
(11, 11, '2025-06-10', 'Delivered', '', NULL, '2025-06-10 16:17:04'),
(12, 12, '2025-06-10', 'Delivered', '', NULL, '2025-06-10 16:21:45'),
(13, 13, '2025-06-10', 'Delivered', '', NULL, '2025-06-10 16:26:18'),
(14, 14, '2025-06-10', 'Delivered', '', NULL, '2025-06-10 16:28:38'),
(15, 15, '2025-06-10', 'Delivered', '', NULL, '2025-06-10 16:31:14'),
(16, 16, '2025-06-10', 'Delivered', '', NULL, '2025-06-10 16:33:02'),
(17, 17, '2025-06-10', 'Delivered', '', NULL, '2025-06-10 16:43:10'),
(18, 17, '2025-06-10', 'Delivered', '', NULL, '2025-06-10 16:43:21'),
(19, 18, '2025-06-11', 'Delivered', '', NULL, '2025-06-11 03:55:20'),
(20, 20, '2025-06-11', 'Delivered', '', NULL, '2025-06-11 04:08:14'),
(21, 22, '2025-06-11', 'Delivered', '', NULL, '2025-06-11 04:25:48'),
(22, 23, '2025-06-11', 'Delivered', '', NULL, '2025-06-11 04:26:17'),
(23, 35, '2025-06-11', 'Delivered', '', NULL, '2025-06-11 09:41:29'),
(24, 39, '2025-06-11', 'Delivered', '', NULL, '2025-06-11 17:41:21'),
(25, 50, '2025-06-12', 'Delivered', '', NULL, '2025-06-12 07:48:36'),
(26, 51, '2025-06-12', 'Delivered', '', NULL, '2025-06-12 10:48:14'),
(27, 52, '2025-06-12', 'Delivered', '', NULL, '2025-06-12 10:58:19'),
(28, 53, '2025-06-12', 'Delivered', '', NULL, '2025-06-12 11:00:29'),
(29, 45, '2025-06-12', 'Delivered', '', NULL, '2025-06-12 12:14:48'),
(30, 47, '2025-06-12', 'Delivered', '', NULL, '2025-06-12 12:15:23'),
(31, 47, '2025-06-12', 'Delivered', '', NULL, '2025-06-12 12:27:27'),
(32, 49, '2025-06-12', 'Delivered', '', NULL, '2025-06-12 12:29:57'),
(34, 57, '2025-08-06', 'Delivered', '1 recieved', 'uploads/grn/grn_57_1754458854.jpeg', '2025-08-06 05:40:54');

-- --------------------------------------------------------

--
-- Table structure for table `delivery_items`
--

CREATE TABLE `delivery_items` (
  `id` int(11) NOT NULL,
  `delivery_id` int(11) NOT NULL,
  `po_item_id` int(11) NOT NULL,
  `quantity_received` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `delivery_items`
--

INSERT INTO `delivery_items` (`id`, `delivery_id`, `po_item_id`, `quantity_received`) VALUES
(1, 1, 2, 19),
(2, 2, 2, 1),
(3, 3, 3, 23),
(4, 4, 3, 2),
(5, 5, 4, 12),
(6, 6, 6, 105),
(7, 7, 7, 500),
(8, 8, 10, 150),
(9, 9, 8, 5000),
(10, 9, 9, 20),
(11, 10, 11, 50),
(12, 11, 12, 200),
(13, 12, 13, 20),
(14, 13, 14, 20),
(15, 14, 15, 25),
(16, 15, 16, 49),
(17, 15, 17, 3),
(18, 16, 18, 200),
(19, 17, 19, 150),
(20, 18, 19, 50),
(21, 19, 20, 20),
(22, 20, 22, 20),
(23, 21, 24, 2),
(24, 22, 25, 2),
(25, 23, 37, 199),
(26, 24, 39, 2000),
(27, 25, 60, 5),
(28, 26, 61, 11),
(29, 27, 62, 5),
(30, 28, 63, 5),
(31, 29, 54, 1199),
(32, 30, 57, 5),
(33, 31, 57, 2),
(34, 32, 59, 2),
(35, 34, 68, 1200);

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `department_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `department_name`) VALUES
(1, 'General Administration'),
(2, 'IT Department'),
(3, 'Marketing'),
(4, 'Operations');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(32) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(50) DEFAULT NULL,
  `hire_date` date NOT NULL,
  `job_title` varchar(100) NOT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `department_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL COMMENT 'Link to their login account, if they have one',
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `employee_id`, `first_name`, `last_name`, `email`, `phone_number`, `hire_date`, `job_title`, `salary`, `department_id`, `user_id`, `is_active`) VALUES
(2, NULL, 'Toufique ', 'Ahamed', 'tofa@gm.com', '0123456789963', '2025-06-13', 'Procurement Officer', 31000.00, 4, 17, 1),
(3, NULL, 'Shahinur Alam', 'Rabbi', 'Shahinur@g.com', '852741963', '2025-06-13', 'Marketing Manager', 25000.00, 3, 18, 1),
(4, NULL, 'Mehedi', 'Rohan', 'rohan15-5910@diu.edu.bd', '01749393453', '2025-06-16', 'Admin', 50000.00, 1, 22, 1),
(5, 'EMP-2025-00005', 'Test Emp', 'Emp', 'testemp@gmail.com', '0123456789', '2025-08-11', 'Employee', 15000.00, 1, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `po_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `invoice_number` varchar(100) NOT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('Draft','Submitted','Approved for Payment','Paid','Disputed') NOT NULL DEFAULT 'Submitted',
  `file_path` varchar(255) DEFAULT NULL COMMENT 'Path to the uploaded invoice PDF',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `po_id`, `supplier_id`, `invoice_number`, `invoice_date`, `due_date`, `total_amount`, `status`, `file_path`, `created_at`) VALUES
(1, 6, 5, '136666', '2025-06-11', '2025-06-20', 120.00, 'Approved for Payment', NULL, '2025-06-11 07:57:42'),
(2, 12, 6, '1260', '2025-06-11', '2025-06-12', 120.00, 'Disputed', 'uploads/invoices/invoice_supp_6_1749655762.pdf', '2025-06-11 15:29:22'),
(3, 47, 8, '136666', '2025-06-13', '2025-06-13', 559.93, 'Approved for Payment', 'uploads/invoices/invoice_supp_8_1749699382.png', '2025-06-12 03:36:22'),
(4, 47, 8, '79520', '2025-06-14', '2025-06-25', 12000.00, 'Disputed', 'uploads/invoices/invoice_supp_8_1749914573.png', '2025-06-14 15:22:53');

-- --------------------------------------------------------

--
-- Table structure for table `journal_entries`
--

CREATE TABLE `journal_entries` (
  `id` int(11) NOT NULL,
  `entry_date` date NOT NULL,
  `reference` varchar(64) DEFAULT NULL,
  `memo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `journal_entries`
--

INSERT INTO `journal_entries` (`id`, `entry_date`, `reference`, `memo`, `created_at`) VALUES
(1, '2024-01-15', 'JE-001', 'Opening balances', '2025-08-11 18:17:30'),
(2, '2024-01-20', 'JE-002', 'Office supplies purchase', '2025-08-11 18:17:30'),
(3, '2024-01-25', 'JE-003', 'Monthly rent payment', '2025-08-11 18:17:30');

-- --------------------------------------------------------

--
-- Table structure for table `journal_entry_lines`
--

CREATE TABLE `journal_entry_lines` (
  `id` int(11) NOT NULL,
  `journal_entry_id` int(11) NOT NULL,
  `account_code` varchar(32) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `debit` decimal(12,2) DEFAULT 0.00,
  `credit` decimal(12,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `journal_entry_lines`
--

INSERT INTO `journal_entry_lines` (`id`, `journal_entry_id`, `account_code`, `description`, `debit`, `credit`) VALUES
(1, 1, '1010', 'Opening cash balance', 8000.00, 0.00),
(2, 1, '3000', 'Owner equity opening', 0.00, 8000.00),
(3, 2, '6030', 'Office supplies expense', 500.00, 0.00),
(4, 2, '1010', 'Cash payment', 0.00, 500.00),
(5, 3, '6010', 'Rent expense', 2000.00, 0.00),
(6, 3, '1010', 'Cash payment', 0.00, 2000.00);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'The user who will receive the notification',
  `message` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL COMMENT 'A link to the relevant page (e.g., a PO)',
  `is_read` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = unread, 1 = read',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `link`, `is_read`, `created_at`) VALUES
(1, 1, 'New PO PO-2025-0018 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=18', 1, '2025-06-11 03:52:26'),
(3, 1, 'New PO PO-2025-0019 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=19', 1, '2025-06-11 04:00:43'),
(5, 1, 'New PO PO-2025-0020 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=20', 1, '2025-06-11 04:07:48'),
(7, 1, 'New PO PO-2025-0021 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=21', 1, '2025-06-11 04:18:25'),
(9, 1, 'New PO PO-2025-0022 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=22', 1, '2025-06-11 04:18:36'),
(11, 1, 'New PO PO-2025-0023 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=23', 1, '2025-06-11 04:19:58'),
(13, 1, 'New PO PO-2025-0024 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=24', 1, '2025-06-11 04:31:33'),
(15, 1, 'New PO PO-2025-0025 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=25', 1, '2025-06-11 04:35:36'),
(17, 1, 'New PO PO-2025-0026 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=26', 1, '2025-06-11 04:38:26'),
(19, 1, 'New PO PO-2025-0028 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=28', 1, '2025-06-11 05:04:28'),
(21, 1, 'New PO PO-2025-0029 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=29', 1, '2025-06-11 05:04:42'),
(23, 1, 'New PO PO-2025-0030 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=30', 1, '2025-06-11 05:14:49'),
(25, 1, 'New PO PO-2025-0031 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=31', 1, '2025-06-11 05:22:11'),
(27, 1, 'New PO PO-2025-0031 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=32', 1, '2025-06-11 06:51:01'),
(28, 1, 'New PO PO-2025-0033 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=33', 1, '2025-06-11 06:51:55'),
(29, 5, 'New PO PO-2025-0034 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=34', 1, '2025-06-11 06:59:11'),
(30, 5, 'New PO PO-2025-0035 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=35', 1, '2025-06-11 09:40:13'),
(31, 5, 'New PO PO-2025-0036 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=36', 1, '2025-06-11 15:23:16'),
(32, 1, 'New task \'Start\' added to project \'Marketing\'', '/erp_project/modules/projects/view_project_details.php?id=2', 1, '2025-06-11 16:07:52'),
(33, 1, 'New task \'End\' added to project \'Marketing\'', '/erp_project/modules/projects/view_project_details.php?id=2', 1, '2025-06-11 16:08:11'),
(34, 1, 'New task \'Mid\' added to project \'Marketing\'', '/erp_project/modules/projects/view_project_details.php?id=2', 1, '2025-06-11 16:08:31'),
(35, 1, 'New task \'Start\' added to project \'Marketing4\'', '/erp_project/modules/projects/view_project_details.php?id=7', 1, '2025-06-11 16:34:55'),
(36, 16, 'New project \'Marketing2\' requires approval.', '/erp_project/modules/projects/view_project_details.php?id=9', 1, '2025-06-11 16:37:30'),
(37, 1, 'New project \'Marketing3\' requires approval.', '/erp_project/modules/projects/view_project_details.php?id=10', 1, '2025-06-11 16:41:15'),
(38, 1, 'New project \'Marketing3\' requires approval.', '/erp_project/modules/projects/view_project_details.php?id=10', 1, '2025-06-11 16:41:15'),
(39, 1, 'New project \'Marketing3\' requires approval.', '/erp_project/modules/projects/view_project_details.php?id=10', 1, '2025-06-11 16:41:15'),
(40, 1, 'New project \'Marketing3\' requires approval.', '/erp_project/modules/projects/view_project_details.php?id=10', 1, '2025-06-11 16:41:15'),
(41, 16, 'New project \'Marketing3\' requires approval.', '/erp_project/modules/projects/view_project_details.php?id=10', 1, '2025-06-11 16:41:15'),
(42, 1, 'New project \'Marketing5\' requires approval.', '/erp_project/modules/projects/view_project_details.php?id=11', 1, '2025-06-11 16:42:48'),
(43, 1, 'New project \'Marketing5\' requires approval.', '/erp_project/modules/projects/view_project_details.php?id=11', 1, '2025-06-11 16:42:48'),
(44, 1, 'New project \'Marketing5\' requires approval.', '/erp_project/modules/projects/view_project_details.php?id=11', 1, '2025-06-11 16:42:48'),
(45, 1, 'New project \'Marketing5\' requires approval.', '/erp_project/modules/projects/view_project_details.php?id=11', 1, '2025-06-11 16:42:48'),
(46, 16, 'New project \'Marketing5\' requires approval.', '/erp_project/modules/projects/view_project_details.php?id=11', 1, '2025-06-11 16:42:48'),
(47, 1, 'New project \'Buy1\' requires approval.', '/erp_project/modules/projects/view_project_details.php?id=12', 1, '2025-06-11 16:53:22'),
(48, 1, 'New project \'Buy1\' requires approval.', '/erp_project/modules/projects/view_project_details.php?id=12', 1, '2025-06-11 16:53:22'),
(49, 1, 'New project \'Buy1\' requires approval.', '/erp_project/modules/projects/view_project_details.php?id=12', 1, '2025-06-11 16:53:22'),
(50, 1, 'New project \'Buy1\' requires approval.', '/erp_project/modules/projects/view_project_details.php?id=12', 1, '2025-06-11 16:53:22'),
(51, 1, 'New project \'buy2\' requires approval.', '/erp_project/modules/projects/view_project_details.php?id=13', 1, '2025-06-11 16:54:47'),
(52, 1, 'New project \'buy2\' requires approval.', '/erp_project/modules/projects/view_project_details.php?id=13', 1, '2025-06-11 16:54:47'),
(53, 1, 'New project \'buy2\' requires approval.', '/erp_project/modules/projects/view_project_details.php?id=13', 1, '2025-06-11 16:54:47'),
(54, 1, 'New project \'buy2\' requires approval.', '/erp_project/modules/projects/view_project_details.php?id=13', 1, '2025-06-11 16:54:47'),
(55, 1, 'New project \'buy3\' requires approval.', '/erp_project/modules/projects/view_project_details.php?id=14', 1, '2025-06-11 16:56:14'),
(56, 1, 'New project \'buy3\' requires approval.', '/erp_project/modules/projects/view_project_details.php?id=14', 1, '2025-06-11 16:56:14'),
(57, 1, 'New project \'buy3\' requires approval.', '/erp_project/modules/projects/view_project_details.php?id=14', 1, '2025-06-11 16:56:14'),
(58, 1, 'New project \'buy3\' requires approval.', '/erp_project/modules/projects/view_project_details.php?id=14', 1, '2025-06-11 16:56:14'),
(59, 5, 'New PO PO-2025-0037 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=39', 1, '2025-06-11 17:41:09'),
(60, 16, 'New PO PO-2025-0037 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=39', 1, '2025-06-11 17:41:09'),
(61, 5, 'Draft PO DRAFT-1749665657-17 has been submitted for approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=41', 1, '2025-06-11 18:14:38'),
(62, 16, 'Draft PO DRAFT-1749665657-17 has been submitted for approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=41', 1, '2025-06-11 18:14:38'),
(63, 5, 'Draft PO DRAFT-1749664967-16 has been submitted for approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=40', 1, '2025-06-11 18:14:41'),
(64, 16, 'Draft PO DRAFT-1749664967-16 has been submitted for approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=40', 1, '2025-06-11 18:14:41'),
(65, 5, 'Draft PO DRAFT-1749665657-17 has been submitted for approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=41', 1, '2025-06-11 18:15:00'),
(66, 16, 'Draft PO DRAFT-1749665657-17 has been submitted for approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=41', 1, '2025-06-11 18:15:00'),
(67, 1, 'Draft PO DRAFT-1749666174-10 has been submitted for approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=42', 1, '2025-06-11 18:23:10'),
(68, 5, 'Draft PO DRAFT-1749666174-10 has been submitted for approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=42', 1, '2025-06-11 18:23:10'),
(69, 16, 'Draft PO DRAFT-1749666174-10 has been submitted for approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=42', 1, '2025-06-11 18:23:10'),
(70, 1, 'Draft PO DRAFT-1749664967-16 has been submitted for approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=40', 1, '2025-06-11 18:28:55'),
(71, 5, 'Draft PO DRAFT-1749664967-16 has been submitted for approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=40', 1, '2025-06-11 18:28:55'),
(72, 16, 'Draft PO DRAFT-1749664967-16 has been submitted for approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=40', 1, '2025-06-11 18:28:55'),
(73, 1, 'Draft PO DRAFT-1749696425-13 has been submitted for approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=43', 1, '2025-06-12 02:48:13'),
(74, 5, 'Draft PO DRAFT-1749696425-13 has been submitted for approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=43', 1, '2025-06-12 02:48:13'),
(75, 16, 'Draft PO DRAFT-1749696425-13 has been submitted for approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=43', 1, '2025-06-12 02:48:13'),
(76, 1, 'Draft PO DRAFT-1749696425-20 has been submitted for approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=44', 1, '2025-06-12 02:48:33'),
(77, 5, 'Draft PO DRAFT-1749696425-20 has been submitted for approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=44', 1, '2025-06-12 02:48:33'),
(78, 16, 'Draft PO DRAFT-1749696425-20 has been submitted for approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=44', 1, '2025-06-12 02:48:33'),
(79, 5, 'New PO PO-2025-0045 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=45', 1, '2025-06-12 02:49:24'),
(80, 16, 'New PO PO-2025-0045 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=45', 1, '2025-06-12 02:49:24'),
(81, 5, 'New PO PO-2025-0046 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=46', 1, '2025-06-12 03:16:02'),
(82, 16, 'New PO PO-2025-0046 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=46', 1, '2025-06-12 03:16:02'),
(83, 1, 'Draft PO PO-2025-0046 has been submitted for approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=46', 1, '2025-06-12 03:16:16'),
(84, 5, 'Draft PO PO-2025-0046 has been submitted for approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=46', 1, '2025-06-12 03:16:16'),
(85, 16, 'Draft PO PO-2025-0046 has been submitted for approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=46', 1, '2025-06-12 03:16:16'),
(86, 5, 'New PO PO-2025-0047 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=47', 1, '2025-06-12 03:17:24'),
(87, 16, 'New PO PO-2025-0047 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=47', 1, '2025-06-12 03:17:24'),
(88, 1, 'New PO PO-2025-0048 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=48', 1, '2025-06-12 03:30:52'),
(89, 5, 'New PO PO-2025-0048 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=48', 1, '2025-06-12 03:30:52'),
(90, 16, 'New PO PO-2025-0048 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=48', 1, '2025-06-12 03:30:52'),
(91, 1, 'New task \'End\' added to project \'Marketing4\'', '/erp_project/modules/projects/view_project_details.php?id=7', 1, '2025-06-12 05:38:48'),
(92, 1, 'New task \'End\' added to project \'Buy1\'', '/erp_project/modules/projects/view_project_details.php?id=12', 1, '2025-06-12 05:39:16'),
(93, 1, 'New PO PO-2025-0049 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=49', 1, '2025-06-12 06:36:52'),
(94, 5, 'New PO PO-2025-0049 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=49', 1, '2025-06-12 06:36:52'),
(95, 16, 'New PO PO-2025-0049 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=49', 1, '2025-06-12 06:36:52'),
(96, 1, 'New PO PO-2025-0050 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=50', 1, '2025-06-12 06:38:04'),
(97, 5, 'New PO PO-2025-0050 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=50', 1, '2025-06-12 06:38:04'),
(98, 16, 'New PO PO-2025-0050 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=50', 1, '2025-06-12 06:38:04'),
(99, 1, 'New PO PO-2025-0051 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=51', 1, '2025-06-12 10:47:36'),
(100, 5, 'New PO PO-2025-0051 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=51', 1, '2025-06-12 10:47:36'),
(101, 16, 'New PO PO-2025-0051 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=51', 1, '2025-06-12 10:47:36'),
(102, 1, 'New PO PO-2025-0052 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=52', 1, '2025-06-12 10:57:53'),
(103, 5, 'New PO PO-2025-0052 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=52', 1, '2025-06-12 10:57:53'),
(104, 16, 'New PO PO-2025-0052 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=52', 1, '2025-06-12 10:57:53'),
(105, 1, 'New PO PO-2025-0053 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=53', 1, '2025-06-12 10:59:59'),
(106, 5, 'New PO PO-2025-0053 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=53', 1, '2025-06-12 10:59:59'),
(107, 16, 'New PO PO-2025-0053 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=53', 1, '2025-06-12 10:59:59'),
(108, 1, 'New project \'Marketing7\' requires approval.', '/erp_project/modules/projects/view_project_details.php?id=15', 1, '2025-06-13 05:04:21'),
(109, 1, 'New project \'Marketing7\' requires approval.', '/erp_project/modules/projects/view_project_details.php?id=15', 1, '2025-06-13 05:04:21'),
(110, 1, 'New project \'Marketing7\' requires approval.', '/erp_project/modules/projects/view_project_details.php?id=15', 1, '2025-06-13 05:04:21'),
(111, 1, 'New project \'Marketing7\' requires approval.', '/erp_project/modules/projects/view_project_details.php?id=15', 1, '2025-06-13 05:04:21'),
(112, 1, 'New project \'Marketing7\' requires approval.', '/erp_project/modules/projects/view_project_details.php?id=15', 1, '2025-06-13 05:04:21'),
(113, 1, 'New project \'Marketing7\' requires approval.', '/erp_project/modules/projects/view_project_details.php?id=15', 1, '2025-06-13 05:04:21'),
(114, 1, 'New project \'Marketing7\' requires approval.', '/erp_project/modules/projects/view_project_details.php?id=15', 1, '2025-06-13 05:04:21'),
(115, 16, 'New project \'Marketing7\' requires approval.', '/erp_project/modules/projects/view_project_details.php?id=15', 1, '2025-06-13 05:04:21'),
(116, 1, 'New project \'DD\' requires approval.', '/erp_project/modules/projects/view_project_details.php?id=16', 1, '2025-06-13 05:21:19'),
(117, 16, 'New project \'DD\' requires approval.', '/erp_project/modules/projects/view_project_details.php?id=16', 1, '2025-06-13 05:21:19'),
(118, 1, 'New project \'DD2\' requires approval.', '/erp_project/modules/projects/view_project_details.php?id=17', 1, '2025-06-13 05:31:23'),
(119, 16, 'New project \'DD2\' requires approval.', '/erp_project/modules/projects/view_project_details.php?id=17', 1, '2025-06-13 05:31:23'),
(120, 1, 'New project \'Marketing8\' requires approval.', '/erp_project/modules/projects/view_project_details.php?id=18', 1, '2025-06-14 15:19:26'),
(121, 16, 'New project \'Marketing8\' requires approval.', '/erp_project/modules/projects/view_project_details.php?id=18', 1, '2025-06-14 15:19:26'),
(122, 1, 'New PO PO-2025-0056 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=56', 1, '2025-08-06 05:17:43'),
(123, 5, 'New PO PO-2025-0056 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=56', 0, '2025-08-06 05:17:43'),
(124, 20, 'New PO PO-2025-0056 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=56', 0, '2025-08-06 05:17:43'),
(125, 16, 'New PO PO-2025-0056 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=56', 1, '2025-08-06 05:17:43'),
(126, 1, 'New PO PO-2025-0057 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=57', 1, '2025-08-06 05:39:48'),
(127, 5, 'New PO PO-2025-0057 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=57', 0, '2025-08-06 05:39:48'),
(128, 20, 'New PO PO-2025-0057 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=57', 0, '2025-08-06 05:39:48'),
(129, 16, 'New PO PO-2025-0057 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=57', 0, '2025-08-06 05:39:48'),
(130, 1, 'New PO PO-2025-0058 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=58', 1, '2025-08-06 06:52:01'),
(131, 5, 'New PO PO-2025-0058 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=58', 0, '2025-08-06 06:52:01'),
(132, 20, 'New PO PO-2025-0058 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=58', 0, '2025-08-06 06:52:01'),
(133, 16, 'New PO PO-2025-0058 requires approval.', '/erp_project/modules/purchase_orders/view_po_details.php?id=58', 0, '2025-08-06 06:52:01');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `po_id` int(11) NOT NULL,
  `payment_date` date NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `payment_method` enum('Bank Transfer','Credit','Cash','Other') NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `po_id`, `payment_date`, `amount_paid`, `payment_method`, `notes`, `created_at`) VALUES
(1, 4, '2025-06-10', 1750.00, 'Cash', '', '2025-06-10 10:40:03'),
(2, 4, '2025-06-10', 50.00, 'Credit', '', '2025-06-10 10:40:13'),
(3, 3, '2025-06-10', 3750.00, 'Cash', '', '2025-06-10 10:41:05'),
(4, 6, '2025-06-10', 105000.00, 'Bank Transfer', '', '2025-06-10 11:30:10'),
(5, 7, '2025-06-10', 68000.00, 'Bank Transfer', '', '2025-06-10 15:50:00'),
(6, 7, '2025-06-10', 7000.00, 'Bank Transfer', '', '2025-06-10 15:50:34'),
(7, 8, '2025-06-10', 770000.00, 'Bank Transfer', '', '2025-06-10 16:08:13'),
(8, 9, '2025-06-10', 22500.00, 'Bank Transfer', '', '2025-06-10 16:08:26'),
(9, 10, '2025-06-10', 50000.00, 'Bank Transfer', '', '2025-06-10 16:13:40'),
(10, 11, '2025-06-10', 30000.00, 'Bank Transfer', '', '2025-06-10 16:17:01'),
(11, 12, '2025-06-10', 3000.00, 'Bank Transfer', '', '2025-06-10 16:21:41'),
(12, 13, '2025-06-10', 20000.00, 'Bank Transfer', '', '2025-06-10 16:26:06'),
(13, 14, '2025-06-10', 25000.00, 'Bank Transfer', '', '2025-06-10 16:28:33'),
(14, 15, '2025-06-10', 49450.00, 'Bank Transfer', '', '2025-06-10 16:31:22'),
(15, 16, '2025-06-10', 30000.00, 'Bank Transfer', '', '2025-06-10 16:33:08'),
(16, 17, '2025-06-10', 30000.00, 'Bank Transfer', '', '2025-06-10 16:43:14'),
(17, 18, '2025-06-12', 20000.00, 'Bank Transfer', '', '2025-06-11 03:55:14'),
(18, 20, '2025-06-11', 3000.00, 'Bank Transfer', '', '2025-06-11 04:08:08'),
(19, 23, '2025-06-11', 300.00, 'Bank Transfer', '', '2025-06-11 04:24:51'),
(20, 22, '2025-06-11', 250.00, 'Bank Transfer', '', '2025-06-11 04:25:39'),
(21, 35, '2025-06-11', 23880.00, 'Bank Transfer', '', '2025-06-11 09:40:51'),
(22, 39, '2025-06-11', 698000.00, 'Bank Transfer', '', '2025-06-11 17:41:29'),
(23, 50, '2025-06-12', 100.00, 'Bank Transfer', '', '2025-06-12 07:48:45'),
(24, 51, '2025-06-12', 1325.00, 'Bank Transfer', '', '2025-06-12 10:48:21'),
(25, 57, '2025-08-06', 24000.00, 'Cash', 'Paid', '2025-08-06 05:40:27');

-- --------------------------------------------------------

--
-- Table structure for table `po_items`
--

CREATE TABLE `po_items` (
  `id` int(11) NOT NULL,
  `po_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `po_items`
--

INSERT INTO `po_items` (`id`, `po_id`, `product_id`, `quantity`, `unit_price`, `total_price`) VALUES
(1, 1, 3, 20, 150.00, 3000.00),
(2, 2, 3, 20, 150.00, 3000.00),
(3, 3, 3, 25, 150.00, 3750.00),
(4, 4, 3, 12, 150.00, 1800.00),
(5, 5, 4, 20, 800.00, 16000.00),
(6, 6, 4, 105, 1000.00, 105000.00),
(7, 7, 3, 500, 150.00, 75000.00),
(8, 8, 3, 5000, 150.00, 750000.00),
(9, 8, 4, 20, 1000.00, 20000.00),
(10, 9, 3, 150, 150.00, 22500.00),
(11, 10, 4, 50, 1000.00, 50000.00),
(12, 11, 3, 200, 150.00, 30000.00),
(13, 12, 3, 20, 150.00, 3000.00),
(14, 13, 4, 20, 1000.00, 20000.00),
(15, 14, 4, 25, 1000.00, 25000.00),
(16, 15, 4, 49, 1000.00, 49000.00),
(17, 15, 3, 3, 150.00, 450.00),
(18, 16, 3, 200, 150.00, 30000.00),
(19, 17, 3, 200, 150.00, 30000.00),
(20, 18, 4, 20, 1000.00, 20000.00),
(22, 20, 3, 20, 150.00, 3000.00),
(23, 21, 4, 2, 1000.00, 2000.00),
(24, 22, 3, 2, 150.00, 300.00),
(25, 23, 3, 2, 150.00, 300.00),
(26, 24, 3, 2, 150.00, 300.00),
(27, 24, 4, 1, 1000.00, 1000.00),
(30, 28, 3, 4, 150.00, 600.00),
(31, 29, 4, 2, 1000.00, 2000.00),
(32, 30, 4, 20, 1000.00, 20000.00),
(34, 32, 3, 20, 150.00, 3000.00),
(35, 33, 4, 12, 1000.00, 12000.00),
(36, 34, 4, 15, 1000.00, 15000.00),
(37, 35, 6, 199, 120.00, 23880.00),
(38, 36, 8, 12, 1250.00, 15000.00),
(39, 39, 16, 2000, 349.00, 698000.00),
(45, 41, 17, 50, 2299.00, 114950.00),
(47, 42, 10, 50, 79.99, 3999.50),
(49, 40, 16, 50, 349.00, 17450.00),
(52, 43, 13, 50, 499.00, 24950.00),
(53, 44, 20, 50, 699.00, 34950.00),
(54, 45, 7, 1199, 20.00, 23980.00),
(56, 46, 15, 133, 1099.00, 146167.00),
(57, 47, 10, 7, 79.99, 559.93),
(58, 48, 4, 1, 1000.00, 1000.00),
(59, 49, 7, 2, 20.00, 40.00),
(60, 50, 7, 5, 20.00, 100.00),
(61, 51, 9, 11, 120.50, 1325.50),
(62, 52, 11, 5, 1299.00, 6495.00),
(63, 53, 14, 5, 299.00, 1495.00),
(64, 54, 13, 50, 499.00, 24950.00),
(65, 55, 20, 50, 699.00, 34950.00),
(66, 56, 7, 1200, 20.00, 24000.00),
(68, 57, 7, 1200, 20.00, 24000.00),
(69, 58, 18, 50, 199.00, 9950.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `sku` varchar(100) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category_id` int(11) NOT NULL,
  `quantity_in_stock` int(11) NOT NULL DEFAULT 0,
  `reorder_point` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `sku`, `product_name`, `description`, `price`, `category_id`, `quantity_in_stock`, `reorder_point`, `created_at`, `updated_at`) VALUES
(3, '1 Ton', 'Rod', '18 mm Rod', 150.00, 1, 6334, NULL, '2025-06-10 10:07:15', '2025-06-11 04:26:17'),
(4, '200', 'testdata', 'testdataa', 1000.00, 5, 289, NULL, '2025-06-10 11:29:03', '2025-06-11 03:55:20'),
(6, '1200', 'tin', '20mm', 120.00, 4, 199, 200, '2025-06-11 09:38:59', '2025-06-11 17:36:04'),
(7, '20 MM nut', 'Nut', '20 MM nut', 20.00, 5, 2406, NULL, '2025-06-11 09:46:29', '2025-08-06 05:40:54'),
(8, 'LP-G15-DELL', 'Dell G15 Gaming Laptop', '15-inch, 16GB RAM, RTX 3050', 1250.00, 6, 0, NULL, '2025-06-11 09:53:48', '2025-06-11 09:53:48'),
(9, 'KB-LOG-MX', 'Logitech MX Keys Keyboard', 'Wireless illuminated keyboard', 120.50, 7, 11, NULL, '2025-06-11 09:53:48', '2025-06-12 10:48:14'),
(10, 'MS-LOG-G502', 'Logitech G502 Hero Mouse', 'High-performance wired gaming mouse', 79.99, 7, 7, 2, '2025-06-11 09:53:48', '2025-06-12 12:27:27'),
(11, 'SKU001', 'Wireless Mouse', 'Ergonomic wireless mouse with 1600 DPI sensitivity', 1299.00, 8, 5, NULL, '2025-06-11 15:19:28', '2025-06-12 10:58:19'),
(12, 'SKU002', 'Water Bottle', '1L stainless steel insulated water bottle', 899.00, 9, 0, NULL, '2025-06-11 15:19:28', '2025-06-11 15:19:28'),
(13, 'SKU003', 'Cotton T-Shirt', 'Unisex 100% cotton round-neck t-shirt (size M)', 499.00, 12, 0, 50, '2025-06-11 15:19:28', '2025-06-11 18:24:50'),
(14, 'SKU004', 'Notebook Set', 'Set of 3 A5 ruled notebooks with kraft cover', 299.00, 11, 5, NULL, '2025-06-11 15:19:28', '2025-06-12 11:00:29'),
(15, 'SKU005', 'USB-C Charger', 'Fast-charging 25W USB-C wall adapter', 1099.00, 8, 0, NULL, '2025-06-11 15:19:28', '2025-06-11 15:19:28'),
(16, 'SKU006', 'Coffee Mug', 'Ceramic coffee mug with motivational quote print', 349.00, 9, 2000, 2002, '2025-06-11 15:19:28', '2025-06-11 17:41:55'),
(17, 'SKU007', 'Sports Shoes', 'Lightweight breathable running shoes for men (size 42)', 2299.00, 12, 0, 20, '2025-06-11 15:19:28', '2025-06-11 18:13:58'),
(18, 'SKU008', 'Gel Pen Pack', 'Pack of 10 smooth-writing gel pens (black ink)', 199.00, 11, 0, NULL, '2025-06-11 15:19:28', '2025-06-11 15:19:28'),
(19, 'SKU009', 'LED Desk Lamp', 'Adjustable LED desk lamp with 3 brightness levels', 1799.00, 8, 0, NULL, '2025-06-11 15:19:28', '2025-06-11 15:19:28'),
(20, 'SKU010', 'Cushion Cover Set', 'Set of 5 decorative cushion covers (16x16 inch)', 699.00, 9, 0, 20, '2025-06-11 15:19:28', '2025-06-12 02:46:31'),
(21, 'SKU1000', 'Compact Phone', 'Eco-friendly and sustainable materials.', 313.10, 18, 247, 49, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(22, 'SKU1001', 'Smart Camera', 'Stylish and modern look.', 858.39, 11, 462, 16, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(23, 'SKU1002', 'Ergonomic Watch', 'A must-have for your collection.', 432.02, 19, 69, 13, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(24, 'SKU1003', 'Compact Gloves', 'Eco-friendly and sustainable materials.', 1304.28, 17, 64, 15, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(25, 'SKU1004', 'Wireless Lamp', 'Eco-friendly and sustainable materials.', 480.29, 14, 466, 5, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(26, 'SKU1005', 'Ergonomic Headphones', 'Eco-friendly and sustainable materials.', 660.21, 10, 379, 49, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(27, 'SKU1006', 'Adjustable Phone', 'Ergonomically designed for comfort.', 146.88, 1, 148, 26, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(28, 'SKU1007', 'Compact Watch', 'Durable and long-lasting.', 469.13, 16, 187, 49, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(29, 'SKU1008', 'Compact Watch', 'High quality and reliable product.', 339.52, 14, 140, 27, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(30, 'SKU1009', 'Smart Mouse', 'Lightweight and easy to use.', 141.42, 8, 9, 29, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(31, 'SKU1010', 'Ergonomic Toy Car', 'A must-have for your collection.', 435.52, 15, 433, 28, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(32, 'SKU1011', 'Wireless Bag', 'Eco-friendly and sustainable materials.', 609.13, 16, 256, 39, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(33, 'SKU1012', 'Premium Router', 'Durable and long-lasting.', 50.50, 15, 66, 49, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(34, 'SKU1013', 'Durable Shirt', 'Eco-friendly and sustainable materials.', 1378.74, 1, 350, 11, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(35, 'SKU1014', 'Durable Toy Car', 'Best in class performance.', 191.85, 16, 337, 12, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(36, 'SKU1015', 'Premium Keyboard', 'Ergonomically designed for comfort.', 78.52, 14, 229, 17, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(37, 'SKU1016', 'Premium Book', 'Stylish and modern look.', 1456.85, 1, 144, 18, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(38, 'SKU1017', 'Wireless Shirt', 'Best in class performance.', 350.69, 21, 292, 8, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(39, 'SKU1018', 'Lightweight Pen', 'Suitable for daily use.', 477.84, 10, 214, 14, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(40, 'SKU1019', 'Adjustable Keyboard', 'Ergonomically designed for comfort.', 1216.56, 21, 444, 37, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(41, 'SKU1020', 'Portable Headphones', 'Eco-friendly and sustainable materials.', 1481.76, 9, 314, 26, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(42, 'SKU1021', 'Adjustable Phone', 'A must-have for your collection.', 1424.07, 19, 354, 36, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(43, 'SKU1022', 'Ergonomic Shirt', 'Best in class performance.', 822.32, 8, 306, 39, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(44, 'SKU1023', 'Adjustable Microwave', 'Lightweight and easy to use.', 39.25, 18, 194, 25, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(45, 'SKU1024', 'Smart Chair', 'Stylish and modern look.', 507.09, 11, 381, 48, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(46, 'SKU1025', 'Smart Shoes', 'High quality and reliable product.', 912.72, 10, 349, 17, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(47, 'SKU1026', 'Portable Shoes', 'High quality and reliable product.', 464.87, 21, 191, 11, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(48, 'SKU1027', 'Portable Shoes', 'Ergonomically designed for comfort.', 1057.12, 8, 20, 12, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(49, 'SKU1028', 'Smart Shirt', 'Durable and long-lasting.', 1452.43, 18, 181, 30, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(50, 'SKU1029', 'Compact Camera', 'Suitable for daily use.', 236.71, 10, 261, 47, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(51, 'SKU1030', 'Eco Lamp', 'Eco-friendly and sustainable materials.', 495.46, 6, 105, 47, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(52, 'SKU1031', 'Wireless Mixer', 'Durable and long-lasting.', 974.41, 1, 252, 28, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(53, 'SKU1032', 'Eco Chair', 'Compact and portable design.', 124.79, 1, 179, 50, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(54, 'SKU1033', 'Premium Mixer', 'Lightweight and easy to use.', 1389.13, 8, 267, 30, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(55, 'SKU1034', 'Portable Mixer', 'Stylish and modern look.', 1125.86, 8, 184, 36, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(56, 'SKU1035', 'Wireless Shirt', 'Durable and long-lasting.', 1129.58, 10, 47, 10, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(57, 'SKU1036', 'Wireless Headphones', 'Lightweight and easy to use.', 1457.02, 18, 132, 46, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(58, 'SKU1037', 'Compact Router', 'Compact and portable design.', 636.17, 17, 288, 39, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(59, 'SKU1038', 'Ergonomic Bag', 'Best in class performance.', 394.15, 20, 86, 33, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(60, 'SKU1039', 'Premium Shirt', 'Suitable for daily use.', 140.34, 7, 268, 16, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(61, 'SKU1040', 'Premium Mixer', 'Eco-friendly and sustainable materials.', 1146.68, 7, 48, 30, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(62, 'SKU1041', 'Smart Book', 'Ergonomically designed for comfort.', 1494.28, 10, 453, 43, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(63, 'SKU1042', 'Durable Bag', 'Durable and long-lasting.', 1308.93, 10, 41, 47, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(64, 'SKU1043', 'Eco Watch', 'Stylish and modern look.', 384.43, 7, 115, 46, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(65, 'SKU1044', 'Ergonomic Lamp', 'Durable and long-lasting.', 1170.87, 8, 39, 6, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(66, 'SKU1045', 'Eco Router', 'Lightweight and easy to use.', 463.50, 1, 16, 28, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(67, 'SKU1046', 'Portable Shoes', 'High quality and reliable product.', 376.83, 16, 148, 32, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(68, 'SKU1047', 'Portable Notebook', 'Suitable for daily use.', 1308.01, 19, 299, 37, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(69, 'SKU1048', 'Eco Table', 'Ergonomically designed for comfort.', 1156.61, 11, 361, 15, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(70, 'SKU1049', 'Premium Shoes', 'Ergonomically designed for comfort.', 1080.07, 6, 70, 47, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(71, 'SKU1050', 'Smart Bag', 'Stylish and modern look.', 1486.52, 11, 489, 11, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(72, 'SKU1051', 'Eco Shirt', 'Best in class performance.', 136.25, 9, 366, 46, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(73, 'SKU1052', 'Portable Camera', 'High quality and reliable product.', 171.13, 14, 219, 34, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(74, 'SKU1053', 'Adjustable Book', 'Lightweight and easy to use.', 688.40, 1, 223, 44, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(75, 'SKU1054', 'Premium Chair', 'Durable and long-lasting.', 1321.65, 16, 187, 31, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(76, 'SKU1055', 'Ergonomic Notebook', 'Durable and long-lasting.', 1271.57, 8, 295, 10, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(77, 'SKU1056', 'Durable Bag', 'Eco-friendly and sustainable materials.', 1238.78, 9, 335, 9, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(78, 'SKU1057', 'Adjustable Watch', 'Durable and long-lasting.', 588.48, 19, 294, 14, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(79, 'SKU1058', 'Ergonomic Shoes', 'High quality and reliable product.', 1472.74, 15, 187, 29, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(80, 'SKU1059', 'Wireless Notebook', 'Durable and long-lasting.', 893.88, 21, 140, 39, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(81, 'SKU1060', 'Smart Book', 'Compact and portable design.', 46.05, 10, 488, 31, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(82, 'SKU1061', 'Lightweight Shoes', 'Best in class performance.', 1070.28, 18, 203, 11, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(83, 'SKU1062', 'Durable Keyboard', 'A must-have for your collection.', 333.92, 7, 170, 12, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(84, 'SKU1063', 'Wireless Mixer', 'Durable and long-lasting.', 821.50, 18, 386, 17, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(85, 'SKU1064', 'Smart Watch', 'Suitable for daily use.', 242.39, 18, 359, 10, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(86, 'SKU1065', 'Eco Pen', 'Compact and portable design.', 303.97, 16, 273, 18, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(87, 'SKU1066', 'Portable Camera', 'Stylish and modern look.', 69.52, 21, 358, 33, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(88, 'SKU1067', 'Ergonomic Bag', 'Best in class performance.', 155.93, 16, 469, 35, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(89, 'SKU1068', 'Lightweight Toy Car', 'Compact and portable design.', 1440.68, 17, 229, 30, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(90, 'SKU1069', 'Premium Book', 'Eco-friendly and sustainable materials.', 1044.02, 16, 11, 44, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(91, 'SKU1070', 'Portable Keyboard', 'Lightweight and easy to use.', 442.61, 19, 69, 14, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(92, 'SKU1071', 'Durable Chair', 'A must-have for your collection.', 272.83, 19, 251, 49, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(93, 'SKU1072', 'Smart Camera', 'Best in class performance.', 1111.01, 10, 286, 8, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(94, 'SKU1073', 'Lightweight Gloves', 'Best in class performance.', 169.35, 18, 34, 37, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(95, 'SKU1074', 'Smart Microwave', 'Ergonomically designed for comfort.', 1048.77, 7, 188, 22, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(96, 'SKU1075', 'Compact Lamp', 'Durable and long-lasting.', 424.64, 19, 460, 39, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(97, 'SKU1076', 'Ergonomic Lamp', 'Best in class performance.', 25.30, 10, 199, 31, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(98, 'SKU1077', 'Wireless Microwave', 'Eco-friendly and sustainable materials.', 60.53, 9, 222, 43, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(99, 'SKU1078', 'Eco Mixer', 'Lightweight and easy to use.', 1171.58, 18, 141, 9, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(100, 'SKU1079', 'Adjustable Microwave', 'A must-have for your collection.', 714.43, 1, 294, 40, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(101, 'SKU1080', 'Eco Router', 'Compact and portable design.', 428.69, 7, 211, 49, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(102, 'SKU1081', 'Adjustable Notebook', 'Best in class performance.', 532.07, 14, 91, 49, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(103, 'SKU1082', 'Adjustable Mixer', 'A must-have for your collection.', 501.46, 15, 115, 34, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(104, 'SKU1083', 'Durable Keyboard', 'High quality and reliable product.', 616.97, 9, 294, 17, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(105, 'SKU1084', 'Compact Camera', 'Lightweight and easy to use.', 1147.83, 18, 68, 5, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(106, 'SKU1085', 'Smart Phone', 'Stylish and modern look.', 1469.94, 18, 356, 49, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(107, 'SKU1086', 'Adjustable Toy Car', 'Eco-friendly and sustainable materials.', 429.03, 16, 366, 6, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(108, 'SKU1087', 'Ergonomic Keyboard', 'Ergonomically designed for comfort.', 345.48, 7, 68, 42, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(109, 'SKU1088', 'Lightweight Pen', 'Best in class performance.', 302.33, 7, 462, 46, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(110, 'SKU1089', 'Wireless Lamp', 'A must-have for your collection.', 877.06, 19, 252, 25, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(111, 'SKU1090', 'Compact Book', 'Suitable for daily use.', 1424.60, 14, 26, 28, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(112, 'SKU1091', 'Ergonomic Notebook', 'Lightweight and easy to use.', 514.00, 6, 262, 41, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(113, 'SKU1092', 'Smart Watch', 'Compact and portable design.', 1340.28, 18, 219, 15, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(114, 'SKU1093', 'Compact Chair', 'Best in class performance.', 69.04, 18, 124, 25, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(115, 'SKU1094', 'Eco Chair', 'Best in class performance.', 1353.84, 20, 499, 10, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(116, 'SKU1095', 'Wireless Shirt', 'Lightweight and easy to use.', 1237.58, 18, 132, 30, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(117, 'SKU1096', 'Adjustable Notebook', 'Lightweight and easy to use.', 870.86, 20, 146, 25, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(118, 'SKU1097', 'Smart Notebook', 'Durable and long-lasting.', 1012.49, 1, 436, 46, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(119, 'SKU1098', 'Eco Microwave', 'A must-have for your collection.', 440.56, 7, 88, 45, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(120, 'SKU1099', 'Eco Router', 'Stylish and modern look.', 525.79, 16, 252, 34, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(121, 'SKU1100', 'Compact Headphones', 'Best in class performance.', 521.93, 19, 81, 9, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(122, 'SKU1101', 'Durable Notebook', 'Durable and long-lasting.', 477.59, 9, 261, 7, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(123, 'SKU1102', 'Wireless Mouse', 'Stylish and modern look.', 627.15, 16, 319, 11, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(124, 'SKU1103', 'Adjustable Microwave', 'Best in class performance.', 247.77, 10, 219, 10, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(125, 'SKU1104', 'Eco Mouse', 'Suitable for daily use.', 401.17, 11, 5, 20, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(126, 'SKU1105', 'Smart Shoes', 'Stylish and modern look.', 28.05, 10, 157, 26, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(127, 'SKU1106', 'Premium Table', 'Eco-friendly and sustainable materials.', 1028.00, 20, 282, 27, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(128, 'SKU1107', 'Premium Router', 'Eco-friendly and sustainable materials.', 500.70, 14, 10, 23, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(129, 'SKU1108', 'Lightweight Headphones', 'Lightweight and easy to use.', 435.19, 1, 172, 15, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(130, 'SKU1109', 'Ergonomic Mouse', 'Suitable for daily use.', 1051.65, 17, 235, 31, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(131, 'SKU1110', 'Premium Pen', 'Durable and long-lasting.', 1437.46, 7, 28, 25, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(132, 'SKU1111', 'Premium Microwave', 'Compact and portable design.', 527.44, 6, 309, 32, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(133, 'SKU1112', 'Wireless Watch', 'Stylish and modern look.', 429.61, 19, 325, 37, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(134, 'SKU1113', 'Eco Table', 'Ergonomically designed for comfort.', 791.90, 21, 2, 49, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(135, 'SKU1114', 'Adjustable Watch', 'A must-have for your collection.', 290.17, 21, 229, 8, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(136, 'SKU1115', 'Ergonomic Router', 'Lightweight and easy to use.', 536.89, 20, 120, 30, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(137, 'SKU1116', 'Ergonomic Camera', 'Durable and long-lasting.', 772.41, 14, 486, 18, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(138, 'SKU1117', 'Durable Watch', 'A must-have for your collection.', 286.42, 16, 442, 5, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(139, 'SKU1118', 'Smart Notebook', 'High quality and reliable product.', 218.67, 21, 89, 17, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(140, 'SKU1119', 'Compact Lamp', 'A must-have for your collection.', 1215.23, 21, 361, 49, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(141, 'SKU1120', 'Durable Shoes', 'Eco-friendly and sustainable materials.', 328.28, 20, 15, 10, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(142, 'SKU1121', 'Portable Router', 'Durable and long-lasting.', 1035.12, 16, 46, 27, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(143, 'SKU1122', 'Ergonomic Toy Car', 'Compact and portable design.', 327.27, 8, 22, 38, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(144, 'SKU1123', 'Adjustable Chair', 'Stylish and modern look.', 1255.51, 7, 433, 13, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(145, 'SKU1124', 'Portable Keyboard', 'Suitable for daily use.', 746.71, 21, 353, 48, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(146, 'SKU1125', 'Premium Book', 'Ergonomically designed for comfort.', 246.60, 21, 209, 20, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(147, 'SKU1126', 'Wireless Shoes', 'Lightweight and easy to use.', 313.84, 7, 25, 6, '2025-08-13 17:47:49', '2025-08-13 17:47:49'),
(148, 'SKU1127', 'Compact Watch', 'Ergonomically designed for comfort.', 1055.85, 7, 89, 33, '2025-08-13 17:47:50', '2025-08-13 17:47:50'),
(149, 'SKU1128', 'Premium Chair', 'Eco-friendly and sustainable materials.', 803.20, 9, 85, 43, '2025-08-13 17:47:50', '2025-08-13 17:47:50'),
(150, 'SKU1129', 'Durable Toy Car', 'Ergonomically designed for comfort.', 1054.45, 1, 364, 6, '2025-08-13 17:47:50', '2025-08-13 17:47:50'),
(151, 'SKU1130', 'Smart Shoes', 'Stylish and modern look.', 1213.90, 6, 208, 47, '2025-08-13 17:47:50', '2025-08-13 17:47:50'),
(152, 'SKU1131', 'Compact Phone', 'A must-have for your collection.', 1167.83, 20, 314, 12, '2025-08-13 17:47:50', '2025-08-13 17:47:50'),
(153, 'SKU1132', 'Portable Bag', 'Ergonomically designed for comfort.', 641.39, 16, 66, 44, '2025-08-13 17:47:50', '2025-08-13 17:47:50'),
(154, 'SKU1133', 'Durable Camera', 'Best in class performance.', 1353.18, 1, 368, 27, '2025-08-13 17:47:50', '2025-08-13 17:47:50'),
(155, 'SKU1134', 'Eco Camera', 'Stylish and modern look.', 1414.32, 11, 83, 24, '2025-08-13 17:47:50', '2025-08-13 17:47:50'),
(156, 'SKU1135', 'Compact Mixer', 'Durable and long-lasting.', 342.20, 8, 452, 39, '2025-08-13 17:47:50', '2025-08-13 17:47:50'),
(157, 'SKU1136', 'Adjustable Shoes', 'Stylish and modern look.', 789.13, 17, 410, 8, '2025-08-13 17:47:50', '2025-08-13 17:47:50'),
(158, 'SKU1137', 'Lightweight Bag', 'Eco-friendly and sustainable materials.', 1025.43, 21, 437, 36, '2025-08-13 17:47:50', '2025-08-13 17:47:50'),
(159, 'SKU1138', 'Lightweight Book', 'Stylish and modern look.', 1250.20, 10, 75, 18, '2025-08-13 17:47:50', '2025-08-13 17:47:50'),
(160, 'SKU1139', 'Adjustable Bag', 'Best in class performance.', 183.02, 15, 31, 17, '2025-08-13 17:47:50', '2025-08-13 17:47:50'),
(161, 'SKU1140', 'Durable Notebook', 'High quality and reliable product.', 958.94, 20, 6, 19, '2025-08-13 17:47:50', '2025-08-13 17:47:50'),
(162, 'SKU1141', 'Durable Headphones', 'High quality and reliable product.', 1224.10, 8, 135, 11, '2025-08-13 17:47:50', '2025-08-13 17:47:50'),
(163, 'SKU1142', 'Premium Chair', 'Compact and portable design.', 805.49, 1, 179, 49, '2025-08-13 17:47:50', '2025-08-13 17:47:50'),
(164, 'SKU1143', 'Wireless Chair', 'A must-have for your collection.', 1233.03, 17, 28, 48, '2025-08-13 17:47:50', '2025-08-13 17:47:50'),
(165, 'SKU1144', 'Ergonomic Mouse', 'High quality and reliable product.', 951.73, 16, 38, 11, '2025-08-13 17:47:50', '2025-08-13 17:47:50'),
(166, 'SKU1145', 'Premium Chair', 'Durable and long-lasting.', 1353.20, 17, 281, 48, '2025-08-13 17:47:50', '2025-08-13 17:47:50'),
(167, 'SKU1146', 'Lightweight Notebook', 'Suitable for daily use.', 865.61, 10, 144, 44, '2025-08-13 17:47:50', '2025-08-13 17:47:50'),
(168, 'SKU1147', 'Adjustable Router', 'High quality and reliable product.', 1496.28, 15, 225, 38, '2025-08-13 17:47:50', '2025-08-13 17:47:50'),
(169, 'SKU1148', 'Smart Table', 'Ergonomically designed for comfort.', 818.97, 17, 303, 40, '2025-08-13 17:47:50', '2025-08-13 17:47:50'),
(170, 'SKU1149', 'Premium Watch', 'Best in class performance.', 435.37, 17, 409, 50, '2025-08-13 17:47:50', '2025-08-13 17:47:50'),
(171, 'wsfhsyiufgwifff;sfhfiuhf8959856', 'wsegawgawgkjhwgfjgwegiuhwiufhiugfhqiugheqiughewgiu`:`', 'wsegawgawgkjhwgfjgwegiuhwiufhiugfhqiugheqiughewgiu`:`', 88520.00, 12, 0, 963, '2025-08-19 02:47:25', '2025-08-19 02:47:25');

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `product_categories`
--

INSERT INTO `product_categories` (`id`, `category_name`, `created_at`) VALUES
(1, 'Rod', '2025-06-10 09:04:39'),
(4, 'New', '2025-06-10 11:26:00'),
(5, 'fake', '2025-06-10 11:26:03'),
(6, 'Laptops', '2025-06-11 09:53:36'),
(7, 'Peripherals', '2025-06-11 09:53:42'),
(8, 'Electronics', '2025-06-11 15:18:14'),
(9, 'Home & Kitchen', '2025-06-11 15:18:47'),
(10, 'Fashion', '2025-06-11 15:19:00'),
(11, 'Stationery', '2025-06-11 15:19:06'),
(12, 'Fashion', '2025-06-11 15:19:16'),
(13, 'Vegitables', '2025-08-06 06:53:26'),
(14, 'Sports', '2025-08-13 16:34:22'),
(15, 'Toys', '2025-08-13 16:34:34'),
(16, 'Books', '2025-08-13 16:34:41'),
(17, 'Beauty', '2025-08-13 16:34:47'),
(18, 'Automotive', '2025-08-13 16:34:52'),
(19, 'Groceries', '2025-08-13 16:34:57'),
(20, 'Furniture', '2025-08-13 16:35:02'),
(21, 'Vegetables', '2025-08-13 17:47:49');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `project_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `project_budget` decimal(12,2) DEFAULT NULL,
  `budget_id` int(11) DEFAULT NULL COMMENT 'Link to a specific budget',
  `manager_id` int(11) DEFAULT NULL COMMENT 'Which user is the project manager',
  `client_id` int(11) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Pending Approval',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `project_name`, `description`, `start_date`, `end_date`, `project_budget`, `budget_id`, `manager_id`, `client_id`, `status`, `created_at`) VALUES
(8, 'Marketing', 'Marketing', '2025-06-11', '2025-06-12', 12000.00, 4, 16, NULL, 'Rejected', '2025-06-11 16:29:26'),
(9, 'Marketing2', 'Marketing2', '2025-06-11', '2025-06-12', 12000.00, 3, 8, NULL, 'In Progress', '2025-06-11 16:37:30'),
(10, 'Marketing3', 'Marketing3', '2025-06-11', '2025-06-12', 12000.00, 3, 1, NULL, 'Approved', '2025-06-11 16:41:15'),
(12, 'Buy1', 'Buy1', '2025-06-11', '2025-06-13', 1200.00, 3, 8, NULL, 'Approved', '2025-06-11 16:53:22'),
(13, 'buy2', 'buy2', '2025-06-11', '2025-06-13', 20000.00, 4, 1, NULL, 'Approved', '2025-06-11 16:54:47'),
(14, 'buy3', 'buy3', '2025-06-11', '2025-06-17', 20000.00, 4, 1, NULL, 'Approved', '2025-06-11 16:56:14'),
(15, 'Marketing7', 'Marketing7', '2025-06-13', '2025-06-19', 500.00, 5, 1, NULL, 'In Progress', '2025-06-13 05:04:21'),
(16, 'DD', 'test', '2025-06-13', '2025-06-24', 80.00, 5, 1, 1, 'Canceled', '2025-06-13 05:21:19');

-- --------------------------------------------------------

--
-- Table structure for table `project_tasks`
--

CREATE TABLE `project_tasks` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `task_name` varchar(255) NOT NULL,
  `assigned_to_user_id` int(11) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'To Do'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `project_tasks`
--

INSERT INTO `project_tasks` (`id`, `project_id`, `task_name`, `assigned_to_user_id`, `due_date`, `status`) VALUES
(6, 12, 'End', 15, '2025-06-12', 'To Do');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `id` int(11) NOT NULL,
  `po_number` varchar(50) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `budget_id` int(11) DEFAULT NULL,
  `order_date` date NOT NULL,
  `expected_delivery_date` date DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` varchar(50) NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `purchase_orders`
--

INSERT INTO `purchase_orders` (`id`, `po_number`, `supplier_id`, `budget_id`, `order_date`, `expected_delivery_date`, `total_amount`, `status`, `created_at`) VALUES
(1, 'PO-2025-0001', 5, NULL, '2025-06-10', NULL, 3000.00, 'Rejected', '2025-06-10 10:07:30'),
(2, 'PO-2025-0002', 5, NULL, '2025-06-10', NULL, 3000.00, 'Completed', '2025-06-10 10:08:12'),
(3, 'PO-2025-0003', 6, NULL, '2025-06-10', NULL, 3750.00, 'Completed', '2025-06-10 10:33:34'),
(4, 'PO-2025-0004', 5, NULL, '2025-06-10', NULL, 1800.00, 'Completed', '2025-06-10 10:34:23'),
(5, 'PO-2025-0005', 6, NULL, '2025-06-10', NULL, 16000.00, 'Rejected', '2025-06-10 11:29:25'),
(6, 'PO-2025-0006', 5, NULL, '2025-06-10', NULL, 105000.00, 'Completed', '2025-06-10 11:30:01'),
(7, 'PO-2025-0007', 6, NULL, '2025-06-10', NULL, 75000.00, 'Completed', '2025-06-10 15:49:39'),
(8, 'PO-2025-0008', 6, NULL, '2025-06-10', NULL, 770000.00, 'Completed', '2025-06-10 15:57:10'),
(9, 'PO-2025-0009', 5, NULL, '2025-06-10', NULL, 22500.00, 'Completed', '2025-06-10 16:05:42'),
(10, 'PO-2025-0010', 6, NULL, '2025-06-10', NULL, 50000.00, 'Completed', '2025-06-10 16:13:29'),
(11, 'PO-2025-0011', 6, NULL, '2025-06-10', NULL, 30000.00, 'Completed', '2025-06-10 16:16:50'),
(12, 'PO-2025-0012', 6, NULL, '2025-06-10', NULL, 3000.00, 'Completed', '2025-06-10 16:21:28'),
(13, 'PO-2025-0013', 5, NULL, '2025-06-10', NULL, 20000.00, 'Completed', '2025-06-10 16:25:50'),
(14, 'PO-2025-0014', 6, NULL, '2025-06-10', NULL, 25000.00, 'Completed', '2025-06-10 16:28:20'),
(15, 'PO-2025-0015', 5, NULL, '2025-06-10', NULL, 49450.00, 'Completed', '2025-06-10 16:31:00'),
(16, 'PO-2025-0016', 5, NULL, '2025-06-09', NULL, 30000.00, 'Completed', '2025-06-10 16:32:48'),
(17, 'PO-2025-0017', 6, NULL, '2025-06-25', NULL, 30000.00, 'Completed', '2025-06-10 16:42:16'),
(18, 'PO-2025-0018', 6, NULL, '0000-00-00', NULL, 20000.00, 'Completed', '2025-06-11 03:52:26'),
(20, 'PO-2025-0020', 5, NULL, '0000-00-00', NULL, 3000.00, 'Completed', '2025-06-11 04:07:48'),
(21, 'PO-2025-0021', 6, NULL, '2025-06-11', NULL, 2000.00, 'Approved', '2025-06-11 04:18:25'),
(22, 'PO-2025-0022', 5, NULL, '2025-06-11', NULL, 300.00, 'Completed', '2025-06-11 04:18:36'),
(23, 'PO-2025-0023', 5, NULL, '2025-06-11', NULL, 300.00, 'Completed', '2025-06-11 04:19:58'),
(24, 'PO-2025-0024', 6, NULL, '2025-06-11', NULL, 1300.00, 'Rejected', '2025-06-11 04:31:33'),
(27, 'PO-2025-0027', 5, NULL, '2025-06-12', NULL, 1050.00, 'Approved', '2025-06-11 04:59:21'),
(28, 'PO-2025-0028', 6, NULL, '2025-06-11', NULL, 600.00, 'Rejected', '2025-06-11 05:04:28'),
(29, 'PO-2025-0029', 5, NULL, '2025-06-13', NULL, 2000.00, 'Rejected', '2025-06-11 05:04:42'),
(30, 'PO-2025-0030', 5, 3, '2025-06-12', NULL, 20000.00, 'Rejected', '2025-06-11 05:14:49'),
(32, 'PO-2025-0031', 5, 3, '2025-06-11', NULL, 3000.00, 'Approved', '2025-06-11 06:51:01'),
(33, 'PO-2025-0033', 5, 3, '2025-06-11', NULL, 12000.00, 'Approved', '2025-06-11 06:51:55'),
(34, 'PO-2025-0034', 5, 3, '2025-06-11', NULL, 15000.00, 'Rejected', '2025-06-11 06:59:11'),
(35, 'PO-2025-0035', 6, 3, '2025-06-11', NULL, 23880.00, 'Approved', '2025-06-11 09:40:13'),
(36, 'PO-2025-0036', 6, 3, '2025-06-11', NULL, 15000.00, 'Approved', '2025-06-11 15:23:16'),
(39, 'PO-2025-0037', 5, NULL, '2025-06-11', NULL, 698000.00, 'Completed', '2025-06-11 17:41:09'),
(40, 'DRAFT-1749664967-16', 5, NULL, '2025-06-11', NULL, 17450.00, 'Approved', '2025-06-11 18:02:47'),
(41, 'DRAFT-1749665657-17', 5, NULL, '2025-06-11', NULL, 114950.00, 'Approved', '2025-06-11 18:14:17'),
(42, 'DRAFT-1749666174-10', 6, NULL, '2025-06-11', NULL, 3999.50, 'Approved', '2025-06-11 18:22:55'),
(43, 'DRAFT-1749696425-13', 8, NULL, '2025-06-12', NULL, 24950.00, 'Rejected', '2025-06-12 02:47:05'),
(44, 'DRAFT-1749696425-20', 6, NULL, '2025-06-12', NULL, 34950.00, 'Rejected', '2025-06-12 02:47:05'),
(45, 'PO-2025-0045', 5, 3, '2025-06-12', NULL, 23980.00, 'Completed', '2025-06-12 02:49:24'),
(46, 'PO-2025-0046', 5, 4, '2025-06-12', NULL, 146167.00, 'Approved', '2025-06-12 03:16:02'),
(47, 'PO-2025-0047', 8, 4, '2025-06-12', NULL, 559.93, 'Completed', '2025-06-12 03:17:24'),
(48, 'PO-2025-0048', 9, 4, '2025-06-12', NULL, 1000.00, 'Approved', '2025-06-12 03:30:52'),
(49, 'PO-2025-0049', 9, 4, '2025-06-12', NULL, 40.00, 'Completed', '2025-06-12 06:36:52'),
(50, 'PO-2025-0050', 9, 5, '2025-06-12', NULL, 100.00, 'Completed', '2025-06-12 06:38:04'),
(51, 'PO-2025-0051', 6, 5, '2025-06-12', NULL, 1325.50, 'Completed', '2025-06-12 10:47:36'),
(52, 'PO-2025-0052', 9, 5, '2025-06-12', NULL, 6495.00, 'Completed', '2025-06-12 10:57:53'),
(53, 'PO-2025-0053', 9, 5, '2025-06-12', NULL, 1495.00, 'Completed', '2025-06-12 10:59:59'),
(54, 'DRAFT-1749734803-13', 8, NULL, '2025-06-12', NULL, 24950.00, 'Draft', '2025-06-12 13:26:43'),
(55, 'DRAFT-1749734803-20', 6, NULL, '2025-06-12', NULL, 34950.00, 'Draft', '2025-06-12 13:26:43'),
(56, 'PO-2025-0056', 6, NULL, '2025-08-06', NULL, 24000.00, 'Rejected', '2025-08-06 05:17:43'),
(57, 'PO-2025-0057', 11, 7, '2025-08-06', NULL, 24000.00, 'Completed', '2025-08-06 05:39:48'),
(58, 'PO-2025-0058', 14, 7, '2025-08-06', NULL, 9950.00, 'Approved', '2025-08-06 06:52:01');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`) VALUES
(13, 'Auditor / Compliance'),
(11, 'Customer / Client'),
(3, 'Department Manager'),
(5, 'Finance Officer'),
(6, 'HR Officer'),
(7, 'Inventory Officer'),
(4, 'Procurement Officer'),
(8, 'Project Manager'),
(2, 'Super Admin / ED'),
(1, 'System Admin'),
(9, 'Team Member / Employee'),
(10, 'Vendor / Supplier'),
(12, 'View-Only / Analyst');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `permission_key` varchar(100) NOT NULL COMMENT 'e.g., po_create, po_approve, supplier_edit'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`id`, `role_id`, `permission_key`) VALUES
(120, 1, 'client_manage'),
(30, 1, 'invoice_delete'),
(29, 1, 'invoice_edit'),
(65, 1, 'product_supplier_manage'),
(68, 1, 'project_full_access'),
(33, 1, 'supplier_delete'),
(22, 1, 'user_manage'),
(37, 2, 'asset_view'),
(2, 2, 'budget_approve'),
(3, 2, 'finance_view'),
(67, 2, 'invoice_approve'),
(28, 2, 'invoice_view'),
(21, 2, 'payment_manage'),
(1, 2, 'po_approve'),
(4, 2, 'procurement_view'),
(5, 2, 'project_full_access'),
(6, 2, 'reports_full_access'),
(117, 2, 'supplier_deactivate'),
(34, 2, 'supplier_delete'),
(70, 2, 'supplier_info_approve'),
(72, 2, 'supplier_rate'),
(38, 3, 'asset_view'),
(8, 3, 'budget_approve'),
(25, 3, 'hr_view_department'),
(7, 3, 'po_approve'),
(32, 3, 'product_supplier_manage'),
(84, 3, 'project_approve'),
(9, 3, 'project_create'),
(11, 3, 'reports_department_only'),
(118, 3, 'supplier_deactivate'),
(69, 3, 'supplier_info_approve'),
(71, 3, 'supplier_rate'),
(14, 4, 'inventory_view'),
(12, 4, 'po_create'),
(13, 4, 'po_edit'),
(31, 4, 'product_supplier_manage'),
(41, 4, 'product_view'),
(15, 4, 'reports_po_only'),
(40, 4, 'supplier_view'),
(39, 5, 'asset_view'),
(17, 5, 'budget_manage'),
(47, 5, 'finance_view'),
(19, 5, 'inventory_view'),
(26, 5, 'invoice_manage'),
(27, 5, 'invoice_view'),
(16, 5, 'payment_manage'),
(24, 5, 'payroll_view'),
(18, 5, 'po_view'),
(20, 5, 'reports_finance_only'),
(48, 5, 'supplier_view'),
(23, 6, 'hr_manage'),
(53, 6, 'hr_view'),
(35, 7, 'asset_manage'),
(36, 7, 'asset_view'),
(52, 7, 'inventory_view'),
(60, 8, 'project_full_access'),
(63, 9, 'project_my_tasks_view'),
(54, 10, 'invoice_upload'),
(119, 11, 'project_status_view_own');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `supplier_name` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `tax_id` varchar(100) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `bank_name` varchar(255) DEFAULT NULL,
  `bank_account_number` varchar(100) DEFAULT NULL,
  `bank_branch_code` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `rating_delivery_time` decimal(2,1) DEFAULT NULL,
  `rating_quality` decimal(2,1) DEFAULT NULL,
  `rating_communication` decimal(2,1) DEFAULT NULL,
  `on_time_delivery_rate` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `supplier_name`, `address`, `tax_id`, `username`, `password`, `is_active`, `bank_name`, `bank_account_number`, `bank_branch_code`, `created_at`, `updated_at`, `rating_delivery_time`, `rating_quality`, `rating_communication`, `on_time_delivery_rate`) VALUES
(5, 'Rohan', '2817 Jerry Toth Drive', '181900', NULL, NULL, 1, NULL, NULL, NULL, '2025-06-10 08:00:44', '2025-08-06 05:17:16', 3.0, 3.0, 4.0, 0.00),
(6, 'test2', 'test2', '1236545', 'supl1', '$2y$10$xpP/sMrD1Qv2ScNS68wZaO9EgW8MBTRN6alIZN9jLDjhd4q.1SPOC', 1, 'Brack', '123741852963', NULL, '2025-06-10 08:41:08', '2025-06-12 13:36:07', 3.5, 3.5, 5.0, 0.00),
(8, 'supl22', 'supl2', '123669', 'supl2', '$2y$10$1Xmic7IfY84VoVkVg2JOwOpbjV0Y.0MEXjMSD20peg5chPzj3rWmy', 1, 'Brac Bank', '129233', NULL, '2025-06-11 15:31:37', '2025-06-14 15:21:52', NULL, NULL, NULL, 0.00),
(9, 'testsup3', 'testsup3', '1236449', 'testsup3', '$2y$10$ietmVzEgII8g.sQq5MwHYOJd4Q2xnv4bxE/KSbdK4U6COlozD1zlW', 1, NULL, NULL, NULL, '2025-06-11 15:36:43', '2025-06-12 10:44:40', NULL, NULL, NULL, 0.00),
(10, 'test3', 'test3', '1285285', NULL, NULL, 1, NULL, NULL, NULL, '2025-06-12 05:01:29', '2025-08-18 15:25:21', NULL, NULL, NULL, 0.00),
(11, 'sup3', 'sup3', '875296', NULL, NULL, 1, NULL, NULL, NULL, '2025-06-12 05:02:01', '2025-06-12 10:44:40', NULL, NULL, NULL, 0.00),
(12, 'sup4', 'sup4', '796541236', NULL, NULL, 1, NULL, NULL, NULL, '2025-06-13 02:43:11', '2025-06-13 02:43:11', NULL, NULL, NULL, NULL),
(13, 'Rohan2', 'Rohan2', '79654412369', 'Rohan2', '$2y$10$/IeBxvX75iJx2igJ/XwMpePMMkmvvtVat2ykb7M4F8tFkMIco75.e', 1, NULL, NULL, NULL, '2025-06-13 03:15:31', '2025-06-13 03:16:06', 5.0, 5.0, 5.0, NULL),
(14, 'SUpplier 025', 'SUpplier 025', '1236545', NULL, NULL, 1, NULL, NULL, NULL, '2025-08-06 06:50:07', '2025-08-06 06:50:07', NULL, NULL, NULL, NULL),
(15, 'Muhammad Yunus[a] (born 28 June 1940) is a Bangladeshi economist, entrepreneur, civil society leader and statesman who has been serving as the fifth chief adviser of Bangladesh[b] since 8 August 2024.[1] Yunus pioneered the modern concept of microcredit a', 'Muhammad Yunus[a] (born 28 June 1940) is a Bangladeshi economist, entrepreneur, civil society leader and statesman who has been serving as the fifth chief adviser of Bangladesh[b] since 8 August 2024.[1] Yunus pioneered the modern concept of microcredit and microfinance, for which he was awarded the Nobel Peace Prize in 2006 as the first Bangladeshi to win the Nobel Peace Prize and he is also the founder of Grameen Bank.\r\n\r\nBorn in Hathazari, Chittagong, Yunus passed his matriculation and intermediate examinations from Chittagong Collegiate School and Chittagong College, respectively. He completed his BA from University of Dhaka and joined as a lecturer in Chittagong College. He obtained his PhD in economics from Vanderbilt University in the United States.\r\n\r\nAfter the devastating famine of 1974, Yunus started to work on poverty elevation in Bangladesh. He began experimenting with microfinance in the late 1970s. In 1983, the Grameen Bank was established. The success of the Grameen microfinance model inspired similar efforts in about 100 developing countries and even in developed countries including the United States.[2] Yunus was awarded the Nobel Peace Prize in 2006 for founding the Grameen Bank and pioneering the concepts of microcredit and microfinance.[3] Yunus has received several other national and international honors, including the United States Presidential Medal of Freedom in 2009 and the Congressional Gold Medal in 2010.[4]\r\n\r\nIn 2012, Yunus became Chancellor of Glasgow Caledonian University in Scotland, a position he held until 2018.[5][6] Previously, he was a professor of economics at Chittagong University in Bangladesh.[7] He published several books related to his finance work. He is a founding board member of Grameen America and Grameen Foundation, which supports microcredit.[8] Yunus also served in the board of directors of the United Nations Foundation, a public charity to support UN causes, from 1998 to 2021.[9] In 2022, he partnered with Global Esports Federation as part of the Esports for Development (E4D) movement to support the development of esports.[10][11]\r\n\r\nFollowing the overthrow of Sheikh Hasina, President Mohammed Shahabuddin gave Yunus a mandate to form an interim government, acceding to calls from student leaders for his appointment.[12] His government has appointed a Constitutional Reform Commission to draft revisions to the Constitution of Bangladesh and has pledged to hold the next general election by June 2026.[13] His name was listed in The 500 Most Influential Muslims in 2024.[14] In 2025, he was named one of Time Magazine\'s 100 Most Influential People in the World.[15]\r\n\r\nEarly life and education\r\n\r\nYunus as a Boy Scout, in 1953\r\nThe third of nine children,[16] Muhammad Yunus was born on 28 June 1940 to a Bengali Muslim family of Saudagars in the village of Bathua, by the Kaptai road at Hathazari in the Chittagong District of Bengal Presidency (now in Bangladesh).[17][18] His father was Haji Muhammad Dula Mia Saudagar, a Sufi jeweller, and his mother was Sufia Khatun. His early childhood was spent in the village. In 1944, his family moved to the city of Chittagong, and he moved from his village school to Lamabazar Primary School.[17][19] By 1949, his mother was afflicted with psychological illness.[18] Later, he passed the matriculation examination from Chittagong Collegiate School ranking 16th out of 39,000 students in East Pakistan.[19] During his school years, he was an active Boy Scout, and travelled to West Pakistan and India in 1952, and to Canada in 1955 to attend Jamborees.[19] Later, while Yunus was studying at Chittagong College, he became active in cultural activities and won awards for drama.[19] In 1957, he enrolled in the Department of Economics at Dhaka University and completed his BA in 1960 and MA in 1961.[20][21]\r\n\r\nCareer\r\nMain article: Grameen Bank\r\nEarly Career\r\n\r\nYunus visiting Chittagong Collegiate School, in 2003\r\nAfter his graduation, Yunus joined the Bureau of Economics at Dhaka University as a research assistant to economists Nurul Islam and Rehman Sobhan.[19] Later, he was appointed lecturer in economics in Chittagong College in 1961.[19] During that time, he also set up a profitable packaging factory on the side.[18] In 1965, he received a Fulbright scholarship to study in the United States. He obtained his PhD in economics from Vanderbilt University through their Graduate Program in Economic Development in 1969.[20][21][22][23] From 1969 to 1972, Yunus was an assistant professor of economics at Middle Tennessee State University in Murfreesboro.[20][21]\r\n\r\nDuring the Bangladesh Liberation War in 1971, Yunus founded a citizen\'s committee and ran the Bangladesh Information Center, with other Bangladeshis in the United States, to raise support for liberation.[19] He also published the Bangladesh Newsletter from his home in Nashville. After the War, he returned to Bangladesh and was appointed to the government\'s Planning Commission headed by Nurul Islam. However, he found the job boring and resigned to join Chittagong University as head of the Economics department.[24] After observing the famine of 1974, he became involved in poverty reduction and established a rural economic programme as a research project. In 1975, he developed a Nabajug Tebhaga Khamar (lit. \'New Era Three-share Farm\') which the government adopted as the Packaged Input Programme.[19] To make the project more effective, Yunus and his associates proposed the Gram Sarkar (lit. \'Village government\') programme.[25] Introduced by President Ziaur Rahman in the late 1970s, the government formed 40,392 village governments as a fourth layer of government in 2003. On 2 August 2005, in response to a petition by Bangladesh Legal Aid and Services Trust (BLAST), the High Court declared village governments illegal and unconstitutional.[26]\r\n\r\nHis concept of microcredit for supporting innovators in multiple developing countries also inspired programmes such as the Info lady Social Entrepreneurship Programme.[27][28][29]\r\n\r\nGrameen and microfinance\r\nFurther information: Grameen family of organizations and Yunus Centre\r\n\r\nGrameen Bank Head Office at Mirpur-2, Dhaka\r\nIn 1976, during visits to the poorest households in the village of Jobra near Chittagong University, Yunus discovered that very small loans could make a disproportionate difference to a poor person. Village women who made bamboo furniture had to take usurious loans to buy bamboo, and repay their profits to the lenders. Traditional banks did not want to make tiny loans at reasonable interest to the poor due to high risk of default.[30] But Yunus believed that, given the chance, the poor will not need to pay high interest on the money, can keep any profits from their own labour, and hence microcredit was a viable business model.[31] Yunus lent US$27 of his money to 42 women in the village, who made a profit of BDT 0.50 (US$0.02) each on the loan.[32] Thus, Yunus is credited with the idea of microcredit.[11]\r\n\r\nIn December 1976, Yunus finally secured a loan from the government Janata Bank to lend to the poor in Jobra. The institution continued to operate, securing loans from other banks for its projects. By 1982, it had 28,000 members. On 1 October 1983, the pilot project began operation as a full-fledged bank for poor Bangladeshis and was renamed Grameen Bank (\"Village Bank\"). By July 2007, Grameen had issued US$6.38 billion to 7.4 million borrowers.[33] To ensure repayment, the bank uses a system of \"solidarity groups\". These small informal groups apply together for loans and its members act as co-guarantors of repayment and support one another\'s efforts at economic self-advancement.[25]\r\n\r\nIn the late 1980s, Grameen started to diversify by attending to underutilized fishing ponds and irrigation pumps like deep tube wells.[34] In 1989, these diversified interests started growing into separate organisations. The fisheries project became Grameen Motsho (\"Grameen Fisheries Foundation\") and the irrigation project became Grameen Krishi (\"Grameen Agriculture Foundation\").[34] In time, the Grameen initiative grew into a multi-faceted group of profitable and non-profit ventures, including major projects like Grameen Trust and Grameen Fund, which runs equity projects like Grameen Software Limited, Grameen CyberNet Limited, and Grameen Knitwear Limited,[35] as well as Grameen Telecom, which has a stake in Grameenphone (GP), the biggest private phone company in Bangladesh.[36] From its start in March 1997 to 2007, GP\'s Village Phone (Polli Phone) project had brought cell-phone ownership to 260,000 rural poor in over 50,000 villages.[37]\r\n\r\nIn 1974 we ended up with a famine in the country. People were dying of hunger and not having enough to eat. And that\'s a terrible situation to see around you. And I was feeling terrible that here I teach elegant theories of economics, and those theories are of no use at the moment with the people who are going hungry. So I wanted to see if as a person, as a human being, I could be of some use to some people.\r\n\r\n– Muhammad Yunus while talking about reason behind creating Grameen Bank[38]\r\nThe success of the Grameen microfinance model inspired similar efforts in about 100 developing countries and even in developed countries including the United States.[39] Many microcredit projects retain Grameen\'s emphasis of lending to women. More than 94% of Grameen loans have gone to women, who suffer disproportionately from poverty and who are more likely than men to devote their earnings to their families.[40]\r\n\r\nFor his work with Grameen, Yunus was named an Ashoka: Innovators for the Public Global Academy Member in 2001.[41] According to Rashidul Bari, the Grameen\'s social business model has gone from being theory to an inspiring practice adopted globally by leading universities, entrepreneurs, social business and corporations.[42]\r\n\r\nThe Yunus Centre, located in Dhaka, Bangladesh, is a think tank focused on social business, poverty alleviation, and sustainability. Founded in 2008 and chaired by Dr Yunus, it promotes his philosophy of social business and serves as a resource center for related initiatives. The centre\'s activities include poverty eradication campaigns, research and publications, support for social business start-ups, organizing the Global Social Business Summit, and developing academic programs on social business with international universities.[43]\r\n\r\nInternational career\r\nIn July 2007, in Johannesburg, South Africa, Nelson Mandela, Graça Machel and Desmond Tutu convened a group of world leaders \"to contribute their wisdom, independent leadership and integrity to tackle some of the world\'s toughest problems.\"[44] Nelson Mandela announced the formation of this new group, The Elders, in a speech he delivered on the occasion of his 89th birthday.[45] Yunus attended the launch of the group and was one of its founding members. He stepped down as an Elder in September 2009, stating that he was unable to do justice to his membership due to the demands of his work.[46]\r\n\r\nYunus is a member of the Africa Progress Panel (APP), a group of ten distinguished individuals who advocate at the highest levels for equitable and sustainable development in Africa. Every year, the Panel releases a report, the Africa Progress Report, that outlines an issue of immediate importance to the continent and suggests a set of associated policies.[47] In July 2009, Yunus became a member of the SNV Netherlands Development Organisation International Advisory Board to support the organisation\'s poverty reduction work.[48] Since 2010, Yunus has served as a Commissioner for the Broadband Commission for Digital Development, a UN initiative which seeks to use broadband internet services to accelerate social and economic development.[49] In March 2016, he was appointed by United Nations Secretary-General Ban Ki-moon to the High-Level Commission on Health Employment and Economic Growth, which was co-chaired by presidents François Hollande of France and Jacob Zuma of South Africa.[50] Following the Rohingya genocide in 2016–2017, Yunus urged Myanmar to end violence against Rohingya Muslims.[51]\r\n\r\nPolitical career\r\nFor many years, Yunus remained a follower of Hasina\'s father, Sheikh Mujib, Former President of Bangladesh.[52] While teaching at Middle Tennessee State University,[53] Yunus founded the Bangladesh Citizens\' Committee (BCC) as a response to West Pakistan\'s aggression against Bangladesh.[54]: 74  After the outbreak of the war of liberation, the BCC selected Yunus to become editor of its Bangladesh News Letter.[55] Inspired by the birth of Bangladesh in 1971, Yunus returned home in 1972. The relationship continued after Mujib\'s death.\r\n\r\nAdvisor to the Caretaker Government\r\nIn 1996, Muhammad Yunus served as an advisor to the caretaker government led by former Chief Justice Muhammad Habibur Rahman. He was responsible for overseeing the Ministry of Primary and Mass Education, the Ministry of Science and Technology, and the Ministry of Environment and Forests.[56][57]\r\n\r\n\r\nYunus at a reception in Peru\r\nNagorik Shakti\r\nFurther information: Nagorik Shakti\r\nIn early 2006, Yunus, along with other members of the civil society including Rehman Sobhan, Muhammad Habibur Rahman, Kamal Hossain, Matiur Rahman, Mahfuz Anam and Debapriya Bhattacharya, participated in a campaign for honest and clean candidates in national elections.[58] He considered entering politics in the later part of that year.[59] On 11 February 2007, Yunus wrote an open letter, published in the Bangladeshi newspaper Daily Star, where he asked citizens for views on his plan to float a political party to establish political goodwill, proper leadership and good governance. In the letter, he called on everyone to briefly outline how he should go about the task and how they can contribute to it.[60] Yunus finally announced that he is willing to launch a political party tentatively called Nagorik Shakti (lit. \'Citizens\' Power\') on 18 February 2007.[61][62] There was speculation that the army supported a move by Yunus into politics.[63] On 3 May, however, Yunus declared that he had decided to abandon his political plans following a meeting with the head of the caretaker government, Fakhruddin Ahmed.[64]\r\n\r\n\r\nYunus with Japanese Prime Minister Naoto Kan in 2010\r\nChief Adviser of Bangladesh\r\nMain article: Interim government of Muhammad Yunus\r\n\r\nJoe Biden with Chief Advisor Yunus at the U.N. Headquarters in New York City.\r\nAmid the Student–People\'s uprising in Bangladesh, Yunus expressed support for the students and his distaste of the current government, and in August 2024, after the resignation of Sheikh Hasina and her departure to India, it was announced that Yunus would be chief adviser of the interim government.[65][66]\r\n\r\nMuhammad Yunus was appointed as the transitional leader of the interim government on 7 August 2024 by president Mohammed Shahabuddin.[67] On 8 August 2024, he took the oath and has been serving as the Chief Advisor of the interim government.[68] After the oath, he visited injured people in Dhaka Medical College.[69] On 10 August 2024, he visited the home and family members of Abu Sayed.[70] He also visited injured student protesters in the Rangpur Medical College.[71] Following communal violence after Hasina\'s resignation, Yunus threatened to resign if the violence continued[72] and vowed to crack down on conspirators of the attacks.[73]\r\n\r\n\r\nChief Adviser Muhammad Yunus (middle) with President of Azerbaijan Ilham Aliyev (left) and the Secretary-general of the United Nations António Guterres (right) in COP29 Baku, Azerbaijan, 11 November 2024\r\nAs Chief Adviser, Yunus has pledged to continue providing humanitarian aid to Rohingya refugees in Bangladesh and support the garment industry amid disruptions caused by the unrest prior to his appointment.[74]\r\n\r\n\r\nYunus with Ursula von der Leyen as the Chief Adviser of Bangladesh\r\nOn 16 December 2024, Yunus announced that general elections would be held in late 2025 or early 2026.[75] On 5 August 2025, Yunus requested the Bangladesh Election Commission to organise the election before Ramadan 2026, which will begin as early as 17 February.[76]\r\n\r\nIn his capacity as Chief Adviser of Bangladesh\'s interim government, Muhammad Yunus has taken initiatives to enhance the country\'s digital infrastructure and support inclusive economic growth. In March 2025, Yunus announced that Bangladesh would finalize a commercial agreement with SpaceX\'s Starlink within three months. The initiative aims to deliver reliable satellite internet across the nation and prevent political disruptions from leading to internet blackouts, as happened in the past.[77] Yunus emphasized that satellite internet through Starlink would allow broader access to education, health services, and entrepreneurship, especially in rural and underserved regions. He also expressed interest in collaborating with Elon Musk to unlock Bangladesh\'s potential through digital innovation.[78]\r\n\r\nIn April 2025, Yunus addressed the 81st session of the United Nations Economic and Social Commission for Asia and the Pacific (ESCAP), reaffirming Bangladesh\'s commitment to building a climate-resilient and inclusive digital economy. He highlighted the government\'s investments in green infrastructure, sustainable housing, and nature-based solutions to support vulnerable populations.[79]\r\n\r\nControversies\r\n\r\nThis \"criticism\" or \"controversy\" section may compromise the article\'s neutrality. Please help integrate negative information into other sections or remove undue focus on minor aspects through discussion on the talk page. (June 2025)\r\nChange of Hasina\'s opinion about Yunus (2007)\r\nYunus maintained a professional relationship with Hasina. Yunus appointed Hasina—along with U.S. First Lady Hillary Clinton—as co-chair of a microcredit summit held 2–4 February 1997. In her statement she praised, \"the outstanding work done by Professor Yunus and the Grameen Bank he founded. ... The success of the Grameen Bank has created optimism about the viability of banks engaged in extending micro-credit to the poor\".[80] The inaugural ceremony of Grameen Phone, Bangladesh\'s largest telephone service, took place at Hasina\'s office on 26 March 1997. Using Grameen Phone, Hasina made the first call to Thorbjorn Jagland, the then-Norwegian prime minister. When her conversation ended, she received another call, from Laily Begum, a Grameen Phone employee.\r\n\r\nOn 11 January 2007, Army General Moeen U Ahmed staged a military coup,[81] and Fakhruddin Ahmed took office on 11 January 2007 as Chief Advisor[82] saying he intended to arrange free and fair elections but also to clean up corruption. While Khaleda Zia and Hasina criticised Fakruddin and claimed that it was not his job to clean up corruption, Yunus expressed his satisfaction. In an interview with the AFP news agency, Yunus remarked \"There is no ideology here.\"[83] In reaction to Yunus\' comments Sheikh Hasina called him a \"usurer who has not only failed to eradicate poverty but has also nurtured poverty.\"[84] This was Hasina\'s first public statement against Yunus.\r\n\r\nThe Awami League government of Sheikh Hasina campaigned against Grameen and Yunus. The New York Times reported, \"Her actions appear to be retaliation for Mr. Yunus\'s announcement in 2007 that he would seek public office, even though he never went through with his plans\".[85] According to Times of India, one other factor contributed to her decision against Yunus: the Nobel Peace Prize.[86]\r\n\r\nHasina thought she would win the Nobel Peace Prize for signing the 1997 Chittagong Hill Tracts peace treaty. On 9 March, Attorney General Mahbubey Alam expressed the government\'s attitude when he said, \"Prime Minister Sheikh Hasina should have been awarded the Nobel Peace Prize\". He went on to challenge the wisdom of the Nobel committee.[87]\r\n\r\nDismissal from and government targeting of Grameen (2011–2013)\r\nThe second Awami League government announced a review of Grameen Bank activities on 11 January 2011.[88] In February 2011, several international leaders, such as Mary Robinson, stepped up their defence of Yunus through a number of efforts, including the founding of a formal network of supporters known as \"Friends of Grameen\".[89]\r\n\r\nOn 15 February 2011, the Finance Minister of Bangladesh, Abul Maal Abdul Muhith, declared that Yunus should \"stay away\" from Grameen Bank while it is being investigated.[90] On 2 March 2011, Muzammel Huq, a former Bank employee, whom the government had appointed chairman in January, announced that Yunus had been fired as managing director of the Bank.[91][92] However, Bank General Manager Jannat-E Quanine issued a statement that Yunus was \"continuing in his office\" pending review of the legal issues surrounding the controversy.[93]\r\n\r\nIn March 2011, Yunus petitioned the Bangladesh High Court challenging the legality of the decision by the Bangladeshi Central Bank to remove him as managing director of Grameen Bank.[94] The same day, nine elected directors of Grameen Bank filed a second petition.[95] U.S. Senator John Kerry expressed his support to Yunus in a statement on 5 March 2011 and declared that he was \"deeply concerned\" by this affair.[96] The same day in Bangladesh, thousands of people protested and formed human chains to support Yunus.[97] The High Court hearing on the petitions, was planned for 6 March 2011 but postponed. On 8 March 2011, the Court confirmed Yunus\'s dismissal.[98]\r\n\r\nOn 2 August 2012, Sheikh Hasina approved a draft of \"Grameen Bank Ordinance 2012\"[99] to increase government control over the bank.[99] That power resided with the bank\'s directors—nine poor women who were elected by 8.3 million Grameen borrowers. Hasina also ordered a fresh investigation into Yunus\'s activities and financial transactions[100] in his later years as managing director of Grameen, but people saw the move as an attempt to destroy his image. The prime minister also alleged that Yunus had received his earnings without the necessary permission from the government, including his Nobel Peace Prize earnings and book royalties.[101]\r\n\r\nOn 4 October 2013, Bangladesh\'s cabinet approved the draft of a new law that would give the country\'s central bank greater control over Grameen Bank,[102] raising the stakes in the long-running dispute. The Grameen Bank Act 2013 was approved at a cabinet meeting chaired by Prime Minister Sheikh Hasina[103] and was passed by parliament on 7 November 2013.[104] It replaced the Grameen Bank Ordinance, the law that underpinned the creation of Grameen Bank as a specialised microcredit institution in 1983.[105] The New York Times reported in August 2013:\r\n\r\nSince then, the government has started an investigation into the bank and is now planning to take over Grameen—a majority of whose shares are owned by its borrowers—and break it up into 19 regional lenders.[85]\r\n\r\nVikas Bajaj wrote on 7 November 2013:\r\n\r\nThe government of Bangladesh has played its trump card in its long-running campaign against Grameen Bank and its founder Muhammad Yunus. Last week, legislators passed a law that effectively nationalizes the bank, which pioneered the idea of making small loans to poor women, by wresting control of it from the 8.4 million rural women that own a majority of its shares.[106]\r\n\r\nLegal cases and trials (2010–2024)\r\nYunus faced 174 lawsuits in Bangladesh, 172 of which were civil cases. Allegations included labour law violations, corruption, and money laundering, which Yunus alleged were politically motivated.[107]\r\n\r\nHasina launched a series of trials against Yunus.[108] The former put the latter on trial in 2010 and ultimately removed him from Grameen Bank,[109] citing his age.[110] The government launched the first trial against Yunus in December 2010, alleging that in 1996 he had transferred approximately $100 million to a sister company of Grameen Bank. Yunus denied[111] the allegations and he was found innocent by the Norwegian government.[112] In 2013, he was tried a second time, because he had supposedly received earnings without the necessary government permission, including his Nobel Peace Prize earnings and royalties from his book sales.[113] The series of trials against Yunus[114] puzzled figures worldwide, from the 8.3 million underprivileged women served by Grameen Bank to U.S. President Barack Obama.[115][116]\r\n\r\nOn 27 January 2011, Yunus appeared in court in a food-adulteration case filed by the Dhaka City Corporation (DCC) Food Safety Court, accusing him of producing an \"adulterated\" yogurt[117] whose fat content was below the legal minimum. This yogurt is produced by Grameen Danone, a social business joint venture between Grameen Bank and Danone that aims to provide opportunities for street vendors who sell the yogurt and to improve child nutrition with the nutrient-fortified yogurt. According to Yunus\' lawyer, the allegations are \"false and baseless\".[118]\r\n\r\nOn 1 January 2024, a court in Bangladesh sentenced Yunus to a six-month prison term, along with three employees from Grameen Telecom for labor law violations. However, the court granted bail pending appeals.[119] Amnesty International declared Yunus\'s conviction a \"blatant abuse\" of the justice system.[120] The conviction was overturned on 7 August 2024 following an appeal.[121][122] He was acquitted in a graft case filed by the Anti-Corruption Commission (ACC) just four days after getting acquittal for the labour violations case.[123][124]\r\n\r\nPolitical motivations behind the allegations\r\nIn December 2010, Grameen Bank was quickly cleared by the Norwegian government of all allegations surrounding misused or misappropriated funds.[88] Yet, in March 2011, the Bangladeshi government launched a three-month investigation of all Grameen Bank\'s activities.[88] This inquiry prevented Muhammad Yunus from participating in the World Economic Forum.[125]\r\n\r\nIn January 2011, Yunus appeared in court in a defamation case filed by a local politician from a minor left-leaning party in 2007, complaining about a statement that Yunus made to the AFP news agency, \"Politicians in Bangladesh only work for power. There is no ideology here\".[126] At the hearing, Yunus was granted bail and exempted from personal appearance at subsequent hearings.[83]\r\n\r\nThese investigations fueled suspicion that many attacks might be politically motivated,[127] due to difficult relations between Sheikh Hasina and Yunus since early 2007, when Yunus created his own political party, an effort he dropped in May 2007.[64] In 2013, he faced a state-backed smear campaign that accused him of being un-Islamic and promoting homosexuality, after he signed a joint statement criticising the prosecution of gay people in Uganda in 2012 with three other nobel laureates.[128][129]\r\n\r\nCriticism over government privileges (2024–2025)\r\nFollowing his appointment as chief adviser, Yunus faced criticism after several Grameen-affiliated institutions received government approvals and benefits. These included approval for Grameen University, tax waivers and a reduction in government shareholding in Grameen Bank, and licenses for manpower export and a digital wallet. The dismissal of labor law violation and money laundering cases against him during this period also raised concerns from some quarters regarding transparency and conflicts of interest.[130][131]\r\n\r\n\r\nYunus in 2007\r\n\r\nYunus with Werner Faymann in 2009\r\nPersonal life\r\n\r\nYunus with his family members including Monica at the Grand Hotel in Oslo, Norway\r\nYunus identifies as a Muslim and has expressed the importance that salah and Ishq-e-Muhammadi holds to him in his personal life.[24] His father, Haji Muhammad Dula Mia Saudagar, completed Hajj three times and was a disciple of two prominent Sufis of Chittagong. Yunus continues to actively display a normative orthodox Sunnite theological creed, whilst rejecting superstition.[132] He encourages the public to engage in Dua directly to Allah,[133][134] whom Yunus publicly recognises as the supreme source of assistance and support,[135] and as the master of Divine Decree.[136] Yunus has also referred to the Qur\'an as the \"guide for mankind\" and acknowledged the concept of ummah in his public speeches.[132]\r\n\r\nIn 1967, while Yunus attended Vanderbilt University, he met Vera Forostenko, a student of Russian literature at Vanderbilt University and daughter of Russian immigrants to Trenton, New Jersey, United States. They were married in 1970.[18][24] Yunus\'s marriage with Vera ended within months of the birth of their baby girl, Monica Yunus, in 1979 in Chittagong,[137] as Vera returned to New Jersey claiming that Bangladesh was not a good place to raise a baby.[18][24] Monica Yunus became an operatic soprano based in New York City.[138] Yunus later married Afrozi Yunus, who was then a researcher in physics at Manchester University.[24] She was later appointed as a professor of physics at Jahangirnagar University. Their daughter Deena Afroz Yunus was born in 1986.[24]\r\n\r\nYunus\'s brother Muhammad Ibrahim is a former professor of physics at the University of Dhaka and the founder of The Center for Mass Education in Science (CMES), which brings science education to adolescent girls in villages.[139] His other brother Muhammad Jahangir (d. 2019) was a television presenter and a social activist in Bangladesh.[140]\r\n\r\n\r\nYunus in 2016\r\nAwards and recognitions\r\nMain article: List of awards and honours received by Muhammad Yunus\r\nYunus was awarded the 2006 Nobel Peace Prize, along with Grameen Bank, for their efforts to create economic and social development:\r\n\r\nMuhammad Yunus has shown himself to be a leader who has managed to translate visions into practical action for the benefit of millions of people, not only in Bangladesh, but also in many other countries. Loans to poor people without any financial security had appeared to be an impossible idea. From modest beginnings three decades ago, Yunus has, first and foremost through Grameen Bank, developed micro-credit into an ever more important instrument in the struggle against poverty.\r\n\r\n— Norwegian Nobel Committee[141]\r\n\r\nBarack Obama speaks to Stephen Hawking and on the left Yunus\r\nYunus was the first Bangladeshi to ever get a Nobel Prize. He established Grameen Bank in 1983, which plays a significant role in poverty alleviation in various countries of the world including Bangladesh. In 2006, he and the Grameen Bank he founded jointly won the Nobel Peace Prize.[142] After receiving the news of the important award, Yunus announced that he would use part of his share of the $1.4 million (equivalent to $2.18 million in 2024) award money to create a company to make low-cost, high-nutrition food for the poor; while the rest would go towards establishing the Yunus Science and Technology University in his home district as well as setting up an eye hospital for the poor in Bangladesh.[143]\r\n\r\nFormer U.S. president Bill Clinton was a vocal advocate for the awarding of the Nobel Prize to Yunus. He expressed this in Rolling Stone magazine[144] as well as in his autobiography My Life.[145] In a speech given at University of California, Berkeley in 2002, President Clinton described Yunus as \"a man who long ago should have won the Nobel Prize [in Economics and] I\'ll keep saying that until they finally give it to him.\"[146] Conversely, The Economist stated explicitly that while Yunus was doing excellent work to fight poverty, it was not appropriate to award him the Peace Prize, stating: \"... the Nobel committee could have made a braver, more difficult, choice by declaring that there would be no recipient at all.\"[147]\r\n\r\n\r\nYunus at the Annual Meeting 2009 of the World Economic Forum in Davos, Switzerland\r\nHe is one of only seven persons to have won the Nobel Peace Prize, Presidential Medal of Freedom,[148] and the Congressional Gold Medal.[149] Other notable awards include the Ramon Magsaysay Award in 1984,[25] the World Food Prize,[150] the International Simon Bolivar Prize (1996),[151] the Prince of Asturias Award for Concord[152] and the Sydney Peace Prize in 1998,[153] and the Seoul Peace Prize in 2006. Additionally, Yunus has been awarded 71 honorary doctorate degrees from universities across 27 countries, and 113 international awards from 26 countries including state honours from 10 countries.[154][155][156] Bangladesh government brought out a commemorative stamp to honour his Nobel Award.[157]\r\n\r\nYunus was named by Fortune Magazine in March 2012 as one of 12 greatest entrepreneurs of the current era.[158] In its citation, Fortune Magazine said \"Yunus\' idea inspired countless numbers of young people to devote themselves to social causes all over the world.\"[159]\r\n\r\nIn January 2008, Houston, Texas declared 14 January as \"Muhammad Yunus Day\".[160]\r\n\r\nYunus was named among the most desired thinkers the world should listen to by the FP 100 (world\'s most influential elite) in the December 2009 issue of Foreign Policy magazine.[161]\r\n\r\n\r\nYunus with Brazilian President Lula Da Silva (right) in 2008 after winning Nobel Peace Prize\r\nIn 2010, the British magazine New Statesman listed Yunus at 40th in the list of \"The World\'s 50 Most Influential Figures 2010\".[162]\r\n\r\n\r\nMuhammad Yunus in Switzerland (1995).\r\nYunus received 72 honorary doctorate degrees from universities from Albania, Argentina, Australia, Bang', 'Muhammad Yunus[a] (born 28 June 1940) is a Bangladeshi economist, entrepreneur, civil society leader', NULL, NULL, 1, NULL, NULL, NULL, '2025-08-19 03:01:41', '2025-08-19 03:01:41', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `supplier_communication_logs`
--

CREATE TABLE `supplier_communication_logs` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `log_type` varchar(50) NOT NULL COMMENT 'e.g., Email, Call, Meeting',
  `notes` text NOT NULL,
  `log_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `supplier_communication_logs`
--

INSERT INTO `supplier_communication_logs` (`id`, `supplier_id`, `log_type`, `notes`, `log_date`) VALUES
(1, 5, 'Call', '!st', '2025-06-10 15:05:00');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_compliance_status`
--

CREATE TABLE `supplier_compliance_status` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `checklist_id` int(11) NOT NULL,
  `status` enum('Not Set','Compliant','Not Compliant','In Progress') NOT NULL DEFAULT 'Not Set',
  `expiry_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `supplier_compliance_status`
--

INSERT INTO `supplier_compliance_status` (`id`, `supplier_id`, `checklist_id`, `status`, `expiry_date`, `notes`, `updated_at`) VALUES
(1, 6, 1, 'Compliant', NULL, NULL, '2025-06-10 09:00:45'),
(3, 6, 2, 'In Progress', NULL, NULL, '2025-06-10 09:01:27'),
(4, 6, 3, 'Compliant', NULL, NULL, '2025-06-10 09:00:49'),
(5, 6, 4, 'Compliant', NULL, NULL, '2025-06-10 08:59:40');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_contacts`
--

CREATE TABLE `supplier_contacts` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `contact_name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone_number` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `supplier_contacts`
--

INSERT INTO `supplier_contacts` (`id`, `supplier_id`, `contact_name`, `email`, `phone_number`) VALUES
(5, 5, 'Mehedi Hasan Rohan', 'rohan15-5910@diu.edu.bd', '01749393453'),
(6, 6, 'dwd', 'mehedihasanrohan07@gmail.com', '0123456789'),
(8, 8, 'supl2', 'supl2@g.com', '12345699630'),
(9, 9, 'testsup3', 'testsup3@g.com', '1234896263'),
(10, 10, 'test3', 'test3@gm.com', '27969685285'),
(11, 11, 'sup3', 'sup3@f.com', '974566321'),
(12, 12, 'sup4', 'supr@f.com', '963852741'),
(13, 13, 'Rohan2', 'Rohan2@g.com', '7954139'),
(14, 14, 'Mehedi Hasan Rohan', 'mehedihasanrohan07@gmail.com', '7954139'),
(15, 15, 'Muhammad Yunus[a] (born 28 June 1940) is a Bangladeshi economist, entrepreneur, civil society leader and statesman who has been serving as the fifth chief adviser of Bangladesh[b] since 8 August 2024.[1] Yunus pioneered the modern concept of microcredit a', 'habijabi@habijabic.com', 'Muhammad Yunus[a] (born 28 June 1940) is a Banglad');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_contracts`
--

CREATE TABLE `supplier_contracts` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `contract_title` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL COMMENT 'Path to the uploaded contract file',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `supplier_contracts`
--

INSERT INTO `supplier_contracts` (`id`, `supplier_id`, `contract_title`, `file_path`, `start_date`, `end_date`, `created_at`) VALUES
(1, 5, '1st', 'uploads/contracts/contract_6847e67f4dfa68.67498773.pdf', '2025-06-10', '2026-01-10', '2025-06-10 08:02:07'),
(2, 6, 'test', 'uploads/contracts/contract_6847efbce196f0.06498464.pdf', '2025-06-10', '2025-06-30', '2025-06-10 08:41:32');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_info_changes`
--

CREATE TABLE `supplier_info_changes` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `change_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'A JSON object holding the proposed new data' CHECK (json_valid(`change_data`)),
  `status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reviewed_by_user_id` int(11) DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `supplier_info_changes`
--

INSERT INTO `supplier_info_changes` (`id`, `supplier_id`, `change_data`, `status`, `requested_at`, `reviewed_by_user_id`, `reviewed_at`) VALUES
(1, 8, '{\"bank_name\":\"Brac\",\"bank_account_number\":\"123456789\"}', 'Rejected', '2025-06-12 08:10:24', 1, '2025-06-12 12:42:29'),
(2, 8, '{\"bank_name\":\"Brac\",\"bank_account_number\":\"852741963\"}', 'Rejected', '2025-06-12 09:47:52', 1, '2025-06-12 12:42:30'),
(3, 8, '{\"bank_name\":\"DBBL\",\"bank_account_number\":\"1292333\"}', 'Approved', '2025-06-12 10:03:19', 1, '2025-06-12 12:05:10'),
(4, 6, '{\"bank_name\":\"Brack\",\"bank_account_number\":\"123741852963\"}', 'Approved', '2025-06-12 13:35:50', 1, '2025-06-12 15:36:07'),
(5, 8, '{\"bank_name\":\"Brac Bank\",\"bank_account_number\":\"129233\"}', 'Approved', '2025-06-14 15:21:41', 1, '2025-06-14 17:21:52'),
(6, 8, '{\"bank_name\":\"Brac Bank2\",\"bank_account_number\":\"129233852\"}', 'Rejected', '2025-06-14 15:22:01', 1, '2025-06-14 17:22:07');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_kpi_history`
--

CREATE TABLE `supplier_kpi_history` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `kpi_date` date NOT NULL,
  `on_time_delivery_rate` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `supplier_products`
--

CREATE TABLE `supplier_products` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `supplier_item_code` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `supplier_products`
--

INSERT INTO `supplier_products` (`id`, `supplier_id`, `product_id`, `supplier_item_code`) VALUES
(2, 5, 6, ''),
(3, 5, 16, ''),
(4, 5, 17, ''),
(5, 6, 10, ''),
(6, 8, 13, ''),
(7, 6, 20, ''),
(8, 9, 14, '');

-- --------------------------------------------------------

--
-- Table structure for table `tax_codes`
--

CREATE TABLE `tax_codes` (
  `id` int(11) NOT NULL,
  `tax_code` varchar(32) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `rate` decimal(5,2) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tax_codes`
--

INSERT INTO `tax_codes` (`id`, `tax_code`, `description`, `rate`, `is_active`, `created_at`) VALUES
(1, 'VAT', 'Value Added Tax', 10.00, 1, '2025-08-11 18:17:29'),
(2, 'GST', 'Goods and Services Tax', 8.50, 1, '2025-08-11 18:17:29'),
(3, 'SALES', 'Sales Tax', 6.25, 1, '2025-08-11 18:17:29'),
(4, 'EXEMPT', 'Tax Exempt', 0.00, 1, '2025-08-11 18:17:29');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `department_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role_id`, `is_active`, `department_id`, `created_at`) VALUES
(1, 'admin', '$2y$10$Lrh8hHVfCjEqxBxUKZJeB.id2Fz/43a.6uTl5rDfw1KREv0OLPAhi', 'admin@example.com', 1, 1, NULL, '2025-06-10 10:55:15'),
(5, 'superadmin', '$2y$10$RLk53vilDSMPtW1c2D1hl.U.n3h9z.uTeGRmqBxtVr6FtoLMlsp1u', 'super@example.com', 2, 1, NULL, '2025-06-11 06:19:07'),
(6, 'procofficer', '$2y$10$yU.x9TNOarZcbgeh30K5Nuwuj6hq8.Hil5UP67aiDMs8AfXDOZLgO', 'officer@example.com', 4, 1, NULL, '2025-06-11 06:19:07'),
(8, 'projectm', '$2y$10$3i4hLmJ5B5GnxvslmxyxFu91u2FdD2c.u/dqCxHxfujqCdhNxPa9y', 'projectm@g.com', 8, 1, NULL, '2025-06-11 07:10:52'),
(9, 'hr', '$2y$10$mbUc1VQK6440TdIKqDtEeuJ84WxBChYr0bzeB7//HyyEH7NoAPwaq', 'hr@a.com', 6, 1, NULL, '2025-06-11 07:17:04'),
(10, 'finance', '$2y$10$KTZAGww/9PV3YHwN4tLUvuEtJ0xNA6dqsu9e75YRJwIBTMYiHTiei', 'finance@g.com', 5, 1, NULL, '2025-06-11 07:18:24'),
(11, 'prooff', '$2y$10$v0IqBF1O8cv.KkCuX/x8VeKA8eDdPiQfeF.SD6mrzbXHrqAYy4A62', 'prooff@gmail.com', 4, 1, NULL, '2025-06-11 08:52:30'),
(12, 'inven', '$2y$10$IY.3w/wqdMoP9qZrcBEHe.i6hUdc2q4PFrAeFryG0a6sBguFtUJxa', 'inven@g.com', 7, 1, NULL, '2025-06-11 14:46:11'),
(14, 'supl2', '$2y$10$O1y9yi0l9kEeTX/Mf4lxXeWjEiKbTeHXWoVcCPa/GgDPWI23DqxYm', 'supl2@g.com', 10, 1, NULL, '2025-06-11 15:31:56'),
(15, 'testsup3', '$2y$10$8ys7z936GuA/eMBToM/c7evlibcgXiq2r73bjglUd9GQ9QKWV5u5C', 'testsup3@g.com', 10, 1, NULL, '2025-06-11 15:36:59'),
(16, 'deptman', '$2y$10$mAJqKYZgWc/9vxYgcyh0d.ioSBNn8XvgHPDfRYasWCbmLOwAQuQuy', 'deptman@g.com', 3, 1, NULL, '2025-06-11 15:51:22'),
(17, 'tofa', '$2y$10$B/ioL/gLdsozNYZU7gmP3u1qZsUI6mCKPznBRgq9sW10.CLvZZ67q', 'tofa@gmai.com', 4, 1, NULL, '2025-06-13 02:25:19'),
(18, 'Shahinur', '$2y$10$u91dl48tXyt.oLeT5D3F5uNLoAUEQ3lo7PT9rKSXgE8gB6cVX16XK', 'Shahinur@g.com', 7, 1, NULL, '2025-06-13 02:28:37'),
(19, 'audit', '$2y$10$faIu1lseLINBohGvpA/AbeUJ3ap2uJG8Zn9Q15kCRgIlUBeRMxcsq', 'audit@g.com', 13, 1, NULL, '2025-06-13 02:30:14'),
(20, 'superad', '$2y$10$EFM3/WniDVWG3iSl.8j8G.9ZLgwQjInYZq2kQCjnRUEhAMVyJiUKy', 'superad@gmail.com', 2, 1, NULL, '2025-06-13 02:37:44'),
(21, 'moshi', '$2y$10$0BGhGweFNudDEQzwXbrJg.sFX7q6Oi7MBfS12GzSuMRo4l15o6YgK', 'moshi@g.com', 11, 1, NULL, '2025-06-13 05:05:43'),
(22, 'raven', '$2y$10$CEMQaVvkpEuCIOznt/3qEeLN5Oo6eiVBcjocV8y2.wZE4H/Am0lsq', 'rohan15-5910@diu.edu.bd', 9, 1, NULL, '2025-06-16 04:11:08'),
(23, 'supplier', '$2y$10$kGq57RP7mwFJJFapPuvfiuA/ul6Jj3a22K9lhSB.8ZVn3LVaBs0Bm', 'supl2@gm.com', 10, 1, NULL, '2025-08-06 06:49:30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ap_bills`
--
ALTER TABLE `ap_bills`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bill_no` (`bill_no`),
  ADD KEY `vendor_id` (`vendor_id`),
  ADD KEY `tax_code_id` (`tax_code_id`);

--
-- Indexes for table `ap_payments`
--
ALTER TABLE `ap_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bill_id` (`bill_id`);

--
-- Indexes for table `ap_vendors`
--
ALTER TABLE `ap_vendors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ar_customers`
--
ALTER TABLE `ar_customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ar_invoices`
--
ALTER TABLE `ar_invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_no` (`invoice_no`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `tax_code_id` (`tax_code_id`);

--
-- Indexes for table `ar_payments`
--
ALTER TABLE `ar_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Indexes for table `assets`
--
ALTER TABLE `assets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `asset_tag` (`asset_tag`),
  ADD KEY `asset_type_id` (`asset_type_id`),
  ADD KEY `assigned_to_employee_id` (`assigned_to_employee_id`);

--
-- Indexes for table `asset_types`
--
ALTER TABLE `asset_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `bank_accounts`
--
ALTER TABLE `bank_accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bank_transactions`
--
ALTER TABLE `bank_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bank_account_id` (`bank_account_id`);

--
-- Indexes for table `budgets`
--
ALTER TABLE `budgets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `chart_of_accounts`
--
ALTER TABLE `chart_of_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `account_code` (`account_code`),
  ADD KEY `idx_parent` (`parent_account_code`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `compliance_checklists`
--
ALTER TABLE `compliance_checklists`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `deliveries`
--
ALTER TABLE `deliveries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `po_id` (`po_id`);

--
-- Indexes for table `delivery_items`
--
ALTER TABLE `delivery_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `delivery_id` (`delivery_id`),
  ADD KEY `po_item_id` (`po_item_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `employee_id` (`employee_id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_number_supplier` (`invoice_number`,`supplier_id`),
  ADD KEY `po_id` (`po_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `journal_entries`
--
ALTER TABLE `journal_entries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `journal_entry_lines`
--
ALTER TABLE `journal_entry_lines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `journal_entry_id` (`journal_entry_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `po_id` (`po_id`);

--
-- Indexes for table `po_items`
--
ALTER TABLE `po_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `po_id` (`po_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `budget_id` (`budget_id`),
  ADD KEY `manager_id` (`manager_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `project_tasks`
--
ALTER TABLE `project_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `assigned_to_user_id` (`assigned_to_user_id`);

--
-- Indexes for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `po_number` (`po_number`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `budget_id` (`budget_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_permission_unique` (`role_id`,`permission_key`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `supplier_communication_logs`
--
ALTER TABLE `supplier_communication_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `supplier_compliance_status`
--
ALTER TABLE `supplier_compliance_status`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `supplier_checklist_unique` (`supplier_id`,`checklist_id`),
  ADD KEY `checklist_id` (`checklist_id`);

--
-- Indexes for table `supplier_contacts`
--
ALTER TABLE `supplier_contacts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `supplier_contracts`
--
ALTER TABLE `supplier_contracts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `supplier_info_changes`
--
ALTER TABLE `supplier_info_changes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `reviewed_by_user_id` (`reviewed_by_user_id`);

--
-- Indexes for table `supplier_kpi_history`
--
ALTER TABLE `supplier_kpi_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `supplier_products`
--
ALTER TABLE `supplier_products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `supplier_product_unique` (`supplier_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `tax_codes`
--
ALTER TABLE `tax_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tax_code` (`tax_code`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `fk_users_department` (`department_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ap_bills`
--
ALTER TABLE `ap_bills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ap_payments`
--
ALTER TABLE `ap_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ap_vendors`
--
ALTER TABLE `ap_vendors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ar_customers`
--
ALTER TABLE `ar_customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ar_invoices`
--
ALTER TABLE `ar_invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ar_payments`
--
ALTER TABLE `ar_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `assets`
--
ALTER TABLE `assets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `asset_types`
--
ALTER TABLE `asset_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=186;

--
-- AUTO_INCREMENT for table `bank_accounts`
--
ALTER TABLE `bank_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `bank_transactions`
--
ALTER TABLE `bank_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `budgets`
--
ALTER TABLE `budgets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `chart_of_accounts`
--
ALTER TABLE `chart_of_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `compliance_checklists`
--
ALTER TABLE `compliance_checklists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `deliveries`
--
ALTER TABLE `deliveries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `delivery_items`
--
ALTER TABLE `delivery_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `journal_entries`
--
ALTER TABLE `journal_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `journal_entry_lines`
--
ALTER TABLE `journal_entry_lines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `po_items`
--
ALTER TABLE `po_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=172;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `project_tasks`
--
ALTER TABLE `project_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `supplier_communication_logs`
--
ALTER TABLE `supplier_communication_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `supplier_compliance_status`
--
ALTER TABLE `supplier_compliance_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `supplier_contacts`
--
ALTER TABLE `supplier_contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `supplier_contracts`
--
ALTER TABLE `supplier_contracts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `supplier_info_changes`
--
ALTER TABLE `supplier_info_changes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `supplier_kpi_history`
--
ALTER TABLE `supplier_kpi_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supplier_products`
--
ALTER TABLE `supplier_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tax_codes`
--
ALTER TABLE `tax_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ap_bills`
--
ALTER TABLE `ap_bills`
  ADD CONSTRAINT `ap_bills_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `ap_vendors` (`id`),
  ADD CONSTRAINT `ap_bills_ibfk_2` FOREIGN KEY (`tax_code_id`) REFERENCES `tax_codes` (`id`);

--
-- Constraints for table `ap_payments`
--
ALTER TABLE `ap_payments`
  ADD CONSTRAINT `ap_payments_ibfk_1` FOREIGN KEY (`bill_id`) REFERENCES `ap_bills` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ar_invoices`
--
ALTER TABLE `ar_invoices`
  ADD CONSTRAINT `ar_invoices_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `ar_customers` (`id`),
  ADD CONSTRAINT `ar_invoices_ibfk_2` FOREIGN KEY (`tax_code_id`) REFERENCES `tax_codes` (`id`);

--
-- Constraints for table `ar_payments`
--
ALTER TABLE `ar_payments`
  ADD CONSTRAINT `ar_payments_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `ar_invoices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `assets`
--
ALTER TABLE `assets`
  ADD CONSTRAINT `assets_ibfk_1` FOREIGN KEY (`asset_type_id`) REFERENCES `asset_types` (`id`),
  ADD CONSTRAINT `assets_ibfk_2` FOREIGN KEY (`assigned_to_employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD CONSTRAINT `audit_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `bank_transactions`
--
ALTER TABLE `bank_transactions`
  ADD CONSTRAINT `bank_transactions_ibfk_1` FOREIGN KEY (`bank_account_id`) REFERENCES `bank_accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `budgets`
--
ALTER TABLE `budgets`
  ADD CONSTRAINT `budgets_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`);

--
-- Constraints for table `deliveries`
--
ALTER TABLE `deliveries`
  ADD CONSTRAINT `deliveries_ibfk_1` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`id`);

--
-- Constraints for table `delivery_items`
--
ALTER TABLE `delivery_items`
  ADD CONSTRAINT `delivery_items_ibfk_1` FOREIGN KEY (`delivery_id`) REFERENCES `deliveries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `delivery_items_ibfk_2` FOREIGN KEY (`po_item_id`) REFERENCES `po_items` (`id`);

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `employees_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`id`),
  ADD CONSTRAINT `invoices_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`);

--
-- Constraints for table `journal_entry_lines`
--
ALTER TABLE `journal_entry_lines`
  ADD CONSTRAINT `journal_entry_lines_ibfk_1` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`id`);

--
-- Constraints for table `po_items`
--
ALTER TABLE `po_items`
  ADD CONSTRAINT `po_items_ibfk_1` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `po_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`);

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`budget_id`) REFERENCES `budgets` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `projects_ibfk_2` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `projects_ibfk_3` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `project_tasks`
--
ALTER TABLE `project_tasks`
  ADD CONSTRAINT `project_tasks_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_tasks_ibfk_2` FOREIGN KEY (`assigned_to_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `purchase_orders_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  ADD CONSTRAINT `purchase_orders_ibfk_2` FOREIGN KEY (`budget_id`) REFERENCES `budgets` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `supplier_communication_logs`
--
ALTER TABLE `supplier_communication_logs`
  ADD CONSTRAINT `supplier_communication_logs_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `supplier_compliance_status`
--
ALTER TABLE `supplier_compliance_status`
  ADD CONSTRAINT `supplier_compliance_status_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `supplier_compliance_status_ibfk_2` FOREIGN KEY (`checklist_id`) REFERENCES `compliance_checklists` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `supplier_contacts`
--
ALTER TABLE `supplier_contacts`
  ADD CONSTRAINT `supplier_contacts_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `supplier_contracts`
--
ALTER TABLE `supplier_contracts`
  ADD CONSTRAINT `supplier_contracts_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `supplier_info_changes`
--
ALTER TABLE `supplier_info_changes`
  ADD CONSTRAINT `supplier_info_changes_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `supplier_info_changes_ibfk_2` FOREIGN KEY (`reviewed_by_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `supplier_kpi_history`
--
ALTER TABLE `supplier_kpi_history`
  ADD CONSTRAINT `supplier_kpi_history_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `supplier_products`
--
ALTER TABLE `supplier_products`
  ADD CONSTRAINT `supplier_products_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `supplier_products_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`),
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
