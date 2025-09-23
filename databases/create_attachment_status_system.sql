-- MIGRATION: CREATE ATTACHMENT STATUS SYSTEM
-- Date: 2025-09-13
-- Purpose: Fix attachment status confusion by creating dedicated status table

-- Step 1: Create dedicated attachment status table
CREATE TABLE IF NOT EXISTS status_attachment (
    id_status_attachment INT PRIMARY KEY AUTO_INCREMENT,
    nama_status VARCHAR(50) NOT NULL UNIQUE,
    deskripsi VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Step 2: Insert proper attachment statuses
INSERT INTO status_attachment (id_status_attachment, nama_status, deskripsi) VALUES
(1, 'AVAILABLE', 'Attachment tersedia untuk digunakan'),
(2, 'USED', 'Attachment sedang digunakan pada unit'),
(3, 'MAINTENANCE', 'Attachment dalam pemeliharaan'),
(4, 'RUSAK', 'Attachment rusak tidak dapat digunakan'),
(5, 'RESERVED', 'Attachment direservasi untuk SPK tertentu')
ON DUPLICATE KEY UPDATE 
    deskripsi = VALUES(deskripsi),
    updated_at = CURRENT_TIMESTAMP;

-- Step 3: Add new column to inventory_attachment
ALTER TABLE inventory_attachment 
ADD COLUMN IF NOT EXISTS status_attachment_id INT DEFAULT 1,
ADD CONSTRAINT fk_inventory_attachment_status 
    FOREIGN KEY (status_attachment_id) 
    REFERENCES status_attachment(id_status_attachment);

-- Step 4: Migrate existing data based on current logic
UPDATE inventory_attachment SET 
    status_attachment_id = CASE
        WHEN id_inventory_unit IS NOT NULL THEN 2  -- USED
        WHEN status_unit = 7 THEN 1                -- AVAILABLE (was STOCK ASET)
        WHEN status_unit = 8 THEN 1                -- AVAILABLE (was STOCK NON ASET)
        WHEN status_unit = 3 THEN 2                -- USED (was RENTAL)
        ELSE 1                                     -- Default AVAILABLE
    END;

-- Step 5: Create view for backward compatibility
CREATE OR REPLACE VIEW vw_attachment_status AS
SELECT 
    ia.*,
    sa.nama_status as status_attachment_name,
    sa.deskripsi as status_attachment_desc,
    CASE 
        WHEN ia.id_inventory_unit IS NOT NULL THEN 'USED'
        ELSE 'AVAILABLE'
    END as simple_status
FROM inventory_attachment ia
LEFT JOIN status_attachment sa ON ia.status_attachment_id = sa.id_status_attachment;

-- Step 6: Create indexes for performance
CREATE INDEX IF NOT EXISTS idx_inventory_attachment_status 
    ON inventory_attachment(status_attachment_id);
CREATE INDEX IF NOT EXISTS idx_inventory_attachment_unit_status 
    ON inventory_attachment(id_inventory_unit, status_attachment_id);

-- Step 7: Optional - Remove old status_unit column after migration complete
-- ALTER TABLE inventory_attachment DROP COLUMN status_unit;
