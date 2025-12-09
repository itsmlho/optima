# 📋 IMPLEMENTASI DATE PICKER - ROLLOUT PLAN

## 🎯 Target Halaman (13 Pages)

Berikut daftar halaman yang perlu diimplementasikan date picker global:

### ✅ **DONE** (3 halaman sudah selesai)
1. ✅ `/marketing/quotations` - DONE with auto-calculate
2. ✅ `/marketing/customer-management` - DONE with auto-calculate
3. ✅ `/marketing/spk` - DONE with initPageDateFilter

### 🔄 **TO DO** (10 halaman)

#### **Marketing Module**
4. ⏳ `/marketing/di` - Delivery Instructions

#### **Service Module**
5. ⏳ `/service/spk_service` - SPK Service
6. ⏳ `/service/work-orders` - Work Orders  
7. ⏳ `/service/area-management` - Area Management (area_employee_management.php)

#### **Purchasing Module**
8. ⏳ `/purchasing` (purchasing.php) - Main Purchasing
9. ⏳ `/purchasing/supplier-management` - Supplier Management

#### **Warehouse Module**
10. ⏳ `/warehouse/purchase-orders/rejected-items` - Rejected Items
11. ⏳ `/warehouse/inventory/invent_unit` - Unit Inventory
12. ⏳ `/warehouse/inventory/invent_attachment` - Attachment Inventory
13. ⏳ `/warehouse/sparepart-usage` (jika file ada)

#### **Operational Module**
14. ⏳ `/operational/delivery` - Delivery Management

---

## 🚀 QUICK IMPLEMENTATION STEPS

Untuk setiap halaman, ikuti 3 langkah ini:

### **STEP 1: Add Date Picker Component** (di bagian atas view)

```php
<!-- Tambahkan sebelum statistics cards -->
<div class="row mt-3">
    <div class="col-md-12 text-end">
        <div class="d-inline-block">
            <?= view('components/date_range_filter', ['id' => '{module}DateRangePicker']) ?>
        </div>
    </div>
</div>
```

**Replace `{module}` dengan:**
- `di` → `diDateRangePicker`
- `spkService` → `spkServiceDateRangePicker`
- `workOrder` → `workOrderDateRangePicker`
- `purchasing` → `purchasingDateRangePicker`
- `supplier` → `supplierDateRangePicker`
- `rejectedItems` → `rejectedItemsDateRangePicker`
- `inventUnit` → `inventUnitDateRangePicker`
- `inventAttachment` → `inventAttachmentDateRangePicker`
- `delivery` → `deliveryDateRangePicker`

---

### **STEP 2: Determine Implementation Type**

**Cek apakah halaman menggunakan:**

#### **Type A: DataTable dengan Server-Side**
Indikator:
- Ada `$('#tableId').DataTable({ ... })`
- Ada `ajax: { url: '...', type: 'POST' }`
- Ada statistics cards yang perlu dihitung

→ **Use:** `initDataTableWithDateFilter` with `autoCalculateStats: true`

#### **Type B: Fetch/AJAX Manual**
Indikator:
- Pakai `fetch()` atau `$.ajax()` manual
- Render table secara manual (tidak pakai DataTable)
- Ada function `loadData()` atau sejenisnya

→ **Use:** `initPageDateFilter`

#### **Type C: Static Data (No Date Filter Needed)**
Indikator:
- Data tidak perlu filter by date
- Tidak ada tanggal di data
- Pure CRUD tanpa time-based data

→ **Skip** (tidak perlu date picker)

---

### **STEP 3: Implement JavaScript**

#### **For Type A (DataTable):**

```javascript
// TEMPLATE untuk DataTable dengan auto-calculate
var myTable;

$(document).ready(function() {
    var config = {
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url("{module}/data") ?>',
            type: 'POST'
        },
        columns: [ /* existing columns */ ]
        // ... existing config
    };
    
    myTable = initDataTableWithDateFilter({
        pickerId: '{module}DateRangePicker',
        tableId: '{tableId}',
        tableConfig: config,
        autoCalculateStats: true,
        statsConfig: {
            total: '#stat-total',
            // Add more stats based on your needs
            active: {
                selector: '#stat-active',
                filter: row => row.status === 'active'
            }
        },
        debug: true
    });
});
```

#### **For Type B (Fetch Manual):**

```javascript
// TEMPLATE untuk fetch/AJAX manual
document.addEventListener('DOMContentLoaded', function() {
    initPageDateFilter({
        pickerId: '{module}DateRangePicker',
        onInit: function() {
            loadData();
        },
        onDateChange: function(startDate, endDate) {
            loadData(startDate, endDate);
        },
        onDateClear: function() {
            loadData();
        },
        debug: true
    });
});

function loadData(startDate, endDate) {
    const params = {};
    if (startDate && endDate) {
        params.start_date = startDate;
        params.end_date = endDate;
    }
    
    fetch('<?= base_url("{module}/data") ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(params)
    })
    .then(r => r.json())
    .then(data => {
        renderData(data);
        updateStats(data);
    });
}
```

---

## 📊 STATISTICS CONFIG PATTERNS

Sesuaikan `statsConfig` berdasarkan kebutuhan halaman:

### **Common Patterns:**

```javascript
// Pattern 1: Simple counts
statsConfig: {
    total: '#stat-total',
    pending: {
        selector: '#stat-pending',
        filter: row => row.status === 'pending'
    },
    completed: {
        selector: '#stat-completed',
        filter: row => row.status === 'completed'
    }
}

// Pattern 2: Sum/Calculate
statsConfig: {
    totalValue: {
        selector: '#stat-total-value',
        calculate: data => data.reduce((sum, row) => sum + parseFloat(row.amount || 0), 0)
    }
}

// Pattern 3: Complex logic
statsConfig: {
    overdue: {
        selector: '#stat-overdue',
        filter: row => {
            const dueDate = new Date(row.due_date);
            return dueDate < new Date() && row.status !== 'completed';
        }
    }
}
```

---

## 🔧 BACKEND CHANGES (Controller)

**Update method data() di controller untuk handle date filter:**

```php
public function data()
{
    // Get date filter dari request
    $startDate = $this->request->getPost('start_date');
    $endDate = $this->request->getPost('end_date');
    
    $builder = $this->db->table('my_table');
    
    // Apply date filter jika ada
    if ($startDate && $endDate) {
        $builder->where('created_at >=', $startDate);
        $builder->where('created_at <=', $endDate . ' 23:59:59');
    }
    
    // ... rest of DataTable logic
    
    return $this->response->setJSON($output);
}
```

**Note:** Jika pakai auto-calculate, **TIDAK PERLU** endpoint statistics terpisah!

---

## ✅ CHECKLIST Per Halaman

Copy checklist ini untuk setiap halaman:

- [ ] Identifikasi tipe implementasi (A/B/C)
- [ ] Add date picker component di view
- [ ] Implement JavaScript (Type A atau B)
- [ ] Configure statsConfig (jika Type A)
- [ ] Update controller untuk handle date params
- [ ] Test: Initial load berfungsi
- [ ] Test: Date filter apply berfungsi
- [ ] Test: Date filter clear berfungsi
- [ ] Test: Statistics update otomatis
- [ ] Test: Search di table tetap berfungsi
- [ ] Verify no console errors
- [ ] Verify no 404 errors

---

## 🎯 PRIORITY ORDER

Implementasi berdasarkan prioritas usage:

### **High Priority** (Sering digunakan)
1. `/purchasing` - Main purchasing page
2. `/warehouse/inventory/invent_unit` - Unit inventory
3. `/service/work-orders` - Work orders
4. `/operational/delivery` - Delivery management

### **Medium Priority**
5. `/marketing/di` - Delivery instructions
6. `/service/spk_service` - Service SPK
7. `/warehouse/inventory/invent_attachment` - Attachment inventory

### **Low Priority** (Less frequent)
8. `/purchasing/supplier-management` - Supplier management
9. `/warehouse/purchase-orders/rejected-items` - Rejected items
10. `/service/area-management` - Area management

---

## 📝 IMPLEMENTATION LOG

Track progress di sini:

| # | Halaman | Type | Status | Notes |
|---|---------|------|--------|-------|
| 1 | marketing/quotations | A | ✅ DONE | Auto-calculate working |
| 2 | marketing/customer-management | A | ✅ DONE | Auto-calculate working |
| 3 | marketing/spk | B | ✅ DONE | Manual fetch working |
| 4 | marketing/di | ? | ⏳ TODO | Need to check |
| 5 | service/spk_service | ? | ⏳ TODO | Need to check |
| 6 | service/work-orders | ? | ⏳ TODO | Need to check |
| 7 | service/area-management | ? | ⏳ TODO | Need to check |
| 8 | purchasing | ? | ⏳ TODO | Need to check |
| 9 | purchasing/supplier-management | ? | ⏳ TODO | Need to check |
| 10 | warehouse/rejected-items | ? | ⏳ TODO | Need to check |
| 11 | warehouse/invent_unit | ? | ⏳ TODO | Need to check |
| 12 | warehouse/invent_attachment | ? | ⏳ TODO | Need to check |
| 13 | operational/delivery | ? | ⏳ TODO | Need to check |

---

## 🚨 COMMON ISSUES & SOLUTIONS

### **Issue 1: Error 404 on stats endpoint**
**Solution:** Remove manual `loadStatistics()` calls, use `autoCalculateStats: true`

### **Issue 2: TypeError in drawCallback**
**Solution:** Use DataTable API: `this.api().page.info().recordsDisplay`

### **Issue 3: Statistics tidak update**
**Solution:** Pastikan `statsConfig` didefinisikan dengan benar

### **Issue 4: Date filter tidak muncul**
**Solution:** Cek ID picker unique, tidak ada typo di `pickerId`

### **Issue 5: Backend tidak dapat date params**
**Solution:** Add `start_date` dan `end_date` handling di controller

---

## 📚 REFERENCE FILES

**Main Implementation Examples:**
- `app/Views/marketing/quotations.php` - DataTable with auto-calculate
- `app/Views/marketing/customer_management.php` - DataTable with auto-calculate
- `app/Views/marketing/spk.php` - Manual fetch implementation

**Helper Files:**
- `public/assets/js/page-date-filter-helper.js` - Main helper
- `docs/GLOBAL_DATE_PICKER_GUIDE.md` - Detailed guide
- `docs/GLOBAL_DATE_PICKER_CHEATSHEET.js` - Quick reference

---

**Gunakan dokumen ini sebagai panduan untuk implementasi batch! 🚀**
