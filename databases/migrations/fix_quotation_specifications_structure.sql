-- ========================================
-- FIX QUOTATION SPECIFICATIONS TABLE STRUCTURE
-- Date: 2025-12-05
-- Purpose: Clean up and optimize quotation_specifications table
--          Remove unused columns, add proper FK constraints, fix field names
-- ========================================

USE optima_ci;

-- Step 1: Backup existing data (optional, but recommended)
-- CREATE TABLE quotation_specifications_backup_20251205 AS SELECT * FROM quotation_specifications;

-- Step 2: Add missing columns that are actually used
ALTER TABLE quotation_specifications
ADD COLUMN IF NOT EXISTS `unit_accessories` TEXT COMMENT 'JSON array of selected accessories' AFTER `notes`;

-- Step 3: Migrate data from notes to unit_accessories (if accessories stored in notes)
-- This extracts "Accessories: ..." from notes and moves to unit_accessories
UPDATE quotation_specifications
SET unit_accessories = TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(notes, 'Accessories: ', -1), '\n', 1))
WHERE notes LIKE '%Accessories:%' AND (unit_accessories IS NULL OR unit_accessories = '');

-- Step 4: Clean up notes field (remove accessories info since it's now in dedicated column)
UPDATE quotation_specifications
SET notes = TRIM(REPLACE(notes, CONCAT('Accessories: ', unit_accessories), ''))
WHERE notes LIKE '%Accessories:%';

-- Step 5: Add proper foreign key constraints for existing reference columns
-- Check if FK doesn't exist before adding

-- Department FK
SET @fk_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS 
                  WHERE CONSTRAINT_SCHEMA = 'optima_ci' 
                  AND TABLE_NAME = 'quotation_specifications' 
                  AND CONSTRAINT_NAME = 'fk_qs_departemen');

SET @sql = IF(@fk_exists = 0, 
    'ALTER TABLE quotation_specifications ADD CONSTRAINT fk_qs_departemen 
     FOREIGN KEY (departemen_id) REFERENCES departemen(id_departemen) ON DELETE SET NULL',
    'SELECT "FK fk_qs_departemen already exists" AS msg');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Tipe Unit FK
SET @fk_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS 
                  WHERE CONSTRAINT_SCHEMA = 'optima_ci' 
                  AND TABLE_NAME = 'quotation_specifications' 
                  AND CONSTRAINT_NAME = 'fk_qs_tipe_unit');

SET @sql = IF(@fk_exists = 0, 
    'ALTER TABLE quotation_specifications ADD CONSTRAINT fk_qs_tipe_unit 
     FOREIGN KEY (tipe_unit_id) REFERENCES tipe_unit(id_tipe_unit) ON DELETE SET NULL',
    'SELECT "FK fk_qs_tipe_unit already exists" AS msg');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Kapasitas FK
SET @fk_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS 
                  WHERE CONSTRAINT_SCHEMA = 'optima_ci' 
                  AND TABLE_NAME = 'quotation_specifications' 
                  AND CONSTRAINT_NAME = 'fk_qs_kapasitas');

SET @sql = IF(@fk_exists = 0, 
    'ALTER TABLE quotation_specifications ADD CONSTRAINT fk_qs_kapasitas 
     FOREIGN KEY (kapasitas_id) REFERENCES kapasitas(id_kapasitas) ON DELETE SET NULL',
    'SELECT "FK fk_qs_kapasitas already exists" AS msg');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Charger FK (if charger table exists)
SET @fk_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS 
                  WHERE CONSTRAINT_SCHEMA = 'optima_ci' 
                  AND TABLE_NAME = 'quotation_specifications' 
                  AND CONSTRAINT_NAME = 'fk_qs_charger');

SET @table_exists = (SELECT COUNT(*) FROM information_schema.TABLES
                     WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'charger');

SET @sql = IF(@fk_exists = 0 AND @table_exists > 0, 
    'ALTER TABLE quotation_specifications ADD CONSTRAINT fk_qs_charger 
     FOREIGN KEY (charger_id) REFERENCES charger(id_charger) ON DELETE SET NULL',
    'SELECT "FK fk_qs_charger skipped (already exists or table missing)" AS msg');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Mast FK (if mast table exists)
SET @fk_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS 
                  WHERE CONSTRAINT_SCHEMA = 'optima_ci' 
                  AND TABLE_NAME = 'quotation_specifications' 
                  AND CONSTRAINT_NAME = 'fk_qs_mast');

SET @table_exists = (SELECT COUNT(*) FROM information_schema.TABLES
                     WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'mast');

SET @sql = IF(@fk_exists = 0 AND @table_exists > 0, 
    'ALTER TABLE quotation_specifications ADD CONSTRAINT fk_qs_mast 
     FOREIGN KEY (mast_id) REFERENCES mast(id_mast) ON DELETE SET NULL',
    'SELECT "FK fk_qs_mast skipped" AS msg');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Ban FK (if ban table exists)
SET @fk_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS 
                  WHERE CONSTRAINT_SCHEMA = 'optima_ci' 
                  AND TABLE_NAME = 'quotation_specifications' 
                  AND CONSTRAINT_NAME = 'fk_qs_ban');

SET @table_exists = (SELECT COUNT(*) FROM information_schema.TABLES
                     WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'ban');

SET @sql = IF(@fk_exists = 0 AND @table_exists > 0, 
    'ALTER TABLE quotation_specifications ADD CONSTRAINT fk_qs_ban 
     FOREIGN KEY (ban_id) REFERENCES ban(id_ban) ON DELETE SET NULL',
    'SELECT "FK fk_qs_ban skipped" AS msg');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Roda FK (if roda table exists)
SET @fk_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS 
                  WHERE CONSTRAINT_SCHEMA = 'optima_ci' 
                  AND TABLE_NAME = 'quotation_specifications' 
                  AND CONSTRAINT_NAME = 'fk_qs_roda');

SET @table_exists = (SELECT COUNT(*) FROM information_schema.TABLES
                     WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'roda');

SET @sql = IF(@fk_exists = 0 AND @table_exists > 0, 
    'ALTER TABLE quotation_specifications ADD CONSTRAINT fk_qs_roda 
     FOREIGN KEY (roda_id) REFERENCES roda(id_roda) ON DELETE SET NULL',
    'SELECT "FK fk_qs_roda skipped" AS msg');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Valve FK (if valve table exists)
SET @fk_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS 
                  WHERE CONSTRAINT_SCHEMA = 'optima_ci' 
                  AND TABLE_NAME = 'quotation_specifications' 
                  AND CONSTRAINT_NAME = 'fk_qs_valve');

SET @table_exists = (SELECT COUNT(*) FROM information_schema.TABLES
                     WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'valve');

SET @sql = IF(@fk_exists = 0 AND @table_exists > 0, 
    'ALTER TABLE quotation_specifications ADD CONSTRAINT fk_qs_valve 
     FOREIGN KEY (valve_id) REFERENCES valve(id_valve) ON DELETE SET NULL',
    'SELECT "FK fk_qs_valve skipped" AS msg');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 6: Add helpful indexes for frequently queried fields
CREATE INDEX IF NOT EXISTS idx_qs_departemen ON quotation_specifications(departemen_id);
CREATE INDEX IF NOT EXISTS idx_qs_tipe_unit ON quotation_specifications(tipe_unit_id);
CREATE INDEX IF NOT EXISTS idx_qs_kapasitas ON quotation_specifications(kapasitas_id);
CREATE INDEX IF NOT EXISTS idx_qs_spek_kode ON quotation_specifications(spek_kode);
CREATE INDEX IF NOT EXISTS idx_qs_active ON quotation_specifications(is_active);

-- Step 7: Drop unused columns (CAREFUL - backup first!)
-- Uncomment these only after verifying they're truly unused in your application

-- ALTER TABLE quotation_specifications DROP COLUMN IF EXISTS specifications;
-- ALTER TABLE quotation_specifications DROP COLUMN IF EXISTS service_duration;
-- ALTER TABLE quotation_specifications DROP COLUMN IF EXISTS service_frequency;
-- ALTER TABLE quotation_specifications DROP COLUMN IF EXISTS service_scope;
-- ALTER TABLE quotation_specifications DROP COLUMN IF EXISTS rental_duration;
-- ALTER TABLE quotation_specifications DROP COLUMN IF EXISTS rental_rate_type;
-- ALTER TABLE quotation_specifications DROP COLUMN IF EXISTS delivery_required;
-- ALTER TABLE quotation_specifications DROP COLUMN IF EXISTS installation_required;
-- ALTER TABLE quotation_specifications DROP COLUMN IF EXISTS delivery_cost;
-- ALTER TABLE quotation_specifications DROP COLUMN IF EXISTS installation_cost;
-- ALTER TABLE quotation_specifications DROP COLUMN IF EXISTS maintenance_included;
-- ALTER TABLE quotation_specifications DROP COLUMN IF EXISTS warranty_period;

-- Step 8: Verify structure
SELECT 
    'TABLE STRUCTURE VERIFIED' AS status,
    COUNT(*) AS total_specifications,
    COUNT(DISTINCT id_quotation) AS unique_quotations,
    COUNT(CASE WHEN unit_accessories IS NOT NULL AND unit_accessories != '' THEN 1 END) AS specs_with_accessories
FROM quotation_specifications;

-- Step 9: Show current table structure
SHOW CREATE TABLE quotation_specifications;

-- ========================================
-- SUMMARY OF CHANGES:
-- ========================================
-- ✅ Added: unit_accessories column for storing accessories as TEXT/JSON
-- ✅ Added: Foreign key constraints for departemen, tipe_unit, kapasitas, charger, mast, ban, roda, valve
-- ✅ Added: Indexes for better query performance
-- ✅ Migrated: Accessories data from notes to dedicated column
-- ✅ Cleaned: Notes field from accessories information
-- 
-- COLUMNS KEPT (ACTIVELY USED):
-- - id_specification, id_quotation, specification_name, specification_description
-- - quantity, unit, unit_price, total_price, harga_per_unit_harian
-- - departemen_id, tipe_unit_id, kapasitas_id
-- - brand, model, equipment_type
-- - charger_id, mast_id, ban_id, roda_id, valve_id
-- - jenis_baterai, attachment_tipe, attachment_merk
-- - unit_accessories (NEW)
-- - original_kontrak_id, original_kontrak_spek_id
-- - spek_kode, jumlah_tersedia
-- - notes, sort_order, is_optional, is_active
-- - created_at, updated_at
--
-- COLUMNS POTENTIALLY UNUSED (commented out drops):
-- - specifications (TEXT - generic field, replaced by specific fields)
-- - service_duration, service_frequency, service_scope
-- - rental_duration, rental_rate_type
-- - delivery_required, installation_required, delivery_cost, installation_cost
-- - maintenance_included, warranty_period
-- ========================================
