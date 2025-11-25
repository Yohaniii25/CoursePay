-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 22, 2025 at 07:17 AM
-- Server version: 5.7.23-23
-- PHP Version: 8.1.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sltdigi2_coursepay`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `email`, `password`, `created_at`) VALUES
(1, 'admin_gem', 'admin_gem@gmail.com', '$2y$10$2.84fEJcvDdpv8EwbvNctePmnzi1oLP2y7S/x8Ny0OHxliyBtibTS', '2025-10-10 11:47:41');

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `regional_centre` varchar(100) NOT NULL,
  `course_type` varchar(100) NOT NULL,
  `course_name` varchar(150) NOT NULL,
  `registration_fee` decimal(10,2) NOT NULL,
  `course_fee` decimal(10,2) NOT NULL,
  `status` enum('pending','completed') DEFAULT 'pending',
  `charge_type` enum('payable','free') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `student_id`, `regional_centre`, `course_type`, `course_name`, `registration_fee`, `course_fee`, `status`, `charge_type`, `created_at`) VALUES
(123, 123, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gemmology', 2000.00, 70000.00, 'pending', 'payable', '2025-11-22 13:10:17');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` enum('Upload Payslip','Online Payment') NOT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'pending',
  `installment_type` enum('first','second','full') NOT NULL DEFAULT 'first',
  `transaction_id` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `paid_amount` decimal(10,2) DEFAULT '0.00',
  `due_amount` decimal(10,2) DEFAULT '0.00',
  `slip_file` varchar(255) DEFAULT NULL,
  `slip_file_2` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `application_id`, `amount`, `method`, `status`, `installment_type`, `transaction_id`, `created_at`, `paid_amount`, `due_amount`, `slip_file`, `slip_file_2`) VALUES
(115, 123, 72000.00, '', 'completed', 'second', 'SLIP-2ED6FBE0-1763817223', '2025-11-22 13:10:37', 72000.00, 0.00, 'Uploads/slips/1763817061_1. Gayan Edirisingha.jpg', 'Uploads/slips/1763817223_3. Ravindra Wickramasingha.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `student_id_manual` varchar(50) DEFAULT NULL,
  `reference_no` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `nic_passport` varchar(50) NOT NULL,
  `nic_file` varchar(255) DEFAULT NULL,
  `education_background` text,
  `next_payment_date` date DEFAULT NULL,
  `declaration` tinyint(1) NOT NULL DEFAULT '0',
  `declaration2` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `contact_number` varchar(20) DEFAULT NULL,
  `address` text,
  `gmail` varchar(100) DEFAULT NULL,
  `checked` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `student_id_manual`, `reference_no`, `name`, `nic_passport`, `nic_file`, `education_background`, `next_payment_date`, `declaration`, `declaration2`, `created_at`, `contact_number`, `address`, `gmail`, `checked`) VALUES
(123, NULL, '2ED6FBE0', 'User Test', '1999999999', 'Uploads/nic/1763817017_1. Gayan Edirisingha.jpg', 'ccc', NULL, 1, 0, '2025-11-22 13:10:17', '0712345678', 'user test address', 'gem_test@sltdigital.site', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `idx_charge_type` (`charge_type`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `application_id` (`application_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference_no` (`reference_no`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=124;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=124;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
