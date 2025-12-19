-- ============================================================================
-- Phase 3: MEDIUM Priority Notification Rules Migration
-- Notification System Implementation - Phase 3
-- Coverage: Customer Management (3), Warehouse Extended (3), 
--           Operational Workflows (4), Finance Extended (3),
--           SPK Management (2), Additional Marketing (2)
-- Total: 17 notification rules
-- ============================================================================

USE `optima_ci`;

-- ============================================================================
-- CATEGORY 1: Customer Management (3 rules)
-- ============================================================================

-- Rule 1: Customer Created
INSERT INTO `notification_rules` (
    `event_name`, `module`, `title_template`, `message_template`, 
    `target_roles`, `target_users`, `priority`, `is_active`, 
    `created_at`, `updated_at`
) VALUES (
    'customer_created',
    'customer',
    'New Customer: {{customer_name}}',
    'New customer {{customer_name}} ({{customer_code}}) has been created. Type: {{customer_type}}, Contact: {{phone}}',
    'admin,marketing_manager,sales',
    NULL,
    'medium',
    1,
    NOW(),
    NOW()
);

-- Rule 2: Customer Updated
INSERT INTO `notification_rules` (
    `event_name`, `module`, `title_template`, `message_template`, 
    `target_roles`, `target_users`, `priority`, `is_active`, 
    `created_at`, `updated_at`
) VALUES (
    'customer_updated',
    'customer',
    'Customer Updated: {{customer_name}}',
    'Customer {{customer_name}} ({{customer_code}}) has been updated. Changes: {{changes}}',
    'admin,marketing_manager,sales',
    NULL,
    'medium',
    1,
    NOW(),
    NOW()
);

-- Rule 3: Customer Status Changed
INSERT INTO `notification_rules` (
    `event_name`, `module`, `title_template`, `message_template`, 
    `target_roles`, `target_users`, `priority`, `is_active`, 
    `created_at`, `updated_at`
) VALUES (
    'customer_status_changed',
    'customer',
    'Customer Status Changed: {{customer_name}}',
    'Customer {{customer_name}} ({{customer_code}}) status changed from {{old_status}} to {{new_status}}. Reason: {{reason}}',
    'admin,marketing_manager,sales',
    NULL,
    'high',
    1,
    NOW(),
    NOW()
);

-- ============================================================================
-- CATEGORY 2: Warehouse Extended (3 rules)
-- ============================================================================

-- Rule 4: Warehouse Stock Alert
INSERT INTO `notification_rules` (
    `event_name`, `module`, `title_template`, `message_template`, 
    `target_roles`, `target_users`, `priority`, `is_active`, 
    `created_at`, `updated_at`
) VALUES (
    'warehouse_stock_alert',
    'warehouse',
    'Low Stock Alert: {{item_name}}',
    'URGENT: {{item_name}} stock is low. Current: {{current_stock}} {{unit}}, Minimum: {{minimum_stock}} {{unit}}. Warehouse: {{warehouse_name}}',
    'admin,warehouse_manager,procurement',
    NULL,
    'high',
    1,
    NOW(),
    NOW()
);

-- Rule 5: Warehouse Transfer Completed
INSERT INTO `notification_rules` (
    `event_name`, `module`, `title_template`, `message_template`, 
    `target_roles`, `target_users`, `priority`, `is_active`, 
    `created_at`, `updated_at`
) VALUES (
    'warehouse_transfer_completed',
    'warehouse',
    'Transfer Completed: {{transfer_code}}',
    'Warehouse transfer {{transfer_code}} completed. From: {{from_warehouse}} to {{to_warehouse}}. Items: {{item_count}}. Completed by: {{completed_by}}',
    'admin,warehouse_manager',
    NULL,
    'medium',
    1,
    NOW(),
    NOW()
);

-- Rule 6: Warehouse Stocktake Completed
INSERT INTO `notification_rules` (
    `event_name`, `module`, `title_template`, `message_template`, 
    `target_roles`, `target_users`, `priority`, `is_active`, 
    `created_at`, `updated_at`
) VALUES (
    'warehouse_stocktake_completed',
    'warehouse',
    'Stocktake Completed: {{warehouse_name}}',
    'Stocktake {{stocktake_code}} completed for {{warehouse_name}}. Items counted: {{items_counted}}, Discrepancies: {{discrepancies}}. Completed by: {{completed_by}}',
    'admin,warehouse_manager,finance_manager',
    NULL,
    'high',
    1,
    NOW(),
    NOW()
);

-- ============================================================================
-- CATEGORY 3: Operational Workflows (4 rules)
-- ============================================================================

-- Rule 7: Inspection Scheduled
INSERT INTO `notification_rules` (
    `event_name`, `module`, `title_template`, `message_template`, 
    `target_roles`, `target_users`, `priority`, `is_active`, 
    `created_at`, `updated_at`
) VALUES (
    'inspection_scheduled',
    'operations',
    'Inspection Scheduled: {{unit_code}}',
    'Unit {{unit_code}} inspection scheduled. Type: {{inspection_type}}, Date: {{scheduled_date}}, Assigned to: {{assigned_to}}, Priority: {{priority}}',
    'admin,operations_manager,mechanic_leader',
    NULL,
    'medium',
    1,
    NOW(),
    NOW()
);

-- Rule 8: Inspection Completed
INSERT INTO `notification_rules` (
    `event_name`, `module`, `title_template`, `message_template`, 
    `target_roles`, `target_users`, `priority`, `is_active`, 
    `created_at`, `updated_at`
) VALUES (
    'inspection_completed',
    'operations',
    'Inspection Completed: {{unit_code}}',
    'Unit {{unit_code}} inspection completed. Type: {{inspection_type}}, Result: {{result}}, Findings: {{findings_count}}. Completed by: {{completed_by}}',
    'admin,operations_manager,fleet_manager',
    NULL,
    'high',
    1,
    NOW(),
    NOW()
);

-- Rule 9: Maintenance Scheduled
INSERT INTO `notification_rules` (
    `event_name`, `module`, `title_template`, `message_template`, 
    `target_roles`, `target_users`, `priority`, `is_active`, 
    `created_at`, `updated_at`
) VALUES (
    'maintenance_scheduled',
    'operations',
    'Maintenance Scheduled: {{unit_code}}',
    'Unit {{unit_code}} maintenance scheduled. Type: {{maintenance_type}}, Date: {{scheduled_date}}, Estimated: {{estimated_hours}}h, Mechanic: {{assigned_mechanic}}, Priority: {{priority}}',
    'admin,operations_manager,mechanic_leader',
    NULL,
    'medium',
    1,
    NOW(),
    NOW()
);

-- Rule 10: Maintenance Completed
INSERT INTO `notification_rules` (
    `event_name`, `module`, `title_template`, `message_template`, 
    `target_roles`, `target_users`, `priority`, `is_active`, 
    `created_at`, `updated_at`
) VALUES (
    'maintenance_completed',
    'operations',
    'Maintenance Completed: {{unit_code}}',
    'Unit {{unit_code}} maintenance completed. Type: {{maintenance_type}}, Duration: {{actual_hours}}h, Parts replaced: {{parts_replaced}}, Cost: Rp {{total_cost}}. Completed by: {{completed_by}}',
    'admin,operations_manager,finance_manager',
    NULL,
    'high',
    1,
    NOW(),
    NOW()
);

-- ============================================================================
-- CATEGORY 4: Finance Extended (3 rules)
-- ============================================================================

-- Rule 11: Payment Received
INSERT INTO `notification_rules` (
    `event_name`, `module`, `title_template`, `message_template`, 
    `target_roles`, `target_users`, `priority`, `is_active`, 
    `created_at`, `updated_at`
) VALUES (
    'payment_received',
    'finance',
    'Payment Received: {{invoice_number}}',
    'Payment received for invoice {{invoice_number}}. Customer: {{customer_name}}, Amount: Rp {{amount}}, Method: {{payment_method}}. Received by: {{received_by}}',
    'admin,finance_manager,accounting',
    NULL,
    'medium',
    1,
    NOW(),
    NOW()
);

-- Rule 12: Payment Overdue
INSERT INTO `notification_rules` (
    `event_name`, `module`, `title_template`, `message_template`, 
    `target_roles`, `target_users`, `priority`, `is_active`, 
    `created_at`, `updated_at`
) VALUES (
    'payment_overdue',
    'finance',
    'OVERDUE Payment: {{invoice_number}}',
    'URGENT: Invoice {{invoice_number}} is overdue by {{days_overdue}} days. Customer: {{customer_name}}, Outstanding: Rp {{outstanding_balance}}, Due date: {{due_date}}',
    'admin,finance_manager,management,marketing_manager',
    NULL,
    'critical',
    1,
    NOW(),
    NOW()
);

-- Rule 13: Budget Threshold Exceeded
INSERT INTO `notification_rules` (
    `event_name`, `module`, `title_template`, `message_template`, 
    `target_roles`, `target_users`, `priority`, `is_active`, 
    `created_at`, `updated_at`
) VALUES (
    'budget_threshold_exceeded',
    'finance',
    'Budget Alert: {{budget_name}}',
    'WARNING: Budget "{{budget_name}}" ({{department}}) has exceeded {{threshold}}% threshold. Allocated: Rp {{allocated_amount}}, Spent: Rp {{spent_amount}} ({{percentage_used}}%)',
    'admin,finance_manager,management',
    NULL,
    'high',
    1,
    NOW(),
    NOW()
);

-- ============================================================================
-- CATEGORY 5: SPK Management (2 rules)
-- ============================================================================

-- Rule 14: SPK Created
INSERT INTO `notification_rules` (
    `event_name`, `module`, `title_template`, `message_template`, 
    `target_roles`, `target_users`, `priority`, `is_active`, 
    `created_at`, `updated_at`
) VALUES (
    'spk_created',
    'spk',
    'New SPK: {{spk_number}}',
    'New SPK {{spk_number}} created for unit {{unit_code}}. Work type: {{work_type}}, Assigned to: {{assigned_to}}, Target date: {{target_date}}, Priority: {{priority}}',
    'admin,operations_manager,mechanic_leader',
    NULL,
    'medium',
    1,
    NOW(),
    NOW()
);

-- Rule 15: SPK Completed
INSERT INTO `notification_rules` (
    `event_name`, `module`, `title_template`, `message_template`, 
    `target_roles`, `target_users`, `priority`, `is_active`, 
    `created_at`, `updated_at`
) VALUES (
    'spk_completed',
    'spk',
    'SPK Completed: {{spk_number}}',
    'SPK {{spk_number}} completed for unit {{unit_code}}. Work type: {{work_type}}, Duration: {{actual_duration}}h, Result: {{result}}. Completed by: {{completed_by}}',
    'admin,operations_manager,fleet_manager',
    NULL,
    'high',
    1,
    NOW(),
    NOW()
);

-- ============================================================================
-- CATEGORY 6: Additional Marketing (2 rules)
-- ============================================================================

-- Rule 16: Quotation Sent to Customer
INSERT INTO `notification_rules` (
    `event_name`, `module`, `title_template`, `message_template`, 
    `target_roles`, `target_users`, `priority`, `is_active`, 
    `created_at`, `updated_at`
) VALUES (
    'quotation_sent_to_customer',
    'marketing',
    'Quotation Sent: {{quote_number}}',
    'Quotation {{quote_number}} sent to {{customer_name}} via {{sent_method}}. Email: {{customer_email}}. Sent by: {{sent_by}}',
    'admin,marketing_manager,sales',
    NULL,
    'medium',
    1,
    NOW(),
    NOW()
);

-- Rule 17: Quotation Follow-up Required
INSERT INTO `notification_rules` (
    `event_name`, `module`, `title_template`, `message_template`, 
    `target_roles`, `target_users`, `priority`, `is_active`, 
    `created_at`, `updated_at`
) VALUES (
    'quotation_follow_up_required',
    'marketing',
    'Follow-up Required: {{quote_number}}',
    'Quotation {{quote_number}} for {{customer_name}} requires follow-up. Days since sent: {{days_since_sent}}, Last contact: {{last_contact}}, Priority: {{follow_up_priority}}. Assigned to: {{assigned_to}}',
    'admin,marketing_manager,sales',
    NULL,
    'medium',
    1,
    NOW(),
    NOW()
);

-- ============================================================================
-- Verification Query
-- ============================================================================

-- Count Phase 3 rules (should be 17)
SELECT 'Phase 3 Rules Count' as Description, COUNT(*) as Count
FROM `notification_rules`
WHERE `event_name` IN (
    'customer_created', 'customer_updated', 'customer_status_changed',
    'warehouse_stock_alert', 'warehouse_transfer_completed', 'warehouse_stocktake_completed',
    'inspection_scheduled', 'inspection_completed', 'maintenance_scheduled', 'maintenance_completed',
    'payment_received', 'payment_overdue', 'budget_threshold_exceeded',
    'spk_created', 'spk_completed',
    'quotation_sent_to_customer', 'quotation_follow_up_required'
);

-- Total notification rules count (should be 39: 8 Phase 1 + 14 Phase 2 + 17 Phase 3)
SELECT 'Total Rules Count' as Description, COUNT(*) as Count
FROM `notification_rules`;

-- List all Phase 3 rules
SELECT id, event_name, module, priority, is_active
FROM `notification_rules`
WHERE `event_name` IN (
    'customer_created', 'customer_updated', 'customer_status_changed',
    'warehouse_stock_alert', 'warehouse_transfer_completed', 'warehouse_stocktake_completed',
    'inspection_scheduled', 'inspection_completed', 'maintenance_scheduled', 'maintenance_completed',
    'payment_received', 'payment_overdue', 'budget_threshold_exceeded',
    'spk_created', 'spk_completed',
    'quotation_sent_to_customer', 'quotation_follow_up_required'
)
ORDER BY module, event_name;

-- ============================================================================
-- Migration Complete
-- Phase 3: 17 MEDIUM priority notification rules
-- Total System Coverage: 39 rules (31%)
-- ============================================================================
