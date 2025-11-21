-- =====================================================
-- SQL Script untuk Membuat Tabel SILO
-- Sistem Manajemen Surat Izin Layak Operasi
-- =====================================================
-- 
-- Deskripsi:
-- Script ini membuat tabel untuk sistem SILO (Surat Izin Layak Operasi)
-- yang digunakan untuk mengelola perizinan unit alat berat/forklift
--
-- Tabel yang dibuat:
-- 1. silo - Tabel utama untuk data SILO
-- 2. silo_history - Tabel untuk tracking perubahan status
--
-- =====================================================

-- Set character set
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================
-- 1. Tabel: silo
-- =====================================================
DROP TABLE IF EXISTS `silo`;

CREATE TABLE `silo` (
  `id_silo` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `unit_id` int(11) unsigned NOT NULL COMMENT 'FK ke inventory_unit.id_inventory_unit',
  `status` enum(
    'BELUM_ADA',
    'PENGAJUAN_PJK3',
    'TESTING_PJK3',
    'SURAT_KETERANGAN_PJK3',
    'PENGAJUAN_UPTD',
    'PROSES_UPTD',
    'SILO_TERBIT',
    'SILO_EXPIRED'
  ) NOT NULL DEFAULT 'BELUM_ADA',
  
  -- Data Pengajuan ke PJK3
  `tanggal_pengajuan_pjk3` datetime DEFAULT NULL,
  `catatan_pengajuan_pjk3` text DEFAULT NULL,
  
  -- Data Testing PJK3
  `tanggal_testing_pjk3` datetime DEFAULT NULL,
  `hasil_testing_pjk3` text DEFAULT NULL,
  
  -- Data Surat Keterangan PJK3
  `nomor_surat_keterangan_pjk3` varchar(100) DEFAULT NULL,
  `tanggal_surat_keterangan_pjk3` date DEFAULT NULL,
  `file_surat_keterangan_pjk3` varchar(255) DEFAULT NULL COMMENT 'Path ke file PDF/image',
  
  -- Data Pengajuan ke UPTD
  `tanggal_pengajuan_uptd` datetime DEFAULT NULL,
  `catatan_pengajuan_uptd` text DEFAULT NULL,
  
  -- Data Proses UPTD
  `tanggal_proses_uptd` datetime DEFAULT NULL,
  `catatan_proses_uptd` text DEFAULT NULL,
  
  -- Data SILO Terbit
  `nomor_silo` varchar(100) DEFAULT NULL,
  `tanggal_terbit_silo` date DEFAULT NULL,
  `tanggal_expired_silo` date DEFAULT NULL,
  `file_silo` varchar(255) DEFAULT NULL COMMENT 'Path ke file PDF/image',
  
  -- Metadata
  `created_by` int(11) DEFAULT NULL COMMENT 'FK ke users.id',
  `updated_by` int(11) DEFAULT NULL COMMENT 'FK ke users.id',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id_silo`),
  KEY `idx_unit_id` (`unit_id`),
  KEY `idx_status` (`status`),
  KEY `idx_nomor_silo` (`nomor_silo`),
  KEY `idx_tanggal_expired` (`tanggal_expired_silo`),
  CONSTRAINT `fk_silo_unit` FOREIGN KEY (`unit_id`) REFERENCES `inventory_unit` (`id_inventory_unit`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabel untuk data SILO (Surat Izin Layak Operasi)';

-- =====================================================
-- 2. Tabel: silo_history
-- =====================================================
DROP TABLE IF EXISTS `silo_history`;

CREATE TABLE `silo_history` (
  `id_history` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `silo_id` int(11) unsigned NOT NULL,
  `status_lama` varchar(50) DEFAULT NULL,
  `status_baru` varchar(50) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `changed_by` int(11) DEFAULT NULL,
  `changed_at` datetime DEFAULT CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id_history`),
  KEY `idx_silo_id` (`silo_id`),
  CONSTRAINT `fk_silo_history_silo` FOREIGN KEY (`silo_id`) REFERENCES `silo` (`id_silo`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabel untuk tracking perubahan status SILO';

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- Selesai
-- =====================================================
-- 
-- Tabel berhasil dibuat!
-- 
-- Untuk verifikasi, jalankan query berikut:
-- SHOW TABLES LIKE 'silo%';
-- DESCRIBE silo;
-- DESCRIBE silo_history;
--
-- =====================================================

