# 🚀 OPTIMA Design System - Implementation Guide

**Quick Start untuk Developer**

---

## ✅ Step 1: Aktifkan Helpers

Edit file `app/Config/Autoload.php`:

```php
<?php

namespace Config;

use CodeIgniter\Config\AutoloadConfig;

class Autoload extends AutoloadConfig
{
    // ... existing code ...
    
    public $helpers = [
        // Existing helpers
        'auth_helper',
        'date_helper',
        'device_helper',
        'cache_helper',
        'global_permission_helper',
        'rbac_helper',
        'permission_helper',
        'notification_helper',
        
        // NEW: Design System Helpers
        'ui_helper',           // ← Add this
        'datatable_helper'     // ← Add this
    ];
    
    // ... rest of the code ...
}
```

---

## ✅ Step 2: Test Installation

Buat test page untuk memastikan helpers berfungsi:

**File:** `app/Controllers/TestDesignSystem.php`

```php
<?php

namespace App\Controllers;

class TestDesignSystem extends BaseController
{
    public function index()
    {
        return view('test_design_system');
    }
}
```

**File:** `app/Views/test_design_system.php`

```php
<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>

<div class="container py-5">
    <h1 class="mb-4">🎨 Design System Test Page</h1>
    
    <!-- Button Tests -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Button Components</h5>
        </div>
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2">
                <?= ui_button('add', 'Add Customer') ?>
                <?= ui_button('edit', 'Edit') ?>
                <?= ui_button('delete', 'Delete') ?>
                <?= ui_button('save', 'Save') ?>
                <?= ui_button('cancel', 'Cancel') ?>
                <?= ui_button('view', 'View') ?>
                <?= ui_button('export', 'Export') ?>
                <?= ui_button('print', 'Print') ?>
            </div>
        </div>
    </div>
    
    <!-- Badge Tests -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Badge Components</h5>
        </div>
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2">
                <!-- Work Order Status -->
                <div>
                    <p class="mb-2"><strong>Work Order Status:</strong></p>
                    <?= ui_badge('open') ?>
                    <?= ui_badge('in_progress') ?>
                    <?= ui_badge('completed') ?>
                    <?= ui_badge('cancelled') ?>
                </div>
                
                <!-- Priority -->
                <div class="ms-4">
                    <p class="mb-2"><strong>Priority:</strong></p>
                    <?= ui_priority_badge('low') ?>
                    <?= ui_priority_badge('medium') ?>
                    <?= ui_priority_badge('high') ?>
                    <?= ui_priority_badge('urgent') ?>
                </div>
                
                <!-- Quotation Status -->
                <div class="ms-4">
                    <p class="mb-2"><strong>Quotation Status:</strong></p>
                    <?= ui_badge('draft') ?>
                    <?= ui_badge('sent') ?>
                    <?= ui_badge('approved') ?>
                    <?= ui_badge('rejected') ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Alert Tests -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Alert Components</h5>
        </div>
        <div class="card-body">
            <?= ui_alert('success', 'This is a success alert!') ?>
            <?= ui_alert('danger', 'This is an error alert!') ?>
            <?= ui_alert('warning', 'This is a warning alert!') ?>
            <?= ui_alert('info', 'This is an info alert!') ?>
            <?= ui_alert('success', 'This is dismissible!', ['dismissible' => true]) ?>
        </div>
    </div>
    
    <!-- Action Buttons Test -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Action Button Groups</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Horizontal Group:</strong></p>
                    <?= ui_action_buttons([
                        'view' => ['onclick' => 'alert("View 123")'],
                        'edit' => ['onclick' => 'alert("Edit 123")'],
                        'delete' => ['onclick' => 'alert("Delete 123")']
                    ], 123) ?>
                </div>
                <div class="col-md-6">
                    <p><strong>Vertical Group:</strong></p>
                    <?= ui_action_buttons([
                        'view' => ['onclick' => 'alert("View 456")'],
                        'edit' => ['onclick' => 'alert("Edit 456")'],
                        'delete' => ['onclick' => 'alert("Delete 456")']
                    ], 456, ['vertical' => true]) ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Empty State & Loading Tests -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Empty State & Loading</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <?= ui_empty_state('No customers found', [
                        'button' => [
                            'type' => 'add',
                            'text' => 'Add Customer',
                            'options' => ['onclick' => 'alert("Add new!")']
                        ]
                    ]) ?>
                </div>
                <div class="col-md-6">
                    <?= ui_loading('Loading customers...', 'md') ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- DataTable Test -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>DataTable Component</h5>
        </div>
        <div class="card-body">
            <p class="text-muted">See browser console for DataTable config output</p>
            <table id="testTable" class="table table-striped"></table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
$(document).ready(function() {
    // Test DataTable config generation
    const config = <?= json_encode(dt_config([
        'ajax' => ['url' => base_url('api/test-data')],
        'columns' => [
            <?= json_encode(dt_column(0, ['title' => 'No', 'orderable' => false])) ?>,
            <?= json_encode(dt_column('name', ['title' => 'Name'])) ?>,
            <?= json_encode(dt_column('email', ['title' => 'Email'])) ?>,
            <?= json_encode(dt_status_column('status')) ?>
        ]
    ])) ?>;
    
    console.log('✅ DataTable Config:', config);
    console.log('🎉 Design System is working!');
    
    // Show success toast
    setTimeout(function() {
        Swal.fire({
            icon: 'success',
            title: 'Design System Active!',
            text: 'All components are loaded and ready to use.',
            timer: 3000,
            showConfirmButton: false
        });
    }, 500);
});
</script>
<?= $this->endSection() ?>
```

---

## ✅ Step 3: Add Routes (Optional)

Edit `app/Config/Routes.php`:

```php
$routes->get('/test-design-system', 'TestDesignSystem::index');
```

Akses: `http://localhost/optima/test-design-system`

---

## ✅ Step 4: First Real Implementation

Mari kita implementasikan di halaman yang sudah ada. Pilih salah satu:

### Option A: Update Customer Management

**File:** `app/Views/marketing/customer_management.php`

Cari dan replace:

**BEFORE:**
```html
<button class="btn btn-primary btn-sm" onclick="addCustomer()">
    <i class="fas fa-plus me-1"></i> Add Customer
</button>
```

**AFTER:**
```php
<?= ui_button('add', 'Add Customer', ['onclick' => 'addCustomer()']) ?>
```

**BEFORE:**
```html
<span class="badge bg-success">Active</span>
```

**AFTER:**
```php
<?= ui_badge('active') ?>
```

### Option B: Update Work Orders

**File:** `app/Views/service/work_orders.php`

Cari section dengan buttons dan replace dengan:

```php
<?= ui_button('add', lang('Common.add') . ' ' . lang('Service.work_order'), [
    'id' => 'btn-add-wo',
    'onclick' => 'openAddModal()'
]) ?>
```

Cari status badges dan replace dengan:

```php
<?= ui_status_badge($row['status'], 'wo') ?>
```

---

## 📊 Verification Checklist

Test setiap function:

```bash
✅ ui_button() - Generates buttons correctly
✅ ui_badge() - Shows correct colors
✅ ui_alert() - Displays properly
✅ ui_status_badge() - Auto-detects colors
✅ ui_priority_badge() - Shows priority
✅ ui_action_buttons() - Groups buttons
✅ ui_empty_state() - Empty placeholder
✅ ui_loading() - Loading spinner
✅ dt_config() - DataTable config
✅ dt_column() - Column config
✅ dt_action_column() - Action buttons
✅ dt_status_column() - Status badges
```

---

## 🐛 Troubleshooting

### Problem: "Undefined function ui_button()"

**Solution:**
1. Pastikan `ui_helper` ada di `app/Config/Autoload.php`
2. Clear cache: `php spark cache:clear`
3. Restart development server

### Problem: "Syntax error in helper file"

**Solution:**
1. Check `app/Helpers/ui_helper.php` untuk typo
2. Pastikan semua functions closed dengan `}`
3. Fix typo: `function_calls` → `function_exists` (line yang salah)

### Problem: Badge colors tidak sesuai

**Solution:**
- Gunakan normalized keys: `'in_progress'` bukan `'In Progress'`
- Helper akan auto-convert `'IN_PROGRESS'` → `'in_progress'`

---

## 🎓 Training Session Outline

### For Team (30 minutes)

1. **Introduction (5 min)**
   - Why we need design system
   - Benefits for development

2. **Demo (10 min)**
   - Show test page
   - Walk through common functions

3. **Hands-on (10 min)**
   - Each developer update 1 button/badge in their module
   - Verify it works

4. **Q&A (5 min)**
   - Answer questions
   - Share documentation links

---

## 📚 Documentation Links

- **[Full Design System Docs](DESIGN_SYSTEM.md)** - Comprehensive guide
- **[Quick Reference](DESIGN_SYSTEM_QUICK_REFERENCE.md)** - Cheat sheet
- **[Audit Report](UI_UX_AUDIT_REPORT.md)** - Findings & statistics

---

## 🎯 Next Steps

1. ✅ Complete Step 1-3 above
2. 📧 Share documentation with team
3. 🎓 Schedule training session
4. 🔄 Start migration (critical pages first)
5. ✅ Code review new PRs

---

## 💬 Support

**Questions?**
- Check documentation first
- Ask in Slack: `#optima-dev`
- Create GitHub issue for bugs

**Good luck! 🚀**
