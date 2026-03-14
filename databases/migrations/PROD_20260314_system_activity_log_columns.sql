-- ============================================================================
-- MIGRATION: Add Security Columns to system_activity_log
-- Date: 2026-03-14
-- ============================================================================
-- INSTRUCTIONS FOR phpMyAdmin:
-- 1. Run Step 1 first (check existing columns)
-- 2. For each column in Step 2, run ONLY if the column doesn't exist
-- 3. Same for Step 3 indexes
-- ============================================================================

-- STEP 1: Check existing columns (run this first)
SELECT COLUMN_NAME 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'system_activity_log'
  AND COLUMN_NAME IN ('ip_address', 'user_agent', 'session_id');

-- ============================================================================
-- STEP 2: Add columns (run each separately, skip if already exists)
-- ============================================================================

-- 2a. Add ip_address column
ALTER TABLE `system_activity_log` 
ADD COLUMN `ip_address` VARCHAR(45) DEFAULT NULL AFTER `user_id`;

-- 2b. Add user_agent column
ALTER TABLE `system_activity_log` 
ADD COLUMN `user_agent` VARCHAR(500) DEFAULT NULL AFTER `ip_address`;

-- 2c. Add session_id column
ALTER TABLE `system_activity_log` 
ADD COLUMN `session_id` VARCHAR(100) DEFAULT NULL AFTER `user_agent`;

-- ============================================================================
-- STEP 3: Add indexes (run each separately, skip if already exists)
-- ============================================================================

-- 3a. Index on ip_address
ALTER TABLE `system_activity_log` 
ADD INDEX `idx_sal_ip_address` (`ip_address`);

-- 3b. Index on user_id + created_at
ALTER TABLE `system_activity_log` 
ADD INDEX `idx_sal_user_created` (`user_id`, `created_at`);

-- ============================================================================
-- VERIFICATION
-- ============================================================================
SELECT 'Columns added successfully' AS status;
DESCRIBE `system_activity_log`;
