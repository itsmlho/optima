-- ============================================================================
-- Employee Assignment: Assign DIESEL and ELECTRIC employees to CENTRAL areas
-- Date: 2026-02-20
-- Description: Assign employees from DIESEL department to all D-* CENTRAL areas
--              and ELECTRIC employees to all E-* CENTRAL areas
-- 
-- IMPORTANT: 
-- - Review employee list before executing
-- - Customize employee_id list based on your actual "admin pusat" users
-- - This script assigns with PRIMARY assignment type
-- - Start date is set to today (2026-02-20)
-- ============================================================================

-- ============================================================================
-- OPTION 1: ASSIGN SPECIFIC EMPLOYEES (RECOMMENDED)
-- Manually specify which employees should be assigned to all CENTRAL areas
-- ============================================================================

-- Example: Assign specific DIESEL admin employees to all D-* CENTRAL areas
-- Replace employee IDs (3, 7, 8) with actual admin pusat DIESEL IDs

/*
INSERT INTO area_employee_assignments (area_id, employee_id, assignment_type, start_date, is_active, department_scope, notes)
SELECT 
    a.id as area_id,
    e.id as employee_id,
    'PRIMARY' as assignment_type,
    '2026-02-20' as start_date,
    1 as is_active,
    'DIESEL' as department_scope,
    CONCAT('Auto-assigned to CENTRAL area ', a.area_code, ' on 2026-02-20') as notes
FROM areas a
CROSS JOIN employees e
WHERE a.area_code LIKE 'D-%'
  AND a.area_type = 'CENTRAL'
  AND a.is_active = 1
  AND e.id IN (3, 7, 8)  -- ⚠️ CUSTOMIZE: Replace with actual DIESEL admin IDs
  AND e.is_active = 1
  AND NOT EXISTS (
      SELECT 1 FROM area_employee_assignments aea
      WHERE aea.area_id = a.id AND aea.employee_id = e.id AND aea.is_active = 1
  );
*/

-- Example: Assign specific ELECTRIC admin employees to all E-* CENTRAL areas
-- Replace employee IDs (1, 2, 18) with actual admin pusat ELECTRIC IDs

/*
INSERT INTO area_employee_assignments (area_id, employee_id, assignment_type, start_date, is_active, department_scope, notes)
SELECT 
    a.id as area_id,
    e.id as employee_id,
    'PRIMARY' as assignment_type,
    '2026-02-20' as start_date,
    1 as is_active,
    'ELECTRIC' as department_scope,
    CONCAT('Auto-assigned to CENTRAL area ', a.area_code, ' on 2026-02-20') as notes
FROM areas a
CROSS JOIN employees e
WHERE a.area_code LIKE 'E-%'
  AND a.area_type = 'CENTRAL'
  AND a.is_active = 1
  AND e.id IN (1, 2, 18)  -- ⚠️ CUSTOMIZE: Replace with actual ELECTRIC admin IDs
  AND e.is_active = 1
  AND NOT EXISTS (
      SELECT 1 FROM area_employee_assignments aea
      WHERE aea.area_id = a.id AND aea.employee_id = e.id AND aea.is_active = 1
  );
*/

-- ============================================================================
-- OPTION 2: ASSIGN ALL EMPLOYEES FROM DEPARTMENT (USE WITH CAUTION)
-- Automatically assigns ALL active employees from each department to all CENTRAL areas
-- This might not be desired if you have mechanics vs admin distinction
-- ============================================================================

-- Option 2A: Assign ALL DIESEL department employees to D-* CENTRAL areas
/*
INSERT INTO area_employee_assignments (area_id, employee_id, assignment_type, start_date, is_active, department_scope, notes)
SELECT 
    a.id as area_id,
    e.id as employee_id,
    'PRIMARY' as assignment_type,
    '2026-02-20' as start_date,
    1 as is_active,
    'DIESEL' as department_scope,
    CONCAT('Auto-assigned to CENTRAL area ', a.area_code, ' on 2026-02-20') as notes
FROM areas a
CROSS JOIN employees e
INNER JOIN departemen d ON e.departemen_id = d.id_departemen
WHERE a.area_code LIKE 'D-%'
  AND a.area_type = 'CENTRAL'
  AND a.is_active = 1
  AND d.nama_departemen = 'DIESEL'
  AND e.is_active = 1
  AND NOT EXISTS (
      SELECT 1 FROM area_employee_assignments aea
      WHERE aea.area_id = a.id AND aea.employee_id = e.id AND aea.is_active = 1
  );
*/

-- Option 2B: Assign ALL ELECTRIC department employees to E-* CENTRAL areas
/*
INSERT INTO area_employee_assignments (area_id, employee_id, assignment_type, start_date, is_active, department_scope, notes)
SELECT 
    a.id as area_id,
    e.id as employee_id,
    'PRIMARY' as assignment_type,
    '2026-02-20' as start_date,
    1 as is_active,
    'ELECTRIC' as department_scope,
    CONCAT('Auto-assigned to CENTRAL area ', a.area_code, ' on 2026-02-20') as notes
FROM areas a
CROSS JOIN employees e
INNER JOIN departemen d ON e.departemen_id = d.id_departemen
WHERE a.area_code LIKE 'E-%'
  AND a.area_type = 'CENTRAL'
  AND a.is_active = 1
  AND d.nama_departemen = 'ELECTRIC'
  AND e.is_active = 1
  AND NOT EXISTS (
      SELECT 1 FROM area_employee_assignments aea
      WHERE aea.area_id = a.id AND aea.employee_id = e.id AND aea.is_active = 1
  );
*/

-- ============================================================================
-- HELPER QUERIES
-- Use these to identify which employees should be assigned
-- ============================================================================

-- List all DIESEL employees
SELECT 'DIESEL Employees:' as info;
SELECT 
    e.id, 
    e.staff_name, 
    e.email,
    e.role,
    COUNT(DISTINCT aea.area_id) as current_area_count,
    GROUP_CONCAT(DISTINCT a.area_code ORDER BY a.area_code SEPARATOR ', ') as current_areas
FROM employees e
INNER JOIN departemen d ON e.departemen_id = d.id_departemen
LEFT JOIN area_employee_assignments aea ON e.id = aea.employee_id AND aea.is_active = 1
LEFT JOIN areas a ON aea.area_id = a.id
WHERE d.nama_departemen = 'DIESEL'
  AND e.is_active = 1
GROUP BY e.id, e.staff_name, e.email, e.role
ORDER BY e.staff_name;

-- List all ELECTRIC employees
SELECT 'ELECTRIC Employees:' as info;
SELECT 
    e.id, 
    e.staff_name, 
    e.email,
    e.role,
    COUNT(DISTINCT aea.area_id) as current_area_count,
    GROUP_CONCAT(DISTINCT a.area_code ORDER BY a.area_code SEPARATOR ', ') as current_areas
FROM employees e
INNER JOIN departemen d ON e.departemen_id = d.id_departemen
LEFT JOIN area_employee_assignments aea ON e.id = aea.employee_id AND aea.is_active = 1
LEFT JOIN areas a ON aea.area_id = a.id
WHERE d.nama_departemen = 'ELECTRIC'
  AND e.is_active = 1
GROUP BY e.id, e.staff_name, e.email, e.role
ORDER BY e.staff_name;

-- Count how many new D-* CENTRAL areas exist
SELECT 'New D-* CENTRAL areas count:' as info, COUNT(*) as count
FROM areas 
WHERE area_code LIKE 'D-%' 
  AND area_type = 'CENTRAL' 
  AND is_active = 1;

-- Count how many new E-* CENTRAL areas exist
SELECT 'New E-* CENTRAL areas count:' as info, COUNT(*) as count
FROM areas 
WHERE area_code LIKE 'E-%' 
  AND area_type = 'CENTRAL' 
  AND is_active = 1;

-- ============================================================================
-- VERIFICATION QUERIES
-- Run these after assignment to verify results
-- ============================================================================

-- Verify DIESEL assignments
SELECT 'DIESEL employee assignments to D-* CENTRAL areas:' as info;
SELECT 
    e.id,
    e.staff_name,
    COUNT(DISTINCT a.id) as total_d_central_areas,
    GROUP_CONCAT(DISTINCT a.area_code ORDER BY a.area_code SEPARATOR ', ') as assigned_d_areas
FROM employees e
INNER JOIN departemen d ON e.departemen_id = d.id_departemen
LEFT JOIN area_employee_assignments aea ON e.id = aea.employee_id AND aea.is_active = 1
LEFT JOIN areas a ON aea.area_id = a.id AND a.area_code LIKE 'D-%' AND a.area_type = 'CENTRAL'
WHERE d.nama_departemen = 'DIESEL'
  AND e.is_active = 1
GROUP BY e.id, e.staff_name
ORDER BY e.staff_name;

-- Verify ELECTRIC assignments
SELECT 'ELECTRIC employee assignments to E-* CENTRAL areas:' as info;
SELECT 
    e.id,
    e.staff_name,
    COUNT(DISTINCT a.id) as total_e_central_areas,
    GROUP_CONCAT(DISTINCT a.area_code ORDER BY a.area_code SEPARATOR ', ') as assigned_e_areas
FROM employees e
INNER JOIN departemen d ON e.departemen_id = d.id_departemen
LEFT JOIN area_employee_assignments aea ON e.id = aea.employee_id AND aea.is_active = 1
LEFT JOIN areas a ON aea.area_id = a.id AND a.area_code LIKE 'E-%' AND a.area_type = 'CENTRAL'
WHERE d.nama_departemen = 'ELECTRIC'
  AND e.is_active = 1
GROUP BY e.id, e.staff_name
ORDER BY e.staff_name;

-- ============================================================================
-- INSTRUCTIONS
-- ============================================================================
/*
HOW TO USE THIS SCRIPT:

1. IDENTIFY ADMIN USERS:
   - Run the HELPER QUERIES section to see list of employees
   - Identify which employees are "admin pusat" for each department
   - Note down their employee IDs

2. CHOOSE OPTION:
   - Option 1 (Recommended): Manually specify admin employee IDs
   - Option 2: Auto-assign all employees from department

3. CUSTOMIZE & EXECUTE:
   - Uncomment the INSERT statement for your chosen option
   - Update employee ID list in the IN clause (for Option 1)
   - Run the script: mysql -u root -h 127.0.0.1 optima_ci < this_file.sql

4. VERIFY:
   - Run VERIFICATION QUERIES to check assignments
   - Check in UI at Service Area Management

EXAMPLE CALCULATION:
- If you assign 3 DIESEL employees to 61 D-* CENTRAL areas = 183 records
- If you assign 3 ELECTRIC employees to 54 E-* CENTRAL areas = 162 records
- Total: 345 new assignment records

ROLLBACK:
- To undo assignments, run the rollback script (separate file)
*/

-- ============================================================================
-- END OF SCRIPT
-- ============================================================================
