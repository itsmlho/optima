-- ============================================================================
-- Migration: Align spk_spareparts schema to current dev schema
-- Date: 2026-03-30
-- Applies to: PRODUCTION (Hostinger)
-- Why: PROD_20260312_create_spk_spareparts.sql used old schema with unit_id NOT NULL
--      and was missing columns: item_type, quantity_brought, source_type,
--      source_unit_id, is_from_warehouse, is_additional, sparepart_validated.
--      The new code (post 2026-03-14) cannot insert rows into the old schema,
--      causing "Gagal menyimpan sparepart" errors on every save attempt.
-- Risk: Table should be empty in production (all inserts were failing).
--       Verify with: SELECT COUNT(*) FROM spk_spareparts;
-- ============================================================================

-- Verify table is empty before proceeding (informational check)
-- SELECT COUNT(*) AS rows_to_be_dropped FROM spk_spareparts;
-- SELECT COUNT(*) AS returns_to_be_dropped FROM spk_sparepart_returns;

-- Step 1: Drop dependent table first (likely does not exist on prod)
DROP TABLE IF EXISTS `spk_sparepart_returns`;

-- Step 2: Drop old schema table
DROP TABLE IF EXISTS `spk_spareparts`;

-- Step 3: Recreate spk_spareparts with correct schema
CREATE TABLE `spk_spareparts` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `spk_id` INT UNSIGNED NOT NULL COMMENT 'FK to spk.id',

    -- Sparepart identification
    `unit_id` INT UNSIGNED NULL COMMENT 'FK to inventory_unit (nullable, for kanibal tracking)',
    `sparepart_code` VARCHAR(50) NULL COMMENT 'Official sparepart code (nullable for manual entries)',
    `sparepart_name` VARCHAR(255) NOT NULL COMMENT 'Sparepart name/description',
    `item_type` ENUM('sparepart','tool') DEFAULT 'sparepart' COMMENT 'Item classification',

    -- Quantity tracking
    `quantity_brought` INT UNSIGNED NOT NULL COMMENT 'Quantity requested from warehouse',
    `quantity_used` INT UNSIGNED DEFAULT 0 COMMENT 'Actual quantity used (filled during validation)',
    `satuan` VARCHAR(50) NOT NULL DEFAULT 'PCS' COMMENT 'Unit of measure',

    -- Source tracking
    `is_from_warehouse` TINYINT(1) DEFAULT 1 COMMENT '1=Warehouse, 0=Bekas/Kanibal',
    `source_type` ENUM('WAREHOUSE','BEKAS','KANIBAL') DEFAULT 'WAREHOUSE' COMMENT 'Source of sparepart',
    `source_unit_id` INT UNSIGNED NULL COMMENT 'FK to inventory_unit (for KANIBAL)',
    `source_notes` TEXT NULL COMMENT 'Additional source information',

    -- Usage tracking
    `notes` TEXT NULL COMMENT 'General notes about usage',
    `is_additional` TINYINT(1) DEFAULT 0 COMMENT '1=Added during validation, 0=Planned initially',
    `sparepart_validated` TINYINT(1) DEFAULT 0 COMMENT '1=Usage validated, 0=Still in plan',

    -- Timestamps
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Indexes
    INDEX `idx_spk_spareparts_spk` (`spk_id`),
    INDEX `idx_spk_spareparts_code` (`sparepart_code`),
    INDEX `idx_spk_spareparts_source` (`source_type`),
    INDEX `idx_spk_spareparts_validated` (`sparepart_validated`),
    INDEX `idx_spk_spareparts_created` (`created_at`),

    -- Foreign keys
    CONSTRAINT `fk_spk_spareparts_spk`
        FOREIGN KEY (`spk_id`) REFERENCES `spk`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_spk_spareparts_source_unit`
        FOREIGN KEY (`source_unit_id`) REFERENCES `inventory_unit`(`id_inventory_unit`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='SPK sparepart planning and actual usage tracking';

-- Step 4: Create spk_sparepart_returns table (new table, never existed on prod)
CREATE TABLE `spk_sparepart_returns` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `spk_id` INT UNSIGNED NOT NULL COMMENT 'FK to spk.id',
    `spk_sparepart_id` INT UNSIGNED NOT NULL COMMENT 'FK to spk_spareparts.id',

    -- Return details
    `quantity_returned` INT UNSIGNED NOT NULL COMMENT 'Quantity returned to warehouse',
    `return_reason` VARCHAR(255) NULL COMMENT 'Reason for return',
    `return_notes` TEXT NULL COMMENT 'Additional notes',

    -- Confirmation
    `confirmed_by` INT UNSIGNED NULL COMMENT 'FK to user.id (WH staff who confirmed)',
    `confirmed_at` DATETIME NULL COMMENT 'When return was confirmed',

    -- Timestamps
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Indexes
    INDEX `idx_spk_returns_spk` (`spk_id`),
    INDEX `idx_spk_returns_sparepart` (`spk_sparepart_id`),
    INDEX `idx_spk_returns_confirmed` (`confirmed_at`),

    -- Foreign keys
    CONSTRAINT `fk_spk_returns_spk`
        FOREIGN KEY (`spk_id`) REFERENCES `spk`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_spk_returns_sparepart`
        FOREIGN KEY (`spk_sparepart_id`) REFERENCES `spk_spareparts`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='SPK sparepart return tracking after PDI validation';

-- Verify result
SELECT 'spk_spareparts' AS table_name, COUNT(*) AS row_count FROM spk_spareparts
UNION ALL
SELECT 'spk_sparepart_returns', COUNT(*) FROM spk_sparepart_returns;

SHOW CREATE TABLE spk_spareparts\G
-- ============================================================================
-- End of Migration
-- ============================================================================
