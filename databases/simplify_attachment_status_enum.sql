-- MIGRATION: SIMPLIFY ATTACHMENT STATUS WITH ENUM
-- Date: 2025-09-13  
-- Purpose: Replace status_attachment table with simple ENUM for better performance

-- Step 1: Drop foreign key constraint first
ALTER TABLE inventory_attachment 
DROP FOREIGN KEY IF EXISTS fk_inventory_attachment_status;

-- Step 2: Add ENUM column for attachment status
ALTER TABLE inventory_attachment 
ADD COLUMN IF NOT EXISTS attachment_status ENUM(
    'AVAILABLE',   -- Tersedia untuk digunakan
    'USED',        -- Sedang digunakan pada unit  
    'MAINTENANCE', -- Dalam pemeliharaan
    'RUSAK',       -- Rusak tidak dapat digunakan
    'RESERVED'     -- Direservasi untuk SPK tertentu
) DEFAULT 'AVAILABLE' AFTER status_unit;

-- Step 3: Migrate data from status_attachment_id to ENUM
UPDATE inventory_attachment SET 
    attachment_status = CASE status_attachment_id
        WHEN 1 THEN 'AVAILABLE'
        WHEN 2 THEN 'USED' 
        WHEN 3 THEN 'MAINTENANCE'
        WHEN 4 THEN 'RUSAK'
        WHEN 5 THEN 'RESERVED'
        ELSE 'AVAILABLE'
    END;

-- Step 4: Verify migration
SELECT 
    attachment_status,
    COUNT(*) as count,
    GROUP_CONCAT(id_inventory_attachment) as sample_ids
FROM inventory_attachment 
GROUP BY attachment_status;

-- Step 5: Create index for performance
CREATE INDEX IF NOT EXISTS idx_attachment_status 
    ON inventory_attachment(attachment_status);

-- Step 6: Remove old columns after migration verified
-- ALTER TABLE inventory_attachment DROP COLUMN status_attachment_id;
-- DROP TABLE IF EXISTS status_attachment;

-- Step 7: Update attachment status based on actual usage
UPDATE inventory_attachment SET 
    attachment_status = CASE 
        WHEN id_inventory_unit IS NOT NULL THEN 'USED'
        ELSE 'AVAILABLE'
    END;
