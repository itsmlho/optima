-- ============================================================
-- Migration: PROD_20260330_add_pre_wo_columns_to_work_orders
-- Purpose  : Add pre_wo_workflow_status and pre_wo_status_unit_id
--            columns to work_orders table on production.
--            These columns store the unit's workflow/status snapshot
--            taken BEFORE a work order is created, so it can be
--            restored when the work order is closed or cancelled.
-- Run on   : Production (Hostinger) via phpMyAdmin
-- Safe     : Uses IF NOT EXISTS / no data loss
-- ============================================================

ALTER TABLE `work_orders`
    ADD COLUMN `pre_wo_workflow_status` VARCHAR(50) NULL DEFAULT NULL
        COMMENT 'Snapshot of inventory_unit.workflow_status before WO creation',
    ADD COLUMN `pre_wo_status_unit_id` TINYINT UNSIGNED NULL DEFAULT NULL
        COMMENT 'Snapshot of inventory_unit.status_unit_id before WO creation';

-- To verify, run: SHOW COLUMNS FROM `work_orders` LIKE 'pre_wo%';
