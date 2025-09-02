-- Rollback Script: Mengembalikan struktur inventory_unit ke kondisi semula
-- Tanggal: 2025-08-30
-- Gunakan jika migration gagal atau ada masalah

-- Step 1: Tambahkan kembali kolom yang dihapus (jika belum ada)
SET @sql = (SELECT IF(
    NOT EXISTS(
        SELECT * FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'inventory_unit'
        AND COLUMN_NAME = 'model_baterai_id'
    ),
    'ALTER TABLE inventory_unit ADD COLUMN model_baterai_id int DEFAULT NULL, ADD COLUMN sn_baterai varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL, ADD COLUMN model_charger_id int DEFAULT NULL, ADD COLUMN sn_charger varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL, ADD COLUMN model_attachment_id int DEFAULT NULL, ADD COLUMN sn_attachment varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL;',
    'SELECT "Columns already exist" as message;'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 2: Restore data dari backup
UPDATE inventory_unit iu
JOIN inventory_unit_backup iub ON iu.id_inventory_unit = iub.id_inventory_unit
SET
    iu.model_baterai_id = iub.model_baterai_id,
    iu.sn_baterai = iub.sn_baterai,
    iu.model_charger_id = iub.model_charger_id,
    iu.sn_charger = iub.sn_charger,
    iu.model_attachment_id = iub.model_attachment_id,
    iu.sn_attachment = iub.sn_attachment;

-- Step 3: Hapus data yang dimigrasi dari inventory_attachment
DELETE FROM inventory_attachment
WHERE catatan_inventory LIKE 'Migrated from inventory_unit%';

-- Step 4: Hapus view dan functions yang dibuat
DROP VIEW IF EXISTS inventory_unit_components;
DROP FUNCTION IF EXISTS get_unit_battery_info;
DROP FUNCTION IF EXISTS get_unit_charger_info;
DROP FUNCTION IF EXISTS get_unit_attachment_info;

-- Step 5: Hapus tabel backup
DROP TABLE IF EXISTS inventory_unit_backup;

-- Step 6: Restore foreign key constraints (jika belum ada)
SET @sql = (SELECT IF(
    NOT EXISTS(
        SELECT * FROM information_schema.TABLE_CONSTRAINTS
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'inventory_unit'
        AND CONSTRAINT_TYPE = 'FOREIGN KEY'
        AND CONSTRAINT_NAME = 'inventory_unit_ibfk_9'
    ),
    'ALTER TABLE inventory_unit ADD CONSTRAINT inventory_unit_ibfk_9 FOREIGN KEY (model_attachment_id) REFERENCES attachment (id_attachment), ADD CONSTRAINT inventory_unit_ibfk_10 FOREIGN KEY (model_baterai_id) REFERENCES baterai (id), ADD CONSTRAINT inventory_unit_ibfk_11 FOREIGN KEY (model_charger_id) REFERENCES charger (id_charger);',
    'SELECT "Foreign key constraints already exist" as message;'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 7: Restore indexes (jika belum ada)
SET @sql = (SELECT IF(
    NOT EXISTS(
        SELECT * FROM information_schema.STATISTICS
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'inventory_unit'
        AND INDEX_NAME = 'model_baterai_id'
    ),
    'ALTER TABLE inventory_unit ADD INDEX model_baterai_id (model_baterai_id), ADD INDEX model_charger_id (model_charger_id), ADD INDEX model_attachment_id (model_attachment_id);',
    'SELECT "Indexes already exist" as message;'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 8: Update comments kembali
ALTER TABLE inventory_attachment
COMMENT = 'Inventory fisik: attachment, baterai, charger';

ALTER TABLE inventory_unit
COMMENT = 'Data unit utama dengan komponen battery, charger, attachment';

-- Log rollback completion
INSERT INTO migration_log (migration_name, executed_at, description)
VALUES ('rollback_consolidate_components', NOW(),
        'Rollback konsolidasi komponen ke inventory_unit');

COMMIT;
