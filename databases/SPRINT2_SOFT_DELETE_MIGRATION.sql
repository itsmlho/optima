-- ============================================================
-- Sprint 2 Migration: Add Soft Delete & Missing Indexes
-- Table: kontrak, inventory_unit, spk, invoices
-- Run ONCE on each environment after code deployment
-- Date: 2026-03-03
-- ============================================================

START TRANSACTION;

-- ─────────────────────────────────────────────────────────────
-- 1. Soft Delete columns
-- ─────────────────────────────────────────────────────────────

-- kontrak
ALTER TABLE `kontrak`
    ADD COLUMN IF NOT EXISTS `deleted_at` DATETIME NULL DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS `deleted_by` INT UNSIGNED NULL DEFAULT NULL;

-- inventory_unit
ALTER TABLE `inventory_unit`
    ADD COLUMN IF NOT EXISTS `deleted_at` DATETIME NULL DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS `deleted_by` INT UNSIGNED NULL DEFAULT NULL;

-- spk
ALTER TABLE `spk`
    ADD COLUMN IF NOT EXISTS `deleted_at` DATETIME NULL DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS `deleted_by` INT UNSIGNED NULL DEFAULT NULL;

-- invoices (adjust table name if different)
ALTER TABLE `invoices`
    ADD COLUMN IF NOT EXISTS `deleted_at` DATETIME NULL DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS `deleted_by` INT UNSIGNED NULL DEFAULT NULL;

-- ─────────────────────────────────────────────────────────────
-- 2. Missing Indexes on kontrak table
-- ─────────────────────────────────────────────────────────────

ALTER TABLE `kontrak`
    ADD INDEX IF NOT EXISTS `idx_kontrak_location`  (`customer_location_id`),
    ADD INDEX IF NOT EXISTS `idx_kontrak_creator`   (`dibuat_oleh`),
    ADD INDEX IF NOT EXISTS `idx_kontrak_status`    (`status`),
    ADD INDEX IF NOT EXISTS `idx_kontrak_deleted`   (`deleted_at`),
    ADD INDEX IF NOT EXISTS `idx_kontrak_report`    (`customer_location_id`, `status`, `dibuat_pada`);

-- ─────────────────────────────────────────────────────────────
-- 3. Missing FK Constraints
-- ─────────────────────────────────────────────────────────────

-- di_workflow_stages.di_id → delivery_instructions.id
ALTER TABLE `di_workflow_stages`
    ADD CONSTRAINT IF NOT EXISTS `fk_workflow_stage_di`
        FOREIGN KEY (`di_id`) REFERENCES `delivery_instructions`(`id`)
        ON DELETE CASCADE ON UPDATE CASCADE;

-- contract_disconnection_log
ALTER TABLE `contract_disconnection_log`
    ADD CONSTRAINT IF NOT EXISTS `fk_disconnect_kontrak`
        FOREIGN KEY (`kontrak_id`) REFERENCES `kontrak`(`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT IF NOT EXISTS `fk_disconnect_unit`
        FOREIGN KEY (`unit_id`) REFERENCES `inventory_unit`(`id_inventory_unit`)
        ON DELETE RESTRICT ON UPDATE CASCADE;

-- ─────────────────────────────────────────────────────────────
-- 4. Version column (Optimistic Locking) — Sprint 3
-- ─────────────────────────────────────────────────────────────
-- Uncomment when implementing updateWithLock():
--
-- ALTER TABLE `kontrak`        ADD COLUMN IF NOT EXISTS `version` INT UNSIGNED NOT NULL DEFAULT 1;
-- ALTER TABLE `inventory_unit` ADD COLUMN IF NOT EXISTS `version` INT UNSIGNED NOT NULL DEFAULT 1;
-- ALTER TABLE `spk`            ADD COLUMN IF NOT EXISTS `version` INT UNSIGNED NOT NULL DEFAULT 1;
-- ALTER TABLE `invoices`       ADD COLUMN IF NOT EXISTS `version` INT UNSIGNED NOT NULL DEFAULT 1;

COMMIT;

-- ─────────────────────────────────────────────────────────────
-- After running this migration, enable useSoftDeletes in:
--   app/Models/KontrakModel.php       → useSoftDeletes = true
--   app/Models/InventoryUnitModel.php → useSoftDeletes = true
--   app/Models/SpkModel.php           → useSoftDeletes = true
-- ─────────────────────────────────────────────────────────────
