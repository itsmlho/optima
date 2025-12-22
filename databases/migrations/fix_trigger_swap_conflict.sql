-- Fix trigger conflict when swapping attachments
-- The trigger tries to SELECT from inventory_unit which causes deadlock
-- Solution: Check if all required fields are already set, skip if yes

DROP TRIGGER IF EXISTS `tr_inventory_attachment_status_sync`;

DELIMITER $$
CREATE TRIGGER `tr_inventory_attachment_status_sync` BEFORE UPDATE ON `inventory_attachment` FOR EACH ROW BEGIN
    
    -- Check if this is a manual swap (all fields already set correctly)
    -- If attachment_status and lokasi_penyimpanan are already set, skip trigger logic
    IF NEW.attachment_status IS NOT NULL 
       AND NEW.lokasi_penyimpanan IS NOT NULL 
       AND NEW.lokasi_penyimpanan != OLD.lokasi_penyimpanan THEN
        -- Manual update detected, skip trigger
        -- This allows swapAttachmentBetweenUnits to work without conflict
        SET @skip_trigger = 1;
    END IF;
    
    -- Only run trigger logic if not skipped
    IF @skip_trigger IS NULL THEN
        
        -- Case 1: Attaching to unit (NULL -> has unit)
        IF OLD.id_inventory_unit IS NULL AND NEW.id_inventory_unit IS NOT NULL THEN
            
            SET NEW.attachment_status = 'IN_USE';
            
            -- Get unit number
            SET NEW.lokasi_penyimpanan = (
                SELECT CONCAT('Terpasang di Unit ', iu.no_unit)
                FROM `inventory_unit` iu 
                WHERE iu.id_inventory_unit = NEW.id_inventory_unit
                LIMIT 1
            );
            
            -- Fallback if unit not found
            IF NEW.lokasi_penyimpanan IS NULL THEN
                SET NEW.lokasi_penyimpanan = CONCAT('Terpasang di Unit ID ', NEW.id_inventory_unit);
            END IF;
        END IF;
        
        -- Case 2: Detaching from unit (has unit -> NULL)
        IF OLD.id_inventory_unit IS NOT NULL AND NEW.id_inventory_unit IS NULL THEN
            
            -- Keep BROKEN/MAINTENANCE status, otherwise set AVAILABLE
            IF NEW.attachment_status NOT IN ('BROKEN', 'MAINTENANCE') THEN
                SET NEW.attachment_status = 'AVAILABLE';
            END IF;
            
            -- Set location to Workshop
            SET NEW.lokasi_penyimpanan = 'Workshop';
        END IF;
        
        -- Case 3: Moving between units (has unit -> different unit)
        IF OLD.id_inventory_unit IS NOT NULL 
           AND NEW.id_inventory_unit IS NOT NULL 
           AND OLD.id_inventory_unit != NEW.id_inventory_unit THEN
            
            -- Get new unit number
            SET NEW.lokasi_penyimpanan = (
                SELECT CONCAT('Terpasang di Unit ', iu.no_unit)
                FROM `inventory_unit` iu 
                WHERE iu.id_inventory_unit = NEW.id_inventory_unit
                LIMIT 1
            );
            
            -- Keep IN_USE status
            SET NEW.attachment_status = 'IN_USE';
        END IF;
        
    END IF;
    
    -- Reset skip flag for next operation
    SET @skip_trigger = NULL;
    
    -- Validation: IN_USE/USED must have unit
    IF NEW.attachment_status IN ('IN_USE', 'USED') AND NEW.id_inventory_unit IS NULL THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Validasi Error: Item dengan status IN_USE/USED harus terpasang di unit.';
    END IF;
END$$

DELIMITER ;
