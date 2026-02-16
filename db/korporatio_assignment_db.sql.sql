-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Feb 15, 2026 at 01:49 PM
-- Server version: 5.7.39
-- PHP Version: 8.1.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `korporatio_assignment`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `currencies`
--

CREATE TABLE `currencies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `symbol` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `currencies`
--

INSERT INTO `currencies` (`id`, `code`, `name`, `symbol`, `status`, `created_at`, `updated_at`) VALUES
(1, 'USD', 'US Dollar', '$', 1, '2026-02-14 02:21:46', '2026-02-14 02:21:46'),
(2, 'EUR', 'Euro', '€', 1, '2026-02-14 02:21:46', '2026-02-14 02:21:46'),
(3, 'SGD', 'Singapore Dollar', 'S$', 1, '2026-02-14 02:21:47', '2026-02-14 02:21:47'),
(4, 'THB', 'Thailand Baht', 'B', 1, '2026-02-14 08:29:44', '2026-02-14 08:29:52');

-- --------------------------------------------------------

--
-- Table structure for table `external_wallets`
--

CREATE TABLE `external_wallets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency_id` bigint(20) UNSIGNED NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `external_wallets`
--

INSERT INTO `external_wallets` (`id`, `address`, `name`, `currency_id`, `status`, `created_at`, `updated_at`) VALUES
(1, '0x71C7656EC7ab88b098defB751B7401B5f6d8976F', 'Binance Hot Wallet', 1, 1, '2026-02-14 02:21:48', '2026-02-14 02:21:48'),
(2, '0x3f5CE5FBFe3E9af3971dD833D26bA9b5C936f0bE', 'Coinbase', 1, 1, '2026-02-14 02:21:48', '2026-02-14 02:21:48'),
(3, '0x250e76987d838a75310c34bf422ea9f1AC408acc', 'Kraken', 2, 1, '2026-02-14 02:21:48', '2026-02-14 02:21:48'),
(4, '0x123abc12345def67890ghi12345jkl67890mno', 'External Vendor', 1, 1, '2026-02-14 02:21:48', '2026-02-14 02:21:48'),
(5, '0xdef456partnerwallet1234567890abcdef123', 'Partner Wallet', 2, 0, '2026-02-14 02:21:48', '2026-02-14 02:21:48'),
(6, '0xdef456partnerwallet1234567890abcdef456', 'Byget', 3, 1, '2026-02-14 02:21:48', '2026-02-14 02:21:48'),
(7, '0x123abc12345def67890ghi12345jkl67890xyz', 'External Supplier', 1, 0, '2026-02-14 02:21:48', '2026-02-14 02:21:48');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_01_28_081616_create_personal_access_tokens_table', 1),
(5, '2026_01_28_170000_create_wallets_table', 1),
(6, '2026_01_28_170001_create_transactions_table', 1),
(7, '2026_01_28_170002_create_wallet_user_table', 1),
(8, '2026_01_28_180000_create_currencies_table', 1),
(9, '2026_01_28_180001_update_wallets_currency_column', 1),
(10, '2026_01_28_190000_update_status_columns_to_boolean', 1),
(11, '2026_02_13_000003_create_external_wallets_table', 1),
(12, '2026_02_13_000004_create_settings_table', 1),
(13, '2026_02_13_000005_add_address_to_wallets_table', 1),
(14, '2026_02_13_000006_create_transaction_statuses_table', 1),
(15, '2026_02_13_000007_update_transactions_table', 1),
(16, '2026_02_13_074125_create_user_roles_table', 1),
(17, '2026_02_13_074127_add_role_id_to_users_table', 1),
(18, '2026_02_13_143519_add_user_id_to_transactions_table', 1),
(19, '2026_02_14_061218_increase_wallet_address_length', 1),
(20, '2026_02_14_092007_make_wallet_address_nullable', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(27, 'App\\Domain\\User\\Models\\User', 2, 'auth_token', 'ce119875ae15bc6012dc8ca94560128b122eb2c64d0265997bb587b491ce0efa', '[\"*\"]', '2026-02-14 05:35:52', NULL, '2026-02-14 04:59:40', '2026-02-14 05:35:52'),
(38, 'App\\Domain\\User\\Models\\User', 2, 'auth_token', 'be6b3c8981ed512a6f180f51a26dc3d6fb9a131aa948efb1a30f488c877b2754', '[\"*\"]', '2026-02-14 09:41:56', NULL, '2026-02-14 09:07:11', '2026-02-14 09:41:56'),
(39, 'App\\Domain\\User\\Models\\User', 1, 'auth_token', '654f8399bd85fc47b5c1ad515cd3cc931d0212236c0c1e1a7fa5dc984e062809', '[\"*\"]', '2026-02-14 22:33:03', NULL, '2026-02-14 21:34:18', '2026-02-14 22:33:03'),
(40, 'App\\Domain\\User\\Models\\User', 1, 'auth_token', 'a969670864ab3dd09f6c829597f2a3815349ba40e117d7f88b2b736b8b1e9c56', '[\"*\"]', '2026-02-14 23:12:03', NULL, '2026-02-14 22:36:00', '2026-02-14 23:12:03'),
(48, 'App\\Domain\\User\\Models\\User', 2, 'auth_token', 'ce1e08916be00c54ad3ff36f4b086789ae7a8aa460ed6e0d01d1d36a8e040b47', '[\"*\"]', '2026-02-15 01:23:18', NULL, '2026-02-15 00:24:11', '2026-02-15 01:23:18'),
(49, 'App\\Domain\\User\\Models\\User', 1, 'auth_token', '349c174e6148221e399d05bf284ba52e6986a56a0c5d73000aeb94ff650886c7', '[\"*\"]', '2026-02-15 02:57:21', NULL, '2026-02-15 02:31:00', '2026-02-15 02:57:21'),
(50, 'App\\Domain\\User\\Models\\User', 1, 'auth_token', '6a05bf1e2bfda7f00062cb5b3d9034cd6486d471d6afc9d39d085bea6929b9f9', '[\"*\"]', '2026-02-15 04:51:04', NULL, '2026-02-15 03:55:51', '2026-02-15 04:51:04');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `description`, `created_at`, `updated_at`) VALUES
(1, 'transfer_limit', '1000', 'Global transfer limit requiring approval', '2026-02-14 02:21:48', '2026-02-14 02:21:48');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `from_wallet_id` bigint(20) UNSIGNED DEFAULT NULL,
  `to_wallet_id` bigint(20) UNSIGNED DEFAULT NULL,
  `external_wallet_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_status_id` bigint(20) UNSIGNED DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `from_wallet_id`, `to_wallet_id`, `external_wallet_id`, `type`, `transaction_status_id`, `amount`, `reference`, `rejection_reason`, `approved_by`, `approved_at`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, 1, NULL, 'credit', 2, '50000.00', 'Initial Seed Deposit', NULL, NULL, NULL, '2025-02-14 02:21:48', '2026-02-14 02:21:48'),
(2, NULL, NULL, 2, NULL, 'credit', 2, '50000.00', 'Initial Seed Deposit', NULL, NULL, NULL, '2025-02-14 02:21:48', '2026-02-14 02:21:48'),
(3, NULL, NULL, 3, NULL, 'credit', 2, '50000.00', 'Initial Seed Deposit', NULL, NULL, NULL, '2025-02-14 02:21:48', '2026-02-14 02:21:48'),
(4, NULL, NULL, 4, NULL, 'credit', 2, '50000.00', 'Initial Seed Deposit', NULL, NULL, NULL, '2025-02-14 02:21:48', '2026-02-14 02:21:48'),
(5, NULL, NULL, 5, NULL, 'credit', 2, '50000.00', 'Initial Seed Deposit', NULL, NULL, NULL, '2025-02-14 02:21:48', '2026-02-14 02:21:48'),
(6, NULL, NULL, 6, NULL, 'credit', 2, '30000.00', 'Initial Seed Deposit', NULL, NULL, NULL, '2025-02-14 02:21:48', '2026-02-14 02:21:48'),
(7, 3, 5, 1, NULL, 'debit', 2, '500.00', 'No Approval Testing - 1', NULL, NULL, '2026-02-14 02:24:17', '2026-02-14 02:24:17', '2026-02-14 02:24:17'),
(8, 3, 5, NULL, 1, 'debit', 2, '300.00', 'No Pending ( To External )', NULL, NULL, '2026-02-14 02:32:40', '2026-02-14 02:32:40', '2026-02-14 02:32:40'),
(9, 3, 5, 4, NULL, 'debit', 2, '1500.00', 'Need Approval Testing ( To Admin Wallet )', NULL, 2, '2026-02-14 02:59:03', '2026-02-14 02:49:15', '2026-02-14 02:59:03'),
(10, 3, 5, NULL, 2, 'debit', 3, '2000.00', 'Need Approval Testing ( To Ext Wallet )', 'rejected by manager.', NULL, NULL, '2026-02-14 03:02:31', '2026-02-14 03:08:28'),
(11, 3, 5, 1, NULL, 'debit', 3, '2300.00', 'Need Approval Testing ( To Manager Wallet )', 'reject by manager', NULL, NULL, '2026-02-14 03:24:13', '2026-02-14 03:56:05'),
(12, 3, 5, NULL, 1, 'debit', 2, '1600.00', 'Need Approval Testing ( To Ext Wallet )', NULL, 2, '2026-02-14 03:54:39', '2026-02-14 03:29:18', '2026-02-14 03:54:39'),
(13, 2, 1, 4, NULL, 'debit', 2, '1500.00', 'No need approval ( Due to Manager )', NULL, 2, '2026-02-14 04:19:00', '2026-02-14 04:19:00', '2026-02-14 04:19:00'),
(14, 2, 1, 4, NULL, 'debit', 2, '1500.00', 'No Approval ( Due To Manager )', NULL, 2, '2026-02-14 04:24:35', '2026-02-14 04:24:35', '2026-02-14 04:24:35'),
(15, 1, 4, 5, NULL, 'debit', 2, '3000.00', 'No Approval ( Due to Admin )', NULL, 1, '2026-02-14 04:28:01', '2026-02-14 04:28:01', '2026-02-14 04:28:01'),
(16, 3, 5, 1, NULL, 'debit', 1, '1700.00', 'Approval-1 ( To Manager )', NULL, NULL, NULL, '2026-02-13 04:59:29', '2026-02-13 04:59:29'),
(17, 4, 6, 3, NULL, 'debit', 1, '3500.00', 'Need Approval ( To Manager 3 )', NULL, NULL, NULL, '2026-02-14 07:50:59', '2026-02-14 07:50:59'),
(18, 4, 6, NULL, 6, 'debit', 1, '7500.00', 'Need Approval ( To Ext Wallet )', NULL, NULL, NULL, '2026-02-14 07:54:48', '2026-02-14 07:54:48'),
(19, NULL, NULL, 7, NULL, 'credit', 2, '45000.00', 'Initial Balance', NULL, NULL, NULL, '2026-02-14 08:38:35', '2026-02-14 08:38:35'),
(20, 3, 5, NULL, 7, 'debit', 1, '3200.00', 'Approval ( Ext Wallet ) Status Change', NULL, NULL, NULL, '2026-02-14 08:47:05', '2026-02-14 08:47:05');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_statuses`
--

CREATE TABLE `transaction_statuses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transaction_statuses`
--

INSERT INTO `transaction_statuses` (`id`, `name`, `code`, `created_at`, `updated_at`) VALUES
(1, 'Pending', 'pending', '2026-02-14 02:21:48', '2026-02-14 02:21:48'),
(2, 'Completed', 'completed', '2026-02-14 02:21:48', '2026-02-14 02:21:48'),
(3, 'Rejected', 'rejected', '2026-02-14 02:21:48', '2026-02-14 02:21:48'),
(4, 'Cancelled', 'cancelled', '2026-02-14 02:21:48', '2026-02-14 02:21:48');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` bigint(20) UNSIGNED DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `role_id`, `email_verified_at`, `password`, `status`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin User', 'admin@gmail.com', 1, '2026-02-14 02:21:47', '$2y$12$Cztr2xC6wNqtBVFN30EPm.0C9TG9R1E51pQT09w82rfqd0FFcHvNa', 1, 'w4mN5xaQt9', '2026-02-14 02:21:48', '2026-02-14 02:21:48'),
(2, 'Manager User', 'manager1@gmail.com', 3, '2026-02-14 02:21:48', '$2y$12$6YfaWGP.BDmMLr2AIFx.h.5nm4vxGokE2/qBU5/7fBtJsOAUI.hUq', 1, 'eUP340Kwzr', '2026-02-14 02:21:48', '2026-02-14 02:21:48'),
(3, 'User One', 'user1@gmail.com', 2, '2026-02-14 02:21:48', '$2y$12$.AjnzZci3HFiXu0CJVdhjuuiFc.BkxINYgUwmR0v62Jy35LmNEmj2', 1, 'e0syB1GsIV', '2026-02-14 02:21:48', '2026-02-14 02:21:48'),
(4, 'User Two', 'user2@gmail.com', 2, '2026-02-14 02:21:48', '$2y$12$.AjnzZci3HFiXu0CJVdhjuuiFc.BkxINYgUwmR0v62Jy35LmNEmj2', 1, '31Do6bVaF8', '2026-02-14 02:21:48', '2026-02-14 02:21:48');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `name`, `label`) VALUES
(1, 'admin', 'Administrator'),
(2, 'user', 'User'),
(3, 'manager', 'Manager');

-- --------------------------------------------------------

--
-- Table structure for table `wallets`
--

CREATE TABLE `wallets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency_id` bigint(20) UNSIGNED NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wallets`
--

INSERT INTO `wallets` (`id`, `address`, `name`, `currency_id`, `status`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Manager Wallet-1', 1, 1, '2026-02-14 02:21:48', '2026-02-14 02:21:48'),
(2, NULL, 'Manager Wallet-2', 2, 1, '2026-02-14 02:21:48', '2026-02-14 02:21:48'),
(3, NULL, 'Manager Wallet-3', 3, 1, '2026-02-14 02:21:48', '2026-02-14 02:21:48'),
(4, NULL, 'Admin Wallet', 1, 1, '2026-02-14 02:21:48', '2026-02-14 02:21:48'),
(5, NULL, 'Wallet-1', 1, 1, '2026-02-14 02:21:48', '2026-02-14 02:21:48'),
(6, NULL, 'Wallet-2', 3, 1, '2026-02-14 02:21:48', '2026-02-14 02:21:48'),
(7, NULL, 'Wallet-3', 1, 0, '2026-02-14 08:38:35', '2026-02-14 08:38:42');

-- --------------------------------------------------------

--
-- Table structure for table `wallet_user`
--

CREATE TABLE `wallet_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `wallet_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wallet_user`
--

INSERT INTO `wallet_user` (`id`, `wallet_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, 2, '2026-02-14 02:21:48', '2026-02-14 02:21:48'),
(2, 2, 2, '2026-02-14 02:21:48', '2026-02-14 02:21:48'),
(3, 3, 2, '2026-02-14 02:21:48', '2026-02-14 02:21:48'),
(4, 4, 1, '2026-02-14 02:21:48', '2026-02-14 02:21:48'),
(5, 5, 3, '2026-02-14 02:21:48', '2026-02-14 02:21:48'),
(6, 6, 4, '2026-02-14 02:21:48', '2026-02-14 02:21:48');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `currencies_code_unique` (`code`);

--
-- Indexes for table `external_wallets`
--
ALTER TABLE `external_wallets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `external_wallets_address_unique` (`address`),
  ADD KEY `external_wallets_currency_id_foreign` (`currency_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transactions_from_wallet_id_foreign` (`from_wallet_id`),
  ADD KEY `transactions_to_wallet_id_foreign` (`to_wallet_id`),
  ADD KEY `transactions_transaction_status_id_foreign` (`transaction_status_id`),
  ADD KEY `transactions_external_wallet_id_foreign` (`external_wallet_id`),
  ADD KEY `transactions_approved_by_foreign` (`approved_by`),
  ADD KEY `transactions_user_id_foreign` (`user_id`);

--
-- Indexes for table `transaction_statuses`
--
ALTER TABLE `transaction_statuses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_statuses_code_unique` (`code`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_id_foreign` (`role_id`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_roles_name_unique` (`name`);

--
-- Indexes for table `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `wallets_address_unique` (`address`),
  ADD KEY `wallets_currency_id_foreign` (`currency_id`);

--
-- Indexes for table `wallet_user`
--
ALTER TABLE `wallet_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `wallet_user_wallet_id_user_id_unique` (`wallet_id`,`user_id`),
  ADD KEY `wallet_user_user_id_foreign` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `external_wallets`
--
ALTER TABLE `external_wallets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `transaction_statuses`
--
ALTER TABLE `transaction_statuses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `wallets`
--
ALTER TABLE `wallets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `wallet_user`
--
ALTER TABLE `wallet_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `external_wallets`
--
ALTER TABLE `external_wallets`
  ADD CONSTRAINT `external_wallets_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `transactions_external_wallet_id_foreign` FOREIGN KEY (`external_wallet_id`) REFERENCES `external_wallets` (`id`),
  ADD CONSTRAINT `transactions_from_wallet_id_foreign` FOREIGN KEY (`from_wallet_id`) REFERENCES `wallets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_to_wallet_id_foreign` FOREIGN KEY (`to_wallet_id`) REFERENCES `wallets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_transaction_status_id_foreign` FOREIGN KEY (`transaction_status_id`) REFERENCES `transaction_statuses` (`id`),
  ADD CONSTRAINT `transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `user_roles` (`id`);

--
-- Constraints for table `wallets`
--
ALTER TABLE `wallets`
  ADD CONSTRAINT `wallets_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`);

--
-- Constraints for table `wallet_user`
--
ALTER TABLE `wallet_user`
  ADD CONSTRAINT `wallet_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wallet_user_wallet_id_foreign` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
