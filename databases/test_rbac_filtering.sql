-- ============================================================================
-- TESTING SCRIPT FOR SERVICE RBAC FILTERING
-- Purpose: Create test scenarios for different service roles
-- ============================================================================

-- Test Scenario 1: Admin Service Pusat Electric
-- Expected: Can see Jakarta Pusat (D) & Jakarta Selatan (C) - Electric department only

-- Test Scenario 2: Admin Service Pusat Diesel  
-- Expected: Can see Jakarta Pusat (D) & Jakarta Selatan (C) - Diesel & Gasoline departments only

-- Test Scenario 3: Admin Service Area (Tangerang)
-- Expected: Can see Tangerang (C) area only - All departments

-- Test Scenario 4: Staff Service Area
-- Expected: Can see assigned area only - All departments

-- ============================================================================
-- SAMPLE USER ROLE ASSIGNMENTS (for testing)
-- ============================================================================

-- Update existing users or create test users
-- User 1: Admin Service Pusat Electric
UPDATE users SET role = 'Admin Service Pusat Electric' WHERE id = 1;

-- User 2: Admin Service Pusat Diesel  
-- UPDATE users SET role = 'Admin Service Pusat Diesel' WHERE id = 2;

-- User 3: Admin Service Area
-- UPDATE users SET role = 'Admin Service Area' WHERE id = 3;

-- Sample area assignments for Service Area users
-- INSERT INTO area_employee_assignments (area_id, employee_id, assignment_type, department_scope, is_active) VALUES
-- (5, 3, 'PRIMARY', 'ALL', 1); -- User 3 assigned to Tangerang (area_id = 5)

-- ============================================================================
-- VERIFICATION QUERIES
-- ============================================================================

-- Check areas by type
SELECT id, area_code, area_name, area_type FROM areas WHERE is_active = 1 ORDER BY area_type, area_name;

-- Check current user roles
SELECT id, username, role FROM users WHERE role LIKE '%Service%';

-- Check area assignments
SELECT aea.*, a.area_name FROM area_employee_assignments aea 
JOIN areas a ON a.id = aea.area_id 
WHERE aea.is_active = 1;

-- ============================================================================
-- EXPECTED FILTERING RESULTS
-- ============================================================================

/*
Role: Admin Service Pusat Electric
- Areas: Jakarta Pusat (D), Jakarta Selatan (C) [CENTRAL type only]
- Departments: [2] Electric only
- Cannot see: Branch areas, Diesel/Gasoline data

Role: Admin Service Pusat Diesel
- Areas: Jakarta Pusat (D), Jakarta Selatan (C) [CENTRAL type only] 
- Departments: [1,3] Diesel + Gasoline
- Cannot see: Branch areas, Electric data

Role: Admin Service Area
- Areas: [Based on area_employee_assignments]
- Departments: [1,2,3] All departments
- Cannot see: Areas not assigned to them

Role: Super Administrator
- Areas: ALL
- Departments: ALL
- Can see: Everything
*/