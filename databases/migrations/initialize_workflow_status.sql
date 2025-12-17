-- ============================================================================
-- Script: Initialize workflow_status for existing units
-- Purpose: Populate workflow_status column based on current kontrak status
-- Date: 2025-12-17
-- Database: optima_ci
-- ============================================================================

USE optima_ci;

-- Update workflow_status for units with active contracts
UPDATE inventory_unit
SET workflow_status = 'DISEWA',
    updated_at = CURRENT_TIMESTAMP
WHERE kontrak_id IS NOT NULL
  AND workflow_status IS NULL
  AND EXISTS (
      SELECT 1 FROM kontrak k 
      WHERE k.id = inventory_unit.kontrak_id 
      AND k.status = 'Aktif'
  );

-- Update workflow_status for units without contracts (available stock)
UPDATE inventory_unit
SET workflow_status = 'TERSEDIA',
    updated_at = CURRENT_TIMESTAMP
WHERE kontrak_id IS NULL
  AND workflow_status IS NULL
  AND status_unit_id IN (
      SELECT id FROM status_unit 
      WHERE nama_status IN ('TERSEDIA', 'AVAILABLE', 'STOCK')
  );

-- Verify updates
SELECT 
    'Workflow Status Distribution' AS info,
    workflow_status,
    COUNT(*) AS total,
    COUNT(kontrak_id) AS with_contract
FROM inventory_unit
GROUP BY workflow_status
ORDER BY workflow_status;

-- ============================================================================
-- Script Complete
-- ============================================================================
