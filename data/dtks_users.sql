-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 26 Jul 2021 pada 17.05
-- Versi server: 10.2.39-MariaDB-log-cll-lve
-- Versi PHP: 7.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pakenjen_dtks`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `dtks_users`
--

CREATE TABLE `dtks_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nik` char(16) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '0',
  `level` int(1) NOT NULL DEFAULT 3,
  `role_id` int(11) NOT NULL,
  `kode_desa` varchar(128) NOT NULL,
  `nope` varchar(20) NOT NULL,
  `user_image` varchar(255) DEFAULT 'default.png',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data untuk tabel `dtks_users`
--

INSERT INTO `dtks_users` (`id`, `nik`, `username`, `fullname`, `email`, `password`, `status`, `level`, `role_id`, `kode_desa`, `nope`, `user_image`, `created_at`, `updated_at`) VALUES
(1, '3205333107920001', NULL, 'Rian Sutarsa', 'riansutarsa@gmail.com', '$2y$10$8PieUcc3tqzpsZ0MahsdPeCmtNngzLyFZWjXcdsAkiI6WdeUbJspu', '1', 0, 1, '3205332006', '085219784242', 'default.png', '2021-07-26 08:08:25', '2021-07-26 08:08:25'),
(2, '3205333107920002', NULL, 'Ujon', 'ujon@gmail.com', '$2y$10$PalXFu3VazUv9XhZl5CTHOKJkdxKrtO29FN.eYJEpdKccrMDh8tK2', '1', 3, 3, '3205332006', '085219784243', 'default.png', '2021-07-26 08:10:08', '2021-07-26 08:10:08'),
(3, '3205331604750002', NULL, 'Maman Karisman', 'mamankarisman10@gmail.com', '$2y$10$7yq.W9w0IX6AT.jDhXOMa.laEDOB/J0jWa.sSO52NU8MlXL6CEac.', '1', 0, 2, '3205332005', '082318343736', 'default.png', '2021-07-26 02:51:46', '2021-07-26 09:51:46'),
(4, '3205330704780004', NULL, 'NANANG KOSIM', 'azdananang84@gmail.com', '$2y$10$.WvYc1kjt8j.O9iG2A4Ozu5A7twrjPd3k0iM9I1FcUNbipSI13IkW', '1', 0, 2, '3205332009', '081383915687', 'default.png', '2021-07-26 02:51:56', '2021-07-26 09:51:56'),
(6, '3205332904830001', NULL, 'Zaenal Mustofa', 'zaenalmustofa2@gmail.com', '$2y$10$vanedG/.QSQPLEZf6DVQRu6RqmahIBsRSZbwH6/RnwNlUM9INHWNK', '1', 2, 3, '3205332009', '085317716408', 'default.png', '2021-07-26 03:41:39', '2021-07-26 10:41:39'),
(7, '3205332507880001', NULL, 'Yusep A Taufik', 'youseptaufik@gmail.com', '$2y$10$WbH6CTGZQ9LXi6y1di8j.uK7g2HAAGb26Y0fUKsVVrz.zhxYxpPUe', '1', 4, 3, '3205332009', '082126674900', 'default.png', '2021-07-26 03:49:53', '2021-07-26 10:49:53'),
(8, '3205330409740002', NULL, 'Asep gunawan', 'ubrutlembur@gmail.com', '$2y$10$pSrxZktIlRnlXaSBGBYt5eWKEZNhF3fRUsZ9SjYYYDjcd2u/666d6', '1', 6, 3, '3205332009', '082172551884', 'default.png', '2021-07-26 03:52:15', '2021-07-26 10:52:15'),
(9, '3205330706820004', NULL, 'Deden subagja dinata', 'farisgunaone@gmail.com', '$2y$10$gk/jNFfB5WesfqFJMh0erebPozYy667O4/KBLMpH9sW7oUgc76cC.', '1', 9, 3, '3205332009', '081220224966', 'default.png', '2021-07-26 03:59:36', '2021-07-26 10:59:36'),
(10, '3205330909690004', NULL, 'Suarsa', 'suarsapengkolan151@mail.com', '$2y$10$yrv/1cnXfsd/IjXh896Gk.nRIC2GA6.lRUKtepRTMTHUolR0d//7.', '1', 2, 3, '3205332009', '085224944741', 'default.png', '2021-07-26 04:02:59', '2021-07-26 11:02:59'),
(11, '3205331407810004', NULL, 'CUCU SIRAJUDIN', 'cudoxastin@gmail.com', '$2y$10$4UgCsIN2JlqeiiZXEvtYFeB1pO56SzZ55UI1xCRi9tE4apZHXAl3y', '1', 0, 2, '3205332011', '085316665777', 'default.png', '2021-07-26 04:06:30', '2021-07-26 11:06:30'),
(12, '3205330203740002', NULL, 'Supendi', 'Supenditea831@gmail.com', '$2y$10$jXdmgwlD4JR9GAS1N93xleRI5oLIdb9GjQqTDW6VLW4tbKiqKxNiy', '1', 1, 3, '3205332009', '082320079554', 'default.png', '2021-07-26 04:28:28', '2021-07-26 11:28:28'),
(13, '3205331109960003', NULL, 'JAJANG NURJAMAN', 'jajangnurjaman19018@gmail.com', '$2y$10$1F6MW6LB95JmCACwSv0P4uetiEQWCbQpd9uNB/pwzOVzSeXsiJ6Vq', '1', 0, 2, '3205332004', '085721621587', 'default.png', '2021-07-26 04:30:51', '2021-07-26 11:30:51'),
(14, '3205331510770003', NULL, 'Suyud ismail saleh', 'suyudismailsaleh@gmail.com', '$2y$10$LBMPq9tGH5hWoY/Iy1m8YucTMTAx/b9IUXazTUZolKvXMYYKiCoBm', '1', 0, 2, '3205332011', '082318917464', 'default.png', '2021-07-26 04:57:28', '2021-07-26 11:57:28'),
(15, '3205331704880006', NULL, 'agus suganda', 'koswara@pemdespsl.site', '$2y$10$u4h7VKi/J.Wk8VA3oGkL5.1FnxQc1wXBBpnrSBWJD3ultaWoSzQyu', '0', 0, 2, '3205332013', '082118540227', 'default.png', '2021-07-26 07:26:33', '2021-07-26 14:26:33'),
(16, '3205332204770002', NULL, 'asep sudirman', 'desajayamekar4@gmail.com', '$2y$10$0J0MqwlZhQ98bX9rp0o1fOKlXHyAUvFKLraZROS8G/BX8zzTL/DZ.', '0', 0, 2, '3205332013', '085210685789', 'default.png', '2021-07-26 07:35:35', '2021-07-26 14:35:35');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `dtks_users`
--
ALTER TABLE `dtks_users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `dtks_users`
--
ALTER TABLE `dtks_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
