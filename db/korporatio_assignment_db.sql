-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Jan 31, 2026 at 06:13 AM
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
(1, 'USD', 'US Dollar', '$', 1, '2026-01-28 03:52:53', '2026-01-28 03:52:53'),
(2, 'EUR', 'Euro', '€', 1, '2026-01-28 03:52:53', '2026-01-28 03:52:53'),
(4, 'SGD', 'Singapore Dollar', 'S$', 1, '2026-01-28 08:12:52', '2026-01-28 08:12:52'),
(5, 'MMK', 'Myanmar Kyats', 'Kyats', 1, '2026-01-28 10:27:23', '2026-01-29 10:38:21');

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
(5, '2026_01_28_170000_create_wallets_table', 2),
(6, '2026_01_28_170001_create_transactions_table', 2),
(7, '2026_01_28_170002_create_wallet_user_table', 2),
(8, '2026_01_28_180000_create_currencies_table', 3),
(9, '2026_01_28_180001_update_wallets_currency_column', 3),
(10, '2026_01_28_190000_update_status_columns_to_boolean', 4);

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
(2, 'App\\Domain\\User\\Models\\User', 2, 'auth_token', '19bc84e79ad6d74717bf61e56bc64280359cfc3ef2717b26617986ee4dfc5ecc', '[\"*\"]', '2026-01-28 01:51:38', NULL, '2026-01-28 01:50:47', '2026-01-28 01:51:38'),
(3, 'App\\Domain\\User\\Models\\User', 1, 'auth_token', '6459a1ef3851ccf10d9b8016686d7e0634e7012e1897544b9e37cf9c7b0c7e69', '[\"*\"]', '2026-01-29 21:53:37', NULL, '2026-01-28 01:59:40', '2026-01-29 21:53:37'),
(4, 'App\\Domain\\User\\Models\\User', 1, 'auth_token', '9317ac79ea6d34bb515148697629baba6532bd675f2a3b6b9f967cde833fc12e', '[\"*\"]', NULL, NULL, '2026-01-28 02:39:43', '2026-01-28 02:39:43'),
(5, 'App\\Domain\\User\\Models\\User', 1, 'auth_token', '47914c0f8704622e5f2d188724cc21983260acc996bb7a506fd39932502d9889', '[\"*\"]', NULL, NULL, '2026-01-28 07:07:02', '2026-01-28 07:07:02'),
(6, 'App\\Domain\\User\\Models\\User', 1, 'auth_token', '53fa65c82b3de69594f0e191cd6df60143dd650a9e23651027a79ec925a19125', '[\"*\"]', NULL, NULL, '2026-01-28 07:33:55', '2026-01-28 07:33:55'),
(7, 'App\\Domain\\User\\Models\\User', 1, 'auth_token', 'fccb07a90b56a9d5fef30bacb5bfe2e8ed829450d9e4bcf2a2f4c874f6a026a4', '[\"*\"]', NULL, NULL, '2026-01-28 08:06:38', '2026-01-28 08:06:38'),
(8, 'App\\Domain\\User\\Models\\User', 1, 'auth_token', '0fbb04f1cc124e50408072ef7135f5c7c57af264207ed33998e6337c7ddabd24', '[\"*\"]', NULL, NULL, '2026-01-28 10:26:08', '2026-01-28 10:26:08'),
(9, 'App\\Domain\\User\\Models\\User', 1, 'auth_token', 'ea6a720c11ff7bcccfae43a722d22c164f448dca63442c17285cc458a6e8f3fa', '[\"*\"]', NULL, NULL, '2026-01-28 20:18:15', '2026-01-28 20:18:15'),
(33, 'App\\Domain\\User\\Models\\User', 1, 'auth_token', '4d44f0ddb9627d8232e8fe5ebfa0fd97f27994b0134ef94e268076b0f3665305', '[\"*\"]', NULL, NULL, '2026-01-29 21:45:20', '2026-01-29 21:45:20'),
(34, 'App\\Domain\\User\\Models\\User', 1, 'auth_token', '6d566d1a41ca992d6a4e6cd8faf2c536b2b1041bab8ae7b0c6be162e8c92cb70', '[\"*\"]', NULL, NULL, '2026-01-29 21:52:41', '2026-01-29 21:52:41'),
(65, 'App\\Domain\\User\\Models\\User', 1, 'auth_token', 'c4e77ca71461f6898173eb9df80c6f50130e216544c344a5764c6644b813d7dc', '[\"*\"]', NULL, NULL, '2026-01-30 04:41:47', '2026-01-30 04:41:47'),
(66, 'App\\Domain\\User\\Models\\User', 1, 'auth_token', '6f592183d188672446d0beb4be58123e0b7580431b1a46b89e7d0202d545d967', '[\"*\"]', NULL, NULL, '2026-01-30 09:20:00', '2026-01-30 09:20:00'),
(68, 'App\\Domain\\User\\Models\\User', 1, 'auth_token', '4137f9e85aee0a95687c29498d60a022bdb8237f0a1b563351857c693908a4d9', '[\"*\"]', '2026-01-30 22:37:33', NULL, '2026-01-30 11:10:44', '2026-01-30 22:37:33'),
(69, 'App\\Domain\\User\\Models\\User', 1, 'auth_token', '9161023cd7e7c2d9e0e20c5f1831c8f1b2758d71d6d8c3e2da7295c144642973', '[\"*\"]', '2026-01-30 22:35:14', NULL, '2026-01-30 22:32:09', '2026-01-30 22:35:14');

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
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `from_wallet_id` bigint(20) UNSIGNED DEFAULT NULL,
  `to_wallet_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `from_wallet_id`, `to_wallet_id`, `type`, `amount`, `reference`, `created_at`, `updated_at`) VALUES
(1, NULL, 1, 'credit', '1000.00', 'Initial Balance', '2026-01-28 07:19:35', '2026-01-28 07:19:35'),
(2, NULL, 2, 'credit', '500.00', 'Initial Balance', '2026-01-28 07:35:19', '2026-01-28 07:35:19'),
(3, NULL, 3, 'credit', '300.00', 'Initial Balance', '2026-01-28 10:51:16', '2026-01-28 10:51:16'),
(16, NULL, 16, 'credit', '700.00', 'Initial Balance', '2026-01-29 23:33:42', '2026-01-29 23:33:42'),
(17, 1, 2, 'debit', '3336.00', 'Client Payment - Invoice #1024', '2026-01-22 02:02:49', '2026-01-22 02:02:49'),
(18, 1, 3, 'debit', '730.00', 'Office Supplies', '2026-01-06 02:03:33', '2026-01-06 02:03:33'),
(19, 1, 16, 'debit', '590.00', 'Server Infrastructure', '2026-01-19 02:03:34', '2026-01-19 02:03:34'),
(20, 1, 2, 'debit', '2669.00', 'SaaS Subscription', '2026-01-24 02:03:34', '2026-01-24 02:03:34'),
(21, 1, 2, 'credit', '2587.00', 'Marketing Budget', '2026-01-06 02:03:34', '2026-01-06 02:03:34'),
(22, 2, 1, 'debit', '3871.00', 'Server Infrastructure', '2026-01-21 02:03:34', '2026-01-21 02:03:34'),
(23, 2, 3, 'debit', '2974.00', 'Travel Expenses', '2026-01-02 02:03:34', '2026-01-02 02:03:34'),
(24, 2, 3, 'credit', '4694.00', 'Consulting Fees', '2026-01-26 02:03:34', '2026-01-26 02:03:34'),
(25, 16, 3, 'credit', '1101.00', 'Consulting Fees', '2026-01-12 02:03:34', '2026-01-12 02:03:34'),
(26, 3, 1, 'debit', '2758.00', 'Salary Payment', '2026-01-08 02:03:34', '2026-01-08 02:03:34'),
(27, 3, 16, 'credit', '1099.00', 'Server Infrastructure', '2026-01-24 02:03:34', '2026-01-24 02:03:34'),
(28, NULL, 16, 'credit', '597.00', 'Travel Expenses', '2025-12-31 02:03:34', '2025-12-31 02:03:34'),
(29, 1, 16, 'credit', '3140.00', 'Server Infrastructure', '2026-01-02 02:03:34', '2026-01-02 02:03:34'),
(30, 16, 1, 'credit', '10000.00', 'Balance Correction', '2026-01-30 02:11:29', '2026-01-30 02:11:29'),
(31, 16, 2, 'credit', '10000.00', 'Balance Correction', '2026-01-30 02:11:29', '2026-01-30 02:11:29'),
(32, 2, 16, 'debit', '50.00', 'Internal Transfer to Marketing', '2026-01-30 02:19:00', '2026-01-30 02:19:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `status`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin User', 'admin@gmail.com', '2026-01-28 01:24:37', '$2y$12$BtM8EOH1mLgrE.bVBHRBtOWeffAWhUDp3bB/o.H3HRoG4Acw17yzy', 'admin', 1, 'sxkb3isTnF', '2026-01-28 01:24:37', '2026-01-28 01:24:37'),
(2, 'User One', 'user1@gmail.com', '2026-01-28 01:24:37', '$2y$12$oVT4wrmizaqmyVSnP1ok6OOFI5JY68Dx1T4Bdj6n/REJBkOOalne.', 'user', 1, 'tLUYThUcc3', '2026-01-28 01:24:37', '2026-01-28 01:24:37'),
(3, 'User Two', 'user2@gmail.com', '2026-01-28 01:24:37', '$2y$12$OXBVS6Fle14n64UD4RbAZuDKfNn5I1b1.ohNsWZPTFsb12JhWhr0m', 'user', 1, 'PBYG84G56u', '2026-01-28 01:24:37', '2026-01-28 01:24:37'),
(4, 'User Three', 'user3@gmail.com', '2026-01-28 01:24:37', '$2y$12$d2xt8y8sGUNGuGZzeOeH1OILkGndS4Fnv.tNKWej47jb9H2l8kEha', 'user', 1, '4g3Ff8knQw', '2026-01-28 01:24:38', '2026-01-28 01:24:38'),
(5, 'User Four', 'user4@gmail.com', '2026-01-28 01:24:38', '$2y$12$I5zvrDpfZ7YvKBKH13f31OQOf1wLIHL4y2gZnzwSRKqkMd2NsuMjS', 'user', 1, 'Y5aZv2ESGi', '2026-01-28 01:24:38', '2026-01-28 01:24:38'),
(6, 'Updated Name', 'new1@member.com', NULL, '$2y$12$DJVzDr83N.TswGSORgxEeO6elWIdYLATT2mTCLPl5dXFVuC98.CnG', 'user', 1, NULL, '2026-01-28 01:50:12', '2026-01-28 07:17:57'),
(16, 'EU User', 'euuser@gmail.com', NULL, '$2y$12$pYE6sgneTThU6TAWNaSeNu2Fcmos4jh7ljpI7pgmICNI7sIzHoiBK', 'user', 1, NULL, '2026-01-29 23:35:52', '2026-01-29 23:35:52');

-- --------------------------------------------------------

--
-- Table structure for table `wallets`
--

CREATE TABLE `wallets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency_id` bigint(20) UNSIGNED NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wallets`
--

INSERT INTO `wallets` (`id`, `name`, `currency_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'USD Wallet', 1, 1, '2026-01-28 07:19:35', '2026-01-28 07:40:28'),
(2, 'EU General Wallet', 2, 1, '2026-01-28 07:35:19', '2026-01-29 23:33:20'),
(3, 'SGD Wallet', 4, 1, '2026-01-28 10:51:16', '2026-01-28 10:52:54'),
(16, 'EU Marketing Wallet', 2, 1, '2026-01-29 23:33:42', '2026-01-29 23:33:42');

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
(5, 1, 2, '2026-01-29 01:22:49', '2026-01-29 01:22:49'),
(6, 1, 3, '2026-01-29 01:22:49', '2026-01-29 01:22:49'),
(7, 1, 1, NULL, NULL),
(8, 2, 16, NULL, NULL),
(9, 16, 16, NULL, NULL);

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
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transactions_from_wallet_id_foreign` (`from_wallet_id`),
  ADD KEY `transactions_to_wallet_id_foreign` (`to_wallet_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`id`),
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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `wallets`
--
ALTER TABLE `wallets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `wallet_user`
--
ALTER TABLE `wallet_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_from_wallet_id_foreign` FOREIGN KEY (`from_wallet_id`) REFERENCES `wallets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_to_wallet_id_foreign` FOREIGN KEY (`to_wallet_id`) REFERENCES `wallets` (`id`) ON DELETE CASCADE;

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
