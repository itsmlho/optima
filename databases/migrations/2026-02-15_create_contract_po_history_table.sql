-- =====================================================
-- Migration: Create Contract PO History Table
-- Date: 2026-02-15
-- Purpose: Track multiple PO numbers per contract (monthly PO rotation)
-- Dependencies: kontrak table
-- =====================================================

-- Drop table if exists (for development/testing only)
-- DROP TABLE IF EXISTS contract_po_history;

CREATE TABLE IF NOT EXISTS contract_po_history (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    
    -- Contract Reference
    contract_id INT UNSIGNED NOT NULL COMMENT 'Reference to kontrak table',
    
    -- PO Details
    po_number VARCHAR(100) NOT NULL COMMENT 'Customer Purchase Order number',
    po_date DATE NOT NULL COMMENT 'Date PO was issued by customer',
    po_value DECIMAL(15,2) NULL COMMENT 'Total PO amount (if specified by customer)',
    po_description TEXT COMMENT 'PO scope or description',
    
    -- Effective Period
    effective_from DATE NOT NULL COMMENT 'Date this PO becomes active',
    effective_to DATE NULL COMMENT 'Date this PO expires (NULL = current/active)',
    
    -- Document Management
    po_document VARCHAR(255) COMMENT 'Uploaded PO file path (PDF/image)',
    document_upload_date TIMESTAMP NULL COMMENT 'When document was uploaded',
    
    -- Status & Tracking
    status ENUM('ACTIVE','EXPIRED','SUPERSEDED','CANCELLED') DEFAULT 'ACTIVE' COMMENT 'PO status',
    superseded_by_po_id INT UNSIGNED NULL COMMENT 'If superseded, ID of replacement PO',
    
    -- Billing Relationship
    invoice_count INT DEFAULT 0 COMMENT 'Number of invoices created with this PO',
    total_invoiced DECIMAL(15,2) DEFAULT 0 COMMENT 'Total amount invoiced under this PO',
    
    -- Customer Communication
    customer_contact_person VARCHAR(100) COMMENT 'Customer contact for this PO',
    customer_email VARCHAR(100) COMMENT 'Email for PO correspondence',
    customer_phone VARCHAR(20) COMMENT 'Phone for PO queries',
    
    -- Notes & Metadata
    notes TEXT COMMENT 'Additional notes about this PO',
    internal_notes TEXT COMMENT 'Internal notes (not for customer view)',
    tags JSON COMMENT 'Tags for categorization',
    
    -- Audit Fields
    created_by INT UNSIGNED COMMENT 'User who created this record',
    updated_by INT UNSIGNED COMMENT 'User who last updated',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL COMMENT 'Soft delete timestamp',
    
    -- Foreign Keys
    FOREIGN KEY (contract_id) REFERENCES kontrak(id) ON DELETE CASCADE,
    FOREIGN KEY (superseded_by_po_id) REFERENCES contract_po_history(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Tracks multiple PO numbers per contract for customers who provide monthly POs';

-- Create Indexes for performance
CREATE INDEX idx_po_contract ON contract_po_history(contract_id, status);
CREATE INDEX idx_po_effective ON contract_po_history(effective_from, effective_to);
CREATE INDEX idx_po_number ON contract_po_history(po_number);
CREATE INDEX idx_po_status ON contract_po_history(status);
CREATE INDEX idx_po_active ON contract_po_history(contract_id, status, effective_from, effective_to);
CREATE INDEX idx_po_date_range ON contract_po_history(contract_id, effective_from, effective_to);

-- =====================================================
-- Sample Data (for testing)
-- =====================================================

-- Example: Contract with monthly PO rotation
-- Assuming contract_id = 1 exists
-- INSERT INTO contract_po_history (contract_id, po_number, po_date, effective_from, effective_to, status) VALUES
--     (1, 'PO-2025-12-001', '2025-12-01', '2025-12-01', '2025-12-31', 'EXPIRED'),
--     (1, 'PO-2026-01-001', '2026-01-01', '2026-01-01', '2026-01-31', 'EXPIRED'),
--     (1, 'PO-2026-02-001', '2026-02-01', '2026-02-01', NULL, 'ACTIVE');

-- =====================================================
-- Verification Queries
-- =====================================================

-- Check table structure
-- DESCRIBE contract_po_history;

-- Count POs by status
-- SELECT status, COUNT(*) as total FROM contract_po_history GROUP BY status;

-- List PO history for a contract
-- SELECT 
--     cph.id, cph.po_number, cph.po_date, 
--     cph.effective_from, cph.effective_to, 
--     cph.status, cph.invoice_count, cph.total_invoiced
-- FROM contract_po_history cph
-- WHERE cph.contract_id = ?
-- ORDER BY cph.effective_from DESC;

-- Get current active PO for contracts
-- SELECT 
--     k.nomor_kontrak, k.customer_id,
--     cph.po_number, cph.effective_from, cph.status
-- FROM kontrak k
-- LEFT JOIN contract_po_history cph ON cph.contract_id = k.id 
--     AND cph.status = 'ACTIVE' 
--     AND cph.effective_to IS NULL
-- WHERE k.status_kontrak = 'ACTIVE'
-- ORDER BY k.nomor_kontrak;

-- Find PO applicable for specific date
-- SELECT * FROM contract_po_history
-- WHERE contract_id = ?
--   AND effective_from <= ?
--   AND (effective_to >= ? OR effective_to IS NULL)
-- LIMIT 1;

-- Check for PO gaps (missing PO for active contracts)
-- SELECT k.id, k.nomor_kontrak, k.customer_id
-- FROM kontrak k
-- LEFT JOIN contract_po_history cph ON cph.contract_id = k.id AND cph.status = 'ACTIVE'
-- WHERE k.status_kontrak = 'ACTIVE'
--   AND k.rental_type = 'CONTRACT'
--   AND cph.id IS NULL;

-- =====================================================
-- Rollback (if needed)
-- =====================================================
-- DROP TABLE IF EXISTS contract_po_history;
