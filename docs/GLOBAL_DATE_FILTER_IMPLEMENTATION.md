# Global Date Range Filter Implementation Guide

## 🎯 Overview
Global date range filtering system yang dapat digunakan di semua halaman dengan minimal code. Terdiri dari:
1. **Backend Trait** (`DateFilterTrait`) - Auto-handle filtering di controller
2. **Frontend Mixin** (`dataTableDateFilterMixin`) - Auto-handle DataTable integration
3. **Component** (`date_range_filter.php`) - Reusable UI component

## 📦 Files Created

### Backend
- `app/Traits/DateFilterTrait.php` - Trait untuk controller
- Updated: `app/Controllers/Marketing.php` - Example implementation

### Frontend
- `public/assets/js/datatable-datefilter-mixin.js` - DataTable mixin
- `public/assets/js/global-daterange.js` - Date picker initialization
- `app/Views/components/date_range_filter.php` - UI component

## 🚀 Quick Start Guide

### Step 1: Add Component to View

```php
<!-- Your page view file -->
<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<!-- Add date filter (optional: positioned right) -->
<div class="row mt-3">
    <div class="col-md-12 text-end">
        <div class="d-inline-block">
            <?= view('components/date_range_filter', [
                'id' => 'myPageDatePicker'  // Unique ID per page
            ]) ?>
        </div>
    </div>
</div>

<!-- Your content here -->
```

### Step 2: Initialize DataTable with Mixin

```javascript
<script>
var myTable;

$(document).ready(function() {
    // Initialize DataTable with date filter mixin
    myTable = $('#myTable').DataTable(
        $.extend({}, dataTableDateFilterMixin('myPageDatePicker'), {
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= base_url('your/controller/getData') ?>',
                type: 'POST'
            },
            columns: [
                // your columns
            ]
        })
    );
    
    // Setup automatic reload on date change
    setupDataTableDateFilter(myTable, 'myPageDatePicker', function(startDate, endDate) {
        // Optional: Do something when date changes
        console.log('Date changed:', startDate, endDate);
        
        // Example: Reload statistics
        if (typeof loadStatistics === 'function') {
            loadStatistics(startDate, endDate);
        }
    });
});
</script>
```

### Step 3: Add Trait to Controller

```php
<?php

namespace App\Controllers;

use App\Traits\DateFilterTrait;

class YourController extends BaseController
{
    use DateFilterTrait;
    
    public function getData()
    {
        // ... your DataTable setup code ...
        
        $builder = $this->yourModel->builder();
        
        // Apply date filter (ONE LINE!)
        $this->applyDateFilter($builder, 'your_date_column');
        
        // Continue with search, pagination, etc.
        // ... rest of your code ...
    }
}
```

## 📋 Complete Examples

### Example 1: Simple Implementation (Quotations Page)

**View:** `app/Views/marketing/quotations.php`
```php
<!-- Date filter at top right -->
<div class="row mt-3">
    <div class="col-md-12 text-end">
        <div class="d-inline-block">
            <?= view('components/date_range_filter', [
                'id' => 'quotationDateRangePicker'
            ]) ?>
        </div>
    </div>
</div>
```

**JavaScript:**
```javascript
quotationsTable = $('#quotationsTable').DataTable(
    $.extend({}, dataTableDateFilterMixin('quotationDateRangePicker'), {
        ajax: { url: '<?= base_url('marketing/quotations/data') ?>' },
        // ... other options
    })
);

setupDataTableDateFilter(quotationsTable, 'quotationDateRangePicker', function(startDate, endDate) {
    loadStatistics(startDate, endDate);
});
```

**Controller:** `app/Controllers/Marketing.php`
```php
use App\Traits\DateFilterTrait;

class Marketing extends BaseController
{
    use DateFilterTrait;
    
    public function getQuotationsData()
    {
        $builder = $this->quotationModel->builder();
        $this->applyDateFilter($builder, 'quotation_date');
        // ... rest of code
    }
}
```

### Example 2: Custom Date Column

```php
// In your controller
$this->applyDateFilter($builder, 'created_at'); // Filter by created_at
$this->applyDateFilter($builder, 'updated_at'); // Filter by updated_at
$this->applyDateFilter($builder, 'contract_date'); // Any date column
```

### Example 3: With Statistics Reload

```javascript
setupDataTableDateFilter(myTable, 'myDatePicker', function(startDate, endDate) {
    // Reload statistics
    loadMyStatistics(startDate, endDate);
    
    // Update chart
    updateMyChart(startDate, endDate);
    
    // Any custom action
    console.log('Filtering from', startDate, 'to', endDate);
});
```

## 🔧 Advanced Usage

### Check if Filter is Active

```php
// In controller
if ($this->hasDateFilter()) {
    // Date filter is active
    $params = $this->getDateFilterParams();
    log_message('info', 'Filtering from ' . $params['start_date'] . ' to ' . $params['end_date']);
}
```

### Custom Parameter Names

```php
// Use different POST parameter names
$this->applyDateFilter($builder, 'date_column', 'my_start_param', 'my_end_param');
```

### Access Date Filter in Frontend

```javascript
// Check current filter
console.log(window.currentDateRange); 
// Output: { start: "2025-12-01", end: "2025-12-31" }

// Access picker instance
var pickerInstance = window.myDatePickerInstance;
```

## 📱 Pages Ready to Migrate

### High Priority
- ✅ `marketing/quotations` - **DONE**
- ⏳ `marketing/customer_management` - Ready to implement
- ⏳ `marketing/spk` - Ready to implement
- ⏳ `marketing/kontrak` - Ready to implement

### Medium Priority
- ⏳ `service/spk_service`
- ⏳ `warehouse/inventory`
- ⏳ Any page with DataTable and date-based data

## 🎨 Customization

### Custom Label
```php
<?= view('components/date_range_filter', [
    'id' => 'myPicker',
    'label' => 'Select Report Period' // Custom label
]) ?>
```

### Custom Ranges
```javascript
// Define before picker initialization
window.myPickerOptions = {
    ranges: {
        'Last 90 Days': [moment().subtract(89, 'days'), moment()],
        'This Year': [moment().startOf('year'), moment().endOf('year')]
    }
};
```

### Custom Styling
```css
/* Override component styles */
.date-range-filter-inline {
    min-width: 320px; /* Wider input */
}

.date-range-filter-inline label {
    font-size: 14px; /* Smaller label */
}
```

## 🐛 Troubleshooting

### Date Filter Not Working?

1. **Check Console Logs**
   - Should see: "DateRangePicker loaded successfully..."
   - Should see: "DataTable request with date filter: ..."

2. **Verify Trait is Added**
   ```php
   use App\Traits\DateFilterTrait; // At top
   use DateFilterTrait; // In class
   ```

3. **Check POST Parameters**
   ```php
   // In controller
   log_message('debug', 'POST data: ' . print_r($this->request->getPost(), true));
   ```

4. **Verify Mixin is Loaded**
   ```javascript
   // In browser console
   console.log(typeof dataTableDateFilterMixin); // Should be "function"
   ```

### Statistics Not Updating?

Make sure you provide callback to `setupDataTableDateFilter()`:
```javascript
setupDataTableDateFilter(table, 'pickerId', function(start, end) {
    loadStatistics(start, end); // Your statistics function
});
```

## 📊 Benefits

### Before (Manual Implementation)
```javascript
// ~30 lines of code per page
ajax: {
    data: function(d) {
        d.start_date = currentDateRange.start;
        d.end_date = currentDateRange.end;
    }
}

window.myPickerOnRangeChange = function(start, end) {
    currentDateRange.start = start;
    currentDateRange.end = end;
    table.ajax.reload();
    loadStatistics(start, end);
};
// ... more code
```

### After (With Mixin)
```javascript
// ~3 lines of code per page
$.extend({}, dataTableDateFilterMixin('myPicker'), { /* options */ })
setupDataTableDateFilter(table, 'myPicker', loadStatistics);
```

**Savings:** ~90% less code per page!

## 🔄 Migration Checklist

When adding date filter to existing page:

- [ ] Add date filter component to view
- [ ] Wrap DataTable init with `dataTableDateFilterMixin()`
- [ ] Add `setupDataTableDateFilter()` after DataTable init
- [ ] Add `DateFilterTrait` to controller
- [ ] Call `$this->applyDateFilter()` in getData method
- [ ] Test: Select date range → DataTable filters
- [ ] Test: Click "Clear" → Shows all data
- [ ] Test: Statistics update (if applicable)

## 📚 API Reference

### DateFilterTrait Methods

```php
// Apply filter to query builder
protected function applyDateFilter(
    $builder,                    // Query builder instance
    $dateColumn = 'created_at',  // Column to filter
    $startParam = 'start_date',  // POST param for start
    $endParam = 'end_date'       // POST param for end
)

// Get filter parameters
protected function getDateFilterParams() // Returns ['start_date' => ..., 'end_date' => ...]

// Check if filter active
protected function hasDateFilter() // Returns bool
```

### Frontend Functions

```javascript
// Create DataTable config with date filter
dataTableDateFilterMixin(pickerId, additionalConfig)

// Setup automatic reload
setupDataTableDateFilter(tableInstance, pickerId, onFilterChange)
```

## 💡 Tips & Best Practices

1. **Use Unique Picker IDs** - One per page to avoid conflicts
2. **Position Filter Consistently** - Right side for better UX
3. **Provide User Feedback** - Show loading state during filter
4. **Log Filter Actions** - Helps with debugging
5. **Test Edge Cases** - Empty data, invalid dates, etc.

## 🎯 Next Steps

1. Migrate all remaining pages (see checklist above)
2. Add month/year picker variants if needed
3. Add filter presets (MTD, YTD, etc.)
4. Integrate with export functions

## 📞 Support

Issues? Check:
- Console logs for errors
- Network tab for API calls
- Database logs for query execution

Happy filtering! 🎉
