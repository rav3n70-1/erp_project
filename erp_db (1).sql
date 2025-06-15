-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 14, 2025 at 05:30 PM
-- Server version: 10.4.32-MariaDB
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(165, 1, 'Toggled active status for employee', 'Employee', 3, '2025-06-14 15:28:37');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `budgets`
--

INSERT INTO `budgets` (`id`, `budget_name`, `department_id`, `allocated_amount`, `start_date`, `end_date`, `created_at`) VALUES
(3, 'Buying', 4, 100000.00, '2025-06-11', '2025-07-11', '2025-06-11 05:13:57'),
(4, 'Marketing', 3, 201000.00, '2025-06-11', '2025-06-30', '2025-06-11 05:14:31'),
(5, 'Buy', 4, 10000.00, '2025-06-12', '2025-06-13', '2025-06-12 06:37:39'),
(6, 'Marketing1', 3, 20000.00, '2025-06-14', '2025-06-20', '2025-06-14 15:18:22');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(32, 49, '2025-06-12', 'Delivered', '', NULL, '2025-06-12 12:29:57');

-- --------------------------------------------------------

--
-- Table structure for table `delivery_items`
--

CREATE TABLE `delivery_items` (
  `id` int(11) NOT NULL,
  `delivery_id` int(11) NOT NULL,
  `po_item_id` int(11) NOT NULL,
  `quantity_received` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(34, 32, 59, 2);

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `department_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `first_name`, `last_name`, `email`, `phone_number`, `hire_date`, `job_title`, `salary`, `department_id`, `user_id`, `is_active`) VALUES
(2, 'Toufique ', 'Ahamed', 'tofa@gm.com', '0123456789963', '2025-06-13', 'Procurement Officer', 31000.00, 4, 17, 1),
(3, 'Shahinur Alam', 'Rabbi', 'Shahinur@g.com', '852741963', '2025-06-13', 'Marketing Manager', 25000.00, 3, 18, 1);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'The user who will receive the notification',
  `message` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL COMMENT 'A link to the relevant page (e.g., a PO)',
  `is_read` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = unread, 1 = read',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(121, 16, 'New project \'Marketing8\' requires approval.', '/erp_project/modules/projects/view_project_details.php?id=18', 1, '2025-06-14 15:19:26');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(24, 51, '2025-06-12', 1325.00, 'Bank Transfer', '', '2025-06-12 10:48:21');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(65, 55, 20, 50, 699.00, 34950.00);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `sku`, `product_name`, `description`, `price`, `category_id`, `quantity_in_stock`, `reorder_point`, `created_at`, `updated_at`) VALUES
(3, '1 Ton', 'Rod', '18 mm Rod', 150.00, 1, 6334, NULL, '2025-06-10 10:07:15', '2025-06-11 04:26:17'),
(4, '200', 'testdata', 'testdataa', 1000.00, 5, 289, NULL, '2025-06-10 11:29:03', '2025-06-11 03:55:20'),
(6, '1200', 'tin', '20mm', 120.00, 4, 199, 200, '2025-06-11 09:38:59', '2025-06-11 17:36:04'),
(7, '20 MM nut', 'Nut', '20 MM nut', 20.00, 5, 1206, NULL, '2025-06-11 09:46:29', '2025-06-12 12:29:57'),
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
(20, 'SKU010', 'Cushion Cover Set', 'Set of 5 decorative cushion covers (16x16 inch)', 699.00, 9, 0, 20, '2025-06-11 15:19:28', '2025-06-12 02:46:31');

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(12, 'Fashion', '2025-06-11 15:19:16');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `project_name`, `description`, `start_date`, `end_date`, `project_budget`, `budget_id`, `manager_id`, `client_id`, `status`, `created_at`) VALUES
(7, 'Marketing4', 'Marketing4', '2025-06-11', '2025-06-12', 1200.00, 4, 8, NULL, 'In Progress', '2025-06-11 16:28:05'),
(8, 'Marketing', 'Marketing', '2025-06-11', '2025-06-12', 12000.00, 4, 16, NULL, 'Rejected', '2025-06-11 16:29:26'),
(9, 'Marketing2', 'Marketing2', '2025-06-11', '2025-06-12', 12000.00, 3, 8, NULL, 'In Progress', '2025-06-11 16:37:30'),
(10, 'Marketing3', 'Marketing3', '2025-06-11', '2025-06-12', 12000.00, 3, 1, NULL, 'Approved', '2025-06-11 16:41:15'),
(11, 'Marketing5', 'Marketing5', '2025-06-11', '2025-06-22', 12000.00, 3, 8, NULL, 'Rejected', '2025-06-11 16:42:48'),
(12, 'Buy1', 'Buy1', '2025-06-11', '2025-06-13', 1200.00, 3, 8, NULL, 'Approved', '2025-06-11 16:53:22'),
(13, 'buy2', 'buy2', '2025-06-11', '2025-06-13', 20000.00, 4, 1, NULL, 'Approved', '2025-06-11 16:54:47'),
(14, 'buy3', 'buy3', '2025-06-11', '2025-06-17', 20000.00, 4, 1, NULL, 'Approved', '2025-06-11 16:56:14'),
(15, 'Marketing7', 'Marketing7', '2025-06-13', '2025-06-19', 500.00, 5, 1, NULL, 'In Progress', '2025-06-13 05:04:21'),
(16, 'DD', 'test', '2025-06-13', '2025-06-24', 80.00, 5, 1, 1, 'Canceled', '2025-06-13 05:21:19'),
(17, 'DD2', 'asx', '2025-06-13', '2026-10-12', 9.99, 4, 8, 1, 'Rejected', '2025-06-13 05:31:23'),
(18, 'Marketing8', 'Marketing8', '2025-06-14', '2025-06-23', 15000.00, 6, 1, 1, 'Rejected', '2025-06-14 15:19:26');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project_tasks`
--

INSERT INTO `project_tasks` (`id`, `project_id`, `task_name`, `assigned_to_user_id`, `due_date`, `status`) VALUES
(4, 7, 'Start', 10, '2025-06-11', 'To Do'),
(5, 7, 'End', 5, '2025-06-12', 'To Do'),
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(55, 'DRAFT-1749734803-20', 6, NULL, '2025-06-12', NULL, 34950.00, 'Draft', '2025-06-12 13:26:43');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `supplier_name`, `address`, `tax_id`, `username`, `password`, `is_active`, `bank_name`, `bank_account_number`, `bank_branch_code`, `created_at`, `updated_at`, `rating_delivery_time`, `rating_quality`, `rating_communication`, `on_time_delivery_rate`) VALUES
(5, 'Rohan', '2817 Jerry Toth Drive', '181900', NULL, NULL, 0, NULL, NULL, NULL, '2025-06-10 08:00:44', '2025-06-13 03:14:55', 3.0, 3.0, 4.0, 0.00),
(6, 'test2', 'test2', '1236545', 'supl1', '$2y$10$xpP/sMrD1Qv2ScNS68wZaO9EgW8MBTRN6alIZN9jLDjhd4q.1SPOC', 1, 'Brack', '123741852963', NULL, '2025-06-10 08:41:08', '2025-06-12 13:36:07', 3.5, 3.5, 5.0, 0.00),
(8, 'supl22', 'supl2', '123669', 'supl2', '$2y$10$1Xmic7IfY84VoVkVg2JOwOpbjV0Y.0MEXjMSD20peg5chPzj3rWmy', 1, 'Brac Bank', '129233', NULL, '2025-06-11 15:31:37', '2025-06-14 15:21:52', NULL, NULL, NULL, 0.00),
(9, 'testsup3', 'testsup3', '1236449', 'testsup3', '$2y$10$ietmVzEgII8g.sQq5MwHYOJd4Q2xnv4bxE/KSbdK4U6COlozD1zlW', 1, NULL, NULL, NULL, '2025-06-11 15:36:43', '2025-06-12 10:44:40', NULL, NULL, NULL, 0.00),
(10, 'test3', 'test3', '1285285', NULL, NULL, 1, NULL, NULL, NULL, '2025-06-12 05:01:29', '2025-06-12 10:44:40', NULL, NULL, NULL, 0.00),
(11, 'sup3', 'sup3', '875296', NULL, NULL, 1, NULL, NULL, NULL, '2025-06-12 05:02:01', '2025-06-12 10:44:40', NULL, NULL, NULL, 0.00),
(12, 'sup4', 'sup4', '796541236', NULL, NULL, 1, NULL, NULL, NULL, '2025-06-13 02:43:11', '2025-06-13 02:43:11', NULL, NULL, NULL, NULL),
(13, 'Rohan2', 'Rohan2', '79654412369', 'Rohan2', '$2y$10$/IeBxvX75iJx2igJ/XwMpePMMkmvvtVat2ykb7M4F8tFkMIco75.e', 1, NULL, NULL, NULL, '2025-06-13 03:15:31', '2025-06-13 03:16:06', 5.0, 5.0, 5.0, NULL);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(13, 13, 'Rohan2', 'Rohan2@g.com', '7954139');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `supplier_products`
--

CREATE TABLE `supplier_products` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `supplier_item_code` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role_id`, `is_active`, `department_id`, `created_at`) VALUES
(1, 'admin', '$2y$10$Lrh8hHVfCjEqxBxUKZJeB.id2Fz/43a.6uTl5rDfw1KREv0OLPAhi', 'admin@example.com', 1, 1, NULL, '2025-06-10 10:55:15'),
(5, 'superadmin', '$2y$10$yAPYqhh5eY/JyXx2lFA43uromd.vEQmQMprz4NxGjj4u6woplyDEC', 'super@example.com', 2, 1, NULL, '2025-06-11 06:19:07'),
(6, 'procofficer', '$2y$10$yU.x9TNOarZcbgeh30K5Nuwuj6hq8.Hil5UP67aiDMs8AfXDOZLgO', 'officer@example.com', 4, 1, NULL, '2025-06-11 06:19:07'),
(8, 'projectm', '$2y$10$1dodNliOy9k6Wal5WoPHAOqpx5sHK5Euspl7UpKZZ6UFgBVvC9vru', 'projectm@g.com', 8, 1, NULL, '2025-06-11 07:10:52'),
(9, 'hr', '$2y$10$mbUc1VQK6440TdIKqDtEeuJ84WxBChYr0bzeB7//HyyEH7NoAPwaq', 'hr@a.com', 6, 1, NULL, '2025-06-11 07:17:04'),
(10, 'finance', '$2y$10$KTZAGww/9PV3YHwN4tLUvuEtJ0xNA6dqsu9e75YRJwIBTMYiHTiei', 'finance@g.com', 5, 1, NULL, '2025-06-11 07:18:24'),
(11, 'prooff', '$2y$10$v0IqBF1O8cv.KkCuX/x8VeKA8eDdPiQfeF.SD6mrzbXHrqAYy4A62', 'prooff@gmail.com', 4, 1, NULL, '2025-06-11 08:52:30'),
(12, 'inven', '$2y$10$IY.3w/wqdMoP9qZrcBEHe.i6hUdc2q4PFrAeFryG0a6sBguFtUJxa', 'inven@g.com', 7, 1, NULL, '2025-06-11 14:46:11'),
(14, 'supl2', '$2y$10$O1y9yi0l9kEeTX/Mf4lxXeWjEiKbTeHXWoVcCPa/GgDPWI23DqxYm', 'supl2@g.com', 10, 1, NULL, '2025-06-11 15:31:56'),
(15, 'testsup3', '$2y$10$8ys7z936GuA/eMBToM/c7evlibcgXiq2r73bjglUd9GQ9QKWV5u5C', 'testsup3@g.com', 10, 1, NULL, '2025-06-11 15:36:59'),
(16, 'deptman', '$2y$10$3xZUtqPRiCOA7tTpwrqhEOLbdS2pw/4L24t0deUJ4oBGrWZHmVSxu', 'deptman@g.com', 3, 1, NULL, '2025-06-11 15:51:22'),
(17, 'tofa', '$2y$10$B/ioL/gLdsozNYZU7gmP3u1qZsUI6mCKPznBRgq9sW10.CLvZZ67q', 'tofa@gmai.com', 4, 1, NULL, '2025-06-13 02:25:19'),
(18, 'Shahinur', '$2y$10$u91dl48tXyt.oLeT5D3F5uNLoAUEQ3lo7PT9rKSXgE8gB6cVX16XK', 'Shahinur@g.com', 7, 1, NULL, '2025-06-13 02:28:37'),
(19, 'audit', '$2y$10$faIu1lseLINBohGvpA/AbeUJ3ap2uJG8Zn9Q15kCRgIlUBeRMxcsq', 'audit@g.com', 13, 1, NULL, '2025-06-13 02:30:14'),
(20, 'superad', '$2y$10$EFM3/WniDVWG3iSl.8j8G.9ZLgwQjInYZq2kQCjnRUEhAMVyJiUKy', 'superad@gmail.com', 2, 1, NULL, '2025-06-13 02:37:44'),
(21, 'moshi', '$2y$10$0BGhGweFNudDEQzwXbrJg.sFX7q6Oi7MBfS12GzSuMRo4l15o6YgK', 'moshi@g.com', 11, 1, NULL, '2025-06-13 05:05:43');

--
-- Indexes for dumped tables
--

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
-- Indexes for table `budgets`
--
ALTER TABLE `budgets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_id` (`department_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=166;

--
-- AUTO_INCREMENT for table `budgets`
--
ALTER TABLE `budgets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `delivery_items`
--
ALTER TABLE `delivery_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=122;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `po_items`
--
ALTER TABLE `po_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

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
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

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
