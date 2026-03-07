# 🎯 User-Specific Permission: Admin Marketing View Warehouse Inventory

## 📋 Scenario

**Role Default**: Admin Marketing
- ✅ Full access module Marketing
- ❌ No access module Warehouse

**Custom User Permission Grant**:
- ✅ Warehouse → Inventory Unit → **View Only**
- ✅ Warehouse → Inventory Unit → **Print**
- ❌ Warehouse → Inventory Unit → Create/Edit/Delete/Export

---

## 🔐 Permission Architecture

### 3-Tier Permission Model

```
┌─────────────────────────────────────────────────────┐
│ PERMISSION PRIORITY (Top = Highest)                 │
├─────────────────────────────────────────────────────┤
│ 1. ADMIN BYPASS                                     │
│    → Admin/Superadmin bypass ALL permission checks  │
├─────────────────────────────────────────────────────┤
│ 2. USER-SPECIFIC PERMISSION (user_permissions)      │
│    → granted = 1 (ALLOW, override role)             │
│    → granted = 0 (DENY, even if role has it)        │
├─────────────────────────────────────────────────────┤
│ 3. ROLE PERMISSION (role_permissions)               │
│    → Default behavior dari role user                │
├─────────────────────────────────────────────────────┤
│ 4. DEFAULT DENY                                     │
│    → Jika tidak ada permission, akses ditolak       │
└─────────────────────────────────────────────────────┘
```

### Permission Key Format

```
module.page.action

Contoh:
warehouse.inventory_unit.navigation  → Menu sidebar
warehouse.inventory_unit.view        → Halaman bisa dibuka
warehouse.inventory_unit.create      → Tombol "Tambah"
warehouse.inventory_unit.edit        → Tombol "Edit"
warehouse.inventory_unit.delete      → Tombol "Hapus"
warehouse.inventory_unit.print       → Tombol "Print"
warehouse.inventory_unit.export      → Tombol "Export"
```

---

## 🎨 UI Behavior dengan Permission

### Granted Permissions (✅ User bisa akses)

| Permission | UI Element | Behavior |
|-----------|-----------|----------|
| `navigation` | Sidebar Menu Item | ✅ **Menu MUNCUL** di sidebar |
| `view` | Halaman/DataTable | ✅ **Halaman bisa dibuka**, data tampil |
| `print` | Tombol Print | ✅ **Tombol AKTIF**, bisa diklik |

**Contoh Implementasi:**
```php
<!-- Sidebar Menu -->
<?php if (canNavigateTo('warehouse', 'inventory_unit')): ?>
    <li><a href="/warehouse/inventory-unit">Inventory Unit</a></li>
<?php endif; ?>

<!-- Halaman View -->
<?php if (!hasPermission('warehouse.inventory_unit.view')): ?>
    <?= redirect()->to('/')->with('error', 'Akses ditolak') ?>
<?php endif; ?>

<!-- Tombol Print -->
<?= renderPrintButton('warehouse.inventory_unit.print', 'printReport()') ?>
<!-- Output: <button class="btn btn-info" onclick="printReport()">
              <i class="fas fa-print"></i> Print
            </button> -->
```

---

### Denied Permissions (❌ User tidak bisa akses)

| Permission | UI Element | Behavior | Reason |
|-----------|-----------|----------|--------|
| `create` | Tombol "Tambah" | ❌ **DISABLED dengan tooltip** | Action button (show disabled) |
| `edit` | Tombol "Edit" | ❌ **DISABLED dengan tooltip** | Action button (show disabled) |
| `delete` | Tombol "Hapus" | ❌ **DISABLED dengan tooltip** | Action button (show disabled) |
| `export` | Tombol "Export" | ❌ **HIDDEN (tidak muncul)** | Export default hidden |

**Contoh Implementasi:**
```php
<!-- Tombol Tambah - DISABLED -->
<?= renderCreateButton('warehouse.inventory_unit.create', 'addUnit()') ?>
<!-- Output jika TIDAK ADA PERMISSION:
     <button class="btn btn-success" disabled data-toggle="tooltip" 
             title="Anda tidak memiliki permission untuk menambah data"
             onclick="showPermissionDenied('Anda tidak memiliki permission untuk menambah data')">
         <i class="fas fa-plus"></i> Tambah
     </button> -->

<!-- Tombol Edit - DISABLED -->
<?= renderEditButton('warehouse.inventory_unit.edit', 'editUnit(123)') ?>
<!-- Disabled dengan tooltip dan SweetAlert saat diklik -->

<!-- Tombol Hapus - DISABLED -->
<?= renderDeleteButton('warehouse.inventory_unit.delete', 'deleteUnit(123)') ?>
<!-- Disabled dengan tooltip dan SweetAlert saat diklik -->

<!-- Tombol Export - HIDDEN -->
<?= renderExportButton('warehouse.inventory_unit.export', 'exportData()') ?>
<!-- Output: (kosong, tombol tidak muncul sama sekali) -->
```

---

## 📊 Comparison: Hide vs Show Disabled

### UX Best Practice (Enterprise Standard)

| UI Element Type | No Permission Behavior | Reasoning |
|----------------|----------------------|-----------|
| **Navigation Menu** | ❌ **HIDE** | Cleaner UI, user tidak tahu fitur yang tidak bisa diakses |
| **Action Buttons** (Edit, Delete, Approve) | ⚪ **SHOW DISABLED** | User aware permission exists, bisa request akses ke admin |
| **Export/Download** | ❌ **HIDE** | Tombol export tidak relevan jika tidak bisa digunakan |
| **Print** | ⚪ **SHOW DISABLED** (default) atau **HIDE** (optional) | Tergantung business rules |

**Why Show Disabled for Action Buttons?**
1. **User Awareness**: User tahu fitur ini ada, tapi perlu permission
2. **Self-Service Request**: User bisa request akses ke admin (tooltip kasih hint)
3. **Consistency**: Button position konsisten di setiap row (tidak loncat-loncat)
4. **Training**: Berguna untuk onboarding user baru

**Why Hide Navigation Menu?**
1. **Clean UI**: Sidebar tidak penuh dengan menu yang tidak bisa diakses
2. **Focus**: User fokus ke fitur yang memang bisa dipakai
3. **Security**: Tidak expose fitur yang hidden

---

## 💾 SQL Implementation

### Step 1: Add Permissions (Jika belum ada)

```sql
INSERT INTO permissions (module, page, action, key_name, display_name, description)
VALUES 
    ('warehouse', 'inventory_unit', 'navigation', 'warehouse.inventory_unit.navigation', 'Warehouse - Inventory Unit - Navigation', 'Akses menu Inventory Unit'),
    ('warehouse', 'inventory_unit', 'view', 'warehouse.inventory_unit.view', 'Warehouse - Inventory Unit - View', 'Lihat data inventory unit'),
    ('warehouse', 'inventory_unit', 'create', 'warehouse.inventory_unit.create', 'Warehouse - Inventory Unit - Create', 'Tambah data inventory unit'),
    ('warehouse', 'inventory_unit', 'edit', 'warehouse.inventory_unit.edit', 'Warehouse - Inventory Unit - Edit', 'Edit data inventory unit'),
    ('warehouse', 'inventory_unit', 'delete', 'warehouse.inventory_unit.delete', 'Warehouse - Inventory Unit - Delete', 'Hapus data inventory unit'),
    ('warehouse', 'inventory_unit', 'print', 'warehouse.inventory_unit.print', 'Warehouse - Inventory Unit - Print', 'Print laporan inventory'),
    ('warehouse', 'inventory_unit', 'export', 'warehouse.inventory_unit.export', 'Warehouse - Inventory Unit - Export', 'Export data inventory')
ON DUPLICATE KEY UPDATE display_name = VALUES(display_name);
```

### Step 2: Grant User Permissions (User ID = 5, Admin Marketing)

```sql
-- ✅ GRANT: Navigation (menu muncul)
INSERT INTO user_permissions (user_id, permission_id, granted, reason, assigned_by)
SELECT 5, p.id, 1, 'Cross-module access untuk koordinasi warehouse', 1
FROM permissions p WHERE p.key_name = 'warehouse.inventory_unit.navigation';

-- ✅ GRANT: View (halaman bisa dibuka)
INSERT INTO user_permissions (user_id, permission_id, granted, reason, assigned_by)
SELECT 5, p.id, 1, 'Read-only access monitoring stock', 1
FROM permissions p WHERE p.key_name = 'warehouse.inventory_unit.view';

-- ✅ GRANT: Print (bisa print laporan)
INSERT INTO user_permissions (user_id, permission_id, granted, reason, assigned_by)
SELECT 5, p.id, 1, 'Print untuk koordinasi marketing campaign', 1
FROM permissions p WHERE p.key_name = 'warehouse.inventory_unit.print';

-- ❌ DENY: Create (explicitly revoke)
INSERT INTO user_permissions (user_id, permission_id, granted, reason, assigned_by)
SELECT 5, p.id, 0, 'Read-only user', 1
FROM permissions p WHERE p.key_name = 'warehouse.inventory_unit.create';

-- ❌ DENY: Edit
INSERT INTO user_permissions (user_id, permission_id, granted, reason, assigned_by)
SELECT 5, p.id, 0, 'Read-only user', 1
FROM permissions p WHERE p.key_name = 'warehouse.inventory_unit.edit';

-- ❌ DENY: Delete
INSERT INTO user_permissions (user_id, permission_id, granted, reason, assigned_by)
SELECT 5, p.id, 0, 'Read-only user', 1
FROM permissions p WHERE p.key_name = 'warehouse.inventory_unit.delete';

-- ❌ DENY: Export
INSERT INTO user_permissions (user_id, permission_id, granted, reason, assigned_by)
SELECT 5, p.id, 0, 'Export restricted', 1
FROM permissions p WHERE p.key_name = 'warehouse.inventory_unit.export';
```

### Step 3: Verify Permissions

```sql
SELECT 
    p.key_name,
    CASE 
        WHEN up.granted = 1 THEN '✅ GRANTED'
        WHEN up.granted = 0 THEN '❌ DENIED'
    END as status,
    up.reason
FROM user_permissions up
JOIN permissions p ON up.permission_id = p.id
WHERE up.user_id = 5
AND p.key_name LIKE 'warehouse.inventory_unit.%'
ORDER BY p.key_name;
```

**Expected Output:**
```
key_name                                | status     | reason
----------------------------------------|------------|---------------------------
warehouse.inventory_unit.navigation     | ✅ GRANTED | Cross-module access...
warehouse.inventory_unit.view           | ✅ GRANTED | Read-only access...
warehouse.inventory_unit.print          | ✅ GRANTED | Print untuk koordinasi...
warehouse.inventory_unit.create         | ❌ DENIED  | Read-only user
warehouse.inventory_unit.edit           | ❌ DENIED  | Read-only user
warehouse.inventory_unit.delete         | ❌ DENIED  | Read-only user
warehouse.inventory_unit.export         | ❌ DENIED  | Export restricted
```

---

## 🎯 Answer: Apakah Tombol Print Muncul?

### ✅ **YA, TOMBOL PRINT MUNCUL AKTIF**

**Alasan:**
1. User punya permission `warehouse.inventory_unit.print` = **granted (1)**
2. Helper `renderPrintButton()` check permission dengan `hasPermission()`
3. `hasPermission()` flow:
   ```
   Check admin bypass → NO (user bukan admin)
   Check user_permissions → YES (granted = 1) ✅ RETURN TRUE
   ```
4. Tombol render sebagai active button (tidak disabled)

### ❌ Tombol Yang DISABLED:

```php
renderCreateButton()  → Disabled (user_permissions.granted = 0)
renderEditButton()    → Disabled (user_permissions.granted = 0)
renderDeleteButton()  → Disabled (user_permissions.granted = 0)
```

**Behavior saat tombol disabled diklik:**
```javascript
// SweetAlert muncul dengan pesan:
Swal.fire({
    icon: 'error',
    title: 'Akses Ditolak',
    text: 'Anda tidak memiliki permission untuk menambah/edit/hapus data',
    confirmButtonText: 'OK'
});
```

### ❌ Tombol Yang HIDDEN (Tidak Muncul):

```php
renderExportButton() → Hidden (showDisabled = false by default)
```

---

## 🔧 PHP Helper Functions Usage

### Check Permission (Untuk conditional logic)

```php
<?php if (hasPermission('warehouse.inventory_unit.edit')): ?>
    <!-- Show edit form -->
<?php else: ?>
    <!-- Show read-only view -->
<?php endif; ?>
```

### Get Permission Source (Debugging/Audit)

```php
$source = getPermissionSource(5, 'warehouse.inventory_unit.print');
print_r($source);

// Output:
Array (
    [has_permission] => 1
    [source] => user_grant
    [details] => Array (
        [granted] => 1
        [reason] => Print untuk koordinasi marketing campaign
        [expires_at] => null
        [is_temporary] => 0
    )
)
```

### Get All User Permission Overrides

```php
$overrides = getUserPermissionOverrides(5);
foreach ($overrides as $perm) {
    echo "{$perm['key_name']}: " . ($perm['granted'] ? 'GRANTED' : 'DENIED');
    echo " - {$perm['reason']}\n";
}
```

---

## 📌 Summary

| Element | Status | UI Display |
|---------|--------|-----------|
| **Sidebar Menu "Inventory Unit"** | ✅ Granted | ✅ Menu MUNCUL |
| **Halaman Inventory Unit** | ✅ Granted | ✅ Bisa DIBUKA |
| **Tombol Print** | ✅ Granted | ✅ **AKTIF** (bisa diklik) |
| **Tombol Tambah** | ❌ Denied | ⚪ **DISABLED** (tooltip + alert) |
| **Tombol Edit** | ❌ Denied | ⚪ **DISABLED** (tooltip + alert) |
| **Tombol Hapus** | ❌ Denied | ⚪ **DISABLED** (tooltip + alert) |
| **Tombol Export** | ❌ Denied | ❌ **HIDDEN** (tidak muncul) |

**Kesimpulan:**
- Admin Marketing bisa **LIHAT** data inventory warehouse
- Admin Marketing bisa **PRINT** laporan inventory
- Admin Marketing **TIDAK BISA** modifikasi data (create/edit/delete)
- Admin Marketing **TIDAK BISA** export data
- User aware bahwa fitur edit/delete ada, tapi tidak punya akses (tombol disabled bukan hidden)

---

**File terkait:**
- Helper: `app/Helpers/permission_helper.php` (core permission check)
- Helper: `app/Helpers/user_permission_helper.php` (manage user permissions)
- Helper: `app/Helpers/action_button_helper.php` (UI components)
- Migration: `databases/migrations/example_user_custom_permission_admin_marketing.sql`
