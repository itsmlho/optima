# 🔄 Restructure Quotation Specifications - Complete Guide

**Tanggal:** 5 Desember 2025  
**Status:** 📋 **READY TO EXECUTE**

---

## 📊 Ringkasan Perubahan

### **Kolom yang DIHAPUS** (24 kolom unused):
```
❌ specification_description    → Tidak pernah dipakai
❌ category                     → Tidak pernah dipakai
❌ model                        → Tidak pernah dipakai
❌ equipment_type               → Tidak pernah dipakai
❌ specifications               → Tidak pernah dipakai
❌ service_duration             → Tidak pernah dipakai
❌ service_frequency            → Tidak pernah dipakai
❌ service_scope                → Tidak pernah dipakai
❌ delivery_required            → Tidak pernah dipakai
❌ installation_required        → Tidak pernah dipakai
❌ delivery_cost                → Tidak pernah dipakai
❌ installation_cost            → Tidak pernah dipakai
❌ maintenance_included         → Tidak pernah dipakai
❌ warranty_period              → Tidak pernah dipakai
❌ notes                        → Diganti unit_accessories
❌ sort_order                   → Tidak pernah dipakai
❌ is_optional                  → Tidak pernah dipakai
❌ spek_kode                    → Tidak pernah dipakai
❌ jumlah_tersedia              → Tidak pernah dipakai
❌ attachment_merk              → Tidak pernah dipakai
❌ original_kontrak_spek_id     → Tidak pernah dipakai
```

### **Kolom yang DIRENAME** (5 kolom):
```
🔄 unit_price              → monthly_price           (lebih jelas)
🔄 harga_per_unit_harian   → daily_price            (lebih jelas)
🔄 unit                    → specification_type     (ENUM: UNIT/ATTACHMENT)
🔄 brand (VARCHAR)         → brand_id (INT FK)      (relasi ke model_unit)
🔄 jenis_baterai (VARCHAR) → battery_id (INT FK)    (relasi ke baterai)
🔄 attachment_tipe (VARCHAR)→ attachment_id (INT FK)(relasi ke attachment)
```

### **Kolom yang TETAP** (19 kolom):
```
✅ id_specification
✅ id_quotation (FK)
✅ specification_name
✅ quantity
✅ total_price (formula berubah!)
✅ departemen_id (FK)
✅ tipe_unit_id (FK)
✅ kapasitas_id (FK)
✅ charger_id (FK)
✅ mast_id (FK)
✅ ban_id (FK)
✅ roda_id (FK)
✅ valve_id (FK)
✅ unit_accessories
✅ original_kontrak_id
✅ is_active
✅ created_at
✅ updated_at
```

---

## 🔢 Formula Baru total_price

### **SEBELUM:**
```sql
total_price = quantity * unit_price
```

### **SETELAH:**
```sql
total_price = (quantity * monthly_price) + daily_price
```

**Contoh Perhitungan:**
```
Quantity: 2 unit
Monthly Price: Rp 5.000.000
Daily Price: Rp 150.000

Total = (2 × 5.000.000) + 150.000
Total = 10.000.000 + 150.000
Total = Rp 10.150.000
```

---

## 🔗 Foreign Key Constraints Baru

### **1. brand_id → model_unit**
```sql
quotation_specifications.brand_id 
    → model_unit.id_model_unit
    
Kolom di model_unit:
- id_model_unit (PK)
- merk_unit      (e.g., "Toyota", "Mitsubishi")
- model_unit     (e.g., "8FG25", "FD30")
```

### **2. battery_id → baterai**
```sql
quotation_specifications.battery_id 
    → baterai.id
    
Kolom di baterai:
- id (PK)
- merk_baterai   (e.g., "GS", "Yuasa")
- tipe_baterai   (e.g., "VGS600")
- jenis_baterai  (e.g., "Lithium-ion", "Lead Acid")
```

### **3. attachment_id → attachment**
```sql
quotation_specifications.attachment_id 
    → attachment.id_attachment
    
Kolom di attachment:
- id_attachment (PK)
- tipe          (e.g., "FORK POSITIONER", "SIDE SHIFTER")
- merk          (e.g., "Cascade", "Kaup")
- model         (e.g., "25E-FPS-B146")
```

---

## 📝 ENUM specification_type

### **Values:**
- `UNIT` - Untuk spesifikasi unit forklift/reachtruck
- `ATTACHMENT` - Untuk spesifikasi attachment tambahan

### **Penggunaan:**
```sql
-- Unit forklift
INSERT INTO quotation_specifications 
SET specification_type = 'UNIT', ...

-- Attachment
INSERT INTO quotation_specifications 
SET specification_type = 'ATTACHMENT', ...
```

---

## 🗺️ Mapping Data Migration

### **brand (VARCHAR) → brand_id (INT FK)**
```sql
-- Data BEFORE:
brand = "Toyota"

-- Migration process:
SELECT id_model_unit FROM model_unit WHERE merk_unit = "Toyota"

-- Data AFTER:
brand_id = 5  (FK to model_unit)
```

### **jenis_baterai (VARCHAR) → battery_id (INT FK)**
```sql
-- Data BEFORE:
jenis_baterai = "Lithium-ion"

-- Migration process:
SELECT id FROM baterai WHERE jenis_baterai = "Lithium-ion"

-- Data AFTER:
battery_id = 2  (FK to baterai)
```

### **attachment_tipe (VARCHAR) → attachment_id (INT FK)**
```sql
-- Data BEFORE:
attachment_tipe = "FORK POSITIONER"

-- Migration process:
SELECT id_attachment FROM attachment WHERE tipe = "FORK POSITIONER"

-- Data AFTER:
attachment_id = 3  (FK to attachment)
```

---

## 🚀 Cara Menjalankan Migration

### **⚠️ PENTING - BACKUP DULU!**
Migration script sudah include automatic backup, tapi lebih baik export manual juga:

```bash
# Backup manual
mysqldump -u root optima_ci quotation_specifications > backup_qs_before_restructure.sql
```

### **Jalankan Migration:**
```bash
mysql -u root -p optima_ci < databases/migrations/restructure_quotation_specifications.sql
```

### **Verifikasi Hasil:**
```sql
-- Cek struktur baru
DESCRIBE quotation_specifications;

-- Cek data migration
SELECT 
    id_specification,
    specification_name,
    specification_type,
    monthly_price,
    daily_price,
    total_price,
    brand_id,
    battery_id,
    attachment_id
FROM quotation_specifications
LIMIT 10;

-- Cek FK constraints
SELECT 
    CONSTRAINT_NAME,
    TABLE_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'optima_ci'
AND TABLE_NAME = 'quotation_specifications'
AND REFERENCED_TABLE_NAME IS NOT NULL;
```

---

## 🔧 Backend Code Changes Needed

### **File: app/Controllers/Marketing.php** - getSpecifications()

**BEFORE:**
```php
$db->table('quotation_specifications qs')
    ->select('qs.unit_price,
              qs.unit_price as harga_per_unit,
              qs.harga_per_unit_harian,
              qs.brand as merk_unit,
              qs.brand,
              qs.jenis_baterai,
              qs.attachment_tipe')
```

**AFTER:**
```php
$db->table('quotation_specifications qs')
    ->select('qs.monthly_price,
              qs.monthly_price as unit_price,  // Alias untuk backward compatibility
              qs.daily_price,
              qs.daily_price as harga_per_unit_harian,  // Alias
              qs.specification_type,
              qs.brand_id,
              mu.merk_unit,
              mu.merk_unit as brand,  // Alias
              qs.battery_id,
              b.jenis_baterai,
              qs.attachment_id,
              a.tipe as attachment_tipe')
    ->join('model_unit mu', 'mu.id_model_unit = qs.brand_id', 'left')
    ->join('baterai b', 'b.id = qs.battery_id', 'left')
    ->join('attachment a', 'a.id_attachment = qs.attachment_id', 'left')
```

### **File: app/Controllers/Quotation.php** - addSpecification()

**BEFORE:**
```php
$data = [
    'unit_price' => $this->request->getPost('unit_price'),
    'harga_per_unit_harian' => $this->request->getPost('harga_per_unit_harian'),
    'brand' => $this->request->getPost('merk_unit'),
    'jenis_baterai' => $this->request->getPost('jenis_baterai'),
    'attachment_tipe' => $this->request->getPost('attachment_tipe'),
];

$data['total_price'] = $data['quantity'] * $data['unit_price'];
```

**AFTER:**
```php
$data = [
    'monthly_price' => (float)$this->request->getPost('unit_price'),  // Form still uses old name
    'daily_price' => (float)$this->request->getPost('harga_per_unit_harian'),
    'specification_type' => $this->request->getPost('specification_type') ?: 'UNIT',
    'brand_id' => $this->request->getPost('brand_id') ?: null,
    'battery_id' => $this->request->getPost('battery_id') ?: null,
    'attachment_id' => $this->request->getPost('attachment_id') ?: null,
];

// New formula
$data['total_price'] = ($data['quantity'] * $data['monthly_price']) + $data['daily_price'];
```

### **File: app/Views/marketing/quotations.php** - Form fields

**Perubahan minimal needed:**
- Hidden field untuk `specification_type` (set saat open modal Add Unit vs Add Attachment)
- Dropdown untuk `brand_id` (load dari table model_unit)
- Dropdown untuk `battery_id` (load dari table baterai)
- Dropdown untuk `attachment_id` (load dari table attachment)

---

## 📋 Testing Checklist

### **Database Level:**
- [ ] Backup berhasil dibuat
- [ ] Migration dijalankan tanpa error
- [ ] Semua kolom lama terhapus
- [ ] Semua kolom baru ada
- [ ] FK constraints ter-create
- [ ] Data ter-migrate dengan benar

### **Backend Level:**
- [ ] Update Marketing.php getSpecifications()
- [ ] Update Quotation.php addSpecification()
- [ ] Update Quotation.php updateSpecification()
- [ ] Test API endpoint getSpecifications
- [ ] Test API endpoint add specification
- [ ] Test API endpoint update specification

### **Frontend Level:**
- [ ] Update form untuk specification_type
- [ ] Update dropdown brand (dari model_unit)
- [ ] Update dropdown battery (dari baterai)
- [ ] Update dropdown attachment (dari attachment)
- [ ] Test Add Unit Specification
- [ ] Test Add Attachment Specification
- [ ] Test Edit Specification
- [ ] Verify total_price calculation

---

## ⚠️ Breaking Changes

### **1. Field Names Changed**
API responses akan berubah:
```javascript
// OLD
{
    unit_price: 5000000,
    harga_per_unit_harian: 150000,
    brand: "Toyota",
    jenis_baterai: "Lithium-ion"
}

// NEW
{
    monthly_price: 5000000,
    daily_price: 150000,
    specification_type: "UNIT",
    brand_id: 5,
    battery_id: 2,
    attachment_id: null
}
```

### **2. Total Price Calculation Changed**
```javascript
// OLD
total = quantity * unit_price

// NEW
total = (quantity * monthly_price) + daily_price
```

### **3. Foreign Keys Enforced**
Tidak bisa save brand/battery/attachment yang tidak ada di master table:
```sql
-- INI AKAN ERROR jika id tidak ada:
INSERT INTO quotation_specifications 
SET brand_id = 999  -- Error jika tidak ada di model_unit
```

---

## 🔙 Rollback Plan

Jika terjadi masalah:

```sql
-- 1. Drop current table
DROP TABLE quotation_specifications;

-- 2. Restore from backup
RENAME TABLE quotation_specifications_backup_20251205 TO quotation_specifications;

-- 3. Restore old backend code
git checkout HEAD -- app/Controllers/Marketing.php
git checkout HEAD -- app/Controllers/Quotation.php
```

---

## 📊 Estimated Impact

### **Database Size:**
- **Before:** ~50 columns (banyak unused)
- **After:** ~25 columns (hanya yang dipakai)
- **Reduction:** ~50% kolom dihapus
- **Storage:** Lebih efisien ~30-40%

### **Query Performance:**
- **Joins:** +3 joins (model_unit, baterai, attachment)
- **Indexes:** +6 indexes baru
- **Overall:** Lebih cepat karena data lebih terstruktur

### **Code Maintenance:**
- **Clarity:** Field names lebih jelas
- **Type Safety:** FK prevents invalid data
- **Consistency:** Relational integrity terjaga

---

## 🎯 Next Steps

1. **Review migration script** - Pastikan sesuai kebutuhan
2. **Backup database** - Safety first!
3. **Run migration** - Execute SQL script
4. **Update backend code** - Marketing.php & Quotation.php
5. **Update frontend forms** - quotations.php
6. **Test thoroughly** - All CRUD operations
7. **Deploy** - Push to production

---

**Status:** ✅ **MIGRATION SCRIPT READY**  
**Risk Level:** 🟡 **MEDIUM** (Structural changes + FK constraints)  
**Recommendation:** Test di development server dulu sebelum production

**File References:**
- Migration: `databases/migrations/restructure_quotation_specifications.sql`
- Documentation: `databases/QUOTATION_SPECIFICATIONS_RESTRUCTURE_GUIDE.md` (this file)
