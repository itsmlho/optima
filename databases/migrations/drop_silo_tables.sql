-- =====================================================
-- SQL Script untuk Menghapus Tabel SILO
-- Script untuk rollback/drop tabel jika diperlukan
-- =====================================================

SET FOREIGN_KEY_CHECKS = 0;

-- Hapus foreign key constraints terlebih dahulu
ALTER TABLE `silo_history` DROP FOREIGN KEY IF EXISTS `fk_silo_history_silo`;
ALTER TABLE `silo` DROP FOREIGN KEY IF EXISTS `fk_silo_unit`;

-- Hapus tabel
DROP TABLE IF EXISTS `silo_history`;
DROP TABLE IF EXISTS `silo`;

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- Selesai - Tabel telah dihapus
-- =====================================================

