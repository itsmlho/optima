-- ========================================
-- RESTRUCTURE QUOTATION SPECIFICATIONS TABLE
-- Date: 2025-12-05
-- Purpose: Clean up unused columns, rename fields, add proper FK constraints
-- ========================================

USE optima_ci;

-- BACKUP TABLE FIRST (IMPORTANT!)
DROP TABLE IF EXISTS quotation_specifications_backup_20251205;
CREATE TABLE quotation_specifications_backup_20251205 AS SELECT * FROM quotation_specifications;
SELECT CONCAT('✅ Backup created: ', COUNT(*), ' rows') AS backup_status 
FROM quotation_specifications_backup_20251205;

-- ========================================
-- STEP 1: ADD NEW COLUMNS (IF NOT EXISTS)
-- ========================================

-- Check and add columns only if they don't exist
SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'quotation_specifications' 
    AND COLUMN_NAME = 'monthly_price');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE quotation_specifications ADD COLUMN monthly_price DECIMAL(15,2) DEFAULT 0 COMMENT "Harga sewa per bulan" AFTER unit_price',
    'SELECT "monthly_price already exists" AS skip');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'quotation_specifications' 
    AND COLUMN_NAME = 'daily_price');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE quotation_specifications ADD COLUMN daily_price DECIMAL(15,2) DEFAULT 0 COMMENT "Harga sewa per hari" AFTER monthly_price',
    'SELECT "daily_price already exists" AS skip');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'quotation_specifications' 
    AND COLUMN_NAME = 'specification_type');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE quotation_specifications ADD COLUMN specification_type ENUM("UNIT", "ATTACHMENT") DEFAULT "UNIT" COMMENT "Tipe spesifikasi: UNIT atau ATTACHMENT" AFTER category',
    'SELECT "specification_type already exists" AS skip');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'quotation_specifications' 
    AND COLUMN_NAME = 'battery_id');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE quotation_specifications ADD COLUMN battery_id INT NULL COMMENT "FK to baterai table" AFTER jenis_baterai',
    'SELECT "battery_id already exists" AS skip');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'quotation_specifications' 
    AND COLUMN_NAME = 'attachment_id');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE quotation_specifications ADD COLUMN attachment_id INT NULL COMMENT "FK to attachment table" AFTER attachment_tipe',
    'SELECT "attachment_id already exists" AS skip');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'quotation_specifications' 
    AND COLUMN_NAME = 'brand_id');
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE quotation_specifications ADD COLUMN brand_id INT NULL COMMENT "FK to model_unit table" AFTER brand',
    'SELECT "brand_id already exists" AS skip');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ========================================
-- STEP 2: MIGRATE DATA TO NEW COLUMNS
-- ========================================

-- Migrate unit_price to monthly_price
UPDATE quotation_specifications 
SET monthly_price = COALESCE(unit_price, 0);

-- Migrate harga_per_unit_harian to daily_price
UPDATE quotation_specifications 
SET daily_price = COALESCE(harga_per_unit_harian, 0);

-- Recalculate total_price = (quantity * monthly_price) + daily_price
UPDATE quotation_specifications 
SET total_price = (COALESCE(quantity, 1) * COALESCE(monthly_price, 0)) + COALESCE(daily_price, 0);

-- Set specification_type based on unit value or default to 'UNIT'
UPDATE quotation_specifications 
SET specification_type = 
    CASE 
        WHEN unit LIKE '%attach%' OR attachment_tipe IS NOT NULL THEN 'ATTACHMENT'
        ELSE 'UNIT'
    END;

-- Migrate brand to brand_id (match by merk_unit in model_unit table)
UPDATE quotation_specifications qs
LEFT JOIN model_unit mu ON qs.brand COLLATE utf8mb4_general_ci = mu.merk_unit COLLATE utf8mb4_general_ci
SET qs.brand_id = mu.id_model_unit
WHERE qs.brand IS NOT NULL AND mu.id_model_unit IS NOT NULL;

-- Migrate jenis_baterai to battery_id (match by jenis_baterai in baterai table)
UPDATE quotation_specifications qs
LEFT JOIN baterai b ON qs.jenis_baterai COLLATE utf8mb4_general_ci = b.jenis_baterai COLLATE utf8mb4_general_ci
SET qs.battery_id = b.id
WHERE qs.jenis_baterai IS NOT NULL AND b.id IS NOT NULL;

-- Migrate attachment_tipe to attachment_id (match by tipe in attachment table)
UPDATE quotation_specifications qs
LEFT JOIN attachment a ON qs.attachment_tipe COLLATE utf8mb4_general_ci = a.tipe COLLATE utf8mb4_general_ci
SET qs.attachment_id = a.id_attachment
WHERE qs.attachment_tipe IS NOT NULL AND a.id_attachment IS NOT NULL;

-- ========================================
-- STEP 3: ADD FOREIGN KEY CONSTRAINTS
-- ========================================

-- Add FK to model_unit (brand)
SET @fk_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = 'optima_ci' AND TABLE_NAME = 'quotation_specifications' 
    AND CONSTRAINT_NAME = 'fk_qs_brand');
SET @sql = IF(@fk_exists = 0, 
    'ALTER TABLE quotation_specifications ADD CONSTRAINT fk_qs_brand FOREIGN KEY (brand_id) REFERENCES model_unit(id_model_unit) ON DELETE SET NULL',
    'SELECT "fk_qs_brand already exists" AS skip');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Add FK to baterai (battery)
SET @fk_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = 'optima_ci' AND TABLE_NAME = 'quotation_specifications' 
    AND CONSTRAINT_NAME = 'fk_qs_battery');
SET @sql = IF(@fk_exists = 0, 
    'ALTER TABLE quotation_specifications ADD CONSTRAINT fk_qs_battery FOREIGN KEY (battery_id) REFERENCES baterai(id) ON DELETE SET NULL',
    'SELECT "fk_qs_battery already exists" AS skip');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Add FK to attachment
SET @fk_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = 'optima_ci' AND TABLE_NAME = 'quotation_specifications' 
    AND CONSTRAINT_NAME = 'fk_qs_attachment');
SET @sql = IF(@fk_exists = 0, 
    'ALTER TABLE quotation_specifications ADD CONSTRAINT fk_qs_attachment FOREIGN KEY (attachment_id) REFERENCES attachment(id_attachment) ON DELETE SET NULL',
    'SELECT "fk_qs_attachment already exists" AS skip');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ========================================
-- STEP 4: DROP UNUSED COLUMNS
-- ========================================

-- Drop completely unused columns (one by one to avoid IF EXISTS syntax issues)
SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'quotation_specifications' AND COLUMN_NAME = 'specification_description');
SET @sql = IF(@col_exists > 0, 'ALTER TABLE quotation_specifications DROP COLUMN specification_description', 'SELECT "specification_description not exists" AS skip');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'quotation_specifications' AND COLUMN_NAME = 'category');
SET @sql = IF(@col_exists > 0, 'ALTER TABLE quotation_specifications DROP COLUMN category', 'SELECT "category not exists" AS skip');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'quotation_specifications' AND COLUMN_NAME = 'model');
SET @sql = IF(@col_exists > 0, 'ALTER TABLE quotation_specifications DROP COLUMN model', 'SELECT "model not exists" AS skip');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'quotation_specifications' AND COLUMN_NAME = 'equipment_type');
SET @sql = IF(@col_exists > 0, 'ALTER TABLE quotation_specifications DROP COLUMN equipment_type', 'SELECT "equipment_type not exists" AS skip');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'quotation_specifications' AND COLUMN_NAME = 'specifications');
SET @sql = IF(@col_exists > 0, 'ALTER TABLE quotation_specifications DROP COLUMN specifications', 'SELECT "specifications not exists" AS skip');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'quotation_specifications' AND COLUMN_NAME = 'service_duration');
SET @sql = IF(@col_exists > 0, 'ALTER TABLE quotation_specifications DROP COLUMN service_duration', 'SELECT "service_duration not exists" AS skip');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'quotation_specifications' AND COLUMN_NAME = 'service_frequency');
SET @sql = IF(@col_exists > 0, 'ALTER TABLE quotation_specifications DROP COLUMN service_frequency', 'SELECT "service_frequency not exists" AS skip');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'quotation_specifications' AND COLUMN_NAME = 'service_scope');
SET @sql = IF(@col_exists > 0, 'ALTER TABLE quotation_specifications DROP COLUMN service_scope', 'SELECT "service_scope not exists" AS skip');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'quotation_specifications' AND COLUMN_NAME = 'delivery_required');
SET @sql = IF(@col_exists > 0, 'ALTER TABLE quotation_specifications DROP COLUMN delivery_required', 'SELECT "delivery_required not exists" AS skip');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'quotation_specifications' AND COLUMN_NAME = 'installation_required');
SET @sql = IF(@col_exists > 0, 'ALTER TABLE quotation_specifications DROP COLUMN installation_required', 'SELECT "installation_required not exists" AS skip');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'quotation_specifications' AND COLUMN_NAME = 'delivery_cost');
SET @sql = IF(@col_exists > 0, 'ALTER TABLE quotation_specifications DROP COLUMN delivery_cost', 'SELECT "delivery_cost not exists" AS skip');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'quotation_specifications' AND COLUMN_NAME = 'installation_cost');
SET @sql = IF(@col_exists > 0, 'ALTER TABLE quotation_specifications DROP COLUMN installation_cost', 'SELECT "installation_cost not exists" AS skip');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'quotation_specifications' AND COLUMN_NAME = 'maintenance_included');
SET @sql = IF(@col_exists > 0, 'ALTER TABLE quotation_specifications DROP COLUMN maintenance_included', 'SELECT "maintenance_included not exists" AS skip');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'quotation_specifications' AND COLUMN_NAME = 'warranty_period');
SET @sql = IF(@col_exists > 0, 'ALTER TABLE quotation_specifications DROP COLUMN warranty_period', 'SELECT "warranty_period not exists" AS skip');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'quotation_specifications' AND COLUMN_NAME = 'notes');
SET @sql = IF(@col_exists > 0, 'ALTER TABLE quotation_specifications DROP COLUMN notes', 'SELECT "notes not exists" AS skip');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'quotation_specifications' AND COLUMN_NAME = 'sort_order');
SET @sql = IF(@col_exists > 0, 'ALTER TABLE quotation_specifications DROP COLUMN sort_order', 'SELECT "sort_order not exists" AS skip');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'quotation_specifications' AND COLUMN_NAME = 'is_optional');
SET @sql = IF(@col_exists > 0, 'ALTER TABLE quotation_specifications DROP COLUMN is_optional', 'SELECT "is_optional not exists" AS skip');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'quotation_specifications' AND COLUMN_NAME = 'spek_kode');
SET @sql = IF(@col_exists > 0, 'ALTER TABLE quotation_specifications DROP COLUMN spek_kode', 'SELECT "spek_kode not exists" AS skip');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'quotation_specifications' AND COLUMN_NAME = 'jumlah_tersedia');
SET @sql = IF(@col_exists > 0, 'ALTER TABLE quotation_specifications DROP COLUMN jumlah_tersedia', 'SELECT "jumlah_tersedia not exists" AS skip');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'quotation_specifications' AND COLUMN_NAME = 'attachment_merk');
SET @sql = IF(@col_exists > 0, 'ALTER TABLE quotation_specifications DROP COLUMN attachment_merk', 'SELECT "attachment_merk not exists" AS skip');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'quotation_specifications' AND COLUMN_NAME = 'original_kontrak_spek_id');
SET @sql = IF(@col_exists > 0, 'ALTER TABLE quotation_specifications DROP COLUMN original_kontrak_spek_id', 'SELECT "original_kontrak_spek_id not exists" AS skip');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ========================================
-- STEP 5: DROP OLD COLUMNS (NOW REPLACED)
-- ========================================

-- Drop old columns that have been replaced by new ones
SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'quotation_specifications' AND COLUMN_NAME = 'unit_price');
SET @sql = IF(@col_exists > 0, 'ALTER TABLE quotation_specifications DROP COLUMN unit_price', 'SELECT "unit_price not exists" AS skip');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'quotation_specifications' AND COLUMN_NAME = 'harga_per_unit_harian');
SET @sql = IF(@col_exists > 0, 'ALTER TABLE quotation_specifications DROP COLUMN harga_per_unit_harian', 'SELECT "harga_per_unit_harian not exists" AS skip');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'quotation_specifications' AND COLUMN_NAME = 'unit');
SET @sql = IF(@col_exists > 0, 'ALTER TABLE quotation_specifications DROP COLUMN unit', 'SELECT "unit not exists" AS skip');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'quotation_specifications' AND COLUMN_NAME = 'jenis_baterai');
SET @sql = IF(@col_exists > 0, 'ALTER TABLE quotation_specifications DROP COLUMN jenis_baterai', 'SELECT "jenis_baterai not exists" AS skip');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'quotation_specifications' AND COLUMN_NAME = 'attachment_tipe');
SET @sql = IF(@col_exists > 0, 'ALTER TABLE quotation_specifications DROP COLUMN attachment_tipe', 'SELECT "attachment_tipe not exists" AS skip');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'quotation_specifications' AND COLUMN_NAME = 'brand');
SET @sql = IF(@col_exists > 0, 'ALTER TABLE quotation_specifications DROP COLUMN brand', 'SELECT "brand not exists" AS skip');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ========================================
-- STEP 6: ADD INDEXES FOR PERFORMANCE
-- ========================================

SET @index_exists = (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'quotation_specifications' AND INDEX_NAME = 'idx_qs_specification_type');
SET @sql = IF(@index_exists = 0, 'CREATE INDEX idx_qs_specification_type ON quotation_specifications(specification_type)', 'SELECT "idx_qs_specification_type exists" AS skip');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @index_exists = (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'quotation_specifications' AND INDEX_NAME = 'idx_qs_brand');
SET @sql = IF(@index_exists = 0, 'CREATE INDEX idx_qs_brand ON quotation_specifications(brand_id)', 'SELECT "idx_qs_brand exists" AS skip');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @index_exists = (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'quotation_specifications' AND INDEX_NAME = 'idx_qs_battery');
SET @sql = IF(@index_exists = 0, 'CREATE INDEX idx_qs_battery ON quotation_specifications(battery_id)', 'SELECT "idx_qs_battery exists" AS skip');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @index_exists = (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'quotation_specifications' AND INDEX_NAME = 'idx_qs_attachment');
SET @sql = IF(@index_exists = 0, 'CREATE INDEX idx_qs_attachment ON quotation_specifications(attachment_id)', 'SELECT "idx_qs_attachment exists" AS skip');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @index_exists = (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'quotation_specifications' AND INDEX_NAME = 'idx_qs_monthly_price');
SET @sql = IF(@index_exists = 0, 'CREATE INDEX idx_qs_monthly_price ON quotation_specifications(monthly_price)', 'SELECT "idx_qs_monthly_price exists" AS skip');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @index_exists = (SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = 'optima_ci' AND TABLE_NAME = 'quotation_specifications' AND INDEX_NAME = 'idx_qs_daily_price');
SET @sql = IF(@index_exists = 0, 'CREATE INDEX idx_qs_daily_price ON quotation_specifications(daily_price)', 'SELECT "idx_qs_daily_price exists" AS skip');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ========================================
-- STEP 7: VERIFY FINAL STRUCTURE
-- ========================================

SELECT '========================================' AS '';
SELECT '✅ MIGRATION COMPLETED' AS status;
SELECT '========================================' AS '';

SELECT 
    COUNT(*) AS total_specifications,
    SUM(CASE WHEN specification_type = 'UNIT' THEN 1 ELSE 0 END) AS units,
    SUM(CASE WHEN specification_type = 'ATTACHMENT' THEN 1 ELSE 0 END) AS attachments,
    SUM(CASE WHEN brand_id IS NOT NULL THEN 1 ELSE 0 END) AS specs_with_brand,
    SUM(CASE WHEN battery_id IS NOT NULL THEN 1 ELSE 0 END) AS specs_with_battery,
    SUM(CASE WHEN attachment_id IS NOT NULL THEN 1 ELSE 0 END) AS specs_with_attachment
FROM quotation_specifications;

-- Show final table structure
SHOW CREATE TABLE quotation_specifications\G

-- ========================================
-- FINAL TABLE STRUCTURE:
-- ========================================
-- ✅ KEPT COLUMNS:
-- - id_specification (PK)
-- - id_quotation (FK to quotations)
-- - specification_name
-- - quantity
-- - monthly_price (NEW - was unit_price)
-- - daily_price (NEW - was harga_per_unit_harian)
-- - total_price (RECALCULATED)
-- - specification_type (NEW - was unit) ENUM('UNIT', 'ATTACHMENT')
-- - departemen_id (FK to departemen)
-- - tipe_unit_id (FK to tipe_unit)
-- - kapasitas_id (FK to kapasitas)
-- - brand_id (NEW FK to model_unit - was brand VARCHAR)
-- - battery_id (NEW FK to baterai - was jenis_baterai VARCHAR)
-- - charger_id (FK to charger)
-- - attachment_id (NEW FK to attachment - was attachment_tipe VARCHAR)
-- - mast_id (FK to mast)
-- - ban_id (FK to ban)
-- - roda_id (FK to roda)
-- - valve_id (FK to valve)
-- - unit_accessories (accessories list)
-- - original_kontrak_id
-- - is_active
-- - created_at
-- - updated_at
--
-- ❌ REMOVED COLUMNS:
-- - specification_description (unused)
-- - category (unused)
-- - model (unused)
-- - equipment_type (unused)
-- - specifications (unused)
-- - service_* (5 columns - unused)
-- - delivery_* (3 columns - unused)
-- - installation_* (2 columns - unused)
-- - maintenance_included (unused)
-- - warranty_period (unused)
-- - notes (replaced by unit_accessories)
-- - sort_order (unused)
-- - is_optional (unused)
-- - spek_kode (unused)
-- - jumlah_tersedia (unused)
-- - attachment_merk (unused)
-- - original_kontrak_spek_id (unused)
-- - unit (replaced by specification_type)
-- - unit_price (replaced by monthly_price)
-- - harga_per_unit_harian (replaced by daily_price)
-- - brand (replaced by brand_id FK)
-- - jenis_baterai (replaced by battery_id FK)
-- - attachment_tipe (replaced by attachment_id FK)
--
-- ========================================
