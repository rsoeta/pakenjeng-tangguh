-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 30, 2021 at 09:13 AM
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
-- Table structure for table `tbl_jenkel`
--

CREATE TABLE `tbl_jenkel` (
  `IdJenKel` int(11) NOT NULL,
  `NamaJenKel` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_jenkel`
--

INSERT INTO `tbl_jenkel` (`IdJenKel`, `NamaJenKel`) VALUES
(1, 'Laki-laki'),
(2, 'Perempuan');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_jenkel`
--
ALTER TABLE `tbl_jenkel`
  ADD PRIMARY KEY (`IdJenKel`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_jenkel`
--
ALTER TABLE `tbl_jenkel`
  MODIFY `IdJenKel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
