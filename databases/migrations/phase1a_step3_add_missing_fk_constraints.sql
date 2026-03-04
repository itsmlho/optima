-- ===================================================
-- PHASE 1A - STEP 3: ADD MISSING FOREIGN KEY CONSTRAINTS
-- ===================================================
-- Purpose: Add critical FK constraints to ensure data integrity
-- Date: March 4, 2026
-- Author: System Refactoring
-- ===================================================

-- CRITICAL FOREIGN KEYS TO ADD:
-- 1. kontrak.customer_location_id → customer_locations.id
-- 2. spk.kontrak_id → kontrak.id
-- 3. customers.marketing_user_id → users.id (after column conversion)

-- BACKUP FIRST!
-- mysqldump -u root -p optima_ci kontrak spk customers > backup_before_add_fks_$(date +%Y%m%d_%H%M%S).sql

-- ===================================================
-- PART 1: ADD FK - kontrak.customer_location_id
-- ===================================================

-- Step 1.1: Check for orphaned records
SELECT 
    'Orphaned customer_location_id in kontrak' as issue,
    k.id as kontrak_id,
    k.no_kontrak,
    k.customer_location_id,
    'Location does not exist' as problem
FROM kontrak k
LEFT JOIN customer_locations cl ON k.customer_location_id = cl.id
WHERE k.customer_location_id IS NOT NULL 
  AND cl.id IS NULL;

-- Step 1.2: Clean orphaned records (IF NEEDED - BUSINESS DECISION REQUIRED)
-- Option A: Set to NULL
-- UPDATE kontrak 
-- SET customer_location_id = NULL 
-- WHERE id IN (SELECT kontrak_id FROM [...above query...]);

-- Option B: Assign to default location
-- UPDATE kontrak k
-- JOIN customers c ON k.customer_id = c.id  -- If customer_id exists
-- JOIN customer_locations cl ON c.id = cl.customer_id AND cl.is_primary = 1
-- SET k.customer_location_id = cl.id
-- WHERE k.customer_location_id IS NULL OR k.customer_location_id NOT IN (SELECT id FROM customer_locations);

-- Step 1.3: Add foreign key constraint
ALTER TABLE kontrak
ADD CONSTRAINT fk_kontrak_customer_location
FOREIGN KEY (customer_location_id)
REFERENCES customer_locations(id)
ON DELETE RESTRICT
ON UPDATE CASCADE;

-- Business Rule: RESTRICT prevents deleting a location if contracts exist
-- This forces users to reassign contracts before deleting location

-- Step 1.4: Verify constraint added
SELECT 
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME,
    DELETE_RULE,
    UPDATE_RULE
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'kontrak'
  AND CONSTRAINT_NAME = 'fk_kontrak_customer_location';

-- ===================================================
-- PART 2: ADD FK - spk.kontrak_id
-- ===================================================

-- Step 2.1: Check for orphaned records
SELECT 
    'Orphaned kontrak_id in spk' as issue,
    s.id as spk_id,
    s.nomor_spk,
    s.kontrak_id,
    'Contract does not exist' as problem
FROM spk s
LEFT JOIN kontrak k ON s.kontrak_id = k.id
WHERE s.kontrak_id IS NOT NULL 
  AND k.id IS NULL;

-- Step 2.2: Clean orphaned records (IF ANY)
-- Business decision: Set to NULL or delete SPK?
-- UPDATE spk SET kontrak_id = NULL WHERE kontrak_id NOT IN (SELECT id FROM kontrak);

-- Step 2.3: Add foreign key constraint
ALTER TABLE spk
ADD CONSTRAINT fk_spk_kontrak
FOREIGN KEY (kontrak_id)
REFERENCES kontrak(id)
ON DELETE SET NULL
ON UPDATE CASCADE;

-- Business Rule: SET NULL allows contract to be deleted while preserving SPK record
-- This is appropriate because some SPKs may exist without contracts (one-time orders)

-- Step 2.4: Verify constraint
SELECT 
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    DELETE_RULE,
    UPDATE_RULE
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'spk'
  AND CONSTRAINT_NAME = 'fk_spk_kontrak';

-- ===================================================
-- PART 3: CONVERT customers.marketing_name TO FK
-- ===================================================

-- Step 3.1: Add new marketing_user_id column
ALTER TABLE customers
ADD COLUMN marketing_user_id INT AFTER marketing_name,
ADD INDEX idx_marketing_user (marketing_user_id);

-- Step 3.2: Map existing marketing_name to user IDs
UPDATE customers c
LEFT JOIN users u ON c.marketing_name = u.name
SET c.marketing_user_id = u.id
WHERE c.marketing_name IS NOT NULL
  AND c.marketing_name != '';

-- Step 3.3: Report unmatched names (for manual resolution)
SELECT 
    'Unmatched marketing names' as issue,
    DISTINCT c.marketing_name,
    COUNT(*) as customer_count
FROM customers c
LEFT JOIN users u ON c.marketing_name = u.name
WHERE c.marketing_name IS NOT NULL 
  AND c.marketing_name != ''
  AND u.id IS NULL
GROUP BY c.marketing_name;

-- Step 3.4: Export for manual review
-- Save results and decide:
--   Option A: Create user accounts for unmatched names
--   Option B: Map to closest match (typo corrections)
--   Option C: Set to default marketing person

-- Step 3.5: Handle unmatched (EXAMPLE - ADJUST AS NEEDED)
-- Example: Create users for unmatched names
-- INSERT INTO users (name, email, username, password, role_id, is_active, created_at)
-- SELECT 
--     DISTINCT c.marketing_name,
--     CONCAT(LOWER(REPLACE(c.marketing_name, ' ', '.')), '@company.com'),
--     LOWER(REPLACE(c.marketing_name, ' ', '_')),
--     '$2y$10$defaultHashedPassword',  -- Temporary password
--     (SELECT id FROM roles WHERE role_name = 'Marketing' LIMIT 1),
--     1,
--     NOW()
-- FROM customers c
-- LEFT JOIN users u ON c.marketing_name = u.name
-- WHERE c.marketing_name IS NOT NULL AND u.id IS NULL;

-- Then re-run mapping:
-- UPDATE customers c
-- LEFT JOIN users u ON c.marketing_name = u.name
-- SET c.marketing_user_id = u.id
-- WHERE c.marketing_user_id IS NULL 
--   AND c.marketing_name IS NOT NULL;

-- Step 3.6: Add foreign key constraint
ALTER TABLE customers
ADD CONSTRAINT fk_customers_marketing_user
FOREIGN KEY (marketing_user_id)
REFERENCES users(id)
ON DELETE SET NULL
ON UPDATE CASCADE;

-- Business Rule: SET NULL if user deleted (customer remains but loses marketing assignment)

-- Step 3.7: Deprecate old marketing_name column
ALTER TABLE customers 
MODIFY marketing_name VARCHAR(255) 
COMMENT 'DEPRECATED: Use marketing_user_id instead. Scheduled for removal: 2026-05-04';

-- Step 3.8: Verify constraint
SELECT 
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    DELETE_RULE,
    UPDATE_RULE
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'customers'
  AND CONSTRAINT_NAME = 'fk_customers_marketing_user';

-- ===================================================
-- PART 4: REVIEW AUDIT TABLE CASCADE RULES
-- ===================================================

-- Current cascade rules for audit tables may be too aggressive
-- Recommendation: Change CASCADE to RESTRICT to preserve history

-- Step 4.1: Check current cascade rules
SELECT 
    TABLE_NAME,
    CONSTRAINT_NAME,
    REFERENCED_TABLE_NAME,
    DELETE_RULE,
    UPDATE_RULE
FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS
WHERE TABLE_SCHEMA = DATABASE()
  AND REFERENCED_TABLE_NAME IN ('kontrak', 'quotations')
  AND DELETE_RULE = 'CASCADE'
  AND TABLE_NAME LIKE '%history%' OR TABLE_NAME LIKE '%amendment%';

-- Step 4.2: Update contract_amendments FK rule
ALTER TABLE contract_amendments
DROP FOREIGN KEY fk_amendments_contract;

ALTER TABLE contract_amendments
ADD CONSTRAINT fk_amendments_contract
FOREIGN KEY (kontrak_id)
REFERENCES kontrak(id)
ON DELETE RESTRICT  -- Changed from CASCADE
ON UPDATE CASCADE;

-- Step 4.3: Update contract_po_history FK rule
ALTER TABLE contract_po_history
DROP FOREIGN KEY contract_po_history_ibfk_1;

ALTER TABLE contract_po_history
ADD CONSTRAINT fk_contract_po_history_kontrak
FOREIGN KEY (kontrak_id)
REFERENCES kontrak(id)
ON DELETE RESTRICT  -- Changed from CASCADE to preserve history
ON UPDATE CASCADE;

-- Step 4.4: Verify updated rules
SELECT 
    TABLE_NAME,
    CONSTRAINT_NAME,
    DELETE_RULE,
    UPDATE_RULE
FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS
WHERE TABLE_SCHEMA = DATABASE()
  AND CONSTRAINT_NAME IN ('fk_amendments_contract', 'fk_contract_po_history_kontrak');

-- ===================================================
-- PART 5: COMPREHENSIVE FK VERIFICATION
-- ===================================================

-- List all foreign keys in database
SELECT 
    TABLE_NAME,
    COLUMN_NAME,
    CONSTRAINT_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME,
    DELETE_RULE,
    UPDATE_RULE
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = DATABASE()
  AND REFERENCED_TABLE_NAME IS NOT NULL
ORDER BY TABLE_NAME, COLUMN_NAME;

-- ===================================================
-- PART 6: TESTING FK CONSTRAINTS
-- ===================================================

-- Test 1: Try to delete customer_location with active contracts (should fail)
-- START TRANSACTION;
-- DELETE FROM customer_locations WHERE id = (
--     SELECT customer_location_id FROM kontrak LIMIT 1
-- );
-- ROLLBACK;
-- Expected: Error 1451 - Cannot delete or update a parent row

-- Test 2: Try to delete contract with amendments (should fail)
-- START TRANSACTION;
-- DELETE FROM kontrak WHERE id IN (
--     SELECT kontrak_id FROM contract_amendments LIMIT 1
-- );
-- ROLLBACK;
-- Expected: Error 1451 - Cannot delete or update a parent row

-- Test 3: Delete SPK's contract (should set spk.kontrak_id to NULL)
-- START TRANSACTION;
-- SELECT kontrak_id FROM spk WHERE kontrak_id IS NOT NULL LIMIT 1;
-- DELETE FROM kontrak WHERE id = [above kontrak_id];
-- SELECT kontrak_id FROM spk WHERE id = [spk_id];  -- Should be NULL
-- ROLLBACK;

-- Test 4: Update customer's marketing person (should work)
-- START TRANSACTION;
-- UPDATE customers 
-- SET marketing_user_id = (SELECT id FROM users WHERE role_id = [marketing_role] LIMIT 1)
-- WHERE id = 1;
-- ROLLBACK;

-- Test 5: Try to delete user who is marketing person (should set customer.marketing_user_id to NULL)
-- START TRANSACTION;
-- DELETE FROM users WHERE id IN (
--     SELECT marketing_user_id FROM customers LIMIT 1
-- );
-- ROLLBACK;

-- ===================================================
-- PART 7: DOCUMENTATION & NEXT STEPS
-- ===================================================

-- WHAT WE ACCOMPLISHED:
-- ✅ Added FK: kontrak.customer_location_id → customer_locations.id (RESTRICT)
-- ✅ Added FK: spk.kontrak_id → kontrak.id (SET NULL)
-- ✅ Converted customers.marketing_name to marketing_user_id FK → users.id (SET NULL)
-- ✅ Updated audit table cascade rules (CASCADE → RESTRICT)
-- ✅ Verified all constraints with INFORMATION_SCHEMA queries

-- DATA INTEGRITY IMPROVEMENTS:
-- 1. Cannot delete customer_location if contracts exist (must reassign first)
-- 2. Cannot delete contract if amendments/PO history exists (preserve audit trail)
-- 3. Marketing assignments now use referential integrity (no orphaned user names)
-- 4. SPK preserves record even if contract deleted (kontrak_id becomes NULL)

-- NEXT STEPS:
-- 1. Update CustomerModel.php to use marketing_user_id
-- 2. Update CustomerManagementController.php forms
-- 3. Update all views that display marketing person (use JOIN to users table)
-- 4. Training for team on new FK constraint behavior
-- 5. Update user documentation on what happens when deleting records
-- 6. Plan to drop marketing_name column after 1 month transition (2026-05-04)

-- ROLLBACK PROCEDURE (IF NEEDED):
-- ALTER TABLE kontrak DROP FOREIGN KEY fk_kontrak_customer_location;
-- ALTER TABLE spk DROP FOREIGN KEY fk_spk_kontrak;
-- ALTER TABLE customers DROP FOREIGN KEY fk_customers_marketing_user;
-- ALTER TABLE customers DROP COLUMN marketing_user_id;
-- ALTER TABLE contract_amendments DROP FOREIGN KEY fk_amendments_contract;
-- [Restore original FK with CASCADE]

-- ===================================================
-- END OF MIGRATION SCRIPT
-- ===================================================
