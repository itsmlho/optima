-- ============================================================================
-- PRODUCTION DATABASE MIGRATION - SIMPLE VERSION
-- ============================================================================
-- Database: u138256737_optima_db (Production)
-- Date: February 14, 2026
-- ============================================================================
-- LANGKAH-LANGKAH:
-- 1. BACKUP dulu: Export database via phpMyAdmin
-- 2. Jalankan script ini via phpMyAdmin SQL tab
-- 3. Cek hasilnya dengan query verifikasi di bawah
-- ============================================================================

USE u138256737_optima_db;

-- ============================================================================
-- CEK TABLE EXISTS DULU
-- ============================================================================

-- Cek table inventory_unit exists
SELECT 
    COUNT(*) as inventory_unit_exists,
    'Table inventory_unit ditemukan' as status
FROM INFORMATION_SCHEMA.TABLES
WHERE TABLE_SCHEMA = 'u138256737_optima_db'
  AND TABLE_NAME = 'inventory_unit';

-- Cek table inventory_attachment exists  
SELECT 
    COUNT(*) as inventory_attachment_exists,
    'Table inventory_attachment ditemukan' as status
FROM INFORMATION_SCHEMA.TABLES
WHERE TABLE_SCHEMA = 'u138256737_optima_db'
  AND TABLE_NAME = 'inventory_attachment';

-- ============================================================================
-- BACKUP TABLE (Opsional tapi Disarankan)
-- ============================================================================

DROP TABLE IF EXISTS inventory_unit_backup_20260214;
CREATE TABLE inventory_unit_backup_20260214 AS SELECT * FROM inventory_unit;

DROP TABLE IF EXISTS inventory_attachment_backup_20260214;
CREATE TABLE inventory_attachment_backup_20260214 AS SELECT * FROM inventory_attachment;

SELECT 'Backup tables created' AS status;

-- ============================================================================
-- PART 1: TAMBAH KOLOM DI inventory_unit
-- ============================================================================

ALTER TABLE inventory_unit 
ADD COLUMN IF NOT EXISTS on_hire_date DATE DEFAULT NULL COMMENT 'Tanggal unit disewakan' AFTER kontrak_id,
ADD COLUMN IF NOT EXISTS off_hire_date DATE DEFAULT NULL COMMENT 'Tanggal unit dikembalikan' AFTER on_hire_date,
ADD COLUMN IF NOT EXISTS rate_changed_at DATETIME DEFAULT NULL COMMENT 'Terakhir ubah harga' AFTER harga_sewa_bulanan;

SELECT '✓ inventory_unit: 3 kolom ditambahkan' AS status;

-- ============================================================================
-- PART 2: CLEANUP inventory_attachment
-- ============================================================================

-- Hapus duplicates dulu (keep yang paling lama)
DELETE t1 FROM inventory_attachment t1
INNER JOIN inventory_attachment t2 
WHERE t1.id_inventory_attachment > t2.id_inventory_attachment
  AND t1.id_inventory_unit = t2.id_inventory_unit
  AND t1.tipe_item = t2.tipe_item
  AND (
    (t1.tipe_item = 'BATTERY' AND t1.baterai_id = t2.baterai_id AND t1.baterai_id IS NOT NULL) OR
    (t1.tipe_item = 'CHARGER' AND t1.charger_id = t2.charger_id AND t1.charger_id IS NOT NULL) OR
    (t1.tipe_item = 'ATTACHMENT' AND t1.attachment_id = t2.attachment_id AND t1.attachment_id IS NOT NULL)
  );

SELECT CONCAT('✓ Duplicates dihapus: ', ROW_COUNT(), ' rows') AS status;

-- Hapus foreign keys lama (jika ada)
SET FOREIGN_KEY_CHECKS = 0;

ALTER TABLE inventory_attachment DROP FOREIGN KEY IF EXISTS fk_inventory_attachment_status_unit;
ALTER TABLE inventory_attachment DROP FOREIGN KEY IF EXISTS fk_inventory_attachment_status_attachment;

SET FOREIGN_KEY_CHECKS = 1;

-- Hapus kolom lama
ALTER TABLE inventory_attachment 
DROP COLUMN IF EXISTS status_unit,
DROP COLUMN IF EXISTS status_attachment_id;

SELECT '✓ inventory_attachment: Kolom lama dihapus' AS status;

-- Tambah index untuk performance
ALTER TABLE inventory_attachment 
ADD INDEX IF NOT EXISTS idx_inventory_attachment_status (attachment_status);

-- Tambah UNIQUE constraints untuk prevent duplicate
ALTER TABLE inventory_attachment 
ADD UNIQUE INDEX IF NOT EXISTS uk_unit_attachment (id_inventory_unit, attachment_id),
ADD UNIQUE INDEX IF NOT EXISTS uk_unit_charger (id_inventory_unit, charger_id),
ADD UNIQUE INDEX IF NOT EXISTS uk_unit_battery (id_inventory_unit, baterai_id);

SELECT '✓ inventory_attachment: Index dan UNIQUE constraints ditambahkan' AS status;

-- ============================================================================
-- VERIFIKASI HASIL
-- ============================================================================

SELECT '========================================' AS separator;
SELECT 'VERIFIKASI MIGRATION:' AS title;
SELECT '========================================' AS separator;

-- Cek kolom baru di inventory_unit
SELECT 
    CONCAT('inventory_unit: ', COUNT(*), ' of 3 columns added') AS result
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'u138256737_optima_db'
  AND TABLE_NAME = 'inventory_unit'
  AND COLUMN_NAME IN ('on_hire_date', 'off_hire_date', 'rate_changed_at');

-- Cek kolom lama di inventory_attachment (harus 0)
SELECT 
    CONCAT('inventory_attachment: ', COUNT(*), ' old columns remaining (should be 0)') AS result
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'u138256737_optima_db'
  AND TABLE_NAME = 'inventory_attachment'
  AND COLUMN_NAME IN ('status_unit', 'status_attachment_id');

-- Cek indexes baru
SELECT 
    CONCAT('inventory_attachment: ', COUNT(DISTINCT INDEX_NAME), ' of 4 indexes added') AS result
FROM INFORMATION_SCHEMA.STATISTICS
WHERE TABLE_SCHEMA = 'u138256737_optima_db'
  AND TABLE_NAME = 'inventory_attachment'
  AND INDEX_NAME IN ('idx_inventory_attachment_status', 'uk_unit_attachment', 'uk_unit_charger', 'uk_unit_battery');

-- Row counts (dengan error handling)
SELECT 
    'inventory_unit' AS tabel, 
    (SELECT COUNT(*) FROM inventory_unit) AS jumlah_rows,
    'OK' as status
FROM DUAL
UNION ALL
SELECT 
    'inventory_attachment', 
    (SELECT COUNT(*) FROM inventory_attachment),
    'OK'
FROM DUAL
UNION ALL
SELECT 
    'inventory_unit_backup', 
    (SELECT COUNT(*) FROM inventory_unit_backup_20260214),
    'BACKUP'
FROM DUAL
UNION ALL
SELECT 
    'inventory_attachment_backup', 
    (SELECT COUNT(*) FROM inventory_attachment_backup_20260214),
    'BACKUP'
FROM DUAL;

SELECT '========================================' AS separator;
SELECT 'MIGRATION SELESAI!' AS status;
SELECT 'Test aplikasi di: https://optima.sml.co.id/warehouse/inventory/invent_unit' AS next_step;
SELECT '========================================' AS separator;

-- ============================================================================
-- ROLLBACK SCRIPT (Jika Ada Masalah)
-- ============================================================================
/*
-- Jalankan ini jika perlu rollback:

USE u138256737_optima_db;

-- Restore dari backup
DROP TABLE IF EXISTS inventory_unit;
DROP TABLE IF EXISTS inventory_attachment;

CREATE TABLE inventory_unit AS SELECT * FROM inventory_unit_backup_20260214;
CREATE TABLE inventory_attachment AS SELECT * FROM inventory_attachment_backup_20260214;

-- Hapus backup tables setelah 30 hari:
-- DROP TABLE inventory_unit_backup_20260214;
-- DROP TABLE inventory_attachment_backup_20260214;
*/
