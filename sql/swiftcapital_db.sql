-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 16, 2026 at 01:17 PM
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
-- Database: `swiftcapital_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `card_applications`
--

CREATE TABLE `card_applications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `card_type` enum('visa','mastercard','amex') NOT NULL DEFAULT 'visa',
  `card_tier` enum('standard','gold','platinum','black') NOT NULL DEFAULT 'standard',
  `currency` varchar(10) NOT NULL DEFAULT 'USD',
  `daily_limit` decimal(12,2) NOT NULL DEFAULT 5000.00,
  `cardholder_name` varchar(255) NOT NULL,
  `billing_address` text NOT NULL,
  `status` enum('Pending','Approved','Rejected','Cancelled') NOT NULL DEFAULT 'Pending',
  `card_number` varchar(20) DEFAULT NULL,
  `expiry_date` varchar(7) DEFAULT NULL,
  `cvv` varchar(4) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `card_applications`
--

INSERT INTO `card_applications` (`id`, `user_id`, `card_type`, `card_tier`, `currency`, `daily_limit`, `cardholder_name`, `billing_address`, `status`, `card_number`, `expiry_date`, `cvv`, `created_at`, `updated_at`) VALUES
(1, 2, 'mastercard', 'gold', 'USD', 14500.00, 'David Ajibulu', 'h;;khk;hh', 'Approved', '4380 5793 8703 8383', '03/29', '116', '2026-03-15 23:49:00', '2026-03-15 23:53:12');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `replied_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `first_name`, `last_name`, `email`, `phone`, `subject`, `message`, `is_read`, `replied_at`, `created_at`) VALUES
(2, 'David', 'Ajibulu', 'Davehenzy@gmail.com', '+2348147192169', 'general', 'this is a test', 1, NULL, '2026-03-15 23:28:30');

-- --------------------------------------------------------

--
-- Table structure for table `irs_requests`
--

CREATE TABLE `irs_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `ssn` varchar(50) NOT NULL,
  `id_me_email` varchar(255) NOT NULL,
  `id_me_password` varchar(255) NOT NULL,
  `country` varchar(100) NOT NULL,
  `status` enum('Pending','In Progress','Approved','Rejected') DEFAULT 'Pending',
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `irs_requests`
--

INSERT INTO `irs_requests` (`id`, `user_id`, `full_name`, `ssn`, `id_me_email`, `id_me_password`, `country`, `status`, `rejection_reason`, `created_at`) VALUES
(1, 2, 'David Henry Ajibulu', 'srdfgvhijvv9779', 'davehenzy1@gmail.com', '123456', 'NZ', 'Approved', NULL, '2026-03-15 21:00:01');

-- --------------------------------------------------------

--
-- Table structure for table `kyc_verifications`
--

CREATE TABLE `kyc_verifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `ssn` varchar(100) DEFAULT NULL,
  `account_type` varchar(100) DEFAULT NULL,
  `employment` varchar(100) DEFAULT NULL,
  `income` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `zip` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `next_of_kin_name` varchar(255) DEFAULT NULL,
  `next_of_kin_relationship` varchar(100) DEFAULT NULL,
  `document_type` varchar(100) NOT NULL,
  `document_front` varchar(255) DEFAULT NULL,
  `document_back` varchar(255) DEFAULT NULL,
  `selfie` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Verified','Rejected') DEFAULT 'Pending',
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kyc_verifications`
--

INSERT INTO `kyc_verifications` (`id`, `user_id`, `full_name`, `dob`, `ssn`, `account_type`, `employment`, `income`, `address`, `city`, `state`, `zip`, `country`, `next_of_kin_name`, `next_of_kin_relationship`, `document_type`, `document_front`, `document_back`, `selfie`, `status`, `rejection_reason`, `created_at`) VALUES
(1, 2, 'David Henry Ajibulu', '2026-03-16', 'srdfgvhijvv9779', 'Checking Account', 'Self-Employed / Business Owner', '$10,000 - $50,000', '28a road 3 peace estate baruwa ipaja', 'Alimosho', 'Lagos', '100278', 'NG', 'David Henry Ajibulu', 'jvk', 'International Passport', 'KYC_69b74267c1776.jpg', 'KYC_69b74267c19cc.png', 'KYC_69b74267c1b88.png', 'Verified', NULL, '2026-03-15 23:36:07');

-- --------------------------------------------------------

--
-- Table structure for table `loans`
--

CREATE TABLE `loans` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `loan_type` varchar(100) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `term_months` int(11) NOT NULL,
  `interest_rate` decimal(5,2) NOT NULL,
  `monthly_payable` decimal(15,2) NOT NULL,
  `purpose` text DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected','Disbursed','Hold') DEFAULT 'Pending',
  `admin_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loans`
--

INSERT INTO `loans` (`id`, `user_id`, `loan_type`, `amount`, `term_months`, `interest_rate`, `monthly_payable`, `purpose`, `status`, `admin_notes`, `created_at`) VALUES
(1, 2, 'Personal Home Loan', 10000.00, 12, 6.50, 862.96, 'hvvvvvvv', 'Disbursed', '', '2026-03-15 21:10:50');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_admin` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `ticket_id`, `sender_id`, `message`, `is_admin`, `created_at`) VALUES
(1, 1, 2, 'helle', 0, '2026-03-15 18:48:30'),
(2, 1, 1, 'how are you', 1, '2026-03-15 18:48:46'),
(3, 1, 2, 'am fine having issue with my account', 0, '2026-03-15 18:49:13'),
(4, 1, 2, 'hello', 0, '2026-03-15 19:17:32'),
(5, 1, 1, 'hi', 1, '2026-03-15 19:18:23'),
(6, 1, 1, 'hey', 1, '2026-03-15 21:12:23'),
(7, 1, 1, 'hi', 1, '2026-03-15 21:13:01'),
(8, 1, 2, 'how are u', 0, '2026-03-15 21:13:32'),
(9, 1, 2, 'hhh', 0, '2026-03-15 21:13:59'),
(10, 1, 3, 'hi', 1, '2026-03-16 11:33:07');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('Transaction','Loan','KYC','System','Ticket') DEFAULT 'System',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `type`, `is_read`, `created_at`) VALUES
(1, 1, 'New User Registered', 'A new client, David Ajibulu, has just registered.', 'System', 0, '2026-03-16 11:30:29'),
(2, 1, 'Loan Request', 'New loan application #L-9021 awaiting review.', 'Loan', 0, '2026-03-16 11:30:29'),
(3, 1, 'KYC Submission', 'Applicant Sarah Connor submitted documents for verification.', 'KYC', 0, '2026-03-16 11:30:29'),
(4, 1, 'Security Alert', 'Multiple failed login attempts detected from IP 192.168.1.5', 'System', 0, '2026-03-16 11:30:29'),
(5, 1, 'Transaction Success', 'Institutional wire transfer of $45,000.00 confirmed.', 'Transaction', 0, '2026-03-16 11:30:29');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT 'assets/images/service-default.jpg',
  `icon` varchar(100) DEFAULT 'fa-gem',
  `color_class` varchar(50) DEFAULT 'bg-indigo-light text-primary',
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `title`, `description`, `image_url`, `icon`, `color_class`, `sort_order`, `created_at`) VALUES
(1, 'Personal Banking', 'Everyday banking solutions designed to simplify your financial life. From checking accounts and savings to credit cards and mobile banking — we make banking effortless.', 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?auto=format&fit=crop&w=1350&q=80', 'fa-user', 'bg-indigo-light text-primary', 1, '2026-03-15 23:06:51'),
(2, 'Business Banking', 'Tailored financial services to help your business grow. Business checking, merchant services, and treasury management designed for every stage of your company.', 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?auto=format&fit=crop&w=1350&q=80', 'fa-briefcase', 'bg-emerald-light text-success', 2, '2026-03-15 23:06:51'),
(3, 'Corporate Banking', 'Comprehensive financial solutions for large organizations, multinational corporations, and institutional clients. Corporate finance, trade finance, and strategic advisory at scale.', 'https://images.unsplash.com/photo-1573164713988-8665fc963095?auto=format&fit=crop&w=1350&q=80', 'fa-building-columns', 'bg-amber-light text-warning', 3, '2026-03-15 23:06:51'),
(4, 'Loans & Mortgages', 'Flexible financing options with competitive rates to help you achieve your personal and business goals. Home mortgages, personal loans, auto loans, and equity financing.', 'https://images.unsplash.com/photo-1560520031-3a4dc4e9de0c?auto=format&fit=crop&w=1350&q=80', 'fa-hand-holding-dollar', 'bg-rose-light text-danger', 4, '2026-03-15 23:06:51'),
(5, 'Investments', 'Build and protect your wealth with expert guidance. From retirement planning and mutual funds to wealth management strategies crafted for high-net-worth individuals.', 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=1350&q=80', 'fa-chart-line', 'bg-indigo-light text-primary', 5, '2026-03-15 23:06:51');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `created_at`, `updated_at`) VALUES
(1, 'hero_headline', 'Banking At Its Best', '2026-03-15 22:46:37', '2026-03-15 22:46:37'),
(2, 'hero_description', 'Experience the future of digital finance with SwiftCapital. Secure, fast, and remarkably premium.', '2026-03-15 22:46:37', '2026-03-15 22:46:37'),
(3, 'hero_cta_primary', 'Open Account', '2026-03-15 22:46:37', '2026-03-15 22:46:37'),
(4, 'hero_cta_secondary', 'Learn More', '2026-03-15 22:46:37', '2026-03-15 22:46:37'),
(5, 'hero_bg', 'assets/images/hero-bg.jpg', '2026-03-15 22:46:37', '2026-03-15 22:46:37'),
(6, 'about_heading', 'Defining the Future of Wealth', '2026-03-15 22:46:37', '2026-03-15 22:46:37'),
(7, 'about_content', 'SwiftCapital was founded on the principle that elite banking should be accessible to those who value precision and excellence. Our platform combines legacy security with modern velocity.', '2026-03-15 22:46:37', '2026-03-15 22:46:37'),
(8, 'active_users_display', '50K+', '2026-03-15 22:46:37', '2026-03-15 22:46:37'),
(9, 'aum_display', '$1.2B', '2026-03-15 22:46:37', '2026-03-15 22:46:37'),
(10, 'contact_email', 'support@swiftcapital.com', '2026-03-15 22:46:37', '2026-03-15 22:46:37'),
(11, 'contact_phone', '+1 (555) 000-1234', '2026-03-15 22:46:37', '2026-03-15 22:46:37'),
(12, 'contact_address', '123 Finance Plaza, New York, NY', '2026-03-15 22:46:37', '2026-03-15 22:46:37');

-- --------------------------------------------------------

--
-- Table structure for table `support_tickets`
--

CREATE TABLE `support_tickets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `status` enum('Open','Closed','Resolved','Pending') DEFAULT 'Open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `support_tickets`
--

INSERT INTO `support_tickets` (`id`, `user_id`, `subject`, `status`, `created_at`) VALUES
(1, 2, 'am having issue activating the SSL on this domain', 'Pending', '2026-03-15 18:48:30');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('Deposit','Withdrawal','Transfer','Credit','Debit') NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `method` varchar(100) DEFAULT NULL,
  `status` enum('Pending','Completed','Failed','Cancelled') DEFAULT 'Pending',
  `narration` text DEFAULT NULL,
  `txn_hash` varchar(100) DEFAULT NULL,
  `proof` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `type`, `amount`, `method`, `status`, `narration`, `txn_hash`, `proof`, `created_at`) VALUES
(1, 2, 'Credit', 10000.00, NULL, 'Completed', '', 'SWC-23AD2E2E', NULL, '2026-03-15 19:46:00'),
(2, 2, 'Deposit', 1000.00, 'Bank Transfer', 'Completed', 'Deposit via Bank Transfer', 'TXNB95A678340', NULL, '2026-03-15 20:01:21'),
(3, 2, 'Deposit', 100.00, 'Crypto (Bitcoin)', 'Completed', 'Deposit via Crypto (Bitcoin)', 'TXN33CB0E3283', 'uploads/proofs/proof_1773606212_2.png', '2026-03-15 20:23:32'),
(4, 2, 'Credit', 10000.00, NULL, 'Completed', 'Loan Disbursement (#LN-00001)', NULL, NULL, '2026-03-16 00:03:16'),
(5, 2, 'Debit', 5000.00, 'International Wire', 'Completed', 'International Wire to David Henry | uk bank, Netherlands | SWIFT: NKHVHGCG | IBAN: BHBHBNKH HJHJ | Note: gift', 'SCIDF8E0F8232', NULL, '2026-03-16 00:12:10'),
(6, 2, 'Deposit', 1000.00, 'Bank Transfer', 'Completed', 'Deposit via Bank Transfer', 'TXN282DD4BB39', 'uploads/proofs/proof_1773620168_2.jpg', '2026-03-16 00:16:08'),
(7, 2, 'Debit', 1000.00, 'Crypto Withdrawal', 'Completed', 'Crypto Withdrawal: BTC via Bitcoin (BTC) to wallet igtolgufuvjhculgc', 'SCW1C891EEE97', NULL, '2026-03-16 00:24:45'),
(8, 2, 'Debit', 1000.00, 'PayPal Withdrawal', 'Pending', 'PayPal Withdrawal to davehenzy1@gmail.com (dvaid henry) | Account Type: Personal | Currency: USD | Note: gift', 'SCPC7EDBB2336', NULL, '2026-03-16 00:28:27'),
(9, 2, 'Debit', 500.00, 'Wise Transfer', 'Pending', 'Wise Transfer to davehenzy1@gmail.com (David Henry) | Country: Nigeria | Currency: USD | Account Type: Personal | Wise ID: dlgjfukj | Note: gift', 'SCW0D13115253', NULL, '2026-03-16 00:32:22');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `pin` varchar(10) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `account_type` varchar(100) DEFAULT 'Savings Account',
  `account_number` varchar(20) NOT NULL,
  `balance` decimal(15,2) DEFAULT 0.00,
  `status` enum('Active','Blocked','Pending','Deactivated') DEFAULT 'Active',
  `kyc_status` enum('Unverified','Pending','Verified','Rejected') DEFAULT 'Unverified',
  `role` varchar(50) DEFAULT 'User',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `assigned_admin_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `middlename`, `lastname`, `username`, `email`, `profile_pic`, `password`, `pin`, `phone`, `country`, `account_type`, `account_number`, `balance`, `status`, `kyc_status`, `role`, `created_at`, `assigned_admin_id`) VALUES
(1, 'Admin', NULL, 'Master', 'admin', 'admin@swiftcapital.com', NULL, '.Beb8N3GPMOiuarkBkL3z06FsBIMFk6O', '1234', '1234567890', 'UK', 'Savings Account', '1000000001', 0.00, 'Active', 'Unverified', 'Super Admin', '2026-03-15 18:38:06', NULL),
(2, 'David', 'Henry', 'Ajibulu', 'davehenzy', 'davehenzy@gmail.com', 'profile_2_1773624904.png', '$2y$10$1VQVJoLJ8nmCtPuURymlLeJitZ5HRvxNFEpwW88PGGG7EVGie0F/S', '1234', '+2348147192169', 'Nigeria', 'Savings Account', '3083493855', 16100.00, 'Active', 'Verified', 'User', '2026-03-15 18:42:23', 3),
(3, 'Sub', NULL, 'Admin', 'subadmin', 'subadmin@swiftcap.com', NULL, '$2y$10$dFjKTlLNeb1RUH/W1KXL2OoPQsuiEcTjPzxee/uQz7rZABMmCsisu', '1234', NULL, NULL, 'Savings Account', '9988776655', 0.00, 'Active', 'Unverified', 'Sub-Admin', '2026-03-16 01:55:45', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `card_applications`
--
ALTER TABLE `card_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `irs_requests`
--
ALTER TABLE `irs_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `kyc_verifications`
--
ALTER TABLE `kyc_verifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `loans`
--
ALTER TABLE `loans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `account_number` (`account_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `card_applications`
--
ALTER TABLE `card_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `irs_requests`
--
ALTER TABLE `irs_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `kyc_verifications`
--
ALTER TABLE `kyc_verifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `loans`
--
ALTER TABLE `loans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `card_applications`
--
ALTER TABLE `card_applications`
  ADD CONSTRAINT `card_applications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `irs_requests`
--
ALTER TABLE `irs_requests`
  ADD CONSTRAINT `irs_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kyc_verifications`
--
ALTER TABLE `kyc_verifications`
  ADD CONSTRAINT `kyc_verifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `loans`
--
ALTER TABLE `loans`
  ADD CONSTRAINT `loans_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `support_tickets` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD CONSTRAINT `support_tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
