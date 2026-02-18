-- =====================================================
-- Migration: Alter kontrak Table - Add Rental Modes
-- Date: 2026-02-15
-- Purpose: Support multiple rental scenarios (PO-only, Spot, Verbal)
-- Dependencies: kontrak table exists
-- Note: rental_type and billing_method already exist, we extend them
-- =====================================================

-- Extend existing rental_type enum to include VERBAL_AGREEMENT
-- Cannot ALTER ENUM directly, so we check if value exists via stored procedure approach is complex
-- For now, we add new complementary fields

-- Add fast_track flag for quick rental approval bypass
ALTER TABLE kontrak ADD COLUMN fast_track BOOLEAN DEFAULT FALSE;

-- Add spot rental number
ALTER TABLE kontrak ADD COLUMN spot_rental_number VARCHAR(50) NULL;

-- Add estimated duration for spot rentals
ALTER TABLE kontrak ADD COLUMN estimated_duration_days INT NULL;

-- Add actual return date for billing accuracy
ALTER TABLE kontrak ADD COLUMN actual_return_date DATE NULL;

-- Add requires_po_approval flag
ALTER TABLE kontrak ADD COLUMN requires_po_approval BOOLEAN DEFAULT FALSE;

-- Add index for fast filtering
CREATE INDEX idx_kontrak_fast_track ON kontrak(fast_track, status);
CREATE INDEX idx_kontrak_spot_rental ON kontrak(spot_rental_number);

-- =====================================================
-- Data Migration: Set defaults for existing records
-- =====================================================

-- Note: rental_mode not added as rental_type already exists
-- Note: billing_basis not added as billing_method already exists
-- Set fast_track to FALSE for all existing contracts (default)
UPDATE kontrak SET fast_track = FALSE WHERE fast_track IS NULL;

-- =====================================================
-- Verification Queries
-- =====================================================

-- Check column additions
-- SHOW COLUMNS FROM kontrak LIKE '%rental_mode%';
-- SHOW COLUMNS FROM kontrak LIKE '%fast_track%';

-- Count contracts by rental mode
-- SELECT rental_mode, COUNT(*) as total 
-- FROM kontrak 
-- GROUP BY rental_mode;

-- List spot rentals
-- SELECT 
--     nomor_kontrak, spot_rental_number, 
--     rental_mode, jenis_sewa, 
--     tanggal_mulai, estimated_duration_days,
--     status_kontrak
-- FROM kontrak 
-- WHERE rental_mode = 'SPOT_RENTAL'
-- ORDER BY tanggal_mulai DESC;

-- Check fast-track contracts
-- SELECT 
--     nomor_kontrak, rental_mode, fast_track,
--     requires_document, status_kontrak
-- FROM kontrak
-- WHERE fast_track = TRUE;

-- =====================================================
-- Rollback (if needed)
-- =====================================================
-- ALTER TABLE kontrak DROP COLUMN IF EXISTS rental_mode;
-- ALTER TABLE kontrak DROP COLUMN IF EXISTS requires_document;
-- ALTER TABLE kontrak DROP COLUMN IF EXISTS fast_track;
-- ALTER TABLE kontrak DROP COLUMN IF EXISTS billing_basis;
-- ALTER TABLE kontrak DROP COLUMN IF EXISTS spot_rental_number;
-- ALTER TABLE kontrak DROP COLUMN IF EXISTS estimated_duration_days;
-- ALTER TABLE kontrak DROP COLUMN IF EXISTS actual_return_date;
-- DROP INDEX IF EXISTS idx_kontrak_rental_mode ON kontrak;
-- DROP INDEX IF EXISTS idx_kontrak_fast_track ON kontrak;
-- DROP INDEX IF EXISTS idx_kontrak_billing ON kontrak;
