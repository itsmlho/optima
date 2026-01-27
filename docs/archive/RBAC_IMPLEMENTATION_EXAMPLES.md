# Contoh Implementasi RBAC - Resource Permissions

**Tanggal:** 2025-01-XX  
**Versi:** 1.0

---

## 📋 DAFTAR ISI

1. [Contoh di Marketing Controller](#contoh-di-marketing-controller)
2. [Contoh di Service Controller](#contoh-di-service-controller)
3. [Contoh di Warehouse Controller](#contoh-di-warehouse-controller)
4. [Contoh di Views](#contoh-di-views)
5. [Best Practices](#best-practices)

---

## 🎯 CONTOH DI MARKETING CONTROLLER

### Scenario: Marketing perlu cek inventory untuk lihat unit tersedia

```php
// app/Controllers/Marketing.php

/**
 * View available units (Marketing perlu akses ke warehouse inventory)
 */
public function availableUnits()
{
    // Check permission: bisa menggunakan module permission atau resource permission
    // Marketing Head/Staff punya: warehouse.inventory.view (resource permission)
    if (!$this->canAccess('warehouse') && !$this->canViewResource('warehouse', 'inventory')) {
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied: You do not have permission to view inventory'
            ])->setStatusCode(403);
        }
        return redirect()->to('/dashboard')->with('error', 'Access denied.');
    }
    
    // ... rest of code
    return view('marketing/unit_tersedia', $data);
}

/**
 * View unit detail (Marketing perlu akses ke warehouse inventory)
 */
public function unitDetail($id)
{
    // Check permission
    if (!$this->canAccess('warehouse') && !$this->canViewResource('warehouse', 'inventory')) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Access denied'
        ])->setStatusCode(403);
    }
    
    // ... rest of code
}

/**
 * Create kontrak (Marketing punya full access ke modul sendiri)
 */
public function createKontrak()
{
    // Check permission: Marketing punya marketing.manage (module permission)
    if (!$this->canManage('marketing')) {
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk membuat kontrak'
            ])->setStatusCode(403);
        }
        return redirect()->back()->with('error', 'Access denied.');
    }
    
    // ... rest of code
}
```

---

## 🔧 CONTOH DI SERVICE CONTROLLER

### Scenario: Service perlu lihat kontrak untuk maintenance

```php
// app/Controllers/Service.php

/**
 * View kontrak untuk maintenance (Service perlu akses ke marketing kontrak)
 */
public function viewContractForMaintenance($kontrakId)
{
    // Check permission: Service Head/Staff punya: marketing.kontrak.view (resource permission)
    if (!$this->canAccess('marketing') && !$this->canViewResource('marketing', 'kontrak')) {
        return redirect()->to('/dashboard')->with('error', 'Access denied.');
    }
    
    // ... rest of code
}

/**
 * Update unit status setelah maintenance (Service perlu manage inventory)
 */
public function updateUnitStatusAfterMaintenance($unitId)
{
    // Check permission: Service Head/Staff punya: warehouse.inventory.manage (resource permission)
    if (!$this->canManage('warehouse') && !$this->canManageResource('warehouse', 'inventory')) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Access denied: You do not have permission to update inventory'
        ])->setStatusCode(403);
    }
    
    // ... rest of code
}

/**
 * Create work order (Service punya full access ke modul sendiri)
 */
public function createWorkOrder()
{
    // Check permission: Service punya service.manage (module permission)
    if (!$this->canManage('service')) {
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk membuat work order'
            ])->setStatusCode(403);
        }
        return redirect()->back()->with('error', 'Access denied.');
    }
    
    // ... rest of code
}
```

---

## 📦 CONTOH DI WAREHOUSE CONTROLLER

### Scenario: Warehouse perlu lihat kontrak terkait unit

```php
// app/Controllers/Warehouse.php

/**
 * View kontrak terkait unit (Warehouse perlu akses ke marketing kontrak)
 */
public function viewContractForUnit($unitId)
{
    // Check permission: Warehouse Head/Staff punya: marketing.kontrak.view (resource permission)
    if (!$this->canAccess('marketing') && !$this->canViewResource('marketing', 'kontrak')) {
        return redirect()->to('/dashboard')->with('error', 'Access denied.');
    }
    
    // ... rest of code
}

/**
 * Update inventory (Warehouse punya full access ke modul sendiri)
 */
public function updateInventory($id)
{
    // Check permission: Warehouse punya warehouse.manage (module permission)
    if (!$this->canManage('warehouse')) {
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied'
            ])->setStatusCode(403);
        }
        return redirect()->back()->with('error', 'Access denied.');
    }
    
    // ... rest of code
}

/**
 * Update unit status (bisa dari Service atau Warehouse sendiri)
 */
public function updateUnitStatus($unitId)
{
    // Check permission: 
    // - Warehouse punya warehouse.manage (module permission)
    // - Service punya warehouse.inventory.manage (resource permission)
    if (!$this->canManage('warehouse') && !$this->canManageResource('warehouse', 'inventory')) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Access denied'
        ])->setStatusCode(403);
    }
    
    // ... rest of code
}
```

---

## 🎨 CONTOH DI VIEWS

### Scenario: Tampilkan UI berdasarkan permission

```php
<!-- app/Views/marketing/kontrak/index.php -->

<!-- Show kontrak data jika punya akses -->
<?php if ($this->canAccess('marketing') || $this->canViewResource('marketing', 'kontrak')): ?>
    <div class="kontrak-list">
        <!-- Kontrak data -->
    </div>
<?php endif; ?>

<!-- Show edit/delete buttons hanya jika punya manage permission -->
<?php if ($this->canManage('marketing')): ?>
    <button class="btn-edit" onclick="editKontrak(<?= $kontrak['id'] ?>)">
        <i class="fas fa-edit"></i> Edit
    </button>
    <button class="btn-delete" onclick="deleteKontrak(<?= $kontrak['id'] ?>)">
        <i class="fas fa-trash"></i> Delete
    </button>
<?php endif; ?>

<!-- Show link ke inventory jika punya akses -->
<?php if ($this->canAccess('warehouse') || $this->canViewResource('warehouse', 'inventory')): ?>
    <a href="/warehouse/inventory" class="btn btn-info">
        <i class="fas fa-boxes"></i> Cek Inventory
    </a>
<?php endif; ?>
```

```php
<!-- app/Views/service/workorder/detail.php -->

<!-- Show work order data -->
<?php if ($this->canAccess('service') || $this->canViewResource('service', 'workorder')): ?>
    <div class="workorder-detail">
        <!-- Work order data -->
    </div>
<?php endif; ?>

<!-- Show kontrak link jika punya akses -->
<?php if ($this->canAccess('marketing') || $this->canViewResource('marketing', 'kontrak')): ?>
    <a href="/marketing/kontrak/detail/<?= $workorder['kontrak_id'] ?>" class="btn btn-info">
        <i class="fas fa-file-contract"></i> Lihat Kontrak
    </a>
<?php endif; ?>

<!-- Show edit button hanya jika punya manage permission -->
<?php if ($this->canManage('service')): ?>
    <button class="btn-edit" onclick="editWorkOrder(<?= $workorder['id'] ?>)">
        <i class="fas fa-edit"></i> Edit
    </button>
<?php endif; ?>
```

---

## ✅ BEST PRACTICES

### 1. Permission Check Pattern

```php
// ✅ GOOD: Check both module and resource permission
if (!$this->canAccess('warehouse') && !$this->canViewResource('warehouse', 'inventory')) {
    return redirect()->to('/dashboard')->with('error', 'Access denied.');
}

// ✅ GOOD: Use requireResourceAccess for cleaner code
if ($response = $this->requireResourceAccess('warehouse', 'inventory', 'view')) {
    return $response;
}

// ❌ BAD: Only check module permission (will block cross-division access)
if (!$this->canAccess('warehouse')) {
    return redirect()->to('/dashboard')->with('error', 'Access denied.');
}
```

### 2. AJAX Request Handling

```php
// ✅ GOOD: Handle AJAX requests properly
public function getInventoryData()
{
    if (!$this->canAccess('warehouse') && !$this->canViewResource('warehouse', 'inventory')) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Access denied'
        ])->setStatusCode(403);
    }
    
    // ... rest of code
}
```

### 3. View Permission Checks

```php
<!-- ✅ GOOD: Check both permissions -->
<?php if ($this->canAccess('marketing') || $this->canViewResource('marketing', 'kontrak')): ?>
    <!-- Show data -->
<?php endif; ?>

<!-- ✅ GOOD: Show different UI based on permission level -->
<?php if ($this->canManage('marketing')): ?>
    <!-- Full access UI -->
<?php elseif ($this->canViewResource('marketing', 'kontrak')): ?>
    <!-- View only UI -->
<?php endif; ?>
```

### 4. Error Messages

```php
// ✅ GOOD: Clear error messages
if (!$this->canViewResource('warehouse', 'inventory')) {
    return redirect()->to('/dashboard')->with('error', 
        'Access denied: You do not have permission to view inventory'
    );
}

// ❌ BAD: Generic error message
if (!$this->canViewResource('warehouse', 'inventory')) {
    return redirect()->to('/dashboard')->with('error', 'Access denied');
}
```

---

## 📝 CHECKLIST IMPLEMENTASI

### Untuk Setiap Controller Method

- [ ] Identifikasi apakah method ini perlu cross-division access
- [ ] Tentukan permission yang diperlukan (module atau resource)
- [ ] Tambahkan permission check di awal method
- [ ] Handle AJAX requests dengan proper response
- [ ] Test dengan berbagai role

### Untuk Setiap View

- [ ] Identifikasi elemen UI yang perlu permission check
- [ ] Gunakan permission check untuk show/hide elements
- [ ] Tampilkan UI berbeda berdasarkan permission level
- [ ] Test dengan berbagai role

---

**Dokumen ini dibuat oleh:** IT Development Team  
**Tanggal:** 2025-01-XX  
**Versi:** 1.0

