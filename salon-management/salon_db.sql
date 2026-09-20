-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 14, 2026 at 06:50 AM
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
-- Database: `salon_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(10) UNSIGNED NOT NULL,
  `customer_id` int(10) UNSIGNED NOT NULL,
  `service_id` int(10) UNSIGNED NOT NULL,
  `beautician_id` int(10) UNSIGNED NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `status` enum('Pending','Confirmed','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `customer_id`, `service_id`, `beautician_id`, `appointment_date`, `appointment_time`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 2, '2026-08-10', '11:00:00', 'Confirmed', NULL, '2026-08-04 19:20:34', '2026-08-04 19:28:49'),
(2, 5, 5, 1, '2026-08-04', '03:30:00', 'Completed', NULL, '2026-08-04 19:22:11', '2026-08-04 19:29:09'),
(3, 3, 1, 3, '2026-08-04', '20:00:00', 'Completed', NULL, '2026-08-04 19:30:40', '2026-08-04 19:30:40'),
(4, 7, 3, 3, '2026-08-04', '05:15:00', 'Completed', NULL, '2026-08-04 19:31:23', '2026-08-04 19:31:23'),
(5, 1, 1, 2, '2026-08-05', '12:00:00', 'Confirmed', NULL, '2026-08-04 19:44:31', '2026-08-04 19:44:31'),
(6, 8, 7, 7, '2026-08-12', '04:00:00', 'Confirmed', NULL, '2026-08-12 03:55:04', '2026-08-12 03:55:04'),
(7, 10, 4, 3, '2026-08-13', '05:15:00', 'Pending', NULL, '2026-08-12 15:11:10', '2026-08-12 15:11:10'),
(8, 11, 1, 2, '2026-08-13', '12:00:00', 'Cancelled', NULL, '2026-08-12 18:01:16', '2026-08-12 18:24:31'),
(9, 11, 4, 3, '2026-08-13', '02:00:00', 'Pending', NULL, '2026-08-12 18:04:17', '2026-08-12 18:04:17'),
(10, 11, 4, 3, '2026-08-13', '20:00:00', 'Pending', NULL, '2026-08-12 18:26:10', '2026-08-12 18:26:10'),
(11, 12, 4, 3, '2026-08-13', '06:00:00', 'Cancelled', NULL, '2026-08-13 08:18:23', '2026-08-13 08:18:28'),
(12, 12, 7, 7, '2026-08-14', '11:00:00', 'Pending', NULL, '2026-08-13 10:50:25', '2026-08-13 10:50:25'),
(13, 12, 7, 7, '2026-08-18', '11:00:00', 'Pending', NULL, '2026-08-13 10:59:37', '2026-08-13 10:59:37'),
(14, 12, 7, 7, '2026-08-15', '10:00:00', 'Pending', NULL, '2026-08-13 11:07:37', '2026-08-13 11:07:37'),
(15, 12, 7, 7, '2026-08-16', '10:00:00', 'Pending', NULL, '2026-08-14 04:44:29', '2026-08-14 04:44:29'),
(16, 13, 7, 7, '2026-08-16', '11:00:00', 'Pending', NULL, '2026-08-14 04:45:59', '2026-08-14 04:45:59');

-- --------------------------------------------------------

--
-- Table structure for table `beauticians`
--

CREATE TABLE `beauticians` (
  `id` int(10) UNSIGNED NOT NULL,
  `beautician_name` varchar(120) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `specialization` varchar(150) DEFAULT NULL,
  `experience` smallint(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Experience in years',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Dumping data for table `beauticians`
--

INSERT INTO `beauticians` (`id`, `beautician_name`, `phone`, `specialization`, `experience`, `created_at`, `updated_at`) VALUES
(1, 'Sujata Das', '6956445891', 'Hair Styling', 2, '2026-08-04 19:05:53', '2026-08-04 19:05:53'),
(2, 'Bittu Sardar', '9985640059', 'Facial', 3, '2026-08-04 19:06:31', '2026-08-04 19:11:34'),
(3, 'Sayan Pal', '9900465215', 'padicure', 6, '2026-08-04 19:11:12', '2026-08-11 16:16:04'),
(5, 'Rina Sen', '6598756498', 'Hydro Facial', 2, '2026-08-12 03:31:23', '2026-08-12 03:31:23'),
(6, 'Mahi Mondal', '6932564878', 'Hair Cutting', 1, '2026-08-12 03:33:24', '2026-08-12 03:33:24'),
(7, 'Puja Das', '6985654102', 'Body Massage', 2, '2026-08-12 03:48:52', '2026-08-12 03:51:42');

-- --------------------------------------------------------

--
-- Table structure for table `beautician_leaves`
--

CREATE TABLE `beautician_leaves` (
  `id` int(10) UNSIGNED NOT NULL,
  `beautician_id` int(10) UNSIGNED NOT NULL,
  `leave_date` date NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `beautician_leaves`
--

INSERT INTO `beautician_leaves` (`id`, `beautician_id`, `leave_date`, `reason`, `created_at`) VALUES
(1, 7, '2026-08-18', 'Travelling', '2026-08-13 11:14:18');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `customer_name` varchar(120) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `gender` enum('Female','Male','Other','Prefer not to say') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `user_id`, `customer_name`, `phone`, `email`, `address`, `gender`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Tuki pradhan', '6549852654', 'tuki@gmail.com', 'kolkata', 'Female', '2026-08-04 16:19:47', '2026-08-04 18:15:21'),
(2, NULL, 'Rohan Roy', '6954893024', 'rohan@gmail.com', 'Siliguri', 'Male', '2026-08-04 16:20:15', '2026-08-04 16:20:15'),
(3, NULL, 'Raj Roy', '6259870122', 'raj@gmail.com', 'Kolkata', 'Male', '2026-08-04 18:28:13', '2026-08-04 18:29:22'),
(4, NULL, 'Sonali Das', '6259984610', 'sonali@gmail.com', 'Madhyamgram', 'Female', '2026-08-04 16:21:59', '2026-08-04 16:21:59'),
(5, NULL, 'Sonu Mondal', '6954982566', 'sonu@gmail.com', 'Kolkata', 'Male', '2026-08-04 18:30:28', '2026-08-04 18:30:54'),
(7, NULL, 'Suparna Das', '6520014698', 'suparna@gmail.com', 'Kolkata', 'Female', '2026-08-04 18:32:11', '2026-08-04 18:32:29'),
(8, NULL, 'Mahi Saxena', '6359874982', 'mahi102@gmail.com', 'madhyamgram', 'Female', '2026-08-12 03:50:34', '2026-08-12 03:59:22'),
(9, 3, 'Riya Sarkar', '6215900215', 'riya00@gmail.com', NULL, NULL, '2026-08-12 11:49:27', '2026-08-12 11:49:27'),
(10, 4, 'Raima Sen', '6956400254', 'raima@gmail.com', NULL, NULL, '2026-08-12 15:05:48', '2026-08-12 15:05:48'),
(11, 5, 'Ruhi Roy', '6259487900', 'ruhi@gmail.com', 'kolkata', NULL, '2026-08-12 17:55:11', '2026-08-12 18:59:15'),
(12, 6, 'Diya Sen', '6200154687', 'diya@gmail.com', 'kolkata', NULL, '2026-08-13 05:42:39', '2026-08-13 08:22:17'),
(13, 7, 'Riya Pal', '6524987621', 'riya12@gmail.com', NULL, 'Female', '2026-08-14 04:45:18', '2026-08-14 04:45:18');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(10) UNSIGNED NOT NULL,
  `appointment_id` int(10) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('Cash','Card','UPI') NOT NULL,
  `payment_status` enum('Pending','Paid','Partially Paid','Refunded') NOT NULL DEFAULT 'Pending',
  `invoice_number` varchar(40) NOT NULL,
  `paid_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `appointment_id`, `amount`, `payment_method`, `payment_status`, `invoice_number`, `paid_at`, `created_at`, `updated_at`) VALUES
(1, 2, 2500.00, 'Cash', 'Paid', 'INV-20260804-2331', '2026-08-04 21:33:00', '2026-08-04 19:33:00', '2026-08-04 19:33:00'),
(2, 4, 800.00, 'UPI', 'Partially Paid', 'INV-20260804-6524', '2026-08-04 21:33:42', '2026-08-04 19:33:42', '2026-08-04 19:33:42'),
(3, 7, 3000.00, 'UPI', 'Paid', 'SAL-20260812-0007', '2026-08-12 20:41:23', '2026-08-12 15:11:23', '2026-08-12 15:11:23'),
(4, 14, 3000.00, 'Cash', 'Paid', 'SAL-20260814-0014', '2026-08-14 10:09:16', '2026-08-14 04:39:16', '2026-08-14 04:39:16');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(10) UNSIGNED NOT NULL,
  `service_name` varchar(120) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `duration` int(10) UNSIGNED NOT NULL COMMENT 'Duration in minutes',
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `service_name`, `price`, `duration`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Facial', 2000.00, 60, 'A salon facial is a professional, multi-step skincare service designed by beauty experts to deeply cleanse, exfoliate, and revitalize the skin\'s surface. It primarily targets the outermost skin layer to provide instant relaxation, hydration, and a noticeable short-term glow.', '2026-08-04 18:37:05', '2026-08-04 18:37:05'),
(2, 'Make-Up', 10000.00, 120, 'Outlines professional cosmetic services meant to enhance appearance for daily wear, parties, or weddings.', '2026-08-04 18:38:56', '2026-08-04 18:38:56'),
(3, 'Nail Extension', 1000.00, 120, 'A professional cosmetic service that adds length and strength to your natural nails using artificial materials like acrylic, gel, or polygel, which are sculpted, cured under UV/LED light, and styled into custom shapes', '2026-08-04 18:40:49', '2026-08-04 18:40:49'),
(4, 'Pedicure', 3000.00, 60, 'A cosmetic foot treatment that cleanses, trims, and softens the feet.', '2026-08-04 18:41:52', '2026-08-04 18:41:52'),
(5, 'Haircut', 2500.00, 45, 'Length, layers, texture, and shape. Clear communication ensures you get the exact style you want.', '2026-08-04 18:48:23', '2026-08-04 18:57:20'),
(7, 'Body Massage', 3000.00, 60, 'A body massage is a hands-on therapeutic treatment where a trained professional manipulates the soft tissues, muscles, tendons, and ligaments of your entire body. Using oils, lotions, or direct pressure, it aims to reduce stress, relieve muscle tension, and boost overall well-being', '2026-08-12 03:54:22', '2026-08-12 03:54:22'),
(8, 'Waxing', 5000.00, 35, 'Waxing services use soft or hard wax to pull hair out from the root. This professional hair removal method keeps skin smooth for three to six weeks, which is much longer than shaving.', '2026-08-12 13:11:49', '2026-08-12 13:11:49');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff','customer') NOT NULL DEFAULT 'customer',
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `role`, `image`, `created_at`, `updated_at`) VALUES
(1, 'Mohini Pradhan', 'pradhanmohini049@gmail.com', '6298465159', '$2y$10$uRhbnulw59ntFe4BT5Y9pO7cGWDKt1dR2OUNhbuLCPvHz4OL3Ap5a', 'staff', 'profile_ffa1dd173eb7728c0ab9bee784827286.jpg', '2026-08-04 16:10:58', '2026-08-04 20:04:38'),
(2, 'Mahi Mondal', 'mahi@gmail.com', '9956001254', '$2y$10$YkxOEvmk6s.TxfqEYv5.dOANDRkYkMhG6l7eZbRqbmLvQJVZisf5u', 'staff', NULL, '2026-08-04 19:46:17', '2026-08-04 19:46:17'),
(3, 'Riya Sarkar', 'riya00@gmail.com', '6215900215', '$2y$10$BdIGlkhNFBCMOuDhO5.RO.GTcOLjl9FYXBgBRUd8M.pFD.yl9blmW', 'customer', NULL, '2026-08-12 11:49:27', '2026-08-12 11:49:27'),
(4, 'Raima Sen', 'raima@gmail.com', '6956400254', '$2y$10$K8W9VIMUekh/9vclTpqJfOhgXtyUe66LM4J7/ujx0iw5gkMq/9zEe', 'customer', NULL, '2026-08-12 15:05:48', '2026-08-12 15:05:48'),
(5, 'Ruhi Roy', 'ruhi@gmail.com', '6259487900', '$2y$10$5oad7amG0OKn32E9hBxDOO683l.myPZmaZof9Z0sq.VdPlu6Fv5FK', 'customer', NULL, '2026-08-12 17:55:11', '2026-08-12 17:55:11'),
(6, 'Diya Sen', 'diya@gmail.com', '6200154687', '$2y$10$th46rv95MM4N6lfh15ySae5aNKXdfpDyHXcOQiM7wGzhmTvsguOV2', 'customer', NULL, '2026-08-13 05:42:39', '2026-08-13 05:42:39'),
(7, 'Riya Pal', 'riya12@gmail.com', '6524987621', '$2y$10$1gFPnuLGhj3phHevDP/oHe/5sHhjZoqBOE.PHdl6tXD1Ma4pTgJ4e', 'customer', NULL, '2026-08-14 04:45:18', '2026-08-14 04:45:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_appointments_service` (`service_id`),
  ADD KEY `idx_appointments_customer` (`customer_id`),
  ADD KEY `idx_appointments_schedule` (`beautician_id`,`appointment_date`,`appointment_time`),
  ADD KEY `idx_appointments_date_status` (`appointment_date`,`status`);

--
-- Indexes for table `beauticians`
--
ALTER TABLE `beauticians`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_beauticians_name` (`beautician_name`),
  ADD KEY `idx_beauticians_phone` (`phone`);

--
-- Indexes for table `beautician_leaves`
--
ALTER TABLE `beautician_leaves`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_beautician_leave_date` (`beautician_id`,`leave_date`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `idx_customers_name` (`customer_name`),
  ADD KEY `idx_customers_phone` (`phone`),
  ADD KEY `idx_customers_email` (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_payments_invoice_number` (`invoice_number`),
  ADD KEY `idx_payments_appointment` (`appointment_id`),
  ADD KEY `idx_payments_status` (`payment_status`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_services_name` (`service_name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_email` (`email`),
  ADD KEY `idx_users_phone` (`phone`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `beauticians`
--
ALTER TABLE `beauticians`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `beautician_leaves`
--
ALTER TABLE `beautician_leaves`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `fk_appointments_beautician` FOREIGN KEY (`beautician_id`) REFERENCES `beauticians` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_appointments_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_appointments_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `beautician_leaves`
--
ALTER TABLE `beautician_leaves`
  ADD CONSTRAINT `fk_beautician_leaves_beautician` FOREIGN KEY (`beautician_id`) REFERENCES `beauticians` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `fk_customers_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payments_appointment` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
