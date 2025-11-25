-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 24, 2025 at 05:34 AM
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
  `slip_file` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `application_id`, `amount`, `method`, `status`, `installment_type`, `transaction_id`, `created_at`, `paid_amount`, `due_amount`, `slip_file`) VALUES
(133, 131, 47000.00, 'Online Payment', 'pending', 'first', NULL, '2025-11-24 11:24:29', 0.00, 47000.00, NULL),
(134, 131, 24500.00, 'Online Payment', 'pending', 'first', 'TXN-78005EFF-FIRST-1763983517', '2025-11-24 11:26:06', 24500.00, 22500.00, NULL),
(135, 131, 22500.00, 'Online Payment', 'pending', 'full', 'TXN-78005EFF-FULL-1763983831', '2025-11-24 11:31:07', 22500.00, 24500.00, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `application_id` (`application_id`),
  ADD KEY `idx_application` (`application_id`),
  ADD KEY `idx_installment` (`installment_type`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=136;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
