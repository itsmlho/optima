# 🎯 Panduan Lengkap: Comprehensive Permission Management System

## 📚 Daftar Isi
1. [Overview](#overview)
2. [Akses Permission Management](#akses-permission-management)
3. [Role Permission Assignment](#role-permission-assignment)
4. [User Custom Permissions](#user-custom-permissions)
5. [Permission Priority](#permission-priority)
6. [Use Cases & Examples](#use-cases--examples)
7. [Troubleshooting](#troubleshooting)

---

## Overview

Sistem permission Optima menggunakan **3-tier model**:
```
Admin Bypass → User Permission → Role Permission → Default DENY
```

### Statistik Permissions
- **Total Modules**: 10 (Dashboard, Marketing, Service, Warehouse, Purchasing, Finance, Operational, Reports, Settings, Activity)
- **Total Permissions**: **348 permissions**
- **Format**: `module.page.action` (contoh: `marketing.customer.edit`)

### Instalasi Selesai ✅
- ✅ Database: 348 permissions telah ditambahkan
- ✅ Backend: Controller `PermissionManagement` siap
- ✅ Frontend: 2 UI interfaces (Role & User Permissions)
- ✅ Routes: Permission filters aktif

---

## Akses Permission Management

### 1. **Role Permission Assignment**
**URL**: `http://your-domain.com/permission-management/role-permissions`

**Required Permission**: `settings.role.assign_permission`

**Fungsi**: Assign permissions ke roles (dilakukan sekali untuk semua user dengan role tersebut)

### 2. **User Custom Permissions**
**URL**: `http://your-domain.com/permission-management/user-permissions`

**Required Permission**: `settings.user.assign_permission`

**Fungsi**: Assign custom permissions ke individual users (override role permissions)

---

## Role Permission Assignment

### Cara Menggunakan

#### Step 1: Select Role
1. Buka `/permission-management/role-permissions`
2. Pilih role dari dropdown (contoh: "Head Marketing")
3. Sistem akan load semua permissions yang bisa assigned

#### Step 2: Assign Permissions
**Grouped by Module & Page**:
```
Marketing
├── Customer
│   ├── ☐ navigation (Menu Customer)
│   ├── ☐ view (Lihat list customer)
│   ├── ☐ create (Tambah customer)
│   ├── ☐ edit (Edit customer)
│   ├── ☐ delete (Hapus customer)
│   └── ☐ export (Export customer data)
└── Quotation
    ├── ☐ navigation
    ├── ☐ view
    ├── ☐ create
    └── ...
```

**Badge Colors**:
- 🔵 **navigation** - Menu access (primary)
- 🔵 **view** - Read data (info)
- 🟢 **create** - Add new (success)
- 🟡 **edit** - Update (warning)
- 🔴 **delete** - Remove (danger)
- ⚫ **export/print** - Download/Print (secondary)

#### Step 3: Save
1. Centang permissions yang ingin diberikan
2. Gunakan "Select All" atau "Deselect All" untuk bulk operations
3. Klik **"Save Permissions"**
4. Konfirmasi jumlah permissions yang akan di-assign

### Best Practices

**Role Marketing Quotation**:
```
✅ marketing.customer.navigation
✅ marketing.customer.view
✅ marketing.customer.create
✅ marketing.customer.edit
❌ marketing.customer.delete (hanya Head Marketing)
✅ marketing.quotation.navigation
✅ marketing.quotation.view
✅ marketing.quotation.create
✅ marketing.quotation.edit
❌ marketing.quotation.approve (hanya Head Marketing)
```

**Role Head Marketing**:
```
✅ ALL marketing.* permissions
✅ marketing.audit_approval.* (approve audit results)
✅ reports.contract.* (view contract reports)
```

---

## User Custom Permissions

### Cara Menggunakan

#### Step 1: Select User
1. Buka `/permission-management/user-permissions`
2. Pilih user dari dropdown
3. Sistem show:
   - User role (inherited permissions)
   - User overrides (custom grants/denials)
   - Effective permissions (actual permissions active)

#### Step 2: View Permission Status

**Permission Status Icons**:
- ✅ **Green Check** - User has permission (granted via override OR role)
- ❌ **Red X** - User explicitly denied (even if role has it)
- 🛡️ **Blue Shield** - Inherited from role
- 🚫 **Gray Ban** - No access

**Override Indicators**:
- 🟢 **USER GRANT** - User-specific grant (override role)
- 🔴 **USER DENY** - User-specific denial (revoke even if role has it)
- 🔵 **FROM ROLE** - Inherited from role (no override)
- ⚫ **NO ACCESS** - No permission from role or user

#### Step 3: Grant/Deny Permissions

**Grant Permission**:
1. Klik tombol **"Grant"** pada permission yang ingin diberikan
2. Input reason (opsional): "Temporary access for Q1 reporting"
3. Klik "Grant"
4. Permission akan di-override menjadi GRANTED

**Deny Permission**:
1. Klik tombol **"Deny"** pada permission
2. Input reason (REQUIRED): "Data security policy - read-only access only"
3. Klik "Deny"
4. Permission akan di-override menjadi DENIED (meskipun role punya)

**Clear Override**:
1. Klik tombol **"Clear"** pada permission yang sudah di-override
2. Konfirmasi removal
3. User akan fallback ke role permissions

#### Step 4: Quick Grant (Bulk Operation)

1. Klik **"Quick Grant"** button
2. Select multiple permissions yang ingin di-grant
3. Input reason (opsional)
4. Set expiration date (opsional untuk temporary access)
5. Klik **"Grant Permissions"**

### Use Case: Temporary Cross-Module Access

**Scenario**: Admin Marketing perlu akses Warehouse Inventory untuk 1 minggu

```
User: admin_marketing@company.com
Role: Staff Marketing (default hanya marketing.*)

Custom Permissions:
✅ warehouse.inventory_unit.navigation (GRANT - "Cross-module coordination")
✅ warehouse.inventory_unit.view (GRANT - "Read-only access for stock monitoring")
✅ warehouse.inventory_unit.print (GRANT - "Print reports for marketing campaign")
❌ warehouse.inventory_unit.edit (DENY - "Read-only user")
❌ warehouse.inventory_unit.delete (DENY - "Read-only user")
❌ warehouse.inventory_unit.export (DENY - "Data security")

Expiration: 2026-03-14 23:59:59 (7 days from now)
```

**Result**:
- Menu "Warehouse → Inventory Unit" muncul di sidebar ✅
- Halaman inventory bisa dibuka ✅
- Tombol "Print" aktif ✅
- Tombol "Tambah/Edit/Hapus" disabled dengan tooltip ⚪
- Tombol "Export" hidden ❌
- Setelah 7 hari, custom permissions expired → fallback ke role permission (tidak ada akses warehouse)

---

## Permission Priority

### Priority Order (Highest to Lowest)

```
1. ADMIN BYPASS
   → Admin/Superadmin bypass ALL checks
   → Role: admin, superadministrator, super_admin

2. USER PERMISSION: DENY (granted = 0)
   → Explicit denial, overrides everything
   → Even if role has permission, user CANNOT access

3. USER PERMISSION: GRANT (granted = 1)
   → Explicit grant, overrides role
   → User CAN access even if role doesn't have permission

4. ROLE PERMISSION: GRANT
   → Default behavior from role
   → User inherits all role permissions

5. DEFAULT DENY
   → No permission found
   → Access DENIED
```

### Examples

#### Example 1: User Override Grant
```
User: john@company.com
Role: Staff Marketing
- Role permissions: marketing.* (no warehouse access)

User Override:
+ warehouse.inventory_unit.view (granted = 1)

Result:
✅ Can view warehouse inventory (override)
✅ Marketing permissions work normal (from role)
```

#### Example 2: User Override Deny
```
User: jane@company.com
Role: Head Marketing
- Role permissions: marketing.* (including delete)

User Override:
+ marketing.customer.delete (granted = 0) ← DENY

Result:
❌ CANNOT delete customers (denied by override)
✅ Other marketing permissions work normal (from role)
```

#### Example 3: No Override (Inherit from Role)
```
User: mike@company.com
Role: Staff Service
- Role permissions: service.*

User Override: (none)

Result:
✅ Service permissions work (from role)
❌ No access to marketing/warehouse/etc (default deny)
```

---

## Use Cases & Examples

### Use Case 1: Temporary Cross-Functional Access

**Scenario**: Marketing staff perlu access warehouse untuk campaign product launch

**Solution**:
```sql
-- Grant temporary permissions (expires in 30 days)
warehouse.inventory_unit.navigation → GRANT (expires 2026-04-07)
warehouse.inventory_unit.view       → GRANT (expires 2026-04-07)
warehouse.inventory_unit.print      → GRANT (expires 2026-04-07)
```

**UI Steps**:
1. Permission Management → User Permissions
2. Select user
3. Grant permissions dengan expiration date
4. After 30 days, auto-revoked

---

### Use Case 2: Read-Only Access Override

**Scenario**: Finance HEAD perlu access marketing data (read-only)

**Solution**:
```sql
marketing.customer.navigation → GRANT
marketing.customer.view       → GRANT
marketing.quotation.view      → GRANT
marketing.contract.view       → GRANT

marketing.customer.edit       → DENY (ensure read-only)
marketing.customer.delete     → DENY
marketing.quotation.approve   → DENY
```

**Behavior**:
- ✅ Menu Marketing muncul
- ✅ Bisa view data customers, quotations, contracts
- ❌ Tombol Edit/Delete/Approve DISABLED dengan tooltip

---

### Use Case 3: Restricted Admin (No Delete Permission)

**Scenario**: Admin Junior tidak boleh delete data critical

**Solution**:
```sql
-- User already has role "Administrator" (all permissions)
-- Override specific actions:
marketing.customer.delete  → DENY
finance.invoice.delete     → DENY
purchasing.unit.delete     → DENY
warehouse.inventory_unit.delete → DENY
```

**Behavior**:
- ✅ All admin features work
- ❌ Delete buttons DISABLED di seluruh aplikasi
- Priority: DENY override > Role grant

---

## Troubleshooting

### Q: Permission tidak apply setelah save?
**A**: 
1. Check session - user harus logout & login ulang untuk refresh session cache
2. Verify di audit log bahwa permission sudah tersimpan
3. Check `user_permissions` table untuk confirm record exists

### Q: Tombol disabled meskipun sudah grant permission?
**A**:
1. Check effective permission di User Permission page
2. Pastikan tidak ada DENY override
3. Check permission key name exact match (case-sensitive)

### Q: Menu tidak muncul di sidebar?
**A**:
1. Ensure `module.page.navigation` permission granted
2. Check sidebar code menggunakan `canNavigateTo(module, page)`
3. Clear browser cache

### Q: Admin bypassed permission?
**A**:
- Normal! Admin role memang bypass all permission checks
- Untuk testing, gunakan user dengan role non-admin

### Q: Expired permission masih aktif?
**A**:
- Run cleanup job: `php spark permission:cleanup-expired` (jika sudah setup cron)
- Manual cleanup via SQL: `DELETE FROM user_permissions WHERE expires_at < NOW()`

---

## API Reference (Untuk Developer)

### PHP Helpers

```php
// Check permission
if (hasPermission('marketing.customer.edit', $userId)) {
    // User can edit customer
}

// Grant permission
grantUserPermission($userId, 'warehouse.inventory_unit.view', true, [
    'reason' => 'Temporary cross-module access',
    'expires_at' => '2026-04-01 23:59:59'
]);

// Revoke permission (explicit deny)
revokeUserPermission($userId, 'marketing.customer.delete', 'Security policy');

// Clear override (fallback to role)
clearUserPermission($userId, 'warehouse.inventory_unit.view');

// Get permission source (debugging)
$source = getPermissionSource($userId, 'marketing.customer.edit');
// Returns: ['has_permission' => true, 'source' => 'role_grant', 'details' => [...]]
```

### View Helpers (UI Components)

```php
<!-- Navigation check -->
<?php if (canNavigateTo('marketing', 'customer')): ?>
    <li><a href="/marketing/customers">Customers</a></li>
<?php endif; ?>

<!-- Action buttons -->
<?= renderEditButton('marketing.customer.edit', 'editCustomer(123)') ?>
<?= renderDeleteButton('marketing.customer.delete', 'deleteCustomer(123)') ?>
<?= renderPrintButton('marketing.customer.print', 'printCustomer(123)') ?>
<?= renderExportButton('marketing.customer.export', 'exportCustomers()') ?>
```

---

## Summary

✅ **348 permissions** added to database
✅ **Role Permission Assignment** UI ready
✅ **User Custom Permission** UI ready
✅ **3-tier permission model** implemented
✅ **Permission helpers** available (grant, revoke, check)
✅ **Action button helpers** available (auto show/hide based on permission)
✅ **Audit trail** available
✅ **Temporary permissions** supported (with expiration)

**Next Steps**:
1. Assign permissions ke roles yang ada (via Role Permission UI)
2. Test dengan user non-admin
3. Setup cron job untuk cleanup expired permissions (opsional)
4. Update existing views untuk gunakan permission-aware components

---

**File Referensi**:
- Controller: `/app/Controllers/PermissionManagement.php`
- Helpers: `/app/Helpers/permission_helper.php`, `/app/Helpers/user_permission_helper.php`
- Views: `/app/Views/settings/permissions/`
- Migration: `/databases/migrations/2026-03-07_add_all_permissions_comprehensive.sql`
- Mapping: `/databases/migrations/PERMISSION_MAPPING_COMPREHENSIVE.md`
