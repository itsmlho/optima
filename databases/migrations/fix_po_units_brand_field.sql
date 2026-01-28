-- ============================================================================
-- Migration: Fix PO Units Brand Field (merk_unit)
-- ============================================================================
-- Purpose: Mengubah po_units.merk_unit dari INTEGER (ID) ke VARCHAR (nama)
-- Date: 2026-01-27
-- Issue: Inkonsistensi data brand antara Create PO, Print Packing List, dan
--        PO Verification karena field merk_unit menyimpan ID tapi query
--        tidak melakukan join yang benar
--
-- Impact: 
--   - po_units table structure akan berubah
--   - Data migration dari ID ke nama brand
--   - Controller dan Model perlu update (lihat doc lengkap)
--
-- Related Files:
--   - docs/PO_BRAND_MODEL_INCONSISTENCY_FIX.md (documentation)
--   - app/Models/POUnitsModel.php (validation update)
--   - app/Controllers/Purchasing.php (storePoUnit update)
-- ============================================================================

-- ============================================================================
-- STEP 1: BACKUP DATA LAMA
-- ============================================================================
-- Simpan ID lama ke column backup sebelum mengubah tipe data
ALTER TABLE po_units 
ADD COLUMN merk_unit_id_backup INT COMMENT 'Backup of old merk_unit ID before migration to VARCHAR' 
AFTER merk_unit;

UPDATE po_units 
SET merk_unit_id_backup = merk_unit 
WHERE merk_unit IS NOT NULL;

SELECT 'Step 1 Complete: Backup column created' as status,
       COUNT(*) as total_rows_backed_up
FROM po_units 
WHERE merk_unit_id_backup IS NOT NULL;

-- ============================================================================
-- STEP 2: UBAH TIPE DATA merk_unit
-- ============================================================================
-- Ubah dari INT menjadi VARCHAR(100) untuk menyimpan nama brand
ALTER TABLE po_units 
MODIFY COLUMN merk_unit VARCHAR(100) COMMENT 'Brand name (changed from ID to VARCHAR on 2026-01-27)';

SELECT 'Step 2 Complete: Column type changed to VARCHAR(100)' as status;

-- ============================================================================
-- STEP 3: MIGRATE DATA - Update merk_unit dengan nama brand
-- ============================================================================
-- Ambil nama brand dari model_unit berdasarkan ID yang tersimpan di backup
UPDATE po_units pu
LEFT JOIN model_unit mu ON mu.id_model_unit = pu.merk_unit_id_backup
SET pu.merk_unit = mu.merk_unit
WHERE pu.merk_unit_id_backup IS NOT NULL;

SELECT 'Step 3 Complete: Data migrated from ID to brand name' as status,
       COUNT(*) as total_rows_migrated
FROM po_units 
WHERE merk_unit IS NOT NULL;

-- ============================================================================
-- STEP 4: VERIFIKASI DATA
-- ============================================================================
-- Check apakah semua data sudah di-migrate dengan benar
SELECT 
    'Data Verification' as step,
    COUNT(*) as total_po_units,
    SUM(CASE WHEN merk_unit IS NOT NULL THEN 1 ELSE 0 END) as rows_with_brand,
    SUM(CASE WHEN merk_unit IS NULL AND merk_unit_id_backup IS NOT NULL THEN 1 ELSE 0 END) as rows_failed_migration
FROM po_units;

-- Tampilkan sample data untuk review
SELECT 
    pu.id_po_unit,
    pu.merk_unit_id_backup as old_id,
    pu.merk_unit as new_brand_name,
    mu.merk_unit as expected_brand_name,
    CASE 
        WHEN pu.merk_unit = mu.merk_unit THEN '✅ Match'
        WHEN pu.merk_unit IS NULL THEN '⚠️ NULL'
        ELSE '❌ Mismatch'
    END as status
FROM po_units pu
LEFT JOIN model_unit mu ON mu.id_model_unit = pu.merk_unit_id_backup
ORDER BY pu.id_po_unit DESC
LIMIT 10;

-- ============================================================================
-- STEP 5: CLEANUP (OPTIONAL - Uncomment setelah verifikasi OK)
-- ============================================================================
-- Setelah verifikasi semua data OK, hapus backup column
-- JANGAN HAPUS DULU sampai yakin migration berhasil!

-- ALTER TABLE po_units 
-- DROP COLUMN merk_unit_id_backup;

-- SELECT 'Step 5 Complete: Backup column removed (CLEANUP DONE)' as status;

-- ============================================================================
-- ROLLBACK SCRIPT (Jika terjadi masalah)
-- ============================================================================
-- Uncomment dan jalankan jika perlu rollback:

/*
-- Restore data dari backup
ALTER TABLE po_units MODIFY COLUMN merk_unit INT;
UPDATE po_units SET merk_unit = merk_unit_id_backup WHERE merk_unit_id_backup IS NOT NULL;
ALTER TABLE po_units DROP COLUMN merk_unit_id_backup;
SELECT 'Rollback Complete: merk_unit restored to INT with old IDs' as status;
*/

-- ============================================================================
-- POST-MIGRATION CHECKLIST
-- ============================================================================
-- [ ] Step 1-4 executed successfully
-- [ ] Data verification shows all rows migrated correctly
-- [ ] POUnitsModel.php validation updated (merk_unit => 'required|max_length[100]')
-- [ ] Purchasing.php storePoUnit() updated to save brand name not ID
-- [ ] Test: Create new PO and verify brand name saved correctly
-- [ ] Test: Print Packing List shows brand correctly
-- [ ] Test: PO Verification shows brand correctly
-- [ ] After all tests pass: Run Step 5 to cleanup backup column
-- ============================================================================

SELECT '🎉 Migration Script Complete! Check verification results above.' as final_status;
