-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 24, 2021 at 02:05 PM
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
-- Table structure for table `tb_sekolah_jenjang`
--

CREATE TABLE `tb_sekolah_jenjang` (
  `sj_id` int(11) NOT NULL,
  `sj_nama` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tb_sekolah_jenjang`
--

INSERT INTO `tb_sekolah_jenjang` (`sj_id`, `sj_nama`) VALUES
(0, 'Blm ditentukan'),
(1, 'Belum/Tidak Pernah Sekolah'),
(2, 'SD/Sederajat'),
(3, 'SLTP/Sederajat'),
(4, 'SLTA/Sederajat'),
(5, 'Kuliah');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_sekolah_jenjang`
--
ALTER TABLE `tb_sekolah_jenjang`
  ADD PRIMARY KEY (`sj_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
