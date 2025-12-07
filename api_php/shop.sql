-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 07, 2025 at 08:36 PM
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
-- Database: `shop`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'admin', '1234');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `zone` varchar(50) NOT NULL COMMENT 'โซน เช่น หน้าต่าง, กลางร้าน',
  `guests` int(11) NOT NULL COMMENT 'จำนวนแขก',
  `time` varchar(10) NOT NULL COMMENT 'เวลา เช่น 11:00, 13:00',
  `customer_name` varchar(100) NOT NULL COMMENT 'ชื่อลูกค้า',
  `phone` varchar(20) NOT NULL COMMENT 'เบอร์โทร',
  `booking_date` date NOT NULL COMMENT 'วันที่จอง',
  `status` enum('รอยืนยัน','ยืนยันแล้ว','ยกเลิก','เสร็จสิ้น') DEFAULT 'รอยืนยัน' COMMENT 'สถานะ',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'เวลาที่สร้าง'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ตารางจองโต๊ะ';

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `zone`, `guests`, `time`, `customer_name`, `phone`, `booking_date`, `status`, `created_at`) VALUES
(1, NULL, 'กลางร้าน', 9, '13:00', 'er', '676767', '2025-12-07', 'ยืนยันแล้ว', '2025-12-07 15:26:57'),
(2, NULL, 'ระเบียง', 1, '13:30', 'dd', '12', '2025-12-07', 'ยืนยันแล้ว', '2025-12-07 15:47:17'),
(3, NULL, 'มุมสบาย', 1, '12:30', 'ww', '121212', '2025-12-07', 'ยืนยันแล้ว', '2025-12-07 16:00:40'),
(4, 1, 'โซน A', 4, '18:00', 'ทดสอบ', '0812345678', '2024-12-10', 'ยืนยันแล้ว', '2025-12-07 16:02:58'),
(5, 1, 'โซน B', 2, '19:00', 'ทดสอบ 2', '0823456789', '2024-12-11', 'ยืนยันแล้ว', '2025-12-07 16:02:58'),
(6, 7, 'โซน A', 4, '18:00', 'สมชาย ใจดี', '0812345678', '2024-12-10', 'ยืนยันแล้ว', '2025-12-07 16:05:38'),
(7, 7, 'โซน B', 2, '19:00', 'สมหญิง รักสุข', '0823456789', '2024-12-11', 'ยืนยันแล้ว', '2025-12-07 16:05:38'),
(8, 7, 'โซน VIP', 6, '20:00', 'นายทดสอบ', '0834567890', '2024-12-09', 'ยืนยันแล้ว', '2025-12-07 16:05:38'),
(9, NULL, 'VIP', 2, '19:30', 'สาว', '098675533', '2025-12-07', 'ยืนยันแล้ว', '2025-12-07 16:06:27'),
(10, 7, 'มุมสบาย', 5, '13:00', 'กก', '3545656', '2025-12-07', 'ยืนยันแล้ว', '2025-12-07 16:12:27'),
(11, 7, 'กลางร้าน', 5, '12:00', 'กก', '435', '2025-12-07', 'ยืนยันแล้ว', '2025-12-07 16:16:21'),
(12, 7, 'กลางร้าน', 8, '12:30', 'บ', 'ๅ44', '2025-12-07', 'ยืนยันแล้ว', '2025-12-07 16:37:31'),
(13, 7, 'VIP', 8, '11:30', 'กกดด', '443', '2025-12-07', 'ยืนยันแล้ว', '2025-12-07 16:44:19'),
(14, 7, 'มุมสบาย', 7, '13:00', 'dsd', '24', '2025-12-07', 'ยืนยันแล้ว', '2025-12-07 16:47:05'),
(15, 3, 'กลางร้าน', 2, '13:00', 'sss', '34', '2025-12-07', 'ยืนยันแล้ว', '2025-12-07 16:50:00'),
(16, 9, 'VIP', 1, '11:30', 'dena', '345678', '2025-12-07', 'รอยืนยัน', '2025-12-07 19:19:59');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(128) NOT NULL,
  `role` varchar(20) DEFAULT 'customer'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `firstName`, `lastName`, `phone`, `username`, `password`, `role`) VALUES
(1, 'Somchai', 'Wongdee', '0812345678', 'somchaiw', 'password123', 'customer'),
(2, 'Anong', 'Srisuk', '0898765432', 'anongs', 'securepass456', 'customer'),
(3, 'Akarapon', 'Komkleaw', '0902510447', 'Fom', '123456', 'customer'),
(4, 'Aphirat', 'Yamyaem', '081552678', 'Do', '546789', 'customer'),
(5, 'aaa', 'aaa', '123213421421', 'aaa', '$2y$10$ypl/FeUAm7H6e.fG0Gf9suzxj10vU0QjNCieYI7DhDE.lcx.ILZ6y', 'customer'),
(6, 'Fat', 'Mak', '1232141351124', 'ww', '$2y$10$VXMV090TW0hM1lLJUDtT1eKrUOdBhgHZUmD3aYRRbp.h.6bsPg1ci', 'customer'),
(7, 'Do', 'dee', '1231232141', 'De', '$2y$10$FexmxdbvcQmbxrUUmEAWju0zkH0lt0FDWBaZijmbDTjLQo3eeUFZu', 'customer'),
(8, 'ww', 'www', '21313421421', 'as', '$2y$10$2uLlFg8okXEv5dpKb/Yq8ufHawBdmYg.CMZ0U8rN2V78oDzXryMea', 'customer'),
(9, 'aa', 'aaa', '12312321312', 'aa', '$2y$10$UTFa0KI.c6ALPy10qu0W3eZie1Oml0uUODDpBh/KZRXrHC0lTX0.K', 'customer'),
(10, 'aa', 'aaa', '1233213213', 'aa', '$2y$10$miDoxIUR3OfxaXyzpwl2f.wbWWm5zCrKQth6haeZf4q5n951DKFe6', 'customer'),
(11, 'aaa', 'aa', '1232112414', 'aa', '$2y$10$abizIAvylH.HZB8M5hxZXe.Cj60T4ywBKIVZrPj2M8ajmfV2eKVea', 'customer'),
(12, 'aa', 'aa', '1231241', 'Unk', '$2y$10$8.683xrkjaLiErz6ffJneOgQ/WDIK0olQR8RZ7LLc8f9pXsqnBXOu', 'customer'),
(13, 'หฟก', 'ฟหกฟห', '1214', '1', '$2y$10$7efXZHaMvdd.VSJhPV60K.s8QtJ4pKMOY8gTVp6gufMF.eNkDldE2', 'customer'),
(14, 'ดกหฟกด', 'ำไพไ', '23', '23', '$2y$10$IIXQ5uLc1CUi5zeQIybGnusuhgdzkPQKdly678WrJ4ZNcpgZYN3zq', 'customer'),
(15, 'สมาน', 'โชคดี', '2345', 'dd', '$2y$10$kNlOyiDBfR7kEqNIEEqSCOotYxcpdv8PCsO08SxK/G6p0/cNkV5xa', 'customer'),
(16, 'กหหฟหก', 'หกฟหหกฟก', '-/ภ423', 'ee', '$2y$10$zyORtneOnSksTTq6Ct/jBOJR7YGFk.02kidzIqhsQN8FsQO28.9kW', 'customer'),
(17, 'erew', 'wer', '2314', '11', '$2y$10$w2gqNTrLymnxkYpJ6mGDteEtMxU34WByrcDlAfg/SMV/pbjMlIZAq', 'customer'),
(18, 'หดหฟ', 'หดหฟด', 'ๅ23123', 'yy', '$2y$10$L.yCps0cibXc1IgslK7lpev2pzNLfOU3sfJ9jliNsTlPaGYpHAA2i', 'customer'),
(19, 'ewe', 'ewe', '2124', '123', '$2y$10$aVEr8c8PGdvlJULBrGHl1eB6/gaTruhueQYbDQxjYjN2grWOxnKdy', 'customer'),
(20, 'กดเ', 'ดเกด', '34545', 'er', '$2y$10$BY9GTswwami0bF.wncCMAO.idd8q5yNTosf9CgbacR/B57sgLQHdq', 'customer');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `role` varchar(20) NOT NULL DEFAULT 'staff'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `first_name`, `last_name`, `username`, `password`, `profile_image`, `created_at`, `updated_at`, `role`) VALUES
(5, 'Akarapon', 'Komkreaw', 'fom', '$2y$10$6SqF6f1WgQNVuMcZVgHDQulNlzT3sVhEQoHqCm4Wo7q77kMGCVebi\r\n', 'images/john_smith.jpg', '2025-10-24 17:23:54', '2025-12-05 10:36:01', 'staff'),
(6, 'Sarah', 'Johnson', 'sarah.j', '$2y$10$zyxwvutsrqponmlkjihgfedcba654321', 'images/sarah_johnson.jpg', '2025-10-24 17:23:54', '2025-10-24 17:23:54', 'staff'),
(7, 'Michael', 'Williams', 'mike.w', '$2y$10$1234567890abcdefghijklmnopqrstuv', 'images/michael_williams.jpg', '2025-10-24 17:23:54', '2025-10-24 17:23:54', 'staff'),
(8, 'Emily', 'Brown', 'emily.brown', '$2y$10$uvwxyz0987654321fedcbazyxwvutsr', NULL, '2025-10-24 17:23:54', '2025-10-24 17:23:54', 'staff'),
(9, 'ww', 'ww', 'ww', '$2y$10$LhSHzNwc.dcyEcdyDCBjm.NQyVEZR7yF2fSVPbZcQGSLJhsvK0XSW\r\n', NULL, '2025-12-05 10:16:47', '2025-12-05 10:45:04', 'admin'),
(10, 'Admin', 'Master', 'admin2', '$2y$10$JORsDWhVR7HzqVcc2OyAley5I0NxNCNgOVrPYR0lI2ApOKeam8Jou', NULL, '2025-12-05 10:52:03', '2025-12-05 10:52:03', 'admin'),
(11, 'Staff', 'One', 'staff01', '$2y$10$uCQf8VA2/JcbrBEdjB8DKe3Y2zc.HV5KM8z84XSX4d9D5M2MJEk2G', NULL, '2025-12-05 10:56:26', '2025-12-05 10:56:26', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `log_activity`
--

CREATE TABLE `log_activity` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `role` enum('admin','staff') NOT NULL,
  `page` varchar(100) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orderdetails`
--

CREATE TABLE `orderdetails` (
  `order_detail_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `table_no` varchar(10) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `order_date` datetime NOT NULL,
  `status` varchar(20) DEFAULT 'รอดำเนินการ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `table_no`, `total_price`, `order_date`, `status`) VALUES
(7, NULL, '4', 898.00, '2025-10-12 21:47:29', 'เสร็จแล้ว'),
(10, NULL, '2', 0.00, '2025-10-15 21:58:39', 'เสร็จแล้ว'),
(11, NULL, '1', 947.00, '2025-10-15 22:17:19', 'เสร็จแล้ว'),
(12, NULL, '1', 1098.00, '2025-10-19 22:18:37', 'เสร็จแล้ว'),
(14, NULL, '2', 1098.00, '2025-10-16 11:37:40', 'เสร็จแล้ว'),
(16, NULL, '3', 29737.00, '2025-10-16 15:39:20', 'เสร็จแล้ว'),
(17, NULL, '5', 2347.00, '2025-10-16 15:52:12', 'เสร็จแล้ว'),
(18, NULL, '2', 3633.00, '2025-10-16 16:01:49', 'เสร็จแล้ว'),
(19, NULL, '1', 1098.00, '2025-10-22 17:23:16', 'เสร็จแล้ว'),
(20, NULL, '2', 1746.00, '2025-10-22 17:30:05', 'เสร็จแล้ว'),
(21, NULL, '5', 0.00, '2025-10-22 21:40:06', 'เสร็จแล้ว'),
(22, NULL, '1', 0.00, '2025-10-22 22:28:16', 'เสร็จแล้ว'),
(26, NULL, '2', 0.00, '2025-10-22 23:33:51', 'เสร็จแล้ว'),
(27, NULL, '4', 0.00, '2025-10-22 23:53:24', 'เสร็จแล้ว'),
(28, NULL, '2', 0.00, '2025-10-23 08:52:04', 'เสร็จแล้ว'),
(29, NULL, '1', 0.00, '2025-10-23 09:35:12', 'เสร็จแล้ว'),
(30, NULL, '4', 0.00, '2025-10-23 10:07:16', 'เสร็จแล้ว'),
(31, NULL, '1', 299.00, '2025-11-14 00:11:35', 'รอดำเนินการ'),
(32, 7, 'กลางร้าน', 1368.00, '2025-12-07 23:16:21', 'pending'),
(33, 7, 'Takeaway', 85.00, '2025-12-07 23:17:14', 'pending'),
(34, 7, 'Takeaway', 335.00, '2025-12-07 23:19:41', 'pending'),
(35, 7, 'Takeaway', 80.00, '2025-12-07 23:20:30', 'pending'),
(36, 7, 'Takeaway', 519.00, '2025-12-07 23:23:20', 'pending'),
(37, 7, 'กลางร้าน', 335.00, '2025-12-07 23:37:31', 'pending'),
(38, 7, 'Takeaway', 299.00, '2025-12-07 23:38:52', 'pending'),
(39, 7, 'Takeaway', 299.00, '2025-12-07 23:41:43', 'pending'),
(40, 7, 'Takeaway', 299.00, '2025-12-07 23:44:01', 'pending'),
(41, 7, 'VIP', 300.00, '2025-12-07 23:44:19', 'pending'),
(42, 3, 'Takeaway', 299.00, '2025-12-07 23:55:08', 'pending'),
(43, 9, 'VIP', 938.00, '2025-12-08 02:19:59', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'รอดำเนินการ' COMMENT 'status'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`, `subtotal`, `status`) VALUES
(3, 7, 10, 2, 199.00, 398.00, 'เสร็จแล้ว'),
(4, 7, 13, 2, 250.00, 500.00, 'เสร็จแล้ว'),
(11, 10, 9, 1, 250.00, 250.00, 'เสร็จแล้ว'),
(13, 11, 9, 1, 250.00, 250.00, 'เสร็จแล้ว'),
(14, 11, 10, 2, 199.00, 398.00, 'เสร็จแล้ว'),
(15, 11, 17, 1, 299.00, 299.00, 'เสร็จแล้ว'),
(16, 12, 14, 2, 299.00, 598.00, 'เสร็จแล้ว'),
(17, 12, 9, 2, 250.00, 500.00, 'เสร็จแล้ว'),
(20, 14, 14, 2, 299.00, 598.00, 'เสร็จแล้ว'),
(21, 14, 13, 2, 250.00, 500.00, 'เสร็จแล้ว'),
(27, 16, 8, 3, 280.00, 840.00, 'เสร็จแล้ว'),
(28, 16, 17, 3, 299.00, 897.00, 'เสร็จแล้ว'),
(29, 17, 9, 4, 250.00, 1000.00, 'เสร็จแล้ว'),
(30, 17, 10, 3, 199.00, 597.00, 'เสร็จแล้ว'),
(31, 17, 13, 3, 250.00, 750.00, 'เสร็จแล้ว'),
(32, 18, 8, 3, 280.00, 840.00, 'เสร็จแล้ว'),
(33, 18, 9, 4, 250.00, 1000.00, 'เสร็จแล้ว'),
(34, 18, 10, 3, 199.00, 597.00, 'เสร็จแล้ว'),
(35, 18, 14, 4, 299.00, 1196.00, 'เสร็จแล้ว'),
(36, 19, 13, 2, 250.00, 500.00, 'เสร็จแล้ว'),
(37, 19, 14, 2, 299.00, 598.00, 'เสร็จแล้ว'),
(38, 20, 10, 2, 199.00, 398.00, 'เสร็จแล้ว'),
(39, 20, 9, 3, 250.00, 750.00, 'เสร็จแล้ว'),
(40, 20, 14, 2, 299.00, 598.00, 'เสร็จแล้ว'),
(41, 21, 9, 2, 250.00, 500.00, 'เสร็จแล้ว'),
(42, 21, 10, 3, 199.00, 597.00, 'เสร็จแล้ว'),
(43, 21, 13, 2, 250.00, 500.00, 'เสร็จแล้ว'),
(44, 22, 9, 1, 250.00, 250.00, 'เสร็จแล้ว'),
(45, 22, 10, 1, 199.00, 199.00, 'เสร็จแล้ว'),
(56, 26, 9, 2, 250.00, 500.00, 'เสร็จแล้ว'),
(60, 27, 13, 3, 250.00, 750.00, 'เสร็จแล้ว'),
(61, 27, 14, 2, 299.00, 598.00, 'เสร็จแล้ว'),
(62, 28, 9, 2, 250.00, 500.00, 'เสร็จแล้ว'),
(63, 28, 10, 2, 199.00, 398.00, 'เสร็จแล้ว'),
(64, 29, 9, 2, 250.00, 500.00, 'เสร็จแล้ว'),
(66, 29, 14, 2, 299.00, 598.00, 'เสร็จแล้ว'),
(67, 30, 8, 2, 280.00, 560.00, 'เสร็จแล้ว'),
(68, 30, 9, 2, 250.00, 500.00, 'เสร็จแล้ว'),
(70, 31, 17, 1, 299.00, 299.00, 'รอดำเนินการ'),
(71, 32, 17, 2, 299.00, 598.00, 'รอดำเนินการ'),
(72, 32, 15, 2, 220.00, 440.00, 'รอดำเนินการ'),
(73, 32, 14, 2, 80.00, 160.00, 'รอดำเนินการ'),
(74, 32, 8, 2, 85.00, 170.00, 'รอดำเนินการ'),
(75, 33, 8, 1, 85.00, 85.00, 'รอดำเนินการ'),
(76, 34, 8, 1, 85.00, 85.00, 'รอดำเนินการ'),
(77, 34, 9, 1, 250.00, 250.00, 'รอดำเนินการ'),
(78, 35, 14, 1, 80.00, 80.00, 'รอดำเนินการ'),
(79, 36, 17, 1, 299.00, 299.00, 'รอดำเนินการ'),
(80, 36, 15, 1, 220.00, 220.00, 'รอดำเนินการ'),
(81, 37, 17, 1, 299.00, 299.00, 'รอดำเนินการ'),
(82, 38, 12, 1, 299.00, 299.00, 'รอดำเนินการ'),
(83, 39, 17, 1, 299.00, 299.00, 'รอดำเนินการ'),
(84, 40, 12, 1, 299.00, 299.00, 'รอดำเนินการ'),
(85, 41, 15, 1, 220.00, 220.00, 'รอดำเนินการ'),
(86, 41, 14, 1, 80.00, 80.00, 'รอดำเนินการ'),
(87, 42, 12, 1, 299.00, 299.00, 'รอดำเนินการ'),
(88, 43, 27, 1, 199.00, 199.00, 'รอดำเนินการ'),
(89, 43, 17, 1, 299.00, 299.00, 'รอดำเนินการ'),
(90, 43, 15, 1, 220.00, 220.00, 'รอดำเนินการ'),
(91, 43, 11, 1, 220.00, 220.00, 'รอดำเนินการ');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `type_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` text DEFAULT NULL,
  `stock` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `type_id`, `description`, `price`, `image`, `stock`, `created_at`) VALUES
(8, 'อะโวคาโดนมถั่วเหลือง', 2, 'อะโวคาโดนมถั่วเหลือง', 85.00, '1764691207_1763733002_อะโวคาโดนมถั่วเหลือง.jpg', 20, '2025-09-11 03:36:39'),
(9, 'สลัดเนื้อย่างโซบะ', 1, 'สลัดเนื้อย่างโซบะ', 250.00, '1764691184_1763731153_สลัดเนื้อย่างโซบะ.jpg', 50, '2025-09-17 02:57:35'),
(10, 'น้ำเปล่า', 2, 'น้ำเปล่า', 15.00, '1764691157_1763733399_น้ำเปล่า.jpg', 20, '2025-09-17 02:58:35'),
(11, 'สลัดเต้าหู้แซลมอนสาหร่ายสามชนิด', 1, 'สลัดเต้าหู้แซลมอนสาหร่ายสามชนิด', 220.00, '1764691090_1763731337_สลัดเต้าหู้แซลมอนสาหร่ายสามชนิด.jpg', 20, '2025-09-17 02:59:44'),
(12, 'สลัดปูอโวคาโด', 1, 'สลัดปูอโวคาโด', 299.00, '1764691067_1763731285_สลัดปูอโวคาโด.jpg', 20, '2025-09-17 04:08:58'),
(13, 'ขนมปังกระเทียมครีมชีส', 1, 'ขนมปังกระเทียมครีมชีส', 250.00, '1764691040_1763731626_ขนมปังกระเทียมครีมชีส.jpg', 20, '2025-09-17 04:15:07'),
(14, 'กีวีโซดา', 2, 'กีวีโซดา', 80.00, '1764691017_1763733187_กีวีโซดา.jpg', 20, '2025-09-18 07:33:32'),
(15, 'สลัดอกไก่ย่างควินัว', 1, 'สลัดอกไก่ย่างควินัว', 220.00, '1764690989_1763730144_10.2-สลัดอกไก่ย่างควินัว.webp', 20, '2025-09-18 07:42:25'),
(17, 'กุ้งย่างสมุนไพร', 1, 'กุ้งย่างสมุนไพร', 299.00, '1764690968_1763730675_กุ้งย่างสมุนไพร.jpg', 30, '2025-10-15 03:18:12'),
(27, 'ไก่อบสมุนไพร เสิร์ฟกับมันม่วงบดและหน่อไม้ฝรั่ง', 1, 'ไก่อบสมุนไพร เสิร์ฟกับมันม่วงบดและหน่อไม้ฝรั่ง', 199.00, '1764690918_1763730225_ไก่อบสมุนไพร เสิร์ฟกับมันม่วงบดและหน่อไม้ฝรั่ง.jpg', 10, '2025-11-14 08:08:02');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','admin') DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `last_login`, `image`) VALUES
(3, 'kun', 'staff01@gmail.com', '$2y$10$bculyYIMO.0y7K1ZI3gn3ucneHSIPe3f2YSIkLYZanW6/25Cn4WOG', 'admin', '2025-12-07 14:26:00', '2025-12-07 19:34:26', NULL),
(6, 'Admin', 'admin@test.com', '$2y$10$wtwS0Jy9WySrPRK0HfTd4.lFbrjtEsDQK3n6wXMQFfN6TU1YPefK2', 'admin', '2025-12-07 14:46:53', '2025-12-07 14:47:23', NULL),
(8, 'hondaa', 'honda1@gmail.com', '$2y$10$H2/q4ifYQ8QFsu5hpwtkIOgAYqEaWOzNwjhXHFNo76KaKezV7Ov9a', 'admin', '2025-12-07 18:33:54', '2025-12-07 18:35:52', NULL),
(9, 'dddd', 'd@gmail.com', '$2y$10$oqgqptoTDOUzhzy/RK8qHO.nghznhglqKmprUZVr7SLw3oEw.XOve', 'customer', '2025-12-07 18:34:52', '2025-12-07 19:18:48', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `idx_booking_date` (`booking_date`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `log_activity`
--
ALTER TABLE `log_activity`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `log_activity`
--
ALTER TABLE `log_activity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
