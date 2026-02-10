-- Sprint 3 Migration: Contract Amendments Support
-- Purpose: Enable mid-period rate changes with prorate split tracking
-- Created: 2026-02-10
-- Dependencies: kontrak table must exist

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

-- Add index for inventory_unit rate_changed_at tracking
ALTER TABLE `inventory_unit` 
ADD COLUMN IF NOT EXISTS `rate_changed_at` DATETIME DEFAULT NULL COMMENT 'Timestamp of last rate change' 
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

SELECT 'Migration: Contract Amendments created successfully' AS status;
