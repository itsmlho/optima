-- ===============================================
-- MULTI-MECHANIC DATABASE DESIGN FOR SPK SYSTEM
-- ===============================================

-- Current Analysis:
-- 1. spk_unit_stages.mekanik column is VARCHAR(255) - currently stores single mechanic name
-- 2. Each stage (persiapan_unit, fabrikasi, painting, pdi) needs different role restrictions
-- 3. Requirements:
--    - Unit Preparation: max 2 MECHANIC_UNIT_PREP + 2 HELPER
--    - Fabrication: max 2 MECHANIC_FABRICATION + 2 HELPER  
--    - Painting: max 2 mechanics (any) + 2 HELPER
--    - PDI: max 2 FOREMAN/SUPERVISOR + 1 HELPER

-- SOLUTION 1: Create separate junction table for multi-mechanic assignments
-- This preserves current structure and adds flexibility

CREATE TABLE IF NOT EXISTS `spk_stage_mechanics` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `spk_id` INT NOT NULL,
    `unit_index` INT NOT NULL,
    `stage_name` ENUM('persiapan_unit', 'fabrikasi', 'painting', 'pdi') NOT NULL,
    `employee_id` INT NOT NULL,
    `employee_role` ENUM('MECHANIC_UNIT_PREP', 'MECHANIC_FABRICATION', 'MECHANIC_SERVICE_AREA', 'FOREMAN', 'SUPERVISOR', 'HELPER') NOT NULL,
    `is_primary` TINYINT(1) DEFAULT 0 COMMENT 'Primary mechanic for this stage',
    `assigned_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `assigned_by` INT NULL COMMENT 'User ID who assigned this mechanic',
    
    -- Foreign keys
    FOREIGN KEY (`spk_id`) REFERENCES `spk`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`employee_id`) REFERENCES `employees`(`id`) ON DELETE CASCADE,
    
    -- Unique constraint to prevent duplicate assignments
    UNIQUE KEY `unique_stage_employee` (`spk_id`, `unit_index`, `stage_name`, `employee_id`),
    
    -- Index for performance
    INDEX `idx_spk_stage` (`spk_id`, `unit_index`, `stage_name`),
    INDEX `idx_employee` (`employee_id`)
);

-- SOLUTION 2: Modify spk_unit_stages to support JSON mechanic data
-- This approach stores mechanic data as JSON in existing structure

-- Add new columns to spk_unit_stages for better mechanic management
ALTER TABLE `spk_unit_stages` 
ADD COLUMN `mechanics_json` JSON NULL COMMENT 'JSON array of assigned mechanics with roles' AFTER `mekanik`,
ADD COLUMN `primary_mechanic_id` INT NULL COMMENT 'Primary mechanic employee ID' AFTER `mechanics_json`,
ADD COLUMN `mechanics_count` INT DEFAULT 0 COMMENT 'Total number of assigned mechanics' AFTER `primary_mechanic_id`;

-- Add foreign key for primary mechanic
ALTER TABLE `spk_unit_stages` 
ADD FOREIGN KEY `fk_primary_mechanic` (`primary_mechanic_id`) REFERENCES `employees`(`id`) ON DELETE SET NULL;

-- Example JSON structure for mechanics_json:
-- {
--   "mechanics": [
--     {"id": 8, "name": "BAGUS", "role": "MECHANIC_UNIT_PREP", "is_primary": true},
--     {"id": 11, "name": "Wahyu", "role": "MECHANIC_UNIT_PREP", "is_primary": false}
--   ],
--   "helpers": [
--     {"id": 15, "name": "Helper1", "role": "HELPER", "is_primary": false},
--     {"id": 16, "name": "Helper2", "role": "HELPER", "is_primary": false}
--   ],
--   "assigned_at": "2025-12-15 10:30:00",
--   "assigned_by": 1
-- }

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

-- Migration strategy: Update existing records to new format
-- This will preserve existing single mechanic assignments

UPDATE spk_unit_stages 
SET mechanics_json = JSON_OBJECT(
    'mechanics', JSON_ARRAY(
        CASE 
            WHEN mekanik IS NOT NULL AND mekanik != '' THEN 
                JSON_OBJECT('name', mekanik, 'role', 'LEGACY', 'is_primary', true)
            ELSE NULL
        END
    ),
    'helpers', JSON_ARRAY(),
    'legacy_migration', true,
    'migrated_at', NOW()
),
mechanics_count = CASE WHEN mekanik IS NOT NULL AND mekanik != '' THEN 1 ELSE 0 END
WHERE mechanics_json IS NULL;

-- Role validation constraints based on stage requirements
DELIMITER $$

-- Trigger to validate mechanic assignments based on stage rules
CREATE TRIGGER `validate_stage_mechanic_assignment` 
BEFORE INSERT ON `spk_stage_mechanics`
FOR EACH ROW
BEGIN
    DECLARE mechanic_count INT DEFAULT 0;
    DECLARE helper_count INT DEFAULT 0;
    DECLARE role_allowed BOOLEAN DEFAULT FALSE;
    
    -- Count existing assignments for this stage
    SELECT 
        COUNT(CASE WHEN employee_role != 'HELPER' THEN 1 END),
        COUNT(CASE WHEN employee_role = 'HELPER' THEN 1 END)
    INTO mechanic_count, helper_count
    FROM spk_stage_mechanics 
    WHERE spk_id = NEW.spk_id 
      AND unit_index = NEW.unit_index 
      AND stage_name = NEW.stage_name;
    
    -- Validate based on stage requirements
    CASE NEW.stage_name
        WHEN 'persiapan_unit' THEN
            SET role_allowed = (NEW.employee_role IN ('MECHANIC_UNIT_PREP', 'HELPER'));
            IF NEW.employee_role != 'HELPER' AND mechanic_count >= 2 THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Maximum 2 mechanics allowed for Unit Preparation';
            END IF;
            IF NEW.employee_role = 'HELPER' AND helper_count >= 2 THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Maximum 2 helpers allowed for Unit Preparation';
            END IF;
            
        WHEN 'fabrikasi' THEN
            SET role_allowed = (NEW.employee_role IN ('MECHANIC_FABRICATION', 'HELPER'));
            IF NEW.employee_role != 'HELPER' AND mechanic_count >= 2 THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Maximum 2 mechanics allowed for Fabrication';
            END IF;
            IF NEW.employee_role = 'HELPER' AND helper_count >= 2 THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Maximum 2 helpers allowed for Fabrication';
            END IF;
            
        WHEN 'painting' THEN
            SET role_allowed = (NEW.employee_role IN ('MECHANIC_UNIT_PREP', 'MECHANIC_FABRICATION', 'MECHANIC_SERVICE_AREA', 'HELPER'));
            IF NEW.employee_role != 'HELPER' AND mechanic_count >= 2 THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Maximum 2 mechanics allowed for Painting';
            END IF;
            IF NEW.employee_role = 'HELPER' AND helper_count >= 2 THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Maximum 2 helpers allowed for Painting';
            END IF;
            
        WHEN 'pdi' THEN
            SET role_allowed = (NEW.employee_role IN ('FOREMAN', 'SUPERVISOR', 'HELPER'));
            IF NEW.employee_role != 'HELPER' AND mechanic_count >= 2 THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Maximum 2 foremen/supervisors allowed for PDI';
            END IF;
            IF NEW.employee_role = 'HELPER' AND helper_count >= 1 THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Maximum 1 helper allowed for PDI';
            END IF;
    END CASE;
    
    IF NOT role_allowed THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Employee role not allowed for this stage';
    END IF;
END$$

DELIMITER ;

-- Test data insertion examples
-- INSERT INTO spk_stage_mechanics (spk_id, unit_index, stage_name, employee_id, employee_role, is_primary, assigned_by) VALUES
-- (1, 1, 'persiapan_unit', 8, 'MECHANIC_UNIT_PREP', 1, 1),  -- Primary mechanic
-- (1, 1, 'persiapan_unit', 11, 'MECHANIC_UNIT_PREP', 0, 1), -- Secondary mechanic  
-- (1, 1, 'persiapan_unit', 15, 'HELPER', 0, 1),             -- Helper 1
-- (1, 1, 'persiapan_unit', 16, 'HELPER', 0, 1);             -- Helper 2

-- Helper functions for PHP integration
-- Function to get formatted mechanic list for display
DELIMITER $$

CREATE FUNCTION `get_stage_mechanics_display`(
    p_spk_id INT, 
    p_unit_index INT, 
    p_stage_name VARCHAR(50)
) RETURNS TEXT READS SQL DATA DETERMINISTIC
BEGIN
    DECLARE result TEXT DEFAULT '';
    DECLARE mechanic_names TEXT DEFAULT '';
    DECLARE helper_names TEXT DEFAULT '';
    
    -- Get mechanics (non-helpers)
    SELECT GROUP_CONCAT(e.staff_name SEPARATOR ', ') INTO mechanic_names
    FROM spk_stage_mechanics ssm
    JOIN employees e ON ssm.employee_id = e.id
    WHERE ssm.spk_id = p_spk_id 
      AND ssm.unit_index = p_unit_index 
      AND ssm.stage_name = p_stage_name
      AND ssm.employee_role != 'HELPER'
    ORDER BY ssm.is_primary DESC, e.staff_name;
    
    -- Get helpers
    SELECT GROUP_CONCAT(e.staff_name SEPARATOR ', ') INTO helper_names
    FROM spk_stage_mechanics ssm
    JOIN employees e ON ssm.employee_id = e.id
    WHERE ssm.spk_id = p_spk_id 
      AND ssm.unit_index = p_unit_index 
      AND ssm.stage_name = p_stage_name
      AND ssm.employee_role = 'HELPER'
    ORDER BY e.staff_name;
    
    -- Format result
    IF mechanic_names IS NOT NULL THEN
        SET result = CONCAT('Mechanics: ', mechanic_names);
        IF helper_names IS NOT NULL THEN
            SET result = CONCAT(result, ' | Helpers: ', helper_names);
        END IF;
    ELSEIF helper_names IS NOT NULL THEN
        SET result = CONCAT('Helpers: ', helper_names);
    END IF;
    
    RETURN COALESCE(result, '');
END$$

DELIMITER ;

-- Create indexes for performance optimization
CREATE INDEX idx_spk_stage_mechanics_lookup ON spk_stage_mechanics(spk_id, unit_index, stage_name, employee_role);
CREATE INDEX idx_spk_stage_mechanics_employee ON spk_stage_mechanics(employee_id, stage_name);

-- Comments for documentation
ALTER TABLE spk_stage_mechanics COMMENT = 'Multi-mechanic assignments for SPK stages with role-based validation';
ALTER TABLE spk_unit_stages COMMENT = 'Updated to support multi-mechanic workflow - mechanics_json stores detailed assignments';