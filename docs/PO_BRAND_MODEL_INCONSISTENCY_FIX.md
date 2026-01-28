# 🔧 Perbaikan Inkonsistensi Brand & Model di PO System

**Tanggal:** 27 Januari 2026
**Dibuat untuk:** Issue inkonsistensi data Brand antara Create PO, Print Packing List, dan PO Verification

---

## 📋 **Ringkasan Masalah**

Terdapat inkonsistensi pada field **Brand (merk_unit)** antara 3 halaman:

| Halaman | URL | Status Brand | Status Model |
|---------|-----|-------------|--------------|
| **Create PO** | `/public/purchasing` | ✅ Berfungsi | ✅ Berfungsi |
| **Print Packing List** | `/public/purchasing/print-packing-list` | ❌ Tidak muncul | ❌ Tidak muncul |
| **PO Verification** | `/public/warehouse/purchase-orders/wh-verification` | ❌ Tidak muncul | ❌ Tidak muncul |

### Gejala:
- Saat create PO, dropdown Brand dan Model berfungsi dengan baik (cascading)
- Saat print packing list dan verification, kolom DATABASE untuk Brand dan Model **kosong**
- Padahal data sudah tersimpan di database

---

## 🔍 **Root Cause Analysis**

### **Struktur Database Saat Ini:**

#### Tabel `po_units`:
```sql
CREATE TABLE po_units (
    id_po_unit INT PRIMARY KEY AUTO_INCREMENT,
    po_id INT,
    merk_unit INT,              -- ❗ INI ADALAH ID (FOREIGN KEY), BUKAN NAMA MERK
    model_unit_id INT,          -- Foreign key ke model_unit.id_model_unit
    tipe_unit_id INT,
    ...
)
```

#### Tabel `model_unit`:
```sql
CREATE TABLE model_unit (
    id_model_unit INT PRIMARY KEY AUTO_INCREMENT,
    merk_unit VARCHAR(100),     -- ❗ INI ADALAH NAMA MERK (BUKAN ID)
    model_unit VARCHAR(255)     -- Nama model
)
```

### **Masalah di Code:**

#### 1. **Create PO (Purchasing.php - Line 2868)** ✅ BENAR
```php
'merk_unit' => $this->request->getPost('merk_unit'), // Menyimpan ID merk
```
- Dropdown Brand mengirim ID dari `model_unit.id_model_unit`
- ID tersebut disimpan di `po_units.merk_unit`
- **Ini BENAR** sesuai validasi model yang mendefinisikan `merk_unit` sebagai INTEGER

#### 2. **Print Packing List (Purchasing.php - Line 633)** ❌ SALAH
```php
$unitSpec = $db->table('po_units pu')
    ->select('..., mu.merk_unit, mu.model_unit')
    ->join('model_unit mu', 'mu.id_model_unit = pu.model_unit_id', 'left')
    // ❌ TIDAK ADA JOIN untuk pu.merk_unit!
```
**Problem:**
- Query join dengan `model_unit` berdasarkan `pu.model_unit_id`
- Tapi `mu.merk_unit` yang diambil adalah field VARCHAR di tabel `model_unit`
- **TIDAK ada join** untuk mengambil nama brand dari `pu.merk_unit` (yang adalah ID)

#### 3. **PO Verification (WarehousePO.php - Line 107)** ❌ SAMA
```php
$dataUnit = $this->pounitsmodel
    ->select('..., mu.model_unit, mu.merk_unit')
    ->join('model_unit mu', 'mu.id_model_unit = po_units.model_unit_id', 'left')
    // ❌ TIDAK ADA JOIN untuk po_units.merk_unit!
```
**Problem yang sama:** Join hanya untuk model, tidak untuk brand

---

## 🎯 **Skenario Kasus Nyata**

### **Data di Database:**
```
po_units:
- id_po_unit = 123
- merk_unit = 5        ← ID dari model_unit where merk_unit='CLARK'
- model_unit_id = 42   ← ID dari model_unit where model_unit='GPX15'

model_unit:
- id_model_unit = 5
- merk_unit = 'DOOSAN'
- model_unit = 'D25G-4.710M'

- id_model_unit = 42
- merk_unit = 'CLARK'
- model_unit = 'GPX15'
```

### **Query yang Salah:**
```sql
SELECT mu.merk_unit, mu.model_unit
FROM po_units pu
LEFT JOIN model_unit mu ON mu.id_model_unit = pu.model_unit_id
WHERE pu.id_po_unit = 123
```

**Hasil:**
```
merk_unit = 'CLARK'     ← Dari model_unit row ke-42
model_unit = 'GPX15'    ← Dari model_unit row ke-42
```

Tapi user menyimpan `pu.merk_unit = 5` (DOOSAN), bukan dari row ke-42!

---

## ✅ **Solusi yang Direkomendasikan**

Ada **2 opsi** perbaikan:

### **OPSI 1: Standardisasi Field di po_units** ⭐ **RECOMMENDED**

**Ubah `po_units.merk_unit` dari INTEGER menjadi VARCHAR:**

#### A. Migration SQL:
```sql
-- Step 1: Backup data lama
ALTER TABLE po_units ADD COLUMN merk_unit_backup INT AFTER merk_unit;
UPDATE po_units SET merk_unit_backup = merk_unit;

-- Step 2: Ubah tipe data dan isi dengan nama merk
ALTER TABLE po_units MODIFY COLUMN merk_unit VARCHAR(100);

-- Step 3: Update data: ambil nama merk dari model_unit berdasarkan ID
UPDATE po_units pu
LEFT JOIN model_unit mu ON mu.id_model_unit = pu.merk_unit_backup
SET pu.merk_unit = mu.merk_unit
WHERE pu.merk_unit_backup IS NOT NULL;

-- Step 4: Hapus backup column setelah verifikasi
-- ALTER TABLE po_units DROP COLUMN merk_unit_backup;
```

#### B. Update Validation (POUnitsModel.php - Line 50):
```php
// SEBELUM:
'merk_unit' => 'required|integer',

// SESUDAH:
'merk_unit' => 'required|max_length[100]',
```

#### C. Update Create PO (Purchasing.php - Line 2868):
```php
// SEBELUM:
'merk_unit' => $this->request->getPost('merk_unit'), // ID

// SESUDAH:
// Ambil nama merk dari model_unit berdasarkan ID yang dipilih
$merkId = $this->request->getPost('merk_unit');
$merkData = $db->table('model_unit')->where('id_model_unit', $merkId)->get()->getRowArray();
$poUnitData[] = [
    ...
    'merk_unit' => $merkData['merk_unit'] ?? null, // Simpan nama merk, bukan ID
    ...
];
```

**Keuntungan:**
- ✅ Konsisten dengan field lain seperti `model_unit.merk_unit` (VARCHAR)
- ✅ Tidak perlu join tambahan di query
- ✅ Data tetap readable di database
- ✅ Lebih mudah di-query dan di-debug

**Kekurangan:**
- ❌ Denormalisasi data (brand name disimpan redundant)
- ❌ Jika nama brand berubah di master, tidak otomatis update di PO lama

---

### **OPSI 2: Tambahkan Join di Query** ⚠️ **LEBIH KOMPLEKS**

**Buat tabel `merk_unit` terpisah dan update semua query:**

#### A. Migration SQL:
```sql
-- Step 1: Buat tabel master brand
CREATE TABLE merk_unit (
    id_merk INT PRIMARY KEY AUTO_INCREMENT,
    merk_unit VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Step 2: Insert unique brands dari model_unit
INSERT INTO merk_unit (merk_unit)
SELECT DISTINCT merk_unit FROM model_unit WHERE merk_unit IS NOT NULL;

-- Step 3: Update model_unit to use FK
ALTER TABLE model_unit ADD COLUMN merk_unit_id INT AFTER id_model_unit;
UPDATE model_unit mu
JOIN merk_unit mk ON mk.merk_unit = mu.merk_unit
SET mu.merk_unit_id = mk.id_merk;

-- Step 4: Update po_units untuk gunakan FK yang benar
UPDATE po_units pu
JOIN merk_unit mk ON mk.id_merk = pu.merk_unit
SET pu.merk_unit = mk.id_merk
WHERE pu.merk_unit IS NOT NULL;

-- Step 5: Add foreign key constraints
ALTER TABLE po_units 
ADD CONSTRAINT fk_po_units_merk 
FOREIGN KEY (merk_unit) REFERENCES merk_unit(id_merk);
```

#### B. Update Query Print Packing List (Purchasing.php):
```php
$unitSpec = $db->table('po_units pu')
    ->select('
        pu.*,
        mk.merk_unit as merk_unit,          // ← TAMBAH JOIN INI
        mu.model_unit,
        ...
    ')
    ->join('merk_unit mk', 'mk.id_merk = pu.merk_unit', 'left')  // ← BARU
    ->join('model_unit mu', 'mu.id_model_unit = pu.model_unit_id', 'left')
    ->where('pu.po_id', $delivery['po_id'])
    ->get()->getRowArray();
```

#### C. Update Query PO Verification (WarehousePO.php):
```php
$dataUnit = $this->pounitsmodel
    ->select('
        po_units.*, 
        purchase_orders.no_po,
        mk.merk_unit as merk_unit,              // ← TAMBAH INI
        mu.model_unit,
        ...
    ')
    ->join('purchase_orders', 'purchase_orders.id_po = po_units.po_id')
    ->join('merk_unit mk', 'mk.id_merk = po_units.merk_unit', 'left')  // ← BARU
    ->join('model_unit mu', 'mu.id_model_unit = po_units.model_unit_id', 'left')
    ->where('po_units.status_verifikasi', 'Belum Dicek')
    ->get()->getResultArray();
```

**Keuntungan:**
- ✅ Normalisasi data (single source of truth)
- ✅ Perubahan nama brand otomatis reflect di semua PO
- ✅ Integritas data terjaga dengan FK constraint

**Kekurangan:**
- ❌ Memerlukan tabel baru dan migrasi data kompleks
- ❌ Harus update banyak query di berbagai controller
- ❌ Lebih banyak join = query lebih lambat

---

## 🚀 **Rekomendasi Final: OPSI 1**

**Alasan:**
1. ✅ **Lebih sederhana** - Hanya ubah 1 field di `po_units`
2. ✅ **Konsisten** dengan desain `model_unit` yang sudah ada
3. ✅ **Backward compatible** - PO lama masih bisa di-migrate
4. ✅ **Performance** - Tidak perlu join tambahan
5. ✅ **Maintainability** - Lebih mudah di-debug

**Catatan:**
- Brand name di PO adalah **snapshot** saat PO dibuat
- Jika brand berubah di master data, PO lama tetap pakai nama lama (ini adalah **expected behavior** untuk audit trail)

---

## 📝 **Langkah Implementasi (Opsi 1)**

### **Phase 1: Database Migration** 🔧
```sql
-- File: databases/migrations/fix_po_units_brand_field.sql

-- 1. Backup data lama
ALTER TABLE po_units ADD COLUMN merk_unit_id_backup INT AFTER merk_unit;
UPDATE po_units SET merk_unit_id_backup = merk_unit WHERE merk_unit IS NOT NULL;

-- 2. Ubah tipe data
ALTER TABLE po_units MODIFY COLUMN merk_unit VARCHAR(100);

-- 3. Update data dari model_unit
UPDATE po_units pu
LEFT JOIN model_unit mu ON mu.id_model_unit = pu.merk_unit_id_backup
SET pu.merk_unit = mu.merk_unit
WHERE pu.merk_unit_id_backup IS NOT NULL;

-- 4. Verifikasi data
SELECT 
    pu.id_po_unit,
    pu.merk_unit_id_backup as old_id,
    pu.merk_unit as new_name,
    mu.merk_unit as expected_name
FROM po_units pu
LEFT JOIN model_unit mu ON mu.id_model_unit = pu.merk_unit_id_backup
WHERE pu.merk_unit_id_backup IS NOT NULL
LIMIT 10;

-- 5. Setelah verifikasi OK, hapus backup
-- ALTER TABLE po_units DROP COLUMN merk_unit_id_backup;
```

### **Phase 2: Update Model Validation** 📄
```php
// File: app/Models/POUnitsModel.php (Line 50)

protected $validationRules = [
    'po_id'             => 'required|integer',
    'jenis_unit'        => 'permit_empty|integer',
    'merk_unit'         => 'required|max_length[100]',  // ← UBAH INI (dari integer jadi max_length)
    'model_unit_id'     => 'permit_empty|integer',
    // ... rest of the rules
];
```

### **Phase 3: Update Controller - Create PO** 🔧
```php
// File: app/Controllers/Purchasing.php (storePoUnit method - Line 2868)

// SEBELUM:
$poUnitData[] = [
    'po_id'             => $newPoId,
    'jenis_unit'        => $this->request->getPost('jenis_unit'),
    'merk_unit'         => $this->request->getPost('merk_unit'), // ID
    'model_unit_id'     => $this->request->getPost('model_unit_id'),
    // ...
];

// SESUDAH:
$db = \Config\Database::connect();
$merkId = $this->request->getPost('merk_unit'); // Ini adalah ID dari dropdown
$merkName = null;

// Ambil nama merk dari model_unit
if ($merkId) {
    $merkData = $db->table('model_unit')
        ->select('merk_unit')
        ->where('id_model_unit', $merkId)
        ->get()
        ->getRowArray();
    $merkName = $merkData['merk_unit'] ?? null;
}

$poUnitData[] = [
    'po_id'             => $newPoId,
    'jenis_unit'        => $this->request->getPost('jenis_unit'),
    'merk_unit'         => $merkName,  // ← SIMPAN NAMA MERK, BUKAN ID
    'model_unit_id'     => $this->request->getPost('model_unit_id'),
    // ...
];
```

### **Phase 4: Verifikasi Query Print Packing List** ✅
```php
// File: app/Controllers/Purchasing.php (printPackingList - Line 633)

// Query sudah OK, hanya perlu verifikasi field yang di-select:
$unitSpec = $db->table('po_units pu')
    ->select('
        pu.*,
        pu.merk_unit,              // ← INI SEKARANG SUDAH VARCHAR (NAMA MERK)
        mu.model_unit,             // ← Model dari model_unit table
        // ... other fields
    ')
    ->join('model_unit mu', 'mu.id_model_unit = pu.model_unit_id', 'left')
    ->where('pu.po_id', $delivery['po_id'])
    ->get()->getRowArray();

// Output:
// $unitSpec['merk_unit'] = 'CLARK' (dari po_units.merk_unit VARCHAR)
// $unitSpec['model_unit'] = 'GPX15' (dari model_unit.model_unit)
```

### **Phase 5: Verifikasi Query PO Verification** ✅
```php
// File: app/Controllers/WarehousePO.php (whVerification - Line 107)

// Query sudah OK:
$dataUnit = $this->pounitsmodel
    ->select('
        po_units.*, 
        po_units.merk_unit,          // ← INI SEKARANG SUDAH VARCHAR
        mu.model_unit,               // ← Model dari model_unit
        // ...
    ')
    ->join('model_unit mu', 'mu.id_model_unit = po_units.model_unit_id', 'left')
    ->where('po_units.status_verifikasi', 'Belum Dicek')
    ->get()->getResultArray();
```

---

## 🧪 **Testing Plan**

### **1. Test Database Migration:**
```sql
-- Check data sebelum migration
SELECT id_po_unit, merk_unit, model_unit_id FROM po_units LIMIT 5;

-- Run migration script
SOURCE fix_po_units_brand_field.sql;

-- Verify data setelah migration
SELECT 
    pu.id_po_unit,
    pu.merk_unit as brand_name_in_po,
    mu.merk_unit as brand_name_in_model,
    pu.merk_unit = mu.merk_unit as is_match
FROM po_units pu
LEFT JOIN model_unit mu ON mu.id_model_unit = pu.model_unit_id
LIMIT 10;
```

### **2. Test Create PO:**
- Buat PO baru dengan Brand = CLARK, Model = GPX15
- Cek database: `SELECT merk_unit FROM po_units WHERE id_po_unit = (last_insert_id)`
- Expected: `merk_unit = 'CLARK'` (VARCHAR, bukan ID)

### **3. Test Print Packing List:**
- Pilih PO yang baru dibuat
- Klik "Print Packing List"
- Expected: Kolom "Database" menampilkan **Brand = CLARK**, **Model = GPX15**

### **4. Test PO Verification:**
- Buka halaman `/warehouse/purchase-orders/wh-verification`
- Klik unit dengan Brand = CLARK
- Expected: Kolom "DATABASE" menampilkan **Brand = CLARK**, **Model = GPX15**

---

## 📌 **Files to Modify**

| File | Line | Action |
|------|------|--------|
| `databases/migrations/fix_po_units_brand_field.sql` | - | **CREATE** migration script |
| `app/Models/POUnitsModel.php` | 50 | **UPDATE** validation rule untuk `merk_unit` |
| `app/Controllers/Purchasing.php` | 2868 | **UPDATE** `storePoUnit()` - simpan nama merk bukan ID |
| `app/Controllers/Purchasing.php` | 633 | **VERIFY** query di `printPackingList()` |
| `app/Controllers/WarehousePO.php` | 107 | **VERIFY** query di `whVerification()` |

---

## ⚠️ **Rollback Plan**

Jika terjadi masalah setelah migration:

```sql
-- Restore data dari backup column
ALTER TABLE po_units MODIFY COLUMN merk_unit INT;
UPDATE po_units SET merk_unit = merk_unit_id_backup WHERE merk_unit_id_backup IS NOT NULL;

-- Restore validation di POUnitsModel.php
'merk_unit' => 'required|integer',
```

---

## 🎯 **Expected Results**

Setelah perbaikan:
- ✅ Create PO: Brand dan Model tersimpan dengan benar (nama, bukan ID)
- ✅ Print Packing List: Kolom Database menampilkan Brand dan Model yang benar
- ✅ PO Verification: Kolom Database menampilkan Brand dan Model yang benar
- ✅ Data konsisten di semua 3 halaman

---

**Author:** GitHub Copilot  
**Date:** 27 Januari 2026  
**Status:** Ready for Implementation ✅
