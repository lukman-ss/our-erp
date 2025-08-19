-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 19, 2025 at 03:36 AM
-- Server version: 8.4.5
-- PHP Version: 8.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `our-erp`
--
CREATE DATABASE IF NOT EXISTS `our-erp` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `our-erp`;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('our-erp-cache-F8UehcGzHls432GB', 'a:1:{s:11:\"valid_until\";i:1755519592;}', 1756729192);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `email`, `phone`, `address`, `created_at`, `updated_at`) VALUES
('0f6df418-2108-461a-b53f-ebf28964fd78', 'PT Sumber Makmur Jaya', 'contact@sumbermakmur.co.id', '+62-812-3456-7890', 'Jl. Raya Industri No. 88, Bekasi', '2025-08-18 20:13:25', '2025-08-18 20:13:25');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
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
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `materials`
--

CREATE TABLE `materials` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cost_price` decimal(16,2) NOT NULL DEFAULT '0.00',
  `stock` decimal(16,2) NOT NULL DEFAULT '0.00',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `materials`
--

INSERT INTO `materials` (`id`, `code`, `name`, `unit`, `cost_price`, `stock`, `description`, `created_at`, `updated_at`) VALUES
('0490af0a-0cf6-42af-9e66-39057fb9c105', 'MTL-003', 'Engine Oil Premium', 'liter', 85000.00, 120.00, 'Premium synthetic engine oil SAE 5W-30 with extended life', '2025-08-18 07:02:06', '2025-08-18 07:04:21'),
('b56b4a38-b38c-4d16-ae63-7eec18f7aefd', 'MTL-001', 'Steel Pipe', 'pcs', 120000.00, 50.00, 'High quality steel pipe 1 inch', '2025-08-18 06:55:49', '2025-08-18 06:55:49'),
('ba94b13a-cd47-4afc-8b2a-31cdb40c6e1e', 'MTL-002', 'Copper Wire', 'meter', 25000.00, 200.00, 'High conductivity copper wire for electrical usage', '2025-08-18 07:01:52', '2025-08-18 07:01:52');

-- --------------------------------------------------------

--
-- Table structure for table `material_batches`
--

CREATE TABLE `material_batches` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `material_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty_initial` decimal(16,4) NOT NULL,
  `qty_remaining` decimal(16,4) NOT NULL,
  `unit_cost` decimal(16,4) NOT NULL,
  `received_at` timestamp NOT NULL,
  `purchase_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `purchase_material_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `material_batches`
--

INSERT INTO `material_batches` (`id`, `material_id`, `qty_initial`, `qty_remaining`, `unit_cost`, `received_at`, `purchase_id`, `purchase_material_id`, `created_at`, `updated_at`) VALUES
('0a1f2ed0-b8bb-4c2c-b499-8eadb9f87c89', 'ba94b13a-cd47-4afc-8b2a-31cdb40c6e1e', 5.0000, 5.0000, 22000.0000, '2025-08-17 17:00:00', '923edb59-091d-4306-8de7-7388bfcc48ee', '5037b9cd-fc5b-479a-8a33-0b8516476bd9', '2025-08-18 09:31:53', '2025-08-18 09:31:53'),
('af51f452-7bb3-419d-8b26-e5e9e6088c48', '0490af0a-0cf6-42af-9e66-39057fb9c105', 10.0000, 10.0000, 15000.0000, '2025-08-17 17:00:00', '923edb59-091d-4306-8de7-7388bfcc48ee', '14046b73-1a02-44ea-ad77-420ffe7c23a1', '2025-08-18 09:31:53', '2025-08-18 09:31:53');

-- --------------------------------------------------------

--
-- Table structure for table `material_moves`
--

CREATE TABLE `material_moves` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `material_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direction` enum('in','out') COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` decimal(16,4) NOT NULL,
  `unit_cost` decimal(16,4) NOT NULL,
  `moved_at` timestamp NOT NULL,
  `ref_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ref_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_08_18_115821_change_users_id_to_uuid', 2),
(5, '2025_08_18_122155_create_customers_table', 3),
(6, '2025_08_18_133222_create_materials_table', 4),
(7, '2025_08_18_133222_create_products_table', 4),
(8, '2025_08_18_133749_create_product_materials_table', 4),
(9, '2025_08_18_150123_create_material_batches_table', 5),
(10, '2025_08_18_150123_create_material_moves_table', 5),
(11, '2025_08_18_152025_purchases', 5),
(12, '2025_08_18_152438_purchase_materials', 5),
(13, '2025_08_18_152528_suppliers', 5),
(14, '2025_08_18_155733_rename_purchase_item_id_to_purchase_material_id_in_material_batches', 6),
(15, '2025_08_18_165009_create_productions_table', 7),
(16, '2025_08_18_165028_create_production_products_table', 7),
(17, '2025_08_18_165058_create_production_materials_table', 7),
(18, '2025_08_18_175205_sales', 8),
(19, '2025_08_18_175218_sale_product', 8);

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
-- Table structure for table `productions`
--

CREATE TABLE `productions` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty_planned` decimal(16,4) NOT NULL,
  `qty_produced` decimal(16,4) NOT NULL DEFAULT '0.0000',
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `scheduled_at` timestamp NULL DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `finished_at` timestamp NULL DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `productions`
--

INSERT INTO `productions` (`id`, `code`, `product_id`, `qty_planned`, `qty_produced`, `status`, `scheduled_at`, `started_at`, `finished_at`, `notes`, `created_at`, `updated_at`) VALUES
('10d38cce-139c-4557-8ee3-721a4459a88c', 'MO-2025-0002', 'bedbbb8d-7796-4569-af03-3d0ff98d4b2e', 10.0000, 0.0000, 'draft', '2025-08-20 01:00:00', NULL, NULL, 'Produksi engine assembly dengan 1 material', '2025-08-18 10:34:17', '2025-08-18 10:34:17');

-- --------------------------------------------------------

--
-- Table structure for table `production_materials`
--

CREATE TABLE `production_materials` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `production_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `material_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty_required` decimal(16,4) NOT NULL,
  `qty_issued` decimal(16,4) NOT NULL DEFAULT '0.0000',
  `unit_cost` decimal(16,4) DEFAULT NULL,
  `total_cost` decimal(16,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `production_materials`
--

INSERT INTO `production_materials` (`id`, `production_id`, `material_id`, `qty_required`, `qty_issued`, `unit_cost`, `total_cost`, `created_at`, `updated_at`) VALUES
('f116c08e-e1aa-4884-a511-699eed84a22a', '10d38cce-139c-4557-8ee3-721a4459a88c', 'ba94b13a-cd47-4afc-8b2a-31cdb40c6e1e', 6.0000, 0.0000, 26000.0000, NULL, '2025-08-18 10:34:17', '2025-08-18 10:34:17');

-- --------------------------------------------------------

--
-- Table structure for table `production_products`
--

CREATE TABLE `production_products` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `production_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` decimal(16,4) NOT NULL,
  `unit_cost` decimal(16,4) DEFAULT NULL,
  `total_cost` decimal(16,2) DEFAULT NULL,
  `produced_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `production_products`
--

INSERT INTO `production_products` (`id`, `production_id`, `product_id`, `qty`, `unit_cost`, `total_cost`, `produced_at`, `created_at`, `updated_at`) VALUES
('6799fbc1-5cbd-4cd2-b596-b2cea75902d6', '10d38cce-139c-4557-8ee3-721a4459a88c', 'bedbbb8d-7796-4569-af03-3d0ff98d4b2e', 10.0000, 0.0000, 0.00, NULL, '2025-08-18 10:34:17', '2025-08-18 10:34:17');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sell_price` decimal(16,2) NOT NULL DEFAULT '0.00',
  `stock` decimal(16,2) NOT NULL DEFAULT '0.00',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `sku`, `name`, `unit`, `sell_price`, `stock`, `description`, `created_at`, `updated_at`) VALUES
('bedbbb8d-7796-4569-af03-3d0ff98d4b2e', 'PRD-003', 'Electrical Service Kit Plus', 'set', 295000.00, 19.00, 'Basic electrical kit for light repairs', '2025-08-18 08:44:21', '2025-08-18 20:23:20');

-- --------------------------------------------------------

--
-- Table structure for table `product_materials`
--

CREATE TABLE `product_materials` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `material_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` decimal(16,4) NOT NULL DEFAULT '0.0000',
  `cost_price` decimal(16,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_materials`
--

INSERT INTO `product_materials` (`id`, `product_id`, `material_id`, `qty`, `cost_price`, `created_at`, `updated_at`) VALUES
('5b661161-e44e-48f7-846e-f10e79067419', 'bedbbb8d-7796-4569-af03-3d0ff98d4b2e', 'ba94b13a-cd47-4afc-8b2a-31cdb40c6e1e', 6.0000, 26000.00, '2025-08-18 08:44:21', '2025-08-18 08:45:13');

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `purchase_date` date NOT NULL,
  `total_amount` decimal(16,2) NOT NULL DEFAULT '0.00',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchases`
--

INSERT INTO `purchases` (`id`, `code`, `supplier_id`, `purchase_date`, `total_amount`, `status`, `created_at`, `updated_at`) VALUES
('923edb59-091d-4306-8de7-7388bfcc48ee', 'PO-2025-001', 'fdebe9ac-3f69-4f3b-9872-1ca8c14c0d39', '2025-08-18', 260000.00, 'pending', '2025-08-18 09:31:53', '2025-08-18 09:31:53');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_materials`
--

CREATE TABLE `purchase_materials` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `purchase_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `material_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` decimal(16,4) NOT NULL,
  `unit_cost` decimal(16,4) NOT NULL,
  `total_cost` decimal(16,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_materials`
--

INSERT INTO `purchase_materials` (`id`, `purchase_id`, `material_id`, `qty`, `unit_cost`, `total_cost`, `created_at`, `updated_at`) VALUES
('14046b73-1a02-44ea-ad77-420ffe7c23a1', '923edb59-091d-4306-8de7-7388bfcc48ee', '0490af0a-0cf6-42af-9e66-39057fb9c105', 10.0000, 15000.0000, 150000.00, '2025-08-18 09:31:53', '2025-08-18 09:31:53'),
('5037b9cd-fc5b-479a-8a33-0b8516476bd9', '923edb59-091d-4306-8de7-7388bfcc48ee', 'ba94b13a-cd47-4afc-8b2a-31cdb40c6e1e', 5.0000, 22000.0000, 110000.00, '2025-08-18 09:31:53', '2025-08-18 09:31:53');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sale_date` date NOT NULL,
  `total_amount` decimal(16,2) NOT NULL DEFAULT '0.00',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `code`, `customer_id`, `sale_date`, `total_amount`, `status`, `created_at`, `updated_at`) VALUES
('504472ea-c979-47fc-a151-26370c01773c', 'SO-2025-00001', NULL, '2025-08-20', 526000.00, 'confirmed', '2025-08-18 10:57:20', '2025-08-18 10:57:20'),
('54b9c79b-3d8f-4c7a-8f44-b23160269120', 'SO-2025-00003', '0f6df418-2108-461a-b53f-ebf28964fd78', '2025-08-19', 526000.00, 'confirmed', '2025-08-18 20:23:20', '2025-08-18 20:23:20'),
('57476ee9-b65c-4430-a6b4-56316d68f5ed', 'SO-2025-00009', '0f6df418-2108-461a-b53f-ebf28964fd78', '2025-08-19', 526000.00, 'confirmed', '2025-08-18 20:14:03', '2025-08-18 20:14:03');

-- --------------------------------------------------------

--
-- Table structure for table `sale_products`
--

CREATE TABLE `sale_products` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sale_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` decimal(16,4) NOT NULL,
  `unit_price` decimal(16,2) NOT NULL,
  `discount_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(16,2) NOT NULL DEFAULT '0.00',
  `line_total` decimal(16,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sale_products`
--

INSERT INTO `sale_products` (`id`, `sale_id`, `product_id`, `qty`, `unit_price`, `discount_percentage`, `discount_amount`, `line_total`, `created_at`, `updated_at`) VALUES
('696c17fb-5510-4e1c-bb56-59cd8658a948', '504472ea-c979-47fc-a151-26370c01773c', 'bedbbb8d-7796-4569-af03-3d0ff98d4b2e', 2.0000, 295000.00, 10.00, 5000.00, 526000.00, '2025-08-18 10:57:20', '2025-08-18 10:57:20'),
('98dc8946-91fe-49cf-a46c-b468b33a0c75', '57476ee9-b65c-4430-a6b4-56316d68f5ed', 'bedbbb8d-7796-4569-af03-3d0ff98d4b2e', 2.0000, 295000.00, 10.00, 5000.00, 526000.00, '2025-08-18 20:14:03', '2025-08-18 20:14:03'),
('f296a8d1-0a41-42a8-84b0-c98a38452595', '54b9c79b-3d8f-4c7a-8f44-b23160269120', 'bedbbb8d-7796-4569-af03-3d0ff98d4b2e', 2.0000, 295000.00, 10.00, 5000.00, 526000.00, '2025-08-18 20:23:20', '2025-08-18 20:23:20');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('hYjrUWMPhqLqdrXwrKVvsx5rvtDCBKrg64Gco4O7', NULL, '127.0.0.1', 'PostmanRuntime/7.44.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiSlRSMTdvbmRkWVRYeU5FU2FXNjY3YkdndnNNdk5DSzFjRXlQUlliVSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1755572876),
('iCRnVkN8TfGquqBKDMF1T2Sgflc1vplXZPR2MwLa', NULL, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36 OPR/120.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoid1hkT2pmSkZXVXZGdkRCaTdPSEU5VWpGVTRKZTZkdnNxQlVjRTBFTiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzg6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9wZW5qdWFsYW4vcHJvZHVrIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1755574140);

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `code`, `name`, `contact_name`, `phone`, `email`, `address`, `city`, `status`, `created_at`, `updated_at`) VALUES
('fdebe9ac-3f69-4f3b-9872-1ca8c14c0d39', 'SUP-IT5G3N', 'PT Sumber Makmur Abadi', 'Andi Wijaya', '+62-877-6543-2100', 'andi@sumbermakmur.co.id', 'Jl. Baru Industri No. 12, Cikarang', 'Cikarang', 'inactive', '2025-08-18 08:37:23', '2025-08-18 08:39:24');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
('f8665e2c-506f-48f9-91ee-342bf5f155de', 'Lukman', 'lukman@lukman.com', NULL, '$2y$12$ZaXpfZIVVeZG2YznHVMJwuTtFNVdMLgZmqGswwmGcIezuVeYoqi06', NULL, '2025-08-18 05:16:39', '2025-08-18 05:16:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customers_email_unique` (`email`);

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
-- Indexes for table `materials`
--
ALTER TABLE `materials`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `materials_code_unique` (`code`);

--
-- Indexes for table `material_batches`
--
ALTER TABLE `material_batches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `material_batches_material_id_index` (`material_id`),
  ADD KEY `material_batches_received_at_index` (`received_at`),
  ADD KEY `material_batches_purchase_id_index` (`purchase_id`),
  ADD KEY `material_batches_purchase_item_id_index` (`purchase_material_id`);

--
-- Indexes for table `material_moves`
--
ALTER TABLE `material_moves`
  ADD PRIMARY KEY (`id`),
  ADD KEY `material_moves_material_id_index` (`material_id`),
  ADD KEY `material_moves_batch_id_index` (`batch_id`),
  ADD KEY `material_moves_direction_index` (`direction`),
  ADD KEY `material_moves_moved_at_index` (`moved_at`),
  ADD KEY `material_moves_ref_type_index` (`ref_type`),
  ADD KEY `material_moves_ref_id_index` (`ref_id`);

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
-- Indexes for table `productions`
--
ALTER TABLE `productions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `productions_code_unique` (`code`),
  ADD KEY `productions_product_id_index` (`product_id`),
  ADD KEY `productions_scheduled_at_index` (`scheduled_at`),
  ADD KEY `productions_started_at_index` (`started_at`),
  ADD KEY `productions_finished_at_index` (`finished_at`);

--
-- Indexes for table `production_materials`
--
ALTER TABLE `production_materials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `production_materials_production_id_material_id_index` (`production_id`,`material_id`),
  ADD KEY `production_materials_production_id_index` (`production_id`),
  ADD KEY `production_materials_material_id_index` (`material_id`);

--
-- Indexes for table `production_products`
--
ALTER TABLE `production_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `production_products_production_id_product_id_index` (`production_id`,`product_id`),
  ADD KEY `production_products_production_id_index` (`production_id`),
  ADD KEY `production_products_product_id_index` (`product_id`),
  ADD KEY `production_products_produced_at_index` (`produced_at`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_sku_unique` (`sku`);

--
-- Indexes for table `product_materials`
--
ALTER TABLE `product_materials`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_materials_product_id_material_id_unique` (`product_id`,`material_id`),
  ADD KEY `product_materials_material_id_foreign` (`material_id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchases_code_unique` (`code`),
  ADD KEY `purchases_supplier_id_index` (`supplier_id`);

--
-- Indexes for table `purchase_materials`
--
ALTER TABLE `purchase_materials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_materials_purchase_id_index` (`purchase_id`),
  ADD KEY `purchase_materials_material_id_index` (`material_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sales_code_unique` (`code`),
  ADD KEY `sales_customer_id_index` (`customer_id`);

--
-- Indexes for table `sale_products`
--
ALTER TABLE `sale_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_products_sale_id_index` (`sale_id`),
  ADD KEY `sale_products_product_id_index` (`product_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `suppliers_code_unique` (`code`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `material_batches`
--
ALTER TABLE `material_batches`
  ADD CONSTRAINT `material_batches_material_id_foreign` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `material_moves`
--
ALTER TABLE `material_moves`
  ADD CONSTRAINT `material_moves_batch_id_foreign` FOREIGN KEY (`batch_id`) REFERENCES `material_batches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `material_moves_material_id_foreign` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `productions`
--
ALTER TABLE `productions`
  ADD CONSTRAINT `productions_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `production_materials`
--
ALTER TABLE `production_materials`
  ADD CONSTRAINT `production_materials_material_id_foreign` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `production_materials_production_id_foreign` FOREIGN KEY (`production_id`) REFERENCES `productions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `production_products`
--
ALTER TABLE `production_products`
  ADD CONSTRAINT `production_products_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `production_products_production_id_foreign` FOREIGN KEY (`production_id`) REFERENCES `productions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_materials`
--
ALTER TABLE `product_materials`
  ADD CONSTRAINT `product_materials_material_id_foreign` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_materials_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `purchase_materials`
--
ALTER TABLE `purchase_materials`
  ADD CONSTRAINT `purchase_materials_material_id_foreign` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_materials_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sale_products`
--
ALTER TABLE `sale_products`
  ADD CONSTRAINT `sale_products_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_products_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
