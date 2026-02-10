-- ============================================================================
-- OPTIMA RENTAL WORKFLOW - CONTRACT RENEWAL ENHANCEMENT
-- ============================================================================
-- Migration: Add renewal tracking fields for gap-free contract transitions
-- Purpose: Enable renewal workflow with approval stages and unit mapping
-- Date: 2026-02-10
-- Author: GitHub Copilot
-- ============================================================================

USE optima_ci;

-- ============================================================================
-- 1. ADD RENEWAL CHAIN TRACKING TO KONTRAK
-- ============================================================================

ALTER TABLE kontrak 
ADD COLUMN parent_contract_id INT UNSIGNED NULL 
    COMMENT 'Previous contract in renewal chain'
    AFTER status,
ADD COLUMN is_renewal BOOLEAN DEFAULT FALSE 
    COMMENT 'Is this a renewed contract?'
    AFTER parent_contract_id,
ADD COLUMN renewal_generation INT DEFAULT 1 
    COMMENT 'Generation: 1=original, 2=first renewal, 3=second renewal, etc'
    AFTER is_renewal,
ADD COLUMN renewal_initiated_at DATETIME NULL 
    COMMENT 'When renewal process started'
    AFTER renewal_generation,
ADD COLUMN renewal_initiated_by INT UNSIGNED NULL 
    COMMENT 'User who initiated renewal'
    AFTER renewal_initiated_at;

-- Add foreign keys
ALTER TABLE kontrak
ADD CONSTRAINT fk_parent_contract 
    FOREIGN KEY (parent_contract_id) REFERENCES kontrak(id) ON DELETE SET NULL,
ADD CONSTRAINT fk_renewal_initiator 
    FOREIGN KEY (renewal_initiated_by) REFERENCES users(id) ON DELETE SET NULL;

-- Add indexes
ALTER TABLE kontrak
ADD INDEX idx_parent_contract (parent_contract_id),
ADD INDEX idx_is_renewal (is_renewal),
ADD INDEX idx_renewal_generation (renewal_generation);

-- ============================================================================
-- 2. CREATE RENEWAL WORKFLOW TABLE
-- ============================================================================

CREATE TABLE IF NOT EXISTS contract_renewal_workflow (
    id INT AUTO_INCREMENT PRIMARY KEY,
    old_contract_id INT UNSIGNED NOT NULL COMMENT 'Original contract being renewed',
    new_contract_id INT UNSIGNED NULL COMMENT 'New contract (after renewal approved)',
    status ENUM(
        'INITIATED',           -- User clicked "Renew" button
        'UNIT_CHECK',          -- System checking unit availability
        'PENDING_APPROVAL',    -- Waiting for manager approval
        'APPROVED',            -- Approved, waiting for activation date
        'CUSTOMER_REVIEW',     -- Sent to customer for confirmation
        'ACTIVATED',           -- Renewal completed, new contract active
        'REJECTED',            -- Renewal rejected
        'CANCELLED'            -- Renewal cancelled by user
    ) DEFAULT 'INITIATED',
    renewal_type ENUM('AUTO', 'MANUAL') DEFAULT 'MANUAL' 
        COMMENT 'AUTO=triggered by system, MANUAL=user initiated',
    
    -- Workflow timestamps
    initiated_by INT UNSIGNED NOT NULL,
    initiated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    approved_by INT UNSIGNED NULL,
    approved_at DATETIME NULL,
    activated_by INT UNSIGNED NULL,
    activated_at DATETIME NULL,
    
    -- Additional data
    rejection_reason TEXT NULL,
    workflow_notes JSON NULL COMMENT 'Store approval chain, revision history, unit changes',
    
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign keys
    FOREIGN KEY (old_contract_id) REFERENCES kontrak(id) ON DELETE CASCADE,
    FOREIGN KEY (new_contract_id) REFERENCES kontrak(id) ON DELETE CASCADE,
    FOREIGN KEY (initiated_by) REFERENCES users(id),
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (activated_by) REFERENCES users(id) ON DELETE SET NULL,
    
    -- Indexes
    INDEX idx_status (status),
    INDEX idx_old_contract (old_contract_id),
    INDEX idx_renewal_type (renewal_type)
) ENGINE=InnoDB COMMENT='Track contract renewal workflow stages';

-- ============================================================================
-- 3. CREATE RENEWAL UNIT MAPPING TABLE
-- ============================================================================

CREATE TABLE IF NOT EXISTS contract_renewal_unit_map (
    id INT AUTO_INCREMENT PRIMARY KEY,
    renewal_workflow_id INT NOT NULL,
    old_unit_id INT UNSIGNED NULL COMMENT 'Unit from old contract (can be NULL if new unit)',
    new_unit_id INT UNSIGNED NOT NULL COMMENT 'Unit for new contract',
    is_replacement BOOLEAN DEFAULT FALSE COMMENT 'TRUE if unit was replaced due to unavailability',
    replacement_reason VARCHAR(255) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (renewal_workflow_id) REFERENCES contract_renewal_workflow(id) ON DELETE CASCADE,
    FOREIGN KEY (old_unit_id) REFERENCES inventory_unit(id) ON DELETE SET NULL,
    FOREIGN KEY (new_unit_id) REFERENCES inventory_unit(id) ON DELETE CASCADE,
    
    INDEX idx_renewal_workflow (renewal_workflow_id)
) ENGINE=InnoDB COMMENT='Map old units to new units in renewal process';

-- ============================================================================
-- 4. UPDATE KONTRAK STATUS ENUM (add renewal statuses)
-- ============================================================================

ALTER TABLE kontrak 
MODIFY COLUMN status ENUM(
    'DRAFT',
    'APPROVED',
    'ACTIVE',
    'COMPLETED',
    'TERMINATED',
    'EXPIRED',
    'DRAFT_RENEWAL',      -- NEW: Renewal in draft state
    'PENDING_RENEWAL',    -- NEW: Renewal waiting approval
    'RENEWAL_REJECTED'    -- NEW: Renewal rejected by approver
) DEFAULT 'DRAFT';

-- ============================================================================
-- VERIFICATION QUERIES
-- ============================================================================

-- Check kontrak table updates
SELECT 
    'Kontrak Renewal Fields' as check_type,
    COLUMN_NAME,
    COLUMN_TYPE,
    COLUMN_DEFAULT,
    COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'optima_ci' 
  AND TABLE_NAME = 'kontrak' 
  AND (COLUMN_NAME LIKE '%renewal%' OR COLUMN_NAME = 'parent_contract_id');

-- Check new tables created
SHOW TABLES LIKE 'contract_renewal%';

-- Check foreign keys
SELECT 
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'optima_ci'
  AND CONSTRAINT_NAME LIKE '%renewal%';

-- ============================================================================
-- ROLLBACK SCRIPT (if needed)
-- ============================================================================

/*
-- Rollback instructions (DO NOT RUN unless rollback needed):

-- Drop tables
DROP TABLE IF EXISTS contract_renewal_unit_map;
DROP TABLE IF EXISTS contract_renewal_workflow;

-- Remove constraints and fields from kontrak
ALTER TABLE kontrak 
DROP FOREIGN KEY fk_parent_contract,
DROP FOREIGN KEY fk_renewal_initiator,
DROP INDEX idx_parent_contract,
DROP INDEX idx_is_renewal,
DROP INDEX idx_renewal_generation,
DROP COLUMN renewal_initiated_by,
DROP COLUMN renewal_initiated_at,
DROP COLUMN renewal_generation,
DROP COLUMN is_renewal,
DROP COLUMN parent_contract_id;

-- Revert status ENUM (remove renewal statuses)
ALTER TABLE kontrak 
MODIFY COLUMN status ENUM(
    'DRAFT',
    'APPROVED',
    'ACTIVE',
    'COMPLETED',
    'TERMINATED',
    'EXPIRED'
) DEFAULT 'DRAFT';

*/

-- ============================================================================
-- MIGRATION COMPLETE
-- ============================================================================

SELECT 'Contract Renewal Migration completed successfully!' as status,
       NOW() as completed_at;
