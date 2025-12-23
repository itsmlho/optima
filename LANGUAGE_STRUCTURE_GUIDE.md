# 🌐 Language Structure - Global & Modular Approach

## 📋 Overview
Struktur language yang terorganisir untuk menghindari duplikasi dan memudahkan maintenance.

---

## 📁 File Structure

```
app/Language/
├── en/
│   ├── Common.php      ← 🌍 GLOBAL: Kata umum (edit, save, delete, status, etc.)
│   ├── App.php         ← 🏠 APPLICATION: Terms spesifik aplikasi
│   ├── Service.php     ← 🔧 MODULE: Service-specific terms
│   ├── Marketing.php   ← 📊 MODULE: Marketing-specific terms
│   └── Warehouse.php   ← 📦 MODULE: Warehouse-specific terms
└── id/
    ├── Common.php      ← 🌍 GLOBAL: Kata umum (Indonesian)
    ├── App.php         ← 🏠 APPLICATION: Terms spesifik aplikasi (Indonesian)
    ├── Service.php     ← 🔧 MODULE: Service-specific terms (Indonesian)
    ├── Marketing.php   ← 📊 MODULE: Marketing-specific terms (Indonesian)
    └── Warehouse.php   ← 📦 MODULE: Warehouse-specific terms (Indonesian)
```

---

## 🎯 Usage Guidelines

### ✅ Common.php - Global Words (177 keys)
**Untuk kata-kata yang dipakai di SEMUA module**

#### CRUD Operations
```php
lang('Common.add')      // Tambah / Add
lang('Common.edit')     // Edit / Edit
lang('Common.delete')   // Hapus / Delete
lang('Common.save')     // Simpan / Save
lang('Common.cancel')   // Batal / Cancel
lang('Common.close')    // Tutup / Close
lang('Common.submit')   // Kirim / Submit
lang('Common.refresh')  // Refresh / Refresh
lang('Common.export')   // Ekspor / Export
lang('Common.filter')   // Filter / Filter
lang('Common.search')   // Cari / Search
```

#### Status & States
```php
lang('Common.active')      // Aktif / Active
lang('Common.inactive')    // Tidak Aktif / Inactive
lang('Common.pending')     // Menunggu / Pending
lang('Common.approved')    // Disetujui / Approved
lang('Common.completed')   // Selesai / Completed
lang('Common.cancelled')   // Dibatalkan / Cancelled
lang('Common.progress')    // Progress / Progress
lang('Common.closed')      // Tertutup / Closed
```

#### Common Fields
```php
lang('Common.name')        // Nama / Name
lang('Common.code')        // Kode / Code
lang('Common.date')        // Tanggal / Date
lang('Common.status')      // Status / Status
lang('Common.type')        // Tipe / Type
lang('Common.category')    // Kategori / Category
lang('Common.priority')    // Prioritas / Priority
lang('Common.location')    // Lokasi / Location
lang('Common.description') // Deskripsi / Description
lang('Common.notes')       // Catatan / Notes
```

#### Actions
```php
lang('Common.action')   // Aksi / Action
lang('Common.actions')  // Aksi / Actions
lang('Common.confirm')  // Konfirmasi / Confirm
lang('Common.verify')   // Verifikasi / Verify
lang('Common.approve')  // Setujui / Approve
lang('Common.reject')   // Tolak / Reject
```

#### Messages
```php
lang('Common.success')        // Berhasil / Success
lang('Common.error')          // Error / Error
lang('Common.loading')        // Memuat / Loading
lang('Common.please_wait')    // Mohon Tunggu / Please Wait
lang('Common.no_data')        // Tidak Ada Data / No Data
lang('Common.are_you_sure')   // Apakah Anda yakin? / Are you sure?
```

#### Filters
```php
lang('Common.all')            // Semua / All
lang('Common.all_status')     // Semua Status / All Status
lang('Common.all_types')      // Semua Tipe / All Types
lang('Common.all_categories') // Semua Kategori / All Categories
```

---

### 🏠 App.php - Application-Specific
**Untuk terms yang spesifik ke aplikasi Optima**

```php
lang('App.dashboard')
lang('App.unit')
lang('App.customer')
lang('App.supplier')
lang('App.department')
lang('App.division')
lang('App.employee')
lang('App.serial_number')
lang('App.model')
lang('App.brand')
// etc... application-specific terms
```

---

### 🔧 Module.php - Module-Specific
**Untuk terms yang spesifik ke module tertentu**

#### Service.php
```php
lang('Service.work_order')
lang('Service.maintenance')
lang('Service.repair')
lang('Service.service_area')
lang('Service.mechanic')
```

#### Marketing.php
```php
lang('Marketing.quotation')
lang('Marketing.proposal')
lang('Marketing.lead')
lang('Marketing.pipeline')
```

#### Warehouse.php
```php
lang('Warehouse.stock')
lang('Warehouse.rental')
lang('Warehouse.silo')
lang('Warehouse.delivery')
```

---

## 💡 Best Practices

### ❌ BEFORE (Redundant)
```php
// Di setiap file: App.php, Service.php, Marketing.php
'edit' => 'Edit',
'delete' => 'Hapus',
'save' => 'Simpan',
'cancel' => 'Batal',
// Duplikasi 177 kata di setiap file! 😱
```

### ✅ AFTER (Efficient)
```php
// Common.php (1x saja)
'edit' => 'Edit',
'delete' => 'Hapus',
'save' => 'Simpan',
'cancel' => 'Batal',

// App.php (specific terms only)
'unit' => 'Unit',
'customer' => 'Customer',

// Service.php (service-specific only)
'work_order' => 'Work Order',
'maintenance' => 'Maintenance',
```

---

## 🔄 Migration Strategy

### Phase 1: Use Common.php for New Code ✅
```php
// Semua code baru gunakan Common.php
<?= lang('Common.edit') ?>
<?= lang('Common.save') ?>
<?= lang('Common.delete') ?>
```

### Phase 2: Gradual Migration (Optional)
```php
// Script untuk replace mass
// From: lang('App.edit')
// To:   lang('Common.edit')
```

### Phase 3: Clean App.php
Hapus keys yang sudah ada di Common.php:
- edit, save, delete, cancel, close, etc.
- status, active, inactive, pending, etc.

---

## 📊 Benefits

### ✅ No Duplication
- 177 common keys defined **once**
- Bukan 177 × 5 files = 885 redundant keys!

### ✅ Easier Maintenance
- Update 1 kata → affect all modules
- Tidak perlu cari-cari di setiap file

### ✅ Faster Development
- Developer tahu mau cari di mana
- Common words → Common.php
- Module terms → Module.php

### ✅ Smaller Files
- App.php sekarang lebih fokus
- Hanya application-specific terms

### ✅ Consistency
- Semua module pakai kata yang sama
- "Edit" selalu "Edit", bukan "Ubah" di satu tempat, "Edit" di tempat lain

---

## 🎨 Example Implementation

### Before (Verbose)
```php
<button><?= lang('App.edit') ?></button>
<button><?= lang('App.save') ?></button>
<button><?= lang('App.delete') ?></button>
<button><?= lang('App.cancel') ?></button>
<span><?= lang('App.status') ?></span>
<span><?= lang('App.active') ?></span>
```

### After (Clean)
```php
<button><?= lang('Common.edit') ?></button>
<button><?= lang('Common.save') ?></button>
<button><?= lang('Common.delete') ?></button>
<button><?= lang('Common.cancel') ?></button>
<span><?= lang('Common.status') ?></span>
<span><?= lang('Common.active') ?></span>

<!-- Only use App.php for specific terms -->
<span><?= lang('App.unit') ?></span>
<span><?= lang('App.customer') ?></span>
```

---

## 📝 Quick Reference Table

| Category | File | Example Keys |
|----------|------|--------------|
| **Common Words** | `Common.php` | add, edit, delete, save, cancel, close, status, active, pending, all, filter, search |
| **Application** | `App.php` | unit, customer, supplier, department, employee, quotation, contract |
| **Service Module** | `Service.php` | work_order, maintenance, repair, service_area, mechanic, foreman, supervisor |
| **Marketing Module** | `Marketing.php` | quotation, proposal, lead, pipeline, prospect, sales |
| **Warehouse Module** | `Warehouse.php` | stock, rental, silo, delivery, inventory, packing |

---

## 🚀 Action Items

### Immediate (Done ✅)
- [x] Create Common.php (ID & EN) with 177 common keys
- [x] Document structure and usage

### Next Steps (Recommended)
- [ ] Update top 5 files to use `lang('Common.xxx')`
- [ ] Create script to help migrate from `App.xxx` → `Common.xxx`
- [ ] Clean duplicate keys from App.php
- [ ] Create module-specific files (Service.php, Marketing.php, Warehouse.php)

---

## 📖 Developer Guide

### When to use which file?

#### Use `Common.php` when:
✅ Kata dipakai di **banyak module** (3+ modules)
✅ Kata general seperti: edit, save, delete, status, active
✅ Standard CRUD operations
✅ Standard field names: name, code, date, description

#### Use `App.php` when:
✅ Term spesifik ke **aplikasi** tapi tidak spesifik ke module
✅ Contoh: unit, customer, supplier, department, division

#### Use `Module.php` when:
✅ Term **sangat spesifik** ke module tertentu
✅ Contoh: work_order (Service), quotation (Marketing), silo (Warehouse)

---

**Last Updated:** December 23, 2024
**Total Common Keys:** 177 (ID + EN)
**Estimated Duplication Saved:** ~885 redundant keys!
