-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 25 Jul 2021 pada 18.17
-- Versi server: 10.2.39-MariaDB-cll-lve
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
-- Database: `pemdesps_bend`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `ket_verivali`
--

CREATE TABLE `ket_verivali` (
  `id_ketvv` int(11) NOT NULL,
  `jenis_keterangan` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `ket_verivali`
--

INSERT INTO `ket_verivali` (`id_ketvv`, `jenis_keterangan`) VALUES
(1, 'NIK Invalid'),
(2, 'NIK Padan Beda Nama'),
(3, 'NIK Valid'),
(4, 'Di Hapus - Meninggal'),
(5, 'Di Hapus - NIK Sudah Terdaftar'),
(6, 'Tidak Memiliki E-KTP'),
(7, 'Di Hapus - Tidak Ditemukan'),
(8, 'Tidak Memiliki E-KTP');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `ket_verivali`
--
ALTER TABLE `ket_verivali`
  ADD PRIMARY KEY (`id_ketvv`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `ket_verivali`
--
ALTER TABLE `ket_verivali`
  MODIFY `id_ketvv` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
