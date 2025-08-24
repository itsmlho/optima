/*
  SAFE MIGRATION STEPS
  0) BACKUP DATABASE FIRST!
  1) Run the SELECT checks below. If checks show "0" (missing), run the matching ALTER/CREATE.
*/

-- 1A) Check if column exists (run and inspect result)
SELECT COUNT(*) AS col_exists
FROM INFORMATION_SCHEMA.COLUMNS
WHERE table_schema = DATABASE()
  AND table_name = 'inventory_attachment'
  AND column_name = 'id_inventory_unit';

-- 1B) If col_exists = 0 THEN run this:
ALTER TABLE inventory_attachment
  ADD COLUMN id_inventory_unit INT NULL AFTER po_id;

-- 1C) Check if index exists (run and inspect result)
SELECT COUNT(*) AS idx_exists
FROM INFORMATION_SCHEMA.STATISTICS
WHERE table_schema = DATABASE()
  AND table_name = 'inventory_attachment'
  AND index_name = 'idx_inv_att_id_inventory_unit';

-- 1D) If idx_exists = 0 THEN run this:
CREATE INDEX idx_inv_att_id_inventory_unit ON inventory_attachment (id_inventory_unit);

-- 2) Create generic log table for inventory items (attachment/charger) if missing
CREATE TABLE IF NOT EXISTS inventory_item_unit_log (
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_inventory_attachment INT NOT NULL,
  id_inventory_unit INT NOT NULL,
  action ENUM('assign','remove') NOT NULL,
  user_id INT NULL,
  note VARCHAR(255) NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX (id_inventory_attachment),
  INDEX (id_inventory_unit)
);

-- 3A) Check if FK constraint exists (inspect before adding)
SELECT CONSTRAINT_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'inventory_attachment'
  AND COLUMN_NAME = 'id_inventory_unit'
  AND REFERENCED_TABLE_NAME = 'inventory_unit';

-- 3B) If no FK exists (previous query returns empty) THEN run:
ALTER TABLE inventory_attachment
  ADD CONSTRAINT fk_inv_att_unit
  FOREIGN KEY (id_inventory_unit) REFERENCES inventory_unit(id_inventory_unit)
  ON DELETE SET NULL ON UPDATE CASCADE;

-- 4) Preview rows that have lokasi_penyimpanan text like "Digunakan pada unit 3"
SELECT id_inventory_attachment, lokasi_penyimpanan
FROM inventory_attachment
WHERE lokasi_penyimpanan IS NOT NULL
  AND lokasi_penyimpanan REGEXP 'unit ?#?[0-9]+';

-- IMPORTANT: verify above SELECT results before running mapping below

-- 5) Mapping lokasi_penyimpanan -> id_inventory_unit
-- NOTE: REGEXP_SUBSTR requires MySQL 8+. Run only after verifying SELECT in step 4.
UPDATE inventory_attachment
SET id_inventory_unit = CAST(REGEXP_SUBSTR(lokasi_penyimpanan, '[0-9]+') AS UNSIGNED)
WHERE id_inventory_unit IS NULL
  AND lokasi_penyimpanan REGEXP 'unit ?#?[0-9]+';

-- 6) Clear lokasi_penyimpanan for items already moved to RENTAL (status_unit = 3)
UPDATE inventory_attachment
SET lokasi_penyimpanan = NULL
WHERE status_unit = 3 AND lokasi_penyimpanan IS NOT NULL;

-- 7) Example assign (run manually with real values)
-- assign inventory_attachment -> unit
-- REPLACE placeholders with actual IDs before running
UPDATE inventory_attachment
SET id_inventory_unit = /*{unit_id}*/, status_unit = 3, lokasi_penyimpanan = NULL
WHERE id_inventory_attachment = /*{inventory_attachment_id}*/;

INSERT INTO inventory_item_unit_log (id_inventory_attachment, id_inventory_unit, action, user_id, note)
VALUES (/*{inventory_attachment_id}*/, /*{unit_id}*/, 'assign', /*{user_id}*/, 'Assigned via SPK fabrikasi #{spk_id}');

-- example remove (return to stock)
UPDATE inventory_attachment
SET id_inventory_unit = NULL, status_unit = 7, lokasi_penyimpanan = /*'Gudang A'*/
WHERE id_inventory_attachment = /*{inventory_attachment_id}*/;

INSERT INTO inventory_item_unit_log (id_inventory_attachment, id_inventory_unit, action, user_id, note)
VALUES (/*{inventory_attachment_id}*/, /*{unit_id}*/, 'remove', /*{user_id}*/, 'Removed via SPK return #{spk_id}');

-- 8) Optional: Trigger to auto-log changes to id_inventory_unit
-- Drop existing trigger if present, then create.
DROP TRIGGER IF EXISTS trg_inv_att_assign;
DELIMITER $$
CREATE TRIGGER trg_inv_att_assign AFTER UPDATE ON inventory_attachment
FOR EACH ROW
BEGIN
  IF OLD.id_inventory_unit IS NULL AND NEW.id_inventory_unit IS NOT NULL THEN
    INSERT INTO inventory_item_unit_log (id_inventory_attachment, id_inventory_unit, action, user_id, note)
    VALUES (NEW.id_inventory_attachment, NEW.id_inventory_unit, 'assign', NULL, 'auto-log on assign');
  ELSEIF OLD.id_inventory_unit IS NOT NULL AND NEW.id_inventory_unit IS NULL THEN
    INSERT INTO inventory_item_unit_log (id_inventory_attachment, id_inventory_unit, action, user_id, note)
    VALUES (OLD.id_inventory_attachment, OLD.id_inventory_unit, 'remove', NULL, 'auto-log on remove');
  END IF;
END$$
DELIMITER ;