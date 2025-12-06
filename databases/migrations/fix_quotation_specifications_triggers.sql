-- ============================================================================
-- Fix Quotation Specifications Triggers
-- Date: 2025-12-05
-- Purpose: Update triggers to use new column names (monthly_price, daily_price)
--          and new formula: total_price = (quantity * monthly_price) + daily_price
-- ============================================================================

USE optima_ci;

-- Drop old triggers that reference unit_price
DROP TRIGGER IF EXISTS tr_quotation_specifications_calculate_total;
DROP TRIGGER IF EXISTS tr_quotation_specifications_update_total;

-- ============================================================================
-- CREATE NEW TRIGGERS WITH UPDATED FORMULA
-- ============================================================================

DELIMITER $$

-- Trigger 1: Calculate total_price on INSERT
-- Formula: (quantity * monthly_price) + daily_price
-- Both monthly_price and daily_price can be NULL, treat NULL as 0
CREATE TRIGGER tr_quotation_specifications_calculate_total
BEFORE INSERT ON quotation_specifications
FOR EACH ROW
BEGIN
    SET NEW.total_price = 
        (NEW.quantity * COALESCE(NEW.monthly_price, 0)) + COALESCE(NEW.daily_price, 0);
END$$

-- Trigger 2: Recalculate total_price on UPDATE
-- Formula: (quantity * monthly_price) + daily_price
CREATE TRIGGER tr_quotation_specifications_update_total
BEFORE UPDATE ON quotation_specifications
FOR EACH ROW
BEGIN
    SET NEW.total_price = 
        (NEW.quantity * COALESCE(NEW.monthly_price, 0)) + COALESCE(NEW.daily_price, 0);
END$$

DELIMITER ;

-- ============================================================================
-- VERIFICATION
-- ============================================================================

-- Show updated triggers
SELECT 
    TRIGGER_NAME,
    EVENT_MANIPULATION,
    EVENT_OBJECT_TABLE,
    ACTION_TIMING,
    LEFT(ACTION_STATEMENT, 100) as ACTION_PREVIEW
FROM information_schema.TRIGGERS
WHERE EVENT_OBJECT_TABLE = 'quotation_specifications'
ORDER BY EVENT_MANIPULATION, ACTION_TIMING;

-- ✅ Triggers updated successfully
SELECT '✅ Triggers updated: tr_quotation_specifications_calculate_total, tr_quotation_specifications_update_total' as status;
