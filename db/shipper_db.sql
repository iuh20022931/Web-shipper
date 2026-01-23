-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th1 23, 2026 lúc 07:02 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `shipper_db`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_code` varchar(20) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `shipper_id` int(11) DEFAULT NULL,
  `pickup_address` text DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `receiver_name` varchar(100) NOT NULL,
  `receiver_phone` varchar(20) NOT NULL,
  `delivery_address` text NOT NULL,
  `package_type` varchar(50) DEFAULT 'other',
  `service_type` varchar(50) NOT NULL DEFAULT 'standard',
  `weight` decimal(10,2) DEFAULT 0.00,
  `cod_amount` decimal(15,2) DEFAULT 0.00,
  `shipping_fee` decimal(15,2) DEFAULT 0.00,
  `pickup_time` varchar(100) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `shipper_note` text DEFAULT NULL,
  `pod_image` varchar(255) DEFAULT NULL,
  `status` enum('pending','shipping','completed','cancelled') DEFAULT 'pending',
  `rating` int(11) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `order_code`, `user_id`, `shipper_id`, `pickup_address`, `name`, `phone`, `receiver_name`, `receiver_phone`, `delivery_address`, `package_type`, `service_type`, `weight`, `cod_amount`, `shipping_fee`, `pickup_time`, `note`, `shipper_note`, `pod_image`, `status`, `rating`, `feedback`, `created_at`) VALUES
(1, 'FAST-000001', 2, NULL, '123 Lê Lợi Quận 1', 'Nguyễn Văn A', '0987654321', '', '', '45 Nguyễn Huệ Quận 1', 'document', 'standard', 1.00, 0.00, 30000.00, '', '', NULL, NULL, 'cancelled', NULL, NULL, '2026-01-22 13:44:23'),
(2, 'FAST-BB02CD', 2, 3, '123 Lê Lợi Quận 1', 'Nguyễn Văn A', '0987654321', 'Nguyễn Văn B', '0987654322', '45 Nguyễn Huệ Quận 1', 'document', 'standard', 1.00, 0.00, 30000.00, '', '', NULL, NULL, 'completed', NULL, NULL, '2026-01-22 16:31:32'),
(3, 'FAST-9B0EC1', 2, 3, '123 Lê Lợi Quận 1', 'Nguyễn Văn A', '0987654321', 'Nguyễn Văn B', '0987654322', '45 Nguyễn Huệ Quận 1', 'document', 'standard', 2.00, 25000.00, 30000.00, '', '', '', NULL, 'cancelled', NULL, NULL, '2026-01-23 00:42:34'),
(4, 'FAST-0287AE', 2, 3, '123 Lê Lợi Quận 1', 'Nguyễn Văn A', '0987654321', 'Nguyễn Văn B', '0987654322', '45 Nguyễn Huệ Quận 12', 'document', 'standard', 2.00, 0.00, 45000.00, '', '', '', NULL, 'completed', NULL, NULL, '2026-01-23 01:38:20');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_logs`
--

CREATE TABLE `order_logs` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `old_status` varchar(50) NOT NULL,
  `new_status` varchar(50) NOT NULL,
  `changed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order_logs`
--

INSERT INTO `order_logs` (`id`, `order_id`, `user_id`, `old_status`, `new_status`, `changed_at`) VALUES
(1, 3, 1, 'pending', 'shipping', '2026-01-23 00:44:17'),
(2, 3, 3, 'unknown', 'cancelled', '2026-01-23 01:11:19'),
(3, 4, 3, 'unknown', 'shipping', '2026-01-23 04:37:07'),
(4, 4, 3, 'unknown', 'completed', '2026-01-23 05:10:19');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type_key` varchar(50) NOT NULL,
  `base_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `services`
--

INSERT INTO `services` (`id`, `name`, `type_key`, `base_price`, `description`, `created_at`) VALUES
(1, 'Giao tiêu chuẩn', 'standard', 30000.00, 'Giao hàng tiêu chuẩn trong nội thành', '2026-01-22 17:28:10'),
(2, 'Giao hỏa tốc', 'express', 50000.00, 'Giao hàng nhanh trong 2h', '2026-01-22 17:28:10'),
(3, 'Giao hàng số lượng lớn', 'bulk', 0.00, 'Liên hệ để có giá tốt nhất', '2026-01-22 17:28:10');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','admin','shipper') DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `fullname`, `phone`, `password`, `role`, `created_at`, `email`) VALUES
(1, 'admin', NULL, NULL, '$2y$10$GXB0ZfzHjcDOR8oa5XSEv.G2nQiN6.O2ZZo/leShPqqKTd4LoKw/a', 'admin', '2026-01-22 13:37:41', ''),
(2, 'Anguyen', 'Nguyễn Văn A', '0987654321', '$2y$10$UP.X471ZkmUjBMASEoR/yuH9Ug4DTXCuC.8YksMl52mHqqgZGdHDq', 'customer', '2026-01-22 13:38:42', 'nguyenvana@gmail.com'),
(3, 'Thien', 'Thiện Bảo', '0987654332', '$2y$10$jyiF4rZilRjMX.X8JVQIzORG36ab7o9exKM3.yzmOsTs18pBVLm4O', 'shipper', '2026-01-22 17:48:43', 'thienbao@gmail.com');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orders_users` (`user_id`),
  ADD KEY `order_code` (`order_code`),
  ADD KEY `fk_orders_shipper` (`shipper_id`);

--
-- Chỉ mục cho bảng `order_logs`
--
ALTER TABLE `order_logs`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `type_key` (`type_key`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `order_logs`
--
ALTER TABLE `order_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_shipper` FOREIGN KEY (`shipper_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_orders_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
