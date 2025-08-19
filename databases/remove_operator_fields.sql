-- ========================================
-- Script untuk Menghapus Field Operator dari Tabel delivery_instructions
-- ========================================

USE optima_db;

-- Cek field operator yang ada saat ini
SELECT 'Field operator yang akan dihapus:' as info;
SELECT COLUMN_NAME, DATA_TYPE 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'optima_db' 
  AND TABLE_NAME = 'delivery_instructions' 
  AND COLUMN_NAME LIKE '%operator%'
ORDER BY COLUMN_NAME;

-- Hapus field perencanaan_operator
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA='optima_db' AND TABLE_NAME='delivery_instructions' 
       AND COLUMN_NAME='perencanaan_operator') > 0,
    'ALTER TABLE delivery_instructions DROP COLUMN perencanaan_operator',
    'SELECT "Field perencanaan_operator tidak ada" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Hapus field berangkat_operator
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA='optima_db' AND TABLE_NAME='delivery_instructions' 
       AND COLUMN_NAME='berangkat_operator') > 0,
    'ALTER TABLE delivery_instructions DROP COLUMN berangkat_operator',
    'SELECT "Field berangkat_operator tidak ada" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Hapus field sampai_operator
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA='optima_db' AND TABLE_NAME='delivery_instructions' 
       AND COLUMN_NAME='sampai_operator') > 0,
    'ALTER TABLE delivery_instructions DROP COLUMN sampai_operator',
    'SELECT "Field sampai_operator tidak ada" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Verifikasi hasil
SELECT 'Struktur tabel setelah penghapusan field operator:' as info;
DESCRIBE delivery_instructions;

SELECT 'Field operator yang tersisa (seharusnya kosong):' as info;
SELECT COLUMN_NAME, DATA_TYPE 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'optima_db' 
  AND TABLE_NAME = 'delivery_instructions' 
  AND COLUMN_NAME LIKE '%operator%'
ORDER BY COLUMN_NAME;

SELECT 'Penghapusan field operator selesai.' as status;
