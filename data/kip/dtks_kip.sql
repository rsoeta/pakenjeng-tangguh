-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 24, 2021 at 02:06 PM
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
-- Table structure for table `dtks_kip`
--

CREATE TABLE `dtks_kip` (
  `dk_id` int(10) UNSIGNED NOT NULL,
  `dk_kks` varchar(100) DEFAULT NULL,
  `dk_kip` varchar(100) DEFAULT NULL,
  `dk_nik` varchar(16) DEFAULT NULL,
  `dk_nama_siswa` varchar(100) DEFAULT NULL,
  `dk_jenkel` int(11) DEFAULT NULL,
  `dk_tmp_lahir` varchar(100) DEFAULT NULL,
  `dk_tgl_lahir` date DEFAULT NULL,
  `dk_alamat` varchar(100) DEFAULT NULL,
  `dk_rt` int(11) DEFAULT NULL,
  `dk_rw` int(11) DEFAULT NULL,
  `dk_desa` varchar(100) DEFAULT NULL,
  `dk_kecamatan` varchar(100) DEFAULT NULL,
  `dk_nama_ibu` varchar(100) DEFAULT NULL,
  `dk_nama_ayah` varchar(100) DEFAULT NULL,
  `dk_nama_sekolah` varchar(100) DEFAULT NULL,
  `dk_jenjang` int(11) DEFAULT NULL,
  `dk_kelas` int(11) DEFAULT NULL,
  `dk_partisipasi` int(11) DEFAULT NULL,
  `dk_created_at` datetime DEFAULT NULL,
  `dk_created_by` varchar(16) DEFAULT NULL,
  `dk_updated_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `dk_updated_by` varchar(16) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `dtks_kip`
--

INSERT INTO `dtks_kip` (`dk_id`, `dk_kks`, `dk_kip`, `dk_nik`, `dk_nama_siswa`, `dk_jenkel`, `dk_tmp_lahir`, `dk_tgl_lahir`, `dk_alamat`, `dk_rt`, `dk_rw`, `dk_desa`, `dk_kecamatan`, `dk_nama_ibu`, `dk_nama_ayah`, `dk_nama_sekolah`, `dk_jenjang`, `dk_kelas`, `dk_partisipasi`, `dk_created_at`, `dk_created_by`, `dk_updated_at`, `dk_updated_by`) VALUES
(2, '37ZAR7', 'P006DG', '3205330303960001', 'ANGGA', 1, 'BANDUNG', '1996-03-03', 'KP. SINGKUR', NULL, NULL, '32.05.33.2009', '32.05.33', 'ETI', 'NULL', 'SMP MUHAMMADIYYAH PAKENJENG', 3, 12, 2, NULL, '3205333107920006', '2021-11-24 01:09:12', NULL),
(3, '37ZCYN', 'P04U98', '3205331710010001', 'ANISA', 2, 'GARUT', '2001-10-17', 'KP CIMAREME', 2, 8, '32.05.33.2006', '32.05.33', 'ENI', 'AMAN', 'SMA MUHAMMADIYYAH PAKENJENG', 4, 10, 2, NULL, '3205333107920005', '2021-11-24 12:11:23', '3205333107920005'),
(4, '37ZC0V', 'P08BNV', '3205330306960001', 'EGI', 1, 'GARUT', '1996-06-03', 'SAWAH LEGA', 2, 3, '32.05.33.2006', '32.05.33', 'OOS', 'OHIM', 'SMA MUHAMMADIYYAH PAKENJENG', 4, 11, 2, NULL, '3205333107920005', '2021-11-24 12:11:34', '3205333107920005'),
(5, '37ZDVQ', 'P0EEWG', '3205330709960001', 'IRPAN', 1, 'GARUT', '1996-09-07', 'KP GUNUNG GADUNG', 2, 8, '32.05.33.2006', '32.05.33', 'MIMIH', 'DADANG', 'SMA MUHAMMADIYYAH PAKENJENG', 4, 12, 2, NULL, '3205333107920005', '2021-11-24 12:11:42', '3205333107920005'),
(6, '37ZHHH', 'P0HF6J', '3205331702000001', 'NURUL', NULL, 'GARUT', '2000-02-17', 'KP CIMIPIR RT 01 RW 10', NULL, NULL, '32.05.33.2009', '32.05.33', 'ROMAYAH', 'PUAD', 'SMP MUHAMMADIYYAH PAKENJENG', 3, 9, 2, NULL, '3205333107920006', '2021-11-24 01:33:40', NULL),
(7, '37ZDQ3', 'P0IPTW', '3205331204030001', 'DEDE MAULANA MUTAKIN', 1, 'BANDUNG', '2003-04-12', 'KP BABAKAN JENTI', 4, 1, '32.05.33.2006', '32.05.33', 'ENUR HAYATI', 'IIB', 'SMA MUHAMMADIYYAH PAKENJENG', 4, 12, 2, '2021-11-23 11:11:21', '3205333107920005', '2021-11-24 12:11:50', '3205333107920005'),
(8, '37ZCBO', 'P0IU2V', '3205332412970001', 'CUCU', 2, 'GARUT', '1997-12-24', 'KP. BOLANG', 1, 9, '32.05.33.2006', '32.05.33', 'TATI', 'ALIT', 'SMA MUHAMMADIYYAH PAKENJENG', 4, 11, 2, '2021-11-23 11:11:56', '3205333107920005', '2021-11-24 12:11:02', '3205333107920005'),
(9, '37ZH1G', 'P0K50G', '3205332903960001', 'DINI SRI LESTARI', 2, 'GARUT', '1996-03-29', 'KP. HALIMPU', 1, 1, '32.05.33.2006', '32.05.33', 'IIS ROSMIAWATI', 'NULL', 'SMA MUHAMMADIYYAH PAKENJENG', 4, 11, 2, '2021-11-24 12:11:46', '3205333107920005', '2021-11-24 13:23:46', NULL),
(10, '37ZC66', 'P0KK6E', '3205332105980001', 'RISWAN', 1, 'GARUT', '1998-05-21', 'KP. SAWAH LEGA', 2, 3, '32.05.33.2006', '32.05.33', 'DEUIS', 'ABAS', 'SMA MUHAMMADIYYAH PAKENJENG', 4, 11, 2, '2021-11-24 12:11:07', '3205333107920005', '2021-11-24 13:32:07', NULL),
(11, '37ZERF', 'P0LRDX', '3205332005990001', 'ASIPA RISPANI', 2, 'GARUT', '1999-05-20', 'KP NANGEWER', 5, 2, '32.05.33.2006', '32.05.33', 'MINTARSIH', 'NULL', 'SMA MUHAMMADIYYAH PAKENJENG', 4, 12, 2, '2021-11-24 12:11:31', '3205333107920005', '2021-11-24 13:40:31', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dtks_kip`
--
ALTER TABLE `dtks_kip`
  ADD PRIMARY KEY (`dk_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dtks_kip`
--
ALTER TABLE `dtks_kip`
  MODIFY `dk_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
