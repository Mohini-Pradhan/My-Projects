-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 05, 2026 at 03:17 PM
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
-- Database: `student_course_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `course_name` varchar(100) NOT NULL,
  `course_code` varchar(30) NOT NULL,
  `duration` int(11) NOT NULL,
  `fees` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `course_name`, `course_code`, `duration`, `fees`, `description`, `created_at`) VALUES
(1, 'PHP', 'PHP101', 12, 60000.00, 'A PHP course teaches server-side web development, covering core syntax, database integration with MySQL, form handling, and dynamic website creation. Students build real-world applications, manage sessions, and learn object-oriented programming (OOP) to design secure, scalable web solutions', '2026-08-24 12:04:22'),
(2, 'B.Tech', 'BT101', 36, 250000.00, 'Bachelor of Technology / Engineering', '2026-08-24 14:08:56'),
(3, 'B.E', 'BE106', 48, 450000.00, 'Bachelor of Engineering', '2026-08-24 14:11:44'),
(4, 'B.Sc', 'BSC006', 36, 150000.00, 'Bachelor of Science', '2026-08-24 14:12:44'),
(5, 'BCA', 'BCA094', 36, 200000.00, 'Bachelor of Computer Science', '2026-08-24 14:13:36'),
(6, 'MBBS', 'MBBS698', 60, 1500000.00, 'Medicine', '2026-08-24 14:14:31'),
(7, 'BDS', 'BDS69', 48, 1000000.00, 'Dental Surgery', '2026-08-24 14:15:15'),
(8, 'BBA', 'BBA66', 24, 800000.00, 'Bachelor of Business Administration', '2026-08-24 14:24:57'),
(9, 'B.Com', 'BCOM60', 36, 100000.00, 'Bachelor of Commerce', '2026-08-24 14:25:55'),
(10, 'BA', 'BA004', 36, 100000.00, 'Bachelor of Arts', '2026-08-24 14:26:38'),
(11, 'Full Stack Web Development', 'FSWD63', 6, 80000.00, 'Full Stack Web Development', '2026-08-24 14:27:33'),
(12, 'Digital Marketing', 'DM669', 10, 690000.00, 'Digital Marketing', '2026-08-24 14:28:07'),
(13, 'bcdg', '5648', 6, 55000.00, 'bcwgdiew', '2026-08-25 05:47:48'),
(14, 'Laravel with MVC', 'LRLWMVC112', 6, 50000.00, 'Laravel with MVC using AI', '2026-08-27 18:42:05'),
(15, 'React JS', 'REJS154', 6, 50000.00, 'Modern React to Learn', '2026-08-27 18:51:06');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `enrollment_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `student_id`, `course_id`, `enrollment_date`) VALUES
(1, 1, 1, '2026-08-24'),
(2, 6, 2, '2026-08-02'),
(3, 8, 11, '2026-07-20'),
(4, 9, 13, '2026-08-25'),
(5, 10, 1, '2026-04-24'),
(6, 3, 12, '2026-08-27'),
(7, 6, 15, '2026-08-27');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `student_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `dob` date NOT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `student_name`, `email`, `phone`, `gender`, `dob`, `address`, `created_at`) VALUES
(1, 'Ritu Shigh', 'ritushigh@gmail.com', '6254955120', 'Female', '2000-05-20', 'Kolkata', '2026-08-24 11:53:10'),
(2, 'Gungun Mondal', 'gungun@gmail.com', '6598415600', 'Female', '1999-02-15', 'Kolkata', '2026-08-24 13:33:42'),
(3, 'Rohan Roy', 'rohan@gmail.com', '6932002406', 'Male', '2001-08-05', 'Barasat', '2026-08-24 13:34:22'),
(4, 'James Alexander', 'james@gmail.com', '6989100026', 'Male', '1998-10-06', 'UK', '2026-08-24 14:02:08'),
(5, 'William Thomas', 'william@gmail.com', '6541200006', 'Male', '1999-12-10', 'UK', '2026-08-24 14:03:07'),
(6, 'Charlotte Rose', 'charlotte@gmail.com', '', 'Female', '1996-08-26', 'UK', '2026-08-24 14:03:58'),
(7, 'Liam Asher', 'liam@gmail.com', '6932001560', 'Male', '1990-06-06', 'Tokyo', '2026-08-24 14:06:08'),
(8, 'Miles Oliver', 'miles@gmail.com', '6987403215', 'Male', '1997-02-12', 'Valleys', '2026-08-24 14:07:03'),
(9, 't roy', 't@gmail.com', '6598745897', 'Female', '2000-10-06', 'Kol', '2026-08-25 05:47:03'),
(10, 'Mohini Pradhan', 'mohini@gmail.com', '6593245600', 'Female', '2000-10-13', 'Madhyamgram', '2026-08-27 18:32:41');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'Mohini Pradhan', 'pradhanmohini049@gmail.com', '$2y$10$6ah9mWmTZBYCvJynksmI5uZbv7JaVxJ0cbjV4s8mHjNt96aWNK.ai', '2026-08-24 07:49:09'),
(2, 'Mohini Pradhan', 'mohini@gmail.com', '$2y$10$yGhrqJ61p/Z8Zy./fYVdP.lNKZsDzPjBGvCh9d/.lHcWi8VEtK7yW', '2026-09-05 13:14:36');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `course_code` (`course_code`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_enrollment` (`student_id`,`course_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

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
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
