-- Setup Basic RBAC Permissions for Optima System
-- Run this SQL to populate basic permissions and roles

-- Hapus data lama jika ada
DELETE FROM role_permissions;
DELETE FROM user_roles WHERE role_id > 1; -- Keep admin role assignments
DELETE FROM roles WHERE id > 1; -- Keep admin role
DELETE FROM permissions;

-- Reset auto increment
ALTER TABLE permissions AUTO_INCREMENT = 1;
ALTER TABLE roles AUTO_INCREMENT = 2;

-- Insert Basic Permissions
INSERT INTO permissions (display_name, key_name, module, page, action, description, is_active) VALUES

-- Admin Module Permissions
('View Admin Dashboard', 'admin.dashboard.view', 'admin', 'dashboard', 'view', 'Access to admin dashboard', 1),
('Access Admin Settings', 'admin.settings.access', 'admin', 'settings', 'access', 'Access admin settings', 1),
('Manage Admin Configuration', 'admin.configuration.manage', 'admin', 'configuration', 'manage', 'Manage system configuration', 1),

-- User Management Permissions
('View Users', 'admin.users.view', 'admin', 'users', 'view', 'View user list and details', 1),
('Create Users', 'admin.users.create', 'admin', 'users', 'create', 'Create new users', 1),
('Edit Users', 'admin.users.edit', 'admin', 'users', 'edit', 'Edit user information', 1),
('Delete Users', 'admin.users.delete', 'admin', 'users', 'delete', 'Delete users', 1),

-- Role Management Permissions
('View Roles', 'admin.roles.view', 'admin', 'roles', 'view', 'View role list and details', 1),
('Manage Roles', 'admin.role_management', 'admin', 'roles', 'manage', 'Create, edit, delete roles', 1),

-- Permission Management Permissions
('View Permissions', 'admin.permissions.view', 'admin', 'permissions', 'view', 'View permission list', 1),
('Manage Permissions', 'admin.permission_management', 'admin', 'permissions', 'manage', 'Manage system permissions', 1),
('Create Permissions', 'admin.permission_create', 'admin', 'permissions', 'create', 'Create new permissions', 1),
('Edit Permissions', 'admin.permission_edit', 'admin', 'permissions', 'edit', 'Edit permissions', 1),
('Delete Permissions', 'admin.permission_delete', 'admin', 'permissions', 'delete', 'Delete permissions', 1),

-- Marketing Module Permissions
('Access Marketing', 'marketing.access', 'marketing', 'dashboard', 'access', 'Access marketing module', 1),
('View Marketing Dashboard', 'marketing.dashboard.view', 'marketing', 'dashboard', 'view', 'View marketing dashboard', 1),
('Manage Marketing Campaigns', 'marketing.campaigns.manage', 'marketing', 'campaigns', 'manage', 'Manage marketing campaigns', 1),

-- Service Module Permissions  
('Access Service', 'service.access', 'service', 'dashboard', 'access', 'Access service module', 1),
('View Service Dashboard', 'service.dashboard.view', 'service', 'dashboard', 'view', 'View service dashboard', 1),
('View Workorders', 'service.workorders.view', 'service', 'workorders', 'view', 'View service workorders', 1),
('Create Workorders', 'service.workorders.create', 'service', 'workorders', 'create', 'Create service workorders', 1),
('Edit Workorders', 'service.workorders.edit', 'service', 'workorders', 'edit', 'Edit service workorders', 1),

-- Warehouse Module Permissions
('Access Warehouse', 'warehouse.access', 'warehouse', 'dashboard', 'access', 'Access warehouse module', 1),
('View Warehouse Dashboard', 'warehouse.dashboard.view', 'warehouse', 'dashboard', 'view', 'View warehouse dashboard', 1),
('Manage Inventory', 'warehouse.inventory.manage', 'warehouse', 'inventory', 'manage', 'Manage warehouse inventory', 1),

-- Purchasing Module Permissions
('Access Purchasing', 'purchasing.access', 'purchasing', 'dashboard', 'access', 'Access purchasing module', 1),
('View Purchase Orders', 'purchasing.orders.view', 'purchasing', 'orders', 'view', 'View purchase orders', 1),
('Create Purchase Orders', 'purchasing.orders.create', 'purchasing', 'orders', 'create', 'Create purchase orders', 1),

-- Accounting Module Permissions
('Access Accounting', 'accounting.access', 'accounting', 'dashboard', 'access', 'Access accounting module', 1),
('View Financial Reports', 'accounting.reports.view', 'accounting', 'reports', 'view', 'View financial reports', 1),
('Manage Accounting', 'accounting.manage', 'accounting', 'dashboard', 'manage', 'Full accounting management', 1),

-- Perizinan Module Permissions
('Access Permits', 'perizinan.access', 'perizinan', 'dashboard', 'access', 'Access permits module', 1),
('Manage SILO', 'perizinan.silo.manage', 'perizinan', 'silo', 'manage', 'Manage SILO permits', 1),
('Manage EMISI', 'perizinan.emisi.manage', 'perizinan', 'emisi', 'manage', 'Manage emission permits', 1);

-- Insert Basic Roles
INSERT INTO roles (name, description, is_active) VALUES
('Marketing Manager', 'Full access to marketing module', 1),
('Service Manager', 'Full access to service module', 1),
('Service Technician', 'Limited service access for technicians', 1),
('Warehouse Manager', 'Full access to warehouse module', 1),
('Warehouse Staff', 'Limited warehouse access', 1),
('Purchasing Manager', 'Full access to purchasing module', 1),
('Accounting Manager', 'Full access to accounting module', 1),
('Permit Officer', 'Manage permits and compliance', 1);

-- Get role IDs (assuming they start from 2 since admin is 1)
SET @marketing_role = (SELECT id FROM roles WHERE name = 'Marketing Manager');
SET @service_mgr_role = (SELECT id FROM roles WHERE name = 'Service Manager');  
SET @service_tech_role = (SELECT id FROM roles WHERE name = 'Service Technician');
SET @warehouse_mgr_role = (SELECT id FROM roles WHERE name = 'Warehouse Manager');
SET @warehouse_staff_role = (SELECT id FROM roles WHERE name = 'Warehouse Staff');
SET @purchasing_role = (SELECT id FROM roles WHERE name = 'Purchasing Manager');
SET @accounting_role = (SELECT id FROM roles WHERE name = 'Accounting Manager');
SET @permit_role = (SELECT id FROM roles WHERE name = 'Permit Officer');

-- Assign permissions to Marketing Manager
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_by, assigned_at, created_at, updated_at)
SELECT @marketing_role, id, 1, 1, NOW(), NOW(), NOW() 
FROM permissions 
WHERE module = 'marketing';

-- Assign permissions to Service Manager
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_by, assigned_at, created_at, updated_at)
SELECT @service_mgr_role, id, 1, 1, NOW(), NOW(), NOW() 
FROM permissions 
WHERE module = 'service';

-- Assign limited permissions to Service Technician (view and edit workorders only)
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_by, assigned_at, created_at, updated_at)
SELECT @service_tech_role, id, 1, 1, NOW(), NOW(), NOW() 
FROM permissions 
WHERE key_name IN ('service.access', 'service.dashboard.view', 'service.workorders.view', 'service.workorders.edit');

-- Assign permissions to Warehouse Manager
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_by, assigned_at, created_at, updated_at)
SELECT @warehouse_mgr_role, id, 1, 1, NOW(), NOW(), NOW() 
FROM permissions 
WHERE module = 'warehouse';

-- Assign limited permissions to Warehouse Staff (access and view only)
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_by, assigned_at, created_at, updated_at)
SELECT @warehouse_staff_role, id, 1, 1, NOW(), NOW(), NOW() 
FROM permissions 
WHERE key_name IN ('warehouse.access', 'warehouse.dashboard.view');

-- Assign permissions to Purchasing Manager
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_by, assigned_at, created_at, updated_at)
SELECT @purchasing_role, id, 1, 1, NOW(), NOW(), NOW() 
FROM permissions 
WHERE module = 'purchasing';

-- Assign permissions to Accounting Manager  
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_by, assigned_at, created_at, updated_at)
SELECT @accounting_role, id, 1, 1, NOW(), NOW(), NOW() 
FROM permissions 
WHERE module = 'accounting';

-- Assign permissions to Permit Officer
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_by, assigned_at, created_at, updated_at)
SELECT @permit_role, id, 1, 1, NOW(), NOW(), NOW() 
FROM permissions 
WHERE module = 'perizinan';

-- Give admin role all permissions (assuming admin role has id = 1)
INSERT INTO role_permissions (role_id, permission_id, granted, assigned_by, assigned_at, created_at, updated_at)
SELECT 1, id, 1, 1, NOW(), NOW(), NOW() 
FROM permissions;

-- Show summary
SELECT 'Permissions Created' as Item, COUNT(*) as Count FROM permissions
UNION ALL
SELECT 'Roles Created', COUNT(*) FROM roles WHERE id > 1
UNION ALL  
SELECT 'Role-Permission Assignments', COUNT(*) FROM role_permissions;

SELECT 'Setup completed successfully!' as Status;