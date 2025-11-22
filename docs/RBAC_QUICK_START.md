# RBAC Resource Permissions - Quick Start Guide

**Status:** ✅ Ready to Implement

---

## 🚀 LANGKAH CEPAT

### Step 1: Setup Database (5 menit)

**Jalankan SQL file:**
```bash
# Via command line
mysql -u root -p optima_db < scripts/setup_resource_permissions.sql

# Atau via phpMyAdmin:
# 1. Buka phpMyAdmin
# 2. Pilih database optima_db
# 3. Klik tab "Import"
# 4. Pilih file: scripts/setup_resource_permissions.sql
# 5. Klik "Go"
```

**Verify:**
```sql
-- Check total permissions (harus 39)
SELECT COUNT(*) FROM permissions;

-- Check resource permissions (harus 7)
SELECT * FROM permissions WHERE category = 'resource';
```

### Step 2: Test Permission (2 menit)

1. Login sebagai **Marketing Head** atau **Marketing Staff**
2. Akses: `/marketing/available-units`
3. Harus bisa akses (punya `warehouse.inventory.view`)

### Step 3: Update Controllers (Ongoing)

Controller yang sudah diupdate:
- ✅ **Marketing Controller** - `availableUnits()`, `unitDetail()`
- ✅ **Warehouse Controller** - `updateUnit()`
- ✅ **BaseController** - Methods baru untuk resource permissions

Controller yang perlu diupdate:
- ⏳ **Service Controller** - Methods yang akses kontrak/inventory
- ⏳ **Purchasing Controller** - Methods yang akses inventory/kontrak
- ⏳ **Operational Controller** - Methods yang akses kontrak/inventory
- ⏳ **Finance Controller** - Tambahkan permission checks
- ⏳ **Reports Controller** - Tambahkan permission checks
- ⏳ **Dashboard Controller** - Tambahkan permission checks

---

## 📝 CONTOH IMPLEMENTASI

### Di Controller

```php
// Marketing perlu cek inventory
if (!$this->canAccess('warehouse') && !$this->canViewResource('warehouse', 'inventory')) {
    return redirect()->to('/dashboard')->with('error', 'Access denied.');
}

// Service perlu update inventory setelah maintenance
if (!$this->canManage('warehouse') && !$this->canManageResource('warehouse', 'inventory')) {
    return $this->response->setJSON([
        'success' => false,
        'message' => 'Access denied'
    ])->setStatusCode(403);
}
```

### Di View

```php
<!-- Show jika punya akses -->
<?php if ($this->canAccess('warehouse') || $this->canViewResource('warehouse', 'inventory')): ?>
    <a href="/warehouse/inventory">Cek Inventory</a>
<?php endif; ?>
```

---

## ✅ CHECKLIST

- [ ] Run SQL setup (`scripts/setup_resource_permissions.sql`)
- [ ] Verify permissions di database (39 total)
- [ ] Test login sebagai Marketing Head/Staff
- [ ] Test akses ke `/marketing/available-units`
- [ ] Update controllers sesuai kebutuhan
- [ ] Test semua role dengan permission yang berbeda

---

## 📚 DOKUMEN LENGKAP

1. **RBAC_RECOMMENDATION_PLAN.md** - Rekomendasi lengkap ⭐
2. **RBAC_IMPLEMENTATION_EXAMPLES.md** - Contoh implementasi
3. **RBAC_AUDIT_REPORT.md** - Audit sistem saat ini
4. **RBAC_IMPLEMENTATION_STATUS.md** - Status implementasi

---

**Next Step:** Run SQL setup, kemudian lanjutkan update controllers sesuai kebutuhan.

