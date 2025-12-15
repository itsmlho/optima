-- ===============================================
-- MULTI-MECHANIC DATABASE IMPLEMENTATION - CORRECTED VERSION
-- ===============================================

-- Add new columns to spk_unit_stages for multi-mechanic support
ALTER TABLE `spk_unit_stages` 
ADD COLUMN `mechanics_json` JSON NULL COMMENT 'JSON array of assigned mechanics with roles' AFTER `mekanik`,
ADD COLUMN `primary_mechanic_id` INT NULL COMMENT 'Primary mechanic employee ID' AFTER `mechanics_json`,
ADD COLUMN `mechanics_count` INT DEFAULT 0 COMMENT 'Total number of assigned mechanics' AFTER `primary_mechanic_id`;

-- Add foreign key for primary mechanic
ALTER TABLE `spk_unit_stages` 
ADD CONSTRAINT `fk_spk_unit_stages_primary_mechanic` 
FOREIGN KEY (`primary_mechanic_id`) REFERENCES `employees`(`id`) ON DELETE SET NULL;

-- Create spk_stage_mechanics table for individual mechanic assignments
CREATE TABLE IF NOT EXISTS `spk_stage_mechanics` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `spk_id` INT UNSIGNED NOT NULL,
    `unit_index` INT NOT NULL,
    `stage_name` ENUM('persiapan_unit', 'fabrikasi', 'painting', 'pdi') NOT NULL,
    `employee_id` INT NOT NULL,
    `employee_role` ENUM('MECHANIC_UNIT_PREP', 'MECHANIC_FABRICATION', 'MECHANIC_SERVICE_AREA', 'FOREMAN', 'SUPERVISOR', 'HELPER') NOT NULL,
    `is_primary` TINYINT(1) DEFAULT 0 COMMENT 'Primary mechanic for this stage',
    `assigned_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `assigned_by` INT NULL COMMENT 'User ID who assigned this mechanic',
    
    -- Foreign keys with correct data types
    CONSTRAINT `fk_spk_stage_mechanics_spk` 
        FOREIGN KEY (`spk_id`) REFERENCES `spk`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_spk_stage_mechanics_employee` 
        FOREIGN KEY (`employee_id`) REFERENCES `employees`(`id`) ON DELETE CASCADE,
    
    -- Unique constraint to prevent duplicate assignments
    UNIQUE KEY `unique_stage_employee` (`spk_id`, `unit_index`, `stage_name`, `employee_id`),
    
    -- Index for performance
    INDEX `idx_spk_stage` (`spk_id`, `unit_index`, `stage_name`),
    INDEX `idx_employee` (`employee_id`)
) COMMENT = 'Multi-mechanic assignments for SPK stages with role-based validation';

-- Update existing records to new format (preserve existing single mechanic assignments)
UPDATE spk_unit_stages 
SET mechanics_json = CASE 
    WHEN mekanik IS NOT NULL AND mekanik != '' THEN 
        JSON_OBJECT(
            'mechanics', JSON_ARRAY(
                JSON_OBJECT('name', mekanik, 'role', 'LEGACY', 'is_primary', true)
            ),
            'helpers', JSON_ARRAY(),
            'legacy_migration', true,
            'migrated_at', NOW()
        )
    ELSE NULL
END,
mechanics_count = CASE WHEN mekanik IS NOT NULL AND mekanik != '' THEN 1 ELSE 0 END
WHERE mechanics_json IS NULL AND mekanik IS NOT NULL AND mekanik != '';

-- Add comments for documentation
ALTER TABLE spk_unit_stages COMMENT = 'Updated to support multi-mechanic workflow - mechanics_json stores detailed assignments';

-- Create view for easy mechanic retrieval
CREATE OR REPLACE VIEW `v_spk_stage_mechanics` AS
SELECT 
    sus.id as stage_id,
    sus.spk_id,
    sus.unit_index,
    sus.stage_name,
    sus.mekanik as legacy_mechanic_name,
    sus.mechanics_json,
    sus.primary_mechanic_id,
    sus.mechanics_count,
    emp.staff_name as primary_mechanic_name,
    emp.staff_role as primary_mechanic_role,
    sus.tanggal_approve,
    sus.estimasi_mulai,
    sus.estimasi_selesai
FROM spk_unit_stages sus
LEFT JOIN employees emp ON sus.primary_mechanic_id = emp.id
WHERE sus.tanggal_approve IS NOT NULL;