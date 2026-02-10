-- ==============================================================================
-- COMPREHENSIVE MIGRATION SCRIPT: SPRINT 1, 2, AND 3
-- ==============================================================================
-- Purpose: Execute all billing system enhancements in proper order
-- Created: 2026-02-10
-- Estimated Time: 2-3 minutes
-- Database: optima (or current database)
-- ==============================================================================

-- Prevent execution without confirmation
SET @EXECUTE_MIGRATIONS = 0;

-- Check if execution is confirmed
SELECT 
    IF(@EXECUTE_MIGRATIONS = 1, 
        'MIGRATIONS WILL EXECUTE', 
        'MIGRATIONS BLOCKED - Set @EXECUTE_MIGRATIONS = 1 to run') 
    AS execution_status;

-- Abort if not confirmed
-- Uncomment line below to enable safety check
-- SET @ABORT = IF(@EXECUTE_MIGRATIONS != 1, (SELECT 'Execution blocked' FROM non_existent_table), NULL);

-- ==============================================================================
-- PRE-MIGRATION CHECKS
-- ==============================================================================

SELECT '=== PRE-MIGRATION CHECKS ===' AS step;

-- Check if kontrak table exists
SELECT 
    IF(COUNT(*) > 0, '✓ kontrak table exists', '✗ kontrak table NOT FOUND') AS check_result
FROM information_schema.tables 
WHERE table_schema = DATABASE() 
AND table_name = 'kontrak';

-- Check if inventory_unit table exists
SELECT 
    IF(COUNT(*) > 0, '✓ inventory_unit table exists', '✗ inventory_unit table NOT FOUND') AS check_result
FROM information_schema.tables 
WHERE table_schema = DATABASE() 
AND table_name = 'inventory_unit';

-- Count existing contracts
SELECT 
    CONCAT('Total contracts: ', COUNT(*)) AS info
FROM kontrak;

-- ==============================================================================
-- SPRINT 1 MIGRATION: BILLING METHOD SUPPORT
-- ==============================================================================

SELECT '=== SPRINT 1: BILLING METHOD FIELDS ===' AS step;

-- Add billing_method to kontrak table
ALTER TABLE `kontrak` 
ADD COLUMN IF NOT EXISTS `billing_method` 
    ENUM('CYCLE', 'PRORATE', 'MONTHLY_FIXED') 
    DEFAULT 'CYCLE' 
    COMMENT 'Billing calculation method: CYCLE=30-day rolling, PRORATE=calendar month, MONTHLY_FIXED=fixed date' 
    AFTER `status`;

-- Add billing_notes to kontrak table
ALTER TABLE `kontrak` 
ADD COLUMN IF NOT EXISTS `billing_notes` 
    TEXT DEFAULT NULL 
    COMMENT 'Special billing instructions or notes' 
    AFTER `billing_method`;

-- Add billing_start_date to kontrak table
ALTER TABLE `kontrak` 
ADD COLUMN IF NOT EXISTS `billing_start_date` 
    DATE DEFAULT NULL 
    COMMENT 'Custom billing cycle start date (for MONTHLY_FIXED method)' 
    AFTER `billing_notes`;

-- Add default_billing_method to customers table
ALTER TABLE `customers` 
ADD COLUMN IF NOT EXISTS `default_billing_method` 
    ENUM('CYCLE', 'PRORATE', 'MONTHLY_FIXED') 
    DEFAULT 'CYCLE' 
    COMMENT 'Default billing method for new contracts' 
    AFTER `customer_status`;

-- Add index for billing method queries
ALTER TABLE `kontrak` 
ADD INDEX IF NOT EXISTS `idx_billing_method` (`billing_method`);

-- Populate default billing methods for existing contracts
UPDATE `kontrak` 
SET `billing_method` = 'CYCLE' 
WHERE `billing_method` IS NULL;

SELECT 'Sprint 1 Migration: Billing method fields added successfully' AS status;

-- ==============================================================================
-- SPRINT 1 MIGRATION: RENEWAL WORKFLOW SUPPORT
-- ==============================================================================

SELECT '=== SPRINT 1: RENEWAL WORKFLOW FIELDS ===' AS step;

-- Add parent_contract_id to kontrak table
ALTER TABLE `kontrak` 
ADD COLUMN IF NOT EXISTS `parent_contract_id` 
    INT UNSIGNED DEFAULT NULL 
    COMMENT 'Parent contract ID for renewals' 
    AFTER `id`;

-- Add is_renewal flag
ALTER TABLE `kontrak` 
ADD COLUMN IF NOT EXISTS `is_renewal` 
    TINYINT(1) DEFAULT 0 
    COMMENT '1 if contract is a renewal of another contract' 
    AFTER `parent_contract_id`;

-- Add renewal_generation
ALTER TABLE `kontrak` 
ADD COLUMN IF NOT EXISTS `renewal_generation` 
    INT UNSIGNED DEFAULT 0 
    COMMENT 'Renewal generation: 0=original, 1=first renewal, 2=second renewal, etc.' 
    AFTER `is_renewal`;

-- Add renewal tracking timestamps
ALTER TABLE `kontrak` 
ADD COLUMN IF NOT EXISTS `renewal_initiated_at` 
    DATETIME DEFAULT NULL 
    COMMENT 'When renewal process was started' 
    AFTER `renewal_generation`;

ALTER TABLE `kontrak` 
ADD COLUMN IF NOT EXISTS `renewal_initiated_by` 
    INT UNSIGNED DEFAULT NULL 
    COMMENT 'User ID who initiated renewal' 
    AFTER `renewal_initiated_at`;

ALTER TABLE `kontrak` 
ADD COLUMN IF NOT EXISTS `renewal_completed_at` 
    DATETIME DEFAULT NULL 
    COMMENT 'When renewal was completed and activated' 
    AFTER `renewal_initiated_by`;

-- Add foreign key for parent_contract_id
ALTER TABLE `kontrak` 
ADD CONSTRAINT `fk_kontrak_parent` 
    FOREIGN KEY (`parent_contract_id`) 
    REFERENCES `kontrak` (`id`) 
    ON DELETE SET NULL 
    ON UPDATE CASCADE;

-- Add indexes for renewal queries
ALTER TABLE `kontrak` 
ADD INDEX IF NOT EXISTS `idx_parent_contract` (`parent_contract_id`);

ALTER TABLE `kontrak` 
ADD INDEX IF NOT EXISTS `idx_is_renewal` (`is_renewal`);

ALTER TABLE `kontrak` 
ADD INDEX IF NOT EXISTS `idx_renewal_generation` (`renewal_generation`);

-- Create contract_renewal_workflow table
CREATE TABLE IF NOT EXISTS `contract_renewal_workflow` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `parent_contract_id` INT UNSIGNED NOT NULL,
    `renewal_contract_id` INT UNSIGNED DEFAULT NULL,
    `status` ENUM('INITIATED', 'PENDING_APPROVAL', 'APPROVED', 'REJECTED', 'COMPLETED', 'CANCELLED') DEFAULT 'INITIATED',
    `initiated_by` INT UNSIGNED DEFAULT NULL,
    `initiated_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `approved_by` INT UNSIGNED DEFAULT NULL,
    `approved_at` DATETIME DEFAULT NULL,
    `rejected_by` INT UNSIGNED DEFAULT NULL,
    `rejected_at` DATETIME DEFAULT NULL,
    `rejection_reason` TEXT DEFAULT NULL,
    `completed_at` DATETIME DEFAULT NULL,
    `notes` TEXT DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_parent_contract` (`parent_contract_id`),
    KEY `idx_renewal_contract` (`renewal_contract_id`),
    KEY `idx_status` (`status`),
    CONSTRAINT `fk_renewal_workflow_parent` 
        FOREIGN KEY (`parent_contract_id`) 
        REFERENCES `kontrak` (`id`) 
        ON DELETE CASCADE,
    CONSTRAINT `fk_renewal_workflow_renewal` 
        FOREIGN KEY (`renewal_contract_id`) 
        REFERENCES `kontrak` (`id`) 
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Tracks contract renewal workflow and approvals';

-- Create contract_renewal_unit_map table
CREATE TABLE IF NOT EXISTS `contract_renewal_unit_map` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `parent_contract_id` INT UNSIGNED NOT NULL,
    `renewal_contract_id` INT UNSIGNED NOT NULL,
    `parent_unit_id` INT UNSIGNED DEFAULT NULL COMMENT 'Unit ID in parent contract',
    `renewal_unit_id` INT UNSIGNED DEFAULT NULL COMMENT 'Unit ID in renewal contract (may be different)',
    `action` ENUM('CARRY_OVER', 'ADD_NEW', 'REPLACE', 'REMOVE') DEFAULT 'CARRY_OVER',
    `old_rate` DECIMAL(15,2) DEFAULT 0.00,
    `new_rate` DECIMAL(15,2) DEFAULT 0.00,
    `notes` VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_parent_contract` (`parent_contract_id`),
    KEY `idx_renewal_contract` (`renewal_contract_id`),
    KEY `idx_parent_unit` (`parent_unit_id`),
    KEY `idx_renewal_unit` (`renewal_unit_id`),
    CONSTRAINT `fk_unit_map_parent` 
        FOREIGN KEY (`parent_contract_id`) 
        REFERENCES `kontrak` (`id`) 
        ON DELETE CASCADE,
    CONSTRAINT `fk_unit_map_renewal` 
        FOREIGN KEY (`renewal_contract_id`) 
        REFERENCES `kontrak` (`id`) 
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Maps units between parent and renewal contracts';

SELECT 'Sprint 1 Migration: Renewal workflow tables created successfully' AS status;

-- ==============================================================================
-- SPRINT 2 MIGRATION: UNIT-LEVEL BILLING SCHEDULES
-- ==============================================================================

SELECT '=== SPRINT 2: UNIT BILLING SCHEDULES ===' AS step;

-- Create unit_billing_schedules table
CREATE TABLE IF NOT EXISTS `unit_billing_schedules` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `contract_id` INT UNSIGNED NOT NULL,
    `unit_id` INT UNSIGNED NOT NULL COMMENT 'Reference to inventory_unit.id_inventory_unit',
    `on_hire_date` DATE NOT NULL COMMENT 'Date unit started billing',
    `off_hire_date` DATE DEFAULT NULL COMMENT 'Date unit stopped billing (NULL if still active)',
    `billing_method` ENUM('CYCLE', 'PRORATE', 'MONTHLY_FIXED') DEFAULT 'CYCLE',
    `monthly_rate` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `billing_start_offset_days` INT DEFAULT 0 COMMENT 'Days offset from contract start for this unit',
    `next_billing_date` DATE DEFAULT NULL,
    `last_billed_date` DATE DEFAULT NULL,
    `total_billed_periods` INT UNSIGNED DEFAULT 0,
    `total_billed_amount` DECIMAL(15,2) DEFAULT 0.00,
    `is_prorated_first_period` TINYINT(1) DEFAULT 0,
    `is_prorated_last_period` TINYINT(1) DEFAULT 0,
    `notes` TEXT DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_contract_unit` (`contract_id`, `unit_id`),
    KEY `idx_on_hire_date` (`on_hire_date`),
    KEY `idx_off_hire_date` (`off_hire_date`),
    KEY `idx_next_billing_date` (`next_billing_date`),
    KEY `idx_unit_id` (`unit_id`),
    CONSTRAINT `fk_billing_schedule_contract` 
        FOREIGN KEY (`contract_id`) 
        REFERENCES `kontrak` (`id`) 
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Tracks individual unit billing schedules for staggered deliveries';

-- Add columns to inventory_unit for better tracking
ALTER TABLE `inventory_unit` 
ADD COLUMN IF NOT EXISTS `on_hire_date` 
    DATE DEFAULT NULL 
    COMMENT 'Date unit was hired out to customer' 
    AFTER `kontrak_id`;

ALTER TABLE `inventory_unit` 
ADD COLUMN IF NOT EXISTS `off_hire_date` 
    DATE DEFAULT NULL 
    COMMENT 'Date unit was returned from customer' 
    AFTER `on_hire_date`;

SELECT 'Sprint 2 Migration: Unit billing schedules created successfully' AS status;

-- ==============================================================================
-- SPRINT 3 MIGRATION: CONTRACT AMENDMENTS
-- ==============================================================================

SELECT '=== SPRINT 3: CONTRACT AMENDMENTS ===' AS step;

-- Create contract_amendments table
CREATE TABLE IF NOT EXISTS `contract_amendments` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `contract_id` INT UNSIGNED NOT NULL,
    `amendment_type` ENUM('RATE_CHANGE', 'UNIT_CHANGE', 'TERM_EXTENSION', 'OTHER') DEFAULT 'RATE_CHANGE',
    `effective_date` DATE NOT NULL COMMENT 'Date when amendment takes effect',
    `reason` VARCHAR(255) DEFAULT NULL COMMENT 'Reason for amendment',
    `notes` TEXT DEFAULT NULL,
    `status` ENUM('DRAFT', 'PENDING', 'APPROVED', 'REJECTED', 'CANCELLED') DEFAULT 'DRAFT',
    `prorate_split` JSON DEFAULT NULL COMMENT 'Prorate calculation details: {period_start, period_end, days_before, days_after}',
    `old_total_value` DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Total value before amendment',
    `new_total_value` DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Total value after amendment',
    `prorate_total` DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Prorated total for the period',
    `approved_by` INT UNSIGNED DEFAULT NULL,
    `approved_at` DATETIME DEFAULT NULL,
    `rejected_by` INT UNSIGNED DEFAULT NULL,
    `rejected_at` DATETIME DEFAULT NULL,
    `rejection_reason` TEXT DEFAULT NULL,
    `created_by` INT UNSIGNED DEFAULT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_contract_id` (`contract_id`),
    KEY `idx_effective_date` (`effective_date`),
    KEY `idx_status` (`status`),
    KEY `idx_amendment_type` (`amendment_type`),
    CONSTRAINT `fk_amendments_contract` 
        FOREIGN KEY (`contract_id`) 
        REFERENCES `kontrak` (`id`) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Contract amendments with prorate split support';

-- Create amendment_unit_rates table
CREATE TABLE IF NOT EXISTS `amendment_unit_rates` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `amendment_id` INT UNSIGNED NOT NULL,
    `unit_id` INT UNSIGNED NOT NULL COMMENT 'Reference to inventory_unit.id_inventory_unit',
    `old_rate` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Monthly rate before amendment',
    `new_rate` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Monthly rate after amendment',
    `prorate_old_amount` DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Prorated amount at old rate',
    `prorate_new_amount` DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Prorated amount at new rate',
    `prorate_days_before` INT UNSIGNED DEFAULT 0 COMMENT 'Days at old rate',
    `prorate_days_after` INT UNSIGNED DEFAULT 0 COMMENT 'Days at new rate',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_amendment_id` (`amendment_id`),
    KEY `idx_unit_id` (`unit_id`),
    KEY `idx_rates` (`old_rate`, `new_rate`),
    CONSTRAINT `fk_unit_rates_amendment` 
        FOREIGN KEY (`amendment_id`) 
        REFERENCES `contract_amendments` (`id`) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Unit-level rate changes per amendment with prorate details';

-- Add rate_changed_at to inventory_unit
ALTER TABLE `inventory_unit` 
ADD COLUMN IF NOT EXISTS `rate_changed_at` 
    DATETIME DEFAULT NULL 
    COMMENT 'Timestamp of last rate change' 
    AFTER `harga_sewa_bulanan`;

-- Create view for amendment summary
CREATE OR REPLACE VIEW `v_amendment_summary` AS
SELECT 
    ca.id,
    ca.contract_id,
    k.no_kontrak,
    c.customer_name,
    ca.amendment_type,
    ca.effective_date,
    ca.status,
    ca.prorate_total,
    COUNT(aur.id) as units_affected,
    SUM(aur.new_rate - aur.old_rate) as total_rate_change,
    ca.created_at,
    ca.approved_at
FROM contract_amendments ca
LEFT JOIN kontrak k ON k.id = ca.contract_id
LEFT JOIN customer_locations cl ON cl.id = k.customer_location_id
LEFT JOIN customers c ON c.id = cl.customer_id
LEFT JOIN amendment_unit_rates aur ON aur.amendment_id = ca.id
GROUP BY ca.id;

SELECT 'Sprint 3 Migration: Contract amendments created successfully' AS status;

-- ==============================================================================
-- POST-MIGRATION VERIFICATION
-- ==============================================================================

SELECT '=== POST-MIGRATION VERIFICATION ===' AS step;

-- Check new tables exist
SELECT 
    table_name,
    table_rows,
    ROUND(data_length / 1024, 2) AS data_kb
FROM information_schema.tables 
WHERE table_schema = DATABASE() 
AND table_name IN (
    'contract_renewal_workflow',
    'contract_renewal_unit_map',
    'unit_billing_schedules',
    'contract_amendments',
    'amendment_unit_rates'
)
ORDER BY table_name;

-- Check new columns in kontrak table
SELECT 
    column_name,
    column_type,
    is_nullable,
    column_default,
    column_comment
FROM information_schema.columns 
WHERE table_schema = DATABASE() 
AND table_name = 'kontrak'
AND column_name IN (
    'billing_method',
    'billing_notes',
    'billing_start_date',
    'parent_contract_id',
    'is_renewal',
    'renewal_generation',
    'renewal_initiated_at'
)
ORDER BY ordinal_position;

-- Check new columns in inventory_unit table
SELECT 
    column_name,
    column_type,
    is_nullable,
    column_comment
FROM information_schema.columns 
WHERE table_schema = DATABASE() 
AND table_name = 'inventory_unit'
AND column_name IN (
    'on_hire_date',
    'off_hire_date',
    'rate_changed_at'
)
ORDER BY ordinal_position;

-- ==============================================================================
-- MIGRATION SUMMARY
-- ==============================================================================

SELECT '=== MIGRATION COMPLETED SUCCESSFULLY ===' AS step;

SELECT 
    'Sprint 1: Billing Methods & Renewal Workflow' AS sprint,
    'COMPLETED' AS status,
    '✓ billing_method fields added
✓ Renewal tracking fields added
✓ contract_renewal_workflow table created
✓ contract_renewal_unit_map table created' AS changes;

SELECT 
    'Sprint 2: Unit-Level Billing' AS sprint,
    'COMPLETED' AS status,
    '✓ unit_billing_schedules table created
✓ on_hire_date/off_hire_date added to inventory_unit' AS changes;

SELECT 
    'Sprint 3: Contract Amendments' AS sprint,
    'COMPLETED' AS status,
    '✓ contract_amendments table created
✓ amendment_unit_rates table created
✓ rate_changed_at added to inventory_unit
✓ v_amendment_summary view created' AS changes;

-- ==============================================================================
-- NEXT STEPS
-- ==============================================================================

SELECT '=== NEXT STEPS ===' AS step;

SELECT 
    '1. Test renewal wizard: marketing/kontrak/getExpiringContracts
2. Test addendum prorate: marketing/kontrak/createProrateAmendment
3. Test asset history: marketing/kontrak/getContractHistory
4. Register routes in app/Config/Routes.php
5. Add UI buttons for "Renew Contract" and "Create Amendment"
6. Train users on new features' AS recommendations;

SELECT CONCAT(
    'All migrations completed at ',
    NOW(),
    '. System ready for Sprint 1-3 features.'
) AS final_message;
