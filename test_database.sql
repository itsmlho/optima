-- Test script untuk memverifikasi perbaikan database
USE optima_db;

-- 1. Cek struktur tabel spk
SELECT 'SPK Table Structure Check:' as test_name;
SELECT column_name, data_type, is_nullable, column_default 
FROM information_schema.columns 
WHERE table_schema = 'optima_db' 
AND table_name = 'spk' 
AND column_name IN ('id', 'spesifikasi', 'kontrak_spesifikasi_id');

-- 2. Cek auto increment spk
SELECT 'SPK Auto Increment Check:' as test_name;
SELECT auto_increment FROM information_schema.tables 
WHERE table_schema = 'optima_db' AND table_name = 'spk';

-- 3. Cek foreign key constraints untuk spk
SELECT 'SPK Foreign Key Check:' as test_name;
SELECT constraint_name, referenced_table_name, referenced_column_name
FROM information_schema.key_column_usage 
WHERE table_schema = 'optima_db' 
AND table_name = 'spk' 
AND referenced_table_name IS NOT NULL;

-- 4. Cek struktur kontrak_spesifikasi
SELECT 'Kontrak Spesifikasi Table Structure Check:' as test_name;
SELECT column_name, data_type, is_nullable 
FROM information_schema.columns 
WHERE table_schema = 'optima_db' 
AND table_name = 'kontrak_spesifikasi' 
AND column_name IN ('id', 'aksesoris');

-- 5. Cek apakah ada record SPK dengan ID = 0
SELECT 'SPK Zero ID Check:' as test_name;
SELECT COUNT(*) as zero_id_count FROM spk WHERE id = 0;

-- 6. Cek record SPK terbaru
SELECT 'Latest SPK Records:' as test_name;
SELECT id, nomor_spk, status, kontrak_spesifikasi_id, dibuat_pada 
FROM spk 
ORDER BY id DESC 
LIMIT 3;

-- 7. Test insert SPK sederhana
SELECT 'Test Insert SPK:' as test_name;
INSERT INTO spk (nomor_spk, pelanggan, status, dibuat_pada) 
VALUES ('TEST/001', 'Test Customer', 'SUBMITTED', NOW());

-- 8. Cek hasil insert
SELECT 'Insert Result Check:' as test_name;
SELECT id, nomor_spk, pelanggan, status 
FROM spk 
WHERE nomor_spk = 'TEST/001';

-- 9. Hapus record test
DELETE FROM spk WHERE nomor_spk = 'TEST/001';

SELECT 'Database verification completed successfully' as final_result;
