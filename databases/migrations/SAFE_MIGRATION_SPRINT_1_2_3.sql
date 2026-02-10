-- ==============================================================================
-- SAFE MIGRATION: SPRINT 1-3 BILLING ENHANCEMENTS
-- ==============================================================================
-- Purpose: Add billing method, renewal and amendment support
-- Database: optima_ci
-- Created: 2026-02-10
-- ==============================================================================

SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;

SELECT '=== STARTING MIGRATIONS ===' AS status;

-- ==============================================================================
-- SPRINT 1: BILLING METHOD FIELDS
-- ==============================================================================

SELECT '=== Sprint 1: Adding billing method to kontrak ===' AS status;

-- billing_method already exists, skip it
-- Add billing_notes and billing_start_date only
ALTER TABLE `kontrak` 
ADD COLUMN `billing_notes` 
    TEXT DEFAULT NULL 
    COMMENT 'Special billing instructions';

ALTER TABLE `kontrak` 
ADD COLUMN `billing_start_date` 
    DATE DEFAULT NULL 
    COMMENT 'Custom billing cycle start date';

-- Ensure index exists (safe to run if already exists with IF NOT EXISTS equivalent)
SELECT COUNT(*) INTO @index_exists 
FROM information_schema.statistics 
WHERE table_schema = 'optima_ci' 
AND table_name = 'kontrak' 
AND index_name = 'idx_billing_method';

SET @sql = IF(@index_exists = 0, 
    'ALTER TABLE kontrak ADD INDEX idx_billing_method (billing_method)', 
    'SELECT ''Index idx_billing_method already exists'' AS status');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

UPDATE `kontrak` SET `billing_method` = 'CYCLE' WHERE `billing_method` IS NULL;

SELECT 'Billing method fields added' AS status;

-- ==============================================================================
-- SPRINT 1: RENEWAL WORKFLOW FIELDS
-- ==============================================================================

SELECT '=== Sprint 1: Adding renewal workflow fields ===' AS status;

ALTER TABLE `kontrak` 
ADD COLUMN `parent_contract_id` 
    INT UNSIGNED DEFAULT NULL 
    COMMENT 'Parent contract ID for renewals' 
    AFTER `id`;

ALTER TABLE `kontrak` 
ADD COLUMN `is_renewal` 
    TINYINT(1) DEFAULT 0 
    COMMENT 'Is this a renewal contract' 
    AFTER `parent_contract_id`;

ALTER TABLE `kontrak` 
ADD COLUMN `renewal_generation` 
    INT UNSIGNED DEFAULT 0 
    COMMENT 'Renewal generation number' 
    AFTER `is_renewal`;

ALTER TABLE `kontrak` 
ADD COLUMN `renewal_initiated_at` 
    DATETIME DEFAULT NULL 
    AFTER `renewal_generation`;

ALTER TABLE `kontrak` 
ADD COLUMN `renewal_initiated_by` 
    INT UNSIGNED DEFAULT NULL 
    AFTER `renewal_initiated_at`;

ALTER TABLE `kontrak` 
ADD COLUMN `renewal_completed_at` 
    DATETIME DEFAULT NULL 
    AFTER `renewal_initiated_by`;

ALTER TABLE `kontrak` 
ADD INDEX `idx_parent_contract` (`parent_contract_id`);

ALTER TABLE `kontrak` 
ADD INDEX `idx_is_renewal` (`is_renewal`);

ALTER TABLE `kontrak` 
ADD INDEX `idx_renewal_generation` (`renewal_generation`);

-- Add foreign key after indexes
ALTER TABLE `kontrak` 
ADD CONSTRAINT `fk_kontrak_parent` 
    FOREIGN KEY (`parent_contract_id`) 
    REFERENCES `kontrak` (`id`) 
    ON DELETE SET NULL 
    ON UPDATE CASCADE;

SELECT 'Renewal fields added to kontrak' AS status;

-- ==============================================================================
-- SPRINT 1: RENEWAL WORKFLOW TABLES
-- ==============================================================================

SELECT '=== Sprint 1: Creating renewal workflow tables ===' AS status;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `contract_renewal_unit_map` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `parent_contract_id` INT UNSIGNED NOT NULL,
    `renewal_contract_id` INT UNSIGNED NOT NULL,
    `parent_unit_id` INT UNSIGNED DEFAULT NULL,
    `renewal_unit_id` INT UNSIGNED DEFAULT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SELECT 'Renewal workflow tables created' AS status;

-- ==============================================================================
-- SPRINT 2: UNIT BILLING SCHEDULES
-- ==============================================================================

SELECT '=== Sprint 2: Creating unit billing schedules ===' AS status;

CREATE TABLE IF NOT EXISTS `unit_billing_schedules` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `contract_id` INT UNSIGNED NOT NULL,
    `unit_id` INT UNSIGNED NOT NULL,
    `on_hire_date` DATE NOT NULL,
    `off_hire_date` DATE DEFAULT NULL,
    `billing_method` ENUM('CYCLE', 'PRORATE', 'MONTHLY_FIXED') DEFAULT 'CYCLE',
    `monthly_rate` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `billing_start_offset_days` INT DEFAULT 0,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `inventory_unit` 
ADD COLUMN `on_hire_date` 
    DATE DEFAULT NULL 
    COMMENT 'Date unit was hired out' 
    AFTER `kontrak_id`;

ALTER TABLE `inventory_unit` 
ADD COLUMN `off_hire_date` 
    DATE DEFAULT NULL 
    COMMENT 'Date unit was returned' 
    AFTER `on_hire_date`;

SELECT 'Unit billing schedules created' AS status;

-- ==============================================================================
-- SPRINT 3: CONTRACT AMENDMENTS
-- ==============================================================================

SELECT '=== Sprint 3: Creating contract amendments ===' AS status;

CREATE TABLE IF NOT EXISTS `contract_amendments` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `contract_id` INT UNSIGNED NOT NULL,
    `amendment_type` ENUM('RATE_CHANGE', 'UNIT_CHANGE', 'TERM_EXTENSION', 'OTHER') DEFAULT 'RATE_CHANGE',
    `effective_date` DATE NOT NULL,
    `reason` VARCHAR(255) DEFAULT NULL,
    `notes` TEXT DEFAULT NULL,
    `status` ENUM('DRAFT', 'PENDING', 'APPROVED', 'REJECTED', 'CANCELLED') DEFAULT 'DRAFT',
    `prorate_split` JSON DEFAULT NULL,
    `old_total_value` DECIMAL(15,2) DEFAULT 0.00,
    `new_total_value` DECIMAL(15,2) DEFAULT 0.00,
    `prorate_total` DECIMAL(15,2) DEFAULT 0.00,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `amendment_unit_rates` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `amendment_id` INT UNSIGNED NOT NULL,
    `unit_id` INT UNSIGNED NOT NULL,
    `old_rate` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `new_rate` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    `prorate_old_amount` DECIMAL(15,2) DEFAULT 0.00,
    `prorate_new_amount` DECIMAL(15,2) DEFAULT 0.00,
    `prorate_days_before` INT UNSIGNED DEFAULT 0,
    `prorate_days_after` INT UNSIGNED DEFAULT 0,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `inventory_unit` 
ADD COLUMN `rate_changed_at` 
    DATETIME DEFAULT NULL 
    COMMENT 'Last rate change timestamp' 
    AFTER `harga_sewa_bulanan`;

SELECT 'Contract amendments tables created' AS status;

-- ==============================================================================
-- CREATE VIEWS
-- ==============================================================================

SELECT '=== Creating views ===' AS status;

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

SELECT 'Views created' AS status;

-- ==============================================================================
-- VERIFICATION
-- ==============================================================================

SELECT '=== VERIFICATION ===' AS status;

SELECT 
    table_name,
    table_rows,
    ROUND(data_length / 1024, 2) AS data_kb
FROM information_schema.tables 
WHERE table_schema = 'optima_ci'
AND table_name IN (
    'contract_renewal_workflow',
    'contract_renewal_unit_map',
    'unit_billing_schedules',
    'contract_amendments',
    'amendment_unit_rates'
)
ORDER BY table_name;

SELECT 
    CONCAT('✓ ', column_name, ' added') AS verification
FROM information_schema.columns 
WHERE table_schema = 'optima_ci'
AND table_name = 'kontrak'
AND column_name IN (
    'billing_method',
    'parent_contract_id',
    'is_renewal',
    'renewal_generation'
)
ORDER BY ordinal_position;

-- Restore settings
SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;

SELECT '=== ALL MIGRATIONS COMPLETED SUCCESSFULLY ===' AS status;
SELECT CONCAT('Database optima_ci updated at ', NOW()) AS final_message;
