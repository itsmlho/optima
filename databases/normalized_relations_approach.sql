-- OptimaPro Activity Logging - Normalized Relations Table
-- Date: 2025-09-09
-- Purpose: Separate table for entity relations (normalized approach)

-- Create separate relations table
CREATE TABLE IF NOT EXISTS system_activity_relations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    activity_log_id INT NOT NULL,
    entity_type VARCHAR(50) NOT NULL, -- 'kontrak', 'spk', 'di', 'po', etc.
    entity_id INT NOT NULL,
    relation_type VARCHAR(50) DEFAULT 'DIRECT', -- 'DIRECT', 'PARENT', 'CHILD', 'REFERENCE'
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (activity_log_id) REFERENCES system_activity_log(id) ON DELETE CASCADE,
    INDEX idx_activity_entity (activity_log_id, entity_type),
    INDEX idx_entity_lookup (entity_type, entity_id),
    UNIQUE KEY unique_relation (activity_log_id, entity_type, entity_id)
);

-- Example usage:
/*
For a Kontrak deletion (ID: 123) that affects SPK (ID: 456) and DI (ID: 789):

system_activity_log:
- id: 100, table_name: 'kontrak', record_id: 123, action_type: 'DELETE'

system_activity_relations:
- activity_log_id: 100, entity_type: 'kontrak', entity_id: 123, relation_type: 'DIRECT'
- activity_log_id: 100, entity_type: 'spk', entity_id: 456, relation_type: 'CHILD'  
- activity_log_id: 100, entity_type: 'di', entity_id: 789, relation_type: 'CHILD'
*/

-- Create view for easy querying
CREATE OR REPLACE VIEW v_activity_log_with_relations AS
SELECT 
    sal.id,
    sal.table_name,
    sal.record_id,
    sal.action_type,
    sal.action_description,
    sal.module_name,
    sal.submenu_item,
    sal.workflow_stage,
    sal.business_impact,
    sal.user_id,
    sal.created_at,
    GROUP_CONCAT(
        CONCAT(sar.entity_type, ':', sar.entity_id, '(', sar.relation_type, ')')
        SEPARATOR ', '
    ) as related_entities
FROM system_activity_log sal
LEFT JOIN system_activity_relations sar ON sal.id = sar.activity_log_id
GROUP BY sal.id;

SELECT 'Normalized relations table created successfully' as status;
