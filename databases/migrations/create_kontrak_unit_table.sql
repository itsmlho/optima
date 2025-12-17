-- ============================================================================
-- Migration: Create kontrak_unit Table
-- Purpose: Junction table for contract-unit relationship with TARIK/TUKAR tracking
-- Date: 2025-12-17
-- Database: optima_ci
-- ============================================================================

USE optima_ci;

-- Drop table if exists (for clean migration)
DROP TABLE IF EXISTS `kontrak_unit`;

-- Create kontrak_unit junction table
CREATE TABLE `kontrak_unit` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `kontrak_id` INT UNSIGNED NOT NULL COMMENT 'Foreign key to kontrak table',
    `unit_id` INT UNSIGNED NOT NULL COMMENT 'Foreign key to inventory_unit table',
    `tanggal_mulai` DATE NOT NULL COMMENT 'Unit start date in contract',
    `tanggal_selesai` DATE NULL COMMENT 'Unit end date in contract',
    `status` ENUM('AKTIF','DITARIK','DITUKAR','NON_AKTIF') NOT NULL DEFAULT 'AKTIF' COMMENT 'Unit status in contract',
    
    -- TARIK workflow tracking
    `tanggal_tarik` DATETIME NULL COMMENT 'Date when unit was picked up',
    `stage_tarik` VARCHAR(50) NULL COMMENT 'Stage when unit was marked as DITARIK',
    
    -- TUKAR workflow tracking
    `tanggal_tukar` DATETIME NULL COMMENT 'Date when unit was exchanged',
    `unit_pengganti_id` INT UNSIGNED NULL COMMENT 'New unit ID that replaces this unit',
    `unit_sebelumnya_id` INT UNSIGNED NULL COMMENT 'Previous unit ID that this unit replaces',
    
    -- Audit fields
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `created_by` INT UNSIGNED NULL COMMENT 'User who created this record',
    `updated_by` INT UNSIGNED NULL COMMENT 'User who last updated this record',
    
    -- Foreign key constraints
    CONSTRAINT `fk_kontrak_unit_kontrak` 
        FOREIGN KEY (`kontrak_id`) REFERENCES `kontrak`(`id`) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    
    CONSTRAINT `fk_kontrak_unit_inventory_unit` 
        FOREIGN KEY (`unit_id`) REFERENCES `inventory_unit`(`id_inventory_unit`) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    
    CONSTRAINT `fk_kontrak_unit_pengganti` 
        FOREIGN KEY (`unit_pengganti_id`) REFERENCES `inventory_unit`(`id_inventory_unit`) 
        ON DELETE SET NULL ON UPDATE CASCADE,
    
    CONSTRAINT `fk_kontrak_unit_sebelumnya` 
        FOREIGN KEY (`unit_sebelumnya_id`) REFERENCES `inventory_unit`(`id_inventory_unit`) 
        ON DELETE SET NULL ON UPDATE CASCADE,
    
    -- Indexes for performance
    INDEX `idx_kontrak_unit_kontrak` (`kontrak_id`),
    INDEX `idx_kontrak_unit_unit` (`unit_id`),
    INDEX `idx_kontrak_unit_status` (`status`),
    INDEX `idx_kontrak_unit_dates` (`tanggal_mulai`, `tanggal_selesai`),
    INDEX `idx_kontrak_unit_pengganti` (`unit_pengganti_id`),
    INDEX `idx_kontrak_unit_sebelumnya` (`unit_sebelumnya_id`),
    
    -- Unique constraint: One active unit per contract (prevent duplicates)
    UNIQUE KEY `unique_active_kontrak_unit` (`kontrak_id`, `unit_id`, `status`)
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Junction table for contract-unit relationship with workflow tracking';

-- ============================================================================
-- Data Migration: Populate from existing inventory_unit.kontrak_id relationships
-- ============================================================================

-- Insert existing contracted units into kontrak_unit table
INSERT INTO `kontrak_unit` 
    (`kontrak_id`, `unit_id`, `tanggal_mulai`, `tanggal_selesai`, `status`, `created_at`)
SELECT 
    iu.kontrak_id,
    iu.id_inventory_unit,
    COALESCE(iu.tanggal_kirim, k.tanggal_mulai, CURRENT_DATE) AS tanggal_mulai,
    k.tanggal_berakhir AS tanggal_selesai,
    CASE 
        WHEN k.status = 'Aktif' THEN 'AKTIF'
        WHEN k.status = 'Berakhir' THEN 'NON_AKTIF'
        ELSE 'AKTIF'
    END AS status,
    CURRENT_TIMESTAMP
FROM inventory_unit iu
INNER JOIN kontrak k ON k.id = iu.kontrak_id
WHERE iu.kontrak_id IS NOT NULL
ON DUPLICATE KEY UPDATE 
    `tanggal_mulai` = VALUES(`tanggal_mulai`),
    `updated_at` = CURRENT_TIMESTAMP;

-- ============================================================================
-- Create activity log tables for TARIK/TUKAR operations
-- ============================================================================

-- Drop if exists
DROP TABLE IF EXISTS `contract_disconnection_log`;
DROP TABLE IF EXISTS `unit_workflow_log`;

-- Contract disconnection log (for TARIK operations)
CREATE TABLE `contract_disconnection_log` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `kontrak_id` INT UNSIGNED NOT NULL,
    `unit_id` INT UNSIGNED NOT NULL,
    `stage` VARCHAR(50) NOT NULL COMMENT 'DI stage when disconnection occurred',
    `disconnected_at` DATETIME NOT NULL,
    `disconnected_by` INT UNSIGNED NULL,
    `notes` TEXT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    
    INDEX `idx_disconnection_kontrak` (`kontrak_id`),
    INDEX `idx_disconnection_unit` (`unit_id`),
    INDEX `idx_disconnection_date` (`disconnected_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Audit log for contract-unit disconnections (TARIK workflow)';

-- Unit workflow activity log (for all DI workflows)
CREATE TABLE `unit_workflow_log` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `unit_id` INT UNSIGNED NOT NULL,
    `di_id` INT UNSIGNED NULL,
    `stage` VARCHAR(50) NOT NULL,
    `jenis_perintah` VARCHAR(50) NOT NULL,
    `old_status` VARCHAR(50) NULL,
    `new_status` VARCHAR(50) NULL,
    `notes` TEXT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `created_by` INT UNSIGNED NULL,
    
    INDEX `idx_workflow_log_unit` (`unit_id`),
    INDEX `idx_workflow_log_di` (`di_id`),
    INDEX `idx_workflow_log_jenis` (`jenis_perintah`),
    INDEX `idx_workflow_log_date` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Audit log for unit workflow activities in DI process';

-- ============================================================================
-- Verification Queries
-- ============================================================================

-- Check kontrak_unit records
SELECT 
    'kontrak_unit records' AS info,
    COUNT(*) AS total,
    COUNT(DISTINCT kontrak_id) AS unique_contracts,
    COUNT(DISTINCT unit_id) AS unique_units
FROM kontrak_unit;

-- Check status distribution
SELECT status, COUNT(*) AS total
FROM kontrak_unit
GROUP BY status;

-- Show sample records
SELECT 
    ku.*,
    k.no_kontrak,
    iu.serial_number
FROM kontrak_unit ku
INNER JOIN kontrak k ON k.id = ku.kontrak_id
INNER JOIN inventory_unit iu ON iu.id_inventory_unit = ku.unit_id
LIMIT 10;

-- ============================================================================
-- Migration Complete
-- ============================================================================
