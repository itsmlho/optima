-- =====================================================
-- Migration: Alter kontrak_spesifikasi - Add Operator Fields
-- Date: 2026-02-15
-- Purpose: Add operator pricing to quotation specifications
-- Dependencies: kontrak_spesifikasi table exists
-- =====================================================

-- Add operator-related fields to quotation specifications
ALTER TABLE quotation_specifications ADD COLUMN include_operator BOOLEAN DEFAULT FALSE;
ALTER TABLE quotation_specifications ADD COLUMN operator_monthly_rate DECIMAL(15,2) DEFAULT 0;
ALTER TABLE quotation_specifications ADD COLUMN operator_daily_rate DECIMAL(10,2) DEFAULT 0;
ALTER TABLE quotation_specifications ADD COLUMN operator_description VARCHAR(255) NULL;
ALTER TABLE quotation_specifications ADD COLUMN operator_certification_required BOOLEAN DEFAULT FALSE;

-- Create index for filtering specs with operators
CREATE INDEX idx_spec_operator ON quotation_specifications(include_operator);

-- =====================================================
-- Verification Queries
-- =====================================================

-- Check column additions
-- SHOW COLUMNS FROM kontrak_spesifikasi LIKE '%operator%';

-- Count specs with operator inclusion
-- SELECT 
--     include_operator,
--     COUNT(*) as total,
--     AVG(operator_monthly_rate) as avg_operator_rate
-- FROM kontrak_spesifikasi
-- GROUP BY include_operator;

-- List specs with operator service
-- SELECT 
--     ks.id, k.nomor_kontrak,
--     ks.kuantitas, ks.harga_per_unit_bulanan,
--     ks.include_operator, ks.operator_monthly_rate,
--     ks.operator_description
-- FROM kontrak_spesifikasi ks
-- JOIN kontrak k ON k.id = ks.kontrak_id
-- WHERE ks.include_operator = TRUE;

-- Calculate total pricing (unit + operator)
-- SELECT 
--     k.nomor_kontrak,
--     SUM(ks.kuantitas * ks.harga_per_unit_bulanan) as unit_total,
--     SUM(CASE WHEN ks.include_operator THEN ks.kuantitas * ks.operator_monthly_rate ELSE 0 END) as operator_total,
--     SUM(ks.kuantitas * (ks.harga_per_unit_bulanan + 
--         CASE WHEN ks.include_operator THEN ks.operator_monthly_rate ELSE 0 END)) as grand_total
-- FROM kontrak_spesifikasi ks
-- JOIN kontrak k ON k.id = ks.kontrak_id
-- WHERE k.status_kontrak = 'ACTIVE'
-- GROUP BY k.nomor_kontrak;

-- =====================================================
-- Rollback (if needed)
-- =====================================================
-- ALTER TABLE kontrak_spesifikasi DROP COLUMN IF EXISTS include_operator;
-- ALTER TABLE kontrak_spesifikasi DROP COLUMN IF EXISTS operator_monthly_rate;
-- ALTER TABLE kontrak_spesifikasi DROP COLUMN IF EXISTS operator_daily_rate;
-- ALTER TABLE kontrak_spesifikasi DROP COLUMN IF EXISTS operator_description;
-- ALTER TABLE kontrak_spesifikasi DROP COLUMN IF EXISTS operator_certification_required;
-- DROP INDEX IF EXISTS idx_spec_operator ON kontrak_spesifikasi;
