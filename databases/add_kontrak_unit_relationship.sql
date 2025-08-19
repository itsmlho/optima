-- ========================================
-- Script untuk Menambahkan Hubungan Kontrak ke Inventory Unit
-- ========================================
-- 
-- Script ini menambahkan field no_kontrak ke inventory_unit
-- untuk menghubungkan unit dengan kontrak setelah delivery selesai
-- 
-- Alur: Kontrak → SPK → DI → Delivery → Unit Sampai → Status RENTAL + No Kontrak
-- ========================================

USE optima_db;

-- Cek struktur tabel inventory_unit saat ini
SELECT 'Struktur tabel inventory_unit sebelum perubahan:' as info;
DESCRIBE inventory_unit;

-- Tambahkan field no_kontrak ke inventory_unit
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA='optima_db' AND TABLE_NAME='inventory_unit' 
       AND COLUMN_NAME='no_kontrak') = 0,
    'ALTER TABLE inventory_unit ADD COLUMN no_kontrak VARCHAR(100) NULL COMMENT "Nomor kontrak untuk unit yang sudah dalam status RENTAL" AFTER keterangan',
    'SELECT "Field no_kontrak sudah ada" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Tambahkan index untuk performa query
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA='optima_db' AND TABLE_NAME='inventory_unit' 
       AND INDEX_NAME='idx_no_kontrak') = 0,
    'ALTER TABLE inventory_unit ADD INDEX idx_no_kontrak (no_kontrak)',
    'SELECT "Index idx_no_kontrak sudah ada" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Verifikasi perubahan
SELECT 'Struktur tabel inventory_unit setelah perubahan:' as info;
DESCRIBE inventory_unit;

-- Tampilkan unit dengan status RENTAL untuk verifikasi
SELECT 'Unit dengan status RENTAL (status_unit_id = 3):' as info;
SELECT 
    id_inventory_unit,
    no_unit,
    no_kontrak,
    status_unit_id,
    lokasi_unit,
    created_at
FROM inventory_unit 
WHERE status_unit_id = 3
ORDER BY created_at DESC
LIMIT 10;

-- ========================================
-- Contoh Query untuk Update Kontrak setelah Delivery Selesai
-- ========================================
-- 
-- Ketika operational menyelesaikan delivery dan unit sampai di lokasi,
-- jalankan query seperti ini untuk menghubungkan unit dengan kontrak:
-- 
-- UPDATE inventory_unit 
-- SET no_kontrak = 'KTR/2025/001',
--     status_unit_id = 3  -- RENTAL status
-- WHERE id_inventory_unit = [unit_id_dari_spk];
-- 
-- ========================================

SELECT 'Script selesai. Field no_kontrak telah ditambahkan ke inventory_unit.' as status;
