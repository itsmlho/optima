-- =====================================================
-- CREATE TABLE po_verification
-- =====================================================
-- Tabel untuk tracking detail discrepancy verifikasi PO
-- =====================================================

CREATE TABLE IF NOT EXISTS `po_verification` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `po_type` ENUM('unit', 'attachment', 'sparepart') NOT NULL COMMENT 'Tipe PO item yang diverifikasi',
  `source_id` INT(11) UNSIGNED NOT NULL COMMENT 'ID dari po_units/po_attachment/po_sparepart_items',
  `po_id` INT(11) UNSIGNED NOT NULL COMMENT 'ID Purchase Order',
  `field_name` VARCHAR(100) NOT NULL COMMENT 'Nama field yang tidak sesuai (e.g., sn_unit, merk, model)',
  `database_value` TEXT NULL COMMENT 'Nilai dari database/PO',
  `real_value` TEXT NULL COMMENT 'Nilai real dari lapangan',
  `discrepancy_type` ENUM('Minor', 'Major', 'Missing') NOT NULL DEFAULT 'Minor' COMMENT 'Tipe ketidaksesuaian',
  `status_verifikasi` ENUM('Sesuai', 'Tidak Sesuai') NOT NULL COMMENT 'Status verifikasi item ini',
  `catatan` TEXT NULL COMMENT 'Catatan tambahan',
  `verified_by` INT(11) UNSIGNED NULL COMMENT 'User ID yang melakukan verifikasi',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_po_type_source` (`po_type`, `source_id`),
  INDEX `idx_po_id` (`po_id`),
  INDEX `idx_status` (`status_verifikasi`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tracking detail discrepancy verifikasi PO';

