-- Database Migration: Add Workflow Fields to inventory_unit
-- File: databases/add_workflow_fields.sql

-- Add missing fields for complete workflow
ALTER TABLE `inventory_unit` 
ADD COLUMN `aksesoris_spk` JSON NULL COMMENT 'Aksesoris yang dipilih dari SPK dalam format JSON' AFTER `harga_sewa_harian`,
ADD COLUMN `lokasi_pelanggan` VARCHAR(255) NULL COMMENT 'Lokasi pelanggan dari kontrak' AFTER `aksesoris_spk`,
ADD COLUMN `workflow_status` ENUM('draft','assigned','spk_created','di_created','delivered','returned') DEFAULT 'draft' COMMENT 'Status workflow unit' AFTER `lokasi_pelanggan`,
ADD COLUMN `spk_id` INT UNSIGNED NULL COMMENT 'Reference ke SPK yang mengalokasikan unit ini' AFTER `workflow_status`,
ADD COLUMN `di_id` INT UNSIGNED NULL COMMENT 'Reference ke DI yang mendelivery unit ini' AFTER `spk_id`;

-- Add indexes for performance
ALTER TABLE `inventory_unit`
ADD INDEX `idx_workflow_status` (`workflow_status`),
ADD INDEX `idx_spk_id` (`spk_id`),
ADD INDEX `idx_di_id` (`di_id`);

-- Add foreign key constraints (optional, if tables exist)
-- ALTER TABLE `inventory_unit` 
-- ADD CONSTRAINT `fk_inventory_unit_spk` FOREIGN KEY (`spk_id`) REFERENCES `spk_service` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
-- ADD CONSTRAINT `fk_inventory_unit_di` FOREIGN KEY (`di_id`) REFERENCES `delivery_instructions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- Update existing records to have proper workflow_status
UPDATE `inventory_unit` 
SET `workflow_status` = 
    CASE 
        WHEN `kontrak_id` IS NOT NULL AND `status_unit_id` = 3 THEN 'assigned'
        WHEN `status_unit_id` = 7 THEN 'draft'  -- STOCK
        WHEN `status_unit_id` = 1 THEN 'delivered'  -- TERSEDIA di lokasi customer
        ELSE 'draft'
    END
WHERE `workflow_status` IS NULL;

-- Add data validation triggers
DELIMITER $$

-- Trigger untuk update workflow_status otomatis
CREATE TRIGGER `update_workflow_status_on_assignment` 
BEFORE UPDATE ON `inventory_unit`
FOR EACH ROW
BEGIN
    -- Auto update workflow status based on kontrak assignment
    IF NEW.kontrak_id IS NOT NULL AND OLD.kontrak_id IS NULL THEN
        SET NEW.workflow_status = 'assigned';
        -- Copy lokasi from kontrak
        SET NEW.lokasi_pelanggan = (
            SELECT pelanggan 
            FROM kontrak 
            WHERE id = NEW.kontrak_id 
            LIMIT 1
        );
    END IF;
    
    -- Update workflow when SPK is assigned
    IF NEW.spk_id IS NOT NULL AND OLD.spk_id IS NULL THEN
        SET NEW.workflow_status = 'spk_created';
    END IF;
    
    -- Update workflow when DI is assigned  
    IF NEW.di_id IS NOT NULL AND OLD.di_id IS NULL THEN
        SET NEW.workflow_status = 'di_created';
    END IF;
END$$

DELIMITER ;

-- Verify the migration
SELECT 
    'Migration completed successfully' as status,
    COUNT(*) as total_units,
    COUNT(CASE WHEN workflow_status = 'assigned' THEN 1 END) as assigned_units,
    COUNT(CASE WHEN harga_sewa_bulanan IS NOT NULL THEN 1 END) as units_with_price
FROM inventory_unit;
