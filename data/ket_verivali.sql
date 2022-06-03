-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 11, 2021 at 12:25 AM
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
-- Table structure for table `ket_verivali`
--

CREATE TABLE `ket_verivali` (
  `id_ketvv` int(11) NOT NULL,
  `jenis_keterangan` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ket_verivali`
--

INSERT INTO `ket_verivali` (`id_ketvv`, `jenis_keterangan`) VALUES
(1, 'NIK Tidak Valid'),
(2, 'NIK Valid Nama Beda'),
(3, 'NIK Valid');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ket_verivali`
--
ALTER TABLE `ket_verivali`
  ADD PRIMARY KEY (`id_ketvv`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ket_verivali`
--
ALTER TABLE `ket_verivali`
  MODIFY `id_ketvv` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
