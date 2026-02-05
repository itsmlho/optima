-- ============================================================
-- QUOTATION VERSIONING & REVISION TRACKING SYSTEM
-- Created: 2026-01-30
-- Purpose: Implement enterprise-grade versioning and audit trail
-- ============================================================

-- Step 1: Add versioning columns to quotations table
ALTER TABLE `quotations` 
ADD COLUMN `version` INT NOT NULL DEFAULT 1 COMMENT 'Quotation version number',
ADD COLUMN `revision_status` ENUM('ORIGINAL', 'REVISED') NOT NULL DEFAULT 'ORIGINAL' COMMENT 'Revision tracking status',
ADD COLUMN `original_quotation_id` INT NULL COMMENT 'Reference to original quotation if this is a revision',
ADD COLUMN `revised_at` DATETIME NULL COMMENT 'Timestamp when quotation was revised',
ADD COLUMN `revised_by` INT NULL COMMENT 'User ID who made the revision',
ADD INDEX `idx_version` (`version`),
ADD INDEX `idx_revision_status` (`revision_status`),
ADD INDEX `idx_original_quotation` (`original_quotation_id`);

-- Step 2: Create quotation history/audit trail table
CREATE TABLE IF NOT EXISTS `quotation_history` (
  `id_history` INT NOT NULL AUTO_INCREMENT,
  `quotation_id` INT NOT NULL COMMENT 'Reference to quotation',
  `version` INT NOT NULL COMMENT 'Version at time of change',
  `action_type` ENUM('CREATED', 'UPDATED', 'SENT', 'REVISED', 'APPROVED', 'REJECTED', 'DELETED') NOT NULL COMMENT 'Type of action performed',
  `changed_by` INT NULL COMMENT 'User ID who made the change',
  `changed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp of change',
  `changes_summary` TEXT NULL COMMENT 'Human-readable summary of changes',
  `old_values` JSON NULL COMMENT 'Previous values before change',
  `new_values` JSON NULL COMMENT 'New values after change',
  `ip_address` VARCHAR(45) NULL COMMENT 'IP address of user making change',
  `user_agent` VARCHAR(255) NULL COMMENT 'Browser/device info',
  PRIMARY KEY (`id_history`),
  INDEX `idx_quotation_id` (`quotation_id`),
  INDEX `idx_changed_by` (`changed_by`),
  INDEX `idx_changed_at` (`changed_at`),
  INDEX `idx_action_type` (`action_type`),
  CONSTRAINT `fk_quotation_history_quotation` 
    FOREIGN KEY (`quotation_id`) 
    REFERENCES `quotations` (`id_quotation`) 
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_quotation_history_user` 
    FOREIGN KEY (`changed_by`) 
    REFERENCES `users` (`id_user`) 
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Audit trail for all quotation changes';

-- Step 3: Create quotation notifications table for customer alerts
CREATE TABLE IF NOT EXISTS `quotation_notifications` (
  `id_notification` INT NOT NULL AUTO_INCREMENT,
  `quotation_id` INT NOT NULL COMMENT 'Reference to quotation',
  `notification_type` ENUM('CREATED', 'UPDATED', 'SENT', 'REVISED', 'APPROVED', 'REJECTED') NOT NULL,
  `recipient_type` ENUM('CUSTOMER', 'INTERNAL', 'BOTH') NOT NULL DEFAULT 'CUSTOMER',
  `recipient_email` VARCHAR(255) NULL COMMENT 'Email address of recipient',
  `subject` VARCHAR(255) NOT NULL COMMENT 'Email subject',
  `message` TEXT NOT NULL COMMENT 'Email body',
  `sent_status` ENUM('PENDING', 'SENT', 'FAILED') NOT NULL DEFAULT 'PENDING',
  `sent_at` DATETIME NULL COMMENT 'When notification was sent',
  `error_message` TEXT NULL COMMENT 'Error details if failed',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_notification`),
  INDEX `idx_quotation_id` (`quotation_id`),
  INDEX `idx_sent_status` (`sent_status`),
  INDEX `idx_created_at` (`created_at`),
  CONSTRAINT `fk_quotation_notifications_quotation` 
    FOREIGN KEY (`quotation_id`) 
    REFERENCES `quotations` (`id_quotation`) 
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Notification queue for quotation changes';

-- Step 4: Create view for quotation history with user details
CREATE OR REPLACE VIEW `vw_quotation_history_detail` AS
SELECT 
    qh.id_history,
    qh.quotation_id,
    q.quotation_number,
    qh.version,
    qh.action_type,
    qh.changed_at,
    qh.changes_summary,
    qh.old_values,
    qh.new_values,
    qh.ip_address,
    u.id_user as changed_by_id,
    u.username as changed_by_username,
    u.email as changed_by_email,
    CONCAT(u.first_name, ' ', u.last_name) as changed_by_name
FROM quotation_history qh
LEFT JOIN quotations q ON qh.quotation_id = q.id_quotation
LEFT JOIN users u ON qh.changed_by = u.id_user
ORDER BY qh.changed_at DESC;

-- Step 5: Insert initial history records for existing quotations
INSERT INTO quotation_history (
    quotation_id,
    version,
    action_type,
    changed_by,
    changed_at,
    changes_summary,
    old_values,
    new_values
)
SELECT 
    id_quotation,
    1,
    'CREATED',
    user_id,
    created_at,
    'Initial quotation creation (migrated from existing data)',
    NULL,
    JSON_OBJECT(
        'quotation_number', quotation_number,
        'total_amount', total_amount,
        'valid_until', valid_until,
        'workflow_stage', workflow_stage
    )
FROM quotations
WHERE NOT EXISTS (
    SELECT 1 FROM quotation_history 
    WHERE quotation_history.quotation_id = quotations.id_quotation
);

-- Step 6: Create indexes for performance optimization
CREATE INDEX idx_quotation_version_lookup ON quotations(id_quotation, version, revision_status);
CREATE INDEX idx_history_lookup ON quotation_history(quotation_id, version, changed_at DESC);

-- ============================================================
-- VERIFICATION QUERIES (commented out - run manually if needed)
-- ============================================================

/*
-- Check quotations with versioning info
SELECT 
    id_quotation,
    quotation_number,
    version,
    revision_status,
    workflow_stage,
    created_at,
    revised_at
FROM quotations
ORDER BY id_quotation DESC
LIMIT 10;

-- Check history records
SELECT * FROM vw_quotation_history_detail LIMIT 10;

-- Count history entries per quotation
SELECT 
    quotation_id,
    quotation_number,
    COUNT(*) as history_count,
    MAX(version) as latest_version
FROM vw_quotation_history_detail
GROUP BY quotation_id, quotation_number
ORDER BY history_count DESC
LIMIT 10;
*/

-- ============================================================
-- ROLLBACK SCRIPT (if needed)
-- ============================================================

/*
-- Uncomment to rollback changes:

DROP VIEW IF EXISTS vw_quotation_history_detail;
DROP TABLE IF EXISTS quotation_notifications;
DROP TABLE IF EXISTS quotation_history;

ALTER TABLE quotations 
DROP COLUMN version,
DROP COLUMN revision_status,
DROP COLUMN original_quotation_id,
DROP COLUMN revised_at,
DROP COLUMN revised_by;
*/
