-- ========================================================================
-- Add Permissions for New Menu Items
-- Date: March 7, 2026
-- Purpose: Add permissions for Unit Audit, Audit Approval, and Surat Jalan
-- ========================================================================

-- ========================================================================
-- 1. MARKETING: Audit Approval
-- ========================================================================

INSERT INTO permissions (module, page, action, key_name, display_name, description, category, is_active, created_at, updated_at)
SELECT 'marketing', 'audit_approval', 'navigation', 'marketing.audit_approval.navigation', 'Audit Approval Menu', 'Access to Audit Approval menu in Marketing', 'Marketing', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE key_name = 'marketing.audit_approval.navigation');

INSERT INTO permissions (module, page, action, key_name, display_name, description, category, is_active, created_at, updated_at)
SELECT 'marketing', 'audit_approval', 'view', 'marketing.audit_approval.view', 'View Audit Requests', 'View unit audit requests from Service', 'Marketing', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE key_name = 'marketing.audit_approval.view');

INSERT INTO permissions (module, page, action, key_name, display_name, description, category, is_active, created_at, updated_at)
SELECT 'marketing', 'audit_approval', 'approve', 'marketing.audit_approval.approve', 'Approve Audit Request', 'Approve unit audit requests', 'Marketing', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE key_name = 'marketing.audit_approval.approve');

INSERT INTO permissions (module, page, action, key_name, display_name, description, category, is_active, created_at, updated_at)
SELECT 'marketing', 'audit_approval', 'reject', 'marketing.audit_approval.reject', 'Reject Audit Request', 'Reject unit audit requests', 'Marketing', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE key_name = 'marketing.audit_approval.reject');

INSERT INTO permissions (module, page, action, key_name, display_name, description, category, is_active, created_at, updated_at)
SELECT 'marketing', 'audit_approval', 'export', 'marketing.audit_approval.export', 'Export Audit Data', 'Export audit approval data', 'Marketing', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE key_name = 'marketing.audit_approval.export');

-- ========================================================================
-- 2. SERVICE: Unit Audit
-- ========================================================================

INSERT INTO permissions (module, page, action, key_name, display_name, description, category, is_active, created_at, updated_at)
SELECT 'service', 'unit_audit', 'navigation', 'service.unit_audit.navigation', 'Unit Audit Menu', 'Access to Unit Audit menu in Service', 'Service', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE key_name = 'service.unit_audit.navigation');

INSERT INTO permissions (module, page, action, key_name, display_name, description, category, is_active, created_at, updated_at)
SELECT 'service', 'unit_audit', 'view', 'service.unit_audit.view', 'View Unit Audit', 'View unit audit requests', 'Service', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE key_name = 'service.unit_audit.view');

INSERT INTO permissions (module, page, action, key_name, display_name, description, category, is_active, created_at, updated_at)
SELECT 'service', 'unit_audit', 'create', 'service.unit_audit.create', 'Create Audit Request', 'Create new unit audit request', 'Service', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE key_name = 'service.unit_audit.create');

INSERT INTO permissions (module, page, action, key_name, display_name, description, category, is_active, created_at, updated_at)
SELECT 'service', 'unit_audit', 'edit', 'service.unit_audit.edit', 'Edit Audit Request', 'Edit unit audit request details', 'Service', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE key_name = 'service.unit_audit.edit');

INSERT INTO permissions (module, page, action, key_name, display_name, description, category, is_active, created_at, updated_at)
SELECT 'service', 'unit_audit', 'submit', 'service.unit_audit.submit', 'Submit Audit Request', 'Submit audit request for approval', 'Service', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE key_name = 'service.unit_audit.submit');

INSERT INTO permissions (module, page, action, key_name, display_name, description, category, is_active, created_at, updated_at)
SELECT 'service', 'unit_audit', 'delete', 'service.unit_audit.delete', 'Delete Audit Request', 'Delete draft audit requests', 'Service', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE key_name = 'service.unit_audit.delete');

INSERT INTO permissions (module, page, action, key_name, display_name, description, category, is_active, created_at, updated_at)
SELECT 'service', 'unit_audit', 'export', 'service.unit_audit.export', 'Export Unit Audit', 'Export unit audit data', 'Service', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE key_name = 'service.unit_audit.export');

-- ========================================================================
-- 3. WAREHOUSE: Unit Movements / Surat Jalan
-- ========================================================================

INSERT INTO permissions (module, page, action, key_name, display_name, description, category, is_active, created_at, updated_at)
SELECT 'warehouse', 'movements', 'navigation', 'warehouse.movements.navigation', 'Surat Jalan Menu', 'Access to Surat Jalan menu in Warehouse', 'Warehouse', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE key_name = 'warehouse.movements.navigation');

INSERT INTO permissions (module, page, action, key_name, display_name, description, category, is_active, created_at, updated_at)
SELECT 'warehouse', 'movements', 'view', 'warehouse.movements.view', 'View Surat Jalan', 'View unit movement records', 'Warehouse', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE key_name = 'warehouse.movements.view');

INSERT INTO permissions (module, page, action, key_name, display_name, description, category, is_active, created_at, updated_at)
SELECT 'warehouse', 'movements', 'create', 'warehouse.movements.create', 'Create Surat Jalan', 'Create new unit movement/surat jalan', 'Warehouse', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE key_name = 'warehouse.movements.create');

INSERT INTO permissions (module, page, action, key_name, display_name, description, category, is_active, created_at, updated_at)
SELECT 'warehouse', 'movements', 'edit', 'warehouse.movements.edit', 'Edit Surat Jalan', 'Edit draft unit movements', 'Warehouse', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE key_name = 'warehouse.movements.edit');

INSERT INTO permissions (module, page, action, key_name, display_name, description, category, is_active, created_at, updated_at)
SELECT 'warehouse', 'movements', 'confirm_departure', 'warehouse.movements.confirm_departure', 'Confirm Departure', 'Confirm unit departure', 'Warehouse', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE key_name = 'warehouse.movements.confirm_departure');

INSERT INTO permissions (module, page, action, key_name, display_name, description, category, is_active, created_at, updated_at)
SELECT 'warehouse', 'movements', 'confirm_arrival', 'warehouse.movements.confirm_arrival', 'Confirm Arrival', 'Confirm unit arrival at destination', 'Warehouse', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE key_name = 'warehouse.movements.confirm_arrival');

INSERT INTO permissions (module, page, action, key_name, display_name, description, category, is_active, created_at, updated_at)
SELECT 'warehouse', 'movements', 'cancel', 'warehouse.movements.cancel', 'Cancel Movement', 'Cancel unit movement/surat jalan', 'Warehouse', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE key_name = 'warehouse.movements.cancel');

INSERT INTO permissions (module, page, action, key_name, display_name, description, category, is_active, created_at, updated_at)
SELECT 'warehouse', 'movements', 'print', 'warehouse.movements.print', 'Print Surat Jalan', 'Print surat jalan document', 'Warehouse', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE key_name = 'warehouse.movements.print');

INSERT INTO permissions (module, page, action, key_name, display_name, description, category, is_active, created_at, updated_at)
SELECT 'warehouse', 'movements', 'export', 'warehouse.movements.export', 'Export Movement Data', 'Export movement history data', 'Warehouse', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE key_name = 'warehouse.movements.export');

-- ========================================================================
-- Verification Queries
-- ========================================================================

-- Check Marketing Audit Approval permissions
SELECT 
    '✓ Marketing Audit Approval Permissions' AS section,
    COUNT(*) AS total_permissions
FROM permissions 
WHERE key_name LIKE 'marketing.audit_approval.%';

-- Check Service Unit Audit permissions
SELECT 
    '✓ Service Unit Audit Permissions' AS section,
    COUNT(*) AS total_permissions
FROM permissions 
WHERE key_name LIKE 'service.unit_audit.%';

-- Check Warehouse Movements permissions
SELECT 
    '✓ Warehouse Movements/Surat Jalan Permissions' AS section,
    COUNT(*) AS total_permissions
FROM permissions 
WHERE key_name LIKE 'warehouse.movements.%';

-- Show all new permissions
SELECT 
    id, 
    module, 
    page, 
    action, 
    key_name, 
    display_name,
    is_active
FROM permissions 
WHERE key_name IN (
    'marketing.audit_approval.navigation',
    'marketing.audit_approval.view',
    'marketing.audit_approval.approve',
    'marketing.audit_approval.reject',
    'marketing.audit_approval.export',
    'service.unit_audit.navigation',
    'service.unit_audit.view',
    'service.unit_audit.create',
    'service.unit_audit.edit',
    'service.unit_audit.submit',
    'service.unit_audit.delete',
    'service.unit_audit.export',
    'warehouse.movements.navigation',
    'warehouse.movements.view',
    'warehouse.movements.create',
    'warehouse.movements.edit',
    'warehouse.movements.confirm_departure',
    'warehouse.movements.confirm_arrival',
    'warehouse.movements.cancel',
    'warehouse.movements.print',
    'warehouse.movements.export'
)
ORDER BY module, page, action;

-- Summary
SELECT 
    'Total new permissions added:' AS summary,
    COUNT(*) AS count
FROM permissions 
WHERE key_name LIKE 'marketing.audit_approval.%'
   OR key_name LIKE 'service.unit_audit.%'
   OR key_name LIKE 'warehouse.movements.%';
