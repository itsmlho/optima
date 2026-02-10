-- ============================================================================
-- OPTIMA RENTAL WORKFLOW - BILLING METHOD ENHANCEMENT
-- ============================================================================
-- Migration: Add billing method fields for flexible invoice calculation
-- Purpose: Support Cycle Billing, Monthly Prorate, and Fixed Monthly billing
-- Date: 2026-02-10
-- Author: GitHub Copilot (based on Gemini's proposal analysis)
-- ============================================================================

USE optima_ci;

-- ============================================================================
-- 1. ADD BILLING METHOD TO KONTRAK TABLE
-- ============================================================================

ALTER TABLE kontrak 
ADD COLUMN billing_method ENUM('CYCLE', 'PRORATE', 'MONTHLY_FIXED') 
    DEFAULT 'CYCLE' 
    COMMENT 'Invoice calculation method: CYCLE=30-day rolling, PRORATE=month-end billing, MONTHLY_FIXED=fixed date'
    AFTER jenis_sewa;

-- Add index for billing method queries
ALTER TABLE kontrak
ADD INDEX idx_billing_method (billing_method);

-- Update existing contracts to CYCLE (safest default)
UPDATE kontrak 
SET billing_method = 'CYCLE' 
WHERE billing_method IS NULL;

-- ============================================================================
-- 2. ADD DEFAULT BILLING METHOD TO CUSTOMERS TABLE
-- ============================================================================

ALTER TABLE customers
ADD COLUMN default_billing_method ENUM('CYCLE', 'PRORATE', 'MONTHLY_FIXED')
    DEFAULT 'CYCLE'
    COMMENT 'Customer preferred billing method (will be auto-filled in new contracts)'
    AFTER alamat;

-- Add index for customer billing preference
ALTER TABLE customers
ADD INDEX idx_default_billing_method (default_billing_method);

-- Update existing customers to CYCLE
UPDATE customers 
SET default_billing_method = 'CYCLE' 
WHERE default_billing_method IS NULL;

-- ============================================================================
-- 3. ADD BILLING NOTES TO KONTRAK TABLE
-- ============================================================================

ALTER TABLE kontrak
ADD COLUMN billing_notes TEXT NULL
    COMMENT 'Special billing instructions or notes (e.g., custom billing date for MONTHLY_FIXED)'
    AFTER billing_method;

-- ============================================================================
-- 4. ADD BILLING START DATE OVERRIDE TO KONTRAK TABLE
-- ============================================================================

ALTER TABLE kontrak
ADD COLUMN billing_start_date DATE NULL
    COMMENT 'Override billing start date (if different from tanggal_mulai). Used for backdating or custom start.'
    AFTER billing_notes;

-- ============================================================================
-- VERIFICATION QUERIES
-- ============================================================================

-- Check table structure
SHOW COLUMNS FROM kontrak LIKE 'billing%';
SHOW COLUMNS FROM customers LIKE 'default_billing%';

-- Verify data migration
SELECT 
    'Total Contracts' as metric,
    COUNT(*) as count,
    billing_method
FROM kontrak
GROUP BY billing_method
UNION ALL
SELECT 
    'Total Customers' as metric,
    COUNT(*) as count,
    default_billing_method
FROM customers
GROUP BY default_billing_method;

-- ============================================================================
-- ROLLBACK SCRIPT (if needed)
-- ============================================================================

/*
-- Rollback instructions (DO NOT RUN unless rollback needed):

ALTER TABLE kontrak 
DROP INDEX idx_billing_method,
DROP COLUMN billing_start_date,
DROP COLUMN billing_notes,
DROP COLUMN billing_method;

ALTER TABLE customers
DROP INDEX idx_default_billing_method,
DROP COLUMN default_billing_method;

*/

-- ============================================================================
-- MIGRATION COMPLETE
-- ============================================================================

SELECT 'Billing Method Migration completed successfully!' as status,
       NOW() as completed_at;
