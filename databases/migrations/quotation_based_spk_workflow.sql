-- ============================================================================
-- OPTIMA RENTAL WORKFLOW - QUOTATION-BASED SPK WITH CONTRACT RECONCILIATION
-- ============================================================================
-- Migration: Enable SPK creation from Quotations without requiring Contract
-- Purpose: Allow operational flexibility while maintaining financial control
-- Date: 2026-02-07
-- ============================================================================

USE optima_ci;

-- ============================================================================
-- 1. MODIFY SPK TABLE - Add Contract Linking Metadata
-- ============================================================================

-- Add new columns to spk table
ALTER TABLE `spk` 
ADD COLUMN `contract_linked_at` DATETIME NULL COMMENT 'Timestamp when contract was linked to this SPK' AFTER `kontrak_id`,
ADD COLUMN `contract_linked_by` INT UNSIGNED NULL COMMENT 'User ID who linked the contract' AFTER `contract_linked_at`,
ADD COLUMN `source_type` ENUM('CONTRACT', 'QUOTATION') DEFAULT 'CONTRACT' COMMENT 'SPK creation source' AFTER `quotation_specification_id`;

-- Add foreign key for contract_linked_by
ALTER TABLE `spk`
ADD CONSTRAINT `fk_spk_contract_linked_by` 
FOREIGN KEY (`contract_linked_by`) REFERENCES `users`(`id`) 
ON DELETE SET NULL;

-- Add index for quotation_specification_id (if not exists)
ALTER TABLE `spk`
ADD INDEX `idx_spk_quotation_specification` (`quotation_specification_id`);

-- Add index for source_type filtering
ALTER TABLE `spk`
ADD INDEX `idx_spk_source_type` (`source_type`);

-- ============================================================================
-- 2. MODIFY DELIVERY_INSTRUCTIONS TABLE - Add Contract Linking & Billing
-- ============================================================================

-- Add new columns to delivery_instructions table
ALTER TABLE `delivery_instructions`
ADD COLUMN `contract_id` INT UNSIGNED NULL COMMENT 'FK to kontrak table' AFTER `spk_id`,
ADD COLUMN `bast_date` DATE NULL COMMENT 'Berita Acara Serah Terima date for billing' AFTER `tanggal_kirim`,
ADD COLUMN `billing_start_date` DATE NULL COMMENT 'Auto-calculated billing start date from BAST' AFTER `bast_date`,
ADD COLUMN `contract_linked_at` DATETIME NULL COMMENT 'Timestamp when contract was linked (inherited from SPK)' AFTER `billing_start_date`,
ADD COLUMN `contract_linked_by` INT UNSIGNED NULL COMMENT 'User ID who linked contract (inherited from SPK)' AFTER `contract_linked_at`;

-- Modify status ENUM to add AWAITING_CONTRACT
ALTER TABLE `delivery_instructions`
MODIFY COLUMN `status` ENUM('SUBMITTED', 'PROCESSED', 'SHIPPED', 'DELIVERED', 'AWAITING_CONTRACT', 'CANCELLED') 
DEFAULT 'SUBMITTED' 
COMMENT 'DI Status - AWAITING_CONTRACT added for quotation-based workflow';

-- Add foreign keys
ALTER TABLE `delivery_instructions`
ADD CONSTRAINT `fk_di_contract` 
FOREIGN KEY (`contract_id`) REFERENCES `kontrak`(`id`) 
ON DELETE SET NULL,
ADD CONSTRAINT `fk_di_contract_linked_by` 
FOREIGN KEY (`contract_linked_by`) REFERENCES `users`(`id`) 
ON DELETE SET NULL;

-- Add indexes for performance
ALTER TABLE `delivery_instructions`
ADD INDEX `idx_di_contract_id` (`contract_id`),
ADD INDEX `idx_di_status` (`status`),
ADD INDEX `idx_di_bast_date` (`bast_date`),
ADD INDEX `idx_di_billing_start_date` (`billing_start_date`);

-- ============================================================================
-- 3. CREATE CONTRACT_AMENDMENTS TABLE
-- ============================================================================

CREATE TABLE IF NOT EXISTS `contract_amendments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_contract_id` INT UNSIGNED NOT NULL COMMENT 'FK to kontrak table',
  `amendment_number` VARCHAR(50) NOT NULL COMMENT 'Amendment reference number',
  `amendment_date` DATE NOT NULL COMMENT 'Date amendment was created',
  `reason` TEXT NOT NULL COMMENT 'Reason for amendment',
  `price_change_percent` DECIMAL(5,2) NULL COMMENT 'Percentage change in price',
  `new_monthly_rate` DECIMAL(15,2) NOT NULL COMMENT 'New monthly rate after amendment',
  `effective_date` DATE NOT NULL COMMENT 'Date when amendment takes effect',
  `created_by` INT UNSIGNED NOT NULL COMMENT 'User who created amendment',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_amendment_number` (`amendment_number`),
  KEY `idx_parent_contract` (`parent_contract_id`),
  KEY `idx_effective_date` (`effective_date`),
  CONSTRAINT `fk_amendment_parent_contract` 
    FOREIGN KEY (`parent_contract_id`) REFERENCES `kontrak`(`id`) 
    ON DELETE RESTRICT,
  CONSTRAINT `fk_amendment_created_by` 
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) 
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Contract amendments for price/term changes';

-- ============================================================================
-- 4. CREATE CONTRACT_RENEWALS TABLE
-- ============================================================================

CREATE TABLE IF NOT EXISTS `contract_renewals` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `original_contract_id` INT UNSIGNED NOT NULL COMMENT 'FK to original kontrak',
  `renewed_contract_id` INT UNSIGNED NOT NULL COMMENT 'FK to renewed kontrak',
  `renewal_date` DATE NOT NULL COMMENT 'Date of renewal',
  `same_location` BOOLEAN DEFAULT 1 COMMENT 'Unit stays at same location (no re-delivery)',
  `notes` TEXT NULL COMMENT 'Renewal notes/reasons',
  `created_by` INT UNSIGNED NOT NULL COMMENT 'User who created renewal',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_renewed_contract` (`renewed_contract_id`),
  KEY `idx_original_contract` (`original_contract_id`),
  KEY `idx_renewal_date` (`renewal_date`),
  CONSTRAINT `fk_renewal_original_contract` 
    FOREIGN KEY (`original_contract_id`) REFERENCES `kontrak`(`id`) 
    ON DELETE RESTRICT,
  CONSTRAINT `fk_renewal_renewed_contract` 
    FOREIGN KEY (`renewed_contract_id`) REFERENCES `kontrak`(`id`) 
    ON DELETE RESTRICT,
  CONSTRAINT `fk_renewal_created_by` 
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) 
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Contract renewals linking old and new contracts';

-- ============================================================================
-- 5. CREATE DATABASE TRIGGER - Propagate Contract from SPK to DI
-- ============================================================================

DELIMITER //

DROP TRIGGER IF EXISTS `propagate_contract_to_di`//

CREATE TRIGGER `propagate_contract_to_di`
AFTER UPDATE ON `spk`
FOR EACH ROW
BEGIN
  -- When SPK gets linked to contract (kontrak_id changes from NULL to value)
  IF NEW.kontrak_id IS NOT NULL AND OLD.kontrak_id IS NULL THEN
    
    -- Update all delivery instructions from this SPK
    UPDATE delivery_instructions 
    SET 
      contract_id = NEW.kontrak_id,
      contract_linked_at = NEW.contract_linked_at,
      contract_linked_by = NEW.contract_linked_by,
      -- Change status from AWAITING_CONTRACT to DELIVERED
      status = CASE 
        WHEN status = 'AWAITING_CONTRACT' THEN 'DELIVERED'
        ELSE status 
      END
    WHERE spk_id = NEW.id;
    
  END IF;
END//

DELIMITER ;

-- ============================================================================
-- 6. DATA MIGRATION - Set source_type for existing SPKs
-- ============================================================================

-- Mark existing SPKs with contracts as CONTRACT source
UPDATE `spk` 
SET `source_type` = 'CONTRACT' 
WHERE `kontrak_id` IS NOT NULL;

-- Mark existing SPKs with quotations but no contract as QUOTATION source
UPDATE `spk` 
SET `source_type` = 'QUOTATION' 
WHERE `kontrak_id` IS NULL 
  AND `quotation_specification_id` IS NOT NULL;

-- ============================================================================
-- 7. DATA MIGRATION - Link existing DIs to contracts via SPK
-- ============================================================================

-- Update existing DIs with contract_id from their parent SPK
UPDATE `delivery_instructions` di
INNER JOIN `spk` s ON di.spk_id = s.id
SET di.contract_id = s.kontrak_id
WHERE s.kontrak_id IS NOT NULL 
  AND di.contract_id IS NULL;

-- ============================================================================
-- MIGRATION COMPLETE
-- ============================================================================

-- Verification queries (run manually to check results)
-- SELECT COUNT(*) as unlinked_spks FROM spk WHERE kontrak_id IS NULL;
-- SELECT COUNT(*) as awaiting_contract_dis FROM delivery_instructions WHERE status = 'AWAITING_CONTRACT';
-- SELECT COUNT(*) as quotation_based_spks FROM spk WHERE source_type = 'QUOTATION';

SELECT 'Migration completed successfully!' as message;
