# RBAC System Audit Report - OPTIMA System

**Tanggal Audit:** 2025-01-XX  
**Status:** ✅ Audit Lengkap  
**Versi Sistem:** Balanced RBAC (32 permissions)

---

## 📋 DAFTAR ISI

1. [Ringkasan Eksekutif](#ringkasan-eksekutif)
2. [Struktur RBAC Saat Ini](#struktur-rbac-saat-ini)
3. [Alur RBAC](#alur-rbac)
4. [Analisis Database](#analisis-database)
5. [Halaman yang Belum Terproteksi](#halaman-yang-belum-terproteksi)
6. [Rekomendasi](#rekomendasi)
7. [Action Items](#action-items)

---

## 🎯 RINGKASAN EKSEKUTIF

### Status Sistem RBAC
- ✅ **Sistem RBAC:** Balanced RBAC (32 permissions)
- ✅ **BaseController:** Memiliki helper methods lengkap
- ✅ **Filters:** PermissionFilter, RoleFilter, LevelFilter tersedia
- ⚠️ **Implementasi:** Beberapa controller belum menggunakan permission checks
- ⚠️ **Routes:** Beberapa route belum menggunakan filter protection

### Temuan Utama
1. **32 Permissions** terdefinisi dengan struktur `module.action`
2. **BaseController** memiliki methods: `hasPermission()`, `canAccess()`, `canManage()`, `canDelete()`, `canExport()`
3. **Beberapa controller** belum memiliki permission checks
4. **Beberapa routes** belum menggunakan filter protection

---

## 🏗️ STRUKTUR RBAC SAAT INI

### 1. Permission Structure

Sistem menggunakan **Balanced RBAC** dengan format:
```
module.action
```

**Modules:**
- `admin` - Administrasi sistem
- `marketing` - Modul marketing
- `service` - Modul service
- `purchasing` - Modul purchasing
- `warehouse` - Modul warehouse
- `perizinan` - Modul perizinan
- `accounting` - Modul accounting
- `operational` - Modul operational

**Actions:**
- `access` - View/Read data
- `manage` - Create & Edit data
- `delete` - Delete data
- `export` - Export data

**Total:** 8 modules × 4 actions = **32 permissions**

### 2. Database Tables

#### Table: `permissions`
```sql
- id (PK)
- key (e.g., 'admin.access', 'marketing.manage')
- name
- description
- module
- category
- is_system_permission
- is_active
- created_by
- updated_by
- created_at
- updated_at
```

#### Table: `roles`
```sql
- id (PK)
- name
- description
- is_preset
- level
- is_active
- created_at
- updated_at
```

#### Table: `user_roles`
```sql
- id (PK)
- user_id (FK)
- role_id (FK)
- assigned_by
- assigned_at
```

#### Table: `role_permissions`
```sql
- id (PK)
- role_id (FK)
- permission_id (FK)
- granted (boolean)
```

#### Table: `user_permissions` (optional, untuk custom permissions)
```sql
- id (PK)
- user_id (FK)
- permission_id (FK)
- division_id (FK, nullable)
- granted (boolean)
- assigned_by
- assigned_at
- expires_at
```

### 3. Helper Functions

#### BaseController Methods
```php
// Check permissions
$this->hasPermission('admin.access')
$this->canAccess('marketing')      // Checks module.access
$this->canManage('marketing')      // Checks module.manage
$this->canDelete('marketing')      // Checks module.delete
$this->canExport('marketing')     // Checks module.export

// Require permissions (auto-redirect/return error)
$this->requireAccess('marketing', $ajax)
$this->requireManage('marketing')
$this->requireDelete('marketing')
$this->requireExport('marketing')
```

#### Simple RBAC Helper (`app/Helpers/simple_rbac_helper.php`)
```php
can_access($module, $level = 'view', $user_id = null)
can_view($module, $user_id = null)
can_edit($module, $user_id = null)
can_manage($module, $user_id = null)
can_create($module, $user_id = null)
can_export($module, $user_id = null)
get_user_permission_level($module, $user_id = null)
get_user_permissions($user_id = null)
get_module_permissions($module)
get_all_modules()
```

### 4. Filters

#### PermissionFilter (`app/Filters/PermissionFilter.php`)
- Checks `has_permission()` helper
- Redirects jika tidak memiliki permission

#### RoleFilter (`app/Filters/RoleFilter.php`)
- Checks `has_role()` helper
- Redirects jika tidak memiliki role

#### LevelFilter (`app/Filters/LevelFilter.php`)
- Checks level: management, head_division, admin, staff

---

## 🔄 ALUR RBAC

### Flow Diagram

```
User Login
    ↓
Session Created (user_id, role, etc.)
    ↓
Request to Controller
    ↓
AuthFilter (Global) - Checks if logged in
    ↓
Controller Method
    ↓
Permission Check (if implemented)
    ├─ hasPermission('module.action')
    │   ├─ Check Super Admin → Return true
    │   ├─ Check role_permissions → Return true/false
    │   └─ Check user_permissions (optional) → Return true/false
    │
    └─ If false → Redirect/Return Error
    ↓
Execute Controller Logic
    ↓
Return Response
```

### Permission Check Logic (BaseController::hasPermission)

1. **Check if user logged in** → Return false if not
2. **Check Super Administrator** → Return true if Super Admin
3. **Resolve permission ID** by key
4. **Check role_permissions** via user_roles → role_permissions
5. **Check user_permissions** (optional, direct assignment)
6. **Return false** if no permission found
7. **Fallback:** Return true if permission key not registered (prevents UI blocking)

---

## 🗄️ ANALISIS DATABASE

### Expected Permissions (32 total)

#### Admin Module (4 permissions)
- `admin.access`
- `admin.manage`
- `admin.delete`
- `admin.export`

#### Marketing Module (4 permissions)
- `marketing.access`
- `marketing.manage`
- `marketing.delete`
- `marketing.export`

#### Service Module (4 permissions)
- `service.access`
- `service.manage`
- `service.delete`
- `service.export`

#### Purchasing Module (4 permissions)
- `purchasing.access`
- `purchasing.manage`
- `purchasing.delete`
- `purchasing.export`

#### Warehouse Module (4 permissions)
- `warehouse.access`
- `warehouse.manage`
- `warehouse.delete`
- `warehouse.export`

#### Perizinan Module (4 permissions)
- `perizinan.access`
- `perizinan.manage`
- `perizinan.delete`
- `perizinan.export`

#### Accounting Module (4 permissions)
- `accounting.access`
- `accounting.manage`
- `accounting.delete`
- `accounting.export`

#### Operational Module (4 permissions)
- `operational.access`
- `operational.manage`
- `operational.delete`
- `operational.export`

### Verification Queries

```sql
-- Check all permissions
SELECT module, key, name, is_active 
FROM permissions 
ORDER BY module, key;

-- Check permissions per module
SELECT module, COUNT(*) as count 
FROM permissions 
GROUP BY module 
ORDER BY module;

-- Check role permissions
SELECT r.name as role_name, COUNT(rp.permission_id) as permission_count
FROM roles r
LEFT JOIN role_permissions rp ON r.id = rp.role_id
GROUP BY r.id, r.name
ORDER BY permission_count DESC;

-- Check user permissions
SELECT u.username, r.name as role_name, p.key as permission_key
FROM users u
JOIN user_roles ur ON u.id = ur.user_id
JOIN role_permissions rp ON ur.role_id = rp.role_id
JOIN permissions p ON rp.permission_id = p.id
WHERE u.id = ?
ORDER BY p.module, p.key;
```

---

## ⚠️ HALAMAN YANG BELUM TERPROTEKSI

### 1. Dashboard Controller (`app/Controllers/Dashboard.php`)

**Status:** ❌ **TIDAK ADA PERMISSION CHECK**

**Methods yang perlu ditambahkan:**
- `index()` - Main dashboard
- `service()` - Service dashboard
- `rolling()` - Rolling unit dashboard
- `marketing()` - Marketing dashboard
- `warehouse()` - Warehouse dashboard

**Rekomendasi:**
```php
public function index()
{
    // Check if user is logged in (already done)
    if (!session()->get('isLoggedIn')) {
        return redirect()->to('/auth/login');
    }
    
    // TODO: Add permission check
    // if (!$this->canAccess('admin')) {
    //     return redirect()->to('/dashboard')->with('error', 'Access denied.');
    // }
    
    // ... rest of code
}
```

### 2. Finance Controller (`app/Controllers/Finance.php`)

**Status:** ❌ **TIDAK ADA PERMISSION CHECK**

**Methods yang perlu ditambahkan:**
- `index()` - Finance dashboard
- `invoices()` - Invoice management
- `payments()` - Payment management
- `expenses()` - Expense management
- `reports()` - Financial reports
- `createInvoice()` - Create invoice
- `updatePaymentStatus()` - Update payment

**Rekomendasi:**
```php
public function index()
{
    if (!$this->canAccess('accounting')) {
        return redirect()->to('/dashboard')->with('error', 'Access denied.');
    }
    // ... rest of code
}
```

### 3. Reports Controller (`app/Controllers/Reports.php`)

**Status:** ❌ **TIDAK ADA PERMISSION CHECK**

**Methods yang perlu ditambahkan:**
- `index()` - Reports dashboard
- `rental()` - Rental reports
- `maintenance()` - Maintenance reports
- `financial()` - Financial reports
- `inventory()` - Inventory reports
- `custom()` - Custom reports
- `generateReport()` - Generate report
- `delete()` - Delete report

**Rekomendasi:**
```php
public function index()
{
    // Reports bisa diakses oleh semua yang punya akses ke modul apapun
    // Atau buat permission khusus: reports.access
    if (!$this->canAccess('admin')) { // atau reports.access
        return redirect()->to('/dashboard')->with('error', 'Access denied.');
    }
    // ... rest of code
}
```

### 4. Kontrak Controller (`app/Controllers/Kontrak.php`)

**Status:** ⚠️ **SEBAGIAN TERPROTEKSI** (menggunakan helper, bukan BaseController method)

**Methods yang perlu diperbaiki:**
- `index()` - Menggunakan `can_view('marketing')` helper
- `store()` - Belum ada check
- `update()` - Belum ada check
- `delete()` - Belum ada check
- `edit()` - Belum ada check

**Rekomendasi:**
```php
public function store()
{
    if (!$this->canManage('marketing')) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Anda tidak memiliki izin untuk membuat kontrak'
        ])->setStatusCode(403);
    }
    // ... rest of code
}
```

### 5. RentalManagement Controller (`app/Controllers/RentalManagement.php`)

**Status:** ❌ **TIDAK ADA PERMISSION CHECK**

**Methods yang perlu ditambahkan:**
- `index()` - Rental list
- `create()` - Create rental
- `store()` - Store rental
- `edit()` - Edit rental
- `update()` - Update rental
- `delete()` - Delete rental
- `updateStatus()` - Update status

**Rekomendasi:**
```php
public function index()
{
    if (!$this->canAccess('operational')) { // atau rental.access
        return redirect()->to('/dashboard')->with('error', 'Access denied.');
    }
    // ... rest of code
}
```

### 6. UnitRolling Controller (`app/Controllers/UnitRolling.php`)

**Status:** ❌ **TIDAK ADA PERMISSION CHECK**

**Methods yang perlu ditambahkan:**
- `index()` - Unit rolling list
- `updateLocation()` - Update location

**Rekomendasi:**
```php
public function index()
{
    if (!$this->canAccess('operational')) {
        return redirect()->to('/dashboard')->with('error', 'Access denied.');
    }
    // ... rest of code
}
```

### 7. Admin/RoleController (`app/Controllers/Admin/RoleController.php`)

**Status:** ❌ **TIDAK ADA PERMISSION CHECK**

**Methods yang perlu ditambahkan:**
- `index()` - Role management
- `getRoles()` - Get roles
- `getRole()` - Get role detail
- `saveRole()` - Save role

**Rekomendasi:**
```php
public function index()
{
    if (!$this->hasPermission('admin.role_management')) {
        return redirect()->to('/dashboard')->with('error', 'Access denied.');
    }
    // ... rest of code
}
```

### 8. ApiController (`app/Controllers/ApiController.php`)

**Status:** ⚠️ **SEBAGIAN TERPROTEKSI** (hanya check AJAX, tidak check permission)

**Methods yang perlu ditambahkan:**
- `getMerk()` - Get merk list
- `getModelsByMerk()` - Get models by merk
- `getFormData()` - Get form data
- `getDropdownData()` - Get dropdown data

**Rekomendasi:**
```php
public function getMerk()
{
    if (!$this->request->isAJAX()) {
        return $this->response->setStatusCode(403)->setJSON(['error' => 'Invalid request method']);
    }
    
    // TODO: Add permission check based on context
    // if (!$this->canAccess('warehouse') && !$this->canAccess('purchasing')) {
    //     return $this->response->setStatusCode(403)->setJSON(['error' => 'Access denied']);
    // }
    
    // ... rest of code
}
```

### 9. Welcome Controller (`app/Controllers/Welcome.php`)

**Status:** ✅ **OK** (Public page, hanya check login)

### 10. Auth Controller (`app/Controllers/Auth.php`)

**Status:** ✅ **OK** (Public pages, tidak perlu permission check)

### 11. System Controller (`app/Controllers/System.php`)

**Status:** ⚠️ **PERLU DICEK** (Profile management, biasanya public untuk user sendiri)

### 12. Settings Controller (`app/Controllers/Settings.php`)

**Status:** ❌ **TIDAK ADA PERMISSION CHECK**

**Methods yang perlu ditambahkan:**
- `index()` - Settings page
- `update()` - Update settings

**Rekomendasi:**
```php
public function index()
{
    if (!$this->hasPermission('admin.settings')) { // atau admin.access
        return redirect()->to('/dashboard')->with('error', 'Access denied.');
    }
    // ... rest of code
}
```

---

## 📊 RINGKASAN CONTROLLER PERMISSION STATUS

| Controller | Status | Methods Tanpa Permission | Prioritas |
|------------|--------|-------------------------|-----------|
| Dashboard | ❌ | 5 methods | HIGH |
| Finance | ❌ | 7 methods | HIGH |
| Reports | ❌ | 7 methods | HIGH |
| RentalManagement | ❌ | 7 methods | MEDIUM |
| UnitRolling | ❌ | 2 methods | MEDIUM |
| Kontrak | ⚠️ | 4 methods | HIGH |
| Admin/RoleController | ❌ | 4 methods | HIGH |
| ApiController | ⚠️ | 4 methods | LOW |
| Settings | ❌ | 2 methods | MEDIUM |
| Service | ✅ | - | - |
| Warehouse | ✅ | - | - |
| Purchasing | ⚠️ | Beberapa methods | MEDIUM |
| Marketing | ⚠️ | Beberapa methods | MEDIUM |
| Perizinan | ✅ | - | - |
| Operational | ✅ | - | - |
| Admin/AdvancedUserManagement | ✅ | - | - |
| Admin/PermissionController | ✅ | - | - |

**Legenda:**
- ✅ = Sudah memiliki permission checks
- ⚠️ = Sebagian memiliki permission checks
- ❌ = Tidak memiliki permission checks

---

## 🔧 REKOMENDASI

### 1. Immediate Actions (HIGH Priority)

#### A. Tambahkan Permission Checks ke Controller yang Belum Terproteksi

**Dashboard Controller:**
```php
public function index()
{
    if (!session()->get('isLoggedIn')) {
        return redirect()->to('/auth/login');
    }
    // Dashboard bisa diakses semua user yang login
    // Tidak perlu permission check khusus
    // ... rest of code
}

public function service()
{
    if (!$this->canAccess('service')) {
        return redirect()->to('/dashboard')->with('error', 'Access denied.');
    }
    // ... rest of code
}

public function marketing()
{
    if (!$this->canAccess('marketing')) {
        return redirect()->to('/dashboard')->with('error', 'Access denied.');
    }
    // ... rest of code
}
```

**Finance Controller:**
```php
public function index()
{
    if (!$this->canAccess('accounting')) {
        return redirect()->to('/dashboard')->with('error', 'Access denied.');
    }
    // ... rest of code
}

public function createInvoice()
{
    if (!$this->canManage('accounting')) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Anda tidak memiliki izin untuk membuat invoice'
        ])->setStatusCode(403);
    }
    // ... rest of code
}
```

**Reports Controller:**
```php
public function index()
{
    // Reports bisa diakses oleh admin atau user dengan akses ke modul apapun
    // Atau buat permission khusus
    if (!$this->canAccess('admin')) {
        return redirect()->to('/dashboard')->with('error', 'Access denied.');
    }
    // ... rest of code
}
```

#### B. Standardisasi Permission Checks

**Gunakan BaseController methods:**
- ✅ `$this->canAccess('module')` - untuk view/read
- ✅ `$this->canManage('module')` - untuk create/edit
- ✅ `$this->canDelete('module')` - untuk delete
- ✅ `$this->canExport('module')` - untuk export

**Jangan gunakan helper langsung di controller:**
- ❌ `can_view('marketing')` - gunakan `$this->canAccess('marketing')`
- ❌ `can_edit('marketing')` - gunakan `$this->canManage('marketing')`

### 2. Medium Priority Actions

#### A. Tambahkan Route Filters

**Di `app/Config/Routes.php`:**
```php
// Finance routes dengan permission filter
$routes->group('finance', ['filter' => 'permission:accounting.access'], static function ($routes) {
    $routes->get('/', 'Finance::index');
    $routes->get('invoices', 'Finance::invoices');
    // ...
});

// Reports routes dengan permission filter
$routes->group('reports', ['filter' => 'permission:admin.access'], static function ($routes) {
    $routes->get('/', 'Reports::index');
    // ...
});
```

#### B. Tambahkan Permission untuk Modul Baru

Jika ada modul baru yang belum terdaftar:
1. Tambahkan 4 permissions baru (access, manage, delete, export)
2. Assign ke roles yang sesuai
3. Update BaseController jika perlu

### 3. Low Priority Actions

#### A. Audit Permission Usage

Buat script untuk:
1. Scan semua controller methods
2. Check apakah ada permission check
3. Generate report halaman yang belum terproteksi

#### B. Testing

1. Test setiap role dengan permission yang berbeda
2. Verify bahwa permission checks bekerja dengan benar
3. Test edge cases (Super Admin, expired permissions, etc.)

---

## ✅ ACTION ITEMS

### High Priority (Lakukan Segera)

- [ ] **Dashboard Controller** - Tambahkan permission checks untuk service, marketing, warehouse dashboards
- [ ] **Finance Controller** - Tambahkan permission checks untuk semua methods
- [ ] **Reports Controller** - Tambahkan permission checks untuk semua methods
- [ ] **Kontrak Controller** - Standardisasi permission checks (gunakan BaseController methods)
- [ ] **Admin/RoleController** - Tambahkan permission checks

### Medium Priority (Lakukan dalam 1-2 minggu)

- [ ] **RentalManagement Controller** - Tambahkan permission checks
- [ ] **UnitRolling Controller** - Tambahkan permission checks
- [ ] **Settings Controller** - Tambahkan permission checks
- [ ] **Tambahkan Route Filters** - Untuk finance, reports, dll
- [ ] **Purchasing Controller** - Lengkapi permission checks untuk semua methods
- [ ] **Marketing Controller** - Lengkapi permission checks untuk semua methods

### Low Priority (Lakukan dalam 1 bulan)

- [ ] **ApiController** - Tambahkan permission checks berdasarkan context
- [ ] **Audit Script** - Buat script untuk scan permission usage
- [ ] **Testing** - Test semua roles dan permissions
- [ ] **Documentation** - Update dokumentasi dengan permission matrix

---

## 📝 CATATAN PENTING

1. **Super Administrator** selalu memiliki akses penuh (bypass semua permission checks)
2. **Permission tidak ditemukan** → System return `true` (permissive mode untuk development)
3. **Gunakan BaseController methods** untuk konsistensi
4. **Route filters** bisa digunakan sebagai layer tambahan protection
5. **Test thoroughly** setelah menambahkan permission checks

---

## 🔍 VERIFIKASI DATABASE

Untuk memverifikasi database, jalankan query berikut:

```sql
-- 1. Check semua permissions
SELECT module, key, name, is_active 
FROM permissions 
ORDER BY module, key;

-- 2. Check permissions per module (harus 4 per module)
SELECT module, COUNT(*) as count 
FROM permissions 
GROUP BY module 
ORDER BY module;

-- 3. Check role permissions assignment
SELECT r.name as role_name, p.module, p.key, rp.granted
FROM roles r
JOIN role_permissions rp ON r.id = rp.role_id
JOIN permissions p ON rp.permission_id = p.id
ORDER BY r.name, p.module, p.key;

-- 4. Check user roles
SELECT u.username, u.email, r.name as role_name
FROM users u
JOIN user_roles ur ON u.id = ur.user_id
JOIN roles r ON ur.role_id = r.id
ORDER BY u.username, r.name;
```

---

**Dokumen ini dibuat oleh:** AI Assistant  
**Tanggal:** 2025-01-XX  
**Versi:** 1.0

