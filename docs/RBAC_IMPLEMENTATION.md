# RBAC Implementation Documentation
## Role-Based Access Control System for OPTIMA

### Overview
This document describes the complete RBAC (Role-Based Access Control) implementation for the OPTIMA system. The RBAC system provides secure and scalable access control by organizing permissions through roles.

### RBAC Architecture

#### Core Components
1. **Users** - System users who need access to various functions
2. **Roles** - Collections of permissions that define what a user can do
3. **Permissions** - Specific actions or access rights in the system
4. **Divisions** - Organizational units that can restrict access to specific areas
5. **Custom Permissions** - User-specific permission overrides

#### Database Structure
```sql
-- Core Tables
users (id, username, email, first_name, last_name, ...)
roles (id, name, description, created_at, updated_at)
permissions (id, key, name, description, module, created_at, updated_at)
divisions (id, name, code, description)

-- Relationship Tables
user_roles (user_id, role_id, assigned_at, assigned_by)
role_permissions (role_id, permission_id, assigned_at, assigned_by)
user_permissions (user_id, permission_id, division_id, granted, assigned_at, assigned_by)
user_divisions (user_id, division_id, is_head, assigned_at, assigned_by)
```

### RBAC Flow

#### 1. Permission Assignment Flow
```
Create Permission → Assign to Role → Assign Role to User → User Access Granted
```

#### 2. Permission Check Priority
```
1. Custom Permission (Override) - Highest Priority
2. Role Permission - Default
3. Deny Access - Fallback
```

#### 3. Division-Based Access
```
User → Division Assignment → Division-Specific Permissions
```

### Menu Structure & Functions

#### Administration Menu
Each menu in the Administration section has a specific role in RBAC:

1. **User Management** (`/admin/advanced-users`)
   - **Function**: Assign roles, divisions, and custom permissions to users
   - **Purpose**: Central user administration - does NOT create roles or permissions
   - **Permissions**: `admin.user_management`, `admin.user_create`, `admin.user_edit`, `admin.user_delete`
   - **Key Features**:
     - Assign existing roles to users
     - Set user divisions and positions
     - Add custom permission overrides (when needed)
     - View effective user permissions

2. **Role Management** (`/admin/roles`)
   - **Function**: Create, edit, delete roles and assign permissions to roles
   - **Purpose**: Define role templates with specific permission sets
   - **Permissions**: `admin.role_management`, `admin.role_create`, `admin.role_edit`, `admin.role_delete`
   - **Key Features**:
     - Create roles (e.g., Manager, Staff, Admin)
     - Assign permissions to roles
     - View which users have which roles
     - Role templates for common positions

3. **Permission Management** (`/admin/permissions`)
   - **Function**: Create, edit, delete system permissions
   - **Purpose**: Define available actions and access rights in the system
   - **Permissions**: `admin.permission_management`, `admin.permission_create`, `admin.permission_edit`, `admin.permission_delete`
   - **Key Features**:
     - Create permissions (e.g., `service.work_orders.create`)
     - Organize permissions by module
     - View permission usage across roles
     - Permission naming conventions

### Permission Naming Convention

#### Format: `module.action` or `module.resource.action`

#### Examples:
```php
// Dashboard
'dashboard.access' => 'Access dashboard'
'dashboard.export' => 'Export dashboard data'

// Administration
'admin.access' => 'Access administration panel'
'admin.user_management' => 'Access user management'
'admin.role_management' => 'Access role management'
'admin.permission_management' => 'Access permission management'

// Service Division
'service.access' => 'Access service module'
'service.work_orders.view' => 'View work orders'
'service.work_orders.create' => 'Create work orders'
'service.work_orders.edit' => 'Edit work orders'
'service.work_orders.delete' => 'Delete work orders'

// Marketing Division
'marketing.access' => 'Access marketing module'
'marketing.customers.view' => 'View customers'
'marketing.rentals.manage' => 'Manage rentals'

// Warehouse Division
'warehouse.access' => 'Access warehouse module'
'warehouse.inventory.view' => 'View inventory'
'warehouse.units.manage' => 'Manage unit assets'
```

### Helper Functions

#### Core RBAC Helper: `rbac_helper.php`

```php
// Check if user can access specific permission
can_access('service.work_orders.create', $user_id)

// Check if user has specific role
user_has_role('Manager', $user_id)

// Check if user is in specific division
user_in_division('SVC', $user_id)

// Check if user is division head
is_division_head('SVC', $user_id)

// Get all user permissions
get_user_permissions($user_id)

// Get menu permissions for sidebar
get_user_menu_permissions($user_id)
```

### Implementation in Views

#### Sidebar Menu (base.php)
```php
<?php if (can_access('service.access')): ?>
<li class="nav-item">
    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse">
        <i class="fas fa-tools"></i>
        <span class="nav-link-text">Service</span>
    </a>
    <div class="collapse">
        <?php if (can_access('service.work_orders.view')): ?>
        <a href="<?= base_url('/service/work-orders') ?>">Work Orders</a>
        <?php endif; ?>
    </div>
</li>
<?php endif; ?>
```

#### Controller Permission Check
```php
public function index()
{
    if (!can_access('service.work_orders.view')) {
        return redirect()->to('/dashboard')->with('error', 'Access denied.');
    }
    // Controller logic
}
```

### Default Roles & Permissions

#### Recommended Role Structure:

1. **Super Administrator**
   - All permissions
   - System configuration access
   - User/role/permission management

2. **Management**
   - Cross-division access
   - Reporting and analytics
   - User management (limited)

3. **Division Head**
   - Full access within assigned division
   - Team management
   - Division reporting

4. **Staff**
   - Limited operational access
   - Division-specific functions
   - Basic reporting

### Best Practices

#### 1. Permission Design
- Use descriptive permission keys
- Follow module.action naming convention
- Group related permissions by module
- Keep permissions granular but not overly complex

#### 2. Role Design
- Create roles based on job functions
- Use role templates for common positions
- Avoid creating too many similar roles
- Document role purposes clearly

#### 3. User Management
- Assign roles first, custom permissions only when needed
- Custom permissions should be exceptions, not the rule
- Document why custom permissions were assigned
- Regular audit of user permissions

#### 4. Division Management
- Use divisions to limit data access scope
- Division heads should have management permissions within their division
- Cross-division access should be role-based, not division-based

### Security Considerations

1. **Permission Caching**: Cache permission checks for performance
2. **Session Management**: Clear permissions on role changes
3. **Audit Logging**: Log all permission changes
4. **Regular Reviews**: Periodic permission audits
5. **Principle of Least Privilege**: Grant minimum necessary permissions

### Migration & Setup

#### Initial Setup Commands:
```php
// 1. Create basic permissions
php spark create:permissions

// 2. Create default roles
php spark create:roles

// 3. Create super admin user
php spark create:superadmin

// 4. Assign default permissions to roles
php spark setup:rbac
```

### Troubleshooting

#### Common Issues:
1. **User can't access menu**: Check role assignment and permission existence
2. **Permission not working**: Verify permission key spelling and module
3. **Custom permission not overriding**: Check custom permission granted flag
4. **Division access issues**: Verify user_divisions table entries

#### Debug Functions:
```php
// Debug user permissions
dd(get_user_permissions($user_id));

// Debug menu permissions
dd(get_user_menu_permissions($user_id));

// Check specific permission
var_dump(can_access('service.access', $user_id));
```

### Conclusion

This RBAC implementation provides:
- **Scalable** permission management
- **Flexible** role-based access
- **Secure** by default approach
- **Maintainable** code structure
- **Auditable** permission changes

The three-tier approach (User Management, Role Management, Permission Management) ensures clear separation of concerns and makes the system easy to understand and maintain.
