-- ============================================================================
-- AREA MANAGEMENT - UPDATE EXISTING DATA & REMOVE DEPARTEMEN_ID
-- Date: December 10, 2025
-- ============================================================================

-- ============================================================================
-- STEP 1: UPDATE EXISTING AREA TYPES
-- ============================================================================

-- Mark existing Pusat/HQ areas as CENTRAL
UPDATE `areas` 
SET `area_type` = 'CENTRAL' 
WHERE `area_code` IN ('A', 'B', 'C', 'D', 'HQ')
   OR `area_name` LIKE '%Pusat%' 
   OR `area_name` LIKE '%Jakarta%';

-- Mark other areas as BRANCH
UPDATE `areas` 
SET `area_type` = 'BRANCH' 
WHERE `area_type` IS NULL OR `area_type` = 'BRANCH';

-- ============================================================================
-- STEP 2: UPDATE EXISTING EMPLOYEE ASSIGNMENTS WITH DEPARTMENT SCOPE
-- ============================================================================

-- For Central HQ areas: Set scope based on employee's departemen_id
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
  AND (aea.department_scope = 'ALL' OR aea.department_scope IS NULL)
  AND e.departemen_id IS NOT NULL;

-- For Branch areas: Keep ALL (default - handle all departments)
UPDATE `area_employee_assignments` aea
INNER JOIN `areas` a ON aea.area_id = a.id
SET aea.department_scope = 'ALL'
WHERE a.area_type = 'BRANCH';

-- ============================================================================
-- STEP 3: REMOVE DEPARTEMEN_ID FROM AREAS
-- ============================================================================

-- Drop foreign key constraints first
SET @fkExists1 = (SELECT COUNT(1) 
    FROM information_schema.table_constraints 
    WHERE table_schema = DATABASE() 
    AND table_name = 'areas' 
    AND constraint_name = 'areas_ibfk_1'
    AND constraint_type = 'FOREIGN KEY');

SET @dropFK1 = IF(@fkExists1 > 0, 
    'ALTER TABLE `areas` DROP FOREIGN KEY `areas_ibfk_1`', 
    'SELECT "FK areas_ibfk_1 does not exist" AS message');

PREPARE stmt1 FROM @dropFK1;
EXECUTE stmt1;
DEALLOCATE PREPARE stmt1;

SET @fkExists2 = (SELECT COUNT(1) 
    FROM information_schema.table_constraints 
    WHERE table_schema = DATABASE() 
    AND table_name = 'areas' 
    AND constraint_name = 'fk_areas_departemen'
    AND constraint_type = 'FOREIGN KEY');

SET @dropFK2 = IF(@fkExists2 > 0, 
    'ALTER TABLE `areas` DROP FOREIGN KEY `fk_areas_departemen`', 
    'SELECT "FK fk_areas_departemen does not exist" AS message');

PREPARE stmt2 FROM @dropFK2;
EXECUTE stmt2;
DEALLOCATE PREPARE stmt2;

-- Check if index exists before dropping
SET @indexExists = (SELECT COUNT(1) 
    FROM information_schema.statistics 
    WHERE table_schema = DATABASE() 
    AND table_name = 'areas' 
    AND index_name = 'idx_areas_departemen');

SET @dropIndexSQL = IF(@indexExists > 0, 
    'ALTER TABLE `areas` DROP INDEX `idx_areas_departemen`', 
    'SELECT "Index does not exist" AS message');

PREPARE stmt FROM @dropIndexSQL;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Now drop the column
ALTER TABLE `areas` DROP COLUMN `departemen_id`;

-- ============================================================================
-- STEP 4: ADD SAMPLE BRANCH AREAS IF NOT EXISTS
-- ============================================================================

-- Surabaya
INSERT INTO `areas` (`area_code`, `area_name`, `area_description`, `area_type`, `is_active`) 
VALUES ('SBY', 'Surabaya', 'Cabang Surabaya - Handle all departments', 'BRANCH', 1)
ON DUPLICATE KEY UPDATE 
    area_type = 'BRANCH',
    area_description = 'Cabang Surabaya - Handle all departments';

-- Perawang
INSERT INTO `areas` (`area_code`, `area_name`, `area_description`, `area_type`, `is_active`) 
VALUES ('PRW', 'Perawang', 'Cabang Perawang - Handle all departments', 'BRANCH', 1)
ON DUPLICATE KEY UPDATE 
    area_type = 'BRANCH',
    area_description = 'Cabang Perawang - Handle all departments';

-- Semarang
INSERT INTO `areas` (`area_code`, `area_name`, `area_description`, `area_type`, `is_active`) 
VALUES ('SMG', 'Semarang', 'Cabang Semarang - Handle all departments', 'BRANCH', 1)
ON DUPLICATE KEY UPDATE 
    area_type = 'BRANCH',
    area_description = 'Cabang Semarang - Handle all departments';

-- ============================================================================
-- STEP 5: VERIFICATION
-- ============================================================================

SELECT '=== AREAS BY TYPE ===' AS Info;
SELECT 
    area_type,
    COUNT(*) as total
FROM `areas`
GROUP BY area_type;

SELECT '=== SAMPLE AREAS ===' AS Info;
SELECT 
    id, 
    area_code, 
    area_name, 
    area_type,
    is_active
FROM `areas`
ORDER BY area_type, area_name
LIMIT 20;

SELECT '=== EMPLOYEE ASSIGNMENTS WITH SCOPE ===' AS Info;
SELECT 
    a.area_code,
    a.area_name,
    a.area_type,
    e.staff_name,
    e.staff_role,
    d.nama_departemen as employee_dept,
    aea.department_scope,
    aea.is_active
FROM `area_employee_assignments` aea
INNER JOIN `areas` a ON aea.area_id = a.id
INNER JOIN `employees` e ON aea.employee_id = e.id
LEFT JOIN `departemen` d ON e.departemen_id = d.id_departemen
WHERE aea.is_active = 1
ORDER BY a.area_type, a.area_name, e.staff_name
LIMIT 20;

-- ============================================================================
-- END OF MIGRATION
-- ============================================================================
