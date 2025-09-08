-- ========================================
-- Add DI status components: jenis_perintah, tujuan_perintah, status_eksekusi
-- Safe/Idempotent script for MySQL
-- ========================================

USE optima_db;

-- jenis_perintah (nullable text or enum candidate)
SET @sql = (
  SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
      WHERE TABLE_SCHEMA='optima_db' AND TABLE_NAME='delivery_instructions' 
        AND COLUMN_NAME='jenis_perintah') = 0,
    'ALTER TABLE delivery_instructions ADD COLUMN jenis_perintah VARCHAR(50) NULL AFTER status',
    'SELECT "Field jenis_perintah sudah ada" as message'
  )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- tujuan_perintah (nullable text or FK candidate)
SET @sql = (
  SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
      WHERE TABLE_SCHEMA='optima_db' AND TABLE_NAME='delivery_instructions' 
        AND COLUMN_NAME='tujuan_perintah') = 0,
    'ALTER TABLE delivery_instructions ADD COLUMN tujuan_perintah VARCHAR(100) NULL AFTER jenis_perintah',
    'SELECT "Field tujuan_perintah sudah ada" as message'
  )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- status_eksekusi (use enum-like check; store as VARCHAR to avoid strict enum migrations)
SET @sql = (
  SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
      WHERE TABLE_SCHEMA='optima_db' AND TABLE_NAME='delivery_instructions' 
        AND COLUMN_NAME='status_eksekusi') = 0,
    'ALTER TABLE delivery_instructions ADD COLUMN status_eksekusi VARCHAR(20) NULL COMMENT "READY, DISPATCHED, DELIVERED, CANCELLED" AFTER tujuan_perintah',
    'SELECT "Field status_eksekusi sudah ada" as message'
  )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Helpful indexes
SET @sql = (
  SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
      WHERE TABLE_SCHEMA='optima_db' AND TABLE_NAME='delivery_instructions' 
        AND INDEX_NAME='idx_di_status_eksekusi') = 0,
    'ALTER TABLE delivery_instructions ADD INDEX idx_di_status_eksekusi (status_eksekusi)',
    'SELECT "Index idx_di_status_eksekusi sudah ada" as message'
  )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Backfill (best-effort):
-- Rule of thumb:
--  - If sampai_tanggal_approve IS NOT NULL => DELIVERED
--  - Else if berangkat_tanggal_approve IS NOT NULL OR status IN ("PROCESSED","SHIPPED") => DISPATCHED
--  - Else if perencanaan_tanggal_approve IS NOT NULL => READY
--  - Else leave NULL
UPDATE delivery_instructions
SET status_eksekusi = 'DELIVERED'
WHERE status_eksekusi IS NULL AND sampai_tanggal_approve IS NOT NULL;

UPDATE delivery_instructions
SET status_eksekusi = 'DISPATCHED'
WHERE status_eksekusi IS NULL AND (
  berangkat_tanggal_approve IS NOT NULL OR status IN ('PROCESSED','SHIPPED')
);

UPDATE delivery_instructions
SET status_eksekusi = 'READY'
WHERE status_eksekusi IS NULL AND perencanaan_tanggal_approve IS NOT NULL;

SELECT 'Done adding DI status components' as status;
