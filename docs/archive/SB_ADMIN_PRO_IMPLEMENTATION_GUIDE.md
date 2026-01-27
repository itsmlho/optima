# SB Admin Pro CSS Implementation Guide
## OPTIMA Application - PT Sarana Mitra Luas Tbk

**Version:** 1.0.0  
**Date:** November 29, 2024  
**Author:** OPTIMA Development Team

---

## 📋 Table of Contents

1. [Introduction](#introduction)
2. [File Structure](#file-structure)
3. [Installation](#installation)
4. [Component Usage](#component-usage)
5. [Migration Guide](#migration-guide)
6. [Best Practices](#best-practices)
7. [Troubleshooting](#troubleshooting)

---

## 🎯 Introduction

This CSS framework is based on **SB Admin Pro** design system and provides a comprehensive, consistent, and professional styling solution for the OPTIMA application. It eliminates CSS duplication across pages and ensures a unified user interface.

### Features

✅ **Complete Component Library** - Cards, Buttons, Tables, Forms, Modals, etc.  
✅ **DataTables Integration** - Professional table styling with sorting, pagination, and search  
✅ **Select2 Integration** - Beautiful dropdowns with search functionality  
✅ **Responsive Design** - Mobile-first approach, works on all devices  
✅ **Dark Mode Ready** - Optional dark theme support  
✅ **Print Optimized** - Clean printing layouts  
✅ **Consistent Design** - Unified color palette and spacing system  

---

## 📁 File Structure

```
public/assets/css/
├── sb-admin-pro-master.css          # Main entry point - Import this file
├── components/
│   ├── sb-admin-pro-components.css  # Core components (cards, buttons, tables, etc.)
│   ├── sb-admin-pro-datatables.css  # DataTables plugin integration
│   └── sb-admin-pro-select2.css     # Select2 dropdown integration
```

### File Descriptions

| File | Purpose | Size |
|------|---------|------|
| `sb-admin-pro-master.css` | Main file that imports all components + custom OPTIMA styles | ~5KB |
| `sb-admin-pro-components.css` | Core UI components (cards, buttons, forms, tables, etc.) | ~35KB |
| `sb-admin-pro-datatables.css` | DataTables styling to match SB Admin Pro design | ~12KB |
| `sb-admin-pro-select2.css` | Select2 dropdown styling | ~10KB |

---

## 🚀 Installation

### Step 1: Update Base Layout

Edit your main layout file: `app/Views/layouts/base.php`

**Replace this:**
```php
<!-- Old CSS -->
<link href="<?= base_url('assets/css/optima-pro.css') ?>?v=<?= time() ?>" rel="stylesheet">
```

**With this:**
```php
<!-- SB Admin Pro CSS - Master File -->
<link href="<?= base_url('assets/css/sb-admin-pro-master.css') ?>?v=<?= time() ?>" rel="stylesheet">
```

### Step 2: Keep Required External Libraries

Make sure you still have these in your layout:

```php
<!-- Bootstrap 5 (Required) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Font Awesome (Required for icons) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

<!-- DataTables CSS (If using DataTables) -->
<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">

<!-- Select2 CSS (If using Select2) -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
```

### Step 3: Remove Old Page-Specific Styles

**Before (in individual pages):**
```php
<?= $this->section('css') ?>
<style>
    .card { /* custom styles */ }
    .btn-custom { /* custom styles */ }
    /* ... lots of duplicate CSS ... */
</style>
<?= $this->endSection() ?>
```

**After:**
```php
<?= $this->section('css') ?>
<!-- Only add page-specific styles if absolutely necessary -->
<?= $this->endSection() ?>
```

---

## 🎨 Component Usage

### 1. Cards

#### Basic Card
```html
<div class="card">
    <div class="card-header">
        Card Title
    </div>
    <div class="card-body">
        Card content goes here
    </div>
    <div class="card-footer">
        Footer content
    </div>
</div>
```

#### Card with Icon
```html
<div class="card">
    <div class="card-body">
        <div class="card-icon-header">
            <div class="card-icon-header-icon bg-primary">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <h5 class="card-title mb-0">Total Users</h5>
                <p class="text-muted mb-0">1,234 active users</p>
            </div>
        </div>
    </div>
</div>
```

#### Stats Card
```html
<div class="card">
    <div class="card-body">
        <div class="stats-card">
            <div class="stats-card-info">
                <h3>1,234</h3>
                <p>Total Orders</p>
            </div>
            <div class="stats-card-icon bg-primary-soft text-primary">
                <i class="fas fa-shopping-cart"></i>
            </div>
        </div>
    </div>
</div>
```

#### Collapsable Card
```html
<div class="card card-collapsable">
    <div class="card-header" data-bs-toggle="collapse" data-bs-target="#collapseCard">
        Collapsable Card
    </div>
    <div class="collapse show" id="collapseCard">
        <div class="card-body">
            Content that can be collapsed
        </div>
    </div>
</div>
```

### 2. Buttons

#### Button Styles
```html
<!-- Solid Buttons -->
<button class="btn btn-primary">Primary</button>
<button class="btn btn-secondary">Secondary</button>
<button class="btn btn-success">Success</button>
<button class="btn btn-danger">Danger</button>
<button class="btn btn-warning">Warning</button>
<button class="btn btn-info">Info</button>

<!-- Outline Buttons -->
<button class="btn btn-outline-primary">Primary</button>
<button class="btn btn-outline-secondary">Secondary</button>

<!-- Button Sizes -->
<button class="btn btn-primary btn-xs">Extra Small</button>
<button class="btn btn-primary btn-sm">Small</button>
<button class="btn btn-primary">Default</button>
<button class="btn btn-primary btn-lg">Large</button>

<!-- Icon Buttons -->
<button class="btn btn-primary btn-icon">
    <i class="fas fa-plus"></i>
</button>

<!-- Button with Icon and Text -->
<button class="btn btn-primary">
    <i class="fas fa-save me-2"></i>
    Save Data
</button>
```

### 3. Tables

#### Basic Table
```html
<div class="card">
    <div class="card-header">
        Data Table
    </div>
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>John Doe</td>
                    <td>john@example.com</td>
                    <td>
                        <div class="table-action-buttons">
                            <button class="btn btn-sm btn-primary">Edit</button>
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
```

#### DataTable (with plugin)
```html
<table id="myDataTable" class="table table-hover table-striped">
    <thead>
        <tr>
            <th>Column 1</th>
            <th>Column 2</th>
            <th>Column 3</th>
        </tr>
    </thead>
    <tbody>
        <!-- Data rows -->
    </tbody>
</table>

<script>
$(document).ready(function() {
    $('#myDataTable').DataTable({
        responsive: true,
        pageLength: 10,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        }
    });
});
</script>
```

### 4. Forms

#### Form Layout
```html
<form>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label" for="inputName">Name</label>
            <input class="form-control" id="inputName" type="text" placeholder="Enter name">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label" for="inputEmail">Email</label>
            <input class="form-control" id="inputEmail" type="email" placeholder="Enter email">
        </div>
    </div>
    
    <div class="mb-3">
        <label class="form-label" for="inputSelect">Category</label>
        <select class="form-select" id="inputSelect">
            <option selected disabled>Choose...</option>
            <option value="1">Option 1</option>
            <option value="2">Option 2</option>
        </select>
    </div>
    
    <div class="mb-3">
        <label class="form-label" for="inputTextarea">Description</label>
        <textarea class="form-control" id="inputTextarea" rows="3"></textarea>
    </div>
    
    <button class="btn btn-primary" type="submit">Submit</button>
    <button class="btn btn-secondary" type="button">Cancel</button>
</form>
```

#### Floating Labels
```html
<div class="form-floating mb-3">
    <input type="email" class="form-control" id="floatingInput" placeholder="name@example.com">
    <label for="floatingInput">Email address</label>
</div>

<div class="form-floating">
    <select class="form-select" id="floatingSelect">
        <option selected>Open this select menu</option>
        <option value="1">One</option>
        <option value="2">Two</option>
    </select>
    <label for="floatingSelect">Works with selects</label>
</div>
```

### 5. Select2 Dropdown

#### Basic Select2
```html
<select class="form-select" id="mySelect2">
    <option value="">Choose...</option>
    <option value="1">Option 1</option>
    <option value="2">Option 2</option>
    <option value="3">Option 3</option>
</select>

<script>
$(document).ready(function() {
    $('#mySelect2').select2({
        placeholder: 'Select an option',
        allowClear: true,
        width: '100%'
    });
});
</script>
```

#### Select2 with AJAX
```html
<select class="form-select" id="ajaxSelect2"></select>

<script>
$(document).ready(function() {
    $('#ajaxSelect2').select2({
        ajax: {
            url: '<?= base_url('api/search') ?>',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term,
                    page: params.page
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            }
        },
        placeholder: 'Search...',
        minimumInputLength: 1
    });
});
</script>
```

### 6. Modals

#### Standard Modal
```html
<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#myModal">
    Open Modal
</button>

<div class="modal fade" id="myModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modal Title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Modal content goes here
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>
```

#### Large Modal
```html
<div class="modal fade" id="largeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <!-- modal-lg for large, modal-xl for extra large -->
        <div class="modal-content">
            <!-- content -->
        </div>
    </div>
</div>
```

### 7. Badges & Alerts

#### Badges
```html
<span class="badge bg-primary">Primary</span>
<span class="badge bg-secondary">Secondary</span>
<span class="badge bg-success">Success</span>
<span class="badge bg-danger">Danger</span>
<span class="badge bg-warning">Warning</span>
<span class="badge bg-info">Info</span>

<!-- Pill Badges -->
<span class="badge badge-pill bg-primary">Primary</span>

<!-- Soft Background Badges -->
<span class="badge bg-primary-soft text-primary">Primary</span>
<span class="badge bg-success-soft text-success">Success</span>
```

#### Alerts
```html
<div class="alert alert-primary" role="alert">
    <strong>Info!</strong> This is a primary alert.
</div>

<div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>Success!</strong> Data saved successfully.
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
```

### 8. Navigation

#### Tabs
```html
<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#tab1">Tab 1</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#tab2">Tab 2</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" href="#tab3">Tab 3</a>
    </li>
</ul>

<div class="tab-content">
    <div class="tab-pane fade show active" id="tab1">
        Content for Tab 1
    </div>
    <div class="tab-pane fade" id="tab2">
        Content for Tab 2
    </div>
    <div class="tab-pane fade" id="tab3">
        Content for Tab 3
    </div>
</div>
```

#### Pills
```html
<ul class="nav nav-pills">
    <li class="nav-item">
        <a class="nav-link active" href="#">Active</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">Link</a>
    </li>
    <li class="nav-item">
        <a class="nav-link disabled" href="#">Disabled</a>
    </li>
</ul>
```

---

## 🔄 Migration Guide

### Migrating Existing Pages

**Step 1: Identify Custom Styles**

Find pages with `<style>` tags or inline styles:
```bash
# Search for style sections
grep -r "<?= \$this->section('css')" app/Views/
```

**Step 2: Replace Common Patterns**

| Old Custom CSS | New SB Admin Pro Class |
|----------------|------------------------|
| `.my-card { border: 1px solid #ddd; }` | Use `<div class="card">` |
| `.my-button { background: blue; }` | Use `<button class="btn btn-primary">` |
| `.my-table { ... }` | Use `<table class="table table-hover">` |
| Custom form styles | Use `.form-control`, `.form-select`, etc. |

**Step 3: Update HTML Structure**

**Before:**
```html
<div style="background: white; padding: 20px; border-radius: 8px;">
    <h3 style="color: #333;">Title</h3>
    <p>Content</p>
</div>
```

**After:**
```html
<div class="card">
    <div class="card-header">
        Title
    </div>
    <div class="card-body">
        <p>Content</p>
    </div>
</div>
```

**Step 4: Test Each Page**

1. Load the page
2. Check for visual issues
3. Test all interactive elements
4. Verify responsive behavior on mobile

---

## ✅ Best Practices

### 1. Use Semantic HTML

```html
<!-- Good -->
<button class="btn btn-primary">Submit</button>

<!-- Avoid -->
<div class="btn btn-primary" onclick="submit()">Submit</div>
```

### 2. Consistent Spacing

Use Bootstrap spacing utilities:
```html
<div class="mb-3">     <!-- Margin bottom 3 -->
<div class="mt-4">     <!-- Margin top 4 -->
<div class="p-3">      <!-- Padding 3 -->
<div class="px-4 py-2"><!-- Padding X: 4, Y: 2 -->
```

### 3. Responsive Design

Always test on different screen sizes:
```html
<div class="col-12 col-md-6 col-lg-4">
    <!-- Full width mobile, half on tablet, third on desktop -->
</div>
```

### 4. Accessibility

Add proper ARIA attributes:
```html
<button class="btn btn-primary" aria-label="Save data">
    <i class="fas fa-save"></i>
</button>

<table role="grid" aria-label="User data table">
    <!-- table content -->
</table>
```

### 5. Performance

- Don't add inline styles unless absolutely necessary
- Minimize custom CSS in `<?= $this->section('css') ?>`
- Use existing classes instead of creating new ones
- Optimize images and icons

---

## 🐛 Troubleshooting

### Issue: Styles Not Loading

**Solution:**
```php
<!-- Clear browser cache (Ctrl + Shift + R) -->
<!-- Add version parameter to force reload -->
<link href="<?= base_url('assets/css/sb-admin-pro-master.css') ?>?v=<?= time() ?>" rel="stylesheet">
```

### Issue: DataTables Not Styled Correctly

**Solution:**
Make sure DataTables CSS is loaded BEFORE sb-admin-pro-master.css:
```php
<!-- DataTables CSS -->
<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<!-- SB Admin Pro CSS -->
<link href="<?= base_url('assets/css/sb-admin-pro-master.css') ?>" rel="stylesheet">
```

### Issue: Select2 Dropdown Looks Wrong

**Solution:**
Initialize Select2 after DOM is ready:
```javascript
$(document).ready(function() {
    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });
});
```

### Issue: Modal Backdrop Issues

**Solution:**
Ensure Bootstrap JS is loaded:
```php
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
```

### Issue: Buttons Not Hovering Properly

**Solution:**
Check if you have conflicting CSS. Remove custom button styles:
```html
<!-- Remove this -->
<button style="background: red;">Button</button>

<!-- Use this -->
<button class="btn btn-danger">Button</button>
```

---

## 📊 Color Reference

### Primary Colors

```css
--bs-primary: #0061f2       /* Main primary blue */
--bs-secondary: #6900c7     /* Purple accent */
--bs-success: #00ac69       /* Green for success */
--bs-info: #00cfd5          /* Cyan for info */
--bs-warning: #f4a100       /* Orange for warnings */
--bs-danger: #e81500        /* Red for errors */
```

### Soft Background Colors

```css
--bs-primary-soft: rgba(0, 97, 242, 0.1)
--bs-success-soft: rgba(0, 172, 105, 0.1)
--bs-warning-soft: rgba(244, 161, 0, 0.1)
--bs-danger-soft: rgba(232, 21, 0, 0.1)
```

### Gray Scale

```css
--bs-gray-100: #f2f6fc     /* Lightest - backgrounds */
--bs-gray-200: #e0e5ec     /* Borders */
--bs-gray-300: #d4dae3     
--bs-gray-400: #c5ccd6     
--bs-gray-500: #a7aeb8     /* Muted text */
--bs-gray-600: #69707a     /* Body text */
--bs-gray-700: #4a5259     
--bs-gray-800: #363d47     /* Sidebar */
--bs-gray-900: #212832     /* Headings */
```

---

## 📚 Additional Resources

- **Bootstrap 5 Documentation:** https://getbootstrap.com/docs/5.3/
- **Font Awesome Icons:** https://fontawesome.com/icons
- **DataTables Documentation:** https://datatables.net/
- **Select2 Documentation:** https://select2.org/

---

## 🤝 Support

Jika Anda mengalami masalah atau memiliki pertanyaan:

1. Cek dokumentasi ini terlebih dahulu
2. Periksa console browser untuk error JavaScript
3. Pastikan semua file CSS dan JS dimuat dengan benar
4. Hubungi tim development OPTIMA

---

## 📝 Changelog

### Version 1.0.0 (November 29, 2024)

- ✅ Initial release
- ✅ Complete component library
- ✅ DataTables integration
- ✅ Select2 integration
- ✅ Responsive design
- ✅ Dark mode support (optional)
- ✅ Print styles

---

**© 2024 PT Sarana Mitra Luas Tbk. All rights reserved.**
