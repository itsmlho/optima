-- OptimaPro Activity Logging - PHASE 1: Safe Expansion
-- Date: 2025-09-09
-- Purpose: Safely extend existing system_activity_log for future menu requirements

-- SAFETY FIRST: Backup existing data
CREATE TABLE IF NOT EXISTS system_activity_log_backup_20250909 AS 
SELECT * FROM system_activity_log;

-- Check current structure
SELECT 'Current system_activity_log structure:' as info;
DESCRIBE system_activity_log;

-- PHASE 1: Add new optional fields (non-breaking)
ALTER TABLE system_activity_log 
ADD COLUMN IF NOT EXISTS submenu_item VARCHAR(100) DEFAULT NULL AFTER module_name,
ADD COLUMN IF NOT EXISTS feature_area VARCHAR(100) DEFAULT NULL AFTER submenu_item,
ADD COLUMN IF NOT EXISTS ip_address VARCHAR(45) DEFAULT NULL AFTER user_id,
ADD COLUMN IF NOT EXISTS user_agent TEXT DEFAULT NULL AFTER ip_address,
ADD COLUMN IF NOT EXISTS session_id VARCHAR(128) DEFAULT NULL AFTER user_agent,
ADD COLUMN IF NOT EXISTS financial_impact DECIMAL(15,2) DEFAULT NULL AFTER business_impact,
ADD COLUMN IF NOT EXISTS currency_code VARCHAR(3) DEFAULT 'IDR' AFTER financial_impact;

-- Verify new structure
SELECT 'Updated system_activity_log structure:' as info;
DESCRIBE system_activity_log;
