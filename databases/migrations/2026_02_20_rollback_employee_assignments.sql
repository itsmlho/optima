-- ============================================================================
-- Rollback Script: Remove Employee Assignments from CENTRAL Areas
-- Date: 2026-02-20
-- Description: Rollback script to undo employee assignments to D-* and E-* CENTRAL areas
--              created on 2026-02-20
-- 
-- WARNING: 
-- - This will remove assignments created by the assignment script
-- - Review carefully before executing
-- ============================================================================

-- Step 1: Preview assignments that will be deleted
SELECT 'Assignments to be deleted:' as info;
SELECT 
    aea.id,
    a.area_code,
    a.area_name,
    e.staff_name,
    d.nama_departemen,
    aea.assignment_type,
    aea.start_date,
    aea.notes
FROM area_employee_assignments aea
INNER JOIN areas a ON aea.area_id = a.id
INNER JOIN employees e ON aea.employee_id = e.id
INNER JOIN departemen d ON e.departemen_id = d.id_departemen
WHERE a.area_type = 'CENTRAL'
  AND (a.area_code LIKE 'D-%' OR a.area_code LIKE 'E-%')
  AND aea.start_date = '2026-02-20'
  AND aea.notes LIKE '%Auto-assigned to CENTRAL area%'
ORDER BY a.area_code, e.staff_name;

-- Step 2: Count assignments to be deleted
SELECT 'Total assignments to delete:' as info, COUNT(*) as total
FROM area_employee_assignments aea
INNER JOIN areas a ON aea.area_id = a.id
WHERE a.area_type = 'CENTRAL'
  AND (a.area_code LIKE 'D-%' OR a.area_code LIKE 'E-%')
  AND aea.start_date = '2026-02-20'
  AND aea.notes LIKE '%Auto-assigned to CENTRAL area%';

-- Step 3: Backup assignments before deletion
CREATE TABLE IF NOT EXISTS area_employee_assignments_backup_20260220 AS
SELECT aea.*
FROM area_employee_assignments aea
INNER JOIN areas a ON aea.area_id = a.id
WHERE a.area_type = 'CENTRAL'
  AND (a.area_code LIKE 'D-%' OR a.area_code LIKE 'E-%')
  AND aea.start_date = '2026-02-20'
  AND aea.notes LIKE '%Auto-assigned to CENTRAL area%';

SELECT 'Backup created:' as info, COUNT(*) as backed_up_records
FROM area_employee_assignments_backup_20260220;

-- Step 4: DELETE assignments (UNCOMMENT TO EXECUTE)
/*
DELETE aea
FROM area_employee_assignments aea
INNER JOIN areas a ON aea.area_id = a.id
WHERE a.area_type = 'CENTRAL'
  AND (a.area_code LIKE 'D-%' OR a.area_code LIKE 'E-%')
  AND aea.start_date = '2026-02-20'
  AND aea.notes LIKE '%Auto-assigned to CENTRAL area%';
*/

-- Step 5: Verify deletion
SELECT 'Remaining assignments after rollback:' as info;
SELECT COUNT(*) as remaining_count
FROM area_employee_assignments aea
INNER JOIN areas a ON aea.area_id = a.id
WHERE a.area_type = 'CENTRAL'
  AND (a.area_code LIKE 'D-%' OR a.area_code LIKE 'E-%')
  AND aea.start_date = '2026-02-20'
  AND aea.notes LIKE '%Auto-assigned to CENTRAL area%';

-- Step 6: Restore from backup (if needed)
/*
INSERT INTO area_employee_assignments
SELECT * FROM area_employee_assignments_backup_20260220;
*/

-- ============================================================================
-- ALTERNATIVE: Delete ALL assignments to specific CENTRAL areas
-- Use this if you want to remove all assignments (not just auto-assigned ones)
-- ============================================================================

/*
-- Preview all assignments to D-* CENTRAL areas
SELECT 'All D-* CENTRAL area assignments:' as info;
SELECT 
    aea.id,
    a.area_code,
    e.staff_name,
    aea.assignment_type,
    aea.start_date
FROM area_employee_assignments aea
INNER JOIN areas a ON aea.area_id = a.id
INNER JOIN employees e ON aea.employee_id = e.id
WHERE a.area_code LIKE 'D-%'
  AND a.area_type = 'CENTRAL'
ORDER BY a.area_code, e.staff_name;

-- Delete all assignments to D-* CENTRAL areas
DELETE aea
FROM area_employee_assignments aea
INNER JOIN areas a ON aea.area_id = a.id
WHERE a.area_code LIKE 'D-%'
  AND a.area_type = 'CENTRAL';

-- Delete all assignments to E-* CENTRAL areas
DELETE aea
FROM area_employee_assignments aea
INNER JOIN areas a ON aea.area_id = a.id
WHERE a.area_code LIKE 'E-%'
  AND a.area_type = 'CENTRAL';
*/

-- ============================================================================
-- CLEANUP
-- Drop backup table after confirming rollback (OPTIONAL)
-- ============================================================================

/*
DROP TABLE IF EXISTS area_employee_assignments_backup_20260220;
*/

-- ============================================================================
-- END OF ROLLBACK SCRIPT
-- ============================================================================
