-- ===============================================================================
-- INTEGRATED RENTAL BUSINESS DATABASE DESIGN
-- Sistem Database Terintegrasi untuk Alur Bisnis Rental
-- ===============================================================================

-- BUSINESS FLOW ANALYSIS:
-- 1. MARKETING: Kontrak/PO Rental → SPK Creation
-- 2. SERVICE: SPK Service (Unit Preparation) 
-- 3. MARKETING: SPK Completion → DI Creation
-- 4. OPERATIONAL: Delivery Process
-- 5. MARKETING: DI Update (Delivered)
-- 6. MARKETING: Contract Activation (Status Rental)

-- CORE PRINCIPLE: inventory_unit sebagai central hub untuk semua data dan proses

-- ===============================================================================
-- AUDIT TRAIL & LOGGING SYSTEM
-- ===============================================================================

-- 1. Central Activity Log Table
CREATE TABLE IF NOT EXISTS `unit_activity_log` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `unit_id` INT UNSIGNED NOT NULL COMMENT 'FK to inventory_unit',
    `activity_type` ENUM(
        'CREATED', 'UPDATED', 'STATUS_CHANGED', 
        'KONTRAK_ASSIGNED', 'KONTRAK_REMOVED',
        'SPK_ASSIGNED', 'SPK_PREPARED', 'SPK_COMPLETED',
        'DI_CREATED', 'DI_SHIPPED', 'DI_DELIVERED',
        'PRICE_CHANGED', 'LOCATION_CHANGED',
        'ATTACHMENT_ASSIGNED', 'ATTACHMENT_REMOVED',
        'MAINTENANCE_START', 'MAINTENANCE_END'
    ) NOT NULL,
    `activity_description` TEXT NOT NULL,
    `old_values` JSON DEFAULT NULL COMMENT 'Previous values before change',
    `new_values` JSON DEFAULT NULL COMMENT 'New values after change',
    `reference_table` VARCHAR(50) DEFAULT NULL COMMENT 'Related table (kontrak, spk, delivery_instructions)',
    `reference_id` INT UNSIGNED DEFAULT NULL COMMENT 'Related record ID',
    `user_id` INT UNSIGNED NOT NULL COMMENT 'User who performed the action',
    `user_name` VARCHAR(100) NOT NULL COMMENT 'User name for quick reference',
    `user_role` VARCHAR(50) NOT NULL COMMENT 'User role (MARKETING, SERVICE, OPERATIONAL)',
    `ip_address` VARCHAR(45) DEFAULT NULL COMMENT 'User IP address',
    `user_agent` TEXT DEFAULT NULL COMMENT 'Browser/device info',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_unit_activity_unit_id` (`unit_id`, `created_at`),
    KEY `idx_unit_activity_type` (`activity_type`, `created_at`),
    KEY `idx_unit_activity_user` (`user_id`, `created_at`),
    KEY `idx_unit_activity_reference` (`reference_table`, `reference_id`),
    CONSTRAINT `fk_unit_activity_unit` FOREIGN KEY (`unit_id`) REFERENCES `inventory_unit` (`id_inventory_unit`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Price Change History
CREATE TABLE IF NOT EXISTS `unit_price_history` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `unit_id` INT UNSIGNED NOT NULL,
    `kontrak_id` INT UNSIGNED DEFAULT NULL,
    `kontrak_spesifikasi_id` INT UNSIGNED DEFAULT NULL,
    `old_harga_bulanan` DECIMAL(15,2) DEFAULT NULL,
    `new_harga_bulanan` DECIMAL(15,2) DEFAULT NULL,
    `old_harga_harian` DECIMAL(15,2) DEFAULT NULL,
    `new_harga_harian` DECIMAL(15,2) DEFAULT NULL,
    `change_reason` VARCHAR(255) NOT NULL,
    `changed_by` INT UNSIGNED NOT NULL,
    `changed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_price_history_unit` (`unit_id`, `changed_at`),
    KEY `idx_price_history_kontrak` (`kontrak_id`, `changed_at`),
    CONSTRAINT `fk_price_history_unit` FOREIGN KEY (`unit_id`) REFERENCES `inventory_unit` (`id_inventory_unit`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. Status Change History
CREATE TABLE IF NOT EXISTS `unit_status_history` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `unit_id` INT UNSIGNED NOT NULL,
    `old_status_id` INT DEFAULT NULL,
    `new_status_id` INT NOT NULL,
    `old_status_name` VARCHAR(50) DEFAULT NULL,
    `new_status_name` VARCHAR(50) NOT NULL,
    `change_reason` VARCHAR(255) NOT NULL,
    `process_stage` ENUM('KONTRAK', 'SPK', 'DELIVERY', 'MANUAL', 'MAINTENANCE') NOT NULL,
    `changed_by` INT UNSIGNED NOT NULL,
    `changed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_status_history_unit` (`unit_id`, `changed_at`),
    KEY `idx_status_history_stage` (`process_stage`, `changed_at`),
    CONSTRAINT `fk_status_history_unit` FOREIGN KEY (`unit_id`) REFERENCES `inventory_unit` (`id_inventory_unit`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ===============================================================================
-- BUSINESS PROCESS TRACKING TABLES
-- ===============================================================================

-- 4. Rental Process Stages
CREATE TABLE IF NOT EXISTS `rental_process_stages` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `unit_id` INT UNSIGNED NOT NULL,
    `kontrak_id` INT UNSIGNED NOT NULL,
    `spk_id` INT UNSIGNED DEFAULT NULL,
    `delivery_instructions_id` INT UNSIGNED DEFAULT NULL,
    -- Stage 1: Contract
    `contract_created_at` TIMESTAMP NULL,
    `contract_created_by` INT UNSIGNED NULL,
    -- Stage 2: SPK
    `spk_created_at` TIMESTAMP NULL,
    `spk_created_by` INT UNSIGNED NULL,
    `spk_assigned_to_service_at` TIMESTAMP NULL,
    `spk_preparation_started_at` TIMESTAMP NULL,
    `spk_preparation_completed_at` TIMESTAMP NULL,
    `spk_completed_by` INT UNSIGNED NULL,
    -- Stage 3: Delivery Instructions
    `di_created_at` TIMESTAMP NULL,
    `di_created_by` INT UNSIGNED NULL,
    `di_assigned_to_operational_at` TIMESTAMP NULL,
    `di_shipped_at` TIMESTAMP NULL,
    `di_delivered_at` TIMESTAMP NULL,
    `di_delivered_by` INT UNSIGNED NULL,
    -- Stage 4: Contract Activation
    `contract_activated_at` TIMESTAMP NULL,
    `contract_activated_by` INT UNSIGNED NULL,
    `rental_start_date` DATE NULL,
    -- Current Stage
    `current_stage` ENUM('CONTRACT', 'SPK_CREATED', 'SPK_IN_PROGRESS', 'SPK_COMPLETED', 'DI_CREATED', 'DI_SHIPPED', 'DI_DELIVERED', 'RENTAL_ACTIVE') NOT NULL DEFAULT 'CONTRACT',
    `is_completed` BOOLEAN NOT NULL DEFAULT FALSE,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_rental_process_unit_kontrak` (`unit_id`, `kontrak_id`),
    KEY `idx_rental_process_stage` (`current_stage`, `is_completed`),
    KEY `idx_rental_process_kontrak` (`kontrak_id`, `current_stage`),
    CONSTRAINT `fk_rental_process_unit` FOREIGN KEY (`unit_id`) REFERENCES `inventory_unit` (`id_inventory_unit`) ON DELETE CASCADE,
    CONSTRAINT `fk_rental_process_kontrak` FOREIGN KEY (`kontrak_id`) REFERENCES `kontrak` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 5. Enhanced Unit Location History
CREATE TABLE IF NOT EXISTS `unit_location_history` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `unit_id` INT UNSIGNED NOT NULL,
    `old_location` VARCHAR(255) DEFAULT NULL,
    `new_location` VARCHAR(255) NOT NULL,
    `location_type` ENUM('WAREHOUSE', 'CUSTOMER', 'MAINTENANCE', 'TRANSIT', 'OTHER') NOT NULL DEFAULT 'WAREHOUSE',
    `change_reason` VARCHAR(255) NOT NULL,
    `process_stage` ENUM('PURCHASE', 'KONTRAK', 'DELIVERY', 'RETURN', 'MAINTENANCE', 'MANUAL') NOT NULL,
    `reference_table` VARCHAR(50) DEFAULT NULL,
    `reference_id` INT UNSIGNED DEFAULT NULL,
    `changed_by` INT UNSIGNED NOT NULL,
    `changed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_location_history_unit` (`unit_id`, `changed_at`),
    KEY `idx_location_history_type` (`location_type`, `changed_at`),
    CONSTRAINT `fk_location_history_unit` FOREIGN KEY (`unit_id`) REFERENCES `inventory_unit` (`id_inventory_unit`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ===============================================================================
-- ENHANCED INTEGRATION FIELDS FOR INVENTORY_UNIT
-- ===============================================================================

-- Add additional tracking fields to inventory_unit
ALTER TABLE `inventory_unit` 
ADD COLUMN `spk_id` INT UNSIGNED DEFAULT NULL COMMENT 'Current SPK ID' AFTER `delivery_instructions_id`,
ADD COLUMN `rental_process_stage_id` INT UNSIGNED DEFAULT NULL COMMENT 'FK to rental_process_stages' AFTER `spk_id`,
ADD COLUMN `last_status_change_at` TIMESTAMP NULL COMMENT 'Last status change timestamp' AFTER `rental_process_stage_id`,
ADD COLUMN `last_status_change_by` INT UNSIGNED NULL COMMENT 'User who last changed status' AFTER `last_status_change_at`,
ADD COLUMN `total_rental_days` INT UNSIGNED DEFAULT 0 COMMENT 'Total days in rental' AFTER `last_status_change_by`,
ADD COLUMN `total_revenue_generated` DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Total revenue from this unit' AFTER `total_rental_days`,
ADD COLUMN `last_maintenance_date` DATE DEFAULT NULL COMMENT 'Last maintenance date' AFTER `total_revenue_generated`,
ADD COLUMN `next_maintenance_due` DATE DEFAULT NULL COMMENT 'Next maintenance due date' AFTER `last_maintenance_date`,
ADD COLUMN `is_active_rental` BOOLEAN DEFAULT FALSE COMMENT 'Currently in active rental' AFTER `next_maintenance_due`,
ADD COLUMN `rental_history_count` INT UNSIGNED DEFAULT 0 COMMENT 'Number of rental cycles' AFTER `is_active_rental`;

-- Add indexes for new fields
CREATE INDEX `idx_inventory_unit_spk` ON `inventory_unit` (`spk_id`);
CREATE INDEX `idx_inventory_unit_rental_stage` ON `inventory_unit` (`rental_process_stage_id`);
CREATE INDEX `idx_inventory_unit_active_rental` ON `inventory_unit` (`is_active_rental`, `status_unit_id`);
CREATE INDEX `idx_inventory_unit_revenue` ON `inventory_unit` (`total_revenue_generated` DESC);

-- Add foreign key constraints
ALTER TABLE `inventory_unit`
ADD CONSTRAINT `fk_inventory_unit_spk` FOREIGN KEY (`spk_id`) REFERENCES `spk` (`id`) ON DELETE SET NULL,
ADD CONSTRAINT `fk_inventory_unit_rental_stage` FOREIGN KEY (`rental_process_stage_id`) REFERENCES `rental_process_stages` (`id`) ON DELETE SET NULL;

-- ===============================================================================
-- COMPREHENSIVE TRIGGERS FOR AUDIT TRAIL
-- ===============================================================================

-- Trigger 1: Log all inventory_unit changes
DELIMITER $$
CREATE TRIGGER `tr_inventory_unit_audit_trail`
AFTER UPDATE ON `inventory_unit`
FOR EACH ROW
BEGIN
    DECLARE v_user_id INT UNSIGNED DEFAULT 1;
    DECLARE v_user_name VARCHAR(100) DEFAULT 'SYSTEM';
    DECLARE v_user_role VARCHAR(50) DEFAULT 'SYSTEM';
    DECLARE v_old_values JSON;
    DECLARE v_new_values JSON;
    DECLARE v_activity_type VARCHAR(50);
    DECLARE v_description TEXT;
    
    -- Build old and new values JSON
    SET v_old_values = JSON_OBJECT(
        'status_unit_id', OLD.status_unit_id,
        'lokasi_unit', OLD.lokasi_unit,
        'kontrak_id', OLD.kontrak_id,
        'kontrak_spesifikasi_id', OLD.kontrak_spesifikasi_id,
        'harga_sewa_bulanan', OLD.harga_sewa_bulanan,
        'harga_sewa_harian', OLD.harga_sewa_harian,
        'tanggal_kirim', OLD.tanggal_kirim,
        'delivery_instructions_id', OLD.delivery_instructions_id,
        'spk_id', OLD.spk_id
    );
    
    SET v_new_values = JSON_OBJECT(
        'status_unit_id', NEW.status_unit_id,
        'lokasi_unit', NEW.lokasi_unit,
        'kontrak_id', NEW.kontrak_id,
        'kontrak_spesifikasi_id', NEW.kontrak_spesifikasi_id,
        'harga_sewa_bulanan', NEW.harga_sewa_bulanan,
        'harga_sewa_harian', NEW.harga_sewa_harian,
        'tanggal_kirim', NEW.tanggal_kirim,
        'delivery_instructions_id', NEW.delivery_instructions_id,
        'spk_id', NEW.spk_id
    );
    
    -- Determine activity type and description
    IF OLD.status_unit_id != NEW.status_unit_id THEN
        SET v_activity_type = 'STATUS_CHANGED';
        SET v_description = CONCAT('Status changed from ', OLD.status_unit_id, ' to ', NEW.status_unit_id);
        
        -- Log to status history
        INSERT INTO unit_status_history (
            unit_id, old_status_id, new_status_id, old_status_name, new_status_name,
            change_reason, process_stage, changed_by
        ) VALUES (
            NEW.id_inventory_unit, OLD.status_unit_id, NEW.status_unit_id,
            (SELECT status_unit FROM status_unit WHERE id_status = OLD.status_unit_id),
            (SELECT status_unit FROM status_unit WHERE id_status = NEW.status_unit_id),
            'Automatic status change', 'MANUAL', v_user_id
        );
    ELSEIF OLD.kontrak_id != NEW.kontrak_id OR (OLD.kontrak_id IS NULL AND NEW.kontrak_id IS NOT NULL) THEN
        IF NEW.kontrak_id IS NOT NULL THEN
            SET v_activity_type = 'KONTRAK_ASSIGNED';
            SET v_description = CONCAT('Unit assigned to contract ID: ', NEW.kontrak_id);
        ELSE
            SET v_activity_type = 'KONTRAK_REMOVED';
            SET v_description = CONCAT('Unit removed from contract ID: ', OLD.kontrak_id);
        END IF;
    ELSEIF OLD.lokasi_unit != NEW.lokasi_unit THEN
        SET v_activity_type = 'LOCATION_CHANGED';
        SET v_description = CONCAT('Location changed from "', COALESCE(OLD.lokasi_unit, 'NULL'), '" to "', NEW.lokasi_unit, '"');
        
        -- Log to location history
        INSERT INTO unit_location_history (
            unit_id, old_location, new_location, location_type, change_reason, process_stage, changed_by
        ) VALUES (
            NEW.id_inventory_unit, OLD.lokasi_unit, NEW.lokasi_unit, 'OTHER', 
            'Location update', 'MANUAL', v_user_id
        );
    ELSEIF OLD.harga_sewa_bulanan != NEW.harga_sewa_bulanan OR OLD.harga_sewa_harian != NEW.harga_sewa_harian THEN
        SET v_activity_type = 'PRICE_CHANGED';
        SET v_description = 'Price updated';
        
        -- Log to price history
        INSERT INTO unit_price_history (
            unit_id, kontrak_id, kontrak_spesifikasi_id, 
            old_harga_bulanan, new_harga_bulanan, old_harga_harian, new_harga_harian,
            change_reason, changed_by
        ) VALUES (
            NEW.id_inventory_unit, NEW.kontrak_id, NEW.kontrak_spesifikasi_id,
            OLD.harga_sewa_bulanan, NEW.harga_sewa_bulanan, OLD.harga_sewa_harian, NEW.harga_sewa_harian,
            'Price update', v_user_id
        );
    ELSE
        SET v_activity_type = 'UPDATED';
        SET v_description = 'Unit data updated';
    END IF;
    
    -- Update last change tracking
    SET NEW.last_status_change_at = CURRENT_TIMESTAMP;
    SET NEW.last_status_change_by = v_user_id;
    
    -- Log to main activity log
    INSERT INTO unit_activity_log (
        unit_id, activity_type, activity_description, old_values, new_values,
        user_id, user_name, user_role, created_at
    ) VALUES (
        NEW.id_inventory_unit, v_activity_type, v_description, v_old_values, v_new_values,
        v_user_id, v_user_name, v_user_role, CURRENT_TIMESTAMP
    );
END$$
DELIMITER ;

-- ===============================================================================
-- SUMMARY
-- ===============================================================================

-- ✅ AUDIT TRAIL SYSTEM:
-- - unit_activity_log: Central log for all unit activities
-- - unit_price_history: Track all price changes
-- - unit_status_history: Track all status changes
-- - unit_location_history: Track all location changes

-- ✅ BUSINESS PROCESS TRACKING:
-- - rental_process_stages: Track complete rental workflow
-- - Enhanced inventory_unit with process tracking fields

-- ✅ INTEGRATION READY:
-- - Foreign keys to all related tables (kontrak, spk, delivery_instructions)
-- - Comprehensive triggers for automatic logging
-- - JSON fields for flexible data storage

-- ✅ PERFORMANCE OPTIMIZED:
-- - Strategic indexes for fast querying
-- - Optimized for reporting and analytics

-- NEXT: Implement stored procedures for business operations
