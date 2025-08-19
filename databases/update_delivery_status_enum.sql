-- ========================================
-- Script untuk Update Status ENUM ke Bahasa Indonesia
-- ========================================

USE optima_db;

-- Cek status enum saat ini
SELECT 'Status ENUM saat ini:' as info;
SELECT COLUMN_TYPE 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'optima_db' 
  AND TABLE_NAME = 'delivery_instructions' 
  AND COLUMN_NAME = 'status';

-- Cek nilai status yang ada di data
SELECT 'Nilai status yang ada di data:' as info;
SELECT status, COUNT(*) as jumlah 
FROM delivery_instructions 
GROUP BY status
ORDER BY status;

-- Update status enum (hanya jika masih menggunakan bahasa Inggris)
SET @current_enum = (
    SELECT COLUMN_TYPE 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'optima_db' 
      AND TABLE_NAME = 'delivery_instructions' 
      AND COLUMN_NAME = 'status'
);

-- Jika masih menggunakan status bahasa Inggris, lakukan migrasi
SET @needs_migration = (
    SELECT IF(
        @current_enum LIKE '%SUBMITTED%' OR 
        @current_enum LIKE '%DISPATCHED%' OR 
        @current_enum LIKE '%ARRIVED%',
        1, 0
    )
);

-- Jika perlu migrasi, lakukan langkah-langkah berikut:
SET @sql = (SELECT IF(
    @needs_migration = 1,
    'ALTER TABLE delivery_instructions ADD COLUMN status_temp ENUM("DIAJUKAN","DIPROSES","DIKIRIM","SAMPAI","DIBATALKAN") COLLATE utf8mb4_general_ci DEFAULT NULL',
    'SELECT "Status sudah dalam bahasa Indonesia" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Migrate data jika perlu
SET @sql = (SELECT IF(
    @needs_migration = 1,
    'UPDATE delivery_instructions SET 
        status_temp = CASE 
            WHEN status = "SUBMITTED" THEN "DIAJUKAN"
            WHEN status = "DISPATCHED" THEN "DIKIRIM" 
            WHEN status = "ARRIVED" THEN "SAMPAI"
            WHEN status = "CANCELLED" THEN "DIBATALKAN"
            ELSE "DIAJUKAN"
        END',
    'SELECT "Tidak perlu migrasi data" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Drop kolom lama dan rename jika perlu
SET @sql = (SELECT IF(
    @needs_migration = 1,
    'ALTER TABLE delivery_instructions DROP COLUMN status',
    'SELECT "Tidak perlu drop kolom status" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    @needs_migration = 1,
    'ALTER TABLE delivery_instructions CHANGE COLUMN status_temp status ENUM("DIAJUKAN","DIPROSES","DIKIRIM","SAMPAI","DIBATALKAN") COLLATE utf8mb4_general_ci NOT NULL DEFAULT "DIAJUKAN"',
    'SELECT "Tidak perlu rename kolom" as message'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Verifikasi hasil
SELECT 'Status ENUM setelah update:' as info;
SELECT COLUMN_TYPE 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'optima_db' 
  AND TABLE_NAME = 'delivery_instructions' 
  AND COLUMN_NAME = 'status';

SELECT 'Nilai status setelah migrasi:' as info;
SELECT status, COUNT(*) as jumlah 
FROM delivery_instructions 
GROUP BY status
ORDER BY status;

SELECT 'Update status enum selesai.' as status;
