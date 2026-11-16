-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 05, 2025 at 06:31 AM
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
-- Database: `ecommercerft`
--

-- --------------------------------------------------------

--
-- Table structure for table `billing_details`
--

CREATE TABLE `billing_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `country` varchar(255) NOT NULL,
  `billing_address` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `zipcode` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `order_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(32) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `created_at`, `updated_at`) VALUES
(1, 'Sayur Daun', 'SAYUR-DAUN', '2025-02-22 06:24:33', '2025-02-23 10:38:03'),
(7, 'Sayur Buah', 'SAYUR-BUAH', '2025-02-23 10:55:25', '2025-02-23 10:55:25'),
(8, 'Sayur Akar', 'SAYUR-AKAR', '2025-02-23 10:55:45', '2025-02-23 10:55:45'),
(9, 'Sayur Kacang-Kacangan', 'SAYUR-KACANG-KACANGAN', '2025-02-23 10:56:01', '2025-02-23 10:56:01'),
(10, 'Buah Tropis', 'BUAH-TROPIS', '2025-02-23 11:38:11', '2025-02-23 11:38:11'),
(11, 'Buah Lokal', 'BUAH-LOKAL', '2025-02-23 11:38:35', '2025-02-23 11:38:35');

-- --------------------------------------------------------

--
-- Table structure for table `category_product`
--

CREATE TABLE `category_product` (
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `category_product`
--

INSERT INTO `category_product` (`category_id`, `product_id`) VALUES
(1, 2),
(1, 3),
(1, 34),
(1, 36),
(7, 6),
(8, 3),
(8, 4),
(8, 7),
(8, 35);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_05_03_000001_create_customer_columns', 1),
(4, '2019_05_03_000002_create_subscriptions_table', 1),
(5, '2019_05_03_000003_create_subscription_items_table', 1),
(6, '2019_08_19_000000_create_failed_jobs_table', 1),
(7, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(8, '2023_05_12_181950_create_categories_table', 1),
(9, '2023_05_12_182019_create_products_table', 1),
(10, '2023_05_13_150158_create_category_product_table', 1),
(11, '2023_05_13_201931_create_orders_table', 1),
(12, '2023_05_13_201932_create_order_items_table', 1),
(13, '2023_05_13_231915_create_billing_details_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('pending','processing','completed','canceled') NOT NULL,
  `total` decimal(8,2) NOT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `status`, `total`, `session_id`, `created_at`, `updated_at`) VALUES
(86, 5, 'completed', 20500.00, NULL, '2025-06-11 21:35:30', '2025-06-11 21:35:30'),
(87, 5, 'completed', 33000.00, NULL, '2025-06-11 21:36:48', '2025-06-11 21:36:48'),
(88, 5, 'completed', 19000.00, NULL, '2025-06-11 21:38:01', '2025-06-11 21:38:01'),
(89, 5, 'completed', 32500.00, NULL, '2025-06-11 21:52:58', '2025-06-11 21:52:58'),
(90, 5, 'completed', 30000.00, NULL, '2025-06-11 21:54:19', '2025-06-11 21:54:19'),
(91, 5, 'completed', 18000.00, NULL, '2025-06-11 21:55:15', '2025-06-11 21:55:15'),
(92, 5, 'completed', 33500.00, NULL, '2025-06-11 21:56:37', '2025-06-11 21:56:37'),
(93, 5, 'completed', 26000.00, NULL, '2025-06-11 21:57:57', '2025-06-11 21:57:57'),
(94, 5, 'completed', 16500.00, NULL, '2025-06-11 21:59:06', '2025-06-11 21:59:06'),
(95, 5, 'completed', 42000.00, NULL, '2025-06-11 22:00:17', '2025-06-11 22:00:17'),
(96, 5, 'completed', 20500.00, NULL, '2025-06-11 22:01:36', '2025-06-11 22:01:36'),
(97, 5, 'completed', 38500.00, NULL, '2025-06-11 22:02:45', '2025-06-11 22:02:45'),
(98, 5, 'completed', 18500.00, NULL, '2025-06-11 22:03:57', '2025-06-11 22:03:57'),
(99, 5, 'completed', 19500.00, NULL, '2025-06-11 22:05:11', '2025-06-11 22:05:11'),
(100, 5, 'completed', 30000.00, NULL, '2025-06-11 22:06:59', '2025-06-11 22:06:59'),
(101, 5, 'completed', 32500.00, NULL, '2025-06-11 22:07:55', '2025-06-11 22:07:55'),
(102, 5, 'completed', 33500.00, NULL, '2025-06-11 22:08:55', '2025-06-11 22:08:55'),
(103, 5, 'completed', 18500.00, NULL, '2025-06-11 22:10:15', '2025-06-11 22:10:15'),
(104, 5, 'completed', 25500.00, NULL, '2025-06-11 22:11:25', '2025-06-11 22:11:25'),
(105, 5, 'completed', 28500.00, NULL, '2025-06-11 22:12:27', '2025-06-11 22:12:27'),
(106, 5, 'completed', 33000.00, NULL, '2025-06-11 22:13:31', '2025-06-11 22:13:31'),
(107, 5, 'completed', 33500.00, NULL, '2025-06-11 22:14:21', '2025-06-12 00:01:42'),
(108, 5, 'completed', 25000.00, NULL, '2025-06-11 22:15:15', '2025-06-12 00:02:15'),
(109, 5, 'completed', 21500.00, NULL, '2025-06-11 22:16:25', '2025-06-12 00:02:29'),
(110, 5, 'completed', 41000.00, NULL, '2025-06-11 22:17:24', '2025-06-12 00:02:49'),
(111, 5, 'pending', 13500.00, NULL, '2025-06-13 04:05:27', '2025-06-13 04:05:27'),
(112, 5, 'pending', 35500.00, NULL, '2025-06-22 18:15:43', '2025-06-22 18:15:43'),
(113, 5, 'pending', 25500.00, NULL, '2025-06-22 18:17:20', '2025-06-22 18:17:20'),
(114, 5, 'pending', 18500.00, NULL, '2025-06-22 18:19:00', '2025-06-22 18:19:00'),
(115, 5, 'pending', 18500.00, NULL, '2025-06-22 18:19:05', '2025-06-22 18:19:05'),
(116, 5, 'pending', 6000.00, NULL, '2025-07-14 00:19:41', '2025-07-14 00:19:41'),
(117, 5, 'pending', 5000.00, NULL, '2025-07-31 08:48:18', '2025-07-31 08:48:18'),
(118, 5, 'pending', 6000.00, NULL, '2025-08-04 21:14:53', '2025-08-04 21:14:53');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`, `created_at`, `updated_at`) VALUES
(129, 86, 3, 1, 5000.00, '2025-06-11 21:35:30', '2025-06-11 21:35:30'),
(130, 86, 4, 1, 7500.00, '2025-06-11 21:35:30', '2025-06-11 21:35:30'),
(131, 86, 6, 1, 8000.00, '2025-06-11 21:35:30', '2025-06-11 21:35:30'),
(132, 87, 34, 1, 5500.00, '2025-06-11 21:36:48', '2025-06-11 21:36:48'),
(133, 87, 35, 1, 14500.00, '2025-06-11 21:36:48', '2025-06-11 21:36:48'),
(134, 87, 7, 1, 13000.00, '2025-06-11 21:36:48', '2025-06-11 21:36:48'),
(135, 88, 6, 1, 8000.00, '2025-06-11 21:38:01', '2025-06-11 21:38:01'),
(136, 88, 3, 1, 5000.00, '2025-06-11 21:38:01', '2025-06-11 21:38:01'),
(137, 88, 2, 1, 6000.00, '2025-06-11 21:38:01', '2025-06-11 21:38:01'),
(138, 89, 3, 1, 5000.00, '2025-06-11 21:52:58', '2025-06-11 21:52:58'),
(139, 89, 36, 1, 20000.00, '2025-06-11 21:52:58', '2025-06-11 21:52:58'),
(140, 89, 4, 1, 7500.00, '2025-06-11 21:52:58', '2025-06-11 21:52:58'),
(141, 90, 4, 1, 7500.00, '2025-06-11 21:54:20', '2025-06-11 21:54:20'),
(142, 90, 35, 1, 14500.00, '2025-06-11 21:54:20', '2025-06-11 21:54:20'),
(143, 90, 6, 1, 8000.00, '2025-06-11 21:54:20', '2025-06-11 21:54:20'),
(144, 91, 7, 1, 13000.00, '2025-06-11 21:55:15', '2025-06-11 21:55:15'),
(145, 91, 3, 1, 5000.00, '2025-06-11 21:55:15', '2025-06-11 21:55:15'),
(146, 92, 2, 1, 6000.00, '2025-06-11 21:56:37', '2025-06-11 21:56:37'),
(147, 92, 36, 1, 20000.00, '2025-06-11 21:56:37', '2025-06-11 21:56:37'),
(148, 92, 4, 1, 7500.00, '2025-06-11 21:56:37', '2025-06-11 21:56:37'),
(149, 93, 3, 1, 5000.00, '2025-06-11 21:57:58', '2025-06-11 21:57:58'),
(150, 93, 6, 1, 8000.00, '2025-06-11 21:57:58', '2025-06-11 21:57:58'),
(151, 93, 7, 1, 13000.00, '2025-06-11 21:57:58', '2025-06-11 21:57:58'),
(152, 94, 34, 1, 5500.00, '2025-06-11 21:59:06', '2025-06-11 21:59:06'),
(153, 94, 2, 1, 6000.00, '2025-06-11 21:59:06', '2025-06-11 21:59:06'),
(154, 94, 3, 1, 5000.00, '2025-06-11 21:59:06', '2025-06-11 21:59:06'),
(155, 95, 35, 1, 14500.00, '2025-06-11 22:00:17', '2025-06-11 22:00:17'),
(156, 95, 36, 1, 20000.00, '2025-06-11 22:00:17', '2025-06-11 22:00:17'),
(157, 95, 4, 1, 7500.00, '2025-06-11 22:00:17', '2025-06-11 22:00:17'),
(158, 96, 6, 1, 8000.00, '2025-06-11 22:01:36', '2025-06-11 22:01:36'),
(159, 96, 3, 1, 5000.00, '2025-06-11 22:01:36', '2025-06-11 22:01:36'),
(160, 96, 4, 1, 7500.00, '2025-06-11 22:01:36', '2025-06-11 22:01:36'),
(161, 97, 7, 1, 13000.00, '2025-06-11 22:02:45', '2025-06-11 22:02:45'),
(162, 97, 36, 1, 20000.00, '2025-06-11 22:02:45', '2025-06-11 22:02:45'),
(163, 97, 34, 1, 5500.00, '2025-06-11 22:02:45', '2025-06-11 22:02:45'),
(164, 98, 4, 1, 7500.00, '2025-06-11 22:03:57', '2025-06-11 22:03:57'),
(165, 98, 3, 1, 5000.00, '2025-06-11 22:03:57', '2025-06-11 22:03:57'),
(166, 98, 2, 1, 6000.00, '2025-06-11 22:03:57', '2025-06-11 22:03:57'),
(167, 99, 6, 1, 8000.00, '2025-06-11 22:05:11', '2025-06-11 22:05:11'),
(168, 99, 34, 1, 5500.00, '2025-06-11 22:05:11', '2025-06-11 22:05:11'),
(169, 99, 2, 1, 6000.00, '2025-06-11 22:05:11', '2025-06-11 22:05:11'),
(170, 100, 35, 1, 14500.00, '2025-06-11 22:06:59', '2025-06-11 22:06:59'),
(171, 100, 4, 1, 7500.00, '2025-06-11 22:06:59', '2025-06-11 22:06:59'),
(172, 100, 6, 1, 8000.00, '2025-06-11 22:06:59', '2025-06-11 22:06:59'),
(173, 101, 3, 1, 5000.00, '2025-06-11 22:07:55', '2025-06-11 22:07:55'),
(174, 101, 7, 1, 13000.00, '2025-06-11 22:07:55', '2025-06-11 22:07:55'),
(175, 101, 35, 1, 14500.00, '2025-06-11 22:07:55', '2025-06-11 22:07:55'),
(176, 102, 36, 1, 20000.00, '2025-06-11 22:08:56', '2025-06-11 22:08:56'),
(177, 102, 2, 1, 6000.00, '2025-06-11 22:08:56', '2025-06-11 22:08:56'),
(178, 102, 4, 1, 7500.00, '2025-06-11 22:08:56', '2025-06-11 22:08:56'),
(179, 103, 3, 1, 5000.00, '2025-06-11 22:10:15', '2025-06-11 22:10:15'),
(180, 103, 6, 1, 8000.00, '2025-06-11 22:10:15', '2025-06-11 22:10:15'),
(181, 103, 34, 1, 5500.00, '2025-06-11 22:10:15', '2025-06-11 22:10:15'),
(182, 104, 7, 1, 13000.00, '2025-06-11 22:11:25', '2025-06-11 22:11:25'),
(183, 104, 3, 1, 5000.00, '2025-06-11 22:11:25', '2025-06-11 22:11:25'),
(184, 104, 4, 1, 7500.00, '2025-06-11 22:11:25', '2025-06-11 22:11:25'),
(185, 105, 6, 1, 8000.00, '2025-06-11 22:12:27', '2025-06-11 22:12:27'),
(186, 105, 35, 1, 14500.00, '2025-06-11 22:12:27', '2025-06-11 22:12:27'),
(187, 105, 2, 1, 6000.00, '2025-06-11 22:12:27', '2025-06-11 22:12:27'),
(188, 106, 36, 1, 20000.00, '2025-06-11 22:13:31', '2025-06-11 22:13:31'),
(189, 106, 3, 1, 5000.00, '2025-06-11 22:13:31', '2025-06-11 22:13:31'),
(190, 106, 6, 1, 8000.00, '2025-06-11 22:13:31', '2025-06-11 22:13:31'),
(191, 107, 3, 1, 5000.00, '2025-06-11 22:14:21', '2025-06-11 22:14:21'),
(192, 107, 6, 1, 8000.00, '2025-06-11 22:14:21', '2025-06-11 22:14:21'),
(193, 107, 4, 1, 7500.00, '2025-06-11 22:14:21', '2025-06-11 22:14:21'),
(194, 107, 7, 1, 13000.00, '2025-06-11 22:14:21', '2025-06-11 22:14:21'),
(195, 108, 34, 1, 5500.00, '2025-06-11 22:15:15', '2025-06-11 22:15:15'),
(196, 108, 3, 1, 5000.00, '2025-06-11 22:15:15', '2025-06-11 22:15:15'),
(197, 108, 35, 1, 14500.00, '2025-06-11 22:15:15', '2025-06-11 22:15:15'),
(198, 109, 2, 1, 6000.00, '2025-06-11 22:16:25', '2025-06-11 22:16:25'),
(199, 109, 4, 1, 7500.00, '2025-06-11 22:16:25', '2025-06-11 22:16:25'),
(200, 109, 6, 1, 8000.00, '2025-06-11 22:16:25', '2025-06-11 22:16:25'),
(201, 110, 36, 1, 20000.00, '2025-06-11 22:17:24', '2025-06-11 22:17:24'),
(202, 110, 7, 1, 13000.00, '2025-06-11 22:17:24', '2025-06-11 22:17:24'),
(203, 110, 6, 1, 8000.00, '2025-06-11 22:17:24', '2025-06-11 22:17:24'),
(204, 111, 2, 1, 6000.00, '2025-06-13 04:05:27', '2025-06-13 04:05:27'),
(205, 111, 4, 1, 7500.00, '2025-06-13 04:05:27', '2025-06-13 04:05:27'),
(206, 112, 6, 1, 8000.00, '2025-06-22 18:15:43', '2025-06-22 18:15:43'),
(207, 112, 4, 1, 7500.00, '2025-06-22 18:15:43', '2025-06-22 18:15:43'),
(208, 112, 36, 1, 20000.00, '2025-06-22 18:15:43', '2025-06-22 18:15:43'),
(209, 113, 4, 1, 7500.00, '2025-06-22 18:17:20', '2025-06-22 18:17:20'),
(210, 113, 3, 1, 5000.00, '2025-06-22 18:17:20', '2025-06-22 18:17:20'),
(211, 113, 7, 1, 13000.00, '2025-06-22 18:17:20', '2025-06-22 18:17:20'),
(212, 114, 6, 1, 8000.00, '2025-06-22 18:19:00', '2025-06-22 18:19:00'),
(213, 114, 34, 1, 5500.00, '2025-06-22 18:19:00', '2025-06-22 18:19:00'),
(214, 114, 3, 1, 5000.00, '2025-06-22 18:19:00', '2025-06-22 18:19:00'),
(215, 115, 6, 1, 8000.00, '2025-06-22 18:19:05', '2025-06-22 18:19:05'),
(216, 115, 34, 1, 5500.00, '2025-06-22 18:19:05', '2025-06-22 18:19:05'),
(217, 115, 3, 1, 5000.00, '2025-06-22 18:19:05', '2025-06-22 18:19:05'),
(218, 116, 2, 1, 6000.00, '2025-07-14 00:19:41', '2025-07-14 00:19:41'),
(219, 117, 3, 1, 5000.00, '2025-07-31 08:48:18', '2025-07-31 08:48:18'),
(220, 118, 2, 1, 6000.00, '2025-08-04 21:14:53', '2025-08-04 21:14:53');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `brief_description` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `old_price` decimal(8,2) NOT NULL,
  `SKU` varchar(255) NOT NULL,
  `stock_status` enum('instock','outstock') NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL DEFAULT 10,
  `image` varchar(255) NOT NULL,
  `images` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `brief_description`, `description`, `price`, `old_price`, `SKU`, `stock_status`, `quantity`, `image`, `images`, `created_at`, `updated_at`) VALUES
(2, 'Selada', 'Selada segar ', '<p>&nbsp;Selada segar dengan daun yang renyah dan rasa yang ringan, ideal untuk salad, burger, atau lalapan. Dipanen langsung dari kebun dan dikemas higienis untuk menjaga kualitas.&nbsp;</p>', 6000.00, 7000.00, 'SKU-4719', 'instock', 43, 'images/products/main_image/ybHjg6yUVPkU0OIiGObEuZVWhCVtxnE1FRI66cLD.png', 'images/products/alt_images/rXFyheE4POy86ole1MTQj8494liHiElfKWSIShsL.png', '2025-02-22 06:24:33', '2025-05-17 06:20:52'),
(3, 'Bayam', 'Bayam Segar', '<p>&nbsp;Bayam segar dengan daun lembut dan kaya nutrisi. Ideal untuk sayur bening, tumisan, atau smoothies sehat.&nbsp;</p>', 5000.00, 6000.00, 'SKU-3088', 'instock', 35, 'images/products/main_image/ylgpA2kPqPbApc3kQx2at07dY98aMu6JIw3Coo8A.png', 'images/products/alt_images/chgn38VanALi7qcT7salpWnN4LQfNwJ2c2rixkU8.png', '2025-02-22 06:24:33', '2025-06-03 23:15:31'),
(4, 'Wortel', 'Wortel segar', '<p>&nbsp;Wortel berkualitas dengan warna oranye cerah dan rasa manis alami. Cocok untuk sup, tumisan, atau camilan sehat.&nbsp;</p>', 7500.00, 8000.00, 'SKU-7413', 'instock', 17, 'images/products/main_image/wFdq4jP1sICGiv1BsVY0XE0I45C6lycNwuTlmXI4.png', 'images/products/alt_images/TWwt8igmQG34zYFmCudZwW5VUsW433OHkcDnnQfd.png', '2025-02-22 06:24:33', '2025-05-17 06:17:07'),
(6, 'Tomat', ' Tomat merah pilihan dengan daging buah padat dan rasa manis-asam.', '<p>&nbsp;Tomat merah pilihan dengan daging buah padat dan rasa manis-asam seimbang. Cocok untuk jus, sambal, atau pelengkap masakan Anda.&nbsp;</p>', 8000.00, 7000.00, 'SKU-1091', 'instock', 46, 'images/products/main_image/JWIKwTG994VajLa0F6gVgdSaQDNxOTmfhtqMhk46.jpg', 'images/products/alt_images/lKNL17DWxeWeX8XkIGKEuOGrZ3CLHplUG3lgmmGM.jpg', '2025-02-22 06:24:33', '2025-06-09 06:25:00'),
(7, 'Bawang merah', 'Bawang merah lokal dengan aroma kuat dan rasa gurih manis.', '<p>&nbsp;Bawang merah lokal dengan aroma kuat dan rasa gurih manis. Bumbu dapur wajib untuk berbagai jenis masakan.&nbsp;</p>', 13000.00, 13500.00, 'SKU-4469', 'instock', 56, 'images/products/main_image/NvAzy4vrUid2Z97wBX5guN3MuJ3B6OCx352yHMeo.jpg', 'images/products/alt_images/NAIgP2HUjCKtQSEQT6wVjmGMMQ9hGKnMOeJOgqJJ.jpg', '2025-02-22 06:24:33', '2025-06-09 06:25:28'),
(34, 'Sawi', 'Sawi ini adalah pilihan sawi terbaik', '<p>Nikmati kesegaran sawi hijau berkualitas langsung dari petani lokal. Cocok untuk berbagai masakan seperti tumis, sop, mie, atau pelengkap hidangan harian Anda.</p>', 5500.00, 6000.00, 'SKU-4500', 'instock', 40, 'images/products/main_image/5H1QFK61fr1HjG4yrvcxDcz04V76V44tkc3gxUdj.png', 'images/products/alt_images/F3MKNhu0hXQwFeysHyMlcpbHQUehwkWr1obqEon2.png', '2025-06-10 08:58:13', '2025-06-10 09:17:14'),
(35, 'Kentang', 'Kentang kualitas premium.', '<p>&nbsp;Kentang kualitas premium dengan tekstur empuk dan serbaguna. Bisa digunakan untuk goreng, rebus, atau mashed potato.</p>', 14500.00, 15000.00, 'SKU-6010', 'instock', 35, 'images/products/main_image/hGIW6IKQ57wxs4POd2Opy7RCf1niwN6ZtFYONvC6.png', 'images/products/alt_images/ozZuqy7nEtRNfJfmeusaydeNtJumB6Whm2Siw9nj.png', '2025-06-10 09:07:38', '2025-06-10 09:12:03'),
(36, 'Brokoli', 'Brokoli Segar dan Kaya Akan Vitamin', '<p>&nbsp;Brokoli adalah sayuran hijau yang kaya akan vitamin, serat, dan antioksidan.&nbsp;</p>', 20000.00, 21000.00, 'SKU-5000', 'instock', 40, 'images/products/main_image/HpaosdxIjhqNFk74Nb43VuZMWmbUnQtqJq1Wbdrz.png', 'images/products/alt_images/RwyxwP7vmvlOEyShbNz33nnm1h7qrE13Ui2bDK0A.png', '2025-06-11 21:48:56', '2025-06-11 21:48:56');

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `stripe_id` varchar(255) NOT NULL,
  `stripe_status` varchar(255) NOT NULL,
  `stripe_price` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `ends_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscription_items`
--

CREATE TABLE `subscription_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subscription_id` bigint(20) UNSIGNED NOT NULL,
  `stripe_id` varchar(255) NOT NULL,
  `stripe_product` varchar(255) NOT NULL,
  `stripe_price` varchar(255) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `stripe_id` varchar(255) DEFAULT NULL,
  `pm_type` varchar(255) DEFAULT NULL,
  `pm_last_four` varchar(4) DEFAULT NULL,
  `trial_ends_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `is_admin`, `remember_token`, `created_at`, `updated_at`, `stripe_id`, `pm_type`, `pm_last_four`, `trial_ends_at`) VALUES
(1, 'Admin', 'admin@gmail.com', '2025-02-22 06:24:36', '$2y$10$IyLSZDBVbHPQzbLX6.oi6eqdJkgBXLvLu794f/BsQ68TN0FIFriyS', 1, 'oBJs1eKaoSB1fKl0b8ziFrU9wmZGdVaY3NUsiK5AHtgTzYwvZiZ3Ztnyfj4K', '2025-02-22 06:24:36', '2025-02-22 09:06:20', NULL, NULL, NULL, NULL),
(5, 'Hafid Roihan', 'hafidroihan@gmail.com', NULL, '$2y$10$NBeCW8LNDlszMyGGNHJjl.XI.hmd1cLpr05jOcQh26rgoiUjfme5u', 0, NULL, '2025-06-01 20:10:57', '2025-06-01 20:10:57', NULL, NULL, NULL, NULL),
(6, 'Hafid', 'hafid@gmail.com', NULL, '$2y$10$yMvsT6i7nfMQvlpQiIzdOOYBkSIU.ymze9YNrNPAZ9ljZrws4Yks2', 1, NULL, '2025-06-10 09:39:29', '2025-06-10 09:39:29', NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `billing_details`
--
ALTER TABLE `billing_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `billing_details_user_id_foreign` (`user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_name_unique` (`name`),
  ADD UNIQUE KEY `categories_slug_unique` (`slug`);

--
-- Indexes for table `category_product`
--
ALTER TABLE `category_product`
  ADD PRIMARY KEY (`category_id`,`product_id`),
  ADD KEY `category_product_product_id_foreign` (`product_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_user_id_foreign` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`),
  ADD KEY `order_items_product_id_foreign` (`product_id`);

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
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subscriptions_stripe_id_unique` (`stripe_id`),
  ADD KEY `subscriptions_user_id_stripe_status_index` (`user_id`,`stripe_status`);

--
-- Indexes for table `subscription_items`
--
ALTER TABLE `subscription_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subscription_items_subscription_id_stripe_price_unique` (`subscription_id`,`stripe_price`),
  ADD UNIQUE KEY `subscription_items_stripe_id_unique` (`stripe_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_stripe_id_index` (`stripe_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `billing_details`
--
ALTER TABLE `billing_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=119;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=221;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscription_items`
--
ALTER TABLE `subscription_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `billing_details`
--
ALTER TABLE `billing_details`
  ADD CONSTRAINT `billing_details_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `category_product`
--
ALTER TABLE `category_product`
  ADD CONSTRAINT `category_product_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `category_product_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
