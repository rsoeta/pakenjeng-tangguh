-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 22, 2021 at 01:35 PM
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
-- Table structure for table `dtks_users`
--

CREATE TABLE `dtks_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nik` char(16) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `status` int(1) DEFAULT NULL,
  `level` varchar(100) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `kode_desa` varchar(128) DEFAULT NULL,
  `nope` varchar(20) DEFAULT NULL,
  `user_image` varchar(255) DEFAULT 'default.png',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `dtks_users`
--

INSERT INTO `dtks_users` (`id`, `nik`, `username`, `fullname`, `email`, `password`, `status`, `level`, `role_id`, `kode_desa`, `nope`, `user_image`, `created_at`, `updated_at`) VALUES
(1, '3205333107920001', NULL, 'Rian Sutarsa', 'riansutarsa@gmail.com', '$2y$10$5BesB5cSob2GnyDYIlBMN.flG1qXvwgXQOyucPEZP1DRAdUdAjdV2', 1, '', 2, '32.05.33.2006', '085219784242', 'default.png', '2021-07-26 08:08:25', '2021-07-26 08:08:25'),
(2, '3205333107920002', NULL, 'Ujon', 'ujon@gmail.com', '$2y$10$PalXFu3VazUv9XhZl5CTHOKJkdxKrtO29FN.eYJEpdKccrMDh8tK2', 1, '003', 3, '32.05.33.2006', '085219784243', 'default.png', '2021-07-26 08:10:08', '2021-07-26 08:10:08'),
(3, '3205331604750002', NULL, 'Maman Karisman', 'mamankarisman10@gmail.com', '$2y$10$7yq.W9w0IX6AT.jDhXOMa.laEDOB/J0jWa.sSO52NU8MlXL6CEac.', 1, '0', 2, '32.05.33.2005', '082318343736', 'default.png', '2021-07-26 02:51:46', '2021-07-26 09:51:46'),
(4, '3205330704780004', NULL, 'NANANG KOSIM', 'azdananang84@gmail.com', '$2y$10$.WvYc1kjt8j.O9iG2A4Ozu5A7twrjPd3k0iM9I1FcUNbipSI13IkW', 0, '0', 2, '32.05.33.2009', '081383915687', 'default.png', '2021-07-26 02:51:56', '2021-07-26 09:51:56'),
(6, '3205332904830001', NULL, 'Zaenal Mustofa', 'zaenalmustofa2@gmail.com', '$2y$10$vanedG/.QSQPLEZf6DVQRu6RqmahIBsRSZbwH6/RnwNlUM9INHWNK', 0, '002', 3, '32.05.33.2009', '085317716408', 'default.png', '2021-07-26 03:41:39', '2021-07-26 10:41:39'),
(7, '3205332507880001', NULL, 'Yusep A Taufik', 'youseptaufik@gmail.com', '$2y$10$WbH6CTGZQ9LXi6y1di8j.uK7g2HAAGb26Y0fUKsVVrz.zhxYxpPUe', 0, '004', 3, '32.05.33.2009', '082126674900', 'default.png', '2021-07-26 03:49:53', '2021-07-26 10:49:53'),
(8, '3205330409740002', NULL, 'Asep gunawan', 'ubrutlembur@gmail.com', '$2y$10$pSrxZktIlRnlXaSBGBYt5eWKEZNhF3fRUsZ9SjYYYDjcd2u/666d6', 0, '006', 3, '32.05.33.2009', '082172551884', 'default.png', '2021-07-26 03:52:15', '2021-07-26 10:52:15'),
(9, '3205330706820004', NULL, 'Deden subagja dinata', 'farisgunaone@gmail.com', '$2y$10$gk/jNFfB5WesfqFJMh0erebPozYy667O4/KBLMpH9sW7oUgc76cC.', 0, '009', 3, '32.05.33.2009', '081220224966', 'default.png', '2021-07-26 03:59:36', '2021-07-26 10:59:36'),
(10, '3205330909690004', NULL, 'Suarsa', 'suarsapengkolan151@mail.com', '$2y$10$yrv/1cnXfsd/IjXh896Gk.nRIC2GA6.lRUKtepRTMTHUolR0d//7.', 0, '002', 3, '32.05.33.2009', '085224944741', 'default.png', '2021-07-26 04:02:59', '2021-07-26 11:02:59'),
(11, '3205331407810004', NULL, 'CUCU SIRAJUDIN', 'cudoxastin@gmail.com', '$2y$10$4UgCsIN2JlqeiiZXEvtYFeB1pO56SzZ55UI1xCRi9tE4apZHXAl3y', 0, '0', 2, '32.05.33.2011', '085316665777', 'default.png', '2021-07-26 04:06:30', '2021-07-26 11:06:30'),
(12, '3205330203740002', NULL, 'Supendi', 'Supenditea831@gmail.com', '$2y$10$jXdmgwlD4JR9GAS1N93xleRI5oLIdb9GjQqTDW6VLW4tbKiqKxNiy', 0, '001', 3, '32.05.33.2009', '082320079554', 'default.png', '2021-07-26 04:28:28', '2021-07-26 11:28:28'),
(13, '3205331109960003', NULL, 'JAJANG NURJAMAN', 'jajangnurjaman19018@gmail.com', '$2y$10$1F6MW6LB95JmCACwSv0P4uetiEQWCbQpd9uNB/pwzOVzSeXsiJ6Vq', 0, '0', 2, '32.05.33.2004', '085721621587', 'default.png', '2021-07-26 04:30:51', '2021-07-26 11:30:51'),
(14, '3205331510770003', NULL, 'Suyud ismail saleh', 'suyudismailsaleh@gmail.com', '$2y$10$LBMPq9tGH5hWoY/Iy1m8YucTMTAx/b9IUXazTUZolKvXMYYKiCoBm', 0, '0', 2, '32.05.33.2011', '082318917464', 'default.png', '2021-07-26 04:57:28', '2021-07-26 11:57:28'),
(15, '3205331704880006', NULL, 'agus suganda', 'koswara@pemdespsl.site', '$2y$10$u4h7VKi/J.Wk8VA3oGkL5.1FnxQc1wXBBpnrSBWJD3ultaWoSzQyu', 0, '0', 2, '32.05.33.2013', '082118540227', 'default.png', '2021-07-26 07:26:33', '2021-07-26 14:26:33'),
(16, '3205332204770002', NULL, 'asep sudirman', 'desajayamekar4@gmail.com', '$2y$10$0J0MqwlZhQ98bX9rp0o1fOKlXHyAUvFKLraZROS8G/BX8zzTL/DZ.', 0, '0', 2, '32.05.33.2013', '085210685789', 'default.png', '2021-07-26 07:35:35', '2021-07-26 14:35:35'),
(17, '3205330512960001', NULL, 'HARIS MULYANA', 'haris.maulana084@gmail.com', '$2y$10$Xm93QslSTufxSsyUDOhYZe4m1l/KvHsaAgQouZKGHVlMvmKLwPp9W', 1, '0', 2, '32.05.33.2010', '085210539624', 'default.png', '2021-07-26 12:53:23', '2021-07-26 19:53:23'),
(18, '3205332510910002', NULL, 'HIDAYAT SUGIHARTO', 'hidayatsugih86a@gmail.com', '$2y$10$o7TO8vSzsCJfDpobuPrXmeCgBJtTCqAMk9Eqi8RF.j3AERg.HtrFi', 1, '0', 2, '32.05.33.2010', '083145541612', 'default.png', '2021-07-26 13:11:25', '2021-07-26 20:11:25'),
(19, '3205332010760002', NULL, 'Benni Yandiana', 'ben.pgw1976@gmail.com', '$2y$10$d728OVmNKU3eCxBRJ8Nnuuf/MlKVLwxSaiXkoar9AgnLIy0kKOC1C', 1, '007', 1, '32.05.33.2008', '085220841238', 'default.png', '2021-07-27 01:04:38', '2021-07-27 08:04:38'),
(20, '3205335701940001', NULL, 'agus suganda', 'susannavis927@gmail.com', '$2y$10$RWSEC5Tk76h3AlrlfjxfWeJktK/mfc5qEtr9AjQt/uMfiRZF0WeJO', 1, '0', 2, '32.05.33.2013', '083105189012', 'default.png', '2021-07-27 02:04:00', '2021-07-27 09:04:00'),
(21, '3205331907770003', NULL, 'Erik hendarsyah', 'mhmdgnwn7@gmail.com', '$2y$10$v0whKo5jxsi.6HTm8sV11OcXVVOivrkH7KlRyXbffTqi85porCiNK', 0, '012', 3, '32.05.33.2008', '085223962478', 'default.png', '2021-07-30 13:19:19', '2021-07-30 20:19:19'),
(22, '3205331203810005', NULL, 'Dasep gumilar', 'dasepgumilar5@gmail.com', '$2y$10$i4wCLGUPwODiEicZ81DHgeHY/zVd8HJKgHyL014RbYDzf9UIFS6yu', 0, '013', 3, '32.05.33.2008', '085353009340', 'default.png', '2021-07-30 14:00:16', '2021-07-30 21:00:16'),
(23, '3205331106890007', NULL, 'Engkos kosasih', 'Khosskosasih@gmail.com', '$2y$10$gw4Tfx7rdh8cA.atAY65FO1Jgtdheznh.r5zEmebKhbaqTClPpBnS', 0, '005', 3, '32.05.33.2008', '085316019534', 'default.png', '2021-07-30 14:03:25', '2021-07-30 21:03:25'),
(24, '3205224410870003', NULL, 'DIAN HERDIANA', 'herdianapramuja@gmail.com', '$2y$10$c9s/vkU8KkKO5zl6FDl20eGDCsMkEc0ygZyXHEpSA3my5bGVayAI2', 0, '0', 2, '32.05.33.2008', '082126600223', 'default.png', '2021-07-30 14:13:35', '2021-07-30 21:13:35'),
(25, '3205275405870003', NULL, 'Ari Usti Utami', 'ariustiutami87@gmel.com', '$2y$10$AtWbugCErrj3KKzObLdX0eE50aZOvk6ahUDO39WsiHrsVN6c2Gu9C', 0, '001', 3, '32.05.33.2008', '085861070830', 'default.png', '2021-07-30 14:15:13', '2021-07-30 21:15:13'),
(26, '3205330103990002', NULL, 'Rian Sutarsa', 'riansutarsa@outlook.com', '$2y$10$i9/Pcf8.tjc4Ga6zJOw.3uvMXDx06dxygTq7jlNnWeN78qzpE3us.', 0, '0', 2, '32.05.33.2008', '085219784244', 'default.png', '2021-07-30 14:15:20', '2021-07-30 21:15:20'),
(27, '3205332608770001', NULL, 'TATANG WAHYUDIN', 'ayonbudiman@gmail.com', '$2y$10$jjh5WkdOcBLJJcdu5XWf5eIVlr8gRnSXe.WmNsNVjpcHpj047cbaW', 0, '003', 3, '32.05.33.2008', '085318024555', 'default.png', '2021-07-30 14:32:16', '2021-07-30 21:32:16'),
(28, '3205331508910004', NULL, 'Dede rahmat', 'rahmatdede734@gmail.com', '$2y$10$mn/bxV0ykP31zfAuS.oXDOaCWDFZ21vR3Pkx1rGfGDA4dICR/sp4y', 0, '004', 3, '32.05.33.2008', '081383915696', 'default.png', '2021-07-30 14:43:05', '2021-07-30 21:43:05'),
(29, '3205330505850017', NULL, 'Ajat yudiansyah', 'yudiansyahajat84@gmail.com', '$2y$10$p3wZ9SQ3bI1/wX8yd.Hhx.TUn2oFxFMfYzQ5ZdMY5S78uOmNDRlOa', 0, '009', 3, '32.05.33.2008', '085323098934', 'default.png', '2021-07-30 15:16:07', '2021-07-30 22:16:07'),
(36, '3205334904930006', NULL, 'RIAN SUTARSA', 'riansutarsa@gehu.com', '$2y$10$rgVZfxa3dlzP.liL5.sgduSIhC63aqYMR/.7649kMI0Jaayw8zxCO', 0, '001', 3, '32.05.33.2008', '085219787777', 'default.png', '2021-10-21 19:21:04', '2021-10-22 07:21:04');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dtks_users`
--
ALTER TABLE `dtks_users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dtks_users`
--
ALTER TABLE `dtks_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
