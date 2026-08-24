-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 21, 2026 at 04:54 AM
-- Server version: 10.1.30-MariaDB
-- PHP Version: 7.2.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tracegrad_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `activity_id` int(11) NOT NULL,
  `user_type` enum('Admin','Alumni') COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `module` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activity` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`activity_id`, `user_type`, `user_id`, `module`, `activity`, `description`, `ip_address`, `created_at`) VALUES
(1, 'Admin', 1, 'Colleges', 'Added college', 'COED', '127.0.0.1', '2026-08-12 15:21:26'),
(2, 'Admin', 1, 'Colleges', 'Added degree program', 'BEED', '127.0.0.1', '2026-08-12 15:22:22'),
(3, 'Admin', 1, 'Roster', 'Added alumni', '23-00016', '127.0.0.1', '2026-08-12 15:33:05'),
(4, 'Admin', 1, 'Admins', 'Added admin', 'cici-dept', '127.0.0.1', '2026-08-12 16:01:34'),
(5, 'Admin', 1, 'Admins', 'Toggled admin', 'admin_id=2', '127.0.0.1', '2026-08-12 16:01:43'),
(6, 'Admin', 1, 'Admins', 'Toggled admin', 'admin_id=2', '127.0.0.1', '2026-08-12 16:01:46'),
(7, 'Admin', 7, 'Gallery', 'Created album', 'Alumni', '127.0.0.1', '2026-08-12 22:43:41'),
(8, 'Admin', 7, 'Gallery', 'Uploaded image', '645419072_911056641914182_7920679560041989265_n.jpg', '127.0.0.1', '2026-08-12 22:44:33'),
(9, 'Admin', 7, 'Roster', 'Deleted alumni', 'graduate_id=1', '127.0.0.1', '2026-08-12 22:53:02'),
(10, 'Admin', 7, 'Roster', 'Added alumni', '2024-00016', '127.0.0.1', '2026-08-12 22:54:09'),
(11, 'Admin', 7, 'Admins', 'Edited admin', 'admin_id=4', '127.0.0.1', '2026-08-17 12:35:29'),
(12, 'Admin', 7, 'Roster', 'Deleted alumni', 'graduate_id=3', '127.0.0.1', '2026-08-18 04:52:14'),
(13, 'Admin', 7, 'Orders', 'Approved order', 'order_id=1', '127.0.0.1', '2026-08-18 04:56:47'),
(14, 'Admin', 7, 'Colleges', 'Edited college', 'college_id=5', '127.0.0.1', '2026-08-18 05:08:35'),
(15, 'Admin', 7, 'Colleges', 'Edited college', 'college_id=4', '127.0.0.1', '2026-08-18 05:09:17'),
(16, 'Admin', 7, 'Colleges', 'Added course', 'BSA', '127.0.0.1', '2026-08-18 05:10:00'),
(17, 'Admin', 3, 'Dept Roster', 'Added alumni', '23-00016', '127.0.0.1', '2026-08-18 05:55:02'),
(18, 'Admin', 3, 'Dept Roster', 'Reset alumni password', 'graduate_id=7', '127.0.0.1', '2026-08-18 06:24:50'),
(19, 'Admin', 3, 'Dept Roster', 'Edited alumni', 'graduate_id=7', '127.0.0.1', '2026-08-18 06:24:57'),
(20, 'Admin', 3, 'Dept Roster', 'Deleted alumni', 'graduate_id=7', '127.0.0.1', '2026-08-18 06:25:44'),
(21, 'Admin', 3, 'Dept Roster', 'Added alumni', '23-00016', '127.0.0.1', '2026-08-18 06:26:03'),
(22, 'Admin', 3, 'Gallery', 'Created album', 'Alumni', '127.0.0.1', '2026-08-18 09:06:55'),
(23, 'Admin', 3, 'Gallery', 'Uploaded photo', '647154897_911058068580706_6003561828397689875_n.jpg', '127.0.0.1', '2026-08-18 09:07:30'),
(24, 'Admin', 3, 'Gallery', 'Uploaded photo', '647155380_911056778580835_5967460127044204666_n.jpg', '127.0.0.1', '2026-08-18 09:17:01'),
(25, 'Admin', 3, 'Dept Roster', 'Added alumni', '23-00017', '127.0.0.1', '2026-08-18 09:28:39'),
(26, 'Admin', 7, 'Gallery', 'Deleted image', 'image_id=1', '127.0.0.1', '2026-08-18 10:01:53'),
(27, 'Admin', 7, 'Gallery', 'Deleted image', 'image_id=2', '127.0.0.1', '2026-08-18 10:01:59'),
(28, 'Admin', 7, 'Gallery', 'Uploaded image', '647154897_911058068580706_6003561828397689875_n.jpg', '127.0.0.1', '2026-08-18 10:02:26'),
(29, 'Admin', 7, 'Gallery', 'Uploaded image', '648459795_911053385247841_739110307445739593_n.jpg', '127.0.0.1', '2026-08-18 10:02:47'),
(30, 'Admin', 7, 'Colleges', 'Added course', 'BSHM', '127.0.0.1', '2026-08-18 12:01:56'),
(31, 'Admin', 7, 'Colleges', 'Added course', 'BSA-Crop', '127.0.0.1', '2026-08-18 12:05:23'),
(32, 'Admin', 7, 'Colleges', 'Added course', 'BSOA', '127.0.0.1', '2026-08-18 12:05:57'),
(33, 'Admin', 7, 'Colleges', 'Added course', 'BSED-English', '127.0.0.1', '2026-08-18 12:06:37'),
(34, 'Admin', 7, 'Colleges', 'Added course', 'BSED-Filipino', '127.0.0.1', '2026-08-18 12:08:19'),
(35, 'Admin', 7, 'Colleges', 'Added course', 'BSED-Filipino', '127.0.0.1', '2026-08-18 12:09:50'),
(36, 'Admin', 7, 'Colleges', 'Added course', 'BSED-Mathematics', '127.0.0.1', '2026-08-18 12:10:17'),
(37, 'Admin', 7, 'Profile', 'Updated own profile', NULL, '127.0.0.1', '2026-08-18 15:05:12'),
(38, 'Admin', 7, 'Profile', 'Updated own profile', NULL, '127.0.0.1', '2026-08-19 02:08:11'),
(39, 'Admin', 6, 'Dept Roster', 'Added alumni', '23-00018', '127.0.0.1', '2026-08-19 02:41:03'),
(40, 'Admin', 3, 'Gallery', 'Uploaded photo', '646874275_911053575247822_8404313510399729435_n.jpg', '127.0.0.1', '2026-08-19 07:38:21'),
(41, 'Admin', 3, 'Gallery', 'Created album', 'ICT Week', '127.0.0.1', '2026-08-19 07:39:12'),
(42, 'Admin', 7, 'Colleges', 'Edited college', 'college_id=5', '127.0.0.1', '2026-08-19 08:48:37'),
(43, 'Admin', 7, 'Colleges', 'Edited college', 'college_id=5', '127.0.0.1', '2026-08-19 08:48:50'),
(44, 'Admin', 7, 'Gallery', 'Uploaded image', '641549370_903253012694545_8852839487955181671_n.jpg', '127.0.0.1', '2026-08-19 08:54:07'),
(45, 'Admin', 7, 'Profile', 'Updated own profile', NULL, '127.0.0.1', '2026-08-19 08:56:09'),
(46, 'Admin', 7, 'Profile', 'Updated own profile', NULL, '127.0.0.1', '2026-08-19 08:56:42'),
(47, 'Admin', 7, 'Settings', 'Updated system settings', NULL, '127.0.0.1', '2026-08-19 08:57:00'),
(48, 'Admin', 7, 'Settings', 'Updated system settings', NULL, '127.0.0.1', '2026-08-19 08:57:11'),
(49, 'Admin', 7, 'Profile', 'Updated own profile', NULL, '127.0.0.1', '2026-08-19 12:42:34'),
(50, 'Admin', 7, 'Profile', 'Updated own profile', NULL, '127.0.0.1', '2026-08-19 15:11:43'),
(51, 'Admin', 7, 'Profile', 'Updated own profile', NULL, '127.0.0.1', '2026-08-19 15:11:50'),
(52, 'Admin', 7, 'Settings', 'Updated system settings', NULL, '127.0.0.1', '2026-08-19 15:12:33'),
(53, 'Admin', 7, 'Colleges', 'Edited college', 'college_id=5', '127.0.0.1', '2026-08-19 15:37:49'),
(54, 'Admin', 7, 'Colleges', 'Edited college', 'college_id=4', '127.0.0.1', '2026-08-19 15:38:06'),
(55, 'Admin', 7, 'Colleges', 'Edited college', 'college_id=2', '127.0.0.1', '2026-08-19 15:38:19'),
(56, 'Admin', 7, 'Colleges', 'Edited college', 'college_id=3', '127.0.0.1', '2026-08-19 15:39:46'),
(57, 'Admin', 7, 'Gallery', 'Edited album', 'album_id=3', '127.0.0.1', '2026-08-19 16:02:02'),
(58, 'Admin', 7, 'Gallery', 'Toggled album status', 'album_id=1', '127.0.0.1', '2026-08-19 16:02:23'),
(59, 'Admin', 7, 'Gallery', 'Toggled album status', 'album_id=1', '127.0.0.1', '2026-08-19 16:03:23'),
(60, 'Admin', 7, 'Gallery', 'Edited album', 'album_id=1', '127.0.0.1', '2026-08-19 16:04:07'),
(61, 'Admin', 7, 'Gallery', 'Edited album', 'album_id=1', '127.0.0.1', '2026-08-19 16:05:14'),
(62, 'Admin', 3, 'Email Reminders', 'Sent reminders', 'batch=2010; template=Urgent; attempted=1; sent=0; failed=1', '127.0.0.1', '2026-08-19 16:59:48'),
(63, 'Admin', 3, 'Email Reminders', 'Sent reminders', 'batch=2010; template=Urgent; attempted=1; sent=0; failed=1', '127.0.0.1', '2026-08-19 16:59:50'),
(64, 'Admin', 3, 'Settings', 'Updated department admin profile', NULL, '127.0.0.1', '2026-08-19 17:13:02'),
(65, 'Admin', 3, 'Settings', 'Updated department logo', 'CICI', '127.0.0.1', '2026-08-19 17:13:27'),
(66, 'Admin', 3, 'Settings', 'Updated department admin profile', NULL, '127.0.0.1', '2026-08-19 17:13:35'),
(67, 'Admin', 3, 'Gallery', 'Uploaded photo', '642710206_903287936024386_4470919799434425829_n.jpg', '127.0.0.1', '2026-08-19 17:14:50'),
(68, 'Admin', 3, 'Email Reminders', 'Sent reminders', 'batch=2010; template=Urgent; attempted=1; sent=0; failed=1', '127.0.0.1', '2026-08-19 17:16:23'),
(69, 'Admin', 3, 'Email Reminders', 'Sent reminders', 'batch=2010; template=Urgent; attempted=1; sent=0; failed=1', '127.0.0.1', '2026-08-19 17:16:25'),
(70, 'Admin', 7, 'Admins', 'Added admin', 'wildie', '127.0.0.1', '2026-08-19 17:22:42'),
(71, 'Admin', 7, 'Admins', 'Toggled admin', 'admin_id=8', '127.0.0.1', '2026-08-19 17:23:18'),
(72, 'Admin', 7, 'Admins', 'Toggled admin', 'admin_id=8', '127.0.0.1', '2026-08-19 17:23:21'),
(73, 'Admin', 7, 'Admins', 'Deleted admin', 'admin_id=8', '127.0.0.1', '2026-08-19 17:23:24'),
(74, 'Admin', 7, 'Settings', 'Updated system settings', NULL, '127.0.0.1', '2026-08-19 17:33:17'),
(75, 'Admin', 3, 'Settings', 'Updated department admin profile', NULL, '127.0.0.1', '2026-08-19 17:52:27'),
(76, 'Admin', 3, 'Settings', 'Updated department admin profile', NULL, '127.0.0.1', '2026-08-19 17:52:55'),
(77, 'Admin', 3, 'Dept Roster', 'Deleted alumni', 'graduate_id=6', '127.0.0.1', '2026-08-20 02:57:33'),
(78, 'Admin', 3, 'Dept Roster', 'Deleted alumni', 'graduate_id=8', '127.0.0.1', '2026-08-20 02:57:40'),
(79, 'Admin', 3, 'Dept Roster', 'Imported alumni', 'Added: 1, Skipped: 0, Errors: 1', '127.0.0.1', '2026-08-20 02:59:51'),
(80, 'Admin', 3, 'Dept Roster', 'Deleted alumni', 'graduate_id=9', '127.0.0.1', '2026-08-20 02:59:58'),
(81, 'Admin', 3, 'Dept Roster', 'Deleted alumni', 'graduate_id=11', '127.0.0.1', '2026-08-20 03:00:03'),
(82, 'Admin', 3, 'Dept Roster', 'Imported alumni', 'Added: 1, Skipped: 0, Errors: 1', '127.0.0.1', '2026-08-20 03:01:47'),
(83, 'Admin', 3, 'Dept Roster', 'Deleted alumni', 'graduate_id=12', '127.0.0.1', '2026-08-20 03:07:55'),
(84, 'Admin', 3, 'Dept Roster', 'Added alumni', '2023-006', '127.0.0.1', '2026-08-20 03:08:30'),
(85, 'Admin', 3, 'Settings', 'Updated department admin profile', NULL, '127.0.0.1', '2026-08-20 04:02:08');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `fullname` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_picture` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'default.png',
  `status` enum('Active','Inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'Active',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `twofa_secret` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twofa_enabled` tinyint(1) DEFAULT '0',
  `college_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `role_id`, `fullname`, `username`, `password`, `email`, `contact_number`, `profile_picture`, `status`, `last_login`, `created_at`, `updated_at`, `twofa_secret`, `twofa_enabled`, `college_id`) VALUES
(3, 2, 'Dean', 'cici_admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cici@isufst.edu.ph', NULL, 'default.png', 'Active', '2026-08-20 12:01:53', '2026-08-12 16:34:28', '2026-08-20 04:02:08', NULL, 0, 3),
(4, 2, 'CBMSD Admin', 'cbmsd_admin', '$2y$10$fbUbctJsGsSaEQ7X3D6t1..UeWDNj46Lnuzbcn8ktpiWhoZ3FYiLK', 'cbmsd@isufst.edu.ph', NULL, 'default.png', 'Active', '2026-08-17 20:35:57', '2026-08-12 16:34:28', '2026-08-17 12:35:57', NULL, 0, 4),
(5, 2, 'COAG Admin', 'coag_admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'coag@isufst.edu.ph', NULL, 'default.png', 'Active', NULL, '2026-08-12 16:34:28', '2026-08-12 16:59:54', NULL, 0, 5),
(6, 2, 'COED Admin', 'coed_admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'coed@isufst.edu.ph', NULL, 'default.png', 'Active', '2026-08-19 10:40:05', '2026-08-12 16:34:28', '2026-08-19 02:40:05', NULL, 0, 2),
(7, 1, 'Wildie', 'admin', '$2y$10$pvYinnTFB.HRxuBDyh7ReuDjb6PIi02chFVOBeElT3TNMLz16Typ2', 'admin@tracegrad.edu.ph', '09273921019', 'admin_7_751bb57166.png', 'Active', '2026-08-20 10:41:30', '2026-08-12 17:30:09', '2026-08-20 02:41:30', NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `alumni_accounts`
--

CREATE TABLE `alumni_accounts` (
  `account_id` int(11) NOT NULL,
  `graduate_id` int(11) NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_status` enum('Active','Locked','Disabled') COLLATE utf8mb4_unicode_ci DEFAULT 'Active',
  `last_login` datetime DEFAULT NULL,
  `password_changed_at` datetime DEFAULT NULL,
  `remember_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `alumni_accounts`
--

INSERT INTO `alumni_accounts` (`account_id`, `graduate_id`, `password`, `account_status`, `last_login`, `password_changed_at`, `remember_token`, `created_at`, `updated_at`) VALUES
(6, 10, '$2y$10$J4tUZRU3ISEuI628sGclN.EUKX3RlfdJ8.dED7OnF/swHhnxXT7z2', 'Active', '2026-08-19 10:42:06', '2026-08-19 10:41:53', NULL, '2026-08-19 02:41:53', '2026-08-19 02:42:06'),
(7, 13, '$2y$10$/3md4OXmFzSlhKTooEkjJuHs4lBOHyLRvpWuEiQ7be31xXaOkNezq', 'Active', '2026-08-20 11:14:33', '2026-08-20 11:09:20', NULL, '2026-08-20 03:09:20', '2026-08-20 03:14:33');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `announcement_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `posted_by` int(11) NOT NULL,
  `publish_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `expiration_date` datetime DEFAULT NULL,
  `status` enum('Published','Draft','Archived') COLLATE utf8mb4_unicode_ci DEFAULT 'Published',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `announcement_reads`
--

CREATE TABLE `announcement_reads` (
  `read_id` int(11) NOT NULL,
  `announcement_id` int(11) NOT NULL,
  `graduate_id` int(11) NOT NULL,
  `read_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chatbot_faqs`
--

CREATE TABLE `chatbot_faqs` (
  `id` int(11) NOT NULL,
  `keywords` text NOT NULL,
  `answer` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `chatbot_faqs`
--

INSERT INTO `chatbot_faqs` (`id`, `keywords`, `answer`) VALUES
(1, 'survey, tracer, CHED, form', 'To complete your CHED Tracer Survey, please log in to your alumni dashboard using your Student ID as username. Once logged in, click on \"Tracer Survey\" in the left sidebar menu.'),
(2, 'login, password, forgot, account', 'Your login credentials (Student ID and temporary password) were sent to your personal email upon graduation. If you forgot your password, please click the \"Forgot Password\" link on the login page or contact your Department Admin directly.'),
(3, 'photo, gallery, buy, purchase, cart', 'You can purchase your graduation photos by logging into your alumni dashboard and going to the \"Photo Gallery\" tab. Select the photo you like, click \"Purchase\", and pay via GCash or Bank Transfer.');

-- --------------------------------------------------------

--
-- Table structure for table `colleges`
--

CREATE TABLE `colleges` (
  `college_id` int(11) NOT NULL,
  `college_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `college_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dean` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `office_email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `college_logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Active','Inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `colleges`
--

INSERT INTO `colleges` (`college_id`, `college_code`, `college_name`, `dean`, `office_email`, `college_logo`, `status`, `created_at`) VALUES
(2, 'COED', 'College of Education', 'Dr. Rene Estomo', 'coed@isufst.edu.ph', 'college_20260819173819_2b4133b87b.png', 'Active', '2026-08-12 15:21:26'),
(3, 'CICI', 'College of Informatics and Computing Innovation', 'Dr. Wenda D. Panes', 'cici@isufst.edu.ph', 'college_d8d59f2628913de7c968.png', 'Active', '2026-08-12 16:34:28'),
(4, 'CBMSD', 'College of Business and Sustainable Development', 'Dr. Janice H. Ching', 'cbmsd@isufst.edu.ph', 'college_20260819173806_259d1e8e25.png', 'Active', '2026-08-12 16:34:28'),
(5, 'COAG', 'College of Agriculture', 'Dr. Noli L. Gerona', 'coag@isufst.edu.ph', 'college_20260819173749_bbce06fa36.png', 'Active', '2026-08-12 16:34:28');

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `company_id` int(11) NOT NULL,
  `company_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `industry` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_address` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `province` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'Philippines',
  `contact_person` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Active','Inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`company_id`, `company_name`, `industry`, `company_address`, `city`, `province`, `country`, `contact_person`, `contact_email`, `contact_number`, `website`, `status`, `created_at`) VALUES
(1, 'ISUFST', 'Education', NULL, 'San Enrique', 'Iloilo', 'Philippines', NULL, NULL, NULL, NULL, 'Active', '2026-08-03 07:25:10');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_id` int(11) NOT NULL,
  `college_id` int(11) NOT NULL,
  `course_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `course_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `course_major` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Active','Inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `college_id`, `course_code`, `course_name`, `course_major`, `status`, `created_at`) VALUES
(1, 3, 'BSIT', 'Bachelor of Science in Information Technology', NULL, 'Active', '2026-08-03 07:25:09'),
(3, 2, 'BEED', 'Bachelor of Elementary Education', NULL, 'Active', '2026-08-12 15:22:22'),
(4, 5, 'BSA', 'Bachelor of Science in Agriculture', 'Animal Science', 'Active', '2026-08-18 05:10:00'),
(5, 4, 'BSHM', 'Bachelor of Science in Hospitality Management', NULL, 'Active', '2026-08-18 12:01:56'),
(7, 5, 'BSA-Crop', 'Bachelor of Science in Agriculture', 'Crop Science', 'Active', '2026-08-18 12:05:23'),
(8, 4, 'BSOA', 'Bachelor of Science in Office Administration', NULL, 'Active', '2026-08-18 12:05:57'),
(9, 2, 'BSED-English', 'Bachelor of Secondary Education', 'English', 'Active', '2026-08-18 12:06:37'),
(11, 2, 'BSED-Filipino', 'Bachelor of Secondary Education', 'Filipino', 'Active', '2026-08-18 12:09:50'),
(12, 2, 'BSED-Mathematics', 'Bachelor of Secondary Education', 'Mathematics', 'Active', '2026-08-18 12:10:17');

-- --------------------------------------------------------

--
-- Table structure for table `employment`
--

CREATE TABLE `employment` (
  `employment_id` int(11) NOT NULL,
  `graduate_id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `employer_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `industry` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `work_region` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employment_status` enum('Employed','Unemployed','Self-Employed','Freelancer','Continuing Studies') COLLATE utf8mb4_unicode_ci NOT NULL,
  `employment_sector` enum('Government','Private','Non-Government Organization','Self-Employed','International') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employment_type` enum('Permanent','Regular','Probationary','Contractual','Temporary','Part-Time','Casual') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position_title` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `monthly_salary` decimal(12,2) DEFAULT NULL,
  `work_location` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_job` tinyint(1) DEFAULT '0',
  `job_related_to_course` enum('Highly Related','Related','Not Related') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_hired` date DEFAULT NULL,
  `currently_employed` tinyint(1) DEFAULT '1',
  `reason_if_unemployed` text COLLATE utf8mb4_unicode_ci,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employment`
--

INSERT INTO `employment` (`employment_id`, `graduate_id`, `company_id`, `employer_name`, `industry`, `work_region`, `employment_status`, `employment_sector`, `employment_type`, `position_title`, `department`, `monthly_salary`, `work_location`, `first_job`, `job_related_to_course`, `date_hired`, `currently_employed`, `reason_if_unemployed`, `remarks`, `created_at`, `updated_at`) VALUES
(4, 13, NULL, 'Ps Company', 'IT / Technology', 'Region VI – Western Visayas', 'Employed', NULL, NULL, 'IT Professional', NULL, '20000.00', NULL, 0, 'Highly Related', NULL, 1, NULL, NULL, '2026-08-20 03:12:39', '2026-08-20 03:12:39');

-- --------------------------------------------------------

--
-- Table structure for table `employment_history`
--

CREATE TABLE `employment_history` (
  `history_id` int(11) NOT NULL,
  `graduate_id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `company_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position_title` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employment_sector` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `employment_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `monthly_salary` decimal(12,2) DEFAULT NULL,
  `date_started` date DEFAULT NULL,
  `date_ended` date DEFAULT NULL,
  `reason_for_leaving` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gallery_albums`
--

CREATE TABLE `gallery_albums` (
  `album_id` int(11) NOT NULL,
  `album_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `cover_image_id` int(11) DEFAULT NULL,
  `cover_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `status` enum('Active','Hidden') COLLATE utf8mb4_unicode_ci DEFAULT 'Active',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gallery_albums`
--

INSERT INTO `gallery_albums` (`album_id`, `album_name`, `description`, `cover_image_id`, `cover_photo`, `event_date`, `status`, `created_by`, `created_at`) VALUES
(1, 'Graduates', 'Latin Honor Graduates', NULL, NULL, '2026-02-27', 'Active', 7, '2026-08-12 22:43:41'),
(2, 'Alumni', 'PAghanduraw', NULL, NULL, '2026-08-18', 'Active', 3, '2026-08-18 09:06:55'),
(3, 'ICT Week', 'CCS Annual Celebration', NULL, NULL, '2026-08-18', 'Active', 3, '2026-08-19 07:39:12');

-- --------------------------------------------------------

--
-- Table structure for table `gallery_images`
--

CREATE TABLE `gallery_images` (
  `image_id` int(11) NOT NULL,
  `album_id` int(11) NOT NULL,
  `bundle_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_filename` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image_size` bigint(20) DEFAULT NULL,
  `image_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `watermark_enabled` tinyint(1) DEFAULT '1',
  `download_price` decimal(10,2) DEFAULT '0.00',
  `status` enum('Available','Hidden') COLLATE utf8mb4_unicode_ci DEFAULT 'Available',
  `uploaded_by` int(11) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gallery_images`
--

INSERT INTO `gallery_images` (`image_id`, `album_id`, `bundle_name`, `filename`, `original_filename`, `title`, `description`, `image_size`, `image_type`, `watermark_enabled`, `download_price`, `status`, `uploaded_by`, `uploaded_at`) VALUES
(3, 2, NULL, 'gal_6ba62e0cefe2b3ebc2e2c761.jpg', '647155380_911056778580835_5967460127044204666_n.jpg', 'Sir Tumoy', 'HEhe', 258055, 'image/jpeg', 1, '10.00', 'Available', 3, '2026-08-18 09:17:01'),
(4, 2, NULL, 'gal_6a842db2e146f.jpg', '647154897_911058068580706_6003561828397689875_n.jpg', 'Batch 2010', NULL, 325971, 'image/jpeg', 1, '20.00', 'Available', 7, '2026-08-18 10:02:26'),
(5, 2, NULL, 'gal_6a842dc749c1c.jpg', '648459795_911053385247841_739110307445739593_n.jpg', 'Batch 2009', NULL, 225225, 'image/jpeg', 1, '20.00', 'Available', 7, '2026-08-18 10:02:47'),
(6, 2, NULL, 'gal_103388b3d8fe127dd0ac02cf.jpg', '646874275_911053575247822_8404313510399729435_n.jpg', 'BAtch 2005', NULL, 191430, 'image/jpeg', 1, '20.00', 'Available', 3, '2026-08-19 07:38:21'),
(7, 2, NULL, 'gal_6a856f2f41e8d.jpg', '641549370_903253012694545_8852839487955181671_n.jpg', 'Speaker', NULL, 148383, 'image/jpeg', 1, '100.00', 'Available', 7, '2026-08-19 08:54:07'),
(8, 3, NULL, 'gal_49841dfb0e39641e32f2c2c7.jpg', '642710206_903287936024386_4470919799434425829_n.jpg', 'ICT WEEK', NULL, 142820, 'image/jpeg', 0, '20.00', 'Available', 3, '2026-08-19 17:14:50');

-- --------------------------------------------------------

--
-- Table structure for table `gallery_orders`
--

CREATE TABLE `gallery_orders` (
  `order_id` int(11) NOT NULL,
  `graduate_id` int(11) NOT NULL,
  `image_id` int(11) NOT NULL,
  `order_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `amount` decimal(10,2) NOT NULL,
  `order_status` enum('Pending','Paid','Cancelled','Completed') COLLATE utf8mb4_unicode_ci DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `graduates`
--

CREATE TABLE `graduates` (
  `graduate_id` int(11) NOT NULL,
  `student_id` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastname` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `firstname` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `middlename` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `suffix` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sex` enum('Male','Female') COLLATE utf8mb4_unicode_ci NOT NULL,
  `birthday` date DEFAULT NULL,
  `civil_status` enum('Single','Married','Widowed','Separated') COLLATE utf8mb4_unicode_ci DEFAULT 'Single',
  `student_email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `personal_email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telephone_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_address` text COLLATE utf8mb4_unicode_ci,
  `permanent_address` text COLLATE utf8mb4_unicode_ci,
  `course_id` int(11) NOT NULL,
  `batch_year` year(4) NOT NULL,
  `graduation_date` date DEFAULT NULL,
  `latin_honor` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `workplace_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_id_proof` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_picture` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'default.png',
  `is_verified` tinyint(1) DEFAULT '0',
  `account_activation` enum('Not Activated','Activated') COLLATE utf8mb4_unicode_ci DEFAULT 'Not Activated',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `graduates`
--

INSERT INTO `graduates` (`graduate_id`, `student_id`, `lastname`, `firstname`, `middlename`, `suffix`, `sex`, `birthday`, `civil_status`, `student_email`, `personal_email`, `mobile_number`, `telephone_number`, `current_address`, `permanent_address`, `course_id`, `batch_year`, `graduation_date`, `latin_honor`, `workplace_photo`, `company_id_proof`, `profile_picture`, `is_verified`, `account_activation`, `created_by`, `created_at`, `updated_at`) VALUES
(10, '23-00018', 'Ruiz', 'Erickson', 'Rivera', 'Jr.', 'Male', NULL, 'Single', NULL, 'wildiepescador@gmail.com', '09273921019', NULL, NULL, NULL, 3, 2005, NULL, 'M', NULL, NULL, 'default.png', 0, 'Activated', 6, '2026-08-19 02:41:03', '2026-08-19 02:41:53'),
(13, '2023-006', 'Dela Cruz', 'Juan', 'Ruiz', NULL, 'Male', '2018-02-20', 'Single', NULL, 'juandelacruz@gmail.com', '09273921019', '09273921019', 'San Enrique Iloilo', 'San Enrique, Iloilo', 1, 2024, NULL, NULL, 'wp_13_061dd128a5541322e47d8bd1.png', 'ci_13_c0773de42c9fa794bde05382.jpg', 'profile_13_368a269d58f191e687fa7368.jpg', 0, 'Activated', 3, '2026-08-20 03:08:30', '2026-08-20 03:15:15');

-- --------------------------------------------------------

--
-- Table structure for table `graduate_import_logs`
--

CREATE TABLE `graduate_import_logs` (
  `import_id` int(11) NOT NULL,
  `imported_by` int(11) NOT NULL,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_records` int(11) DEFAULT '0',
  `successful_imports` int(11) DEFAULT '0',
  `failed_imports` int(11) DEFAULT '0',
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `imported_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `login_id` int(11) NOT NULL,
  `user_type` enum('Admin','Alumni') COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `login_status` enum('Success','Failed') COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `attempted_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`login_id`, `user_type`, `username`, `login_status`, `ip_address`, `user_agent`, `attempted_at`) VALUES
(1, 'Admin', 'admin', 'Failed', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-03 08:01:19'),
(2, 'Alumni', '2024-00142', 'Failed', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-03 08:12:21'),
(3, 'Admin', 'admin', 'Failed', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-03 08:12:44'),
(4, 'Admin', 'admin', 'Failed', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-12 13:34:40'),
(5, 'Admin', 'admin', 'Failed', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-12 13:34:48'),
(6, 'Admin', 'admin', 'Failed', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-12 13:40:23'),
(7, 'Admin', 'admin', 'Failed', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-12 13:40:31'),
(8, 'Admin', 'admin', 'Failed', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-12 13:42:26'),
(9, 'Admin', 'admin', 'Failed', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-12 13:55:23'),
(10, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-12 14:53:52'),
(11, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-12 15:14:14'),
(12, 'Admin', 'admin', 'Failed', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-12 15:52:22'),
(13, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-12 15:52:28'),
(14, 'Alumni', '23-00016', 'Failed', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-12 16:05:16'),
(15, 'Admin', 'admin', 'Failed', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-12 16:22:26'),
(16, 'Admin', 'admin', 'Failed', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-12 16:22:34'),
(17, 'Admin', 'admin', 'Failed', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-12 16:37:07'),
(18, 'Admin', 'admin', 'Failed', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-12 16:38:19'),
(19, 'Admin', 'admin', 'Failed', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-12 16:38:28'),
(20, 'Admin', 'cici_admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-12 16:40:39'),
(21, 'Admin', 'coed_admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-12 16:41:25'),
(22, 'Admin', 'cici_admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-12 16:50:32'),
(23, 'Admin', 'admin', 'Failed', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-12 16:54:59'),
(24, 'Admin', 'cici_admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-12 16:55:10'),
(25, 'Admin', 'cici_admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-12 17:01:34'),
(26, 'Admin', 'admin', 'Failed', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-12 17:15:16'),
(27, 'Admin', 'admin', 'Failed', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-12 17:15:24'),
(28, 'Admin', 'admin', 'Failed', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-12 17:17:48'),
(29, 'Admin', 'admin', 'Failed', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-12 17:17:58'),
(30, 'Admin', 'admin', 'Failed', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-12 17:23:10'),
(31, 'Admin', 'admin', 'Failed', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-12 17:25:40'),
(32, 'Admin', 'cici_admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-12 17:25:51'),
(33, 'Admin', 'admin', 'Failed', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-12 17:26:12'),
(34, 'Admin', 'admin', 'Failed', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-12 17:27:42'),
(35, 'Admin', 'admin', 'Failed', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-12 17:27:51'),
(36, 'Admin', 'admin', 'Failed', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-12 17:31:51'),
(37, 'Admin', 'admin', 'Failed', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-12 17:31:57'),
(38, 'Admin', 'admin', 'Failed', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-12 21:25:23'),
(39, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-12 21:35:18'),
(40, 'Admin', 'cici_admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-12 22:17:26'),
(41, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-12 22:37:45'),
(42, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', '2026-08-12 23:00:42'),
(43, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-17 12:34:36'),
(44, 'Admin', 'cbmsd_admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-17 12:35:57'),
(45, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-17 15:08:26'),
(46, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-17 16:02:17'),
(47, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-17 16:54:03'),
(48, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-17 17:00:04'),
(49, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-17 17:07:16'),
(50, 'Alumni', '2024-00142', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-17 18:11:00'),
(51, 'Alumni', '2024-00142', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-17 18:17:29'),
(52, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 04:51:49'),
(53, 'Alumni', '2024-00142', 'Failed', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 04:53:14'),
(54, 'Alumni', '2024-00142', 'Failed', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 04:53:55'),
(55, 'Alumni', '2024-00142', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 04:54:01'),
(56, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 04:56:37'),
(57, 'Admin', 'cici_admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 05:12:00'),
(58, 'Admin', 'coed_admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 05:12:28'),
(59, 'Admin', 'cici_admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 05:54:31'),
(60, 'Alumni', '23-00016', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 05:55:55'),
(61, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 06:19:44'),
(62, 'Alumni', '23-00016', 'Failed', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 06:21:22'),
(63, 'Alumni', '23-00016', 'Failed', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 06:21:36'),
(64, 'Alumni', '23-00016', 'Failed', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 06:22:09'),
(65, 'Alumni', '23-00016', 'Failed', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 06:22:15'),
(66, 'Alumni', '2024-00142', 'Failed', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 06:23:21'),
(67, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 06:23:36'),
(68, 'Admin', 'cici_admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 06:24:42'),
(69, 'Admin', 'cici_admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 06:25:33'),
(70, 'Alumni', '23-00016', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 06:26:41'),
(71, 'Alumni', '23-00016', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 06:27:32'),
(72, 'Alumni', '23-00016', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 06:49:02'),
(73, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 07:01:18'),
(74, 'Alumni', '23-00016', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 07:59:59'),
(75, 'Admin', 'cici_admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 08:00:34'),
(76, 'Alumni', '23-00016', 'Failed', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 08:01:48'),
(77, 'Alumni', '23-00016', 'Failed', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 08:01:55'),
(78, 'Alumni', '23-00016', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 08:02:03'),
(79, 'Admin', 'cici_admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 08:04:51'),
(80, 'Admin', 'cici_admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 08:11:24'),
(81, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 08:24:13'),
(82, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 08:24:54'),
(83, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 08:48:45'),
(84, 'Admin', 'cici_admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 08:53:19'),
(85, 'Admin', 'cici_admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 09:07:56'),
(86, 'Alumni', '23-00017', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 09:30:40'),
(87, 'Admin', 'cici_admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 09:30:57'),
(88, 'Alumni', '23-00016', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 09:34:58'),
(89, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 09:36:35'),
(90, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 11:30:16'),
(91, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 12:01:13'),
(92, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 12:41:44'),
(93, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 12:44:07'),
(94, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 14:22:58'),
(95, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 14:47:22'),
(96, 'Admin', 'cici_admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 14:51:05'),
(97, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 15:04:27'),
(98, 'Alumni', '23-00016', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 15:31:18'),
(99, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-18 15:36:36'),
(100, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 02:07:28'),
(101, 'Admin', 'cici_admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 02:08:43'),
(102, 'Alumni', '23-00016', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 02:22:55'),
(103, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 02:32:06'),
(104, 'Admin', 'cbmsd_admin', 'Failed', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 02:39:56'),
(105, 'Admin', 'coed_admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 02:40:05'),
(106, 'Alumni', '23-00018', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 02:42:06'),
(107, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 03:13:07'),
(108, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 06:06:57'),
(109, 'Admin', 'cici_admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 06:07:25'),
(110, 'Admin', 'cici_admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 06:27:13'),
(111, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 07:31:03'),
(112, 'Admin', 'cici_admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 07:31:51'),
(113, 'Admin', 'cici_admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 07:37:19'),
(114, 'Alumni', '23-00016', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 07:58:03'),
(115, 'Alumni', '23-00016', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 08:20:50'),
(116, 'Alumni', '23-00016', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 08:32:37'),
(117, 'Admin', 'cici_admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 08:34:34'),
(118, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 08:44:57'),
(119, 'Admin', 'cici_admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 08:59:48'),
(120, 'Alumni', '23-00016', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 09:05:05'),
(121, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 12:03:24'),
(122, 'Alumni', '23-00016', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 12:11:01'),
(123, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 12:15:03'),
(124, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 12:31:44'),
(125, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 13:43:26'),
(126, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 13:52:30'),
(127, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 14:09:15'),
(128, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 14:15:07'),
(129, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 14:17:04'),
(130, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 14:32:18'),
(131, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 15:24:15'),
(132, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 15:47:06'),
(133, 'Admin', 'cici_admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 16:16:59'),
(134, 'Admin', 'cici_admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 17:15:15'),
(135, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 17:21:31'),
(136, 'Admin', 'cici_admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 17:39:12'),
(137, 'Alumni', '23-00016', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 17:58:44'),
(138, 'Admin', 'cici_admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 18:41:37'),
(139, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 18:41:59'),
(140, 'Alumni', '23-00016', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 18:43:04'),
(141, 'Alumni', '23-00016', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 18:44:31'),
(142, 'Alumni', '23-00016', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-19 19:29:56'),
(143, 'Admin', 'admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-20 02:41:30'),
(144, 'Admin', 'cici_admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-20 02:51:09'),
(145, 'Admin', 'cici_admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-20 03:05:18'),
(146, 'Admin', 'cici_admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-20 03:07:47'),
(147, 'Alumni', '2023-006', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-20 03:09:37'),
(148, 'Admin', 'cici_admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-20 03:12:57'),
(149, 'Alumni', '2023-006', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-20 03:14:33'),
(150, 'Admin', 'cici_admin', 'Success', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', '2026-08-20 04:01:53');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `graduate_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `notification_type` enum('Announcement','Survey','Gallery','Account','System') COLLATE utf8mb4_unicode_ci DEFAULT 'System',
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `reset_id` int(11) NOT NULL,
  `graduate_id` int(11) NOT NULL,
  `reset_token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` datetime NOT NULL,
  `is_used` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_reference` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` enum('GCash','Cash','Bank Transfer') COLLATE utf8mb4_unicode_ci DEFAULT 'GCash',
  `amount_paid` decimal(10,2) NOT NULL,
  `proof_of_payment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_status` enum('Pending','Verified','Rejected') COLLATE utf8mb4_unicode_ci DEFAULT 'Pending',
  `verified_by` int(11) DEFAULT NULL,
  `payment_date` datetime DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `remarks` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`, `description`, `created_at`) VALUES
(1, 'Super Admin', 'Full system access', '2026-08-03 07:25:09'),
(2, 'Dept Admin', 'Graduate management', '2026-08-03 07:25:09');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `setting_id` int(11) NOT NULL,
  `setting_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_unicode_ci,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`setting_id`, `setting_key`, `setting_value`, `description`, `updated_at`) VALUES
(1, 'system_name', 'TRACEGRAD', 'System Name', '2026-08-19 08:57:11'),
(2, 'campus_name', 'ISUFST - San Enrique Campus', 'Campus', '2026-08-03 07:25:09'),
(3, 'academic_year', '2026', 'Current Academic Year', '2026-08-03 07:25:09'),
(4, 'allow_gallery_payment', '1', 'Enable Gallery Payment', '2026-08-03 07:25:09'),
(5, 'allow_profile_update', '1', 'Allow Alumni Profile Update', '2026-08-03 07:25:09'),
(6, 'system_version', '1.0', 'Current Version', '2026-08-03 07:25:09'),
(7, 'force_2fa', '0', 'Force Two-Factor Authentication for all admins', '2026-08-12 15:48:13'),
(14, 'watermark_text', '© TRACEGRAD', 'Gallery Watermark Text', '2026-08-19 15:12:33');

-- --------------------------------------------------------

--
-- Table structure for table `survey_answers`
--

CREATE TABLE `survey_answers` (
  `answer_id` int(11) NOT NULL,
  `graduate_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `option_id` int(11) DEFAULT NULL,
  `answer_text` text COLLATE utf8mb4_unicode_ci,
  `answered_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `survey_answers`
--

INSERT INTO `survey_answers` (`answer_id`, `graduate_id`, `question_id`, `option_id`, `answer_text`, `answered_at`) VALUES
(24, 13, 1, NULL, '4', '2026-08-20 03:12:39'),
(25, 13, 5, NULL, 'I found my graduation photos in the gallery and downloaded the HD copy in minutes — proceeds going to the alumni fund is a nice touch.', '2026-08-20 03:12:39'),
(26, 13, 2, NULL, '5', '2026-08-20 03:12:39'),
(27, 13, 6, NULL, 'Very Satisfied', '2026-08-20 03:12:39'),
(28, 13, 3, NULL, '4', '2026-08-20 03:12:39'),
(29, 13, 4, NULL, '5', '2026-08-20 03:12:39');

-- --------------------------------------------------------

--
-- Table structure for table `survey_categories`
--

CREATE TABLE `survey_categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `display_order` int(11) DEFAULT '1',
  `status` enum('Active','Inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `survey_categories`
--

INSERT INTO `survey_categories` (`category_id`, `category_name`, `description`, `display_order`, `status`, `created_at`) VALUES
(1, 'Personal Information', NULL, 1, 'Active', '2026-08-03 07:25:09'),
(2, 'Educational Background', NULL, 2, 'Active', '2026-08-03 07:25:09'),
(3, 'Employment Information', NULL, 3, 'Active', '2026-08-03 07:25:09'),
(4, 'Job Relevance', NULL, 4, 'Active', '2026-08-03 07:25:09'),
(5, 'Skills and Competencies', NULL, 5, 'Active', '2026-08-03 07:25:09'),
(6, 'Graduate Feedback', NULL, 6, 'Active', '2026-08-03 07:25:09');

-- --------------------------------------------------------

--
-- Table structure for table `survey_questions`
--

CREATE TABLE `survey_questions` (
  `question_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `question` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `question_type` enum('Text','Textarea','Radio','Checkbox','Dropdown','Number','Date') COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_required` tinyint(1) DEFAULT '1',
  `display_order` int(11) DEFAULT '1',
  `status` enum('Active','Inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `survey_questions`
--

INSERT INTO `survey_questions` (`question_id`, `category_id`, `question`, `question_type`, `is_required`, `display_order`, `status`, `created_at`) VALUES
(1, 5, 'Communication Skills', 'Radio', 0, 1, 'Active', '2026-08-18 06:06:13'),
(2, 5, 'Technical / Professional Skills', 'Radio', 0, 2, 'Active', '2026-08-18 06:06:13'),
(3, 5, 'Critical Thinking & Problem Solving', 'Radio', 0, 3, 'Active', '2026-08-18 06:06:13'),
(4, 5, 'Work Ethics & Professionalism', 'Radio', 0, 4, 'Active', '2026-08-18 06:06:13'),
(5, 6, 'Your Testimony', 'Textarea', 0, 1, 'Active', '2026-08-18 06:06:13'),
(6, 6, 'Overall Satisfaction', 'Dropdown', 0, 2, 'Active', '2026-08-18 06:06:13'),
(7, 6, 'Curriculum Improvement Suggestions', 'Textarea', 0, 3, 'Active', '2026-08-18 06:06:13');

-- --------------------------------------------------------

--
-- Table structure for table `survey_question_options`
--

CREATE TABLE `survey_question_options` (
  `option_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `option_text` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `option_value` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_order` int(11) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `survey_submissions`
--

CREATE TABLE `survey_submissions` (
  `submission_id` int(10) UNSIGNED NOT NULL,
  `graduate_id` int(11) NOT NULL,
  `status` enum('Draft','Submitted') NOT NULL DEFAULT 'Draft',
  `started_at` datetime DEFAULT NULL,
  `last_saved_at` datetime DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `survey_submissions`
--

INSERT INTO `survey_submissions` (`submission_id`, `graduate_id`, `status`, `started_at`, `last_saved_at`, `submitted_at`) VALUES
(3, 13, 'Submitted', '2026-08-20 11:12:39', '2026-08-20 11:12:39', '2026-08-20 11:12:39');

-- --------------------------------------------------------

--
-- Table structure for table `system_logs`
--

CREATE TABLE `system_logs` (
  `log_id` int(11) NOT NULL,
  `log_level` enum('INFO','WARNING','ERROR','CRITICAL') COLLATE utf8mb4_unicode_ci DEFAULT 'INFO',
  `module` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `line_number` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_alumni_survey_status`
-- (See below for the actual view)
--
CREATE TABLE `v_alumni_survey_status` (
`graduate_id` int(11)
,`answers_count` bigint(21)
,`question_total` bigint(21)
,`submission_status` enum('Draft','Submitted')
,`survey_status` varchar(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_survey_answer_counts`
-- (See below for the actual view)
--
CREATE TABLE `v_survey_answer_counts` (
`graduate_id` int(11)
,`answers_count` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_survey_question_total`
-- (See below for the actual view)
--
CREATE TABLE `v_survey_question_total` (
`question_total` bigint(21)
);

-- --------------------------------------------------------

--
-- Structure for view `v_alumni_survey_status`
--
DROP TABLE IF EXISTS `v_alumni_survey_status`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_alumni_survey_status`  AS  select `g`.`graduate_id` AS `graduate_id`,coalesce(`ac`.`answers_count`,0) AS `answers_count`,coalesce(`qt`.`question_total`,0) AS `question_total`,`ss`.`status` AS `submission_status`,(case when (`ss`.`status` = 'Submitted') then 'Completed' when (`ss`.`status` = 'Draft') then 'Partial' when (coalesce(`ac`.`answers_count`,0) = 0) then 'Not Started' when ((coalesce(`qt`.`question_total`,0) > 0) and (coalesce(`ac`.`answers_count`,0) >= `qt`.`question_total`)) then 'Completed' else 'Partial' end) AS `survey_status` from (((`graduates` `g` left join `v_survey_answer_counts` `ac` on((`ac`.`graduate_id` = `g`.`graduate_id`))) left join `survey_submissions` `ss` on((`ss`.`graduate_id` = `g`.`graduate_id`))) join `v_survey_question_total` `qt`) ;

-- --------------------------------------------------------

--
-- Structure for view `v_survey_answer_counts`
--
DROP TABLE IF EXISTS `v_survey_answer_counts`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_survey_answer_counts`  AS  select `survey_answers`.`graduate_id` AS `graduate_id`,count(distinct `survey_answers`.`question_id`) AS `answers_count` from `survey_answers` group by `survey_answers`.`graduate_id` ;

-- --------------------------------------------------------

--
-- Structure for view `v_survey_question_total`
--
DROP TABLE IF EXISTS `v_survey_question_total`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_survey_question_total`  AS  select count(0) AS `question_total` from `survey_questions` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`activity_id`),
  ADD KEY `idx_activity_user` (`user_type`,`user_id`),
  ADD KEY `idx_activity_module` (`module`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fk_admin_role` (`role_id`),
  ADD KEY `college_id` (`college_id`);

--
-- Indexes for table `alumni_accounts`
--
ALTER TABLE `alumni_accounts`
  ADD PRIMARY KEY (`account_id`),
  ADD UNIQUE KEY `graduate_id` (`graduate_id`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`announcement_id`),
  ADD KEY `fk_announcement_admin` (`posted_by`);

--
-- Indexes for table `announcement_reads`
--
ALTER TABLE `announcement_reads`
  ADD PRIMARY KEY (`read_id`),
  ADD KEY `fk_read_announcement` (`announcement_id`),
  ADD KEY `fk_read_graduate` (`graduate_id`);

--
-- Indexes for table `chatbot_faqs`
--
ALTER TABLE `chatbot_faqs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `colleges`
--
ALTER TABLE `colleges`
  ADD PRIMARY KEY (`college_id`),
  ADD UNIQUE KEY `college_code` (`college_code`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`company_id`),
  ADD KEY `idx_company_name` (`company_name`(191));

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`),
  ADD UNIQUE KEY `course_code` (`course_code`),
  ADD KEY `fk_course_college` (`college_id`);

--
-- Indexes for table `employment`
--
ALTER TABLE `employment`
  ADD PRIMARY KEY (`employment_id`),
  ADD UNIQUE KEY `graduate_id` (`graduate_id`),
  ADD KEY `idx_employment_status` (`employment_status`),
  ADD KEY `idx_employment_sector` (`employment_sector`),
  ADD KEY `idx_company` (`company_id`);

--
-- Indexes for table `employment_history`
--
ALTER TABLE `employment_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `idx_graduate_history` (`graduate_id`),
  ADD KEY `idx_history_company` (`company_id`);

--
-- Indexes for table `gallery_albums`
--
ALTER TABLE `gallery_albums`
  ADD PRIMARY KEY (`album_id`),
  ADD KEY `fk_album_admin` (`created_by`);

--
-- Indexes for table `gallery_images`
--
ALTER TABLE `gallery_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `fk_image_admin` (`uploaded_by`),
  ADD KEY `idx_album` (`album_id`),
  ADD KEY `idx_gallery_album_bundle` (`album_id`,`bundle_name`);

--
-- Indexes for table `gallery_orders`
--
ALTER TABLE `gallery_orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `idx_order_graduate` (`graduate_id`),
  ADD KEY `idx_order_image` (`image_id`);

--
-- Indexes for table `graduates`
--
ALTER TABLE `graduates`
  ADD PRIMARY KEY (`graduate_id`),
  ADD UNIQUE KEY `student_id` (`student_id`),
  ADD UNIQUE KEY `idx_student_id` (`student_id`),
  ADD KEY `fk_graduate_admin` (`created_by`),
  ADD KEY `idx_lastname` (`lastname`),
  ADD KEY `idx_firstname` (`firstname`),
  ADD KEY `idx_course` (`course_id`),
  ADD KEY `idx_batch_year` (`batch_year`),
  ADD KEY `idx_activation` (`account_activation`);

--
-- Indexes for table `graduate_import_logs`
--
ALTER TABLE `graduate_import_logs`
  ADD PRIMARY KEY (`import_id`),
  ADD KEY `idx_import_admin` (`imported_by`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`login_id`),
  ADD KEY `idx_login_username` (`username`),
  ADD KEY `idx_login_status` (`login_status`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `fk_notification_graduate` (`graduate_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`reset_id`),
  ADD KEY `fk_password_reset_graduate` (`graduate_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `fk_payment_admin` (`verified_by`),
  ADD KEY `idx_payment_status` (`payment_status`),
  ADD KEY `idx_payment_order` (`order_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`setting_id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `survey_answers`
--
ALTER TABLE `survey_answers`
  ADD PRIMARY KEY (`answer_id`),
  ADD UNIQUE KEY `uniq_graduate_question` (`graduate_id`,`question_id`),
  ADD UNIQUE KEY `uq_survey_answer_graduate_question` (`graduate_id`,`question_id`),
  ADD KEY `fk_answer_option` (`option_id`),
  ADD KEY `idx_answer_graduate` (`graduate_id`),
  ADD KEY `idx_answer_question` (`question_id`);

--
-- Indexes for table `survey_categories`
--
ALTER TABLE `survey_categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `survey_questions`
--
ALTER TABLE `survey_questions`
  ADD PRIMARY KEY (`question_id`),
  ADD KEY `idx_category` (`category_id`);

--
-- Indexes for table `survey_question_options`
--
ALTER TABLE `survey_question_options`
  ADD PRIMARY KEY (`option_id`),
  ADD KEY `idx_question` (`question_id`);

--
-- Indexes for table `survey_submissions`
--
ALTER TABLE `survey_submissions`
  ADD PRIMARY KEY (`submission_id`),
  ADD UNIQUE KEY `uq_survey_submission_graduate` (`graduate_id`),
  ADD KEY `idx_survey_submission_status` (`status`);

--
-- Indexes for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_system_level` (`log_level`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `alumni_accounts`
--
ALTER TABLE `alumni_accounts`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `announcement_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `announcement_reads`
--
ALTER TABLE `announcement_reads`
  MODIFY `read_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chatbot_faqs`
--
ALTER TABLE `chatbot_faqs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `colleges`
--
ALTER TABLE `colleges`
  MODIFY `college_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `employment`
--
ALTER TABLE `employment`
  MODIFY `employment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `employment_history`
--
ALTER TABLE `employment_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gallery_albums`
--
ALTER TABLE `gallery_albums`
  MODIFY `album_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `gallery_images`
--
ALTER TABLE `gallery_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `gallery_orders`
--
ALTER TABLE `gallery_orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `graduates`
--
ALTER TABLE `graduates`
  MODIFY `graduate_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `graduate_import_logs`
--
ALTER TABLE `graduate_import_logs`
  MODIFY `import_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `login_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=151;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `reset_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `survey_answers`
--
ALTER TABLE `survey_answers`
  MODIFY `answer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `survey_categories`
--
ALTER TABLE `survey_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `survey_questions`
--
ALTER TABLE `survey_questions`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `survey_question_options`
--
ALTER TABLE `survey_question_options`
  MODIFY `option_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `survey_submissions`
--
ALTER TABLE `survey_submissions`
  MODIFY `submission_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `fk_admin_college` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`college_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_admin_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON UPDATE CASCADE;

--
-- Constraints for table `alumni_accounts`
--
ALTER TABLE `alumni_accounts`
  ADD CONSTRAINT `fk_account_graduate` FOREIGN KEY (`graduate_id`) REFERENCES `graduates` (`graduate_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `fk_announcement_admin` FOREIGN KEY (`posted_by`) REFERENCES `admins` (`admin_id`) ON UPDATE CASCADE;

--
-- Constraints for table `announcement_reads`
--
ALTER TABLE `announcement_reads`
  ADD CONSTRAINT `fk_read_announcement` FOREIGN KEY (`announcement_id`) REFERENCES `announcements` (`announcement_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_read_graduate` FOREIGN KEY (`graduate_id`) REFERENCES `graduates` (`graduate_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `fk_course_college` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`college_id`) ON UPDATE CASCADE;

--
-- Constraints for table `employment`
--
ALTER TABLE `employment`
  ADD CONSTRAINT `fk_employment_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_employment_graduate` FOREIGN KEY (`graduate_id`) REFERENCES `graduates` (`graduate_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `employment_history`
--
ALTER TABLE `employment_history`
  ADD CONSTRAINT `fk_history_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_history_graduate` FOREIGN KEY (`graduate_id`) REFERENCES `graduates` (`graduate_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `gallery_albums`
--
ALTER TABLE `gallery_albums`
  ADD CONSTRAINT `fk_album_admin` FOREIGN KEY (`created_by`) REFERENCES `admins` (`admin_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `gallery_images`
--
ALTER TABLE `gallery_images`
  ADD CONSTRAINT `fk_image_admin` FOREIGN KEY (`uploaded_by`) REFERENCES `admins` (`admin_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_image_album` FOREIGN KEY (`album_id`) REFERENCES `gallery_albums` (`album_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `gallery_orders`
--
ALTER TABLE `gallery_orders`
  ADD CONSTRAINT `fk_order_graduate` FOREIGN KEY (`graduate_id`) REFERENCES `graduates` (`graduate_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_order_image` FOREIGN KEY (`image_id`) REFERENCES `gallery_images` (`image_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `graduates`
--
ALTER TABLE `graduates`
  ADD CONSTRAINT `fk_graduate_admin` FOREIGN KEY (`created_by`) REFERENCES `admins` (`admin_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_graduate_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON UPDATE CASCADE;

--
-- Constraints for table `graduate_import_logs`
--
ALTER TABLE `graduate_import_logs`
  ADD CONSTRAINT `fk_import_admin` FOREIGN KEY (`imported_by`) REFERENCES `admins` (`admin_id`) ON UPDATE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notification_graduate` FOREIGN KEY (`graduate_id`) REFERENCES `graduates` (`graduate_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `fk_password_reset_graduate` FOREIGN KEY (`graduate_id`) REFERENCES `graduates` (`graduate_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payment_admin` FOREIGN KEY (`verified_by`) REFERENCES `admins` (`admin_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_payment_order` FOREIGN KEY (`order_id`) REFERENCES `gallery_orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `survey_answers`
--
ALTER TABLE `survey_answers`
  ADD CONSTRAINT `fk_answer_graduate` FOREIGN KEY (`graduate_id`) REFERENCES `graduates` (`graduate_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_answer_option` FOREIGN KEY (`option_id`) REFERENCES `survey_question_options` (`option_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_answer_question` FOREIGN KEY (`question_id`) REFERENCES `survey_questions` (`question_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `survey_questions`
--
ALTER TABLE `survey_questions`
  ADD CONSTRAINT `fk_question_category` FOREIGN KEY (`category_id`) REFERENCES `survey_categories` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `survey_question_options`
--
ALTER TABLE `survey_question_options`
  ADD CONSTRAINT `fk_option_question` FOREIGN KEY (`question_id`) REFERENCES `survey_questions` (`question_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `survey_submissions`
--
ALTER TABLE `survey_submissions`
  ADD CONSTRAINT `fk_survey_submission_graduate` FOREIGN KEY (`graduate_id`) REFERENCES `graduates` (`graduate_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
