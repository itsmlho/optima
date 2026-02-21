-- ============================================================================
-- Rollback Script: Remove Central Areas for DIESEL and ELECTRIC
-- Date: 2026-02-20
-- Description: Rollback script to undo migration 2026_02_20_add_central_areas_diesel_electric.sql
--              This will DELETE 115 CENTRAL areas (61 DIESEL + 54 ELECTRIC)
-- 
-- WARNING: 
-- - This will permanently delete area records
-- - Check for foreign key dependencies before running
-- - Backup data before executing rollback
-- - Areas assigned to employees, customers, or inventory units may cause FK constraint errors
-- ============================================================================

-- Step 1: Backup data before deletion
-- Save to backup table for recovery if needed
CREATE TABLE IF NOT EXISTS areas_backup_20260220 AS
SELECT * FROM areas 
WHERE area_code IN (
    -- DIESEL CENTRAL areas (61)
    'D-ANCOL', 'D-BLRJ', 'D-BANDUNG', 'D-BANJAR', 'D-BATANG', 'D-BEKASI', 'D-BLORA', 
    'D-BOGOR', 'D-BREBES', 'D-CAKUNG', 'D-CIANJUR', 'D-CIBINONG', 'D-CIBITUNG', 
    'D-CKMPK', 'D-CIKARANG', 'D-CIKUPA', 'D-CLNGSI', 'D-CILEGON', 'D-CIMANGGS', 
    'D-CIRACAS', 'D-CIREBON', 'D-DNMGT', 'D-EJIP', 'D-GDBG', 'D-GRESIK', 
    'D-HARIAN', 'D-HYUNDAI', 'D-JABABEKA', 'D-JAKARTA', 'D-JATENG', 'D-JATIM', 
    'D-JAWILAN', 'D-JEPARA', 'D-KALASAN', 'D-KRWC', 'D-KARAWANG', 'D-KLATEN', 
    'D-KOPO', 'D-MJLGK', 'D-MALANG', 'D-MM2100', 'D-NAROGONG', 'D-PDLRG', 
    'D-PANDAAN', 'D-PSRRB', 'D-PATI', 'D-PIYUNGAN', 'D-PLGDG', 'D-PWKRT', 
    'D-PWDAD', 'D-PWSAR', 'D-REMBANG', 'D-SAWANGAN', 'D-SEMARANG', 'D-SERANG', 
    'D-SERPONG', 'D-SIDOARJO', 'D-SOLO', 'D-SUBANG', 'D-SUKABUMI', 'D-SURABAYA', 
    'D-TNG', 'D-YGY',
    
    -- ELECTRIC CENTRAL areas (54)
    'E-BANDUNG', 'E-BATANG', 'E-BEKASI', 'E-BOGOR', 'E-BREBES', 'E-CIBITUNG', 
    'E-CCLGK', 'E-CIANJUR', 'E-CKMPK', 'E-CIKANDE', 'E-CKR1', 'E-CKR2', 
    'E-CIKUPA', 'E-CILEGON', 'E-CIMANGGS', 'E-CIREBON', 'E-CITEUREP', 'E-DNMGT', 
    'E-DELTAMAS', 'E-DUMAI', 'E-GDBG', 'E-GRESIK', 'E-INDRMY', 'E-JBK1', 
    'E-JBK2', 'E-JAKARTA', 'E-JOGJA', 'E-JOMBANG', 'E-KARAWANG', 'E-KEDIRI', 
    'E-KLATEN', 'E-KOSAMBI', 'E-KRIAN', 'E-MADIUN', 'E-MJLGK', 'E-MALANG', 
    'E-MEDAN', 'E-PDLRG', 'E-PLMBG', 'E-PANDAAN', 'E-PASURUAN', 'E-PERAWANG', 
    'E-PWKRT', 'E-PWSAR', 'E-RNCEK', 'E-SEMARANG', 'E-SERANG', 'E-SIDOARJO', 
    'E-SUBANG', 'E-SUKABUMI', 'E-SURABAYA', 'E-TAMBUN', 'E-TNG', 'E-TLNGG'
);

-- Step 2: Check dependencies before deletion
-- Check area_employee_assignments
SELECT 'area_employee_assignments dependencies:' AS warning;
SELECT COUNT(*) AS count, 'Records will be CASCADE deleted if FK constraint exists' AS note
FROM area_employee_assignments 
WHERE area_id IN (
    SELECT id FROM areas WHERE area_code IN (
        'D-ANCOL', 'D-BLRJ', 'D-BANDUNG', 'D-BANJAR', 'D-BATANG', 'D-BEKASI', 'D-BLORA', 
        'D-BOGOR', 'D-BREBES', 'D-CAKUNG', 'D-CIANJUR', 'D-CIBINONG', 'D-CIBITUNG', 
        'D-CKMPK', 'D-CIKARANG', 'D-CIKUPA', 'D-CLNGSI', 'D-CILEGON', 'D-CIMANGGS', 
        'D-CIRACAS', 'D-CIREBON', 'D-DNMGT', 'D-EJIP', 'D-GDBG', 'D-GRESIK', 
        'D-HARIAN', 'D-HYUNDAI', 'D-JABABEKA', 'D-JAKARTA', 'D-JATENG', 'D-JATIM', 
        'D-JAWILAN', 'D-JEPARA', 'D-KALASAN', 'D-KRWC', 'D-KARAWANG', 'D-KLATEN', 
        'D-KOPO', 'D-MJLGK', 'D-MALANG', 'D-MM2100', 'D-NAROGONG', 'D-PDLRG', 
        'D-PANDAAN', 'D-PSRRB', 'D-PATI', 'D-PIYUNGAN', 'D-PLGDG', 'D-PWKRT', 
        'D-PWDAD', 'D-PWSAR', 'D-REMBANG', 'D-SAWANGAN', 'D-SEMARANG', 'D-SERANG', 
        'D-SERPONG', 'D-SIDOARJO', 'D-SOLO', 'D-SUBANG', 'D-SUKABUMI', 'D-SURABAYA', 
        'D-TNG', 'D-YGY',
        'E-BANDUNG', 'E-BATANG', 'E-BEKASI', 'E-BOGOR', 'E-BREBES', 'E-CIBITUNG', 
        'E-CCLGK', 'E-CIANJUR', 'E-CKMPK', 'E-CIKANDE', 'E-CKR1', 'E-CKR2', 
        'E-CIKUPA', 'E-CILEGON', 'E-CIMANGGS', 'E-CIREBON', 'E-CITEUREP', 'E-DNMGT', 
        'E-DELTAMAS', 'E-DUMAI', 'E-GDBG', 'E-GRESIK', 'E-INDRMY', 'E-JBK1', 
        'E-JBK2', 'E-JAKARTA', 'E-JOGJA', 'E-JOMBANG', 'E-KARAWANG', 'E-KEDIRI', 
        'E-KLATEN', 'E-KOSAMBI', 'E-KRIAN', 'E-MADIUN', 'E-MJLGK', 'E-MALANG', 
        'E-MEDAN', 'E-PDLRG', 'E-PLMBG', 'E-PANDAAN', 'E-PASURUAN', 'E-PERAWANG', 
        'E-PWKRT', 'E-PWSAR', 'E-RNCEK', 'E-SEMARANG', 'E-SERANG', 'E-SIDOARJO', 
        'E-SUBANG', 'E-SUKABUMI', 'E-SURABAYA', 'E-TAMBUN', 'E-TNG', 'E-TLNGG'
    )
);

-- Check customer_locations
SELECT 'customer_locations dependencies:' AS warning;
SELECT COUNT(*) AS count, 'May cause FK constraint error - manual cleanup required' AS note
FROM customer_locations 
WHERE area_id IN (
    SELECT id FROM areas WHERE area_code IN (
        'D-ANCOL', 'D-BLRJ', 'D-BANDUNG', 'D-BANJAR', 'D-BATANG', 'D-BEKASI', 'D-BLORA', 
        'D-BOGOR', 'D-BREBES', 'D-CAKUNG', 'D-CIANJUR', 'D-CIBINONG', 'D-CIBITUNG', 
        'D-CKMPK', 'D-CIKARANG', 'D-CIKUPA', 'D-CLNGSI', 'D-CILEGON', 'D-CIMANGGS', 
        'D-CIRACAS', 'D-CIREBON', 'D-DNMGT', 'D-EJIP', 'D-GDBG', 'D-GRESIK', 
        'D-HARIAN', 'D-HYUNDAI', 'D-JABABEKA', 'D-JAKARTA', 'D-JATENG', 'D-JATIM', 
        'D-JAWILAN', 'D-JEPARA', 'D-KALASAN', 'D-KRWC', 'D-KARAWANG', 'D-KLATEN', 
        'D-KOPO', 'D-MJLGK', 'D-MALANG', 'D-MM2100', 'D-NAROGONG', 'D-PDLRG', 
        'D-PANDAAN', 'D-PSRRB', 'D-PATI', 'D-PIYUNGAN', 'D-PLGDG', 'D-PWKRT', 
        'D-PWDAD', 'D-PWSAR', 'D-REMBANG', 'D-SAWANGAN', 'D-SEMARANG', 'D-SERANG', 
        'D-SERPONG', 'D-SIDOARJO', 'D-SOLO', 'D-SUBANG', 'D-SUKABUMI', 'D-SURABAYA', 
        'D-TNG', 'D-YGY',
        'E-BANDUNG', 'E-BATANG', 'E-BEKASI', 'E-BOGOR', 'E-BREBES', 'E-CIBITUNG', 
        'E-CCLGK', 'E-CIANJUR', 'E-CKMPK', 'E-CIKANDE', 'E-CKR1', 'E-CKR2', 
        'E-CIKUPA', 'E-CILEGON', 'E-CIMANGGS', 'E-CIREBON', 'E-CITEUREP', 'E-DNMGT', 
        'E-DELTAMAS', 'E-DUMAI', 'E-GDBG', 'E-GRESIK', 'E-INDRMY', 'E-JBK1', 
        'E-JBK2', 'E-JAKARTA', 'E-JOGJA', 'E-JOMBANG', 'E-KARAWANG', 'E-KEDIRI', 
        'E-KLATEN', 'E-KOSAMBI', 'E-KRIAN', 'E-MADIUN', 'E-MJLGK', 'E-MALANG', 
        'E-MEDAN', 'E-PDLRG', 'E-PLMBG', 'E-PANDAAN', 'E-PASURUAN', 'E-PERAWANG', 
        'E-PWKRT', 'E-PWSAR', 'E-RNCEK', 'E-SEMARANG', 'E-SERANG', 'E-SIDOARJO', 
        'E-SUBANG', 'E-SUKABUMI', 'E-SURABAYA', 'E-TAMBUN', 'E-TNG', 'E-TLNGG'
    )
);

-- Check inventory_unit
SELECT 'inventory_unit dependencies:' AS warning;
SELECT COUNT(*) AS count, 'May cause FK constraint error - manual cleanup required' AS note
FROM inventory_unit 
WHERE area_id IN (
    SELECT id FROM areas WHERE area_code IN (
        'D-ANCOL', 'D-BLRJ', 'D-BANDUNG', 'D-BANJAR', 'D-BATANG', 'D-BEKASI', 'D-BLORA', 
        'D-BOGOR', 'D-BREBES', 'D-CAKUNG', 'D-CIANJUR', 'D-CIBINONG', 'D-CIBITUNG', 
        'D-CKMPK', 'D-CIKARANG', 'D-CIKUPA', 'D-CLNGSI', 'D-CILEGON', 'D-CIMANGGS', 
        'D-CIRACAS', 'D-CIREBON', 'D-DNMGT', 'D-EJIP', 'D-GDBG', 'D-GRESIK', 
        'D-HARIAN', 'D-HYUNDAI', 'D-JABABEKA', 'D-JAKARTA', 'D-JATENG', 'D-JATIM', 
        'D-JAWILAN', 'D-JEPARA', 'D-KALASAN', 'D-KRWC', 'D-KARAWANG', 'D-KLATEN', 
        'D-KOPO', 'D-MJLGK', 'D-MALANG', 'D-MM2100', 'D-NAROGONG', 'D-PDLRG', 
        'D-PANDAAN', 'D-PSRRB', 'D-PATI', 'D-PIYUNGAN', 'D-PLGDG', 'D-PWKRT', 
        'D-PWDAD', 'D-PWSAR', 'D-REMBANG', 'D-SAWANGAN', 'D-SEMARANG', 'D-SERANG', 
        'D-SERPONG', 'D-SIDOARJO', 'D-SOLO', 'D-SUBANG', 'D-SUKABUMI', 'D-SURABAYA', 
        'D-TNG', 'D-YGY',
        'E-BANDUNG', 'E-BATANG', 'E-BEKASI', 'E-BOGOR', 'E-BREBES', 'E-CIBITUNG', 
        'E-CCLGK', 'E-CIANJUR', 'E-CKMPK', 'E-CIKANDE', 'E-CKR1', 'E-CKR2', 
        'E-CIKUPA', 'E-CILEGON', 'E-CIMANGGS', 'E-CIREBON', 'E-CITEUREP', 'E-DNMGT', 
        'E-DELTAMAS', 'E-DUMAI', 'E-GDBG', 'E-GRESIK', 'E-INDRMY', 'E-JBK1', 
        'E-JBK2', 'E-JAKARTA', 'E-JOGJA', 'E-JOMBANG', 'E-KARAWANG', 'E-KEDIRI', 
        'E-KLATEN', 'E-KOSAMBI', 'E-KRIAN', 'E-MADIUN', 'E-MJLGK', 'E-MALANG', 
        'E-MEDAN', 'E-PDLRG', 'E-PLMBG', 'E-PANDAAN', 'E-PASURUAN', 'E-PERAWANG', 
        'E-PWKRT', 'E-PWSAR', 'E-RNCEK', 'E-SEMARANG', 'E-SERANG', 'E-SIDOARJO', 
        'E-SUBANG', 'E-SUKABUMI', 'E-SURABAYA', 'E-TAMBUN', 'E-TNG', 'E-TLNGG'
    )
);

-- Step 3: DELETE CENTRAL areas
-- WARNING: This is a destructive operation!
-- Uncomment the following DELETE statement to execute rollback

/*
DELETE FROM areas 
WHERE area_code IN (
    -- DIESEL CENTRAL areas (61)
    'D-ANCOL', 'D-BLRJ', 'D-BANDUNG', 'D-BANJAR', 'D-BATANG', 'D-BEKASI', 'D-BLORA', 
    'D-BOGOR', 'D-BREBES', 'D-CAKUNG', 'D-CIANJUR', 'D-CIBINONG', 'D-CIBITUNG', 
    'D-CKMPK', 'D-CIKARANG', 'D-CIKUPA', 'D-CLNGSI', 'D-CILEGON', 'D-CIMANGGS', 
    'D-CIRACAS', 'D-CIREBON', 'D-DNMGT', 'D-EJIP', 'D-GDBG', 'D-GRESIK', 
    'D-HARIAN', 'D-HYUNDAI', 'D-JABABEKA', 'D-JAKARTA', 'D-JATENG', 'D-JATIM', 
    'D-JAWILAN', 'D-JEPARA', 'D-KALASAN', 'D-KRWC', 'D-KARAWANG', 'D-KLATEN', 
    'D-KOPO', 'D-MJLGK', 'D-MALANG', 'D-MM2100', 'D-NAROGONG', 'D-PDLRG', 
    'D-PANDAAN', 'D-PSRRB', 'D-PATI', 'D-PIYUNGAN', 'D-PLGDG', 'D-PWKRT', 
    'D-PWDAD', 'D-PWSAR', 'D-REMBANG', 'D-SAWANGAN', 'D-SEMARANG', 'D-SERANG', 
    'D-SERPONG', 'D-SIDOARJO', 'D-SOLO', 'D-SUBANG', 'D-SUKABUMI', 'D-SURABAYA', 
    'D-TNG', 'D-YGY',
    
    -- ELECTRIC CENTRAL areas (54)
    'E-BANDUNG', 'E-BATANG', 'E-BEKASI', 'E-BOGOR', 'E-BREBES', 'E-CIBITUNG', 
    'E-CCLGK', 'E-CIANJUR', 'E-CKMPK', 'E-CIKANDE', 'E-CKR1', 'E-CKR2', 
    'E-CIKUPA', 'E-CILEGON', 'E-CIMANGGS', 'E-CIREBON', 'E-CITEUREP', 'E-DNMGT', 
    'E-DELTAMAS', 'E-DUMAI', 'E-GDBG', 'E-GRESIK', 'E-INDRMY', 'E-JBK1', 
    'E-JBK2', 'E-JAKARTA', 'E-JOGJA', 'E-JOMBANG', 'E-KARAWANG', 'E-KEDIRI', 
    'E-KLATEN', 'E-KOSAMBI', 'E-KRIAN', 'E-MADIUN', 'E-MJLGK', 'E-MALANG', 
    'E-MEDAN', 'E-PDLRG', 'E-PLMBG', 'E-PANDAAN', 'E-PASURUAN', 'E-PERAWANG', 
    'E-PWKRT', 'E-PWSAR', 'E-RNCEK', 'E-SEMARANG', 'E-SERANG', 'E-SIDOARJO', 
    'E-SUBANG', 'E-SUKABUMI', 'E-SURABAYA', 'E-TAMBUN', 'E-TNG', 'E-TLNGG'
);
*/

-- Step 4: Verification after rollback
SELECT 'Areas deleted:' AS info;
SELECT 'Expected: 0 rows if rollback executed successfully' AS note;
SELECT COUNT(*) AS count 
FROM areas 
WHERE area_code IN (
    'D-ANCOL', 'D-BLRJ', 'D-BANDUNG', 'D-BANJAR', 'D-BATANG', 'D-BEKASI', 'D-BLORA', 
    'D-BOGOR', 'D-BREBES', 'D-CAKUNG', 'D-CIANJUR', 'D-CIBINONG', 'D-CIBITUNG', 
    'D-CKMPK', 'D-CIKARANG', 'D-CIKUPA', 'D-CLNGSI', 'D-CILEGON', 'D-CIMANGGS', 
    'D-CIRACAS', 'D-CIREBON', 'D-DNMGT', 'D-EJIP', 'D-GDBG', 'D-GRESIK', 
    'D-HARIAN', 'D-HYUNDAI', 'D-JABABEKA', 'D-JAKARTA', 'D-JATENG', 'D-JATIM', 
    'D-JAWILAN', 'D-JEPARA', 'D-KALASAN', 'D-KRWC', 'D-KARAWANG', 'D-KLATEN', 
    'D-KOPO', 'D-MJLGK', 'D-MALANG', 'D-MM2100', 'D-NAROGONG', 'D-PDLRG', 
    'D-PANDAAN', 'D-PSRRB', 'D-PATI', 'D-PIYUNGAN', 'D-PLGDG', 'D-PWKRT', 
    'D-PWDAD', 'D-PWSAR', 'D-REMBANG', 'D-SAWANGAN', 'D-SEMARANG', 'D-SERANG', 
    'D-SERPONG', 'D-SIDOARJO', 'D-SOLO', 'D-SUBANG', 'D-SUKABUMI', 'D-SURABAYA', 
    'D-TNG', 'D-YGY',
    'E-BANDUNG', 'E-BATANG', 'E-BEKASI', 'E-BOGOR', 'E-BREBES', 'E-CIBITUNG', 
    'E-CCLGK', 'E-CIANJUR', 'E-CKMPK', 'E-CIKANDE', 'E-CKR1', 'E-CKR2', 
    'E-CIKUPA', 'E-CILEGON', 'E-CIMANGGS', 'E-CIREBON', 'E-CITEUREP', 'E-DNMGT', 
    'E-DELTAMAS', 'E-DUMAI', 'E-GDBG', 'E-GRESIK', 'E-INDRMY', 'E-JBK1', 
    'E-JBK2', 'E-JAKARTA', 'E-JOGJA', 'E-JOMBANG', 'E-KARAWANG', 'E-KEDIRI', 
    'E-KLATEN', 'E-KOSAMBI', 'E-KRIAN', 'E-MADIUN', 'E-MJLGK', 'E-MALANG', 
    'E-MEDAN', 'E-PDLRG', 'E-PLMBG', 'E-PANDAAN', 'E-PASURUAN', 'E-PERAWANG', 
    'E-PWKRT', 'E-PWSAR', 'E-RNCEK', 'E-SEMARANG', 'E-SERANG', 'E-SIDOARJO', 
    'E-SUBANG', 'E-SUKABUMI', 'E-SURABAYA', 'E-TAMBUN', 'E-TNG', 'E-TLNGG'
);

-- Step 5: Restore from backup (if needed)
-- Uncomment if you need to restore deleted data
/*
INSERT INTO areas 
SELECT * FROM areas_backup_20260220;
*/

-- ============================================================================
-- ROLLBACK COMPLETE
-- Note: If you need to recover data, use areas_backup_20260220 table
-- To drop backup table after confirming rollback: DROP TABLE areas_backup_20260220;
-- ============================================================================
