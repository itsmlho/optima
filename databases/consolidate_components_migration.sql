-- Migration Script: Konsolidasi Battery/Charger/Attachment ke inventory_attachment
-- Tanggal: 2025-08-30
-- Tujuan: Menggunakan inventory_attachment sebagai single source of truth

-- Migration Script: Konsolidasi Battery/Charger/Attachment ke inventory_attachment
-- Tanggal: 2025-08-30
-- Tujuan: Menggunakan inventory_attachment sebagai single source of truth

-- Check if migration is needed
SET @migration_needed = (SELECT IF(
    EXISTS(
        SELECT * FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'inventory_unit'
        AND COLUMN_NAME IN ('model_baterai_id', 'sn_baterai', 'model_charger_id', 'sn_charger', 'model_attachment_id', 'sn_attachment')
    ),
    1,
    0
));

-- Step 1: Migrasi data existing dari inventory_unit ke inventory_attachment (jika kolom ada)
SET @migration_sql = (
    SELECT IF(@migration_needed = 1,
        'INSERT INTO inventory_attachment (
            tipe_item,
            po_id,
            id_inventory_unit,
            baterai_id,
            sn_baterai,
            charger_id,
            sn_charger,
            attachment_id,
            sn_attachment,
            kondisi_fisik,
            kelengkapan,
            status_unit,
            tanggal_masuk,
            catatan_inventory,
            created_at,
            updated_at
        )
        SELECT
            CASE
                WHEN iu.model_baterai_id IS NOT NULL THEN ''battery''
                WHEN iu.model_charger_id IS NOT NULL THEN ''charger''
                WHEN iu.model_attachment_id IS NOT NULL THEN ''attachment''
                ELSE ''attachment''
            END as tipe_item,
            COALESCE(iu.id_po, 1) as po_id,
            iu.id_inventory_unit,
            iu.model_baterai_id as baterai_id,
            iu.sn_baterai,
            iu.model_charger_id as charger_id,
            iu.sn_charger,
            iu.model_attachment_id as attachment_id,
            iu.sn_attachment,
            ''Baik'' as kondisi_fisik,
            ''Lengkap'' as kelengkapan,
            CASE
                WHEN iu.status_unit_id = 8 THEN 8
                ELSE 7
            END as status_unit,
            COALESCE(iu.created_at, NOW()) as tanggal_masuk,
            CONCAT(''Migrated from inventory_unit on '', NOW()) as catatan_inventory,
            iu.created_at,
            iu.updated_at
        FROM inventory_unit iu
        WHERE iu.model_baterai_id IS NOT NULL
           OR iu.model_charger_id IS NOT NULL
           OR iu.model_attachment_id IS NOT NULL;',
        'SELECT "No migration needed - columns do not exist" as message;'
    )
);

PREPARE stmt FROM @migration_sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 2: Update foreign key references di SPK untuk menggunakan inventory_attachment ID
UPDATE spk s
JOIN inventory_attachment ia ON (
    (s.persiapan_unit_id = ia.id_inventory_unit AND ia.tipe_item = 'battery' AND s.spesifikasi->>'$.persiapan_battery_id' = CAST(ia.baterai_id AS CHAR))
    OR (s.persiapan_unit_id = ia.id_inventory_unit AND ia.tipe_item = 'charger' AND s.spesifikasi->>'$.persiapan_charger_id' = CAST(ia.charger_id AS CHAR))
    OR (s.persiapan_unit_id = ia.id_inventory_unit AND ia.tipe_item = 'attachment' AND s.fabrikasi_attachment_id = ia.attachment_id)
)
SET s.spesifikasi = JSON_SET(
    s.spesifikasi,
    '$.persiapan_battery_inventory_id',
    CASE WHEN ia.tipe_item = 'battery' THEN ia.id_inventory_attachment ELSE s.spesifikasi->>'$.persiapan_battery_inventory_id' END,
    '$.persiapan_charger_inventory_id',
    CASE WHEN ia.tipe_item = 'charger' THEN ia.id_inventory_attachment ELSE s.spesifikasi->>'$.persiapan_charger_inventory_id' END,
    '$.fabrikasi_attachment_inventory_id',
    CASE WHEN ia.tipe_item = 'attachment' THEN ia.id_inventory_attachment ELSE s.spesifikasi->>'$.fabrikasi_attachment_inventory_id' END
)
WHERE s.persiapan_unit_id IS NOT NULL;

-- Step 3: Backup kolom yang akan dihapus (untuk rollback jika diperlukan)
CREATE TABLE IF NOT EXISTS inventory_unit_backup AS
SELECT
    iu.*,
    CASE WHEN @migration_needed = 1 THEN iu.model_baterai_id ELSE NULL END as model_baterai_id,
    CASE WHEN @migration_needed = 1 THEN iu.sn_baterai ELSE NULL END as sn_baterai,
    CASE WHEN @migration_needed = 1 THEN iu.model_charger_id ELSE NULL END as model_charger_id,
    CASE WHEN @migration_needed = 1 THEN iu.sn_charger ELSE NULL END as sn_charger,
    CASE WHEN @migration_needed = 1 THEN iu.model_attachment_id ELSE NULL END as model_attachment_id,
    CASE WHEN @migration_needed = 1 THEN iu.sn_attachment ELSE NULL END as sn_attachment,
    NOW() as backup_timestamp
FROM inventory_unit iu
WHERE @migration_needed = 1
    AND (iu.model_baterai_id IS NOT NULL
         OR iu.model_charger_id IS NOT NULL
         OR iu.model_attachment_id IS NOT NULL);

-- Step 4: Hapus kolom yang redundant dari inventory_unit (jika ada)
SET @sql = (SELECT IF(
    EXISTS(
        SELECT * FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'inventory_unit'
        AND COLUMN_NAME IN ('model_baterai_id', 'sn_baterai', 'model_charger_id', 'sn_charger', 'model_attachment_id', 'sn_attachment')
    ),
    'ALTER TABLE inventory_unit DROP COLUMN model_baterai_id, DROP COLUMN sn_baterai, DROP COLUMN model_charger_id, DROP COLUMN sn_charger, DROP COLUMN model_attachment_id, DROP COLUMN sn_attachment;',
    'SELECT "Columns already dropped or do not exist" as message;'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 5: Hapus foreign key constraints yang tidak diperlukan lagi (jika ada)
SET @sql = (SELECT IF(
    EXISTS(
        SELECT * FROM information_schema.TABLE_CONSTRAINTS
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'inventory_unit'
        AND CONSTRAINT_TYPE = 'FOREIGN KEY'
        AND CONSTRAINT_NAME IN ('inventory_unit_ibfk_9', 'inventory_unit_ibfk_10', 'inventory_unit_ibfk_11')
    ),
    'ALTER TABLE inventory_unit DROP FOREIGN KEY inventory_unit_ibfk_9, DROP FOREIGN KEY inventory_unit_ibfk_10, DROP FOREIGN KEY inventory_unit_ibfk_11;',
    'SELECT "Foreign key constraints already dropped or do not exist" as message;'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 6: Hapus indexes yang tidak diperlukan lagi (jika ada)
SET @sql = (SELECT IF(
    EXISTS(
        SELECT * FROM information_schema.STATISTICS
        WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = 'inventory_unit'
        AND INDEX_NAME IN ('model_baterai_id', 'model_charger_id', 'model_attachment_id')
    ),
    'ALTER TABLE inventory_unit DROP INDEX model_baterai_id, DROP INDEX model_charger_id, DROP INDEX model_attachment_id;',
    'SELECT "Indexes already dropped or do not exist" as message;'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 7: Update status_unit_id mapping untuk konsistensi
UPDATE inventory_unit
SET status_unit_id = CASE
    WHEN status_unit_id = 3 THEN 8  -- RENTAL -> In use
    WHEN status_unit_id = 1 THEN 7  -- STOK -> Available
    ELSE status_unit_id
END;

-- Step 8: Create view untuk backward compatibility (opsional)
CREATE OR REPLACE VIEW inventory_unit_components AS
SELECT
    iu.id_inventory_unit,
    iu.no_unit,
    iu.serial_number,
    -- Battery info
    ia_battery.baterai_id as model_baterai_id,
    ia_battery.sn_baterai,
    b.merk_baterai,
    b.tipe_baterai,
    b.jenis_baterai,
    -- Charger info
    ia_charger.charger_id as model_charger_id,
    ia_charger.sn_charger,
    c.merk_charger,
    c.tipe_charger,
    -- Attachment info
    ia_attachment.attachment_id as model_attachment_id,
    ia_attachment.sn_attachment,
    a.tipe as attachment_tipe,
    a.merk as attachment_merk,
    a.model as attachment_model
FROM inventory_unit iu
LEFT JOIN inventory_attachment ia_battery ON (
    iu.id_inventory_unit = ia_battery.id_inventory_unit
    AND ia_battery.tipe_item = 'battery'
    AND ia_battery.status_unit = 8
)
LEFT JOIN baterai b ON ia_battery.baterai_id = b.id
LEFT JOIN inventory_attachment ia_charger ON (
    iu.id_inventory_unit = ia_charger.id_inventory_unit
    AND ia_charger.tipe_item = 'charger'
    AND ia_charger.status_unit = 8
)
LEFT JOIN charger c ON ia_charger.charger_id = c.id_charger
LEFT JOIN inventory_attachment ia_attachment ON (
    iu.id_inventory_unit = ia_attachment.id_inventory_unit
    AND ia_attachment.tipe_item = 'attachment'
    AND ia_attachment.status_unit = 8
)
LEFT JOIN attachment a ON ia_attachment.attachment_id = a.id_attachment;

-- Step 9: Update comments pada tabel
ALTER TABLE inventory_attachment
COMMENT = 'Single source of truth untuk semua komponen: battery, charger, attachment';

ALTER TABLE inventory_unit
COMMENT = 'Data unit utama - komponen disimpan di inventory_attachment';

-- Step 10: Create helper functions untuk query yang sering digunakan
DELIMITER //

CREATE FUNCTION get_unit_battery_info(unit_id INT)
RETURNS JSON
DETERMINISTIC
BEGIN
    DECLARE result JSON DEFAULT NULL;

    SELECT JSON_OBJECT(
        'battery_id', ia.baterai_id,
        'sn_baterai', ia.sn_baterai,
        'merk', b.merk_baterai,
        'tipe', b.tipe_baterai,
        'jenis', b.jenis_baterai,
        'inventory_id', ia.id_inventory_attachment
    ) INTO result
    FROM inventory_attachment ia
    JOIN baterai b ON ia.baterai_id = b.id
    WHERE ia.id_inventory_unit = unit_id
      AND ia.tipe_item = 'battery'
      AND ia.status_unit = 8
    LIMIT 1;

    RETURN result;
END //

CREATE FUNCTION get_unit_charger_info(unit_id INT)
RETURNS JSON
DETERMINISTIC
BEGIN
    DECLARE result JSON DEFAULT NULL;

    SELECT JSON_OBJECT(
        'charger_id', ia.charger_id,
        'sn_charger', ia.sn_charger,
        'merk', c.merk_charger,
        'tipe', c.tipe_charger,
        'inventory_id', ia.id_inventory_attachment
    ) INTO result
    FROM inventory_attachment ia
    JOIN charger c ON ia.charger_id = c.id_charger
    WHERE ia.id_inventory_unit = unit_id
      AND ia.tipe_item = 'charger'
      AND ia.status_unit = 8
    LIMIT 1;

    RETURN result;
END //

CREATE FUNCTION get_unit_attachment_info(unit_id INT)
RETURNS JSON
DETERMINISTIC
BEGIN
    DECLARE result JSON DEFAULT NULL;

    SELECT JSON_OBJECT(
        'attachment_id', ia.attachment_id,
        'sn_attachment', ia.sn_attachment,
        'tipe', a.tipe,
        'merk', a.merk,
        'model', a.model,
        'inventory_id', ia.id_inventory_attachment
    ) INTO result
    FROM inventory_attachment ia
    JOIN attachment a ON ia.attachment_id = a.id_attachment
    WHERE ia.id_inventory_unit = unit_id
      AND ia.tipe_item = 'attachment'
      AND ia.status_unit = 8
    LIMIT 1;

    RETURN result;
END //

DELIMITER ;

-- Log migration completion
INSERT INTO migration_log (migration_name, executed_at, description)
VALUES ('consolidate_components_to_inventory_attachment', NOW(),
        'Konsolidasi battery/charger/attachment ke inventory_attachment sebagai single source of truth');

COMMIT;
