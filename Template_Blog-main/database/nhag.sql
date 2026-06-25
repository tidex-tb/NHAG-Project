-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 17, 2026 at 11:51 AM
-- Server version: 8.4.7
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS;
SET FOREIGN_KEY_CHECKS=0;


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nhag`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `action` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `entity_type` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `entity_id` int DEFAULT NULL,
  `old_values` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_general_ci,
  `logged_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `entity_type` (`entity_type`),
  KEY `logged_at` (`logged_at`),
  KEY `action` (`action`),
  KEY `idx_audit_entity` (`entity_type`,`entity_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity_type`, `entity_id`, `old_values`, `new_values`, `description`, `ip_address`, `user_agent`, `logged_at`) VALUES
(1, 1, 'LOGIN', 'User', NULL, NULL, NULL, 'User login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-15 17:03:55'),
(2, 1, 'LOGIN', 'User', NULL, NULL, NULL, 'User login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-15 17:15:35'),
(3, 1, 'LOGIN', 'User', NULL, NULL, NULL, 'User login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-15 17:26:53'),
(4, 1, 'LOGIN', 'User', NULL, NULL, NULL, 'User login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-16 03:05:12'),
(5, 1, 'LOGIN', 'User', NULL, NULL, NULL, 'User login', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36', '2026-06-17 09:51:13');

-- --------------------------------------------------------

--
-- Table structure for table `contributions`
--

DROP TABLE IF EXISTS `contributions`;
CREATE TABLE IF NOT EXISTS `contributions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `reference_number` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `member_id` int NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_method` enum('Bank Transfer','Cash','Cheque','Mobile Money') COLLATE utf8mb4_general_ci DEFAULT 'Bank Transfer',
  `transaction_date` date NOT NULL,
  `received_date` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_general_ci,
  `status` enum('Pending','Approved','Rejected') COLLATE utf8mb4_general_ci DEFAULT 'Pending',
  `approved_by` int DEFAULT NULL,
  `approval_date` datetime DEFAULT NULL,
  `rejection_reason` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` int DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reference_number` (`reference_number`),
  KEY `approved_by` (`approved_by`),
  KEY `created_by` (`created_by`),
  KEY `status` (`status`),
  KEY `transaction_date` (`transaction_date`),
  KEY `member_id` (`member_id`),
  KEY `idx_contributions_member` (`member_id`),
  KEY `idx_contributions_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contribution_documents`
--

DROP TABLE IF EXISTS `contribution_documents`;
CREATE TABLE IF NOT EXISTS `contribution_documents` (
  `id` int NOT NULL AUTO_INCREMENT,
  `contribution_id` int NOT NULL,
  `document_type` enum('Proof of Payment','Receipt','Invoice','Other') COLLATE utf8mb4_general_ci DEFAULT 'Proof of Payment',
  `file_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `file_size` int DEFAULT NULL,
  `file_type` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `uploaded_by` int DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uploaded_by` (`uploaded_by`),
  KEY `contribution_id` (`contribution_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contribution_status_history`
--

DROP TABLE IF EXISTS `contribution_status_history`;
CREATE TABLE IF NOT EXISTS `contribution_status_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `contribution_id` int NOT NULL,
  `old_status` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `new_status` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `changed_reason` text COLLATE utf8mb4_general_ci,
  `changed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `changed_by` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `changed_by` (`changed_by`),
  KEY `contribution_id` (`contribution_id`),
  KEY `changed_at` (`changed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `group_data_entries`
--

DROP TABLE IF EXISTS `group_data_entries`;
CREATE TABLE IF NOT EXISTS `group_data_entries` (
  `id` int NOT NULL AUTO_INCREMENT,
  `region_name` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `municipal_area_name` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `area_type` varchar(80) COLLATE utf8mb4_general_ci NOT NULL,
  `area_name` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `area_count` int DEFAULT '0',
  `entry_notes` text COLLATE utf8mb4_general_ci,
  `capture_year` int NOT NULL,
  `group_number` varchar(80) COLLATE utf8mb4_general_ci NOT NULL,
  `group_name` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `number_of_members` int DEFAULT '0',
  `number_of_males` int DEFAULT '0',
  `number_of_females` int DEFAULT '0',
  `savings_brought_forward` decimal(15,2) DEFAULT '0.00',
  `six_month_savings` decimal(15,2) DEFAULT '0.00',
  `total_land` decimal(15,2) DEFAULT '0.00',
  `total_member_withdrawals` decimal(15,2) DEFAULT '0.00',
  `total_loans_issued` decimal(15,2) DEFAULT '0.00',
  `total_loans_repaid` decimal(15,2) DEFAULT '0.00',
  `total_interest_earned` decimal(15,2) DEFAULT '0.00',
  `total_advance_payments` decimal(15,2) DEFAULT '0.00',
  `total_short` decimal(15,2) DEFAULT '0.00',
  `total_cash` decimal(15,2) DEFAULT '0.00',
  `total_bank_amount` decimal(15,2) DEFAULT '0.00',
  `total_bank_charges` decimal(15,2) DEFAULT '0.00',
  `total_business` decimal(15,2) DEFAULT '0.00',
  `total_daily_needs` decimal(15,2) DEFAULT '0.00',
  `total_ppe` decimal(15,2) DEFAULT '0.00',
  `total_buy_a_brick` decimal(15,2) DEFAULT '0.00',
  `total_expenditure` decimal(15,2) DEFAULT '0.00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` int DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `region_name` (`region_name`),
  KEY `capture_year` (`capture_year`),
  KEY `group_number` (`group_number`),
  KEY `created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

DROP TABLE IF EXISTS `members`;
CREATE TABLE IF NOT EXISTS `members` (
  `id` int NOT NULL AUTO_INCREMENT,
  `member_number` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` int DEFAULT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `gender` enum('Male','Female','Other') COLLATE utf8mb4_general_ci DEFAULT 'Other',
  `date_of_birth` date DEFAULT NULL,
  `national_id` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `phone_number` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `physical_address` text COLLATE utf8mb4_general_ci,
  `registration_date` date NOT NULL,
  `status` enum('Active','Suspended','Inactive') COLLATE utf8mb4_general_ci DEFAULT 'Active',
  `next_of_kin_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `next_of_kin_phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `next_of_kin_relationship` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `employment_status` enum('Employed','Self-Employed','Unemployed','Student','Retired') COLLATE utf8mb4_general_ci DEFAULT 'Employed',
  `employer_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `monthly_income` decimal(12,2) DEFAULT NULL,
  `total_savings` decimal(15,2) DEFAULT '0.00',
  `current_balance` decimal(15,2) DEFAULT '0.00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` int DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `member_number` (`member_number`),
  UNIQUE KEY `national_id` (`national_id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`),
  KEY `registration_date` (`registration_date`),
  KEY `idx_members_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- --------------------------------------------------------

--
-- Table structure for table `member_profile_requests`
--

DROP TABLE IF EXISTS `member_profile_requests`;
CREATE TABLE IF NOT EXISTS `member_profile_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `gender` enum('Male','Female','Other') COLLATE utf8mb4_general_ci DEFAULT 'Other',
  `date_of_birth` date DEFAULT NULL,
  `national_id` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `phone_number` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `physical_address` text COLLATE utf8mb4_general_ci,
  `status` enum('Pending','Approved','Rejected') COLLATE utf8mb4_general_ci DEFAULT 'Pending',
  `member_id` int DEFAULT NULL,
  `admin_notes` text COLLATE utf8mb4_general_ci,
  `reviewed_by` int DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `status` (`status`),
  KEY `member_id` (`member_id`),
  KEY `reviewed_by` (`reviewed_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


--
-- Table structure for table `member_profiles`
--

DROP TABLE IF EXISTS `member_profiles`;
CREATE TABLE IF NOT EXISTS `member_profiles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `member_id` int NOT NULL,
  `profile_picture` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `about_me` text COLLATE utf8mb4_general_ci,
  `bank_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bank_account_number` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bank_account_holder` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `member_status_history`
--

DROP TABLE IF EXISTS `member_status_history`;
CREATE TABLE IF NOT EXISTS `member_status_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `member_id` int NOT NULL,
  `old_status` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `new_status` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `reason` text COLLATE utf8mb4_general_ci,
  `changed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `changed_by` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `changed_by` (`changed_by`),
  KEY `member_id` (`member_id`),
  KEY `changed_at` (`changed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `message` text COLLATE utf8mb4_general_ci NOT NULL,
  `notification_type` enum('Registration','Contribution','Approval','Rejection','Statement','System') COLLATE utf8mb4_general_ci DEFAULT 'System',
  `related_entity_type` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `related_entity_id` int DEFAULT NULL,
  `channel` enum('In-System','Email','SMS') COLLATE utf8mb4_general_ci DEFAULT 'In-System',
  `is_read` tinyint(1) DEFAULT '0',
  `read_at` datetime DEFAULT NULL,
  `sent_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `is_read` (`is_read`),
  KEY `created_at` (`created_at`),
  KEY `idx_notifications_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `module` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `action` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `description`, `module`, `action`, `created_at`) VALUES
(1, 'view_members', 'View member list', 'members', 'view', '2026-06-14 01:20:33'),
(2, 'create_member', 'Create new member', 'members', 'create', '2026-06-14 01:20:33'),
(3, 'edit_member', 'Edit member details', 'members', 'edit', '2026-06-14 01:20:33'),
(4, 'delete_member', 'Delete member', 'members', 'delete', '2026-06-14 01:20:33'),
(5, 'suspend_member', 'Suspend member account', 'members', 'suspend', '2026-06-14 01:20:33'),
(6, 'activate_member', 'Activate member account', 'members', 'activate', '2026-06-14 01:20:33'),
(7, 'view_member_profile', 'View member profile', 'members', 'view_profile', '2026-06-14 01:20:33'),
(8, 'view_contributions', 'View contributions', 'contributions', 'view', '2026-06-14 01:20:33'),
(9, 'submit_contribution', 'Submit contribution', 'contributions', 'submit', '2026-06-14 01:20:33'),
(10, 'approve_contribution', 'Approve contribution', 'contributions', 'approve', '2026-06-14 01:20:33'),
(11, 'reject_contribution', 'Reject contribution', 'contributions', 'reject', '2026-06-14 01:20:33'),
(12, 'edit_contribution', 'Edit contribution', 'contributions', 'edit', '2026-06-14 01:20:33'),
(13, 'view_ledger', 'View savings ledger', 'ledger', 'view', '2026-06-14 01:20:33'),
(14, 'manage_ledger', 'Manage ledger entries', 'ledger', 'manage', '2026-06-14 01:20:33'),
(15, 'view_statement', 'View member statement', 'statements', 'view', '2026-06-14 01:20:33'),
(16, 'download_statement', 'Download statement', 'statements', 'download', '2026-06-14 01:20:33'),
(17, 'email_statement', 'Email statement', 'statements', 'email', '2026-06-14 01:20:33'),
(18, 'view_reports', 'View reports', 'reports', 'view', '2026-06-14 01:20:33'),
(19, 'generate_reports', 'Generate reports', 'reports', 'generate', '2026-06-14 01:20:33'),
(20, 'export_reports', 'Export reports', 'reports', 'export', '2026-06-14 01:20:33'),
(21, 'view_notifications', 'View notifications', 'notifications', 'view', '2026-06-14 01:20:33'),
(22, 'manage_notifications', 'Manage notifications', 'notifications', 'manage', '2026-06-14 01:20:33'),
(23, 'view_audit_logs', 'View audit logs', 'audit', 'view', '2026-06-14 01:20:33'),
(24, 'manage_users', 'Manage system users', 'users', 'manage', '2026-06-14 01:20:33'),
(25, 'manage_roles', 'Manage roles', 'roles', 'manage', '2026-06-14 01:20:33'),
(26, 'manage_permissions', 'Manage permissions', 'permissions', 'manage', '2026-06-14 01:20:33');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Super Administrator', 'Full system access and configuration', '2026-06-14 01:20:33', '2026-06-14 01:20:33'),
(2, 'Administrator', 'Member management and system oversight', '2026-06-14 01:20:33', '2026-06-14 01:20:33'),
(3, 'Finance Officer', 'Contribution processing and approvals', '2026-06-14 01:20:33', '2026-06-14 01:20:33'),
(4, 'Member', 'Self-service savings management', '2026-06-14 01:20:33', '2026-06-14 01:20:33');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
CREATE TABLE IF NOT EXISTS `role_permissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `role_id` int NOT NULL,
  `permission_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_role_permission` (`role_id`,`permission_id`),
  KEY `permission_id` (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO 
ole_permissions (
ole_id, permission_id) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16),
(1, 17),
(1, 18),
(1, 19),
(1, 20),
(1, 21),
(1, 22),
(1, 23),
(1, 24),
(1, 25),
(1, 26),
(2, 1),
(2, 2),
(2, 3),
(2, 5),
(2, 6),
(2, 7),
(2, 8),
(2, 13),
(2, 15),
(2, 16),
(2, 18),
(2, 19),
(2, 20),
(2, 21),
(2, 24),
(3, 8),
(3, 10),
(3, 11),
(3, 13),
(3, 14),
(3, 15),
(3, 16),
(3, 18),
(3, 19),
(3, 20),
(3, 21),
(4, 7),
(4, 9),
(4, 15),
(4, 16),
(4, 21);

-- --------------------------------------------------------

--
-- Table structure for table `savings_ledger`
--

DROP TABLE IF EXISTS `savings_ledger`;
CREATE TABLE IF NOT EXISTS `savings_ledger` (
  `id` int NOT NULL AUTO_INCREMENT,
  `member_id` int NOT NULL,
  `transaction_type` enum('Contribution','Adjustment','Reversal','Correction','Dividend','Withdrawal') COLLATE utf8mb4_general_ci DEFAULT 'Contribution',
  `reference_number` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `debit_amount` decimal(12,2) DEFAULT '0.00',
  `credit_amount` decimal(12,2) DEFAULT '0.00',
  `balance_after` decimal(15,2) DEFAULT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `transaction_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `member_id` (`member_id`),
  KEY `transaction_date` (`transaction_date`),
  KEY `transaction_type` (`transaction_type`),
  KEY `idx_ledger_member` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `statements`
--

DROP TABLE IF EXISTS `statements`;
CREATE TABLE IF NOT EXISTS `statements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `statement_number` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `member_id` int NOT NULL,
  `statement_type` enum('Monthly','Quarterly','Annual','Custom') COLLATE utf8mb4_general_ci DEFAULT 'Monthly',
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `opening_balance` decimal(15,2) DEFAULT NULL,
  `total_contributions` decimal(15,2) DEFAULT NULL,
  `total_adjustments` decimal(15,2) DEFAULT NULL,
  `closing_balance` decimal(15,2) DEFAULT NULL,
  `generated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `generated_by` int DEFAULT NULL,
  `pdf_file_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `downloaded_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `statement_number` (`statement_number`),
  KEY `generated_by` (`generated_by`),
  KEY `member_id` (`member_id`),
  KEY `period_start` (`period_start`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

DROP TABLE IF EXISTS `system_settings`;
CREATE TABLE IF NOT EXISTS `system_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_general_ci,
  `data_type` enum('string','integer','boolean','json') COLLATE utf8mb4_general_ci DEFAULT 'string',
  `description` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `data_type`, `description`, `updated_at`) VALUES
(1, 'company_name', 'Namibia Housing Action Group', 'string', 'Organization name', '2026-06-14 01:25:20'),
(2, 'company_phone', '+264 61 123456', 'string', 'Organization phone number', '2026-06-14 01:25:20'),
(3, 'company_email', 'info@nhag.org.na', 'string', 'Organization email', '2026-06-14 01:25:20'),
(4, 'company_address', 'Windhoek, Namibia', 'string', 'Organization address', '2026-06-14 01:25:20'),
(5, 'notification_email_enabled', '1', 'boolean', 'Enable email notifications', '2026-06-14 01:25:20'),
(6, 'notification_sms_enabled', '0', 'boolean', 'Enable SMS notifications', '2026-06-14 01:25:20'),
(7, 'max_upload_size_mb', '10', 'integer', 'Maximum file upload size in MB', '2026-06-14 01:25:20'),
(8, 'allowed_file_types', 'pdf,jpg,png,jpeg', 'string', 'Allowed file extensions', '2026-06-14 01:25:20'),
(9, 'member_number_prefix', 'MEM', 'string', 'Member number prefix', '2026-06-14 01:25:20'),
(10, 'contribution_ref_prefix', 'CONT', 'string', 'Contribution reference prefix', '2026-06-14 01:25:20'),
(11, 'statement_number_prefix', 'STMT', 'string', 'Statement number prefix', '2026-06-14 01:25:20');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `phone_number` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `profile_picture` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `role_id` int DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_by` int DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`),
  KEY `role_id` (`role_id`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `username`, `password`, `phone_number`, `profile_picture`, `role_id`, `is_active`, `last_login`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`) VALUES
(1, 'Raimo Elvis Dengeinge Jr.', 'raimobfg@gmail.com', 'Trae', '$2y$10$zGI9QauL4EH4p4Ivl7nY2e.W8ClhuU6GQ/7xGKQirBLdn3eQBtSe.', NULL, NULL, 1, 1, NULL, '2026-06-15 19:02:16', NULL, '2026-06-15 19:25:19', NULL, NULL);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `contributions`
--
ALTER TABLE `contributions`
  ADD CONSTRAINT `contributions_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contributions_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `contributions_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `contribution_documents`
--
ALTER TABLE `contribution_documents`
  ADD CONSTRAINT `contribution_documents_ibfk_1` FOREIGN KEY (`contribution_id`) REFERENCES `contributions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contribution_documents_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `contribution_status_history`
--
ALTER TABLE `contribution_status_history`
  ADD CONSTRAINT `contribution_status_history_ibfk_1` FOREIGN KEY (`contribution_id`) REFERENCES `contributions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contribution_status_history_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `members`
--
ALTER TABLE `members`
  ADD CONSTRAINT `members_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `member_profiles`
--
ALTER TABLE `member_profiles`
--
-- Constraints for table `member_profile_requests`
--
ALTER TABLE `member_profile_requests`
  ADD CONSTRAINT `member_profile_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `member_profile_requests_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `member_profile_requests_ibfk_3` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

  ADD CONSTRAINT `member_profiles_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `member_status_history`
--
ALTER TABLE `member_status_history`
  ADD CONSTRAINT `member_status_history_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `member_status_history_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `savings_ledger`
--
ALTER TABLE `savings_ledger`
  ADD CONSTRAINT `savings_ledger_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `savings_ledger_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `statements`
--
ALTER TABLE `statements`
  ADD CONSTRAINT `statements_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `statements_ibfk_2` FOREIGN KEY (`generated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
