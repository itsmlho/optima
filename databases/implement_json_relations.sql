-- OptimaPro Database Migration: JSON Relations Implementation
-- Date: 2025-09-09
-- Purpose: Clean up related_* columns and implement JSON approach

-- STEP 1: Remove existing related_* columns
ALTER TABLE system_activity_log 
DROP COLUMN IF EXISTS related_kontrak_id,
DROP COLUMN IF EXISTS related_spk_id,
DROP COLUMN IF EXISTS related_di_id;

-- STEP 2: Add JSON related_entities column
ALTER TABLE system_activity_log 
ADD COLUMN related_entities JSON NULL COMMENT 'JSON object storing related entity relationships' AFTER business_impact;

-- STEP 3: Create indexes for JSON queries (MariaDB compatible)
-- Simple index on JSON column
CREATE INDEX idx_related_entities ON system_activity_log (related_entities(255));

-- STEP 4: Create helper view for easy querying
CREATE OR REPLACE VIEW v_activity_log_relations AS
SELECT 
    id,
    table_name,
    record_id,
    action_type,
    action_description,
    module_name,
    submenu_item,
    workflow_stage,
    business_impact,
    user_id,
    created_at,
    related_entities,
    -- Extract specific entity arrays
    JSON_EXTRACT(related_entities, '$.kontrak') as related_kontrak,
    JSON_EXTRACT(related_entities, '$.spk') as related_spk,
    JSON_EXTRACT(related_entities, '$.di') as related_di,
    JSON_EXTRACT(related_entities, '$.po') as related_po,
    JSON_EXTRACT(related_entities, '$.rental') as related_rental,
    JSON_EXTRACT(related_entities, '$.inventory') as related_inventory,
    JSON_EXTRACT(related_entities, '$.maintenance') as related_maintenance
FROM system_activity_log
WHERE related_entities IS NOT NULL;

-- STEP 5: Create stored procedure for easy logging
DELIMITER //
CREATE OR REPLACE PROCEDURE sp_log_activity_with_relations(
    IN p_table_name VARCHAR(100),
    IN p_record_id INT,
    IN p_action_type ENUM('CREATE','READ','UPDATE','DELETE','EXPORT','IMPORT','LOGIN','LOGOUT','APPROVE','REJECT','SUBMIT','CANCEL'),
    IN p_action_description TEXT,
    IN p_module_name VARCHAR(100),
    IN p_submenu_item VARCHAR(100),
    IN p_workflow_stage VARCHAR(50),
    IN p_business_impact ENUM('LOW','MEDIUM','HIGH','CRITICAL'),
    IN p_user_id INT,
    IN p_related_entities JSON
)
BEGIN
    INSERT INTO system_activity_log (
        table_name, record_id, action_type, action_description,
        module_name, submenu_item, workflow_stage, business_impact,
        user_id, related_entities, created_at
    ) VALUES (
        p_table_name, p_record_id, p_action_type, p_action_description,
        p_module_name, p_submenu_item, p_workflow_stage, p_business_impact,
        p_user_id, p_related_entities, NOW()
    );
END //
DELIMITER ;

-- STEP 6: Test data insertion examples
-- Example 1: Kontrak deletion affecting SPK and DI
/*
CALL sp_log_activity_with_relations(
    'kontrak', 123, 'DELETE', 'Kontrak deleted by user',
    'MARKETING', 'Data Kontrak', 'DELETE_CONFIRMED', 'HIGH', 1,
    JSON_OBJECT(
        'kontrak', JSON_ARRAY(123),
        'spk', JSON_ARRAY(456, 789),
        'di', JSON_ARRAY(101112)
    )
);
*/

-- Example 2: SPK creation with parent Kontrak
/*
CALL sp_log_activity_with_relations(
    'spk', 456, 'CREATE', 'New SPK created from Kontrak',
    'SERVICE', 'SPK Management', 'SPK_CREATED', 'MEDIUM', 1,
    JSON_OBJECT(
        'spk', JSON_ARRAY(456),
        'kontrak', JSON_ARRAY(123)
    )
);
*/

SELECT 'JSON Relations migration completed successfully!' as status;
SELECT 'Use related_entities JSON column for flexible entity relationships' as note;
