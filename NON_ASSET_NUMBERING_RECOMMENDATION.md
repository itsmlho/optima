# Rekomendasi: Sistem Penomoran untuk Unit Non-Asset

## 📊 Analisis Situasi

### Current Database Structure
```sql
CREATE TABLE inventory_unit (
  id_inventory_unit INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  no_unit INT UNSIGNED DEFAULT NULL,  -- Nullable, untuk unit Asset saja
  serial_number VARCHAR(255),
  status_unit_id INT,
  ...
)
```

### Status Unit Mapping
- **Status 7**: STOCK ASET (Asset) → `no_unit` diisi dengan nomor aset
- **Status 8**: STOCK NON ASET (Non-Asset) → `no_unit` saat ini NULL

### Problem Statement
- Di lapangan, unit Non-Asset **juga diberikan nomor** untuk tracking
- Kolom `no_unit` saat ini NULL untuk Non-Asset
- User khawatir kalau pakai `id_inventory_unit` akan rancu dengan ID sistem

---

## 🎯 Rekomendasi Solusi: **Prefix-Based Numbering with Gap-Filling**

### **OPSI 1: Tambah Kolom Baru `no_unit_na` (Non-Asset Number)** ⭐ **RECOMMENDED**

#### Key Features
- ✅ **Format Sederhana**: NA-001, NA-002, ..., NA-500
- ✅ **Max Capacity**: 500 nameplates (controlled growth)
- ✅ **Gap Filling**: Reuse nomor yang kosong saat unit convert ke Asset
- ✅ **Compact Numbering**: Tidak ada "lompatan" nomor yang besar
- ✅ **Clear Separation**: Asset vs Non-Asset jelas berbeda

#### Logic Flow
```
1. Unit baru masuk sebagai Non-Asset
   ↓
2. System scan NA-001 to NA-500
   ↓
3. Cari nomor terkecil yang KOSONG
   ↓
4. Assign nomor tersebut (e.g., NA-009 if it's free)
   ↓
5. If no gaps, assign next sequential (e.g., NA-011)
   ↓
6. If all 500 slots full → ERROR (capacity reached)
```

#### Gap Creation & Reuse
```
Unit NA-009 convert to Asset → no_unit_na = NULL
                             ↓
                   NA-009 becomes AVAILABLE
                             ↓
              Next unit will get NA-009 (reused!)
```

#### Struktur Database
```sql
ALTER TABLE inventory_unit 
ADD COLUMN no_unit_na VARCHAR(50) DEFAULT NULL 
COMMENT 'Nomor unit untuk Non-Asset (contoh: NA-001, NA-002)' 
AFTER no_unit;

-- Create unique index
CREATE UNIQUE INDEX idx_no_unit_na ON inventory_unit(no_unit_na);
```

#### Keuntungan
✅ **Jelas terpisah**: Asset punya `no_unit` (angka), Non-Asset punya `no_unit_na` (string dengan prefix)  
✅ **Tidak rancu**: User bisa langsung tahu NA-001 = Non-Asset, sedangkan 001 = Asset  
✅ **Backward compatible**: Tidak mengubah struktur existing `no_unit`  
✅ **Flexible**: Bisa pakai prefix berbeda per departemen (NA-D-001 untuk Diesel Non-Asset, NA-E-001 untuk Electric)  
✅ **Easy to migrate**: Data existing tidak perlu diubah  
✅ **Query friendly**: `WHERE no_unit_na IS NOT NULL` untuk Non-Asset, `WHERE no_unit IS NOT NULL` untuk Asset

#### Display Logic
```php
// Di View/Controller
public function getDisplayNumber($unit) {
    if ($unit['status_unit_id'] == 7 && $unit['no_unit']) {
        // Asset number
        return "FL-" . str_pad($unit['no_unit'], 3, '0', STR_PAD_LEFT);  // FL-001
    } elseif ($unit['status_unit_id'] == 8 && $unit['no_unit_na']) {
        // Non-Asset number
        return $unit['no_unit_na'];  // NA-001
    } else {
        // Belum diberi nomor, pakai ID sementara
        return "TEMP-" . $unit['id_inventory_unit'];  // TEMP-20
    }
}
```

#### Auto-Generate Pattern untuk Non-Asset (FILL THE GAPS Strategy)
```php
// Di Model atau Controller
public function generateNonAssetNumber() {
    $db = \Config\Database::connect();
    
    // STRATEGY: Fill the gaps first, then sequential
    // Max capacity: NA-500 (500 nameplates)
    $maxCapacity = 500;
    
    // Get all existing non-asset numbers
    $existingNumbers = $db->table('inventory_unit')
        ->select('no_unit_na')
        ->where('no_unit_na IS NOT NULL')
        ->where('no_unit_na LIKE "NA-%"')
        ->orderBy('no_unit_na', 'ASC')
        ->get()
        ->getResultArray();
    
    // Extract numeric parts
    $usedNumbers = [];
    foreach ($existingNumbers as $row) {
        if (preg_match('/NA-(\d+)/', $row['no_unit_na'], $matches)) {
            $usedNumbers[] = (int) $matches[1];
        }
    }
    
    // Find the first available gap (1 to maxCapacity)
    for ($i = 1; $i <= $maxCapacity; $i++) {
        if (!in_array($i, $usedNumbers)) {
            // Found a gap! Use this number
            return "NA-" . str_pad($i, 3, '0', STR_PAD_LEFT);
        }
    }
    
    // All slots are full (NA-001 to NA-500)
    throw new \Exception("Kapasitas nomor Non-Asset penuh (maksimal {$maxCapacity} unit). Silakan konversi unit lama ke Asset atau hapus unit tidak terpakai.");
}

// Example usage with conversion tracking
public function convertNonAssetToAsset($unitId) {
    $unit = $this->find($unitId);
    
    if (!$unit || $unit['status_unit_id'] != 8) {
        throw new \Exception('Unit bukan Non-Asset');
    }
    
    // Vacate the non-asset number (will be reused by next unit)
    $oldNonAssetNumber = $unit['no_unit_na'];
    
    // Generate new asset number
    $newAssetNumber = $this->generateAssetNumber();
    
    // Update unit: status → Asset, clear no_unit_na, set no_unit
    $this->update($unitId, [
        'status_unit_id' => 7,  // Asset
        'no_unit_na' => null,   // Clear non-asset number (makes it available for reuse)
        'no_unit' => $newAssetNumber
    ]);
    
    log_message('info', "Unit {$unitId}: Converted from Non-Asset ({$oldNonAssetNumber}) to Asset ({$newAssetNumber})");
    
    return [
        'old_number' => $oldNonAssetNumber,
        'new_number' => $newAssetNumber
    ];
}
```

#### UI Implementation
```php
// View: Warehouse Inventory Unit
<td>
    <?php if ($unit['status_unit_id'] == 7): ?>
        <!-- Asset number -->
        <span class="badge bg-success">
            FL-<?= str_pad($unit['no_unit'], 3, '0', STR_PAD_LEFT) ?>
        </span>
    <?php elseif ($unit['status_unit_id'] == 8 && !empty($unit['no_unit_na'])): ?>
        <!-- Non-Asset with number -->
        <span class="badge bg-warning">
            <?= $unit['no_unit_na'] ?>
        </span>
    <?php else: ?>
        <!-- No number yet -->
        <span class="badge bg-secondary">
            TEMP-<?= $unit['id_inventory_unit'] ?>
        </span>
        <button class="btn btn-sm btn-primary" onclick="assignNumber(<?= $unit['id_inventory_unit'] ?>)">
            Assign Number
        </button>
    <?php endif; ?>
</td>
```

---

### **OPSI 2: Unified Numbering dengan Prefix di `no_unit`** (Perlu Migrasi Besar)

#### Struktur Database
```sql
-- Ubah no_unit dari INT ke VARCHAR
ALTER TABLE inventory_unit 
MODIFY COLUMN no_unit VARCHAR(50) DEFAULT NULL 
COMMENT 'Nomor unit (Asset: 001, Non-Asset: NA-001)';
```

#### Keuntungan
✅ **Single source**: Semua nomor di satu kolom  
✅ **Simpler query**: Tidak perlu check dua kolom berbeda

#### Kekurangan
❌ **Breaking change**: Mengubah tipe data dari INT → VARCHAR (butuh migrasi data existing)  
❌ **Query performance**: VARCHAR lebih lambat untuk indexing dibanding INT  
❌ **Code refactor**: Semua code yang asumsi `no_unit` adalah INT harus diubah  
❌ **Risk tinggi**: Bisa break existing features yang depend on `no_unit` sebagai integer

---

### **OPSI 3: Pakai `id_inventory_unit` dengan Display Prefix** (Tidak Recommended)

#### Cara Kerja
Tidak ada perubahan database, cukup display logic di frontend:
```php
// Display saja
if ($unit['status_unit_id'] == 8) {
    echo "NA-" . str_pad($unit['id_inventory_unit'], 4, '0', STR_PAD_LEFT);  // NA-0020
}
```

#### Kekurangan
❌ **Tidak persisten**: Nomor hanya tampilan, tidak tersimpan di database  
❌ **Sequential issue**: Kalau unit ID 20 dihapus, nomor NA-0020 hilang, bikin gap  
❌ **Tidak bisa custom**: User tidak bisa assign nomor spesifik (harus ikut ID sistem)  
❌ **Rancu tetap ada**: User tetap bingung karena ID sistem bukan nomor tracking yang mereka kontrol

---

## ✅ Kesimpulan: Pilih **OPSI 1**

### Implementation Summary

#### 1. Database Migration
```sql
-- Step 1: Add new column
ALTER TABLE inventory_unit 
ADD COLUMN no_unit_na VARCHAR(50) DEFAULT NULL 
COMMENT 'Nomor unit untuk Non-Asset (contoh: NA-001)' 
AFTER no_unit;

-- Step 2: Create unique index
CREATE UNIQUE INDEX idx_no_unit_na ON inventory_unit(no_unit_na);

-- Step 3: (Optional) Populate existing non-asset units
UPDATE inventory_unit 
SET no_unit_na = CONCAT('NA-', LPAD(id_inventory_unit, 3, '0'))
WHERE status_unit_id = 8 AND no_unit_na IS NULL;
```

#### 2. Model Update (InventoryUnitModel.php)
```php
protected $allowedFields = [
    'serial_number',
    'no_unit',        // Untuk Asset
    'no_unit_na',     // Untuk Non-Asset (NEW)
    'id_po',
    // ... rest of fields
];

// Generate Non-Asset Number (Fill the Gaps Strategy)
public function generateNonAssetNumber()
{
    $maxCapacity = 500; // Max nameplate capacity: NA-001 to NA-500
    
    // Get all existing non-asset numbers
    $existingNumbers = $this->db->table('inventory_unit')
        ->select('no_unit_na')
        ->where('no_unit_na IS NOT NULL')
        ->where('no_unit_na LIKE "NA-%"')
        ->get()
        ->getResultArray();
    
    // Extract numeric parts from existing numbers
    $usedNumbers = [];
    foreach ($existingNumbers as $row) {
        if (preg_match('/NA-(\d+)/', $row['no_unit_na'], $matches)) {
            $usedNumbers[] = (int) $matches[1];
        }
    }
    
    // Find first available number (fill gaps first)
    for ($i = 1; $i <= $maxCapacity; $i++) {
        if (!in_array($i, $usedNumbers)) {
            return "NA-" . str_pad($i, 3, '0', STR_PAD_LEFT);
        }
    }
    
    // All slots full (NA-001 to NA-500)
    throw new \Exception("Kapasitas nomor Non-Asset penuh (maksimal {$maxCapacity} unit). Silakan konversi unit ke Asset atau hapus unit tidak terpakai.");
}

// Get display number for any unit
public function getDisplayNumber($unitId)
{
    $unit = $this->find($unitId);
    
    if (!$unit) {
        return null;
    }
    
    // Asset with no_unit
    if ($unit['no_unit']) {
        return "FL-" . str_pad($unit['no_unit'], 3, '0', STR_PAD_LEFT);
    }
    
    // Non-Asset with no_unit_na
    if ($unit['no_unit_na']) {
        return $unit['no_unit_na'];
    }
    
    // No number assigned yet
    return "TEMP-" . $unit['id_inventory_unit'];
}
```

#### 3. Controller Enhancement (Warehouse.php)
```php
// New function: Assign Non-Asset Number
public function assignNonAssetNumber()
{
    try {
        $id = $this->request->getPost('id');
        $model = new \App\Models\InventoryUnitModel();
        
        $unit = $model->find($id);
        
        if (!$unit) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unit tidak ditemukan'
            ]);
        }
        
        // Check if it's non-asset
        if ($unit['status_unit_id'] != 8) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unit bukan Non-Asset, tidak bisa assign nomor Non-Asset'
            ]);
        }
        
        // Check if already has number
        if ($unit['no_unit_na']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unit sudah memiliki nomor: ' . $unit['no_unit_na']
            ]);
        }
        
        // Generate new number
        $newNumber = $model->generateNonAssetNumber($unit['departemen_id']);
        
        // Update unit
        if ($model->update($id, ['no_unit_na' => $newNumber])) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Nomor Non-Asset berhasil di-assign',
                'no_unit_na' => $newNumber
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal mengupdate nomor Non-Asset'
        ]);
        
    } catch (\Exception $e) {
        log_message('error', '[Warehouse::assignNonAssetNumber] Error: ' . $e->getMessage());
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ]);
    }
}
```

#### 4. Frontend Enhancement (invent_unit.php)
```javascript
// DataTables column update
{
    data: null,
    render: function(data, type, row) {
        let display = '';
        
        if (row.no_unit) {
            // Asset number
            display = '<span class="badge bg-success">FL-' + 
                      String(row.no_unit).padStart(3, '0') + '</span>';
        } else if (row.no_unit_na) {
            // Non-Asset number
            display = '<span class="badge bg-warning">' + row.no_unit_na + '</span>';
        } else if (row.status_unit_id == 8) {
            // Non-Asset without number - show assign button
            display = '<span class="badge bg-secondary">TEMP-' + row.id_inventory_unit + '</span> ' +
                      '<button class="btn btn-xs btn-primary" onclick="assignNonAssetNumber(' + 
                      row.id_inventory_unit + ')">Assign Number</button>';
        } else {
            // Asset without number
            display = '<span class="badge bg-secondary">TEMP-' + row.id_inventory_unit + '</span>';
        }
        
        return display;
    }
}

// Function to assign non-asset number
function assignNonAssetNumber(unitId) {
    Swal.fire({
        title: 'Assign Nomor Non-Asset?',
        text: 'Nomor akan di-generate otomatis',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Assign',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('warehouse/assignNonAssetNumber') ?>',
                type: 'POST',
                data: {
                    id: unitId,
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Berhasil!', 'Nomor Non-Asset: ' + response.no_unit_na, 'success');
                        $('#inventUnitTable').DataTable().ajax.reload();
                    } else {
                        Swal.fire('Gagal', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
                }
            });
        }
    });
}
```

---

## 📊 Comparison Matrix

| Kriteria | OPSI 1 (no_unit_na) | OPSI 2 (Unified VARCHAR) | OPSI 3 (Display Only) |
|----------|---------------------|--------------------------|----------------------|
| **Database Change** | Minimal (add column) | Major (change type) | None |
| **Backward Compatibility** | ✅ 100% | ❌ Breaking change | ✅ 100% |
| **Performance Impact** | ✅ Low | ⚠️ Medium | ✅ None |
| **Data Persistence** | ✅ Yes | ✅ Yes | ❌ No |
| **User Control** | ✅ Full control | ✅ Full control | ❌ Auto-generated only |
| **Clarity** | ✅ Very clear (prefix) | ⚠️ Mixed format | ❌ Confusing with ID |
| **Migration Risk** | ✅ Low | ❌ High | ✅ None |
| **Development Effort** | ⚠️ Medium | ❌ High | ✅ Low |
| **Long-term Maintenance** | ✅ Easy | ⚠️ Medium | ❌ Problematic |

---

## 🚀 Implementation Roadmap

### Phase 1: Database Setup (15 menit)
1. ✅ Add `no_unit_na` column
2. ✅ Create unique index
3. ✅ Test migration script

### Phase 2: Model Enhancement (30 menit)
1. ✅ Add `no_unit_na` to `allowedFields`
2. ✅ Create `generateNonAssetNumber()` method
3. ✅ Create `getDisplayNumber()` method
4. ✅ Test auto-generation logic

### Phase 3: Controller & View (1 jam)
1. ✅ Add `assignNonAssetNumber()` endpoint
2. ✅ Update DataTables column rendering
3. ✅ Add JavaScript handler for assign button
4. ✅ Test UI workflow

### Phase 4: Testing & Documentation (30 menit)
1. ✅ Test assign number untuk non-asset unit
2. ✅ Test uniqueness constraint
3. ✅ Test display logic untuk asset vs non-asset
4. ✅ Document numbering convention

---

## 📝 Numbering Convention

### Asset Numbering (no_unit)
- **Format**: Numeric only (1, 2, 3, ...)
- **Display**: `FL-001`, `FL-002`, `FL-003`
- **Use Case**: Unit yang sudah dikonfirmasi sebagai Asset (status 7)

### NFormat: Simple Sequential with Gap Filling
- **Format**: `NA-001`, `NA-002`, `NA-003`, ..., `NA-500`
- **Max Capacity**: 500 nameplates (NA-001 to NA-500)
- **Strategy**: Fill the gaps first (reuse vacated numbers)
- **Example**: 
  - Existing: NA-001, NA-002, NA-003, NA-005
  - Next unit: NA-004 (fills the gap)
  - If NA-003 converts to Asset → NA-003 becomes available again
- **Use Case**: Compact numbering dengan max capacity control
- **Use Case**: Tracking berdasarkan departemen untuk inventory management lebih baik

---

## 📊 Gap-Filling Strategy Example

### Scenario 1: Normal Sequential Assignment
```
Current State:
- NA-001 (Unit ID 20)
- NA-002 (Unit ID 21)
- NA-003 (Unit ID 22)

New unit masuk → Gets NA-004
```

### Scenario 2: Gap Created by Asset Conversion
```
Initial State:
- NA-001 (Unit ID 20)
- NA-002 (Unit ID 21) ← Converts to Asset
- NA-003 (Unit ID 22)
- NA-004 (Unit ID 23)

After Conversion:
- NA-001 (Unit ID 20)
- [GAP] ← NA-002 is now FREE
- NA-003 (Unit ID 22)
- NA-004 (Unit ID 23)

New unit masuk → Gets NA-002 (fills the gap!)
```

### Scenario 3: Multiple Gaps
```
Current State:
- NA-001 (Unit ID 20)
- [GAP] ← NA-002 available
- NA-003 (Unit ID 22)
- [GAP] ← NA-004 available
- NA-005 (Unit ID 24)

New unit masuk → Gets NA-002 (smallest available number)
After: Gets NA-004 (next smallest)
After: Gets NA-006 (sequential after NA-005)
```

### Scenario 4: Max Capacity Reached
```
Current State:
- NA-001 to NA-500 (all occupied)
- Total: 500 nameplates

New unit masuk → ERROR: "Kapasitas penuh, silakan konversi unit lama ke Asset"

After converting NA-010 to Asset:
- NA-010 becomes FREE

New unit masuk → Gets NA-010 ✅
```

---

## 🔄 Conversion Workflow

### Non-Asset → Asset Conversion
```php
// Before Conversion
Unit ID: 20
Status: STOCK NON ASET (8)
no_unit: NULL
no_unit_na: "NA-005"

// After Conversion
Unit ID: 20
Status: STOCK ASET (7)
no_unit: 123  ← New asset number
no_unit_na: NULL  ← Freed up, available for reuse!

// Result: NA-005 is now available for next Non-Asset unit
```

### Complete Conversion Function
```php
public function convertToAsset($unitId, $newAssetNumber = null)
{
    $unit = $this->find($unitId);
    
    if (!$unit || $unit['status_unit_id'] != 8) {
        throw new \Exception('Unit bukan Non-Asset');
    }
    
    // Auto-generate asset number if not provided
    if (!$newAssetNumber) {
        $newAssetNumber = $this->generateAssetNumber();
    }
    
    $oldNonAssetNumber = $unit['no_unit_na'];
    
    // Update: Clear no_unit_na (makes it available), set no_unit, change status
    $this->update($unitId, [
        'status_unit_id' => 7,        // STOCK ASET
        'no_unit' => $newAssetNumber,  // Asset number
        'no_unit_na' => null           // Clear (now available for reuse)
    ]);
    
    log_message('info', "Unit {$unitId}: {$oldNonAssetNumber} → FL-{$newAssetNumber} (converted to Asset)");
    
    return [
        'success' => true,
        'old_number' => $oldNonAssetNumber,
        'new_number' => "FL-{$newAssetNumber}",
        'freed_number' => $oldNonAssetNumber . " is now available for reuse"
    ];
}
```

---

## ✅ Final RecommendationGap-Filling Strategy:**

1. **Database**: Add `no_unit_na VARCHAR(50)` dengan unique index
2. **Auto-generation**: Format `NA-{Sequential}` dengan **fill gaps first** logic
3. **Max Capacity**: 500 nameplates (NA-001 to NA-500)
4. **Reuse Strategy**: Nomor yang kosong (unit jadi Asset) akan dipakai lagi
5. **UI**: Show assign button untuk Non-Asset tanpa nomor
6. **Display**: Badge berbeda untuk Asset (green) vs Non-Asset (yellow)
7. **Validation**: Ensure uniqueness + capacity check

**Keuntungan Utama:**
- ✅ Jelas membedakan Asset vs Non-Asset
- ✅ Tidak mengubah existing data
- ✅ Low risk implementation
- ✅ User-friendly numbering
- ✅ Compact numbering (no gaps)
- ✅ Max capacity control (500 nameplates)
- ✅ Reuse vacated numbers data
- ✅ Low risk implementation
- ✅ User-friendly numbering
- ✅ Easy maintenance

---

**Status:** 📋 Ready for Implementation  
**Estimated Time:** 2-3 jam  
**Risk Level:** 🟢 Low  
**Impact:** 🟢 High (solve real field problem)
