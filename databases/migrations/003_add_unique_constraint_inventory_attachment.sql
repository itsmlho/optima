-- Migration: Add UNIQUE constraints to prevent duplicate attachments
-- Date: 2026-02-02
-- Description: Prevent duplicate attachment/charger/battery records for same unit

USE optima_ci;

-- First, clean up any existing duplicates
-- Keep only the most recent record for each unit + item combination

-- Show duplicates before cleanup
SELECT 
    'BEFORE CLEANUP:' as stage,
    id_inventory_unit,
    tipe_item,
    attachment_id,
    charger_id,
    baterai_id,
    COUNT(*) as duplicate_count
FROM inventory_attachment
WHERE id_inventory_unit IS NOT NULL
GROUP BY id_inventory_unit, tipe_item, attachment_id, charger_id, baterai_id
HAVING COUNT(*) > 1;

-- Clean up duplicates for BATTERY type
DELETE ia1 FROM inventory_attachment ia1
INNER JOIN (
    SELECT 
        id_inventory_unit,
        baterai_id,
        MIN(id_inventory_attachment) as keep_id
    FROM inventory_attachment
    WHERE id_inventory_unit IS NOT NULL 
    AND tipe_item = 'BATTERY'
    AND baterai_id IS NOT NULL
    GROUP BY id_inventory_unit, baterai_id
) ia2 ON ia1.id_inventory_unit = ia2.id_inventory_unit 
    AND ia1.baterai_id = ia2.baterai_id
    AND ia1.id_inventory_attachment > ia2.keep_id;

-- Clean up duplicates for CHARGER type
DELETE ia1 FROM inventory_attachment ia1
INNER JOIN (
    SELECT 
        id_inventory_unit,
        charger_id,
        MIN(id_inventory_attachment) as keep_id
    FROM inventory_attachment
    WHERE id_inventory_unit IS NOT NULL 
    AND tipe_item = 'CHARGER'
    AND charger_id IS NOT NULL
    GROUP BY id_inventory_unit, charger_id
) ia2 ON ia1.id_inventory_unit = ia2.id_inventory_unit 
    AND ia1.charger_id = ia2.charger_id
    AND ia1.id_inventory_attachment > ia2.keep_id;

-- Clean up duplicates for ATTACHMENT type
DELETE ia1 FROM inventory_attachment ia1
INNER JOIN (
    SELECT 
        id_inventory_unit,
        attachment_id,
        MIN(id_inventory_attachment) as keep_id
    FROM inventory_attachment
    WHERE id_inventory_unit IS NOT NULL 
    AND tipe_item = 'ATTACHMENT'
    AND attachment_id IS NOT NULL
    GROUP BY id_inventory_unit, attachment_id
) ia2 ON ia1.id_inventory_unit = ia2.id_inventory_unit 
    AND ia1.attachment_id = ia2.attachment_id
    AND ia1.id_inventory_attachment > ia2.keep_id;

-- Show remaining records after cleanup
SELECT 
    'AFTER CLEANUP:' as stage,
    id_inventory_unit,
    tipe_item,
    attachment_id,
    charger_id,
    baterai_id,
    COUNT(*) as record_count
FROM inventory_attachment
WHERE id_inventory_unit IS NOT NULL
GROUP BY id_inventory_unit, tipe_item, attachment_id, charger_id, baterai_id;

-- Add UNIQUE constraint to prevent future duplicates
-- This ensures one unit can only have one specific attachment/charger/battery at a time
ALTER TABLE inventory_attachment
ADD UNIQUE KEY `uk_unit_attachment` (`id_inventory_unit`, `attachment_id`) USING BTREE,
ADD UNIQUE KEY `uk_unit_charger` (`id_inventory_unit`, `charger_id`) USING BTREE,
ADD UNIQUE KEY `uk_unit_battery` (`id_inventory_unit`, `baterai_id`) USING BTREE;

-- Add comment to table
ALTER TABLE inventory_attachment 
COMMENT = 'Inventory attachment records with UNIQUE constraints to prevent duplicates';

SELECT 'Migration 003 completed successfully!' as status;
