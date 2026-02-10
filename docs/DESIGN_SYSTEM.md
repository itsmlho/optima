# 🎨 OPTIMA Design System Documentation

**Version:** 1.0  
**Last Updated:** <?= date('F d, Y') ?>  
**Author:** OPTIMA Dev Team

---

## 📋 Table of Contents

1. [Introduction](#introduction)
2. [Design Principles](#design-principles)
3. [Color System](#color-system)
4. [Typography](#typography)
5. [Button Components](#button-components)
6. [Badge Components](#badge-components)
7. [Alert Components](#alert-components)
8. [Table Components](#table-components)
9. [Form Components](#form-components)
10. [Modal & Card Components](#modal--card-components)
11. [Implementation Guide](#implementation-guide)
12. [Migration Guide](#migration-guide)
13. [Best Practices](#best-practices)
14. [Examples](#examples)

---

## 🎯 Introduction

OPTIMA Design System adalah kumpulan komponen UI/UX yang terstandarisasi untuk memastikan konsistensi visual dan user experience di seluruh aplikasi OPTIMA.

### Goals

- ✅ **Konsistensi Visual** - Semua komponen menggunakan warna, ukuran, dan style yang sama
- ✅ **Efisiensi Development** - Developer hanya perlu memanggil function, tidak perlu menulis HTML berulang
- ✅ **Maintainability** - Perubahan di satu tempat otomatis berlaku ke seluruh aplikasi
- ✅ **Accessibility** - Komponen dibuat dengan mempertimbangkan aksesibilitas

### Key Features

- 🎨 **Centralized UI Helper** - Single source of truth untuk semua komponen UI
- 📊 **DataTable Helper** - Standardisasi konfigurasi tabel di seluruh aplikasi
- 📖 **Comprehensive Documentation** - Dokumentasi lengkap dengan contoh penggunaan
- 🔄 **Easy Migration** - Panduan migrasi dari kode lama ke design system baru

---

## 🌈 Design Principles

### 1. **Consistency First**
Semua komponen harus konsisten dalam:
- Warna dan kontras
- Ukuran dan spacing
- Ikon dan typography
- Behavior dan interaksi

### 2. **User-Centric**
Prioritaskan:
- Kemudahan penggunaan
- Feedback visual yang jelas
- Loading states yang informatif
- Error handling yang helpful

### 3. **Responsive & Accessible**
- Mobile-friendly design
- Screen reader compatible
- Keyboard navigation support
- Proper ARIA labels

### 4. **Performance Optimized**
- Lazy loading untuk komponen berat
- Minimal HTTP requests
- Optimized assets
- Efficient DOM manipulation

---

## 🎨 Color System

### Brand Colors

```css
/* Primary - Blue */
--bs-primary: #0d6efd;

/* Secondary - Gray */
--bs-secondary: #6c757d;

/* Success - Green */
--bs-success: #198754;

/* Danger - Red */
--bs-danger: #dc3545;

/* Warning - Yellow/Orange */
--bs-warning: #ffc107;

/* Info - Light Blue */
--bs-info: #0dcaf0;
```

### Status Colors

| Status | Color Class | Use Case |
|--------|-------------|----------|
| **Open / Pending** | `bg-warning` | Work orders yang baru dibuka |
| **In Progress** | `bg-info` | Pekerjaan yang sedang berlangsung |
| **On Hold** | `bg-secondary` | Pekerjaan yang ditangguhkan |
| **Completed** | `bg-success` | Pekerjaan yang selesai |
| **Cancelled** | `bg-danger` | Pekerjaan yang dibatalkan |
| **Approved** | `bg-success` | Item yang disetujui |
| **Rejected** | `bg-danger` | Item yang ditolak |

### Priority Colors

| Priority | Color Class | Use Case |
|----------|-------------|----------|
| **Low** | `bg-secondary` | Prioritas rendah |
| **Medium** | `bg-warning` | Prioritas menengah |
| **High** | `bg-danger` | Prioritas tinggi |
| **Urgent** | `bg-danger` + icon | Prioritas sangat tinggi |

---

## 📝 Typography

### Hierarchy

```html
<h1>Main Page Title</h1>         <!-- 2.5rem / 40px -->
<h2>Section Title</h2>            <!-- 2rem / 32px -->
<h3>Subsection Title</h3>         <!-- 1.75rem / 28px -->
<h4>Component Title</h4>          <!-- 1.5rem / 24px -->
<h5>Card Header</h5>              <!-- 1.25rem / 20px -->
<h6>Small Header</h6>             <!-- 1rem / 16px -->
<p>Body text</p>                  <!-- 1rem / 16px -->
<small>Helper text</small>        <!-- 0.875rem / 14px -->
```

### Font Weights

- **Regular:** 400 (body text)
- **Medium:** 500 (labels, subheadings)
- **Semibold:** 600 (button text, emphasis)
- **Bold:** 700 (headings, important info)

---

## 🔘 Button Components

### Button Types & Usage

| Type | Color Class | Icon | Usage |
|------|-------------|------|-------|
| **Add** | `btn-primary` | `fa-plus` | Tambah data baru |
| **Edit** | `btn-warning` | `fa-edit` | Edit data existing |
| **Delete** | `btn-danger` | `fa-trash` | Hapus data |
| **Save** | `btn-success` | `fa-save` | Simpan perubahan |
| **Cancel** | `btn-secondary` | `fa-times` | Batal/tutup |
| **View** | `btn-info` | `fa-eye` | Lihat detail |
| **Export** | `btn-success` | `fa-file-export` | Export data |
| **Filter** | `btn-outline-secondary` | `fa-filter` | Filter data |
| **Print** | `btn-primary` | `fa-print` | Print dokumen |
| **Refresh** | `btn-outline-primary` | `fa-sync-alt` | Refresh data |

### Implementation

#### Basic Button

```php
<?php
// Add Button
echo ui_button('add', 'Add Customer');
// Output: <button type="button" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i>Add Customer</button>

// Edit Button with onclick
echo ui_button('edit', 'Edit', [
    'id' => 'btn-edit-123',
    'onclick' => 'editRecord(123)'
]);

// Delete Button with confirmation
echo ui_button('delete', '', [
    'onclick' => "confirmDelete(123, 'Customer XYZ')",
    'title' => 'Delete this customer'
]);
?>
```

#### Custom Button

```php
<?php
echo ui_button('custom', 'Submit Report', [
    'class' => 'btn-info',
    'icon' => 'fas fa-paper-plane',
    'size' => 'btn-md',
    'onclick' => 'submitReport()'
]);
?>
```

#### Button as Link

```php
<?php
echo ui_button('view', 'View Details', [
    'href' => base_url('customers/view/123'),
    'target' => '_blank'
]);
?>
```

#### Action Button Group

```php
<?php
// Vertical button group untuk action column table
echo ui_action_buttons([
    'view' => ['onclick' => 'viewRecord'],
    'edit' => ['onclick' => 'editRecord'],
    'delete' => ['onclick' => 'deleteRecord']
], 123, ['vertical' => true]);
?>
```

### Button Sizes

```php
<?php
// Small (default untuk table actions)
echo ui_button('edit', '', ['size' => 'btn-sm']);

// Medium (default untuk forms)
echo ui_button('save', 'Save Data', ['size' => '']);

// Large (untuk primary actions)
echo ui_button('submit', 'Submit Now', ['size' => 'btn-lg']);
?>
```

### Disabled State

```php
<?php
echo ui_button('save', 'Saving...', [
    'disabled' => true,
    'id' => 'btn-save'
]);
?>
```

---

## 🏷️ Badge Components

### Badge Types

#### Status Badges

```php
<?php
// Work Order Status
echo ui_badge('open');                  // Yellow badge
echo ui_badge('in_progress');           // Blue badge  
echo ui_badge('completed');             // Green badge
echo ui_badge('cancelled');             // Red badge

// Quotation Status
echo ui_badge('draft');                 // Gray badge
echo ui_badge('sent');                  // Yellow badge
echo ui_badge('approved');              // Green badge
echo ui_badge('rejected');              // Red badge

// Custom text
echo ui_badge('pending', 'Menunggu Approval');
?>
```

#### Priority Badges

```php
<?php
echo ui_priority_badge('low');          // Gray badge
echo ui_priority_badge('medium');       // Yellow badge
echo ui_priority_badge('high');         // Red badge
echo ui_priority_badge('urgent');       // Red badge with icon
?>
```

#### Custom Badges

```php
<?php
// Custom color
echo ui_badge('custom', 'VIP Customer', [
    'class' => 'bg-purple',
    'icon' => 'fas fa-star'
]);

// Pill style
echo ui_badge('success', 'Active', ['pill' => true]);

// With custom attributes
echo ui_badge('pending', 'Under Review', [
    'id' => 'status-badge-123',
    'title' => 'Click to change status'
]);
?>
```

### Badge with Auto-Detection

```php
<?php
// Automatically detects color based on status
echo ui_status_badge('IN_PROGRESS', 'wo');
echo ui_status_badge('SENT', 'quotation');
echo ui_status_badge('DELIVERED', 'spk');
?>
```

---

## 🔔 Alert Components

### Alert Types

```php
<?php
// Success Alert
echo ui_alert('success', 'Data berhasil disimpan!');

// Error Alert
echo ui_alert('danger', 'Terjadi kesalahan saat menyimpan data.');

// Warning Alert
echo ui_alert('warning', 'Perhatian: Data akan dihapus permanen!');

// Info Alert
echo ui_alert('info', 'Proses upload sedang berlangsung...');
?>
```

### Dismissible Alerts

```php
<?php
echo ui_alert('success', 'Operation completed successfully!', [
    'dismissible' => true
]);

echo ui_alert('warning', 'This action cannot be undone.', [
    'dismissible' => true,
    'title' => 'Warning!'
]);
?>
```

### Custom Alert

```php
<?php
echo ui_alert('info', 'Check your email for verification link.', [
    'icon' => 'fas fa-envelope',
    'title' => 'Email Sent',
    'dismissible' => true
]);
?>
```

---

## 📊 Table Components

### Basic DataTable Setup

```php
<?php
use function App\Helpers\dt_config;
use function App\Helpers\dt_column;
use function App\Helpers\dt_action_column;

// Define table configuration
$tableConfig = dt_config([
    'ajax' => [
        'url' => base_url('api/customers/datatable'),
        'type' => 'POST'
    ],
    'columns' => [
        dt_column(0, ['title' => 'No', 'orderable' => false, 'searchable' => false, 'width' => '50px']),
        dt_column('customer_name', ['title' => 'Customer Name']),
        dt_column('email', ['title' => 'Email']),
        dt_column('phone', ['title' => 'Phone', 'className' => 'text-center']),
        dt_status_column('status', ['active' => 'bg-success', 'inactive' => 'bg-secondary']),
        dt_action_column(['view', 'edit', 'delete'], [
            'callbacks' => [
                'view' => 'viewCustomer',
                'edit' => 'editCustomer',
                'delete' => 'deleteCustomer'
            ]
        ])
    ],
    'order' => [[1, 'asc']]  // Sort by customer name
]);
?>

<table id="customersTable" class="table table-striped table-hover"></table>

<script>
$(document).ready(function() {
    $('#customersTable').DataTable(<?= json_encode($tableConfig) ?>);
});
</script>
```

### Advanced Column Types

#### Date Column

```php
<?php
dt_date_column('created_at', 'd/m/Y H:i');
dt_date_column('report_date', 'DD MMM YYYY');
?>
```

#### Number Column

```php
<?php
// Currency
dt_number_column('amount', [
    'decimals' => 2,
    'prefix' => 'Rp ',
    'thousands_sep' => ','
]);

// Quantity
dt_number_column('stock', [
    'decimals' => 0,
    'suffix' => ' units'
]);
?>
```

#### Link Column

```php
<?php
dt_link_column('wo_number', base_url('service/work-orders/view/{id}'), [
    'target' => '_blank',
    'title' => 'View Work Order'
]);
?>
```

### Export Buttons

```php
<?php
$tableConfig = dt_config([
    // ... other config
    'buttons' => dt_export_buttons(['excel', 'pdf', 'print'], [
        'title' => 'Customer List',
        'filename' => 'customers_' . date('Y-m-d')
    ]),
    'dom' => 'Bfrtip'  // Include buttons in DOM
]);
?>
```

---

## 📝 Form Components

### Standard Form Elements

#### Text Input

```html
<div class="mb-3">
    <label for="customer_name" class="form-label">Customer Name <span class="text-danger">*</span></label>
    <input type="text" class="form-control" id="customer_name" name="customer_name" required>
    <small class="form-text text-muted">Enter full customer name</small>
</div>
```

#### Select Dropdown

```html
<div class="mb-3">
    <label for="status" class="form-label">Status</label>
    <select class="form-select" id="status" name="status">
        <option value="">-- Select Status --</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
    </select>
</div>
```

#### Textarea

```html
<div class="mb-3">
    <label for="notes" class="form-label">Additional Notes</label>
    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
</div>
```

### Form with Validation

```html
<form id="customerForm" class="needs-validation" novalidate>
    <div class="mb-3">
        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
        <input type="email" class="form-control" id="email" name="email" required>
        <div class="invalid-feedback">
            Please provide a valid email address.
        </div>
    </div>
    
    <div class="d-flex justify-content-end gap-2">
        <?= ui_button('cancel', '', ['data-bs-dismiss' => 'modal']) ?>
        <?= ui_button('save', 'Save Customer', ['type' => 'submit']) ?>
    </div>
</form>
```

---

## 🪟 Modal & Card Components

### Standard Modal Structure

```html
<!-- Modal -->
<div class="modal fade" id="customerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user me-2 text-primary"></i>
                    Add New Customer
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <!-- Body -->
            <div class="modal-body">
                <!-- Form content here -->
            </div>
            
            <!-- Footer -->
            <div class="modal-footer">
                <?= ui_button('cancel', '', ['data-bs-dismiss' => 'modal']) ?>
                <?= ui_button('save', 'Save Customer', ['onclick' => 'saveCustomer()']) ?>
            </div>
        </div>
    </div>
</div>
```

### Card Component

```html
<!-- Standard Card -->
<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h6 class="mb-0">
            <i class="fas fa-chart-line me-2"></i>Statistics
        </h6>
    </div>
    <div class="card-body">
        <!-- Card content -->
    </div>
    <div class="card-footer text-muted">
        Last updated: <?= date('d M Y H:i') ?>
    </div>
</div>
```

---

## 🚀 Implementation Guide

### Step 1: Load Helpers

Pastikan helper dimuat di `app/Config/Autoload.php`:

```php
public $helpers = [
    'ui_helper',
    'datatable_helper'
];
```

### Step 2: Replace Existing Code

**Before (Old Code):**
```php
<button type="button" class="btn btn-primary btn-sm" onclick="addCustomer()">
    <i class="fas fa-plus me-1"></i> Add Customer
</button>
```

**After (Using Design System):**
```php
<?= ui_button('add', 'Add Customer', ['onclick' => 'addCustomer()']) ?>
```

### Step 3: Standardize Status Badges

**Before:**
```php
<?php if ($status == 'OPEN'): ?>
    <span class="badge bg-warning">Open</span>
<?php elseif ($status == 'COMPLETED'): ?>
    <span class="badge bg-success">Completed</span>
<?php endif; ?>
```

**After:**
```php
<?= ui_status_badge($status, 'wo') ?>
```

### Step 4: Standardize DataTables

**Before:**
```javascript
$('#table').DataTable({
    processing: true,
    serverSide: true,
    ajax: '<?= base_url("api/data") ?>',
    // ... repetitive configuration
});
```

**After:**
```php
<?php
$config = dt_config([
    'ajax' => ['url' => base_url('api/data')],
    'columns' => [
        dt_column('name'),
        dt_column('email'),
        dt_action_column(['view', 'edit', 'delete'])
    ]
]);
?>

<script>
$('#table').DataTable(<?= json_encode($config) ?>);
</script>
```

---

## 🔄 Migration Guide

### Priority Order

1. ✅ **Phase 1: Critical Pages** (Week 1-2)
   - Work Orders
   - Quotations
   - Customer Management

2. ✅ **Phase 2: Common Pages** (Week 3-4)
   - SPK/DI
   - Purchase Orders
   - Inventory Units

3. ✅ **Phase 3: Admin Pages** (Week 5-6)
   - User Management
   - Settings
   - Reports

### Migration Checklist

- [ ] Update button components
- [ ] Update badge/status components
- [ ] Update alert components
- [ ] Standardize DataTable configuration
- [ ] Update form components
- [ ] Update modal structure
- [ ] Test all interactions
- [ ] Review responsive behavior
- [ ] Update documentation

---

## ⚡ Best Practices

### DO ✅

1. **Always use helper functions** untuk komponen UI
2. **Keep consistency** dalam penggunaan warna dan ikon
3. **Test responsive** behavior di mobile devices
4. **Add loading states** untuk operasi async
5. **Provide feedback** untuk user actions
6. **Use semantic HTML** dan proper ARIA labels
7. **Comment your code** terutama logic kompleks

### DON'T ❌

1. **Hardcode HTML** untuk buttons, badges, alerts
2. **Mix different badge colors** untuk status yang sama
3. **Skip validation** di form inputs
4. **Forget error handling** di AJAX calls
5. **Use inline styles** kecuali absolutely necessary
6. **Create duplicate DataTable configs**
7. **Ignore accessibility** requirements

---

## 💡 Examples

### Complete CRUD Page Example

```php
<!-- View File: app/Views/customers/index.php -->
<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="fas fa-users me-2"></i>Customer Management</h3>
        <?= ui_button('add', 'Add Customer', [
            'onclick' => 'openAddModal()',
            'size' => 'btn-md'
        ]) ?>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary">
                <div class="card-body">
                    <h5>Total Customers</h5>
                    <h2 id="total-customers">0</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-success">
                <div class="card-body">
                    <h5>Active</h5>
                    <h2 id="active-customers">0</h2>
                </div>
            </div>
        </div>
        <!-- More stats... -->
    </div>

    <!-- DataTable Card -->
    <div class="card shadow">
        <div class="card-body">
            <table id="customersTable" class="table table-striped table-hover">
                <!-- Table will be populated by DataTables -->
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="customerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="customerForm">
                <div class="modal-body">
                    <!-- Form fields here -->
                </div>
                <div class="modal-footer">
                    <?= ui_button('cancel', '', ['data-bs-dismiss' => 'modal']) ?>
                    <?= ui_button('save', 'Save Customer', ['type' => 'submit']) ?>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
$(document).ready(function() {
    // Initialize DataTable
    let table = $('#customersTable').DataTable(<?= json_encode(
        dt_config([
            'ajax' => ['url' => base_url('api/customers')],
            'columns' => [
                dt_column(0, ['title' => 'No', 'orderable' => false]),
                dt_column('name', ['title' => 'Customer Name']),
                dt_column('email', ['title' => 'Email']),
                dt_column('phone', ['title' => 'Phone']),
                dt_status_column('status'),
                dt_action_column(['view', 'edit', 'delete'], [
                    'callbacks' => [
                        'view' => 'viewCustomer',
                        'edit' => 'editCustomer',
                        'delete' => 'deleteCustomer'
                    ]
                ])
            ]
        ])
    ) ?>);
});

function openAddModal() {
    $('#modalTitle').text('Add Customer');
    $('#customerForm')[0].reset();
    $('#customerModal').modal('show');
}

function viewCustomer(id) {
    // Implementation
}

function editCustomer(id) {
    // Implementation
}

function deleteCustomer(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Delete implementation
        }
    });
}
</script>
<?= $this->endSection() ?>
```

---

## 📚 Additional Resources

### Related Documentation

- [Bootstrap 5 Documentation](https://getbootstrap.com/docs/5.0/)
- [DataTables Documentation](https://datatables.net/)
- [Font Awesome Icons](https://fontawesome.com/icons)
- [SweetAlert2 Documentation](https://sweetalert2.github.io/)

### Internal Resources

- `app/Helpers/ui_helper.php` - UI component helper functions
- `app/Helpers/datatable_helper.php` - DataTable configuration helpers
- `app/Views/_partials/datatable_template.php` - DataTable template reference

### Contact & Support

Untuk pertanyaan atau bantuan implementasi design system:
- **Development Team** - [Slack Channel: #optima-dev]
- **Documentation Issues** - [GitHub Issues]

---

## 📝 Changelog

### Version 1.0 (<?= date('Y-m-d') ?>)
- ✨ Initial release of OPTIMA Design System
- 🎨 Standardized button components with 15 types
- 🏷️ Comprehensive badge system untuk semua status
- 📊 DataTable helper dengan advanced configurations
- 📖 Complete documentation dengan examples

---

**Made with ❤️ by OPTIMA Dev Team**
