-- ============================================================================
-- Phase 2: HIGH Priority Notification Rules
-- Created: 2025-01-XX
-- Description: Adds 14 notification rules for HIGH priority business operations
--              Marketing/Quotation (4), WorkOrder Extended (4), Service Assignments (3),
--              Security/Admin (3)
-- ============================================================================

-- 1. MARKETING / QUOTATION NOTIFICATIONS (4 rules)
-- ============================================================================

-- Rule 1: Quotation Created
INSERT INTO notification_rules (trigger_event, target_divisions, target_roles, title_template, message_template, is_active)
VALUES (
    'quotation_created',
    'marketing,management',
    'admin,marketing_manager,sales_manager',
    'Quotation Baru: {{quotation_number}}',
    'Quotation baru telah dibuat untuk customer {{customer_name}} dengan nilai {{total_value}}. Stage: {{stage}}. Dibuat oleh: {{created_by}}',
    1
);

-- Rule 2: Quotation Stage Changed
INSERT INTO notification_rules (trigger_event, target_divisions, target_roles, title_template, message_template, is_active)
VALUES (
    'quotation_stage_changed',
    'marketing,management',
    'admin,marketing_manager,sales_manager',
    'Stage Quotation Berubah: {{quotation_number}}',
    'Stage quotation untuk {{customer_name}} berubah dari {{old_stage}} menjadi {{new_stage}}. Diupdate oleh: {{updated_by}}',
    1
);

-- Rule 3: Contract Completed
INSERT INTO notification_rules (trigger_event, target_divisions, target_roles, title_template, message_template, is_active)
VALUES (
    'contract_completed',
    'marketing,finance,management',
    'admin,marketing_manager,finance_manager,director',
    'Kontrak Selesai: {{contract_number}}',
    'Kontrak dengan {{customer_name}} telah diselesaikan. Nilai total: {{total_value}}. Diselesaikan oleh: {{completed_by}}',
    1
);

-- Rule 4: PO Created from Quotation
INSERT INTO notification_rules (trigger_event, target_divisions, target_roles, title_template, message_template, is_active)
VALUES (
    'po_created_from_quotation',
    'purchasing,marketing,management',
    'admin,purchasing_manager,marketing_manager',
    'PO Dibuat dari Quotation: {{quotation_number}}',
    'Purchase Order {{po_number}} telah dibuat dari quotation {{quotation_number}} untuk {{customer_name}}. Dibuat oleh: {{created_by}}',
    1
);

-- 2. WORKORDER EXTENDED NOTIFICATIONS (4 rules)
-- ============================================================================

-- Rule 5: WorkOrder TTR Updated
INSERT INTO notification_rules (trigger_event, target_divisions, target_roles, title_template, message_template, is_active)
VALUES (
    'workorder_ttr_updated',
    'service,management',
    'admin,service_manager,supervisor',
    'TTR Update: WO {{wo_number}}',
    'Time To Repair untuk unit {{unit_code}} telah diupdate menjadi {{ttr_hours}} jam. WO: {{wo_number}}. Diupdate oleh: {{updated_by}}',
    1
);

-- Rule 6: Unit Verification Saved
INSERT INTO notification_rules (trigger_event, target_divisions, target_roles, title_template, message_template, is_active)
VALUES (
    'unit_verification_saved',
    'service,warehouse,management',
    'admin,service_manager,warehouse_manager,supervisor',
    'Unit Terverifikasi: {{unit_code}}',
    'Unit {{unit_code}} telah diverifikasi untuk WO {{wo_number}}. Status: {{verification_status}}. Diverifikasi oleh: {{verified_by}}',
    1
);

-- Rule 7: Sparepart Validation Saved
INSERT INTO notification_rules (trigger_event, target_divisions, target_roles, title_template, message_template, is_active)
VALUES (
    'sparepart_validation_saved',
    'service,warehouse,management',
    'admin,service_manager,warehouse_manager',
    'Sparepart Divalidasi: WO {{wo_number}}',
    'Validasi sparepart untuk WO {{wo_number}} telah selesai. Total sparepart: {{sparepart_count}}. Divalidasi oleh: {{validated_by}}',
    1
);

-- Rule 8: Sparepart Used/Consumed
INSERT INTO notification_rules (trigger_event, target_divisions, target_roles, title_template, message_template, is_active)
VALUES (
    'sparepart_used',
    'service,warehouse,management',
    'admin,service_manager,warehouse_manager',
    'Sparepart Digunakan: {{sparepart_name}}',
    'Sparepart {{sparepart_name}} ({{quantity}} pcs) telah digunakan untuk unit {{unit_code}} di WO {{wo_number}}. Digunakan oleh: {{used_by}}',
    1
);

-- 3. SERVICE ASSIGNMENT NOTIFICATIONS (3 rules)
-- ============================================================================

-- Rule 9: Service Assignment Created
INSERT INTO notification_rules (trigger_event, target_divisions, target_roles, title_template, message_template, is_active)
VALUES (
    'service_assignment_created',
    'service,hr,management',
    'admin,service_manager,hr_manager',
    'Assignment Baru: {{employee_name}}',
    'Employee {{employee_name}} telah ditugaskan ke area {{area_name}} dengan role {{role}}. Mulai: {{start_date}}. Dibuat oleh: {{created_by}}',
    1
);

-- Rule 10: Service Assignment Updated
INSERT INTO notification_rules (trigger_event, target_divisions, target_roles, title_template, message_template, is_active)
VALUES (
    'service_assignment_updated',
    'service,hr,management',
    'admin,service_manager,hr_manager',
    'Assignment Diupdate: {{employee_name}}',
    'Assignment {{employee_name}} di area {{area_name}} telah diupdate. Perubahan: {{changes}}. Diupdate oleh: {{updated_by}}',
    1
);

-- Rule 11: Service Assignment Deleted
INSERT INTO notification_rules (trigger_event, target_divisions, target_roles, title_template, message_template, is_active)
VALUES (
    'service_assignment_deleted',
    'service,hr,management',
    'admin,service_manager,hr_manager',
    'Assignment Dihapus: {{employee_name}}',
    'Assignment {{employee_name}} di area {{area_name}} telah dihapus. Dihapus oleh: {{deleted_by}}',
    1
);

-- 4. SECURITY / ADMIN NOTIFICATIONS (3 rules for critical security events)
-- ============================================================================

-- Rule 12: User Removed from Division
INSERT INTO notification_rules (trigger_event, target_divisions, target_roles, title_template, message_template, is_active)
VALUES (
    'user_removed_from_division',
    'admin,it,management',
    'admin,super_admin,it_manager',
    '⚠️ User Dihapus dari Divisi: {{user_name}}',
    'User {{user_name}} telah dihapus dari divisi {{division_name}}. Dihapus oleh: {{removed_by}}. Segera lakukan review akses.',
    1
);

-- Rule 13: User Permissions Updated
INSERT INTO notification_rules (trigger_event, target_divisions, target_roles, title_template, message_template, is_active)
VALUES (
    'user_permissions_updated',
    'admin,it,management',
    'admin,super_admin,it_manager',
    '⚠️ Permission Diubah: {{user_name}}',
    'Custom permission user {{user_name}} telah diubah. Perubahan: {{permissions_changed}}. Diubah oleh: {{updated_by}}. Lakukan audit.',
    1
);

-- Rule 14: Permission Created
INSERT INTO notification_rules (trigger_event, target_divisions, target_roles, title_template, message_template, is_active)
VALUES (
    'permission_created',
    'admin,it,management',
    'admin,super_admin',
    '🔐 Permission Baru Dibuat: {{permission_name}}',
    'Permission baru {{permission_name}} ({{permission_code}}) telah dibuat untuk module {{module_name}}. Dibuat oleh: {{created_by}}',
    1
);

-- ============================================================================
-- Verification Query
-- ============================================================================
SELECT 
    id,
    trigger_event,
    target_divisions,
    target_roles,
    title_template,
    is_active,
    created_at
FROM notification_rules
WHERE trigger_event IN (
    'quotation_created',
    'quotation_stage_changed',
    'contract_completed',
    'po_created_from_quotation',
    'workorder_ttr_updated',
    'unit_verification_saved',
    'sparepart_validation_saved',
    'sparepart_used',
    'service_assignment_created',
    'service_assignment_updated',
    'service_assignment_deleted',
    'user_removed_from_division',
    'user_permissions_updated',
    'permission_created'
)
ORDER BY created_at DESC;

-- ============================================================================
-- STATISTICS
-- ============================================================================
-- Total Rules Added in Phase 2: 14
-- Categories:
--   - Marketing/Quotation: 4 rules
--   - WorkOrder Extended: 4 rules
--   - Service Assignments: 3 rules
--   - Security/Admin: 3 rules
--
-- Phase 2 Coverage: 11 functions implemented out of 22 planned HIGH priority
-- Combined Coverage (Phase 1 + Phase 2 Partial): 20 out of 126 total = 15.9%
-- ============================================================================
