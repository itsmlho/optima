-- ===========================================
-- FIX SWAP UNIT CONFLICT - FINAL SOLUTION
-- ===========================================
-- Problem: Multiple triggers causing conflicts when swapping attachments
-- Solution: Simplify triggers and remove UPDATE-in-UPDATE trigger

-- 1. Drop problematic AFTER UPDATE trigger that tries to UPDATE same table
DROP TRIGGER IF EXISTS `tr_inventory_attachment_unit_sync`;

-- 2. Drop old status sync trigger
DROP TRIGGER IF EXISTS `tr_inventory_attachment_status_sync`;

-- 3. Keep simple BEFORE triggers (these work fine)
-- Already exist: tr_inventory_attachment_before_insert, tr_inventory_attachment_before_update

-- 4. Create NEW simplified status sync trigger without SELECT query
DELIMITER $$

CREATE TRIGGER `tr_inventory_attachment_status_sync_v2` 
BEFORE UPDATE ON `inventory_attachment` 
FOR EACH ROW 
BEGIN
    -- Case 1: Attaching to unit (NULL -> has unit)
    -- Only auto-set if lokasi_penyimpanan is NOT already set (manual swap sets it)
    IF OLD.id_inventory_unit IS NULL 
       AND NEW.id_inventory_unit IS NOT NULL 
       AND (NEW.lokasi_penyimpanan IS NULL OR NEW.lokasi_penyimpanan = OLD.lokasi_penyimpanan) 
    THEN
        SET NEW.attachment_status = 'IN_USE';
        -- Don't auto-set lokasi, let application handle it
        -- This prevents SELECT query conflict
    END IF;
    
    -- Case 2: Detaching from unit (has unit -> NULL)
    IF OLD.id_inventory_unit IS NOT NULL 
       AND NEW.id_inventory_unit IS NULL 
    THEN
        -- Keep BROKEN/MAINTENANCE status, otherwise set AVAILABLE
        IF NEW.attachment_status NOT IN ('BROKEN', 'MAINTENANCE') THEN
            SET NEW.attachment_status = 'AVAILABLE';
        END IF;
        
        -- Set location to Workshop
        IF NEW.lokasi_penyimpanan IS NULL OR NEW.lokasi_penyimpanan = '' THEN
            SET NEW.lokasi_penyimpanan = 'Workshop';
        END IF;
    END IF;
    
    -- Validation: IN_USE/USED must have unit
    IF NEW.attachment_status IN ('IN_USE', 'USED') 
       AND NEW.id_inventory_unit IS NULL 
    THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Validasi Error: Item dengan status IN_USE/USED harus terpasang di unit.';
    END IF;
END$$

DELIMITER ;

-- Note: Application (PHP) will handle lokasi_penyimpanan when swapping
-- This avoids SELECT query in trigger which causes deadlock
