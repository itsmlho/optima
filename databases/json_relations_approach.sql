-- OptimaPro Activity Logging - JSON Based Relations (RECOMMENDED)
-- Date: 2025-09-09
-- Purpose: Efficient logging with JSON relations instead of multiple columns

-- Backup existing data first
CREATE TABLE IF NOT EXISTS system_activity_log_backup_json AS 
SELECT * FROM system_activity_log;

-- Add JSON field for all relations (replacing multiple related_* columns)
ALTER TABLE system_activity_log 
ADD COLUMN related_entities JSON DEFAULT NULL AFTER related_di_id,
ADD COLUMN context_data JSON DEFAULT NULL AFTER related_entities;

-- Index for JSON field (MySQL 5.7+)
ALTER TABLE system_activity_log 
ADD INDEX idx_related_entities ((CAST(related_entities->'$.kontrak_id' AS UNSIGNED))),
ADD INDEX idx_related_spk ((CAST(related_entities->'$.spk_id' AS UNSIGNED))),
ADD INDEX idx_related_di ((CAST(related_entities->'$.di_id' AS UNSIGNED)));

-- Example JSON structure:
/*
related_entities: {
  "kontrak_id": 123,
  "spk_id": 456, 
  "di_id": 789,
  "po_id": 101,
  "invoice_id": 112,
  "unit_id": 334,
  "user_id": 5,
  "quotation_id": 78
}

context_data: {
  "menu_path": "MARKETING > Kontrak/PO Rental",
  "action_source": "web_interface",
  "client_data": {...},
  "financial_amount": 1500000,
  "currency": "IDR",
  "priority": "HIGH"
}
*/

-- Verification
SELECT 'JSON-based relations added successfully' as status;
DESCRIBE system_activity_log;
