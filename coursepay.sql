-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 23, 2026 at 05:33 AM
-- Server version: 10.4.18-MariaDB
-- PHP Version: 8.0.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `coursepay`
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
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
  `refundable_deposit` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','completed') DEFAULT 'pending',
  `charge_type` enum('payable','free') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `student_id`, `regional_centre`, `course_type`, `course_name`, `registration_fee`, `course_fee`, `refundable_deposit`, `status`, `charge_type`, `created_at`) VALUES
(173, 173, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Jewellery Certificate in Tailor – Made Courses', '0.00', '0.00', '0.00', 'pending', 'payable', '2025-11-26 11:51:31'),
(175, 175, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Jewellery Certificate in Tailor – Made Courses', '0.00', '0.00', '0.00', 'pending', 'payable', '2025-11-27 03:31:04'),
(176, 176, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gemmology', '2000.00', '70000.00', '0.00', 'pending', 'payable', '2025-12-11 05:36:40'),
(177, 177, 'Head Office - Kaduwela', 'Diploma Level Courses', 'Diploma in Professional Gemmology (Dip. PGSL)', '5000.00', '200000.00', '5000.00', 'pending', NULL, '2025-12-26 04:16:52'),
(178, 178, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Geuda Heat Treatment', '2000.00', '55000.00', '0.00', 'pending', NULL, '2025-12-27 08:17:15'),
(179, 179, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Basic Gemmology', '2000.00', '50000.00', '0.00', 'pending', NULL, '2025-12-28 17:23:41'),
(180, 180, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gemmology', '2000.00', '70000.00', '0.00', 'pending', NULL, '2025-12-29 02:33:44'),
(181, 181, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Basic Gemmology', '2000.00', '50000.00', '0.00', 'pending', NULL, '2025-12-30 12:32:17'),
(184, 184, 'Badulla', 'Certificate Level Courses', 'Certificate in Basic Gemmology', '2000.00', '50000.00', '0.00', 'pending', NULL, '2026-01-01 07:13:03'),
(185, 185, 'Kandy', 'Certificate Level Courses', 'Certificate in Jewellery Manufacturing (NVQ 3)', '2000.00', '20000.00', '0.00', 'pending', NULL, '2026-01-02 07:11:38'),
(186, 186, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gemmology', '2000.00', '70000.00', '0.00', 'pending', NULL, '2026-01-03 06:20:38'),
(187, 187, 'Head Office - Kaduwela', 'International Courses', 'Gem-A Foundation Course', '10000.00', '589567.22', '0.00', 'pending', NULL, '2026-01-03 12:07:38'),
(188, 188, 'Gampola', 'Diploma Level Courses', 'Diploma in Professional Gemmology (Dip. PGSL)', '5000.00', '200000.00', '5000.00', 'pending', NULL, '2026-01-03 19:38:41'),
(189, 189, 'Ratnapura', 'Diploma Level Courses', 'Diploma in Professional Gemmology (Dip. PGSL)', '5000.00', '200000.00', '5000.00', 'pending', NULL, '2026-01-04 12:19:50'),
(190, 190, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Basic Gemmology', '2000.00', '50000.00', '0.00', 'pending', NULL, '2026-01-05 02:52:48'),
(191, 191, 'Head Office - Kaduwela', 'Diploma Level Courses', 'Diploma in Professional Gemmology (Dip. PGSL)', '5000.00', '200000.00', '5000.00', 'pending', NULL, '2026-01-05 08:58:23'),
(192, 192, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gemmology', '2000.00', '70000.00', '0.00', 'pending', NULL, '2026-01-05 14:25:30'),
(195, 195, 'Head Office - Kaduwela', 'Diploma Level Courses', 'Diploma in Professional Gemmology (Dip. PGSL)', '5000.00', '200000.00', '5000.00', 'pending', NULL, '2026-01-08 04:42:18'),
(196, 196, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gemmology', '2000.00', '70000.00', '0.00', 'pending', NULL, '2026-01-08 09:20:45'),
(197, 197, 'Ratnapura', 'Certificate Level Courses', 'Certificate in Gemmology', '2000.00', '70000.00', '0.00', 'pending', NULL, '2026-01-09 10:34:00'),
(198, 198, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Computer Aided Jewellery Designing and Manufacturing (CAD/CAM)', '2000.00', '60000.00', '0.00', 'pending', NULL, '2026-01-10 05:54:55'),
(199, 199, 'Ratnapura', 'Certificate Level Courses', 'Certificate in Gems and Jewellery Valuation and Marketing', '2000.00', '20000.00', '0.00', 'pending', NULL, '2026-01-11 06:55:36'),
(200, 200, 'Kandy', 'Certificate Level Courses', 'Certificate in Jewellery Manufacturing (NVQ 4)', '2000.00', '50000.00', '0.00', 'pending', NULL, '2026-01-11 10:05:12'),
(201, 201, 'Head Office - Kaduwela', 'International Courses', 'Gem-A Foundation Course', '10000.00', '589567.22', '0.00', 'pending', NULL, '2026-01-12 12:58:26'),
(202, 202, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gems and Jewellery Valuation and Marketing', '2000.00', '20000.00', '0.00', 'pending', NULL, '2026-01-13 09:55:43'),
(203, 203, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gem Cutting and Polishing (Weekend)', '2000.00', '35000.00', '0.00', 'pending', NULL, '2026-01-13 19:08:43'),
(204, 204, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Geuda Heat Treatment', '2000.00', '55000.00', '0.00', 'pending', NULL, '2026-01-14 02:02:04'),
(205, 205, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Basic Gemmology', '2000.00', '50000.00', '0.00', 'pending', NULL, '2026-01-14 02:05:03'),
(206, 206, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Computer Aided Jewellery Designing and Manufacturing (CAD/CAM)', '2000.00', '60000.00', '0.00', 'pending', NULL, '2026-01-15 14:38:25'),
(207, 207, 'Ratnapura', 'Certificate Level Courses', 'Certificate in Geuda Heat Treatment', '2000.00', '55000.00', '0.00', 'pending', NULL, '2026-01-16 05:57:43'),
(208, 208, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gem Cutting and Polishing (NVQ 4)', '2000.00', '45000.00', '0.00', 'pending', NULL, '2026-01-18 03:05:30'),
(209, 209, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gem Cutting and Polishing (NVQ 4)', '2000.00', '45000.00', '0.00', 'pending', NULL, '2026-01-19 07:39:45'),
(210, 210, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Basic Gemmology', '2000.00', '50000.00', '0.00', 'pending', NULL, '2026-01-19 12:25:37'),
(211, 211, 'Head Office - Kaduwela', 'Diploma Level Courses', 'Diploma in Professional Gemmology (Dip. PGSL)', '5000.00', '200000.00', '5000.00', 'pending', NULL, '2026-01-20 05:32:36'),
(212, 212, 'Badulla', 'Certificate Level Courses', 'Certificate in Gem Cutting and Polishing (NVQ 3)', '2000.00', '35000.00', '0.00', 'pending', NULL, '2026-01-20 17:49:10'),
(213, 213, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gemmology', '2000.00', '70000.00', '0.00', 'pending', NULL, '2026-01-21 16:57:38'),
(214, 214, 'Maradana', 'Certificate Level Courses', 'Certificate in Gem Cutting and Polishing (NVQ 4)', '2000.00', '45000.00', '0.00', 'pending', NULL, '2026-01-22 06:18:08'),
(215, 215, 'Head Office - Kaduwela', 'Diploma Level Courses', 'Diploma in Jewellery Manufacturing and Designing Technology (NVQ 5)/ Diploma in Professional Jewellery (Dip. PJSL)', '2500.00', '125000.00', '5000.00', 'pending', NULL, '2026-01-22 13:55:30'),
(216, 216, 'Head Office - Kaduwela', 'Diploma Level Courses', 'Diploma in Professional Gemmology (Dip. PGSL)', '5000.00', '200000.00', '5000.00', 'pending', NULL, '2026-01-23 00:35:13'),
(217, 217, 'Ratnapura', 'Certificate Level Courses', 'Certificate in Gemmology', '2000.00', '70000.00', '0.00', 'pending', NULL, '2026-01-24 05:15:15'),
(218, 218, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gem Cutting and Polishing (Weekend)', '2000.00', '35000.00', '0.00', 'pending', NULL, '2026-01-25 04:34:58'),
(219, 219, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gem Cutting and Polishing (10 DAYS)', '2000.00', '14000.00', '0.00', 'pending', NULL, '2026-01-25 04:39:35'),
(220, 220, 'Ratnapura', 'Certificate Level Courses', 'Certificate in Basic Gemmology', '2000.00', '50000.00', '0.00', 'pending', NULL, '2026-01-27 06:08:19'),
(221, 221, 'Ratnapura', 'Diploma Level Courses', 'Diploma in Professional Gemmology (Dip. PGSL)', '5000.00', '200000.00', '5000.00', 'pending', NULL, '2026-01-28 04:49:49'),
(222, 222, 'Kandy', 'Certificate Level Courses', 'Certificate in Jewellery Designing (Manual)', '2000.00', '43000.00', '0.00', 'pending', NULL, '2026-01-30 10:10:59'),
(223, 223, 'Kandy', 'Certificate Level Courses', 'Certificate in Gem Cutting and Polishing (NVQ 4)', '2000.00', '45000.00', '0.00', 'pending', NULL, '2026-01-30 13:53:29'),
(224, 224, 'Ratnapura', 'Certificate Level Courses', 'Certificate in Basic Gemmology', '2000.00', '50000.00', '0.00', 'pending', NULL, '2026-01-31 10:11:13'),
(225, 225, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gems and Jewellery Valuation and Marketing', '2000.00', '20000.00', '0.00', 'pending', NULL, '2026-01-31 12:20:46'),
(226, 226, 'Ratnapura', 'Certificate Level Courses', 'Certificate in Gem Cutting and Polishing (Weekend)', '2000.00', '35000.00', '0.00', 'pending', NULL, '2026-02-01 06:39:54'),
(227, 227, 'Ratnapura', 'Certificate Level Courses', 'Certificate in Geuda Heat Treatment', '2000.00', '55000.00', '0.00', 'pending', NULL, '2026-02-01 09:08:52'),
(228, 228, 'Ratnapura', 'Certificate Level Courses', 'Certificate in Geuda Heat Treatment', '2000.00', '55000.00', '0.00', 'pending', NULL, '2026-02-01 09:09:04'),
(229, 229, 'Naula', 'Certificate Level Courses', 'Certificate in Gem Cutting and Polishing (10 DAYS)', '2000.00', '14000.00', '0.00', 'pending', NULL, '2026-02-01 17:41:00'),
(230, 230, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Computer Aided Jewellery Designing and Manufacturing (CAD/CAM)', '2000.00', '60000.00', '0.00', 'pending', NULL, '2026-02-02 03:48:40'),
(231, 231, 'Head Office - Kaduwela', 'Diploma Level Courses', 'Diploma in Jewellery Manufacturing and Designing Technology (NVQ 5)/ Diploma in Professional Jewellery (Dip. PJSL)', '2500.00', '125000.00', '5000.00', 'pending', NULL, '2026-02-08 13:21:13'),
(232, 232, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gemmology', '2000.00', '70000.00', '0.00', 'pending', NULL, '2026-02-13 08:37:43'),
(233, 233, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gemmology', '2000.00', '70000.00', '0.00', 'pending', NULL, '2026-02-13 10:29:30'),
(234, 234, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gemmology', '2000.00', '70000.00', '0.00', 'pending', NULL, '2026-02-16 05:50:51'),
(235, 235, 'Attanagalla', 'Certificate Level Courses', 'Certificate in Jewellery Manufacturing (NVQ 4)', '2000.00', '50000.00', '0.00', 'pending', NULL, '2026-02-16 06:02:39'),
(236, 236, 'Kandy', 'Certificate Level Courses', 'Certificate in Basic Gemmology', '2000.00', '50000.00', '0.00', 'pending', NULL, '2026-02-16 20:39:13'),
(237, 237, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gemmology', '2000.00', '70000.00', '0.00', 'pending', NULL, '2026-02-19 04:12:25'),
(238, 238, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Jewellery Manufacturing (NVQ 3)', '2000.00', '20000.00', '0.00', 'pending', NULL, '2026-03-03 18:22:48'),
(239, 239, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Basic Gemmology', '2000.00', '50000.00', '0.00', 'pending', NULL, '2026-03-04 04:22:41'),
(240, 240, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Basic Gemmology', '2000.00', '50000.00', '0.00', 'pending', NULL, '2026-03-09 04:54:10'),
(241, 241, 'Attanagalla', 'Certificate Level Courses', 'Certificate in Jewellery Manufacturing (NVQ 3)', '2000.00', '20000.00', '0.00', 'pending', NULL, '2026-03-12 15:36:46'),
(242, 242, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Basic Gemmology', '2000.00', '50000.00', '0.00', 'pending', NULL, '2026-03-13 04:07:55'),
(243, 243, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gemmology', '2000.00', '70000.00', '0.00', 'pending', NULL, '2026-03-13 10:37:57'),
(244, 244, 'Kandy', 'Certificate Level Courses', 'Certificate in Jewellery Manufacturing (NVQ 4)', '2000.00', '50000.00', '0.00', 'pending', NULL, '2026-03-13 14:03:54'),
(245, 245, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Basic Gemmology', '2000.00', '50000.00', '0.00', 'pending', NULL, '2026-03-14 07:42:42'),
(246, 246, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gemmology', '2000.00', '70000.00', '0.00', 'pending', NULL, '2026-03-14 18:04:38'),
(247, 247, 'Head Office - Kaduwela', 'Diploma Level Courses', 'Diploma in Professional Gemmology (Dip. PGSL)', '5000.00', '200000.00', '5000.00', 'pending', NULL, '2026-03-16 20:26:58'),
(248, 248, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gemmology', '2000.00', '70000.00', '0.00', 'pending', NULL, '2026-03-17 09:08:10'),
(249, 249, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gemmology', '2000.00', '70000.00', '0.00', 'pending', NULL, '2026-03-18 06:54:13'),
(250, 250, 'Ratnapura (NYSC)', 'Certificate Level Courses', 'Certificate in Gem Cutting and Polishing (NVQ 4)', '2000.00', '45000.00', '0.00', 'pending', NULL, '2026-03-18 13:01:29'),
(251, 251, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Blended Gemmology', '2000.00', '70000.00', '0.00', 'pending', NULL, '2026-03-19 04:51:07'),
(252, 252, 'Head Office - Kaduwela', 'Diploma Level Courses', 'Diploma in Professional Gemmology (Dip. PGSL)', '5000.00', '200000.00', '5000.00', 'pending', NULL, '2026-03-19 08:44:21'),
(253, 253, 'Head Office - Kaduwela', 'Diploma Level Courses', 'Diploma in Professional Gemmology (Dip. PGSL)', '5000.00', '200000.00', '5000.00', 'pending', NULL, '2026-03-20 08:47:33'),
(254, 254, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Basic Gemmology', '2000.00', '50000.00', '0.00', 'pending', NULL, '2026-03-22 13:46:10'),
(255, 255, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gemmology', '2000.00', '70000.00', '0.00', 'pending', NULL, '2026-03-24 10:55:07'),
(256, 256, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gem Cutting and Polishing (10 DAYS)', '2000.00', '14000.00', '0.00', 'pending', NULL, '2026-03-25 02:45:32'),
(257, 257, 'Head Office - Kaduwela', 'International Courses', 'Gem-A Foundation Course', '10000.00', '589567.22', '0.00', 'pending', NULL, '2026-03-26 22:06:15'),
(258, 258, 'Ratnapura', 'Certificate Level Courses', 'Certificate in Gem Cutting and Polishing (NVQ 4)', '2000.00', '45000.00', '0.00', 'pending', NULL, '2026-04-01 07:18:57'),
(259, 259, 'Ratnapura', 'Certificate Level Courses', 'Certificate in Gem Cutting and Polishing (NVQ 4)', '2000.00', '45000.00', '0.00', 'pending', NULL, '2026-04-02 04:27:34'),
(260, 260, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Jewellery Certificate in Tailor â€“ Made Courses', '0.00', '0.00', '0.00', 'pending', NULL, '2026-04-02 04:53:09'),
(261, 261, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gem Cutting and Polishing (10 DAYS)', '2000.00', '14000.00', '0.00', 'pending', NULL, '2026-04-04 06:30:39'),
(262, 262, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gemmology', '2000.00', '70000.00', '0.00', 'pending', NULL, '2026-04-15 07:17:39'),
(263, 263, 'Ratnapura', 'Certificate Level Courses', 'Certificate in Gems and Jewellery Valuation and Marketing', '2000.00', '20000.00', '0.00', 'pending', NULL, '2026-04-15 17:07:02'),
(264, 264, 'Ratnapura', 'Certificate Level Courses', 'Certificate in Gem Cutting and Polishing (Weekend)', '2000.00', '35000.00', '0.00', 'pending', NULL, '2026-04-18 08:36:32'),
(265, 265, 'Ratnapura', 'Certificate Level Courses', 'Gem Related Certificate in Tailor â€“ Made Courses', '0.00', '0.00', '0.00', 'pending', NULL, '2026-04-18 08:39:46'),
(266, 266, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Geuda Heat Treatment', '2000.00', '55000.00', '0.00', 'pending', NULL, '2026-04-21 11:13:29'),
(267, 267, 'Head Office - Kaduwela', 'Diploma Level Courses', 'Diploma in Jewellery Manufacturing and Designing Technology (NVQ 5)/ Diploma in Professional Jewellery (Dip. PJSL)', '2500.00', '125000.00', '5000.00', 'pending', NULL, '2026-04-22 05:40:49'),
(268, 268, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gemmology', '2000.00', '70000.00', '0.00', 'pending', NULL, '2026-04-22 07:57:48'),
(269, 269, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Basic Gemmology', '2000.00', '50000.00', '0.00', 'pending', NULL, '2026-04-23 07:00:56'),
(270, 270, 'Head Office - Kaduwela', 'Diploma Level Courses', 'Diploma in Professional Gemmology (Dip. PGSL)', '5000.00', '200000.00', '5000.00', 'pending', NULL, '2026-04-25 13:28:15'),
(271, 271, 'Kandy', 'Certificate Level Courses', 'Certificate in Basic Gemmology', '2000.00', '50000.00', '0.00', 'pending', NULL, '2026-04-27 05:06:45'),
(272, 272, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Basic Gemmology', '2000.00', '50000.00', '0.00', 'pending', NULL, '2026-04-27 05:52:32'),
(273, 273, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gem Cutting and Polishing (10 DAYS)', '2000.00', '14000.00', '0.00', 'pending', NULL, '2026-04-27 11:29:44'),
(274, 274, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gem Cutting and Polishing (NVQ 3)', '2000.00', '35000.00', '0.00', 'pending', NULL, '2026-04-28 06:03:39'),
(275, 275, 'Head Office - Kaduwela', 'International Courses', 'Gem-A Foundation Course', '10000.00', '589567.22', '0.00', 'pending', NULL, '2026-04-28 14:26:16'),
(276, 276, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Jewellery Manufacturing (NVQ 3)', '2000.00', '20000.00', '0.00', 'pending', NULL, '2026-04-30 05:09:31'),
(277, 277, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Jewellery Manufacturing (NVQ 3)', '2000.00', '20000.00', '0.00', 'pending', NULL, '2026-04-30 06:25:31'),
(278, 278, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Jewellery Designing Technology (NVQ 4)', '2000.00', '60000.00', '0.00', 'pending', NULL, '2026-04-30 17:43:06'),
(279, 279, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Geuda Heat Treatment', '2000.00', '55000.00', '0.00', 'pending', NULL, '2026-05-02 05:29:45'),
(280, 280, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gems and Jewellery Valuation and Marketing', '2000.00', '20000.00', '0.00', 'pending', NULL, '2026-05-02 17:52:36'),
(281, 281, 'Kandy', 'Certificate Level Courses', 'Certificate in Gem Cutting and Polishing (NVQ 4)', '2000.00', '45000.00', '0.00', 'pending', NULL, '2026-05-03 04:54:54'),
(282, 282, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gemmology', '2000.00', '70000.00', '0.00', 'pending', NULL, '2026-05-04 04:24:33'),
(283, 283, 'Head Office - Kaduwela', 'Diploma Level Courses', 'Diploma in Jewellery Manufacturing and Designing Technology (NVQ 5)/ Diploma in Professional Jewellery (Dip. PJSL)', '2500.00', '125000.00', '5000.00', 'pending', NULL, '2026-05-07 10:16:30'),
(284, 284, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Geuda Heat Treatment', '2000.00', '55000.00', '0.00', 'pending', NULL, '2026-05-08 10:52:24'),
(285, 285, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Basic Gemmology', '2000.00', '50000.00', '0.00', 'pending', NULL, '2026-05-10 02:26:01'),
(286, 286, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gem Cutting and Polishing (Weekend)', '2000.00', '35000.00', '0.00', 'pending', NULL, '2026-05-11 09:43:30'),
(287, 287, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Geuda Heat Treatment', '2000.00', '55000.00', '0.00', 'pending', NULL, '2026-05-12 11:33:06'),
(288, 288, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gem Cutting and Polishing (NVQ 4)', '2000.00', '45000.00', '0.00', 'pending', NULL, '2026-05-17 06:54:31'),
(289, 289, 'Head Office - Kaduwela', 'Diploma Level Courses', 'Diploma in Jewellery Manufacturing and Designing Technology (NVQ 5)/ Diploma in Professional Jewellery (Dip. PJSL)', '2500.00', '125000.00', '5000.00', 'pending', NULL, '2026-05-17 14:46:37'),
(290, 290, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Jewellery Assaying and Hallmarking', '2000.00', '45000.00', '0.00', 'pending', NULL, '2026-05-19 02:01:19'),
(291, 291, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gem Cutting and Polishing (NVQ 4)', '2000.00', '45000.00', '0.00', 'pending', NULL, '2026-05-19 05:46:54'),
(292, 292, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gem Cutting and Polishing (NVQ 4)', '2000.00', '45000.00', '0.00', 'pending', NULL, '2026-05-19 09:11:23'),
(293, 293, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gemmology', '2000.00', '70000.00', '0.00', 'pending', NULL, '2026-05-23 04:23:48'),
(294, 294, 'Head Office - Kaduwela', 'Diploma Level Courses', 'Diploma in Jewellery Manufacturing and Designing Technology (NVQ 5)/ Diploma in Professional Jewellery (Dip. PJSL)', '2500.00', '125000.00', '5000.00', 'pending', NULL, '2026-05-24 06:34:45'),
(295, 295, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Basic Gemmology', '2000.00', '50000.00', '0.00', 'pending', NULL, '2026-05-24 22:54:00'),
(296, 296, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Jewellery Manufacturing (NVQ 4)', '2000.00', '50000.00', '0.00', 'pending', NULL, '2026-05-25 06:52:12'),
(297, 297, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gemmology', '2000.00', '70000.00', '0.00', 'pending', NULL, '2026-05-25 17:23:02'),
(298, 298, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gem Cutting and Polishing (10 DAYS)', '2000.00', '14000.00', '0.00', 'pending', NULL, '2026-05-27 07:02:55'),
(299, 299, 'Head Office - Kaduwela', 'Diploma Level Courses', 'Diploma in Jewellery Manufacturing and Designing Technology (NVQ 5)/ Diploma in Professional Jewellery (Dip. PJSL)', '2500.00', '125000.00', '5000.00', 'pending', NULL, '2026-05-27 09:16:14'),
(300, 300, 'Ratnapura', 'Certificate Level Courses', 'Certificate in Geuda Heat Treatment', '2000.00', '55000.00', '0.00', 'pending', NULL, '2026-05-29 17:45:33'),
(301, 301, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Basic Gemmology', '2000.00', '50000.00', '0.00', 'pending', NULL, '2026-05-31 04:20:16'),
(302, 302, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gemmology', '2000.00', '70000.00', '0.00', 'pending', NULL, '2026-05-31 06:00:25'),
(303, 303, 'Ratnapura (NYSC)', 'Certificate Level Courses', 'Certificate in Gem Cutting and Polishing (NVQ 4)', '2000.00', '45000.00', '0.00', 'pending', NULL, '2026-05-31 13:11:07'),
(305, 305, 'Kandy', 'Certificate Level Courses', 'Certificate in Gemmology', '2000.00', '70000.00', '0.00', 'pending', NULL, '2026-06-03 15:05:21'),
(307, 307, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gemmology', '2000.00', '70000.00', '0.00', 'pending', NULL, '2026-06-07 08:31:29'),
(308, 308, 'Head Office - Kaduwela', 'Diploma Level Courses', 'Diploma in Professional Gemmology (Dip. PGSL)', '5000.00', '200000.00', '5000.00', 'pending', NULL, '2026-06-08 07:40:41'),
(309, 309, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gem Cutting and Polishing (10 DAYS)', '2000.00', '14000.00', '0.00', 'pending', NULL, '2026-06-09 03:43:47'),
(310, 310, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gems and Jewellery Valuation and Marketing', '2000.00', '20000.00', '0.00', 'pending', NULL, '2026-06-10 04:57:25'),
(311, 311, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Basic Gemmology', '2000.00', '50000.00', '0.00', 'pending', NULL, '2026-06-10 06:04:51'),
(312, 312, 'Ratnapura', 'Certificate Level Courses', 'Certificate in Gemmology', '2000.00', '70000.00', '0.00', 'pending', NULL, '2026-06-10 06:32:03'),
(313, 313, 'Ratnapura', 'Certificate Level Courses', 'Certificate in Gemmology', '2000.00', '70000.00', '0.00', 'pending', NULL, '2026-06-10 06:56:41'),
(314, 314, 'Head Office - Kaduwela', 'Diploma Level Courses', 'Diploma in Professional Gemmology (Dip. PGSL)', '5000.00', '200000.00', '5000.00', 'pending', NULL, '2026-06-10 14:56:02'),
(315, 315, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gemmology', '2000.00', '70000.00', '0.00', 'pending', NULL, '2026-06-11 20:21:54'),
(316, 316, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gemmology', '2000.00', '70000.00', '0.00', 'pending', NULL, '2026-06-16 07:03:27'),
(317, 317, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Geuda Heat Treatment', '2000.00', '55000.00', '0.00', 'pending', NULL, '2026-06-17 14:12:36'),
(318, 318, 'Ratnapura', 'Certificate Level Courses', 'Certificate in Gem Cutting and Polishing (Weekend)', '2000.00', '35000.00', '0.00', 'pending', NULL, '2026-06-17 14:19:10'),
(319, 319, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gemmology', '2000.00', '70000.00', '0.00', 'pending', NULL, '2026-06-20 06:25:54'),
(320, 320, 'Maradana', 'Certificate Level Courses', 'Certificate in Gem Cutting and Polishing (NVQ 3)', '2000.00', '35000.00', '0.00', 'pending', NULL, '2026-06-20 07:25:44'),
(321, 321, 'Maradana', 'Certificate Level Courses', 'Certificate in Gem Cutting and Polishing (NVQ 3)', '2000.00', '35000.00', '0.00', 'pending', NULL, '2026-06-21 05:41:34'),
(322, 322, 'Kandy', 'Certificate Level Courses', 'Certificate in Jewellery Manufacturing (NVQ 3)', '2000.00', '20000.00', '0.00', 'pending', NULL, '2026-06-21 11:32:09'),
(323, 323, 'Maradana', 'Certificate Level Courses', 'Certificate in Gem Cutting and Polishing (NVQ 4)', '2000.00', '45000.00', '0.00', 'pending', NULL, '2026-06-21 11:38:40'),
(324, 324, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Computer Aided Jewellery Designing and Manufacturing (CAD/CAM)', '2000.00', '60000.00', '0.00', 'pending', NULL, '2026-06-22 05:09:00'),
(325, 325, 'Head Office - Kaduwela', 'Diploma Level Courses', 'Diploma in Professional Gemmology (Dip. PGSL)', '5000.00', '200000.00', '5000.00', 'pending', NULL, '2026-06-22 08:12:20'),
(326, 326, 'Kandy', 'Certificate Level Courses', 'Certificate in Jewellery Designing (Manual)', '2000.00', '43000.00', '0.00', 'pending', NULL, '2026-06-22 16:34:59'),
(327, 327, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Basic Gemmology', '2000.00', '50000.00', '0.00', 'pending', NULL, '2026-06-23 05:34:22'),
(328, 328, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gemmology', '2000.00', '70000.00', '0.00', 'pending', NULL, '2026-06-24 09:57:57'),
(329, 329, 'Ratnapura', 'Certificate Level Courses', 'Certificate in Geuda Heat Treatment', '2000.00', '55000.00', '0.00', 'pending', NULL, '2026-06-25 05:40:16'),
(330, 330, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gems and Jewellery Valuation and Marketing', '2000.00', '20000.00', '0.00', 'pending', NULL, '2026-06-25 05:45:59'),
(331, 331, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gem Cutting and Polishing (10 DAYS)', '2000.00', '14000.00', '0.00', 'pending', NULL, '2026-06-25 10:24:01'),
(332, 332, 'Head Office - Kaduwela', 'Diploma Level Courses', 'Diploma in Professional Gemmology (Dip. PGSL)', '5000.00', '200000.00', '5000.00', 'pending', NULL, '2026-06-25 18:17:53'),
(333, 333, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gem Cutting and Polishing (Weekend)', '2000.00', '35000.00', '0.00', 'pending', NULL, '2026-06-26 08:12:30'),
(334, 334, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gemmology', '2000.00', '70000.00', '0.00', 'pending', NULL, '2026-06-27 05:56:03'),
(335, 335, 'Ratnapura', 'Certificate Level Courses', 'Certificate in Geuda Heat Treatment', '2000.00', '55000.00', '0.00', 'pending', NULL, '2026-06-28 06:48:51'),
(336, 336, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gemmology', '2000.00', '70000.00', '0.00', 'pending', NULL, '2026-06-28 11:56:58'),
(337, 337, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gem Cutting and Polishing (Weekend)', '2000.00', '35000.00', '0.00', 'pending', NULL, '2026-06-28 12:03:19'),
(338, 338, 'Head Office - Kaduwela', 'Diploma Level Courses', 'Diploma in Professional Gemmology (Dip. PGSL)', '5000.00', '200000.00', '5000.00', 'pending', NULL, '2026-06-28 12:11:26'),
(339, 339, 'Ratnapura', 'Certificate Level Courses', 'Certificate in Basic Gemmology', '2000.00', '50000.00', '0.00', 'pending', NULL, '2026-06-29 06:00:08'),
(340, 340, 'Head Office - Kaduwela', 'Diploma Level Courses', 'Diploma in Professional Gemmology (Dip. PGSL)', '5000.00', '200000.00', '5000.00', 'pending', NULL, '2026-06-29 21:30:18'),
(341, 341, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gemmology', '2000.00', '70000.00', '0.00', 'pending', NULL, '2026-06-30 11:23:55'),
(342, 342, 'Ratnapura', 'Diploma Level Courses', 'Diploma in Professional Gemmology (Dip. PGSL)', '5000.00', '200000.00', '5000.00', 'pending', NULL, '2026-06-30 22:54:21'),
(343, 343, 'Head Office - Kaduwela', 'Diploma Level Courses', 'Diploma in Jewellery Manufacturing and Designing Technology (NVQ 5)/ Diploma in Professional Jewellery (Dip. PJSL)', '2500.00', '125000.00', '5000.00', 'pending', NULL, '2026-07-02 08:35:35'),
(344, 344, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Blended Gemmology', '2000.00', '70000.00', '0.00', 'pending', NULL, '2026-07-02 12:41:02'),
(345, 345, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Jewellery Casting and Electro Plating', '2000.00', '50000.00', '0.00', 'pending', NULL, '2026-07-02 18:05:29'),
(346, 346, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Jewellery Assaying and Hallmarking', '2000.00', '45000.00', '0.00', 'pending', NULL, '2026-07-02 19:59:04'),
(347, 347, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Jewellery Casting and Electro Plating', '2000.00', '50000.00', '0.00', 'pending', NULL, '2026-07-03 02:37:35'),
(348, 348, 'Ratnapura', 'Certificate Level Courses', 'Certificate in Gem Cutting and Polishing (NVQ 4)', '2000.00', '45000.00', '0.00', 'pending', NULL, '2026-07-03 15:52:53'),
(349, 349, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gemmology', '2000.00', '70000.00', '0.00', 'pending', NULL, '2026-07-04 09:27:12'),
(350, 350, 'Galle', 'Diploma Level Courses', 'Diploma in Professional Gemmology (Dip. PGSL)', '5000.00', '200000.00', '5000.00', 'pending', NULL, '2026-07-04 18:26:07'),
(351, 351, 'Head Office - Kaduwela', 'Diploma Level Courses', 'Diploma in Professional Gemmology (Dip. PGSL)', '5000.00', '200000.00', '5000.00', 'pending', NULL, '2026-07-05 12:50:11'),
(352, 352, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gem Cutting and Polishing (Weekend)', '2000.00', '35000.00', '0.00', 'pending', NULL, '2026-07-07 13:01:53'),
(353, 353, 'Ratnapura', 'Certificate Level Courses', 'Certificate in Geuda Heat Treatment', '2000.00', '55000.00', '0.00', 'pending', NULL, '2026-07-08 05:07:21'),
(354, 354, 'Ratnapura', 'Certificate Level Courses', 'Certificate in Basic Gemmology', '2000.00', '50000.00', '0.00', 'pending', NULL, '2026-07-08 12:34:59'),
(355, 355, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Computer Aided Jewellery Designing and Manufacturing (CAD/CAM)', '2000.00', '60000.00', '0.00', 'pending', NULL, '2026-07-11 15:23:36'),
(356, 356, 'Kandy', 'Certificate Level Courses', 'Certificate in Jewellery Designing (Manual)', '2000.00', '43000.00', '0.00', 'pending', NULL, '2026-07-11 19:16:27'),
(357, 357, 'Ratnapura', 'Certificate Level Courses', 'Certificate in Basic Gemmology', '2000.00', '50000.00', '0.00', 'pending', NULL, '2026-07-13 06:12:23'),
(358, 358, 'Head Office - Kaduwela', 'Diploma Level Courses', 'Diploma in Jewellery Manufacturing and Designing Technology (NVQ 5)/ Diploma in Professional Jewellery (Dip. PJSL)', '2500.00', '125000.00', '5000.00', 'pending', NULL, '2026-07-14 01:09:31'),
(359, 359, 'Kandy', 'Certificate Level Courses', 'Certificate in Gem Cutting and Polishing (10 DAYS)', '2000.00', '14000.00', '0.00', 'pending', NULL, '2026-07-14 09:53:53'),
(360, 360, 'Maradana', 'Certificate Level Courses', 'Certificate in Gem Cutting and Polishing (NVQ 3)', '2000.00', '35000.00', '0.00', 'pending', NULL, '2026-07-15 00:35:24'),
(361, 361, 'Ratnapura', 'Certificate Level Courses', 'Certificate in Geuda Heat Treatment', '2000.00', '55000.00', '0.00', 'pending', NULL, '2026-07-15 18:20:11'),
(362, 362, 'Head Office - Kaduwela', 'Diploma Level Courses', 'Diploma in Professional Gemmology (Dip. PGSL)', '5000.00', '200000.00', '5000.00', 'pending', NULL, '2026-07-16 09:09:18'),
(363, 363, 'Head Office - Kaduwela', 'Diploma Level Courses', 'Diploma in Professional Gemmology (Dip. PGSL)', '5000.00', '200000.00', '5000.00', 'pending', NULL, '2026-07-16 10:05:00'),
(364, 364, 'Head Office - Kaduwela', 'Diploma Level Courses', 'Diploma in Jewellery Manufacturing and Designing Technology (NVQ 5)/ Diploma in Professional Jewellery (Dip. PJSL)', '2500.00', '125000.00', '5000.00', 'pending', NULL, '2026-07-16 10:06:33'),
(365, 365, 'Head Office - Kaduwela', 'Diploma Level Courses', 'Diploma in Professional Gemmology (Dip. PGSL)', '5000.00', '200000.00', '5000.00', 'pending', NULL, '2026-07-16 12:21:20'),
(366, 366, 'Head Office - Kaduwela', 'Diploma Level Courses', 'Diploma in Jewellery Manufacturing and Designing Technology (NVQ 5)/ Diploma in Professional Jewellery (Dip. PJSL)', '2500.00', '125000.00', '5000.00', 'pending', NULL, '2026-07-16 12:23:02'),
(367, 367, 'Ratnapura', 'Diploma Level Courses', 'Diploma in Professional Gemmology (Dip. PGSL)', '5000.00', '200000.00', '5000.00', 'pending', NULL, '2026-07-16 13:49:40'),
(368, 368, 'Ratnapura', 'Diploma Level Courses', 'Diploma in Professional Gemmology (Dip. PGSL)', '5000.00', '200000.00', '5000.00', 'pending', NULL, '2026-07-16 18:16:54'),
(369, 369, 'Kandy', 'Certificate Level Courses', 'Certificate in Gem Cutting and Polishing (NVQ 3)', '2000.00', '35000.00', '0.00', 'pending', NULL, '2026-07-17 06:54:42'),
(370, 370, 'Ratnapura', 'Diploma Level Courses', 'Diploma in Professional Gemmology (Dip. PGSL)', '5000.00', '200000.00', '5000.00', 'pending', NULL, '2026-07-17 07:45:22'),
(371, 371, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Jewellery Manufacturing (NVQ 4)', '2000.00', '50000.00', '0.00', 'pending', NULL, '2026-07-17 12:31:56'),
(372, 372, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gemmology', '2000.00', '70000.00', '0.00', 'pending', NULL, '2026-07-18 02:46:49'),
(373, 373, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gemmology', '2000.00', '70000.00', '0.00', 'pending', NULL, '2026-07-18 02:48:53'),
(374, 374, 'Ratnapura', 'Diploma Level Courses', 'Diploma in Professional Gemmology (Dip. PGSL)', '5000.00', '200000.00', '5000.00', 'pending', NULL, '2026-07-18 08:39:53'),
(375, 375, 'Head Office - Kaduwela', 'Diploma Level Courses', 'Diploma in Professional Gemmology (Dip. PGSL)', '5000.00', '200000.00', '5000.00', 'pending', NULL, '2026-07-18 13:22:32'),
(376, 376, 'Head Office - Kaduwela', 'Diploma Level Courses', 'Diploma in Professional Gemmology (Dip. PGSL)', '5000.00', '200000.00', '5000.00', 'pending', NULL, '2026-07-18 23:23:09'),
(377, 377, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Gem Cutting and Polishing (Weekend)', '2000.00', '35000.00', '0.00', 'pending', NULL, '2026-07-20 12:58:44'),
(378, 378, 'Head Office - Kaduwela', 'Certificate Level Courses', 'Certificate in Geuda Heat Treatment', '2000.00', '55000.00', '0.00', 'pending', NULL, '2026-07-20 13:04:46'),
(379, 379, 'Head Office - Kaduwela', 'Diploma Level Courses', 'Diploma in Jewellery Manufacturing and Designing Technology (NVQ 5)/ Diploma in Professional Jewellery (Dip. PJSL)', '2500.00', '125000.00', '5000.00', 'pending', NULL, '2026-07-21 03:44:41'),
(380, 380, 'Head Office - Kaduwela', 'Diploma Level Courses', 'Diploma in Professional Gemmology (Dip. PGSL)', '5000.00', '200000.00', '5000.00', 'pending', NULL, '2026-07-22 06:36:13'),
(381, 381, 'Ratnapura', 'Certificate Level Courses', 'Certificate in Geuda Heat Treatment', '2000.00', '55000.00', '0.00', 'pending', NULL, '2026-07-22 06:41:13');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `paid_amount` decimal(10,2) DEFAULT 0.00,
  `due_amount` decimal(10,2) DEFAULT 0.00,
  `slip_file` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `application_id`, `amount`, `method`, `status`, `installment_type`, `transaction_id`, `created_at`, `paid_amount`, `due_amount`, `slip_file`) VALUES
(234, 173, '50000.00', '', 'pending', 'first', NULL, '2025-11-26 11:51:54', '0.00', '50000.00', NULL),
(235, 173, '50000.00', 'Online Payment', 'pending', 'first', NULL, '2025-11-26 11:52:04', '0.00', '50000.00', NULL),
(236, 173, '50000.00', 'Online Payment', 'completed', 'full', 'TXN-DFD70A24-FULL-1764157950', '2025-11-26 11:53:24', '50000.00', '0.00', NULL),
(238, 175, '75000.00', '', 'pending', 'first', NULL, '2025-11-27 03:43:40', '0.00', '75000.00', NULL),
(239, 175, '75000.00', 'Online Payment', 'pending', 'first', NULL, '2025-11-27 03:56:25', '0.00', '75000.00', NULL),
(241, 176, '72000.00', 'Upload Payslip', 'pending', 'first', NULL, '2025-12-11 05:36:40', '0.00', '72000.00', NULL),
(242, 176, '72000.00', 'Online Payment', 'pending', 'first', NULL, '2025-12-11 05:40:28', '0.00', '72000.00', NULL),
(243, 176, '37000.00', 'Online Payment', 'pending', 'first', 'TXN-98E6C6DC-FIRST-1765431749', '2025-12-11 05:43:58', '37000.00', '35000.00', NULL),
(244, 177, '210000.00', 'Upload Payslip', 'pending', 'first', NULL, '2025-12-26 04:16:52', '0.00', '210000.00', NULL),
(245, 178, '57000.00', 'Upload Payslip', 'pending', 'first', NULL, '2025-12-27 08:17:15', '0.00', '57000.00', NULL),
(246, 179, '52000.00', 'Upload Payslip', 'pending', 'first', NULL, '2025-12-28 17:23:41', '0.00', '52000.00', NULL),
(247, 180, '72000.00', 'Upload Payslip', 'pending', 'first', NULL, '2025-12-29 02:33:44', '0.00', '72000.00', NULL),
(248, 181, '52000.00', 'Upload Payslip', 'pending', 'first', NULL, '2025-12-30 12:32:17', '0.00', '52000.00', NULL),
(254, 184, '52000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-01-01 07:13:03', '0.00', '52000.00', NULL),
(255, 185, '22000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-01-02 07:11:38', '0.00', '22000.00', NULL),
(256, 186, '72000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-01-03 06:20:38', '0.00', '72000.00', NULL),
(257, 187, '599567.22', 'Upload Payslip', 'pending', 'first', NULL, '2026-01-03 12:07:38', '0.00', '599567.22', NULL),
(258, 188, '210000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-01-03 19:38:41', '0.00', '210000.00', NULL),
(259, 189, '210000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-01-04 12:19:50', '0.00', '210000.00', NULL),
(260, 190, '52000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-01-05 02:52:48', '0.00', '52000.00', NULL),
(261, 191, '210000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-01-05 08:58:23', '0.00', '210000.00', NULL),
(262, 192, '72000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-01-05 14:25:30', '0.00', '72000.00', NULL),
(269, 195, '210000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-01-08 04:42:18', '0.00', '210000.00', NULL),
(270, 196, '72000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-01-08 09:20:45', '0.00', '72000.00', NULL),
(271, 197, '72000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-01-09 10:34:00', '0.00', '72000.00', NULL),
(272, 198, '62000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-01-10 05:54:55', '0.00', '62000.00', NULL),
(273, 199, '22000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-01-11 06:55:36', '0.00', '22000.00', NULL),
(274, 200, '52000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-01-11 10:05:12', '0.00', '52000.00', NULL),
(275, 201, '599567.22', 'Upload Payslip', 'pending', 'first', NULL, '2026-01-12 12:58:26', '0.00', '599567.22', NULL),
(276, 202, '22000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-01-13 09:55:43', '0.00', '22000.00', NULL),
(277, 203, '37000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-01-13 19:08:43', '0.00', '37000.00', NULL),
(278, 204, '57000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-01-14 02:02:04', '0.00', '57000.00', NULL),
(279, 205, '52000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-01-14 02:05:03', '0.00', '52000.00', NULL),
(280, 206, '62000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-01-15 14:38:25', '0.00', '62000.00', NULL),
(281, 207, '57000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-01-16 05:57:43', '0.00', '57000.00', NULL),
(282, 208, '47000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-01-18 03:05:30', '0.00', '47000.00', NULL),
(283, 209, '47000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-01-19 07:39:45', '0.00', '47000.00', NULL),
(284, 210, '52000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-01-19 12:25:37', '0.00', '52000.00', NULL),
(285, 211, '210000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-01-20 05:32:36', '0.00', '210000.00', NULL),
(286, 212, '37000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-01-20 17:49:10', '0.00', '37000.00', NULL),
(287, 213, '72000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-01-21 16:57:38', '0.00', '72000.00', NULL),
(288, 214, '47000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-01-22 06:18:08', '0.00', '47000.00', NULL),
(289, 215, '132500.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-01-22 13:55:30', '0.00', '132500.00', NULL),
(290, 216, '210000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-01-23 00:35:13', '0.00', '210000.00', NULL),
(291, 217, '72000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-01-24 05:15:15', '0.00', '72000.00', NULL),
(292, 218, '37000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-01-25 04:34:58', '0.00', '37000.00', NULL),
(293, 219, '16000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-01-25 04:39:35', '0.00', '16000.00', NULL),
(294, 220, '52000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-01-27 06:08:19', '0.00', '52000.00', NULL),
(295, 221, '210000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-01-28 04:49:49', '0.00', '210000.00', NULL),
(296, 222, '45000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-01-30 10:10:59', '0.00', '45000.00', NULL),
(297, 223, '47000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-01-30 13:53:29', '0.00', '47000.00', NULL),
(298, 224, '52000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-01-31 10:11:13', '0.00', '52000.00', NULL),
(299, 225, '22000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-01-31 12:20:46', '0.00', '22000.00', NULL),
(300, 226, '37000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-02-01 06:39:54', '0.00', '37000.00', NULL),
(301, 227, '57000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-02-01 09:08:52', '0.00', '57000.00', NULL),
(302, 228, '57000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-02-01 09:09:04', '0.00', '57000.00', NULL),
(303, 229, '16000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-02-01 17:41:00', '0.00', '16000.00', NULL),
(304, 230, '62000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-02-02 03:48:40', '0.00', '62000.00', NULL),
(305, 231, '132500.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-02-08 13:21:13', '0.00', '132500.00', NULL),
(306, 232, '72000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-02-13 08:37:43', '0.00', '72000.00', NULL),
(307, 233, '72000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-02-13 10:29:30', '0.00', '72000.00', NULL),
(308, 234, '72000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-02-16 05:50:51', '0.00', '72000.00', NULL),
(309, 235, '52000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-02-16 06:02:39', '0.00', '52000.00', NULL),
(310, 236, '52000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-02-16 20:39:13', '0.00', '52000.00', NULL),
(311, 237, '72000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-02-19 04:12:25', '0.00', '72000.00', NULL),
(312, 238, '22000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-03-03 18:22:48', '0.00', '22000.00', NULL),
(313, 239, '52000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-03-04 04:22:41', '0.00', '52000.00', NULL),
(314, 240, '52000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-03-09 04:54:10', '0.00', '52000.00', NULL),
(315, 241, '22000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-03-12 15:36:46', '0.00', '22000.00', NULL),
(316, 242, '52000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-03-13 04:07:55', '0.00', '52000.00', NULL),
(317, 243, '72000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-03-13 10:37:57', '0.00', '72000.00', NULL),
(318, 244, '52000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-03-13 14:03:54', '0.00', '52000.00', NULL),
(319, 245, '52000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-03-14 07:42:42', '0.00', '52000.00', NULL),
(320, 246, '72000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-03-14 18:04:38', '0.00', '72000.00', NULL),
(321, 247, '210000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-03-16 20:26:58', '0.00', '210000.00', NULL),
(322, 248, '72000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-03-17 09:08:10', '0.00', '72000.00', NULL),
(323, 249, '72000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-03-18 06:54:13', '0.00', '72000.00', NULL),
(324, 250, '47000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-03-18 13:01:29', '0.00', '47000.00', NULL),
(325, 251, '72000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-03-19 04:51:07', '0.00', '72000.00', NULL),
(326, 252, '210000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-03-19 08:44:21', '0.00', '210000.00', NULL),
(327, 253, '210000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-03-20 08:47:33', '0.00', '210000.00', NULL),
(328, 254, '52000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-03-22 13:46:10', '0.00', '52000.00', NULL),
(329, 255, '72000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-03-24 10:55:07', '0.00', '72000.00', NULL),
(330, 256, '16000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-03-25 02:45:32', '0.00', '16000.00', NULL),
(331, 257, '599567.22', 'Upload Payslip', 'pending', 'first', NULL, '2026-03-26 22:06:15', '0.00', '599567.22', NULL),
(332, 258, '47000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-04-01 07:18:57', '0.00', '47000.00', NULL),
(333, 259, '47000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-04-02 04:27:34', '0.00', '47000.00', NULL),
(334, 261, '16000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-04-04 06:30:39', '0.00', '16000.00', NULL),
(335, 262, '72000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-04-15 07:17:39', '0.00', '72000.00', NULL),
(336, 263, '22000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-04-15 17:07:02', '0.00', '22000.00', NULL),
(337, 264, '37000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-04-18 08:36:32', '0.00', '37000.00', NULL),
(338, 266, '57000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-04-21 11:13:29', '0.00', '57000.00', NULL),
(339, 267, '132500.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-04-22 05:40:49', '0.00', '132500.00', NULL),
(340, 268, '72000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-04-22 07:57:48', '0.00', '72000.00', NULL),
(341, 269, '52000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-04-23 07:00:56', '0.00', '52000.00', NULL),
(342, 270, '210000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-04-25 13:28:15', '0.00', '210000.00', NULL),
(343, 271, '52000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-04-27 05:06:45', '0.00', '52000.00', NULL),
(344, 272, '52000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-04-27 05:52:32', '0.00', '52000.00', NULL),
(345, 273, '16000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-04-27 11:29:44', '0.00', '16000.00', NULL),
(346, 274, '37000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-04-28 06:03:39', '0.00', '37000.00', NULL),
(347, 275, '599567.22', 'Upload Payslip', 'pending', 'first', NULL, '2026-04-28 14:26:16', '0.00', '599567.22', NULL),
(348, 276, '22000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-04-30 05:09:31', '0.00', '22000.00', NULL),
(349, 277, '22000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-04-30 06:25:31', '0.00', '22000.00', NULL),
(350, 278, '62000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-04-30 17:43:06', '0.00', '62000.00', NULL),
(351, 279, '57000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-05-02 05:29:45', '0.00', '57000.00', NULL),
(352, 280, '22000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-05-02 17:52:36', '0.00', '22000.00', NULL),
(353, 281, '47000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-05-03 04:54:54', '0.00', '47000.00', NULL),
(354, 282, '72000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-05-04 04:24:33', '0.00', '72000.00', NULL),
(355, 283, '132500.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-05-07 10:16:30', '0.00', '132500.00', NULL),
(356, 284, '57000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-05-08 10:52:24', '0.00', '57000.00', NULL),
(357, 285, '52000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-05-10 02:26:01', '0.00', '52000.00', NULL),
(358, 286, '37000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-05-11 09:43:30', '0.00', '37000.00', NULL),
(359, 287, '57000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-05-12 11:33:06', '0.00', '57000.00', NULL),
(360, 288, '47000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-05-17 06:54:31', '0.00', '47000.00', NULL),
(361, 289, '132500.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-05-17 14:46:37', '0.00', '132500.00', NULL),
(362, 290, '47000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-05-19 02:01:19', '0.00', '47000.00', NULL),
(363, 291, '47000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-05-19 05:46:54', '0.00', '47000.00', NULL),
(364, 292, '47000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-05-19 09:11:23', '0.00', '47000.00', NULL),
(365, 293, '72000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-05-23 04:23:48', '0.00', '72000.00', NULL),
(366, 294, '132500.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-05-24 06:34:45', '0.00', '132500.00', NULL),
(367, 295, '52000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-05-24 22:54:00', '0.00', '52000.00', NULL),
(368, 296, '52000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-05-25 06:52:12', '0.00', '52000.00', NULL),
(369, 297, '72000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-05-25 17:23:02', '0.00', '72000.00', NULL),
(370, 298, '16000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-05-27 07:02:55', '0.00', '16000.00', NULL),
(371, 299, '132500.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-05-27 09:16:14', '0.00', '132500.00', NULL),
(372, 300, '57000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-05-29 17:45:33', '0.00', '57000.00', NULL),
(373, 301, '52000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-05-31 04:20:16', '0.00', '52000.00', NULL),
(374, 302, '72000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-05-31 06:00:25', '0.00', '72000.00', NULL),
(375, 303, '47000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-05-31 13:11:07', '0.00', '47000.00', NULL),
(378, 305, '72000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-06-03 15:05:21', '0.00', '72000.00', NULL),
(382, 307, '72000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-06-07 08:31:29', '0.00', '72000.00', NULL),
(383, 308, '210000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-06-08 07:40:41', '0.00', '210000.00', NULL),
(384, 309, '16000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-06-09 03:43:47', '0.00', '16000.00', NULL),
(385, 310, '22000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-06-10 04:57:25', '0.00', '22000.00', NULL),
(386, 311, '52000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-06-10 06:04:51', '0.00', '52000.00', NULL),
(387, 312, '72000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-06-10 06:32:03', '0.00', '72000.00', NULL),
(388, 313, '72000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-06-10 06:56:41', '0.00', '72000.00', NULL),
(389, 314, '210000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-06-10 14:56:02', '0.00', '210000.00', NULL),
(390, 315, '72000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-06-11 20:21:54', '0.00', '72000.00', NULL),
(391, 316, '72000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-06-16 07:03:27', '0.00', '72000.00', NULL),
(392, 317, '57000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-06-17 14:12:36', '0.00', '57000.00', NULL),
(393, 318, '37000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-06-17 14:19:10', '0.00', '37000.00', NULL),
(394, 319, '72000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-06-20 06:25:54', '0.00', '72000.00', NULL),
(395, 320, '37000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-06-20 07:25:44', '0.00', '37000.00', NULL),
(396, 321, '37000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-06-21 05:41:34', '0.00', '37000.00', NULL),
(397, 322, '22000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-06-21 11:32:09', '0.00', '22000.00', NULL),
(398, 323, '47000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-06-21 11:38:40', '0.00', '47000.00', NULL),
(399, 324, '62000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-06-22 05:09:00', '0.00', '62000.00', NULL),
(400, 325, '210000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-06-22 08:12:20', '0.00', '210000.00', NULL),
(401, 326, '45000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-06-22 16:34:59', '0.00', '45000.00', NULL),
(402, 327, '52000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-06-23 05:34:22', '0.00', '52000.00', NULL),
(403, 328, '72000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-06-24 09:57:57', '0.00', '72000.00', NULL),
(404, 329, '57000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-06-25 05:40:16', '0.00', '57000.00', NULL),
(405, 330, '22000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-06-25 05:45:59', '0.00', '22000.00', NULL),
(406, 331, '16000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-06-25 10:24:01', '0.00', '16000.00', NULL),
(407, 332, '210000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-06-25 18:17:53', '0.00', '210000.00', NULL),
(408, 333, '37000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-06-26 08:12:30', '0.00', '37000.00', NULL),
(409, 334, '72000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-06-27 05:56:03', '0.00', '72000.00', NULL),
(410, 335, '57000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-06-28 06:48:51', '0.00', '57000.00', NULL),
(411, 336, '72000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-06-28 11:56:58', '0.00', '72000.00', NULL),
(412, 337, '37000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-06-28 12:03:19', '0.00', '37000.00', NULL),
(413, 338, '210000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-06-28 12:11:26', '0.00', '210000.00', NULL),
(414, 339, '52000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-06-29 06:00:08', '0.00', '52000.00', NULL),
(415, 340, '210000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-06-29 21:30:18', '0.00', '210000.00', NULL),
(416, 341, '72000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-06-30 11:23:55', '0.00', '72000.00', NULL),
(417, 342, '210000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-06-30 22:54:21', '0.00', '210000.00', NULL),
(418, 343, '132500.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-07-02 08:35:35', '0.00', '132500.00', NULL),
(419, 344, '72000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-07-02 12:41:02', '0.00', '72000.00', NULL),
(420, 345, '52000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-07-02 18:05:29', '0.00', '52000.00', NULL),
(421, 346, '47000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-07-02 19:59:04', '0.00', '47000.00', NULL),
(422, 347, '52000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-07-03 02:37:35', '0.00', '52000.00', NULL),
(423, 348, '47000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-07-03 15:52:53', '0.00', '47000.00', NULL),
(424, 349, '72000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-07-04 09:27:12', '0.00', '72000.00', NULL),
(425, 350, '210000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-07-04 18:26:07', '0.00', '210000.00', NULL),
(426, 351, '210000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-07-05 12:50:11', '0.00', '210000.00', NULL),
(427, 352, '37000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-07-07 13:01:53', '0.00', '37000.00', NULL),
(428, 353, '57000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-07-08 05:07:21', '0.00', '57000.00', NULL),
(429, 354, '52000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-07-08 12:34:59', '0.00', '52000.00', NULL),
(430, 355, '62000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-07-11 15:23:36', '0.00', '62000.00', NULL),
(431, 356, '45000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-07-11 19:16:27', '0.00', '45000.00', NULL),
(432, 357, '52000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-07-13 06:12:23', '0.00', '52000.00', NULL),
(433, 358, '132500.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-07-14 01:09:31', '0.00', '132500.00', NULL),
(434, 359, '16000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-07-14 09:53:53', '0.00', '16000.00', NULL),
(435, 360, '37000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-07-15 00:35:24', '0.00', '37000.00', NULL),
(436, 361, '57000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-07-15 18:20:11', '0.00', '57000.00', NULL),
(437, 362, '210000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-07-16 09:09:18', '0.00', '210000.00', NULL),
(438, 363, '210000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-07-16 10:05:00', '0.00', '210000.00', NULL),
(439, 364, '132500.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-07-16 10:06:33', '0.00', '132500.00', NULL),
(440, 365, '210000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-07-16 12:21:20', '0.00', '210000.00', NULL),
(441, 366, '132500.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-07-16 12:23:02', '0.00', '132500.00', NULL),
(442, 367, '210000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-07-16 13:49:40', '0.00', '210000.00', NULL),
(443, 368, '210000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-07-16 18:16:54', '0.00', '210000.00', NULL),
(444, 369, '37000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-07-17 06:54:42', '0.00', '37000.00', NULL),
(445, 370, '210000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-07-17 07:45:22', '0.00', '210000.00', NULL),
(446, 371, '52000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-07-17 12:31:56', '0.00', '52000.00', NULL),
(447, 372, '72000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-07-18 02:46:49', '0.00', '72000.00', NULL),
(448, 373, '72000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-07-18 02:48:53', '0.00', '72000.00', NULL),
(449, 374, '210000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-07-18 08:39:53', '0.00', '210000.00', NULL),
(450, 375, '210000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-07-18 13:22:32', '0.00', '210000.00', NULL),
(451, 376, '210000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-07-18 23:23:09', '0.00', '210000.00', NULL),
(452, 377, '37000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-07-20 12:58:44', '0.00', '37000.00', NULL),
(453, 378, '57000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-07-20 13:04:46', '0.00', '57000.00', NULL),
(454, 379, '132500.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-07-21 03:44:41', '0.00', '132500.00', NULL),
(455, 380, '210000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-07-22 06:36:13', '0.00', '210000.00', NULL),
(456, 381, '57000.00', 'Upload Payslip', 'pending', 'first', NULL, '2026-07-22 06:41:13', '0.00', '57000.00', NULL);

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
  `education_background` text DEFAULT NULL,
  `next_payment_date` date DEFAULT NULL,
  `declaration` tinyint(1) NOT NULL DEFAULT 0,
  `declaration2` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `contact_number` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `gmail` varchar(100) DEFAULT NULL,
  `checked` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `student_id_manual`, `reference_no`, `name`, `nic_passport`, `nic_file`, `education_background`, `next_payment_date`, `declaration`, `declaration2`, `created_at`, `contact_number`, `address`, `gmail`, `checked`) VALUES
(173, 'GJRTI001', 'DFD70A24', 'Sutharshan', '1999999999', 'Uploads/nic/1764157891_1. Gayan Edirisingha.jpg', 'test', '2025-11-27', 1, 0, '2025-11-26 11:51:31', '0778439871', 'test address', 'gem_test@sltdigital.site', 2),
(175, 'GJRTI B001', '71F462B6', 'Jegan Viththiya', '930643523V', 'Uploads/nic/1764214264_chinthaka photo.jpg', 'Test', '2025-11-27', 1, 0, '2025-11-27 03:31:04', '0717744341', 'Shangar Mill Road Vantharumoolai', 'jeganviththiya@gmail.com', 1),
(176, NULL, '98E6C6DC', 'SATKUNANATHAN SUTHARSHAN', '930643523V', 'Uploads/nic/1765431400_M7.png', '', NULL, 1, 0, '2025-12-11 05:36:40', '0754347886', 'Shangar Mill Road Vantharumoolai', 'sutharshankanna04@gmail.com', 1),
(177, NULL, 'E81A93BC', 'M.R.M  AASHIF', '200524601192', 'Uploads/nic/1766722612_B3C4161F-EFD5-4752-83B6-2E69A5E61132.jpeg', 'A/l 3c\r\nO/l 1A 3b 4c 1s', NULL, 1, 0, '2025-12-26 04:16:52', '+94768904262', '239/4 ELAGOLLA ESTATE HEERASSAGALA KANDY', 'rizwanaashif8889@gmail.com', 0),
(178, NULL, 'F9BC3857', 'Dimuth Priyankakumara Hakmana Vitharana', '691963438V', 'Uploads/nic/1766823435_71DD502D-968A-496F-A395-AA6C7076E53E.jpeg', 'GCE O/L\r\nGCE A/L\r\nDiploma in Civil Engineering at Open University Nawala', NULL, 1, 0, '2025-12-27 08:17:15', '0778167275', '197,Mandavila Road,Mulleriyawa New Town.', 'dimuth.priyanka@gmail.com', 0),
(179, NULL, 'DF62D46F', 'LAWRANCE ANTHONY MACMILAN', '940030200V', 'Uploads/nic/1766942621_IMG_9348.jpeg', 'Al combined maths done', NULL, 1, 0, '2025-12-28 17:23:41', '0777855070', 'No 83 st benadict street colombo 13', 'm3macmilan@gmail.com', 0),
(180, NULL, 'A65931E2', 'Elamullage Don Rukshan Tharindu Gunawardana', 'N11493338', 'Uploads/nic/1766975624_New Passport.pdf', 'BSc in Engineering', NULL, 1, 0, '2025-12-29 02:33:44', '0715803114', '73/3, Galahitiyawa Road, Siyambalape South, Siyambalape.', 'edrukshan@gmail.com', 0),
(181, NULL, '79CE55CE', 'PRIYAN KARUNARATHNE', '197703801681', 'Uploads/nic/1767097937_ID..jpg', 'G.C.E (A/L)', NULL, 1, 0, '2025-12-30 12:32:17', '0775951185', '11/1 1st Lane\r\nGnanendra Mawatha\r\nNawala', 'priyankarunarathne@gmail.com', 0),
(184, NULL, '73DD43A2', 'Krishon Vinal', '200300401653', 'Uploads/nic/1767251583_image.jpg', 'G. C. E O/L - 8 A 1 B\r\nCertHE in law\r\nLLB (UoL) - ug', NULL, 1, 0, '2026-01-01 07:13:03', '0760064388', 'Wakarawatta, Andeniya, Badulla', 'pkp2774@gmail.com', 0),
(185, NULL, 'F5C688EB', 'Haseena Fuward', '200566504831', 'Uploads/nic/1767337898_Id 2.pdf', 'Completed O/l and Advanced level in Bio science stream and got 3C with B in General English, currently applied for the Bachelor of Software engineering Hons degree in OUSL', NULL, 1, 0, '2026-01-02 07:11:38', '0773175221', '43/Kahawatta,Ambathenna', 'hazeena11@icloud.com', 0),
(186, NULL, '425566E4', 'Asma Amjad Ali', '200559604969', 'Uploads/nic/1767421238_image.jpg', 'Completed o levels\r\n3A4B2C', NULL, 1, 0, '2026-01-03 06:20:38', '0768784535', '42/27,Henawatte,beruwala', 'asmaamjadali3@gmail.com', 0),
(187, NULL, 'B2D09EFC', 'Sunera Bandara', '199331902318', 'Uploads/nic/1767442058_17674419550605624205289745001666.jpg', 'LLB (2:1 Hons) and have passed the Bar Course in England and Wales. Called to the Bar of England and Wales in 2024.', NULL, 1, 0, '2026-01-03 12:07:38', '0771418675', '109/3 Fife Road Colombo 5', 'sunerab@gmail.com', 0),
(188, NULL, 'B302EA53', 'Charaka Deneth Induwara Samarahewa', '200001302522', 'Uploads/nic/1767469121_IMG_1594.jpeg', 'Ordinary level\r\nFoundation of law', NULL, 1, 0, '2026-01-03 19:38:41', '0761183411', 'No.24 Ragala Halgranoya, Nuwara eliya', 'Denethinduwara12@gmail.com', 0),
(189, NULL, 'ABD35C9F', 'Lesley George Joe Khadhaffi', '783272580V', 'Uploads/nic/1767529190_NIC Front-Joe Khadhaffi.png', 'MSc in IT\r\nFoundation Gemology Course at IGA Colombo', NULL, 1, 0, '2026-01-04 12:19:50', '0767624477', '42/1A, First Lane,\r\nMihindugama, Rathnapura', 'khadhaff@yahoo.com', 0),
(190, NULL, '7E984410', 'Rukshan Tharindu Gunawardana', 'N11493338', 'Uploads/nic/1767581568_Passport Rukshan Tharindu.pdf', 'BSc MEchanical Engineer', NULL, 1, 0, '2026-01-05 02:52:48', '0715803114', '73/3, Galahitoyawa Road, Siyambalape South Siyambalape', 'edrukshan@gmail.com', 0),
(191, NULL, '850B905B', 'M.R.M.Aashif', '200524601192', 'Uploads/nic/1767603503_4c72e9aa-97ae-46b8-9857-b4f8034d49d9.jpeg', 'A/L 3c\r\nO/l 1A 3B 4C 1S', NULL, 1, 0, '2026-01-05 08:58:23', '0768904262', '239/4 Elagolla Estate Heerassagala Kandy', 'rizwanaashif8889@gmail.com', 0),
(192, NULL, '531E4689', 'Rumeth Sanjutha Jayawardhana', '200709703532', 'Uploads/nic/1767623130_NIC.jpeg', 'G.C.E Ordinary Level In Sri Lanka - 2023(2024)', NULL, 1, 0, '2026-01-05 14:25:30', '0776050404', 'No 148/11,\r\nSausiri Uyana,\r\nSchool Lane,Kirigampamunuwa\r\nPolgasovita', 'rumethsanjutha@gmail.com', 0),
(195, NULL, '7AC27155', 'Nethumina Kavisanka Pananwala', '200014000598', 'Uploads/nic/1767847338_Nethumina_Pananwala_NIC.jpg', 'BSc Engineering (Hons) in Electrical and Electronic Engineering (Sri Lanka Institute of Information Technology - Malabe)', NULL, 1, 0, '2026-01-08 04:42:18', '0776352505', '149/8/3, Kahanthota Road, Malabe', 'nkptc4732@gmail.com', 0),
(196, NULL, '4103161A', 'KURUPPU APPUHAMILAGE LAHIRU UDARA KURUPPU', '971600691V', 'Uploads/nic/1767864045_Travel Document_1.jpg', '', NULL, 1, 0, '2026-01-08 09:20:45', '0770630747/076713710', '21/70/01 \r\nDADAGAMUWA \r\nVEYANGODA', 'lahirukuruppu01@gmail.com', 0),
(197, NULL, '0B749871', 'Samarasinghe Lasangi Hiranya Samarasinghe', '200585701666', 'Uploads/nic/1767954840_Lasangi NIC.pdf', 'Appeared for the A/L Examination in the Bio System Technology Stream in the year 2024. \r\n Science For Technology - S\r\nBio System technology - S\r\nAgriculture - S', NULL, 1, 0, '2026-01-09 10:34:00', '0762413329', 'Endana road\r\nKanthi sewana, \r\nHorahinella,\r\nKahawatta.', 'lasangisamarasinghe@gmail.com', 0),
(198, NULL, 'EF0C1B77', 'Yahathugoda Badalge Piyumi Mekala.', '875971522v', 'Uploads/nic/1768024495_Piyumi Mekala-Passport.pdf', '- Passed GCE O/L(2003)\r\n-Followed jewellery designing course(manual )\r\nat GJRTI (2010/2011)', NULL, 1, 0, '2026-01-10 05:54:55', '071 1445510', '51/2A,\r\nKoswattagoda Rd,\r\nMatugama.', 'y.b.piyumi.mekala@gmail.com', 0),
(199, NULL, 'FADAA7E9', 'Tharika Nethmini Athurupana', '199982111274', 'Uploads/nic/1768114536_17681144614655548410007531327109.jpg', 'Bsc hons in biosystems engineering', NULL, 1, 0, '2026-01-11 06:55:36', '0719035714', 'No.354, Rathnapura road ,\r\nThalavitiya , parakaduwa.', 'athurupanatharika@gmail.com', 0),
(200, NULL, 'B8852E2B', 'ILANGAKOON MUDIYANSELAGE SHAVINDYA SATHSARANI ABAYARATHNE', '200570404530', 'Uploads/nic/1768125912_20260111_153412-COLLAGE.jpg', 'GCE ORDINARY LEVEL\r\n\r\nFOUNDATION IN DESIGNING CERTIFICATION FROM NATIONAL INSTITUTE OF BUSINESS MANAGEMENT (NIBM)', NULL, 1, 0, '2026-01-11 10:05:12', '0713552175', 'NO 39, NISHSHANKA MAWATHA, WAHARA, KURUNAGALA', 'sathsaranishavindya@gmail.com', 0),
(201, NULL, 'A136CAD8', 'YU LE', 'EK7160070', 'Uploads/nic/1768222706_YULE.jpg', 'Bachelor of arts degree, \r\nlearned elementary course of Gems , want apply FGA certificate.', NULL, 1, 0, '2026-01-12 12:58:26', '0772126666', '4th Floor, NO.481 GALLE ROAD , COLOMBO 03 .', '9948308@qq.com', 0),
(202, NULL, 'FF928EE3', 'Vimukthi Guruge', 'N8849533', 'Uploads/nic/1768298143_Passport-1.png', 'MBA in Business Analytics (Reading), BSc in Software Engineering', NULL, 1, 0, '2026-01-13 09:55:43', '0775720160', '17, Summer City\r\nPathana, Hikkaduwa', 'gurugev@gmail.com', 0),
(203, NULL, '18CFDAAC', 'Hapugoda Arachchi Kankanamge prabodha Nilani', '927162130v', 'Uploads/nic/1768331323_20260114_003555-COLLAGE.jpg', '', NULL, 1, 0, '2026-01-13 19:08:43', '0767887345', 'No 135\r\nMaithrigama \r\nHambantota', 'www.praprabodhanilani@gmail.com', 0),
(204, NULL, '3FF4EC7C', 'Hapugoda Arachchi Kankanamge Prabodha Nilani', '927162130V', 'Uploads/nic/1768356124_IMG-20260114-WA0000.jpg', '', NULL, 1, 0, '2026-01-14 02:02:04', '0767887345', '135 Maithrigama \r\nHambantota', 'www.prabodahapugoda@gmail.com', 0),
(205, NULL, 'BCBD8166', 'Hapugoda Arachchi Kankanamge Prabodha Nilani', '927162130V', 'Uploads/nic/1768356303_IMG-20260114-WA0000.jpg', '', NULL, 1, 0, '2026-01-14 02:05:03', '0767887345', '135 Maithrigama\r\nHambantota', 'www.prabodahapugoda@gmail.com', 0),
(206, NULL, '3E8B4C1D', 'Karunanayakage Tharindi Nishamini Dissanayaka', '200278100808', 'Uploads/nic/1768487905_ID.pdf', 'University of Kelaniya -BA(Hons)  International Studies (3 year) 2023 - present\r\n\r\nPassed G.C.E A/L Examination (Art Scheme)  2021(2022)\r\nArt -A\r\nMedia-A\r\nSinhala-A\r\n \r\nVocational Training Authority (VT A)-NVQ Level 3 Draughtsperson course\r\n\r\n Basic Course in CorelDRAW -Wijeya Graphics(pvt) Ltd.\r\nBasic Course in Graphic Designing-Wijeya Graphics(pvt) Ltd.\r\n \r\nDiploma in Psychology and Counselling (Reading) â€“ LPEC\r\nCampus', NULL, 1, 0, '2026-01-15 14:38:25', '0714323627', 'E 264/1,Koongahagodawatta,Nikapitiya,Ussapitiya.', 'tharindidissanayaka2@gmail.com', 0),
(207, NULL, 'B0C6B9D9', 'Joe Khadhaffi Lesley George', '783272580V', 'Uploads/nic/1768543063_NIC Front-Joe Khadhaffi.png', 'MSC IT/ Foundation of Gemmology', NULL, 1, 0, '2026-01-16 05:57:43', '0767624477', '72 Kurunduwatta Lane\r\nNawala', 'khadhaff@yahoo.com', 0),
(208, NULL, 'B30F9A0A', 'ARR Kareem', '960700392V', 'Uploads/nic/1768705530_20240114_180155.jpg', 'O/L\r\nA/L\r\nDiploma FIATA\r\nDiploma in Gemology (IGA)', NULL, 1, 0, '2026-01-18 03:05:30', '0774244462', '56,\r\nGalagedara,\r\nPadukka.', 'rizaulkareem07@gmail.com', 0),
(209, NULL, '8A489FF3', 'Sajani Herath menike', '957740189V', 'Uploads/nic/1768808385_20211101_163440000_iOS.jpeg', 'MA', NULL, 1, 0, '2026-01-19 07:39:45', '0773637647', '1 pannipitiya road Battaramulla', 'sajaaniherath@hmail.com', 0),
(210, NULL, '886A994C', 'Brahmanage Don Harshana Hasintha Pasqual', '200131202461', 'Uploads/nic/1768825537_NIC.pdf', 'Undergraduate - BBA (Hons) in Accounting and Finance', NULL, 1, 0, '2026-01-19 12:25:37', '0719140972', 'No. 255,\r\nGalewela.', 'Pasqual2001h@gmail.com', 0),
(211, NULL, '6A87DF28', 'Gayan Dhanushka Tennakoon', '842241845v', 'Uploads/nic/1768887156_WhatsApp Image 2026-01-20 at 11.00.43.jpeg', 'Bachelor of Computer applications, Post graduate certificate computer science', NULL, 1, 0, '2026-01-20 05:32:36', '0702272687', '64 Main St, Battaramulla', 'tenako@outlook.com', 0),
(212, NULL, 'D3A67A7B', 'Mohomad Arshath', '199919302542', 'Uploads/nic/1768931350_13973e83-77c4-4cda-bb45-abbdaf69c3bc.jpeg', '', NULL, 1, 0, '2026-01-20 17:49:10', '0764293938', 'No. 27, Widuhalpathana, Kabillewela South, Bandarawela', 'ash0764293938@gmail.com', 0),
(213, NULL, 'FBB703A3', 'AVILIKARA GALAPPATTIGE PRIYANTHI ARUNA KAPUKOTUWA', '666623304V', 'Uploads/nic/1769014658_WhatsApp Image 2026-01-21 at 22.25.25.jpeg', 'GCE O/L', NULL, 1, 0, '2026-01-21 16:57:38', '0740085003', '174/10 Temple Road Thalapathpitiya Nugegoda', 'rasialwis@yahoo.com', 0),
(214, NULL, '62B189C9', 'Tuwan Schareem', '200421001260', 'Uploads/nic/1769062688_NATIONAL IDENTITY CARD Tuwan Schareem .pdf', 'Ordinary Level 5A (Including Mathematics and English )2B 2C\r\nAdvanced Level 1A 1B 1C Commerce Stream', NULL, 1, 0, '2026-01-22 06:18:08', '0743067466', '40A 7/7 Metro Homes Malay Street Colombo 02', 'schareem69@gmail.com', 0),
(215, NULL, 'A79D4B99', 'Arama Beligodagedara Chanuka Akila Beligodagedara', '980703070V', 'Uploads/nic/1769090130_ID.pdf', 'Passed the G.C.E. A/L in the Commerce stream.', NULL, 1, 0, '2026-01-22 13:55:30', '075 7351715', 'NO; 202,\r\nManikkawa,\r\nBambaragahaella,\r\nGampola.', 'akilac769@gmail.com', 0),
(216, NULL, '258E71E2', 'H A Rasith Nadeeshan', '200228202123', 'Uploads/nic/1769128513_IMG_5138.jpeg', '*GCE Advanced Level (A/L) â€“ 3 Passes \r\n*Followed certificate course in Basic Gemmology(Institute: Gem & Jewellery Research and Trainin)', NULL, 1, 0, '2026-01-23 00:35:13', '0761721509', 'No 213 pallemulla halloluwa kandy', 'rasithnadeeshan486@gmail.com', 0),
(217, NULL, '9F0CA744', 'S.Lasangi Hiranya Samarasinghe', '200585701666', 'Uploads/nic/1769231715_NIC_ 2024:2285495:24:03707 .pdf', 'Successfully Done by A/L in Biosystem Technology stream', NULL, 1, 0, '2026-01-24 05:15:15', '0762413329', 'Kanthi Sewana, Horahinella, Kahawatta', 'lasangisamarasinghe@gmail.com', 0),
(218, NULL, 'BE981EAF', 'Adhikari Pelige Sameera Kantha Gunasekara', '893073868V', 'Uploads/nic/1769315698_NIC.pdf', 'B.Sc.Eng (Hons. University of Peradeniya)', NULL, 1, 0, '2026-01-25 04:34:58', '0716817020', 'No.152, Wathupitiwala , Nittambuwa', 'gunasekarask@gmail.com', 0),
(219, NULL, 'CFCAB86F', 'Adhikari Pelige Sameera Kantha Gunasekara', '893073868V', 'Uploads/nic/1769315975_NIC.pdf', 'Bsc.Eng (Hons.)', NULL, 1, 0, '2026-01-25 04:39:35', '0716817020', '152, Wathupitiwala, Nittambuwa', 'gunasekarask@gmail.com', 0),
(220, NULL, 'FDD3A04B', 'Prabakaran Nadarajan', 'S9969299', 'Uploads/nic/1769494099_WhatsApp Image 2026-01-27 at 11.32.10 AM.jpeg', '+2 ,Diploma in computer application', NULL, 1, 0, '2026-01-27 06:08:19', '+918220090222', 'N.puduroad, Nagayakottai (P.O.)vedasundur (T.K) Dindigul\r\nNear ENP school.', 'karurprintercare@gmail.com', 0),
(221, NULL, 'CBD1F2D8', 'Mohomad Rasul Mohomad pasil', '199536003144', 'Uploads/nic/1769575789_20260128_101935.jpg', 'HNDEn', NULL, 1, 0, '2026-01-28 04:49:49', '0769998410', 'No 50,Sumudugama,Lunugala', 'imfazz1995@gmail.com', 0),
(222, NULL, '6C7214A8', 'Mohamed Rifaz Fathima Atheefa', '200467300904', 'Uploads/nic/1769767859_17697677664897071714314201285188.jpg', 'Completed Advanced Level', NULL, 1, 0, '2026-01-30 10:10:59', '0776486017', '105/8,Tennekumbura,Kandy', 'atheefarifaz264@gmail.com', 0),
(223, NULL, '252B5E1C', 'Mohamed Inshaf', '200610301249', 'Uploads/nic/1769781209_1000877058.jpeg', 'Completed Ordinary level at S Thomas\' College Gurutalawa (2022)\r\nCompleted Advanced Level at Bandarawela Central College (Mathematics Stream - 2025 batch) (Results pending)', NULL, 1, 0, '2026-01-30 13:53:29', '768388808', 'Kurundhugolla, Guruthalawa', 'mohamedinshaf3737@gmail.com', 0),
(224, NULL, '19F5A5B8', 'Mohamed', '200017101704', 'Uploads/nic/1769854273_9ed0cf854e67f3e6b387ef09feb36497.jpeg', 'GCE Cambridge O/L', NULL, 1, 0, '2026-01-31 10:11:13', '0761551515', 'C/85/1/2 Panawala Road, Asgangula,Eheliyagoda,70600', 'hkmsaharan@gmail.com', 0),
(225, NULL, '45A6349F', 'Pitipana Arachchige Lahiru Nayanjith', '199401304110', 'Uploads/nic/1769862046_DRIVING.pdf', '', NULL, 1, 0, '2026-01-31 12:20:46', '0777494351', '475/2 awarihena hokandara,south', 'lahirunayanajith94@gmail.com', 0),
(226, NULL, '7B9E2EEE', 'Vidura Prabath Wijethilaka', '200023603319', 'Uploads/nic/1769927994_Id card.pdf', 'O/L, A/L, and degree holder', NULL, 1, 0, '2026-02-01 06:39:54', '0719224769', 'NO 23 ihala bopitiya road pelmadulla', 'viduraprabathwi@gmail.com', 0),
(227, NULL, '77AA8690', 'DHANAWEERA GAMAGE SAMODITHA DIMALSHA GUNARATHNA', '200511101627', 'Uploads/nic/1769936932_IMG_3656.jpeg', 'Waiting for A/L  results.', NULL, 1, 0, '2026-02-01 09:08:52', '0714226551', 'Priyanga Weladasela,Wewalkandura,Kalawana', 'dimalshagunarathna@gmail.com', 0),
(228, NULL, '9B042B16', 'DHANAWEERA GAMAGE SAMODITHA DIMALSHA GUNARATHNA', '200511101627', 'Uploads/nic/1769936944_IMG_3656.jpeg', 'Waiting for A/L  results.', NULL, 1, 0, '2026-02-01 09:09:04', '0714226551', 'Priyanga Weladasela,Wewalkandura,Kalawana', 'dimalshagunarathna@gmail.com', 0),
(229, NULL, 'F0772772', 'Kavindu Sachintha', '200223302720', 'Uploads/nic/1769967660_IMG_5054.jpeg', 'Went to p/L President college divulankadawala\r\nA/L result - Bio system technology â€œCâ€\r\n                     Science for technology â€œCâ€\r\n                     Art. â€œSâ€', NULL, 1, 0, '2026-02-01 17:41:00', '0755493709', 'No,68/1,nadavirugama , divulankadawala,Medirigiriya', 'kavindusachintha2002@gmail.com', 0),
(230, NULL, '60D1B7E7', 'pitipana arachchige lahiru nayanajith', '199401304110', 'Uploads/nic/1770004120_ID.png', '', NULL, 1, 0, '2026-02-02 03:48:40', '0777494351', '475/2 awarihena hokandara south', 'lahirunayanajith94@gmail.com', 0),
(231, NULL, '8B0009FE', 'Shafiya Shafideen', '200361310146', 'Uploads/nic/1770556873_id .pdf', '*O/l & a/l completed ,\r\n*Jewellery manufacturing and designing navq level 04 in maradana technical  ,\r\n* HRM in open university of Sri Lanka, \r\n*certificate in gemology followed by diploma(reading),\r\n* certification in gem bead knotting and designing. (Group class)', NULL, 1, 0, '2026-02-08 13:21:13', '0767459739', '457/2, thakkiya Rd, daluwakottuwa, kochchikade, negombo.', 'shafiyashafideen2003@gmail.com', 0),
(232, NULL, 'EFF4CBCE', 'Gustinnawaduge Dulmini Namalika de Silva', '197964200090', 'Uploads/nic/1770971863_NIC - Front.jpeg', 'Diploma in Chemical Engineering', NULL, 1, 0, '2026-02-13 08:37:43', '0719719507', 'No. 9, Sri Gunarathana Road,\r\nPanadura', 'slaf_flying@hotmail.com', 0),
(233, NULL, '648260B8', 'AHAMED SHAKOOR  AHLAK AHAMED', '200900503006', 'Uploads/nic/1770978570_AHAMED...jpeg', 'DID THE O/L\'S', NULL, 1, 0, '2026-02-13 10:29:30', '0701500217', '453,malay colony ambalantota', 'Aqlakahmed2009@gmail.com', 0),
(234, NULL, '45549020', 'mahesh nilanka', '199204902550', 'Uploads/nic/1771221051_2022-08-16 16-30-37_0195 (2).jpg', 'A/L', NULL, 1, 0, '2026-02-16 05:50:51', '0761020769', 'f-51,\r\nfrankland watta\r\nveyangoda', 'apmnilanka6@gmail.com', 0),
(235, NULL, 'F2C293A4', 'mahesh nilanka', '199204902550', 'Uploads/nic/1771221759_2022-08-16 16-30-37_0195 (2).jpg', 'A/L', NULL, 1, 0, '2026-02-16 06:02:39', '0761020769', 'f-51, frankland watta, \r\nveyangoda', 'apmnilanka6@gmail.com', 0),
(236, NULL, '7D57B0D2', 'Abdul samad Mohamed sasrin', '962914101v', 'Uploads/nic/1771274353_1D74E1D1-B682-47CF-923E-F2079B21F515.jpeg', 'O/l', NULL, 1, 0, '2026-02-16 20:39:13', '0775992753', 'No.140 ragala road padiyapelella', 'sazrin1996@gmail.com', 0),
(237, NULL, '7C0B932B', 'Saman Keerthi de Silva', '197101100891', 'Uploads/nic/1771474345_national-id-front.JPEG', 'G.C.E. (A/L) in bio science stream\r\nCertificate in Web Design (University of Colombo School of Computing)', NULL, 1, 0, '2026-02-19 04:12:25', '0777560118', '63/2, Sri Gunarathana Mawatha,\r\nMount Lavinia.', 'samand71@yahoo.com', 0),
(238, NULL, 'B6ADE3A4', 'Hanan Husaiin Ibrahim', '200507500197', 'Uploads/nic/1772562168_20251220133154864.pdf', 'Dimploma in cyber security', NULL, 1, 0, '2026-03-03 18:22:48', '0750949162', '38/3d Kuda Buthgamuwa Road, kotikawatte', 'hananhusainibrahim22@gmail.com', 0),
(239, NULL, '2207D1F9', 'Rajitha Pethiyagoda', '911033259v', 'Uploads/nic/1772598161_5 National Id Card (Original) (2).pdf', '', NULL, 1, 0, '2026-03-04 04:22:41', '0776846400', '38/23, Anniwatte', 'rajitha.pethiyagoda@gmail.com', 0),
(240, NULL, 'B2811949', 'Wenushka Mallikarachchi', '982842468v', 'Uploads/nic/1773032050_NIC.pdf', 'I have completed a BSc in Computer Science at IIT and am currently working as a Senior UI/UX Designer.', NULL, 1, 0, '2026-03-09 04:54:10', '0771363694', '498/3,Siyambalape road,Heiyanthuduwa', 'wenushka18273@gmail.com', 0),
(241, NULL, 'B0C785CF', 'Narasinha Keshara Bandaralage Lahiru suraj', '200102301243', 'Uploads/nic/1773329806_IMG-20260312-WA0032.jpg', 'O/L pass', NULL, 1, 0, '2026-03-12 15:36:46', '071 456 6852', 'Nitalawa,\r\nMakulawe,\r\nGalgamuwa.', 'surajnarasingha47@gmail.com', 0),
(242, NULL, 'FD184FA2', 'Narasinha Keshara Bandaralage Lahiru suraj', '200102301243', 'Uploads/nic/1773374875_IMG-20260312-WA0043.pdf', 'O/L Pass', NULL, 1, 0, '2026-03-13 04:07:55', '071 456 6852', 'Nitalawa, \r\nMakulawe,\r\nGalgamuwa.', 'surajnarasingha47@gmail.com', 0),
(243, NULL, 'C36B8F4E', 'Ahamed Hafeel', '200436404187', 'Uploads/nic/1773398277_2024-04-02_15_41_52.jpeg', '', NULL, 1, 0, '2026-03-13 10:37:57', '0755060110', '', 'ahamedhafeel29@gmail.com', 0),
(244, NULL, '1AD14B93', 'Kavishka Supun', '200431601194', 'Uploads/nic/1773410634_Screenshot_20260313_193315_Drive.jpg', 'Advanced level', NULL, 1, 0, '2026-03-13 14:03:54', '0750632505', '104/A Alapalawala Handessa', 'skavishka393@gmail.com', 0),
(245, NULL, '7636169B', 'Manikkuwadura Vishwa Rameeda De Silva', '892903344V', 'Uploads/nic/1773474162_NIC_Sinhala - Vishwa De Silva.pdf', 'I am a graduate from SLIIT with a BSc (Hons) in Information Technology, and currently working as an Engineer.', NULL, 1, 0, '2026-03-14 07:42:42', '0717677443', '04, Ganegoda, Rathgama', 'vishwarameeda@gmail.com', 0),
(246, NULL, '29934078', 'Mohan Philaman', '199625102798', 'Uploads/nic/1773511478_20251017_123939-pica.png', 'Completed O/L & A/L', NULL, 1, 0, '2026-03-14 18:04:38', '0757281155', '1B/F11/U03 , siyapath sewana housing scheme , Colombo 09', 'allenphilaman07@gmail.com', 0),
(247, NULL, '9546E378', 'DHARMASIRI PATABANDIGE RAJITHA MADURANGA ARIYADASA', '911580772V', 'Uploads/nic/1773692818_20250707_100734.jpg', 'B.Sc. Entrepreneurship (Special) Degree - University of Sri Jayewardenepura', NULL, 1, 0, '2026-03-16 20:26:58', '0702578698', 'NO. 218, BANDARANAYAKE MAWATHA, WERAHERA, BORALESGAMUWA.', 'raji.maduranga@gmail.com', 0),
(248, NULL, '655ED31B', 'SAMARASUNDARA SENEVIRATHNA MUHANDIRAMGE MALINDU NIMANTHA SENEVIRATHNA', '921770456V', 'Uploads/nic/1773738490_ID.jpg', 'Master Business Administration', NULL, 1, 0, '2026-03-17 09:08:10', '0770497491', 'No,83 Wawanahena,Katukurunda,Henegama (W,P)', 'malindus007@gmail.com', 0),
(249, NULL, '63B5D09E', 'UDUWAKA HEWAGE PASINDU HANSAKA KARUNARATHNA', '199835401399', 'Uploads/nic/1773816853_20260318_122202.jpg', 'HND in Business Management \r\nUndergraduate', NULL, 1, 0, '2026-03-18 06:54:13', '0760289709', '176/A NATHUDUWA KELANIYA', 'pasindukarunarathne98@gmail.com', 0),
(250, NULL, 'A7A2EE4C', 'Ganji sreekar Lakshman', 'Z7150364', 'Uploads/nic/1773838889_IMG-20250506-WA0006.jpg', 'Completed MBA from icfai business school in hyderabad India, actively into family jewelry business from 2021, Completed polished diamond graduate and cad jewelry design diploms from Mumbai\'s reputed jewlery institute. Been into international study tours as well like Zambia, Tanzania and visited Sri Lanka as well for gem tour and had opportunity to meet national gem and jewelry authority of ministry of gems and jewelry department chairman in a meeting', NULL, 1, 0, '2026-03-18 13:01:29', '+91 8074146783', '11-7-22/a beside, e seva huda colony, Rajayogi Jewelers, kothapet, saroornagar, Hyderabad 500035', 'ganjisreekar@gmail.com', 0),
(251, NULL, '69598033', 'Herath Mudiyanselage Chathurika Madubashini Herath', 'N9947882', 'Uploads/nic/1773895867_Passport - Herath HMCM.pdf', 'BSc (Hons) in Marine and Freshwater Sciences\r\nSpecialization: Oceanography and Marine Geology\r\nUniversity of Ruhuna, Sri Lanka.\r\n\r\nResearch Collaborator in Paleoceanography (Current) â€“ Focus: Marine Sediment Analysis and Geochemical Sampling.\r\n\r\nNote: As a Marine Geology graduate, I am seeking to apply my knowledge of Earth minerals to the professional identification and analysis of gemstones.', NULL, 1, 0, '2026-03-19 04:51:07', '0714177818', 'No. 118, Rajasirila, Bakmeegolla, Ibbagamuwa', 'herathc17@gmail.com', 0),
(252, NULL, '7C2C4EE6', 'Ruvindya Wickrama Arachchi', '200282500741', 'Uploads/nic/1773909861_id copy.pdf', 'I completed my G.C.E. Ordinary Level examination in the English medium with 7 As, and my G.C.E. Advanced Level examination in the English medium with 3 As, achieving a Z-score of 2.0648. I am currently a final-year undergraduate reading for a BSc (Hons) in Finance at the University of Sri Jayawardenepura. I am. Currently gaining practical exposure as a Finance Intern at the London Stock Exchange. In addition, I am reading for the Corporate Level of Chartered Accountancy at CA Sri Lanka, further strengthening my professional and technical knowledge in finance and accounting.', NULL, 1, 0, '2026-03-19 08:44:21', '0715143220', '123/1,2nd Mile Post, siyambalagoda, polgasowita', 'ruvindya20@gmail.com', 0),
(253, NULL, '624F7A29', 'Farees Umair', '200630802312', 'Uploads/nic/1773996453_WhatsApp Image 2026-03-20 at 2.12.14 PM.jpeg', 'GCE A/L Examination (2025) - Results Pending,\r\nCompleted a gem business certificate course at Gem Ethics Academy (Zafran Sir)', NULL, 1, 0, '2026-03-20 08:47:33', '0778814288', 'No. 5/1, Hussain Avenue, Matale', 'umairfarees3@gmail.com', 0),
(254, NULL, '1107224E', 'Thusith Fonseka', '198925103422', 'Uploads/nic/1774187170_IMG_6108.jpg', 'Customs House Agent \r\nSri Lanka Custom\r\nDecember 7, 2019\r\n Marine Surveyor                                                                                         \r\nCeylon Chamber of Commerce \r\nJanuary 2018\r\n ICS Ship Charterer and Broker', NULL, 1, 0, '2026-03-22 13:46:10', '0773081208', '121/B Cemetry Road \r\nThalapathpitiya \r\nNugegoda \r\nSri lanka \r\nPostal code  : 10250', 'thusith.fonseka@gmail.com', 0),
(255, NULL, 'A3CB2990', 'Dilan Chinthaka', '921850158v', 'Uploads/nic/1774349707_01.jpg', '', NULL, 1, 0, '2026-03-24 10:55:07', '0767356482', 'No, 209, Kongahahena Korathota, kaduwela', 'dilan.dcchinthaka.1992@gmail.com', 0),
(256, NULL, '62BC9B03', 'Hewawasam thuduwawattage anushka maduranga rajapaksha', '871471797v', 'Uploads/nic/1774406732_IMG_6040.jpeg', 'Attorney-at-law', NULL, 1, 0, '2026-03-25 02:45:32', '0772331176', '249/11, thalawathugoda road, pita kotte', 'chamber.arajapakse@gmail.com', 0),
(257, NULL, '71A866EE', 'Sreekar', 'Z7150364', 'Uploads/nic/1774562775_WhatsApp Image 2026-03-27 at 3.32.54 AM.jpeg', 'Completed BBA and MBA from ICFAI business school. \r\ndone diploma in graduate polished diamonds, cad jewelry designing\r\nactively into family business from last 4 years', NULL, 1, 0, '2026-03-26 22:06:15', '+918074146783', 'BESIDE E SEAV HUDA COLONY, SAROORNAGAR, HYDERABAD, RANGAREDDY. 500035, RAJAYOJI JEWELLERS', 'ganjisreekar@gmail.com', 0),
(258, NULL, '6FFB0D72', 'A.M.Theekshana Rukshan Dharmawansha', '200213901560', 'Uploads/nic/1775027937_WhatsApp Image 2026-04-01 at 12.30.41.pdf', 'General Certificate Of Education (Advanced Level) Examination (01)Distinction (01)Very Good Pass (01)Credit Pass (Commerce stream 2021)\r\n\r\nGeneral Certificate Of Education (Ordinary Level) Examination,(02)Very Good Pass (02)Credit Pass (04)Ordinary Pass\r\n\r\nand I have artistic abilities for fine work.', NULL, 1, 0, '2026-04-01 07:18:57', '0767518350 / 0778540', 'Aluthwela,Kotabowa,Medagama,Bibile', 'bhashi1818@gmail.com', 0),
(259, NULL, 'D05D80DA', 'M.R AZKI AHAMED', '200622700131', 'Uploads/nic/1775104054_IMG_2543.jpeg', 'Completed Ordinary level\r\nCompleted Advanced level\r\nAat qualified', NULL, 1, 0, '2026-04-02 04:27:34', '0766992248', '80/3A BARNS RATHWATHA MAWATHA BALANGODA', 'azkiahammed19@gmail.com', 0),
(260, NULL, '5A70B2F4', 'Kajananan Jegatheeswaran', '200132604916', 'Uploads/nic/1775105589_69637AE4-327C-485D-8D77-EB55177E50E5.jpeg', 'School : Colombo International School\r\nOL : 7A*AC ; AL: 5A*\r\n\r\nUniversity : Imperial College London, UK \r\nMEng Civil Engineering', NULL, 1, 0, '2026-04-02 04:53:09', '0764302995', '11 Kelankaduwa Place, Colombo 06', 'kajananan21@iCloud.com', 0),
(261, NULL, '16C474A2', 'Lahiru Ashan Wijesinghe', '872860320V', 'Uploads/nic/1775284239_NIC.pdf', '', NULL, 1, 0, '2026-04-04 06:30:39', '0711068944', '', '', 0),
(262, NULL, 'CB6B1E95', 'Mohan Philaman', '199625102798', 'Uploads/nic/1776237459_20251017_123939-pica.png', 'Successfully completed O/L and A/L', NULL, 1, 0, '2026-04-15 07:17:39', '0757281155', '1B/F11/U03 SIYAPATH SEWANA ,COLOMBO 09', 'allenphilaman07@gmail.com', 0),
(263, NULL, 'F7A9E654', 'THISAN MINDIYA KULATHILAKA', '200324001118', 'Uploads/nic/1776272822_WhatsApp Image 2026-04-15 at 22.31.03.jpeg', 'University undergraduate', NULL, 1, 0, '2026-04-15 17:07:02', '0769652272', '80/13, manamendra mawatha, rathnapura road, Avissawella', 'mindiyakulathilake648@gmail.com', 0),
(264, NULL, 'BC7E3FE8', 'Naseef AR', '942662360V', 'Uploads/nic/1776501392_photo6152110300548147723.jpg', 'Degree in Computer Science and Englineering.\r\n(I have also completed courses in gem identification and heat treatment. Looking to learn and gain some knowledge on cutting and polishing)', NULL, 1, 0, '2026-04-18 08:36:32', '0767324216', '07, Aluthnuwara Road, Hingula', 'naseef14@gmail.com', 0),
(265, NULL, '55192DAB', 'Naseef AR', '942662360V', 'Uploads/nic/1776501586_photo6152110300548147723.jpg', 'Degree in Computer Science & Engineering.\r\n(also have completed a course in gem identification and heat treatment. looking to learn about cutting and polishing + international trading + valuation)', NULL, 1, 0, '2026-04-18 08:39:46', '+94767324216', '07, Aluthnuwara Rd, Hingula', 'naseef14@gmail.com', 0),
(266, NULL, '487091A6', 'Ranasinghe Arachchilage Darshana Prasad Rathnasinghe', '893561722V', 'Uploads/nic/1776770009_NIC Darshana.pdf', 'Bsc Agricultural Technology & Management, Faculty of Agriculture, University of Peradeniya', NULL, 1, 0, '2026-04-21 11:13:29', '0767039716', 'No 81, 1st step, Aluth Malkaduwawa, kurunegala', 'radpdarshana@gmail.com', 0),
(267, NULL, '40FFBCDA', 'M D Dilantha Maneth Perera', '198318401966', 'Uploads/nic/1776836449_Dilantha Perera ID Card.png', 'Professional Graguate Diploma in IT (Level 6)', NULL, 1, 0, '2026-04-22 05:40:49', '0772259232', '81/15 A\r\nRukmal Mv, Temple road, Rukmalgama, Kottawa', 'dilanthaonline@gmail.com', 0),
(268, NULL, '9565D6F3', 'Jason Daniel Subandriyo', '199816910276', 'Uploads/nic/1776844668_Jason ID 2026.jpg', 'Bachelors in Commerce, Univeristy of Woolongong, Dubai Campus, UAE', NULL, 1, 0, '2026-04-22 07:57:48', '0771233683', '10A 121 Residences\r\nNo 121 Park Road', 'silverines.muller@gmail.com', 0),
(269, NULL, '20B59D14', 'B.Mithula hansaja', '200915700306', 'Uploads/nic/1776927656_IMG_4250.jpeg', 'After o/l', NULL, 1, 0, '2026-04-23 07:00:56', '0741960673', '176/1 Bokutupalassa uduwila thissamaharama', 'mithulahansaja@gmail.com', 0),
(270, NULL, 'FF3FBF4E', 'IBRALEBBE MUHAMMADHU MUSTHAKEEM', '941114563V', 'Uploads/nic/1777123695_CamScanner 2026-04-25 18.43.pdf', 'GCE A/L 3 subjects  QUALIFIED in Mathematics Stream \r\n NDT in Mathematics ( Government Teacher)', NULL, 1, 0, '2026-04-25 13:28:15', '0771600993', '41,Ansari Masjidh Road, Oluvil - 05', 'musthakeem787@gmail.com', 0),
(271, NULL, 'A9DA47F9', 'Senthil kumaran Balakrishnan', '198027602950', 'Uploads/nic/1777266405_IMG_7354 (2).jpg', 'B.E(Bachelor of Engineering)', NULL, 1, 0, '2026-04-27 05:06:45', '0773661055', '36/2A/1 Vidhyartha Mawatha,Kandy', 'senstepn@gmail.com', 0),
(272, NULL, '2CFC954F', 'Balage Mithula hansaja', '200915700306', 'Uploads/nic/1777269152_IMG_4389.jpeg', 'after o/l Pending results', NULL, 1, 0, '2026-04-27 05:52:32', '0741960673', '176/1 Bokutupalassa , uduwila , thissamaharama', 'mithulahansaja@gmail.com', 0),
(273, NULL, '087774B1', 'Anushka De Siilva', '882132994V', 'Uploads/nic/1777289384_IMG_2921.jpeg', 'O/L , A/L and NIBM diploma in computer system design', NULL, 1, 0, '2026-04-27 11:29:44', '0777270406', 'No 63/93i , Dambahena Road, 4th Lane, Maharagama', 'kadkdesilva@gmail.com', 0),
(274, NULL, 'F92058B7', 'K.T.E.N.Subasena', '572071375V', 'Uploads/nic/1777356219_IMG_20260428_113223.jpg', 'GCE AL. Maths', NULL, 1, 0, '2026-04-28 06:03:39', '0776101957', '502/5/2, 6th lane, Halbarawa Gardens, Thalahena, Malabe', 'esala257@gmail.com', 0),
(275, NULL, '001C38AF', 'Mohottige Kalidu Ranmith Dayaratna', '200423403876', 'Uploads/nic/1777386376_WhatsApp Image 2026-04-28 at 7.54.02 PM.jpeg', 'G.C.E. Advanced Level (Physical Science) - All 3 Subjects \"S\" passes in one sitting.', NULL, 1, 0, '2026-04-28 14:26:16', '+94719494189', '366, Thekkawatta, Palatota, Kalutara South.', 'ranmithdayaratna04@gmail.com', 0),
(276, NULL, '5254DE98', 'M J K C UDayanganie', '807050800V', 'Uploads/nic/1777525771_IMG_0498.jpeg', 'I have completed O/L and A/L.', NULL, 1, 0, '2026-04-30 05:09:31', '0774894050', 'Metro Manar Resudencies \r\n45/1A, Braybrooke Strret\r\nColombo 02', 'chamila109@yahoo.com', 0),
(277, NULL, '85076AF4', 'Lishan Chamuditha', '200534802223', 'Uploads/nic/1777530331_IMG_2598.JPG', 'Ordinary Level (O/L) Completed', NULL, 1, 0, '2026-04-30 06:25:31', '0703653935', '284, Pahala Imbulgoda imbulgoda.', 'lishanbro05@gmail.com', 0),
(278, NULL, 'EC142002', 'Mahalekame Gamagedara Manuja Bandara', '200835100400', 'Uploads/nic/1777570986_IMG-20260430-WA0027.jpg', 'GCE O/L', NULL, 1, 0, '2026-04-30 17:43:06', '0773069534', 'No. 533, Victoria Range, Digana', 'lankabhoomioffice@gmail.com', 0),
(279, NULL, '59D470F4', 'IBRALEBBE MOHAMMETHU MSTHAKEEM', '941114563V', 'Uploads/nic/1777699785_IMG-20260425-WA0018.jpg', '', NULL, 1, 0, '2026-05-02 05:29:45', '0771600993', '41, Ansari masjid road oluvil-5', 'musthakeem787@gmail.com', 0),
(280, NULL, '85AD40E6', 'Sashitha Charunga', '200124703236', 'Uploads/nic/1777744356_Identity card.pdf', 'I am currently an undergraduate at the University of Moratuwa', NULL, 1, 0, '2026-05-02 17:52:36', '0714800710', 'Indunil\r\nwewagoda,wathukanda', 'sashithacharungayapaindunilper@gmail.com', 0),
(281, NULL, 'F222EC2C', 'YGAUS  Bandara', '19860961903', 'Uploads/nic/1777784094_Image_00003.pdf', 'O/l', NULL, 1, 0, '2026-05-03 04:54:54', '0773663959/071364890', 'No 46/A wawathupola Alawathugoda', 'udayab992@gmail.com', 0),
(282, NULL, '671E08EC', 'Bulathsinhalage Chinthaka Cooray', '199324901451', 'Uploads/nic/1777868673_My ID.pdf', 'Successfully passed the GCE Ordinary Level and Advanced Level examinations.', NULL, 1, 0, '2026-05-04 04:24:33', '0755302900', 'No 05/T20, Baseline Mawatha, Borella, Colombo 08', 'chinthakacooray93@gmail.com', 0),
(283, NULL, '8743F303', 'Kavya Devmi Weerawickrama', '200263700251', 'Uploads/nic/1778148990_NIC copy.pdf', '2022 A/L 3 pass in 1st sitting. I do the A/L art stream.', NULL, 1, 0, '2026-05-07 10:16:30', '0782396466', '130/5, Egodawatta, Boralesgamuwa.', 'kavyaweerawickrama@gmail.com', 0),
(284, NULL, '0F5FABE7', 'Nuwan Chamara Mudannayake', '198804901083', 'Uploads/nic/1778237544_NIC front.jpg', 'I have completed a MSc in sustainable process engineering at University of Moratuwa in 2025 and I completed BSc in Mineral Resources and technology specialized in mineral processing technology at Uva wellassa university Badulla in 2015.', NULL, 1, 0, '2026-05-08 10:52:24', '0712380224', 'No 1/75\r\nBanduragoda', 'nuwanmudannayake@gmail.com', 0),
(285, NULL, '168F5533', 'Kankanamge Chathura Nipun Kaushalya', '200007300837', 'Uploads/nic/1778379961_CamScanner 04-03-2023 22.24.pdf', '', NULL, 1, 0, '2026-05-10 02:26:01', '0740535212', '', 'chathura200313@gmail.com', 0),
(286, NULL, '2C6C78AB', 'D A I ATHUKORALA', '960770617V', 'Uploads/nic/1778492610_WhatsApp Image 2026-05-11 at 3.12.46 PM.jpeg', 'O/L', NULL, 1, 0, '2026-05-11 09:43:30', '0713533462', 'No. 43/13, Singhe road, keragapokuna, wattala.', 'ishara308@gmail.com', 0),
(287, NULL, 'A97E43B1', 'Mudannayake Appuhamilage Nuwan Chamara', '198804901083', 'Uploads/nic/1778585586_NIC front.jpg', 'I have completed a MSc in sustainable process Engineering at university of Moratuwa in 2025 and I have a BSc special degree in Mineral Resources and technology specialized in mineral processing technology at Uva Wellassa University Badulla.', NULL, 1, 0, '2026-05-12 11:33:06', '0712380224', 'No 1/75\r\nBanduragoda\r\nMirigama', 'nuwanmudannayake@gmail.com', 0),
(288, NULL, '33CDF19C', 'M. M. Madhushi Wijayasooriya', '200558603780', 'Uploads/nic/1779000871_CamScanner 02-28-2024 15.14_4.jpg', 'E. C. G. Advanced Level', NULL, 1, 0, '2026-05-17 06:54:31', '0772159225', 'No. 357/5, Embaraluwa South, Weliweriya', 'madhushiwijayasooriya05@gmail.com', 0),
(289, NULL, '5E057B9A', 'Rashmi Dinithya Rathnayake', '200252402659', 'Uploads/nic/1779029197_ID.pdf', 'I have successfully completed my G.C.E. Ordinary Level (O/L) and Advanced Level (A/L) examinations, and I am currently in the final year of my university studies.', NULL, 1, 0, '2026-05-17 14:46:37', '0716599769', '25/7 , Korenailis Mawatha , Thalapathpitiya\r\nNugegoda', 'rashmirathnayake2002@gmail.com', 0),
(290, NULL, 'FA6F06F8', 'Koggala Maha Vidanage Thishakya Sachintha Pandukabhaya Silva', '200636003584', 'Uploads/nic/1779156079_IMG_1406.jpeg', 'Advance level Bio stream', NULL, 1, 0, '2026-05-19 02:01:19', '0707612257', '87, Earnest place, Laxapathiya , Moratuwa', 'pandukabhaya2006@gmail.com', 0),
(291, NULL, 'DA28D4B4', 'Kusal Pasanjith', '983140467V', 'Uploads/nic/1779169614_17791695844022957141488370471959.jpg', 'BEng Electrical Engineering', NULL, 1, 0, '2026-05-19 05:46:54', '0773238923', 'Kotte', 'kusalpasanjith98@gmail.com', 0),
(292, NULL, '1E2831C0', 'Tharindu Weeramanthri', '862944356V', 'Uploads/nic/1779181883_received_10206006336069244.jpeg', 'O/L, A/L, NDSM (SLIM)', NULL, 1, 0, '2026-05-19 09:11:23', '0777865965', 'No.199, Averiyawatta, Alubomulla, Panadura', 'tharindu865965@gmail.com', 0),
(293, NULL, '96B34FC2', 'Mohan Philaman', '199625102798', 'Uploads/nic/1779510228_20251017_123939-pica.png', 'I have successfully completed my Ordinary level & Advance level', NULL, 1, 0, '2026-05-23 04:23:48', '0757281155', '1B/F11/U03 Siyapathsevana , Colombo 09', 'allenphilaman07@gmail.com', 0),
(294, NULL, 'D09A561B', 'Welikala Appuhamillage Dona Kaweesha Vidusarani Samaraweera', '200558104495', 'Uploads/nic/1779604485_17159.pdf', 'I have  completed my G.C.E. Advanced Level (A/L) examination with 3 \'A\' passes in Political Science, History, and Japanese Language. Additionally, I hold the Japanese Language Proficiency Test (JLPT) N3 qualification. Currently, I am a undergraduate student at the University of Colombo, pursuing a Bachelor of Arts (BA) Degree on weekdays, majoring in Economics, International Relations, and Communication & Creative Arts.', NULL, 1, 0, '2026-05-24 06:34:45', '0768529316', '294,\r\nSamagi Mawatha,\r\nHewagama,\r\nKaduwela', 'kaweesamaraweera@gmail.com', 0),
(295, NULL, 'EF1CD6E2', 'Tariq Ramzeen', '200431000667', 'Uploads/nic/1779663240_photo_2023-05-29_19-49-37.jpg', 'University studying', NULL, 1, 0, '2026-05-24 22:54:00', '0705746846', 'Negombo', 'tariqramzeen@gmail.com', 0),
(296, NULL, '58197044', 'KAHAWALA DABARA KAPUGE PRAVEEN SANKALPA', '199620000401', 'Uploads/nic/1779691932_4323acbe-638a-4fb1-93db-7d7359baef89.jpeg', '', NULL, 1, 0, '2026-05-25 06:52:12', '0767629244', '98/B, Viharamawatha,Kothalawala,Kaduwela', '', 0),
(297, NULL, 'EC6A80DB', 'Mayantha Jayawardena', '199436603211', 'Uploads/nic/1779729782_nic.pdf', '', NULL, 1, 0, '2026-05-25 17:23:02', '0714977770', '202, \"Ramya\", Seelawimala Mawatha, Pore\r\nAthurugiriya', 'mpjayawardena@gmail.com', 0),
(298, NULL, '19A81D24', 'Dilan Darshana', '882794210V', 'Uploads/nic/1779865375_IMG_7520.jpg', 'Advanced Level', NULL, 1, 0, '2026-05-27 07:02:55', '0777153152', '', 'dilan.darshana@ikman.lk', 0),
(299, NULL, 'D266610D', 'Umar Hamdhan', '200409401075', 'Uploads/nic/1779873374_Scanned_20260527-1443.pdf', 'Bsc Computer science, Northwood University Michigan, via ANC Education', NULL, 1, 0, '2026-05-27 09:16:14', '0761499021', 'A,130/3/2, Hondenigoda, Mawanella', 'umarhamdhan23@gmail.com', 0),
(300, NULL, '3D76AA91', 'Iddamalgodage Don Ayesh Chathuranga', '970312935V', 'Uploads/nic/1780076733_Passportcopy.pdf', 'Msc Business Administration USJP', NULL, 1, 0, '2026-05-29 17:45:33', '0753468995', 'No.146\r\nSamagipura\r\nRatnapura', 'iddamalgodaayesh@gmail.com', 0),
(301, NULL, '98B4741D', 'P.K Anuk sathsara pothupitiya', '200810700127', 'Uploads/nic/1780201216_WhatsApp Image 2026-05-31 at 9.46.32 AM.jpeg', 'O/ls - 6As 3Cs and pending results in edexcel A/Ls in commerce stream in st josephs college colombo 10', NULL, 1, 0, '2026-05-31 04:20:16', '0751380039', '34/1 Parakandeniya Imbulgoda', 'anukpothupitiya1@gmail.com', 0),
(302, NULL, 'EF010DFA', 'Samadhi Wanigasekara', '200485202390', 'Uploads/nic/1780207225_IMG_20260531_112308-01.jpeg', '27 the district ranker of kaluthara district in art stream 2023/2024 GCE A/L Examination.\r\nUndergraduate of Faculty of Law, University of Colombo (2nd year)', NULL, 1, 0, '2026-05-31 06:00:25', '0773156436', 'No.207/06, Ranaviru gammanaya ,bodhirajagama ,ingirya', 'samadhiwanigasekara857@gmail.com', 0),
(303, NULL, 'B35CBF9A', 'Sreekar', 'Z7150364', 'Uploads/nic/1780233067_IMG-20250506-WA0006(1).jpg', '', NULL, 1, 0, '2026-05-31 13:11:07', '+91 8074146783', 'Rajayogi Jewelers, saroornagar, kothapet, Hyderabad, 500035', 'ganjisreekar@gmail.com', 0),
(305, NULL, '0BAF14FE', 'Sathmi Tharushika Galagama', '200482500721', 'Uploads/nic/1780499121_NIC.pdf', '2021 Ordinary Level- Kandy Girls High School- 3B,4C,2S \r\n2023 Advance level -Kandy Girls High School- 1C,2S,1F\r\n2024 - ICBT Kandy Campus- International Diploma in English \r\n2025- ICBT Kandy Campus- Higher National Diploma in business management', NULL, 1, 0, '2026-06-03 15:05:21', '0759392294', 'No 30,Ambathannawaththa ,\r\nJambugahapaitiya,Kandy', 'sathmitharushika24@gmail.com', 0),
(307, NULL, 'BB411220', 'Gagan Hansaka', '200430800194', 'Uploads/nic/1780821089_20260607_140016.jpg', 'Second year undergraduate student in Nsbm Green University Homagama', NULL, 1, 0, '2026-06-07 08:31:29', '0772290543', '201/4,Captain Ashoka Peries Mw ,Nalluruwa,Panadura', 'gaganhanz113@gmail.com', 0),
(308, NULL, 'E6011100', 'charitha Siriwardana', '890711081V', 'Uploads/nic/1780904441_ID.JPG', 'uk level 07 PDG in project management', NULL, 1, 0, '2026-06-08 07:40:41', '0716528428', '75/5, pulinathalarama road ,magammana, ragama', 'charithasdesign@gmail.com', 0),
(309, NULL, '3871ED32', 'lalith gunathilaka', '833373552V', 'Uploads/nic/1780976627_ID Copy AMLK.pdf', 'GCE Advance Level', NULL, 1, 0, '2026-06-09 03:43:47', '0706813783', 'No 418/3, Panadura road, Horana', 'lalith3838@gmail.com', 0),
(310, NULL, 'E1945085', 'Isanka Thalagala', 'N11168402', 'Uploads/nic/1781067445_Passport bio data page.jpg', 'BSc.(hons) in Information Technology.\r\n12+ years of experiance in Software Engineering', NULL, 1, 0, '2026-06-10 04:57:25', '0713616749', '286,Abethissa Mawatha,Oruwala,Athurugiriya,10150', 'isanka.thalagala@gmail.com', 0),
(311, NULL, '2EA066A8', 'Jason Daniel Subandriyo', '199816910276', 'Uploads/nic/1781071491_Screenshot 2026-06-10 at 11.33.17â€¯AM.png', 'Graduate in International Business', NULL, 1, 0, '2026-06-10 06:04:51', '0771233683', '10A 121 Residences\r\nNo 121 Park Road\r\nColombo 5', 'silverines.muller@gmail.com', 0),
(312, NULL, '8CE820C9', 'yasindu madhumal', '200129501790', 'Uploads/nic/1781073123_original_e83e447a-6493-4ed9-84b6-a5c5ea577334_PXL_20260221_142137935.jpg', '', NULL, 1, 0, '2026-06-10 06:32:03', '0711711668', '699/B udhagama mugunamale,Balangoda', 'yasinmadumal01@gmail.com', 0),
(313, NULL, '435BFDCB', 'Moshan Manusha', '199922602125', 'Uploads/nic/1781074601_ID 1.jpeg', '', NULL, 1, 0, '2026-06-10 06:56:41', '0785556089', '50/2 Galapallearawa,Mugunamalaya,Balangoda', 'moshanmanushaofficial@gmail.com', 0),
(314, NULL, 'C14BD12A', 'Mohamed Afran', '200333712254', 'Uploads/nic/1781103362_NIC_Scans.pdf', '1. Successfully completed G.C.E O/L.\r\n2. Successfully completed Diploma in Business Management.\r\n3. Have got 1 year industrial experience.', NULL, 1, 0, '2026-06-10 14:56:02', '0770496976', '30/6, Dippitawattha, Welipenna', 'afranceo123@gmail.com', 0),
(315, NULL, 'D878B668', 'Abhaya wikrama kankanam Pathiranage Dileepa daham pahasara abhayawikrama', '200926403145', 'Uploads/nic/1781209314_17812092343247025852801630599531.jpg', 'O/L pending results', NULL, 1, 0, '2026-06-11 20:21:54', '0743213912', '329/ Ranaviru abaya bandara mawatha heen Kanda waththa ihala kosgama', 'dilipdahamtech0@gmail.com', 0),
(316, NULL, 'B0A03EB7', 'Wickramasinghe Mudiyanselage Darshani Piyumali Wickramasinghe', '199872901478', 'Uploads/nic/1781593407_ID-0.pdf', 'Bachelor\'s degree in Biosystems Technology, specializing in Food Science and Technology \r\nDiploma in Quality Management. \r\nCurrently, I\'m pursuing certificates in capital markets and work as an Investment Advisor in the equity market at Senfin Securities Ltd.', NULL, 1, 0, '2026-06-16 07:03:27', '0772773429', 'Atubedda waththa, Koongamuwa, Mawanella', 'piyumaliwickramasinghe816@gmail.com', 0),
(317, NULL, 'E3CED2A1', 'Najmath Nasreen', '960253477v', 'Uploads/nic/1781705556_image.jpg', '', NULL, 1, 0, '2026-06-17 14:12:36', '0772313008', '22/a darga road,china fort,beruwala', 'najmathmohamed@gmail.com', 0),
(318, NULL, 'D1B9962A', 'Jananjaya Lakshan', '200228800379', 'Uploads/nic/1781705950_17817059118664377123522982732128.jpg', '', NULL, 1, 0, '2026-06-17 14:19:10', '0789315057', '230/2 Sri Pada Mawatha Godigamuwa ratnapura', 'jananjaya.lakshan@gmail.com', 0),
(319, NULL, '7FF70B11', 'Dilip daham', '200926403145', 'Uploads/nic/1781936754_17819366902081131920414798595850.jpg', 'O/L student', NULL, 1, 0, '2026-06-20 06:25:54', '0743213912', '329/ Ranaviru abaya bandara mawatha heen Kanda waththa ihala kosgama', 'dilipdahamnxt@gmail.com', 0),
(320, NULL, '5D94722A', 'Ganesha murthi niwvithan', '200918503324', 'Uploads/nic/1781940344_IMG-20260327-WA0026.jpg', '', NULL, 1, 0, '2026-06-20 07:25:44', '0751124924', '180/31 leyars broadway, Colombo 14', 'nisvinisvithan71@gmail.com', 0),
(321, NULL, 'FEBBB657', 'Shuhaib Mohamed', '199719400913', 'Uploads/nic/1782020494_IMG_6342.jpeg', 'Iâ€™m currently working as assistant accountant', NULL, 1, 0, '2026-06-21 05:41:34', '0772111792', '269/5 Pasyala', 'zshuhaib1@gmail.com', 0),
(322, NULL, 'ADD228D5', 'BANGALAWE GEDARA FAIZER MOHAMMED AFLAL AHAMED', '200733404374', 'Uploads/nic/1782041529_27A7F0C7-3E47-4AE5-9BEE-8AAC902AD56C.jpeg', 'O/L only', NULL, 1, 0, '2026-06-21 11:32:09', '+94766683232', '285 yahalathenna junction yahalathenna', 'globalafrgemandjewellers@gmail.com', 0),
(323, NULL, '59482399', 'Mohammed Ameen Mohammed Iqram', '200832201823', 'Uploads/nic/1782041920_Google 2.pdf', 'O/L completed \r\nCertificate in basic Gemology', NULL, 1, 0, '2026-06-21 11:38:40', '0706440039', '28/11 Jiffry moulana mawatha \r\nKalutara south', 'iqramameen328@gmail.com', 0),
(324, NULL, '002AE91D', 'Nadeer naleef', '200607101149', 'Uploads/nic/1782104940_20260622_103533.jpg', 'A/L three passes', NULL, 1, 0, '2026-06-22 05:09:00', '0770627876', '213/8,kanuketiya ,monnekulama.', 'nnaleef95@gmail.com', 0),
(325, NULL, '391EE02A', 'Imesh Anjana Hanwella', '980403157v', 'Uploads/nic/1782115940_ID.pdf', 'Passed GCE A/Ls\r\nCompleted the Gemmology Certificate course in Kaduwela GJRTI 2025-26', NULL, 1, 0, '2026-06-22 08:12:20', '0768682328', 'No.225/10A,Thambili Uyana,Hirana,Panadura', 'imeshanjanahanwella@gmail.com', 0),
(326, NULL, 'F0DE488C', 'Godamadiththe Gedara Sindoopa Anuradha Maduldeniya', '200926600260', 'Uploads/nic/1782146099_NIC 1_260622_215443.pdf', 'G.C.E O/L', NULL, 1, 0, '2026-06-22 16:34:59', '0766262832', 'No,6/B Galmatiyawa North,Kanthale', 'anuradhamaduldeniya@gmail.com', 0),
(327, NULL, 'EE264748', 'Adambarage indika Thomas De Alwis', '842510260v', 'Uploads/nic/1782192862_IMG_0878.jpeg', '', NULL, 1, 0, '2026-06-23 05:34:22', '0713555555', 'No 18 2/1 \r\n25th Lane \r\nColombo 07', 'indikad777@yahoo.com', 0),
(328, NULL, 'D81ABA22', 'Sasindu Rashmika', '200416601610', 'Uploads/nic/1782295077_inbound7775307421416913638.pdf', 'O/L\r\nA/L', NULL, 1, 0, '2026-06-24 09:57:57', '0710603353', '69/2 nambapana,ingiriya', 'sasindurashmika94@gmail.com', 0),
(329, NULL, '6F28226E', 'Chathuri Dasanayake', '198964700450', 'Uploads/nic/1782366016_2026623.pdf', '', NULL, 1, 0, '2026-06-25 05:40:16', '0713007316', '413,\r\nEpitawala \r\nKiriella', 'chath.dasanayake@gmail.com', 0),
(330, NULL, '86733E9F', 'MIRISAGE THARUSHA RAHUL CHAMODYA FERNANDO', '200309313417', 'Uploads/nic/1782366359_Screenshot_20260625_110824_Gallery.jpg', 'O/!L & A/L', NULL, 1, 0, '2026-06-25 05:45:59', '0763856565', '683/A,Japamala Mawatha \r\nWennappuwa\r\n61170', 'tharusharahul@gmail.com', 0),
(331, NULL, 'A646DC98', 'Ruwan Jayarathne', '197812702957', 'Uploads/nic/1782383041_Ruwan NIC.png', 'GCE A/L', NULL, 1, 0, '2026-06-25 10:24:01', '0777250146', 'Wennappuwa', 'ruwanwlr@gmail.com', 0),
(332, NULL, '240B12D6', 'HERATH H M K M', '893052232V', 'Uploads/nic/1782411473_NIC.pdf', 'BVSc Hons, BCS PGD(UK)', NULL, 1, 0, '2026-06-25 18:17:53', '0716668478', '155 3rd lane, Gamunu Peddesa, Aluth Malkaduwawa, Kurunegala', 'kherath34@gmail.com', 0),
(333, NULL, '9704261C', 'M F Azim', '870680821V', 'Uploads/nic/1782461550_Fahd ID.pdf', '', NULL, 1, 0, '2026-06-26 08:12:30', '0768378118', '', '19871987cool@gmail.com', 0),
(334, NULL, '2BB9EC38', 'K.A.D Gayashan Gunathilake', 'N6824800', 'Uploads/nic/1782539763_Gayashan Gunathilake PP.jpg', 'BSc Eng', NULL, 1, 0, '2026-06-27 05:56:03', '0777320753', 'No.491/C,\r\nPannila,\r\nRuggahawila', 'kadgayashan@gmail.com', 0),
(335, NULL, 'E2891A31', 'Chathuri Dasanayake', '198964700450', 'Uploads/nic/1782629331_2026623.pdf', '', NULL, 1, 0, '2026-06-28 06:48:51', '0713007316', '413\r\nEpitawala \r\nKiKiriella', 'chath.dasanayake@gmail.com', 0),
(336, NULL, '2B528702', 'Denan Olin Vanderslott', '199031402960', 'Uploads/nic/1782647818_IMG_3827.jpeg', 'Bsc (hons) degree in computing\r\nBTEC HND in computing and system development.', NULL, 1, 0, '2026-06-28 11:56:58', '0777755887', '487/3/3, d d kulathunga mawatha, makumbura, pannipitiya.', 'denan.olin@gmail.com', 0),
(337, NULL, 'D21139CB', 'Denan Olin Vanderslott', '199031402960', 'Uploads/nic/1782648199_IMG_3827.jpeg', 'Bsc (hons) degree in computing\r\nBTEC HND in computing and system development', NULL, 1, 0, '2026-06-28 12:03:19', '0777755887', '487/3/3, d d kulathunga mawatha, makumbura, pannipitiya.', 'denan.olin@gmail.com', 0),
(338, NULL, '186D6073', 'Denan Olin Vanderslott', '199031402960', 'Uploads/nic/1782648686_IMG_3827.jpeg', 'Bsc (hons) degree in computing\r\nBTEC HND in computing and system development', NULL, 1, 0, '2026-06-28 12:11:26', '0777755887', '487/3/3, d d kulathunga mawatha, makumbura, pannipitiya.', 'denan.olin@gmail.com', 0);
INSERT INTO `students` (`id`, `student_id_manual`, `reference_no`, `name`, `nic_passport`, `nic_file`, `education_background`, `next_payment_date`, `declaration`, `declaration2`, `created_at`, `contact_number`, `address`, `gmail`, `checked`) VALUES
(339, NULL, 'AA028117', 'Manawaththalage Sameera Dishan Suranga', '883133722V', 'Uploads/nic/1782712808_CBF037BD-D3E8-4AE4-B5B7-41E65DA84B5A.jpeg', 'Degree \r\nLLB', NULL, 1, 0, '2026-06-29 06:00:08', '0711803158', 'No53\r\nSchool lane \r\nNewtown \r\nRatnapura', 'sameeradishan@gmail.com', 0),
(340, NULL, 'D0E111D1', 'Shuhaib Mohamed', '199710400913', 'Uploads/nic/1782768618_54AB9D8D-8CF7-46FC-B420-FC3AAE4FD462.jpeg', 'GCE A/L commerce stream pass', NULL, 1, 0, '2026-06-29 21:30:18', '0772111792', '269/5 Nambuluwa,\r\nPasyala.', 'zshuhaib1@gmail.com', 0),
(341, NULL, 'BA1D86BC', 'K A D N Gayashan Gunathilake', 'N6824800', 'Uploads/nic/1782818635_Gayashan Gunathilake PP.jpg', 'Bsc Engineering Degree\r\nUniversity of peradeniya', NULL, 1, 0, '2026-06-30 11:23:55', '0714012070', 'NO.491/C ,Pannila, Ruggahavila', 'kadgayashan@gmail.com', 0),
(342, NULL, 'F4191C7C', 'R.M.R.Chamara', '200028800330', 'Uploads/nic/1782860061_Screenshot 2026-07-01 042217.jpg', 'I am fallowedHND Agriculture Praduction Technology', NULL, 1, 0, '2026-06-30 22:54:21', '0775759455', 'Aluththanna,Keppetipola', 'chamararmr50@gmail.com', 0),
(343, NULL, '49538525', 'Umar Hamdhan', '200409401075', 'Uploads/nic/1782981335_Screenshot_20260702-140423_CamScanner~2.jpg', 'Bsc in Computer Science (Northwood University, Michigan)', NULL, 1, 0, '2026-07-02 08:35:35', '0761499021', 'A,130/3/2, Hondenigoda, Mawanella', 'umarhamdhan23@gmail.com', 0),
(344, NULL, '74436658', 'Vandana Hettiaratchi', '199581504437', 'Uploads/nic/1782996062_IMG_7966.jpeg', 'A-Level Gateway College, Colombo\r\nBachelor International Relations at Royal Institute, Colombo \r\nMaster of Science Development Studies at Lund University (Sweden)', NULL, 1, 0, '2026-07-02 12:41:02', '+94702251758', '20/3 Pipe Road\r\nKoswatta \r\nBattaramulla, 10120', 'gemtraillab@gmail.com', 0),
(345, NULL, '2410F03F', 'Vihan nadith pattividhana', '200118003941', 'Uploads/nic/1783015529_IMG_2055.jpeg', 'Done a/l', NULL, 1, 0, '2026-07-02 18:05:29', '0763254372', 'No 03, siri sunanda nahimi mawatha ,batagolla , yakkala', 'vihanpattividhana@gmail.com', 0),
(346, NULL, 'D2FB4824', 'Weerakkodi Mudiyanselage Thilina Lakmal Bandara', '910632655V', 'Uploads/nic/1783022344_20191003_223509.jpg', 'Successfully completed AL', NULL, 1, 0, '2026-07-02 19:59:04', '0767678506', '', 'thilinalakmalbandara@gmail.com', 0),
(347, NULL, '76B9A7ED', 'Warnakulsuriya Praveen Heshan Fernando', '851631992V', 'Uploads/nic/1783046255_NIC_Heshan.pdf', 'A/L Mathematics 2004\r\nBritish Computer Society - Certificate Level', NULL, 1, 0, '2026-07-03 02:37:35', '0761183239', '511/02/A, St. Anne\'s Road, Daluwakotuwa, Kochchikade', 'praveenheshanfdo@gmail.com', 0),
(348, NULL, '1C2A3FD3', 'Konara Mudiyanselage Javindu Jinal', '200127101595', 'Uploads/nic/1783093973_5b48ba68-d4c9-4fef-8fa7-ef3f8eee1888.jpeg', 'G.C.E. O/L - 8A,1B\r\nG.C.E. A/L - 1C,2S', NULL, 1, 0, '2026-07-03 15:52:53', '0740471401', 'A/7/A,Gurugehela,Ganegama,Pelmadulla', 'javindujinal224@gmail.com', 0),
(349, NULL, '47DD7518', 'Neelanga Weerasekera', '196524601387', 'Uploads/nic/1783157232_IMG-20251001-WA0009.jpg', '', NULL, 1, 0, '2026-07-04 09:27:12', '0771959884', '41 Chapel Ln', 'neelangaaj90@gmail.com', 0),
(350, NULL, '4313FE39', 'Kurupanawa Gamage Wiran Charuka', '953161222V', 'Uploads/nic/1783189567_Image to PDF 20250324 14.37.22.pdf', '2A and 1B passes for AL', NULL, 1, 0, '2026-07-04 18:26:07', '0717186472', 'Nagoda stores, Dammala colony, Halvitigala, Galle.', 'wirancharuka1@gmail.com', 0),
(351, NULL, '4C52A177', 'Palipana herath mudhiyanselage Nishanthi Palipana', '916380755V', 'Uploads/nic/1783255811_80483659-F176-4AE9-867F-878F183BA433.jpeg', 'Diploma in LLB, QS diploma ,17 years of experience', NULL, 1, 0, '2026-07-05 12:50:11', '+94764262470', '97/10 Galle road,dehiwala', 'ceo@vintageceylon.lk', 0),
(352, NULL, '8C24F8FF', 'Vithanage Prasad Nandana Abeywansa', '743063236V', 'Uploads/nic/1783429313_both sides.png', 'MCSE (Microsoft certified Systems Engineer )', NULL, 1, 0, '2026-07-07 13:01:53', '0773909533', '100, Udayapura Rd, Thalangama south', 'ocomnets@gmail.com', 0),
(353, NULL, '3C12A050', 'Amara Sulochana Fernando', '198165403827', 'Uploads/nic/1783487241_Nic new.pdf', '', NULL, 1, 0, '2026-07-08 05:07:21', '071 9573515', '149/14,\r\nBandaranayake Mawatha,\r\nRathnapura', 'amarafernando302@gmail.com', 0),
(354, NULL, '6094EB5A', 'Nadun Chinthaka Kumara', '7511503955', 'Uploads/nic/1783514099_Gold_silver_Reg.pdf', '', NULL, 1, 0, '2026-07-08 12:34:59', '0778766404', 'No 81, Hidellana', 'krishantha1975@gmail.com', 0),
(355, NULL, 'A32B2F22', 'Nadeer Naleef', '200607101149', 'Uploads/nic/1783783416_20260622_103533.jpg', 'A/L art stream 3 passes', NULL, 1, 0, '2026-07-11 15:23:36', '0770627876', '213/8,kanuketiya,monnekulama.', 'nnaleef95@gmail.com', 0),
(356, NULL, 'A55CD6B6', 'Sajani Nihara', '946120004V', 'Uploads/nic/1783797387_pexels-kelly-3030290.jpg', 'Degree', NULL, 1, 0, '2026-07-11 19:16:27', '0772250330', '101, Kandy road, Kadugannawa', 'snkurukuththala@gmail.com', 0),
(357, NULL, '7BFC0BE3', 'Belawanage Amila Lakmal', '198533402793', 'Uploads/nic/1783923143_IMG-20260504-WA0000.jpg', 'BA Economics(Special)', NULL, 1, 0, '2026-07-13 06:12:23', '0779339135', '279/2\r\nPahalamulla\r\nWaharaka', 'lakmalba@gmail.com', 0),
(358, NULL, 'CE684F74', 'Nadeer Naleef', '200607101149', 'Uploads/nic/1783991371_20260622_103533.jpg', 'AL 3 Passes', NULL, 1, 0, '2026-07-14 01:09:31', '0770627876', '213/8,Kanuketiya,Monmekulama.', 'nnaleef95@gmail.com', 0),
(359, NULL, '45EE0C29', 'Aruna Lakmal', '872463941V', 'Uploads/nic/1784022833_20260714_152111.jpg', 'Advance level art subject', NULL, 1, 0, '2026-07-14 09:53:53', '0774559373', '39,Ginigathhena road, Hatton', 'arunapadmi@gmail.com', 0),
(360, NULL, '2EE57FD0', 'MOHAMED IMADH HUSSAIN', '200202204636', 'Uploads/nic/1784075724_Imadh Hussain Scanned Passport Copy.pdf', 'O/L & Foundation in Engineering', NULL, 1, 0, '2026-07-15 00:35:24', '0756136445', 'No.390/09 Chilaw Road Negombo', 'imadhhussain@gmail.com', 0),
(361, NULL, 'CF60B8D0', 'HERATH H M K M', '893052232V', 'Uploads/nic/1784139611_NIC.pdf', 'BVSc, PGDip, Gemmology', NULL, 1, 0, '2026-07-15 18:20:11', '0774951700', '155,Aluth Malkaduwawa, kurunegala', 'kherath34@gmail.com', 0),
(362, NULL, '90123618', 'Aslam Jiffry', '198900101900', 'Uploads/nic/1784192958_ECC9B4D9-DD58-484A-A6AE-506569190BFA.jpeg', 'Advance Level Pass & 16 Years experience in Gem and jewelry industry', NULL, 1, 0, '2026-07-16 09:09:18', '0777344688', '9/B Palace Path, Beruwala', 'jiffry2aslam@gmail.com', 0),
(363, NULL, '5FB8926F', 'Vithanage Damith Harsha', '199116602825', 'Uploads/nic/1784196300_IMG_0313.jpeg', 'I completed my GCE Advanced Level (Commerce Stream) with 3 A passes. I have also partially completed the Chartered Accountancy qualification in Sri Lanka. Currently, I am working as an Audit Manager at a reputed audit firm, where I have gained extensive experience in auditing, financial reporting, risk assessment, internal controls, and managing audit engagements across various industries.', NULL, 1, 0, '2026-07-16 10:05:00', '0719069791', '127/5, Temple road, Nawala', 'damith.vh@gmail.com', 0),
(364, NULL, '9FD2C84B', 'Ruvinya Kodithuwakku', '200274302048', 'Uploads/nic/1784196393_8b9cd1b0-d3ad-417a-ab8f-c7c3e03ddf1e.jpeg', 'Bsc (hons) in fashion business and management with second class upper', NULL, 1, 0, '2026-07-16 10:06:33', '0779880717', '16/18 wijayaba mw, nawala rd, Nugegoda', 'ruvinyakodi002@gmail.com', 0),
(365, NULL, '6C7E3A28', 'Mohamed Mulafir Fathima Mubashira', '199860301766', 'Uploads/nic/1784204480_NIC.pdf', 'BSc (Honors) in Biomedical Science', NULL, 1, 0, '2026-07-16 12:21:20', '0761042811', '158/2A, Marikkar Street, Dharga Town', 'mmfmubashira98@gmail.com', 0),
(366, NULL, '143F6F4B', 'Mohamed Mulafir Fathima Mubashira', '199860301766', 'Uploads/nic/1784204582_NIC.pdf', 'BSc (Honors) in Biomedical Science', NULL, 1, 0, '2026-07-16 12:23:02', '0761042811', '158/2A, Marikkar Street, Dharga Town', 'mmfmubashira98@gmail.com', 0),
(367, NULL, 'F1D39695', 'A.Z Ahamedh', '200034801854', 'Uploads/nic/1784209780_Ahamedh ID .pdf', 'O/L - 5A3BC\r\nA/L - completed in maths stream(only have a A pass in English) \r\nHave experience in gem business more than 3years \r\nHave completed certificate course in gemology in IGA Colombo and currently at final stages of Diploma in Gemology in IGA Colombo', NULL, 1, 0, '2026-07-16 13:49:40', '0775451206', '462/3,Beligammana,Mawanella', 'ahamedhzulfiqar22@gmail.com', 0),
(368, NULL, '03386325', 'GANJI SREEKAR LAKSHMAN', 'Z7150364', 'Uploads/nic/1784225814_WhatsApp Image 2026-03-27 at 3.32.54 AM.jpeg', 'Done MBA and BBA from icfai business school hyderabad india . done polished diamond graduate and CAD jewelry designing from jk diamonds in mumbai india. certified gold appraiser. visited gem mines in tanzania, sri lanka and zambia', NULL, 1, 0, '2026-07-16 18:16:54', '+918074146783', '11-7-22/A HUDA COLONY SAROORNAGAR, HYDERABAD, KOTHAPET, RANGA REDDY, TELANGANA, 500035', 'ganjisreekar@gmail.com', 0),
(369, NULL, 'D5883F9B', 'cert-test', '1234567897v', 'Uploads/nic/1784271282_xss 1.pdf', '', NULL, 1, 0, '2026-07-17 06:54:42', '3456789078', '', 'nobeyar391@24faw.com', 0),
(370, NULL, 'AEB3CDF2', 'Matharage Hashan Dhananjaya', '942770904V', 'Uploads/nic/1784274322_NIC HASHAN.pdf', 'Successfully completed Gemmology certification \r\nSLIM in Marketing', NULL, 1, 0, '2026-07-17 07:45:22', '0713029384', 'No:288,Dampe,Meegoda', 'hashanmatharage@gmail.com', 0),
(371, NULL, '57519F1E', 'BW BISHAN ESITH RAVINDRA', '200206801590', 'Uploads/nic/1784291516_inbound4144027992773175172.png', 'O/l', NULL, 1, 0, '2026-07-17 12:31:56', '0777 294 517', '12/10A, Mihidu Mawatha, New Hospital Road, Pamunuwa, Maharagama', 'bishanesith2002@gmail.com', 0),
(372, NULL, 'F3C60F44', 'WITHANA ARACHCHIGE NAMAL GUNAWARDHANA', '198420200964', 'Uploads/nic/1784342809_WhatsApp Image 2026-07-18 at 08.15.20.jpeg', 'MBBS (Peradeniya)', NULL, 1, 0, '2026-07-18 02:46:49', '0759566040', '16/2, 2/1\r\nsubadhrarama lane\r\nNugegoda', 'namalgu1984@gmail.com', 0),
(373, NULL, 'A41C2405', 'WITHANA ARACHCHIGE NAMAL GUNAWARDHANA', '198420200964', 'Uploads/nic/1784342933_WhatsApp Image 2026-07-18 at 08.15.20.jpeg', 'MBBS (Pera)', NULL, 1, 0, '2026-07-18 02:48:53', '0759566040', '16/2, 2/1, Subadhrarama Lane, Nugegoda', 'namalgu1984@gmail.com', 0),
(374, NULL, '7801D218', 'Krishantha Wakkumbura', '197511503953', 'Uploads/nic/1784363993_20250510_130342.jpg', 'Test mail', NULL, 1, 0, '2026-07-18 08:39:53', '0714396404', 'No 810, Hidellana', 'krishantha1975@gmail.com', 0),
(375, NULL, '37DE4DF2', 'Rasith Nadeeshan', '200228202123', 'Uploads/nic/1784380952_WhatsApp Image 2026-07-18 at 18.43.51.jpeg', 'Advanced Level (G.C.E.) Pass\r\n\r\nSuccessfully completed the Gemmology Basic Course at the Gem & Jewellery Research and Training Institute (GJRTI)', NULL, 1, 0, '2026-07-18 13:22:32', '0761721509', 'No.213 pallemulla halloluwa, kandy', 'rasithnadeeshan486@gmail.com', 0),
(376, NULL, 'E5994D5C', 'Rathnayaka Mudiyanselage Ruwan Chamara', '200028800330', 'Uploads/nic/1784416989_ID.pdf', 'HND Agricultural Production Technology - School of agriculture - Kundasale', NULL, 1, 0, '2026-07-18 23:23:09', '0775759455', 'Aluththanna,Keppetipola', 'chamararma49@gmail.com', 0),
(377, NULL, '392178E6', 'Nuwan Niroshan', '198507203745', 'Uploads/nic/1784552324_IMG_20260611_114849.jpg', '', NULL, 1, 0, '2026-07-20 12:58:44', '0714914591', 'No.316/2B,Aruppitiya, Battaramulla .', 'niro085@gmail.com', 0),
(378, NULL, '0BA9A3F9', 'Nuwan Niroshan', '198507203745', 'Uploads/nic/1784552686_IMG_20260611_114849.jpg', 'Faced to G.C.E advance level in Mathematics subjects.', NULL, 1, 0, '2026-07-20 13:04:46', '0714914591', 'No.316/2B , Aruppitiya, Battaramulla .', 'niro085@gmail.com', 0),
(379, NULL, '41A6FA5F', 'Sanjaya Ponnamperuma', '921461414V', 'Uploads/nic/1784605481_ID.jpg', '', NULL, 1, 0, '2026-07-21 03:44:41', '0707626511', '112/3B, Horaketiya rd, Korathota, Kaduwela', 'pasdanushka@gmail.com', 0),
(380, NULL, 'C2A13B2A', 'HMKM Herath', '198930502232', 'Uploads/nic/1784702173_NIC.pdf', 'BVSc, PGDip(IT)', NULL, 1, 0, '2026-07-22 06:36:13', '0774951700', '155-Gamunu Peddessa, Aluth MAlkaduwawa, Kurunegala', 'kherath34@gmail.com', 0),
(381, NULL, '08092C00', 'Sandun sampath abhayasinghe', '942781075V', 'Uploads/nic/1784702473_NIC.pdf', 'A/L', NULL, 1, 0, '2026-07-22 06:41:13', '0766009588', '219/A/ senanayaka place ingiriya', 'sandunsampath94@gmail.com', 0);

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
  ADD KEY `application_id` (`application_id`),
  ADD KEY `idx_application` (`application_id`),
  ADD KEY `idx_installment` (`installment_type`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=382;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=457;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=382;

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
