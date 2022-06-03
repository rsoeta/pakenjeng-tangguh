-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 29, 2021 at 02:12 PM
-- Server version: 5.7.24
-- PHP Version: 7.3.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_bend`
--

-- --------------------------------------------------------

--
-- Table structure for table `pbb_users`
--

CREATE TABLE `pbb_users` (
  `id` int(11) UNSIGNED NOT NULL,
  `nik` char(16) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(30) DEFAULT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `user_image` varchar(255) NOT NULL DEFAULT 'default.png',
  `password` varchar(255) NOT NULL,
  `level` tinyint(1) NOT NULL DEFAULT '3',
  `reset_hash` varchar(255) DEFAULT NULL,
  `reset_at` datetime DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `activate_hash` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `status_message` varchar(255) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `force_pass_reset` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pbb_users`
--

INSERT INTO `pbb_users` (`id`, `nik`, `email`, `username`, `fullname`, `user_image`, `password`, `level`, `reset_hash`, `reset_at`, `reset_expires`, `activate_hash`, `status`, `status_message`, `active`, `force_pass_reset`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, '3205333107920001', 'riansutarsa@gmail.com', 'riansutarsa', 'Rian Sutarsa', 'rian.jpg', 'admin123', 1, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL),
(3, '3205336512950001', 'rahmat@gmail.com', 'dede_rahmat', 'Dede Rahmat', 'dede_rahmat.jpg', '123456', 2, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL),
(4, '3205333107920001', 'somantri@gmail.com', 'a_somantri', 'A. Somantri', 'a_somantri.jpg', '123456', 2, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '2021-05-06 13:46:20', '2021-05-06 13:46:20', NULL),
(5, '3205333107920001', 'agussudrajat@gmail.com', 'agus_sudrajat', 'Agus Sudrajat', 'agus_sudrajat.jpg', '123456', 2, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '2021-05-06 23:10:52', '2021-05-06 23:10:52', NULL),
(6, '3205336512950001', 'agentia@gmail.com', 'agentia', NULL, 'default.png', '123456', 3, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '2021-05-06 23:12:36', '2021-05-06 23:12:36', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pbb_users`
--
ALTER TABLE `pbb_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pbb_users`
--
ALTER TABLE `pbb_users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
