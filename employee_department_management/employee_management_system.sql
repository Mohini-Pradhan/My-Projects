-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 05, 2026 at 03:18 PM
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
-- Database: `employee_management_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `department_name` varchar(100) NOT NULL,
  `department_code` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `department_name`, `department_code`, `description`, `created_at`) VALUES
(1, 'IT', 'PROGRAMMER', 'Handles coding and company Projects.', '2026-07-29 08:10:25'),
(2, 'Human Resources', 'HR', 'Hiring and Onboarding', '2026-07-29 08:42:38'),
(3, 'Sales', 'SALE', 'Finds buyers and sells goods or services.', '2026-08-17 13:02:52'),
(4, 'Finance & Accounting', 'F & A', 'Tracks money, budgets, and pays bills.', '2026-08-17 15:33:34'),
(5, 'Customer Service', 'CS', 'Helps buyers with questions and problems.', '2026-08-17 17:03:54');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `employee_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `department_id`, `employee_name`, `email`, `phone`, `gender`, `salary`, `joining_date`, `address`, `created_at`) VALUES
(1, 1, 'Mohini Pradhan', 'mohini@gmail.com', '6290056498', 'Female', 90000.00, '2000-10-13', 'Madhyamgram', '2026-07-29 08:31:05'),
(2, 2, 'Sohan Roy', 'sohan@gmail.com', '6259784156', 'Male', 50000.00, '2022-06-15', 'Kolkata', '2026-07-29 08:43:31'),
(3, 2, 'Priya Das', 'priya@gmail.com', '6259800154', 'Female', 60000.00, '2020-08-17', 'Kolkata', '2026-08-17 14:02:14'),
(4, 4, 'Sanju Sharma', 'sanju@gmail.com', '6545897458', 'Male', 650000.00, '2026-08-17', 'Barasat', '2026-08-17 15:35:01'),
(5, 1, 'Mohini Pradhan', 'mohi@gmail.com', '6549874584', 'Female', 70000.00, '2026-08-06', 'Kolkata', '2026-08-25 05:44:42');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(6, 'Mohini Pradhan', 'pradhanmohini049@gmail.com', '$2y$10$ZBpfjiEyqmpavkue.kXHoO69AzefxCNNNxmZxeNxVb8ogqisUk.ai', '2026-08-17 12:48:36'),
(7, 'Tina Das', 'tina@gmail.com', '$2y$10$x2pEdzF3KQATZaw/3NDODOWdQttdZw7AeqZWd9Nfe/dyGT17Yb9EC', '2026-09-05 12:35:32'),
(8, 'Tuki', 'tuki@gmail.com', '$2y$10$a/r2Z0A3dA1qTpidMLeHkeVtMrY1IS5Yz1r8fxe2CYhH8F1cHCSqu', '2026-09-05 13:02:35');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `department_code` (`department_code`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
