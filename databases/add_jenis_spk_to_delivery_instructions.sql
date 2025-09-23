-- Add jenis_spk field to delivery_instructions table to track UNIT vs ATTACHMENT delivery type
-- This enables proper Total Items calculation based on delivery type

-- Add jenis_spk column to delivery_instructions
ALTER TABLE delivery_instructions 
ADD COLUMN jenis_spk ENUM('UNIT', 'ATTACHMENT') DEFAULT 'UNIT' AFTER spk_id,
ADD INDEX idx_delivery_instructions_jenis_spk (jenis_spk);

-- Update existing delivery_instructions records based on their SPK jenis_spk
UPDATE delivery_instructions di 
INNER JOIN spk s ON di.spk_id = s.id 
SET di.jenis_spk = s.jenis_spk 
WHERE s.jenis_spk IN ('UNIT', 'ATTACHMENT');

-- Verification query
SELECT 
    jenis_spk,
    COUNT(*) as count,
    CONCAT(ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM delivery_instructions), 2), '%') as percentage
FROM delivery_instructions 
GROUP BY jenis_spk 
ORDER BY jenis_spk;

-- Show sample records
SELECT 
    di.id, 
    di.nomor_di, 
    di.jenis_spk as di_jenis_spk,
    s.nomor_spk,
    s.jenis_spk as spk_jenis_spk
FROM delivery_instructions di 
INNER JOIN spk s ON di.spk_id = s.id 
LIMIT 10;