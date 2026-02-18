-- =====================================================
-- FIX CONTRACT & PO RELATIONSHIPS
-- Date: 2026-02-17
-- Purpose: Support flexible contract/PO system:
--   - 1 kontrak/PO bisa untuk banyak lokasi
--   - Harga berbeda per lokasi  
--   - Support: KONTRAK only, PO only, BOTH, RECURRING_PO, NONE
-- =====================================================

-- STEP 1: Tambah document_type di table kontrak
ALTER TABLE kontrak 
ADD COLUMN document_type ENUM('KONTRAK', 'PO', 'AGREEMENT', 'RECURRING_PO', 'STATUS_PENDING') 
DEFAULT 'KONTRAK' 
COMMENT 'Type of document: KONTRAK (fixed contract), PO (purchase order), RECURRING_PO (monthly/periodic PO), AGREEMENT (basic agreement without doc number)'
AFTER no_kontrak;

-- STEP 2: Update existing data based on no_kontrak pattern
UPDATE kontrak 
SET document_type = CASE
    WHEN no_kontrak LIKE 'PO%' OR no_kontrak LIKE '%/PO/%' THEN 'PO'
    WHEN no_kontrak LIKE '%Agrement%' OR no_kontrak LIKE '%Agreement%' THEN 'AGREEMENT'
    WHEN no_kontrak IN ('PO PERBULAN', 'PO Perbulan', 'PO On Progres', 'dalam proses') THEN 'STATUS_PENDING'
    WHEN no_kontrak REGEXP '^[0-9]+$' THEN 'PO'  -- Pure numbers = PO
    ELSE 'KONTRAK'
END;

-- STEP 3: Buat table baru untuk kontrak ↔ locations (many-to-many)
CREATE TABLE IF NOT EXISTS kontrak_locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kontrak_id INT UNSIGNED NOT NULL COMMENT 'FK to kontrak table',
    customer_location_id INT NOT NULL COMMENT 'FK to customer_locations table',
    harga_per_lokasi DECIMAL(15,2) DEFAULT 0 COMMENT 'Harga spesifik untuk lokasi ini (bisa berbeda per lokasi)',
    jumlah_unit INT DEFAULT 0 COMMENT 'Jumlah unit di lokasi ini',
    catatan TEXT COMMENT 'Catatan khusus untuk lokasi ini',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (kontrak_id) REFERENCES kontrak(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (customer_location_id) REFERENCES customer_locations(id) ON DELETE CASCADE ON UPDATE CASCADE,
    
    UNIQUE KEY uk_kontrak_location (kontrak_id, customer_location_id),
    INDEX idx_kontrak_id (kontrak_id),
    INDEX idx_location_id (customer_location_id)
) ENGINE=InnoDB 
COMMENT='Many-to-many: 1 kontrak/PO bisa untuk banyak lokasi dengan harga berbeda';

-- STEP 4: Migrate existing data dari kontrak ke kontrak_locations
-- (jika kontrak sudah ada customer_location_id)
INSERT INTO kontrak_locations (kontrak_id, customer_location_id, harga_per_lokasi, is_active, created_at, updated_at)
SELECT 
    id as kontrak_id,
    customer_location_id,
    nilai_total as harga_per_lokasi,
    1 as is_active,
    NOW() as created_at,
    NOW() as updated_at
FROM kontrak 
WHERE customer_location_id IS NOT NULL
ON DUPLICATE KEY UPDATE updated_at = NOW();

-- STEP 5: Tambah field untuk customer contract flexibility
ALTER TABLE customer_contracts 
ADD COLUMN contract_type ENUM('KONTRAK_ONLY', 'PO_ONLY', 'BOTH', 'RECURRING_PO', 'NONE') 
DEFAULT 'KONTRAK_ONLY'
COMMENT 'How this customer uses documents'
AFTER kontrak_id;

-- STEP 6: Make kontrak_id NULLABLE in customer_contracts
-- (karena ada customer yang tidak pakai kontrak/PO)
ALTER TABLE customer_contracts 
MODIFY COLUMN kontrak_id INT UNSIGNED NULL COMMENT 'FK to kontrak - NULL jika customer tidak pakai kontrak/PO';

-- STEP 7: Drop unique constraint yang terlalu strict
ALTER TABLE customer_contracts 
DROP INDEX uk_customer_kontrak;

-- STEP 8: Add new composite index
ALTER TABLE customer_contracts
ADD UNIQUE KEY uk_customer_kontrak_type (customer_id, kontrak_id, contract_type);

-- =====================================================
-- SUMMARY OF CHANGES:
-- =====================================================
-- ✓ kontrak.document_type: Distinguish KONTRAK vs PO vs AGREEMENT vs STATUS
-- ✓ kontrak_locations table: 1 kontrak → many locations with different prices
-- ✓ customer_contracts.contract_type: Customer document policy
-- ✓ customer_contracts.kontrak_id: Now NULLABLE for customers without contracts
-- ✓ Migrated existing kontrak → kontrak_locations data
-- =====================================================
