-- =====================================================
-- Migration: Create Operators Table
-- Date: 2026-02-15
-- Purpose: Master data for operators/mechanics/drivers
-- Dependencies: None
-- =====================================================

-- Drop table if exists (for development/testing only)
-- DROP TABLE IF EXISTS operators;

CREATE TABLE IF NOT EXISTS operators (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    
    -- Operator Identification
    operator_code VARCHAR(50) UNIQUE NOT NULL COMMENT 'Unique operator code (e.g., OP-001)',
    operator_name VARCHAR(100) NOT NULL COMMENT 'Full name of operator',
    nik VARCHAR(20) COMMENT 'National ID number',
    
    -- Certification & Skills
    certification_level ENUM('BASIC','INTERMEDIATE','ADVANCED','EXPERT') DEFAULT 'BASIC' COMMENT 'Skill level',
    certification_number VARCHAR(100) COMMENT 'Certificate number',
    certification_issued_date DATE COMMENT 'Date certificate was issued',
    certification_expiry DATE COMMENT 'Certificate expiration date',
    certification_issuer VARCHAR(100) COMMENT 'Certifying organization',
    skills JSON COMMENT 'Array of equipment types operator is certified for',
    
    -- Pricing (Monthly Package Rate)
    monthly_rate DECIMAL(15,2) NOT NULL DEFAULT 0 COMMENT 'Standard monthly package rate',
    daily_rate DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT 'Daily rate (for short-term)',
    hourly_rate DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT 'Hourly rate (for spot work)',
    
    -- Contact Information
    phone VARCHAR(20) COMMENT 'Primary phone number',
    email VARCHAR(100) COMMENT 'Email address',
    address TEXT COMMENT 'Residential address',
    emergency_contact_name VARCHAR(100) COMMENT 'Emergency contact person',
    emergency_contact_phone VARCHAR(20) COMMENT 'Emergency contact phone',
    
    -- Employment Details
    employment_type ENUM('PERMANENT','CONTRACT','FREELANCE') DEFAULT 'PERMANENT',
    join_date DATE COMMENT 'Date joined company',
    employee_id VARCHAR(50) COMMENT 'Link to HR employee ID if applicable',
    
    -- Status & Availability
    status ENUM('AVAILABLE','ASSIGNED','ON_LEAVE','INACTIVE','TERMINATED') DEFAULT 'AVAILABLE',
    current_assignment_id INT COMMENT 'Current contract assignment ID',
    
    -- Notes & Documents
    notes TEXT COMMENT 'Additional notes about operator',
    photo VARCHAR(255) COMMENT 'Photo file path',
    documents JSON COMMENT 'Array of document attachments (certificates, licenses)',
    
    -- Audit Fields
    created_by INT COMMENT 'User who created this record',
    updated_by INT COMMENT 'User who last updated this record',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL COMMENT 'Soft delete timestamp',
    
    -- Full-text search
    FULLTEXT INDEX idx_operator_search (operator_name, operator_code, nik)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Master data for equipment operators, mechanics, and drivers';

-- Create Indexes for performance
CREATE INDEX idx_operator_status ON operators(status);
CREATE INDEX idx_operator_certification ON operators(certification_level, certification_expiry);
CREATE INDEX idx_operator_employment ON operators(employment_type, join_date);
CREATE INDEX idx_operator_availability ON operators(status, current_assignment_id);

-- Insert sample operators (for development/testing)
INSERT INTO operators (
    operator_code, operator_name, certification_level, 
    certification_number, monthly_rate, daily_rate, hourly_rate,
    phone, employment_type, status
) VALUES
    ('OP-001', 'Budi Santoso', 'EXPERT', 'CERT-FL-2024-001', 8000000, 400000, 50000, '081234567890', 'PERMANENT', 'AVAILABLE'),
    ('OP-002', 'Ahmad Fauzi', 'ADVANCED', 'CERT-FL-2024-002', 7000000, 350000, 45000, '081234567891', 'PERMANENT', 'AVAILABLE'),
    ('OP-003', 'Rizki Pratama', 'INTERMEDIATE', 'CERT-FL-2025-001', 6000000, 300000, 40000, '081234567892', 'CONTRACT', 'AVAILABLE'),
    ('OP-004', 'Eko Wijaya', 'BASIC', 'CERT-FL-2025-002', 5000000, 250000, 35000, '081234567893', 'FREELANCE', 'AVAILABLE')
ON DUPLICATE KEY UPDATE operator_name = VALUES(operator_name);

-- =====================================================
-- Verification Queries
-- =====================================================

-- Check table structure
-- DESCRIBE operators;

-- Count operators by status
-- SELECT status, COUNT(*) as total FROM operators GROUP BY status;

-- List available operators
-- SELECT operator_code, operator_name, certification_level, monthly_rate, status 
-- FROM operators WHERE status = 'AVAILABLE' ORDER BY certification_level DESC;

-- =====================================================
-- Rollback (if needed)
-- =====================================================
-- DROP TABLE IF EXISTS operators;
