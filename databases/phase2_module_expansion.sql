-- OptimaPro Activity Logging - PHASE 2: Module Enhancement  
-- Date: 2025-09-09
-- Purpose: Safely expand module_name enum to support all divisions

-- PHASE 2: Expand module_name enum (safe - adds new values, keeps existing)
ALTER TABLE system_activity_log 
MODIFY COLUMN module_name ENUM(
    -- Keep existing values
    'PURCHASING',
    'WAREHOUSE', 
    'MARKETING',
    'SERVICE',
    'OPERATIONAL',
    'ACCOUNTING',
    'PERIZINAN',
    'ADMIN',
    'DASHBOARD',
    'REPORTS',
    'SETTINGS',
    'USER_MANAGEMENT',
    -- Add new values for complete coverage
    'MONITORING',
    'ADMINISTRATION'
) DEFAULT NULL;

-- Verify enum expansion
SELECT 'Module name enum expanded successfully' as status;
SHOW COLUMNS FROM system_activity_log WHERE Field = 'module_name';
