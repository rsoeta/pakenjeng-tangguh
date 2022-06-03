-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 09, 2022 at 11:32 AM
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
-- Table structure for table `dtks_bansos`
--

CREATE TABLE `dtks_bansos` (
  `Id` int(11) NOT NULL,
  `NamaBansos` varchar(100) DEFAULT NULL,
  `KetBansos` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `dtks_bansos`
--

INSERT INTO `dtks_bansos` (`Id`, `NamaBansos`, `KetBansos`) VALUES
(1, 'PKH', 'PROGRAM KELUARGA HARAPAN'),
(2, 'BPNT', 'BPNT/SEMBAKO'),
(3, 'BST', 'BANTUAN SOSIAL TUNAI'),
(4, 'NONBANSOS', 'NON BANSOS'),
(5, 'PBI', 'PENERIMA BANTUAN IURAN'),
(6, 'DISFSIK\r\n', 'DISABILITAS FISIK\r\n'),
(7, 'DISNTRA\r\n', 'DISABILITAS NETRA\r\n'),
(8, 'DISRNGU\r\n', 'DISABILITAS RUNGU\r\n'),
(9, 'DISWCRA\r\n', 'DISABILITAS WICARA\r\n'),
(10, 'DISRGWC\r\n', 'DISABILITAS RUNGU & WICARA\r\n'),
(11, 'DISNTFK\r\n', 'DISABILITAS NETRA & FISIK\r\n'),
(12, 'DISNRWC\r\n', 'DISABILITAS NETRA, RUNGU & WICARA\r\n'),
(13, 'DISRWFK\r\n', 'DISABILITAS RUNGU, WICARA & FISIK\r\n'),
(14, 'DISRWNF\r\n', 'DISABILTAS RUNGU, WICARA, NETRA & FISIK\r\n'),
(15, 'DISINTL\r\n', 'DISABILITAS INTELEKTUAL\r\n'),
(16, 'DISMOMD\r\n', 'DISABILTAS MENTAL (OMK DAN ODK)\r\n'),
(17, 'DISGDMT\r\n', 'DISABILTAS GANDA/MULTI\r\n');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dtks_bansos`
--
ALTER TABLE `dtks_bansos`
  ADD PRIMARY KEY (`Id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dtks_bansos`
--
ALTER TABLE `dtks_bansos`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
