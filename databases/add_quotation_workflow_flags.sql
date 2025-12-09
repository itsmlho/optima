-- Add workflow completion flags to quotations table
-- This enables proper sequential workflow tracking

ALTER TABLE `quotations` 
ADD COLUMN `customer_location_complete` TINYINT(1) DEFAULT 0 COMMENT 'Flag: Customer location has been set',
ADD COLUMN `customer_contract_complete` TINYINT(1) DEFAULT 0 COMMENT 'Flag: Contract has been created/linked',
ADD COLUMN `spk_created` TINYINT(1) DEFAULT 0 COMMENT 'Flag: SPK has been created';

-- Add indexes for performance
ALTER TABLE `quotations`
ADD INDEX `idx_workflow_flags` (`customer_location_complete`, `customer_contract_complete`, `spk_created`);

-- Update existing quotations based on current state
UPDATE `quotations` 
SET `customer_location_complete` = 1,
    `customer_contract_complete` = 1
WHERE `created_contract_id` IS NOT NULL;

UPDATE `quotations`
SET `customer_location_complete` = 1
WHERE `created_customer_id` IS NOT NULL 
  AND `workflow_stage` = 'DEAL'
  AND `created_contract_id` IS NULL;

-- Show results
SELECT 
    'Migration completed successfully' AS status,
    COUNT(*) AS total_quotations,
    SUM(customer_location_complete) AS location_complete_count,
    SUM(customer_contract_complete) AS contract_complete_count
FROM `quotations`;
