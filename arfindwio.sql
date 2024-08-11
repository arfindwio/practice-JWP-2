-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 29, 2024 at 10:11 AM
-- Server version: 10.4.19-MariaDB
-- PHP Version: 8.0.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `arfindwio`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_order`
--

CREATE TABLE `tb_order` (
  `order_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `user_id` int(5) NOT NULL,
  `wedding_date` date NOT NULL,
  `status` enum('requested','approved','rejected') NOT NULL DEFAULT 'requested',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tb_order`
--

INSERT INTO `tb_order` (`order_id`, `service_id`, `user_id`, `wedding_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 8, 3, '2024-06-22', 'approved', '2024-06-14 10:42:40', '2024-06-14 10:42:40'),
(9, 8, 3, '2024-06-29', 'requested', '2024-06-14 23:31:12', '2024-06-14 23:31:12'),
(12, 8, 3, '2024-08-01', 'requested', '2024-07-29 13:08:06', '2024-07-29 13:08:06'),
(14, 8, 9, '2024-08-08', 'approved', '2024-07-29 14:43:15', '2024-07-29 14:43:15');

-- --------------------------------------------------------

--
-- Table structure for table `tb_services`
--

CREATE TABLE `tb_services` (
  `service_id` int(11) NOT NULL,
  `image` varchar(100) NOT NULL,
  `package_name` varchar(256) NOT NULL,
  `description` text NOT NULL,
  `price` int(11) NOT NULL,
  `status_publish` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tb_services`
--

INSERT INTO `tb_services` (`service_id`, `image`, `package_name`, `description`, `price`, `status_publish`, `created_at`, `updated_at`) VALUES
(8, '666b5668dfa34_Tips-Sukses-Bisnis-Wedding-Organizer-Pranata-Printing-1170x658.jpg', 'Wedding Organizer Platinum Package', 'This package includes full wedding planning and coordination services, from venue selection to vendor management. Ideal for couples who want a stress-free wedding planning experience.', 15000000, 1, '2024-06-14 03:28:24', '2024-06-14 03:28:24'),
(9, '666b58814376d_medina-catering_pernikahan-fani-hanief-di-menara-165_9.jpg', 'Wedding Organizer Gold Package', 'The Gold Package offers partial wedding planning and coordination services, including vendor recommendations and day-of coordination. Perfect for couples who need assistance with specific aspects of their wedding.', 8000000, 1, '2024-06-14 03:37:21', '2024-06-14 03:37:21'),
(11, '66a73ead13511_images.jpg', 'Wedding Organizer Silver Package', 'This package provides wedding consultation and day-of support, including vendor recommendations and schedule coordination. Ideal for couples who need guidance and assistance during planning and on their wedding day.', 7000000, 1, '2024-07-29 14:03:09', '2024-07-29 14:03:09');

-- --------------------------------------------------------

--
-- Table structure for table `tb_users`
--

CREATE TABLE `tb_users` (
  `user_id` int(5) NOT NULL,
  `full_name` varchar(80) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(256) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tb_users`
--

INSERT INTO `tb_users` (`user_id`, `full_name`, `phone_number`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
(3, 'Arfin Dwi Octavianto', '08123456789', 'arfin@mail.com', '$1$a0qmQ1fv$5TeDqylCiUmCTeYhLEAt10', 'admin', '2024-06-14 01:15:39', '2024-06-14 01:15:39'),
(9, 'Arfin Dwi Octavianto 3', '08123456787', 'arfin333@mail.com', '$2y$10$gzScjvni9TGBu6RcZ/0FKOU5amJ1V6Wac39iQyDADxIZf3yRGeovq', 'user', '2024-07-29 14:41:38', '2024-07-29 14:41:38');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_order`
--
ALTER TABLE `tb_order`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `service_id` (`service_id`) USING BTREE,
  ADD KEY `user_id` (`user_id`) USING BTREE;

--
-- Indexes for table `tb_services`
--
ALTER TABLE `tb_services`
  ADD PRIMARY KEY (`service_id`);

--
-- Indexes for table `tb_users`
--
ALTER TABLE `tb_users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_order`
--
ALTER TABLE `tb_order`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tb_services`
--
ALTER TABLE `tb_services`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tb_users`
--
ALTER TABLE `tb_users`
  MODIFY `user_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_order`
--
ALTER TABLE `tb_order`
  ADD CONSTRAINT `tb_order_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tb_users` (`user_id`),
  ADD CONSTRAINT `tb_order_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `tb_services` (`service_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
