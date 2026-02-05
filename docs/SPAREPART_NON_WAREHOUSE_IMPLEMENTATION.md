# ✅ SPAREPART NON-WAREHOUSE FEATURE - IMPLEMENTATION COMPLETE

**Date**: 2026-02-05  
**Feature**: Differentiate Warehouse Stock vs Non-Warehouse (Bekas/Reuse) Spareparts  
**Status**: ✅ **FULLY IMPLEMENTED**

---

## 📊 Implementation Summary

### **1. Database Migration** ✅
**File**: `databases/migrations/add_is_from_warehouse_to_spareparts.sql`

```sql
ALTER TABLE work_order_spareparts 
ADD COLUMN is_from_warehouse TINYINT(1) DEFAULT 1 
COMMENT '1=From Warehouse, 0=Non-Warehouse (bekas/reuse)';

CREATE INDEX idx_from_warehouse ON work_order_spareparts(is_from_warehouse);
```

**Result**: 
- ✅ Column added successfully
- ✅ 59 existing records migrated with default value `1` (Warehouse)
- ✅ Index created for performance

---

### **2. Model Update** ✅
**File**: `app/Models/WorkOrderSparepartModel.php`

**Changes**:
```php
protected $allowedFields = [
    // ... existing fields ...
    'is_from_warehouse',  // ← NEW
];

protected $validationRules = [
    // ... existing rules ...
    'is_from_warehouse' => 'permit_empty|in_list[0,1]'
];
```

**File**: `app/Models/WorkOrderModel.php` (getWorkOrderSpareparts)

**Changes**:
```php
->select('
    // ... existing fields ...
    wos.is_from_warehouse  // ← NEW
', false)
```

---

### **3. Form UI - New Work Order** ✅
**File**: `app/Views/service/work_orders.php`

**Table Header** (line 440):
```html
<th width="35%">Sparepart Name*</th>
<th width="15%">Quantity*</th>
<th width="15%">Unit*</th>
<th width="25%">Source*</th>  <!-- ← NEW -->
<th width="10%">Action</th>
```

**Row Template** (addSparepartRow function, line 3335):
```html
<td>
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
            <span class="badge bg-warning text-dark non-warehouse-badge d-none">
                <i class="fas fa-recycle me-1"></i>Bekas
            </span>
        </label>
    </div>
</td>
```

**Toggle Function** (line 3505):
```javascript
window.toggleSourceLabel = function(checkbox) {
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
};
```

---

### **4. Controller - Store Work Order** ✅
**File**: `app/Controllers/WorkOrderController.php` (store method, line 1140)

**Changes**:
```php
$isFromWarehouse = $input['is_from_warehouse'] ?? []; // ← NEW

for ($i = 0; $i < count($sparepartNames); $i++) {
    if (!empty($sparepartNames[$i])) {
        $spareparts[] = [
            'sparepart_code' => $sparepartCode,
            'sparepart_name' => $sparepartName,
            'quantity_brought' => $sparepartQuantities[$i] ?? 1,
            'satuan' => $sparepartUnits[$i] ?? 'pcs',
            'is_from_warehouse' => isset($isFromWarehouse[$i]) && $isFromWarehouse[$i] == '1' ? 1 : 0 // ← NEW
        ];
    }
}
```

---

### **5. Sparepart Validation - Skip Return for Non-Warehouse** ✅
**File**: `app/Controllers/WorkOrderController.php` (saveSparepartValidation, line 3910)

**Changes**:
```php
if ($originalSparepart) {
    $isFromWarehouse = (int)($originalSparepart['is_from_warehouse'] ?? 1); // ← NEW
    $quantityBrought = (int)($originalSparepart['quantity_brought'] ?? 0);
    $quantityUsed = (int)($sparepart['used_quantity'] ?? 0);
    $quantityReturn = $quantityBrought - $quantityUsed;

    // Update used quantity
    $db->table('work_order_spareparts')
        ->where('id', $sparepart['id'])
        ->update($updateData);

    // ✅ ONLY create return record if FROM WAREHOUSE
    if ($quantityReturn > 0 && $isFromWarehouse == 1) {
        $returnModel->insert($returnData);
        log_message('info', "Auto-created return record...");
    } else if ($quantityReturn > 0 && $isFromWarehouse == 0) {
        // ← NEW: Log skip for non-warehouse
        log_message('info', "Skipped return record for NON-WAREHOUSE sparepart (Bekas/Reuse)");
    }
}
```

**Impact**: 
- ✅ Warehouse spareparts → create return record
- ✅ Non-warehouse spareparts → **NO return record** (tidak masuk warehouse)

---

### **6. Print Work Order - Visual Indicator** ✅
**File**: `app/Views/service/print_work_order.php` (line 330)

**Changes**:
```php
foreach ($spareparts as $part) {
    $rowCount++;
    $qtyWithUnit = htmlspecialchars($part['qty']??'') . ' ' . htmlspecialchars($part['satuan']??'pcs');
    $sparepartName = htmlspecialchars($part['name']??'');
    
    // ← NEW: Add NON-WH badge
    $isFromWarehouse = isset($part['is_from_warehouse']) ? (int)$part['is_from_warehouse'] : 1;
    if ($isFromWarehouse == 0) {
        $sparepartName .= ' <span style="background-color: #ffc107; color: #000; padding: 2px 6px; font-size: 7pt; border-radius: 3px; font-weight: bold;">NON-WH</span>';
    }
    
    echo '<tr>...<td>'.$sparepartName.'</td>...</tr>';
}
```

**Print Preview**:
```
Sparepart yang Dibawa:
1. Filter Oli Hidrolik - 2 PCS
2. Bearing 6205 - 4 PCS [NON-WH]
```

---

### **7. Sparepart Usage Report - Stats & Filter** ✅
**File**: `app/Controllers/Warehouse/SparepartUsageController.php` (line 45)

**Changes**:
```php
'stats' => [
    'usage_total' => ...,
    
    // ← NEW: Warehouse only usage
    'usage_warehouse' => $db->table('work_order_spareparts')
        ->where('quantity_used >', 0)
        ->where('is_from_warehouse', 1)
        ->countAllResults(),
        
    // ← NEW: Non-warehouse usage
    'usage_non_warehouse' => $db->table('work_order_spareparts')
        ->where('quantity_used >', 0)
        ->where('is_from_warehouse', 0)
        ->countAllResults(),
    
    'return_pending' => ...,
    'return_confirmed' => ...
]
```

**File**: `app/Views/warehouse/sparepart_usage.php` (line 8)

**UI Update**:
```html
<!-- Total Usage (All) -->
<div class="stat-card bg-info-soft">
    <div class="stat-value"><?= $stats['usage_total'] ?? 0 ?></div>
    <div class="text-muted">Total Usage (All)</div>
</div>

<!-- Warehouse Stock -->
<div class="stat-card bg-primary-soft">
    <i class="fas fa-warehouse stat-icon text-primary"></i>
    <div class="stat-value"><?= $stats['usage_warehouse'] ?? 0 ?></div>
    <div class="text-muted">Warehouse Stock</div>
</div>

<!-- Bekas/Reuse -->
<div class="stat-card" style="background-color: #fff3cd;">
    <i class="fas fa-recycle stat-icon" style="color: #856404;"></i>
    <div class="stat-value"><?= $stats['usage_non_warehouse'] ?? 0 ?></div>
    <div class="text-muted">Bekas/Reuse</div>
</div>
```

---

## 🎯 Feature Flow

### **Create New Work Order**
1. User clicks "Add Sparepart" button
2. Row added with **toggle switch** (default: ✅ Warehouse)
3. User can toggle to **Bekas/Reuse** if needed
4. Badge changes: 
   - ✅ ON → 🟢 **Warehouse** (green badge)
   - ❌ OFF → 🟡 **Bekas** (yellow badge)
5. On submit: `is_from_warehouse` saved (1 or 0)

### **Sparepart Validation (Close WO)**
1. Mechanic enters `quantity_used`
2. System calculates `quantity_return = quantity_brought - quantity_used`
3. **Decision Logic**:
   - If `is_from_warehouse = 1` AND `quantity_return > 0` → **CREATE return record**
   - If `is_from_warehouse = 0` → **SKIP return** (log info message)

### **Print Work Order**
1. System loads spareparts with `is_from_warehouse` field
2. **Warehouse sparepart**: Display normally
   ```
   Filter Oli Hidrolik - 2 PCS
   ```
3. **Non-warehouse sparepart**: Add **[NON-WH]** badge
   ```
   Bearing 6205 - 4 PCS [NON-WH]
   ```

### **Usage Report**
1. Dashboard shows **3 separate stats**:
   - Total Usage (All)
   - Warehouse Stock (accurate count)
   - Bekas/Reuse (excluded from inventory)
2. Accurate inventory calculation (only warehouse items)

---

## 📈 Impact

### **Before Implementation**
- ❌ No distinction between warehouse and non-warehouse spareparts
- ❌ Return records created for ALL spareparts (including bekas)
- ❌ Inventory reports inaccurate (mixed with non-warehouse items)
- ❌ No visual indicator on print

### **After Implementation**
- ✅ Clear distinction: Warehouse vs Bekas/Reuse
- ✅ Return records ONLY for warehouse spareparts
- ✅ Accurate inventory tracking (warehouse stock only)
- ✅ Visual indicator on print ([NON-WH] badge)
- ✅ Separate statistics in usage report
- ✅ User-friendly toggle switch interface

---

## 🧪 Testing Checklist

- [x] Database migration successful (59 records migrated)
- [ ] Create new WO with warehouse sparepart → checkbox ON
- [ ] Create new WO with bekas sparepart → checkbox OFF
- [ ] Toggle switch changes badge (Warehouse ↔ Bekas)
- [ ] Validate warehouse sparepart → return record created
- [ ] Validate bekas sparepart → NO return record
- [ ] Print SPK shows [NON-WH] badge correctly
- [ ] Usage report shows separate stats
- [ ] Edit existing WO preserves is_from_warehouse value
- [ ] DataTable loads without errors

---

## 📁 Files Modified

| **File** | **Type** | **Changes** |
|----------|----------|-------------|
| `databases/migrations/add_is_from_warehouse_to_spareparts.sql` | SQL | ✅ NEW - Database migration |
| `app/Models/WorkOrderSparepartModel.php` | Model | ✅ Added field to allowedFields + validation |
| `app/Models/WorkOrderModel.php` | Model | ✅ Added field to getWorkOrderSpareparts() |
| `app/Views/service/work_orders.php` | View | ✅ Added Source column + toggle switch |
| `app/Controllers/WorkOrderController.php` | Controller | ✅ Store: Handle checkbox array<br>✅ Validation: Skip return for bekas |
| `app/Views/service/print_work_order.php` | View | ✅ Added [NON-WH] badge |
| `app/Controllers/Warehouse/SparepartUsageController.php` | Controller | ✅ Added separate stats |
| `app/Views/warehouse/sparepart_usage.php` | View | ✅ Display warehouse/non-warehouse stats |

**Total**: 8 files modified, 1 file created (migration)

---

## 🚀 Deployment Notes

1. **Run migration** (DONE ✅):
   ```bash
   mysql -u root optima_ci < databases/migrations/add_is_from_warehouse_to_spareparts.sql
   ```

2. **Clear cache** (if any):
   ```bash
   php spark cache:clear
   ```

3. **Test in browser**:
   - Create new Work Order
   - Toggle sparepart source
   - Validate sparepart
   - Check print output
   - View usage report

4. **Verify logs**:
   ```bash
   tail -f writable/logs/log-2026-02-05.log | grep -i "warehouse\|bekas\|return"
   ```

---

## 📞 Support

For questions or issues, contact development team.

**Implementation completed**: 2026-02-05  
**Estimated development time**: 3.5 hours  
**Tested**: ⏳ Pending user acceptance testing
