-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 15, 2025 at 05:48 AM
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
(28, 28, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Basic Gemmology', 2000.00, 50000.00, 'pending', '2025-10-11 07:37:27'),
(29, 29, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Basic Gemmology', 2000.00, 50000.00, 'pending', '2025-10-11 08:27:08'),
(30, 30, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gem Cutting and Polishing (Weekend)', 2000.00, 35000.00, 'pending', '2025-10-11 09:46:49'),
(31, 31, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Geuda Heat Treatment', 2000.00, 55000.00, 'pending', '2025-10-15 05:46:57'),
(32, 32, 'Ratnapura', 'Diploma Level Courses', 'Diploma in Professional Gemmology (Dip. PGSL)', 2000.00, 150000.00, 'pending', '2025-10-15 10:44:53'),
(33, 33, 'Head Office - Kaduwela', 'International Courses', 'Gem-A Foundation Course', 10000.00, 589567.22, 'pending', '2025-10-15 10:46:38');

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
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `application_id`, `amount`, `method`, `status`, `transaction_id`, `created_at`) VALUES
(28, 28, 52000.00, 'Online Payment', 'completed', 'd4d067193c2e4b59', '2025-10-11 07:37:27'),
(29, 29, 52000.00, 'Online Payment', 'completed', 'fc99205f3bf74622', '2025-10-11 08:27:08'),
(30, 30, 37000.00, 'Online Payment', 'completed', '2fc7f65576d542de', '2025-10-11 09:46:49'),
(31, 31, 57000.00, 'Online Payment', 'completed', 'e87107b6173448ef', '2025-10-15 05:46:57'),
(32, 32, 152000.00, 'Online Payment', 'pending', NULL, '2025-10-15 10:44:53'),
(33, 33, 599567.22, 'Online Payment', 'pending', NULL, '2025-10-15 10:46:38');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `reference_no` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `nic_passport` varchar(50) NOT NULL,
  `nic_file` varchar(255) DEFAULT NULL,
  `education_background` text,
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

INSERT INTO `students` (`id`, `reference_no`, `name`, `nic_passport`, `nic_file`, `education_background`, `declaration`, `created_at`, `contact_number`, `address`, `gmail`, `checked`) VALUES
(28, 'C7B20E73', 'Test Student', '1999999999', 'uploads/nic/1760168247_New Project.jpg', 'hi', 1, '2025-10-11 07:37:27', '011234456', '23/B, Beddagana Road, Pitakotte', 'tstdent@gmail.com', 1),
(29, '5F2CBE37', 'Test Student 2', '1999999999', 'uploads/nic/1760171228_New Project.jpg', 'hi', 1, '2025-10-11 08:27:08', '011234456', 'test', 'test2@gmail.com', 1),
(30, 'D81EC722', 'Test Course Student', '199958123456', 'uploads/nic/1760176009_success-1f0i15o39jo26z2o.jpg', 'test qualiication', 1, '2025-10-11 09:46:49', '0112345678', 'test Sri Lanka', 'teststudent2@gmail.com', 1),
(31, 'C5719701', 'Nuwanthi Wijesinghe', '19898978004123', 'uploads/nic/1760507217_image.png', '', 1, '2025-10-15 05:46:57', '0775454496', 'No. 398/2A, Elvitigala Place, Highlevel Road, Pannipitiya', 'nuwanthi.wijesinghe@gmail.com', 0),
(32, '2503D99C', 'Test Student 3', '1999999999', 'uploads/nic/1760525093_natural-mosaic.jpg', 'test', 1, '2025-10-15 10:44:53', '011234456', '23/B, Beddagana Road, Pitakotte', 'yohanii725@gmail.com', 0),
(33, '3A40FA2C', 'Test Student 3', '1999999999', 'uploads/nic/1760525198_ORISAL_MINERAL.jpg', 'sss', 1, '2025-10-15 10:46:38', '0778439871', 'fsdffdfsd', 'yohanii725@gmail.com', 0);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

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
