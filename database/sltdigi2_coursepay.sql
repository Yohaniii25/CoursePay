-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 18, 2025 at 04:34 AM
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
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `student_id`, `regional_centre`, `course_type`, `course_name`, `registration_fee`, `course_fee`, `status`, `created_at`) VALUES
(100, 100, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Basic Gemmology', 2000.00, 50000.00, 'pending', '2025-11-13 04:52:30'),
(101, 101, 'Head Office - Kaduwela', 'International Courses', 'Gem-A Foundation Course', 10000.00, 589567.22, 'pending', '2025-11-14 03:09:47'),
(104, 104, 'Ratnapura', 'Diploma Level Courses', 'Diploma in Professional Gemmology (Dip. PGSL)', 2000.00, 150000.00, 'pending', '2025-11-14 04:13:45');

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
  `transaction_id` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `paid_amount` decimal(10,2) DEFAULT '0.00',
  `due_amount` decimal(10,2) DEFAULT '0.00',
  `slip_file` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `application_id`, `amount`, `method`, `status`, `transaction_id`, `created_at`, `paid_amount`, `due_amount`, `slip_file`) VALUES
(95, 100, 52000.00, '', 'completed', NULL, '2025-11-13 04:58:27', 52000.00, 0.00, 'Uploads/slips/1763010638_Payment Receipt - GJRTI.pdf'),
(96, 101, 599567.22, 'Online Payment', 'completed', NULL, '2025-11-14 03:12:40', 599567.22, 0.00, NULL),
(99, 104, 152000.00, 'Online Payment', 'pending', NULL, '2025-11-14 04:16:00', 76000.00, 76000.00, NULL);

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
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `contact_number` varchar(20) DEFAULT NULL,
  `address` text,
  `gmail` varchar(100) DEFAULT NULL,
  `checked` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `student_id_manual`, `reference_no`, `name`, `nic_passport`, `nic_file`, `education_background`, `next_payment_date`, `declaration`, `created_at`, `contact_number`, `address`, `gmail`, `checked`) VALUES
(100, NULL, 'A5DEBE0D', 'SATKUNANATHAN SUTHARSHAN', '930643523V', 'Uploads/nic/1763009550_Sym gjrti logo 2025.png', 'A/L', NULL, 0, '2025-11-13 04:52:30', '0754347886', 'Shangar Mill Road Vantharumoolai', 'sutharshankanna04@gmail.com', 1),
(101, NULL, 'EAE0DF7F', 'SATKUNANATHAN SUTHARSHAN', '930643523V', 'Uploads/nic/1763089787_WhatsApp Image 2025-11-06 at 9.09.04 AM.jpeg', '', NULL, 0, '2025-11-14 03:09:47', '0754347886', 'Shangar Mill Road Vantharumoolai', 'sutharshankanna04@gmail.com', 1),
(104, NULL, '5F06D467', 'Jegan Viththiya', '930643523V', 'Uploads/nic/1763093625_11.11 (2) (2).pdf', '', NULL, 0, '2025-11-14 04:13:45', '0754347886', 'Shangar Mill Road Vantharumoolai', 'jeganviththiya@gmail.com', 1);

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
  ADD KEY `student_id` (`student_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

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
