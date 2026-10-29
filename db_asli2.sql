-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 21, 2026 at 06:07 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_asli2`
--

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `id_barang` int(11) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `satuan` varchar(20) NOT NULL,
  `harga_beli` decimal(15,2) NOT NULL,
  `stok` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id_barang`, `nama_barang`, `kategori`, `satuan`, `harga_beli`, `stok`) VALUES
(2, 'semen', 'semen', 'sak', 70000.00, 41),
(3, 'pasir', 'bahan bangunan', 'truk', 1000000.00, 9),
(5, 'cat tembok nippon paint', 'estetika bangunan', 'ember besar/kecil', 120000.00, 29),
(7, 'Batu bata merah', 'batu', 'per biji', 1000.00, 2060),
(8, 'paku', 'perkakas', 'per kilo gram', 25000.00, 501),
(9, 'Besi beton', 'besi', 'satuan batang', 45000.00, 302),
(10, 'kawat bendrat', 'besi', 'per kilo gram', 22000.00, 450),
(11, 'keramik', 'keramik', 'Per Dus', 65000.00, 120),
(12, 'Genteng tanah liat', 'atap', 'per lembar', 75000.00, 6027),
(13, 'palu', 'perkakas', 'per buah', 35000.00, 50);

-- --------------------------------------------------------

--
-- Table structure for table `detail_pembelian`
--

CREATE TABLE `detail_pembelian` (
  `id_detail` int(11) NOT NULL,
  `id_pembelian` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `harga_beli` decimal(15,2) NOT NULL,
  `subtotal` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_pembelian`
--

INSERT INTO `detail_pembelian` (`id_detail`, `id_pembelian`, `id_barang`, `jumlah`, `harga_beli`, `subtotal`) VALUES
(1, 1, 2, 1, 70000.00, 70000.00),
(2, 2, 3, 1, 1000000.00, 1000000.00),
(3, 3, 7, 60, 1000.00, 60000.00),
(4, 4, 9, 2, 45000.00, 90000.00),
(5, 5, 5, 9, 120000.00, 1080000.00),
(6, 6, 12, 27, 75000.00, 2025000.00),
(7, 7, 8, 1, 25000.00, 25000.00);

-- --------------------------------------------------------

--
-- Table structure for table `detail_penerimaan_barang`
--

CREATE TABLE `detail_penerimaan_barang` (
  `id_detail_penerimaan` int(11) NOT NULL,
  `id_penerimaan` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `jumlah_dipesan` int(11) NOT NULL DEFAULT 0,
  `jumlah_diterima` int(11) NOT NULL DEFAULT 0,
  `jumlah_baik` int(11) NOT NULL DEFAULT 0,
  `jumlah_rusak` int(11) NOT NULL DEFAULT 0,
  `keterangan` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pembelian`
--

CREATE TABLE `pembelian` (
  `id_pembelian` int(11) NOT NULL,
  `tanggal_pembelian` date NOT NULL,
  `id_supplier` int(11) NOT NULL,
  `total_pembelian` decimal(15,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembelian`
--

INSERT INTO `pembelian` (`id_pembelian`, `tanggal_pembelian`, `id_supplier`, `total_pembelian`) VALUES
(1, '2026-09-10', 2, 70000.00),
(2, '2026-09-11', 2, 1000000.00),
(3, '2026-09-16', 2, 60000.00),
(4, '2026-09-12', 2, 90000.00),
(5, '2026-09-13', 2, 1080000.00),
(6, '2026-09-14', 5, 2025000.00),
(7, '2026-09-16', 2, 25000.00);

-- --------------------------------------------------------

--
-- Table structure for table `penerimaan_barang`
--

CREATE TABLE `penerimaan_barang` (
  `id_penerimaan` int(11) NOT NULL,
  `nomor_penerimaan` varchar(30) NOT NULL,
  `id_pembelian` int(11) NOT NULL,
  `tanggal_penerimaan` date NOT NULL,
  `nama_penerima` varchar(100) NOT NULL,
  `status_penerimaan` enum('Menunggu','Diterima Sebagian','Selesai','Ditolak') NOT NULL DEFAULT 'Menunggu',
  `keterangan` text DEFAULT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `id_supplier` int(11) NOT NULL,
  `nama_supplier` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL,
  `no_telepon` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`id_supplier`, `nama_supplier`, `alamat`, `no_telepon`, `email`) VALUES
(2, 'raisyah', 'balek rumah lamo', '090807060504', 'aurahaniya11@gmail.com'),
(4, 'hani', 'rumah baru', '081234567890', 'aurahaniya11@gmail.com'),
(5, 'salwi', 'rumah maskeb', '082154367892', 'aurahaniya11@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `level` enum('admin','pemilik') NOT NULL DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `nama_lengkap`, `level`) VALUES
(2, 'pemilik', '$2y$10$icKEtSztTFYkj23iW6qb2ObqyvcYpDp8FcVWfKDTuomhPcEo6zO6q', 'Rahma', 'pemilik'),
(3, 'owner', '$2y$10$x53H/u7VaEhgwUBPOLOVaOR8KYM7BILtNAGY/lgISRUQEu24H8VJa', 'yaya', 'pemilik'),
(4, 'kasir', '$2y$10$Weq.M9O9Ex12SzCaKWkeEewDPJZx2qd4eRI37pwIUrfxSXe15eA5S', 'ying', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id_barang`);

--
-- Indexes for table `detail_pembelian`
--
ALTER TABLE `detail_pembelian`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `fk_detail_pembelian` (`id_pembelian`),
  ADD KEY `fk_detail_barang` (`id_barang`);

--
-- Indexes for table `detail_penerimaan_barang`
--
ALTER TABLE `detail_penerimaan_barang`
  ADD PRIMARY KEY (`id_detail_penerimaan`),
  ADD KEY `idx_detail_penerimaan` (`id_penerimaan`),
  ADD KEY `idx_detail_penerimaan_barang` (`id_barang`);

--
-- Indexes for table `pembelian`
--
ALTER TABLE `pembelian`
  ADD PRIMARY KEY (`id_pembelian`),
  ADD KEY `fk_pembelian_supplier` (`id_supplier`);

--
-- Indexes for table `penerimaan_barang`
--
ALTER TABLE `penerimaan_barang`
  ADD PRIMARY KEY (`id_penerimaan`),
  ADD UNIQUE KEY `nomor_penerimaan` (`nomor_penerimaan`),
  ADD KEY `idx_penerimaan_tanggal` (`tanggal_penerimaan`),
  ADD KEY `idx_penerimaan_pembelian` (`id_pembelian`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`id_supplier`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `id_barang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `detail_pembelian`
--
ALTER TABLE `detail_pembelian`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `detail_penerimaan_barang`
--
ALTER TABLE `detail_penerimaan_barang`
  MODIFY `id_detail_penerimaan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pembelian`
--
ALTER TABLE `pembelian`
  MODIFY `id_pembelian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `penerimaan_barang`
--
ALTER TABLE `penerimaan_barang`
  MODIFY `id_penerimaan` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `id_supplier` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_pembelian`
--
ALTER TABLE `detail_pembelian`
  ADD CONSTRAINT `fk_detail_barang` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_detail_pembelian` FOREIGN KEY (`id_pembelian`) REFERENCES `pembelian` (`id_pembelian`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `detail_penerimaan_barang`
--
ALTER TABLE `detail_penerimaan_barang`
  ADD CONSTRAINT `fk_detail_penerimaan_header` FOREIGN KEY (`id_penerimaan`) REFERENCES `penerimaan_barang` (`id_penerimaan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pembelian`
--
ALTER TABLE `pembelian`
  ADD CONSTRAINT `fk_pembelian_supplier` FOREIGN KEY (`id_supplier`) REFERENCES `supplier` (`id_supplier`) ON UPDATE CASCADE;

--
-- Constraints for table `penerimaan_barang`
--
ALTER TABLE `penerimaan_barang`
  ADD CONSTRAINT `fk_penerimaan_pembelian` FOREIGN KEY (`id_pembelian`) REFERENCES `pembelian` (`id_pembelian`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
