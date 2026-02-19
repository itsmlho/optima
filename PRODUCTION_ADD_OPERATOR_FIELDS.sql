-- =====================================================
-- ADD OPERATOR FIELDS - PRODUCTION
-- =====================================================
-- Purpose: Support hardcoded operator in quotations (like spare units)
-- Date: 2026-02-19
-- Database: u138256737_optima_db
-- NOTE: Select database in PHPMyAdmin before running
-- =====================================================

-- Add operator fields to kontrak table
ALTER TABLE kontrak
ADD COLUMN operator_quantity INT UNSIGNED DEFAULT 0 COMMENT 'Number of operators included in contract',
ADD COLUMN operator_monthly_rate DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Monthly rate per operator (if applicable)';

-- Add operator_quantity to quotation_specifications table
ALTER TABLE quotation_specifications
ADD COLUMN operator_quantity INT UNSIGNED DEFAULT 0 COMMENT 'Number of operators included';

-- Verify columns added to kontrak
SHOW COLUMNS FROM kontrak WHERE Field IN ('operator_quantity', 'operator_monthly_rate');

-- Verify columns added to quotation_specifications
SHOW COLUMNS FROM quotation_specifications WHERE Field IN ('operator_quantity', 'operator_monthly_rate');

-- =====================================================
-- EXPECTED RESULT:
-- kontrak table:
--   operator_quantity       | int unsigned   | 0
--   operator_monthly_rate   | decimal(15,2)  | 0.00
--
-- quotation_specifications table:
--   operator_quantity       | int unsigned   | 0
--   operator_monthly_rate   | decimal(15,2)  | (already exists)
-- =====================================================
