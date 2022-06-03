-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 30, 2021 at 09:08 AM
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
-- Table structure for table `tbl_rt`
--

CREATE TABLE `tbl_rt` (
  `rt_id` int(11) NOT NULL,
  `id_rt` varchar(50) DEFAULT NULL,
  `id_rw` varchar(50) DEFAULT NULL,
  `id_dusun` varchar(50) DEFAULT NULL,
  `id_wil` varchar(50) DEFAULT NULL,
  `nama_rt` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tbl_rt`
--

INSERT INTO `tbl_rt` (`rt_id`, `id_rt`, `id_rw`, `id_dusun`, `id_wil`, `nama_rt`) VALUES
(1, '001', '001', '1', '1001001', 'AMAT'),
(2, '002', '001', '1', '1001002', 'ALIMIN'),
(3, '003', '001', '1', '1001003', 'RAHMAT HANAPI'),
(4, '004', '001', '1', '1001004', 'ENUNG'),
(5, '005', '001', '1', '1001005', 'ROHIM'),
(7, '007', '001', '1', '1001007', 'RIPKI RISWANDI'),
(8, '008', '001', '1', '1001008', 'TETENG JUANDI'),
(9, '009', '001', '1', '1001009', 'ADE LILAH'),
(10, '010', '001', '1', '1001010', 'ASEP HARJA'),
(11, '001', '003', '1', '1003001', 'UJON'),
(12, '002', '003', '1', '1003002', 'ALIT SOLEHADIN'),
(13, '003', '003', '1', '1003003', 'JAJANG SURYANA'),
(14, '004', '003', '1', '1003004', 'HERI'),
(15, '005', '003', '1', '1003005', 'IIP SAEPULOH'),
(16, '001', '002', '2', '2002001', 'ADE SUPRIATNA'),
(17, '002', '002', '2', '2002002', 'SARIP SAEPUL ROHMAN'),
(18, '003', '002', '2', '2002003', 'IWAN SETIAWAN'),
(19, '004', '002', '2', '2002004', 'ADIS'),
(20, '005', '002', '2', '2002005', 'AYI HERMAWAN'),
(21, '006', '002', '2', '2002006', 'NANAN'),
(22, '001', '005', '2', '2005001', 'JAJANG AWAL'),
(23, '002', '005', '2', '2005002', 'AIM'),
(24, '003', '005', '2', '2005003', 'DADIH'),
(25, '004', '005', '2', '2005004', 'RISWAN ABDUL GANI'),
(26, '001', '004', '3', '3004001', 'RODI'),
(27, '002', '004', '3', '3004002', 'IRWAN HERMAWAN'),
(28, '003', '004', '3', '3004003', 'ASEP NURJAMAN'),
(29, '004', '004', '3', '3004004', 'SEHAB'),
(30, '001', '006', '3', '3006001', 'HOLID'),
(31, '002', '006', '3', '3006002', 'YANA RUDIANA'),
(32, '003', '006', '3', '3006003', 'ABDUL WAHID'),
(33, '004', '006', '3', '3006004', 'MANSUR'),
(34, '005', '006', '3', '3006005', 'WAWAN JANA HERMAWAN'),
(35, '006', '006', '3', '3006006', 'JAJANG'),
(36, '007', '006', '3', '3006007', 'RODIN'),
(37, '001', '007', '3', '3007001', 'ANDA'),
(38, '002', '007', '3', '3007002', 'ROBI'),
(39, '003', '007', '3', '3007003', 'SOLEHADIN'),
(40, '004', '007', '3', '3007004', 'TONI SUTRISNO'),
(41, '005', '007', '3', '3007005', 'SARIPUDIN'),
(42, '0', '0', '3', '300', '-');

--
-- Triggers `tbl_rt`
--
DELIMITER $$
CREATE TRIGGER `insert_trigger` BEFORE INSERT ON `tbl_rt` FOR EACH ROW SET new.id_wil = CONCAT(new.id_dusun, new.id_rw, new.id_rt)
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_trigger` BEFORE UPDATE ON `tbl_rt` FOR EACH ROW SET new.id_wil = CONCAT(new.id_dusun, new.id_rw, new.id_rt)
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_rt`
--
ALTER TABLE `tbl_rt`
  ADD PRIMARY KEY (`rt_id`),
  ADD KEY `id_rw` (`id_rw`),
  ADD KEY `id_dusun` (`id_dusun`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_rt`
--
ALTER TABLE `tbl_rt`
  MODIFY `rt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
