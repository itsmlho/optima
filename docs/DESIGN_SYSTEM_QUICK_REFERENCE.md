# ⚡ OPTIMA Design System - Quick Reference

**Cheat sheet untuk developers** | [Full Documentation](DESIGN_SYSTEM.md)

---

## 🔘 Buttons

### Common Buttons

```php
// Add button
<?= ui_button('add', 'Add Customer') ?>

// Edit button
<?= ui_button('edit', '', ['onclick' => 'edit(123)']) ?>

// Delete button
<?= ui_button('delete', '', ['onclick' => 'confirmDelete(123)']) ?>

// Save button
<?= ui_button('save', 'Save Changes', ['type' => 'submit']) ?>

// Cancel button
<?= ui_button('cancel', '', ['data-bs-dismiss' => 'modal']) ?>

// View button
<?= ui_button('view', 'View Details', ['href' => base_url('view/123')']) ?>

// Export button
<?= ui_button('export', 'Export Excel') ?>

// Custom button
<?= ui_button('custom', 'My Button', ['class' => 'btn-info', 'icon' => 'fas fa-star']) ?>
```

### Button Sizes

```php
<?= ui_button('save', '', ['size' => 'btn-sm']) ?>    // Small
<?= ui_button('save', '', ['size' => '']) ?>          // Medium (default)
<?= ui_button('save', '', ['size' => 'btn-lg']) ?>    // Large
```

### Action Button Group

```php
// Horizontal
<?= ui_action_buttons(['view' => [...], 'edit' => [...], 'delete' => [...]],  123) ?>

// Vertical (for table columns)
<?= ui_action_buttons([...], 123, ['vertical' => true]) ?>
```

---

## 🏷️ Badges

### Status Badges

```php
// Work Order Status
<?= ui_badge('open') ?>                 // Yellow
<?= ui_badge('in_progress') ?>          // Blue
<?= ui_badge('completed') ?>            // Green
<?= ui_badge('cancelled') ?>            // Red

// Quotation Status
<?= ui_badge('draft') ?>                // Gray
<?= ui_badge('sent') ?>                 // Yellow
<?= ui_badge('approved') ?>             // Green
<?= ui_badge('rejected') ?>             // Red

// Custom text
<?= ui_badge('pending', 'Waiting Approval') ?>

// With icon
<?= ui_badge('urgent', 'Urgent', ['icon' => 'fas fa-exclamation-triangle']) ?>

// Pill style
<?= ui_badge('active', '', ['pill' => true]) ?>
```

### Priority Badges

```php
<?= ui_priority_badge('low') ?>         // Gray
<?= ui_priority_badge('medium') ?>      // Yellow
<?= ui_priority_badge('high') ?>        // Red
<?= ui_priority_badge('urgent') ?>      // Red + icon
```

### Auto-Detection

```php
<?= ui_status_badge($status, 'wo') ?>           // Auto work order colors
<?= ui_status_badge($status, 'quotation') ?>    // Auto quotation colors
```

---

## 🔔 Alerts

```php
// Success
<?= ui_alert('success', 'Data berhasil disimpan!') ?>

// Error
<?= ui_alert('danger', 'Terjadi kesalahan!') ?>

// Warning
<?= ui_alert('warning', 'Data akan dihapus permanent!') ?>

// Info
<?= ui_alert('info', 'Proses sedang berlangsung...') ?>

// Dismissible
<?= ui_alert('success', 'Success!', ['dismissible' => true]) ?>

// With title
<?= ui_alert('warning', 'Check your data carefully.', [
    'title' => 'Warning!',
    'dismissible' => true
]) ?>
```

---

## 📊 DataTables

### Basic Setup

```php
<?php
$config = dt_config([
    'ajax' => ['url' => base_url('api/customers')],
    'columns' => [
        dt_column(0, ['title' => 'No', 'orderable' => false]),
        dt_column('name', ['title' => 'Name']),
        dt_column('email'),
        dt_status_column('status'),
        dt_action_column(['view', 'edit', 'delete'])
    ]
]);
?>

<table id="myTable" class="table table-striped"></table>

<script>
$('#myTable').DataTable(<?= json_encode($config) ?>);
</script>
```

### Column Types

```php
// Regular column
dt_column('name', ['title' => 'Customer Name', 'className' => 'text-center'])

// Status column with auto badge
dt_status_column('status')

// Date column
dt_date_column('created_at', 'd/m/Y')

// Number column (currency)
dt_number_column('amount', ['decimals' => 2, 'prefix' => 'Rp '])

// Link column  
dt_link_column('wo_number', base_url('wo/view/{id}'))

// Action buttons
dt_action_column(['view', 'edit', 'delete'], [
    'callbacks' => [
        'view' => 'viewRecord',
        'edit' => 'editRecord',
        'delete' => 'deleteRecord'
    ]
])
```

### Export Buttons

```php
$config = dt_config([
    'buttons' => dt_export_buttons(['excel', 'pdf', 'print'], [
        'title' => 'Customer List',
        'filename' => 'customers_' . date('Y-m-d')
    ]),
    'dom' => 'Bfrtip'
]);
```

---

## 📝 Forms

### Standard Form Input

```html
<div class="mb-3">
    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
    <input type="text" class="form-control" id="name" name="name" required>
    <small class="form-text text-muted">Enter full name</small>
    <div class="invalid-feedback">Name is required</div>
</div>
```

### Select Dropdown

```html
<div class="mb-3">
    <label for="status" class="form-label">Status</label>
    <select class="form-select" id="status" name="status">
        <option value="">-- Select --</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
    </select>
</div>
```

### Form Buttons

```html
<div class="d-flex justify-content-end gap-2">
    <?= ui_button('cancel', '', ['data-bs-dismiss' => 'modal']) ?>
    <?= ui_button('save', 'Save Data', ['type' => 'submit']) ?>
</div>
```

---

## 🪟 Modals

### Standard Modal

```html
<div class="modal fade" id="myModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user me-2 text-primary"></i>Modal Title
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <!-- Body -->
            <div class="modal-body">
                <!-- Content here -->
            </div>
            
            <!-- Footer -->
            <div class="modal-footer">
                <?= ui_button('cancel', '', ['data-bs-dismiss' => 'modal']) ?>
                <?= ui_button('save', 'Save', ['onclick' => 'save()']) ?>
            </div>
        </div>
    </div>
</div>
```

---

## 🏗️ Cards

```html
<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h6 class="mb-0">
            <i class="fas fa-chart-line me-2"></i>Card Title
        </h6>
    </div>
    <div class="card-body">
        <!-- Content -->
    </div>
</div>
```

---

## 💡 Helper Functions

### Empty State

```php
<?= ui_empty_state('No data found', [
    'icon' => 'fas fa-inbox',
    'button' => [
        'type' => 'add',
        'text' => 'Add New',
        'options' => ['onclick' => 'addNew()']
    ]
]) ?>
```

### Loading Spinner

```php
<?= ui_loading('Loading data...', 'md') ?>
```

---

## 🎨 Color Reference

| Type | Color Class | Usage |
|------|-------------|-------|
| Primary | `btn-primary`, `bg-primary` | Add, Submit, Primary actions |
| Success | `btn-success`, `bg-success` | Save, Completed, Approved |
| Warning | `btn-warning`, `bg-warning` | Edit, Pending, Caution |
| Danger | `btn-danger`, `bg-danger` | Delete, Cancelled, Rejected |
| Info | `btn-info`, `bg-info` | View, In Progress, Info |
| Secondary | `btn-secondary`, `bg-secondary` | Cancel, Inactive, Default |

---

## 🔍 Quick Search

| Need | Function | Example |
|------|----------|---------|
| Button | `ui_button()` | `ui_button('add', 'New')` |
| Badge | `ui_badge()` | `ui_badge('success', 'Active')` |
| Alert | `ui_alert()` | `ui_alert('success', 'Done!')` |
| Status | `ui_status_badge()` | `ui_status_badge($status)` |
| Priority | `ui_priority_badge()` | `ui_priority_badge('high')` |
| Actions | `ui_action_buttons()` | `ui_action_buttons([...], $id)` |
| Table | `dt_config()` | `dt_config([...])` |
| Column | `dt_column()` | `dt_column('name')` |
| Empty | `ui_empty_state()` | `ui_empty_state('No data')` |
| Loading | `ui_loading()` | `ui_loading('Wait...')` |

---

## 🚫 Common Mistakes

### ❌ DON'T

```php
// Hardcoded HTML
<button class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Add</button>
<span class="badge bg-success">Active</span>

// Inconsistent colors
<button class="btn btn-success">Edit</button>  // Should be warning!
<span class="badge bg-primary">Pending</span>   // Should be warning!
```

### ✅ DO

```php
// Use helper functions
<?= ui_button('add', 'Add') ?>
<?= ui_badge('active') ?>

// Consistent colors
<?= ui_button('edit', 'Edit') ?>         // Yellow/Warning
<?= ui_badge('pending') ?>                // Yellow/Warning
```

---

## 📚 See Also

- [Full Documentation](DESIGN_SYSTEM.md) - Comprehensive guide
- [Audit Report](UI_UX_AUDIT_REPORT.md) - Findings & recommendations
- [Migration Guide](DESIGN_SYSTEM.md#migration-guide) - How to migrate existing code

---

**Questions?** Check `app/Helpers/ui_helper.php` for function definitions
