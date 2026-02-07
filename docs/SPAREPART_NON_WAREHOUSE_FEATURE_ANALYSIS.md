# 📋 Analisa & Rekomendasi: Fitur Sparepart Non-Warehouse (Bekas)

## 🎯 Kebutuhan Bisnis

**Konteks**: Pada Work Order, teknisi sering membawa sparepart yang **bukan dari warehouse** (sparepart bekas/reuse) yang tidak perlu dicatat dalam inventory gudang.

**Problem**: 
- Saat ini semua sparepart dianggap dari warehouse
- Tidak ada pembeda antara sparepart warehouse vs sparepart bekas
- Laporan sparepart usage tidak akurat karena mencampur warehouse stock dengan non-warehouse item

---

## 🔍 Analisa Struktur Saat Ini

### 1. **Database Structure**

**Table: `work_order_spareparts`**
```sql
| Field               | Type          | Catatan                    |
|---------------------|---------------|----------------------------|
| id                  | int           | PK                         |
| work_order_id       | int           | FK to work_orders          |
| sparepart_code      | varchar(50)   | Kode sparepart             |
| sparepart_name      | varchar(255)  | Nama sparepart             |
| quantity_brought    | int           | Qty yang dibawa            |
| satuan              | varchar(50)   | Unit (PCS, KG, dll)        |
| notes               | text          | Catatan                    |
| quantity_used       | decimal(10,2) | Qty yang dipakai (validasi)|
| is_additional       | tinyint(1)    | Flag tambahan              |
| sparepart_validated | tinyint(1)    | Sudah divalidasi?          |
| created_at          | timestamp     |                            |
| updated_at          | timestamp     |                            |
```

**🔴 MISSING**: Tidak ada field untuk menandai sparepart **non-warehouse**

### 2. **Form Input - New Work Order**

**Location**: `app/Views/service/work_orders.php` (line 430-530)

**Current Fields**:
- Sparepart Name (dropdown searchable dengan Select2)
- Quantity (number)
- Unit (dropdown: PCS, KG, LITER, dll)
- Action (remove button)

**🔴 MISSING**: Checkbox/toggle untuk menandai "Non-Warehouse" atau "Bekas"

### 3. **Proses Terdampak**

#### A. **Print SPK** (`app/Views/service/print_work_order.php`)
- Menampilkan daftar sparepart yang dibawa
- **Impact**: Perlu visual indicator untuk sparepart non-warehouse

#### B. **Sparepart Validation** (`app/Views/service/sparepart_validation.php`)
- Validasi qty used vs qty brought
- Create return records untuk sparepart yang tersisa
- **Impact**: Sparepart non-warehouse **TIDAK PERLU** di-return ke warehouse

#### C. **Laporan Sparepart Usage** (`app/Controllers/Warehouse/SparepartUsageController.php`)
- Menghitung total usage dari warehouse
- **Impact**: Harus filter exclude sparepart non-warehouse untuk akurasi stock

---

## 💡 Rekomendasi Implementasi

### **OPSI 1: Simple Checkbox (RECOMMENDED) ⭐**

#### **A. Database Migration**

**File**: `databases/migrations/add_is_from_warehouse_to_spareparts.sql`

```sql
-- Add new column to work_order_spareparts
ALTER TABLE work_order_spareparts 
ADD COLUMN is_from_warehouse TINYINT(1) DEFAULT 1 
COMMENT '1=From Warehouse, 0=Non-Warehouse (bekas/reuse)' 
AFTER is_additional;

-- Add index for reporting
CREATE INDEX idx_from_warehouse ON work_order_spareparts(is_from_warehouse);

-- Update existing records (default semua dari warehouse)
UPDATE work_order_spareparts SET is_from_warehouse = 1 WHERE is_from_warehouse IS NULL;
```

**Reasoning**: 
- Default `1` (from warehouse) untuk backward compatibility
- Explicit naming: `is_from_warehouse` lebih jelas daripada `is_used` atau `is_bekas`

#### **B. Model Update**

**File**: `app/Models/WorkOrderSparepartModel.php`

```php
protected $allowedFields = [
    'work_order_id',
    'sparepart_code',
    'sparepart_name',
    'quantity_brought',
    'satuan',
    'notes',
    'quantity_used',
    'is_additional',
    'is_from_warehouse',  // ← NEW
    'sparepart_validated'
];

protected $validationRules = [
    // ... existing rules ...
    'is_from_warehouse' => 'permit_empty|in_list[0,1]'
];
```

#### **C. Form UI - New Work Order**

**File**: `app/Views/service/work_orders.php`

**Current HTML (line 440-450)**:
```html
<thead>
    <tr>
        <th width="50%">Sparepart Name*</th>
        <th width="20%">Quantity*</th>
        <th width="20%">Unit*</th>
        <th width="10%">Action</th>
    </tr>
</thead>
```

**IMPROVED HTML**:
```html
<thead>
    <tr>
        <th width="40%">Sparepart Name*</th>
        <th width="15%">Quantity*</th>
        <th width="15%">Unit*</th>
        <th width="20%">Source</th>  <!-- NEW -->
        <th width="10%">Action</th>
    </tr>
</thead>
```

**JavaScript Update** (line 3329 - `addSparepartRow` function):

```javascript
addSparepartRow = function(sparepartData = null) {
    sparepartRowCount++;
    
    const row = `
        <tr>
            <td>
                <select class="form-select" name="sparepart_name[]" id="sparepart_${sparepartRowCount}" required>
                    <option value="">-- Select Sparepart --</option>
                </select>
            </td>
            <td>
                <input type="number" class="form-control" name="sparepart_quantity[]" value="1" min="1" required>
            </td>
            <td>
                <select class="form-select form-select-sm" name="sparepart_unit[]" required>
                    <!-- ... existing unit options ... -->
                </select>
            </td>
            <td>
                <!-- NEW: Source Indicator -->
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" 
                           name="is_from_warehouse[]" 
                           id="warehouse_${sparepartRowCount}" 
                           value="1" 
                           checked 
                           onchange="toggleSourceLabel(this)">
                    <label class="form-check-label" for="warehouse_${sparepartRowCount}">
                        <span class="badge bg-success warehouse-badge">
                            <i class="fas fa-warehouse me-1"></i>Warehouse
                        </span>
                        <span class="badge bg-warning non-warehouse-badge d-none">
                            <i class="fas fa-recycle me-1"></i>Bekas/Reuse
                        </span>
                    </label>
                </div>
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm removeSparepartRow">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        </tr>
    `;
    
    $('#sparepartTableBody').append(row);
    // ... rest of function ...
};

// Toggle label function
function toggleSourceLabel(checkbox) {
    const row = $(checkbox).closest('tr');
    const warehouseBadge = row.find('.warehouse-badge');
    const nonWarehouseBadge = row.find('.non-warehouse-badge');
    
    if (checkbox.checked) {
        warehouseBadge.removeClass('d-none');
        nonWarehouseBadge.addClass('d-none');
    } else {
        warehouseBadge.addClass('d-none');
        nonWarehouseBadge.removeClass('d-none');
    }
}
```

**CSS Addition**:
```css
.form-switch .form-check-input {
    width: 3em;
    height: 1.5em;
}

.warehouse-badge {
    font-size: 0.75rem;
    font-weight: 500;
}

.non-warehouse-badge {
    font-size: 0.75rem;
    font-weight: 500;
}
```

#### **D. Controller Update - Store Work Order**

**File**: `app/Controllers/WorkOrderController.php` (store method, around line 800)

```php
public function store()
{
    try {
        // ... existing code ...
        
        // Handle spareparts
        $sparepartNames = $this->request->getPost('sparepart_name');
        $sparepartQuantities = $this->request->getPost('sparepart_quantity');
        $sparepartUnits = $this->request->getPost('sparepart_unit');
        $isFromWarehouse = $this->request->getPost('is_from_warehouse'); // ← NEW
        
        if (!empty($sparepartNames) && is_array($sparepartNames)) {
            $spareparts = [];
            foreach ($sparepartNames as $index => $name) {
                if (!empty($name)) {
                    $spareparts[] = [
                        'sparepart_name' => $name,
                        'sparepart_code' => $this->generateSparepartCode($name),
                        'quantity_brought' => $sparepartQuantities[$index] ?? 1,
                        'satuan' => $sparepartUnits[$index] ?? 'PCS',
                        'is_from_warehouse' => isset($isFromWarehouse[$index]) ? 1 : 0 // ← NEW
                    ];
                }
            }
            
            if (!empty($spareparts)) {
                $this->sparepartModel->addSpareparts($workOrderId, $spareparts);
            }
        }
        
        // ... rest of code ...
    }
}
```

#### **E. Print SPK - Visual Indicator**

**File**: `app/Views/service/print_work_order.php`

**Current**:
```html
<tr>
    <td>{{ sparepart_name }}</td>
    <td>{{ quantity_brought }}</td>
    <td>{{ satuan }}</td>
</tr>
```

**IMPROVED**:
```html
<tr>
    <td>
        {{ sparepart_name }}
        <?php if ($sparepart['is_from_warehouse'] == 0): ?>
            <span class="badge badge-warning" style="font-size: 0.7rem;">
                <i class="fas fa-recycle"></i> NON-WH
            </span>
        <?php endif; ?>
    </td>
    <td>{{ quantity_brought }}</td>
    <td>{{ satuan }}</td>
</tr>
```

#### **F. Sparepart Validation - Skip Return for Non-Warehouse**

**File**: `app/Controllers/WorkOrderController.php` (saveSparepartValidation method, line 3897)

**Current Logic**:
```php
foreach ($usedSpareparts as $sparepart) {
    // ... validasi qty ...
    
    // ALWAYS create return record
    if ($quantityReturn > 0) {
        $returnModel->insert([
            'work_order_id' => $workOrderId,
            'quantity_return' => $quantityReturn,
            // ...
        ]);
    }
}
```

**IMPROVED Logic**:
```php
foreach ($usedSpareparts as $sparepart) {
    $originalSparepart = $db->table('work_order_spareparts')
        ->where('id', $sparepart['id'])
        ->where('work_order_id', $workOrderId)
        ->get()
        ->getRowArray();
    
    if ($originalSparepart) {
        $isFromWarehouse = (int)($originalSparepart['is_from_warehouse'] ?? 1);
        $quantityBrought = (int)($originalSparepart['quantity_brought'] ?? 0);
        $quantityUsed = (int)($sparepart['used_quantity'] ?? 0);
        $quantityReturn = $quantityBrought - $quantityUsed;
        
        // Update used quantity
        $updateData = ['quantity_used' => $quantityUsed];
        $db->table('work_order_spareparts')
            ->where('id', $sparepart['id'])
            ->update($updateData);
        
        // ✅ ONLY create return record if FROM WAREHOUSE
        if ($quantityReturn > 0 && $isFromWarehouse == 1) {
            $returnModel->insert([
                'work_order_id' => $workOrderId,
                'work_order_sparepart_id' => $sparepart['id'],
                'quantity_return' => $quantityReturn,
                'sparepart_code' => $originalSparepart['sparepart_code'],
                'sparepart_name' => $originalSparepart['sparepart_name'],
                'satuan' => $originalSparepart['satuan'],
                'quantity_brought' => $quantityBrought,
                'quantity_used' => $quantityUsed,
                'status' => 'PENDING'
            ]);
            
            log_message('info', "Created return record for warehouse sparepart: {$originalSparepart['sparepart_name']}");
        } else if ($quantityReturn > 0 && $isFromWarehouse == 0) {
            log_message('info', "Skipped return for non-warehouse sparepart: {$originalSparepart['sparepart_name']}");
        }
    }
}
```

#### **G. Laporan Sparepart Usage - Filter Option**

**File**: `app/Controllers/Warehouse/SparepartUsageController.php`

**Add Filter Method**:
```php
public function index()
{
    // ... existing code ...
    
    $data = [
        'title' => 'Sparepart Usage & Returns | OPTIMA',
        // ... existing data ...
        'stats' => [
            // Total dari warehouse (exclude non-warehouse)
            'usage_total_warehouse' => $db->table('work_order_spareparts')
                ->where('quantity_used >', 0)
                ->where('is_from_warehouse', 1)  // ← FILTER
                ->countAllResults(),
            
            // Total non-warehouse (for reference)
            'usage_total_non_warehouse' => $db->table('work_order_spareparts')
                ->where('quantity_used >', 0)
                ->where('is_from_warehouse', 0)  // ← FILTER
                ->countAllResults(),
            
            // Existing stats...
        ]
    ];
    
    return view('warehouse/sparepart_usage', $data);
}
```

**View Update** (`app/Views/warehouse/sparepart_usage.php`):
```html
<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6>Total Usage (Warehouse)</h6>
                <h2><?= number_format($stats['usage_total_warehouse']) ?></h2>
                <small>Items from warehouse stock</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <h6>Total Usage (Non-Warehouse)</h6>
                <h2><?= number_format($stats['usage_total_non_warehouse']) ?></h2>
                <small>Bekas/Reuse items</small>
            </div>
        </div>
    </div>
    <!-- ... rest of stats ... -->
</div>

<!-- DataTable with filter -->
<div class="mb-3">
    <label for="sourceFilter">Filter by Source:</label>
    <select id="sourceFilter" class="form-select form-select-sm w-auto d-inline-block">
        <option value="">All Sources</option>
        <option value="1" selected>Warehouse Only</option>
        <option value="0">Non-Warehouse Only</option>
    </select>
</div>

<script>
$('#sourceFilter').on('change', function() {
    usageTable.column(6).search($(this).val()).draw(); // Assuming column 6 is source
});
</script>
```

---

## 📊 Summary of Changes

| **Component**                  | **File**                                      | **Change Type** |
|--------------------------------|-----------------------------------------------|-----------------|
| Database Schema                | `work_order_spareparts` table                 | **ALTER TABLE** |
| Model                          | `WorkOrderSparepartModel.php`                 | Add field       |
| Form UI                        | `work_orders.php` (line 440)                  | Add column      |
| JavaScript                     | `work_orders.php` (line 3329)                 | Add checkbox    |
| Controller - Store             | `WorkOrderController.php` (store)             | Handle new field|
| Controller - Validation        | `WorkOrderController.php` (validation)        | Skip non-WH return |
| Print SPK                      | `print_work_order.php`                        | Badge indicator |
| Sparepart Usage Report         | `SparepartUsageController.php`                | Add filter      |

---

## ✅ Testing Checklist

- [ ] Create new WO dengan sparepart warehouse → checkbox ON
- [ ] Create new WO dengan sparepart bekas → checkbox OFF
- [ ] Validasi sparepart warehouse → return record created
- [ ] Validasi sparepart bekas → NO return record
- [ ] Print SPK menampilkan badge "NON-WH" untuk sparepart bekas
- [ ] Laporan usage menghitung warehouse sparepart saja
- [ ] Edit WO existing → checkbox visible dan editable
- [ ] Database migration tidak error pada existing data

---

## 🎨 UI Preview

**Form Input**:
```
┌─────────────────────────────────────────────────────────────────┐
│ Sparepart Name          Qty    Unit    Source         Action    │
├─────────────────────────────────────────────────────────────────┤
│ [Filter Oli Hidrolik]   [2]   [PCS]   [✓ Warehouse  ]  [❌]     │
│                                         🟢 Warehouse              │
│                                                                   │
│ [Bearing 6205]          [4]   [PCS]   [  Bekas/Reuse]  [❌]     │
│                                         🟡 Bekas/Reuse            │
└─────────────────────────────────────────────────────────────────┘
```

**Print SPK**:
```
Sparepart yang Dibawa:
1. Filter Oli Hidrolik - 2 PCS
2. Bearing 6205 - 4 PCS [🔄 NON-WH]
```

---

## � REQUIREMENT TAMBAHAN - TOOLS & NOTES COLUMN

### **📌 Requirement 1: Tools Tracking**

**Konteks**: Mekanik juga membawa **tools/perkakas** ke lapangan yang perlu ditrack:
- **Tools Examples**: Kunci inggris, obeng, multipemeter, jack stand, dll
- **Behavior**: Mirip sparepart (dibawa, digunakan, dikembalikan)
- **Difference**: Tools biasanya **tidak habis pakai** (durable goods)

#### **Analisa Opsi Implementasi**

##### **OPSI A: Gabung di `work_order_spareparts` ⭐ RECOMMENDED**

**Strategy**: Rename table concept menjadi "Items" dan tambah `item_type`

**Pros**:
- ✅ Single source of truth untuk semua items yang dibawa
- ✅ UI tetap simpel - 1 section "Items Brought"
- ✅ Reuse existing validation logic
- ✅ Mudah maintain
- ✅ Reports bisa filter by item_type

**Cons**:
- ⚠️ Mixing spareparts dan tools dalam 1 tabel
- ⚠️ Perlu kolom tambahan untuk distinguish

**Database Migration**:
```sql
-- Add item_type column
ALTER TABLE work_order_spareparts 
ADD COLUMN item_type ENUM('sparepart', 'tool') DEFAULT 'sparepart'
COMMENT 'Type: sparepart (consumable) or tool (durable)'
AFTER sparepart_name;

-- Add index
CREATE INDEX idx_item_type ON work_order_spareparts(item_type);

-- Update existing records
UPDATE work_order_spareparts SET item_type = 'sparepart' WHERE item_type IS NULL;
```

**Form UI Enhancement**:
```html
<td>
    <select class="form-select form-select-sm" name="item_type[]" 
            onchange="updateItemLabel(this)">
        <option value="sparepart" selected>
            <i class="fas fa-cog"></i> Sparepart
        </option>
        <option value="tool">
            <i class="fas fa-tools"></i> Tool/Perkakas
        </option>
    </select>
</td>
```

**Label Update Based on Type**:
```javascript
function updateItemLabel(select) {
    const row = $(select).closest('tr');
    const type = $(select).val();
    
    if (type === 'tool') {
        row.find('.item-type-badge').html(
            '<i class="fas fa-tools text-primary"></i> Tool'
        );
        // Tools biasanya tidak habis, hint untuk quantity
        row.find('input[name="sparepart_quantity[]"]')
           .attr('title', 'Jumlah tool yang dibawa (untuk tracking)');
    } else {
        row.find('.item-type-badge').html(
            '<i class="fas fa-cog text-success"></i> Sparepart'
        );
    }
}
```

---

##### **OPSI B: Tabel Terpisah `work_order_tools`**

**Strategy**: Buat tabel baru dengan struktur mirip

**Pros**:
- ✅ Separation of concerns
- ✅ Cleaner data model
- ✅ Tidak mixing domain concepts

**Cons**:
- ❌ Duplicate code untuk validation logic
- ❌ 2 queries untuk fetch items
- ❌ UI lebih kompleks (2 sections)
- ❌ More maintenance overhead

**Database Schema**:
```sql
CREATE TABLE work_order_tools (
    id INT PRIMARY KEY AUTO_INCREMENT,
    work_order_id INT NOT NULL,
    tool_name VARCHAR(255) NOT NULL,
    tool_code VARCHAR(50),
    quantity_brought INT DEFAULT 1,
    notes TEXT,
    is_returned TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (work_order_id) REFERENCES work_orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**🎯 RECOMMENDATION: OPSI A (Gabung)**

**Reasoning**:
1. Behavior sangat mirip (brought, used/returned, validated)
2. UI experience lebih baik - single list
3. Less code duplication
4. Flexible - bisa extend ke item types lain (consumables, PPE, etc)
5. Mudah query dan report

---

### **📌 Requirement 2: Notes/Keterangan Column**

**Current State**: 
- ✅ Database sudah punya kolom `notes` (TEXT)
- ❌ UI form **BELUM** ada input untuk notes

**Use Cases untuk Notes**:
- "Bekas dari unit #12345"
- "Sisa dari WO minggu lalu"
- "Pinjam dari tim lain"
- "Customer provided"
- "Emergency purchase"

#### **Implementation - Add Notes Column to Form**

**File**: `app/Views/service/work_orders.php`

**Current Table Header** (line 440):
```html
<thead>
    <tr>
        <th width="40%">Sparepart Name*</th>
        <th width="15%">Quantity*</th>
        <th width="15%">Unit*</th>
        <th width="20%">Source</th>
        <th width="10%">Action</th>
    </tr>
</thead>
```

**IMPROVED Table Header**:
```html
<thead>
    <tr>
        <th width="30%">Item Name*</th>
        <th width="10%">Type*</th>
        <th width="10%">Qty*</th>
        <th width="10%">Unit*</th>
        <th width="15%">Source</th>
        <th width="20%">Notes/Keterangan</th>
        <th width="5%">Action</th>
    </tr>
</thead>
```

**Updated Row in `addSparepartRow` function** (line 3340):
```javascript
addSparepartRow = function(sparepartData = null) {
    sparepartRowCount++;
    
    const row = `
        <tr>
            <td>
                <select class="form-select form-select-sm" name="sparepart_name[]" 
                        id="sparepart_${sparepartRowCount}" required>
                    <option value="">-- Select Item --</option>
                </select>
            </td>
            <td>
                <!-- NEW: Item Type -->
                <select class="form-select form-select-sm" name="item_type[]" 
                        onchange="updateItemTypeLabel(this)">
                    <option value="sparepart" selected>
                        <i class="fas fa-cog"></i> Sparepart
                    </option>
                    <option value="tool">
                        <i class="fas fa-tools"></i> Tool
                    </option>
                </select>
            </td>
            <td>
                <input type="number" class="form-control form-control-sm" 
                       name="sparepart_quantity[]" value="1" min="1" required>
            </td>
            <td>
                <select class="form-select form-select-sm" name="sparepart_unit[]" required>
                    <option value="PCS">PCS</option>
                    <option value="UNIT">UNIT</option>
                    <option value="SET">SET</option>
                    <!-- ... other units ... -->
                </select>
            </td>
            <td>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" 
                           name="is_from_warehouse[]" 
                           id="warehouse_${sparepartRowCount}" 
                           value="1" checked 
                           onchange="toggleSourceLabel(this)">
                    <label class="form-check-label small" for="warehouse_${sparepartRowCount}">
                        <span class="badge bg-success warehouse-badge">
                            <i class="fas fa-warehouse"></i> WH
                        </span>
                        <span class="badge bg-warning non-warehouse-badge d-none">
                            <i class="fas fa-recycle"></i> Bekas
                        </span>
                    </label>
                </div>
            </td>
            <td>
                <!-- NEW: Notes/Keterangan -->
                <input type="text" class="form-control form-control-sm" 
                       name="sparepart_notes[]" 
                       placeholder="Optional notes..."
                       maxlength="255">
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm removeSparepartRow">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        </tr>
    `;
    
    $('#sparepartTableBody').append(row);
    // ... rest of Select2 initialization ...
};
```

**Controller Update** (`WorkOrderController.php` store method):
```php
public function store()
{
    // ... existing code ...
    
    $sparepartNames = $this->request->getPost('sparepart_name');
    $sparepartQuantities = $this->request->getPost('sparepart_quantity');
    $sparepartUnits = $this->request->getPost('sparepart_unit');
    $itemTypes = $this->request->getPost('item_type'); // NEW
    $isFromWarehouse = $this->request->getPost('is_from_warehouse');
    $sparepartNotes = $this->request->getPost('sparepart_notes'); // NEW
    
    if (!empty($sparepartNames) && is_array($sparepartNames)) {
        $spareparts = [];
        foreach ($sparepartNames as $index => $name) {
            if (!empty($name)) {
                $spareparts[] = [
                    'sparepart_name' => $name,
                    'sparepart_code' => $this->generateSparepartCode($name),
                    'quantity_brought' => $sparepartQuantities[$index] ?? 1,
                    'satuan' => $sparepartUnits[$index] ?? 'PCS',
                    'item_type' => $itemTypes[$index] ?? 'sparepart', // NEW
                    'is_from_warehouse' => isset($isFromWarehouse[$index]) ? 1 : 0,
                    'notes' => $sparepartNotes[$index] ?? null // NEW
                ];
            }
        }
        
        if (!empty($spareparts)) {
            $this->sparepartModel->addSpareparts($workOrderId, $spareparts);
        }
    }
}
```

**Print SPK Update** (`print_work_order.php`):
```php
<table class="table table-sm">
    <thead>
        <tr>
            <th width="5%">No</th>
            <th width="35%">Item Name</th>
            <th width="10%">Type</th>
            <th width="15%">Quantity</th>
            <th width="35%">Notes</th>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1; ?>
        <?php foreach ($spareparts as $sp): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td>
                <?= esc($sp['sparepart_name']) ?>
                <?php if ($sp['is_from_warehouse'] == 0): ?>
                    <span class="badge badge-warning badge-sm">NON-WH</span>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($sp['item_type'] == 'tool'): ?>
                    <i class="fas fa-tools"></i> Tool
                <?php else: ?>
                    <i class="fas fa-cog"></i> Sparepart
                <?php endif; ?>
            </td>
            <td><?= $sp['quantity_brought'] ?> <?= $sp['satuan'] ?></td>
            <td>
                <small class="text-muted">
                    <?= esc($sp['notes'] ?? '-') ?>
                </small>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
```

**Sparepart Validation Modal Update**:
```php
<!-- Show notes in validation for context -->
<tr>
    <td><?= esc($sp['sparepart_name']) ?></td>
    <td>
        <?php if (!empty($sp['notes'])): ?>
            <small class="text-muted d-block">
                <i class="fas fa-info-circle"></i> <?= esc($sp['notes']) ?>
            </small>
        <?php endif; ?>
    </td>
    <td><?= $sp['quantity_brought'] ?> <?= $sp['satuan'] ?></td>
    <td>
        <input type="number" name="used_quantity[]" 
               class="form-control form-control-sm" 
               max="<?= $sp['quantity_brought'] ?>" required>
    </td>
</tr>
```

---

## 📊 FINAL SUMMARY - ALL FEATURES

| **Feature**                    | **Database Column**   | **UI Element**          | **Impact**                |
|--------------------------------|-----------------------|-------------------------|---------------------------|
| Non-Warehouse Flag             | `is_from_warehouse`   | Checkbox toggle         | Skip return, filter report|
| Item Type (Sparepart/Tool)     | `item_type`           | Dropdown select         | Badge in print, filtering |
| Notes/Keterangan               | `notes`               | Text input              | Context in print/validate |

---

## ✅ COMPLETE TESTING CHECKLIST

### **Database**
- [ ] Migration adds `item_type` column successfully
- [ ] Migration adds index on `item_type`
- [ ] Existing records default to 'sparepart'
- [ ] `notes` column accepts NULL values

### **Form Input**
- [ ] Item type dropdown shows Sparepart/Tool options
- [ ] Quantity input works for both types
- [ ] Source toggle works (Warehouse/Bekas)
- [ ] Notes field accepts text up to 255 chars
- [ ] Add row button creates new row with all fields
- [ ] Remove row button deletes row

### **Store/Save**
- [ ] Create WO with sparepart + warehouse → saves correctly
- [ ] Create WO with tool + bekas → saves correctly
- [ ] Create WO with notes → notes saved to database
- [ ] Edit WO preserves item_type and notes

### **Print SPK**
- [ ] Tools show tool icon and label
- [ ] Spareparts show sparepart icon
- [ ] Non-warehouse items show "NON-WH" badge
- [ ] Notes displayed in notes column
- [ ] Empty notes show "-"

### **Validation**
- [ ] Warehouse spareparts create return records
- [ ] Bekas spareparts skip return creation
- [ ] Tools can be marked as returned
- [ ] Notes visible during validation for context

### **Reports**
- [ ] Sparepart usage report filters by item_type
- [ ] Warehouse-only filter excludes non-warehouse items
- [ ] Tool usage can be tracked separately
- [ ] Notes included in detailed reports

---

## �🚀 Alternatif Implementasi (Jika Diperlukan)

### **OPSI 2: Dropdown Source Type**

Jika ke depan ada lebih dari 2 tipe source (misal: Warehouse, Bekas, Customer-Provided, Vendor-Provided):

**Database**:
```sql
ALTER TABLE work_order_spareparts 
ADD COLUMN source_type ENUM('WAREHOUSE', 'BEKAS', 'CUSTOMER', 'VENDOR') 
DEFAULT 'WAREHOUSE' 
AFTER is_additional;
```

**UI**:
```html
<select name="source_type[]" class="form-select form-select-sm">
    <option value="WAREHOUSE" selected>🏭 Warehouse</option>
    <option value="BEKAS">🔄 Bekas/Reuse</option>
    <option value="CUSTOMER">👤 Customer Provided</option>
    <option value="VENDOR">🏢 Vendor Provided</option>
</select>
```

---

## 📝 Kesimpulan

**Rekomendasi Final**: **OPSI 1** (Simple Checkbox)

**Alasan**:
1. ✅ **Simple & Clear**: Binary choice (Warehouse vs Non-Warehouse)
2. ✅ **Fast Implementation**: ~2-3 jam development
3. ✅ **Backward Compatible**: Default value untuk existing data
4. ✅ **Accurate Reporting**: Memisahkan warehouse stock dari non-warehouse
5. ✅ **User Friendly**: Toggle switch lebih intuitif daripada dropdown

**Estimasi Waktu**:
- Database Migration: 10 menit
- Model Update: 5 menit
- Form UI: 30 menit
- Controller Logic: 45 menit
- Print SPK: 20 menit
- Sparepart Validation: 30 menit
- Report Filter: 30 menit
- Testing: 1 jam
- **TOTAL**: ~3-4 jam

**Priority**: HIGH (impact pada akurasi laporan stock warehouse)
