-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 01, 2021 at 03:15 PM
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
-- Database: `db_pakdtks2`
--

-- --------------------------------------------------------

--
-- Table structure for table `dtks_verivali_pbi`
--

CREATE TABLE `dtks_verivali_pbi` (
  `vp_id` int(11) NOT NULL,
  `vp_keterangan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `dtks_verivali_pbi`
--

INSERT INTO `dtks_verivali_pbi` (`vp_id`, `vp_keterangan`) VALUES
(1, 'Berhasil diusulkan'),
(2, 'Nik Tidak Ditemukan!'),
(3, 'NIK Ditemukan Dan Nama Ibu Kandung Berbeda Dengan Dukcapil');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dtks_verivali_pbi`
--
ALTER TABLE `dtks_verivali_pbi`
  ADD PRIMARY KEY (`vp_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dtks_verivali_pbi`
--
ALTER TABLE `dtks_verivali_pbi`
  MODIFY `vp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
