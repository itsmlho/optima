-- =====================================================================
-- MIGRATION: Unit Billing Schedules Table
-- Date: 2026-02-10
-- Purpose: Enable unit-level billing tracking for staggered deliveries
--          and individual unit billing cycles
-- =====================================================================

-- CREATE TABLE: unit_billing_schedules
-- Tracks billing schedule per unit to support:
-- - Staggered deliveries (different on-hire dates per unit)
-- - Individual billing cycles per unit
-- - Unit-specific rate overrides
-- - Back-billing detection (missing invoices per unit)
CREATE TABLE IF NOT EXISTS `unit_billing_schedules` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `contract_id` INT UNSIGNED NOT NULL COMMENT 'FK to kontrak table',
    `unit_id` INT UNSIGNED NOT NULL COMMENT 'FK to unit table',
    `on_hire_date` DATE NOT NULL COMMENT 'Date when unit started rental (dapat berbeda per unit)',
    `off_hire_date` DATE NULL COMMENT 'Date when unit ended rental (NULL = masih aktif)',
    `billing_method` ENUM('CYCLE', 'PRORATE', 'MONTHLY_FIXED') NOT NULL DEFAULT 'CYCLE' COMMENT 'Billing calculation method (inherit dari contract atau override)',
    `billing_start_date` INT NULL COMMENT 'For MONTHLY_FIXED: billing date (1-31)',
    `next_billing_date` DATE NULL COMMENT 'Next scheduled billing date',
    `last_billed_date` DATE NULL COMMENT 'Last date that was successfully billed',
    `monthly_rate` DECIMAL(15,2) NOT NULL COMMENT 'Monthly rental rate for this unit',
    `status` ENUM('PENDING', 'ACTIVE', 'PAUSED', 'COMPLETED', 'CANCELLED') NOT NULL DEFAULT 'PENDING' COMMENT 'Billing status',
    `notes` TEXT NULL COMMENT 'Unit-specific billing notes',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_contract_id` (`contract_id`),
    KEY `idx_unit_id` (`unit_id`),
    KEY `idx_status` (`status`),
    KEY `idx_next_billing_date` (`next_billing_date`),
    KEY `idx_on_hire_date` (`on_hire_date`),
    CONSTRAINT `fk_unit_billing_contract` FOREIGN KEY (`contract_id`) REFERENCES `kontrak` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_unit_billing_unit` FOREIGN KEY (`unit_id`) REFERENCES `unit` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Unit-level billing schedules for staggered deliveries';

-- CREATE INDEX: Composite index for contract + status queries
CREATE INDEX `idx_contract_status` ON `unit_billing_schedules` (`contract_id`, `status`);

-- CREATE INDEX: Composite index for back-billing detection
CREATE INDEX `idx_backbilling_detection` ON `unit_billing_schedules` (`status`, `next_billing_date`, `last_billed_date`);

-- =====================================================================
-- INITIAL DATA POPULATION
-- Populate from existing contract_units table (if exists)
-- =====================================================================

-- Check if contract_units table exists, then populate unit_billing_schedules
INSERT INTO `unit_billing_schedules` 
    (`contract_id`, `unit_id`, `on_hire_date`, `off_hire_date`, `billing_method`, `monthly_rate`, `status`)
SELECT 
    cu.contract_id,
    cu.unit_id,
    COALESCE(cu.on_hire_date, k.start_date) AS on_hire_date,
    cu.off_hire_date,
    COALESCE(k.billing_method, 'CYCLE') AS billing_method,
    COALESCE(cu.monthly_rate, 0) AS monthly_rate,
    CASE 
        WHEN cu.off_hire_date IS NOT NULL THEN 'COMPLETED'
        WHEN cu.on_hire_date IS NOT NULL THEN 'ACTIVE'
        ELSE 'PENDING'
    END AS status
FROM `contract_units` cu
INNER JOIN `kontrak` k ON cu.contract_id = k.id
WHERE NOT EXISTS (
    SELECT 1 FROM `unit_billing_schedules` ubs 
    WHERE ubs.contract_id = cu.contract_id AND ubs.unit_id = cu.unit_id
)
ON DUPLICATE KEY UPDATE
    `updated_at` = CURRENT_TIMESTAMP;

-- =====================================================================
-- CALCULATE INITIAL next_billing_date FOR ACTIVE SCHEDULES
-- Uses contract billing_method to determine next billing
-- =====================================================================

-- For CYCLE billing: next_billing_date = on_hire_date + 30 days (then +30 each cycle)
UPDATE `unit_billing_schedules` ubs
INNER JOIN `kontrak` k ON ubs.contract_id = k.id
SET ubs.next_billing_date = DATE_ADD(ubs.on_hire_date, INTERVAL 30 DAY)
WHERE ubs.status = 'ACTIVE' 
  AND ubs.billing_method = 'CYCLE'
  AND ubs.next_billing_date IS NULL;

-- For PRORATE billing: next_billing_date = first day of next month
UPDATE `unit_billing_schedules` ubs
INNER JOIN `kontrak` k ON ubs.contract_id = k.id
SET ubs.next_billing_date = DATE_ADD(LAST_DAY(ubs.on_hire_date), INTERVAL 1 DAY)
WHERE ubs.status = 'ACTIVE' 
  AND ubs.billing_method = 'PRORATE'
  AND ubs.next_billing_date IS NULL;

-- For MONTHLY_FIXED billing: next_billing_date = billing_start_date of next month
UPDATE `unit_billing_schedules` ubs
INNER JOIN `kontrak` k ON ubs.contract_id = k.id
SET ubs.next_billing_date = DATE_ADD(
    DATE_FORMAT(ubs.on_hire_date, CONCAT('%Y-%m-', LPAD(COALESCE(k.billing_start_date, 1), 2, '0'))),
    INTERVAL IF(DAY(ubs.on_hire_date) >= COALESCE(k.billing_start_date, 1), 1, 0) MONTH
)
WHERE ubs.status = 'ACTIVE' 
  AND ubs.billing_method = 'MONTHLY_FIXED'
  AND ubs.next_billing_date IS NULL;

COMMIT;

-- =====================================================================
-- ROLLBACK SCRIPT (for migration reversal)
-- =====================================================================
-- DROP TABLE IF EXISTS `unit_billing_schedules`;
