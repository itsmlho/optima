-- ============================================================================
-- FIX: Update triggers to use correct column name 'attachment_status'
-- ============================================================================
-- ISSUE: Triggers use old column name 'status_unit' which was renamed to 'attachment_status'
-- ERROR: "Unknown column 'status_unit' in 'field list'" when updating inventory_unit
-- DATE: 2026-02-12
-- ============================================================================

USE optima_ci;

-- Drop existing triggers
DROP TRIGGER IF EXISTS tr_inventory_unit_attachment_sync;
DROP TRIGGER IF EXISTS tr_inventory_unit_status_sync;

-- Recreate tr_inventory_unit_attachment_sync with CORRECT column name
DELIMITER $$
CREATE TRIGGER tr_inventory_unit_attachment_sync
AFTER UPDATE ON inventory_unit
FOR EACH ROW
BEGIN
    -- Sync status_unit_id changes to inventory_attachment
    IF OLD.status_unit_id != NEW.status_unit_id THEN
        -- FIXED: Changed 'status_unit' to 'attachment_status'
        UPDATE inventory_attachment 
        SET attachment_status = NEW.status_unit_id, 
            updated_at = NOW()
        WHERE id_inventory_unit = NEW.id_inventory_unit;
    END IF;
END$$
DELIMITER ;

-- Recreate tr_inventory_unit_status_sync with CORRECT column name
DELIMITER $$
CREATE TRIGGER tr_inventory_unit_status_sync
AFTER UPDATE ON inventory_unit
FOR EACH ROW
BEGIN
    -- Sync status_unit_id changes to inventory_attachment
    IF OLD.status_unit_id != NEW.status_unit_id THEN
        -- FIXED: Changed 'status_unit' to 'attachment_status'
        UPDATE inventory_attachment 
        SET attachment_status = NEW.status_unit_id
        WHERE id_inventory_unit = NEW.id_inventory_unit;
    END IF;
END$$
DELIMITER ;

-- ============================================================================
-- VERIFICATION
-- ============================================================================

-- Show updated triggers
SHOW TRIGGERS WHERE `Table` = 'inventory_unit' AND `Event` = 'UPDATE';

SELECT '✅ Triggers updated successfully!' AS status;
