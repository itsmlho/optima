# Global Date Range Filter Component

## Overview
Komponen global untuk filter date range menggunakan CoreUI Date Range Picker yang dapat digunakan di semua halaman OPTIMA.

## Features
- ✅ Dual calendar display (Start date | End date)
- ✅ Custom preset ranges (Today, Yesterday, Last 7 Days, Last 30 Days, This Month, Last Month)
- ✅ Global callbacks untuk handle date changes
- ✅ Responsive design
- ✅ Easy integration di semua halaman

## Files Created
1. **Component View**: `app/Views/components/date_range_filter.php`
2. **Global Script**: `public/assets/js/global-daterange.js`
3. **Loaded in**: `app/Views/layouts/base.php`

## How to Use

### 1. Basic Usage
Tambahkan component di view file Anda:

```php
<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<!-- Include Date Range Filter -->
<?= view('components/date_range_filter') ?>

<!-- Your content here -->
```

### 2. Custom ID dan Label
```php
<?= view('components/date_range_filter', [
    'id' => 'myCustomDatePicker',
    'label' => 'Select Date Range for Report'
]) ?>
```

### 3. Add Callbacks untuk Handle Date Changes

Di page-specific JavaScript Anda:

```javascript
// Callback saat start date berubah
window.myCustomDatePickerOnStartChange = function(date) {
    console.log('Start date:', date);
};

// Callback saat end date berubah
window.myCustomDatePickerOnEndChange = function(date) {
    console.log('End date:', date);
};

// Callback saat range lengkap (kedua tanggal sudah dipilih)
window.myCustomDatePickerOnRangeChange = function(startDate, endDate) {
    console.log('Date range:', startDate, 'to', endDate);
    
    // Convert to YYYY-MM-DD format
    const start = new Date(startDate).toISOString().split('T')[0];
    const end = new Date(endDate).toISOString().split('T')[0];
    
    // Reload your data with date filter
    loadDataWithDateRange(start, end);
};
```

### 4. Custom Options (Optional)
Jika ingin override default options:

```javascript
// Define sebelum component di-render
window.myCustomDatePickerOptions = {
    locale: 'id-ID',
    ranges: {
        'Hari Ini': [new Date(), new Date()],
        'Minggu Ini': [
            new Date(new Date().setDate(new Date().getDate() - 7)),
            new Date()
        ]
    }
};
```

### 5. Access Picker Instance
```javascript
// Access the CoreUI picker instance
const pickerInstance = window.myCustomDatePickerInstance;

// Clear selection
pickerInstance.clear();

// Set dates programmatically
pickerInstance.setStartDate(new Date('2025-01-01'));
pickerInstance.setEndDate(new Date('2025-01-31'));
```

## Example Implementation

### Quotations Page
```php
<!-- quotations.php -->
<?= view('components/date_range_filter', [
    'id' => 'quotationDateRangePicker',
    'label' => 'Filter by Date Range'
]) ?>
```

```javascript
<script>
// Callbacks for quotations
window.quotationDateRangePickerOnRangeChange = function(startDate, endDate) {
    const start = new Date(startDate).toISOString().split('T')[0];
    const end = new Date(endDate).toISOString().split('T')[0];
    
    // Reload statistics
    loadStatistics(start, end);
    
    // Reload DataTable
    if (typeof quotationsTable !== 'undefined') {
        quotationsTable.ajax.reload();
    }
};
</script>
```

### Reports Page
```php
<!-- reports.php -->
<?= view('components/date_range_filter', [
    'id' => 'reportDateRangePicker',
    'label' => 'Report Period'
]) ?>
```

```javascript
<script>
window.reportDateRangePickerOnRangeChange = function(startDate, endDate) {
    generateReport(startDate, endDate);
};
</script>
```

## Available Preset Ranges

Default preset ranges yang tersedia:
- **Today**: Hari ini
- **Yesterday**: Kemarin
- **Last 7 Days**: 7 hari terakhir
- **Last 30 Days**: 30 hari terakhir
- **This Month**: Bulan ini
- **Last Month**: Bulan lalu

## Styling

Component sudah include styling untuk proper display. Jika perlu custom styling:

```css
.global-date-range-picker {
    /* Your custom styles */
}

.date-range-picker .calendars {
    gap: 2rem; /* Space between calendars */
}
```

## Browser Console Debug

Component akan log ke console:
- "Date Range Picker initialized: [pickerId]" - saat berhasil init
- "Start date changed: [date]" - saat start date dipilih
- "End date changed: [date]" - saat end date dipilih

## Troubleshooting

### Picker tidak muncul?
1. Check browser console untuk errors
2. Pastikan CoreUI CSS dan JS sudah loaded (cek Network tab)
3. Pastikan element dengan class `global-date-range-picker` ada di DOM

### Callbacks tidak dipanggil?
1. Pastikan nama callback sesuai format: `[pickerId]OnStartChange`, `[pickerId]OnEndChange`, `[pickerId]OnRangeChange`
2. Check console log untuk errors
3. Pastikan callbacks didefinisikan sebagai global window function

### Styling tidak sesuai?
1. Check apakah CoreUI CSS sudah loaded
2. Clear browser cache
3. Check untuk CSS conflicts dengan styles lain

## Dependencies
- CoreUI CSS: `@coreui/coreui@5.2.0/dist/css/coreui.min.css`
- CoreUI JS: `@coreui/coreui@5.2.0/dist/js/coreui.bundle.min.js`
- Bootstrap Icons: untuk icon calendar

## License
Internal use only - PT Sarana Mitra Luas Tbk
