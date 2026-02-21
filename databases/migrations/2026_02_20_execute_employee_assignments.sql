-- ============================================================================
-- READY-TO-RUN: Employee Assignment to CENTRAL Areas
-- Date: 2026-02-20
-- CUSTOMIZED based on current employee data
-- ============================================================================

-- ============================================================================
-- ASSIGNMENT PLAN:
-- DIESEL: Assign CENTRAL staff (BAGUS #8, Deni #9) to all 61 D-* CENTRAL areas
-- ELECTRIC: Assign ADMIN staff (Novi #1, Sari #2, AgusA #18) to all 54 E-* CENTRAL areas
-- ============================================================================

-- Preview: Count areas to be assigned
SELECT 'D-* CENTRAL areas (DIESEL):' as info, COUNT(*) as total_areas
FROM areas 
WHERE area_code LIKE 'D-%' AND area_type = 'CENTRAL' AND is_active = 1;

SELECT 'E-* CENTRAL areas (ELECTRIC):' as info, COUNT(*) as total_areas
FROM areas 
WHERE area_code LIKE 'E-%' AND area_type = 'CENTRAL' AND is_active = 1;

-- Expected assignments:
-- DIESEL: 2 employees × 61 areas = 122 records
-- ELECTRIC: 3 employees × 54 areas = 162 records
-- Total: 284 new assignment records

SELECT 'Expected new assignments:' as info, 
       '284 records (122 DIESEL + 162 ELECTRIC)' as calculation;

-- ============================================================================
-- EXECUTE: Assign DIESEL CENTRAL staff to all D-* CENTRAL areas
-- ============================================================================

INSERT INTO area_employee_assignments (area_id, employee_id, assignment_type, start_date, is_active, department_scope, notes)
SELECT 
    a.id as area_id,
    e.id as employee_id,
    'PRIMARY' as assignment_type,
    '2026-02-20' as start_date,
    1 as is_active,
    'DIESEL' as department_scope,
    CONCAT('Auto-assigned CENTRAL staff to area ', a.area_code, ' on 2026-02-20') as notes
FROM areas a
CROSS JOIN employees e
WHERE a.area_code LIKE 'D-%'
  AND a.area_type = 'CENTRAL'
  AND a.is_active = 1
  AND e.id IN (8, 9)  -- BAGUS & Deni (CENTRAL staff)
  AND e.is_active = 1
  AND NOT EXISTS (
      SELECT 1 FROM area_employee_assignments aea
      WHERE aea.area_id = a.id AND aea.employee_id = e.id AND aea.is_active = 1
  );

SELECT 'DIESEL assignments created:' as info, ROW_COUNT() as records_inserted;

-- ============================================================================
-- EXECUTE: Assign ELECTRIC ADMIN staff to all E-* CENTRAL areas
-- ============================================================================

INSERT INTO area_employee_assignments (area_id, employee_id, assignment_type, start_date, is_active, department_scope, notes)
SELECT 
    a.id as area_id,
    e.id as employee_id,
    'PRIMARY' as assignment_type,
    '2026-02-20' as start_date,
    1 as is_active,
    'ELECTRIC' as department_scope,
    CONCAT('Auto-assigned ADMIN staff to area ', a.area_code, ' on 2026-02-20') as notes
FROM areas a
CROSS JOIN employees e
WHERE a.area_code LIKE 'E-%'
  AND a.area_type = 'CENTRAL'
  AND a.is_active = 1
  AND e.id IN (1, 2, 18)  -- Novi, Sari, AgusA (ADMIN staff)
  AND e.is_active = 1
  AND NOT EXISTS (
      SELECT 1 FROM area_employee_assignments aea
      WHERE aea.area_id = a.id AND aea.employee_id = e.id AND aea.is_active = 1
  );

SELECT 'ELECTRIC assignments created:' as info, ROW_COUNT() as records_inserted;

-- ============================================================================
-- VERIFICATION: Check assignment results
-- ============================================================================

-- Verify DIESEL assignments
SELECT 'DIESEL staff area coverage:' as report;
SELECT 
    e.id,
    e.staff_name,
    e.staff_role,
    COUNT(DISTINCT CASE WHEN a.area_code LIKE 'D-%' AND a.area_type='CENTRAL' THEN a.id END) as d_central_areas,
    COUNT(DISTINCT aea.area_id) as total_all_areas
FROM employees e
INNER JOIN departemen d ON e.departemen_id = d.id_departemen
LEFT JOIN area_employee_assignments aea ON e.id = aea.employee_id AND aea.is_active = 1
LEFT JOIN areas a ON aea.area_id = a.id
WHERE d.nama_departemen = 'DIESEL'
  AND e.is_active = 1
  AND e.id IN (8, 9)
GROUP BY e.id, e.staff_name, e.staff_role
ORDER BY e.staff_name;

-- Verify ELECTRIC assignments
SELECT 'ELECTRIC staff area coverage:' as report;
SELECT 
    e.id,
    e.staff_name,
    e.staff_role,
    COUNT(DISTINCT CASE WHEN a.area_code LIKE 'E-%' AND a.area_type='CENTRAL' THEN a.id END) as e_central_areas,
    COUNT(DISTINCT aea.area_id) as total_all_areas
FROM employees e
INNER JOIN departemen d ON e.departemen_id = d.id_departemen
LEFT JOIN area_employee_assignments aea ON e.id = aea.employee_id AND aea.is_active = 1
LEFT JOIN areas a ON aea.area_id = a.id
WHERE d.nama_departemen = 'ELECTRIC'
  AND e.is_active = 1
  AND e.id IN (1, 2, 18)
GROUP BY e.id, e.staff_name, e.staff_role
ORDER BY e.staff_name;

-- Show sample assignments
SELECT 'Sample DIESEL assignments (first 10):' as report;
SELECT 
    a.area_code,
    a.area_name,
    e.staff_name,
    aea.assignment_type,
    aea.start_date
FROM area_employee_assignments aea
INNER JOIN areas a ON aea.area_id = a.id
INNER JOIN employees e ON aea.employee_id = e.id
WHERE a.area_code LIKE 'D-%'
  AND a.area_type = 'CENTRAL'
  AND aea.start_date = '2026-02-20'
ORDER BY a.area_code, e.staff_name
LIMIT 10;

SELECT 'Sample ELECTRIC assignments (first 10):' as report;
SELECT 
    a.area_code,
    a.area_name,
    e.staff_name,
    aea.assignment_type,
    aea.start_date
FROM area_employee_assignments aea
INNER JOIN areas a ON aea.area_id = a.id
INNER JOIN employees e ON aea.employee_id = e.id
WHERE a.area_code LIKE 'E-%'
  AND a.area_type = 'CENTRAL'
  AND aea.start_date = '2026-02-20'
ORDER BY a.area_code, e.staff_name
LIMIT 10;

-- ============================================================================
-- SUMMARY
-- ============================================================================
SELECT '=== ASSIGNMENT SUMMARY ===' as summary;

SELECT 
    'DIESEL' as department,
    COUNT(DISTINCT aea.employee_id) as employees_assigned,
    COUNT(DISTINCT aea.area_id) as areas_covered,
    COUNT(*) as total_assignment_records
FROM area_employee_assignments aea
INNER JOIN areas a ON aea.area_id = a.id
INNER JOIN employees e ON aea.employee_id = e.id
INNER JOIN departemen d ON e.departemen_id = d.id_departemen
WHERE d.nama_departemen = 'DIESEL'
  AND a.area_code LIKE 'D-%'
  AND a.area_type = 'CENTRAL'
  AND aea.start_date = '2026-02-20'

UNION ALL

SELECT 
    'ELECTRIC' as department,
    COUNT(DISTINCT aea.employee_id) as employees_assigned,
    COUNT(DISTINCT aea.area_id) as areas_covered,
    COUNT(*) as total_assignment_records
FROM area_employee_assignments aea
INNER JOIN areas a ON aea.area_id = a.id
INNER JOIN employees e ON aea.employee_id = e.id
INNER JOIN departemen d ON e.departemen_id = d.id_departemen
WHERE d.nama_departemen = 'ELECTRIC'
  AND a.area_code LIKE 'E-%'
  AND a.area_type = 'CENTRAL'
  AND aea.start_date = '2026-02-20';

-- ============================================================================
-- COMPLETED!
-- Next steps:
-- 1. Verify in UI: Service Area Management → View area assignments
-- 2. Test permissions: Login as assigned staff and check area access
-- 3. If needed, run rollback script: 2026_02_20_rollback_employee_assignments.sql
-- ============================================================================
