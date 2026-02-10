# 📚 OPTIMA UI/UX Helper System - Developer Guide
**Version**: 1.0.0  
**Date**: 2026-02-09  
**Status**: ✅ Production Ready  

---

## 🎯 Quick Start

### What's New?
After comprehensive UI/UX analysis, we've standardized **all** UI components:
- ✅ **Buttons**: Consistent colors, sizes, icons
- ✅ **Forms**: Helper functions for all field types
- ✅ **Tables**: Standard DataTable structure
- ✅ **JavaScript**: Select2, loading states, toasts
- ✅ **Bootstrap 5**: Fully migrated from Bootstrap 4

---

## 📦 Available Helpers

### 1. **ui_helper.php** (Auto-loaded)
Buttons, badges, alerts, empty states, action buttons

### 2. **form_field_helper.php** (Auto-loaded) ⭐ NEW
Text inputs, selects, textareas, checkboxes, radios, file uploads

### 3. **datatable_helper.php** (Auto-loaded)
DataTable config, column definitions, standard table HTML

### 4. **ui_helpers.js** ⭐ NEW
Select2 initialization, button loading, toasts, confirmations

---

## 🔘 Button Usage

### Basic Buttons
```php
// Primary button (Add/Create actions)
<?= ui_button('add', 'Add Customer') ?>

// Secondary button (Cancel/Close)
<?= ui_button('cancel', 'Cancel', ['color' => 'secondary']) ?>

// Edit button
<?= ui_button('edit', 'Edit Record', ['onclick' => 'editRecord(123)']) ?>

// Delete button
<?= ui_button('delete', 'Delete', ['onclick' => 'deleteRecord(123)']) ?>

// Export button
<?= ui_button('export', 'Export', [
    'href' => base_url('export/customers'),
    'color' => 'outline-success'
]) ?>
```

### Button Sizes
```php
// Small (recommended for headers, tables)
<?= ui_button('add', 'Add', ['size' => 'sm']) ?>

// Normal (default, for modals, forms)
<?= ui_button('save', 'Save Changes') ?>

// Large (for prominent CTAs)
<?= ui_button('subscribe', 'Subscribe Now', ['size' => 'lg']) ?>
```

### Button with Icons
```php
// Icon-only button (for tables, compact spaces)
<?= ui_button('edit', '', [
    'icon-only' => true,
    'size' => 'sm',
    'title' => 'Edit Record'
]) ?>

// Custom icon
<?= ui_button('custom', 'Download Report', [
    'icon' => 'fas fa-file-download'
]) ?>
```

### Link Buttons
```php
// Button styled as link
<?= ui_button('view', 'View Details', [
    'href' => base_url('customers/view/123')
]) ?>

// Open in new tab
<?= ui_button('view', 'Open Report', [
    'href' => base_url('reports/annual'),
    'target' => '_blank'
]) ?>
```

### Button Groups
```php
<div class="d-flex gap-2">
    <?= ui_button('save', 'Save', ['size' => 'sm']) ?>
    <?= ui_button('cancel', 'Cancel', ['color' => 'secondary', 'size' => 'sm']) ?>
</div>
```

---

## 📝 Form Field Usage

### Text Input
```php
<?= form_field('text', 'customer_name', 'Customer Name', [
    'required' => true,
    'placeholder' => 'Enter customer name',
    'helpText' => 'Full legal name of the customer'
]) ?>
```

### Email/Phone/Number
```php
<?= form_field('email', 'email', 'Email Address', [
    'required' => true,
    'placeholder' => 'customer@example.com'
]) ?>

<?= form_field('tel', 'phone', 'Phone Number', [
    'pattern' => '[0-9]{10,13}',
    'placeholder' => '08123456789'
]) ?>

<?= form_field('number', 'quantity', 'Quantity', [
    'min' => 1,
    'max' => 100,
    'step' => 1,
    'required' => true
]) ?>
```

### Date Field
```php
<?= form_field('date', 'contract_date', 'Contract Date', [
    'required' => true,
    'min' => date('Y-m-d'),
    'max' => date('Y-m-d', strtotime('+1 year'))
]) ?>
```

### Select Dropdown
```php
<?= form_select_field('status', 'Status', [
    'active' => 'Active',
    'pending' => 'Pending',
    'inactive' => 'Inactive'
], [
    'required' => true,
    'selected' => 'active'
]) ?>
```

### Textarea
```php
<?= form_textarea_field('notes', 'Notes', [
    'rows' => 4,
    'placeholder' => 'Enter additional notes...',
    'maxlength' => 500
]) ?>
```

### Checkbox
```php
<?= form_checkbox_field('is_active', 'Active Status', [
    'checked' => true,
    'helpText' => 'Check to activate customer account'
]) ?>

// Switch style
<?= form_checkbox_field('email_notifications', 'Email Notifications', [
    'switch' => true,
    'checked' => true
]) ?>
```

### Radio Group
```php
<?= form_radio_group('payment_method', 'Payment Method', [
    'cash' => 'Cash',
    'transfer' => 'Bank Transfer',
    'credit' => 'Credit Card'
], [
    'required' => true,
    'selected' => 'transfer'
]) ?>

// Inline layout
<?= form_radio_group('gender', 'Gender', [
    'M' => 'Male',
    'F' => 'Female'
], [
    'inline' => true
]) ?>
```

### File Upload
```php
<?= form_file_field('document', 'Upload Document', [
    'accept' => '.pdf,.doc,.docx',
    'required' => true,
    'helpText' => 'Accepted formats: PDF, DOC, DOCX'
]) ?>

// Multiple files
<?= form_file_field('images', 'Upload Images', [
    'accept' => 'image/*',
    'multiple' => true
]) ?>
```

### Form Row (Multiple Fields Side-by-Side)
```php
<?= form_row([
    form_field('text', 'first_name', 'First Name', ['required' => true]),
    form_field('text', 'last_name', 'Last Name', ['required' => true])
], ['col-md-6', 'col-md-6']) ?>

// Auto-split equally
<?= form_row([
    form_field('text', 'city', 'City'),
    form_field('text', 'postal_code', 'Postal Code'),
    form_field('text', 'country', 'Country')
]) ?>
```

---

## 📊 DataTable Usage

### Generate Standard Table HTML
```php
<?= dt_table_standard('customersTable', [
    'Customer Name',
    'Email',
    'Phone',
    'Status',
    'Actions'
]) ?>
```

### With Options
```php
<?= dt_table_standard('productsTable', [
    'SKU',
    'Product Name',
    'Category',
    'Price',
    'Stock'
], [
    'class' => 'table-bordered',  // Additional classes
    'caption' => 'Product Inventory',
    'footer' => true  // Mirror header in footer
]) ?>
```

### JavaScript Initialization
```javascript
$(document).ready(function() {
    $('#customersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '<?= base_url('api/customers') ?>',
        columns: [
            { data: 'name' },
            { data: 'email' },
            { data: 'phone' },
            { data: 'status', render: function(data) {
                return uiBadge(data);  // Use badge helper
            }},
            { data: 'actions', orderable: false }
        ]
    });
});
```

---

## 🎨 Badge Usage

### PHP (Static Content)
```php
<?= ui_badge('success', 'Active') ?>
<?= ui_badge('warning', 'Pending') ?>
<?= ui_badge('danger', 'Cancelled') ?>
```

### JavaScript (Dynamic Content)
```javascript
// Status badge with auto-color mapping
const badge = uiBadge('success', 'Approved');

// In DataTable render
{
    data: 'status',
    render: function(data) {
        return uiBadge(data);  // Auto-maps status to color
    }
}

// Custom color
const badge = uiBadge('custom', 'VIP', {
    class: 'bg-purple text-white'
});
```

---

## 🛠️ JavaScript Helpers

### Select2 Initialization

#### Standard Select2
```javascript
// Basic
initSelect2('#statusSelect');

// With options
initSelect2('#categorySelect', {
    placeholder: 'Choose a category',
    allowClear: false
});
```

#### AJAX Select2
```javascript
// Basic AJAX
initSelect2Ajax('#customerSelect', '<?= base_url('api/customers/search') ?>', {
    placeholder: 'Search customer...',
    minimumInputLength: 3
});

// With custom data mapping
initSelect2Ajax('#userSelect', '<?= base_url('api/users') ?>', {
    dataMapper: function(data) {
        return data.users.map(u => ({
            id: u.user_id,
            text: u.full_name + ' (' + u.email + ')'
        }));
    },
    paramMapper: function(params) {
        return {
            search: params.term,
            page: params.page,
            role: 'customer'
        };
    }
});
```

### Button Loading States
```javascript
// Show loading
btnLoading('#saveButton', 'Saving...');

// Perform AJAX
$.ajax({
    url: '<?= base_url('api/save') ?>',
    method: 'POST',
    data: formData,
    success: function(response) {
        btnReset('#saveButton');  // Reset button
        showToast('Data saved successfully!', 'success');
    },
    error: function() {
        btnReset('#saveButton');
        showToast('Failed to save data', 'error');
    }
});
```

### Toast Notifications
```javascript
// Success
showToast('Data saved successfully!', 'success');

// Error
showToast('Please fix the errors', 'error');

// Warning
showToast('Your session will expire soon', 'warning', {
    duration: 5000
});

// Info with title
showToast('New updates available', 'info', {
    title: 'System Update',
    duration: 4000
});
```

### Confirmation Dialogs
```javascript
confirmAction({
    title: 'Delete Customer?',
    text: 'This action cannot be undone',
    type: 'error',
    confirmText: 'Yes, Delete',
    cancelText: 'Cancel',
    onConfirm: function() {
        // Perform delete
        deleteCustomer(customerId);
    }
});
```

### Form Validation
```javascript
enableFormValidation('#customerForm', {
    onSubmit: function(form) {
        // Valid form - submit via AJAX
        const formData = $(form).serialize();
        
        $.ajax({
            url: '<?= base_url('customers/save') ?>',
            method: 'POST',
            data: formData,
            success: function(response) {
                showToast('Customer saved!', 'success');
                clearFormValidation('#customerForm');
            }
        });
        
        return false;  // Prevent default submit
    },
    onInvalid: function(form) {
        showToast('Please fix the errors', 'error');
    }
});
```

### Loading Overlay
```javascript
// Show overlay on element
showLoadingOverlay('#dataContainer', 'Loading data...');

// Perform AJAX
$.ajax({
    url: '<?= base_url('api/data') ?>',
    success: function(data) {
        hideLoadingOverlay('#dataContainer');
        // Render data
    }
});
```

### Utility Functions
```javascript
// Copy to clipboard
copyToClipboard('QUO-2026-001', 'Quotation number copied!');

// Format number
formatNumber(1234567);  // "1,234,567"
formatNumber(1234.567, 2);  // "1,234.57"

// Format currency
formatCurrency(1000000);  // "Rp 1.000.000"
formatCurrency(1500000, false);  // "1.500.000"

// Debounce (for search inputs)
const searchCustomers = debounce(function(query) {
    // Perform AJAX search
}, 300);

$('#searchInput').on('keyup', function() {
    searchCustomers(this.value);
});
```

---

## 🎨 Bootstrap 5 Migration Cheat Sheet

| Bootstrap 4 | Bootstrap 5 | Notes |
|-------------|-------------|-------|
| `mr-*` | `me-*` | Margin right → Margin end |
| `ml-*` | `ms-*` | Margin left → Margin start |
| `pr-*` | `pe-*` | Padding right → Padding end |
| `pl-*` | `ps-*` | Padding left → Padding start |
| `border-left` | `border-start` | Left border |
| `border-right` | `border-end` | Right border |
| `text-left` | `text-start` | Text align left |
| `text-right` | `text-end` | Text align right |
| `float-left` | `float-start` | Float left |
| `float-right` | `float-end` | Float right |
| `no-gutters` | `g-0` | No gutters in row |
| `font-weight-bold` | `fw-bold` | Bold text |
| `font-weight-normal` | `fw-normal` | Normal weight |
| `text-monospace` | `font-monospace` | Monospace font |
| `badge-primary` | `bg-primary` | Badge color |
| `btn-block` | `w-100` + `d-grid` | Full width button |
| `form-group` | `mb-3` | Form spacing |
| `custom-select` | `form-select` | Select dropdown |
| `custom-file` | `form-control` | File input |
| `sr-only` | `visually-hidden` | Screen reader only |
| `data-toggle` | `data-bs-toggle` | Bootstrap data attr |
| `data-dismiss` | `data-bs-dismiss` | Dismiss attr |

---

## ✅ Development Standards

### ALWAYS Use Helpers
```php
// ✅ CORRECT - Use helper
<?= ui_button('add', 'Add Customer', ['size' => 'sm']) ?>

// ❌ WRONG - Don't use raw HTML
<button class="btn btn-primary btn-sm">Add Customer</button>
```

### Form Fields
```php
// ✅ CORRECT - Use form helper
<?= form_field('text', 'name', 'Customer Name', ['required' => true]) ?>

// ❌ WRONG - Don't use manual HTML
<div class="mb-3">
    <label class="form-label">Customer Name <span class="text-danger">*</span></label>
    <input type="text" class="form-control" name="name" required>
</div>
```

### Tables
```php
// ✅ CORRECT - Use standard table
<?= dt_table_standard('customersTable', ['Name', 'Email', 'Status']) ?>

// ❌ WRONG - Inconsistent classes
<table class="table">  <!-- Missing table-hover, table-sm, table-striped -->
```

### JavaScript
```javascript
// ✅ CORRECT - Use helper
initSelect2Ajax('#customerSelect', '/api/customers');

// ❌ WRONG - Manual init
$('#customerSelect').select2({
    ajax: {...}  // Inconsistent config
});
```

---

## 📁 File Structure

```
app/
├── Helpers/
│   ├── ui_helper.php              ← Buttons, badges, alerts
│   ├── form_field_helper.php      ← Form fields (NEW)
│   └── datatable_helper.php       ← DataTables + standard table (UPDATED)
├── Config/
│   └── Autoload.php               ← Helpers auto-loaded
public/
└── assets/
    └── js/
        └── ui_helpers.js           ← JS utilities (NEW)
```

---

## 🚦 Helper Function Reference

### PHP Helpers (Auto-loaded)

#### ui_helper.php
- `ui_button($type, $label, $options)` - Generate buttons
- `ui_badge($type, $label, $options)` - Generate badges
- `ui_alert($type, $message, $options)` - Generate alerts
- `ui_action_buttons($id, $actions)` - Generate action button group
- `ui_empty_state($message, $icon)` - Empty state placeholder
- `ui_loading($message)` - Loading spinner

#### form_field_helper.php ⭐ NEW
- `form_field($type, $name, $label, $options)` - Text/email/number/date fields
- `form_select_field($name, $label, $options_array, $options)` - Select dropdown
- `form_textarea_field($name, $label, $options)` - Textarea
- `form_checkbox_field($name, $label, $options)` - Checkbox/switch
- `form_radio_group($name, $label, $options_array, $options)` - Radio group
- `form_file_field($name, $label, $options)` - File upload
- `form_row($fields, $columns, $rowClass)` - Multi-column form row

#### datatable_helper.php
- `dt_config($options)` - DataTable configuration
- `dt_column($options)` - Column definition
- `dt_action_column($actions)` - Action column
- `dt_status_column($statusMap)` - Status badge column
- `dt_date_column($format)` - Date column
- `dt_number_column($decimals)` - Number column
- `dt_export_buttons($buttons)` - Export buttons
- `dt_table_standard($id, $columns, $options)` - Standard table HTML ⭐ NEW

### JavaScript Functions (ui_helpers.js) ⭐ NEW

#### Select2
- `initSelect2(selector, options)` - Standard Select2
- `initSelect2Ajax(selector, url, options)` - AJAX Select2

#### Button States
- `btnLoading(selector, text)` - Show loading state
- `btnReset(selector)` - Reset button

#### Notifications
- `showToast(message, type, options)` - Toast notification
- `confirmAction(options)` - Confirmation dialog

#### Form Validation
- `enableFormValidation(formSelector, options)` - Enable validation
- `clearFormValidation(formSelector)` - Clear validation state

#### UI Utilities
- `showLoadingOverlay(selector, message)` - Show loading overlay
- `hideLoadingOverlay(selector)` - Hide loading overlay
- `copyToClipboard(text, successMessage)` - Copy to clipboard

#### Utilities
- `debounce(func, wait)` - Debounce function
- `formatNumber(num, decimals)` - Format number
- `formatCurrency(amount, showSymbol)` - Format currency (Rupiah)

---

## 🎓 Training Notes

### For New Developers
1. **Always use helpers** - Don't write raw HTML for buttons/forms/tables
2. **Check this guide** - Before creating UI components
3. **Be consistent** - Same components should look the same everywhere
4. **Bootstrap 5** - Use new classes (me-2 not mr-2)
5. **Test responsiveness** - Check mobile/tablet view

### Common Mistakes
- ❌ Using `btn-block` (Bootstrap 4) instead of `w-100` + `d-grid`
- ❌ Using `mr-2` instead of `me-2`
- ❌ Using `badge-success` instead of `bg-success`
- ❌ Hardcoding buttons instead of using `ui_button()`
- ❌ Manual Select2 init instead of using `initSelect2()`

---

## 📊 Quick Migration Checklist

When updating old views:
- [ ] Replace hardcoded buttons with `ui_button()`
- [ ] Replace manual form fields with `form_field()` functions
- [ ] Replace table HTML with `dt_table_standard()`
- [ ] Update Bootstrap 4 classes to Bootstrap 5
- [ ] Use `initSelect2()` / `initSelect2Ajax()` for dropdowns
- [ ] Replace `badge-*` with `bg-*`
- [ ] Test in mobile view

---

## 🆘 Troubleshooting

### Helpers not found?
- Check `app/Config/Autoload.php` - helpers should be in `$helpers` array
- Clear cache: `php spark cache:clear`

### Select2 not working?
- Ensure jQuery is loaded before select2
- Ensure ui_helpers.js is loaded in layout
- Check browser console for errors

### Buttons look wrong?
- Check if ui_helper.php is loaded
- Verify Bootstrap 5 CSS is loaded
- Check for custom CSS overrides

### Forms not validating?
- Ensure form has `<form>` tag
- Call `enableFormValidation('#formId')`
- Check HTML5 validation attributes (required, pattern, etc.)

---

## 📢 Need Help?

- **Documentation**: This file + inline code comments
- **Examples**: Check existing Marketing module views
- **Code Review**: Ask senior developer before committing

---

**Last Updated**: 2026-02-09  
**Maintained By**: OPTIMA Dev Team
