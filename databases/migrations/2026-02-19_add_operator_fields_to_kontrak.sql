-- =====================================================
-- ADD OPERATOR FIELDS TO KONTRAK & QUOTATION_SPECIFICATIONS
-- =====================================================
-- Purpose: Support hardcoded operator in quotations (like spare units)
-- Date: 2026-02-19
-- Run in: Development & Production
-- =====================================================

-- Add operator fields to kontrak table
ALTER TABLE kontrak
ADD COLUMN operator_quantity INT UNSIGNED DEFAULT 0 COMMENT 'Number of operators included in contract',
ADD COLUMN operator_monthly_rate DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Monthly rate per operator (if applicable)';

-- Add operator_quantity to quotation_specifications table (operator_monthly_rate already exists)
ALTER TABLE quotation_specifications
ADD COLUMN operator_quantity INT UNSIGNED DEFAULT 0 COMMENT 'Number of operators included' AFTER is_spare_unit;

-- Verify columns added
SHOW COLUMNS FROM kontrak WHERE Field IN ('operator_quantity', 'operator_monthly_rate');
SHOW COLUMNS FROM quotation_specifications WHERE Field IN ('operator_quantity', 'operator_monthly_rate');

-- =====================================================
-- ROLLBACK (if needed)
-- =====================================================
-- ALTER TABLE kontrak DROP COLUMN operator_quantity;
-- ALTER TABLE kontrak DROP COLUMN operator_monthly_rate;
-- ALTER TABLE quotation_specifications DROP COLUMN operator_quantity;
