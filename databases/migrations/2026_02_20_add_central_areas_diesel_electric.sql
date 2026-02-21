-- ============================================================================
-- Migration: Add Central Areas for DIESEL and ELECTRIC Departments
-- Date: 2026-02-20
-- Description: Insert 115 CENTRAL type areas (61 DIESEL + 54 ELECTRIC)
--              Admin pusat akan menghandle seluruh area CENTRAL per departemen
--              Admin branch akan di-assign ke multiple area (D-LOKASI + E-LOKASI)
-- 
-- IMPORTANT: 
-- - Verify no duplicate area_code before running
-- - Check existing areas: SELECT area_code FROM areas WHERE area_code LIKE 'D-%' OR area_code LIKE 'E-%';
-- - This script uses APPEND mode - existing data will NOT be deleted
-- ============================================================================

-- ============================================================================
-- SECTION 1: DIESEL DEPARTMENT CENTRAL AREAS (61 areas)
-- Prefix: D- | Type: CENTRAL | Department: DIESEL
-- ============================================================================

INSERT INTO areas (area_code, area_name, area_description, area_type, is_active) VALUES
('D-ANCOL', 'Ancol', 'Area Ancol - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-BLRJ', 'Balaraja', 'Area Balaraja - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-BANDUNG', 'Bandung', 'Area Bandung - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-BANJAR', 'Banjar', 'Area Banjar - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-BATANG', 'Batang', 'Area Batang - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-BEKASI', 'Bekasi', 'Area Bekasi - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-BLORA', 'Blora', 'Area Blora - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-BOGOR', 'Bogor', 'Area Bogor - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-BREBES', 'Brebes', 'Area Brebes - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-CAKUNG', 'Cakung', 'Area Cakung - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-CIANJUR', 'Cianjur', 'Area Cianjur - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-CIBINONG', 'Cibinong', 'Area Cibinong - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-CIBITUNG', 'Cibitung', 'Area Cibitung - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-CKMPK', 'Cikampek', 'Area Cikampek - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-CIKARANG', 'Cikarang', 'Area Cikarang - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-CIKUPA', 'Cikupa', 'Area Cikupa - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-CLNGSI', 'Cileungsi', 'Area Cileungsi - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-CILEGON', 'Cilegon', 'Area Cilegon - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-CIMANGGS', 'Cimanggis', 'Area Cimanggis - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-CIRACAS', 'Ciracas', 'Area Ciracas - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-CIREBON', 'Cirebon', 'Area Cirebon - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-DNMGT', 'Daan Mogot', 'Area Daan Mogot - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-EJIP', 'EJIP', 'Area EJIP - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-GDBG', 'Gedebage', 'Area Gedebage - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-GRESIK', 'Gresik', 'Area Gresik - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-HARIAN', 'Harian', 'Area Harian - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-HYUNDAI', 'Hyundai', 'Area Hyundai - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-JABABEKA', 'Jababeka', 'Area Jababeka - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-JAKARTA', 'Jakarta', 'Area Jakarta - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-JATENG', 'Jawa Tengah', 'Area Jawa Tengah - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-JATIM', 'Jawa Timur', 'Area Jawa Timur - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-JAWILAN', 'Jawilan', 'Area Jawilan - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-JEPARA', 'Jepara', 'Area Jepara - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-KALASAN', 'Kalasan', 'Area Kalasan - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-KRWC', 'Karawaci', 'Area Karawaci - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-KARAWANG', 'Karawang', 'Area Karawang - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-KLATEN', 'Klaten', 'Area Klaten - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-KOPO', 'Kopo', 'Area Kopo - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-MJLGK', 'Majalengka', 'Area Majalengka - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-MALANG', 'Malang', 'Area Malang - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-MM2100', 'MM2100', 'Area MM2100 - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-NAROGONG', 'Narogong', 'Area Narogong - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-PDLRG', 'Padalarang', 'Area Padalarang - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-PANDAAN', 'Pandaan', 'Area Pandaan - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-PSRRB', 'Pasar Rebo', 'Area Pasar Rebo - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-PATI', 'Pati', 'Area Pati - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-PIYUNGAN', 'Piyungan', 'Area Piyungan - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-PLGDG', 'Pulo Gadung', 'Area Pulo Gadung - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-PWKRT', 'Purwakarta', 'Area Purwakarta - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-PWDAD', 'Purwodadi', 'Area Purwodadi - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-PWSAR', 'Purwosari', 'Area Purwosari - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-REMBANG', 'Rembang', 'Area Rembang - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-SAWANGAN', 'Sawangan', 'Area Sawangan - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-SEMARANG', 'Semarang', 'Area Semarang - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-SERANG', 'Serang', 'Area Serang - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-SERPONG', 'Serpong', 'Area Serpong - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-SIDOARJO', 'Sidoarjo', 'Area Sidoarjo - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-SOLO', 'Solo', 'Area Solo - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-SUBANG', 'Subang', 'Area Subang - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-SUKABUMI', 'Sukabumi', 'Area Sukabumi - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-SURABAYA', 'Surabaya', 'Area Surabaya - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-TNG', 'Tangerang', 'Area Tangerang - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1),
('D-YGY', 'Yogyakarta', 'Area Yogyakarta - Wilayah operasional Departemen DIESEL', 'CENTRAL', 1);

-- ============================================================================
-- SECTION 2: ELECTRIC DEPARTMENT CENTRAL AREAS (54 areas)
-- Prefix: E- | Type: CENTRAL | Department: ELECTRIC
-- ============================================================================

INSERT INTO areas (area_code, area_name, area_description, area_type, is_active) VALUES
('E-BANDUNG', 'Bandung', 'Area Bandung - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-BATANG', 'Batang', 'Area Batang - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-BEKASI', 'Bekasi', 'Area Bekasi - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-BOGOR', 'Bogor', 'Area Bogor - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-BREBES', 'Brebes', 'Area Brebes - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-CIBITUNG', 'Cibitung', 'Area Cibitung - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-CCLGK', 'Cicalengka', 'Area Cicalengka - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-CIANJUR', 'Cianjur', 'Area Cianjur - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-CKMPK', 'Cikampek', 'Area Cikampek - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-CIKANDE', 'Cikande', 'Area Cikande - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-CKR1', 'Cikarang 1', 'Area Cikarang 1 - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-CKR2', 'Cikarang 2', 'Area Cikarang 2 - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-CIKUPA', 'Cikupa', 'Area Cikupa - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-CILEGON', 'Cilegon', 'Area Cilegon - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-CIMANGGS', 'Cimanggis', 'Area Cimanggis - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-CIREBON', 'Cirebon', 'Area Cirebon - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-CITEUREP', 'Citeurep', 'Area Citeurep - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-DNMGT', 'Daan Mogot', 'Area Daan Mogot - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-DELTAMAS', 'Deltamas', 'Area Deltamas - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-DUMAI', 'Dumai', 'Area Dumai - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-GDBG', 'Gedebage', 'Area Gedebage - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-GRESIK', 'Gresik', 'Area Gresik - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-INDRMY', 'Indramayu', 'Area Indramayu - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-JBK1', 'Jababeka 1', 'Area Jababeka 1 - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-JBK2', 'Jababeka 2', 'Area Jababeka 2 - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-JAKARTA', 'Jakarta', 'Area Jakarta - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-JOGJA', 'Jogja', 'Area Jogja - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-JOMBANG', 'Jombang', 'Area Jombang - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-KARAWANG', 'Karawang', 'Area Karawang - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-KEDIRI', 'Kediri', 'Area Kediri - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-KLATEN', 'Klaten', 'Area Klaten - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-KOSAMBI', 'Kosambi', 'Area Kosambi - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-KRIAN', 'Krian', 'Area Krian - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-MADIUN', 'Madiun', 'Area Madiun - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-MJLGK', 'Majalengka', 'Area Majalengka - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-MALANG', 'Malang', 'Area Malang - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-MEDAN', 'Medan', 'Area Medan - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-PDLRG', 'Padalarang', 'Area Padalarang - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-PLMBG', 'Palembang', 'Area Palembang - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-PANDAAN', 'Pandaan', 'Area Pandaan - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-PASURUAN', 'Pasuruan', 'Area Pasuruan - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-PERAWANG', 'Perawang', 'Area Perawang - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-PWKRT', 'Purwakarta', 'Area Purwakarta - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-PWSAR', 'Purwosari', 'Area Purwosari - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-RNCEK', 'Rancaekek', 'Area Rancaekek - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-SEMARANG', 'Semarang', 'Area Semarang - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-SERANG', 'Serang', 'Area Serang - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-SIDOARJO', 'Sidoarjo', 'Area Sidoarjo - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-SUBANG', 'Subang', 'Area Subang - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-SUKABUMI', 'Sukabumi', 'Area Sukabumi - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-SURABAYA', 'Surabaya', 'Area Surabaya - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-TAMBUN', 'Tambun', 'Area Tambun - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-TNG', 'Tangerang', 'Area Tangerang - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1),
('E-TLNGG', 'Tulungagung', 'Area Tulungagung - Wilayah operasional Departemen ELECTRIC', 'CENTRAL', 1);

-- ============================================================================
-- VALIDATION QUERIES
-- Run these after migration to verify data integrity
-- ============================================================================

-- Should show total CENTRAL areas (existing + new 115)
SELECT 'Total CENTRAL areas:' AS info, COUNT(*) AS count 
FROM areas 
WHERE area_type = 'CENTRAL';

-- Should show 61 DIESEL areas
SELECT 'DIESEL CENTRAL areas:' AS info, COUNT(*) AS count 
FROM areas 
WHERE area_code LIKE 'D-%' AND area_type = 'CENTRAL';

-- Should show 54 ELECTRIC areas
SELECT 'ELECTRIC CENTRAL areas:' AS info, COUNT(*) AS count 
FROM areas 
WHERE area_code LIKE 'E-%' AND area_type = 'CENTRAL';

-- Check for duplicate area_code (should return 0 rows)
SELECT 'Duplicate area_code check:' AS info;
SELECT area_code, COUNT(*) as count 
FROM areas 
GROUP BY area_code 
HAVING COUNT(*) > 1;

-- Display all new DIESEL areas
SELECT 'All DIESEL CENTRAL areas:' AS info;
SELECT area_code, area_name, area_type, is_active 
FROM areas 
WHERE area_code LIKE 'D-%' AND area_type = 'CENTRAL'
ORDER BY area_code;

-- Display all new ELECTRIC areas
SELECT 'All ELECTRIC CENTRAL areas:' AS info;
SELECT area_code, area_name, area_type, is_active 
FROM areas 
WHERE area_code LIKE 'E-%' AND area_type = 'CENTRAL'
ORDER BY area_code;

-- ============================================================================
-- MIGRATION COMPLETE
-- Next steps:
-- 1. Verify data via Service Area Management UI
-- 2. Assign admin pusat DIESEL to all D-* areas via area_employee_assignments
-- 3. Assign admin pusat ELECTRIC to all E-* areas via area_employee_assignments
-- 4. Assign admin branch to both D-LOKASI and E-LOKASI where applicable
-- ============================================================================
