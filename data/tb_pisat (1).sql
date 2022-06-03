-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 22, 2021 at 02:10 PM
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
-- Table structure for table `tb_pisat`
--

CREATE TABLE `tb_pisat` (
  `id` int(11) NOT NULL,
  `kode_pisat` varchar(1) NOT NULL,
  `jenis_pisat` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tb_pisat`
--

INSERT INTO `tb_pisat` (`id`, `kode_pisat`, `jenis_pisat`) VALUES
(1, 'P', 'PEKERJA'),
(2, 'S', 'SUAMI'),
(3, 'I', 'ISTRI'),
(4, 'A', 'ANAK'),
(5, 'T', 'TAMBAHAN');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_pisat`
--
ALTER TABLE `tb_pisat`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_pisat`
--
ALTER TABLE `tb_pisat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
