-- ============================================================================
-- AREA MANAGEMENT ENHANCEMENT - HYBRID APPROACH
-- Date: December 10, 2025
-- Purpose: Enable flexible area-department management for Central vs Branch
-- ============================================================================

-- ============================================================================
-- STEP 1: ADD NEW COLUMNS
-- ============================================================================

-- Add area_type to distinguish Central HQ vs Branch locations
ALTER TABLE `areas` 
ADD COLUMN `area_type` ENUM('CENTRAL', 'BRANCH') DEFAULT 'BRANCH' 
COMMENT 'CENTRAL=Pusat (per-dept focus), BRANCH=Cabang (all-dept)' 
AFTER `area_description`;

-- Add department_scope to control employee access per department
ALTER TABLE `area_employee_assignments` 
ADD COLUMN `department_scope` VARCHAR(100) DEFAULT 'ALL' 
COMMENT 'ALL=all departments, ELECTRIC=electric only, DIESEL=diesel only, DIESEL,GASOLINE=diesel+gasoline' 
AFTER `notes`;

-- Add index for better performance
ALTER TABLE `areas` 
ADD INDEX `idx_area_type` (`area_type`);

ALTER TABLE `area_employee_assignments` 
ADD INDEX `idx_department_scope` (`department_scope`);

-- ============================================================================
-- STEP 2: UPDATE EXISTING DATA
-- ============================================================================

-- Mark existing Pusat/HQ areas as CENTRAL
UPDATE `areas` 
SET `area_type` = 'CENTRAL' 
WHERE `area_code` = 'HQ' 
   OR `area_name` LIKE '%Pusat%' 
   OR `area_name` LIKE '%Jakarta%'
   OR `area_code` IN ('A', 'B', 'C', 'D');

-- Mark other areas as BRANCH
UPDATE `areas` 
SET `area_type` = 'BRANCH' 
WHERE `area_type` IS NULL 
   OR (`area_type` = 'BRANCH' AND `area_code` NOT IN ('A', 'B', 'C', 'D', 'HQ'));

-- ============================================================================
-- STEP 3: SET DEFAULT DEPARTMENT SCOPE FOR EXISTING ASSIGNMENTS
-- ============================================================================

-- For Central HQ areas: Set scope based on area's departemen_id
UPDATE `area_employee_assignments` aea
INNER JOIN `areas` a ON aea.area_id = a.id
INNER JOIN `employees` e ON aea.employee_id = e.id
SET aea.department_scope = CASE 
    WHEN e.departemen_id = 1 THEN 'DIESEL,GASOLINE'  -- DIESEL staff handle DIESEL+GASOLINE
    WHEN e.departemen_id = 2 THEN 'ELECTRIC'          -- ELECTRIC staff handle ELECTRIC only
    WHEN e.departemen_id = 3 THEN 'GASOLINE'          -- GASOLINE staff (rare case)
    ELSE 'ALL'
END
WHERE a.area_type = 'CENTRAL' 
  AND aea.department_scope = 'ALL'
  AND e.departemen_id IS NOT NULL;

-- For Branch areas: Keep ALL (default - handle all departments)
UPDATE `area_employee_assignments` aea
INNER JOIN `areas` a ON aea.area_id = a.id
SET aea.department_scope = 'ALL'
WHERE a.area_type = 'BRANCH';

-- ============================================================================
-- STEP 4: REMOVE DEPARTEMEN_ID FROM AREAS (FULLY DECOUPLE)
-- ============================================================================
-- Areas are now pure geographical locations, not tied to specific departments
-- Department access is controlled via area_employee_assignments.department_scope

-- Drop the departemen_id column and its index
ALTER TABLE `areas` DROP INDEX `idx_areas_departemen`;
ALTER TABLE `areas` DROP COLUMN `departemen_id`;

-- ============================================================================
-- STEP 5: CREATE SAMPLE BRANCH AREAS (OPTIONAL)
-- ============================================================================

-- Add Surabaya branch if not exists
INSERT INTO `areas` (`area_code`, `area_name`, `area_description`, `area_type`, `is_active`) 
VALUES ('SBY', 'Surabaya', 'Cabang Surabaya - Handle all departments', 'BRANCH', 1)
ON DUPLICATE KEY UPDATE 
    area_type = 'BRANCH',
    area_description = 'Cabang Surabaya - Handle all departments';

-- Add Perawang branch if not exists
INSERT INTO `areas` (`area_code`, `area_name`, `area_description`, `area_type`, `is_active`) 
VALUES ('PRW', 'Perawang', 'Cabang Perawang - Handle all departments', 'BRANCH', 1)
ON DUPLICATE KEY UPDATE 
    area_type = 'BRANCH',
    area_description = 'Cabang Perawang - Handle all departments';

-- Add Semarang branch if not exists
INSERT INTO `areas` (`area_code`, `area_name`, `area_description`, `area_type`, `is_active`) 
VALUES ('SMG', 'Semarang', 'Cabang Semarang - Handle all departments', 'BRANCH', 1)
ON DUPLICATE KEY UPDATE 
    area_type = 'BRANCH',
    area_description = 'Cabang Semarang - Handle all departments';

-- ============================================================================
-- STEP 6: CREATE SAMPLE EMPLOYEES FOR BRANCH AREAS (OPTIONAL)
-- ============================================================================

-- Sample: Admin Surabaya (no specific department - handle ALL)
INSERT INTO `employees` (`staff_code`, `staff_name`, `staff_role`, `departemen_id`, `is_active`) 
VALUES ('ADM-SBY-001', 'Admin Surabaya', 'ADMIN', NULL, 1)
ON DUPLICATE KEY UPDATE staff_name = 'Admin Surabaya';

-- Sample: Admin Perawang (no specific department - handle ALL)
INSERT INTO `employees` (`staff_code`, `staff_name`, `staff_role`, `departemen_id`, `is_active`) 
VALUES ('ADM-PRW-001', 'Admin Perawang', 'ADMIN', NULL, 1)
ON DUPLICATE KEY UPDATE staff_name = 'Admin Perawang';

-- Sample: Admin Semarang (no specific department - handle ALL)
INSERT INTO `employees` (`staff_code`, `staff_name`, `staff_role`, `departemen_id`, `is_active`) 
VALUES ('ADM-SMG-001', 'Admin Semarang', 'ADMIN', NULL, 1)
ON DUPLICATE KEY UPDATE staff_name = 'Admin Semarang';

-- ============================================================================
-- STEP 7: ASSIGN BRANCH ADMINS TO THEIR AREAS WITH FULL SCOPE
-- ============================================================================

-- Assign Admin Surabaya to Surabaya area with ALL scope
INSERT INTO `area_employee_assignments` 
    (`area_id`, `employee_id`, `assignment_type`, `start_date`, `department_scope`, `is_active`)
SELECT 
    a.id as area_id,
    e.id as employee_id,
    'PRIMARY' as assignment_type,
    CURDATE() as start_date,
    'ALL' as department_scope,
    1 as is_active
FROM `areas` a
CROSS JOIN `employees` e
WHERE a.area_code = 'SBY' 
  AND e.staff_code = 'ADM-SBY-001'
ON DUPLICATE KEY UPDATE 
    department_scope = 'ALL',
    is_active = 1;

-- Assign Admin Perawang to Perawang area with ALL scope
INSERT INTO `area_employee_assignments` 
    (`area_id`, `employee_id`, `assignment_type`, `start_date`, `department_scope`, `is_active`)
SELECT 
    a.id as area_id,
    e.id as employee_id,
    'PRIMARY' as assignment_type,
    CURDATE() as start_date,
    'ALL' as department_scope,
    1 as is_active
FROM `areas` a
CROSS JOIN `employees` e
WHERE a.area_code = 'PRW' 
  AND e.staff_code = 'ADM-PRW-001'
ON DUPLICATE KEY UPDATE 
    department_scope = 'ALL',
    is_active = 1;

-- Assign Admin Semarang to Semarang area with ALL scope
INSERT INTO `area_employee_assignments` 
    (`area_id`, `employee_id`, `assignment_type`, `start_date`, `department_scope`, `is_active`)
SELECT 
    a.id as area_id,
    e.id as employee_id,
    'PRIMARY' as assignment_type,
    CURDATE() as start_date,
    'ALL' as department_scope,
    1 as is_active
FROM `areas` a
CROSS JOIN `employees` e
WHERE a.area_code = 'SMG' 
  AND e.staff_code = 'ADM-SMG-001'
ON DUPLICATE KEY UPDATE 
    department_scope = 'ALL',
    is_active = 1;

-- ============================================================================
-- STEP 8: VERIFICATION QUERIES
-- ============================================================================

-- View all areas with their type
SELECT 
    id, 
    area_code, 
    area_name, 
    area_type,
    is_active
FROM `areas`
ORDER BY area_type, area_name;

-- View all employee assignments with department scope
SELECT 
    aea.id,
    a.area_code,
    a.area_name,
    a.area_type,
    e.staff_code,
    e.staff_name,
    e.staff_role,
    d.nama_departemen as employee_dept,
    aea.department_scope,
    aea.assignment_type,
    aea.is_active
FROM `area_employee_assignments` aea
INNER JOIN `areas` a ON aea.area_id = a.id
INNER JOIN `employees` e ON aea.employee_id = e.id
LEFT JOIN `departemen` d ON e.departemen_id = d.id_departemen
WHERE aea.is_active = 1
ORDER BY a.area_type, a.area_name, e.staff_name;

-- ============================================================================
-- ROLLBACK SCRIPT (If needed)
-- ============================================================================
/*
-- Remove new columns
ALTER TABLE `areas` DROP COLUMN `area_type`;
ALTER TABLE `areas` DROP INDEX `idx_area_type`;

ALTER TABLE `area_employee_assignments` DROP COLUMN `department_scope`;
ALTER TABLE `area_employee_assignments` DROP INDEX `idx_department_scope`;

-- Restore departemen_id column (if you want to rollback completely)
ALTER TABLE `areas` 
ADD COLUMN `departemen_id` INT(11) DEFAULT NULL AFTER `area_description`,
ADD INDEX `idx_areas_departemen` (`departemen_id`);
*/

-- ============================================================================
-- END OF MIGRATION
-- ============================================================================
