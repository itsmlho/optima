-- ============================================================
-- Migration: PROD_20260330_add_missing_work_orders_columns
-- Purpose  : Add columns to work_orders that exist locally but
--            are missing on production, causing transaction failures
--            in saveUnitVerification() and related methods.
-- Run on   : Production (Hostinger) via phpMyAdmin
-- Safe     : Each column uses separate ALTER (no IF NOT EXISTS)
--            Run all - skip any that already exist with duplicate error.
-- ============================================================

-- unit_verified: marks that unit verification has been completed for this WO
ALTER TABLE `work_orders`
    ADD COLUMN `unit_verified` TINYINT(1) NOT NULL DEFAULT 0
        COMMENT 'Whether unit verification has been completed';

-- unit_verified_at: timestamp when unit verification was completed
ALTER TABLE `work_orders`
    ADD COLUMN `unit_verified_at` TIMESTAMP NULL DEFAULT NULL
        COMMENT 'Timestamp when unit verification was completed';

-- sparepart_validated: marks that sparepart usage has been validated
ALTER TABLE `work_orders`
    ADD COLUMN `sparepart_validated` TINYINT(1) NOT NULL DEFAULT 0
        COMMENT 'Whether sparepart usage has been validated';

-- sparepart_validated_at: timestamp when spareparts were validated
ALTER TABLE `work_orders`
    ADD COLUMN `sparepart_validated_at` TIMESTAMP NULL DEFAULT NULL
        COMMENT 'Timestamp when spareparts were validated';

-- customer_location_id: links WO to a specific customer location
ALTER TABLE `work_orders`
    ADD COLUMN `customer_location_id` INT NULL DEFAULT NULL
        COMMENT 'FK to customer_locations.id for site-scoped WOs';

-- hm: hour meter reading at time of work order
ALTER TABLE `work_orders`
    ADD COLUMN `hm` DECIMAL(10,1) NULL DEFAULT NULL
        COMMENT 'Hour meter reading captured during WO';

-- completion_date: when the WO was marked completed
ALTER TABLE `work_orders`
    ADD COLUMN `completion_date` DATETIME NULL DEFAULT NULL
        COMMENT 'Timestamp when WO was completed';

-- Add useful index on unit_verified for dashboard queries
ALTER TABLE `work_orders`
    ADD INDEX `idx_unit_verified` (`unit_verified`);

-- Add useful index on sparepart_validated
ALTER TABLE `work_orders`
    ADD INDEX `idx_sparepart_validated` (`sparepart_validated`);

-- Verify: run this to confirm which columns now exist
-- SHOW COLUMNS FROM `work_orders` LIKE 'unit_verified%';
-- SHOW COLUMNS FROM `work_orders` LIKE 'sparepart_%';
-- SHOW COLUMNS FROM `work_orders` LIKE 'completion_date';
-- SHOW COLUMNS FROM `work_orders` LIKE 'hm';
-- SHOW COLUMNS FROM `work_orders` LIKE 'customer_location_id';
