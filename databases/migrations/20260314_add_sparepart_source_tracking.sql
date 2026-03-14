-- Migration: Add Enhanced Sparepart Source Tracking (Warehouse, Bekas, Kanibal)
-- Date: 2026-03-14
-- Purpose: Track sparepart source including cannibalization from other units

-- Step 1: Add new columns for enhanced source tracking
ALTER TABLE `work_order_spareparts` 
ADD COLUMN `source_type` ENUM('WAREHOUSE', 'BEKAS', 'KANIBAL') 
    NOT NULL DEFAULT 'WAREHOUSE' 
    COMMENT 'Sparepart source: WAREHOUSE=Stock baru dari gudang, BEKAS=Reuse sparepart bekas, KANIBAL=Copotan dari unit lain'
    AFTER `is_from_warehouse`,

ADD COLUMN `source_unit_id` INT UNSIGNED NULL 
    COMMENT 'FK to inventory_unit.id_inventory_unit - Unit asal jika source_type = KANIBAL'
    AFTER `source_type`,
    
ADD COLUMN `source_notes` TEXT NULL 
    COMMENT 'Detail sumber: alasan kanibal, kondisi part, lokasi copotan, dll'
    AFTER `source_unit_id`;

-- Step 2: Add indexes for better query performance
ALTER TABLE `work_order_spareparts`
ADD INDEX `idx_source_type` (`source_type`),
ADD INDEX `idx_source_unit` (`source_unit_id`),
ADD INDEX `idx_source_composite` (`source_type`, `is_from_warehouse`);

-- Step 3: Add foreign key constraint untuk source_unit_id
-- Note: Only execute if inventory_unit table exists and has proper index
ALTER TABLE `work_order_spareparts`
ADD CONSTRAINT `fk_wosp_source_unit` 
    FOREIGN KEY (`source_unit_id`) 
    REFERENCES `inventory_unit` (`id_inventory_unit`) 
    ON DELETE SET NULL 
    ON UPDATE CASCADE;

-- Step 4: Migrate existing data
-- Convert is_from_warehouse to source_type for backward compatibility
UPDATE `work_order_spareparts` 
SET `source_type` = CASE 
    WHEN `is_from_warehouse` = 1 THEN 'WAREHOUSE'
    WHEN `is_from_warehouse` = 0 THEN 'BEKAS'
    ELSE 'WAREHOUSE'
END
WHERE `source_type` = 'WAREHOUSE' AND `is_from_warehouse` IS NOT NULL;

-- Step 5: Update column comment for is_from_warehouse (mark as deprecated but keep for compatibility)
ALTER TABLE `work_order_spareparts` 
MODIFY COLUMN `is_from_warehouse` TINYINT(1) DEFAULT 1 
COMMENT 'DEPRECATED: Use source_type instead. 1=From Warehouse, 0=Non-Warehouse (Bekas/Kanibal). Kept for backward compatibility';

-- Verification queries (commented out - for manual testing)
-- SELECT source_type, COUNT(*) as total FROM work_order_spareparts GROUP BY source_type;
-- SELECT * FROM work_order_spareparts WHERE source_type = 'KANIBAL';
-- SHOW INDEX FROM work_order_spareparts WHERE Key_name LIKE 'idx_source%';
