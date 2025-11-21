-- Migration: Add departemen_id to areas table
-- Date: 2025-11-21
-- Description: Add foreign key to departemen table for filtering areas by department

-- Step 1: Add departemen_id column to areas table
ALTER TABLE `areas` 
ADD COLUMN IF NOT EXISTS `departemen_id` INT(11) NULL 
COMMENT 'FK to departemen table for filtering areas by department' 
AFTER `area_description`;

-- Step 2: Add index for better query performance
ALTER TABLE `areas` 
ADD INDEX IF NOT EXISTS `idx_areas_departemen` (`departemen_id`);

-- Step 3: Add foreign key constraint
-- Note: Drop existing constraint if it exists
ALTER TABLE `areas` 
DROP FOREIGN KEY IF EXISTS `fk_areas_departemen`;

ALTER TABLE `areas` 
ADD CONSTRAINT `fk_areas_departemen` 
FOREIGN KEY (`departemen_id`) 
REFERENCES `departemen` (`id_departemen`) 
ON DELETE SET NULL 
ON UPDATE CASCADE;

-- Step 4: Update existing areas with departemen_id based on their units
-- This will set departemen_id for areas that have units
UPDATE `areas` a
INNER JOIN (
    SELECT DISTINCT 
        a.id as area_id,
        iu.departemen_id
    FROM areas a
    INNER JOIN customer_locations cl ON cl.area_id = a.id
    INNER JOIN kontrak k ON k.customer_location_id = cl.id
    INNER JOIN inventory_unit iu ON iu.kontrak_id = k.id
    WHERE iu.departemen_id IS NOT NULL
    GROUP BY a.id, iu.departemen_id
    HAVING COUNT(DISTINCT iu.departemen_id) = 1  -- Only update if area has units from single department
) area_dept ON area_dept.area_id = a.id
SET a.departemen_id = area_dept.departemen_id
WHERE a.departemen_id IS NULL;

