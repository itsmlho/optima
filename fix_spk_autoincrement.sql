-- SPK Table Fix Script
-- Run this in phpMyAdmin at http://localhost:8081

-- 1. Check current table status
SHOW TABLE STATUS LIKE 'spk';

-- 2. Check records with ID = 0
SELECT id, nomor_spk, status, dibuat_pada FROM spk WHERE id = 0;

-- 3. Check current max ID
SELECT MAX(id) as max_id FROM spk WHERE id > 0;

-- 4. Check all SPK records
SELECT id, nomor_spk, status, dibuat_pada FROM spk ORDER BY id;

-- 5. Fix auto increment (adjust the number based on max ID + 1)
-- If max ID is 25, then use 26
ALTER TABLE spk AUTO_INCREMENT = 26;

-- 6. Delete records with ID = 0 (if any exist)
-- DELETE FROM spk WHERE id = 0;

-- 7. Verify auto increment is fixed
SHOW TABLE STATUS LIKE 'spk';
