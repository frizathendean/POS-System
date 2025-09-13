-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 22 Apr 2025 pada 17.08
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `president_store_pos`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','cashier') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `employees`
--

INSERT INTO `employees` (`id`, `employee_id`, `name`, `password`, `role`) VALUES
(1, 'ADM225', 'Admin User', '$2y$10$KsqVdNOnFa3ndYBlrOTd.O3L3w2QJRluVgJ4agDcwMontJcXr4gBm', 'admin'),
(2, 'CSR543', 'Cashier User', '$2y$10$AOg2GFL9tJ/2b2yOqN7RnO7zjiA3ftczH3M6X5eHhQRrWLX7Hdjaq', 'cashier');

-- --------------------------------------------------------

--
-- Struktur dari tabel `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `product_id` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) NOT NULL,
  `subcategory` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `cost` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `products`
--

INSERT INTO `products` (`id`, `product_id`, `name`, `description`, `category`, `subcategory`, `price`, `cost`, `stock_quantity`, `created_at`, `updated_at`) VALUES
(1, 'TSN-001', 'PU T-Shirt Navy', NULL, 'clothing', 't-shirt', 150000.00, 90000.00, 8, '2025-04-17 04:37:18', '2025-04-17 06:17:21'),
(2, 'TSW-002', 'PU T-Shirt White', '', 'clothing', 't-shirt', 150000.00, 90000.00, 10, '2025-04-17 04:37:18', '2025-04-19 07:41:55'),
(3, 'TSM-003', 'PU T-Shirt Maroon', NULL, 'clothing', 't-shirt', 150000.00, 90000.00, 7, '2025-04-17 04:37:18', '2025-04-18 17:47:05'),
(4, 'TSB-004', 'PU T-Shirt Black', 'blablabla', 'clothing', 't-shirt', 150000.00, 90000.00, 10, '2025-04-17 04:37:18', '2025-04-20 13:40:32'),
(5, 'HDN-001', 'PU Hoodie Navy', NULL, 'clothing', 'hoodie', 300000.00, 180000.00, 9, '2025-04-17 04:37:18', '2025-04-18 17:15:46'),
(6, 'HDW-002', 'PU Hoodie White', NULL, 'clothing', 'hoodie', 300000.00, 180000.00, 10, '2025-04-17 04:37:18', '2025-04-17 04:37:18'),
(7, 'HDM-003', 'PU Hoodie Maroon', NULL, 'clothing', 'hoodie', 300000.00, 180000.00, 10, '2025-04-17 04:37:18', '2025-04-17 04:37:18'),
(8, 'HDB-004', 'PU Hoodie Black', '', 'clothing', 'hoodie', 300000.00, 180000.00, 10, '2025-04-17 04:37:18', '2025-04-22 08:39:26'),
(9, 'HDP-005', 'PU Hoodie Pink', NULL, 'clothing', 'hoodie', 300000.00, 180000.00, 0, '2025-04-17 04:37:18', '2025-04-22 10:41:32'),
(10, 'CPN-001', 'PU Cap Navy', NULL, 'accessories', 'cap', 70000.00, 40000.00, 13, '2025-04-17 04:37:18', '2025-04-18 17:08:21'),
(11, 'CPW-002', 'PU Cap White', NULL, 'accessories', 'cap', 70000.00, 40000.00, 14, '2025-04-17 04:37:18', '2025-04-18 17:15:46'),
(12, 'CPM-003', 'PU Cap Maroon', NULL, 'accessories', 'cap', 70000.00, 40000.00, 14, '2025-04-17 04:37:18', '2025-04-19 07:28:35'),
(13, 'STC-001', 'PU Sticker', NULL, 'accessories', 'sticker', 20000.00, 5000.00, 39, '2025-04-17 04:37:18', '2025-04-22 10:41:32'),
(14, 'TBN-001', 'PU Tote Bag Navy', NULL, 'accessories', 'tote bag', 50000.00, 25000.00, 13, '2025-04-17 04:37:18', '2025-04-18 17:08:21'),
(15, 'TBW-002', 'PU Tote Bag White', NULL, 'accessories', 'tote bag', 50000.00, 25000.00, 13, '2025-04-17 04:37:18', '2025-04-22 10:55:16'),
(16, 'TBM-003', 'PU Tote Bag Maroon', NULL, 'accessories', 'tote bag', 50000.00, 25000.00, 15, '2025-04-17 04:37:18', '2025-04-17 04:37:18'),
(17, 'TMW/TMB-001', 'PU Tumbler', NULL, 'accessories', 'tumbler', 100000.00, 60000.00, 10, '2025-04-17 04:37:18', '2025-04-17 04:37:18'),
(18, 'MUG-001', 'PU Mug', NULL, 'accessories', 'mug', 80000.00, 40000.00, 10, '2025-04-17 04:37:18', '2025-04-17 04:37:18'),
(19, 'LNN-001', 'PU Lanyard Navy', NULL, 'accessories', 'lanyard', 30000.00, 10000.00, 10, '2025-04-17 04:37:18', '2025-04-18 14:48:47'),
(20, 'LNW-002', 'PU Lanyard White', NULL, 'accessories', 'lanyard', 30000.00, 10000.00, 17, '2025-04-17 04:37:18', '2025-04-22 10:37:17'),
(21, 'LNM-003', 'PU Lanyard Maroon', NULL, 'accessories', 'lanyard', 30000.00, 10000.00, 20, '2025-04-17 04:37:18', '2025-04-17 04:37:18'),
(22, 'KYC-001', 'PU Keychain', NULL, 'accessories', 'keychain', 30000.00, 10000.00, 30, '2025-04-17 04:37:18', '2025-04-17 04:37:18');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `invoice_no` varchar(20) NOT NULL,
  `cashier_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `status` enum('paid','unpaid') NOT NULL DEFAULT 'unpaid',
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transactions`
--

INSERT INTO `transactions` (`id`, `invoice_no`, `cashier_id`, `customer_id`, `status`, `total_amount`, `tax_amount`, `discount_amount`, `payment_method`, `notes`, `created_at`) VALUES
(1, 'INV-20250417-9A3A', 2, NULL, 'paid', 600000.00, 60000.00, 0.00, 'cash', '', '2025-04-17 06:15:53'),
(2, 'INV-20250417-1961', 2, NULL, 'paid', 750000.00, 75000.00, 0.00, 'debit_card', 'no notes', '2025-04-17 06:17:21'),
(3, 'INV-20250417-E6ED', 2, NULL, 'paid', 450000.00, 45000.00, 0.00, 'credit_card', 'good t-shirt', '2025-04-17 06:42:53'),
(4, 'INV-20250418-5AF2', 2, NULL, 'paid', 3300000.00, 330000.00, 0.00, 'debit_card', '', '2025-04-18 14:48:47'),
(5, 'INV-20250418-329F', 2, NULL, 'paid', 600000.00, 60000.00, 0.00, 'cash', '', '2025-04-18 17:08:06'),
(6, 'INV-20250418-194E', 2, NULL, 'paid', 600000.00, 60000.00, 0.00, 'cash', '', '2025-04-18 17:08:21'),
(7, 'INV-20250418-FC40', 2, NULL, 'paid', 420000.00, 42000.00, 0.00, 'cash', '', '2025-04-18 17:15:46'),
(8, 'INV-20250418-7D59', 2, NULL, 'paid', 150000.00, 15000.00, 0.00, 'cash', '', '2025-04-18 17:39:25'),
(9, 'INV-20250418-79BD', 2, NULL, 'paid', 150000.00, 15000.00, 0.00, 'cash', '', '2025-04-18 17:39:33'),
(10, 'INV-20250418-A09D', 2, NULL, 'paid', 150000.00, 15000.00, 0.00, 'cash', '', '2025-04-18 17:47:05'),
(11, 'INV-20250418-3791', 2, NULL, 'paid', 20000.00, 2000.00, 0.00, 'cash', '', '2025-04-18 18:10:20'),
(12, 'INV-20250419-12A8', 1, NULL, 'paid', 70000.00, 7000.00, 0.00, 'cash', '', '2025-04-19 07:28:35'),
(13, 'INV-20250419-F9B4', 1, NULL, 'paid', 100000.00, 10000.00, 0.00, 'cash', '', '2025-04-19 07:40:50'),
(14, 'INV-20250419-C72E', 1, NULL, 'paid', 600000.00, 60000.00, 0.00, 'cash', '', '2025-04-19 07:52:04'),
(15, 'INV-20250419-6BFF', 2, NULL, 'paid', 300000.00, 30000.00, 0.00, 'cash', '', '2025-04-19 18:18:32'),
(16, 'INV-20250419-83BE', 2, NULL, 'paid', 150000.00, 15000.00, 0.00, 'cash', '', '2025-04-19 18:27:59'),
(29, 'INV-20250422-E3EC', 2, NULL, 'paid', 30000.00, 3000.00, 0.00, 'cash', '', '2025-04-22 10:37:17'),
(30, 'INV-20250422-6EF1', 2, NULL, 'paid', 2500000.00, 250000.00, 0.00, 'cash', '', '2025-04-22 10:41:32'),
(31, 'INV-20250422-E19F', 2, NULL, 'paid', 50000.00, 5000.00, 0.00, 'credit_card', '', '2025-04-22 10:55:16');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaction_items`
--

CREATE TABLE `transaction_items` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transaction_items`
--

INSERT INTO `transaction_items` (`id`, `transaction_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 9, 2, 300000.00),
(2, 2, 2, 3, 150000.00),
(3, 2, 1, 2, 150000.00),
(4, 3, 2, 3, 150000.00),
(5, 4, 8, 10, 300000.00),
(6, 4, 19, 10, 30000.00),
(7, 5, 8, 1, 300000.00),
(8, 5, 10, 1, 70000.00),
(9, 5, 20, 1, 30000.00),
(10, 5, 4, 1, 150000.00),
(11, 5, 14, 1, 50000.00),
(12, 6, 8, 1, 300000.00),
(13, 6, 10, 1, 70000.00),
(14, 6, 20, 1, 30000.00),
(15, 6, 4, 1, 150000.00),
(16, 6, 14, 1, 50000.00),
(17, 7, 11, 1, 70000.00),
(18, 7, 5, 1, 300000.00),
(19, 7, 15, 1, 50000.00),
(20, 8, 3, 1, 150000.00),
(21, 9, 3, 1, 150000.00),
(22, 10, 3, 1, 150000.00),
(23, 11, 13, 1, 20000.00),
(24, 12, 12, 1, 70000.00),
(25, 13, 13, 5, 20000.00),
(26, 14, 4, 4, 150000.00),
(27, 15, 8, 1, 300000.00),
(28, 16, 4, 1, 150000.00),
(29, 29, 20, 1, 30000.00),
(30, 30, 9, 8, 300000.00),
(31, 30, 13, 5, 20000.00),
(32, 31, 15, 1, 50000.00);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `role` enum('admin','cashier') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `name`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User', 'admin', '2025-04-17 04:37:17'),
(2, 'cashier', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Cashier User', 'cashier', '2025-04-17 04:37:17');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_id` (`employee_id`);

--
-- Indeks untuk tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_id` (`product_id`);

--
-- Indeks untuk tabel `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_no` (`invoice_no`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `transactions_ibfk_1` (`cashier_id`);

--
-- Indeks untuk tabel `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT untuk tabel `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT untuk tabel `transaction_items`
--
ALTER TABLE `transaction_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`cashier_id`) REFERENCES `employees` (`id`);

--
-- Ketidakleluasaan untuk tabel `transaction_items`
--
ALTER TABLE `transaction_items`
  ADD CONSTRAINT `transaction_items_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaction_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

