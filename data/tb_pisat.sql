-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 19 Okt 2021 pada 13.53
-- Versi server: 10.2.40-MariaDB-log-cll-lve
-- Versi PHP: 7.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pemdesps_bend`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_pisat`
--

CREATE TABLE `tb_pisat` (
  `id` int(11) NOT NULL,
  `kode_pisat` varchar(1) NOT NULL,
  `jenis_pisat` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `tb_pisat`
--

INSERT INTO `tb_pisat` (`id`, `kode_pisat`, `jenis_pisat`) VALUES
(1, 'P', 'Pekerja'),
(2, 'S', 'Suami'),
(3, 'I', 'Istri'),
(4, 'A', 'Anak'),
(5, 'T', 'Tambahan');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `tb_pisat`
--
ALTER TABLE `tb_pisat`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `tb_pisat`
--
ALTER TABLE `tb_pisat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
