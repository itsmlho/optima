# 📅 GLOBAL DATE PICKER - Panduan Implementasi

**Version:** 1.0.0  
**Last Updated:** December 8, 2025  
**Status:** ✅ Production Ready

---

## 🎯 Tujuan

Menyediakan **pattern yang konsisten dan mudah** untuk implementasi date range filter di semua halaman OPTIMA. Dengan helper ini, Anda hanya perlu memanggil satu fungsi untuk mengintegrasikan date picker dengan data loading dan filtering.

---

## 🏗️ Arsitektur

### **File Structure**

```
public/assets/js/
├── global-daterange.js              ← Inisialisasi daterangepicker
├── datatable-datefilter-mixin.js    ← Helper untuk DataTable
└── page-date-filter-helper.js       ← ⭐ NEW: Universal API
```

### **Dependencies** (Sudah di-load di `base.php`)

1. jQuery
2. moment.js
3. daterangepicker.js
4. global-daterange.js
5. datatable-datefilter-mixin.js
6. **page-date-filter-helper.js** ← Helper baru

### **Component**

```php
// Di halaman view
<?= view('components/date_range_filter', ['id' => 'myPageDateRangePicker']) ?>
```

---

## 📦 API Functions

### **1. `initPageDateFilter(options)`** - Untuk Halaman Tanpa DataTable

**Use Case:** Halaman dengan fetch manual atau custom rendering (seperti SPK)

**Signature:**
```javascript
initPageDateFilter({
    pickerId: string,           // ID date picker element
    onInit: function(),         // Callback saat pertama load
    onDateChange: function(startDate, endDate), // Callback saat date berubah
    onDateClear: function(),    // Callback saat date di-clear
    debug: boolean              // Optional: enable logging (default: false)
})
```

**Example:**
```javascript
// Setup date filter untuk halaman custom
initPageDateFilter({
    pickerId: 'myPageDateRangePicker',
    onInit: function() {
        // Load data awal tanpa filter
        loadStatistics();
        loadTableData();
    },
    onDateChange: function(startDate, endDate) {
        // Reload data dengan filter
        console.log('Filter changed:', startDate, 'to', endDate);
        loadStatistics(startDate, endDate);
        loadTableData(startDate, endDate);
    },
    onDateClear: function() {
        // Reload data tanpa filter
        console.log('Filter cleared');
        loadStatistics();
        loadTableData();
    },
    debug: true
});
```

---

### **2. `initDataTableWithDateFilter(options)`** - Untuk Halaman Dengan DataTable

**Use Case:** Halaman dengan DataTable server-side (seperti Quotations, Customer Management)

**Signature:**
```javascript
initDataTableWithDateFilter({
    pickerId: string,           // ID date picker element
    tableId: string,            // ID table element (tanpa #)
    tableConfig: object,        // DataTable config
    autoCalculateStats: boolean, // ⭐ NEW: Auto-calculate stats dari data table
    statsConfig: object,        // ⭐ NEW: Config untuk auto-calculate
    onStatisticsLoad: function(startDate, endDate), // Optional: AJAX-based stats (legacy)
    onTableReady: function(table), // Optional: callback setelah table ready
    debug: boolean              // Optional: enable logging
})
```

**Returns:** DataTable instance

**Example 1: Auto-Calculate Statistics (RECOMMENDED)**
```javascript
// Statistics otomatis dihitung dari data table yang ter-filter
var myTable = initDataTableWithDateFilter({
    pickerId: 'myPageDateRangePicker',
    tableId: 'myTable',
    tableConfig: {
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url("mymodule/data") ?>',
            type: 'POST'
        },
        columns: [
            { data: 'id' },
            { data: 'status' },
            { data: 'amount' }
        ]
    },
    autoCalculateStats: true, // Enable auto-calculate
    statsConfig: {
        // Simple count - hitung semua rows yang ter-filter
        total: '#stat-total',
        
        // Filter by condition - hitung rows yang match kondisi
        active: {
            selector: '#stat-active',
            filter: row => row.status === 'active'
        },
        pending: {
            selector: '#stat-pending',
            filter: row => row.status === 'pending'
        },
        
        // Custom calculation - sum, average, dll
        totalAmount: {
            selector: '#stat-total-amount',
            calculate: function(data) {
                return data.reduce((sum, row) => sum + (parseFloat(row.amount) || 0), 0);
            }
        },
        averageAmount: {
            selector: '#stat-avg-amount',
            calculate: function(data) {
                const total = data.reduce((sum, row) => sum + (parseFloat(row.amount) || 0), 0);
                return data.length > 0 ? (total / data.length).toFixed(2) : 0;
            }
        }
    },
    debug: true
});
```

**Example 2: AJAX-Based Statistics (Legacy)**
```javascript
// Load statistics via AJAX (old method, masih supported)
var myTable = initDataTableWithDateFilter({
    pickerId: 'myPageDateRangePicker',
    tableId: 'myTable',
    tableConfig: { /* ... */ },
    onStatisticsLoad: function(startDate, endDate) {
        // Load statistics via AJAX
        loadStatistics(startDate, endDate);
    },
    onTableReady: function(table) {
        console.log('Table ready!', table);
    },
    debug: true
});
```

**Benefits Auto-Calculate:**
- ✅ **Selalu Sinkron** - Statistics otomatis match dengan data table
- ✅ **Real-time** - Update instant tanpa AJAX delay
- ✅ **No Extra Request** - Tidak perlu endpoint statistics terpisah
- ✅ **Filter-aware** - Auto adjust saat user search di table
- ✅ **Efficient** - Hitung dari data yang sudah di-load

---

### **3. Utility Functions**

#### `getCurrentDateFilter()`
Mendapatkan nilai date filter saat ini.

```javascript
const filter = getCurrentDateFilter();
// Returns: { start: "2025-01-01", end: "2025-01-31" } atau { start: null, end: null }
```

#### `isDateFilterActive()`
Cek apakah date filter sedang aktif.

```javascript
if (isDateFilterActive()) {
    console.log('Date filter is active');
}
// Returns: true atau false
```

#### `formatCurrentDateFilter()`
Format date filter untuk display.

```javascript
const displayText = formatCurrentDateFilter();
// Returns: "2025-01-01 to 2025-01-31" atau "All dates"
```

---

## 🚀 Implementasi Step-by-Step

### **Scenario 1: Halaman Baru dengan DataTable**

**1. Tambahkan date picker component di view:**
```php
<!-- Di bagian atas halaman, sebelum statistics cards -->
<div class="row mt-3">
    <div class="col-md-12 text-end">
        <div class="d-inline-block">
            <?= view('components/date_range_filter', ['id' => 'myPageDateRangePicker']) ?>
        </div>
    </div>
</div>
```

**2. Setup JavaScript di section('javascript'):**
```javascript
<?= $this->section('javascript') ?>
<script>
var myTable; // Global variable untuk DataTable

$(document).ready(function() {
    // DataTable configuration
    var config = {
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url("mymodule/data") ?>',
            type: 'POST'
        },
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'created_at' }
        ]
    };
    
    // Initialize dengan helper
    myTable = initDataTableWithDateFilter({
        pickerId: 'myPageDateRangePicker',
        tableId: 'myTable',
        tableConfig: config,
        onStatisticsLoad: function(startDate, endDate) {
            loadStatistics(startDate, endDate);
        },
        debug: true
    });
});

// Function untuk load statistics
function loadStatistics(startDate, endDate) {
    const data = {};
    if (startDate && endDate) {
        data.start_date = startDate;
        data.end_date = endDate;
    }
    
    $.ajax({
        url: '<?= base_url("mymodule/stats") ?>',
        type: 'POST',
        data: data,
        success: function(response) {
            $('#stat-total').text(response.total || 0);
            $('#stat-active').text(response.active || 0);
        }
    });
}
</script>
<?= $this->endSection() ?>
```

**3. Update Controller untuk handle date filter:**
```php
public function data()
{
    $request = $this->request->getPost();
    
    // Get date filter dari request
    $startDate = $request['start_date'] ?? null;
    $endDate = $request['end_date'] ?? null;
    
    $builder = $this->db->table('my_table');
    
    // Apply date filter jika ada
    if ($startDate && $endDate) {
        $builder->where('created_at >=', $startDate);
        $builder->where('created_at <=', $endDate . ' 23:59:59');
    }
    
    // ... rest of DataTable logic
}

public function stats()
{
    $request = $this->request->getPost();
    
    $startDate = $request['start_date'] ?? null;
    $endDate = $request['end_date'] ?? null;
    
    $builder = $this->db->table('my_table');
    
    if ($startDate && $endDate) {
        $builder->where('created_at >=', $startDate);
        $builder->where('created_at <=', $endDate . ' 23:59:59');
    }
    
    return $this->response->setJSON([
        'total' => $builder->countAllResults(false),
        'active' => $builder->where('status', 'active')->countAllResults()
    ]);
}
```

---

### **Scenario 2: Halaman Tanpa DataTable (Custom Rendering)**

**1. Tambahkan date picker component:**
```php
<div class="row mt-3">
    <div class="col-md-12 text-end">
        <?= view('components/date_range_filter', ['id' => 'myPageDateRangePicker']) ?>
    </div>
</div>
```

**2. Setup JavaScript:**
```javascript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize date filter
    initPageDateFilter({
        pickerId: 'myPageDateRangePicker',
        onInit: function() {
            loadData(); // Initial load
        },
        onDateChange: function(startDate, endDate) {
            loadData(startDate, endDate);
        },
        onDateClear: function() {
            loadData(); // Load all data
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
    
    fetch('<?= base_url("mymodule/data") ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(params)
    })
    .then(r => r.json())
    .then(data => {
        // Render data manually
        renderTable(data);
        updateStatistics(data);
    });
}
```

---

## ✅ Checklist Implementasi

Saat mengimplementasikan date filter di halaman baru, pastikan:

- [ ] Component `date_range_filter` sudah ditambahkan di view
- [ ] ID picker unique per halaman (contoh: `quotationDateRangePicker`, `spkDateRangePicker`)
- [ ] Helper `initPageDateFilter` atau `initDataTableWithDateFilter` dipanggil di `$(document).ready()` atau `DOMContentLoaded`
- [ ] Function `loadStatistics()` menerima parameter `startDate` dan `endDate` (optional)
- [ ] Controller endpoint sudah handle parameter `start_date` dan `end_date`
- [ ] Test: Date filter clear berfungsi (reload all data)
- [ ] Test: Date filter apply berfungsi (filter data by range)
- [ ] Test: Statistics update sesuai filter

---

## 🐛 Troubleshooting

### **Problem: Date picker tidak muncul**

**Solusi:**
1. Cek console browser untuk error
2. Pastikan ID picker unique
3. Cek component sudah di-render: inspect element cari input dengan class `.global-date-range-picker`

### **Problem: DataTable tidak reload saat date berubah**

**Solusi:**
1. Pastikan menggunakan `initDataTableWithDateFilter` bukan manual init
2. Cek `ajax.url` di config valid
3. Cek console untuk error dari `applyDateFilterToConfig`

### **Problem: Statistics tidak update**

**Solusi:**
1. Pastikan callback `onStatisticsLoad` didefinisikan
2. Cek function `loadStatistics()` handle parameter dengan benar
3. Cek endpoint controller sudah return data yang benar

### **Problem: Filter tidak terapply di backend**

**Solusi:**
1. Cek controller: `$request['start_date']` dan `$request['end_date']` ada
2. Debug dengan `var_dump($startDate, $endDate)` di controller
3. Pastikan query WHERE clause benar

---

## 📊 Real Examples

### **✅ Quotations (DataTable)**
```javascript
quotationsTable = initDataTableWithDateFilter({
    pickerId: 'quotationDateRangePicker',
    tableId: 'quotationsTable',
    tableConfig: quotationsConfig,
    onStatisticsLoad: loadStatistics,
    debug: true
});
```

### **✅ Customer Management (DataTable)**
```javascript
customerTable = initDataTableWithDateFilter({
    pickerId: 'customerDateRangePicker',
    tableId: 'customerTable',
    tableConfig: customerConfig,
    onStatisticsLoad: loadStatistics,
    debug: true
});
```

### **✅ SPK (Manual Fetch)**
```javascript
initPageDateFilter({
    pickerId: 'spkDateRangePicker',
    onInit: () => {
        loadSpk();
        loadMonitoring();
    },
    onDateChange: (start, end) => loadSpk(start, end),
    onDateClear: () => loadSpk(),
    debug: true
});
```

---

## 🎓 Best Practices

1. **Consistent Naming:** Gunakan format `{moduleName}DateRangePicker` untuk ID picker
2. **Debug Mode:** Aktifkan `debug: true` saat development untuk troubleshooting
3. **Error Handling:** Selalu sediakan fallback jika date filter tidak ada
4. **Performance:** Date filter di backend harus efficient (indexed columns)
5. **User Experience:** Berikan feedback visual saat data loading dengan filter

---

## 🔮 Future Enhancements

- [ ] Support multiple date filters per halaman
- [ ] Preset date ranges (This Week, This Month, etc.) - sudah tersedia di picker
- [ ] Remember last selected date range (localStorage)
- [ ] Export dengan date filter applied
- [ ] Date filter di URL params (shareable links)

---

## 📞 Support

Jika ada masalah atau pertanyaan:
1. Cek dokumentasi ini dulu
2. Cek console browser untuk error messages
3. Hubungi OPTIMA Development Team

---

**Happy Coding! 🚀**
