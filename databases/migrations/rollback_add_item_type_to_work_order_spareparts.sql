-- =====================================================
-- ROLLBACK Migration: Remove Item Type Column
-- Purpose: Revert changes if needed
-- Date: 2026-02-06
-- WARNING: This will remove item_type data permanently!
-- =====================================================

USE optima_ci;

-- Step 1: Show current data before rollback
SELECT 
    '=== DATA BEFORE ROLLBACK ===' as status;

SELECT 
    item_type,
    COUNT(*) as total
FROM work_order_spareparts
GROUP BY item_type;

-- Step 2: Drop composite index
DROP INDEX idx_item_warehouse ON work_order_spareparts;

-- Step 3: Drop item_type index
DROP INDEX idx_item_type ON work_order_spareparts;

-- Step 4: Remove column
ALTER TABLE work_order_spareparts 
DROP COLUMN item_type;

-- Step 5: Verify rollback
SELECT 
    '=== ROLLBACK COMPLETED ===' as status;

DESCRIBE work_order_spareparts;

SELECT 
    '=== item_type column has been removed ===' as status;
