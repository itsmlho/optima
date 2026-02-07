-- =====================================================
-- Migration: Add Item Type Column to work_order_spareparts
-- Purpose: Support tracking both Spareparts and Tools
-- Date: 2026-02-06
-- =====================================================

USE optima_ci;

-- Step 1: Add item_type column
ALTER TABLE work_order_spareparts 
ADD COLUMN item_type ENUM('sparepart', 'tool') NOT NULL DEFAULT 'sparepart'
COMMENT 'Item type: sparepart (consumable) or tool (durable equipment)'
AFTER sparepart_name;

-- Step 2: Add index for better query performance
CREATE INDEX idx_item_type ON work_order_spareparts(item_type);

-- Step 3: Add composite index for common queries
CREATE INDEX idx_item_warehouse ON work_order_spareparts(item_type, is_from_warehouse);

-- Step 4: Update existing records to 'sparepart' (default behavior)
UPDATE work_order_spareparts 
SET item_type = 'sparepart' 
WHERE item_type IS NULL OR item_type = '';

-- Step 5: Verify migration
SELECT 
    '=== MIGRATION RESULT ===' as status;

SELECT 
    item_type,
    COUNT(*) as total_items,
    SUM(CASE WHEN is_from_warehouse = 1 THEN 1 ELSE 0 END) as from_warehouse,
    SUM(CASE WHEN is_from_warehouse = 0 THEN 1 ELSE 0 END) as non_warehouse,
    SUM(CASE WHEN notes IS NOT NULL AND notes != '' THEN 1 ELSE 0 END) as with_notes
FROM work_order_spareparts
GROUP BY item_type
ORDER BY item_type;

SELECT 
    '=== TABLE STRUCTURE ===' as status;

DESCRIBE work_order_spareparts;

-- Step 6: Show sample data
SELECT 
    '=== SAMPLE DATA (First 5 Records) ===' as status;

SELECT 
    id,
    work_order_id,
    item_type,
    sparepart_name,
    quantity_brought,
    satuan,
    CASE WHEN is_from_warehouse = 1 THEN '✓ Warehouse' ELSE '⚠ Non-WH' END as source,
    COALESCE(SUBSTRING(notes, 1, 30), '-') as notes_preview
FROM work_order_spareparts
ORDER BY id DESC
LIMIT 5;

SELECT 
    '=== MIGRATION COMPLETED SUCCESSFULLY ===' as status;
