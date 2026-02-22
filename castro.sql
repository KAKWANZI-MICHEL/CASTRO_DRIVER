-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 22, 2026 at 01:13 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `castro`
--

-- --------------------------------------------------------

--
-- Table structure for table `drivers`
--

CREATE TABLE `drivers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `DOB` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `drivers`
--

INSERT INTO `drivers` (`id`, `name`, `email`, `password`, `DOB`, `profile_image`, `created_at`, `updated_at`) VALUES
(1, 'David Opondo', 'david.opondo@email.com', 'l/bViP9AWsEfyqDc7FQQgw==:YPmutGg8wCxiX3uygn9Yew==', '1985-03-10', 'david.jpg', '2026-02-15 15:51:11', '2026-02-16 13:16:25'),
(2, 'Sarah Nalubega', 'sarah.nalubega@email.com', 'l/bViP9AWsEfyqDc7FQQgw==:YPmutGg8wCxiX3uygn9Yew==', '1990-07-25', 'sarah.jpg', '2026-02-15 15:51:11', '2026-02-16 13:16:30'),
(3, 'Robert Mugisha', 'robert.mugisha@email.com', 'l/bViP9AWsEfyqDc7FQQgw==:YPmutGg8wCxiX3uygn9Yew==', '1987-12-05', 'robert.jpg', '2026-02-15 15:51:11', '2026-02-16 13:16:35');

-- --------------------------------------------------------

--
-- Table structure for table `driver_location_history`
--

CREATE TABLE `driver_location_history` (
  `id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `driver_location_history`
--

INSERT INTO `driver_location_history` (`id`, `driver_id`, `latitude`, `longitude`, `created_at`) VALUES
(1, 1, 0.32750000, 32.63000000, '2026-02-15 15:52:04'),
(2, 2, 0.35210000, 32.59000000, '2026-02-15 15:52:04'),
(3, 3, 0.29870000, 32.60500000, '2026-02-15 15:52:04'),
(4, 1, 37.78583400, -122.40641700, '2026-02-16 13:40:00'),
(5, 1, 37.78583400, -122.40641700, '2026-02-16 13:40:10'),
(6, 1, 37.78583400, -122.40641700, '2026-02-16 13:40:20'),
(7, 1, 37.78583400, -122.40641700, '2026-02-16 13:40:30'),
(8, 1, 37.78583400, -122.40641700, '2026-02-16 13:40:40'),
(9, 1, 37.78583400, -122.40641700, '2026-02-16 13:40:50'),
(10, 1, 37.78583400, -122.40641700, '2026-02-16 13:41:00'),
(11, 1, 37.78583400, -122.40641700, '2026-02-16 13:41:10'),
(12, 1, 37.78583400, -122.40641700, '2026-02-16 13:41:20'),
(13, 1, 37.78583400, -122.40641700, '2026-02-16 13:41:30'),
(14, 1, 37.78583400, -122.40641700, '2026-02-16 13:41:40'),
(15, 1, 37.78583400, -122.40641700, '2026-02-16 13:41:50'),
(16, 1, 37.78583400, -122.40641700, '2026-02-16 13:42:00'),
(17, 1, 37.78583400, -122.40641700, '2026-02-16 13:42:10'),
(18, 1, 37.78583400, -122.40641700, '2026-02-16 13:42:20'),
(19, 1, 37.78583400, -122.40641700, '2026-02-16 13:42:30'),
(20, 1, 37.78583400, -122.40641700, '2026-02-16 13:42:40'),
(21, 1, 37.78583400, -122.40641700, '2026-02-16 13:42:50'),
(22, 1, 37.78583400, -122.40641700, '2026-02-16 13:43:00'),
(23, 1, 37.78583400, -122.40641700, '2026-02-16 13:49:22'),
(24, 1, 37.78583400, -122.40641700, '2026-02-16 13:49:32'),
(25, 1, 37.78583400, -122.40641700, '2026-02-16 13:49:42'),
(26, 1, 37.78583400, -122.40641700, '2026-02-16 13:49:52'),
(27, 1, 37.78583400, -122.40641700, '2026-02-16 13:50:02'),
(28, 1, 37.78583400, -122.40641700, '2026-02-16 13:50:12'),
(29, 1, 37.78583400, -122.40641700, '2026-02-16 13:50:22'),
(30, 1, 37.78583400, -122.40641700, '2026-02-16 13:50:32'),
(31, 1, 37.78583400, -122.40641700, '2026-02-16 13:50:42'),
(32, 1, 37.78583400, -122.40641700, '2026-02-16 13:50:52'),
(33, 1, 37.78583400, -122.40641700, '2026-02-16 13:51:02'),
(34, 1, 37.78583400, -122.40641700, '2026-02-16 13:51:12'),
(35, 1, 37.78583400, -122.40641700, '2026-02-16 13:51:22'),
(36, 1, 37.78583400, -122.40641700, '2026-02-16 13:51:32'),
(37, 1, 37.78583400, -122.40641700, '2026-02-16 13:51:42'),
(38, 1, 37.78583400, -122.40641700, '2026-02-16 13:51:52'),
(39, 1, 37.78583400, -122.40641700, '2026-02-16 13:52:02'),
(40, 1, 37.78583400, -122.40641700, '2026-02-16 13:52:12'),
(41, 1, 37.78583400, -122.40641700, '2026-02-16 13:52:22'),
(42, 1, 37.78583400, -122.40641700, '2026-02-16 13:52:32'),
(43, 1, 37.78583400, -122.40641700, '2026-02-16 13:52:42'),
(44, 1, 37.78583400, -122.40641700, '2026-02-16 13:52:52'),
(45, 1, 37.78583400, -122.40641700, '2026-02-16 13:53:02'),
(46, 1, 37.78583400, -122.40641700, '2026-02-16 13:53:12'),
(47, 1, 37.78583400, -122.40641700, '2026-02-16 13:53:22'),
(48, 1, 37.78583400, -122.40641700, '2026-02-16 13:53:32'),
(49, 1, 37.78583400, -122.40641700, '2026-02-16 13:53:42'),
(50, 1, 37.78583400, -122.40641700, '2026-02-16 13:53:52'),
(51, 1, 37.78583400, -122.40641700, '2026-02-16 13:54:43'),
(52, 1, 37.78583400, -122.40641700, '2026-02-16 13:54:53'),
(53, 1, 37.78583400, -122.40641700, '2026-02-16 13:55:03'),
(54, 1, 37.78583400, -122.40641700, '2026-02-16 13:55:13'),
(55, 1, 37.78583400, -122.40641700, '2026-02-16 13:55:23'),
(56, 1, 37.78583400, -122.40641700, '2026-02-16 13:55:33'),
(57, 1, 37.78583400, -122.40641700, '2026-02-16 13:55:42'),
(58, 1, 37.78583400, -122.40641700, '2026-02-16 13:55:52'),
(59, 1, 37.78583400, -122.40641700, '2026-02-16 13:56:02'),
(60, 1, 37.78583400, -122.40641700, '2026-02-16 13:56:12'),
(61, 1, 37.78583400, -122.40641700, '2026-02-16 13:56:22'),
(62, 1, 37.78583400, -122.40641700, '2026-02-16 13:56:33'),
(63, 1, 37.78583400, -122.40641700, '2026-02-16 13:56:43'),
(64, 1, 37.78583400, -122.40641700, '2026-02-16 13:56:53'),
(65, 1, 37.78583400, -122.40641700, '2026-02-16 13:57:03'),
(66, 1, 37.78583400, -122.40641700, '2026-02-16 13:57:12'),
(67, 1, 37.78583400, -122.40641700, '2026-02-16 13:57:22'),
(68, 1, 37.78583400, -122.40641700, '2026-02-16 13:57:33'),
(69, 1, 37.78583400, -122.40641700, '2026-02-16 13:57:53'),
(70, 1, 37.78583400, -122.40641700, '2026-02-16 13:58:31'),
(71, 1, 37.78583400, -122.40641700, '2026-02-16 13:58:41'),
(72, 1, 37.78583400, -122.40641700, '2026-02-16 13:58:51'),
(73, 1, 37.78583400, -122.40641700, '2026-02-16 13:59:01'),
(74, 1, 37.78583400, -122.40641700, '2026-02-16 13:59:11'),
(75, 1, 37.78583400, -122.40641700, '2026-02-16 13:59:21'),
(76, 1, 37.78583400, -122.40641700, '2026-02-16 13:59:31'),
(77, 1, 37.78583400, -122.40641700, '2026-02-16 13:59:41'),
(78, 1, 37.78583400, -122.40641700, '2026-02-16 13:59:51'),
(79, 1, 37.78583400, -122.40641700, '2026-02-16 14:00:01'),
(80, 1, 37.78583400, -122.40641700, '2026-02-16 14:00:11'),
(81, 1, 37.78583400, -122.40641700, '2026-02-16 14:00:21'),
(82, 1, 37.78583400, -122.40641700, '2026-02-16 14:00:31'),
(83, 1, 37.78583400, -122.40641700, '2026-02-16 14:00:41'),
(84, 1, 37.78583400, -122.40641700, '2026-02-16 14:00:51'),
(85, 1, 37.78583400, -122.40641700, '2026-02-16 14:01:01'),
(86, 1, 37.78583400, -122.40641700, '2026-02-16 14:01:11'),
(87, 1, 37.78583400, -122.40641700, '2026-02-16 14:01:21'),
(88, 1, 37.78583400, -122.40641700, '2026-02-16 14:01:31'),
(89, 1, 37.78583400, -122.40641700, '2026-02-16 14:01:41'),
(90, 1, 37.78583400, -122.40641700, '2026-02-16 14:01:51'),
(91, 1, 37.78583400, -122.40641700, '2026-02-16 14:02:01'),
(92, 1, 37.78583400, -122.40641700, '2026-02-16 14:02:11'),
(93, 1, 37.78583400, -122.40641700, '2026-02-16 14:02:21'),
(94, 1, 37.78583400, -122.40641700, '2026-02-16 14:02:31'),
(95, 1, 37.78583400, -122.40641700, '2026-02-16 14:02:41'),
(96, 1, 37.78583400, -122.40641700, '2026-02-16 14:02:51'),
(97, 1, 37.78583400, -122.40641700, '2026-02-16 14:03:01'),
(98, 1, 37.78583400, -122.40641700, '2026-02-16 14:03:11'),
(99, 1, 37.78583400, -122.40641700, '2026-02-16 14:03:21'),
(100, 1, 37.78583400, -122.40641700, '2026-02-16 14:03:31'),
(101, 1, 37.78583400, -122.40641700, '2026-02-16 14:03:41'),
(102, 1, 37.78583400, -122.40641700, '2026-02-16 14:03:51'),
(103, 1, 37.78583400, -122.40641700, '2026-02-16 14:04:01'),
(104, 1, 37.78583400, -122.40641700, '2026-02-16 14:04:11'),
(105, 1, 37.78583400, -122.40641700, '2026-02-16 14:04:21'),
(106, 1, 37.78583400, -122.40641700, '2026-02-16 14:04:31'),
(107, 1, 37.78583400, -122.40641700, '2026-02-16 14:04:41'),
(108, 1, 37.78583400, -122.40641700, '2026-02-16 14:04:51'),
(109, 1, 37.78583400, -122.40641700, '2026-02-16 14:05:02'),
(110, 1, 37.78583400, -122.40641700, '2026-02-16 14:06:13'),
(111, 1, 37.78583400, -122.40641700, '2026-02-16 14:06:43'),
(112, 1, 37.78583400, -122.40641700, '2026-02-16 14:07:13'),
(113, 1, 37.78583400, -122.40641700, '2026-02-16 14:07:43'),
(114, 1, 37.78583400, -122.40641700, '2026-02-16 14:08:13'),
(115, 1, 37.78583400, -122.40641700, '2026-02-16 14:08:43'),
(116, 1, 37.78583400, -122.40641700, '2026-02-16 14:10:01'),
(117, 1, 37.78583400, -122.40641700, '2026-02-16 14:10:31'),
(118, 1, 37.78583400, -122.40641700, '2026-02-16 14:11:01'),
(119, 1, 37.78583400, -122.40641700, '2026-02-16 14:11:31'),
(120, 1, 37.78583400, -122.40641700, '2026-02-16 14:12:01'),
(121, 1, 37.78583400, -122.40641700, '2026-02-16 14:12:31'),
(122, 1, 37.78583400, -122.40641700, '2026-02-16 14:13:01'),
(123, 1, 37.78583400, -122.40641700, '2026-02-16 14:13:31'),
(124, 1, 37.78583400, -122.40641700, '2026-02-16 14:14:01'),
(125, 1, 37.78583400, -122.40641700, '2026-02-16 14:14:31'),
(126, 1, 37.78583400, -122.40641700, '2026-02-16 14:15:01'),
(127, 1, 37.78583400, -122.40641700, '2026-02-16 14:15:31'),
(128, 1, 37.78583400, -122.40641700, '2026-02-16 14:16:01'),
(129, 1, 37.78583400, -122.40641700, '2026-02-16 14:16:31'),
(130, 1, 37.78583400, -122.40641700, '2026-02-16 14:17:01'),
(131, 1, 37.78583400, -122.40641700, '2026-02-16 14:17:31'),
(132, 1, 37.78583400, -122.40641700, '2026-02-16 14:18:01'),
(133, 1, 37.78583400, -122.40641700, '2026-02-16 14:18:31'),
(134, 1, 37.78583400, -122.40641700, '2026-02-16 14:19:01'),
(135, 1, 37.78583400, -122.40641700, '2026-02-16 14:19:31'),
(136, 1, 37.78583400, -122.40641700, '2026-02-16 14:20:01'),
(137, 1, 37.78583400, -122.40641700, '2026-02-16 14:20:31'),
(138, 1, 37.78583400, -122.40641700, '2026-02-16 14:21:01'),
(139, 1, 37.78583400, -122.40641700, '2026-02-16 14:21:31'),
(140, 1, 37.78583400, -122.40641700, '2026-02-16 14:22:54'),
(141, 1, 37.78583400, -122.40641700, '2026-02-16 14:23:23'),
(142, 1, 37.78583400, -122.40641700, '2026-02-16 14:23:53'),
(143, 1, 37.78583400, -122.40641700, '2026-02-16 14:24:23'),
(144, 1, 37.78583400, -122.40641700, '2026-02-16 14:24:54'),
(145, 1, 37.78583400, -122.40641700, '2026-02-16 14:25:23'),
(146, 1, 37.78583400, -122.40641700, '2026-02-16 14:25:53'),
(147, 1, 37.78583400, -122.40641700, '2026-02-16 14:26:23'),
(148, 1, 37.78583400, -122.40641700, '2026-02-16 14:26:53'),
(149, 1, 37.78583400, -122.40641700, '2026-02-16 14:27:24'),
(150, 1, 37.78583400, -122.40641700, '2026-02-16 14:27:53'),
(151, 1, 37.78583400, -122.40641700, '2026-02-16 14:28:23'),
(152, 1, 37.78583400, -122.40641700, '2026-02-16 14:28:53'),
(153, 1, 37.78583400, -122.40641700, '2026-02-16 14:29:23'),
(154, 1, 37.78583400, -122.40641700, '2026-02-16 14:29:53'),
(155, 1, 37.78583400, -122.40641700, '2026-02-16 14:30:24'),
(156, 1, 37.78583400, -122.40641700, '2026-02-16 14:30:53'),
(157, 1, 37.78583400, -122.40641700, '2026-02-16 14:31:23'),
(158, 1, 37.78583400, -122.40641700, '2026-02-16 14:31:53'),
(159, 1, 37.78583400, -122.40641700, '2026-02-16 14:32:23'),
(160, 1, 37.78583400, -122.40641700, '2026-02-16 14:32:53'),
(161, 1, 37.78583400, -122.40641700, '2026-02-16 14:33:23'),
(162, 1, 37.78583400, -122.40641700, '2026-02-16 14:33:53'),
(163, 1, 37.78583400, -122.40641700, '2026-02-16 14:34:23'),
(164, 1, 37.78583400, -122.40641700, '2026-02-16 14:34:53'),
(165, 1, 37.78583400, -122.40641700, '2026-02-16 14:35:23'),
(166, 1, 37.78583400, -122.40641700, '2026-02-16 14:35:53'),
(167, 1, 37.78583400, -122.40641700, '2026-02-16 14:36:23'),
(168, 1, 37.78583400, -122.40641700, '2026-02-16 14:36:53'),
(169, 1, 37.78583400, -122.40641700, '2026-02-16 14:37:23'),
(170, 1, 37.78583400, -122.40641700, '2026-02-16 14:37:53'),
(171, 1, 37.78583400, -122.40641700, '2026-02-16 14:38:23'),
(172, 1, 37.78583400, -122.40641700, '2026-02-16 14:38:53'),
(173, 1, 37.78583400, -122.40641700, '2026-02-16 14:39:23'),
(174, 1, 37.78583400, -122.40641700, '2026-02-16 14:39:53'),
(175, 1, 37.78583400, -122.40641700, '2026-02-16 14:40:23'),
(176, 1, 37.78583400, -122.40641700, '2026-02-16 14:40:53'),
(177, 1, 37.78583400, -122.40641700, '2026-02-16 14:41:24'),
(178, 1, 37.78583400, -122.40641700, '2026-02-16 14:41:53'),
(179, 1, 37.78583400, -122.40641700, '2026-02-16 14:42:23'),
(180, 1, 37.78583400, -122.40641700, '2026-02-16 14:42:53'),
(181, 1, 37.78583400, -122.40641700, '2026-02-16 14:43:23'),
(182, 1, 37.78583400, -122.40641700, '2026-02-16 14:43:53'),
(183, 1, 37.78583400, -122.40641700, '2026-02-16 14:44:23'),
(184, 1, 37.78583400, -122.40641700, '2026-02-16 14:44:53'),
(185, 1, 37.78583400, -122.40641700, '2026-02-16 14:45:23'),
(186, 1, 37.78583400, -122.40641700, '2026-02-16 14:45:53'),
(187, 1, 37.78583400, -122.40641700, '2026-02-16 14:46:23'),
(188, 1, 37.78583400, -122.40641700, '2026-02-16 14:46:53'),
(189, 1, 37.78583400, -122.40641700, '2026-02-16 14:47:24'),
(190, 1, 37.78583400, -122.40641700, '2026-02-16 14:47:54'),
(191, 1, 37.78583400, -122.40641700, '2026-02-16 14:48:23'),
(192, 1, 37.78583400, -122.40641700, '2026-02-16 14:48:54'),
(193, 1, 37.78583400, -122.40641700, '2026-02-16 14:49:23'),
(194, 1, 37.78583400, -122.40641700, '2026-02-16 14:49:54'),
(195, 1, 37.78583400, -122.40641700, '2026-02-16 14:50:54'),
(196, 1, 37.78583400, -122.40641700, '2026-02-16 14:51:23'),
(197, 1, 37.78583400, -122.40641700, '2026-02-16 14:51:53'),
(198, 1, 37.78583400, -122.40641700, '2026-02-16 14:52:23'),
(199, 1, 37.78583400, -122.40641700, '2026-02-16 14:52:53'),
(200, 1, 37.78583400, -122.40641700, '2026-02-16 14:53:23'),
(201, 1, 37.78583400, -122.40641700, '2026-02-16 14:53:53');

-- --------------------------------------------------------

--
-- Table structure for table `driver_stats`
--

CREATE TABLE `driver_stats` (
  `id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `total_rides` int(11) DEFAULT 0,
  `total_earnings` decimal(10,2) DEFAULT 0.00,
  `rating` decimal(2,1) DEFAULT 0.0,
  `total_ratings` int(11) DEFAULT 0,
  `acceptance_rate` decimal(5,2) DEFAULT 100.00,
  `cancellation_rate` decimal(5,2) DEFAULT 0.00,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `driver_stats`
--

INSERT INTO `driver_stats` (`id`, `driver_id`, `total_rides`, `total_earnings`, `rating`, `total_ratings`, `acceptance_rate`, `cancellation_rate`, `updated_at`) VALUES
(1, 1, 150, 450000.00, 4.8, 45, 100.00, 0.00, '2026-02-15 15:52:04'),
(2, 2, 89, 178000.00, 4.5, 30, 100.00, 0.00, '2026-02-15 15:52:04'),
(3, 3, 212, 169600.00, 4.9, 60, 100.00, 0.00, '2026-02-15 15:52:04');

-- --------------------------------------------------------

--
-- Table structure for table `driver_status`
--

CREATE TABLE `driver_status` (
  `id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `current_latitude` decimal(10,8) DEFAULT NULL,
  `current_longitude` decimal(11,8) DEFAULT NULL,
  `is_available` tinyint(1) DEFAULT 0,
  `is_online` tinyint(1) DEFAULT 0,
  `current_ride_id` int(11) DEFAULT NULL,
  `last_location_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `driver_status`
--

INSERT INTO `driver_status` (`id`, `driver_id`, `current_latitude`, `current_longitude`, `is_available`, `is_online`, `current_ride_id`, `last_location_update`, `created_at`) VALUES
(1, 1, 37.78583400, -122.40641700, 1, 1, NULL, '2026-02-16 14:53:53', '2026-02-15 15:52:04'),
(2, 2, 0.35210000, 32.59000000, 1, 1, NULL, '2026-02-15 15:52:04', '2026-02-15 15:52:04'),
(3, 3, 0.29870000, 32.60500000, 0, 1, NULL, '2026-02-15 15:52:04', '2026-02-15 15:52:04');

-- --------------------------------------------------------

--
-- Table structure for table `driver_vehicles`
--

CREATE TABLE `driver_vehicles` (
  `id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `vehicle_type` enum('car','motorcycle','bike') NOT NULL,
  `vehicle_model` varchar(100) NOT NULL,
  `vehicle_color` varchar(50) DEFAULT NULL,
  `plate_number` varchar(20) NOT NULL,
  `registration_document` varchar(255) DEFAULT NULL,
  `insurance_document` varchar(255) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `driver_vehicles`
--

INSERT INTO `driver_vehicles` (`id`, `driver_id`, `vehicle_type`, `vehicle_model`, `vehicle_color`, `plate_number`, `registration_document`, `insurance_document`, `is_verified`, `verified_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'car', 'Toyota Premio', 'White', 'UAA 123A', NULL, NULL, 1, NULL, '2026-02-15 15:51:11', '2026-02-15 15:51:11'),
(2, 2, 'motorcycle', 'Bajaj Boxer', 'Red', 'UBB 456B', NULL, NULL, 1, NULL, '2026-02-15 15:51:11', '2026-02-15 15:51:11'),
(3, 3, 'bike', 'Mountain Bike', 'Black', 'UCC 789C', NULL, NULL, 1, NULL, '2026-02-15 15:51:11', '2026-02-15 15:51:11');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_type` enum('passenger','driver') NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_type`, `user_id`, `type`, `title`, `message`, `data`, `is_read`, `created_at`) VALUES
(1, 'passenger', 1, 'ride_confirmed', 'Ride Confirmed', 'Your ride has been confirmed. Driver is on the way.', '{\"ride_id\": 1}', 0, '2026-02-15 15:52:04'),
(2, 'driver', 1, 'new_ride', 'New Ride Request', 'You have a new ride request from Kololo to Nakasero.', '{\"ride_id\": 1}', 1, '2026-02-15 15:52:04'),
(3, 'passenger', 2, 'payment_success', 'Payment Successful', 'Your payment of UGX 9,800 was successful.', '{\"ride_id\": 2, \"amount\": 9800}', 0, '2026-02-15 15:52:04');

-- --------------------------------------------------------

--
-- Table structure for table `passengers`
--

CREATE TABLE `passengers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `DOB` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `passengers`
--

INSERT INTO `passengers` (`id`, `name`, `email`, `password`, `DOB`, `profile_image`, `created_at`, `updated_at`) VALUES
(1, 'John Doe', 'john.doe@email.com', 'K+CKBUA6TulgQwlDKAofew==:JvqZBlHzCt0pjonmtfEcoQ==', '1990-05-15', 'john.jpg', '2026-02-15 15:50:14', '2026-02-16 11:23:17'),
(2, 'Jane Smith', 'jane.smith@email.com', 'K+CKBUA6TulgQwlDKAofew==:JvqZBlHzCt0pjonmtfEcoQ==', '1992-08-22', 'jane.jpg', '2026-02-15 15:50:14', '2026-02-16 11:25:04'),
(3, 'Michael Johnson', 'michael.j@email.com', 'K+CKBUA6TulgQwlDKAofew==:JvqZBlHzCt0pjonmtfEcoQ==', '1988-11-30', 'michael.jpg', '2026-02-15 15:50:14', '2026-02-16 11:25:09'),
(4, 'Noel Emma', 'rutsnoel@gmail.com', 'l/bViP9AWsEfyqDc7FQQgw==:YPmutGg8wCxiX3uygn9Yew==', '1999-02-17', NULL, '2026-02-16 11:33:52', '2026-02-16 11:33:52');

-- --------------------------------------------------------

--
-- Table structure for table `passenger_addresses`
--

CREATE TABLE `passenger_addresses` (
  `id` int(11) NOT NULL,
  `passenger_id` int(11) NOT NULL,
  `address_name` varchar(100) NOT NULL,
  `address_text` text NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `is_favorite` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `passenger_addresses`
--

INSERT INTO `passenger_addresses` (`id`, `passenger_id`, `address_name`, `address_text`, `latitude`, `longitude`, `is_favorite`, `created_at`) VALUES
(1, 1, 'Home', 'Kololo, Kampala', 0.34760000, 32.58250000, 1, '2026-02-15 15:51:11'),
(2, 1, 'Work', 'Nakasero, Kampala', 0.31360000, 32.58110000, 0, '2026-02-15 15:51:11'),
(3, 2, 'Home', 'Muyenga, Kampala', 0.30410000, 32.61420000, 1, '2026-02-15 15:51:11');

-- --------------------------------------------------------

--
-- Table structure for table `rides`
--

CREATE TABLE `rides` (
  `id` int(11) NOT NULL,
  `passenger_id` int(11) NOT NULL,
  `driver_id` int(11) DEFAULT NULL,
  `vehicle_type` enum('car','motorcycle','bike') NOT NULL,
  `pickup_latitude` decimal(10,8) NOT NULL,
  `pickup_longitude` decimal(11,8) NOT NULL,
  `pickup_address` text NOT NULL,
  `dropoff_latitude` decimal(10,8) NOT NULL,
  `dropoff_longitude` decimal(11,8) NOT NULL,
  `dropoff_address` text NOT NULL,
  `distance_km` decimal(5,2) DEFAULT NULL,
  `estimated_duration_minutes` int(11) DEFAULT NULL,
  `base_fare` decimal(10,2) DEFAULT NULL,
  `distance_fare` decimal(10,2) DEFAULT NULL,
  `total_fare` decimal(10,2) DEFAULT NULL,
  `status` enum('searching','driver_assigned','driver_arrived','in_progress','completed','cancelled_by_passenger','cancelled_by_driver','cancelled_system') DEFAULT 'searching',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `assigned_at` timestamp NULL DEFAULT NULL,
  `driver_arrived_at` timestamp NULL DEFAULT NULL,
  `started_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_status` enum('pending','paid','failed') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rides`
--

INSERT INTO `rides` (`id`, `passenger_id`, `driver_id`, `vehicle_type`, `pickup_latitude`, `pickup_longitude`, `pickup_address`, `dropoff_latitude`, `dropoff_longitude`, `dropoff_address`, `distance_km`, `estimated_duration_minutes`, `base_fare`, `distance_fare`, `total_fare`, `status`, `created_at`, `assigned_at`, `driver_arrived_at`, `started_at`, `completed_at`, `cancelled_at`, `payment_method`, `payment_status`) VALUES
(1, 1, 1, 'car', 0.34760000, 32.58250000, 'Kololo, Kampala', 0.31360000, 32.58110000, 'Nakasero, Kampala', 3.80, 10, 3000.00, 7600.00, 10600.00, 'completed', '2026-02-15 15:52:04', NULL, NULL, NULL, '2026-02-15 15:52:04', NULL, NULL, 'pending'),
(2, 2, 2, 'motorcycle', 0.30410000, 32.61420000, 'Muyenga, Kampala', 0.35120000, 32.59450000, 'Bukoto, Kampala', 5.20, 15, 2000.00, 7800.00, 9800.00, 'completed', '2026-02-15 15:52:04', NULL, NULL, NULL, '2026-02-15 15:52:04', NULL, NULL, 'pending'),
(3, 1, 3, 'bike', 0.29130000, 32.59240000, 'Kansanga, Kampala', 0.31770000, 32.58170000, 'Kampala Road', 2.50, 8, 1000.00, 2000.00, 3000.00, 'in_progress', '2026-02-15 15:52:04', NULL, NULL, NULL, NULL, NULL, NULL, 'pending'),
(37, 1, NULL, 'car', 0.34760000, 32.58250000, 'Kololo, Kampala (Acacia Mall)', 0.31360000, 32.58110000, 'Nakasero, Kampala (Work)', 3.80, 8, 3000.00, 7600.00, 10600.00, 'searching', '2026-02-16 14:13:17', NULL, NULL, NULL, NULL, NULL, NULL, 'pending'),
(38, 2, NULL, 'car', 0.34000000, 32.58500000, 'Kamwokya, Kampala (Near Kira Road)', 0.33300000, 32.58900000, 'Kisementi, Kampala (Shoprite)', 2.10, 5, 3000.00, 4200.00, 7200.00, 'searching', '2026-02-16 14:13:17', NULL, NULL, NULL, NULL, NULL, NULL, 'pending'),
(39, 3, NULL, 'car', 0.35120000, 32.59450000, 'Bukoto, Kampala (Kira Road)', 0.30410000, 32.61420000, 'Muyenga, Kampala (Tank Hill)', 5.20, 11, 3000.00, 10400.00, 13400.00, 'searching', '2026-02-16 14:13:17', NULL, NULL, NULL, NULL, NULL, NULL, 'pending'),
(40, 1, NULL, 'car', 0.35800000, 32.61500000, 'Ntinda, Kampala (Shopping Centre)', 0.32400000, 32.57900000, 'Garden City Mall, Kampala', 4.50, 9, 3000.00, 9000.00, 12000.00, 'searching', '2026-02-16 14:13:17', NULL, NULL, NULL, NULL, NULL, NULL, 'pending'),
(41, 2, NULL, 'car', 0.33500000, 32.56900000, 'Wandegeya, Kampala (Near Makerere)', 0.34560000, 32.58100000, 'Kololo, Kampala', 2.50, 6, 3000.00, 5000.00, 8000.00, 'searching', '2026-02-16 14:11:17', NULL, NULL, NULL, NULL, NULL, NULL, 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `ride_ratings`
--

CREATE TABLE `ride_ratings` (
  `id` int(11) NOT NULL,
  `ride_id` int(11) NOT NULL,
  `passenger_id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ride_ratings`
--

INSERT INTO `ride_ratings` (`id`, `ride_id`, `passenger_id`, `driver_id`, `rating`, `comment`, `created_at`) VALUES
(1, 1, 1, 1, 5, 'Excellent driver, very professional', '2026-02-15 15:52:04'),
(2, 2, 2, 2, 4, 'Good ride, but arrived a bit late', '2026-02-15 15:52:04'),
(3, 3, 1, 3, 5, 'Great bike ride, very safe', '2026-02-15 15:52:04');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `drivers`
--
ALTER TABLE `drivers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `driver_location_history`
--
ALTER TABLE `driver_location_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_driver_time` (`driver_id`,`created_at`);

--
-- Indexes for table `driver_stats`
--
ALTER TABLE `driver_stats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `driver_id` (`driver_id`),
  ADD KEY `idx_driver` (`driver_id`);

--
-- Indexes for table `driver_status`
--
ALTER TABLE `driver_status`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `driver_id` (`driver_id`),
  ADD KEY `idx_location` (`current_latitude`,`current_longitude`),
  ADD KEY `idx_availability` (`is_available`,`is_online`);

--
-- Indexes for table `driver_vehicles`
--
ALTER TABLE `driver_vehicles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `driver_id` (`driver_id`),
  ADD KEY `idx_driver` (`driver_id`),
  ADD KEY `idx_vehicle_type` (`vehicle_type`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_type`,`user_id`,`is_read`);

--
-- Indexes for table `passengers`
--
ALTER TABLE `passengers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `passenger_addresses`
--
ALTER TABLE `passenger_addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_passenger` (`passenger_id`);

--
-- Indexes for table `rides`
--
ALTER TABLE `rides`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_passenger` (`passenger_id`),
  ADD KEY `idx_driver` (`driver_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `ride_ratings`
--
ALTER TABLE `ride_ratings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ride_id` (`ride_id`),
  ADD KEY `passenger_id` (`passenger_id`),
  ADD KEY `idx_ride` (`ride_id`),
  ADD KEY `idx_driver_rating` (`driver_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `drivers`
--
ALTER TABLE `drivers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `driver_location_history`
--
ALTER TABLE `driver_location_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=202;

--
-- AUTO_INCREMENT for table `driver_stats`
--
ALTER TABLE `driver_stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `driver_status`
--
ALTER TABLE `driver_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `driver_vehicles`
--
ALTER TABLE `driver_vehicles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `passengers`
--
ALTER TABLE `passengers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `passenger_addresses`
--
ALTER TABLE `passenger_addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `rides`
--
ALTER TABLE `rides`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `ride_ratings`
--
ALTER TABLE `ride_ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `driver_location_history`
--
ALTER TABLE `driver_location_history`
  ADD CONSTRAINT `driver_location_history_ibfk_1` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `driver_stats`
--
ALTER TABLE `driver_stats`
  ADD CONSTRAINT `driver_stats_ibfk_1` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `driver_status`
--
ALTER TABLE `driver_status`
  ADD CONSTRAINT `driver_status_ibfk_1` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `driver_vehicles`
--
ALTER TABLE `driver_vehicles`
  ADD CONSTRAINT `driver_vehicles_ibfk_1` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `passenger_addresses`
--
ALTER TABLE `passenger_addresses`
  ADD CONSTRAINT `passenger_addresses_ibfk_1` FOREIGN KEY (`passenger_id`) REFERENCES `passengers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rides`
--
ALTER TABLE `rides`
  ADD CONSTRAINT `rides_ibfk_1` FOREIGN KEY (`passenger_id`) REFERENCES `passengers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `rides_ibfk_2` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ride_ratings`
--
ALTER TABLE `ride_ratings`
  ADD CONSTRAINT `ride_ratings_ibfk_2` FOREIGN KEY (`passenger_id`) REFERENCES `passengers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ride_ratings_ibfk_3` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
