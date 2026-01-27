# Global Date Filter - Troubleshooting & Fixes

## Issues Fixed (December 6, 2025)

### Issue 1: DataTable Showing No Data
**Symptom:** Quotations and Customer Management pages showing empty DataTables

**Root Cause:** 
- Missing comma after `ajax` object in DataTable initialization
- `$.extend()` not properly merging mixin config with page config

**Fix Applied:**
```javascript
// BEFORE (WRONG):
quotationsTable = $('#quotationsTable').DataTable(
    $.extend({}, dataTableDateFilterMixin('quotationDateRangePicker'), {
        ajax: { ... }
    },  // Missing comma here!
    columns: [ ... ]
```

```javascript
// AFTER (CORRECT):
quotationsTable = $('#quotationsTable').DataTable(
    $.extend({}, dataTableDateFilterMixin('quotationDateRangePicker'), {
        ajax: { ... },
        columns: [ ... ]  // Proper structure
    })
);
```

**Files Fixed:**
- ✅ `app/Views/marketing/quotations.php` (lines 976-1041)
- ✅ `app/Views/marketing/customer_management.php` (lines 1059-1165)

### Issue 2: Date Filter Not Filtering Data
**Symptom:** Data showing but not filtering when date range selected

**Root Cause:**
- Mixin sending `null` values for `start_date` and `end_date` even when no filter active
- Backend trait applying empty filter conditions

**Fix Applied:**
```javascript
// datatable-datefilter-mixin.js - Only send params if dates exist
data: function(d) {
    if (window.currentDateRange && 
        window.currentDateRange.start && 
        window.currentDateRange.end) {
        d.start_date = window.currentDateRange.start;
        d.end_date = window.currentDateRange.end;
    }
    // Don't send null values!
    return d;
}
```

**Files Fixed:**
- ✅ `public/assets/js/datatable-datefilter-mixin.js` (lines 25-40)
- ✅ `public/assets/js/global-daterange.js` (added early initialization)

### Issue 3: window.currentDateRange Undefined Error
**Symptom:** Console errors about `currentDateRange` being undefined

**Root Cause:**
- Global variable not initialized before mixin tries to access it

**Fix Applied:**
```javascript
// global-daterange.js - Initialize immediately on script load
if (typeof window.currentDateRange === 'undefined') {
    window.currentDateRange = { start: null, end: null };
    console.log('✅ Global date range storage initialized');
}
```

**Files Fixed:**
- ✅ `public/assets/js/global-daterange.js` (lines 8-12)

## Verification Steps

### 1. Check Browser Console
Open Developer Tools (F12) and look for these logs:

**On Page Load:**
```
✅ Global date range storage initialized
DateRangePicker loaded successfully, initializing...
Date Range Picker initialized: quotationDateRangePicker
DataTable date filter setup complete for: quotationDateRangePicker
📅 DataTable request WITHOUT date filter (showing all data)
```

**After Selecting Date Range:**
```
Date range selected: 2025-12-01 to 2025-12-06
Date range changed, reloading DataTable...
📅 DataTable request WITH date filter: {start_date: "2025-12-01", end_date: "2025-12-06"}
```

### 2. Test Quotations Page
1. Navigate to `/public/marketing/quotations`
2. **Expected:** DataTable shows all quotations
3. Click date range picker → Select "Last 7 Days"
4. **Expected:** DataTable reloads and shows only quotations from last 7 days
5. Statistics cards update to match filtered data
6. Click Clear (cancel button)
7. **Expected:** DataTable shows all data again

### 3. Test Customer Management
1. Navigate to `/public/marketing/customer-management`
2. Repeat same tests as quotations
3. **Expected:** Same behavior - filter works, clear works

### 4. Test SPK Page
1. Navigate to `/public/marketing/spk`
2. SPK uses different implementation (no DataTable)
3. Select date range
4. **Expected:** SPK list filters by created_at date
5. Statistics update correctly

## Common Error Messages & Solutions

### Error: "DataTable AJAX error"
**Check:**
- Browser console for actual error message
- Network tab → Check XHR request to see POST data
- Server logs in `writable/logs/`

**Solution:**
- Ensure controller has `use DateFilterTrait;`
- Verify route exists for AJAX endpoint
- Check database column name matches trait parameter

### Error: "$ is not defined" or "moment is not defined"
**Check:**
- `app/Views/layouts/base.php` has correct script order:
  1. jQuery
  2. moment.js
  3. daterangepicker.js
  4. global-daterange.js
  5. datatable-datefilter-mixin.js

**Solution:**
```php
<!-- Correct order in base.php -->
<script src="<?= base_url('assets/js/jquery-3.7.1.min.js') ?>"></script>
<script src="<?= base_url('assets/js/moment.min.js') ?>"></script>
<script src="<?= base_url('assets/js/daterangepicker.min.js') ?>"></script>
<script src="<?= base_url('assets/js/global-daterange.js?v=') . time() ?>"></script>
<script src="<?= base_url('assets/js/datatable-datefilter-mixin.js?v=') . time() ?>"></script>
```

### Error: DataTable shows data but doesn't filter
**Check:**
- Console log shows "WITHOUT date filter" even after selecting dates
- Check if callbacks are registered: `window.quotationDateRangePickerOnRangeChange`

**Solution:**
```javascript
// Ensure setupDataTableDateFilter is called AFTER DataTable init
setupDataTableDateFilter(quotationsTable, 'quotationDateRangePicker', function(start, end) {
    loadStatistics(start, end);
});
```

### Statistics not updating on filter change
**Check:**
- Custom callback provided to `setupDataTableDateFilter()`
- Statistics endpoint accepts POST parameters

**Solution:**
```javascript
// Add callback to reload statistics
setupDataTableDateFilter(myTable, 'myDatePicker', function(startDate, endDate) {
    loadStatistics(startDate, endDate); // Your stats function
});
```

```php
// Controller stats method must use trait
public function getStats() {
    $builder = $this->model->builder();
    $this->applyDateFilter($builder, 'date_column');
    // ... calculate stats
}
```

## Debug Mode

### Enable Verbose Logging
Add to beginning of page JavaScript:
```javascript
// Enable verbose date filter debugging
window.DEBUG_DATE_FILTER = true;
```

Then update mixin to respect debug flag:
```javascript
if (window.DEBUG_DATE_FILTER) {
    console.log('🔍 DEBUG - Current date range:', window.currentDateRange);
    console.log('🔍 DEBUG - DataTable data object:', d);
}
```

### Check Backend Filtering
Add to controller method:
```php
public function getData() {
    $params = $this->getDateFilterParams();
    log_message('debug', 'Date filter params: ' . json_encode($params));
    
    // ... rest of code
}
```

Check logs in `writable/logs/log-YYYY-MM-DD.log`

## Performance Considerations

### Date Filter Impact
- Date filtering adds WHERE clause to SQL query
- Should have index on date columns for performance
- Example: `ALTER TABLE quotations ADD INDEX idx_quotation_date (quotation_date);`

### Recommended Indexes
```sql
-- Quotations
ALTER TABLE quotations ADD INDEX idx_quotation_date (quotation_date);

-- Customers
ALTER TABLE customers ADD INDEX idx_created_at (created_at);

-- SPK
ALTER TABLE spk ADD INDEX idx_created_at (created_at);
```

## File Modification Summary

| File | Status | Description |
|------|--------|-------------|
| `app/Traits/DateFilterTrait.php` | ✅ Working | Backend filtering logic |
| `public/assets/js/datatable-datefilter-mixin.js` | ✅ Fixed | Proper null handling |
| `public/assets/js/global-daterange.js` | ✅ Fixed | Early initialization |
| `app/Views/marketing/quotations.php` | ✅ Fixed | Syntax error corrected |
| `app/Views/marketing/customer_management.php` | ✅ Fixed | Syntax error corrected |
| `app/Views/marketing/spk.php` | ✅ Working | Custom implementation |
| `app/Controllers/Marketing.php` | ✅ Working | Uses trait correctly |
| `app/Controllers/CustomerManagementController.php` | ✅ Working | Uses trait correctly |

## Testing Checklist

- [x] Quotations page loads DataTable with data
- [x] Date filter applies correctly on Quotations
- [x] Statistics update when date filter changes
- [x] Clear filter shows all data again
- [x] Customer Management DataTable works
- [x] Customer Management date filter works
- [x] SPK page loads and filters correctly
- [x] No console errors on any page
- [x] Network requests show correct POST parameters

## Support

If issues persist after applying these fixes:

1. **Clear Browser Cache** - Hard refresh (Ctrl+Shift+R)
2. **Check PHP Logs** - `writable/logs/log-*.log`
3. **Verify Database** - Test queries manually in PHPMyAdmin
4. **Test in Incognito** - Rule out browser extension issues

## Changelog

**2025-12-06 - Initial Fixes**
- Fixed DataTable initialization syntax errors
- Fixed mixin to not send null date parameters
- Added early initialization of window.currentDateRange
- Updated documentation with troubleshooting guide
