-- ============================================================================
-- Add Foreign Key Constraint: notification_rules -> trigger_events
-- ============================================================================

-- First, ensure both columns have the same collation
ALTER TABLE trigger_events 
MODIFY COLUMN event_code VARCHAR(100) 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_general_ci 
NOT NULL UNIQUE 
COMMENT 'Unique event identifier (sama dengan notification_rules.trigger_event)';

-- Check if foreign key already exists
SELECT 'Checking existing constraints...' as Status;

SELECT 
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'optima_ci'
  AND TABLE_NAME = 'notification_rules'
  AND REFERENCED_TABLE_NAME IS NOT NULL;

-- Add foreign key constraint (drop first if exists)
SET @constraint_exists = (
    SELECT COUNT(*) 
    FROM information_schema.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = 'optima_ci' 
    AND TABLE_NAME = 'notification_rules' 
    AND CONSTRAINT_NAME = 'fk_notification_rules_trigger_event'
);

-- Drop if exists
SET @drop_query = IF(@constraint_exists > 0, 
    'ALTER TABLE notification_rules DROP FOREIGN KEY fk_notification_rules_trigger_event', 
    'SELECT "No constraint to drop" as Status');

PREPARE stmt FROM @drop_query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add foreign key constraint
ALTER TABLE notification_rules
ADD CONSTRAINT fk_notification_rules_trigger_event
FOREIGN KEY (trigger_event) 
REFERENCES trigger_events(event_code)
ON UPDATE CASCADE
ON DELETE RESTRICT;

-- Verify the constraint
SELECT 'Foreign key constraint added successfully!' as Status;

SELECT 
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'optima_ci'
  AND TABLE_NAME = 'notification_rules'
  AND CONSTRAINT_NAME = 'fk_notification_rules_trigger_event';
