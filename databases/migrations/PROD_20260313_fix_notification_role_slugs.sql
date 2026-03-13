-- =====================================================
-- Fix Notification Rules - Update Role Slugs to Valid Values
-- Migration: Fix invalid role slugs in notification_rules
-- Date: 2026-03-13
-- =====================================================

-- Notes:
-- Valid role slugs in the system:
-- super_admin, head_marketing, staff_marketing, head_operational, staff_operational,
-- head_purchasing, staff_purchasing, head_accounting, staff_accounting,
-- head_hrd, staff_hrd, head_warehouse, staff_warehouse,
-- administrator, head_it, staff_it,
-- head_service, admin_service_pusat, admin_service_area, supervisor_service, staff_service, manager_service_area

-- Fix: Change generic "supervisor,staff,manager" to actual role slugs
UPDATE notification_rules
SET target_roles = 'head_service,supervisor_service,admin_service_pusat,admin_service_area'
WHERE trigger_event = 'spk_created' AND target_roles = 'supervisor,staff,manager';

-- Fix: Change generic "manager" to valid role slugs for PO notifications
UPDATE notification_rules
SET target_roles = 'head_warehouse,head_purchasing'
WHERE trigger_event = 'purchase_order_created' AND (target_roles = 'manager' OR target_roles IS NULL OR target_roles = '');

-- Fix: DI notifications should target operational and service
UPDATE notification_rules
SET target_roles = 'head_operational,staff_operational,head_service,supervisor_service'
WHERE trigger_event = 'di_submitted' AND target_roles = 'operational';

-- Fix: Service notifications
UPDATE notification_rules
SET target_roles = 'head_service,supervisor_service,staff_service'
WHERE trigger_event IN ('spk_ready', 'work_order_created', 'work_order_assigned')
AND (target_roles LIKE '%supervisor%' OR target_roles LIKE '%staff%' OR target_roles LIKE '%manager%');

-- Fix: Warehouse notifications
UPDATE notification_rules
SET target_roles = 'head_warehouse,staff_warehouse'
WHERE trigger_event IN ('po_received', 'po_verified', 'inventory_unit_added')
AND (target_roles LIKE '%manager%' OR target_roles = 'warehouse');

-- Fix: Marketing notifications
UPDATE notification_rules
SET target_roles = 'head_marketing,staff_marketing'
WHERE trigger_event IN ('quotation_created', 'quotation_approved', 'quotation_rejected', 'contract_created')
AND (target_roles LIKE '%manager%' OR target_roles = 'marketing');

-- Fix: Accounting notifications
UPDATE notification_rules
SET target_roles = 'head_accounting,staff_accounting'
WHERE trigger_event IN ('invoice_created', 'invoice_sent', 'invoice_paid', 'invoice_overdue')
AND (target_roles LIKE '%manager%' OR target_roles = 'accounting');

-- Fix: HRD notifications
UPDATE notification_rules
SET target_roles = 'head_hrd,staff_hrd'
WHERE trigger_event IN ('user_created', 'user_activated', 'user_deactivated', 'employee_assigned')
AND (target_roles LIKE '%manager%' OR target_roles = 'hrd');

-- Fix: Multi-division notifications using target_mixed JSON
-- Example: PO created should notify both Purchasing and Warehouse
UPDATE notification_rules
SET target_mixed = '{"divisions": ["Purchasing", "Warehouse"], "roles": ["head_purchasing", "head_warehouse"], "users": [], "departments": []}'
WHERE trigger_event = 'purchase_order_created';

-- =====================================================
-- Optional: Reset all rules to use info_only style
-- =====================================================
UPDATE notification_rules
SET notification_style = 'info_only'
WHERE notification_style IS NULL OR notification_style = '';

-- =====================================================
-- Optional: Delete rules that are duplicates or unused
-- =====================================================
-- Check for duplicate rules by trigger_event
-- DELETE FROM notification_rules WHERE id NOT IN (
--     SELECT MIN(id) FROM notification_rules GROUP BY trigger_event
-- );
