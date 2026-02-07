-- ============================================================================
-- OPTIMA RENTAL WORKFLOW - COMPLETE INVOICING SYSTEM
-- ============================================================================
-- Migration: Create comprehensive invoicing and billing system
-- Purpose: Enable invoice generation with contract-linking prerequisites
-- Date: 2026-02-07
-- ============================================================================

USE optima_ci;

-- ============================================================================
-- 1. CREATE INVOICES TABLE
-- ============================================================================

CREATE TABLE IF NOT EXISTS `invoices` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_number` VARCHAR(50) NOT NULL UNIQUE COMMENT 'Unique invoice number (INV/YYYYMM/NNN)',
  `contract_id` INT UNSIGNED NOT NULL COMMENT 'FK to kontrak - REQUIRED for billing control',
  `di_id` INT UNSIGNED NULL COMMENT 'FK to delivery_instructions (for one-time invoices)',
  `customer_id` INT UNSIGNED NOT NULL COMMENT 'FK to customers',
  `invoice_type` ENUM('ONE_TIME', 'RECURRING_RENTAL', 'ADDENDUM') NOT NULL DEFAULT 'ONE_TIME' 
    COMMENT 'Type of invoice',
  
  -- Billing Period
  `billing_period_start` DATE NOT NULL COMMENT 'Start date of billing period',
  `billing_period_end` DATE NOT NULL COMMENT 'End date of billing period',
  `issue_date` DATE NOT NULL COMMENT 'Invoice issue date',
  `due_date` DATE NOT NULL COMMENT 'Payment due date',
  
  -- Financial Details
  `subtotal` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Subtotal before discount and tax',
  `discount_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Discount amount',
  `tax_percent` DECIMAL(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Tax percentage (e.g., 11.00 for 11%)',
  `tax_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Calculated tax amount',
  `total_amount` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Final total amount',
  
  -- Status & Workflow
  `status` ENUM('DRAFT', 'PENDING_APPROVAL', 'APPROVED', 'SENT', 'PAID', 'OVERDUE', 'CANCELLED') 
    NOT NULL DEFAULT 'DRAFT' 
    COMMENT 'Invoice status',
  
  -- Payment Information
  `payment_date` DATE NULL COMMENT 'Date payment was received',
  `payment_method` VARCHAR(100) NULL COMMENT 'Payment method (Bank Transfer, Cash, etc.)',
  `payment_reference` VARCHAR(100) NULL COMMENT 'Payment reference/transaction number',
  
  -- Metadata
  `notes` TEXT NULL COMMENT 'Invoice notes/comments',
  `created_by` INT UNSIGNED NOT NULL COMMENT 'User who created invoice',
  `approved_by` INT UNSIGNED NULL COMMENT 'User who approved invoice',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_invoice_number` (`invoice_number`),
  KEY `idx_contract_id` (`contract_id`),
  KEY `idx_di_id` (`di_id`),
  KEY `idx_customer_id` (`customer_id`),
  KEY `idx_status` (`status`),
  KEY `idx_issue_date` (`issue_date`),
  KEY `idx_due_date` (`due_date`),
  KEY `idx_billing_period` (`billing_period_start`, `billing_period_end`),
  
  CONSTRAINT `fk_invoice_contract` 
    FOREIGN KEY (`contract_id`) REFERENCES `kontrak`(`id`) 
    ON DELETE RESTRICT,
  CONSTRAINT `fk_invoice_di` 
    FOREIGN KEY (`di_id`) REFERENCES `delivery_instructions`(`id`) 
    ON DELETE SET NULL,
  CONSTRAINT `fk_invoice_customer` 
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) 
    ON DELETE RESTRICT,
  CONSTRAINT `fk_invoice_created_by` 
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) 
    ON DELETE RESTRICT,
  CONSTRAINT `fk_invoice_approved_by` 
    FOREIGN KEY (`approved_by`) REFERENCES `users`(`id`) 
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Main invoices table with contract-based billing control';

-- ============================================================================
-- 2. CREATE INVOICE_ITEMS TABLE
-- ============================================================================

CREATE TABLE IF NOT EXISTS `invoice_items` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_id` INT UNSIGNED NOT NULL COMMENT 'FK to invoices',
  `item_type` ENUM('UNIT_RENTAL', 'ATTACHMENT_RENTAL', 'DELIVERY_FEE', 'OTHER') 
    NOT NULL DEFAULT 'UNIT_RENTAL' 
    COMMENT 'Type of invoice line item',
  `description` VARCHAR(500) NOT NULL COMMENT 'Item description',
  `unit_id` INT UNSIGNED NULL COMMENT 'FK to inventory_unit (if applicable)',
  `quantity` INT NOT NULL DEFAULT 1 COMMENT 'Quantity',
  `unit_price` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Price per unit',
  `subtotal` DECIMAL(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Line item subtotal (qty * price)',
  `reference_contract_spec_id` INT UNSIGNED NULL COMMENT 'FK to kontrak_spesifikasi (for rental items)',
  `notes` TEXT NULL COMMENT 'Item notes',
  
  PRIMARY KEY (`id`),
  KEY `idx_invoice_id` (`invoice_id`),
  KEY `idx_unit_id` (`unit_id`),
  KEY `idx_contract_spec` (`reference_contract_spec_id`),
  
  CONSTRAINT `fk_invoice_item_invoice` 
    FOREIGN KEY (`invoice_id`) REFERENCES `invoices`(`id`) 
    ON DELETE CASCADE,
  CONSTRAINT `fk_invoice_item_unit` 
    FOREIGN KEY (`unit_id`) REFERENCES `inventory_unit`(`id_inventory_unit`) 
    ON DELETE SET NULL,
  CONSTRAINT `fk_invoice_item_contract_spec` 
    FOREIGN KEY (`reference_contract_spec_id`) REFERENCES `kontrak_spesifikasi`(`id`) 
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Invoice line items with detailed pricing';

-- ============================================================================
-- 3. CREATE RECURRING_BILLING_SCHEDULES TABLE
-- ============================================================================

CREATE TABLE IF NOT EXISTS `recurring_billing_schedules` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `contract_id` INT UNSIGNED NOT NULL UNIQUE COMMENT 'FK to kontrak (one schedule per contract)',
  `frequency` ENUM('MONTHLY', 'QUARTERLY', 'YEARLY') NOT NULL DEFAULT 'MONTHLY' 
    COMMENT 'Billing frequency',
  `next_billing_date` DATE NOT NULL COMMENT 'Next scheduled billing date',
  `last_invoice_id` INT UNSIGNED NULL COMMENT 'FK to last generated invoice',
  `auto_generate` BOOLEAN NOT NULL DEFAULT 1 COMMENT 'Enable automatic invoice generation',
  `status` ENUM('ACTIVE', 'PAUSED', 'COMPLETED') NOT NULL DEFAULT 'ACTIVE' 
    COMMENT 'Schedule status',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_contract_schedule` (`contract_id`),
  KEY `idx_next_billing_date` (`next_billing_date`),
  KEY `idx_status` (`status`),
  
  CONSTRAINT `fk_schedule_contract` 
    FOREIGN KEY (`contract_id`) REFERENCES `kontrak`(`id`) 
    ON DELETE RESTRICT,
  CONSTRAINT `fk_schedule_last_invoice` 
    FOREIGN KEY (`last_invoice_id`) REFERENCES `invoices`(`id`) 
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Recurring billing schedules for rental contracts';

-- ============================================================================
-- 4. CREATE INVOICE STATUS HISTORY TABLE (Audit Trail)
-- ============================================================================

CREATE TABLE IF NOT EXISTS `invoice_status_history` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_id` INT UNSIGNED NOT NULL COMMENT 'FK to invoices',
  `old_status` VARCHAR(50) NULL COMMENT 'Previous status',
  `new_status` VARCHAR(50) NOT NULL COMMENT 'New status',
  `changed_by` INT UNSIGNED NOT NULL COMMENT 'User who changed status',
  `notes` TEXT NULL COMMENT 'Status change notes',
  `changed_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  
  PRIMARY KEY (`id`),
  KEY `idx_invoice_id` (`invoice_id`),
  KEY `idx_changed_at` (`changed_at`),
  
  CONSTRAINT `fk_status_history_invoice` 
    FOREIGN KEY (`invoice_id`) REFERENCES `invoices`(`id`) 
    ON DELETE CASCADE,
  CONSTRAINT `fk_status_history_user` 
    FOREIGN KEY (`changed_by`) REFERENCES `users`(`id`) 
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Audit trail for invoice status changes';

-- ============================================================================
-- 5. CREATE DATABASE TRIGGERS
-- ============================================================================

-- Trigger: Auto-calculate invoice item subtotal
DELIMITER //

DROP TRIGGER IF EXISTS `calculate_invoice_item_subtotal`//

CREATE TRIGGER `calculate_invoice_item_subtotal`
BEFORE INSERT ON `invoice_items`
FOR EACH ROW
BEGIN
  SET NEW.subtotal = NEW.quantity * NEW.unit_price;
END//

DROP TRIGGER IF EXISTS `update_invoice_item_subtotal`//

CREATE TRIGGER `update_invoice_item_subtotal`
BEFORE UPDATE ON `invoice_items`
FOR EACH ROW
BEGIN
  SET NEW.subtotal = NEW.quantity * NEW.unit_price;
END//

-- Trigger: Update invoice total when items change
DROP TRIGGER IF EXISTS `update_invoice_total_on_item_insert`//

CREATE TRIGGER `update_invoice_total_on_item_insert`
AFTER INSERT ON `invoice_items`
FOR EACH ROW
BEGIN
  DECLARE v_subtotal DECIMAL(15,2);
  DECLARE v_discount DECIMAL(15,2);
  DECLARE v_tax_percent DECIMAL(5,2);
  DECLARE v_tax_amount DECIMAL(15,2);
  DECLARE v_total DECIMAL(15,2);
  
  -- Get current invoice discount and tax
  SELECT discount_amount, tax_percent 
  INTO v_discount, v_tax_percent
  FROM invoices 
  WHERE id = NEW.invoice_id;
  
  -- Calculate subtotal from all items
  SELECT COALESCE(SUM(subtotal), 0) 
  INTO v_subtotal
  FROM invoice_items 
  WHERE invoice_id = NEW.invoice_id;
  
  -- Calculate tax
  SET v_tax_amount = (v_subtotal - v_discount) * v_tax_percent / 100;
  
  -- Calculate total
  SET v_total = v_subtotal - v_discount + v_tax_amount;
  
  -- Update invoice
  UPDATE invoices 
  SET 
    subtotal = v_subtotal,
    tax_amount = v_tax_amount,
    total_amount = v_total
  WHERE id = NEW.invoice_id;
END//

DROP TRIGGER IF EXISTS `update_invoice_total_on_item_update`//

CREATE TRIGGER `update_invoice_total_on_item_update`
AFTER UPDATE ON `invoice_items`
FOR EACH ROW
BEGIN
  DECLARE v_subtotal DECIMAL(15,2);
  DECLARE v_discount DECIMAL(15,2);
  DECLARE v_tax_percent DECIMAL(5,2);
  DECLARE v_tax_amount DECIMAL(15,2);
  DECLARE v_total DECIMAL(15,2);
  
  SELECT discount_amount, tax_percent 
  INTO v_discount, v_tax_percent
  FROM invoices 
  WHERE id = NEW.invoice_id;
  
  SELECT COALESCE(SUM(subtotal), 0) 
  INTO v_subtotal
  FROM invoice_items 
  WHERE invoice_id = NEW.invoice_id;
  
  SET v_tax_amount = (v_subtotal - v_discount) * v_tax_percent / 100;
  SET v_total = v_subtotal - v_discount + v_tax_amount;
  
  UPDATE invoices 
  SET 
    subtotal = v_subtotal,
    tax_amount = v_tax_amount,
    total_amount = v_total
  WHERE id = NEW.invoice_id;
END//

DROP TRIGGER IF EXISTS `update_invoice_total_on_item_delete`//

CREATE TRIGGER `update_invoice_total_on_item_delete`
AFTER DELETE ON `invoice_items`
FOR EACH ROW
BEGIN
  DECLARE v_subtotal DECIMAL(15,2);
  DECLARE v_discount DECIMAL(15,2);
  DECLARE v_tax_percent DECIMAL(5,2);
  DECLARE v_tax_amount DECIMAL(15,2);
  DECLARE v_total DECIMAL(15,2);
  
  SELECT discount_amount, tax_percent 
  INTO v_discount, v_tax_percent
  FROM invoices 
  WHERE id = OLD.invoice_id;
  
  SELECT COALESCE(SUM(subtotal), 0) 
  INTO v_subtotal
  FROM invoice_items 
  WHERE invoice_id = OLD.invoice_id;
  
  SET v_tax_amount = (v_subtotal - v_discount) * v_tax_percent / 100;
  SET v_total = v_subtotal - v_discount + v_tax_amount;
  
  UPDATE invoices 
  SET 
    subtotal = v_subtotal,
    tax_amount = v_tax_amount,
    total_amount = v_total
  WHERE id = OLD.invoice_id;
END//

-- Trigger: Log invoice status changes
DROP TRIGGER IF EXISTS `log_invoice_status_change`//

CREATE TRIGGER `log_invoice_status_change`
AFTER UPDATE ON `invoices`
FOR EACH ROW
BEGIN
  IF OLD.status != NEW.status THEN
    INSERT INTO invoice_status_history 
      (invoice_id, old_status, new_status, changed_by, changed_at)
    VALUES 
      (NEW.id, OLD.status, NEW.status, NEW.approved_by, NOW());
  END IF;
END//

DELIMITER ;

-- ============================================================================
-- 6. CREATE STORED PROCEDURES
-- ============================================================================

-- Procedure: Generate next invoice number
DELIMITER //

DROP PROCEDURE IF EXISTS `sp_generate_invoice_number`//

CREATE PROCEDURE `sp_generate_invoice_number`(OUT p_invoice_number VARCHAR(50))
BEGIN
  DECLARE v_year_month VARCHAR(6);
  DECLARE v_sequence INT;
  DECLARE v_lock_acquired INT;
  
  -- Acquire lock for invoice number generation
  SELECT GET_LOCK('invoice_number_generation', 10) INTO v_lock_acquired;
  
  IF v_lock_acquired = 1 THEN
    -- Get current year-month (YYYYMM format)
    SET v_year_month = DATE_FORMAT(NOW(), '%Y%m');
    
    -- Get last sequence for this month
    SELECT COALESCE(MAX(CAST(SUBSTRING(invoice_number, -3) AS UNSIGNED)), 0) + 1
    INTO v_sequence
    FROM invoices
    WHERE invoice_number LIKE CONCAT('INV/', v_year_month, '/%');
    
    -- Format invoice number: INV/YYYYMM/NNN
    SET p_invoice_number = CONCAT('INV/', v_year_month, '/', LPAD(v_sequence, 3, '0'));
    
    -- Release lock
    SELECT RELEASE_LOCK('invoice_number_generation') INTO v_lock_acquired;
  ELSE
    SIGNAL SQLSTATE '45000' 
    SET MESSAGE_TEXT = 'Failed to acquire lock for invoice number generation';
  END IF;
END//

DELIMITER ;

-- ============================================================================
-- MIGRATION COMPLETE
-- ============================================================================

SELECT 'Invoicing system migration completed successfully!' as message;
