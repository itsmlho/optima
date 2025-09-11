-- OptimaPro Database Migration: Clean JSON Relations Implementation
-- Date: 2025-09-09
-- Purpose: Clean up related_* columns and optimize JSON approach

-- STEP 1: Remove existing related_* columns
ALTER TABLE system_activity_log 
DROP COLUMN IF EXISTS related_kontrak_id,
DROP COLUMN IF EXISTS related_spk_id,
DROP COLUMN IF EXISTS related_di_id;

-- STEP 2: Modify existing related_entities to be proper JSON
ALTER TABLE system_activity_log 
MODIFY COLUMN related_entities JSON NULL COMMENT 'JSON object storing related entity relationships';

-- STEP 3: Create index for JSON column
CREATE INDEX idx_related_entities ON system_activity_log (related_entities(255));

-- STEP 4: Update action_type enum to include more comprehensive actions
ALTER TABLE system_activity_log 
MODIFY COLUMN action_type ENUM(
    'CREATE','READ','UPDATE','DELETE','EXPORT','IMPORT',
    'LOGIN','LOGOUT','APPROVE','REJECT','SUBMIT','CANCEL',
    'ASSIGN','UNASSIGN','COMPLETE','PRINT','DOWNLOAD'
) NOT NULL;

-- STEP 5: Add submenu_item field if not exists
ALTER TABLE system_activity_log 
ADD COLUMN IF NOT EXISTS submenu_item VARCHAR(100) NULL COMMENT 'Specific submenu item accessed' AFTER module_name;

-- STEP 6: Create helper view for easy querying
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
    -- Extract specific entity arrays for easier querying
    CASE 
        WHEN JSON_VALID(related_entities) = 1 THEN JSON_EXTRACT(related_entities, '$.kontrak')
        ELSE NULL 
    END as related_kontrak,
    CASE 
        WHEN JSON_VALID(related_entities) = 1 THEN JSON_EXTRACT(related_entities, '$.spk')
        ELSE NULL 
    END as related_spk,
    CASE 
        WHEN JSON_VALID(related_entities) = 1 THEN JSON_EXTRACT(related_entities, '$.di')
        ELSE NULL 
    END as related_di,
    CASE 
        WHEN JSON_VALID(related_entities) = 1 THEN JSON_EXTRACT(related_entities, '$.po')
        ELSE NULL 
    END as related_po
FROM system_activity_log;

-- STEP 7: Create stored procedure for easy logging
DELIMITER //
CREATE OR REPLACE PROCEDURE sp_log_activity_with_relations(
    IN p_table_name VARCHAR(64),
    IN p_record_id INT,
    IN p_action_type VARCHAR(20),
    IN p_action_description VARCHAR(255),
    IN p_module_name VARCHAR(50),
    IN p_submenu_item VARCHAR(100),
    IN p_workflow_stage VARCHAR(50),
    IN p_business_impact VARCHAR(20),
    IN p_user_id INT,
    IN p_related_entities_json TEXT
)
BEGIN
    INSERT INTO system_activity_log (
        table_name, record_id, action_type, action_description,
        module_name, submenu_item, workflow_stage, business_impact,
        user_id, related_entities, created_at
    ) VALUES (
        p_table_name, p_record_id, p_action_type, p_action_description,
        p_module_name, p_submenu_item, p_workflow_stage, p_business_impact,
        p_user_id, p_related_entities_json, NOW()
    );
END //
DELIMITER ;

SELECT 'JSON Relations migration completed successfully!' as status;
SELECT 'Table structure optimized for JSON relations approach' as note;
