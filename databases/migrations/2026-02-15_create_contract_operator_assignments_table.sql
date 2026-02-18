-- =====================================================
-- Migration: Create Contract Operator Assignments Table
-- Date: 2026-02-15
-- Purpose: Track operator assignments to rental contracts
-- Dependencies: operators, kontrak tables
-- =====================================================

-- Drop table if exists (for development/testing only)
-- DROP TABLE IF EXISTS contract_operator_assignments;

CREATE TABLE IF NOT EXISTS contract_operator_assignments (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    
    -- Assignment Details
    contract_id INT UNSIGNED NOT NULL COMMENT 'Reference to kontrak table',
    operator_id INT UNSIGNED NOT NULL COMMENT 'Reference to operators table',
    
    -- Assignment Period
    assignment_start DATE NOT NULL COMMENT 'Date operator assignment begins',
    assignment_end DATE NULL COMMENT 'Date operator assignment ends (NULL = ongoing)',
    actual_end_date DATE NULL COMMENT 'Actual date operator stopped (if different from planned)',
    
    -- Pricing & Billing
    billing_type ENUM('MONTHLY_PACKAGE','DAILY_RATE','HOURLY_RATE') DEFAULT 'MONTHLY_PACKAGE',
    monthly_billing_rate DECIMAL(15,2) NOT NULL DEFAULT 0 COMMENT 'Monthly rate for this assignment',
    daily_billing_rate DECIMAL(10,2) DEFAULT 0 COMMENT 'Daily rate (if applicable)',
    hourly_billing_rate DECIMAL(10,2) DEFAULT 0 COMMENT 'Hourly rate (if applicable)',
    
    -- Work Schedule & Terms
    work_hours_per_day INT DEFAULT 8 COMMENT 'Standard work hours per day',
    work_days_per_week INT DEFAULT 5 COMMENT 'Work days per week (5 or 6)',
    overtime_allowed BOOLEAN DEFAULT TRUE COMMENT 'Whether overtime is allowed',
    overtime_rate_multiplier DECIMAL(5,2) DEFAULT 1.5 COMMENT 'Overtime rate multiplier',
    
    -- Assignment Scope
    equipment_assigned VARCHAR(255) COMMENT 'Specific equipment/units operator is assigned to',
    location_id INT UNSIGNED COMMENT 'Work location (customer_location_id)',
    shift_schedule ENUM('DAY_SHIFT','NIGHT_SHIFT','ROTATING','ON_CALL') DEFAULT 'DAY_SHIFT',
    
    -- Status & Performance
    status ENUM('PENDING','ACTIVE','COMPLETED','CANCELLED','TERMINATED') DEFAULT 'PENDING',
    performance_rating DECIMAL(3,2) COMMENT 'Rating 0-5.0 (added after assignment)',
    performance_notes TEXT COMMENT 'Performance feedback',
    
    -- Special Conditions
    billing_notes TEXT COMMENT 'Special billing terms or notes',
    contract_notes TEXT COMMENT 'Assignment terms and conditions',
    replacement_for_assignment_id INT UNSIGNED NULL COMMENT 'If this is a replacement, ID of original assignment',
    
    -- Approval Workflow
    approved_by INT COMMENT 'User who approved this assignment',
    approved_at TIMESTAMP NULL COMMENT 'Approval timestamp',
    rejection_reason TEXT COMMENT 'If rejected, reason for rejection',
    
    -- Audit Fields
    created_by INT COMMENT 'User who created this assignment',
    updated_by INT COMMENT 'User who last updated',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL COMMENT 'Soft delete timestamp',
    
    -- Foreign Keys
    FOREIGN KEY (contract_id) REFERENCES kontrak(id) ON DELETE CASCADE,
    FOREIGN KEY (operator_id) REFERENCES operators(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Tracks operator assignments to rental contracts for billing and management';

-- Create Indexes for performance
CREATE INDEX idx_assignment_contract ON contract_operator_assignments(contract_id, status);
CREATE INDEX idx_assignment_operator ON contract_operator_assignments(operator_id, status);
CREATE INDEX idx_assignment_dates ON contract_operator_assignments(assignment_start, assignment_end);
CREATE INDEX idx_assignment_status ON contract_operator_assignments(status);
CREATE INDEX idx_assignment_location ON contract_operator_assignments(location_id, status);
CREATE INDEX idx_assignment_active ON contract_operator_assignments(status, assignment_start, assignment_end);

-- =====================================================
-- Verification Queries
-- =====================================================

-- Check table structure
-- DESCRIBE contract_operator_assignments;

-- Count assignments by status
-- SELECT status, COUNT(*) as total FROM contract_operator_assignments GROUP BY status;

-- List active assignments with operator details
-- SELECT 
--     coa.id, k.nomor_kontrak, o.operator_name, 
--     coa.assignment_start, coa.monthly_billing_rate, coa.status
-- FROM contract_operator_assignments coa
-- JOIN kontrak k ON k.id = coa.contract_id
-- JOIN operators o ON o.id = coa.operator_id
-- WHERE coa.status = 'ACTIVE'
-- ORDER BY coa.assignment_start DESC;

-- Check for overlapping assignments (same operator, overlapping dates)
-- SELECT o.operator_name, COUNT(*) as concurrent_assignments
-- FROM contract_operator_assignments coa
-- JOIN operators o ON o.id = coa.operator_id
-- WHERE coa.status = 'ACTIVE' 
-- AND coa.assignment_end IS NULL
-- GROUP BY o.operator_name
-- HAVING concurrent_assignments > 1;

-- =====================================================
-- Rollback (if needed)
-- =====================================================
-- DROP TABLE IF EXISTS contract_operator_assignments;
