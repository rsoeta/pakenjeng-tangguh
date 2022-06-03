-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 09, 2021 at 09:03 AM
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
-- Table structure for table `dtks_status`
--

CREATE TABLE `dtks_status` (
  `id_status` int(11) NOT NULL,
  `jenis_status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `dtks_status`
--

INSERT INTO `dtks_status` (`id_status`, `jenis_status`) VALUES
(1, '--Pilih Status--'),
(2, 'Aktif'),
(3, 'Meninggal Dunia'),
(4, 'Ganda'),
(5, 'Pindah'),
(6, 'Tidak Ditemukan'),
(7, 'Menolak');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dtks_status`
--
ALTER TABLE `dtks_status`
  ADD PRIMARY KEY (`id_status`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dtks_status`
--
ALTER TABLE `dtks_status`
  MODIFY `id_status` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
