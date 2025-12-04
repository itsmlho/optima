-- Add workflow tracking fields to quotations table (optima_ci database)
-- This ensures sequential workflow enforcement
-- ✅ MIGRATION COMPLETED SUCCESSFULLY

-- Step 1: Add tracking columns
ALTER TABLE quotations 
ADD COLUMN customer_location_complete TINYINT(1) DEFAULT 0 COMMENT 'Customer location has been selected/created',
ADD COLUMN customer_contract_complete TINYINT(1) DEFAULT 0 COMMENT 'Customer contract has been selected/created',
ADD COLUMN spk_created TINYINT(1) DEFAULT 0 COMMENT 'SPK has been created from this quotation';

-- Step 2: Create index for faster workflow queries
CREATE INDEX idx_workflow_tracking ON quotations(workflow_stage, customer_location_complete, customer_contract_complete);

-- Update existing DEAL quotations to mark completed steps
-- (This is safe migration - marks existing deals that already have customer/contract)
UPDATE quotations q
SET customer_location_complete = 1
WHERE workflow_stage = 'DEAL' 
  AND created_customer_id IS NOT NULL
  AND EXISTS (
      SELECT 1 FROM customer_locations cl 
      WHERE cl.customer_id = q.created_customer_id 
      AND cl.is_active = 1
      LIMIT 1
  );

UPDATE quotations q
SET customer_contract_complete = 1
WHERE workflow_stage = 'DEAL' 
  AND created_customer_id IS NOT NULL
  AND EXISTS (
      SELECT 1 FROM kontrak k
      JOIN customer_locations cl ON k.customer_location_id = cl.id
      WHERE cl.customer_id = q.created_customer_id 
      AND k.status = 'Aktif'
      LIMIT 1
  );
