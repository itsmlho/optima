-- ===================================================
-- PHASE 1A - STEP 4: DROP REDUNDANT COLUMNS (FINAL STEP)
-- ===================================================
-- Purpose: Remove redundant FK fields from inventory_unit table
--          after successful transition to kontrak_unit junction table
-- Date: March 4, 2026
-- Author: System Refactoring
-- ===================================================

-- ⚠️ CRITICAL: DO NOT RUN THIS SCRIPT UNTIL:
-- 1. Phase 1A Steps 1-3 completed and deployed to production
-- 2. vw_unit_with_contracts VIEW tested and verified
-- 3. All code updated to use VIEW or kontrak_unit junction
-- 4. Minimum 2 weeks of parallel operation without issues
-- 5. Business stakeholder approval obtained
-- 6. Full database backup created

-- RECOMMENDED EXECUTION DATE: 2026-03-25 or later
-- (3 weeks after Phase 1A initial deployment)

-- ===================================================
-- PRE-DROP VERIFICATION
-- ===================================================

-- Step 1: Confirm VIEW exists and works
SELECT COUNT(*) as view_row_count
FROM vw_unit_with_contracts;

-- Step 2: Verify no code still references old columns directly
-- Manual check required:
-- - grep -r "inventory_unit.kontrak_id" app/
-- - grep -r "inventory_unit.customer_id" app/
-- - grep -r "->kontrak_id" app/Controllers/
-- - grep -r "->customer_id" app/Controllers/
-- Expected: Zero matches (all should use VIEW or junction table)

-- Step 3: Verify data integrity one last time
SELECT 
    'Verification before column drop' as check_type,
    COUNT(*) as total_units,
    COUNT(DISTINCT vuc.id_inventory_unit) as units_in_view,
    COUNT(DISTINCT ku.unit_id) as units_in_junction
FROM inventory_unit iu
LEFT JOIN vw_unit_with_contracts vuc ON iu.id_inventory_unit = vuc.id_inventory_unit
LEFT JOIN kontrak_unit ku ON iu.id_inventory_unit = ku.unit_id;

-- Step 4: Document current state
SELECT 
    TABLE_NAME,
    COLUMN_NAME,
    COLUMN_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'inventory_unit'
  AND COLUMN_NAME IN ('kontrak_id', 'customer_id', 'customer_location_id');

-- ===================================================
-- BACKUP (MANDATORY)
-- ===================================================

-- Run this command in terminal BEFORE executing DROP statements:
-- mysqldump -u root -p optima_ci inventory_unit > backup_inventory_unit_before_drop_$(date +%Y%m%d_%H%M%S).sql

-- Verify backup created:
-- ls -lh backup_inventory_unit_before_drop_*.sql

-- ===================================================
-- DROP REDUNDANT COLUMNS
-- ===================================================

-- Step 5: Drop kontrak_id column
ALTER TABLE inventory_unit
DROP COLUMN kontrak_id;

-- Step 6: Drop customer_id column
ALTER TABLE inventory_unit
DROP COLUMN customer_id;

-- Step 7: Drop customer_location_id column
ALTER TABLE inventory_unit
DROP COLUMN customer_location_id;

-- ===================================================
-- POST-DROP VERIFICATION
-- ===================================================

-- Step 8: Verify columns dropped
SHOW COLUMNS FROM inventory_unit;

-- Confirm these columns are gone:
-- - kontrak_id
-- - customer_id
-- - customer_location_id

-- Step 9: Verify VIEW still works
SELECT 
    id_inventory_unit,
    no_unit,
    kontrak_id,           -- Should still work (from VIEW)
    customer_id,          -- Should still work (from VIEW)
    customer_location_id, -- Should still work (from VIEW)
    contract_number,
    customer_name
FROM vw_unit_with_contracts
LIMIT 10;

-- Step 10: Test critical queries
-- Query 1: Get unit's current contract
SELECT *
FROM vw_unit_with_contracts
WHERE no_unit = 'FL001';

-- Query 2: Get units by customer
SELECT 
    id_inventory_unit,
    no_unit,
    customer_name,
    contract_number
FROM vw_unit_with_contracts
WHERE customer_id = 1;

-- Query 3: Get units by contract
SELECT 
    id_inventory_unit,
    no_unit,
    customer_name
FROM vw_unit_with_contracts
WHERE kontrak_id = 1;

-- ===================================================
-- APPLICATION CODE SMOKE TEST
-- ===================================================

-- After dropping columns, test these workflows in staging:
-- 
-- 1. SPK Creation
--    - Open Marketing > SPK > Create
--    - Select contract and units
--    - Verify units display correctly
--    - Create SPK
--    - Verify success
--
-- 2. DI Creation
--    - Create DI from SPK
--    - Approve planning stage
--    - Approve departure
--    - Approve arrival
--    - Verify unit delivery_date updated
--
-- 3. Unit Assignment
--    - Create new contract
--    - Assign units to contract
--    - Verify kontrak_unit junction populated
--    - View contract details
--    - Verify units display correctly
--
-- 4. Reports
--    - Generate unit assignment report
--    - Export to Excel
--    - Verify all customer/contract data displays
--
-- 5. DataTables
--    - Open unit list page
--    - Filter by customer
--    - Filter by contract
--    - Sort by various columns
--    - Verify no errors

-- ===================================================
-- ROLLBACK PROCEDURE (EMERGENCY ONLY)
-- ===================================================

-- IF CRITICAL ISSUES FOUND:
-- 
-- 1. Restore from backup:
--    mysql -u root -p optima_ci < backup_inventory_unit_before_drop_[timestamp].sql
-- 
-- 2. Re-add columns if needed:
--    ALTER TABLE inventory_unit
--    ADD COLUMN kontrak_id INT AFTER status_unit_id,
--    ADD COLUMN customer_id INT AFTER kontrak_id,
--    ADD COLUMN customer_location_id INT AFTER customer_id;
-- 
-- 3. Repopulate from kontrak_unit:
--    UPDATE inventory_unit iu
--    LEFT JOIN kontrak_unit ku ON iu.id_inventory_unit = ku.unit_id 
--        AND ku.status IN ('AKTIF', 'DIPERPANJANG')
--    LEFT JOIN kontrak k ON ku.kontrak_id = k.id
--    LEFT JOIN customer_locations cl ON k.customer_location_id = cl.id
--    SET 
--        iu.kontrak_id = ku.kontrak_id,
--        iu.customer_location_id = k.customer_location_id,
--        iu.customer_id = cl.customer_id
--    WHERE ku.unit_id IS NOT NULL;
-- 
-- 4. Investigate root cause
-- 5. Fix code issues
-- 6. Re-test in staging
-- 7. Schedule new deployment

-- ===================================================
-- SUCCESS CRITERIA
-- ===================================================

-- Phase 1A is considered COMPLETE when:
-- ✅ Redundant columns dropped from inventory_unit
-- ✅ vw_unit_with_contracts VIEW performs well (< 100ms queries)
-- ✅ All application code uses VIEW or kontrak_unit junction
-- ✅ Zero references to old column names in codebase
-- ✅ All critical workflows tested and working
-- ✅ No errors in production logs for 1 week post-deployment
-- ✅ Team trained on new data access pattern
-- ✅ Documentation updated

-- ===================================================
-- COMMUNICATION PLAN
-- ===================================================

-- Before dropping columns:
-- 1. Email to development team (3 days notice)
-- 2. Update in team standup
-- 3. Slack announcement
-- 4. Update project documentation
--
-- After dropping columns:
-- 1. Deployment notification
-- 2. Monitor error logs (share in Slack)
-- 3. Weekly status update to stakeholders
-- 4. Close JIRA tickets related to Phase 1A

-- ===================================================
-- DOCUMENTATION UPDATES NEEDED
-- ===================================================

-- 1. Database Schema Documentation
--    - Remove kontrak_id, customer_id, customer_location_id from inventory_unit schema docs
--    - Add section on vw_unit_with_contracts VIEW
--    - Document correct way to query unit contracts
--
-- 2. API Documentation
--    - Update examples to use VIEW
--    - Note that old fields no longer exist
--
-- 3. Developer Onboarding Guide
--    - Add section on junction table pattern
--    - Explain why redundant fields removed
--    - Best practices for querying unit relationships
--
-- 4. User Manual
--    - No changes needed (UI remains same)

-- ===================================================
-- METRICS TO TRACK
-- ===================================================

-- Track these metrics before and after column drop:
-- 
-- 1. Database size
--    SELECT 
--        table_name,
--        ROUND(((data_length + index_length) / 1024 / 1024), 2) AS "Size (MB)"
--    FROM information_schema.TABLES
--    WHERE table_schema = DATABASE()
--      AND table_name = 'inventory_unit';
--
-- 2. Query performance
--    - Average response time for unit list page
--    - Average response time for contract details page
--    - DataTables rendering time
--
-- 3. Code quality
--    - Lines of code reduced (cleanup)
--    - Code complexity score
--    - Test coverage percentage
--
-- 4. Team productivity
--    - Time to implement new features (before/after)
--    - Bug count related to unit assignments
--    - Developer satisfaction survey

-- ===================================================
-- LESSONS LEARNED (TO BE FILLED POST-DEPLOYMENT)
-- ===================================================

-- What went well:
-- - 
--
-- What could be improved:
-- - 
--
-- Action items for future refactoring:
-- - 

-- ===================================================
-- SIGN-OFF
-- ===================================================

-- Phase 1A Final Step Approved By:
-- 
-- Developer: ________________  Date: __________
-- QA Lead:   ________________  Date: __________
-- DevOps:    ________________  Date: __________
-- Business:  ________________  Date: __________

-- ===================================================
-- END OF PHASE 1A
-- ===================================================

-- Next: Proceed to Phase 1B - Database Column Renaming
-- See: databases/migrations/phase1b_*_rename_*.sql
