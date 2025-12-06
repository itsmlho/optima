# 📋 Date Filter - Standard Implementation Approach

## 🎯 Problem Analysis

**Root Cause:** Inconsistent implementation across pages causing:
- Page A works ✅
- Page B broken ❌
- Page C works but stats don't ✅⚠️

**Why?** Different approaches used:
1. Some use `$.extend()` mixin (deprecated, causes ajax override)
2. Some manually add date params (duplicated code)
3. Some missing `applyDateFilterToConfig()` call

## ✅ STANDARD APPROACH (Use This Everywhere!)

### Step-by-Step Implementation

#### 1. **Frontend View** (3 steps)

```javascript
// STEP 1: Create DataTable config WITHOUT ajax.data function
var myTableConfig = {
    processing: true,
    serverSide: true,
    ajax: {
        url: '<?= base_url("controller/getData") ?>',
        type: 'POST',
        error: function(xhr, error, code) {
            console.error('DataTable Error:', error);
        }
        // NO data: function(d) {} here!
    },
    columns: [
        { data: 'column1' },
        { data: 'column2' }
    ],
    // ... other options
};

// STEP 2: Apply date filter wrapper
applyDateFilterToConfig(myTableConfig, 'myPageDatePicker');

// STEP 3: Initialize DataTable
var myTable = $('#myTable').DataTable(myTableConfig);

// STEP 4: Setup auto-reload callbacks
setupDataTableDateFilter(myTable, 'myPageDatePicker', function(startDate, endDate) {
    // Optional: reload statistics or other elements
    loadStatistics(startDate, endDate);
});
```

#### 2. **Backend Controller** (1 line)

```php
public function getData() {
    $builder = $this->model->builder();
    
    // ONE LINE: Apply date filter
    $this->applyDateFilter($builder, 'date_column_name');
    
    // Continue with pagination, search, etc.
    return $this->response->setJSON($data);
}
```

#### 3. **Statistics Method** (same pattern)

```php
public function getStats() {
    $builder = $this->model->builder();
    
    // Apply date filter
    $this->applyDateFilter($builder, 'date_column_name');
    
    $stats = [
        'total' => $builder->countAllResults(),
        // ... other stats
    ];
    
    return $this->response->setJSON(['success' => true, 'data' => $stats]);
}
```

## 📦 Required Components

### 1. Backend Trait (Already Created)
- `app/Traits/DateFilterTrait.php`
- Methods: `applyDateFilter()`, `getDateFilterParams()`, `hasDateFilter()`
- Supports both GET and POST automatically

### 2. Frontend Mixin (Already Created)
- `public/assets/js/datatable-datefilter-mixin.js`
- Functions: `applyDateFilterToConfig()`, `setupDataTableDateFilter()`

### 3. Date Picker Initializer (Already Created)
- `public/assets/js/global-daterange.js`
- Auto-initializes all `.global-date-range-picker` elements

### 4. UI Component (Already Created)
- `app/Views/components/date_range_filter.php`
- Usage: `<?= view('components/date_range_filter', ['id' => 'uniqueId']) ?>`

## ⚠️ Common Mistakes to AVOID

### ❌ WRONG: Manual ajax.data function
```javascript
var config = {
    ajax: {
        url: '...',
        data: function(d) {
            // Don't manually add date params here!
            d.start_date = window.currentDateRange.start;
            d.end_date = window.currentDateRange.end;
        }
    }
};
```

### ❌ WRONG: Using deprecated $.extend mixin
```javascript
// This is DEPRECATED and breaks ajax config
var table = $('#table').DataTable(
    $.extend({}, dataTableDateFilterMixin('id'), { ajax: {...} })
);
```

### ❌ WRONG: Forgetting to call applyDateFilterToConfig
```javascript
var config = { ajax: {...} };
// Missing: applyDateFilterToConfig(config, 'pickerId');
var table = $('#table').DataTable(config); // Won't have date filter!
```

### ❌ WRONG: Backend not using trait
```php
// Don't do manual date filtering
$startDate = $this->request->getPost('start_date');
if ($startDate) {
    $builder->where('date >=', $startDate);
}
// Use trait instead: $this->applyDateFilter($builder, 'date');
```

## ✅ Correct Implementation Examples

### Example 1: Quotations Page

**View:**
```javascript
var quotationsConfig = {
    processing: true,
    serverSide: true,
    ajax: {
        url: '<?= base_url('marketing/quotations/data') ?>',
        type: 'POST',
        error: function(xhr) {
            console.error('Error:', xhr.responseText);
        }
    },
    columns: [
        { data: 'quotation_number' },
        { data: 'prospect_name' }
    ]
};

applyDateFilterToConfig(quotationsConfig, 'quotationDateRangePicker');
quotationsTable = $('#quotationsTable').DataTable(quotationsConfig);
setupDataTableDateFilter(quotationsTable, 'quotationDateRangePicker', function(start, end) {
    loadStatistics(start, end);
});
```

**Controller:**
```php
public function getQuotationsData() {
    $builder = $this->quotationModel->builder();
    $this->applyDateFilter($builder, 'quotation_date');
    // ... rest of code
}

public function getQuotationStats() {
    $builder = $this->quotationModel->builder();
    $this->applyDateFilter($builder, 'quotation_date');
    // ... calculate stats
}
```

### Example 2: Customer Management Page

**View:**
```javascript
var customerConfig = {
    ajax: {
        url: '<?= base_url('marketing/customer-management/getCustomers') ?>',
        type: 'POST'
    },
    columns: [
        { data: 'customer_code' },
        { data: 'customer_name' }
    ]
};

applyDateFilterToConfig(customerConfig, 'customerDateRangePicker');
customerTable = $('#customerTable').DataTable(customerConfig);
setupDataTableDateFilter(customerTable, 'customerDateRangePicker', loadStatistics);
```

**Controller:**
```php
public function getCustomers() {
    $builder = $this->customerModel->builder();
    $this->applyDateFilter($builder, 'customers.created_at');
    // ... rest of code
}

public function getCustomerStats() {
    // Customers
    $customerBuilder = $this->customerModel->builder();
    $this->applyDateFilter($customerBuilder, 'created_at');
    $total = $customerBuilder->countAllResults();
    
    // Contracts (filtered by customer date)
    $contractBuilder = $this->db->table('kontrak k')
        ->join('customer_locations cl', 'k.customer_location_id = cl.id')
        ->join('customers c', 'cl.customer_id = c.id');
    $this->applyDateFilter($contractBuilder, 'c.created_at');
    $contracts = $contractBuilder->countAllResults();
    
    return $this->response->setJSON([
        'success' => true,
        'data' => ['total_customers' => $total, 'total_contracts' => $contracts]
    ]);
}
```

### Example 3: SPK Page (Non-DataTable)

**View:**
```javascript
function loadSpk(startDate, endDate) {
    var data = {};
    if (startDate && endDate) {
        data = { start_date: startDate, end_date: endDate };
    }
    
    fetch('<?= base_url('marketing/spk/list') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(j => {
        // Update UI
    });
}

// Setup callbacks
window.spkDateRangePickerOnRangeChange = function(start, end) {
    loadSpk(start, end);
};
window.spkDateRangePickerOnClear = function() {
    loadSpk(); // Load all
};
```

**Controller:**
```php
public function spkList() {
    $builder = $this->spkModel->builder();
    $this->applyDateFilter($builder, 'created_at');
    $data = $builder->get()->getResultArray();
    return $this->response->setJSON(['data' => $data]);
}
```

## 🔍 Debugging Checklist

When date filter not working, check:

### Frontend:
- [ ] `applyDateFilterToConfig()` called BEFORE DataTable init?
- [ ] `setupDataTableDateFilter()` called AFTER DataTable init?
- [ ] Console shows: "✅ Date filter applied to DataTable config"?
- [ ] Console shows: "📅 DataTable request WITH date filter" when date selected?
- [ ] No JavaScript errors in console?

### Backend:
- [ ] Controller has `use App\Traits\DateFilterTrait;`?
- [ ] Controller has `use DateFilterTrait;` in class body?
- [ ] `$this->applyDateFilter($builder, 'column')` called?
- [ ] Check logs: `writable/logs/log-*.log` for "DateFilter applied"?

### Network:
- [ ] Open DevTools → Network tab
- [ ] Select date range
- [ ] Check AJAX request shows `start_date` and `end_date` in payload?
- [ ] Response returns filtered data?

## 📊 Implementation Checklist

For each new page:

**Frontend (View):**
```
[ ] Add date filter component: <?= view('components/date_range_filter', ['id' => '...']) ?>
[ ] Create config object WITHOUT ajax.data
[ ] Call applyDateFilterToConfig(config, 'pickerId')
[ ] Initialize DataTable with config
[ ] Call setupDataTableDateFilter(table, 'pickerId', callback)
```

**Backend (Controller):**
```
[ ] Add: use App\Traits\DateFilterTrait;
[ ] Add: use DateFilterTrait; (in class)
[ ] In getData: $this->applyDateFilter($builder, 'column')
[ ] In getStats: $this->applyDateFilter($builder, 'column')
[ ] Test with Postman/curl to verify filtering works
```

**Testing:**
```
[ ] Page loads without errors
[ ] DataTable shows all data initially
[ ] Select "Last 7 Days" → table filters
[ ] Statistics update (if applicable)
[ ] Click Clear → shows all data again
[ ] Check console logs show correct flow
```

## 🎓 Key Principles

1. **Separation of Concerns:**
   - `global-daterange.js` = UI picker only
   - `datatable-datefilter-mixin.js` = DataTable integration only
   - `DateFilterTrait.php` = Backend filtering only

2. **Single Responsibility:**
   - Each component does ONE thing well
   - No component knows about others' internals

3. **Consistency:**
   - Same approach on ALL pages
   - Same function names everywhere
   - Same callback pattern

4. **Fail-Safe:**
   - Works without date filter (shows all data)
   - Logs errors to console
   - Graceful degradation

## 📝 Migration Guide

Converting existing page to standard approach:

### Before (Mixed Approach):
```javascript
// Messy manual implementation
quotationsTable = $('#quotationsTable').DataTable({
    ajax: {
        url: '...',
        data: function(d) {
            d.start_date = getCurrentStartDate();
            d.end_date = getCurrentEndDate();
        }
    }
});

// Manual callbacks
window.myPickerCallback = function(start, end) {
    window.currentStart = start;
    window.currentEnd = end;
    quotationsTable.ajax.reload();
    loadStats();
};
```

### After (Standard Approach):
```javascript
// Clean, consistent
var config = {
    ajax: { url: '...', type: 'POST' },
    columns: [...]
};
applyDateFilterToConfig(config, 'quotationDateRangePicker');
quotationsTable = $('#quotationsTable').DataTable(config);
setupDataTableDateFilter(quotationsTable, 'quotationDateRangePicker', loadStats);
```

**Result:** 20+ lines → 4 lines, guaranteed to work!

## 🚀 Quick Reference

**Add date filter to new page:**
```bash
# 1. View: Add component
<?= view('components/date_range_filter', ['id' => 'myDatePicker']) ?>

# 2. JavaScript: Standard 4-line pattern
var config = { ajax: {...}, columns: [...] };
applyDateFilterToConfig(config, 'myDatePicker');
var table = $('#table').DataTable(config);
setupDataTableDateFilter(table, 'myDatePicker', callback);

# 3. Controller: Add trait
use App\Traits\DateFilterTrait;
use DateFilterTrait;
$this->applyDateFilter($builder, 'date_column');
```

Done! 🎉

---

**Last Updated:** December 6, 2025  
**Status:** ✅ Production Ready  
**Pages Implemented:** Quotations, Customer Management, SPK
